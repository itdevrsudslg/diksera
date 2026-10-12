<?php

namespace App\Services;

use App\Models\LogbookEntry;
use App\Models\LogbookKegiatanTindakan;
use App\Models\LogbookTindakan;
use App\Models\PerawatPekerjaan;
use App\Models\PerawatProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogbookService
{
    /* =========================================================
     * IDENTITAS & STRUKTUR
     * ========================================================= */

    /** Unit kerja terkini perawat (riwayat pekerjaan terbaru, sama seperti OPPE). */
    public static function unitKerja(int $userId): ?string
    {
        return PerawatPekerjaan::where('user_id', $userId)
            ->orderBy('tahun_mulai', 'desc')
            ->value('unit_kerja');
    }

    public static function jabatan(User $user): string
    {
        return optional($user->profile)->jabatan_struktural ?? 'pelaksana';
    }

    public static function isKaru(User $user): bool
    {
        return self::jabatan($user) === 'kepala_ruangan';
    }

    public static function isKasi(User $user): bool
    {
        return self::jabatan($user) === 'kepala_seksi';
    }

    /** Kepala ruangan untuk unit tertentu (dipakai di blok TTD bila rekap belum ditandatangani). */
    public static function karuUnit(?string $unit): ?User
    {
        if (!$unit) return null;
        $ids = PerawatPekerjaan::where('unit_kerja', $unit)->pluck('user_id');
        $profile = PerawatProfile::whereIn('user_id', $ids)->where('jabatan_struktural', 'kepala_ruangan')->first();
        return $profile ? User::find($profile->user_id) : null;
    }

    /* =========================================================
     * SIMRS KHANZA (read-only)
     * ========================================================= */

    public static function khanzaAktif(): bool
    {
        return (bool) config('database.connections.khanza.username');
    }

    /** Cari nama pasien dari No RM. Null bila Khanza tidak tersedia / tidak ditemukan. */
    public static function cariPasien(string $noRm): ?array
    {
        if (!self::khanzaAktif()) return null;
        try {
            $p = DB::connection('khanza')->table('pasien')
                ->where('no_rkm_medis', $noRm)
                ->first(['no_rkm_medis', 'nm_pasien', 'jk', 'tgl_lahir']);
            return $p ? (array) $p : null;
        } catch (\Throwable $e) {
            Log::warning('Logbook: gagal cari pasien di Khanza: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sinkron master tindakan dari Khanza (jns_perawatan = rawat jalan, jns_perawatan_inap = rawat inap).
     * Tindakan yang sudah tidak aktif di Khanza ikut dinonaktifkan; data lama tidak dihapus.
     */
    public static function sinkronTindakanKhanza(): array
    {
        $hasil = ['baru' => 0, 'diperbarui' => 0, 'nonaktif' => 0];
        $sumber = ['ralan' => 'jns_perawatan', 'ranap' => 'jns_perawatan_inap'];

        foreach ($sumber as $kategori => $tabel) {
            $rows = DB::connection('khanza')->table($tabel)->get(['kd_jenis_prw', 'nm_perawatan', 'status']);
            $kodeAktif = [];

            foreach ($rows as $r) {
                $aktif = (string) $r->status === '1';
                $t = LogbookTindakan::firstOrNew(['kode' => trim($r->kd_jenis_prw), 'kategori' => $kategori]);
                $baru = !$t->exists;
                $t->fill(['nama' => trim($r->nm_perawatan), 'sumber' => 'khanza', 'aktif' => $aktif]);
                if ($baru || $t->isDirty()) {
                    $t->save();
                    $baru ? $hasil['baru']++ : $hasil['diperbarui']++;
                }
                $kodeAktif[] = $t->kode;
            }

            $hasil['nonaktif'] += LogbookTindakan::where('sumber', 'khanza')
                ->where('kategori', $kategori)
                ->whereNotIn('kode', $kodeAktif)
                ->where('aktif', true)
                ->update(['aktif' => false]);
        }

        return $hasil;
    }

    /* =========================================================
     * REKAP
     * ========================================================= */

    /**
     * Ringkasan logbook satu perawat pada rentang bulan (bulanan / triwulan / semester).
     * $hanyaValid: rekap resmi hanya menghitung entri yang sudah divalidasi karu.
     */
    public static function rekap(int $userId, Carbon $mulai, Carbon $selesai, bool $hanyaValid = true): array
    {
        $entries = LogbookEntry::with('kegiatans.tindakans')
            ->where('user_id', $userId)
            ->whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])
            ->when($hanyaValid, fn($q) => $q->where('status', 'valid'))
            ->orderBy('tanggal')
            ->get();

        $tindakan = $entries->flatMap(fn($e) => $e->kegiatans->flatMap->tindakans)
            ->groupBy(fn($t) => $t->kode . '|' . $t->nama)
            ->map(function ($items) {
                $f = $items->first();
                return ['kode' => $f->kode, 'nama' => $f->nama, 'jumlah' => $items->sum('jumlah'), 'frekuensi' => $items->count()];
            })
            ->sortByDesc('jumlah')
            ->values();

        $perBulan = $entries->groupBy(fn($e) => $e->tanggal->format('Y-m'))->map(fn($g) => [
            'pasien' => $g->pluck('no_rm')->unique()->count(),
            'entri' => $g->count(),
            'tindakan' => $g->sum(fn($e) => $e->kegiatans->sum(fn($k) => $k->tindakans->sum('jumlah'))),
        ]);

        return [
            'entries' => $entries,
            'tindakan' => $tindakan,
            'per_bulan' => $perBulan,
            'total_pasien' => $entries->pluck('no_rm')->unique()->count(),
            'total_kunjungan' => $entries->count(),
            'total_kegiatan' => $entries->sum(fn($e) => $e->kegiatans->count()),
            'total_tindakan' => $tindakan->sum('jumlah'),
            'hari_aktif' => $entries->pluck('tanggal')->map->toDateString()->unique()->count(),
        ];
    }

    /** Rentang tanggal dari jenis periode: bulan | triwulan | semester. */
    public static function rentang(string $jenis, int $tahun, int $ke): array
    {
        switch ($jenis) {
            case 'triwulan':
                $ke = max(1, min(4, $ke));
                $mulai = Carbon::create($tahun, ($ke - 1) * 3 + 1, 1);
                $selesai = $mulai->copy()->addMonths(2)->endOfMonth();
                $nama = 'Triwulan ' . ['I', 'II', 'III', 'IV'][$ke - 1] . ' ' . $tahun;
                break;
            case 'semester':
                $ke = max(1, min(2, $ke));
                $mulai = Carbon::create($tahun, ($ke - 1) * 6 + 1, 1);
                $selesai = $mulai->copy()->addMonths(5)->endOfMonth();
                $nama = 'Semester ' . ($ke === 1 ? 'I' : 'II') . ' ' . $tahun;
                break;
            default:
                $ke = max(1, min(12, $ke));
                $mulai = Carbon::create($tahun, $ke, 1);
                $selesai = $mulai->copy()->endOfMonth();
                $nama = $mulai->translatedFormat('F Y');
        }

        return [$mulai->startOfDay(), $selesai->endOfDay(), $nama];
    }
}
