<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Pejabat penanda tangan dokumen & label kolom TTD yang bisa diatur admin.
 *
 * Nilai bawaan di bawah = nilai yang sebelumnya ditulis langsung di kode,
 * sehingga dokumen tidak berubah sampai admin mengisi pengaturan.
 */
class Penandatangan
{
    /** Pejabat tetap: jabatan (tampil di dokumen), label QR, dan data bawaan. */
    const SLOT = [
        'direktur' => [
            'jabatan'  => 'Direktur UOBK RSUD Simpang Lima Gumul Kediri',
            'label_qr' => 'Direktur UOBK RSUD SLG',
            'nama'     => 'dr. TONY WIDYANTO, Sp.OG (K)',
            'nip'      => '19750714 200212 1 006',
            'pangkat'  => 'Pembina Tingkat I / IV b',
        ],
        'ketua_komite' => [
            'jabatan'  => 'Ketua Komite Keperawatan',
            'label_qr' => 'Ketua Komite Keperawatan',
            'nama'     => 'BAMBANG WAHYU SETYONO, S.Kep.,Ns',
            'nip'      => '19841025 200901 1 003',
            'pangkat'  => null,
        ],
        'kasi_keperawatan' => [
            'jabatan'  => 'Kepala Seksi Keperawatan',
            'label_qr' => 'Kepala Seksi Keperawatan',
            'nama'     => 'SUJIMAN',
            'nip'      => null,
            'pangkat'  => null,
        ],
        'ketua_subkomite' => [
            'jabatan'  => 'Ketua Sub Komite Kredensial',
            'label_qr' => 'Ketua Sub Komite Kredensial',
            'nama'     => 'HARIS LISTYAWAN, S.Kep.,Ns',
            'nip'      => '19820214 200901 1 003',
            'pangkat'  => null,
        ],
    ];

    /** Label kolom TTD yang bisa diubah (kunci => bawaan). */
    const LABEL = [
        'oppe.ttd1'    => 'Teman Sejawat',
        'oppe.ttd2'    => 'Perawat Yang Dinilai',
        'oppe.ttd3'    => 'Atasan Langsung',
        'logbook.ttd1' => 'Perawat Pelaksana',
        'logbook.ttd2' => 'Kepala Ruangan',
        'logbook.ttd3' => "Mengetahui,\nKepala Seksi Keperawatan",
    ];

    private static $cache = [];

    /** Pejabat untuk slot pada tanggal dokumen (default: hari ini). */
    public static function untuk(string $slot, $tanggal = null): object
    {
        $def = self::SLOT[$slot];
        $tgl = $tanggal ? Carbon::parse($tanggal)->toDateString() : now()->toDateString();
        $key = "$slot|$tgl";

        if (!array_key_exists($key, self::$cache)) {
            self::$cache[$key] = self::tabelAda()
                ? DB::table('penandatangans')->where('slot', $slot)->where('berlaku_mulai', '<=', $tgl)
                    ->orderByDesc('berlaku_mulai')->orderByDesc('id')->first()
                : null;
        }
        $row = self::$cache[$key];

        return (object) [
            'slot'     => $slot,
            'jabatan'  => $def['jabatan'],
            'nama'     => $row->nama ?? $def['nama'],
            'nip'      => $row ? $row->nip : $def['nip'],
            'pangkat'  => $row ? $row->pangkat : $def['pangkat'],
            'nama_qr'  => trim(explode(',', $row->nama ?? $def['nama'])[0]),
            'label_qr' => $def['label_qr'],
        ];
    }

    /** URL gambar QR TTD (format sama seperti sebelumnya). */
    public static function qr(string $slot, string $verifyUrl, $tanggal = null, string $size = '100x100'): string
    {
        $p = self::untuk($slot, $tanggal);
        return "https://api.qrserver.com/v1/create-qr-code/?size=$size&data="
            . urlencode("{$p->label_qr}: {$p->nama_qr}. Validasi: " . $verifyUrl);
    }

    /** Teks label kolom TTD. */
    public static function label(string $kunci): string
    {
        $all = self::semuaLabel();
        return $all[$kunci] ?? (self::LABEL[$kunci] ?? $kunci);
    }

    public static function semuaLabel(): array
    {
        if (!array_key_exists('__label', self::$cache)) {
            $db = Schema::hasTable('pengaturans')
                ? DB::table('pengaturans')->whereIn('kunci', array_keys(self::LABEL))->pluck('nilai', 'kunci')->filter()->all()
                : [];
            self::$cache['__label'] = array_merge(self::LABEL, $db);
        }
        return self::$cache['__label'];
    }

    public static function lupakanCache(): void
    {
        self::$cache = [];
    }

    private static function tabelAda(): bool
    {
        return self::$cache['__tabel'] ??= Schema::hasTable('penandatangans');
    }
}
