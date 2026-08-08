<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BankSoalController extends Controller
{

public function index(Request $request)
    {
        $query = BankSoal::query();

        // 1. Filter pencarian teks
        if ($request->filled('search')) {
            $query->where('pertanyaan', 'like', '%' . $request->search . '%');
        }

        // 2. Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // 3. Filter Soal Duplikat & Unik
        if ($request->filled('filter_tipe')) {
            $duplicatePertanyaan = BankSoal::select('pertanyaan')
                ->groupBy('pertanyaan')
                ->havingRaw('COUNT(id) > 1')
                ->pluck('pertanyaan');

            if ($request->filter_tipe == 'duplikat') {
                // Tampilkan hanya yang duplikat dan urutkan agar yang sama berdekatan
                $query->whereIn('pertanyaan', $duplicatePertanyaan)
                      ->orderBy('pertanyaan');
            } elseif ($request->filter_tipe == 'unik') {
                // Tampilkan hanya soal yang tidak duplikat
                $query->whereNotIn('pertanyaan', $duplicatePertanyaan)
                      ->latest();
            } else {
                $query->latest();
            }
        } else {
            $query->latest();
        }

        // 4. Pagination dinamis
        $perPage = $request->input('per_page', 10);
        $soals = $query->paginate($perPage);

        // Ambil daftar kategori unik
        $kategoris = BankSoal::select('kategori')
            ->whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->pluck('kategori');

        return view('admin.bank_soal.index', compact('soals', 'kategoris'));
    }

    // Fungsi baru untuk export SEMUA data berdasarkan filter saat ini
    public function exportJsonData(Request $request)
    {
        $query = BankSoal::query();

        if ($request->filled('search')) {
            $query->where('pertanyaan', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('filter_tipe')) {
            $duplicatePertanyaan = BankSoal::select('pertanyaan')
                ->groupBy('pertanyaan')
                ->havingRaw('COUNT(id) > 1')
                ->pluck('pertanyaan');

            if ($request->filter_tipe == 'duplikat') {
                $query->whereIn('pertanyaan', $duplicatePertanyaan)->orderBy('pertanyaan');
            } elseif ($request->filter_tipe == 'unik') {
                $query->whereNotIn('pertanyaan', $duplicatePertanyaan)->latest();
            }
        }

        // Ambil semua data tanpa pagination untuk di-export
        $data = $query->get()->map(function ($soal) {
            return [
                'ID' => $soal->id,
                'Pertanyaan' => $soal->pertanyaan,
                'Kategori' => $soal->kategori ?? 'Umum',
                'Opsi A' => $soal->opsi_jawaban['a'] ?? '-',
                'Opsi B' => $soal->opsi_jawaban['b'] ?? '-',
                'Opsi C' => $soal->opsi_jawaban['c'] ?? '-',
                'Opsi D' => $soal->opsi_jawaban['d'] ?? '-',
                'Opsi E' => $soal->opsi_jawaban['e'] ?? '-',
                'Kunci Jawaban' => strtoupper($soal->kunci_jawaban),
            ];
        });

        return response()->json($data);
    }
    public function create()
    {
        return view('admin.bank_soal.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'pertanyaan'     => 'required|string',
            'kategori'       => 'required|string',
            'gambar'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'opsi.a'         => 'required|string',
            'opsi.b'         => 'required|string',
            'opsi.c'         => 'required|string',
            'opsi.d'         => 'required|string',
            'opsi.e'         => 'required|string',
            'kunci_jawaban'  => 'required|in:a,b,c,d,e',
        ]);

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('soal', 'public');
        }

        BankSoal::create([
            'pertanyaan'   => $request->pertanyaan,
            'gambar'       => $gambarPath,
            'kategori'     => $request->kategori,
            'opsi_jawaban' => $request->opsi,
            'kunci_jawaban' => $request->kunci_jawaban,
        ]);

        return redirect()->route('admin.bank-soal.index')->with('success', 'Soal berhasil ditambahkan');
    }

    public function edit($id)
    {
        $soal = BankSoal::findOrFail($id);
        return view('admin.bank_soal.edit', compact('soal'));
    }

    public function update(Request $request, $id)
    {
        $bankSoal = BankSoal::findOrFail($id);

        $request->validate([
            'pertanyaan'     => 'required|string',
            'kategori'       => 'required|string',
            'gambar'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'opsi.a'         => 'required|string',
            'opsi.b'         => 'required|string',
            'opsi.c'         => 'required|string',
            'opsi.d'         => 'required|string',
            'opsi.e'         => 'required|string',
            'kunci_jawaban'  => 'required|in:a,b,c,d,e',
        ]);

        $data = [
            'pertanyaan'   => $request->pertanyaan,
            'kategori'     => $request->kategori,
            'opsi_jawaban' => $request->opsi,
            'kunci_jawaban' => $request->kunci_jawaban,
        ];

        if ($request->hasFile('gambar')) {
            if ($bankSoal->gambar) {
                Storage::disk('public')->delete($bankSoal->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('soal', 'public');
        }

        $bankSoal->update($data);

        return redirect()->route('admin.bank-soal.index')->with('success', 'Soal berhasil diperbarui');
    }

    public function destroy($id)
    {
        $soal = BankSoal::findOrFail($id);
        if ($soal->gambar) {
            Storage::disk('public')->delete($soal->gambar);
        }
        $soal->delete();

        return back()->with('success', 'Soal berhasil dihapus');
    }

    public function importJson(Request $request)
    {
        $data = $request->json()->all();
        $count = 0;
        foreach ($data as $row) {
            if (empty($row['pertanyaan']) || empty($row['kunci_jawaban'])) continue;

            BankSoal::create([
                'pertanyaan' => $row['pertanyaan'],
                'kategori'   => $row['kategori'] ?? 'Umum',
                'opsi_jawaban' => [
                    'a' => $row['opsi_a'] ?? '-',
                    'b' => $row['opsi_b'] ?? '-',
                    'c' => $row['opsi_c'] ?? '-',
                    'd' => $row['opsi_d'] ?? '-',
                    'e' => $row['opsi_e'] ?? '-',
                ],
                'kunci_jawaban' => strtolower($row['kunci_jawaban']),
            ]);
            $count++;
        }
        return response()->json(['message' => "Berhasil mengimport $count soal.", 'count' => $count]);
    }
}
