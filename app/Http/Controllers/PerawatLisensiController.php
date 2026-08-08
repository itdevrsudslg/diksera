<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSertifikat;
use App\Models\PerawatLisensi;
use App\Models\PerawatPekerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // <--- TAMBAHKAN INI
use Barryvdh\DomPDF\Facade\Pdf; // <--- TAMBAHKAN INI

class PerawatLisensiController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        // Ambil data lisensi milik user login
        // Diurutkan berdasarkan tanggal terbit terbaru
        $data = PerawatLisensi::where('user_id', $user->id)
            ->latest('tgl_terbit')
            ->get();

        // Pastikan file view ini ada di: resources/views/perawat/lisensi/index.blade.php
        return view('perawat.dokumen.lisensi.index', compact('data'));
    }
    private function checkEligibility($userId, $requestedMetode)
    {
        $latestJob = PerawatPekerjaan::where('user_id', $userId)->orderBy('tahun_mulai', 'desc')->first();

        if (!$latestJob) return ['allowed' => false, 'message' => 'Lengkapi Riwayat Pekerjaan dahulu.'];

        $currentUnit = $latestJob->unit_kerja;

        // Gunakan 'metode' sesuai nama kolom DB
        $existingLisensi = PerawatLisensi::where('user_id', $userId)
            ->where('unit_kerja_saat_buat', $currentUnit)
            ->where('metode', $requestedMetode)
            ->exists();

        if ($existingLisensi) {
            return ['allowed' => false, 'message' => "Anda sudah membuat pengajuan di unit $currentUnit."];
        }

        return ['allowed' => true, 'unit_kerja' => $currentUnit];
    }

    public function lisensiCreate($metode)
    {
        if (!in_array($metode, ['interview_only', 'pg_interview'])) {
            return back()->with('swal', ['icon' => 'error', 'title' => 'Error', 'text' => 'Metode salah']);
        }
        $user = Auth::user();
        $check = $this->checkEligibility($user->id, $metode);

        if (!$check['allowed']) {
            return redirect()->route('perawat.lisensi.index')->with('swal', ['icon' => 'error', 'title' => 'Akses Ditolak', 'text' => $check['message']]);
        }

        return view('perawat.dokumen.lisensi.create', [
            'user' => $user,
            'unit_kerja' => $check['unit_kerja'],
            'metode' => $metode
        ]);
    }

    public function lisensiStore(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:100',
            'lembaga' => 'required|string|max:100',
            'bidang' => 'required|string|max:100',
            'kfk' => 'required|array',
            'tgl_mulai' => 'required|date',
            'tgl_diselenggarakan' => 'required|date',
            'tgl_terbit' => 'required|date',
            'tgl_expired' => 'required|date',
            'metode_perpanjangan' => 'required|in:interview_only,pg_interview', // Validasi input form
            'dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $check = $this->checkEligibility($user->id, $request->metode_perpanjangan);
        if (!$check['allowed']) {
            return redirect()->route('perawat.lisensi.index')->with('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => $check['message']]);
        }

       $tahun = date('Y');
$count = PerawatLisensi::whereYear('created_at', $tahun)->count() + 1;
$nomorOtomatis = sprintf("%03d", $count); //

        $data = [
            'user_id' => $user->id,
            'nomor' => $nomorOtomatis,
            'nama' => $request->nama,
            'lembaga' => $request->lembaga,
            'bidang' => $request->bidang,
            'kfk' => json_encode($request->kfk),
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_diselenggarakan' => $request->tgl_diselenggarakan,
            'tgl_terbit' => $request->tgl_terbit,
            'tgl_expired' => $request->tgl_expired,
            'unit_kerja_saat_buat' => $check['unit_kerja'],

            // PENTING: Map input 'metode_perpanjangan' ke kolom DB 'metode'
            'metode' => $request->metode_perpanjangan,

            // Status Awal Pending
            'status' => 'pending',
        ];

        if ($request->hasFile('dokumen')) {
            $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/lisensi', 'public');
        }

        $lisensiBaru = PerawatLisensi::create($data);

        // Buat Tiket Pengajuan (Metode NULL = New Submission)
        PengajuanSertifikat::create([
            'user_id' => $user->id,
            'lisensi_lama_id' => $lisensiBaru->id,
            'metode' => null, // NULL untuk menandai Pengajuan Baru
            'status' => 'pending',
            'keterangan' => 'Permohonan Lisensi Baru (Menunggu Verifikasi Admin)'
        ]);

        return redirect()->route('perawat.lisensi.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil Diajukan',
            'text' => 'Data lisensi berhasil disimpan. Menunggu persetujuan Admin.'
        ]);
    }

public function downloadHasil($id)
{
    $pengajuan = PengajuanSertifikat::with([
        'jadwalWawancara.penilaian',
        'lisensiLama',
        'user'
    ])
    ->where('user_id', Auth::id())
    ->findOrFail($id);

    /**
     * METODE AKTIF (SUMBER KEBENARAN)
     * 1. pengajuan.metode (kalau perpanjangan)
     * 2. lisensiLama.metode (kalau pengajuan baru)
     */
    $metodeAktif = $pengajuan->metode 
        ?? $pengajuan->lisensiLama->metode 
        ?? null;

    if (!$metodeAktif) {
        return back()->with('swal', [
            'icon' => 'error',
            'title' => 'Metode Tidak Diketahui',
            'text' => 'Metode evaluasi tidak ditemukan.'
        ]);
    }

    /**
     * ===============================
     * KREDENSIALING (UPLOAD FILE)
     * ===============================
     */
    if ($metodeAktif === 'interview_only') {

        $jadwal = $pengajuan->jadwalWawancara;

        if (
            !$jadwal ||
            !$jadwal->penilaian ||
            !$jadwal->penilaian->file_hasil
        ) {
            return back()->with('swal', [
                'icon' => 'error',
                'title' => 'Dokumen Belum Tersedia',
                'text' => 'SK Kredensial belum diunggah oleh pewawancara.'
            ]);
        }

        $filePath = $jadwal->penilaian->file_hasil;

        if (!Storage::disk('public')->exists($filePath)) {
            return back()->with('swal', [
                'icon' => 'error',
                'title' => 'File Tidak Ditemukan',
                'text' => 'File SK tidak ditemukan di server.'
            ]);
        }

        $fullPath  = storage_path('app/public/' . $filePath);
        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);

        $slugName = str_replace(' ', '_', strtolower($pengajuan->user->name));
        $downloadName = "SK_Kredensial_{$slugName}_{$pengajuan->id}.{$extension}";

        return response()->download($fullPath, $downloadName);
    }

    /**
     * ===============================
     * UJI KOMPETENSI (GENERATE PDF)
     * ===============================
     */
    if ($pengajuan->status !== 'completed') {
        return back()->with('swal', [
            'icon' => 'error',
            'title' => 'Belum Tersedia',
            'text' => 'Sertifikat hanya tersedia setelah lulus.'
        ]);
    }

   $lisensi = $pengajuan->lisensiLama;
$tahunSurat = $lisensi ? \Carbon\Carbon::parse($lisensi->tgl_terbit)->format('Y') : date('Y');
$nomorUrut = $lisensi ? preg_replace('/[^0-9]/', '', $lisensi->nomor) : '000';
$nomorUrut = str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);

$nomorFull = "800.2.4.2.1/{$nomorUrut}/418.101/SERKOM/{$tahunSurat}";

$data = [
    'nama'          => $pengajuan->user->name,
    'nomor_lisensi' => $nomorFull, // Menggunakan format baru
    'tanggal_lulus' => $pengajuan->updated_at->translatedFormat('d F Y'),
    'jenis_lisensi' => $lisensi->nama ?? 'Sertifikat Kompetensi',
];

    $pdf = Pdf::loadView('perawat.dokumen.pdf_sertifikat', compact('data'))
        ->setPaper('a4', 'landscape');

    return $pdf->stream("Sertifikat_Kompetensi_{$pengajuan->id}.pdf");
}

/**
     * ========================================================
     * HELPER: NORMALISASI JENJANG & KOREKSI PROFESI
     * ========================================================
     */
    private function normalizeJenjang($string, $profesi = '')
    {
        if (!$string) return '-';
        $string = strtoupper(trim($string));
        
        $type = '';
        $level = '';

        // Tangkap pola PK/BK dan angkanya
        if (preg_match('/(PK|BK)\s*([1-5]|I{1,3}|IV|V)/', $string, $matches)) {
            $type = $matches[1];
            $level = $matches[2];
        } elseif (preg_match('/(PK|BK)/', $string, $matches)) {
            $type = $matches[1];
        }

        // KOREKSI OTOMATIS: Jika Bidan wajib BK, Jika Perawat wajib PK.
        $profesiStr = strtolower(trim($profesi));
        if (str_contains($profesiStr, 'bidan')) {
            $type = 'BK';
        } elseif (str_contains($profesiStr, 'perawat')) {
            $type = 'PK';
        }

        // Konversi angka ke Romawi
        if ($type && $level) {
            $map = ['1' => 'I', '2' => 'II', '3' => 'III', '4' => 'IV', '5' => 'V'];
            $level = $map[$level] ?? $level;
            return $type . ' ' . $level;
        } elseif ($type) {
            return $type; 
        }

        return '-';
    }

/**
     * ========================================================
     * FUNGSI PERSIAPAN DATA SPK & SPKK 
     * ========================================================
     */
   /**
     * ========================================================
     * FUNGSI PERSIAPAN DATA SPK & SPKK 
     * ========================================================
     */
    private function prepareDataSurat($pengajuan_id, $isIndividu = false)
    {
        $pengajuan = \App\Models\PengajuanSertifikat::with(['user.profile', 'lisensiLama'])->findOrFail($pengajuan_id);
        $lisensiUtama = $pengajuan->lisensiLama;
        $nomorKolektif = $lisensiUtama->nomor;

        // Pastikan KFK Utama berupa string
        $kfkUtamaStr = is_array($lisensiUtama->kfk) ? implode(' ', $lisensiUtama->kfk) : $lisensiUtama->kfk;
        
        // 1. Dapatkan Jenjang Utama (PK/BK) dari dokumen ini
        $profesiUtama = $pengajuan->user->profile->profesi ?? '';
        $jenjangUtama = $this->normalizeJenjang(($lisensiUtama->bidang . ' ' . $kfkUtamaStr), $profesiUtama);

        // 2. Tarik semua perawat dengan nomor kolektif yang sama
        $pesertaBatch = \App\Models\PerawatLisensi::with(['user.profile'])->where('nomor', $nomorKolektif)->get();
        $listPeserta = [];
        $no = 1;

        // Looping untuk menyaring siapa saja yang masuk ke tabel lampiran
        foreach($pesertaBatch as $lis) {
            $prof = $lis->user->profile;
            $pek = \App\Models\PerawatPekerjaan::where('user_id', $lis->user_id)->orderBy('tahun_mulai', 'desc')->first();
            
            // Pastikan KFK Peserta berupa string
            $kfkPesertaStr = is_array($lis->kfk) ? implode(' ', $lis->kfk) : $lis->kfk;
            
            // Cari Jenjang PK/BK Peserta
            $gabunganKfkPeserta = ($lis->bidang ?? '') . ' ' . $kfkPesertaStr;
            $jenjangPeserta = $this->normalizeJenjang($gabunganKfkPeserta, $prof->profesi ?? '');
            
            // FILTER KETAT: Jika PK/BK peserta beda dengan PK/BK dokumen utama, BUANG!
            if ($jenjangPeserta !== $jenjangUtama) {
                continue; 
            }

            // PERBAIKAN JENIS KELAMIN (Cek berbagai format: L, Laki-laki, Laki - laki, pria)
            $jk = '-';
            if ($prof && $prof->jenis_kelamin) {
                $jkStr = strtolower(trim($prof->jenis_kelamin));
                $jk = (str_contains($jkStr, 'laki') || $jkStr === 'l' || $jkStr === 'pria') ? 'L' : 'P';
            }

            // Bersihkan data pangkat/golongan dari teks "none"
            $pangkat = $prof->pangkat ?? '-';
            $golongan = $prof->golongan ?? '-';
            if (strtolower(trim($pangkat)) === 'none') $pangkat = '-';
            if (strtolower(trim($golongan)) === 'none') $golongan = '-';
            $pangkatGolongan = ($pangkat === '-' && $golongan === '-') ? '-' : $pangkat . ' / ' . $golongan;

            $listPeserta[] = [
                'no'               => $no++,
                'nama_pegawai'     => $prof->nama_lengkap ?? $lis->user->name,
                'ruang'            => $pek->unit_kerja ?? '-',
                'profesi'          => $prof->profesi ?? '-',
                'jk'               => $jk,
                'pangkat_golongan' => $pangkatGolongan,
                'pk'               => $jenjangPeserta // Pasti rapi jadi PK I atau BK I
            ];
        }

        // Ambil No Urut
        $baseFormat = explode('/', $nomorKolektif);
        $noUrut = $baseFormat[0] ?? '000';

        // ==========================================
        // LOGIKA TANGGAL TERBIT DAN EXPIRED (INDIVIDU/BATCH)
        // ==========================================
        $tglTerbitObj = \Carbon\Carbon::parse($lisensiUtama->tgl_terbit ?? now());
        $tglTerbitFormatted = $tglTerbitObj->translatedFormat('d F Y');
        $tahunTerbit = $tglTerbitObj->year; 
        
        $profile = $pengajuan->user->profile;
        if ($profile && $profile->tanggal_lahir) {
            $tglLahirObj = \Carbon\Carbon::parse($profile->tanggal_lahir);
            // Tanggal lahir di tahun terbit + 3
            $tglExpiredObj = \Carbon\Carbon::createFromDate($tglTerbitObj->year + 3, $tglLahirObj->month, $tglLahirObj->day);
        } else {
            $tglExpiredObj = $tglTerbitObj->copy()->addYears(3);
        }
        $tglExpiredFormatted = $tglExpiredObj->translatedFormat('d F Y');

        // ==========================================
        // LOGIKA PENOMORAN SURAT BERDASARKAN LEVEL
        // ==========================================
        $kodeSpkk = '3827'; 
        $kodeSpk  = '3828'; 
        
        if (preg_match('/(I{1,3}|IV|V|1|2|3|4|5)$/i', trim($jenjangUtama), $matches)) {
            $level = strtoupper($matches[1]);
            if ($level === '1' || $level === 'I') { $kodeSpkk = '3827'; $kodeSpk  = '3828'; } 
            elseif ($level === '2' || $level === 'II') { $kodeSpkk = '4050'; $kodeSpk  = '4051'; } 
            elseif ($level === '3' || $level === 'III') { $kodeSpkk = '4300'; $kodeSpk  = '4301'; } 
            elseif ($level === '4' || $level === 'IV') { $kodeSpkk = '4302'; $kodeSpk  = '4303'; }
        }

        $formatNoSpkk = "100.3/{$kodeSpkk}.{$noUrut}/418.101/{$tahunTerbit}";
        $formatNoSpk  = "100.3/{$kodeSpk}.{$noUrut}/418.101/{$tahunTerbit}";

        // Mapping Judul Profesi (Keperawatan / Kebidanan)
        $profesiTitle = 'STAF KLINIS';
        $jenjangKlinis = $jenjangUtama;
        if (str_contains($jenjangUtama, 'PK')) {
            $profesiTitle = 'STAF KEPERAWATAN';
            $jenjangKlinis = str_replace('PK', 'PERAWAT KLINIS', $jenjangUtama);
        } elseif (str_contains($jenjangUtama, 'BK')) {
            $profesiTitle = 'STAF KEBIDANAN';
            $jenjangKlinis = str_replace('BK', 'BIDAN KLINIS', $jenjangUtama);
        }

        // Ekstrak Spesialisasi dari gabungan Bidang dan KFK
        $gabunganKfkUtama = $lisensiUtama->bidang . ' ' . $kfkUtamaStr;
        $spesialisasi = preg_replace('/(PK|BK)\s*([1-5]|I{1,3}|IV|V)/i', '', strtoupper($gabunganKfkUtama));
        $spesialisasi = trim(preg_replace('/[^A-Z\s]/', '', $spesialisasi));
        if (empty($spesialisasi)) $spesialisasi = 'UMUM';

        $namaKualifikasi = $jenjangKlinis . ' (' . $spesialisasi . ')';

        return [
            'pengajuan'             => $pengajuan,
            'list_peserta'          => $listPeserta, 
            'no_spkk'               => $formatNoSpkk,
            'no_spk'                => $formatNoSpk, 
            'profesi_title'         => $profesiTitle,
            'nama_kualifikasi'      => $namaKualifikasi,
            'tgl_terbit'            => $tglTerbitFormatted,
            'tgl_expired'           => $tglExpiredFormatted,
            'qr_url'                => "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode(route('sertifikat.verify', $pengajuan->id)),
            'direktur_nama'         => 'dr. TONY WIDYANTO, Sp.OG (K)',
            'direktur_nip'          => '19750714 200212 1 006',
            'direktur_pangkat'      => 'Pembina Tingkat I / IV b',
            
            // Data flag untuk penentu tampilan di Blade
            'is_individu'           => $isIndividu,
            'nama_pegawai_individu' => strtoupper($pengajuan->user->profile->nama_lengkap ?? $pengajuan->user->name),
        ];
    }


   public function printSpkMassalDashboard($jenjang_klinis)
    {
        // 1. Dekode URL (Misal "PK I" dari URL %20)
        $jenjangQuery = urldecode($jenjang_klinis);
        
        // 2. Tarik SEMUA data lisensi yang berstatus aktif/expired dari SEMUA perawat
        $semuaLisensi = \App\Models\PerawatLisensi::with(['user.profile'])
            ->whereIn('status', ['active', 'expired'])
            ->orderBy('tgl_terbit', 'desc') 
            ->get();

        // 3. LOGIKA FILTERING (Anti Duplikat & Anti Muncul di Jenjang Rendah)
        $userTertinggi = [];

        foreach ($semuaLisensi as $lis) {
            $userId = $lis->user_id;
            $prof = $lis->user->profile;
            if (!$prof) continue; 
            
            $profesi = $prof->profesi ?? '';
            
            // Susun dan bersihkan nama Jenjang
            $kfkStr = is_array($lis->kfk) ? implode(' ', $lis->kfk) : $lis->kfk;
            $gabungan = ($lis->bidang ?? '') . ' ' . $kfkStr;
            $jenjangText = $this->normalizeJenjang($gabungan, $profesi); 
            
            $levelValue = 0;
            if (preg_match('/(I{1,3}|IV|V|1|2|3|4|5)$/i', trim($jenjangText), $matches)) {
                $lvlStr = strtoupper($matches[1]);
                if ($lvlStr === '1' || $lvlStr === 'I') $levelValue = 1;
                elseif ($lvlStr === '2' || $lvlStr === 'II') $levelValue = 2;
                elseif ($lvlStr === '3' || $lvlStr === 'III') $levelValue = 3;
                elseif ($lvlStr === '4' || $lvlStr === 'IV') $levelValue = 4;
                elseif ($lvlStr === '5' || $lvlStr === 'V') $levelValue = 5;
            }

            // SIMPAN HANYA JENJANG TERTINGGI
            if (!isset($userTertinggi[$userId]) || $levelValue > $userTertinggi[$userId]['level']) {
                $userTertinggi[$userId] = [
                    'level' => $levelValue,
                    'jenjang_text' => $jenjangText,
                    'lisensi' => $lis
                ];
            }
        }

        // 4. Siapkan Array untuk List Tabel & Cari Tahun DB
        $listPeserta = [];
        $no = 1;
        
        $tahunExpiredMassal = date('Y') + 3; 
        $tglTerbitMassal = null; // Menampung tanggal terbit dari DB

        foreach ($userTertinggi as $userId => $dataUser) {
            if ($dataUser['jenjang_text'] === $jenjangQuery) {
                
                $lis = $dataUser['lisensi'];
                $prof = $lis->user->profile;
                $pek = \App\Models\PerawatPekerjaan::where('user_id', $userId)->orderBy('tahun_mulai', 'desc')->first();
                
                // AMBIL TGL TERBIT TERBARU DARI DATABASE
                if ($lis->tgl_terbit) {
                    if (!$tglTerbitMassal || $lis->tgl_terbit > $tglTerbitMassal) {
                        $tglTerbitMassal = $lis->tgl_terbit;
                    }
                }

                // AMBIL TAHUN EXPIRED DARI DATABASE
                if ($lis->tgl_expired) {
                    $tahunLisensi = \Carbon\Carbon::parse($lis->tgl_expired)->year;
                    if ($tahunLisensi > $tahunExpiredMassal || $no === 1) {
                        $tahunExpiredMassal = $tahunLisensi;
                    }
                }

                // PERBAIKAN JENIS KELAMIN (Cek L/Pria/Laki-laki)
                $jk = '-';
                if ($prof && $prof->jenis_kelamin) {
                    $jkStr = strtolower(trim($prof->jenis_kelamin));
                    $jk = (str_contains($jkStr, 'laki') || $jkStr === 'l' || $jkStr === 'pria') ? 'L' : 'P';
                }

                $pangkat = $prof->pangkat ?? '-';
                $golongan = $prof->golongan ?? '-';
                if (strtolower(trim($pangkat)) === 'none') $pangkat = '-';
                if (strtolower(trim($golongan)) === 'none') $golongan = '-';
                $pangkatGolongan = ($pangkat === '-' && $golongan === '-') ? '-' : $pangkat . ' / ' . $golongan;

                $listPeserta[] = [
                    'no'               => $no++,
                    'nama_pegawai'     => $prof->nama_lengkap ?? $lis->user->name,
                    'ruang'            => $pek->unit_kerja ?? '-',
                    'profesi'          => $prof->profesi ?? '-',
                    'jk'               => $jk,
                    'pangkat_golongan' => $pangkatGolongan,
                    'pk'               => $jenjangQuery
                ];
            }
        }

        if (empty($listPeserta)) {
            return back()->with('swal', ['icon' => 'error', 'title' => 'Data Bersih', 'text' => "Tidak ada perawat murni di jenjang {$jenjangQuery}."]);
        }

        // 5. Setup Nomor & Tanggal (AMBIL DARI DB SEKARANG, BUKAN HARI INI)
        if (!$tglTerbitMassal) $tglTerbitMassal = date('Y-m-d'); // Fallback jika anehnya kosong
        
        $tglTerbitObj = \Carbon\Carbon::parse($tglTerbitMassal);
        $tahunTerbit = $tglTerbitObj->year;
        $tglTerbitFormatted = $tglTerbitObj->translatedFormat('d F Y');
        
        // MENGGUNAKAN TAHUN DARI DATABASE TGL_EXPIRED
        $tglExpiredFormatted = "tanggal dan bulan kelahiran tahun " . $tahunExpiredMassal;
        
        $kodeSpkk = '3827'; 
        $kodeSpk  = '3828'; 
        if (preg_match('/(I{1,3}|IV|V|1|2|3|4|5)$/i', trim($jenjangQuery), $matches)) {
            $level = strtoupper($matches[1]);
            if ($level === '1' || $level === 'I') { $kodeSpkk = '3827'; $kodeSpk  = '3828'; } 
            elseif ($level === '2' || $level === 'II') { $kodeSpkk = '4050'; $kodeSpk  = '4051'; } 
            elseif ($level === '3' || $level === 'III') { $kodeSpkk = '4300'; $kodeSpk  = '4301'; } 
            elseif ($level === '4' || $level === 'IV') { $kodeSpkk = '4302'; $kodeSpk  = '4303'; }
        }

        $formatNoSpkk = "100.3/{$kodeSpkk}.MASSAL/418.101/{$tahunTerbit}";
        $formatNoSpk  = "100.3/{$kodeSpk}.MASSAL/418.101/{$tahunTerbit}";

        $profesiTitle = str_contains($jenjangQuery, 'PK') ? 'STAF KEPERAWATAN' : 'STAF KEBIDANAN';
        $jenjangKlinis = str_contains($jenjangQuery, 'PK') 
            ? str_replace('PK', 'PERAWAT KLINIS', $jenjangQuery) 
            : str_replace('BK', 'BIDAN KLINIS', $jenjangQuery);

        $namaKualifikasi = $jenjangKlinis . ' (UMUM / BERBAGAI SPESIALISASI)';

        $data = [
            'list_peserta'     => $listPeserta, 
            'no_spkk'          => $formatNoSpkk,
            'no_spk'           => $formatNoSpk, 
            'profesi_title'    => $profesiTitle,
            'nama_kualifikasi' => $namaKualifikasi,
            'tgl_terbit'       => $tglTerbitFormatted,
            'tgl_expired'      => $tglExpiredFormatted,
            'qr_url'           => "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode(url('/')),
            'direktur_nama'    => 'dr. TONY WIDYANTO, Sp.OG (K)',
            'direktur_nip'     => '19750714 200212 1 006',
            'direktur_pangkat' => 'Pembina Tingkat I / IV b',
            'is_individu'      => false, 
        ];

        // 6. Cetak PDF
        $namaFile = "SPK_MASSAL_" . str_replace(' ', '_', $jenjangQuery) . ".pdf";
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('perawat.dokumen.pdf_spk', $data)->setPaper('a4', 'portrait');
        return $pdf->stream($namaFile);
    }
    public function printSpkk(Request $request, $id)
    {
        // Cek URL apakah meminta mode individu: /cetak-spkk?mode=individu
        $isIndividu = $request->query('mode') === 'individu';
        $data = $this->prepareDataSurat($id, $isIndividu);
        
        $modeText = $isIndividu ? 'Individu' : 'Kolektif';
        $namaFile = "SPKK_{$modeText}_" . str_replace([' ', '/', '\\', '(', ')'], '_', $data['nama_kualifikasi']) . ".pdf";
        
        $pdf = Pdf::loadView('perawat.dokumen.pdf_spkk', $data)->setPaper('a4', 'portrait');
        return $pdf->stream($namaFile);
    }

    public function printSpk(Request $request, $id)
    {
        // Cek URL apakah meminta mode individu: /cetak-spk?mode=individu
        $isIndividu = $request->query('mode') === 'individu';
        $data = $this->prepareDataSurat($id, $isIndividu);
        
        $modeText = $isIndividu ? 'Individu' : 'Kolektif';
        $namaFile = "SPK_{$modeText}_" . str_replace([' ', '/', '\\', '(', ')'], '_', $data['nama_kualifikasi']) . ".pdf";
        
        $pdf = Pdf::loadView('perawat.dokumen.pdf_spk', $data)->setPaper('a4', 'portrait');
        return $pdf->stream($namaFile);
    }

}
