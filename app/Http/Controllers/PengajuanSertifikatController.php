<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PengajuanSertifikat;
use App\Models\PenanggungJawabUjian;
use App\Models\JadwalWawancara;
use App\Models\PerawatLisensi;
use PhpOffice\PhpWord\TemplateProcessor;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PengajuanSertifikatController extends Controller
{
    
private function getPpiItems()
    {
        return [
            ['no_item' => 'PPI-01', 'deskripsi_kompetensi' => 'Menerapkan prinsip kerjasama interdisiplin terkait infeksi (dokter, perawat, laboratorium, PPRA, Gizi, CSSD, Laundry, sanitasi, house keeping, IPSRS, farmasi, security)'],
            ['no_item' => 'PPI-02', 'deskripsi_kompetensi' => 'Memberikan konsultasi tentang pencegahan dan pengendalian infeksi yang diperlukan pada kasus yang terjadi di RS'],
            ['no_item' => 'PPI-03', 'deskripsi_kompetensi' => 'Mengajukan prosedur isolasi sesuai transmisi'],
            ['no_item' => 'PPI-04', 'deskripsi_kompetensi' => 'Memberikan motivasi dan teguran tentang pelaksanaan program PPI'],
            ['no_item' => 'PPI-05', 'deskripsi_kompetensi' => 'Menunjukkan sikap asertif'],
            ['no_item' => 'PPI-06', 'deskripsi_kompetensi' => 'Menunjukkan sikap empati'],
            ['no_item' => 'PPI-07', 'deskripsi_kompetensi' => 'Melakukan surveilans infeksi Phlebitis, CAUTI (Catheter Associated Urinary Tract Infection), VAP (Ventilator Associated Pneumonia), IADP (Infeksi Aliran Darah Primer), IDO (Infeksi Daerah Operasi), Emerging dan Re-emerging Disease'],
            ['no_item' => 'PPI-08', 'deskripsi_kompetensi' => 'Melakukan audit penerapan bundles pencegahan HAIs (Phlebitis, ISK, VAP, IADP, dan IDO)'],
            ['no_item' => 'PPI-09', 'deskripsi_kompetensi' => 'Memonitoring dan evaluasi peralatan medis single use dan di re-use'],
            ['no_item' => 'PPI-10', 'deskripsi_kompetensi' => 'Melakukan asuhan keperawatan pada pasien yang beresiko infeksi (terpasang alat infus, kateter urin, kateter vena sentral dan ventilator serta pasien post operasi)'],
            ['no_item' => 'PPI-11', 'deskripsi_kompetensi' => 'Memonitor kepatuhan terhadap penerapan SPO Kewaspadaan Standar : SPO kebersihan tangan, SPO penggunaan alat pelindung diri, SPO pemilahan sampah, SPO pemilahan linen, SPO pengelolaan peralatan perawatan pasien, SPO praktek menyuntik yang aman, SPO penempatan pasien, SPO etika batuk, SPO pengendalian lingkungan, SPO Lumbal Punksi'],
            ['no_item' => 'PPI-12', 'deskripsi_kompetensi' => 'Melakukan audit pencegahan dan pengendalian infeksi di unit terkait : Audit kewaspadaan standar (kebersihan tangan, APD, limbah, dll), Audit PPI di Gizi, Audit PPI di laundry, Audit PPI di CSSD, Audit PPI di kamar jenazah, Audit PPI di ruang hemodialisa, Audit PPI di kamar operasi, Audit PPI di kamar isolasi'],
            ['no_item' => 'PPI-13', 'deskripsi_kompetensi' => 'Melakukan monitoring terhadap penggunaan antibiotik yang rasional'],
            ['no_item' => 'PPI-14', 'deskripsi_kompetensi' => 'Melakukan riset keperawatan terkait pencegahan dan pengendalian infeksi'],
            ['no_item' => 'PPI-15', 'deskripsi_kompetensi' => 'Memberikan pelatihan dasar PPI pada petugas kesehatan (perawat dan dokter)'],
            ['no_item' => 'PPI-16', 'deskripsi_kompetensi' => 'Memberikan edukasi tentang PPI pada pasien, keluarga dan pengunjung (kebersihan tangan, etika batuk, dan pemilahan sampah di rumah sakit)'],
            ['no_item' => 'PPI-17', 'deskripsi_kompetensi' => 'Memberikan edukasi tentang PPI pada Security (kebersihan tangan dan penggunaan APD), Pramusaji (PPI di pelayanan makanan), House Keeping (kebersihan tangan, penggunaan APD dan pengendalian lingkungan / pembersihan RS)'],
            ['no_item' => 'PPI-18', 'deskripsi_kompetensi' => 'Memberikan pelatihan tentang PPIRS pada petugas kesehatan di area penunjang (laboratorium, radiologi, CSSD, laundry, gizi, kamar jenazah, fisioterapi, dan farmasi)'],
        ];
    }
    // Tambahkan di dalam PengajuanSertifikatController
private function getMppItems()
{
    return [
        ['no_item' => 'MPP-01', 'deskripsi_kompetensi' => 'Melakukan skrining kebutuhan Case Management'],
        ['no_item' => 'MPP-02', 'deskripsi_kompetensi' => 'Menentukan pasien yang memerlukan Case Management'],
        ['no_item' => 'MPP-03', 'deskripsi_kompetensi' => 'Melakukan asesmen Case Management komprehensif'],
        ['no_item' => 'MPP-04', 'deskripsi_kompetensi' => 'Menyusun Individual Care Plan bersama PPA'],
        ['no_item' => 'MPP-05', 'deskripsi_kompetensi' => 'Mengkoordinasikan DPJP, Perawat, Apoteker, Ahli Gizi dan profesi lain'],
        ['no_item' => 'MPP-06', 'deskripsi_kompetensi' => 'Mengkoordinasikan discharge planning'],
        ['no_item' => 'MPP-07', 'deskripsi_kompetensi' => 'Melakukan monitoring implementasi rencana pelayanan'],
        ['no_item' => 'MPP-08', 'deskripsi_kompetensi' => 'Mengidentifikasi hambatan pelayanan pasien'],
        ['no_item' => 'MPP-09', 'deskripsi_kompetensi' => 'Melakukan koordinasi perpindahan pasien antar unit'],
        ['no_item' => 'MPP-10', 'deskripsi_kompetensi' => 'Melakukan koordinasi rujukan'],
        ['no_item' => 'MPP-11', 'deskripsi_kompetensi' => 'Mengkoordinasikan pelayanan BPJS dan administrasi pelayanan'],
        ['no_item' => 'MPP-12', 'deskripsi_kompetensi' => 'Melakukan edukasi kepada pasien dan keluarga'],
        ['no_item' => 'MPP-13', 'deskripsi_kompetensi' => 'Mengkoordinasikan penyelesaian masalah pelayanan pasien'],
        ['no_item' => 'MPP-14', 'deskripsi_kompetensi' => 'Melakukan dokumentasi Case Management'],
        ['no_item' => 'MPP-15', 'deskripsi_kompetensi' => 'Melakukan evaluasi outcome pelayanan pasien'],
        ['no_item' => 'MPP-16', 'deskripsi_kompetensi' => 'Mengikuti clinical conference'],
        ['no_item' => 'MPP-17', 'deskripsi_kompetensi' => 'Mengikuti discharge conference'],
        ['no_item' => 'MPP-18', 'deskripsi_kompetensi' => 'Melakukan koordinasi dengan MPP eksternal'],
        ['no_item' => 'MPP-19', 'deskripsi_kompetensi' => 'Melakukan monitoring LOS (Length of Stay)'],
        ['no_item' => 'MPP-20', 'deskripsi_kompetensi' => 'Mengidentifikasi potensi readmission'],
        ['no_item' => 'MPP-21', 'deskripsi_kompetensi' => 'Melakukan koordinasi pelayanan paliatif / kronis'],
        ['no_item' => 'MPP-22', 'deskripsi_kompetensi' => 'Melakukan koordinasi pelayanan pasien kompleks / resti'],
        ['no_item' => 'MPP-23', 'deskripsi_kompetensi' => 'Melakukan koordinasi kesinambungan pelayanan (Continuity of Care)'],
        ['no_item' => 'MPP-24', 'deskripsi_kompetensi' => 'Melakukan audit dokumentasi Case Management'],
        ['no_item' => 'MPP-25', 'deskripsi_kompetensi' => 'Menyusun laporan indikator mutu MPP'],
        ['no_item' => 'MPP-26', 'deskripsi_kompetensi' => 'Mengkoordinasikan penempatan tempat tidur (Bed Management)'],
        ['no_item' => 'MPP-27', 'deskripsi_kompetensi' => 'Melakukan koordinasi pelayanan pasien prioritas'],
        ['no_item' => 'MPP-28', 'deskripsi_kompetensi' => 'Melakukan koordinasi pelayanan multidisiplin'],
        ['no_item' => 'MPP-29', 'deskripsi_kompetensi' => 'Monitoring Clinical Pathway'],
        ['no_item' => 'MPP-30', 'deskripsi_kompetensi' => 'Mengkoordinasikan rujukan internal dan eksternal'],
    ];
}
    public function index(Request $request)
    {
        $user = Auth::user();
        $listSertifikat = PerawatLisensi::select('nama')->distinct()->pluck('nama');

   $query = PengajuanSertifikat::where('user_id', $user->id)
            ->with(['lisensiLama', 'jadwalWawancara.pewawancara', 'jadwalWawancara.penilaian']); // <-- Tambahkan ini

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('jenis_pengajuan', 'LIKE', "%{$search}%")
                    ->orWhereHas('lisensiLama', function ($subQ) use ($search) {
                        $subQ->where('nama', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pengajuan = $query->latest()->paginate(10)->withQueryString();
        $pjs = PenanggungJawabUjian::all();

        return view('perawat.pengajuan.index', compact('user', 'pengajuan', 'pjs', 'listSertifikat'));
    }

    /**
     * [FIXED] LOGIKA CREATE
     * Cek apakah user sudah punya lisensi UNTUK METODE TERSEBUT.
     */
public function create($metode = 'pg_interview')
{
    $user = Auth::user();

    // Cek apakah sudah punya lisensi yang sudah TERBIT (Aktif/Expired)
    $sudahPunyaLisensi = PerawatLisensi::where('user_id', $user->id)
        ->where('metode', $metode)
        ->whereIn('status', ['active', 'expired'])
        ->exists();

    // NEW: Cek apakah user sedang punya pengajuan yang masih PROSES (Pending/Draft/dll)
    $sedangMengajukan = PengajuanSertifikat::where('user_id', $user->id)
    ->where('metode', $metode)
    // Cek hanya status yang benar-benar sedang berproses/aktif
    ->whereIn('status', ['pending', 'verified', 'method_selected', 'interview_scheduled'])
    ->exists();

    return view('perawat.dokumen.lisensi.create', compact('sudahPunyaLisensi', 'sedangMengajukan', 'metode'));
}
    public function store(Request $request)
    {
        $user = Auth::user();

        // 1. VALIDASI INPUT
       $request->validate([
    'jenis_pengajuan' => 'required|in:baru,perpanjangan',
    'link_gdrive'     => 'required|url',
    'kfk'             => 'required|array|min:2', // Minimal 2 item
    'metode'          => 'required',
]);

$kfkInput = $request->kfk;
        $countKfk = 0;
        $countJenjang = 0;

        foreach ($kfkInput as $item) {
            // [FIXED]: Loloskan jika string mengandung 'KFK' ATAU stringnya adalah 'Keperawatan'
            if (str_contains($item, 'KFK') || $item === 'Keperawatan') {
                $countKfk++;
            }
            // Pengecekan Jenjang tetap sama
            if (str_contains($item, 'PK') || str_contains($item, 'BK')) {
                $countJenjang++;
            }
        }

        // Cek apakah minimal ada 1 KFK dan minimal ada 1 Jenjang
        if ($countKfk < 1 || $countJenjang < 1) {
            return back()->withInput()->with('swal', [
                'icon' => 'error',
                'title' => 'Kombinasi Tidak Valid',
                'text' => 'Pilihan harus mencakup minimal satu KFK / Keperawatan dan satu Jenjang (PK/BK).'
            ]);
        }

        // 2. VALIDASI DUPLIKAT (Backend Protection)
        if ($request->jenis_pengajuan == 'baru') {
            $cekLisensi = PerawatLisensi::where('user_id', $user->id)
                ->where('metode', $request->metode) // Cek spesifik metode
                ->whereIn('status', ['active', 'expired'])
                ->exists();

            if ($cekLisensi) {
                return back()->with('swal', ['icon' => 'error', 'title' => 'Error', 'text' => 'Anda sudah memiliki dokumen jenis ini. Harap pilih Perpanjangan.']);
            }
        }

        // 3. VALIDASI FILE DINAMIS
        if ($request->jenis_pengajuan == 'baru') {
            // Jika Baru -> Butuh 1 File PDF Lengkap
            $request->validate(['file_dokumen_baru' => 'required|mimes:pdf|max:5120']);
        } else {
            // Jika Perpanjangan -> Butuh Sertifikat Lama & Rekomendasi
            $request->validate([
                'file_sertifikat_lama'   => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
                'file_surat_rekomendasi' => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);
        }

        // 4. BERSIHKAN PENGAJUAN GANTUNG (Hanya untuk metode yang sama)
        PengajuanSertifikat::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('metode', $request->metode)
            ->delete();

        // Reset Nilai Ujian jika metode butuh ujian
        if ($request->metode != 'interview_only' && $user->examResult) {
            $user->examResult->delete();
        }

        // 5. UPLOAD FILE
        $pathBaru = $request->hasFile('file_dokumen_baru') ? $request->file('file_dokumen_baru')->store('dokumen_pengajuan', 'public') : null;
        $pathLama = $request->hasFile('file_sertifikat_lama') ? $request->file('file_sertifikat_lama')->store('sertifikat_lama', 'public') : null;
        $pathRekom = $request->hasFile('file_surat_rekomendasi') ? $request->file('file_surat_rekomendasi')->store('surat_rekomendasi', 'public') : null;

        // 6. SIMPAN DATA
        $infoKeterangan = "Pengajuan " . ($request->metode == 'interview_only' ? 'Kredensialing' : 'Uji Kompetensi');
        $infoKeterangan .= " (" . ucfirst($request->jenis_pengajuan) . ")";

        // Cari ID Lisensi lama untuk relasi (jika perpanjangan)
        $lisensiLamaId = null;
        if ($request->jenis_pengajuan == 'perpanjangan') {
            $lastLicense = PerawatLisensi::where('user_id', $user->id)
                ->where('metode', $request->metode)
                ->latest()->first();
            $lisensiLamaId = $lastLicense ? $lastLicense->id : null;
        }

        PengajuanSertifikat::create([
            'user_id'                => $user->id,
            'lisensi_lama_id'        => $lisensiLamaId,
            'status'                 => 'pending',
            'metode'                 => $request->metode,
            'jenis_pengajuan'        => $request->jenis_pengajuan,
            'link_gdrive'            => $request->link_gdrive,
            'file_dokumen_baru'      => $pathBaru,
             'kfk'                    => $request->kfk,
            'file_sertifikat_lama'   => $pathLama,
            'file_surat_rekomendasi' => $pathRekom,
            'keterangan'             => $infoKeterangan
        ]);

        return redirect()->route('perawat.pengajuan.index')
            ->with('swal', ['icon' => 'success', 'title' => 'Terkirim', 'text' => 'Pengajuan berhasil dikirim.']);
    }

public function storeWawancara(Request $request, $id)
    {
        // 1. Ambil Data Pengajuan Milik User Login
        $pengajuan = PengajuanSertifikat::where('user_id', Auth::id())->findOrFail($id);

        // 2. Validasi Input
        $request->validate([
            'penanggung_jawab_id' => 'nullable|exists:penanggung_jawab_ujians,id',
            'tgl_wawancara'       => 'required|date',
            'jam_wawancara'       => 'required',
            'lokasi_wawancara'    => 'nullable|string|max:255'
        ]);

        // 3. HAPUS JADWAL LAMA (CLEAN UP)
        // Ini wajib agar tidak ada duplikat jadwal untuk satu pengajuan.
        // Terutama jika user melakukan pengajuan ulang (remedial) atau jadwal sebelumnya ditolak.
        if ($pengajuan->jadwalWawancara) {
            $pengajuan->jadwalWawancara->delete();
        }

        // 4. Gabungkan Tanggal & Jam
        $waktu = $request->tgl_wawancara . ' ' . $request->jam_wawancara;

        // 5. Buat Jadwal Baru
        JadwalWawancara::create([
            'pengajuan_sertifikat_id' => $pengajuan->id,
            'penanggung_jawab_id'     => $request->penanggung_jawab_id,
            'waktu_wawancara'         => $waktu,
            'lokasi'                  => $request->lokasi_wawancara,
            'status'                  => 'pending', // Status awal jadwal selalu pending menunggu acc admin
            'deskripsi_skill'         => null // Reset deskripsi
        ]);

        // 6. Update Status Pengajuan
        // Kembalikan status ke 'interview_scheduled' agar Admin mendapat notifikasi/filter
        $pengajuan->update(['status' => 'interview_scheduled']);

        return back()->with('swal', ['icon' => 'success', 'title' => 'Tersimpan', 'text' => 'Jadwal wawancara berhasil diajukan. Menunggu konfirmasi Admin.']);
    }
public function verifyPublic($id) {
    $lisensi = PerawatLisensi::with('user.perawatProfile')->findOrFail($id);
    
    $idMentah = $lisensi->id;
    $nomorUrutMurni = str_pad($idMentah, 3, '0', STR_PAD_LEFT);
    $tahunSurat = \Carbon\Carbon::parse($lisensi->tgl_terbit)->format('Y');

    // FIX NOMOR
    $jenisSurat = ($lisensi->metode === 'interview_only') ? 'KRED' : 'SERKOM';
    $lisensi->nomor = "800.2.4.2.1/{$nomorUrutMurni}/418.101/{$jenisSurat}/{$tahunSurat}";
    
    // Warnanya kita sesuaikan dengan nama KFK
    $kfkRaw = is_array($lisensi->kfk) ? implode(' ', $lisensi->kfk) : $lisensi->kfk;
    $kfkValue = strtoupper($kfkRaw ?? $lisensi->nama); 
    
    $themeColor = '#512da8'; // Default Ungu

    if (str_contains($kfkValue, 'KRITIS')) { $themeColor = '#c62828'; }
    elseif (str_contains($kfkValue, 'MEDIKAL BEDAH')) { $themeColor = '#1565c0'; }
    elseif (str_contains($kfkValue, 'ANAK')) { $themeColor = '#2e7d32'; }
    elseif (str_contains($kfkValue, 'MATERNITAS')) { $themeColor = '#ad1457'; }

    return view('public.verify_sertifikat', compact('lisensi', 'themeColor'));
}
public function printSertifikat($id)
    {
        $user = Auth::user();
        $pengajuan = PengajuanSertifikat::where('user_id', $user->id)
            ->with(['user.perawatProfile', 'user.pendidikanTerakhir', 'lisensiLama', 'jadwalWawancara.penilaian']) 
            ->findOrFail($id);

        if ($pengajuan->status != 'completed') {
            return back()->with('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Sertifikat belum tersedia.']);
        }

        $lisensi = $pengajuan->lisensiLama;
        if (!$lisensi) {
            $lisensi = PerawatLisensi::where('user_id', $user->id)->where('status', 'active')->latest()->first();
            if (!$lisensi) return back()->with('swal', ['icon' => 'error', 'title' => 'Data Kosong', 'text' => 'Data Lisensi belum dibuat Admin.']);
        }

        $profile = $pengajuan->user->perawatProfile;
        $pekerjaan = \App\Models\PerawatPekerjaan::where('user_id', $user->id)->latest()->first();
        $pendidikan = $pengajuan->user->pendidikanTerakhir;
        
        $namaLengkap = $profile->nama_lengkap ?? $user->name;

       Carbon::setLocale('id');
        $tglMulai = $lisensi->tgl_mulai ?? ($lisensi->tgl_terbit ?? now());
        $carbonDate = Carbon::parse($tglMulai);
        $tglMulaiIndo = $carbonDate->translatedFormat('d F Y');
        
      // Cek apakah kolom tgl_expired di database sudah ada isinya
if (!empty($lisensi->tgl_expired)) {
    $carbonSelesai = Carbon::parse($lisensi->tgl_expired);
} else {
    // --- Fallback: LOGIKA TANGGAL BERAKHIR SESUAI TANGGAL LAHIR ---
    if (!empty($profile->tanggal_lahir)) {
        $tglLahir = Carbon::parse($profile->tanggal_lahir);
        // Ganti addYears(3) menjadi addYears(2) jika masa berlaku seharusnya 2 tahun
        $tahunExpired = $carbonDate->copy()->addYears(3)->year; 
        $carbonSelesai = Carbon::create($tahunExpired, $tglLahir->month, $tglLahir->day);
    } else {
        $carbonSelesai = $carbonDate->copy()->addYears(3);
    }
}

$tglSelesaiIndo = $carbonSelesai->translatedFormat('d F Y');
       
        // -----------------------------------------------------------

        $tglTerbitIndo = Carbon::parse($lisensi->tgl_terbit ?? now())->translatedFormat('d F Y');
        // =================================================================
        // FIX NOMOR: BEDA KREDENSIAL (KRED) DAN UJI KOMPETENSI (SERKOM)
        // =================================================================
        $idMentah = $lisensi->id;
        $nomorUrutMurni = str_pad($idMentah, 3, '0', STR_PAD_LEFT);
        $tahunSurat = \Carbon\Carbon::parse($lisensi->tgl_terbit)->format('Y');
        
        $jenisSurat = ($lisensi->metode === 'interview_only') ? 'KRED' : 'SERKOM';
        $lisensi->nomor = "800.2.4.2.1/{$nomorUrutMurni}/418.101/{$jenisSurat}/{$tahunSurat}";

        $verifyUrl = route('sertifikat.verify', ['id' => $lisensi->id]);

        // ======================================================================
        // 1. GENERATE BUNDLE PDF (SPK -> SPKK -> RKK)
        // ======================================================================
// ======================================================================
            // 1. GENERATE BUNDLE PDF (SPK -> SPKK -> RKK)
            // ======================================================================
            if ($pengajuan->metode == 'interview_only') {
                $jadwal = $pengajuan->jadwalWawancara;
                $penilaian = $jadwal ? $jadwal->penilaian : null;
                
                // Siapkan variabel default untuk MPP & PPI
                $isMpp = false;
                $rkkMpp = [];
                $isPpi = false;
                $rkkPpi = [];
                $rkkDraft = [];

                if ($penilaian && !empty($penilaian->detail_penilaian)) {
                    $detailPenilaian = is_string($penilaian->detail_penilaian) ? json_decode($penilaian->detail_penilaian, true) : $penilaian->detail_penilaian;
                    $rkkDraft = $detailPenilaian['rkk'] ?? $detailPenilaian; 
                    
                    // Cek & Ambil Data MPP
                    $isMpp = (($detailPenilaian['is_mpp'] ?? '0') == '1') || 
                             collect($rkkDraft)->contains(function($value, $key) { 
                                 return strpos((string)$key, 'MPP') !== false; 
                             });
                    $rkkMpp = $isMpp ? $this->getMppItems() : [];

                    // [KODE BARU] Cek & Ambil Data PPI
                    $isPpi = (($detailPenilaian['is_ppi'] ?? '0') == '1') || 
                             collect($rkkDraft)->contains(function($value, $key) { 
                                 return strpos((string)$key, 'PPI') !== false; 
                             });
                    $rkkPpi = $isPpi ? $this->getPpiItems() : [];
                }

          $kfkArray = $pengajuan->kfk ?? [];

// FIX: Jika $kfkArray adalah string JSON dari database, ubah dulu jadi Array
if (is_string($kfkArray)) {
    $kfkArray = json_decode($kfkArray, true) ?? [];
}

$namaJenjang = '-'; $namaKfk = '-';

// Sekarang foreach sudah aman karena $kfkArray dipastikan berupa array
foreach ((array) $kfkArray as $item) {
    if (str_contains($item, 'PK') || str_contains($item, 'BK')) {
        $namaJenjang = $item;
    } else {
        $namaKfk = $item;
    }
}

            $namaKfkBersih = trim($namaKfk);
    $mapJenjang = [
    'PK 1' => 'PK I', 'PK 2' => 'PK II', 'PK 3' => 'PK III', 'PK 4' => 'PK IV', 'PK 5' => 'PK V',
    'BK 1' => 'BK I', 'BK 2' => 'BK II', 'BK 3' => 'BK III', 'BK 4' => 'BK IV', 'BK 5' => 'BK V',
];
$jenjangQuery = $mapJenjang[$namaJenjang] ?? $namaJenjang;

// Ubah where() menjadi whereIn()
$rkkItems = \App\Models\BankItemRkk::where('kfk', 'like', "%{$namaKfkBersih}%")
    ->whereIn('jenjang_klinis', array_unique([$namaJenjang, $jenjangQuery]))
    ->orderBy('kategori_tugas', 'asc')
    ->orderBy('no_item', 'asc')
    ->get();
            $rkkGrouped = $rkkItems->groupBy('kategori_tugas');

            $bidang = str_contains($namaJenjang, 'PK') ? 'KEPERAWATAN' : (str_contains($namaJenjang, 'BK') ? 'KEBIDANAN' : 'UMUM');

         $verifyUrlRkk = route('sertifikat.verify', ['id' => $lisensi->id]) . '?doc=rkk';
            $verifyUrlSpk = route('sertifikat.verify', ['id' => $lisensi->id]) . '?doc=spk';

            // QR UNTUK RKK (Pakai URL RKK)
            $qrBambang = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode("Ketua Komite Keperawatan: BAMBANG WAHYU SETYONO. Validasi: " . $verifyUrlRkk);
            $qrHaris = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode("Ketua Sub Komite Kredensial: HARIS LISTYAWAN. Validasi: " . $verifyUrlRkk);
            $qrDirekturRkk = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode("Direktur UOBK RSUD SLG: dr. TONY WIDYANTO. Validasi: " . $verifyUrlRkk);
            
            // QR UNTUK SPK & SPKK (Pakai URL SPK)
            $qrDirekturSpk = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode("Direktur UOBK RSUD SLG: dr. TONY WIDYANTO. Validasi: " . $verifyUrlSpk);

            // === [DATA UNTUK RKK] ===
            
            $dataPDF = [
                'tglMulai' => $tglMulaiIndo,
                'tglAkhir' => $tglSelesaiIndo,
                'tglTerbit' => $tglTerbitIndo,
                'bidang' => $bidang,
                'namaLengkap' => strtoupper($namaLengkap),
                'unitKerja' => strtoupper($pekerjaan->unit_kerja ?? 'RSUD SLG'),
                'nip' => $profile->nip ?? ($profile->nirp ?? '-'),
                'pendidikanFormal' => strtoupper($pendidikan ? ($pendidikan->jenjang . ' ' . $pendidikan->jurusan) : '-'),
                'kualifikasi' => strtoupper($jenjangQuery . ' ' . $namaKfk),
                'namaJenjang' => $jenjangQuery,
                'rkkGrouped' => $rkkGrouped,
                'rkkDraft' => $rkkDraft,
                'isMpp'    => $isMpp,
                'isPpi'    => $isPpi,     // <--- [BARU] Kirim flag PPI ke PDF
                'rkkPpi'   => $rkkPpi,
                'rkkMpp'   => $rkkMpp,
                'qrBambang' => $qrBambang,
                'qrHaris' => $qrHaris,
                'qrDirektur' => $qrDirekturRkk,
            ];

            // DATA SPK & SPKK
            $kodeSpkk = '3827';
            $kodeSpk  = '3828';
            if (preg_match('/(I{1,3}|IV|V|1|2|3|4|5)$/i', trim($jenjangQuery), $matches)) {
                $level = strtoupper($matches[1]);
                if ($level === '1' || $level === 'I') { $kodeSpkk = '3827'; $kodeSpk = '3828'; }
                elseif ($level === '2' || $level === 'II') { $kodeSpkk = '4050'; $kodeSpk = '4051'; }
                elseif ($level === '3' || $level === 'III') { $kodeSpkk = '4300'; $kodeSpk = '4301'; }
                elseif ($level === '4' || $level === 'IV') { $kodeSpkk = '4302'; $kodeSpk = '4303'; }
            }

            $formatNoSpkk = "100.3/{$kodeSpkk}.{$nomorUrutMurni}/418.101/{$tahunSurat}";
            $formatNoSpk  = "100.3/{$kodeSpk}.{$nomorUrutMurni}/418.101/{$tahunSurat}";

            $profesiTitle = 'STAF KLINIS';
            $jenjangKlinis = $jenjangQuery;
            if (str_contains($jenjangQuery, 'PK')) {
                $profesiTitle = 'STAF KEPERAWATAN';
                $jenjangKlinis = str_replace('PK', 'PERAWAT KLINIS', $jenjangQuery);
            } elseif (str_contains($jenjangQuery, 'BK')) {
                $profesiTitle = 'STAF KEBIDANAN';
                $jenjangKlinis = str_replace('BK', 'BIDAN KLINIS', $jenjangQuery);
            }

            $dataSpkSpkk = [
                'is_individu'           => true, 
                'nama_pegawai_individu' => strtoupper($namaLengkap),
                'no_spkk'               => $formatNoSpkk,
                'no_spk'                => $formatNoSpk,
                'profesi_title'         => $profesiTitle,
                'nama_kualifikasi'      => $jenjangKlinis . ' (' . ($namaKfkBersih ?: 'UMUM') . ')',
                'tgl_terbit'            => $tglTerbitIndo,
                'qr_url'                => $qrDirekturSpk,
                'tgl_expired'           => $tglSelesaiIndo,
            
                'direktur_nama'         => 'dr. TONY WIDYANTO, Sp.OG (K)',
                'direktur_nip'          => '19750714 200212 1 006',
                'direktur_pangkat'      => 'Pembina Tingkat I / IV b'
            ];

            // RENDER URUTAN: SPK -> SPKK -> RKK
            $htmlSpk  = view('perawat.dokumen.pdf_spk', $dataSpkSpkk)->render();
            $htmlSpkk = view('perawat.dokumen.pdf_spkk', $dataSpkSpkk)->render();
            $htmlRkk  = view('perawat.pengajuan.kredensial_pdf', $dataPDF)->render();

            $finalHtml = $htmlSpk . '<div style="page-break-after: always;"></div>' . $htmlSpkk . '<div style="page-break-after: always;"></div>' . $htmlRkk;

            $pdf = Pdf::setOptions(['isRemoteEnabled' => true])->loadHTML($finalHtml)->setPaper('a4', 'portrait');
            $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $namaLengkap);
            
            return $pdf->download('Kredensialing_Bundle_' . $cleanName . '.pdf');

        } else {
            // ======================================================================
            // 2. GENERATE PDF SERTIFIKAT UJI KOMPETENSI (TETAP SAMA)
            // ======================================================================
             $rawKfk = !empty($pengajuan->kfk) ? $pengajuan->kfk : $lisensi->kfk;
             $jenjangKompetensi = '-';
             if (is_array($rawKfk)) {
                $jenjangKompetensi = implode(', ', $rawKfk);
             } elseif (is_string($rawKfk)) {
                $jenjangKompetensi = str_replace(['[', ']', '"'], '', $rawKfk);
             }
            $kfkValue = strtoupper($jenjangKompetensi);
            $themeColor = '#512da8'; // Default Ungu
            
            if (str_contains($kfkValue, 'KRITIS')) { $themeColor = '#c62828'; } 
            elseif (str_contains($kfkValue, 'MEDIKAL BEDAH')) { $themeColor = '#1565c0'; } 
            elseif (str_contains($kfkValue, 'ANAK')) { $themeColor = '#2e7d32'; } 
            elseif (str_contains($kfkValue, 'MATERNITAS')) { $themeColor = '#ad1457'; }

            $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($verifyUrl);
           
             $dataPDF = [
                'user' => $user,
                'theme_color' => $themeColor,
                'profile' => $profile,
                'lisensi' => $lisensi,
                'pengajuan' => $pengajuan,
                'jenjang_kompetensi' => strtoupper($jenjangKompetensi),
                'tgl_ujian_indo' => $tglMulaiIndo,
                'tgl_expired_indo' => $tglSelesaiIndo,
                'tgl_terbit_indo' => $tglTerbitIndo,
                'nomor_surat' => $lisensi->nomor_surat_full,
                'qrCodeUrl' => $qrCodeUrl,
                'verifyUrl' => $verifyUrl
            ];
            
            
        $pdf = Pdf::setOptions(['isRemoteEnabled' => true])->loadView('perawat.pengajuan.sertifikat_pdf', $dataPDF)->setPaper('a4', 'landscape');
            $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $namaLengkap);
            return $pdf->download('Sertifikat_Kompetensi_' . $cleanName . '.pdf');
        }
    }
}