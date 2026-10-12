<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerawatLisensi;
use App\Models\User;
use App\Models\PengajuanSertifikat; // WAJIB DITAMBAHKAN
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Services\Penandatangan;

class AdminLisensiController extends Controller
{
    public function lisensiCreate(Request $request, $metode = null)
    {
        $users = User::where('role', 'perawat')
            ->leftJoin('perawat_pekerjaans', 'users.id', '=', 'perawat_pekerjaans.user_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'perawat_pekerjaans.unit_kerja'
            )
            ->orderBy('users.name', 'asc')
            ->get();

        if ($metode === 'interview_only') {
            return view('admin.lisensi_interview_only.create', compact('users'));
        } else {
            return view('admin.lisensi_pg_interview.create', compact('users'));
        }
    }
    /**
     * ======================================================================
     * FUNGSI CETAK BUNDLE RKK (SPK + SPKK + RKK) KHUSUS ADMIN
     * ======================================================================
     */
    public function cetakRkkBundle($id)
    {
        // 1. Ambil data pengajuan (Bebas tanpa filter Auth::id() karena ini Admin)
        $pengajuan = PengajuanSertifikat::with([
            'user.perawatProfile', 
            'user.pendidikanTerakhir', 
            'lisensiLama', 
            'jadwalWawancara.penilaian'
        ])->findOrFail($id);

        $lisensi = $pengajuan->lisensiLama;
        if (!$lisensi) {
            return back()->with('swal', ['icon' => 'error', 'title' => 'Data Kosong', 'text' => 'Data Lisensi belum tersedia.']);
        }

        $user = $pengajuan->user;
        $profile = $user->perawatProfile;
        $pekerjaan = \App\Models\PerawatPekerjaan::where('user_id', $user->id)->latest()->first();
        $pendidikan = $user->pendidikanTerakhir;
        
        $namaLengkap = $profile->nama_lengkap ?? $user->name;

        \Carbon\Carbon::setLocale('id');
        $tglMulai = $lisensi->tgl_mulai ?? ($lisensi->tgl_terbit ?? now());
        $carbonDate = \Carbon\Carbon::parse($tglMulai);
        $tglMulaiIndo = $carbonDate->translatedFormat('d F Y');
        
        // Logika Expired
        if (!empty($lisensi->tgl_expired)) {
            $carbonSelesai = \Carbon\Carbon::parse($lisensi->tgl_expired);
        } else {
            if (!empty($profile->tanggal_lahir)) {
                $tglLahir = \Carbon\Carbon::parse($profile->tanggal_lahir);
                $tahunExpired = $carbonDate->copy()->addYears(3)->year; 
                $carbonSelesai = \Carbon\Carbon::create($tahunExpired, $tglLahir->month, $tglLahir->day);
            } else {
                $carbonSelesai = $carbonDate->copy()->addYears(3);
            }
        }
        $tglSelesaiIndo = $carbonSelesai->translatedFormat('d F Y');
        $tglTerbitIndo = \Carbon\Carbon::parse($lisensi->tgl_terbit ?? now())->translatedFormat('d F Y');

        // FIX NOMOR
        $idMentah = $lisensi->id;
        $nomorUrutMurni = str_pad($idMentah, 3, '0', STR_PAD_LEFT);
        $tahunSurat = \Carbon\Carbon::parse($lisensi->tgl_terbit)->format('Y');
        $jenisSurat = ($lisensi->metode === 'interview_only') ? 'KRED' : 'SERKOM';
        $lisensi->nomor = "800.2.4.2.1/{$nomorUrutMurni}/418.101/{$jenisSurat}/{$tahunSurat}";

        // Persiapan Data MPP & PPI dari Hasil Wawancara
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
        if (is_string($kfkArray)) {
            $kfkArray = json_decode($kfkArray, true) ?? [];
        }

        $namaJenjang = '-'; $namaKfk = '-';
        foreach ((array) $kfkArray as $item) {
            if (str_contains($item, 'PK') || str_contains($item, 'BK')) {
                $namaJenjang = $item;
            } else {
                $namaKfk = $item;
            }
        }

        $namaKfkBersih = trim($namaKfk);
        $mapJenjang = ['PK 1'=>'PK I','PK 2'=>'PK II','PK 3'=>'PK III','PK 4'=>'PK IV','PK 5'=>'PK V','BK 1'=>'BK I','BK 2'=>'BK II','BK 3'=>'BK III','BK 4'=>'BK IV','BK 5'=>'BK V'];
        $jenjangQuery = $mapJenjang[$namaJenjang] ?? $namaJenjang;

        $rkkItems = \App\Models\BankItemRkk::where('kfk', 'like', "%{$namaKfkBersih}%")
            ->whereIn('jenjang_klinis', array_unique([$namaJenjang, $jenjangQuery]))
            ->orderBy('kategori_tugas', 'asc')
            ->orderBy('no_item', 'asc')
            ->get();
            
        $rkkGrouped = $rkkItems->groupBy('kategori_tugas');
        $bidang = str_contains($namaJenjang, 'PK') ? 'KEPERAWATAN' : (str_contains($namaJenjang, 'BK') ? 'KEBIDANAN' : 'UMUM');

        // URL QR Code
        $verifyUrlRkk = route('sertifikat.verify', ['id' => $lisensi->id]) . '?doc=rkk';
        $verifyUrlSpk = route('sertifikat.verify', ['id' => $lisensi->id]) . '?doc=spk';

        $qrBambang = Penandatangan::qr('ketua_komite', $verifyUrlRkk, ($lisensi->tgl_terbit ?? null));
        $qrHaris = Penandatangan::qr('ketua_subkomite', $verifyUrlRkk, ($lisensi->tgl_terbit ?? null));
        $qrDirekturRkk = Penandatangan::qr('direktur', $verifyUrlRkk, ($lisensi->tgl_terbit ?? null));
        $qrDirekturSpk = Penandatangan::qr('direktur', $verifyUrlSpk, ($lisensi->tgl_terbit ?? null));

        // DATA UNTUK RKK (Halaman 3)
        $dataPDF = [
            'tglMulai' => $tglMulaiIndo, 'tglAkhir' => $tglSelesaiIndo, 'tglTerbit' => $tglTerbitIndo,
            'bidang' => $bidang, 'namaLengkap' => strtoupper($namaLengkap),
            'unitKerja' => strtoupper($pekerjaan->unit_kerja ?? 'RSUD SLG'),
            'nip' => $profile->nip ?? ($profile->nirp ?? '-'),
            'pendidikanFormal' => strtoupper($pendidikan ? ($pendidikan->jenjang . ' ' . $pendidikan->jurusan) : '-'),
            'kualifikasi' => strtoupper($jenjangQuery . ' ' . $namaKfk),
            'namaJenjang' => $jenjangQuery, 'rkkGrouped' => $rkkGrouped, 'rkkDraft' => $rkkDraft,
            'isMpp' => $isMpp, 'isPpi' => $isPpi, 'rkkPpi' => $rkkPpi, 'rkkMpp' => $rkkMpp,
            'qrBambang' => $qrBambang, 'qrHaris' => $qrHaris, 'qrDirektur' => $qrDirekturRkk,
        ];

        // DATA UNTUK SPK & SPKK (Halaman 1 & 2)
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
            'direktur_nama'         => Penandatangan::untuk('direktur', ($lisensi->tgl_terbit ?? null))->nama,
            'direktur_nip'          => Penandatangan::untuk('direktur', ($lisensi->tgl_terbit ?? null))->nip,
            'direktur_pangkat'      => Penandatangan::untuk('direktur', ($lisensi->tgl_terbit ?? null))->pangkat
        ];

        // RENDER KETIGA DOKUMEN MENJADI 1 PDF BUNDLE
        $htmlSpk  = view('perawat.dokumen.pdf_spk', $dataSpkSpkk)->render();
        $htmlSpkk = view('perawat.dokumen.pdf_spkk', $dataSpkSpkk)->render();
        $htmlRkk  = view('perawat.pengajuan.kredensial_pdf', $dataPDF)->render();

        $finalHtml = $htmlSpk . '<div style="page-break-after: always;"></div>' . $htmlSpkk . '<div style="page-break-after: always;"></div>' . $htmlRkk;

        $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $namaLengkap);
        
        // Mode stream agar admin bisa melihat preview di tab baru
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions(['isRemoteEnabled' => true])->loadHTML($finalHtml)->setPaper('a4', 'portrait');
        return $pdf->stream('RKK_Bundle_' . $cleanName . '.pdf');
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

    public function lisensiStore(Request $request, $metode = null)
    {
        $request->validate([
            'user_ids'            => 'required|array',
            'user_ids.*'          => 'exists:users,id',
            'nama'                => 'required|string|max:100',
            'lembaga'             => 'required|string|max:100',
            'bidang'              => 'nullable|string|max:100',
            'kfk'   => 'required|array',
            'kfk.*' => 'required|string|max:100',
            'tgl_mulai'           => 'required|date',
            'tgl_diselenggarakan' => 'required|date',
            'tgl_terbit'          => 'required|date',
            'tgl_expired'         => 'required|date',
        ]);

        $commonData = $request->except(['_token', 'user_ids']);
        $commonData['kfk'] = json_encode($request->kfk);
        $commonData['metode'] = $metode ?? 'pg_interview';
        $commonData['status'] = 'active';

        $lastId = PerawatLisensi::max('id') ?? 0;
        $count = 0;

        foreach ($request->user_ids as $index => $userId) {
            $urutan = $lastId + 1 + $count;
            $nomorOtomatis = strtoupper($request->nama) . '-' . date('Y') . '-' . sprintf('%04d', $urutan);

            $data = array_merge($commonData, [
                'user_id' => $userId,
                'nomor'   => $nomorOtomatis
            ]);

            // 1. Simpan di Master Lisensi
            $lisensi = PerawatLisensi::create($data);

            // 2. [SINKRONISASI PENTING] Buat data Pengajuan agar PDF bisa dicetak
            PengajuanSertifikat::create([
                'user_id' => $userId,
                'lisensi_lama_id' => $lisensi->id,
                'status' => 'completed',
                'jenis_pengajuan' => 'baru',
                'metode' => $lisensi->metode,
                'kfk' => $lisensi->kfk,
                'tgl_wawancara' => $lisensi->tgl_diselenggarakan,
                'tgl_mulai_berlaku' => $lisensi->tgl_terbit,
                'tgl_akhir_berlaku' => $lisensi->tgl_expired,
            ]);

            $count++;
        }

        $route = $metode === 'interview_only' ? 'admin.lisensi_interview_only.index' : 'admin.lisensi_pg_interview.index';

        return redirect()->route($route)->with('swal', [
            'icon'  => 'success',
            'title' => 'Berhasil',
            'text'  => "Lisensi & Data Cetak berhasil dibuat untuk $count perawat."
        ]);
    }

 public function lisensiIndex(Request $request, $metode = null)
    {
        // BACA LANGSUNG DARI PENGAJUAN AGAR SINKRON 100% DENGAN MENU APPROVAL
        $query = \App\Models\PengajuanSertifikat::with(['user', 'lisensiLama'])
            ->where('status', 'completed');

        if ($metode) {
            $query->where('metode', $metode);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kfk', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('lisensiLama', function ($l) use ($search) {
                      $l->where('nomor', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $today = \Carbon\Carbon::now()->format('Y-m-d');
            if ($request->status == 'aktif') {
                $query->where('tgl_akhir_berlaku', '>=', $today);
            } elseif ($request->status == 'expired') {
                $query->where('tgl_akhir_berlaku', '<', $today);
            }
        }

        $data = $query->latest()->paginate(10);

        if ($metode === 'interview_only') {
            return view('admin.lisensi_interview_only.index', compact('data'));
        } else {
            return view('admin.lisensi_pg_interview.index', compact('data'));
        }
    }

    public function lisensiEdit($id, $metode = null)
    {
        session()->put('url.intended', url()->previous());

        $data = PerawatLisensi::findOrFail($id);
        $users = User::where('role', 'perawat')
            ->leftJoin('perawat_pekerjaans', 'users.id', '=', 'perawat_pekerjaans.user_id')
            ->select(
                'users.id',
                'users.name',
                'perawat_pekerjaans.unit_kerja'
            )
            ->orderBy('users.name', 'asc')
            ->get();

        if ($metode === 'interview_only') {
            return view('admin.lisensi_interview_only.edit', compact('data', 'users'));
        } else {
            return view('admin.lisensi_pg_interview.edit', compact('data', 'users'));
        }
    }

    public function lisensiUpdate(Request $request, $id, $metode = null)
    {
        $lisensi = PerawatLisensi::findOrFail($id);

        $request->validate([
            'user_id'             => 'required|exists:users,id',
            'nama'                => 'required|string|max:100',
            'lembaga'             => 'required|string|max:100',
            'nomor'               => 'required|string|max:100',
            'tgl_terbit'          => 'required|date',
            'tgl_expired'         => 'required|date',
            'dokumen'             => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'bidang'              => 'nullable|string|max:100',
            'kfk'                 => 'required|array',
            'kfk.*'               => 'required|string|max:100',
            'tgl_mulai'           => 'required|date',
            'tgl_diselenggarakan' => 'required|date',
        ]);

        $data = $request->except(['dokumen', '_token', '_method']);
        $data['user_id'] = $request->user_id;
        $data['kfk'] = json_encode($request->kfk);
        $data['metode'] = $metode ?? 'pg_interview';

        if ($request->hasFile('dokumen')) {
            if ($lisensi->file_path && Storage::disk('public')->exists($lisensi->file_path)) {
                Storage::disk('public')->delete($lisensi->file_path);
            }
            $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/lisensi', 'public');
        }

        // 1. Update Lisensi Master
        $lisensi->update($data);

        // 2. [SINKRONISASI PENTING] Update tabel Pengajuan agar PDF tidak expired/salah tanggal
        $pengajuan = PengajuanSertifikat::where('lisensi_lama_id', $lisensi->id)->first();
        if ($pengajuan) {
            $pengajuan->update([
                'kfk' => $lisensi->kfk,
                'tgl_wawancara' => $lisensi->tgl_diselenggarakan,
                'tgl_mulai_berlaku' => $lisensi->tgl_terbit,
                'tgl_akhir_berlaku' => $lisensi->tgl_expired,
            ]);
        }

        $route = $metode === 'interview_only' ? 'admin.lisensi_interview_only.index' : 'admin.lisensi_pg_interview.index';
        $redirectUrl = session()->pull('url.intended', route($route));

        return redirect($redirectUrl)->with('swal', [
            'icon' => 'success', 
            'title' => 'Berhasil', 
            'text' => 'Lisensi & Data Cetak PDF diperbarui.'
        ]);
    }

    public function lisensiDestroy($id, $metode = null)
    {
        $data = PerawatLisensi::findOrFail($id);

        if ($data->file_path && Storage::disk('public')->exists($data->file_path)) {
            Storage::disk('public')->delete($data->file_path);
        }

        $data->delete(); // Pengajuan yang terkait otomatis terhapus karena foreign key ON DELETE CASCADE

        return back()->with('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Lisensi dihapus.']);
    }

    public function lisensiBulkDestroy(Request $request, $metode = null)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:perawat_lisensis,id'
        ]);

        $lisensis = PerawatLisensi::whereIn('id', $request->ids)->get();

        foreach ($lisensis as $data) {
            if ($data->file_path && Storage::disk('public')->exists($data->file_path)) {
                Storage::disk('public')->delete($data->file_path);
            }
            $data->delete();
        }

        return back()->with('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => count($request->ids) . ' data lisensi berhasil dihapus.']);
    }
}