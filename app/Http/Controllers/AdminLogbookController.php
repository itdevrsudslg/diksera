<?php

namespace App\Http\Controllers;

use App\Models\LogbookEntry;
use App\Models\LogbookPeriode;
use App\Models\LogbookRekap;
use App\Models\LogbookTindakan;
use App\Models\User;
use App\Services\LogbookService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLogbookController extends Controller
{
    public function __construct()
    {
        // Nama hari & bulan berbahasa Indonesia di semua halaman logbook
        Carbon::setLocale('id');
    }

    /* =========================================================
     * PERIODE: BUKA / TUTUP
     * ========================================================= */
    public function periode(Request $request)
    {
        $tahun = (int) $request->get('tahun', now()->year);

        $tersimpan = LogbookPeriode::with('pengubah')->where('tahun', $tahun)->get()->keyBy('bulan');
        $periodes = collect(range(1, 12))->map(fn($b) => $tersimpan[$b] ?? LogbookPeriode::untuk($tahun, $b));

        $statistik = LogbookEntry::selectRaw('MONTH(tanggal) as bulan, COUNT(*) as entri, COUNT(DISTINCT user_id) as perawat, SUM(status = "valid") as valid')
            ->whereYear('tanggal', $tahun)->groupByRaw('MONTH(tanggal)')->get()->keyBy('bulan');

        return view('admin.logbook.periode', compact('tahun', 'periodes', 'statistik'));
    }

    public function ubahPeriode(Request $request)
    {
        $data = $request->validate([
            'tahun'   => 'required|integer|min:2020|max:2100',
            'bulan'   => 'required|integer|min:1|max:12',
            'status'  => 'required|in:open,closed,auto',
            'catatan' => 'nullable|string|max:255',
        ]);

        $p = LogbookPeriode::untuk($data['tahun'], $data['bulan']);
        $p->fill([
            'status_manual' => $data['status'] === 'auto' ? null : $data['status'],
            'diubah_oleh'   => Auth::id(),
            'diubah_at'     => now(),
            'catatan'       => $data['catatan'] ?? null,
        ])->save();

        $label = ['open' => 'dibuka', 'closed' => 'ditutup', 'auto' => 'dikembalikan ke jadwal otomatis'][$data['status']];
        return back()->with('swal', ['icon' => 'success', 'title' => 'Periode Diperbarui', 'text' => "Logbook {$p->nama} $label."]);
    }

    /* =========================================================
     * MASTER TINDAKAN
     * ========================================================= */
    public function tindakan(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $tindakans = LogbookTindakan::query()
            ->when($q !== '', fn($w) => $w->where(fn($x) => $x->where('kode', 'like', "%$q%")->orWhere('nama', 'like', "%$q%")))
            ->when($request->filled('kategori'), fn($w) => $w->where('kategori', $request->get('kategori')))
            ->when($request->get('aktif') === '0', fn($w) => $w->where('aktif', false), fn($w) => $w->where('aktif', true))
            ->orderBy('nama')
            ->paginate(25)->withQueryString();

        return view('admin.logbook.tindakan', [
            'tindakans' => $tindakans,
            'khanza'    => LogbookService::khanzaAktif(),
            'total'     => LogbookTindakan::count(),
            'terakhirSinkron' => LogbookTindakan::where('sumber', 'khanza')->max('updated_at'),
        ]);
    }

    public function simpanTindakan(Request $request)
    {
        $data = $request->validate([
            'kode' => 'required|string|max:30',
            'nama' => 'required|string|max:255',
        ]);

        LogbookTindakan::updateOrCreate(
            ['kode' => $data['kode'], 'kategori' => 'manual'],
            ['nama' => $data['nama'], 'sumber' => 'manual', 'aktif' => true]
        );

        return back()->with('swal', ['icon' => 'success', 'title' => 'Tersimpan', 'text' => 'Tindakan ditambahkan.']);
    }

    public function toggleTindakan($id)
    {
        $t = LogbookTindakan::findOrFail($id);
        $t->update(['aktif' => !$t->aktif]);

        return back()->with('swal', ['icon' => 'success', 'title' => 'Diperbarui', 'text' => $t->nama . ($t->aktif ? ' diaktifkan.' : ' dinonaktifkan.')]);
    }

    public function sinkronKhanza()
    {
        if (!LogbookService::khanzaAktif()) {
            return back()->with('swal', ['icon' => 'warning', 'title' => 'Khanza Belum Diatur', 'text' => 'Isi KHANZA_DB_* di file .env server terlebih dahulu.']);
        }

        try {
            $h = LogbookService::sinkronTindakanKhanza();
        } catch (\Throwable $e) {
            report($e);
            return back()->with('swal', ['icon' => 'error', 'title' => 'Gagal Terhubung', 'text' => 'Tidak bisa membaca database Khanza: ' . $e->getMessage()]);
        }

        return back()->with('swal', [
            'icon' => 'success', 'title' => 'Sinkron Selesai',
            'text' => "Baru: {$h['baru']}, diperbarui: {$h['diperbarui']}, dinonaktifkan: {$h['nonaktif']}.",
        ]);
    }

    /* =========================================================
     * REKAP SEMUA PERAWAT
     * ========================================================= */
    public function rekap(Request $request)
    {
        $bulanStr = $request->get('bulan', now()->format('Y-m'));
        $bulan = preg_match('/^\d{4}-\d{2}$/', $bulanStr) ? Carbon::createFromFormat('Y-m-d', $bulanStr . '-01') : now()->startOfMonth();
        $unit = $request->get('unit');

        $agregat = LogbookEntry::selectRaw('user_id, unit_kerja, COUNT(*) as entri, COUNT(DISTINCT no_rm) as pasien,
                SUM(status = "valid") as valid, SUM(status = "menunggu") as menunggu, SUM(status = "ditolak") as ditolak')
            ->whereYear('tanggal', $bulan->year)->whereMonth('tanggal', $bulan->month)
            ->when($unit, fn($q) => $q->where('unit_kerja', $unit))
            ->groupBy('user_id', 'unit_kerja')
            ->get();

        $users = User::whereIn('id', $agregat->pluck('user_id'))->get()->keyBy('id');
        $rekaps = LogbookRekap::with('karu', 'kasi')
            ->where(['tahun' => $bulan->year, 'bulan' => $bulan->month])
            ->whereIn('user_id', $agregat->pluck('user_id'))->get()->keyBy('user_id');

        $baris = $agregat->map(fn($a) => (object) [
            'user'   => $users[$a->user_id] ?? null,
            'unit'   => $a->unit_kerja,
            'entri'  => $a->entri,
            'pasien' => $a->pasien,
            'valid'  => (int) $a->valid,
            'menunggu' => (int) $a->menunggu,
            'ditolak'  => (int) $a->ditolak,
            'rekap'  => $rekaps[$a->user_id] ?? null,
        ])->filter(fn($b) => $b->user)->sortBy(fn($b) => [$b->unit, $b->user->name])->values();

        $units = LogbookEntry::whereNotNull('unit_kerja')->distinct()->orderBy('unit_kerja')->pluck('unit_kerja');

        return view('admin.logbook.rekap', compact('bulan', 'unit', 'units', 'baris'));
    }
}
