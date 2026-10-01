<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PerawatProfile;
use App\Models\PerawatPendidikan;
use App\Models\PerawatPelatihan;
use App\Models\PerawatPekerjaan;
use App\Models\PerawatKeluarga;
use App\Models\PerawatOrganisasi;
use App\Models\PerawatTandaJasa;
use App\Models\PerawatLisensi;
use App\Models\PerawatStr;
use App\Models\PerawatSip;
use App\Models\PerawatKunjungan;
use App\Models\PerawatDataTambahan;
use Illuminate\Support\Facades\Hash;

class KepegawaianController extends Controller
{
    
    // Tambahkan di bagian atas file, di bawah namespace


// ... di dalam class KepegawaianController tambahkan method ini:

    /**
     * Endpoint untuk verifikasi login dari aplikasi luar
     */
    public function loginPegawai(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // 2. Cari akun perawat berdasarkan email
        $user = User::where('email', $request->email)
                    ->where('role', 'perawat')
                    ->first();

        // 3. Cek apakah user ada dan password cocok
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email atau password salah, atau Anda bukan perawat.'
            ], 401);
        }

        // 4. Jika sukses, kembalikan respon OK beserta NIK untuk modal narik data DRH
        $profile = PerawatProfile::where('user_id', $user->id)->first();

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil',
            'data'    => [
                'user_id' => $user->id,
                'email'   => $user->email,
                'name'    => $user->name,
                'nik'     => $profile ? $profile->nik : null
            ]
        ], 200);
    }
    /**
     * Endpoint untuk mengambil daftar semua pegawai (Hanya role: perawat)
     */
    public function getAllPegawai()
    {
        // Tarik data user yang rolenya HANYA perawat beserta relasi profilnya
        $pegawai = User::where('role', 'perawat')
            ->with('profile:user_id,nik,nama_lengkap,jabatan')
            ->get()
            ->map(function ($user) {
                return [
                    'user_id'      => $user->id,
                    'nik'          => $user->profile->nik ?? null,
                    'nama_lengkap' => $user->profile->nama_lengkap ?? null,
                    'jabatan'      => $user->profile->jabatan ?? null,
                ];
            });
            
        return response()->json([
            'status' => 'success',
            'data'   => $pegawai
        ], 200);
    }

    /**
     * Endpoint untuk mengambil data lengkap 1 pegawai berdasarkan NIK (Hanya role: perawat)
     */
    public function getPegawaiByNik($nik)
    {
        // 1. Cari profil berdasarkan NIK
        $profile = PerawatProfile::where('nik', $nik)->first();

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pegawai tidak ditemukan'
            ], 404);
        }

        $userId = $profile->user_id;

        // 2. Ambil data akun User
        $user = User::find($userId);

        // 3. Validasi Role: Pastikan dia perawat
        if (!$user || $user->role !== 'perawat') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak ditemukan atau akses ditolak (bukan perawat).'
            ], 403);
        }

        // 4. Kumpulkan semua data DRH
        $dataLengkap = $this->susunDataDrh($user, $profile);

        return response()->json([
            'status' => 'success',
            'data'   => $dataLengkap
        ], 200);
    }

    /**
     * Endpoint untuk mengambil data lengkap 1 pegawai berdasarkan Email (Hanya role: perawat)
     */
    public function getPegawaiByEmail($email)
    {
        // 1. Cari data akun User berdasarkan email DAN rolenya harus perawat
        $user = User::where('email', $email)->where('role', 'perawat')->first();

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data pegawai dengan email tersebut tidak ditemukan atau bukan perawat'
            ], 404);
        }

        $userId = $user->id;

        // 2. Ambil profil perawat
        $profile = PerawatProfile::where('user_id', $userId)->first();

        // 3. Kumpulkan semua data DRH
        $dataLengkap = $this->susunDataDrh($user, $profile);

        return response()->json([
            'status' => 'success',
            'data'   => $dataLengkap
        ], 200);
    }

    /**
     * Fungsi helper agar tidak mengulang penulisan array data yang panjang
     */
    private function susunDataDrh($user, $profile)
    {
        $userId = $user->id;

        return [
            'akun' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
            'profile'    => $profile,
            'pendidikan' => PerawatPendidikan::where('user_id', $userId)->orderBy('tahun_lulus', 'desc')->get(),
            'pelatihan'  => PerawatPelatihan::where('user_id', $userId)->orderBy('tanggal_mulai', 'desc')->get(),
            'pekerjaan'  => PerawatPekerjaan::where('user_id', $userId)->orderBy('tahun_mulai', 'desc')->get(),
            'keluarga'   => PerawatKeluarga::where('user_id', $userId)->get(),
            'organisasi' => PerawatOrganisasi::where('user_id', $userId)->orderBy('tahun_mulai', 'desc')->get(),
            'tanda_jasa' => PerawatTandaJasa::where('user_id', $userId)->orderBy('tahun', 'desc')->get(),
            'kunjungan'  => PerawatKunjungan::where('user_id', $userId)->latest()->get(),
            'dokumen'    => [
                'lisensi'  => PerawatLisensi::where('user_id', $userId)->orderBy('tgl_expired', 'desc')->get(),
                'str'      => PerawatStr::where('user_id', $userId)->orderBy('tgl_terbit', 'desc')->get(),
                'sip'      => PerawatSip::where('user_id', $userId)->orderBy('tgl_terbit', 'desc')->get(),
                'tambahan' => PerawatDataTambahan::where('user_id', $userId)->orderBy('tgl_terbit', 'desc')->get(),
            ]
        ];
    }
}