<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanOppe;
use App\Models\BankItemRkk;
use App\Models\PerawatProfile;
use App\Models\PerawatPekerjaan;
use App\Models\User;

class OppeController extends Controller
{
    private function getMppItems()
    {
        return [
            ['no_item' => 'MPP-01', 'deskripsi_kompetensi' => 'Melakukan skrining kebutuhan Case Management', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-02', 'deskripsi_kompetensi' => 'Menentukan pasien yang memerlukan Case Management', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-03', 'deskripsi_kompetensi' => 'Melakukan asesmen Case Management komprehensif', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-04', 'deskripsi_kompetensi' => 'Menyusun Individual Care Plan bersama PPA', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-05', 'deskripsi_kompetensi' => 'Mengkoordinasikan DPJP, Perawat, Apoteker, Ahli Gizi dan profesi lain', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-06', 'deskripsi_kompetensi' => 'Mengkoordinasikan discharge planning', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-07', 'deskripsi_kompetensi' => 'Melakukan monitoring implementasi rencana pelayanan', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-08', 'deskripsi_kompetensi' => 'Mengidentifikasi hambatan pelayanan pasien', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-09', 'deskripsi_kompetensi' => 'Melakukan koordinasi perpindahan pasien antar unit', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-10', 'deskripsi_kompetensi' => 'Melakukan koordinasi rujukan', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-11', 'deskripsi_kompetensi' => 'Mengkoordinasikan pelayanan BPJS dan administrasi pelayanan', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-12', 'deskripsi_kompetensi' => 'Melakukan edukasi kepada pasien dan keluarga', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-13', 'deskripsi_kompetensi' => 'Mengkoordinasikan penyelesaian masalah pelayanan pasien', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-14', 'deskripsi_kompetensi' => 'Melakukan dokumentasi Case Management', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-15', 'deskripsi_kompetensi' => 'Melakukan evaluasi outcome pelayanan pasien', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-16', 'deskripsi_kompetensi' => 'Mengikuti clinical conference', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-17', 'deskripsi_kompetensi' => 'Mengikuti discharge conference', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-18', 'deskripsi_kompetensi' => 'Melakukan koordinasi dengan MPP eksternal', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-19', 'deskripsi_kompetensi' => 'Melakukan monitoring LOS (Length of Stay)', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-20', 'deskripsi_kompetensi' => 'Mengidentifikasi potensi readmission', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-21', 'deskripsi_kompetensi' => 'Melakukan koordinasi pelayanan paliatif / kronis', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-22', 'deskripsi_kompetensi' => 'Melakukan koordinasi pelayanan pasien kompleks / resti', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-23', 'deskripsi_kompetensi' => 'Melakukan koordinasi kesinambungan pelayanan (Continuity of Care)', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-24', 'deskripsi_kompetensi' => 'Melakukan audit dokumentasi Case Management', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-25', 'deskripsi_kompetensi' => 'Menyusun laporan indikator mutu MPP', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-26', 'deskripsi_kompetensi' => 'Mengkoordinasikan penempatan tempat tidur (Bed Management)', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-27', 'deskripsi_kompetensi' => 'Melakukan koordinasi pelayanan pasien prioritas', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-28', 'deskripsi_kompetensi' => 'Melakukan koordinasi pelayanan multidisiplin', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-29', 'deskripsi_kompetensi' => 'Monitoring Clinical Pathway', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
            ['no_item' => 'MPP-30', 'deskripsi_kompetensi' => 'Mengkoordinasikan rujukan internal dan eksternal', 'kategori_tugas' => 'MANAJER PELAYANAN PASIEN (MPP)'],
        ];
    }

    private function getPpiItems()
    {
        return [
            ['no_item' => 'PPI-01', 'deskripsi_kompetensi' => 'Menerapkan prinsip kerjasama interdisiplin terkait infeksi (dokter, perawat, laboratorium, PPRA, Gizi, CSSD, Laundry, sanitasi, house keeping, IPSRS, farmasi, security)', 'kategori_tugas' => 'KOMUNIKASI INTERPERSONAL'],
            ['no_item' => 'PPI-02', 'deskripsi_kompetensi' => 'Memberikan konsultasi tentang pencegahan dan pengendalian infeksi yang diperlukan pada kasus yang terjadi di RS', 'kategori_tugas' => 'KOMUNIKASI INTERPERSONAL'],
            ['no_item' => 'PPI-03', 'deskripsi_kompetensi' => 'Mengajukan prosedur isolasi sesuai transmisi', 'kategori_tugas' => 'KOMUNIKASI INTERPERSONAL'],
            ['no_item' => 'PPI-04', 'deskripsi_kompetensi' => 'Memberikan motivasi dan teguran tentang pelaksanaan program PPI', 'kategori_tugas' => 'KOMUNIKASI INTERPERSONAL'],
            ['no_item' => 'PPI-05', 'deskripsi_kompetensi' => 'Menunjukkan sikap asertif', 'kategori_tugas' => 'KOMUNIKASI INTERPERSONAL'],
            ['no_item' => 'PPI-06', 'deskripsi_kompetensi' => 'Menunjukkan sikap empati', 'kategori_tugas' => 'KOMUNIKASI INTERPERSONAL'],
            ['no_item' => 'PPI-07', 'deskripsi_kompetensi' => 'Melakukan surveilans infeksi Phlebitis, CAUTI (Catheter Associated Urinary Tract Infection), VAP (Ventilator Associated Pneumonia), IADP (Infeksi Aliran Darah Primer), IDO (Infeksi Daerah Operasi), Emerging dan Re-emerging Disease', 'kategori_tugas' => 'PATIENT SAFETY'],
            ['no_item' => 'PPI-08', 'deskripsi_kompetensi' => 'Melakukan audit penerapan bundles pencegahan HAIs (Phlebitis, ISK, VAP, IADP, dan IDO)', 'kategori_tugas' => 'PATIENT SAFETY'],
            ['no_item' => 'PPI-09', 'deskripsi_kompetensi' => 'Memonitoring dan evaluasi peralatan medis single use dan di re-use', 'kategori_tugas' => 'PATIENT SAFETY'],
            ['no_item' => 'PPI-10', 'deskripsi_kompetensi' => 'Melakukan asuhan keperawatan pada pasien yang beresiko infeksi (terpasang alat infus, kateter urin, kateter vena sentral dan ventilator serta pasien post operasi)', 'kategori_tugas' => 'PRINSIP PENGENDALIAN DAN PENCEGAHAN INFEKSI'],
            ['no_item' => 'PPI-11', 'deskripsi_kompetensi' => 'Memonitor kepatuhan terhadap penerapan SPO Kewaspadaan Standar : SPO kebersihan tangan, SPO penggunaan alat pelindung diri, SPO pemilahan sampah, SPO pemilahan linen, SPO pengelolaan peralatan perawatan pasien, SPO praktek menyuntik yang aman, SPO penempatan pasien, SPO etika batuk, SPO pengendalian lingkungan, SPO Lumbal Punksi', 'kategori_tugas' => 'PRINSIP PENGENDALIAN DAN PENCEGAHAN INFEKSI'],
            ['no_item' => 'PPI-12', 'deskripsi_kompetensi' => 'Melakukan audit pencegahan dan pengendalian infeksi di unit terkait : Audit kewaspadaan standar (kebersihan tangan, APD, limbah, dll), Audit PPI di Gizi, Audit PPI di laundry, Audit PPI di CSSD, Audit PPI di kamar jenazah, Audit PPI di ruang hemodialisa, Audit PPI di kamar operasi, Audit PPI di kamar isolasi', 'kategori_tugas' => 'PRINSIP PENGENDALIAN DAN PENCEGAHAN INFEKSI'],
            ['no_item' => 'PPI-13', 'deskripsi_kompetensi' => 'Melakukan monitoring terhadap penggunaan antibiotik yang rasional', 'kategori_tugas' => 'PRINSIP PENGENDALIAN DAN PENCEGAHAN INFEKSI'],
            ['no_item' => 'PPI-14', 'deskripsi_kompetensi' => 'Melakukan riset keperawatan terkait pencegahan dan pengendalian infeksi', 'kategori_tugas' => 'PRINSIP PENGENDALIAN DAN PENCEGAHAN INFEKSI'],
            ['no_item' => 'PPI-15', 'deskripsi_kompetensi' => 'Memberikan pelatihan dasar PPI pada petugas kesehatan (perawat dan dokter)', 'kategori_tugas' => 'PRINSIP PENGENDALIAN DAN PENCEGAHAN INFEKSI'],
            ['no_item' => 'PPI-16', 'deskripsi_kompetensi' => 'Memberikan edukasi tentang PPI pada pasien, keluarga dan pengunjung (kebersihan tangan, etika batuk, dan pemilahan sampah di rumah sakit)', 'kategori_tugas' => 'PRINSIP PENGENDALIAN DAN PENCEGAHAN INFEKSI'],
            ['no_item' => 'PPI-17', 'deskripsi_kompetensi' => 'Memberikan edukasi tentang PPI pada Security (kebersihan tangan dan penggunaan APD), Pramusaji (PPI di pelayanan makanan), House Keeping (kebersihan tangan, penggunaan APD dan pengendalian lingkungan / pembersihan RS)', 'kategori_tugas' => 'PRINSIP PENGENDALIAN DAN PENCEGAHAN INFEKSI'],
            ['no_item' => 'PPI-18', 'deskripsi_kompetensi' => 'Memberikan pelatihan tentang PPIRS pada petugas kesehatan di area penunjang (laboratorium, radiologi, CSSD, laundry, gizi, kamar jenazah, fisioterapi, dan farmasi)', 'kategori_tugas' => 'PRINSIP PENGENDALIAN DAN PENCEGAHAN INFEKSI'],
        ];
    }

    public function create()
    {
        $user = Auth::user();
        $myProfile = PerawatProfile::where('user_id', $user->id)->first();
        
        $myJob = PerawatPekerjaan::where('user_id', $user->id)->orderBy('tahun_mulai', 'desc')->first();
        $myUnit = $myJob->unit_kerja ?? '';

        if (empty($myUnit)) {
            return redirect()->route('dashboard')->with('swal', ['icon' => 'warning', 'title' => 'Unit Kerja Kosong', 'text' => 'Silakan lengkapi riwayat pekerjaan Anda terlebih dahulu.']);
        }

        $userIdsInUnit = PerawatPekerjaan::where('unit_kerja', $myUnit)->pluck('user_id')->toArray();

        $peers = PerawatProfile::whereIn('user_id', $userIdsInUnit)
            ->where('user_id', '!=', $user->id)
            ->where('jabatan_struktural', 'pelaksana')
            ->get();

        if ($myProfile && $myProfile->jabatan_struktural === 'kepala_ruangan') {
            $evaluators = PerawatProfile::where('jabatan_struktural', 'kepala_seksi')->get();
            $evaluatorLabel = 'Kepala Seksi (Kasi)';
        } else {
            $evaluators = PerawatProfile::whereIn('user_id', $userIdsInUnit)
                ->where('jabatan_struktural', 'kepala_ruangan')
                ->get();
            $evaluatorLabel = 'Kepala Ruangan (Karu)';
        }

        $tahunIni = date('Y');
        $sedangMengajukan = PengajuanOppe::where('user_id', $user->id)
            ->where('periode', $tahunIni)
            ->whereNotIn('status', ['completed'])
            ->exists();

        return view('perawat.oppe.create', compact('peers', 'evaluators', 'evaluatorLabel', 'myUnit', 'sedangMengajukan', 'tahunIni'));
    }

    private function normalizeJenjang($string, $profesi = '')
    {
        if (!$string) return '-';
        $string = strtoupper(trim($string));
        
        $type = ''; $level = '';

        if (preg_match('/(PK|BK)\s*([1-5]|I{1,3}|IV|V)/', $string, $matches)) {
            $type = $matches[1]; $level = $matches[2];
        } elseif (preg_match('/(PK|BK)/', $string, $matches)) {
            $type = $matches[1];
        }

        $profesiStr = strtolower(trim($profesi));
        if (str_contains($profesiStr, 'bidan')) $type = 'BK';
        elseif (str_contains($profesiStr, 'perawat')) $type = 'PK';

        if ($type && $level) {
            $map = ['1' => 'I', '2' => 'II', '3' => 'III', '4' => 'IV', '5' => 'V'];
            $level = $map[$level] ?? $level;
            return $type . ' ' . $level;
        } elseif ($type) {
            return $type; 
        }

        return '-';
    }

    public function store(Request $request)
    {
        $request->validate([
            'kfk' => 'required|array|min:2',
            'peer_id' => 'required|exists:users,id',
            'evaluator_id' => 'required|exists:users,id',
            'periode' => 'required|date',
        ]);

        $user = Auth::user();

        $jenjangDb = '';
        $kfkDb = '';

        foreach ($request->kfk as $item) {
            if (str_contains(strtoupper($item), 'PK') || str_contains(strtoupper($item), 'BK')) {
                $jenjangDb = $item; 
            } else {
                $kfkDb = $item;     
            }
        }

        $jenjangQuery = trim($jenjangDb);
        $kfkQuery = trim($kfkDb);

        $rkkItems = BankItemRkk::where('kfk', 'like', "%{$kfkQuery}%")
            ->where('jenjang_klinis', $jenjangQuery)
            ->orderBy('kategori_tugas', 'asc')
            ->orderBy('no_item', 'asc')
            ->get();

        if ($rkkItems->isEmpty()) {
            return back()->withInput()->with('swal', [
                'icon' => 'error',
                'title' => 'Instrumen Kosong',
                'text' => "Instrumen RKK untuk {$jenjangQuery} - {$kfkQuery} belum tersedia di Bank Soal."
            ]);
        }

        $instrumenSnapshot = [];
        
        foreach ($rkkItems as $item) {
            $instrumenSnapshot[] = [
                'item_id' => $item->id,
                'kategori_tugas' => $item->kategori_tugas,
                'deskripsi' => $item->deskripsi_kompetensi,
                'skor_self' => null, 
                'skor_peer' => null, 
                'skor_karu' => null  
            ];
        }

        DB::beginTransaction();
        try {
            $lisensiAktif = \App\Models\PerawatLisensi::where('user_id', $user->id)
                ->where('status', 'active')
                ->latest('tgl_terbit')
                ->first();

            $oppe = PengajuanOppe::create([
                'user_id' => $user->id,
                'peer_id' => $request->peer_id,
                'evaluator_id' => $request->evaluator_id,
                'lisensi_id' => $lisensiAktif ? $lisensiAktif->id : null,
                'periode' => $request->periode,
                'status' => 'pending_self', 
                'instrumen_penilaian' => $instrumenSnapshot
            ]);

            DB::commit();
            
            return redirect()->route('perawat.oppe.evaluate', $oppe->id)->with('swal', [
                'icon' => 'success',
                'title' => 'OPPE Dibuat!',
                'text' => "Pengajuan tanggal {$request->periode} berhasil dibuat. Silakan lakukan evaluasi Diri Sendiri."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => $e->getMessage()]);
        }
    }
    
    public function cetak($id)
    {
        $oppe = PengajuanOppe::with(['user.profile', 'user.pekerjaans', 'peer.profile', 'evaluator.profile', 'lisensi'])->findOrFail($id);
        
        if (!$this->bolehLihatOppe($oppe, Auth::user())) {
            abort(403, 'Anda tidak berhak mencetak OPPE ini.');
        }

        if ($oppe->status !== 'completed') {
            return back()->with('swal', ['icon' => 'warning', 'title' => 'Belum Selesai', 'text' => 'Dokumen OPPE belum selesai dinilai oleh Atasan.']);
        }

        $instrumenGrouped = collect($oppe->instrumen_penilaian)->groupBy('kategori_tugas');

        $totalSelf = 0;
        $totalPeer = 0;
        $totalKaru = 0;
        $maxSkor = count($oppe->instrumen_penilaian) * 3; 

        foreach ($oppe->instrumen_penilaian as $item) {
            $totalSelf += (int) ($item['skor_self'] ?? 0);
            $totalPeer += (int) ($item['skor_peer'] ?? 0);
            $totalKaru += (int) ($item['skor_karu'] ?? 0);
        }

        $rataRataTotal = ($totalSelf + $totalPeer + $totalKaru) / 3;

        return view('perawat.oppe.cetak', compact(
            'oppe', 'instrumenGrouped', 'totalSelf', 'totalPeer', 'totalKaru', 'maxSkor', 'rataRataTotal'
        ));
    }

    public function listEvaluasi(Request $request)
    {
        $user = Auth::user();
        
        $query = PengajuanOppe::with([
            'user.profile', 
            'peer.profile', 
            'evaluator.profile'
        ]);

        if ($user->role !== 'admin') {
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('peer_id', $user->id)
                  ->orWhere('evaluator_id', $user->id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('periode', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhereHas('profile', function($p) use ($search) {
                            $p->where('nama_lengkap', 'like', "%{$search}%");
                        });
                  });
            });
        }

        $daftarEvaluasi = $query->latest()->paginate(10)->withQueryString();

        return view('perawat.oppe.list_evaluasi', compact('daftarEvaluasi'));
    }

    public function evaluate($id)
    {
        $oppe = PengajuanOppe::with([
            'user.profile', 
            'user.pekerjaans',
            'peer.profile', 
            'evaluator.profile', 
            'lisensi'
        ])->findOrFail($id);
        
        $currentUser = Auth::user();

        $roleEvaluasi = '';
        $canEdit = false;

        if ($currentUser->role === 'admin') {
            $roleEvaluasi = 'viewer'; 
            $canEdit = false;
        } elseif ($oppe->status === 'pending_self' && $oppe->user_id === $currentUser->id) {
            $roleEvaluasi = 'self'; $canEdit = true;
        } elseif ($oppe->status === 'pending_peer' && $oppe->peer_id === $currentUser->id) {
            $roleEvaluasi = 'peer'; $canEdit = true;
        } elseif ($oppe->status === 'pending_karu' && $oppe->evaluator_id === $currentUser->id) {
            $roleEvaluasi = 'karu'; $canEdit = true;
        } elseif ($oppe->status === 'completed' && $this->bolehLihatOppe($oppe, $currentUser)) {
            $roleEvaluasi = 'viewer';
        } else {
            return redirect()->route('dashboard')->with('swal', ['icon' => 'error', 'title' => 'Akses Ditolak', 'text' => 'Bukan wewenang atau giliran Anda.']);
        }

        $lastOppe = PengajuanOppe::where('user_id', $oppe->user_id)
            ->where('status', 'completed')
            ->where('id', '!=', $oppe->id)
            ->orderBy('created_at', 'desc')
            ->first();

        // ---------------------------------------------------------
        // PAKSA BACA JSON AGAR TIDAK ERROR (PENTING)
        // ---------------------------------------------------------
        $instrumenData = $oppe->instrumen_penilaian;
        if (is_string($instrumenData)) {
            $instrumenData = json_decode($instrumenData, true);
        }
        if (!is_array($instrumenData)) {
            $instrumenData = [];
        }

        $instrumenGrouped = collect($instrumenData)->groupBy('kategori_tugas');

        return view('perawat.oppe.evaluate', compact('oppe', 'instrumenGrouped', 'roleEvaluasi', 'canEdit', 'lastOppe'));
    }

    public function destroy($id)
    {
        try {
            $oppe = PengajuanOppe::findOrFail($id);
            $user = Auth::user();

            if ($user->role !== 'admin' && $oppe->user_id !== $user->id) {
                return back()->with('swal', [
                    'icon' => 'error', 
                    'title' => 'Akses Ditolak', 
                    'text' => 'Anda tidak memiliki hak untuk menghapus pengajuan ini.'
                ]);
            }

            $oppe->delete();

            return back()->with('swal', [
                'icon' => 'success', 
                'title' => 'Berhasil!', 
                'text' => 'Pengajuan OPPE berhasil dihapus.'
            ]);

        } catch (\Exception $e) {
            return back()->with('swal', [
                'icon' => 'error', 
                'title' => 'Gagal', 
                'text' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Simpan Hasil Skoring atau Update Tambahan Instrumen
     */
    /** Peran user saat ini pada OPPE: self | peer | karu | null (bukan gilirannya). */
    private function giliranPenilai(PengajuanOppe $oppe, $user): ?string
    {
        if ($oppe->status === 'pending_self' && $oppe->user_id == $user->id) return 'self';
        if ($oppe->status === 'pending_peer' && $oppe->peer_id == $user->id) return 'peer';
        if ($oppe->status === 'pending_karu' && $oppe->evaluator_id == $user->id) return 'karu';
        return null;
    }

    /** Yang boleh melihat/mencetak OPPE selesai: admin, pihak terkait, kepala ruangan & kepala seksi. */
    private function bolehLihatOppe(PengajuanOppe $oppe, $user): bool
    {
        return $user->role === 'admin'
            || in_array($user->id, [$oppe->user_id, $oppe->peer_id, $oppe->evaluator_id])
            || in_array(optional($user->profile)->jabatan_struktural, ['kepala_ruangan', 'kepala_seksi'], true);
    }

    public function storeEvaluation(Request $request, $id)
    {
        $oppe = PengajuanOppe::findOrFail($id);

        // Hanya penilai yang sedang mendapat giliran yang boleh menyimpan
        if (!$this->giliranPenilai($oppe, Auth::user())) {
            return redirect()->route('dashboard')->with('swal', ['icon' => 'error', 'title' => 'Akses Ditolak', 'text' => 'Bukan wewenang atau giliran Anda.']);
        }

        // Memaksa format array yang bersih
        $instrumen = $oppe->instrumen_penilaian;
        if (is_string($instrumen)) {
            $instrumen = json_decode($instrumen, true);
        }
        if (!is_array($instrumen)) {
            $instrumen = [];
        }

        // =========================================================================
        // 1. JIKA ACTION = UPDATE INSTRUMEN (AUTO-RELOAD SWITCH MPP/PPI)
        // =========================================================================
        if ($request->input('action') === 'update_instrumen') {
            $existingItemIds = array_column($instrumen, 'item_id');
            
            // --- JIKA SWITCH MPP AKTIF ---
            if ($request->has('is_mpp') && $request->is_mpp == '1') {
                foreach ($this->getMppItems() as $mpp) {
                    if (!in_array($mpp['no_item'], $existingItemIds)) {
                        $instrumen[] = [
                            'item_id' => $mpp['no_item'],
                            'kategori_tugas' => $mpp['kategori_tugas'],
                            'deskripsi' => $mpp['deskripsi_kompetensi'],
                            'skor_self' => null, 'skor_peer' => null, 'skor_karu' => null
                        ];
                    }
                }
            } else {
                // Hapus jika dimatikan
                $instrumen = array_filter($instrumen, function($item) {
                    return !str_starts_with($item['item_id'], 'MPP');
                });
            }

            // --- JIKA SWITCH PPI AKTIF ---
            if ($request->has('is_ppi') && $request->is_ppi == '1') {
                foreach ($this->getPpiItems() as $ppi) {
                    if (!in_array($ppi['no_item'], $existingItemIds)) {
                        $instrumen[] = [
                            'item_id' => $ppi['no_item'],
                            'kategori_tugas' => $ppi['kategori_tugas'],
                            'deskripsi' => $ppi['deskripsi_kompetensi'],
                            'skor_self' => null, 'skor_peer' => null, 'skor_karu' => null
                        ];
                    }
                }
            } else {
                // Hapus jika dimatikan
                $instrumen = array_filter($instrumen, function($item) {
                    return !str_starts_with($item['item_id'], 'PPI');
                });
            }

            // Kelompok tugas spesifik (Hemodialisa, dst.)
            $instrumen = \App\Services\RkkKhusus::terapkanKeInstrumen(array_values($instrumen), $request);

            // Eksekusi Paksa Simpan ke Database
            $hasilAkhirInstrumen = array_values($instrumen);
            
            // Kita bypass fungsi cast bawaan laravel menggunakan DB facade agar 100% tersimpan
            DB::table($oppe->getTable())->where('id', $id)->update([
                'instrumen_penilaian' => json_encode($hasilAkhirInstrumen)
            ]);

            return back()->with('swal', ['icon' => 'success', 'title' => 'Berhasil!', 'text' => 'Instrumen kompetensi telah diperbarui.']);
        }

        // =========================================================================
        // 2. JIKA ACTION = SIMPAN SKORING PENILAIAN FINAL
        // =========================================================================
        $skorInput = $request->input('skor', []); 
        $totalSkorKaru = 0;
        $maxSkor = count($instrumen) * 3;

        foreach ($instrumen as &$item) {
            $itemId = $item['item_id'];
            $nilai = isset($skorInput[$itemId]) ? (int) $skorInput[$itemId] : null;

            if ($oppe->status === 'pending_self') {
                $item['skor_self'] = $nilai;
            } elseif ($oppe->status === 'pending_peer') {
                $item['skor_peer'] = $nilai;
            } elseif ($oppe->status === 'pending_karu') {
                $item['skor_karu'] = $nilai;
                $totalSkorKaru += $nilai;
            }
        }

        DB::beginTransaction();
        try {
            $oppe->instrumen_penilaian = array_values($instrumen);

            if ($oppe->status === 'pending_self') {
                $oppe->status = 'pending_peer';
                $msg = "Self-Assessment berhasil disimpan. Menunggu penilaian Teman Sejawat.";
            } elseif ($oppe->status === 'pending_peer') {
                $oppe->status = 'pending_karu';
                $msg = "Penilaian Sejawat berhasil disimpan. Menunggu persetujuan Karu/Atasan.";
            } elseif ($oppe->status === 'pending_karu') {
                $oppe->status = 'completed';
                $oppe->catatan_evaluator = $request->catatan;
                
                $nilaiAkhir = ($maxSkor > 0) ? ($totalSkorKaru / $maxSkor) * 100 : 0;
                $oppe->nilai_akhir = round($nilaiAkhir, 2);
                $msg = "Evaluasi OPPE berhasil dikunci. Nilai Akhir: {$oppe->nilai_akhir}%";
            }

            $oppe->save();
            DB::commit();

            return redirect()->route('dashboard')->with('swal', ['icon' => 'success', 'title' => 'Tersimpan!', 'text' => $msg]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => $e->getMessage()]);
        }
    }
}