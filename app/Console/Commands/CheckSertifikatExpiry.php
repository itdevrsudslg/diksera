<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PerawatSip;
use App\Models\PerawatStr;
use App\Models\PerawatLisensi;
use App\Models\PerawatDataTambahan;
use App\Services\TelegramService;
use Carbon\Carbon;

class CheckSertifikatExpiry extends Command
{
    protected $signature = 'sertifikat:check-expiry';
    protected $description = 'Cek masa berlaku sertifikat perawat dan kirim notifikasi';

    protected $telegramService;
    protected $notificationSent = 0;
    // Daftar hari pengingat
    protected $reminderDays = [90, 60, 30, 14, 7, 3, 1, 0];

    public function __construct(TelegramService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
    }

    public function handle()
    {
        $this->info('🚀 Memulai pengecekan masa berlaku sertifikat...');
        $start = microtime(true);

        // 1. Cek SIP
        $this->processDocuments(PerawatSip::with('user.profile'), 'SIP');

        // 2. Cek STR
        $this->processDocuments(PerawatStr::with('user.profile'), 'STR');

        // 3. Cek Lisensi
        $this->processDocuments(PerawatLisensi::with('user.profile'), 'LISENSI');

        // 4. Cek Data Tambahan
        // Khusus data tambahan perlu logic sedikit berbeda karena ada field 'jenis'
        PerawatDataTambahan::with('user.profile')->chunk(100, function ($items) {
            foreach ($items as $data) {
                if (!$data->tgl_expired) continue;

                $jenis = strtoupper($data->jenis ?? 'DOKUMEN LAIN');
                $this->checkAndNotify($data, $jenis, $data->tgl_expired);
            }
        });

        $duration = round(microtime(true) - $start, 2);
        $this->info("✓ Selesai dalam {$duration} detik! Total {$this->notificationSent} notifikasi terkirim.");
        return 0;
    }

    /**
     * Fungsi helper untuk memproses query dengan chunking
     */
    private function processDocuments($queryBuilder, $jenisDokumen)
    {
        $this->info("Mengecek {$jenisDokumen}...");

        // Gunakan chunk(100) untuk menghemat RAM
        $queryBuilder->chunk(100, function ($items) use ($jenisDokumen) {
            foreach ($items as $item) {
                if (!$item->tgl_expired) continue;
                $this->checkAndNotify($item, $jenisDokumen, $item->tgl_expired);
            }
        });
    }

    /**
     * Logika pengecekan tanggal dan pengiriman notifikasi
     */
    private function checkAndNotify($item, $jenis, $expiryDate)
    {
        try {
            // Pastikan user ada
            if (!$item->user) return;

            $daysLeft = Carbon::now()->diffInDays(Carbon::parse($expiryDate), false);
            // Cast ke int agar pencocokan array akurat
            $daysLeftInt = (int) $daysLeft;

            // Cek apakah hari ini jadwal kirim notifikasi ATAU sudah kadaluarsa (minus)
            if (in_array($daysLeftInt, $this->reminderDays) || $daysLeftInt < 0) {

                // 1. Kirim ke Admin/Group Global
                $this->telegramService->notifySertifikatExpiring($item->user, $item, $jenis, $daysLeftInt);

                // 2. Kirim Personal ke User
                if ($item->user->telegram_chat_id) {
                    $this->telegramService->notifySertifikatExpiringToUser(
                        $item->user->telegram_chat_id,
                        $item,
                        $jenis,
                        $daysLeftInt
                    );
                }

                $this->notificationSent++;
                $this->line("  ✓ Notifikasi {$jenis}: {$item->user->name} (Sisa {$daysLeftInt} hari)");
            }
        } catch (\Exception $e) {
            $this->error("  x Error pada ID {$item->id}: " . $e->getMessage());
        }
    }
}
