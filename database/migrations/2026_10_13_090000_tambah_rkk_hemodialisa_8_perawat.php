<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Data: tambahkan kelompok RKK Hemodialisa (HD-01..HD-08) ke penilaian kredensialing 8 perawat
 * (permintaan Komite Keperawatan, 13 Okt 2026). Kewenangan sesuai jenjang:
 *   PK 2 & PK 3 -> Mandiri + Berwenang sendiri, PK 1 -> Mandiri + Dengan supervisi.
 *
 * Aman: hanya MENAMBAH penanda is_hd & jawaban HD; jawaban RKK lain tidak diubah.
 * Setiap baris dicocokkan dengan nama perawat; bila tidak cocok dilewati. Idempoten.
 */
class TambahRkkHemodialisa8Perawat extends Migration
{
    /** penilaian_id => [potongan nama perawat, jenjang PK] */
    const PESERTA = [
        226 => ['DESY NATALIA', 2],
        215 => ['DISMALYANSA', 2],
        462 => ['SISKA HENI RAHAYU', 2],
        485 => ['LENDRA KUSDIANTORO', 2],
        101 => ['HURRIA ALFISANA', 1],
        418 => ['KIKI RISKA NURAINI', 1],
        229 => ['BASUKI RAHMAD', 3],
        579 => ['SLAMET HIDAYAT', 3],
    ];

    const BUTIR = ['HD-01', 'HD-02', 'HD-03', 'HD-04', 'HD-05', 'HD-06', 'HD-07', 'HD-08'];

    public function up()
    {
        foreach (self::PESERTA as $penilaianId => [$nama, $pk]) {
            $row = $this->baris($penilaianId, $nama);
            if (!$row) continue;

            $detail = json_decode($row->detail_penilaian ?: '[]', true) ?: [];
            $detail['rkk'] = (array) ($detail['rkk'] ?? []);
            foreach (self::BUTIR as $kode) {
                if (!isset($detail['rkk'][$kode])) {
                    $detail['rkk'][$kode] = ['jenis' => 'mandiri', 'kewenangan' => $pk === 1 ? 'supervisi' : 'sendiri'];
                }
            }
            $detail['is_hd'] = '1';

            DB::table('wawancara_penilaians')->where('id', $penilaianId)
                ->update(['detail_penilaian' => json_encode($detail), 'updated_at' => now()]);
        }
    }

    public function down()
    {
        foreach (self::PESERTA as $penilaianId => [$nama]) {
            $row = $this->baris($penilaianId, $nama);
            if (!$row) continue;

            $detail = json_decode($row->detail_penilaian ?: '[]', true) ?: [];
            foreach (self::BUTIR as $kode) unset($detail['rkk'][$kode]);
            unset($detail['is_hd']);

            DB::table('wawancara_penilaians')->where('id', $penilaianId)
                ->update(['detail_penilaian' => json_encode($detail), 'updated_at' => now()]);
        }
    }

    /** Ambil penilaian hanya bila milik perawat dengan nama yang diharapkan. */
    private function baris(int $penilaianId, string $nama)
    {
        return DB::table('wawancara_penilaians as wp')
            ->join('jadwal_wawancaras as j', 'j.id', '=', 'wp.jadwal_wawancara_id')
            ->join('pengajuan_sertifikats as p', 'p.id', '=', 'j.pengajuan_sertifikat_id')
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->where('wp.id', $penilaianId)
            ->where('u.name', 'like', '%' . $nama . '%')
            ->select('wp.id', 'wp.detail_penilaian')
            ->first();
    }
}
