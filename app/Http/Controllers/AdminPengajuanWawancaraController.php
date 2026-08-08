<?php

namespace App\Http\Controllers;

use App\Models\JadwalWawancara;
use App\Models\PengajuanSertifikat;
use App\Models\PerawatLisensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;// Pastikan import ini ada
use App\Services\TelegramService;
use Carbon\Carbon;

class AdminPengajuanWawancaraController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function approveJadwal(Request $request, $id)
    {
        $pengajuan = PengajuanSertifikat::with(['jadwalWawancara', 'user'])->findOrFail($id);
        $jadwal = $pengajuan->jadwalWawancara;

        if (!$jadwal) {
            return back()->with('error', 'Jadwal belum diajukan oleh perawat.');
        }

        // 1. Validasi Input (Tambahkan validasi pewawancara)
        $request->validate([
            'penanggung_jawab_id' => 'required|exists:penanggung_jawab_ujians,id', // [BARU]
            'tgl_wawancara'       => 'required|date',
            'jam_wawancara'       => 'required',
            'lokasi'              => 'required|string',
            'deskripsi_skill'     => 'nullable|string',
        ]);

        $waktuFix = $request->tgl_wawancara . ' ' . $request->jam_wawancara;

        // 2. Update Data Jadwal (Simpan ID Pewawancara Baru)
        $jadwal->update([
            'penanggung_jawab_id' => $request->penanggung_jawab_id, // [BARU] Simpan perubahan
            'waktu_wawancara'     => $waktuFix,
            'lokasi'              => $request->lokasi,
            'deskripsi_skill'     => $request->deskripsi_skill,
            'status'              => 'approved',
            'catatan_admin'       => null
        ]);

        $pengajuan->update(['status' => 'interview_scheduled']);

        // 3. Kirim Notifikasi Telegram (Ke pewawancara yang (baru) dipilih)
        try {
            // Refresh relasi agar mengambil data pewawancara terbaru
            $jadwal->load('pewawancara.user');

            $pewawancaraProfile = $jadwal->pewawancara;
            if ($pewawancaraProfile && $pewawancaraProfile->user) {
                $this->telegramService->notifyNewScheduleForInterviewer($pewawancaraProfile->user, $jadwal);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gagal kirim notif telegram: " . $e->getMessage());
        }

        return back()->with('success', 'Jadwal disetujui. Pewawancara dan waktu telah diperbarui.');
    }
public function terbitkanSpk(Request $request, $id)
    {
        $pengajuan = PengajuanSertifikat::with('user')->findOrFail($id);

        // Pastikan hanya pengajuan yang sudah lulus ujian/wawancara yang bisa diterbitkan
        if (!in_array($pengajuan->status, ['interview_passed', 'exam_passed', 'pending'])) {
            return back()->with('error', 'Status pengajuan belum memenuhi syarat untuk diterbitkan SPK.');
        }

        DB::beginTransaction();
        try {
            $tglMulai = now();
            $tglAkhir = now()->addYears(3); // Masa berlaku 3 tahun sesuai dokumen
            $kfk = is_array($pengajuan->kfk) ? implode(', ', $pengajuan->kfk) : $pengajuan->kfk;

            if ($pengajuan->jenis_pengajuan == 'baru') {
                // 1. Buat Lisensi/Sertifikat Baru
                $bidangValue = (is_array($pengajuan->kfk) && isset($pengajuan->kfk[0])) ? $pengajuan->kfk[0] : null;
                $kfkValue    = (is_array($pengajuan->kfk) && isset($pengajuan->kfk[1])) ? $pengajuan->kfk[1] : null;

                $newLisensi = PerawatLisensi::create([
                    'user_id'              => $pengajuan->user_id,
                    'nama'                 => 'Penugasan Klinis (SPK & SPKK)',
                    'nomor'                => '-', // Setup awal
                    'lembaga'              => 'RSUD Simpang Lima Gumul',
                    'bidang'               => $bidangValue,
                    'kfk'                  => $kfkValue,
                    'metode'               => $pengajuan->metode,
                    'status'               => 'active',
                    'tgl_terbit'           => $tglMulai,
                    'tgl_expired'          => $tglAkhir,
                    'tgl_mulai'            => $tglMulai,
                    'tgl_diselenggarakan'  => now(),
                    'unit_kerja_saat_buat' => $pengajuan->user->unit_kerja ?? 'RSUD SLG'
                ]);

                // 2. Buat Nomor Surat Resmi
                $idMentah = $newLisensi->id;
                $nomorUrutMurni = str_pad($idMentah, 3, '0', STR_PAD_LEFT);
                $tahunSurat = \Carbon\Carbon::parse($tglMulai)->format('Y');
                
                // Format SPKK (800) dan SPK (589) akan di-render di PDF, kita simpan base nomornya
                $nomorBaru = "{$nomorUrutMurni}/418.101/SERKOM/{$tahunSurat}";
                $newLisensi->update(['nomor' => $nomorBaru]);

                // 3. Update Status Pengajuan
                $pengajuan->update([
                    'status' => 'completed', // Status ini yang memunculkan tombol download di sisi perawat
                    'lisensi_lama_id' => $newLisensi->id
                ]);

            } else {
                // Proses Perpanjangan
                if ($pengajuan->lisensiLama) {
                    $pengajuan->lisensiLama->update([
                        'status'      => 'active',
                        'tgl_terbit'  => $tglMulai,
                        'tgl_expired' => $tglAkhir,
                    ]);
                }
              $pengajuan->update(['status' => 'completed']);
            }

            DB::commit();

            // Opsional: Jika kamu sudah setup Telegram Bot, bisa sekalian notif ke perawat
            // TelegramController::sendNotif($pengajuan->user_id, "Selamat! SPK & SPKK Anda telah diterbitkan. Silakan unduh di Dashboard.");

            return back()->with('success', 'SPK & SPKK berhasil diterbitkan dan sudah terkirim ke dashboard perawat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menerbitkan SPK: ' . $e->getMessage());
        }
    }
    
    public function rejectJadwal(Request $request, $id)
    {
        // Asumsi $id adalah ID Pengajuan
        $pengajuan = PengajuanSertifikat::with('jadwalWawancara')->findOrFail($id);
        $jadwal = $pengajuan->jadwalWawancara;

        if ($jadwal) {
            $jadwal->update([
                'status' => 'rejected',
                'catatan_admin' => $request->alasan
            ]);
        }

        // Status pengajuan tetap di tahap interview agar user bisa resubmit
        // Tidak perlu ubah ke 'exam_passed' jika logic view sudah handle resubmit based on jadwal status rejected

        return back()->with('success', 'Pengajuan jadwal ditolak. Perawat diminta mengajukan ulang.');
    }
}
