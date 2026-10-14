<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Data: permintaan Komite Keperawatan (14 Okt 2026) — RKK Hemodialisa butir HD-06
 * "Melakukan program sesuai Prescripe dokter (QB, QD, UF dan Anti Koagulan)"
 * untuk Lendra Kusdiantoro & Kiki Riska Nuraini: kolom kewenangan -> Dengan Supervisi.
 * Hanya butir HD-06 yang diubah; format JSON (jumlah lapis encode) dipertahankan.
 */
class RkkHd06SupervisiLendraKiki extends Migration
{
    /** penilaian_id => [potongan nama, kewenangan sebelum perubahan (untuk rollback)] */
    const PESERTA = [
        485 => ['LENDRA KUSDIANTORO', 'sendiri'],
        418 => ['KIKI RISKA NURAINI', 'supervisi'],
    ];

    public function up()
    {
        $this->set(fn($lama) => 'supervisi');
    }

    public function down()
    {
        $this->set(fn($lama, $sebelum) => $sebelum);
    }

    private function set(callable $nilai): void
    {
        foreach (self::PESERTA as $id => [$nama, $sebelum]) {
            $row = DB::table('wawancara_penilaians as wp')
                ->join('jadwal_wawancaras as j', 'j.id', '=', 'wp.jadwal_wawancara_id')
                ->join('pengajuan_sertifikats as p', 'p.id', '=', 'j.pengajuan_sertifikat_id')
                ->join('users as u', 'u.id', '=', 'p.user_id')
                ->where('wp.id', $id)->where('u.name', 'like', '%' . $nama . '%')
                ->select('wp.detail_penilaian')->first();
            if (!$row) continue;

            $v = $row->detail_penilaian ?: '[]';
            $lapis = 0;
            while (is_string($v) && $lapis < 5) { $v = json_decode($v, true); $lapis++; }
            if (!is_array($v) || !isset($v['rkk']['HD-06'])) continue;

            $v['rkk']['HD-06']['kewenangan'] = $nilai($v['rkk']['HD-06']['kewenangan'] ?? null, $sebelum);

            for ($i = 0; $i < max(1, $lapis); $i++) $v = json_encode($v);
            DB::table('wawancara_penilaians')->where('id', $id)->update(['detail_penilaian' => $v, 'updated_at' => now()]);
        }
    }
}
