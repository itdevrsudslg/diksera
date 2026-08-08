<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PerawatArsipDokumen;

class PerawatArsipDokumenController extends Controller
{
    protected function currentPerawat()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'perawat') {
            return null;
        }
        return $user;
    }

    public function index()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $data = PerawatArsipDokumen::where('user_id', $user->id)
            ->orderBy('tahun', 'desc')
            ->get();

        return view('perawat.arsip.index', compact('user', 'data'));
    }

    public function create()
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        
        return view('perawat.arsip.create', compact('user'));
    }

    public function store(Request $request)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');

        $request->validate([
            'tahun' => 'required|digits:4',
            'link_drive' => 'required|url',
            'isian_dokumen' => 'required|array',
            'isian_dokumen.*' => 'required|string|max:255',
        ], [
            'isian_dokumen.required' => 'Minimal harus ada 1 isian dokumen.',
            'isian_dokumen.*.required' => 'Isian dokumen tidak boleh kosong.'
        ]);

        $data = $request->only(['tahun', 'link_drive']);
        $data['user_id'] = $user->id;
        
        // Membersihkan array dari data kosong jika terkirim
        $data['isian_dokumen'] = array_filter($request->isian_dokumen);

        PerawatArsipDokumen::create($data);

        return redirect()->route('perawat.arsip.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Arsip dokumen berhasil disimpan.'
        ]);
    }

    public function edit($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        
        $data = PerawatArsipDokumen::where('user_id', $user->id)->findOrFail($id);
        
        return view('perawat.arsip.edit', compact('user', 'data'));
    }

    public function update(Request $request, $id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        
        $arsip = PerawatArsipDokumen::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'tahun' => 'required|digits:4',
            'link_drive' => 'required|url',
            'isian_dokumen' => 'required|array',
            'isian_dokumen.*' => 'required|string|max:255',
        ]);

        $data = $request->only(['tahun', 'link_drive']);
        $data['isian_dokumen'] = array_filter($request->isian_dokumen);

        $arsip->update($data);

        return redirect()->route('perawat.arsip.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Arsip dokumen diperbarui.'
        ]);
    }

    public function destroy($id)
    {
        $user = $this->currentPerawat();
        if (!$user) return redirect('/');
        
        PerawatArsipDokumen::where('user_id', $user->id)->findOrFail($id)->delete();

        return redirect()->route('perawat.arsip.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Arsip dokumen dihapus.'
        ]);
    }
}