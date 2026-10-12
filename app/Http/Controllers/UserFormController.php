<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\UserAnswer;
use App\Models\ExamResult;
use App\Models\PengajuanSertifikat;
use App\Models\PerawatLisensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserFormController extends Controller
{
    /**
     * Helper: Cek apakah user punya akses ke form berdasarkan KFK
     */
private function checkKfkAccess($user, $form)
{
    // 1. PRIORITAS: Ambil KFK dari Pengajuan Sertifikat yang sedang aktif
    // Status 'method_selected' berarti user sudah memilih metode dan sedang proses ujian/wawancara
    $pengajuanAktif = \App\Models\PengajuanSertifikat::where('user_id', $user->id)
        ->whereIn('status', ['pending', 'verified', 'method_selected', 'interview_scheduled'])
        ->latest()
        ->first();

    $userKfkList = [];

    if ($pengajuanAktif && !empty($pengajuanAktif->kfk)) {
        // Ambil KFK dari tabel pengajuan_sertifikats
        $userKfkList = is_array($pengajuanAktif->kfk) ? $pengajuanAktif->kfk : json_decode($pengajuanAktif->kfk, true);
    } else {
        // 2. FALLBACK: Jika tidak ada pengajuan aktif, ambil dari lisensi terakhir yang aktif
        $lastLicense = \App\Models\PerawatLisensi::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest('tgl_terbit')
            ->first();

        if ($lastLicense) {
            $userKfkList = is_array($lastLicense->kfk) ? $lastLicense->kfk : json_decode($lastLicense->kfk, true);
        }
    }

    if (empty($userKfkList)) return false;

    // 3. Ambil Target KFK Form
    $formTargets = $form->kfk_target ?? [];
    if (!is_array($formTargets)) $formTargets = json_decode($formTargets, true) ?? [];

    // 4. Cek Irisan (Intersection)
    $intersection = array_intersect(
        array_map('trim', (array)$userKfkList), 
        array_map('trim', (array)$formTargets)
    );

    return count($intersection) > 0;
}
public function index()
{
    $user = Auth::user();
    $now = Carbon::now();

    // 1. Ambil data pengajuan terakhir untuk cek status global
    $pengajuanTerakhir = PengajuanSertifikat::where('user_id', $user->id)
        ->latest()
        ->first();

    $forms = Form::where('status', 'publish')
        ->with(['examResults' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])
        ->orderBy('waktu_mulai', 'desc')
        ->get()
        ->filter(function ($form) use ($user, $pengajuanTerakhir) {
            
            // --- TAMBAHAN LOGIKA PENDING ---
            // Jika ada pengajuan dan statusnya masih 'pending', sembunyikan ujian 
            // (Kecuali jika form tersebut bukan bagian dari alur sertifikasi, misal: Survei Umum)
            if ($pengajuanTerakhir && $pengajuanTerakhir->status == 'pending') {
                // Opsional: Jika Anda ingin memblokir SEMUA ujian saat pending:
                return false; 
                
                // ATAU: Hanya blokir jika judul ujian mengandung nama lisensi yang sedang diajukan
                // $namaLisensi = $pengajuanTerakhir->lisensiLama->nama ?? '';
                // if (stripos($form->judul, $namaLisensi) !== false) return false;
            }

            // --- FILTER LOGIC TAMPILAN ASLI ---
            if ($form->target_peserta == 'semua') return true;

            if ($form->target_peserta == 'khusus') {
                return $form->participants->contains('id', $user->id);
            }

            if ($form->target_peserta == 'kfk') {
                return $this->checkKfkAccess($user, $form);
            }

            return false;
        });

    // Fitur Prioritas (Tetap sama)
    if ($pengajuanTerakhir && $pengajuanTerakhir->status == 'method_selected' && $pengajuanTerakhir->lisensiLama) {
        $namaLisensi = $pengajuanTerakhir->lisensiLama->nama;
        $forms = $forms->sortByDesc(function ($form) use ($namaLisensi) {
            return stripos($form->judul, $namaLisensi) !== false;
        });
    }

    return view('perawat.ujian_aktif.index', compact('forms', 'now'));
}
    public function show(Form $form)
    {
        $user = Auth::user();

        // Validasi Akses (Security Layer 1)
        if ($form->target_peserta == 'khusus' && !$form->participants->contains($user->id)) {
            abort(403, 'Anda tidak terdaftar untuk ujian ini.');
        }

        if ($form->target_peserta == 'kfk' && !$this->checkKfkAccess($user, $form)) {
            abort(403, 'Kompetensi KFK Anda tidak sesuai untuk ujian ini.');
        }

        return view('perawat.ujian_aktif.show', compact('form'));
    }

    /** Sudah 3x atau lebih tidak lulus dan belum pernah lulus pada form ini. */
    private function sudahGagalTigaKali(int $userId, int $formId): bool
    {
        $hasil = ExamResult::where('user_id', $userId)->where('form_id', $formId)->get(['total_nilai']);
        return $hasil->count() >= 3 && $hasil->where('total_nilai', '>=', 75)->isEmpty();
    }

    public function kerjakan(Form $form)
    {
        $user = Auth::user();

        // --- VALIDASI AKSES (PENTING AGAR TIDAK TEMBUS URL) ---

        // 1. Cek Peserta Khusus
        if ($form->target_peserta == 'khusus') {
            if (!$form->participants->contains($user->id)) {
                return redirect()->route('perawat.ujian.index')->with('error', 'Akses ditolak: Anda tidak terdaftar.');
            }
        }

        // 2. Cek Peserta KFK
        if ($form->target_peserta == 'kfk') {
            if (!$this->checkKfkAccess($user, $form)) {
                return redirect()->route('perawat.ujian.index')->with('error', 'Akses ditolak: KFK tidak sesuai.');
            }
        }

        // --- VALIDASI LOGIC UJIAN ---


        // 3. Cek apakah sudah mengerjakan
        $userResult = ExamResult::where('user_id', $user->id)
            ->where('form_id', $form->id)
            ->latest('id')
            ->first();

        if ($userResult && (!$userResult->remidi && $userResult->total_nilai >= 75)) {
            // Sudah mengerjakan dan TIDAK remidi (lulus)
            return redirect()->route('perawat.ujian.index')
                ->with('error', 'Anda sudah menyelesaikan ujian ini.');
        }

        // 3b. Batas 3x percobaan (sebelumnya hanya ditegakkan lewat popup di browser)
        if ($this->sudahGagalTigaKali($user->id, $form->id)) {
            return redirect()->route('perawat.ujian.index')
                ->with('error', 'Anda sudah 3x tidak lulus pada ujian ini. Hubungi admin untuk reset hasil ujian.');
        }

        // 4. Cek Waktu
        $now = Carbon::now();
        if ($now->lessThan($form->waktu_mulai)) {
            return back()->with('error', 'Ujian belum dimulai.');
        }
        if ($now->greaterThan($form->waktu_selesai)) {
            return back()->with('error', 'Waktu ujian telah habis.');
        }

     $sessionKey = 'exam_end_time_' . $form->id . '_' . $user->id;
        
        if (!session()->has($sessionKey)) {
            // Ambil durasi dari form (default ke 90 jika bernilai null)
            $durasiUjian = $form->durasi ?? 90;
            
            // Set waktu selesai: Waktu SEKARANG + Durasi Ujian
            $endTime = Carbon::now()->addMinutes($durasiUjian);

            // TAPI, jika waktu pengerjaan melewati batas 'Waktu Selesai' global, paksa ikut batas global
            if ($endTime->greaterThan($form->waktu_selesai)) {
                $endTime = $form->waktu_selesai;
            }

            session([$sessionKey => $endTime]);
        }

    $timerEnd = session($sessionKey);

    // Ambil Soal (Acak)
    $questions = $form->questions()
        ->select('bank_soals.*')
        ->withPivot('bobot')
        ->inRandomOrder()
        ->get();

     return view('perawat.ujian_aktif.kerjakan', compact('form', 'questions', 'timerEnd'));
    }

 public function submit(Request $request, Form $form)
{
    $user = Auth::user();
    $now = Carbon::now();

    // 1. Validasi Keamanan: Toleransi waktu (5 menit)
    if ($now->greaterThan($form->waktu_selesai->addMinutes(5))) {
        return redirect()->route('perawat.ujian.index')
            ->with('error', 'Waktu ujian sudah habis, jawaban ditolak secara otomatis.');
    }

    // 1b. Validasi peserta & jadwal (sama seperti saat membuka ujian, agar tidak tembus lewat POST langsung)
    if ($form->target_peserta == 'khusus' && !$form->participants->contains($user->id)) {
        return redirect()->route('perawat.ujian.index')->with('error', 'Akses ditolak: Anda tidak terdaftar.');
    }
    if ($form->target_peserta == 'kfk' && !$this->checkKfkAccess($user, $form)) {
        return redirect()->route('perawat.ujian.index')->with('error', 'Akses ditolak: KFK tidak sesuai.');
    }
    if ($now->lessThan($form->waktu_mulai)) {
        return redirect()->route('perawat.ujian.index')->with('error', 'Ujian belum dimulai.');
    }
    if ($this->sudahGagalTigaKali($user->id, $form->id)) {
        return redirect()->route('perawat.ujian.index')->with('error', 'Anda sudah 3x tidak lulus pada ujian ini. Hubungi admin untuk reset hasil ujian.');
    }

    // 2. Cek apakah user sudah pernah lulus sebelumnya (mencegah double submit/bypass URL)
    $hasPassed = ExamResult::where('user_id', $user->id)
        ->where('form_id', $form->id)
        ->where('total_nilai', '>=', 75)
        ->exists();

    if ($hasPassed) {
        return redirect()->route('perawat.ujian.index')
            ->with('error', 'Anda sudah dinyatakan LULUS untuk ujian ini.');
    }

    // 3. Hitung jumlah percobaan sebelumnya
    $previousAttempts = ExamResult::where('user_id', $user->id)
        ->where('form_id', $form->id)
        ->count();

    // 4. Proses Penilaian Jawaban
    $questions = $form->questions()->withPivot('bobot')->get();
    $userAnswers = $request->input('answers', []);
    
    $totalBenar = 0;
    $totalSalah = 0;
    $totalNilaiBobot = 0;
    $maxBobot = 0;

    DB::beginTransaction();
    try {
        foreach ($questions as $question) {
            $bobotSoal = $question->pivot->bobot ?? 1;
            $maxBobot += $bobotSoal;

            $jawabanUser = $userAnswers[$question->id] ?? null;
            $isCorrect = false;

            if ($jawabanUser && $question->kunci_jawaban && strtolower($jawabanUser) === strtolower($question->kunci_jawaban)) {
                $isCorrect = true;
                $totalBenar++;
                $totalNilaiBobot += $bobotSoal;
            } else {
                $totalSalah++;
            }

            // Simpan Detail Jawaban (untuk audit/review jika diperlukan)
            UserAnswer::create([
                'user_id' => $user->id,
                'form_id' => $form->id,
                'bank_soal_id' => $question->id,
                'jawaban_user' => $jawabanUser,
                'is_correct' => $isCorrect,
                'nilai_diperoleh' => $isCorrect ? $bobotSoal : 0,
            ]);
        }

        // Skor Skala 0-100
        $finalScore = $maxBobot > 0 ? round(($totalNilaiBobot / $maxBobot) * 100) : 0;
        $isRemidi = $finalScore < 75;

        // Simpan Hasil Akhir ke Tabel exam_results
        $result = ExamResult::create([
            'user_id' => $user->id,
            'form_id' => $form->id,
            'total_nilai' => $finalScore,
            'total_benar' => $totalBenar,
            'total_salah' => $totalSalah,
            'remidi' => $isRemidi,
            'waktu_selesai' => now(),
        ]);

        // --- LOGIKA KRUSIAL: CEK GAGAL 3 KALI ---
      // ... di dalam method submit ...
$currentAttemptNumber = $previousAttempts + 1;

if ($isRemidi && $currentAttemptNumber >= 3) {
    DB::commit(); // Simpan dulu hasil ke-3 nya agar bisa ditampilkan di halaman selesai/index
    
    // Kirim tanda ke session untuk memicu SweetAlert di halaman index
    return redirect()->route('perawat.ujian.index')->with('trigger_hard_reset', [
        'form_id' => $form->id,
        'message' => 'Anda telah gagal dalam 3x percobaan. Sesuai aturan, data ujian dan pengajuan sertifikasi Anda akan dihapus.'
    ]);
}

        // Hapus session urutan soal setelah pengerjaan normal
        session()->forget('questions_order_' . $form->id . '_' . $user->id);
        
        DB::commit();

        return redirect()->route('perawat.ujian.hasil', [
            'result_id' => $result->id
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
    }
}

public function hardResetUjian(Request $request)
{
    $user = Auth::user();
    $formId = $request->form_id;

    DB::beginTransaction();
    try {
        // 1. Hapus Riwayat Ujian
        ExamResult::where('user_id', $user->id)->where('form_id', $formId)->delete();
        UserAnswer::where('user_id', $user->id)->where('form_id', $formId)->delete();

        // 2. Hapus Pengajuan Sertifikat
        \App\Models\PengajuanSertifikat::where('user_id', $user->id)
            ->whereIn('status', ['method_selected', 'verified', 'pending', 'interview_scheduled'])
            ->delete();

        DB::commit();
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

    public function selesai(Form $form, Request $request)
    {
        // Pastikan hanya pemilik hasil yang bisa lihat
        $result = ExamResult::where('id', $request->query('result_id'))
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('perawat.ujian_aktif.selesai', compact('form', 'result'));
    }
    
    public function lihatHasil($result_id)
{
    // Cari hasil berdasarkan ID dan pastikan milik user yang login
    $result = ExamResult::with('form')
        ->where('id', $result_id)
        ->where('user_id', Auth::id())
        ->firstOrFail();

    $form = $result->form;

    return view('perawat.ujian_aktif.selesai', compact('form', 'result'));
}
}
