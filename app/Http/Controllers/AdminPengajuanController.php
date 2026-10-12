<?php

namespace App\Http\Controllers;
use App\Models\BankItemRkk;
use App\Models\WawancaraPenilaian;
use Illuminate\Support\Facades\Auth;
use App\Services\Penandatangan;

use App\Models\JadwalWawancara;
use App\Models\PenanggungJawabUjian;
use Illuminate\Http\Request;
use App\Models\PengajuanSertifikat;
use App\Models\PerawatLisensi;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
class AdminPengajuanController extends Controller
{
    /**
     * MENAMPILKAN DAFTAR PENGAJUAN (DENGAN FILTER LAMA)
     */
     /**
     * ========================================================
     * TERBITKAN SPK KOLEKTIF BERDASARKAN JENJANG KLINIS
     * ========================================================
     */
     /**
     * ========================================================
     * FUNGSI CETAK KHUSUS ADMIN (Tanpa Limitasi Auth::id)
     * ========================================================
     */
    public function cetakSerkom($id)
    {
        $pengajuan = \App\Models\PengajuanSertifikat::with([
            'user.perawatProfile', 
            'user.pendidikanTerakhir', 
            'lisensiLama', 
            'jadwalWawancara.penilaian'
        ])->findOrFail($id);
        
        $user = $pengajuan->user;
        $lisensi = $pengajuan->lisensiLama;
        
        if (!$lisensi) {
            return back()->with('swal', ['icon' => 'error', 'title' => 'Data Kosong', 'text' => 'Data Lisensi belum dibuat.']);
        }

        $profile = $user->perawatProfile;
        $pekerjaan = \App\Models\PerawatPekerjaan::where('user_id', $user->id)->latest()->first();
        $pendidikan = $user->pendidikanTerakhir;
        
        $namaLengkap = $profile->nama_lengkap ?? $user->name;

        \Carbon\Carbon::setLocale('id');
        $tglMulaiIndo = \Carbon\Carbon::parse($lisensi->tgl_mulai ?? now())->translatedFormat('d F Y');
        
        if (!empty($lisensi->tgl_expired)) {
            $tglSelesaiIndo = \Carbon\Carbon::parse($lisensi->tgl_expired)->translatedFormat('d F Y');
        } else {
            $tglSelesaiIndo = \Carbon\Carbon::parse($lisensi->tgl_mulai)->addYears(3)->translatedFormat('d F Y');
        }
        
        $tglTerbitIndo = \Carbon\Carbon::parse($lisensi->tgl_terbit ?? now())->translatedFormat('d F Y');

        $idMentah = $lisensi->id;
        $nomorUrutMurni = str_pad($idMentah, 3, '0', STR_PAD_LEFT);
        $tahunSurat = \Carbon\Carbon::parse($lisensi->tgl_terbit)->format('Y');
        $lisensi->nomor = "800.2.4.2.1/{$nomorUrutMurni}/418.101/SERKOM/{$tahunSurat}";
        
        $verifyUrl = route('sertifikat.verify', ['id' => $lisensi->id]);

        $rawKfk = !empty($pengajuan->kfk) ? $pengajuan->kfk : $lisensi->kfk;
        $jenjangKompetensi = '-';
        if (is_array($rawKfk)) {
            $jenjangKompetensi = implode(', ', $rawKfk);
        } elseif (is_string($rawKfk)) {
            $decoded = json_decode($rawKfk, true);
            if(is_array($decoded)) {
                $jenjangKompetensi = implode(', ', $decoded);
            } else {
                $jenjangKompetensi = str_replace(['[', ']', '"'], '', $rawKfk);
            }
        }
        
        $kfkValue = strtoupper($jenjangKompetensi);
        $themeColor = '#512da8'; 
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
            'nomor_surat' => $lisensi->nomor,
            'qrCodeUrl' => $qrCodeUrl,
            'verifyUrl' => $verifyUrl
        ];
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions(['isRemoteEnabled' => true])->loadView('perawat.pengajuan.sertifikat_pdf', $dataPDF)->setPaper('a4', 'landscape');
        $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $namaLengkap);
        
        return $pdf->stream('Sertifikat_Kompetensi_' . $cleanName . '.pdf');
    }
    

  public function cetakRkk($id)
    {
        // Proteksi: Pastikan data hanya milik user yang sedang login
        $pengajuan = PengajuanSertifikat::with([
            'user.perawatProfile', 
            'user.profile', // Di-load dua-duanya untuk jaga-jaga penamaan relasi
            'user.pendidikanTerakhir', 
            'lisensiLama', 
            'jadwalWawancara.penilaian'
        ])
        ->where('user_id', Auth::id()) 
        ->findOrFail($id);
        
        $user = $pengajuan->user;
        $lisensi = $pengajuan->lisensiLama;
        
        if (!$lisensi) {
            return back()->with('swal', ['icon' => 'error', 'title' => 'Data Kosong', 'text' => 'Data Lisensi belum dibuat.']);
        }

        // Fleksibilitas relasi profile
        $profile = $user->perawatProfile ?? $user->profile;
        $pekerjaan = \App\Models\PerawatPekerjaan::where('user_id', $user->id)->orderBy('tahun_mulai', 'desc')->first();
        $pendidikan = $user->pendidikanTerakhir;
        
        $namaLengkap = $profile->nama_lengkap ?? $user->name;

        \Carbon\Carbon::setLocale('id');
        $tglMulaiIndo = \Carbon\Carbon::parse($lisensi->tgl_mulai ?? now())->translatedFormat('d F Y');
        $tglSelesaiIndo = \Carbon\Carbon::parse($lisensi->tgl_expired ?? now()->addYears(3))->translatedFormat('d F Y');
        $tglTerbitIndo = \Carbon\Carbon::parse($lisensi->tgl_terbit ?? now())->translatedFormat('d F Y');

        $jadwal = $pengajuan->jadwalWawancara;
        $penilaian = $jadwal ? $jadwal->penilaian : null;
        
        $isMpp = false; $rkkMpp = [];
        $isPpi = false; $rkkPpi = [];
        $rkkDraft = [];

        if ($penilaian && !empty($penilaian->detail_penilaian)) {
            $detailPenilaian = is_string($penilaian->detail_penilaian) ? json_decode($penilaian->detail_penilaian, true) : $penilaian->detail_penilaian;
            $rkkDraft = $detailPenilaian['rkk'] ?? $detailPenilaian; 
            
            $isMpp = (($detailPenilaian['is_mpp'] ?? '0') == '1') || collect($rkkDraft)->contains(function($value, $key) { return strpos((string)$key, 'MPP') !== false; });
            $rkkMpp = $isMpp ? $this->getMppItems() : [];

            $isPpi = (($detailPenilaian['is_ppi'] ?? '0') == '1') || collect($rkkDraft)->contains(function($value, $key) { return strpos((string)$key, 'PPI') !== false; });
            $rkkPpi = $isPpi ? $this->getPpiItems() : [];
        }

        $kfkArray = $pengajuan->kfk ?? [];
        if(is_string($kfkArray)) {
            $decoded = json_decode($kfkArray, true);
            $kfkArray = is_array($decoded) ? $decoded : [$kfkArray];
        }
        
        $namaJenjang = '-'; $namaKfk = '-';
        foreach ($kfkArray as $item) {
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

        $rkkItems = \App\Models\BankItemRkk::where('kfk', 'like', "%{$namaKfkBersih}%")
            ->whereIn('jenjang_klinis', array_unique([$namaJenjang, $jenjangQuery]))
            ->orderBy('kategori_tugas', 'asc')
            ->orderBy('no_item', 'asc')
            ->get();
            
        $rkkGrouped = $rkkItems->groupBy('kategori_tugas');
        $bidang = str_contains($namaJenjang, 'PK') ? 'KEPERAWATAN' : (str_contains($namaJenjang, 'BK') ? 'KEBIDANAN' : 'UMUM');

        $verifyUrlRkk = route('sertifikat.verify', ['id' => $lisensi->id]) . '?doc=rkk';
        $qrBambang = Penandatangan::qr('ketua_komite', $verifyUrlRkk, ($lisensi->tgl_terbit ?? null));
        $qrHaris = Penandatangan::qr('ketua_subkomite', $verifyUrlRkk, ($lisensi->tgl_terbit ?? null));
        $qrDirekturRkk = Penandatangan::qr('direktur', $verifyUrlRkk, ($lisensi->tgl_terbit ?? null));
        
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
            'isPpi'    => $isPpi,
            'rkkPpi'   => $rkkPpi,
            'rkkMpp'   => $rkkMpp,
            'qrBambang' => $qrBambang,
            'qrHaris' => $qrHaris,
            'qrDirektur' => $qrDirekturRkk,
            'ttdKomite' => Penandatangan::untuk('ketua_komite', ($lisensi->tgl_terbit ?? null)),
            'ttdSubkomite' => Penandatangan::untuk('ketua_subkomite', ($lisensi->tgl_terbit ?? null)),
            'ttdDirektur' => Penandatangan::untuk('direktur', ($lisensi->tgl_terbit ?? null)),
        ];

        // Karena di PerawatLisensiController memanggil facade sebagai Pdf::
        $pdf = Pdf::setOptions(['isRemoteEnabled' => true])->loadView('perawat.pengajuan.kredensial_pdf', $dataPDF)->setPaper('a4', 'portrait');
        $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $namaLengkap);
        
        return $pdf->stream('RKK_' . $cleanName . '.pdf');
    }
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
     
        private function getBankSoalLisan()
    {
        return [
            'tema_1' => [
                [
                    'tema' => 'ISBAR-T Ba K, Pendokumentasian dan Komunikasi Terapeutik',
                    'indikator' => 'Menerapkan komunikasi ISBAR – T Ba K',
                    'pertanyaan' => '<strong>Kasus 1: Pasien Syok Sepsis di Ruang Rawat Inap.</strong><br><br>Anda bertugas sebagai perawat shift sore di ruang penyakit dalam.[cite: 2] Seorang pasien laki-laki bernama Bapak Ahmad, usia 67 tahun, nomor rekam medis 23017854, dirawat sejak 3 hari yang lalu dengan diagnosis Sepsis ec Pneumonia Community Acquired.[cite: 2]<br><br>Saat melakukan observasi pukul 14.30 WIB Anda mendapatkan kondisi pasien mengalami perubahan mendadak.[cite: 2]<br><br>Hasil pengkajian menunjukkan:<br><ul><li>Kesadaran mulai menurun (GCS E3V4M5)[cite: 2]</li><li>TD 82/48 mmHg[cite: 2]</li><li>Nadi 128 x/menit[cite: 2]</li><li>RR 34 x/menit[cite: 2]</li><li>Suhu 39,5°C[cite: 2]</li><li>SpO₂ 88% tanpa oksigen[cite: 2]</li></ul>Pasien tampak sesak berat, kulit dingin, CRT >3 detik.[cite: 2]<br><br>Terapi yang sedang diberikan:<br><ul><li>Infus RL 20 tpm[cite: 2]</li><li>Ceftriaxone 2 gram IV[cite: 2]</li><li>Paracetamol IV[cite: 2]</li><li>Oksigen nasal kanul 3 L/menit[cite: 2]</li></ul>Riwayat alergi : Tidak ada.[cite: 2]<br><br>Hasil laboratorium terbaru:<br><ul><li>Leukosit 24.000/mm³[cite: 2]</li><li>Laktat 5,8 mmol/L[cite: 2]</li><li>Prokalsitonin meningkat[cite: 2]</li><li>Kreatinin 2,3 mg/dL[cite: 2]</li></ul>Dokter DPJP sedang berada di poli rawat jalan.[cite: 2]<br><br><strong>Pertanyaan:</strong><br>Sebagai perawat yang menemukan kondisi tersebut, bagaimana Anda menyampaikan laporan kepada dokter menggunakan komunikasi efektif sesuai standar keselamatan pasien?[cite: 2]',
                    'jawaban' => '<u>Jawaban :</u><br><strong>I (Identification)</strong><ul><li>Memperkenalkan diri.[cite: 2]</li><li>Menyebutkan unit.[cite: 2]</li><li>Menyebutkan nama pasien.[cite: 2]</li><li>Nomor RM.[cite: 2]</li><li>Umur pasien.[cite: 2]</li></ul><strong>S (Situation)</strong><ul><li>Menjelaskan kondisi terkini.[cite: 2]</li><li>Menyampaikan perubahan kondisi.[cite: 2]</li><li>Menjelaskan alasan menghubungi dokter.[cite: 2]</li></ul><strong>B (Background)</strong><ul><li>Diagnosis medis.[cite: 2]</li><li>Hari perawatan.[cite: 2]</li><li>Riwayat terapi.[cite: 2]</li><li>Riwayat alergi.[cite: 2]</li><li>Tindakan yang telah dilakukan.[cite: 2]</li></ul><strong>A (Assessment)</strong><ul><li>Menjelaskan hasil pemeriksaan fisik.[cite: 2]</li><li>Menjelaskan tanda vital.[cite: 2]</li><li>Menjelaskan hasil laboratorium.[cite: 2]</li><li>Menjelaskan interpretasi kondisi (mengarah syok sepsis).[cite: 2]</li></ul><strong>R (Recommendation)</strong><ul><li>Meminta dokter segera datang.[cite: 2]</li><li>Mengusulkan evaluasi segera.[cite: 2]</li><li>Menanyakan instruksi lanjutan.[cite: 2]</li><li>Read back instruksi dokter.[cite: 2]</li></ul><strong>Kemudian lakukan :</strong><br><strong>T (Tulis):</strong> Menuliskan seluruh instruksi dokter di rekam medis.[cite: 2]<br><strong>Ba (Baca kembali):</strong> Membaca kembali seluruh instruksi dokter.[cite: 2]<br><strong>K (Konfirmasi):</strong> Mengonfirmasi bahwa dokter menyetujui hasil read back.[cite: 2]'
                ],
                [
                    'tema' => 'ISBAR-T Ba K, Pendokumentasian dan Komunikasi Terapeutik',
                    'indikator' => 'Menerapkan komunikasi ISBAR – T Ba K',
                    'pertanyaan' => '<strong>Kasus 2 : Reaksi Transfusi Darah</strong><br><br>Pasien perempuan usia 42 tahun dirawat karena Anemia Berat akibat perdarahan uterus abnormal.[cite: 2] Hari ini pasien menjalani transfusi PRC kantong pertama.[cite: 2]<br><br>Lima belas menit setelah transfusi dimulai pasien mengeluh:<br><ul><li>Menggigil hebat[cite: 2]</li><li>Demam 39°C[cite: 2]</li><li>Nyeri pinggang[cite: 2]</li><li>Sesak napas[cite: 2]</li><li>Urtikaria[cite: 2]</li></ul>TD turun menjadi 88/56 mmHg.[cite: 2] Nadi 124 x/menit.[cite: 2] RR 32 x/menit.[cite: 2] SpO₂ 90%.[cite: 2]<br><br>Anda sudah menghentikan transfusi sesuai SOP dan mempertahankan akses IV menggunakan NaCl 0,9%.[cite: 2] Dokter belum berada di ruangan.[cite: 2]<br><br><strong>Pertanyaan</strong><br>Bagaimana Anda melaporkan kondisi pasien kepada dokter menggunakan komunikasi efektif ?[cite: 2]',
                    'jawaban' => '<u>Jawaban :</u><br><strong>I (Identification)</strong><br>Identifikasi petugas, unit, nama pasien, umur, nomor RM.[cite: 2]<br><br><strong>S (Situation)</strong><br>Pasien mengalami reaksi transfusi akut.[cite: 2]<br><br><strong>B (Background)</strong><ul><li>Diagnosis awal.[cite: 2]</li><li>Jenis darah yang ditransfusikan.[cite: 2]</li><li>Waktu mulai transfusi.[cite: 2]</li><li>Terapi sebelumnya.[cite: 2]</li><li>Riwayat alergi.[cite: 2]</li></ul><strong>A (Assessment)</strong><br>Sampaikan:<ul><li>TTV[cite: 2]</li><li>Keluhan pasien[cite: 2]</li><li>Hasil observasi[cite: 2]</li><li>Dugaan reaksi transfusi[cite: 2]</li><li>Tindakan yang telah dilakukan[cite: 2]</li></ul><strong>R (Recommendation)</strong><ul><li>Mohon dokter segera datang.[cite: 2]</li><li>Mohon instruksi terapi lanjutan.[cite: 2]</li><li>Konfirmasi apakah transfusi dihentikan permanen.[cite: 2]</li><li>Persiapan pelaporan ke Bank Darah.[cite: 2]</li></ul><strong>Kemudian dilakukan:</strong><br><strong>T (Tulis):</strong> Menuliskan seluruh instruksi dokter di rekam medis.[cite: 2]<br><strong>Ba (Baca kembali):</strong> Membaca kembali seluruh instruksi dokter.[cite: 2]<br><strong>K (Konfirmasi):</strong> Mengonfirmasi bahwa dokter menyetujui hasil read back.[cite: 2]'
                ],
                [
                    'tema' => 'ISBAR-T Ba K, Pendokumentasian dan Komunikasi Terapeutik',
                    'indikator' => 'Menerapkan komunikasi ISBAR – T Ba K',
                    'pertanyaan' => '<strong>Kasus 3 : Pasien STEMI Mengalami Penurunan Kondisi</strong><br><br>Pasien laki-laki usia 58 tahun dirawat dengan diagnosis ST Elevation Myocardial Infarction (STEMI).[cite: 2]<br>Hari kedua perawatan.[cite: 2]<br>Saat observasi pasien mengeluh:<br><ul><li>Nyeri dada semakin berat.[cite: 2]</li><li>Skala nyeri 9/10.[cite: 2]</li><li>Keringat dingin.[cite: 2]</li><li>Mual.[cite: 2]</li><li>Sesak.[cite: 2]</li></ul>Hasil pemeriksaan:<br><ul><li>TD 78/46 mmHg[cite: 2]</li><li>HR 48 x/menit[cite: 2]</li><li>RR 30 x/menit[cite: 2]</li><li>SpO₂ 86%[cite: 2]</li></ul>EKG monitor menunjukkan ventricular tachycardia intermiten.[cite: 2]<br>Troponin meningkat.[cite: 2]<br>Pasien telah mendapat:<br><ul><li>Aspirin[cite: 2]</li><li>Clopidogrel[cite: 2]</li><li>Heparin[cite: 2]</li><li>Nitrogliserin[cite: 2]</li></ul>Dokter sedang melakukan tindakan di ICU.[cite: 2]<br><br><strong>Pertanyaan</strong><br>Bagaimana Anda menyampaikan laporan kepada dokter menggunakan metode komunikasi efektif ?[cite: 2]',
                    'jawaban' => '<u>Jawaban :</u><br><strong>I (Identification)</strong><ul><li>Identifikasi lengkap.[cite: 2]</li></ul><strong>S (Situation)</strong><ul><li>Pasien STEMI mengalami perburukan kondisi.[cite: 2]</li></ul><strong>B (Background)</strong><ul><li>Diagnosis.[cite: 2]</li><li>Hari perawatan.[cite: 2]</li><li>Terapi yang telah diberikan.[cite: 2]</li><li>Riwayat penyakit.[cite: 2]</li><li>Riwayat alergi.[cite: 2]</li></ul><strong>A (Assessment)</strong><ul><li>TTV.[cite: 2]</li><li>Nyeri.[cite: 2]</li><li>Hasil monitor EKG.[cite: 2]</li><li>Hasil laboratorium.[cite: 2]</li><li>Interpretasi kondisi.[cite: 2]</li></ul><strong>R (Recommendation)</strong><ul><li>Mohon dokter segera datang.[cite: 2]</li><li>Mohon instruksi ACLS bila diperlukan.[cite: 2]</li><li>Persiapan transfer ICU bila diinstruksikan.[cite: 2]</li></ul><strong>Kemudian dilakukan:</strong><br><strong>T (Tulis):</strong> Menuliskan seluruh instruksi dokter di rekam medis.[cite: 2]<br><strong>Ba (Baca kembali):</strong> Membaca kembali seluruh instruksi dokter.[cite: 2]<br><strong>K (Konfirmasi):</strong> Mengonfirmasi bahwa dokter menyetujui hasil read back.[cite: 2]'
                ],
                [
                    'tema' => 'ISBAR-T Ba K, Pendokumentasian dan Komunikasi Terapeutik',
                    'indikator' => 'Menerapkan komunikasi ISBAR – T Ba K',
                    'pertanyaan' => '<strong>Kasus 4 : Perdarahan Post Partum</strong><br><br>Seorang ibu usia 28 tahun melahirkan spontan dua jam yang lalu.[cite: 2]<br>Bayi lahir sehat.[cite: 2]<br>Plasenta lahir lengkap.[cite: 2]<br>Saat observasi Anda menemukan:<br><ul><li>Perdarahan semakin banyak.[cite: 2]</li><li>Pembalut penuh dalam waktu kurang dari 15 menit.[cite: 2]</li><li>Uterus lembek.[cite: 2]</li><li>TFU meningkat.[cite: 2]</li><li>TD 82/50 mmHg.[cite: 2]</li><li>Nadi 132 x/menit.[cite: 2]</li><li>RR 28 x/menit.[cite: 2]</li></ul>Hb sebelum persalinan 10 g/dL.[cite: 2]<br>Pasien tampak pucat dan gelisah.[cite: 2]<br>Terapi:<br><ul><li>Infus RL.[cite: 2]</li><li>Oksitosin infus.[cite: 2]</li></ul>Belum ada riwayat alergi obat.[cite: 2]<br>Dokter SpOG sedang melakukan operasi SC.[cite: 2]<br><br><strong>Pertanyaan</strong><br>Bagaimana Anda melaporkan kondisi pasien kepada dokter menggunakan metode komunikasi efektif ?[cite: 2]',
                    'jawaban' => '<u>Jawaban :</u><br><strong>I (Identification)</strong><ul><li>Identifikasi lengkap.[cite: 2]</li></ul><strong>S (Situation)</strong><ul><li>Pasien mengalami perdarahan postpartum.[cite: 2]</li></ul><strong>B (Background)</strong><ul><li>Riwayat persalinan.[cite: 2]</li><li>Diagnosis.[cite: 2]</li><li>Jam persalinan.[cite: 2]</li><li>Terapi.[cite: 2]</li><li>Alergi.[cite: 2]</li></ul><strong>A (Assessment)</strong><ul><li>Perdarahan.[cite: 2]</li><li>TTV.[cite: 2]</li><li>Kontraksi uterus.[cite: 2]</li><li>Estimasi kehilangan darah.[cite: 2]</li><li>Kondisi umum.[cite: 2]</li></ul><strong>R (Recommendation)</strong><ul><li>Mohon dokter segera datang.[cite: 2]</li><li>Mohon instruksi uterotonika tambahan.[cite: 2]</li><li>Persiapan transfusi bila diperlukan.[cite: 2]</li></ul><strong>Kemudian dilakukan:</strong><br><strong>T (Tulis):</strong> Menuliskan seluruh instruksi dokter di rekam medis.[cite: 2]<br><strong>Ba (Baca kembali):</strong> Membaca kembali seluruh instruksi dokter.[cite: 2]<br><strong>K (Konfirmasi):</strong> Mengonfirmasi bahwa dokter menyetujui hasil read back.[cite: 2]'
                ],
                [
                    'tema' => 'ISBAR-T Ba K, Pendokumentasian dan Komunikasi Terapeutik',
                    'indikator' => 'Menerapkan komunikasi ISBAR – T Ba K',
                    'pertanyaan' => '<strong>Kasus 5 : Preeklamsia Berat Menuju Eklampsia</strong><br><br>Seorang ibu hamil G2P1A0 usia kehamilan 36 minggu dirawat karena Preeklamsia Berat.[cite: 2]<br>Saat dilakukan observasi pukul 21.00 WIB diperoleh:<br><ul><li>TD 182/118 mmHg[cite: 2]</li><li>Protein urin +++[cite: 2]</li><li>Nyeri kepala hebat[cite: 2]</li><li>Pandangan kabur[cite: 2]</li><li>Nyeri epigastrium[cite: 2]</li><li>Refleks patella meningkat[cite: 2]</li></ul>Pasien mulai tampak gelisah dan sulit diajak komunikasi.[cite: 2]<br>Terapi:<br><ul><li>MgSO₄ loading dose sudah diberikan.[cite: 2]</li><li>Nifedipin oral.[cite: 2]</li><li>Infus RL.[cite: 2]</li></ul>Riwayat alergi tidak ada.[cite: 2]<br>Dokter SpOG sedang menangani pasien lain di kamar operasi.[cite: 2]<br><br><strong>Pertanyaan</strong><br>Bagaimana Anda menyampaikan kondisi pasien kepada dokter menggunakan metode komunikasi efektif ?[cite: 2]',
                    'jawaban' => '<u>Jawaban :</u><br><strong>I (Identification)</strong><ul><li>Memperkenalkan diri.[cite: 2]</li><li>Menyebutkan unit kerja.[cite: 2]</li><li>Menyebutkan identitas pasien (nama, umur, nomor rekam medis).[cite: 2]</li></ul><strong>S (Situation)</strong><ul><li>Menjelaskan bahwa pasien mengalami perburukan kondisi preeklamsia berat dengan tanda-tanda mengarah ke eklampsia.[cite: 2]</li><li>Menyampaikan alasan utama menghubungi dokter.[cite: 2]</li></ul><strong>B (Background)</strong><ul><li>Diagnosis medis.[cite: 2]</li><li>Usia kehamilan.[cite: 2]</li><li>Hari perawatan.[cite: 2]</li><li>Terapi yang sudah diberikan.[cite: 2]</li><li>Riwayat alergi.[cite: 2]</li><li>Riwayat obstetri yang relevan.[cite: 2]</li></ul><strong>A (Assessment)</strong><ul><li>Menyampaikan hasil pemeriksaan fisik dan tanda vital.[cite: 2]</li><li>Menjelaskan hasil pemeriksaan penunjang (protein urin).[cite: 2]</li><li>Menjelaskan tanda neurologis dan kondisi klinis yang mengarah ke eklampsia.[cite: 2]</li></ul><strong>R (Recommendation)</strong><ul><li>Meminta dokter segera datang untuk evaluasi.[cite: 2]</li><li>Memohon instruksi lanjutan terkait penatalaksanaan, persiapan terminasi kehamilan bila diperlukan, dan tata laksana kegawatdaruratan.[cite: 2]</li></ul><strong>Selanjutnya dilakukan:</strong><br><strong>T (Tulis):</strong> Mendokumentasikan seluruh instruksi dokter pada rekam medis sesuai ketentuan.[cite: 2]<br><strong>Ba (Baca kembali):</strong> Melakukan read back seluruh instruksi dokter untuk memastikan tidak ada kesalahan komunikasi.[cite: 2]<br><strong>K (Konfirmasi):</strong> Memastikan dokter menyatakan bahwa hasil read back sudah benar dan instruksi dipahami dengan tepat.[cite: 2]'
                ],
            ],
            'tema_2' => [
                [
                    'tema' => 'Penerapan Hand Hygiene, Enam Langkah Cuci Tangan WHO, dan Five Moments',
                    'indikator' => 'Menjelaskan teknik cuci tangan Berbasis Alkohol (Handrub)',
                    'pertanyaan' => '<strong>KASUS 1 (KEPERAWATAN): Handrub Sebelum Melakukan Pemasangan Infus</strong><br><br>Anda bertugas sebagai perawat pagi di Ruang Penyakit Dalam.[cite: 1] Pada pukul 08.00 WIB Anda menerima pasien baru bernama Bapak Andi Prasetyo, usia 58 tahun, dengan diagnosis Pneumonia Komunitas.[cite: 1] Dokter telah menginstruksikan pemasangan infus perifer untuk pemberian antibiotik intravena.[cite: 1]<br><br>Sebelum tindakan dilakukan, Anda telah melakukan identifikasi pasien, menjelaskan prosedur, memperoleh persetujuan pasien, serta menyiapkan seluruh peralatan steril di atas troli tindakan.[cite: 1] Saat akan mulai menyentuh pasien, kepala ruangan meminta Anda memperagakan teknik hand hygiene berbasis alkohol sesuai standar WHO dan Standar Pencegahan dan Pengendalian Infeksi (PPI).[cite: 1]<br><br>Sebagai seorang perawat profesional, Anda diminta menjelaskan secara lisan sekaligus memperagakan setiap langkah handrub secara berurutan, termasuk alasan mengapa setiap langkah harus dilakukan untuk mengurangi transmisi mikroorganisme selama pemberian asuhan keperawatan.[cite: 1]<br><br><strong>Pertanyaan</strong><br>Jelaskan dan demonstrasikan secara lengkap prosedur cuci tangan berbasis alkohol (Handrub) yang benar sebelum melakukan pemasangan infus pada pasien.[cite: 1]',
                    'jawaban' => '<u>Menyebutkan seluruh langkah:</u><br><ol><li><strong>Melepaskan seluruh perhiasan</strong><br><ul><li>Melepaskan cincin.[cite: 1]</li><li>Melepaskan gelang.[cite: 1]</li><li>Melepaskan jam tangan.[cite: 1]</li><li>Memastikan tangan bebas dari aksesoris.[cite: 1]</li></ul></li><li><strong>Menuangkan handrub</strong><br><ul><li>Menuangkan cairan handrub sebanyak ±3–5 mL atau sesuai petunjuk produk.[cite: 1]</li><li>Memastikan seluruh permukaan tangan akan dapat terlapisi.[cite: 1]</li></ul></li><li><strong>Langkah 1</strong><br>Menggosok kedua telapak tangan secara memutar hingga cairan merata.[cite: 1]</li><li><strong>Langkah 2</strong><br>Menggosok punggung tangan kanan menggunakan telapak tangan kiri beserta sela-sela jari, kemudian bergantian.[cite: 1]</li><li><strong>Langkah 3</strong><br>Menggosok kedua telapak tangan dengan posisi saling berhadapan disertai sela-sela jari.[cite: 1]</li><li><strong>Langkah 4</strong><br>Menggosok sela-sela jari bagian dalam dengan posisi kedua tangan saling mengunci.[cite: 1]</li><li><strong>Langkah 5</strong><br>Menggosok ibu jari kiri secara memutar menggunakan genggaman tangan kanan, kemudian bergantian.[cite: 1]</li><li><strong>Langkah 6</strong><br>Menggosok ujung-ujung jari tangan kanan secara memutar pada telapak tangan kiri, kemudian bergantian.[cite: 1]</li></ol>'
                ],
                [
                    'tema' => 'Penerapan Hand Hygiene, Enam Langkah Cuci Tangan WHO, dan Five Moments',
                    'indikator' => 'Menjelaskan Five Moments',
                    'pertanyaan' => '<strong>KASUS 2 (KEPERAWATAN): Pasien Pneumonia dengan Pemasangan Infus dan Pemberian Antibiotik Intravena</strong><br><br>Anda bertugas sebagai perawat shift pagi di Ruang Penyakit Dalam.[cite: 1] Seorang pasien laki-laki bernama Bapak Hendra Wijaya, usia 62 tahun, dirawat dengan diagnosis Pneumonia Community Acquired disertai Diabetes Melitus Tipe II.[cite: 1] Pasien mengeluh sesak napas, batuk berdahak, dan demam tinggi sejak tiga hari terakhir.[cite: 1]<br><br>Pada awal dinas, Anda melakukan serah terima pasien dan memperoleh instruksi untuk:<br><ul><li>Melakukan pengkajian awal.[cite: 1]</li><li>Mengukur tanda-tanda vital.[cite: 1]</li><li>Memasang infus perifer baru.[cite: 1]</li><li>Memberikan antibiotik Ceftriaxone intravena.[cite: 1]</li><li>Mengganti balutan infus lama.[cite: 1]</li><li>Membantu pasien mengubah posisi tidur.[cite: 1]</li><li>Membersihkan meja pasien dan pagar tempat tidur setelah tindakan selesai.[cite: 1]</li></ul>Selama proses tersebut Anda akan melakukan beberapa kali kontak dengan pasien, tindakan aseptik, kemungkinan kontak dengan cairan tubuh saat mengganti balutan, kontak langsung dengan pasien, serta kontak dengan lingkungan sekitar pasien.[cite: 1]<br><br><strong>Pertanyaan Interview</strong><br>Berdasarkan kasus tersebut, jelaskan kapan saja Anda wajib melakukan hand hygiene sesuai konsep 5 Moments WHO, serta jelaskan alasan (rasional) setiap momen tersebut.[cite: 1]',
                    'jawaban' => '<u>Menyebutkan 5 momen dengan contoh:</u><br><br><strong>Momen 1: Sebelum Kontak dengan Pasien</strong><br>Dilakukan sebelum menyentuh pasien.[cite: 1]<br>Rasional: Melindungi pasien dari mikroorganisme yang terbawa pada tangan petugas.[cite: 1]<br><br><strong>Momen 2: Sebelum Tindakan Aseptik/Bersih</strong><br>Dilakukan sebelum:<br><ul><li>pemasangan infus[cite: 1]</li><li>pemberian obat IV[cite: 1]</li><li>mengganti balutan[cite: 1]</li><li>tindakan steril lainnya[cite: 1]</li></ul>Rasional: Melindungi pasien dari masuknya mikroorganisme ke area steril atau jaringan tubuh.[cite: 1]<br><br><strong>Momen 3: Setelah Terpapar Cairan Tubuh</strong><br>Dilakukan setelah kemungkinan kontak dengan:<br><ul><li>darah[cite: 1]</li><li>sekret[cite: 1]</li><li>sputum[cite: 1]</li><li>cairan infus yang terkontaminasi[cite: 1]</li><li>balutan bekas[cite: 1]</li></ul>Rasional: Melindungi petugas dan lingkungan rumah sakit dari kontaminasi mikroorganisme pasien.[cite: 1]<br><br><strong>Momen 4: Setelah Kontak dengan Pasien</strong><br>Dilakukan setelah selesai melakukan pemeriksaan atau membantu pasien.[cite: 1]<br>Rasional: Melindungi diri petugas dan lingkungan dari kuman pasien.[cite: 1]<br><br><strong>Momen 5: Setelah Kontak dengan Lingkungan Sekitar Pasien</strong><br>Dilakukan setelah menyentuh:<br><ul><li>pagar tempat tidur[cite: 1]</li><li>meja pasien[cite: 1]</li><li>monitor[cite: 1]</li><li>kursi[cite: 1]</li><li>alat sekitar pasien[cite: 1]</li></ul>Rasional: Melindungi petugas dari mikroorganisme yang berada pada benda di sekitar pasien.[cite: 1]'
                ],
                [
                    'tema' => 'Penerapan Hand Hygiene, Enam Langkah Cuci Tangan WHO, dan Five Moments',
                    'indikator' => 'Menjelaskan teknik cuci tangan Berbasis Alkohol (Handrub)',
                    'pertanyaan' => '<strong>KASUS 3 (KEPERAWATAN): Handrub Sebelum Pemasangan Kateter Urin</strong><br><br>Seorang pasien laki-laki usia 70 tahun dengan diagnosis Retensi Urin akibat Benign Prostatic Hyperplasia (BPH) mengalami kesulitan berkemih.[cite: 1] Dokter menginstruksikan pemasangan kateter urin steril.[cite: 1]<br><br>Anda telah menyiapkan seluruh alat, melakukan identifikasi pasien menggunakan dua identitas, menjelaskan prosedur kepada pasien dan keluarga, serta memasang tirai privasi.[cite: 1]<br><br>Sebelum mengenakan sarung tangan steril dan memulai prosedur pemasangan kateter, asesor meminta Anda memperagakan handrub sesuai prosedur standar WHO.[cite: 1]<br><br><strong>Pertanyaan</strong><br>Jelaskan secara lengkap langkah-langkah handrub berbasis alkohol yang benar sebelum pemasangan kateter urin.[cite: 1]',
                    'jawaban' => '<u>Menyebutkan seluruh langkah:</u><br><ol><li><strong>Melepaskan seluruh perhiasan</strong><br><ul><li>Melepaskan cincin.[cite: 1]</li><li>Melepaskan gelang.[cite: 1]</li><li>Melepaskan jam tangan.[cite: 1]</li><li>Memastikan tangan bebas dari aksesoris.[cite: 1]</li></ul></li><li><strong>Menuangkan handrub</strong><br><ul><li>Menuangkan cairan handrub sebanyak ±3–5 mL atau sesuai petunjuk produk.[cite: 1]</li><li>Memastikan seluruh permukaan tangan akan dapat terlapisi.[cite: 1]</li></ul></li><li><strong>Langkah 1</strong><br>Menggosok kedua telapak tangan secara memutar hingga cairan merata.[cite: 1]</li><li><strong>Langkah 2</strong><br>Menggosok punggung tangan kanan menggunakan telapak tangan kiri beserta sela-sela jari, kemudian bergantian.[cite: 1]</li><li><strong>Langkah 3</strong><br>Menggosok kedua telapak tangan dengan posisi saling berhadapan disertai sela-sela jari.[cite: 1]</li><li><strong>Langkah 4</strong><br>Menggosok sela-sela jari bagian dalam dengan posisi kedua tangan saling mengunci.[cite: 1]</li><li><strong>Langkah 5</strong><br>Menggosok ibu jari kiri secara memutar menggunakan genggaman tangan kanan, kemudian bergantian.[cite: 1]</li><li><strong>Langkah 6</strong><br>Menggosok ujung-ujung jari tangan kanan secara memutar pada telapak tangan kiri, kemudian bergantian.[cite: 1]</li></ol>'
                ],
                [
                    'tema' => 'Penerapan Hand Hygiene, Enam Langkah Cuci Tangan WHO, dan Five Moments',
                    'indikator' => 'Menjelaskan teknik cuci tangan Berbasis Alkohol (Handrub)',
                    'pertanyaan' => '<strong>KASUS 4 (KEBIDANAN): Handrub Sebelum Pemeriksaan Dalam Persalinan</strong><br><br>Seorang ibu hamil G1P0A0 usia kehamilan 39 minggu datang ke ruang bersalin dengan keluhan mulas teratur sejak 6 jam yang lalu.[cite: 1] Hasil pemeriksaan menunjukkan kontraksi adekuat, denyut jantung janin normal, dan ibu tampak siap menjalani pemeriksaan dalam untuk menentukan kemajuan persalinan.[cite: 1]<br><br>Seluruh alat pelindung diri telah tersedia.[cite: 1] Ruang bersalin menerapkan program PPI yang mewajibkan seluruh tenaga kesehatan melakukan hand hygiene sesuai standar sebelum setiap tindakan aseptik.[cite: 1]<br><br>Sebagai bidan yang bertugas, Anda diminta menjelaskan dan memperagakan teknik handrub yang benar sebelum melakukan pemeriksaan dalam.[cite: 1]<br><br><strong>Pertanyaan</strong><br>Jelaskan secara lengkap langkah-langkah handrub berbasis alkohol sesuai standar WHO sebelum melakukan pemeriksaan dalam pada ibu bersalin.[cite: 1]',
                    'jawaban' => '<u>Menyebutkan seluruh langkah:</u><br><ol><li><strong>Melepaskan seluruh perhiasan</strong><br><ul><li>Melepaskan cincin.[cite: 1]</li><li>Melepaskan gelang.[cite: 1]</li><li>Melepaskan jam tangan.[cite: 1]</li><li>Memastikan tangan bebas dari aksesoris.[cite: 1]</li></ul></li><li><strong>Menuangkan handrub</strong><br><ul><li>Menuangkan cairan handrub sebanyak ±3–5 mL atau sesuai petunjuk produk.[cite: 1]</li><li>Memastikan seluruh permukaan tangan akan dapat terlapisi.[cite: 1]</li></ul></li><li><strong>Langkah 1</strong><br>Menggosok kedua telapak tangan secara memutar hingga cairan merata.[cite: 1]</li><li><strong>Langkah 2</strong><br>Menggosok punggung tangan kanan menggunakan telapak tangan kiri beserta sela-sela jari, kemudian bergantian.[cite: 1]</li><li><strong>Langkah 3</strong><br>Menggosok kedua telapak tangan dengan posisi saling berhadapan disertai sela-sela jari.[cite: 1]</li><li><strong>Langkah 4</strong><br>Menggosok sela-sela jari bagian dalam dengan posisi kedua tangan saling mengunci.[cite: 1]</li><li><strong>Langkah 5</strong><br>Menggosok ibu jari kiri secara memutar menggunakan genggaman tangan kanan, kemudian bergantian.[cite: 1]</li><li><strong>Langkah 6</strong><br>Menggosok ujung-ujung jari tangan kanan secara memutar pada telapak tangan kiri, kemudian bergantian.[cite: 1]</li></ol>'
                ],
                [
                    'tema' => 'Penerapan Hand Hygiene, Enam Langkah Cuci Tangan WHO, dan Five Moments',
                    'indikator' => 'Menjelaskan Five Moments',
                    'pertanyaan' => '<strong>KASUS 5 (KEBIDANAN): Penjahitan Laserasi Perineum Pasca Persalinan</strong><br><br>Seorang ibu usia 29 tahun baru saja melahirkan spontan bayi laki-laki dengan berat lahir 3.500 gram.[cite: 1] Setelah plasenta lahir lengkap ditemukan laserasi perineum derajat II sehingga memerlukan penjahitan.[cite: 1]<br><br>Selama tindakan bidan melakukan:<br><ul><li>pemeriksaan kondisi ibu[cite: 1]</li><li>pemasangan duk steril[cite: 1]</li><li>infiltrasi anestesi lokal[cite: 1]</li><li>penjahitan laserasi[cite: 1]</li><li>pembersihan darah[cite: 1]</li><li>membantu ibu berpindah posisi[cite: 1]</li><li>merapikan alat[cite: 1]</li><li>membuang limbah medis[cite: 1]</li></ul><strong>Pertanyaan Interview</strong><br>Jelaskan kapan hand hygiene harus dilakukan sesuai 5 Moments WHO selama seluruh prosedur tersebut, serta jelaskan alasan setiap momennya.[cite: 1]',
                    'jawaban' => '<u>Mampu menentukan momen secara benar:</u><br><br><strong>Momen 1: Sebelum Kontak dengan Pasien</strong><br>Dilakukan sebelum menyentuh ibu.[cite: 1]<br>Rasional: Melindungi ibu dari mikroorganisme yang terbawa pada tangan petugas.[cite: 1]<br><br><strong>Momen 2: Sebelum Tindakan Aseptik/Bersih</strong><br>Dilakukan sebelum:<br><ul><li>infiltrasi anestesi[cite: 1]</li><li>penjahitan laserasi[cite: 1]</li><li>tindakan steril lainnya[cite: 1]</li></ul>Rasional: Melindungi pasien dari masuknya mikroorganisme ke jaringan tubuh.[cite: 1]<br><br><strong>Momen 3: Setelah Terpapar Cairan Tubuh</strong><br>Dilakukan setelah kontak dengan:<br><ul><li>darah[cite: 1]</li><li>cairan nifas[cite: 1]</li><li>cairan ketuban[cite: 1]</li><li>kasa bekas[cite: 1]</li></ul>Rasional: Melindungi petugas dan lingkungan dari kontaminasi mikroorganisme pasien.[cite: 1]<br><br><strong>Momen 4: Setelah Kontak dengan Pasien</strong><br>Dilakukan setelah seluruh tindakan selesai dan sebelum meninggalkan pasien.[cite: 1]<br>Rasional: Melindungi petugas dan lingkungan dari penyebaran kuman pasien.[cite: 1]<br><br><strong>Momen 5: Setelah Kontak dengan Lingkungan Sekitar Pasien</strong><br>Dilakukan setelah menyentuh:<br><ul><li>tempat tidur[cite: 1]</li><li>meja instrumen[cite: 1]</li><li>lampu tindakan[cite: 1]</li><li>troli[cite: 1]</li><li>kursi[cite: 1]</li><li>benda lain di sekitar pasien[cite: 1]</li></ul>Rasional: Melindungi petugas dari mikroorganisme yang menempel pada lingkungan sekitar pasien.[cite: 1]'
                ],
            ],
            'tema_3' => [
                [
                    'tema' => 'Penerapan Sasaran Keselamatan Pasien (6 SKP)',
                    'indikator' => 'Menjelaskan konsep 6 SKP',
                    'pertanyaan' => '<strong>KASUS 1 (KEPERAWATAN): Pasien Stroke Hemoragik dengan Risiko Tinggi Kesalahan Identifikasi, Obat High Alert, Infeksi, dan Jatuh</strong><br><br>Anda bertugas sebagai perawat shift pagi di Ruang High Care Unit (HCU).[cite: 3] Pada pukul 08.00 WIB Anda menerima operan seorang pasien laki-laki bernama Bapak Ahmad Setiawan, usia 68 tahun, nomor rekam medis RM-22034567, dengan diagnosis medis Stroke Hemoragik disertai Hipertensi Emergensi dan Diabetes Mellitus Tipe II.[cite: 3]<br><br>Pasien mengalami penurunan kesadaran (GCS E3V4M5), kelemahan ekstremitas kanan, dan menggunakan selang infus, kateter urin, serta oksigen nasal kanul 3 L/menit.[cite: 3] Dokter memberikan terapi antara lain:<br><ul><li>Nicardipine infus menggunakan syringe pump.[cite: 3]</li><li>Insulin regular sliding scale.[cite: 3]</li><li>Ceftriaxone intravena.[cite: 3]</li><li>Omeprazole intravena.[cite: 3]</li><li>Diet enteral melalui NGT.[cite: 3]</li></ul>Pasien memiliki gelang identitas, gelang risiko jatuh warna kuning, dan gelang alergi warna merah karena riwayat alergi Cefadroxil.[cite: 3]<br><br>Selama dinas Anda akan melakukan beberapa tindakan, yaitu:<br><ul><li>Melakukan identifikasi pasien.[cite: 3]</li><li>Memberikan obat intravena.[cite: 3]</li><li>Berkomunikasi dengan dokter melalui telepon mengenai perubahan tekanan darah pasien.[cite: 3]</li><li>Melakukan pemasangan infus baru.[cite: 3]</li><li>Membantu pasien berpindah dari tempat tidur ke kursi roda untuk pemeriksaan CT Scan.[cite: 3]</li><li>Memberikan edukasi kepada keluarga mengenai keselamatan pasien.[cite: 3]</li></ul><strong>Pertanyaan Interview:</strong><br>Berdasarkan kasus tersebut, jelaskan bagaimana Anda menerapkan 6 Sasaran Keselamatan Pasien (SKP) pada setiap pelayanan yang diberikan kepada pasien.[cite: 3]',
                    'jawaban' => '<u>Menjelaskan seluruh SKP:</u><br><br><strong>SKP 1. Mengidentifikasi Pasien dengan Benar</strong><br>Dilakukan dengan menggunakan minimal dua identitas pasien, misalnya:<br><ul><li>Nama lengkap.[cite: 3]</li><li>Tanggal lahir atau nomor rekam medis.[cite: 3]</li><li>Memastikan kecocokan gelang identitas dengan rekam medis dan instruksi tindakan.[cite: 3]</li></ul>Tujuan: Mencegah kesalahan identifikasi pasien.[cite: 3]<br><br><strong>SKP 2. Meningkatkan Komunikasi yang Efektif</strong><br>Menerapkan komunikasi yang terstruktur, misalnya:<br><ul><li>ISBAR saat melapor kepada dokter.[cite: 3]</li><li>Read back (Tulis-Baca kembali-Konfirmasi) untuk instruksi verbal atau melalui telepon.[cite: 3]</li><li>Dokumentasi komunikasi secara lengkap.[cite: 3]</li></ul>Tujuan: Mencegah kesalahan komunikasi dalam pelayanan pasien.[cite: 3]<br><br><strong>SKP 3. Meningkatkan Keamanan Obat yang Perlu Diwaspadai (High-Alert Medications)</strong><br>Pada kasus ini termasuk:<br><ul><li>Insulin.[cite: 3]</li><li>Nicardipine infus.[cite: 3]</li></ul>Penerapan:<br><ul><li>Verifikasi resep.[cite: 3]</li><li>Double check bila diperlukan.[cite: 3]</li><li>Benar pasien.[cite: 3]</li><li>Benar obat.[cite: 3]</li><li>Benar dosis.[cite: 3]</li><li>Benar waktu.[cite: 3]</li><li>Benar cara pemberian.[cite: 3]</li><li>Label obat jelas.[cite: 3]</li></ul>Tujuan: Mengurangi kejadian medication error.[cite: 3]<br><br><strong>SKP 4. Memastikan Pasien, Lokasi, dan Prosedur yang Benar</strong><br>Sebelum tindakan:<br><ul><li>Verifikasi identitas pasien.[cite: 3]</li><li>Verifikasi tindakan.[cite: 3]</li><li>Verifikasi lokasi tindakan bila diperlukan.[cite: 3]</li><li>Konfirmasi dengan pasien/keluarga dan dokumen medis.[cite: 3]</li></ul>Tujuan: Mencegah salah pasien atau salah prosedur.[cite: 3]<br><br><strong>SKP 5. Mengurangi Risiko Infeksi Terkait Pelayanan Kesehatan (HAIs)</strong><br>Penerapan:<br><ul><li>Five Moments Hand Hygiene.[cite: 3]</li><li>Teknik aseptik.[cite: 3]</li><li>Penggunaan APD sesuai indikasi.[cite: 3]</li><li>Sterilisasi alat.[cite: 3]</li><li>Pencegahan infeksi pada pemasangan infus, NGT, dan kateter.[cite: 3]</li></ul>Tujuan: Menurunkan risiko Healthcare-Associated Infections (HAIs).[cite: 3]<br><br><strong>SKP 6. Mengurangi Risiko Cedera Pasien Akibat Terjatuh</strong><br>Penerapan:<br><ul><li>Melakukan asesmen risiko jatuh.[cite: 3]</li><li>Memasang gelang risiko jatuh.[cite: 3]</li><li>Menaikkan pagar tempat tidur.[cite: 3]</li><li>Mengunci roda tempat tidur dan kursi roda.[cite: 3]</li><li>Edukasi keluarga.[cite: 3]</li><li>Pendampingan saat mobilisasi.[cite: 3]</li></ul>Tujuan: Mencegah pasien mengalami cedera akibat jatuh.[cite: 3]'
                ],
                [
                    'tema' => 'Penerapan Sasaran Keselamatan Pasien (6 SKP)',
                    'indikator' => 'Menjelaskan konsep 6 SKP',
                    'pertanyaan' => '<strong>KASUS 2 (KEPERAWATAN): Pasien Gagal Ginjal Kronik Menjalani Hemodialisis</strong><br><br>Seorang pasien perempuan usia 56 tahun dengan diagnosis Chronic Kidney Disease Stadium V menjalani hemodialisis rutin.[cite: 3] Pasien memiliki akses AV fistula pada lengan kiri dan riwayat Diabetes Mellitus serta Hipertensi.[cite: 3]<br><br>Hari ini pasien dijadwalkan menjalani hemodialisis selama empat jam.[cite: 3] Sebelum tindakan, Anda melakukan identifikasi pasien, pemeriksaan tanda vital, verifikasi jadwal hemodialisis, serta pengecekan obat-obatan yang akan diberikan selama prosedur, termasuk Heparin sebagai obat high-alert.[cite: 3]<br><br>Selama tindakan Anda juga melakukan komunikasi dengan dokter nefrologi mengenai hasil laboratorium terbaru, menerapkan teknik aseptik saat mengakses AV fistula, serta membantu pasien berpindah dari tempat tidur ke kursi hemodialisis karena pasien tampak lemah dan berisiko jatuh.[cite: 3]<br><br><strong>Pertanyaan Interview:</strong><br>Jelaskan bagaimana penerapan 6 Sasaran Keselamatan Pasien (SKP) pada kasus tersebut.[cite: 3]',
                    'jawaban' => '<u>Menjelaskan seluruh SKP:</u><br><br><ul><li><strong>SKP 1:</strong> Melakukan identifikasi pasien menggunakan minimal dua identitas sebelum hemodialisis dan pemberian obat.[cite: 3]</li><li><strong>SKP 2:</strong> Menggunakan komunikasi efektif (misalnya ISBAR dan read back) saat menyampaikan hasil laboratorium dan menerima instruksi dokter.[cite: 3]</li><li><strong>SKP 3:</strong> Menerapkan prinsip keamanan obat pada pemberian Heparin sebagai obat high-alert melalui verifikasi resep, dosis, pasien, waktu, rute, serta double check sesuai kebijakan rumah sakit.[cite: 3]</li><li><strong>SKP 4:</strong> Memastikan pasien yang benar, prosedur hemodialisis yang benar, dan akses AV fistula yang benar sebelum tindakan dimulai.[cite: 3]</li><li><strong>SKP 5:</strong> Melakukan pencegahan infeksi melalui kebersihan tangan, teknik aseptik saat mengakses AV fistula, penggunaan APD, dan pengelolaan alat sesuai prosedur.[cite: 3]</li><li><strong>SKP 6:</strong> Mengurangi risiko jatuh dengan melakukan asesmen risiko, mendampingi pasien saat berpindah, memastikan kursi hemodialisis aman, dan memberikan edukasi kepada pasien serta keluarga.[cite: 3]</li></ul>'
                ],
                [
                    'tema' => 'Penerapan Sasaran Keselamatan Pasien (6 SKP)',
                    'indikator' => 'Menjelaskan konsep 6 SKP',
                    'pertanyaan' => '<strong>KASUS 3 (KEBIDANAN): Ibu Post Sectio Caesarea dengan Terapi Magnesium Sulfat</strong><br><br>Seorang ibu berusia 30 tahun menjalani operasi Sectio Caesarea karena Preeklamsia Berat pada usia kehamilan 37 minggu.[cite: 3] Saat ini ibu dirawat di ruang nifas dengan kondisi sadar, tekanan darah masih tinggi, dan mendapatkan terapi Magnesium Sulfat (MgSO₄), antihipertensi, antibiotik, serta analgetik.[cite: 3]<br><br>Pasien masih menggunakan infus intravena dan kateter urin.[cite: 3] Bayi dirawat bersama ibu (rooming-in).[cite: 3] Pada pergantian shift, Anda akan:<br><ul><li>Melakukan identifikasi ibu sebelum pemeriksaan.[cite: 3]</li><li>Memberikan MgSO₄ sesuai instruksi dokter.[cite: 3]</li><li>Menghubungi dokter karena refleks patella menurun.[cite: 3]</li><li>Mengganti balutan luka operasi menggunakan teknik aseptik.[cite: 3]</li><li>Membantu ibu pertama kali berjalan setelah operasi.[cite: 3]</li><li>Memberikan edukasi kepada keluarga mengenai keselamatan pasien selama perawatan.[cite: 3]</li></ul><strong>Pertanyaan Interview:</strong><br>Berdasarkan kasus tersebut, jelaskan bagaimana Anda menerapkan 6 Sasaran Keselamatan Pasien (SKP) selama memberikan asuhan kebidanan.[cite: 3]',
                    'jawaban' => '<u>Menjelaskan seluruh SKP:</u><br><br><ul><li><strong>SKP 1. Mengidentifikasi Pasien dengan Benar:</strong> Memastikan identitas ibu menggunakan minimal dua identitas (nama lengkap, tanggal lahir atau nomor rekam medis).[cite: 3] Mencocokkan gelang identitas dengan rekam medis dan instruksi tindakan sebelum pemeriksaan, pemberian obat, atau tindakan lainnya.[cite: 3]</li><li><strong>SKP 2. Meningkatkan Komunikasi yang Efektif:</strong> Menggunakan komunikasi terstruktur seperti ISBAR saat melaporkan perubahan kondisi ibu kepada dokter.[cite: 3] Melakukan read back terhadap instruksi verbal atau melalui telepon.[cite: 3] Mendokumentasikan seluruh komunikasi dengan lengkap dan tepat waktu.[cite: 3]</li><li><strong>SKP 3. Meningkatkan Keamanan Obat (High-Alert):</strong> MgSO₄ termasuk obat high-alert.[cite: 3] Verifikasi resep dan dosis.[cite: 3] Memastikan benar pasien, benar obat, benar dosis, benar waktu, benar rute, dan benar dokumentasi.[cite: 3] Melakukan pemantauan efek samping dan tanda toksisitas sesuai prosedur.[cite: 3]</li><li><strong>SKP 4. Memastikan Pasien, Lokasi, dan Prosedur yang Benar:</strong> Sebelum tindakan seperti ganti balutan, memastikan identitas, prosedur, dan lokasi benar.[cite: 3]</li><li><strong>SKP 5. Mengurangi Risiko Infeksi (HAIs):</strong> Melaksanakan kebersihan tangan Five Moments, teknik aseptik, penggunaan APD, menjaga kebersihan alat dan lingkungan perawatan.[cite: 3]</li><li><strong>SKP 6. Mengurangi Risiko Cedera Jatuh:</strong> Melakukan asesmen risiko jatuh (saat mobilisasi pertama pasca operasi), mendampingi saat berjalan, memastikan tempat tidur aman, edukasi keluarga.[cite: 3]</li></ul>'
                ],
                [
                    'tema' => 'Penerapan Sasaran Keselamatan Pasien (6 SKP)',
                    'indikator' => 'Analisis Kasus (Emergency Code)',
                    'pertanyaan' => '<strong>KASUS 4 (KEPERAWATAN): Pasien Stroke di Ruang Rawat Inap Saat Terjadi Berbagai Kejadian Darurat</strong><br><br>Anda bertugas sebagai perawat shift pagi di Ruang Rawat Inap Penyakit Dalam.[cite: 3] Pada pukul 09.00 WIB terdapat pasien laki-laki berusia 67 tahun dengan diagnosis Stroke Hemoragik yang sedang menjalani terapi oksigen, infus, dan NGT.[cite: 3] Saat melakukan observasi, kondisi ruang perawatan berubah sangat dinamis.[cite: 3]<br><br>Dalam satu hari dinas terjadi beberapa kejadian berikut secara terpisah:<br><ol><li>Seorang pasien di ruang sebelah tiba-tiba tidak sadar, tidak bernapas, dan tidak teraba nadi.[cite: 3]</li><li>Di pantry ruang perawatan muncul asap tebal akibat hubungan arus pendek listrik.[cite: 3]</li><li>Petugas keamanan menerima laporan adanya seseorang yang membawa senjata tajam dan mengancam tenaga kesehatan di poliklinik.[cite: 3]</li><li>Rumah sakit menerima ancaman melalui telepon yang menyatakan terdapat bom di gedung rawat inap.[cite: 3]</li><li>Terjadi gempa bumi yang cukup kuat hingga lampu ruangan bergoyang dan sebagian plafon mengalami keretakan.[cite: 3]</li><li>Pada saat bersamaan, BPBD menginformasikan akan datang korban kecelakaan massal sehingga rumah sakit harus mengaktifkan sistem penanggulangan bencana.[cite: 3]</li></ol><strong>Pertanyaan Interview:</strong><br>Berdasarkan situasi di atas, jelaskan jenis Emergency Code yang harus digunakan pada masing-masing kondisi serta arti dari setiap kode tersebut.[cite: 3]',
                    'jawaban' => '<u>Mengidentifikasi Kode Darurat (Emergency Code):</u><br><br><ul><li><strong>Code Blue (Kode Biru):</strong> Untuk kegawatdaruratan medis (henti jantung, henti napas, pasien tidak sadar).[cite: 3] Tujuan: Mengaktifkan Tim Code Blue (resusitasi).[cite: 3]</li><li><strong>Code Red (Kode Merah):</strong> Untuk kejadian kebakaran/asap korsleting/ledakan kecil.[cite: 3] Tujuan: Mengaktifkan Tim Penanggulangan Kebakaran.[cite: 3]</li><li><strong>Code Pink:</strong> Dugaan penculikan/kehilangan bayi/anak.[cite: 3] Tujuan: Mengamankan akses RS dan pencarian.[cite: 3]</li><li><strong>Code Black (Kode Hitam):</strong> Ancaman bom, senjata, paket mencurigakan.[cite: 3] Tujuan: Pengamanan dan koordinasi aparat.[cite: 3]</li><li><strong>Code Grey (Kode Abu-Abu):</strong> Pasien/keluarga mengamuk, kekerasan, ancaman petugas.[cite: 3] Tujuan: Mengendalikan situasi agar aman.[cite: 3]</li><li><strong>Code Purple (Kode Ungu):</strong> Bencana massal / korban massal.[cite: 3] Tujuan: Mengaktifkan Hospital Disaster Plan.[cite: 3]</li><li><strong>Code Green (Kode Hijau):</strong> Gempa bumi.[cite: 3] Tujuan: Melaksanakan prosedur keselamatan dan evakuasi.[cite: 3]</li></ul>'
                ],
                [
                    'tema' => 'Penerapan Sasaran Keselamatan Pasien (6 SKP)',
                    'indikator' => 'Analisis Kasus (Emergency Code)',
                    'pertanyaan' => '<strong>Kasus 5 (KEBIDANAN) Ruang Bersalin dan Perinatologi</strong><br><br>Anda bertugas sebagai bidan di ruang bersalin dan perinatologi rumah sakit.[cite: 3] Pada saat pergantian shift, beberapa situasi darurat terjadi secara bergantian:<br><ol><li>Seorang ibu postpartum mengalami perdarahan hebat hingga tidak sadar dan henti napas.[cite: 3]</li><li>Di ruang penyimpanan linen muncul percikan api akibat korsleting listrik.[cite: 3]</li><li>Seorang bayi baru lahir tidak ditemukan di ruang perinatologi dan diduga dibawa oleh orang yang tidak dikenal.[cite: 3]</li><li>Seorang anggota keluarga pasien membawa senjata tajam dan mengancam bidan di ruang bersalin.[cite: 3]</li><li>Rumah sakit menerima ancaman bom melalui sambungan telepon.[cite: 3]</li><li>Pemerintah daerah menetapkan status tanggap darurat karena banjir besar sehingga RS harus menerima korban massal.[cite: 3]</li><li>Saat pelayanan berlangsung terjadi gempa bumi.[cite: 3]</li></ol><strong>Pertanyaan Interview:</strong><br>Jelaskan jenis Emergency Code yang sesuai dengan masing-masing kejadian beserta arti dan tujuan penggunaannya.[cite: 3]',
                    'jawaban' => '<u>Mengidentifikasi Kode Darurat (Emergency Code):</u><br><br><ul><li><strong>Code Blue:</strong> Kegawatdaruratan medis (ibu postpartum henti napas/jantung).[cite: 3]</li><li><strong>Code Red:</strong> Kebakaran atau muncul api/asap di ruang linen.[cite: 3]</li><li><strong>Code Pink:</strong> Dugaan penculikan / kehilangan bayi di perinatologi.[cite: 3]</li><li><strong>Code Black:</strong> Ancaman bom, senjata, atau paket mencurigakan.[cite: 3]</li><li><strong>Code Grey:</strong> Gangguan keamanan, agresif/kekerasan terhadap petugas.[cite: 3]</li><li><strong>Code Purple:</strong> Bencana / kejadian korban massal (banjir besar).[cite: 3]</li><li><strong>Code Green:</strong> Gempa bumi yang mengharuskan penerapan prosedur keselamatan/evakuasi.[cite: 3]</li></ul>'
                ],
            ],
            'tema_4' => [
                [
                    'tema' => 'Kompetensi Dasar Keperawatan 1 (Tanda-tanda Vital, Pemenuhan Kebutuhan Oksigen, Pemenuhan Kebutuhan Cairan dan Elektrolit)',
                    'indikator' => 'Menganalisis hasil TTV',
                    'pertanyaan' => '<strong>Kasus 1:</strong> Pria 58 tahun datang ke IGD dengan keluhan sesak progresif, batuk berdahak, riwayat hipertensi.[cite: 4] Hasil pemeriksaan: TD 88/56 mmHg, Nadi 128x/menit, RR 34x/menit, Suhu 38,9°C, SpO₂ 84% udara ruangan, CRT 4 detik.[cite: 4]<br><br><strong>Pertanyaan:</strong><br>Jelaskan interpretasi seluruh tanda vital, prioritas masalah, tindakan awal berdasarkan ABCDE, kebutuhan kolaborasi, monitoring lanjutan, dan dokumentasi.[cite: 4]',
                    'jawaban' => '<u>Menentukan prioritas klinis:</u><br><br><strong>Interpretasi:</strong> Pasien mengalami gangguan oksigenasi berat dengan tanda syok/hipoperfusi (TD 88/56 mmHg, Nadi 128 x/menit, RR 34 x/menit, SpO₂ 84%, CRT 4 detik) disertai demam yang mengarah pada infeksi berat.[cite: 4]<br><strong>Prioritas masalah:</strong> Gangguan pertukaran gas dan perfusi jaringan tidak efektif.[cite: 4]<br><strong>Tindakan awal (ABCDE):</strong><ul><li>A: Pastikan jalan napas terbuka.[cite: 4]</li><li>B: Berikan oksigen konsentrasi tinggi dan monitor SpO₂.[cite: 4]</li><li>C: Pasang akses IV, monitor TD dan nadi, siapkan resusitasi cairan sesuai instruksi.[cite: 4]</li><li>D: Nilai tingkat kesadaran (GCS).[cite: 4]</li><li>E: Evaluasi suhu dan lakukan pemeriksaan fisik menyeluruh.[cite: 4]</li></ul><strong>Kolaborasi:</strong> Segera laporkan ke dokter, lakukan EKG, cek AGD, lab, foto toraks, dan terapi sesuai instruksi.[cite: 4]<br><strong>Monitoring:</strong> Pantau TTV, SpO₂, GCS, urine output, dan respons terhadap terapi secara berkala.[cite: 4]<br><strong>Dokumentasi:</strong> Catat hasil pengkajian, tindakan, respons, komunikasi (ISBAR), dan evaluasi secara lengkap, objektif, dan tepat waktu.[cite: 4]'
                ],
                [
                    'tema' => 'Kompetensi Dasar Keperawatan 1 (Tanda-tanda Vital, Pemenuhan Kebutuhan Oksigen, Pemenuhan Kebutuhan Cairan dan Elektrolit)',
                    'indikator' => 'Menentukan terapi oksigen',
                    'pertanyaan' => '<strong>Kasus 2:</strong> Pasien PPOK eksaserbasi mengalami RR 30x/menit, SpO₂ 82%, penggunaan otot bantu napas.[cite: 4] Dokter meminta terapi oksigen.[cite: 4]<br><br><strong>Pertanyaan:</strong><br>Jelaskan pemilihan alat, target saturasi, alasan ilmiah, pemantauan klinis, serta risiko pemberian oksigen berlebihan.[cite: 4]',
                    'jawaban' => '<u>Menjelaskan terapi oksigen tepat:</u><br><br><strong>Pemilihan alat:</strong> Gunakan Venturi mask (atau nasal kanul sesuai instruksi) karena memberikan konsentrasi oksigen yang terkontrol.[cite: 4]<br><strong>Target saturasi:</strong> SpO₂ 88–92% pada pasien PPOK.[cite: 4]<br><strong>Alasan ilmiah:</strong> Meningkatkan oksigenasi tanpa meningkatkan risiko retensi CO₂.[cite: 4]<br><strong>Pemantauan:</strong> Pantau SpO₂, RR, pola napas, kesadaran, TTV, dan bila perlu AGD.[cite: 4]<br><strong>Risiko oksigen berlebihan:</strong> Menyebabkan retensi CO₂ (hiperkapnia), depresi napas, penurunan kesadaran, hingga gagal napas.[cite: 4]'
                ],
                [
                    'tema' => 'Kompetensi Dasar Keperawatan 1 (Tanda-tanda Vital, Pemenuhan Kebutuhan Oksigen, Pemenuhan Kebutuhan Cairan dan Elektrolit)',
                    'indikator' => 'Menganalisis cairan dan elektrolit',
                    'pertanyaan' => '<strong>Kasus 3:</strong> Pasien diare dan muntah 3 hari, TD 82/50 mmHg, Nadi 132x/menit, RR 28x/menit, mukosa kering, turgor menurun, urine 200 mL/24 jam.[cite: 4]<br><br><strong>Pertanyaan:</strong><br>Jelaskan gangguan yang terjadi, kemungkinan perubahan elektrolit, intervensi prioritas, parameter evaluasi, dan edukasi.[cite: 4]',
                    'jawaban' => '<u>Mampu menentukan intervensi:</u><br><br><strong>Gangguan:</strong> Pasien mengalami hipovolemia/dehidrasi berat dengan risiko syok hipovolemik.[cite: 4]<br><strong>Perubahan elektrolit:</strong> Kemungkinan hiponatremia, hipokalemia, dan gangguan keseimbangan asam-basa.[cite: 4]<br><strong>Intervensi prioritas:</strong> Pendekatan ABCDE, pasang akses IV, kolaborasi terapi cairan sesuai program, pantau TTV, intake-output, dan elektrolit.[cite: 4]<br><strong>Parameter evaluasi:</strong> TD meningkat, nadi & RR membaik, mukosa lembap, turgor membaik, urine ≥0,5 mL/kgBB/jam, elektrolit batas normal.[cite: 4]<br><strong>Edukasi:</strong> Anjurkan rehidrasi oral (oralit), kenali tanda bahaya dehidrasi, segera kontrol bila memburuk.[cite: 4]'
                ],
                [
                    'tema' => 'Kompetensi Dasar Keperawatan 1 (Tanda-tanda Vital, Pemenuhan Kebutuhan Oksigen, Pemenuhan Kebutuhan Cairan dan Elektrolit)',
                    'indikator' => 'Mengintegrasikan data klinis',
                    'pertanyaan' => '<strong>Kasus 4:</strong> Pasien pasca operasi abdomen mengalami TD 100/70 mmHg, Nadi 116x/menit, RR 26x/menit, SpO₂ 90%, produksi urin menurun.[cite: 4]<br><br><strong>Pertanyaan:</strong><br>Jelaskan hubungan perubahan TTV, status oksigenasi dan cairan, diagnosis keperawatan yang mungkin, intervensi prioritas, serta evaluasi.[cite: 4]',
                    'jawaban' => '<u>Mengintegrasikan pengkajian:</u><br><br><strong>Hubungan TTV, oksigenasi, dan cairan:</strong> Takikardia, takipnea, SpO₂ 90%, dan penurunan urin menunjukkan kemungkinan hipovolemia disertai gangguan oksigenasi pasca operasi.[cite: 4]<br><strong>Diagnosis keperawatan:</strong> Perfusi jaringan tidak efektif, gangguan pertukaran gas, dan defisit volume cairan.[cite: 4]<br><strong>Intervensi prioritas:</strong> Pendekatan ABCDE, berikan oksigen indikasi, monitor TTV dan SpO₂, pantau intake-output, evaluasi perdarahan, kolaborasi cairan, dokumentasikan hasil.[cite: 4]<br><strong>Evaluasi:</strong> TTV stabil, SpO₂ ≥94%, produksi urin ≥0,5 mL/kgBB/jam, pasien tidak sesak, perfusi jaringan membaik.[cite: 4]'
                ],
                [
                    'tema' => 'Kompetensi Dasar Keperawatan 1 (Tanda-tanda Vital, Pemenuhan Kebutuhan Oksigen, Pemenuhan Kebutuhan Cairan dan Elektrolit)',
                    'indikator' => 'Pengambilan keputusan klinis',
                    'pertanyaan' => '<strong>Kasus 5:</strong> Selama observasi pasien mengalami penurunan SpO₂ dari 96% menjadi 88%, RR meningkat dari 20 menjadi 32x/menit, TD turun menjadi 86/54 mmHg dan urine semakin sedikit.[cite: 4]<br><br><strong>Pertanyaan:</strong><br>Jelaskan langkah pengambilan keputusan klinis perawat mulai dari pengkajian ulang, prioritas tindakan, komunikasi kepada dokter menggunakan ISBAR, dokumentasi, hingga evaluasi hasil tindakan.[cite: 4]',
                    'jawaban' => '<u>Menunjukkan clinical reasoning:</u><br><br><strong>Pengkajian ulang:</strong> Lakukan ABCDE, nilai jalan napas, napas, sirkulasi, kesadaran, SpO₂, TTV, dan urine output.[cite: 4]<br><strong>Prioritas tindakan:</strong> Berikan oksigen, monitor ketat, pertahankan akses IV, siapkan tindakan resusitasi sesuai instruksi, observasi kondisi.[cite: 4]<br><strong>Komunikasi ISBAR:</strong> Laporkan identitas pasien, kondisi terkini (SpO₂ 88%, RR 32x/menit, TD 86/54 mmHg, urine menurun), hasil pengkajian, serta rekomendasi penanganan segera.[cite: 4]<br><strong>Dokumentasi:</strong> Catat hasil pengkajian, tindakan, komunikasi dokter, instruksi, respons pasien, evaluasi secara lengkap dan objektif.[cite: 4]<br><strong>Evaluasi:</strong> TTV membaik, SpO₂ meningkat ≥94% (atau target), urine output membaik, kondisi stabil.[cite: 4]'
                ],
            ],
            'tema_5' => [
                [
                    'tema' => 'Kompetensi Dasar Keperawatan 2 (Perawatan Luka, Pemberian Obat Secara Aman dan Tepat, Pemberian Darah dan Produk Darah Secara Aman)',
                    'indikator' => 'Menganalisis perawatan luka',
                    'pertanyaan' => '<strong>Kasus 1.</strong> Pasien DM tipe 2 usia 62 tahun datang dengan ulkus kaki kanan sejak 3 minggu.[cite: 5] Luka berukuran 5×4 cm, kedalaman 1 cm, terdapat jaringan nekrotik 30%, slough, eksudat sedang, bau, nyeri skala 6, GDP 320 mg/dL.[cite: 5]<br><br><strong>Pertanyaan:</strong><br>Jelaskan pengkajian luka secara komprehensif, klasifikasi luka, prinsip TIME, pemilihan balutan, teknik aseptik, edukasi, dan evaluasi penyembuhan.[cite: 5]',
                    'jawaban' => '<u>Mampu melakukan clinical reasoning:</u><br><br><strong>Pengkajian:</strong> Nilai ukuran, kedalaman, lokasi luka, jaringan nekrotik/slough, eksudat, bau, nyeri, tanda infeksi, status vaskular, dan gula darah.[cite: 5]<br><strong>Klasifikasi:</strong> Ulkus kaki diabetik dengan tanda infeksi dan jaringan nekrotik.[cite: 5]<br><strong>Prinsip TIME:</strong><ul><li>T (Tissue): Debridement jaringan nekrotik.[cite: 5]</li><li>I (Infection): Kendalikan infeksi.[cite: 5]</li><li>M (Moisture): Jaga kelembapan luka dengan balutan yang sesuai.[cite: 5]</li><li>E (Edge): Evaluasi tepi luka dan stimulasi penyembuhan.[cite: 5]</li></ul><strong>Balutan & Teknik:</strong> Gunakan balutan modern sesuai kondisi luka dengan teknik aseptik.[cite: 5]<br><strong>Edukasi:</strong> Kontrol gula darah, nutrisi tinggi protein, perawatan kaki diabetik, hindari tekanan pada luka, kontrol rutin.[cite: 5]<br><strong>Evaluasi:</strong> Luka mengecil, jaringan granulasi meningkat, eksudat dan bau berkurang, nyeri menurun, tidak ada infeksi, kadar glukosa darah terkontrol.[cite: 5]'
                ],
                [
                    'tema' => 'Kompetensi Dasar Keperawatan 2 (Perawatan Luka, Pemberian Obat Secara Aman dan Tepat, Pemberian Darah dan Produk Darah Secara Aman)',
                    'indikator' => 'Menganalisis luka operasi',
                    'pertanyaan' => '<strong>Kasus 2.</strong> Hari kedua pasca laparotomi pasien mengalami demam 38,7°C, balutan basah, keluar cairan purulen, luka tampak kemerahan dan nyeri.[cite: 5]<br><br><strong>Pertanyaan:</strong><br>Jelaskan prioritas pengkajian, tanda infeksi, prosedur penggantian balutan steril, kolaborasi yang diperlukan, pencegahan infeksi, dan dokumentasi.[cite: 5]',
                    'jawaban' => '<u>Menentukan prioritas:</u><br><br><strong>Prioritas pengkajian:</strong> Nilai kondisi luka (ukuran, kemerahan, eksudat, bau, nyeri), tanda vital, dan tanda infeksi sistemik.[cite: 5]<br><strong>Tanda infeksi:</strong> Demam, kemerahan, cairan purulen, balutan basah, nyeri, dan pembengkakan.[cite: 5]<br><strong>Penggantian balutan:</strong> Lakukan hand hygiene, gunakan APD, buka balutan lama dengan teknik bersih, bersihkan luka & ganti balutan teknik aseptik/steril, buang limbah sesuai prosedur.[cite: 5]<br><strong>Kolaborasi:</strong> Laporkan kepada dokter, lakukan pemeriksaan kultur luka bila diindikasikan, serta pemberian antibiotik sesuai instruksi.[cite: 5]<br><strong>Pencegahan infeksi:</strong> Terapkan hand hygiene, teknik aseptik, penggunaan APD, dan perawatan luka sesuai standar PPI.[cite: 5]<br><strong>Dokumentasi:</strong> Catat kondisi luka, tindakan yang dilakukan, respons pasien, kolaborasi, dan hasil evaluasi secara lengkap, objektif, dan tepat waktu.[cite: 5]'
                ],
                [
                    'tema' => 'Kompetensi Dasar Keperawatan 2 (Perawatan Luka, Pemberian Obat Secara Aman dan Tepat, Pemberian Darah dan Produk Darah Secara Aman)',
                    'indikator' => 'Menerapkan pemberian obat aman',
                    'pertanyaan' => '<strong>Kasus 3.</strong> Perawat akan memberikan insulin kerja cepat, antibiotik intravena, dan kalium intravena pada pasien dengan riwayat alergi penisilin dan penyakit ginjal kronis.[cite: 5]<br><br><strong>Pertanyaan:</strong><br>Jelaskan seluruh tahapan pemberian obat secara aman mulai verifikasi resep, identifikasi pasien, prinsip 10 benar, double check, pencegahan medication error, monitoring efek samping, hingga dokumentasi.[cite: 5]',
                    'jawaban' => '<u>Menerapkan prinsip keselamatan obat:</u><br><br><strong>Tahapan pemberian obat aman:</strong><ul><li>Verifikasi resep, dosis, rute, waktu, dan cek riwayat alergi penisilin serta fungsi ginjal.[cite: 5]</li><li>Identifikasi pasien menggunakan dua identitas.[cite: 5]</li><li>Terapkan prinsip 10 benar pemberian obat.[cite: 5]</li><li>Lakukan double check untuk insulin dan kalium (obat high alert).[cite: 5]</li><li>Cegah medication error dengan memastikan obat, dosis, pasien, dan rute benar serta memperhatikan kompatibilitas obat.[cite: 5]</li><li>Monitor kadar glukosa, tanda alergi, efek samping, fungsi ginjal, dan tanda vital selama pemberian.[cite: 5]</li><li>Dokumentasikan obat yang diberikan, dosis, waktu, rute, respons pasien, dan efek samping secara lengkap, objektif, dan tepat waktu.[cite: 5]</li></ul>'
                ],
                [
                    'tema' => 'Kompetensi Dasar Keperawatan 2 (Perawatan Luka, Pemberian Obat Secara Aman dan Tepat, Pemberian Darah dan Produk Darah Secara Aman)',
                    'indikator' => 'Menangani reaksi obat',
                    'pertanyaan' => '<strong>Kasus 4.</strong> Lima menit setelah pemberian antibiotik intravena pasien mengalami gatal seluruh tubuh, sesak napas, hipotensi, dan urtikaria.[cite: 5]<br><br><strong>Pertanyaan:</strong><br>Jelaskan identifikasi kondisi, tindakan segera, kolaborasi, pemantauan lanjutan, pelaporan insiden, dan dokumentasi.[cite: 5]',
                    'jawaban' => '<u>Mampu menangani kondisi gawat:</u><br><br><strong>Identifikasi kondisi:</strong> Pasien mengalami reaksi anafilaksis ditandai gatal, urtikaria, sesak napas, dan hipotensi setelah pemberian antibiotik.[cite: 5]<br><strong>Tindakan segera:</strong> Hentikan pemberian antibiotik, pertahankan akses IV dengan NaCl 0,9%, lakukan penilaian ABCDE, berikan oksigen, panggil bantuan segera.[cite: 5]<br><strong>Kolaborasi:</strong> Segera laporkan ke dokter, siapkan pemberian obat sesuai instruksi (misalnya adrenalin, antihistamin, kortikosteroid), serta siapkan resusitasi bila diperlukan.[cite: 5]<br><strong>Pemantauan:</strong> Pantau tanda vital, SpO₂, jalan napas, kesadaran, dan respons terhadap terapi.[cite: 5]<br><strong>Pelaporan insiden:</strong> Laporkan sebagai Kejadian Tidak Diharapkan (KTD) atau reaksi obat sesuai prosedur rumah sakit.[cite: 5]<br><strong>Dokumentasi:</strong> Catat waktu kejadian, gejala, obat yang diberikan, tindakan, respons pasien, instruksi dokter, dan hasil evaluasi secara lengkap, objektif, dan tepat waktu.[cite: 5]'
                ],
                [
                    'tema' => 'Kompetensi Dasar Keperawatan 2 (Perawatan Luka, Pemberian Obat Secara Aman dan Tepat, Pemberian Darah dan Produk Darah Secara Aman)',
                    'indikator' => 'Melaksanakan transfusi darah aman',
                    'pertanyaan' => '<strong>Kasus 5.</strong> Pasien anemia berat Hb 6,5 g/dL akan mendapat transfusi PRC.[cite: 5] Setelah 15 menit transfusi pasien demam, menggigil, nyeri pinggang, dan sesak.[cite: 5]<br><br><strong>Pertanyaan:</strong><br>Jelaskan prosedur transfusi darah yang benar sejak persiapan, verifikasi identitas dan produk darah, pemantauan selama transfusi, identifikasi reaksi transfusi, tindakan segera, pelaporan ke bank darah, dan dokumentasi.[cite: 5]',
                    'jawaban' => '<u>Menerapkan prosedur transfusi aman:</u><br><br><strong>Prosedur transfusi:</strong> Verifikasi dua identitas pasien, kecocokan kantong darah (golongan darah, nomor unit, tanggal kedaluwarsa), lakukan baseline tanda vital, dan observasi ketat terutama 15 menit pertama.[cite: 5]<br><strong>Identifikasi reaksi transfusi:</strong> Demam, menggigil, nyeri pinggang, dan sesak menunjukkan reaksi transfusi.[cite: 5]<br><strong>Tindakan segera:</strong> Hentikan transfusi, pertahankan jalur IV dengan NaCl 0,9%, lakukan penilaian ABCDE, monitor tanda vital, dan berikan oksigen bila diperlukan.[cite: 5]<br><strong>Kolaborasi:</strong> Segera laporkan ke dokter dan Bank Darah, kirim kantong darah beserta sampel darah pasien sesuai prosedur untuk investigasi.[cite: 5]<br><strong>Dokumentasi:</strong> Catat waktu kejadian, tanda dan gejala, hasil pemantauan, tindakan yang dilakukan, instruksi dokter, pelaporan ke Bank Darah, serta respons pasien secara lengkap, objektif, dan tepat waktu.[cite: 5]'
                ],
            ],
        ];
    }

    /**
     * TAHAP 1: FORMAT CHECKLIST UJI SKILL (DITAMPILKAN SEMUA/FLAT)
     */
private function getFormatUjiSkill()
    {
        return [
            'apar' => [
                'judul' => '1. Keterampilan Penggunaan APAR (11 Poin)',
                'komponen' => [
                    'Aktivasi Code Red di telepon 1133.',
                    'Mengetahui warna helm Siaga Code Red.',
                    'Cek Tekanan (Pressure Gauge): Peserta memeriksa jarum indikator tekanan pada tabung dan memastikan berada di area hijau (tekanan normal).',
                    'Cek Fisik Tabung: Peserta memastikan selang tidak retak/bocor, corong tidak tersumbat, dan segel pengaman masih utuh.',
                    'Cara Membawa APAR: Peserta menjinjing APAR dengan benar (memegang handle bawah/leher tabung) atau memanggulnya di atas bahu.',
                    'Memperhatikan Arah Angin: Peserta memosisikan diri searah dengan arah angin (membelakangi arah angin, tidak menantang angin).',
                    'Jarak Aman: Peserta mengambil posisi berdiri pada jarak aman dari sumber api (sekitar 2-3 meter).',
                    '"TARIK PIN": Peserta menarik pin pengaman/segel plastik dengan tegas.',
                    '"ARAHKAN CORONG": Peserta memegang ujung selang/corong (nozzle) dengan mantap dan diarahkan tepat ke dasar/pusat api.',
                    '"TEKAN TUAS": Peserta menekan tuas bagian atas secara penuh/maksimal agar media pemadam keluar dengan kekuatan optimal.',
                    '"SAPUKAN": Peserta menggerakkan selang secara menyapu (ke kiri dan ke kanan) secara merata hingga api benar-benar padam.'
                ]
            ],
            'oksigenasi' => [
                'judul' => '2. Keterampilan Pemberian Oksigenasi (11 Poin)',
                'komponen' => [
                    'Mampu menilai jalan napas: Look (lihat gerak dada/perut, warna mukosa), Listen (dengar gerak udara), dan Feel (rasakan gerak udara dengan pipi).',
                    'Mampu menilai sumbatan jalan napas TOTAL (kesulitan bernapas, pernapasan see-saw) maupun PARSIAL (snoring, gurgling, stridor).',
                    'Mampu menjelaskan/memperagakan cara membebaskan jalan napas sumbatan TOTAL baik tanpa alat (Backblow, Heimlich) maupun dengan alat (Magyl forcep).',
                    'Mampu memperagakan cara membebaskan jalan napas Sumbatan jalan napas PARSIAL tanpa alat: Head-tilt, Chin-lift, Jaw thrust.',
                    'Mampu memperagakan cara membebaskan jalan napas Sumbatan jalan napas PARSIAL dengan alat: OPA, NPA, Magyl forcep, Suction, ETT, LMA, Trakeostomi.',
                    'Mampu memperagakan cara mengukur dan cara pemasangan OPA dan NPA.',
                    'Mampu menyebutkan dan mengenali macam-macam alat untuk memberikan bantuan napas: pocket mask, nasal canul, simple mask, NRM, RM, BVM, Jackson rees.',
                    'Mampu memperagakan pemberian napas bantuan dengan menggunakan BVM dengan baik dan benar.',
                    'Mengetahui dosis pemberian oksigen dan FiO2 yang dihasilkan sesuai alat yang digunakan (nasal canul, simple mask, NRM, dll).',
                    'Memposisikan pasien pada posisi recovery (pemulihan) pasca RJP.',
                    'Memahami Tindakan pasca RJP dan indikasi penghentian RJP: Sirkulasi spontan kembali, tim advance tiba, penolong kelelahan, indikasi DNR, atau ada tanda kematian.'
                ]
            ],
            'bhd' => [
                'judul' => '3. Keterampilan Bantuan Hidup Dasar / BHD (11 Poin)',
                'komponen' => [
                    'Memastikan aman diri / penolong (Menggunakan APD).',
                    'Memastikan Aman lingkungan (lingkungan aman dari bahaya).',
                    'Memastikan Aman korban (Korban ditempatkan di area aman, datar, keras).',
                    'Mengecek kesadaran korban (5-10 detik) dengan memanggil suara keras dan memberi rangsang nyeri (tepuk bahu, pangkal kuku, otot trapesium, atau supraorbital).',
                    'Meminta bantuan: Berteriak meminta tolong dan Aktivasi Code Blue di Nomor 1133 (menyebutkan situasi, jumlah korban, lokasi, dan meminta AED).',
                    'Mengecek napas (Head-Tilt Chin-Lift/jaw-thrust) dan Nadi Karotis secara bersamaan (5-10 detik).',
                    'Menentukan titik tumpuan RJP: Menemukan tulang dada (sternum) di posisi setengah bawah atau menggunakan garis antar puting sebagai acuan.',
                    'Melakukan 30x kompresi dada: Kecepatan 100-120x/menit, kedalaman 5-6 cm, beri kesempatan dada mengembang sempurna (recoil), dan minimal interupsi.',
                    'Memberikan 2x bantuan napas: Menutup lubang hidung saat meniup (selama 1 detik) dan menghindari ventilasi berlebihan (excessive ventilation).',
                    'Melakukan Re-assessment (evaluasi ulang) napas dan nadi karotis secara bersamaan setelah melakukan RJP sebanyak 5 siklus (2 menit).',
                    'Memahami indikasi penghentian RJP: Sirkulasi spontan kembali, tim advance tiba, >30 menit tanpa respon, penolong kelelahan, indikasi DNR, atau tanda kematian irreversibel.'
                ]
            ]
        ];
    }
     
public function edit($id)
    {
        $pengajuan = PengajuanSertifikat::with(['user', 'jadwalWawancara.penilaian', 'lisensiLama'])->findOrFail($id);
        $pjs = PenanggungJawabUjian::all();

        $selectedKfk = is_array($pengajuan->kfk) ? $pengajuan->kfk : json_decode($pengajuan->kfk, true) ?? [];

        $jadwal = $pengajuan->jadwalWawancara;
        $penilaian = $jadwal ? $jadwal->penilaian : null;
        $isKredensial = $pengajuan->metode === 'interview_only';

        $soalTerpilih = [];
        $formatUjiSkill = [];
        $rkkItems = collect();
        $rkkGrouped = collect();
        $namaKfk = '-'; 
        $namaJenjang = '-';
        $bankSoal = [];

        if (!$isKredensial) {
            $bankSoal = $this->getBankSoalLisan();
            $formatUjiSkill = $this->getFormatUjiSkill();
            $nilaiData = is_string($penilaian->nilai_pertanyaan ?? null) ? json_decode($penilaian->nilai_pertanyaan, true) : ($penilaian->nilai_pertanyaan ?? []);
            
            $draftValid = false;
            if (!empty($nilaiData)) {
                $draftValid = true;
                foreach ($nilaiData as $key => $item) {
                    if (!isset($item['tema'])) { $draftValid = false; break; }
                    $soalTerpilih[$key] = $item;
                }
            }
            if (!$draftValid) {
                $soalTerpilih = []; $i = 1;
                $daftarTema = ['tema_1', 'tema_2', 'tema_3', 'tema_4', 'tema_5'];
                foreach ($daftarTema as $temaKey) {
                    $kumpulanSoal = $bankSoal[$temaKey] ?? []; 
                    if (!empty($kumpulanSoal)) {
                        $indexAcak = array_rand($kumpulanSoal); 
                        $soalAcak = $kumpulanSoal[$indexAcak];
                        $soalTerpilih[$i] = [
                            'tema'       => $soalAcak['tema'],
                            'pertanyaan' => $soalAcak['pertanyaan'],
                            'jawaban'    => $soalAcak['jawaban'],
                            'skor'       => null 
                        ];
                        $i++;
                    }
                }
            }
        } else {
            $detailPenilaian = [];
            if (isset($penilaian) && $penilaian->detail_penilaian) {
                $detailPenilaian = is_string($penilaian->detail_penilaian) ? json_decode($penilaian->detail_penilaian, true) : $penilaian->detail_penilaian;
            }

            foreach ($selectedKfk as $item) {
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

// KODE BARU: Menggunakan whereIn untuk mencari kedua string
$rkkItems = BankItemRkk::where('kfk', 'like', "%{$namaKfkBersih}%")
    ->whereIn('jenjang_klinis', array_unique([$namaJenjang, $jenjangQuery]))
    ->orderBy('kategori_tugas', 'asc')
    ->orderBy('no_item', 'asc')
    ->get();
                
            if (isset($detailPenilaian['is_mpp']) && $detailPenilaian['is_mpp'] == '1') {
                $mppCollection = collect($this->getMppItems())->map(function($item) {
                    $obj = (object)$item;
                    $obj->id = $item['no_item']; 
                    return $obj;
                });
                $rkkItems = $rkkItems->concat($mppCollection);
            }

            if (isset($detailPenilaian['is_ppi']) && $detailPenilaian['is_ppi'] == '1') {
                $ppiCollection = collect($this->getPpiItems())->map(function($item) {
                    $obj = (object)$item;
                    $obj->id = $item['no_item']; 
                    return $obj;
                });
                $rkkItems = $rkkItems->concat($ppiCollection);
            }
                    
            $rkkGrouped = $rkkItems->groupBy('kategori_tugas');
        }

        return view('admin.pengajuan.edit', compact(
            'pengajuan', 'pjs', 'selectedKfk', 'jadwal', 'penilaian', 'isKredensial',
            'soalTerpilih', 'formatUjiSkill', 'rkkItems', 'rkkGrouped', 'namaKfk', 'namaJenjang', 'bankSoal'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'file_dokumen_baru'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_sertifikat_lama'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_surat_rekomendasi' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        $pengajuan = PengajuanSertifikat::with(['jadwalWawancara.penilaian', 'lisensiLama'])->findOrFail($id);

        DB::beginTransaction();
        try {
            // STEP 1: Update Pengajuan
            $pengajuan->jenis_pengajuan = $request->jenis_pengajuan;
            $pengajuan->metode = $request->metode;
            $pengajuan->status = $request->status;
            $pengajuan->link_gdrive = $request->link_gdrive;
            $pengajuan->keterangan = $request->keterangan;

            if ($request->has('kfk')) {
                $pengajuan->kfk = $request->kfk;
            }

            // Update Lisensi Lama jika ada inputnya
            if ($pengajuan->lisensiLama && $request->has('lisensi_lama_nama')) {
                $pengajuan->lisensiLama->update([
                    'nama'  => $request->input('lisensi_lama_nama'),
                    'nomor' => $request->input('lisensi_lama_nomor'),
                ]);
            }

            if ($request->hasFile('file_dokumen_baru')) {
                if ($pengajuan->file_dokumen_baru) Storage::disk('public')->delete($pengajuan->file_dokumen_baru);
                $pengajuan->file_dokumen_baru = $request->file('file_dokumen_baru')->store('dokumen_pengajuan', 'public');
            }
            if ($request->hasFile('file_sertifikat_lama')) {
                if ($pengajuan->file_sertifikat_lama) Storage::disk('public')->delete($pengajuan->file_sertifikat_lama);
                $pengajuan->file_sertifikat_lama = $request->file('file_sertifikat_lama')->store('sertifikat_lama', 'public');
            }
            if ($request->hasFile('file_surat_rekomendasi')) {
                if ($pengajuan->file_surat_rekomendasi) Storage::disk('public')->delete($pengajuan->file_surat_rekomendasi);
                $pengajuan->file_surat_rekomendasi = $request->file('file_surat_rekomendasi')->store('surat_rekomendasi', 'public');
            }
            $pengajuan->save();

            // STEP 2 & 3: Update Jadwal & Penilaian
            $jadwal = $pengajuan->jadwalWawancara;
            if ($request->filled('waktu_wawancara')) {
                if (!$jadwal) {
                    $jadwal = new JadwalWawancara();
                    $jadwal->pengajuan_sertifikat_id = $pengajuan->id;
                }
                $jadwal->penanggung_jawab_id = $request->penanggung_jawab_id;
                $jadwal->waktu_wawancara = $request->waktu_wawancara;
                $jadwal->lokasi = $request->lokasi;
                $jadwal->status = $request->jadwal_status;
                $jadwal->save();

                if ($request->filled('keputusan_penilaian')) {
                    $penilaian = $jadwal->penilaian;
                    if (!$penilaian) {
                        $penilaian = new WawancaraPenilaian();
                        $penilaian->jadwal_wawancara_id = $jadwal->id;
                    }

                    $penilaian->keputusan = $request->keputusan_penilaian;
                    $penilaian->catatan_pewawancara = $request->catatan_pewawancara;

                    if ($pengajuan->metode == 'interview_only') {
                        $penilaian->detail_penilaian = json_encode([
                            'poin_penilaian' => $request->poin_penilaian ?? [],
                            'rkk' => $request->rkk ?? [],
                            'is_mpp' => $request->is_mpp ?? '0',
                            'is_ppi' => $request->is_ppi ?? '0'
                        ]);
                    } else {
                        if ($request->has('nilai_pertanyaan')) {
                            $penilaian->nilai_pertanyaan = $request->nilai_pertanyaan;
                        }
                        if ($request->has('uji_skill')) {
                            $penilaian->uji_skill = $request->uji_skill;
                        }
                    }
                    $penilaian->save();
                }
            }

            DB::commit();
            return redirect()->route('admin.pengajuan.index')->with('success', 'Data pengajuan & penilaian berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
    public function terbitkanKolektif(Request $request)
    {
        $request->validate([
            'pengajuan_ids' => 'required|array',
            'jenjang' => 'required|string'
        ]);

        $pengajuanIds = $request->pengajuan_ids;
        $tglMulai = now();
        $tglAkhir = now()->addYears(3); // Masa berlaku 3 tahun
        $tahunSurat = now()->format('Y');

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // 1. Generate SATU nomor urut kolektif untuk batch/rombongan ini.
            // Ambil total lisensi tahun ini ditambah 1
            $count = \App\Models\PerawatLisensi::whereYear('created_at', $tahunSurat)->count() + 1;
            $nomorBaruKolektif = $this->nomorBelumDipakai($count, fn($n) => str_pad($n, 3, '0', STR_PAD_LEFT) . "/418.101/SERKOM/{$tahunSurat}");

            // 2. Looping semua perawat yang terpilih
            foreach ($pengajuanIds as $id) {
                $pengajuan = \App\Models\PengajuanSertifikat::with('user')->find($id);
                if (!$pengajuan) continue;

                // Cek unit kerja terakhir
                $pekerjaan = \App\Models\PerawatPekerjaan::where('user_id', $pengajuan->user_id)
                                ->orderBy('tahun_mulai', 'desc')->first();

                // Pembuatan Data Lisensi Baru
                if ($pengajuan->jenis_pengajuan == 'baru' || !$pengajuan->lisensi_lama_id) {
                    
                    $bidangValue = (is_array($pengajuan->kfk) && isset($pengajuan->kfk[0])) ? $pengajuan->kfk[0] : null;
                    $kfkValue    = (is_array($pengajuan->kfk) && isset($pengajuan->kfk[1])) ? $pengajuan->kfk[1] : (is_string($pengajuan->kfk) ? $pengajuan->kfk : null);

                    $newLisensi = \App\Models\PerawatLisensi::create([
                        'user_id'              => $pengajuan->user_id,
                        'nama'                 => 'Penugasan Klinis (SPK & SPKK)',
                        'nomor'                => $nomorBaruKolektif, // Pakai Nomor Kolektif
                        'lembaga'              => 'RSUD Simpang Lima Gumul',
                        'bidang'               => $bidangValue,
                        'kfk'                  => $kfkValue,
                        'metode'               => $pengajuan->metode,
                        'status'               => 'active',
                        'tgl_terbit'           => $tglMulai,
                        'tgl_expired'          => $tglAkhir,
                        'tgl_mulai'            => $tglMulai,
                        'tgl_diselenggarakan'  => now(),
                        'unit_kerja_saat_buat' => $pekerjaan->unit_kerja ?? 'RSUD SLG'
                    ]);

                    $pengajuan->update([
                       'status' => 'completed',
                        'lisensi_lama_id' => $newLisensi->id
                    ]);

                } else {
                    // Jika ini proses perpanjangan
                    if ($pengajuan->lisensiLama) {
                        $pengajuan->lisensiLama->update([
                            'status'      => 'active',
                            'nomor'       => $nomorBaruKolektif, // Pakai Nomor Kolektif
                            'tgl_terbit'  => $tglMulai,
                            'tgl_expired' => $tglAkhir,
                        ]);
                    }
                    $pengajuan->update(['status' => 'disetujui']);
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', count($pengajuanIds) . ' SPK Kolektif berhasil diterbitkan untuk jenjang ' . $request->jenjang . ' dengan Nomor: ' . $nomorBaruKolektif);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal menerbitkan SPK Kolektif: ' . $e->getMessage());
        }
    }
    public function index(Request $request)
    {
        $listSertifikat = PerawatLisensi::select('nama')->distinct()->pluck('nama');
        $pjs = PenanggungJawabUjian::all();
$query = PengajuanSertifikat::with([
    'user.perawatProfile', // <-- Tambahkan ini (sesuaikan dengan nama relasi di model User)
    'lisensiLama', 
    'jadwalWawancara', 
    'user.examResult'
]);

        // Filter Pencarian (Nama/Email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Sertifikat
        if ($request->filled('sertifikat')) {
            $query->whereHas('lisensiLama', function ($q) use ($request) {
                $q->where('nama', $request->sertifikat);
            });
        }

        // Filter Ujian (Sudah/Belum)
        if ($request->filled('ujian')) {
            if ($request->ujian == 'sudah') {
                $query->whereHas('user.examResult');
            } elseif ($request->ujian == 'belum') {
                $query->whereDoesntHave('user.examResult');
            }
        }

        $pengajuan = $query->latest()->paginate(10)->withQueryString();

        return view('admin.pengajuan.index', compact('pengajuan', 'listSertifikat', 'pjs'));
    }

    public function show($id)
    {
        $pengajuan = PengajuanSertifikat::with([
            'user.examResult',
            'lisensiLama',
            'jadwalWawancara.pewawancara',
            'jadwalWawancara.penilaian'
        ])->findOrFail($id);

        return view('admin.pengajuan.show', compact('pengajuan'));
    }

/**
     * APPROVE PENGAJUAN (SINGLE)
     * REVISI: Menggunakan metode yang dipilih user, tidak memaksa berdasarkan jenis.
     */
    public function approve(Request $request, $id)
    {
        $pengajuan = PengajuanSertifikat::findOrFail($id);

        // 1. Validasi Tanggal
        $request->validate([
            'tgl_mulai_berlaku' => 'required|date',
            'tgl_akhir_berlaku' => 'required|date|after_or_equal:tgl_mulai_berlaku',
        ]);

        // 2. AMBIL METODE (Prioritas: Pilihan User -> Auto Detect)
        $metode = $pengajuan->metode;

        // Jika metode kosong (misal data lama), baru kita tebak
        if (empty($metode)) {
            $metode = ($pengajuan->jenis_pengajuan == 'baru') ? 'pg_interview' : 'interview_only';
        }

        // 3. TENTUKAN STATUS BERDASARKAN METODE
        if ($metode == 'pg_interview') {
            // Jika Uji Kompetensi -> Harus Ujian Tulis dulu
            $nextStatus = 'method_selected'; 
            $pesan = 'Metode: Uji Kompetensi. Menunggu peserta ujian.';
        } else {
            // Jika Interview Only / Kredensial -> Langsung Jadwal
            $nextStatus = 'interview_scheduled'; 
            $pesan = 'Metode: Kredensialing (Wawancara). Menunggu peserta atur jadwal.';
        }

        // 4. Update Database
        $pengajuan->update([
            'status'            => $nextStatus,
            'metode'            => $metode, // Simpan metode yang valid
            'tgl_mulai_berlaku' => $request->tgl_mulai_berlaku,
            'tgl_akhir_berlaku' => $request->tgl_akhir_berlaku,
        ]);

        return redirect()->back()->with('success', 'Pengajuan disetujui. ' . $pesan);
    }

    /**
     * BULK APPROVE (REVISI)
     * Sama, menghormati metode pilihan user per item.
     */
    public function bulkApprove(Request $request)
    {
        $ids = $request->ids;
        if (!$ids) return back()->with('error', 'Tidak ada data dipilih');

        $request->validate([
            'tgl_mulai_berlaku' => 'required|date',
            'tgl_akhir_berlaku' => 'required|date|after_or_equal:tgl_mulai_berlaku',
        ]);

        $count = 0;
        foreach ($ids as $id) {
            $pengajuan = PengajuanSertifikat::find($id);
            
            if ($pengajuan && $pengajuan->status == 'pending') {
                
                // 1. Ambil Metode per Item
                $metode = $pengajuan->metode;
                if (empty($metode)) {
                    $metode = ($pengajuan->jenis_pengajuan == 'baru') ? 'pg_interview' : 'interview_only';
                }

                // 2. Tentukan Status per Item
                if ($metode == 'pg_interview') {
                    $nextStatus = 'method_selected';
                } else {
                    $nextStatus = 'interview_scheduled';
                }

                $pengajuan->update([
                    'status'            => $nextStatus,
                    'metode'            => $metode,
                    'tgl_mulai_berlaku' => $request->tgl_mulai_berlaku,
                    'tgl_akhir_berlaku' => $request->tgl_akhir_berlaku
                ]);
                $count++;
            }
        }

        return back()->with('success', "$count pengajuan disetujui. Metode dan Tanggal telah ditetapkan.");
    }

    /**
     * REJECT PENGAJUAN
     */
    public function reject($id)
    {
        $pengajuan = PengajuanSertifikat::with('lisensiLama')->findOrFail($id);

        // Jika ini pengajuan baru yang ditolak, tandai lisensi lama (jika ada relasi aneh) sebagai rejected
        if ($pengajuan->jenis_pengajuan == 'baru' && $pengajuan->lisensiLama) {
            $pengajuan->lisensiLama->update(['status' => 'rejected']);
        }

        $pengajuan->update(['status' => 'rejected']);

        return back()->with('swal', ['icon' => 'success', 'title' => 'Ditolak', 'text' => 'Pengajuan telah ditolak.']);
    }

    /**
     * 2. UPDATE TANGGAL MASA BERLAKU (FITUR BARU)
     */
    public function updateDates(Request $request, $id)
    {
        $request->validate([
            'tgl_mulai_berlaku' => 'required|date',
            'tgl_akhir_berlaku' => 'required|date|after_or_equal:tgl_mulai_berlaku',
        ]);

        $pengajuan = PengajuanSertifikat::findOrFail($id);
        $pengajuan->update([
            'tgl_mulai_berlaku' => $request->tgl_mulai_berlaku,
            'tgl_akhir_berlaku' => $request->tgl_akhir_berlaku
        ]);

        // Jika status sudah completed, update data di Master Lisensi juga secara real-time
        if ($pengajuan->status == 'completed') {
            $this->finalizeLisensi($pengajuan);
        }

        return back()->with('swal', ['icon' => 'success', 'title' => 'Tersimpan', 'text' => 'Tanggal masa berlaku berhasil diperbarui.']);
    }

    /**
     * 3. APPROVE NILAI UJIAN (LOGIKA BARU)
     */
    public function approveExamScore($id)
    {
        $pengajuan = PengajuanSertifikat::findOrFail($id);
        $examResult = $pengajuan->user->examResult;

        if ($pengajuan->metode == 'interview_only') {
            return back()->with('swal', ['icon' => 'warning', 'title' => 'Info', 'text' => 'Metode Wawancara Saja tidak butuh validasi nilai.']);
        }

        if (!$examResult) {
            return back()->with('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Peserta belum mengerjakan ujian.']);
        }

        // Cek Nilai (Opsional: Batas KKM)
        if ($examResult->total_nilai < 75) {
            return back()->with('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Nilai peserta dibawah KKM (<75).']);
        }

        // Luluskan
        $examResult->update(['lulus' => 1]);

        // Pindah Status ke Siap Wawancara
        $pengajuan->update([
            'status' => 'exam_passed',
            'keterangan' => 'Lulus Ujian (Skor: ' . $examResult->total_nilai . '). Menunggu Wawancara.'
        ]);

        return back()->with('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Nilai disetujui. Status lanjut ke Uji Skill.']);
    }

    /**
     * 4. COMPLETE PROCESS (FINALIZE)
     * Menerbitkan Lisensi Baru / Memperpanjang Lisensi Lama
     */
    public function completeProcess($id)
    {
        $pengajuan = PengajuanSertifikat::findOrFail($id);

        // 1. Ubah Status Pengajuan
        $pengajuan->update(['status' => 'completed']);

        // 2. Buat atau Update Lisensi di Tabel Master
        $this->finalizeLisensi($pengajuan);

        return back()->with('swal', ['icon' => 'success', 'title' => 'Selesai', 'text' => 'Proses selesai. Lisensi berhasil diterbitkan/diperbarui.']);
    }

    /**
     * PRIVATE HELPER: CREATE OR UPDATE LISENSI
     */
   /**
     * PRIVATE HELPER: CREATE OR UPDATE LISENSI
     */
  /**
     * PRIVATE HELPER: CREATE OR UPDATE LISENSI
     */
   /**
     * PRIVATE HELPER: CREATE OR UPDATE LISENSI
     * Fix: Mengambil data KFK dari Pengajuan, bukan Hardcode.
     */
    private function finalizeLisensi($pengajuan)
    {
        $tglMulai = $pengajuan->tgl_mulai_berlaku ?? now();
        $tglAkhir = $pengajuan->tgl_akhir_berlaku ?? now()->addYears(1);

        // 1. AMBIL KFK DARI DATA PENGAJUAN (YANG SUDAH BENAR DI DB)
        // Pastikan variabel ini menampung data kfk baru dari tabel pengajuan
        $kfkBaru = $pengajuan->kfk;

        // Validasi: Jika ternyata masih kosong, baru pakai default (Jaga-jaga)
        if (empty($kfkBaru)) {
            $kfkBaru = ['KFK Belum Diisi'];
        }

        // KASUS 1: PERPANJANGAN (Update data lisensi lama)
        if ($pengajuan->lisensiLama) {
            $pengajuan->lisensiLama->update([
                'status'      => 'active',
                'tgl_terbit'  => $tglMulai,
                'tgl_expired' => $tglAkhir,
                'metode'      => $pengajuan->metode,
                
                // [PENTING] Update KFK Lisensi dengan yang baru
                'kfk'         => $kfkBaru 
            ]);
        }
        
        // KASUS 2: PENGAJUAN BARU (Buat data lisensi baru)
        else {
            // Generate Nomor Surat Otomatis
            $bulanRomawi = $this->getRomawi(date('n'));
            $tahun       = date('Y');
            $count       = PerawatLisensi::count() + 1;
            $nomorBaru = $this->nomorBelumDipakai($count, fn($n) => "800.2.4.2.1 / " . sprintf("%03d", $n) . " / 418.101 / SERKOM / " . $tahun);

            $newLisensi = PerawatLisensi::create([
                'user_id'              => $pengajuan->user_id,
                'nama'                 => 'Sertifikat Kompetensi',
                'nomor'                => $nomorBaru,
                'lembaga'              => 'Komite Keperawatan',
                'bidang'               => 'Keperawatan',
                
                // [PENTING] Masukkan data KFK yang benar ke tabel master
                'kfk'                  => $kfkBaru, 
                
                'metode'               => $pengajuan->metode,
                'status'               => 'active',
                'tgl_terbit'           => $tglMulai,
                'tgl_expired'          => $tglAkhir,
                'tgl_mulai'            => $tglMulai,
                'tgl_diselenggarakan'  => now(),
                'unit_kerja_saat_buat' => $pengajuan->user->unit_kerja ?? 'RSUD Simpang Lima Gumul'
            ]);

            // Link balik pengajuan ke lisensi baru ini
            $pengajuan->update(['lisensi_lama_id' => $newLisensi->id]);
        }
    }
    /**
     * Nomor urut berbasis jumlah data bisa bentrok bila ada lisensi yang pernah dihapus.
     * Mulai dari $mulai lalu naik sampai nomor hasil $format belum dipakai lisensi lain.
     */
    private function nomorBelumDipakai(int $mulai, callable $format): string
    {
        $n = $mulai;
        while (PerawatLisensi::where('nomor', $format($n))->exists()) {
            $n++;
        }
        return $format($n);
    }

    private function getRomawi($bulan)
    {
        $map = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
        return $map[$bulan] ?? 'I';
    }

    /**
     * DELETE PENGAJUAN & FILE FISIK
     */
    public function destroy($id)
    {
        $pengajuan = PengajuanSertifikat::findOrFail($id);

        // Hapus File Fisik
        if ($pengajuan->file_dokumen_baru) Storage::disk('public')->delete($pengajuan->file_dokumen_baru);
        if ($pengajuan->file_sertifikat_lama) Storage::disk('public')->delete($pengajuan->file_sertifikat_lama);
        if ($pengajuan->file_surat_rekomendasi) Storage::disk('public')->delete($pengajuan->file_surat_rekomendasi);

        $pengajuan->delete();

        return back()->with('swal', ['icon' => 'success', 'title' => 'Terhapus', 'text' => 'Data pengajuan berhasil dihapus permanen.']);
    }

    // ========================================================================
    // BULK ACTIONS (FITUR DARI KODE LAMA)
    // ========================================================================

    // public function bulkApprove(Request $request)
    // {
    //     $request->validate(['ids' => 'required|array']);

    //     $ids = $request->ids;
    //     $pengajuans = PengajuanSertifikat::whereIn('id', $ids)->where('status', 'pending')->get();
    //     $count = 0;

    //     foreach ($pengajuans as $pengajuan) {
    //         // Default jika metode null
    //         if (!$pengajuan->metode) $pengajuan->metode = 'pg_interview';

    //         if ($pengajuan->metode == 'interview_only') {
    //             $pengajuan->update(['status' => 'exam_passed']);
    //         } else {
    //             $pengajuan->update(['status' => 'method_selected']);
    //         }
    //         $count++;
    //     }

    //     return back()->with('success', "Berhasil menyetujui $count pengajuan terpilih.");
    // }

    public function bulkApproveScore(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        $pengajuans = PengajuanSertifikat::with('user.examResult')
            ->whereIn('id', $request->ids)
            ->where('status', 'method_selected') // Hanya yg sedang ujian
            ->get();

        $count = 0;

        foreach ($pengajuans as $pengajuan) {
            if ($pengajuan->user && $pengajuan->user->examResult) {
                // Luluskan
                $pengajuan->user->examResult->update(['lulus' => 1]);
                // Update Status
                $pengajuan->update(['status' => 'exam_passed']);
                $count++;
            }
        }

        return back()->with('success', "Berhasil memproses nilai untuk $count peserta.");
    }

public function bulkApproveInterview(Request $request)
    {
        $request->validate([
            'ids'                 => 'required|array',
            'penanggung_jawab_id' => 'required|exists:penanggung_jawab_ujians,id',
            'tgl_wawancara'       => 'required|date',
            'jam_wawancara'       => 'required',
            'lokasi'              => 'required|string',
            'deskripsi_skill'     => 'nullable|string',
        ]);

        $ids = $request->ids;
        $waktu = $request->tgl_wawancara . ' ' . $request->jam_wawancara;
        $count = 0;

        foreach ($ids as $id) {
            $pengajuan = PengajuanSertifikat::with('jadwalWawancara')->find($id);

            // Pastikan pengajuan ada, status benar, dan jadwal sudah di-create oleh user (status pending)
            if ($pengajuan && $pengajuan->status == 'interview_scheduled' && $pengajuan->jadwalWawancara) {
                
                $pengajuan->jadwalWawancara->update([
                    'penanggung_jawab_id' => $request->penanggung_jawab_id,
                    'waktu_wawancara'     => $waktu,
                    'lokasi'              => $request->lokasi,
                    'deskripsi_skill'     => $request->deskripsi_skill,
                    'status'              => 'approved'
                ]);
                
                $count++;
            }
        }

        return back()->with('success', "$count jadwal berhasil diatur & disetujui.");
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        // Loop untuk hapus file fisik dulu (best practice)
        $pengajuans = PengajuanSertifikat::whereIn('id', $request->ids)->get();
        foreach ($pengajuans as $p) {
            if ($p->file_dokumen_baru) Storage::disk('public')->delete($p->file_dokumen_baru);
            if ($p->file_sertifikat_lama) Storage::disk('public')->delete($p->file_sertifikat_lama);
        }

        $count = PengajuanSertifikat::whereIn('id', $request->ids)->delete();

        return back()->with('success', "Berhasil menghapus $count data terpilih.");
    }

    // ========================================================================
    // EXPORT EXCEL (FITUR DARI KODE LAMA)
    // ========================================================================
public function exportJadwal()
{
    $data = JadwalWawancara::with(['pengajuan.user', 'pewawancara'])
        ->whereIn('status', ['approved', 'completed'])
        ->get();

    $filename = 'Jadwal_' . date('Ymd_His') . '.csv';
    
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
    
    fputcsv($output, ['No', 'Nama', 'Unit', 'Pewawancara', 'Tanggal', 'Jam', 'Lokasi', 'Status']);
    
    foreach ($data as $i => $row) {
        $waktu = \Carbon\Carbon::parse($row->waktu_wawancara);
        fputcsv($output, [
            $i + 1,
            $row->pengajuan->user->name ?? '-',
            $row->pengajuan->lisensiLama->unit_kerja_saat_buat ?? '-',
            $row->pewawancara->nama ?? '-',
            $waktu->format('d/m/Y'),
            $waktu->format('H:i'),
            $row->lokasi ?? '-',
            ucfirst($row->status)
        ]);
    }
    
    fclose($output);
    exit; // Wajib
}
}
