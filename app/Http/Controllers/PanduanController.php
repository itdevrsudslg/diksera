<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Buku Panduan: isi ditulis dalam Markdown di resources/panduan/*.md
 * dan diperbarui setiap ada perubahan aplikasi (lihat 07-perubahan.md).
 */
class PanduanController extends Controller
{
    /** file => [judul, ikon, role yang boleh melihat (null = semua)] */
    const BAGIAN = [
        '01-pengenalan'  => ['Pengenalan & Akun', 'bi-info-circle', null],
        '02-perawat'     => ['Perawat & Bidan', 'bi-person-heart', ['perawat', 'admin']],
        '03-karu-kasi'   => ['Kepala Ruangan & Kasi', 'bi-person-badge', ['perawat', 'admin']],
        '04-pewawancara' => ['Pewawancara', 'bi-person-video2', ['pewawancara', 'admin']],
        '05-admin'       => ['Admin', 'bi-shield-lock', ['admin']],
        '06-faq'         => ['Pertanyaan Umum', 'bi-question-circle', null],
        '07-perubahan'   => ['Riwayat Perubahan', 'bi-clock-history', null],
    ];

    public function index()
    {
        $role = Auth::user()->role;
        $bagian = [];

        foreach (self::BAGIAN as $file => [$judul, $ikon, $roles]) {
            if ($roles !== null && !in_array($role, $roles, true)) continue;
            $path = resource_path("panduan/$file.md");
            if (!is_file($path)) continue;

            [$html, $toc] = $this->render(file_get_contents($path), $file);
            $bagian[] = [
                'id'     => $file,
                'judul'  => $judul,
                'ikon'   => $ikon,
                'html'   => $html,
                'toc'    => $toc,
                'diubah' => filemtime($path),
            ];
        }

        return view('panduan.index', [
            'bagian'    => $bagian,
            'terakhir'  => collect($bagian)->max('diubah'),
        ]);
    }

    /** Markdown -> HTML, beri id pada judul (h2/h3) untuk daftar isi. */
    private function render(string $md, string $prefix): array
    {
        $html = Str::markdown($md, ['html_input' => 'strip', 'allow_unsafe_links' => false]);
        $toc = [];

        $html = preg_replace_callback('/<h([23])>(.*?)<\/h\1>/s', function ($m) use (&$toc, $prefix) {
            $teks = html_entity_decode(strip_tags($m[2]), ENT_QUOTES, 'UTF-8');
            $id = $prefix . '-' . Str::slug($teks) . '-' . count($toc);
            $toc[] = ['level' => (int) $m[1], 'teks' => $teks, 'id' => $id];
            return "<h{$m[1]} id=\"$id\">{$m[2]}</h{$m[1]}>";
        }, $html);

        // Tabel agar rapi & responsif
        $html = str_replace(['<table>', '</table>'], ['<div class="table-responsive"><table class="table table-sm table-bordered">', '</table></div>'], $html);

        return [$html, $toc];
    }
}
