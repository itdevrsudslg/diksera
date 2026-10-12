<?php

namespace App\Http\Controllers;

use App\Services\Penandatangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminPenandatanganController extends Controller
{
    public function index()
    {
        $riwayat = DB::table('penandatangans')
            ->leftJoin('users', 'users.id', '=', 'penandatangans.dibuat_oleh')
            ->select('penandatangans.*', 'users.name as pembuat')
            ->orderByDesc('berlaku_mulai')->orderByDesc('penandatangans.id')
            ->get()->groupBy('slot');

        $aktif = collect(array_keys(Penandatangan::SLOT))->mapWithKeys(fn($s) => [$s => Penandatangan::untuk($s)]);

        return view('admin.penandatangan.index', [
            'slots'   => Penandatangan::SLOT,
            'aktif'   => $aktif,
            'riwayat' => $riwayat,
            'label'   => Penandatangan::semuaLabel(),
            'labelDefault' => Penandatangan::LABEL,
        ]);
    }

    /** Ganti pejabat mulai tanggal tertentu (dokumen sebelum tanggal itu tetap memakai pejabat lama). */
    public function simpan(Request $request)
    {
        $data = $request->validate([
            'slot'          => 'required|in:' . implode(',', array_keys(Penandatangan::SLOT)),
            'nama'          => 'required|string|max:150',
            'nip'           => 'nullable|string|max:50',
            'pangkat'       => 'nullable|string|max:100',
            'berlaku_mulai' => 'required|date',
        ]);

        // Catat pejabat bawaan sebagai riwayat awal agar dokumen lama tetap memakai nama lama
        if (!DB::table('penandatangans')->where('slot', $data['slot'])->exists()) {
            $def = Penandatangan::SLOT[$data['slot']];
            DB::table('penandatangans')->insert([
                'slot' => $data['slot'], 'nama' => $def['nama'], 'nip' => $def['nip'], 'pangkat' => $def['pangkat'],
                'berlaku_mulai' => '1900-01-01', 'dibuat_oleh' => null, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        DB::table('penandatangans')->insert(array_merge($data, [
            'dibuat_oleh' => Auth::id(), 'created_at' => now(), 'updated_at' => now(),
        ]));
        Penandatangan::lupakanCache();

        $jabatan = Penandatangan::SLOT[$data['slot']]['jabatan'];
        return back()->with('swal', ['icon' => 'success', 'title' => 'Tersimpan', 'text' => "$jabatan diganti mulai " . \Carbon\Carbon::parse($data['berlaku_mulai'])->translatedFormat('d M Y') . '.']);
    }

    public function hapus($id)
    {
        $row = DB::table('penandatangans')->where('id', $id)->first();
        abort_unless($row, 404);
        abort_if($row->berlaku_mulai === '1900-01-01', 422, 'Data awal tidak bisa dihapus.');
        DB::table('penandatangans')->where('id', $id)->delete();
        Penandatangan::lupakanCache();

        return back()->with('swal', ['icon' => 'success', 'title' => 'Dihapus', 'text' => 'Riwayat penanda tangan dihapus.']);
    }

    public function simpanLabel(Request $request)
    {
        $data = $request->validate([
            'label'   => 'required|array',
            'label.*' => 'nullable|string|max:150',
        ]);

        foreach (array_keys(Penandatangan::LABEL) as $kunci) {
            $nilai = trim((string) ($data['label'][$kunci] ?? ''));
            DB::table('pengaturans')->updateOrInsert(['kunci' => $kunci], [
                'nilai' => $nilai === '' || $nilai === Penandatangan::LABEL[$kunci] ? null : $nilai,
                'updated_at' => now(), 'created_at' => now(),
            ]);
        }
        Penandatangan::lupakanCache();

        return back()->with('swal', ['icon' => 'success', 'title' => 'Tersimpan', 'text' => 'Label kolom tanda tangan diperbarui.']);
    }
}
