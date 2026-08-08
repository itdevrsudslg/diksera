<?php

namespace App\Http\Controllers;

use App\Models\BankSoal;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\User;
use App\Models\PenanggungJawabUjian;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FormController extends Controller
{
    // --- LIST KFK ---
private function getKfkOptions()
{
    return [
        'KFK KRITIS' => [
            'KFK Kritis Gawat Darurat', 'KFK Kritis ICU', 'KFK Kritis ICVCU', 'KFK Kritis Anestesi',
        ],
        'KFK MEDIKAL BEDAH' => [
            'KFK Medikal Bedah Penyakit Dalam', 'KFK Medikal Bedah Bedah', 'KFK Medikal Bedah Perioperatif', 'KFK Medikal Bedah Isolasi (Keperawatan Khusus)',
        ],
        'KFK ANAK' => [
            'KFK Anak NICU', 'KFK Anak PICU', 'KFK Anak Pediatri', 'KFK Anak Neonatus',
        ],
        'KFK MATERNITAS' => [
            'KFK Maternitas',
        ],
        'JENIS PERAWAT KLINIS (PK)' => [
            'PRA PK', 'PK 1',
            'PK 2 KEP GAWAT DARURAT', 'PK 3 KEP GAWAT DARURAT', 'PK 4 KEP GAWAT DARURAT', 'PK 5 KEP GAWAT DARURAT',
            'PK 2 KEP ICU', 'PK 3 KEP ICU', 'PK 4 KEP ICU', 'PK 5 KEP ICU',
            'PK 2 KEP ICVCU', 'PK 3 KEP ICVCU', 'PK 4 KEP ICVCU', 'PK 5 KEP ICVCU',
            'PK 2 KEP ANESTESI', 'PK 3 KEP ANESTESI', 'PK 4 KEP ANESTESI', 'PK 5 KEP ANESTESI',
            'PK 2 KEP PENYAKIT DALAM', 'PK 3 KEP PENYAKIT DALAM', 'PK 4 KEP PENYAKIT DALAM', 'PK 5 KEP PENYAKIT DALAM',
            'PK 2 KEP BEDAH', 'PK 3 KEP BEDAH', 'PK 4 KEP BEDAH', 'PK 5 KEP BEDAH',
            'PK 2 KEP PERIOPERATIF', 'PK 3 KEP PERIOPERATIF', 'PK 4 KEP PERIOPERATIF', 'PK 5 KEP PERIOPERATIF',
            'PK 2 KEP ISOLASI (KEPERAWATAN KHUSUS)', 'PK 3 KEP ISOLASI (KEPERAWATAN KHUSUS)', 'PK 4 KEP ISOLASI (KEPERAWATAN KHUSUS)', 'PK 5 KEP ISOLASI (KEPERAWATAN KHUSUS)',
            'PK 2 KEP NICU', 'PK 3 KEP NICU', 'PK 4 KEP NICU', 'PK 5 KEP NICU',
            'PK 2 KEP PICU', 'PK 3 KEP PICU', 'PK 4 KEP PICU', 'PK 5 KEP PICU',
            'PK 2 KEP PEDIATRI', 'PK 3 KEP PEDIATRI', 'PK 4 KEP PEDIATRI', 'PK 5 KEP PEDIATRI',
            'PK 2 KEP NEONATUS', 'PK 3 KEP NEONATUS', 'PK 4 KEP NEONATUS', 'PK 5 KEP NEONATUS',
        ],
        'JENIS BIDAN KLINIS (BK)' => [
            'PRA BK', 'BK 1',
            'BK 2 MATERNITAS', 'BK 3 MATERNITAS', 'BK 4 MATERNITAS', 'BK 5 MATERNITAS',
        ],
    ];
}

    public function index()
    {
        $forms = Form::with('penanggungJawab')->latest()->get();
        return view('admin.form.index', compact('forms'));
    }

    public function create()
    {
        $pjs = PenanggungJawabUjian::all();
        $users = User::where('role', 'perawat')->get();
        $kfkOptions = $this->getKfkOptions(); // Load KFK

        return view('admin.form.create', compact('users', 'pjs', 'kfkOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'target_peserta' => 'required|in:semua,khusus,kfk', // Tambah 'kfk'
            'durasi' => 'required|integer|min:1',
            // Validasi jika pilih khusus
            'participants' => 'required_if:target_peserta,khusus|array',
            
            // Validasi jika pilih kfk
            'kfk_target' => 'required_if:target_peserta,kfk|array',
        ]);

        $form = Form::create([
            'judul' => $request->judul,
            'slug' => Str::slug($request->judul) . '-' . Str::random(5),
            'deskripsi' => $request->deskripsi,
            'penanggung_jawab_id' => $request->penanggung_jawab_id,
            'waktu_mulai' => $request->waktu_mulai,
            'durasi' => $request->durasi, 
            'waktu_selesai' => $request->waktu_selesai,
            'target_peserta' => $request->target_peserta,
            'kfk_target' => $request->target_peserta == 'kfk' ? $request->kfk_target : null, // Simpan KFK
            'status' => 'draft',
        ]);

        if ($request->target_peserta == 'khusus' && $request->has('participants')) {
            $form->participants()->attach($request->participants);
        }

        return redirect()->route('admin.form.index')->with('success', 'Form berhasil dibuat!');
    }

    public function edit(Form $form)
    {
        $pjs = PenanggungJawabUjian::all();
        $users = User::where('role', 'perawat')->get();
        $selectedParticipants = $form->participants->pluck('id')->toArray();
        $kfkOptions = $this->getKfkOptions(); // Load KFK
        
        // Ambil KFK yang tersimpan (jika ada)
        $selectedKfk = $form->kfk_target ?? [];

        return view('admin.form.edit', compact('form', 'users', 'selectedParticipants', 'pjs', 'kfkOptions', 'selectedKfk'));
    }

    public function update(Request $request, Form $form)
    {
        $request->validate([
            'target_peserta' => 'required|in:semua,khusus,kfk',
            'participants' => 'required_if:target_peserta,khusus|array',
            'kfk_target' => 'required_if:target_peserta,kfk|array',
            'durasi' => 'required|integer|min:1',
        ]);

        $form->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'penanggung_jawab_id' => $request->penanggung_jawab_id,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'target_peserta' => $request->target_peserta,
            'durasi' => $request->durasi,
            'kfk_target' => $request->target_peserta == 'kfk' ? $request->kfk_target : null,
        ]);

        // Sync Peserta Khusus
        if ($request->target_peserta == 'khusus') {
            $form->participants()->sync($request->participants ?? []);
        } else {
            $form->participants()->detach();
        }

        return redirect()->route('admin.form.index')->with('success', 'Form berhasil diperbarui!');
    }
    
    public function updateStatus(Request $request, Form $form)
    {
        $request->validate([
            'status' => 'required|in:draft,publish,closed',
        ]);

        $form->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Status ujian berhasil diperbarui menjadi ' . ucfirst($request->status));
    }

    public function destroy(Form $form)
    {
        $form->delete();
        return back()->with('success', 'Form berhasil dihapus!');
    }

    // --- FITUR SOAL ---

  public function kelolaSoal(Form $form)
    {
        // Hilangkan duplikat pertanyaan pakai ->unique() jaga-jaga jika ada data double di DB
        $allSoals = BankSoal::latest()->get()->unique('pertanyaan'); 
        $existingSoalIds = $form->questions()->allRelatedIds()->toArray();

        // Ambil list kategori unik untuk bahan filter
        $kategoris = BankSoal::select('kategori')->distinct()->pluck('kategori');

        return view('admin.form.kelola_soal', compact('form', 'allSoals', 'existingSoalIds', 'kategoris'));
    }

    public function simpanSoal(Request $request, Form $form)
    {
        $request->validate([
            'soal_ids' => 'array',
            'soal_ids.*' => 'exists:bank_soals,id',
        ]);

        $form->questions()->sync($request->soal_ids ?? []);

        return redirect()->route('admin.form.index')->with('success', 'Soal berhasil diatur!');
    }

  
    public function generateSoal(Request $request, Form $form)
    {
        $request->validate([
            'jumlah_soal' => 'required|integer|min:1',
            'kategori'    => 'required|array', // Ubah jadi array karena bisa pilih banyak
        ]);

        $form->questions()->detach();

        $query = BankSoal::query();

        // Kalau usernya TIDAK memilih 'Semua'
        if (!in_array('Semua', $request->kategori)) {
            // whereIn akan mencari soal yang kategorinya ada di dalam array pilihanmu
            $query->whereIn('kategori', $request->kategori);
        }

        // Ambil ID secara acak dari gabungan pool kategori tersebut
        $randomSoalIds = $query->inRandomOrder()
            ->take((int) $request->jumlah_soal)
            ->pluck('id')
            ->toArray();

        if (empty($randomSoalIds)) {
            return back()->with('error', 'Stok soal tidak cukup untuk kategori yang dipilih.');
        }

        // Siapkan Data Insert
        $dataInsert = [];
        $now = Carbon::now();

        foreach ($randomSoalIds as $soalId) {
            $dataInsert[] = [
                'form_id'      => $form->id,
                'bank_soal_id' => $soalId,
                'bobot'        => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        // Insert ke database
        DB::table('form_questions')->insert($dataInsert);

        return back()->with('success', 'Berhasil mereset dan mengambil ' . count($dataInsert) . ' soal acak.');
    }

    public function hasil(Form $form)
    {
        $results = $form->examResults()
            ->with('user')
            ->orderByDesc('total_nilai')
            ->get();

        return view('admin.form.hasil', compact('form', 'results'));
    }

    public function resetHasil($id)
    {
        $result = ExamResult::findOrFail($id);
        $result->delete();
        return back()->with('success', 'Data ujian peserta berhasil direset.');
    }
}
