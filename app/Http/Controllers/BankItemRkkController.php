<?php

namespace App\Http\Controllers;

use App\Models\BankItemRkk;
use Illuminate\Http\Request;

class BankItemRkkController extends Controller
{
    public function index(Request $request)
    {
        $query = BankItemRkk::query();

        // 1. Filter pencarian teks berdasarkan Deskripsi Kompetensi
        if ($request->filled('search')) {
            $query->where('deskripsi_kompetensi', 'like', '%' . $request->search . '%');
        }

        // 2. Filter opsional berdasarkan Jenjang Klinis
        if ($request->filled('jenjang_klinis')) {
            $query->where('jenjang_klinis', $request->jenjang_klinis);
        }

        $query->latest();

        // 3. Pagination dinamis (default 10)
        $perPage = $request->input('per_page', 10);
        $items = $query->paginate($perPage);

        // Ambil daftar jenjang klinis unik untuk filter dropdown di view (jika diperlukan)
        $jenjangKlinis = BankItemRkk::select('jenjang_klinis')
            ->whereNotNull('jenjang_klinis')
            ->where('jenjang_klinis', '!=', '')
            ->distinct()
            ->pluck('jenjang_klinis');

        return view('admin.bank_item_rkk.index', compact('items', 'jenjangKlinis'));
    }

    public function exportJsonData(Request $request)
    {
        $query = BankItemRkk::query();

        if ($request->filled('search')) {
            $query->where('deskripsi_kompetensi', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('jenjang_klinis')) {
            $query->where('jenjang_klinis', $request->jenjang_klinis);
        }

        // Ambil semua data sesuai filter dan map ke format persis seperti Header Excel
        $data = $query->get()->map(function ($item) {
            return [
                'KFK' => $item->kfk,
                'Jenjang_Klinis' => $item->jenjang_klinis,
                'Kategori_Tugas' => $item->kategori_tugas,
                'No_Item' => $item->no_item,
                'Deskripsi_Kompetensi' => $item->deskripsi_kompetensi,
            ];
        });

        return response()->json($data);
    }

    public function create()
    {
        return view('admin.bank_item_rkk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kfk'                  => 'nullable|string',
            'jenjang_klinis'       => 'nullable|string',
            'kategori_tugas'       => 'nullable|string',
            'no_item'              => 'nullable|string',
            'deskripsi_kompetensi' => 'required|string',
        ]);

        BankItemRkk::create($request->all());

        return redirect()->route('admin.bank-item-rkk.index')->with('success', 'Item RKK berhasil ditambahkan');
    }

    public function edit($id)
    {
        $item = BankItemRkk::findOrFail($id);
        return view('admin.bank_item_rkk.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = BankItemRkk::findOrFail($id);

        $request->validate([
            'kfk'                  => 'nullable|string',
            'jenjang_klinis'       => 'nullable|string',
            'kategori_tugas'       => 'nullable|string',
            'no_item'              => 'nullable|string',
            'deskripsi_kompetensi' => 'required|string',
        ]);

        $item->update($request->all());

        return redirect()->route('admin.bank-item-rkk.index')->with('success', 'Item RKK berhasil diperbarui');
    }

    public function destroy($id)
    {
        $item = BankItemRkk::findOrFail($id);
        $item->delete();

        return back()->with('success', 'Item RKK berhasil dihapus');
    }

    public function importJson(Request $request)
    {
        $data = $request->json()->all();
        $count = 0;
        
        foreach ($data as $row) {
            // Kita kasih fallback operator (??) jaga-jaga huruf besar/kecil di Excelnya beda
            $deskripsi = $row['Deskripsi_Kompetensi'] ?? $row['deskripsi_kompetensi'] ?? null;
            
            // Lewati baris kalau deskripsi kompetensinya kosong
            if (empty($deskripsi)) continue;

            BankItemRkk::create([
                'kfk'                  => $row['KFK'] ?? $row['kfk'] ?? null,
                'jenjang_klinis'       => $row['Jenjang_Klinis'] ?? $row['jenjang_klinis'] ?? null,
                'kategori_tugas'       => $row['Kategori_Tugas'] ?? $row['kategori_tugas'] ?? null,
                'no_item'              => $row['No_Item'] ?? $row['no_item'] ?? null,
                'deskripsi_kompetensi' => $deskripsi,
            ]);
            
            $count++;
        }
        
        return response()->json([
            'message' => "Berhasil mengimport $count Item RKK.", 
            'count' => $count
        ]);
    }
}