<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Buku Panduan: isi ditulis dalam Markdown di resources/panduan/*.md
 * dan diperbarui setiap ada perubahan aplikasi (lihat 07-perubahan.md).
 * Bagian yang tampil disesuaikan dengan peran pengguna (perawat, karu, kasi, pewawancara, admin).
 */
class PanduanController extends Controller
{
    /** Peran yang bisa dipilih admin lewat "Lihat sebagai". */
    const PERAN = [
        'perawat'     => 'Perawat / Bidan',
        'karu'        => 'Kepala Ruangan',
        'kasi'        => 'Kasi Keperawatan',
        'pewawancara' => 'Pewawancara',
        'admin'       => 'Admin',
    ];

    /** Urutan bagian per peran: [file, judul, ikon]. Bagian khusus peran diletakkan setelah Pengenalan. */
    const SUSUNAN = [
        'perawat'     => ['01-pengenalan', '02-perawat', '06-faq', '07-perubahan'],
        'karu'        => ['01-pengenalan', '03-karu', '02-perawat', '06-faq', '07-perubahan'],
        'kasi'        => ['01-pengenalan', '03-kasi', '02-perawat', '06-faq', '07-perubahan'],
        'pewawancara' => ['01-pengenalan', '04-pewawancara', '06-faq', '07-perubahan'],
        'admin'       => ['01-pengenalan', '05-admin', '02-perawat', '03-karu', '03-kasi', '04-pewawancara', '06-faq', '07-perubahan'],
    ];

    const INFO = [
        '01-pengenalan'  => ['Pengenalan & Akun', 'bi-info-circle'],
        '02-perawat'     => ['Perawat & Bidan', 'bi-person-heart'],
        '03-karu'        => ['Kepala Ruangan', 'bi-person-badge'],
        '03-kasi'        => ['Kasi Keperawatan', 'bi-person-check'],
        '04-pewawancara' => ['Pewawancara', 'bi-person-video2'],
        '05-admin'       => ['Admin', 'bi-shield-lock'],
        '06-faq'         => ['Pertanyaan Umum', 'bi-question-circle'],
        '07-perubahan'   => ['Riwayat Perubahan', 'bi-clock-history'],
    ];

    public function index(Request $request)
    {
        $user = Auth::user();
        $peranSaya = $this->peran($user);

        // Admin bisa melihat panduan dari sudut pandang peran lain
        $peran = $peranSaya === 'admin' && array_key_exists($request->get('sebagai'), self::PERAN)
            ? $request->get('sebagai')
            : $peranSaya;

        $bagian = [];
        foreach (self::SUSUNAN[$peran] as $file) {
            $path = resource_path("panduan/$file.md");
            if (!is_file($path)) continue;

            [$html, $toc] = $this->render(file_get_contents($path), $file);
            [$judul, $ikon] = self::INFO[$file];
            $bagian[] = compact('judul', 'ikon', 'html', 'toc') + ['id' => $file, 'diubah' => filemtime($path)];
        }

        return view('panduan.index', [
            'bagian'    => $bagian,
            'terakhir'  => collect($bagian)->max('diubah'),
            'peran'     => $peran,
            'namaPeran' => self::PERAN[$peran],
            'isAdmin'   => $peranSaya === 'admin',
            'daftarPeran' => self::PERAN,
        ]);
    }

    private function peran($user): string
    {
        if ($user->role === 'admin') return 'admin';
        if ($user->role === 'pewawancara') return 'pewawancara';

        $jabatan = optional($user->profile)->jabatan_struktural;
        if ($jabatan === 'kepala_ruangan') return 'karu';
        if ($jabatan === 'kepala_seksi') return 'kasi';
        return 'perawat';
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
