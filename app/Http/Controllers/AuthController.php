<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PerawatProfile;
use App\Models\PerawatPendidikan;
use App\Models\PerawatPelatihan;
use App\Models\PerawatPekerjaan;
use App\Models\PerawatTandaJasa;
use App\Models\PerawatKeluarga;
use App\Models\PerawatOrganisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
class AuthController extends Controller
{
    
    public function showForgetPasswordForm()
{
    return view('auth.passwords.email');
}

// 2. Kirim Link Reset ke Email
public function sendResetLinkEmail(Request $request)
{
    $request->validate(['email' => 'required|email|exists:users,email']);

    $status = Password::sendResetLink($request->only('email'));

    if ($status === Password::RESET_LINK_SENT) {
        return back()->with('swal', [
            'icon' => 'success',
            'title' => 'Email Terkirim',
            'text' => 'Kami telah mengirimkan link reset password ke email Anda.',
        ]);
    }

    return back()->withErrors(['email' => __($status)]);
}

// 3. Tampilkan Form Password Baru (dari Link Email)
public function showResetPasswordForm($token, Request $request)
{
    return view('auth.passwords.reset')->with(['token' => $token, 'email' => $request->email]);
}

// 4. Proses Update Password Baru
public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));
            $user->save();
        }
    );

    if ($status === Password::PASSWORD_RESET) {
        return redirect()->route('login')->with('swal', [
            'icon' => 'success',
            'title' => 'Password Diperbarui',
            'text' => 'Silakan masuk dengan password baru Anda.',
        ]);
    }

    return back()->withErrors(['email' => [__($status)]]);
}
    // =========================================================================
    // LOGIN
    // =========================================================================
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Cek Status Akun (Kecuali Admin/Pewawancara)
            if (!in_array($user->role, ['admin', 'pewawancara']) && $user->status_akun !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $pesanError = 'Akun Anda sedang menunggu persetujuan Admin. Mohon tunggu verifikasi.';
                if ($user->status_akun === 'rejected') {
                    $pesanError = 'Mohon maaf, pendaftaran akun Anda telah ditolak oleh Admin.';
                }

                return back()->withInput()->with('swal', [
                    'icon'  => 'warning',
                    'title' => 'Akses Dibatasi',
                    'text'  => $pesanError,
                ]);
            }

            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->route('dashboard.admin')->with('swal', ['icon' => 'success', 'title' => 'Berhasil masuk', 'text' => 'Selamat datang Admin.']);
            }
            if ($user->role === 'pewawancara') {
                return redirect()->route('dashboard.pewawancara')->with('swal', ['icon' => 'success', 'title' => 'Login Berhasil', 'text' => 'Selamat bertugas.']);
            }

            return redirect()->route('dashboard')->with('swal', [
                'icon'  => 'success',
                'title' => 'Berhasil masuk',
                'text'  => 'Selamat datang di DIKSERA, ' . $user->name . '.',
            ]);
        }

        return back()->withErrors(['email' => 'Email atau password tidak sesuai.'])->withInput()->with('swal', [
            'icon'  => 'error',
            'title' => 'Gagal masuk',
            'text'  => 'Email atau password tidak cocok.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing')->with('swal', [
            'icon'  => 'success',
            'title' => 'Berhasil keluar',
            'text'  => 'Anda telah keluar dari DIKSERA.',
        ]);
    }

    // =========================================================================
    // REGISTRASI PERAWAT
    // =========================================================================
    public function showPerawatRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register-perawat');
    }

    public function registerPerawat(Request $request)
    {
        // 1. LOGIKA VALIDASI DINAMIS (NIP vs NIRP)
        $status   = $request->status_kepegawaian;
        $isNonAsn = str_contains($status, 'NON ASN'); // True jika ada kata "NON ASN"
        $isAsn    = str_contains($status, 'ASN') && !$isNonAsn; // True jika "ASN" tapi bukan "NON"

        $request->validate([
            // --- Step 1: Akun & Identitas ---
            'name'                => 'required|string|max:150',
            'email'               => 'required|email|unique:users,email',
            'password'            => 'required|string|min:6|confirmed',
            'nik'                 => 'required|string|max:30',
            
            // LOGIKA BARU DI SINI
            'profesi'             => 'required|in:Perawat,Bidan',
            'status_kepegawaian'  => 'required|string',
            'jabatan'             => 'required|string|max:150', // Jabatan wajib diisi (dropdown otomatis)

            // NIP Wajib jika ASN, NIRP Wajib jika NON ASN
            'nip'                 => $isAsn ? 'required|string|max:50' : 'nullable|string|max:50',
            'nirp'                => $isNonAsn ? 'required|string|max:50' : 'nullable|string|max:50',

            'tempat_lahir'        => 'nullable|string|max:100',
            'tanggal_lahir'       => 'nullable|date',
            'jenis_kelamin'       => 'nullable|string|max:20',
            'agama'               => 'nullable|string|max:30',
            'status_perkawinan'   => 'nullable|string|max:50',
            'pangkat'             => 'nullable|string|max:50',
            'golongan'            => 'nullable|string|max:20',
            'hobby'               => 'nullable|string|max:150',

            // --- Step 2: Kontak & Fisik ---
            'no_hp'               => 'required|string|max:30',
            'alamat_jalan'        => 'required|string|max:150',
            'alamat_kelurahan'    => 'required|string|max:100',
            'alamat_kecamatan'    => 'required|string|max:100',
            'alamat_kabkota'      => 'required|string|max:100',
            'alamat_provinsi'     => 'required|string|max:100',
            'golongan_darah'      => 'nullable|string|max:100',
            'tinggi_badan'        => 'nullable|integer',
            'berat_badan'         => 'nullable|integer',
            'rambut'              => 'nullable|string|max:100',
            'bentuk_muka'         => 'nullable|string|max:100',
            'warna_kulit'         => 'nullable|string|max:100',
            'ciri_khas'           => 'nullable|string|max:150',
            'cacat_tubuh'         => 'nullable|string|max:150',
            'foto_3x4'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            // --- Step 3 & 4: Array Data ---
            'pendidikan_jenjang.*'=> 'nullable|string|max:50',
            'pendidikan_nama.*'   => 'nullable|string|max:150',
            'pendidikan_file.*'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',

            'pelatihan_nama.*'    => 'nullable|string|max:150',
            'pelatihan_file.*'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',

            'pekerjaan_instansi.*'=> 'nullable|string|max:150',
            'pekerjaan_file.*'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',

            'keluarga_nama.*'     => 'nullable|string|max:150',

            'organisasi_nama.*'   => 'nullable|string|max:150',
            'organisasi_file.*'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',

            'tandajasa_nama.*'    => 'nullable|string|max:150',
            'tandajasa_file.*'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        DB::beginTransaction();

        try {
            // 2. BUAT USER
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'perawat', 
                // status_akun default 'pending' dari database migration
            ]);

            // 3. HANDLE FOTO UTAMA
            $fotoPath = null;
            if ($request->hasFile('foto_3x4')) {
                $fotoPath = $request->file('foto_3x4')->store('perawat/foto', 'public');
            }

            // 4. GABUNG ALAMAT
            $alamatGabungan = trim(
                ($request->alamat_jalan ?? '') . ', Kel. ' . ($request->alamat_kelurahan ?? '') .
                ', Kec. ' . ($request->alamat_kecamatan ?? '') . ', ' . ($request->alamat_kabkota ?? '') .
                ', ' . ($request->alamat_provinsi ?? '')
            );

            // 5. BUAT PROFILE (UPDATE LOGIKA)
            PerawatProfile::create([
                'user_id'            => $user->id,
                'nik'                => $request->nik,
                
                // DATA BARU
                'profesi'            => $request->profesi,
                'status_kepegawaian' => $request->status_kepegawaian,
                // Kondisional: Jika ASN simpan NIP (NIRP null), Jika Non ASN simpan NIRP (NIP null)
                'nip'                => $isAsn ? $request->nip : null,
                'nirp'               => $isNonAsn ? $request->nirp : null,
                
                'nama_lengkap'       => $request->name,
                'jabatan'            => $request->jabatan,
                'pangkat'            => $request->pangkat,
                'golongan'           => $request->golongan,
                
                'tempat_lahir'       => $request->tempat_lahir,
                'tanggal_lahir'      => $request->tanggal_lahir,
                'jenis_kelamin'      => $request->jenis_kelamin,
                'agama'              => $request->agama,
                'status_perkawinan'  => $request->status_perkawinan,
                
                'alamat'             => $alamatGabungan ?: null,
                'kota'               => $request->alamat_kabkota,
                'no_hp'              => $request->no_hp,
                
                'golongan_darah'     => $request->golongan_darah,
                'tinggi_badan'       => $request->tinggi_badan,
                'berat_badan'        => $request->berat_badan,
                'rambut'             => $request->rambut,
                'bentuk_muka'        => $request->bentuk_muka,
                'warna_kulit'        => $request->warna_kulit,
                'ciri_khas'          => $request->ciri_khas,
                'cacat_tubuh'        => $request->cacat_tubuh,
                'hobby'              => $request->hobby,
                'foto_3x4'           => $fotoPath,

                'type_perawat'       => $request->type_perawat ?? null,
            ]);

            // =========================================================
            // 6. SIMPAN DATA RELASI (Verbose Mode - Lengkap & Aman)
            // =========================================================
            
            // --- A. PENDIDIKAN ---
            $pendFiles = $request->file('pendidikan_file', []);
            $jenjangs  = $request->pendidikan_jenjang ?? [];
            $p_nama    = $request->pendidikan_nama ?? [];
            $p_akre    = $request->pendidikan_akreditasi ?? [];
            $p_tempat  = $request->pendidikan_tempat ?? [];

            foreach ($jenjangs as $i => $jenjang) {
                $nama   = $p_nama[$i] ?? null;
                $akre   = $p_akre[$i] ?? null;
                $tempat = $p_tempat[$i] ?? null;

                $filePath = null;
                if (is_array($pendFiles) && array_key_exists($i, $pendFiles) && $pendFiles[$i]) {
                    $filePath = $pendFiles[$i]->store('perawat/pendidikan', 'public');
                }

                if (!$jenjang && !$nama && !$akre && !$tempat && !$filePath) continue;

                PerawatPendidikan::create([
                    'user_id'        => $user->id,
                    'jenjang'        => $jenjang,
                    'nama_institusi' => $nama,
                    'akreditasi'     => $akre,
                    'tempat'         => $tempat,
                    'dokumen_path'   => $filePath,
                ]);
            }

            // --- B. PELATIHAN ---
            $pelatFiles = $request->file('pelatihan_file', []);
            $pel_nama   = $request->pelatihan_nama ?? [];
            $pel_durasi = $request->pelatihan_durasi ?? [];
            $pel_mulai  = $request->pelatihan_mulai ?? [];
            $pel_selesai= $request->pelatihan_selesai ?? [];

            foreach ($pel_nama as $i => $namaPel) {
                $dur   = $pel_durasi[$i] ?? null;
                $mulai = $pel_mulai[$i] ?? null;
                $sel   = $pel_selesai[$i] ?? null;

                $filePath = null;
                if (is_array($pelatFiles) && array_key_exists($i, $pelatFiles) && $pelatFiles[$i]) {
                    $filePath = $pelatFiles[$i]->store('perawat/pelatihan', 'public');
                }

                if (!$namaPel && !$dur && !$mulai && !$sel && !$filePath) continue;

                PerawatPelatihan::create([
                    'user_id'         => $user->id,
                    'nama_pelatihan'  => $namaPel,
                    'durasi'          => $dur,
                    'tanggal_mulai'   => $mulai,
                    'tanggal_selesai' => $sel,
                    'dokumen_path'    => $filePath,
                ]);
            }

            // --- C. PEKERJAAN ---
            $pekFiles = $request->file('pekerjaan_file', []);
            $instansi = $request->pekerjaan_instansi ?? [];
            $jabatan  = $request->pekerjaan_jabatan ?? [];
            $mulai    = $request->pekerjaan_mulai ?? [];
            $selesai  = $request->pekerjaan_selesai ?? [];

            foreach ($instansi as $i => $ins) {
                $jab = $jabatan[$i] ?? null;
                $m   = $mulai[$i] ?? null;
                $s   = $selesai[$i] ?? null;

                $filePath = null;
                if (is_array($pekFiles) && array_key_exists($i, $pekFiles) && $pekFiles[$i]) {
                    $filePath = $pekFiles[$i]->store('perawat/pekerjaan', 'public');
                }

                if (!$ins && !$jab && !$m && !$s && !$filePath) continue;

                PerawatPekerjaan::create([
                    'user_id'         => $user->id,
                    'nama_instansi'   => $ins,
                    'jabatan'         => $jab,
                    'tanggal_mulai'   => $m,
                    'tanggal_selesai' => $s,
                    'dokumen_path'    => $filePath,
                ]);
            }

            // --- D. KELUARGA ---
            $kHub  = $request->keluarga_hubungan ?? [];
            $kNama = $request->keluarga_nama ?? [];
            $kTTL  = $request->keluarga_ttl ?? [];
            $kPek  = $request->keluarga_pekerjaan ?? [];

            foreach ($kNama as $i => $namaKel) {
                $hub = $kHub[$i] ?? null;
                $ttl = $kTTL[$i] ?? null;
                $pek = $kPek[$i] ?? null;

                if (!$hub && !$namaKel && !$ttl && !$pek) continue;

                PerawatKeluarga::create([
                    'user_id'   => $user->id,
                    'hubungan'  => $hub,
                    'nama'      => $namaKel,
                    'ttl'       => $ttl,
                    'pekerjaan' => $pek,
                ]);
            }

            // --- E. ORGANISASI ---
            $orgFiles = $request->file('organisasi_file', []);
            $oNama    = $request->organisasi_nama ?? [];
            $oJab     = $request->organisasi_jabatan ?? [];
            $oMulai   = $request->organisasi_mulai ?? [];
            $oSelesai = $request->organisasi_selesai ?? [];

            foreach ($oNama as $i => $namaOrg) {
                $jab = $oJab[$i] ?? null;
                $m   = $oMulai[$i] ?? null;
                $s   = $oSelesai[$i] ?? null;

                $filePath = null;
                if (is_array($orgFiles) && array_key_exists($i, $orgFiles) && $orgFiles[$i]) {
                    $filePath = $orgFiles[$i]->store('perawat/organisasi', 'public');
                }

                if (!$namaOrg && !$jab && !$m && !$s && !$filePath) continue;

                PerawatOrganisasi::create([
                    'user_id'         => $user->id,
                    'nama_organisasi' => $namaOrg,
                    'jabatan'         => $jab,
                    'tanggal_mulai'   => $m,
                    'tanggal_selesai' => $s,
                    'dokumen_path'    => $filePath,
                ]);
            }

            // --- F. TANDA JASA ---
            $tjFiles = $request->file('tandajasa_file', []);
            $tNama   = $request->tandajasa_nama ?? [];
            $tIns    = $request->tandajasa_instansi ?? [];
            $tThn    = $request->tandajasa_tahun ?? [];

            foreach ($tNama as $i => $namaJasa) {
                $ins = $tIns[$i] ?? null;
                $thn = $tThn[$i] ?? null;

                $filePath = null;
                if (is_array($tjFiles) && array_key_exists($i, $tjFiles) && $tjFiles[$i]) {
                    $filePath = $tjFiles[$i]->store('perawat/tandajasa', 'public');
                }

                if (!$namaJasa && !$ins && !$thn && !$filePath) continue;

                PerawatTandaJasa::create([
                    'user_id'          => $user->id,
                    'nama_penghargaan' => $namaJasa,
                    'instansi_pemberi' => $ins,
                    'tahun'            => $thn,
                    'dokumen_path'     => $filePath,
                ]);
            }

            DB::commit();

            return redirect()->route('login')->with('swal', [
                'icon'  => 'success',
                'title' => 'Registrasi Berhasil',
                'text'  => 'Data Anda telah tersimpan. Mohon tunggu verifikasi Admin.',
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();

            if (config('app.debug')) {
                return back()->withInput()->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Terjadi kesalahan sistem',
                    'text'  => $th->getMessage(),
                ]);
            }

            return back()->withInput()->with('swal', [
                'icon'  => 'error',
                'title' => 'Terjadi kesalahan',
                'text'  => 'Silakan cek kembali isian Anda atau coba beberapa saat lagi.',
            ]);
        }
    }
}