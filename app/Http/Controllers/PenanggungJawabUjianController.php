<?php

namespace App\Http\Controllers;

use App\Models\PenanggungJawabUjian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\JadwalWawancara;

class PenanggungJawabUjianController extends Controller
{
 public function index()
{
    // Mengambil data beserta jumlah ujian yang sudah 'completed' (selesai diuji)
    $data = PenanggungJawabUjian::with('user')
        ->withCount([
            'jadwalWawancara as total_uji_komp' => function ($query) {
                $query->where('status', 'completed')
                      ->whereHas('pengajuan', function ($q) {
                          $q->where('metode', 'pg_interview');
                      });
            },
            'jadwalWawancara as total_kredensial' => function ($query) {
                $query->where('status', 'completed')
                      ->whereHas('pengajuan', function ($q) {
                          $q->where('metode', 'interview_only');
                      });
            }
        ])->paginate(10);

    return view('admin.penanggung_jawab_ujian.index', compact('data'));
}

public function show(Request $request, $id)
{
    $penanggungJawab = PenanggungJawabUjian::findOrFail($id);
$listAsesor = PenanggungJawabUjian::where('id', '!=', $id)->get();
    // Hitung Statistik Selesai
    $totalUjiKompSelesai = JadwalWawancara::where('penanggung_jawab_id', $id)
        ->where('status', 'completed')
        ->whereHas('pengajuan', function($q){ $q->where('metode', 'pg_interview'); })
        ->count();

    $totalKredensialSelesai = JadwalWawancara::where('penanggung_jawab_id', $id)
        ->where('status', 'completed')
        ->whereHas('pengajuan', function($q){ $q->where('metode', 'interview_only'); })
        ->count();

    // Hitung Statistik On-Going (Menunggu / Sedang Berjalan)
    $totalOnGoing = JadwalWawancara::where('penanggung_jawab_id', $id)
        ->where('status', 'approved') // approved = terjadwal & belum selesai
        ->count();

    $totalSemuaSelesai = $totalUjiKompSelesai + $totalKredensialSelesai;

    // Query untuk Tabel Riwayat (Tarik semua, tidak dibatasi hanya completed)
    $query = JadwalWawancara::with(['pengajuan.user', 'penilaian'])
        ->where('penanggung_jawab_id', $id);

    // Filter Pencarian Nama
    if ($request->filled('search')) {
        $query->whereHas('pengajuan.user', function($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%');
        });
    }

    // Filter Tipe (Metode)
    if ($request->filled('metode')) {
        $query->whereHas('pengajuan', function($q) use ($request) {
            $q->where('metode', $request->metode);
        });
    }

    // Filter Status Jadwal (Selesai vs On-Going)
    if ($request->filled('status_jadwal')) {
        $query->where('status', $request->status_jadwal);
    }

    // Filter Rentang Tanggal
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereDate('waktu_wawancara', '>=', $request->start_date)
              ->whereDate('waktu_wawancara', '<=', $request->end_date);
    }

    $riwayat = $query->orderBy('waktu_wawancara', 'desc')->paginate(10)->withQueryString();

    return view('admin.penanggung_jawab_ujian.show', compact(
        'penanggungJawab', 'riwayat', 
        'totalUjiKompSelesai', 'totalKredensialSelesai', 'totalSemuaSelesai', 'totalOnGoing','listAsesor'
    ));
}
public function updateTanggal(Request $request, $id)
{
    $request->validate([
        'waktu_wawancara' => 'required|date',
        'penanggung_jawab_id' => 'required|exists:penanggung_jawab_ujians,id' // Validasi ID asesor
    ]);

    $jadwal = \App\Models\JadwalWawancara::findOrFail($id);
    
    $jadwal->update([
        'waktu_wawancara' => $request->waktu_wawancara,
        'penanggung_jawab_id' => $request->penanggung_jawab_id // Update asesor baru
    ]);

    return back()->with('success', 'Jadwal dan Asesor berhasil diperbarui.');
}
    public function create()
    {
        return view('admin.penanggung_jawab_ujian.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi Input (Hapus validasi type)
        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'no_hp'    => 'required|string|max:20',
            'jabatan'  => 'required|string|max:255',
            // 'type' dihapus
        ]);

        DB::beginTransaction();

        try {
            // A. Buat Akun User Baru
            $user = User::create([
                'name'     => $request->nama,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'pewawancara',
            ]);

            // B. Buat Data Profil Penanggung Jawab
            PenanggungJawabUjian::create([
                'user_id' => $user->id,
                'nama'    => $request->nama,
                'no_hp'   => $request->no_hp,
                'jabatan' => $request->jabatan,
                // 'type' dihapus
            ]);

            DB::commit();

            return redirect()->route('admin.penanggung-jawab.index')
                ->with('success', 'Akun & Data Penanggung Jawab berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $item = PenanggungJawabUjian::with('user')->findOrFail($id);
        return view('admin.penanggung_jawab_ujian.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = PenanggungJawabUjian::findOrFail($id);

        // 1. Validasi Update (Hapus validasi type)
        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . ($item->user_id ?? 0),
            'password' => 'nullable|string|min:6',
            'no_hp'    => 'required|string|max:20',
            'jabatan'  => 'required|string|max:255',
            // 'type' dihapus
        ]);

        DB::beginTransaction();

        try {
            // A. Update Data Profil
            $item->update([
                'nama'    => $request->nama,
                'no_hp'   => $request->no_hp,
                'jabatan' => $request->jabatan,
                // 'type' dihapus
            ]);

            // B. Update Data Akun User
            if ($item->user) {
                $dataUser = [
                    'name'  => $request->nama,
                    'email' => $request->email
                ];

                if ($request->filled('password')) {
                    $dataUser['password'] = Hash::make($request->password);
                }

                $item->user->update($dataUser);
            }

            DB::commit();

            return redirect()->route('admin.penanggung-jawab.index')
                ->with('success', 'Data & Akun Login berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $item = PenanggungJawabUjian::findOrFail($id);

        DB::beginTransaction();
        try {
            if ($item->user) {
                $item->user->delete();
            }

            $item->delete();

            DB::commit();
            return redirect()->route('admin.penanggung-jawab.index')
                ->with('success', 'Data & Akun berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data.');
        }
    }
}
