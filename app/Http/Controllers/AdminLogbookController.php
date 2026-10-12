<?php

namespace App\Http\Controllers;

use App\Models\LogbookEntry;
use App\Models\LogbookKegiatanTindakan;
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

    /** Semua master tindakan (JSON) untuk diunduh sebagai Excel di browser. */
    public function exportTindakan()
    {
        $rows = LogbookTindakan::orderBy('kategori')->orderBy('nama')->get()->map(fn($t) => [
            'Kode' => $t->kode,
            'Nama Tindakan' => $t->nama,
            'Kategori' => $t->kategori,
            'Aktif' => $t->aktif ? 'Ya' : 'Tidak',
            'Sumber' => $t->sumber,
        ]);

        return response()->json($rows);
    }

    /**
     * Import master tindakan dari Excel (baris dibaca di browser lalu dikirim sebagai JSON).
     * Kode + kategori yang sudah ada diperbarui, sisanya ditambahkan. Tidak ada data yang dihapus.
     */
    public function importTindakan(Request $request)
    {
        $data = $request->validate([
            'rows'            => 'required|array|min:1|max:20000',
            'rows.*.kode'     => 'nullable',
            'rows.*.nama'     => 'nullable',
            'rows.*.kategori' => 'nullable',
            'rows.*.aktif'    => 'nullable',
        ]);

        $hasil = ['baru' => 0, 'diperbarui' => 0, 'dilewati' => 0, 'error' => []];
        $ada = LogbookTindakan::all()->keyBy(fn($t) => $t->kode . '|' . $t->kategori);

        \Illuminate\Support\Facades\DB::transaction(function () use ($data, &$hasil, $ada) {
            foreach ($data['rows'] as $i => $r) {
                $kode = trim((string) ($r['kode'] ?? ''));
                $nama = trim((string) ($r['nama'] ?? ''));
                $kategori = strtolower(trim((string) ($r['kategori'] ?? ''))) ?: 'manual';
                $aktifRaw = strtolower(trim((string) ($r['aktif'] ?? '')));

                if ($kode === '' || $nama === '') {
                    $hasil['dilewati']++;
                    continue;
                }
                if (!in_array($kategori, ['ralan', 'ranap', 'manual'], true)) {
                    $hasil['error'][] = 'Baris ' . ($i + 2) . ": kategori '$kategori' tidak dikenal (pakai ralan / ranap / manual).";
                    $hasil['dilewati']++;
                    continue;
                }
                if (mb_strlen($kode) > 30 || mb_strlen($nama) > 255) {
                    $hasil['error'][] = 'Baris ' . ($i + 2) . ': kode maks 30 karakter, nama maks 255 karakter.';
                    $hasil['dilewati']++;
                    continue;
                }
                $aktif = !in_array($aktifRaw, ['0', 'tidak', 'nonaktif', 'no', 'false', 'n'], true);

                $t = $ada[$kode . '|' . $kategori] ?? null;
                if ($t) {
                    $t->fill(['nama' => $nama, 'aktif' => $aktif]);
                    if ($t->isDirty()) {
                        $t->save();
                        $hasil['diperbarui']++;
                    }
                } else {
                    $ada[$kode . '|' . $kategori] = LogbookTindakan::create([
                        'kode' => $kode, 'nama' => $nama, 'kategori' => $kategori, 'sumber' => 'manual', 'aktif' => $aktif,
                    ]);
                    $hasil['baru']++;
                }
            }
        });

        $hasil['error'] = array_slice($hasil['error'], 0, 20);
        return response()->json($hasil);
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
        [$jenis, $tahun, $ke] = LogbookService::periodeDariRequest($request);
        [$mulai, $selesai, $namaPeriode] = LogbookService::rentang($jenis, $tahun, $ke);
        $unit = $request->get('unit');
        $q = trim((string) $request->get('q'));

        $baris = $this->barisRekap($mulai, $selesai, $unit, $q);
        $units = LogbookEntry::whereNotNull('unit_kerja')->distinct()->orderBy('unit_kerja')->pluck('unit_kerja');

        return view('admin.logbook.rekap', compact('jenis', 'tahun', 'ke', 'mulai', 'selesai', 'namaPeriode', 'unit', 'q', 'units', 'baris'));
    }

    /** Data Excel (JSON) seluruh perawat sesuai filter: ringkasan, rekap tindakan, dan detail harian. */
    public function exportRekap(Request $request)
    {
        [$jenis, $tahun, $ke] = LogbookService::periodeDariRequest($request);
        [$mulai, $selesai, $namaPeriode] = LogbookService::rentang($jenis, $tahun, $ke);
        $unit = $request->get('unit');
        $baris = $this->barisRekap($mulai, $selesai, $unit, trim((string) $request->get('q')));

        $entries = LogbookEntry::with('user', 'kegiatans.tindakans')
            ->whereIn('user_id', $baris->pluck('user.id'))
            ->whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])
            ->when($unit, fn($w) => $w->where('unit_kerja', $unit))
            ->orderBy('unit_kerja')->orderBy('user_id')->orderBy('tanggal')
            ->get();

        $tgl = fn($d) => $d ? $d->format('d-m-Y H:i') : '';
        $ringkasan = $baris->map(fn($b) => [
            'Nama' => $b->user->name, 'NIP' => optional($b->user->profile)->nip, 'Unit' => $b->unit,
            'Pasien' => $b->pasien, 'Kunjungan' => $b->entri, 'Tindakan (valid)' => $b->tindakan,
            'Valid' => $b->valid, 'Menunggu' => $b->menunggu, 'Ditolak' => $b->ditolak,
            'TTD Perawat' => $tgl(optional($b->rekap)->perawat_signed_at),
            'TTD Karu' => $tgl(optional($b->rekap)->karu_signed_at),
            'TTD Kasi' => $tgl(optional($b->rekap)->kasi_signed_at),
        ])->values();

        $tindakan = [];
        $detail = [];
        foreach ($entries as $e) {
            foreach ($e->kegiatans as $k) {
                foreach ($k->tindakans as $t) {
                    $detail[] = [
                        'Tanggal' => $e->tanggal->format('d-m-Y'), 'Nama' => $e->user->name, 'Unit' => $e->unit_kerja,
                        'No RM' => $e->no_rm, 'Pasien' => $e->nama_pasien, 'Kegiatan' => $k->nama,
                        'Kode' => $t->kode, 'Tindakan' => $t->nama, 'Jumlah' => $t->jumlah, 'Status' => $e->status,
                    ];
                    if ($e->status === 'valid') {
                        $key = $e->user_id . '|' . $t->kode . '|' . $t->nama;
                        $tindakan[$key] = $tindakan[$key] ?? ['Nama' => $e->user->name, 'Unit' => $e->unit_kerja,
                            'Kode' => $t->kode, 'Tindakan' => $t->nama, 'Frekuensi' => 0, 'Jumlah' => 0];
                        $tindakan[$key]['Frekuensi']++;
                        $tindakan[$key]['Jumlah'] += $t->jumlah;
                    }
                }
            }
        }

        return response()->json([
            'periode'   => $namaPeriode,
            'ringkasan' => $ringkasan,
            'tindakan'  => array_values($tindakan),
            'detail'    => $detail,
        ]);
    }

    /** Cetak / PDF semua lembar rekap perawat sesuai filter (satu halaman per perawat). */
    public function cetakSemua(Request $request)
    {
        [$jenis, $tahun, $ke] = LogbookService::periodeDariRequest($request);
        [$mulai, $selesai, $namaPeriode] = LogbookService::rentang($jenis, $tahun, $ke);
        $baris = $this->barisRekap($mulai, $selesai, $request->get('unit'), trim((string) $request->get('q')));

        $lembar = $baris->map(fn($b) => LogbookService::lembarRekap($b->user, $jenis, $tahun, $ke));

        return view('admin.logbook.cetak_semua', compact('lembar', 'namaPeriode'));
    }

    /** Agregat per perawat untuk rentang tanggal, filter unit & cari nama/NIP. */
    private function barisRekap(Carbon $mulai, Carbon $selesai, ?string $unit, string $q)
    {
        $dari = $mulai->toDateString();
        $sampai = $selesai->toDateString();

        $cariIds = $q === '' ? null : User::where('name', 'like', "%$q%")
            ->orWhereHas('profile', fn($p) => $p->where('nip', 'like', "%$q%")->orWhere('nama_lengkap', 'like', "%$q%"))
            ->pluck('id');

        $agregat = LogbookEntry::query()
            ->selectRaw("user_id, MAX(unit_kerja) as unit_kerja, COUNT(*) as entri,
                COUNT(DISTINCT CASE WHEN status = 'valid' THEN no_rm END) as pasien,
                SUM(status = 'valid') as valid, SUM(status = 'menunggu') as menunggu, SUM(status = 'ditolak') as ditolak")
            ->whereBetween('tanggal', [$dari, $sampai])
            ->when($unit, fn($w) => $w->where('unit_kerja', $unit))
            ->when($cariIds !== null, fn($w) => $w->whereIn('user_id', $cariIds))
            ->groupBy('user_id')
            ->get();

        $ids = $agregat->pluck('user_id');
        $users = User::with('profile')->whereIn('id', $ids)->get()->keyBy('id');

        $tindakan = LogbookKegiatanTindakan::query()
            ->join('logbook_kegiatans as k', 'k.id', '=', 'logbook_kegiatan_tindakans.kegiatan_id')
            ->join('logbook_entries as e', 'e.id', '=', 'k.entry_id')
            ->whereIn('e.user_id', $ids)->where('e.status', 'valid')
            ->whereBetween('e.tanggal', [$dari, $sampai])
            ->when($unit, fn($w) => $w->where('e.unit_kerja', $unit))
            ->groupBy('e.user_id')
            ->selectRaw('e.user_id, SUM(logbook_kegiatan_tindakans.jumlah) as total')
            ->pluck('total', 'user_id');

        // Status TTD: bulan terakhir dalam rentang yang punya rekap
        $rekaps = LogbookRekap::whereIn('user_id', $ids)
            ->whereRaw('(tahun * 100 + bulan) between ? and ?', [$mulai->year * 100 + $mulai->month, $selesai->year * 100 + $selesai->month])
            ->orderBy('tahun')->orderBy('bulan')->get()->groupBy('user_id')->map->last();

        return $agregat->map(fn($a) => (object) [
            'user'     => $users[$a->user_id] ?? null,
            'unit'     => $a->unit_kerja,
            'entri'    => (int) $a->entri,
            'pasien'   => (int) $a->pasien,
            'tindakan' => (int) ($tindakan[$a->user_id] ?? 0),
            'valid'    => (int) $a->valid,
            'menunggu' => (int) $a->menunggu,
            'ditolak'  => (int) $a->ditolak,
            'rekap'    => $rekaps[$a->user_id] ?? null,
        ])->filter(fn($b) => $b->user)->sortBy(fn($b) => [$b->unit, $b->user->name])->values();
    }
}
