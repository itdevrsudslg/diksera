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
   
    public function create()
    {
        $user = Auth::user();
        $myProfile = PerawatProfile::where('user_id', $user->id)->first();
        
        // Cari Unit Kerja terakhir
        $myJob = PerawatPekerjaan::where('user_id', $user->id)->orderBy('tahun_mulai', 'desc')->first();
        $myUnit = $myJob->unit_kerja ?? '';

        if (empty($myUnit)) {
            return redirect()->route('dashboard')->with('swal', ['icon' => 'warning', 'title' => 'Unit Kerja Kosong', 'text' => 'Silakan lengkapi riwayat pekerjaan Anda terlebih dahulu.']);
        }

        // Cari semua User ID yang berada di Unit Kerja yang sama
        $userIdsInUnit = PerawatPekerjaan::where('unit_kerja', $myUnit)->pluck('user_id')->toArray();

        // 1. Cari Teman Sejawat (Di unit yang sama, jabatan pelaksana, dan BUKAN diri sendiri)
        $peers = PerawatProfile::whereIn('user_id', $userIdsInUnit)
            ->where('user_id', '!=', $user->id)
            ->where('jabatan_struktural', 'pelaksana')
            ->get();

        // 2. Cari Evaluator (Karu atau Kasi)
        if ($myProfile && $myProfile->jabatan_struktural === 'kepala_ruangan') {
            // Jika yang login adalah Karu, maka evaluatornya adalah Kasi
            $evaluators = PerawatProfile::where('jabatan_struktural', 'kepala_seksi')->get();
            $evaluatorLabel = 'Kepala Seksi (Kasi)';
        } else {
            // Jika yang login adalah Pelaksana, evaluatornya adalah Karu di unit yang sama
            $evaluators = PerawatProfile::whereIn('user_id', $userIdsInUnit)
                ->where('jabatan_struktural', 'kepala_ruangan')
                ->get();
            $evaluatorLabel = 'Kepala Ruangan (Karu)';
        }

        // Cek apakah ada pengajuan OPPE tahun ini yang masih berjalan
        $tahunIni = date('Y');
        $sedangMengajukan = PengajuanOppe::where('user_id', $user->id)
            ->where('periode', $tahunIni)
            ->whereNotIn('status', ['completed'])
            ->exists();

        return view('perawat.oppe.create', compact('peers', 'evaluators', 'evaluatorLabel', 'myUnit', 'sedangMengajukan', 'tahunIni'));
    }

    /**
     * Normalisasi Jenjang (Fungsi yang sama persis seperti di Lisensi)
     */
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
            'periode' => 'required|date', // <--- VALIDASI DIUBAH MENJADI FORMAT TANGGAL
        ]);

        $user = Auth::user();

        // 1. Ekstrak KFK dan Jenjang dari Input secara mentah (Sesuai database)
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

        // 2. Tarik Data RKK dari BankItemRkk menggunakan nilai yang sudah diformat
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

        // 3. Susun Snapshot JSON untuk Instrumen OPPE
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

        // 4. Simpan ke Database
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
                'periode' => $request->periode, // <--- MENYIMPAN TANGGAL LENGKAP (CONTOH: 2025-05-12)
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
    
    /**
     * Cetak Dokumen Hasil OPPE
     */
    public function cetak($id)
    {
        $oppe = PengajuanOppe::with(['user.profile', 'user.pekerjaans', 'peer.profile', 'evaluator.profile', 'lisensi'])->findOrFail($id);
        
        // Pastikan hanya dokumen yang sudah selesai yang bisa dicetak utuh
        if ($oppe->status !== 'completed') {
            return back()->with('swal', ['icon' => 'warning', 'title' => 'Belum Selesai', 'text' => 'Dokumen OPPE belum selesai dinilai oleh Atasan.']);
        }

        // Kelompokkan instrumen berdasarkan kategori tugas (A. Tugas Pokok, B. Spesifik)
        $instrumenGrouped = collect($oppe->instrumen_penilaian)->groupBy('kategori_tugas');

        // Hitung total skor masing-masing penilai
        $totalSelf = 0;
        $totalPeer = 0;
        $totalKaru = 0;
        $maxSkor = count($oppe->instrumen_penilaian) * 3; // Nilai Maksimum (seluruh indikator x 3)

        foreach ($oppe->instrumen_penilaian as $item) {
            $totalSelf += (int) ($item['skor_self'] ?? 0);
            $totalPeer += (int) ($item['skor_peer'] ?? 0);
            $totalKaru += (int) ($item['skor_karu'] ?? 0);
        }

        // Menghitung Nilai Rata-rata dari 3 penilai (opsional jika dibutuhkan di form)
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

        // JIKA BUKAN ADMIN, terapkan visibilitas (Hanya tampilkan yang relevan dengan user)
        if ($user->role !== 'admin') {
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('peer_id', $user->id)
                  ->orWhere('evaluator_id', $user->id);
            });
        }

        // --- FILTER STATUS ---
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // --- SEARCH KEYWORD ---
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Cari berdasarkan Periode
                $q->where('periode', 'like', "%{$search}%")
                  // Atau cari berdasarkan Nama Pegawai yang dinilai
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhereHas('profile', function($p) use ($search) {
                            $p->where('nama_lengkap', 'like', "%{$search}%");
                        });
                  });
            });
        }

        // Ubah get() menjadi paginate() dan bawa parameter query string (agar filter tidak hilang saat ganti halaman)
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

        // BERIKAN AKSES BEBAS UNTUK ADMIN SEBAGAI VIEWER
        if ($currentUser->role === 'admin') {
            $roleEvaluasi = 'viewer'; 
            $canEdit = false;
        } elseif ($oppe->status === 'pending_self' && $oppe->user_id === $currentUser->id) {
            $roleEvaluasi = 'self'; $canEdit = true;
        } elseif ($oppe->status === 'pending_peer' && $oppe->peer_id === $currentUser->id) {
            $roleEvaluasi = 'peer'; $canEdit = true;
        } elseif ($oppe->status === 'pending_karu' && $oppe->evaluator_id === $currentUser->id) {
            $roleEvaluasi = 'karu'; $canEdit = true;
        } elseif ($oppe->status === 'completed') {
            $roleEvaluasi = 'viewer'; 
        } else {
            return redirect()->route('dashboard')->with('swal', ['icon' => 'error', 'title' => 'Akses Ditolak', 'text' => 'Bukan wewenang atau giliran Anda.']);
        }

        // ===============================================================
        // TAMBAHAN: Cari Nilai OPPE Terakhir milik perawat yang dinilai
        // ===============================================================
        $lastOppe = PengajuanOppe::where('user_id', $oppe->user_id)
            ->where('status', 'completed')
            ->where('id', '!=', $oppe->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $instrumenGrouped = collect($oppe->instrumen_penilaian)->groupBy('kategori_tugas');

        // Jangan lupa tambahkan 'lastOppe' ke dalam compact()
        return view('perawat.oppe.evaluate', compact('oppe', 'instrumenGrouped', 'roleEvaluasi', 'canEdit', 'lastOppe'));
    }

    public function destroy($id)
    {
        try {
            $oppe = PengajuanOppe::findOrFail($id);
            $user = Auth::user();

            // Cek: Hanya Pemilik atau Admin yang boleh menghapus
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
     * Simpan Hasil Skoring
     */
    public function storeEvaluation(Request $request, $id)
    {
        $oppe = PengajuanOppe::findOrFail($id);
        $skorInput = $request->input('skor', []); // Format: skor[item_id] = nilai (1-3)
        $instrumen = $oppe->instrumen_penilaian;

        $totalSkorKaru = 0;
        $maxSkor = count($instrumen) * 3;

        // Looping dan Update JSON
        foreach ($instrumen as &$item) {
            $itemId = $item['item_id'];
            $nilai = isset($skorInput[$itemId]) ? (int) $skorInput[$itemId] : null;

            if ($oppe->status === 'pending_self') {
                $item['skor_self'] = $nilai;
            } elseif ($oppe->status === 'pending_peer') {
                $item['skor_peer'] = $nilai;
            } elseif ($oppe->status === 'pending_karu') {
                $item['skor_karu'] = $nilai;
                $totalSkorKaru += $nilai; // Karu penentu nilai akhir
            }
        }

        // Update Status & Simpan
        DB::beginTransaction();
        try {
            $oppe->instrumen_penilaian = $instrumen;

            if ($oppe->status === 'pending_self') {
                $oppe->status = 'pending_peer';
                $msg = "Self-Assessment berhasil disimpan. Menunggu penilaian Teman Sejawat.";
            } elseif ($oppe->status === 'pending_peer') {
                $oppe->status = 'pending_karu';
                $msg = "Penilaian Sejawat berhasil disimpan. Menunggu persetujuan Karu/Atasan.";
            } elseif ($oppe->status === 'pending_karu') {
                $oppe->status = 'completed';
                $oppe->catatan_evaluator = $request->catatan;
                
                // Hitung Nilai Akhir (Total / Max * 100)
                $nilaiAkhir = ($totalSkorKaru / $maxSkor) * 100;
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