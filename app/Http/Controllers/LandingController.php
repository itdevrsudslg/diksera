<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\PerawatProfile;
use App\Models\PerawatPekerjaan;
use App\Models\PerawatPelatihan;
use App\Models\PerawatLisensi;
use App\Models\PerawatStr;
use App\Models\PerawatSip;
use App\Models\PerawatPendidikan;
use App\Models\PengajuanSertifikat;

class LandingController extends Controller
{
    public function index()
    {
        // 1. Jika user sudah login, langsung arahkan ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // 2. Query data kaya & komprehensif untuk disuplai ke AI Chatbot
        $stats = [
            // Statistik Pengguna & Profil
            'users'               => User::count(),
            'profiles'            => PerawatProfile::count(),
            'perawat_count'       => PerawatProfile::where(function($q) {
                                        $q->where('profesi', 'Perawat')
                                          ->orWhereNull('profesi');
                                    })->count(),
            'bidan_count'         => PerawatProfile::where('profesi', 'Bidan')->count(),

            // Riwayat DRH (Pengalaman & Pengembangan)
            'pelatihan'           => PerawatPelatihan::count(),
            'pekerjaan'           => PerawatPekerjaan::count(),

            // Legalitas & Lisensi Aktif
            'lisensi_aktif'       => PerawatLisensi::where('status', 'active')
                                        ->orWhere('tgl_expired', '>=', now())
                                        ->count(),
            'str_aktif'           => PerawatStr::where('tgl_expired', '>=', now())->count(),
            'sip_aktif'           => PerawatSip::where('tgl_expired', '>=', now())->count(),

            // Demografi Pendidikan
            'pendidikan_d3'       => PerawatPendidikan::where('jenjang', 'LIKE', '%D3%')->count(),
            'pendidikan_s1'       => PerawatPendidikan::where(function($q) {
                                        $q->where('jenjang', 'LIKE', '%S1%')
                                          ->orWhere('jenjang', 'LIKE', '%Profesi%');
                                    })->count(),
            'pendidikan_s2'       => PerawatPendidikan::where('jenjang', 'LIKE', '%S2%')->count(),

            // Status Asesmen & Sertifikasi
            'pengajuan_berjalan'  => PengajuanSertifikat::whereIn('status', ['pending', 'proses', 'wawancara'])->count(),
            'total_terkompetensi' => PengajuanSertifikat::whereIn('status', ['approved', 'kompeten'])->count(),
        ];

        // 3. Kirim data ke view landing
        return view('landing', compact('stats'));
    }
}