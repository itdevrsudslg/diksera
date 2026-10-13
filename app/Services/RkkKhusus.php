<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Kelompok kompetensi RKK tambahan / tugas spesifik (selain Bank Item RKK per jenjang),
 * seperti MPP & PPI. Untuk menambah kelompok baru cukup tambahkan entri di KELOMPOK:
 * otomatis muncul sebagai switch di penilaian pewawancara, edit pengajuan admin, OPPE,
 * dan sebagai bagian tersendiri di PDF RKK.
 *
 * Penanda aktif disimpan di detail_penilaian sebagai 'is_<kode>' => '1',
 * jawaban per butir di detail_penilaian['rkk'][<no_item>] (sama seperti MPP/PPI).
 *
 * Catatan: MPP & PPI masih memakai implementasi lama di masing-masing controller
 * agar dokumen yang sudah terbit tidak berubah.
 */
class RkkKhusus
{
    const KELOMPOK = [
        'hd' => [
            'judul'      => 'HEMODIALISA (HD)',
            'label'      => 'Tandai sebagai Hemodialisa (HD)',
            'keterangan' => 'Tambahkan item kompetensi tugas spesifik Hemodialisa.',
            'ikon'       => 'bi-droplet-half',
            'warna'      => 'text-danger',
            'awalan'     => 'HD-',
            'items'      => [
                'HD-01' => 'Asuhan Keperawatan : Pre HD, Intra HD dan Post HD',
                'HD-02' => 'Melakukan Monitoring Durante HD (Pasien, Mesin, Sirkulasi Extrakorporeal)',
                'HD-03' => 'Melakukan Punksi Akses Vaskuler AV Fistule',
                'HD-04' => 'Mengalirkan darah ke Extrakorporeal sistem',
                'HD-05' => 'Memulai Tindakan HD',
                'HD-06' => 'Melakukan program sesuai Prescripe dokter (QB, QD, UF dan Anti Koagulan)',
                'HD-07' => 'Penanganan Komplikasi Durante (Pasien, Mesin, Ekstracorporeal)',
                'HD-08' => 'Menyiapkan Peralatan untuk pemasangan double lumen HD',
            ],
        ],
    ];

    /** Butir satu kelompok dalam format yang sama dengan getMppItems()/getPpiItems(). */
    public static function items(string $kode): array
    {
        $k = self::KELOMPOK[$kode];
        $out = [];
        foreach ($k['items'] as $no => $desk) {
            $out[] = ['no_item' => $no, 'deskripsi_kompetensi' => $desk, 'kategori_tugas' => $k['judul']];
        }
        return $out;
    }

    /** Kode kelompok yang aktif pada detail_penilaian (lewat penanda atau karena sudah ada jawabannya). */
    public static function aktif($detail): array
    {
        // detail_penilaian bisa ter-encode lebih dari satu lapis
        for ($i = 0; is_string($detail) && $i < 5; $i++) $detail = json_decode($detail, true);
        $detail = is_array($detail) ? $detail : [];
        $rkk = (array) ($detail['rkk'] ?? []);
        $aktif = [];
        foreach (self::KELOMPOK as $kode => $k) {
            $ditandai = (string) ($detail['is_' . $kode] ?? '0') === '1';
            $adaJawaban = collect(array_keys($rkk))->contains(fn($key) => str_starts_with((string) $key, $k['awalan']));
            if ($ditandai || $adaJawaban) $aktif[] = $kode;
        }
        return $aktif;
    }

    /** Butir aktif sebagai objek (id = no_item) untuk digabung ke daftar penilaian RKK. */
    public static function itemsAktif($detail): Collection
    {
        return collect(self::aktif($detail))->flatMap(fn($kode) => collect(self::items($kode))->map(function ($item) {
            $obj = (object) $item;
            $obj->id = $item['no_item'];
            return $obj;
        }));
    }

    /** Untuk PDF: [['judul' => ..., 'items' => [...]], ...] */
    public static function untukPdf($detail): array
    {
        return array_map(fn($kode) => ['judul' => self::KELOMPOK[$kode]['judul'], 'items' => self::items($kode)], self::aktif($detail));
    }

    /** Penanda is_<kode> dari request form (switch). */
    public static function penandaDariRequest(Request $request): array
    {
        $out = [];
        foreach (array_keys(self::KELOMPOK) as $kode) {
            $out['is_' . $kode] = $request->input('is_' . $kode) == '1' ? '1' : '0';
        }
        return $out;
    }

    /** Untuk OPPE: tambah/hapus butir kelompok pada instrumen sesuai switch. */
    public static function terapkanKeInstrumen(array $instrumen, Request $request): array
    {
        foreach (self::KELOMPOK as $kode => $k) {
            if ($request->input('is_' . $kode) == '1') {
                $ada = array_column($instrumen, 'item_id');
                foreach (self::items($kode) as $it) {
                    if (!in_array($it['no_item'], $ada, true)) {
                        $instrumen[] = [
                            'item_id' => $it['no_item'], 'kategori_tugas' => $it['kategori_tugas'],
                            'deskripsi' => $it['deskripsi_kompetensi'],
                            'skor_self' => null, 'skor_peer' => null, 'skor_karu' => null,
                        ];
                    }
                }
            } else {
                $instrumen = array_filter($instrumen, fn($item) => !str_starts_with((string) ($item['item_id'] ?? ''), $k['awalan']));
            }
        }
        return array_values($instrumen);
    }

    /** Untuk OPPE: kelompok yang sedang ada di instrumen. */
    public static function aktifDiInstrumen($instrumen): array
    {
        $ids = collect($instrumen)->pluck('item_id')->filter()->map(fn($v) => (string) $v);
        return collect(self::KELOMPOK)->filter(fn($k) => $ids->contains(fn($id) => str_starts_with($id, $k['awalan'])))->keys()->all();
    }
}
