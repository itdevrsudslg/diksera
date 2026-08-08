<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Carbon\Carbon;

class TelegramPolling extends Command
{
    protected $signature = 'telegram:polling';
    protected $description = 'Listen to Telegram updates via polling (Daemon) - PHP 7.4 Compatible';

    // Flag untuk memantau apakah Supervisor menyuruh berhenti
    protected $shouldExit = false;

    public function handle()
    {
        // 1. OPTIMASI: Ambil env di luar loop
        $botToken = env('TELEGRAM_BOT_TOKEN');
        if (!$botToken) {
            $this->error('TELEGRAM_BOT_TOKEN belum diatur di .env');
            return 1;
        }

        $offset = 0;

        // 2. SAFETY: Register Signal Handler (Agar bisa distop halus oleh Supervisor)
        // Pastikan ekstensi PCNTL aktif di PHP aaPanel (biasanya default aktif)
        if (extension_loaded('pcntl')) {
            pcntl_async_signals(true);
            pcntl_signal(SIGINT, [$this, 'stopGracefully']);
            pcntl_signal(SIGTERM, [$this, 'stopGracefully']);
        }

        $this->info("🤖 Bot Sistem Kepegawaian RSUD SLG berjalan (PHP 7.4 Safe Mode)...");

        // Batas waktu eksekusi (1 jam) untuk mencegah memory leak
        $startTime = time();
        $maxExecutionTime = 3600;

        while (true) {
            // Cek apakah disuruh berhenti oleh Supervisor atau Time Limit
            if ($this->shouldExit || (time() - $startTime) > $maxExecutionTime) {
                $this->comment("↻ Restarting process for cleanup/maintenance...");
                return 0;
            }

            try {
                $response = Http::timeout(35)->get("https://api.telegram.org/bot{$botToken}/getUpdates", [
                    'offset' => $offset,
                    'timeout' => 30
                ]);

                if ($response->successful()) {
                    $updates = $response->json()['result'] ?? [];

                    foreach ($updates as $update) {
                        $offset = $update['update_id'] + 1;

                        if (isset($update['message']['text'])) {
                            $this->processMessage($update['message'], $botToken);
                        }
                    }
                } else {
                    // Log error jika API Telegram menolak (misal 401 Unauthorized)
                    $this->error("API Error: " . $response->status());
                    sleep(5);
                }
            } catch (\Exception $e) {
                $this->error("Connection Error: " . $e->getMessage());
                sleep(5);
            }

            // Bersihkan memori
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
            usleep(200000); // Delay 0.2 detik
        }
    }

    public function stopGracefully()
    {
        $this->info("🛑 Menerima sinyal berhenti. Menyelesaikan proses...");
        $this->shouldExit = true;
    }

    private function processMessage($message, $botToken)
    {
        $chatId = $message['chat']['id'];
        $text = strtoupper(trim($message['text']));
        $firstName = $message['from']['first_name'] ?? 'User';

        $this->comment("📩 Pesan: {$text} | ID: {$chatId}");

        // --- 1. Handle START ---
        if ($text === '/START') {
            $welcomeMsg = "👋 <b>Selamat Datang di Bot Notifikasi Kepegawaian</b>\n";
            $welcomeMsg .= "<b>RSUD Simpang Lima Gumul Kediri</b>\n\n";
            $welcomeMsg .= "Silahkan kirimkan <b>Kode Verifikasi</b> yang Anda dapatkan dari aplikasi web.\n";
            $welcomeMsg .= "⚠️ <i>Pastikan kode tersebut belum kadaluarsa.</i>";

            $this->sendMessage($chatId, $welcomeMsg, $botToken);
            return;
        }

        // --- 2. Cek Kode Verifikasi ---
        $user = User::where('telegram_verification_code', $text)
            ->where('telegram_verification_expires_at', '>', Carbon::now())
            ->first();

        if ($user) {
            $user->telegram_chat_id = $chatId;
            $user->telegram_verification_code = null;
            $user->telegram_verification_expires_at = null;
            $user->save();

            // FIX: Ganti MATCH (PHP 8) menjadi SWITCH (PHP 7.4)
            $msgRole = 'Staf Perawat'; // Default value
            switch ($user->role) {
                case 'admin':
                    $msgRole = 'Administrator';
                    break;
                case 'pewawancara':
                    $msgRole = 'Pewawancara';
                    break;
                    // default case sudah dihandle di awal
            }

            $successMsg = "✅ <b>AKUN TERHUBUNG!</b>\n\n";
            $successMsg .= "Yth. <b>{$user->name}</b> ({$msgRole}),\n";
            $successMsg .= "Akun Telegram Anda telah berhasil ditautkan.\n\n";

            if ($user->role === 'pewawancara') {
                $successMsg .= "Anda akan menerima notifikasi jadwal wawancara.";
            } else {
                $successMsg .= "Anda akan menerima notifikasi masa berlaku dokumen.";
            }

            $this->sendMessage($chatId, $successMsg, $botToken);
            $this->info("✓ User {$user->name} connected!");
        } else {
            $this->sendMessage($chatId, "❌ Kode tidak valid atau sudah kadaluarsa.", $botToken);
        }
    }

    private function sendMessage($chatId, $text, $botToken)
    {
        // Bungkus try-catch agar jika gagal kirim pesan (misal user blokir bot), bot tidak mati
        try {
            Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML'
            ]);
        } catch (\Exception $e) {
            $this->error("Gagal mengirim pesan ke {$chatId}: " . $e->getMessage());
        }
    }
}
