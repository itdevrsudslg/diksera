<?php

namespace App\Services;

use App\Models\PerawatProfile;
use App\Models\PerawatPendidikan;
use App\Models\PerawatPekerjaan;
use App\Models\PerawatDataTambahan;
use Carbon\Carbon;

class PkRecommendationService 
{
    public function getRecommendation($userId) 
    {
        // 1. AMBIL DATA
        $profile = PerawatProfile::where('user_id', $userId)->first();
        $pendidikan = PerawatPendidikan::where('user_id', $userId)->orderBy('tahun_lulus', 'desc')->get();
        $pekerjaan = PerawatPekerjaan::where('user_id', $userId)->orderBy('tahun_mulai', 'asc')->get();
        
        // Tentukan Prefix berdasarkan Profesi (Perawat = PK, Bidan = BK)
        $prefix = (strtolower($profile->profesi ?? '') == 'bidan') ? 'BK' : 'PK';

        // 2. MENGHITUNG TOTAL SKP AKTIF (Dari PerawatDataTambahan)
        $hariIni = Carbon::now()->format('Y-m-d');
        
        $totalSkp = (int) PerawatDataTambahan::where('user_id', $userId)
            ->where(function ($query) use ($hariIni) {
                // KONDISI A: Dokumen Biasa (Belum Expired)
                $query->where(function ($q) use ($hariIni) {
                    $q->where(function($q2) {
                        $q2->where('is_lifetime', false)->orWhereNull('is_lifetime');
                    })
                    ->where('tgl_terbit', '<=', $hariIni)
                    ->where('tgl_expired', '>=', $hariIni);
                })
                // KONDISI B: Dokumen Seumur Hidup (Telah di-ACC Admin)
                ->orWhere(function ($q) use ($hariIni) {
                    $q->where('is_lifetime', true)
                      ->where('lifetime_approved', true)
                      ->where('tgl_terbit', '<=', $hariIni);
                });
            })->sum('skp');

        // 3. DETEKSI LEVEL PENDIDIKAN TERTINGGI (D3, S1/D4/Ners, S2/Magister)
        $pendidikanLevel = 'D3'; // Default
        foreach($pendidikan as $p) {
            $namaJurusan = strtolower($p->jurusan . ' ' . $p->jenjang);
            if (str_contains($namaJurusan, 's2') || str_contains($namaJurusan, 'magister')) {
                $pendidikanLevel = 'S2';
                break; 
            } elseif (str_contains($namaJurusan, 's1') || str_contains($namaJurusan, 'd4') || str_contains($namaJurusan, 'ners')) {
                $pendidikanLevel = 'S1';
            }
        }

        // 4. DETEKSI PK/BK TERTINGGI SAAT INI & MASA KERJA
        $totalMasaKerjaKlinis = 0;
        $highestPk = 0;
        $tahunSekarang = (int) date('Y');

        $romanToInt = ['I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4, 'V' => 5];
        $intToRoman = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V'];

        // Cari PK/BK tertinggi dari riwayat pekerjaan menggunakan Regex yang bisa baca PK dan BK
        foreach($pekerjaan as $job) {
            if (preg_match('/(?:PK|BK)\s*(I|II|III|IV|V)\b/i', $job->level_pk, $matches)) {
                $romawi = strtoupper(trim($matches[1]));
                $level = $romanToInt[$romawi] ?? 0;
                
                if ($level > $highestPk) {
                    $highestPk = $level;
                }
            }
        }

        // Hitung masa kerja TOTAL dan masa kerja SPESIFIK di PK/BK tertinggi
        $masaKerjaSebagaiPkSaatIni = 0;
        $highestPkRoman = $highestPk > 0 ? $intToRoman[$highestPk] : '';

        foreach($pekerjaan as $job) {
            $mulai = (int) $job->tahun_mulai;
            $selesai = $job->tahun_selesai ? (int) $job->tahun_selesai : $tahunSekarang;
            $durasi = max(0, $selesai - $mulai); 
            
            $totalMasaKerjaKlinis += $durasi;

            // Hitung durasi khusus saat menjabat PK/BK tertingginya
            if ($highestPk > 0 && preg_match('/(?:PK|BK)\s*('.$highestPkRoman.')\b/i', $job->level_pk)) {
                $masaKerjaSebagaiPkSaatIni += $durasi;
            }
        }

        // 5. RULE ENGINE REKOMENDASI MANUSIAWI
        $reqYears = 0;
        $reqSkp = 0;
        $targetPk = '';
        $isMax = false;
        $currentMasaKerja = 0;

        // Aturan Kenaikan (Berlaku untuk PK maupun BK)
        if ($highestPk == 0) {
            $reqYears = ($pendidikanLevel == 'D3') ? 2 : 1;
            $reqSkp = 5;
            $targetPk = "$prefix I";
            $currentMasaKerja = $totalMasaKerjaKlinis; 
        } elseif ($highestPk == 1) {
            $reqYears = ($pendidikanLevel == 'D3') ? 4 : 3;
            $reqSkp = 10;
            $targetPk = "$prefix II";
            $currentMasaKerja = $masaKerjaSebagaiPkSaatIni;
        } elseif ($highestPk == 2) {
            $reqYears = 4; 
            $reqSkp = 20;
            $targetPk = "$prefix III";
            $currentMasaKerja = $masaKerjaSebagaiPkSaatIni;
        } elseif ($highestPk == 3) {
            $reqYears = ($pendidikanLevel == 'S2') ? 4 : 6; 
            $reqSkp = 40;
            $targetPk = "$prefix IV";
            $currentMasaKerja = $masaKerjaSebagaiPkSaatIni;
        } elseif ($highestPk == 4) {
            $reqYears = ($pendidikanLevel == 'S2') ? 4 : 6;
            $reqSkp = 50;
            $targetPk = "$prefix V";
            $currentMasaKerja = $masaKerjaSebagaiPkSaatIni;
        } else {
            $isMax = true;
        }

        // Susun Pesan Dinamis
        if ($isMax) {
            $status = 'maksimal';
            $rekomendasi = null;
            $pesan = "Luar biasa! Anda telah mencapai jenjang karir klinis tertinggi (<span class='fw-bold text-dark'>$prefix V</span>). Terus pertahankan dedikasi dan kompetensi Anda sebagai expert.";
        } else {
            $kurangTahun = max(0, $reqYears - $currentMasaKerja);
            $kurangSkp = max(0, $reqSkp - $totalSkp);
            $rekomendasi = $targetPk;

            if ($kurangTahun == 0 && $kurangSkp == 0) {
                $status = 'bisa_upgrade';
                $pesan = "Kualifikasi Anda lengkap! Berdasarkan masa kerja (<span class='fw-bold text-dark'>$currentMasaKerja tahun</span>) dan SKP aktif (<span class='fw-bold text-dark'>$totalSkp SKP</span>), Anda siap naik ke <span class='fw-bold text-dark'>$targetPk</span>.";
            } else {
                $status = 'belum_memenuhi';
                $teksAwal = "Anda diproyeksikan naik ke <span class='fw-bold text-dark'>$targetPk</span>. ";
                
                if ($kurangTahun > 0 && $kurangSkp > 0) {
                    $pesan = $teksAwal . "Saat ini Anda masih butuh tambahan masa kerja <span class='fw-bold text-dark'>$kurangTahun tahun</span> lagi dan mengumpulkan <span class='fw-bold text-dark'>$kurangSkp SKP</span> aktif.";
                } elseif ($kurangTahun > 0) {
                    $pesan = $teksAwal . "SKP terpenuhi (<span class='text-success fw-bold'>$totalSkp aktif</span>), tapi Anda masih butuh tambahan masa kerja <span class='fw-bold text-dark'>$kurangTahun tahun</span> lagi.";
                } elseif ($kurangSkp > 0) {
                    $pesan = $teksAwal . "Masa kerja terpenuhi (<span class='text-success fw-bold'>$currentMasaKerja tahun</span>), tapi Anda perlu mengumpulkan <span class='fw-bold text-dark'>$kurangSkp SKP</span> aktif lagi (Terkumpul: $totalSkp dari $reqSkp SKP).";
                }
            }
        }

        return [
            'current_pk' => $highestPk > 0 ? "$prefix $intToRoman[$highestPk]" : "Pra $prefix",
            'pendidikan_level' => $pendidikanLevel,
            'total_tahun_kerja' => $totalMasaKerjaKlinis,
            'tahun_sebagai_pk_current' => $currentMasaKerja,
            'total_skp_aktif' => $totalSkp,
            'req_years' => $reqYears,
            'req_skp' => $reqSkp,
            'rekomendasi' => [
                'status' => $status,
                'target_pk' => $rekomendasi,
                'pesan' => $pesan
            ]
        ];
    }
}