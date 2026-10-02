<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;
use App\Models\PerawatDataTambahan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Log;
use App\Services\PkRecommendationService; 
use App\Models\PerawatKunjungan;

class PerawatDrhController extends Controller
{
    protected function currentPerawat()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'perawat') {
            return null;
        }
        return $user;
    }
public function downloadPdf()
{
    $user = $this->currentPerawat();
    if (!$user) return redirect('/');

    // Ambil data lengkap
    $profile    = PerawatProfile::where('user_id', $user->id)->first();
    $pendidikan = PerawatPendidikan::where('user_id', $user->id)->orderBy('tahun_lulus', 'desc')->get();
    $pelatihan  = PerawatPelatihan::where('user_id', $user->id)->orderBy('tanggal_mulai', 'desc')->get();
    $pekerjaan  = PerawatPekerjaan::where('user_id', $user->id)->orderBy('tahun_mulai', 'desc')->get();
    $keluarga   = PerawatKeluarga::where('user_id', $user->id)->get();
    $organisasi = PerawatOrganisasi::where('user_id', $user->id)->orderBy('tahun_mulai', 'desc')->get();
    $tandajasa  = PerawatTandaJasa::where('user_id', $user->id)->orderBy('tahun', 'desc')->get();
    $kunjungan  = PerawatKunjungan::where('user_id', $user->id)->get(); // Data baru
    $str        = PerawatStr::where('user_id', $user->id)->orderBy('tgl_terbit', 'desc')->get();
    $sip        = PerawatSip::where('user_id', $user->id)->orderBy('tgl_terbit', 'desc')->get();

    $data = compact(
        'user', 'profile', 'pendidikan', 'pelatihan', 'pekerjaan', 
        'keluarga', 'organisasi', 'tandajasa', 'kunjungan', 'str', 'sip'
    );

    $pdf = Pdf::loadView('perawat.drh.pdf', $data);
    $pdf->setPaper('legal', 'portrait');

    $fileName = 'DRH_BKN_' . str_replace(' ', '_', $profile->nama_lengkap ?? $user->name) . '.pdf';
    return $pdf->download($fileName);
}
    /* ============ DRH SUMMARY (READ ONLY) ============ */
   public function index(PkRecommendationService $pkService) // <-- Inject Service di sini
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $profile     = PerawatProfile::where('user_id', $user->id)->first();
        $pendidikan  = PerawatPendidikan::where('user_id', $user->id)->orderBy('tahun_lulus', 'desc')->get();
        $pelatihan   = PerawatPelatihan::where('user_id', $user->id)->orderBy('tanggal_mulai', 'desc')->get();
        $pekerjaan   = PerawatPekerjaan::where('user_id', $user->id)->orderBy('tahun_mulai', 'desc')->get();
        $keluarga    = PerawatKeluarga::where('user_id', $user->id)->get();
        $organisasi  = PerawatOrganisasi::where('user_id', $user->id)->orderBy('tahun_mulai', 'desc')->get();
        $tandajasa   = PerawatTandaJasa::where('user_id', $user->id)->orderBy('tahun', 'desc')->get();

        // Panggil Service Rekomendasi PK
        $rekomendasiPk = $pkService->getRecommendation($user->id);

        return view('perawat.drh.index', compact(
            'user',
            'profile',
            'pendidikan',
            'pelatihan',
            'pekerjaan',
            'keluarga',
            'organisasi',
            'tandajasa',
            'rekomendasiPk' // <-- Passing variabel ini ke view
        ));
    }
    /* ============ IDENTITAS (Updated) ============ */
    public function editIdentitas()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        // Buat data dummy jika belum ada, agar form tidak error
        $profile = PerawatProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['nama_lengkap' => $user->name]
        );

        return view('perawat.drh.identitas', compact('user', 'profile'));
    }

    public function updateIdentitas(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        // Validasi disesuaikan dengan Migration agar lengkap
        $request->validate([
            // Identitas Utama
            'nama_lengkap'      => 'required|string|max:150',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'type_perawat' => 'required|array|min:2',
            'nik'               => 'nullable|string|max:30',
            'nip'               => 'nullable|string|max:50',
            'nirp'              => 'nullable|string|max:50',
'jabatan_struktural' => 'required|string',
            // Biodata
            'tempat_lahir'      => 'nullable|string|max:100',
            'tanggal_lahir'     => 'nullable|date',
            'jenis_kelamin'     => 'nullable|string|in:L,P',
            'agama'             => 'nullable|string|max:30',
            'aliran_kepercayaan' => 'nullable|string|max:100', // Added
            'status_perkawinan' => 'nullable|string|max:50',
            'npwp' => 'nullable|string|max:50',
'tmt_cpns' => 'nullable|date',
'tmt_pns' => 'nullable|date',
'tmt_jabatan' => 'nullable|date',
'masa_kerja_tahun' => 'nullable|integer',
'masa_kerja_bulan' => 'nullable|integer',
'kelurahan_desa' => 'nullable|string|max:100',
'kecamatan' => 'nullable|string|max:100',
'skck_pejabat' => 'nullable|string|max:150',
'skck_nomor' => 'nullable|string|max:100',
'skck_tanggal' => 'nullable|date',
'sehat_pejabat' => 'nullable|string|max:150',
'sehat_nomor' => 'nullable|string|max:100',
'sehat_tanggal' => 'nullable|date',
'ket_lain_pejabat' => 'nullable|string|max:150',
'ket_lain_nomor' => 'nullable|string|max:100',
'ket_lain_tanggal' => 'nullable|date',
            // Kontak
            'no_hp'             => 'nullable|string|max:30',
            'alamat'            => 'nullable|string|max:255',
            'kota'              => 'nullable|string|max:100',

            // Jabatan
            'jabatan'           => 'nullable|string|max:100',
            'pangkat'           => 'nullable|string|max:50',
            'golongan'          => 'nullable|string|max:20',

            // Fisik
            'tinggi_badan'      => 'nullable|integer',
            'berat_badan'       => 'nullable|integer',
            'golongan_darah'    => 'nullable|string|max:5',
            'rambut'            => 'nullable|string|max:100', // Adjusted size
            'bentuk_muka'       => 'nullable|string|max:100', // Adjusted size
            'warna_kulit'       => 'nullable|string|max:100', // Adjusted size
            'ciri_khas'         => 'nullable|string|max:150', // Adjusted size
            'cacat_tubuh'       => 'nullable|string|max:150', // Adjusted size

            // Lainnya
            'hobby'             => 'nullable|string|max:150', // Added
            'foto_3x4'          => 'nullable|image|max:2048', // Max 2MB
        ]);
$user->update([
        'email' => $request->email,
        'name'  => $request->nama_lengkap // Opsional: sinkronkan nama di tabel users
    ]);
        $profile = PerawatProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['nama_lengkap' => $user->name]
        );
    $types = $request->type_perawat;
    $hasKfk = false;
    $hasJenjang = false;

    foreach ($types as $item) {
        if (str_contains($item, 'KFK')) $hasKfk = true;
        if (str_contains($item, 'PK') || str_contains($item, 'BK')) $hasJenjang = true;
    }

    if (!$hasKfk || !$hasJenjang) {
        return back()->withInput()->with('swal', [
            'icon' => 'error',
            'title' => 'Kombinasi Tidak Valid',
            'text' => 'Jenjang Klinis harus terdiri dari 1 KFK dan 1 Level (PK/BK).'
        ]);
    }
        // Ambil semua input kecuali foto & token
        $data = $request->except(['email', 'foto_3x4', '_token', '_method']);

        // Handle Upload Foto
        if ($request->hasFile('foto_3x4')) {
            // Hapus foto lama jika ada (opsional, untuk hemat storage)
            if ($profile->foto_3x4 && Storage::disk('public')->exists($profile->foto_3x4)) {
                Storage::disk('public')->delete($profile->foto_3x4);
            }
            $data['foto_3x4'] = $request->file('foto_3x4')->store('perawat/foto', 'public');
        }

        $profile->update($data);

        return redirect()->route('perawat.drh')->with('swal', [
            'icon'  => 'success',
            'title' => 'Identitas Diperbarui',
            'text'  => 'Data profil lengkap berhasil disimpan.',
        ]);
    }

    /* ============ DATA LENGKAP ============ */
    public function showDataLengkap()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $profile    = PerawatProfile::where('user_id', $user->id)->first();

        // Mengambil semua data relasi
        $pendidikan = PerawatPendidikan::where('user_id', $user->id)->orderBy('tahun_lulus', 'desc')->get();
        $pelatihan  = PerawatPelatihan::where('user_id', $user->id)->orderBy('tanggal_mulai', 'desc')->get();
        $pekerjaan  = PerawatPekerjaan::where('user_id', $user->id)->orderBy('tahun_mulai', 'desc')->get();
        $keluarga   = PerawatKeluarga::where('user_id', $user->id)->get(); // Biasanya tidak butuh order khusus
        $organisasi = PerawatOrganisasi::where('user_id', $user->id)->orderBy('tahun_mulai', 'desc')->get();
        $tandajasa  = PerawatTandaJasa::where('user_id', $user->id)->orderBy('tahun', 'desc')->get();

        return view('perawat.drh.data-lengkap', compact(
            'user',
            'profile',
            'pendidikan',
            'pelatihan',
            'pekerjaan',
            'keluarga',
            'organisasi',
            'tandajasa'
        ));
    }

    /* ============ PENDIDIKAN ============ */
    public function pendidikanIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pendidikan = PerawatPendidikan::where('user_id', $user->id)->orderBy('tahun_lulus', 'desc')->get();
        return view('perawat.pendidikan.index', compact('user', 'pendidikan'));
    }

    public function pendidikanCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.pendidikan.create', compact('user'));
    }

    public function pendidikanStore(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $request->validate([
            'jenjang'        => 'required|string|max:50',
            'nama_institusi' => 'required|string|max:150',
            'akreditasi'     => 'nullable|string|max:50',
            'tempat'         => 'nullable|string|max:100',
            'tahun_lulus'    => 'nullable|digits:4',
            'jurusan'        => 'nullable|string|max:150',
            'tahun_masuk'    => 'nullable|digits:4',
            'dokumen'        => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
            'nomor_ijazah'   => 'nullable|string|max:100',
        ]);

        $data = $request->except(['dokumen', '_token']);
        $data['user_id'] = $user->id;

        if ($request->hasFile('dokumen')) {
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/pendidikan', 'public');
        }

        PerawatPendidikan::create($data);

        return redirect()->route('perawat.pendidikan.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Pendidikan ditambahkan.'
        ]);
    }

    public function pendidikanEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pendidikan = PerawatPendidikan::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.pendidikan.edit', compact('user', 'pendidikan'));
    }

    public function pendidikanUpdate(Request $request, $id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pendidikan = PerawatPendidikan::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'jenjang'        => 'required|string|max:50',
            'nama_institusi' => 'required|string|max:150',
            'akreditasi'     => 'nullable|string|max:50',
            'tempat'         => 'nullable|string|max:100',
            'tahun_lulus'    => 'nullable|digits:4',
            'jurusan'        => 'nullable|string|max:150',
            'tahun_masuk'    => 'nullable|digits:4',
            'dokumen'        => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
            'nomor_ijazah'   => 'nullable|string|max:100',
        ]);

        $data = $request->except(['dokumen', '_token', '_method']);

        if ($request->hasFile('dokumen')) {
            if ($pendidikan->dokumen_path && Storage::disk('public')->exists($pendidikan->dokumen_path)) {
                Storage::disk('public')->delete($pendidikan->dokumen_path);
            }
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/pendidikan', 'public');
        }

        $pendidikan->update($data);

        return redirect()->route('perawat.pendidikan.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Pendidikan diperbarui.'
        ]);
    }

    public function pendidikanDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pendidikan = PerawatPendidikan::where('user_id', $user->id)->findOrFail($id);

        if ($pendidikan->dokumen_path && Storage::disk('public')->exists($pendidikan->dokumen_path)) {
            Storage::disk('public')->delete($pendidikan->dokumen_path);
        }
        $pendidikan->delete();

        return redirect()->route('perawat.pendidikan.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Pendidikan dihapus.'
        ]);
    }

    /* ============ PELATIHAN ============ */
public function pelatihanIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        
        $pelatihan = PerawatPelatihan::where('user_id', $user->id)
                        ->orderBy('tanggal_mulai', 'desc')
                        ->get();
        
        $skpAktif = 0;
        $skpNonAktif = 0;

        foreach ($pelatihan as $item) {
            // 1. Jadikan tanggal_selesai sebagai patokan. 
            // Jika kosong (misal pelatihan cuma 1 hari), gunakan tanggal_mulai.
            $tanggalPatokan = $item->tanggal_selesai ?? $item->tanggal_mulai;

            if ($tanggalPatokan) {
                // 2. Masa berlaku SKP umumnya 5 tahun dari tanggal selesai pelatihan
                $batasKadaluarsa = \Carbon\Carbon::parse($tanggalPatokan)->addYears(5);

                // 3. Jika hari ini (now) masih kurang dari atau sama dengan batas kadaluarsa -> AKTIF
                if (now()->lessThanOrEqualTo($batasKadaluarsa)) {
                    $skpAktif += (float) $item->skp;
                } else {
                    $skpNonAktif += (float) $item->skp;
                }
            }
        }

        return view('perawat.pelatihan.index', compact('user', 'pelatihan', 'skpAktif', 'skpNonAktif'));
    }

    public function pelatihanCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.pelatihan.create', compact('user'));
    }

    public function pelatihanStore(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

       // Di dalam method pelatihanStore & pelatihanUpdate
$request->validate([
    'nama_pelatihan'  => 'required|string|max:150',
    'penyelenggara'   => 'nullable|string|max:150',
    'tempat'          => 'nullable|string|max:100',
    'durasi'          => 'nullable|string|max:50',
    'tanggal_mulai'   => 'nullable|date',
    'tanggal_selesai' => 'nullable|date',
    'skp'             => 'nullable|integer|min:0', // <-- Validasi SKP
    'dokumen'         => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
]);

        $data = $request->except(['dokumen', '_token']);
        $data['user_id'] = $user->id;
        $data['skp'] = $request->input('skp') ?? 0;

        if ($request->hasFile('dokumen')) {
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/pelatihan', 'public');
        }

        PerawatPelatihan::create($data);

        return redirect()->route('perawat.pelatihan.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Pelatihan ditambahkan.'
        ]);
    }

    public function pelatihanEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pelatihan = PerawatPelatihan::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.pelatihan.edit', compact('user', 'pelatihan'));
    }

    public function pelatihanUpdate(Request $request, $id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pelatihan = PerawatPelatihan::where('user_id', $user->id)->findOrFail($id);

      // Di dalam method pelatihanStore & pelatihanUpdate
$request->validate([
    'nama_pelatihan'  => 'required|string|max:150',
    'penyelenggara'   => 'nullable|string|max:150',
    'tempat'          => 'nullable|string|max:100',
    'durasi'          => 'nullable|string|max:50',
    'tanggal_mulai'   => 'nullable|date',
    'tanggal_selesai' => 'nullable|date',
    'skp'             => 'nullable|integer|min:0', // <-- Validasi SKP
    'dokumen'         => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
]);

        $data = $request->except(['dokumen', '_token', '_method']);
        $data['skp'] = $request->input('skp') ?? 0;

        if ($request->hasFile('dokumen')) {
            if ($pelatihan->dokumen_path && Storage::disk('public')->exists($pelatihan->dokumen_path)) {
                Storage::disk('public')->delete($pelatihan->dokumen_path);
            }
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/pelatihan', 'public');
        }

        $pelatihan->update($data);

        return redirect()->route('perawat.pelatihan.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Pelatihan diperbarui.'
        ]);
    }

    public function pelatihanDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pelatihan = PerawatPelatihan::where('user_id', $user->id)->findOrFail($id);

        if ($pelatihan->dokumen_path && Storage::disk('public')->exists($pelatihan->dokumen_path)) {
            Storage::disk('public')->delete($pelatihan->dokumen_path);
        }
        $pelatihan->delete();

        return redirect()->route('perawat.pelatihan.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Pelatihan dihapus.'
        ]);
    }

    /* ============ PEKERJAAN ============ */
    public function pekerjaanIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pekerjaan = PerawatPekerjaan::where('user_id', $user->id)->orderBy('tahun_mulai', 'desc')->get();
        return view('perawat.pekerjaan.index', compact('user', 'pekerjaan'));
    }

    public function pekerjaanCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.pekerjaan.create', compact('user'));
    }

    public function pekerjaanStore(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $request->validate([
            'nama_instansi' => 'required|string|max:150',
            'unit_kerja'    => 'required|string|max:150',
            'jabatan'       => 'required|string|max:150',
            'tahun_mulai'   => 'nullable|string|max:4',
            'tahun_selesai' => 'nullable|string|max:4',
            'keterangan'    => 'nullable|string|max:255',
            'level_pk' => 'nullable|string|max:50',
            'gol_ruang' => 'nullable|string|max:50',
'gaji_pokok' => 'nullable|string|max:100',
'pejabat_sk' => 'nullable|string|max:150',
'nomor_sk' => 'nullable|string|max:100',
'tanggal_sk' => 'nullable|date',
            'dokumen'       => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
            'pangkat' => 'nullable|string|max:50',
            'status_kepegawaian' => 'required|string',
        ]);

        $data = $request->except(['dokumen', '_token']);
        $data['user_id'] = $user->id;

        if ($request->hasFile('dokumen')) {
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/pekerjaan', 'public');
        }

        PerawatPekerjaan::create($data);

        return redirect()->route('perawat.pekerjaan.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Riwayat pekerjaan ditambahkan.'
        ]);
    }

    public function pekerjaanEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pekerjaan = PerawatPekerjaan::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.pekerjaan.edit', compact('user', 'pekerjaan'));
    }

    public function pekerjaanUpdate(Request $request, $id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pekerjaan = PerawatPekerjaan::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'nama_instansi' => 'required|string|max:150',
            'unit_kerja'    => 'required|string|max:150',
            'jabatan'       => 'required|string|max:150',
            'tahun_mulai'   => 'nullable|string|max:4',
            'tahun_selesai' => 'nullable|string|max:4',
            'keterangan'    => 'nullable|string|max:255',
            'dokumen'       => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
            'pangkat' => 'nullable|string|max:50',
            'level_pk' => 'nullable|string|max:50',
            'gol_ruang' => 'nullable|string|max:50',
'gaji_pokok' => 'nullable|string|max:100',
'pejabat_sk' => 'nullable|string|max:150',
'nomor_sk' => 'nullable|string|max:100',
'tanggal_sk' => 'nullable|date',
            'status_kepegawaian' => 'required|string',
        ]);

        $data = $request->except(['dokumen', '_token', '_method']);

        if ($request->hasFile('dokumen')) {
            if ($pekerjaan->dokumen_path && Storage::disk('public')->exists($pekerjaan->dokumen_path)) {
                Storage::disk('public')->delete($pekerjaan->dokumen_path);
            }
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/pekerjaan', 'public');
        }

        $pekerjaan->update($data);

        return redirect()->route('perawat.pekerjaan.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Riwayat pekerjaan diperbarui.'
        ]);
    }

    public function pekerjaanDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $pekerjaan = PerawatPekerjaan::where('user_id', $user->id)->findOrFail($id);

        if ($pekerjaan->dokumen_path && Storage::disk('public')->exists($pekerjaan->dokumen_path)) {
            Storage::disk('public')->delete($pekerjaan->dokumen_path);
        }
        $pekerjaan->delete();

        return redirect()->route('perawat.pekerjaan.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Riwayat pekerjaan dihapus.'
        ]);
    }

    /* ============ KELUARGA ============ */
    public function keluargaIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $keluarga = PerawatKeluarga::where('user_id', $user->id)->get();
        return view('perawat.keluarga.index', compact('user', 'keluarga'));
    }

    public function keluargaCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.keluarga.create', compact('user'));
    }

    public function keluargaStore(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $request->validate([
    'hubungan'      => 'required|string|max:50',
    'nama'          => 'required|string|max:150',
    'tanggal_lahir' => 'nullable|date',
    'pekerjaan'     => 'nullable|string|max:150',
    'no_darurat'    => 'nullable|string|max:20',
    'tempat_lahir'  => 'nullable|string|max:100', // BARU
    'jenis_kelamin' => 'nullable|in:L,P', // BARU
    'tanggal_nikah' => 'nullable|date', // BARU
    'keterangan'    => 'nullable|string|max:255',
        ]);

        // Tambahkan 'no_darurat' ke request->only
       $data = $request->except(['_token', '_method']);
$data['user_id'] = $user->id;

        PerawatKeluarga::create($data);

        return redirect()->route('perawat.keluarga.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Data keluarga ditambahkan.'
        ]);
    }

    public function keluargaEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $keluarga = PerawatKeluarga::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.keluarga.edit', compact('user', 'keluarga'));
    }

    public function keluargaUpdate(Request $request, $id)
    {
        // 1. DEFINISI USER (Ini yang menyebabkan error sebelumnya jika hilang)
        $user = $this->currentPerawat();

        // 2. Cek jika user tidak ditemukan
        if (!$user) return redirect('/');

        // 3. Cari data keluarga berdasarkan ID dan User ID (security)
        $keluarga = PerawatKeluarga::where('user_id', $user->id)->findOrFail($id);

        // 4. Validasi Input
        $request->validate([
            'hubungan'      => 'required|string|max:50',
            'nama'          => 'required|string|max:150',
            'tanggal_lahir' => 'nullable|date',
            'pekerjaan'     => 'nullable|string|max:150',
            'no_darurat'    => 'nullable|string|max:20', // Validasi no_darurat
        ]);

        // 5. Update Data
        $keluarga->update($request->only(
            'hubungan',
            'nama',
            'tanggal_lahir',
            'pekerjaan',
            'no_darurat' // Pastikan no_darurat ikut di-update
        ));

        // 6. Redirect kembali
        return redirect()->route('perawat.keluarga.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Data keluarga diperbarui.'
        ]);
    }

    public function keluargaDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $keluarga = PerawatKeluarga::where('user_id', $user->id)->findOrFail($id);
        $keluarga->delete();

        return redirect()->route('perawat.keluarga.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Data keluarga dihapus.'
        ]);
    }

    /* ============ ORGANISASI ============ */
    public function organisasiIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $organisasi = PerawatOrganisasi::where('user_id', $user->id)->get();
        return view('perawat.organisasi.index', compact('user', 'organisasi'));
    }

    public function organisasiCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.organisasi.create', compact('user'));
    }

    public function organisasiStore(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $request->validate([
            'nama_organisasi' => 'required|string|max:150',
            'jabatan'         => 'required|string|max:150',
            'tempat'          => 'nullable|string|max:100',
            'tahun_mulai'     => 'nullable|date',
            'tahun_selesai'   => 'nullable|date',
            'masa_organisasi' => 'nullable|string|max:50',
            'pemimpin'        => 'nullable|string|max:150',
            'dokumen'         => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $data = $request->except(['dokumen', '_token']);
        $data['user_id'] = $user->id;

        if ($request->hasFile('dokumen')) {
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/organisasi', 'public');
        }

        PerawatOrganisasi::create($data);

        return redirect()->route('perawat.organisasi.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Data organisasi ditambahkan.'
        ]);
    }

    public function organisasiEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $organisasi = PerawatOrganisasi::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.organisasi.edit', compact('user', 'organisasi'));
    }

    public function organisasiUpdate(Request $request, $id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $organisasi = PerawatOrganisasi::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'nama_organisasi' => 'required|string|max:150',
            'jabatan'         => 'required|string|max:150',
            'tempat'          => 'nullable|string|max:100',
            'tahun_mulai'     => 'nullable|date',
            'tahun_selesai'   => 'nullable|date',
            'masa_organisasi' => 'nullable|string|max:50',
            'pemimpin'        => 'nullable|string|max:150',
            'dokumen'         => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $data = $request->except(['dokumen', '_token', '_method']);

        if ($request->hasFile('dokumen')) {
            if ($organisasi->dokumen_path && Storage::disk('public')->exists($organisasi->dokumen_path)) {
                Storage::disk('public')->delete($organisasi->dokumen_path);
            }
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/organisasi', 'public');
        }

        $organisasi->update($data);

        return redirect()->route('perawat.organisasi.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Data organisasi diperbarui.'
        ]);
    }

    public function organisasiDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $organisasi = PerawatOrganisasi::where('user_id', $user->id)->findOrFail($id);

        if ($organisasi->dokumen_path && Storage::disk('public')->exists($organisasi->dokumen_path)) {
            Storage::disk('public')->delete($organisasi->dokumen_path);
        }
        $organisasi->delete();

        return redirect()->route('perawat.organisasi.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Data organisasi dihapus.'
        ]);
    }
/* ============ KUNJUNGAN LUAR NEGERI ============ */
    public function kunjunganIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $kunjungan = PerawatKunjungan::where('user_id', $user->id)->latest()->get();
        return view('perawat.kunjungan.index', compact('user', 'kunjungan'));
    }

    public function kunjunganCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.kunjungan.create', compact('user'));
    }

    public function kunjunganStore(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $request->validate([
            'negara' => 'required|string|max:100',
            'tujuan_kunjungan' => 'required|string|max:255',
            'lamanya' => 'nullable|string|max:100',
            'yang_membiayai' => 'nullable|string|max:150',
        ]);

        $data = $request->except(['_token']);
        $data['user_id'] = $user->id;

        PerawatKunjungan::create($data);

        return redirect()->route('perawat.kunjungan.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Data kunjungan luar negeri ditambahkan.'
        ]);
    }

    public function kunjunganEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $kunjungan = PerawatKunjungan::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.kunjungan.edit', compact('user', 'kunjungan'));
    }

    public function kunjunganUpdate(Request $request, $id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $kunjungan = PerawatKunjungan::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'negara' => 'required|string|max:100',
            'tujuan_kunjungan' => 'required|string|max:255',
            'lamanya' => 'nullable|string|max:100',
            'yang_membiayai' => 'nullable|string|max:150',
        ]);

        $kunjungan->update($request->except(['_token', '_method']));

        return redirect()->route('perawat.kunjungan.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Data kunjungan diperbarui.'
        ]);
    }

    public function kunjunganDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        
        PerawatKunjungan::where('user_id', $user->id)->findOrFail($id)->delete();

        return redirect()->route('perawat.kunjungan.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Data kunjungan dihapus.'
        ]);
    }
    /* ============ TANDA JASA ============ */
    public function tandajasaIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $tandajasa = PerawatTandaJasa::where('user_id', $user->id)->get();
        return view('perawat.tandajasa.index', compact('user', 'tandajasa'));
    }

    public function tandajasaCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.tandajasa.create', compact('user'));
    }

    public function tandajasaStore(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $request->validate([
            'nama_penghargaan' => 'required|string|max:150',
            'instansi_pemberi' => 'nullable|string|max:150',
            'tahun'            => 'nullable|string|max:10',
            'nomor_sk'         => 'nullable|string|max:100',
            'tanggal_sk'       => 'nullable|date',
            'dokumen'          => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $data = $request->except(['dokumen', '_token']);
        $data['user_id'] = $user->id;

        if ($request->hasFile('dokumen')) {
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/tandajasa', 'public');
        }

        PerawatTandaJasa::create($data);

        return redirect()->route('perawat.tandajasa.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Tanda jasa ditambahkan.'
        ]);
    }

    public function tandajasaEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $tandajasa = PerawatTandaJasa::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.tandajasa.edit', compact('user', 'tandajasa'));
    }

    public function tandajasaUpdate(Request $request, $id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $tj = PerawatTandaJasa::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'nama_penghargaan' => 'required|string|max:150',
            'instansi_pemberi' => 'nullable|string|max:150',
            'tahun'            => 'nullable|string|max:10',
            'nomor_sk'         => 'nullable|string|max:100',
            'tanggal_sk'       => 'nullable|date',
            'dokumen'          => 'nullable|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $data = $request->except(['dokumen', '_token', '_method']);

        if ($request->hasFile('dokumen')) {
            if ($tj->dokumen_path && Storage::disk('public')->exists($tj->dokumen_path)) {
                Storage::disk('public')->delete($tj->dokumen_path);
            }
            $data['dokumen_path'] = $request->file('dokumen')->store('perawat/tandajasa', 'public');
        }

        $tj->update($data);

        return redirect()->route('perawat.tandajasa.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Tanda jasa diperbarui.'
        ]);
    }

    public function tandajasaDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $tj = PerawatTandaJasa::where('user_id', $user->id)->findOrFail($id);

        if ($tj->dokumen_path && Storage::disk('public')->exists($tj->dokumen_path)) {
            Storage::disk('public')->delete($tj->dokumen_path);
        }
        $tj->delete();

        return redirect()->route('perawat.tandajasa.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Tanda jasa dihapus.'
        ]);
    }

    /* ==========================================================
       BAGIAN MANAJEMEN DOKUMEN (LISENSI, STR, SIP, TAMBAHAN)
       ========================================================== */

    public function lisensiIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        // Mengambil data lisensi milik user yang sedang login
        $data = PerawatLisensi::where('user_id', $user->id)
            ->orderBy('tgl_expired', 'desc')
            ->get();

        return view('perawat.dokumen.lisensi.index', compact('user', 'data'));
    }

    public function generateLisensi($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $lisensi = PerawatLisensi::where('user_id', $user->id)->findOrFail($id);
        $profile = PerawatProfile::where('user_id', $user->id)->first();
        $pendidikan = PerawatPendidikan::where('user_id', $user->id)->orderBy('tahun_lulus', 'desc')->first();

        // --------------------------------------------------------
        // 1. BERSIHKAN NOMOR SURAT (Mencegah Duplikat Prefix)
        // --------------------------------------------------------
        Carbon::setLocale('id');
        $dateTerbit = !empty($lisensi->tgl_terbit) ? Carbon::parse($lisensi->tgl_terbit) : Carbon::now();
        $tahunSurat = $dateTerbit->format('Y');

        $rawNomor   = $lisensi->nomor;
        $prefix     = "188/4150/418.25";

        // Cek: Apakah sudah ada prefix lengkap?
        if (str_contains($rawNomor, '188/4150') || str_contains($rawNomor, '418.25')) {
            // Jika sudah format lengkap, kita ambil saja dan update tahunnya
            $parts = explode('/', $rawNomor);
            array_pop($parts); // Buang tahun lama
            $nomorSuratFull = implode('/', $parts) . '/' . $tahunSurat;
        } else {
            // Jika belum (hanya nomor urut), baru kita rakit
            $cleanNomor = preg_replace('/[^0-9\-\.]/', '', $rawNomor);
            $nomorUrut  = !empty($cleanNomor) ? $cleanNomor : '0';
            $nomorUrut  = ltrim($nomorUrut, '.'); // Hapus titik di depan jika ada

            // Tambahkan titik di akhir prefix jika belum ada
            $prefixFull = str_ends_with($prefix, '.') ? $prefix : $prefix . '.';
            $nomorSuratFull = "{$prefixFull}{$nomorUrut}/{$tahunSurat}";
        }

        $lisensi->nomor = $nomorSuratFull;

        // --------------------------------------------------------
        // 2. CEK METODE (PERBAIKAN LOGIKA DISINI)
        // --------------------------------------------------------
        $rawMetode = $lisensi->metode_perpanjangan ?? $lisensi->metode ?? '';
        $cekMetode = strtolower($rawMetode);

        // Deteksi Unsur
        $hasInterview = str_contains($cekMetode, 'interview') || str_contains($cekMetode, 'wawancara');
        $hasPG        = str_contains($cekMetode, 'pg') || str_contains($cekMetode, 'pilihan') || str_contains($cekMetode, 'ganda') || str_contains($cekMetode, 'choice');

        // LOGIKA BARU:
        // Masuk ke Word (Template) HANYA JIKA: Ada Wawancara DAN Tidak Ada PG.
        // Jika "PG + Wawancara", $hasPG bernilai true, maka kondisi ini FALSE (masuk ke else/PDF).
        if ($hasInterview && !$hasPG) {

            // ==========================================
            // LOGIKA WORD (HANYA WAWANCARA MURNI)
            // ==========================================

            $pathTemplate = storage_path('app/templates/template_serkom.docx');

            if (!file_exists($pathTemplate)) {
                return back()->with('swal', [
                    'icon' => 'error',
                    'title' => 'Template Tidak Ditemukan',
                    'text' => 'File template_serkom.docx tidak ada.'
                ]);
            }

            $templateProcessor = new TemplateProcessor($pathTemplate);
            $namaLengkap = $profile->nama_lengkap ?? $user->name;

            // --- PENGISIAN VARIABLE WORD ---
            $templateProcessor->setValue('nama', strtoupper($namaLengkap));
            $templateProcessor->setValue('nirp', $profile->nirp ?? ($profile->nip ?? '-'));
            $templateProcessor->setValue('unit_kerja', strtoupper($profile->unit_kerja ?? 'RSUD SLG'));
            $templateProcessor->setValue('nomor', $lisensi->nomor);
            $templateProcessor->setValue('jenis_lisensi', strtoupper($lisensi->nama));
            $templateProcessor->setValue('bidang', strtoupper($lisensi->bidang));

            // KFK
            $pkRaw = $lisensi->kfk;
            $finalPk = $pkRaw;
            if (is_array($pkRaw)) {
                $finalPk = implode(', ', $pkRaw);
            } elseif (is_string($pkRaw) && str_contains($pkRaw, '[')) {
                $decoded = json_decode($pkRaw, true);
                $finalPk = (is_array($decoded)) ? implode(', ', $decoded) : $pkRaw;
            }
            $templateProcessor->setValue('kfk', strtoupper($finalPk));

            // Tanggal
            $templateProcessor->setValue('tgl_terbit', $dateTerbit->translatedFormat('d F Y'));
            $tglSelesai = !empty($lisensi->tgl_expired) ? Carbon::parse($lisensi->tgl_expired)->translatedFormat('d F Y') : '-';
            $templateProcessor->setValue('tgl_selesai', $tglSelesai);
            $tglMulai = !empty($lisensi->tgl_mulai) ? Carbon::parse($lisensi->tgl_mulai)->translatedFormat('d F Y') : $dateTerbit->translatedFormat('d F Y');
            $templateProcessor->setValue('tgl_mulai', $tglMulai);

            // Pendidikan
            $txtPendidikan = $pendidikan ? trim($pendidikan->jenjang . ' ' . $pendidikan->jurusan) : '-';
            $templateProcessor->setValue('pendidikan', strtoupper($txtPendidikan));

            // Download Word
            $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $namaLengkap);
            $tempPath  = storage_path('app/public/Sertifikat_Wawancara_' . $cleanName . '.docx');

            try {
                $templateProcessor->saveAs($tempPath);
                return response()->download($tempPath)->deleteFileAfterSend(true);
            } catch (\Exception $e) {
                return back()->with('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Gagal memproses Word: ' . $e->getMessage()]);
            }
        } else {

            // ==========================================
            // LOGIKA PDF (PG MURNI ATAU CAMPURAN PG+WAWANCARA)
            // ==========================================

            $data = [
                'lisensi' => $lisensi,
                'user'    => $user,
                'profile' => $profile,
                'date'    => date('d F Y')
            ];

            $pdf = Pdf::loadView('perawat.dokumen.lisensi.print', $data);
            $pdf->setPaper('A4', 'landscape');

            $fileName = 'generated_lisensi_' . time() . '.pdf';
            $path = 'perawat/dokumen/lisensi/' . $fileName;
            Storage::disk('public')->put($path, $pdf->output());

            $lisensi->update(['file_path' => $path]);

            return $pdf->stream('Lisensi_' . ($profile->nama_lengkap ?? $user->name) . '.pdf');
        }
    }


    /* ------------ 2. STR (Surat Tanda Registrasi) ------------ */
    public function strIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $data = PerawatStr::where('user_id', $user->id)->orderBy('tgl_expired', 'desc')->get();
        return view('perawat.dokumen.str.index', compact('user', 'data'));
    }

    public function strCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.dokumen.str.create', compact('user'));
    }

    public function strStore(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        // Cek apakah sudah punya STR (Limit 1 per perawat)
        if (PerawatStr::where('user_id', $user->id)->exists()) {
            return redirect()->route('perawat.str.index')
                ->with('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Anda hanya diperbolehkan memiliki 1 dokumen STR.']);
        }

        $request->validate([
            'nama'        => 'nullable|string|max:100',
            'nomor'       => 'required|string|max:100',
            'tgl_terbit'  => 'required|date',
            'tgl_expired' => 'nullable|date', // Jadi nullable
            'dokumen'     => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->except(['dokumen', '_token']);
        $data['user_id'] = $user->id;

        // Pastikan null jika kosong (untuk lifetime)
        $data['tgl_expired'] = $request->tgl_expired ?: null;

        if ($request->hasFile('dokumen')) {
            $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/str', 'public');
        }

        PerawatStr::create($data);
        return redirect()->route('perawat.str.index')->with('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'STR berhasil disimpan.']);
    }

    public function strUpdate(Request $request, $id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $str = PerawatStr::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'nama'        => 'nullable|string|max:100',
            'nomor'       => 'required|string|max:100',
            'tgl_terbit'  => 'required|date',
            'tgl_expired' => 'nullable|date', // [UBAH] Jadi nullable
            'dokumen'     => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->except(['dokumen', '_token', '_method']);

        // Pastikan null jika kosong
        $data['tgl_expired'] = $request->tgl_expired ?: null;

        if ($request->hasFile('dokumen')) {
            if ($str->file_path && Storage::disk('public')->exists($str->file_path)) {
                Storage::disk('public')->delete($str->file_path);
            }
            $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/str', 'public');
        }

        $str->update($data);
        return redirect()->route('perawat.str.index')->with('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'STR diperbarui.']);
    }

    public function strEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $data = PerawatStr::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.dokumen.str.edit', compact('user', 'data'));
    }

    public function strDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $data = PerawatStr::where('user_id', $user->id)->findOrFail($id);

        if ($data->file_path && Storage::disk('public')->exists($data->file_path)) {
            Storage::disk('public')->delete($data->file_path);
        }
        $data->delete();
        return redirect()->route('perawat.str.index')->with('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'STR dihapus.']);
    }

    /* ------------ 3. SIP (Surat Izin Praktik) ------------ */
    public function sipIndex()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $data = PerawatSip::where('user_id', $user->id)->orderBy('tgl_expired', 'desc')->get();
        return view('perawat.dokumen.sip.index', compact('user', 'data'));
    }

    public function sipCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.dokumen.sip.create', compact('user'));
    }

    public function sipStore(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        // [BARU] Cek apakah sudah punya SIP (Limit 1 per perawat)
        if (PerawatSip::where('user_id', $user->id)->exists()) {
            return redirect()->route('perawat.sip.index')
                ->with('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Anda hanya diperbolehkan memiliki 1 dokumen SIP.']);
        }

        $request->validate([
            'nama'        => 'nullable|string|max:100',
            'nomor'       => 'required|string|max:100',
            'tgl_terbit'  => 'required|date',
            'tgl_expired' => 'nullable|date', // [UBAH] Jadi nullable
            'dokumen'     => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->except(['dokumen', '_token']);
        $data['user_id'] = $user->id;

        // Pastikan null jika kosong
        $data['tgl_expired'] = $request->tgl_expired ?: null;

        if ($request->hasFile('dokumen')) {
            $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/sip', 'public');
        }

        PerawatSip::create($data);
        return redirect()->route('perawat.sip.index')->with('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'SIP berhasil disimpan.']);
    }
    public function sipEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $data = PerawatSip::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.dokumen.sip.edit', compact('user', 'data'));
    }

    public function sipUpdate(Request $request, $id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $sip = PerawatSip::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'nama'        => 'nullable|string|max:100',
            'nomor'       => 'required|string|max:100',
            'tgl_terbit'  => 'required|date',
            'tgl_expired' => 'nullable|date', // [UBAH] Jadi nullable
            'dokumen'     => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->except(['dokumen', '_token', '_method']);

        // Pastikan null jika kosong
        $data['tgl_expired'] = $request->tgl_expired ?: null;

        if ($request->hasFile('dokumen')) {
            if ($sip->file_path && Storage::disk('public')->exists($sip->file_path)) {
                Storage::disk('public')->delete($sip->file_path);
            }
            $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/sip', 'public');
        }

        $sip->update($data);
        return redirect()->route('perawat.sip.index')->with('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'SIP diperbarui.']);
    }

    public function sipDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $data = PerawatSip::where('user_id', $user->id)->findOrFail($id);

        if ($data->file_path && Storage::disk('public')->exists($data->file_path)) {
            Storage::disk('public')->delete($data->file_path);
        }
        $data->delete();
        return redirect()->route('perawat.sip.index')->with('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'SIP dihapus.']);
    }

    /* ------------ 4. DATA TAMBAHAN (Ada Field Jenis) ------------ */
public function tambahanIndex()
{
    $user = $this->currentPerawat();
    if (!$user) return redirect('/');

    $data = PerawatDataTambahan::where('user_id', $user->id)->orderBy('tgl_terbit', 'desc')->get();
    
    // Logika perhitungan SKP
    $today = Carbon::now();
    $limaTahunLalu = Carbon::now()->subYears(5);

    $totalSkpAktif = 0;
    $totalSkpNonAktif = 0;

    foreach ($data as $item) {
        $isAktif = false;
        
        // Cek validitas dokumen (mirip dengan logika di Service)
        if ($item->is_lifetime) {
            if ($item->lifetime_approved) $isAktif = true;
        } elseif ($item->tgl_expired && Carbon::parse($item->tgl_expired)->greaterThanOrEqualTo($today)) {
            $isAktif = true;
        }

        if ($isAktif && Carbon::parse($item->tgl_terbit)->greaterThanOrEqualTo($limaTahunLalu)) {
            $totalSkpAktif += $item->skp;
        } else {
            $totalSkpNonAktif += $item->skp;
        }
    }

    return view('perawat.dokumen.tambahan.index', compact('user', 'data', 'totalSkpAktif', 'totalSkpNonAktif'));
}

    public function tambahanCreate()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        return view('perawat.dokumen.tambahan.create', compact('user'));
    }

    public function tambahanStore(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $isLifetime = $request->has('is_lifetime') && $request->input('is_lifetime') == '1';

    $rules = [
            'nama'       => 'required|string|max:100',
            'lembaga'    => 'required|string|max:100',
            'nomor'      => 'required|string|max:100',
            'tgl_terbit' => 'required|date',
            'skp'        => 'nullable|integer|min:0', // <-- TAMBAHKAN INI DI RULES
            'dokumen'    => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
            'is_lifetime' => 'nullable|in:1',
        ];

        // Jika bukan lifetime, expired wajib
        if ($isLifetime) {
            $rules['tgl_expired'] = 'nullable|date';
        } else {
            $rules['tgl_expired'] = 'required|date';
        }

        $request->validate($rules);

        $data = $request->except(['dokumen', '_token']);
        $data['user_id'] = $user->id;
        $data['is_lifetime'] = $isLifetime ? 1 : 0;

        if ($isLifetime) {
            $data['tgl_expired'] = null;
            $data['lifetime_approved'] = false;
            $data['kelayakan'] = 'pending_lifetime';
        }

        if ($request->hasFile('dokumen')) {
            $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/tambahan', 'public');
        }

        $record = PerawatDataTambahan::create($data);

        // notify admins when lifetime requested
        if (!empty($data['is_lifetime']) && !$record->lifetime_approved) {
            try {
                $tg = new TelegramService();
                $tg->notifyLifetimeRequested($user, $record);
            } catch (\Throwable $e) {
                Log::error('notifyLifetimeRequested failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('perawat.tambahan.index')->with('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Dokumen berhasil disimpan.']);
    }

    public function tambahanEdit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $data = PerawatDataTambahan::where('user_id', $user->id)->findOrFail($id);
        return view('perawat.dokumen.tambahan.edit', compact('user', 'data'));
    }

    public function tambahanUpdate(Request $request, $id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $tambahan = PerawatDataTambahan::where('user_id', $user->id)->findOrFail($id);

        $isLifetime = $request->has('is_lifetime') && $request->input('is_lifetime') == '1';

      $rules = [
            'nama'       => 'required|string|max:100',
            'lembaga'    => 'required|string|max:100',
            'nomor'      => 'required|string|max:100',
            'tgl_terbit' => 'required|date',
            'skp'        => 'nullable|integer|min:0', // <-- TAMBAHKAN INI DI RULES
            'dokumen'    => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
            'is_lifetime' => 'nullable|in:1',
        ];

        if ($isLifetime) {
            $rules['tgl_expired'] = 'nullable|date';
        } else {
            $rules['tgl_expired'] = 'required|date';
        }

        $request->validate($rules);

        $data = $request->except(['dokumen', '_token', '_method']);
        $data['is_lifetime'] = $isLifetime ? 1 : 0;

        if ($isLifetime) {
            $data['tgl_expired'] = null;
            // mark as not approved if switched to lifetime
            $data['lifetime_approved'] = false;
            $data['kelayakan'] = 'pending_lifetime';
        }

        if ($request->hasFile('dokumen')) {
            if ($tambahan->file_path && Storage::disk('public')->exists($tambahan->file_path)) {
                Storage::disk('public')->delete($tambahan->file_path);
            }
            $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/tambahan', 'public');
        }

        $tambahan->update($data);
        $tambahan->refresh();

        if (!empty($data['is_lifetime']) && !$tambahan->lifetime_approved) {
            try {
                $tg = new TelegramService();
                $tg->notifyLifetimeRequested($user, $tambahan);
            } catch (\Throwable $e) {
                Log::error('notifyLifetimeRequested failed (update): ' . $e->getMessage());
            }
        }

        return redirect()->route('perawat.tambahan.index')->with('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Dokumen diperbarui.']);
    }

    public function tambahanDestroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        $data = PerawatDataTambahan::where('user_id', $user->id)->findOrFail($id);

        if ($data->file_path && Storage::disk('public')->exists($data->file_path)) {
            Storage::disk('public')->delete($data->file_path);
        }
        $data->delete();
        return redirect()->route('perawat.tambahan.index')->with('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Dokumen tambahan dihapus.']);
    }
}
