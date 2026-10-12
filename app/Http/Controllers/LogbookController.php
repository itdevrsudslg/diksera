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
use Illuminate\Support\Facades\DB;

class LogbookController extends Controller
{
    public function __construct()
    {
        // Nama hari & bulan berbahasa Indonesia di semua halaman logbook
        Carbon::setLocale('id');
    }

    /* =========================================================
     * DAFTAR LOGBOOK BULANAN (PERAWAT)
     * ========================================================= */
    public function index(Request $request)
    {
        $user = Auth::user();
        [$tahun, $bulan] = $this->bulanDipilih($request);
        $periode = LogbookPeriode::untuk($tahun, $bulan);

        $entries = LogbookEntry::with('kegiatans.tindakans', 'validator')
            ->where('user_id', $user->id)
            ->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)
            ->orderByDesc('tanggal')->orderByDesc('id')
            ->get();

        $rekap = LogbookRekap::with('karu', 'kasi')
            ->where(['user_id' => $user->id, 'tahun' => $tahun, 'bulan' => $bulan])->first();

        $ringkas = [
            'pasien'   => $entries->pluck('no_rm')->unique()->count(),
            'entri'    => $entries->count(),
            'tindakan' => $entries->sum->jumlah_tindakan,
            'valid'    => $entries->where('status', 'valid')->count(),
            'menunggu' => $entries->where('status', 'menunggu')->count(),
            'ditolak'  => $entries->where('status', 'ditolak')->count(),
        ];

        return view('perawat.logbook.index', [
            'entries'  => $entries->groupBy(fn($e) => $e->tanggal->toDateString()),
            'periode'  => $periode,
            'rekap'    => $rekap,
            'ringkas'  => $ringkas,
            'tahun'    => $tahun,
            'bulan'    => $bulan,
            'unit'     => LogbookService::unitKerja($user->id),
            // Kepala ruangan: entrinya divalidasi Kasi & ia sendiri mengisi kolom TTD kepala ruangan
            'isKaru'   => LogbookService::isKaru($user),
        ]);
    }

    /* =========================================================
     * TAMBAH / UBAH ENTRI
     * ========================================================= */
    public function create(Request $request)
    {
        $unit = LogbookService::unitKerja(Auth::id());
        if (!$unit) {
            return redirect()->route('perawat.logbook.index')->with('swal', [
                'icon' => 'warning', 'title' => 'Unit Kerja Kosong',
                'text' => 'Lengkapi riwayat pekerjaan (unit kerja) di DRH terlebih dahulu agar logbook bisa divalidasi kepala ruangan.',
            ]);
        }

        $tanggal = $request->get('tanggal');
        if (!$tanggal || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) || $tanggal > now()->toDateString()) {
            $tanggal = now()->toDateString();
        }
        if (!LogbookPeriode::untukTanggal($tanggal)->terbuka) {
            return redirect()->route('perawat.logbook.index')->with('swal', [
                'icon' => 'info', 'title' => 'Periode Ditutup',
                'text' => 'Pengisian logbook untuk bulan tersebut sudah ditutup. Hubungi admin bila perlu dibuka kembali.',
            ]);
        }

        return view('perawat.logbook.form', [
            'entry'   => new LogbookEntry(['tanggal' => $tanggal]),
            'unit'    => $unit,
            'khanza'  => LogbookService::khanzaAktif(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validasiForm($request);
        $unit = LogbookService::unitKerja(Auth::id());

        DB::transaction(function () use ($data, $unit) {
            $entry = LogbookEntry::create([
                'user_id'     => Auth::id(),
                'tanggal'     => $data['tanggal'],
                'unit_kerja'  => $unit,
                'no_rm'       => $data['no_rm'],
                'nama_pasien' => $data['nama_pasien'] ?? null,
                'catatan'     => $data['catatan'] ?? null,
            ]);
            $this->simpanKegiatan($entry, $data['kegiatan']);
        });

        $lagi = $request->input('aksi') === 'simpan_lagi';
        return redirect()
            ->route($lagi ? 'perawat.logbook.create' : 'perawat.logbook.index', $lagi
                ? ['tanggal' => $data['tanggal']]
                : ['bulan' => Carbon::parse($data['tanggal'])->format('Y-m')])
            ->with('swal', ['icon' => 'success', 'title' => 'Tersimpan', 'text' => 'Logbook pasien ' . $data['no_rm'] . ' disimpan.']);
    }

    public function edit($id)
    {
        $entry = $this->milikSaya($id);
        if (!$entry->bisaDiubah()) {
            return back()->with('swal', ['icon' => 'info', 'title' => 'Terkunci', 'text' => 'Entri sudah divalidasi atau periodenya ditutup.']);
        }

        return view('perawat.logbook.form', [
            'entry'  => $entry->load('kegiatans.tindakans'),
            'unit'   => $entry->unit_kerja,
            'khanza' => LogbookService::khanzaAktif(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $entry = $this->milikSaya($id);
        abort_unless($entry->bisaDiubah(), 403, 'Entri sudah terkunci.');
        $data = $this->validasiForm($request);

        DB::transaction(function () use ($entry, $data) {
            $entry->update([
                'tanggal'     => $data['tanggal'],
                'no_rm'       => $data['no_rm'],
                'nama_pasien' => $data['nama_pasien'] ?? null,
                'catatan'     => $data['catatan'] ?? null,
                // Entri yang ditolak lalu diperbaiki kembali menunggu validasi
                'status'      => 'menunggu',
                'validator_id' => null,
                'validated_at' => null,
            ]);
            $entry->kegiatans()->delete();
            $this->simpanKegiatan($entry, $data['kegiatan']);
        });

        return redirect()->route('perawat.logbook.index', ['bulan' => Carbon::parse($data['tanggal'])->format('Y-m')])
            ->with('swal', ['icon' => 'success', 'title' => 'Diperbarui', 'text' => 'Logbook berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $entry = $this->milikSaya($id);
        abort_unless($entry->bisaDiubah(), 403, 'Entri sudah terkunci.');
        $entry->delete();

        return back()->with('swal', ['icon' => 'success', 'title' => 'Dihapus', 'text' => 'Entri logbook dihapus.']);
    }

    /* =========================================================
     * AJAX: PASIEN & TINDAKAN
     * ========================================================= */
    public function cariPasien(Request $request)
    {
        $rm = trim((string) $request->get('no_rm'));
        if ($rm === '') return response()->json(['found' => false]);

        $p = LogbookService::cariPasien($rm);
        if ($p) {
            return response()->json(['found' => true, 'nama' => $p['nm_pasien'], 'jk' => $p['jk'] ?? null]);
        }

        // Fallback: nama dari entri sebelumnya
        $nama = LogbookEntry::where('no_rm', $rm)->whereNotNull('nama_pasien')->latest()->value('nama_pasien');
        return response()->json(['found' => (bool) $nama, 'nama' => $nama, 'sumber' => 'riwayat']);
    }

    public function cariTindakan(Request $request)
    {
        $q = trim((string) $request->get('q'));

        $rows = LogbookTindakan::aktif()
            ->when($q !== '', function ($w) use ($q) {
                $w->where(fn($x) => $x->where('kode', 'like', "%$q%")->orWhere('nama', 'like', "%$q%"));
            })
            ->orderBy('nama')->limit(30)
            ->get(['id', 'kode', 'nama', 'kategori']);

        return response()->json($rows->map(fn($t) => [
            'value' => $t->id,
            'label' => $t->kode . ' - ' . $t->nama . ($t->kategori !== 'manual' ? ' (' . strtoupper($t->kategori) . ')' : ''),
        ]));
    }

    /* =========================================================
     * REKAP & TANDA TANGAN PERAWAT
     * ========================================================= */
    public function rekap(Request $request, $userId = null)
    {
        $viewer = Auth::user();
        $target = $userId ? User::findOrFail($userId) : $viewer;
        $this->bolehLihatRekap($viewer, $target);

        [$jenis, $tahun, $ke] = LogbookService::periodeDariRequest($request);

        return view('logbook.rekap', array_merge(
            LogbookService::lembarRekap($target, $jenis, $tahun, $ke),
            ['cetak' => $request->boolean('cetak')]
        ));
    }

    public function tandaTangan(Request $request)
    {
        $user = Auth::user();
        [$tahun, $bulan] = $this->bulanDipilih($request);

        $entries = LogbookEntry::where('user_id', $user->id)
            ->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);

        if (!(clone $entries)->exists()) {
            return back()->with('swal', ['icon' => 'info', 'title' => 'Belum Ada Data', 'text' => 'Belum ada logbook pada bulan ini.']);
        }
        if ((clone $entries)->where('status', 'ditolak')->exists()) {
            return back()->with('swal', ['icon' => 'warning', 'title' => 'Ada Entri Ditolak', 'text' => 'Perbaiki entri yang ditolak kepala ruangan sebelum menandatangani rekap.']);
        }

        $rekap = LogbookRekap::firstOrNew(['user_id' => $user->id, 'tahun' => $tahun, 'bulan' => $bulan]);
        if ($rekap->karu_signed_at) {
            return back()->with('swal', ['icon' => 'info', 'title' => 'Sudah Divalidasi', 'text' => 'Rekap bulan ini sudah ditandatangani kepala ruangan.']);
        }

        // Kepala ruangan: entrinya divalidasi Kasi, lalu ia menandatangani sebagai penyusun sekaligus kepala ruangan
        if (LogbookService::isKaru($user)) {
            if ((clone $entries)->where('status', '!=', 'valid')->exists()) {
                return back()->with('swal', ['icon' => 'info', 'title' => 'Menunggu Validasi Kasi', 'text' => 'Logbook kepala ruangan divalidasi Kepala Seksi. Tanda tangan setelah semua entri valid.']);
            }
            $rekap->fill(['unit_kerja' => LogbookService::unitKerja($user->id), 'perawat_signed_at' => now(),
                'karu_id' => $user->id, 'karu_signed_at' => now()])->save();
            return back()->with('swal', ['icon' => 'success', 'title' => 'Rekap Diajukan', 'text' => 'Rekap diteruskan ke Kepala Seksi.']);
        }

        $rekap->fill(['unit_kerja' => LogbookService::unitKerja($user->id), 'perawat_signed_at' => now()])->save();

        return back()->with('swal', ['icon' => 'success', 'title' => 'Rekap Diajukan', 'text' => 'Rekap ditandatangani dan diteruskan ke kepala ruangan.']);
    }

    /* =========================================================
     * HELPER
     * ========================================================= */
    private function bulanDipilih(Request $request): array
    {
        $b = $request->get('bulan');
        $d = $b && preg_match('/^\d{4}-\d{2}$/', $b) ? Carbon::createFromFormat('Y-m-d', $b . '-01') : now();
        return [$d->year, $d->month];
    }

    private function milikSaya($id): LogbookEntry
    {
        return LogbookEntry::where('user_id', Auth::id())->findOrFail($id);
    }

    private function validasiForm(Request $request): array
    {
        $data = $request->validate([
            'tanggal'                   => 'required|date|before_or_equal:today',
            'no_rm'                     => 'required|string|max:30',
            'nama_pasien'               => 'nullable|string|max:150',
            'catatan'                   => 'nullable|string|max:2000',
            'kegiatan'                  => 'required|array|min:1',
            'kegiatan.*.nama'           => 'required|string|max:255',
            'kegiatan.*.tindakan'       => 'required|array|min:1',
            'kegiatan.*.tindakan.*.id'  => 'required|integer|exists:logbook_tindakans,id',
            'kegiatan.*.tindakan.*.jumlah' => 'required|integer|min:1|max:999',
        ], [
            'tanggal.before_or_equal'        => 'Tanggal tidak boleh melebihi hari ini.',
            'kegiatan.required'              => 'Isi minimal satu kegiatan.',
            'kegiatan.*.nama.required'       => 'Nama kegiatan wajib diisi.',
            'kegiatan.*.tindakan.required'   => 'Setiap kegiatan minimal punya satu tindakan.',
            'kegiatan.*.tindakan.*.id.required' => 'Pilih jenis tindakan.',
        ]);

        if (!LogbookPeriode::untukTanggal($data['tanggal'])->terbuka) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'tanggal' => 'Periode bulan tersebut sudah ditutup.',
            ]);
        }

        // Bulan yang rekapnya sudah ditandatangani kepala ruangan tidak boleh ditambah/diubah lagi
        $tgl = Carbon::parse($data['tanggal']);
        $terkunci = LogbookRekap::where(['user_id' => Auth::id(), 'tahun' => $tgl->year, 'bulan' => $tgl->month])
            ->whereNotNull('karu_signed_at')->exists();
        if ($terkunci) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'tanggal' => 'Rekap bulan tersebut sudah ditandatangani kepala ruangan, logbook tidak bisa ditambah lagi.',
            ]);
        }

        if (!LogbookService::unitKerja(Auth::id())) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'tanggal' => 'Unit kerja belum diisi di riwayat pekerjaan.',
            ]);
        }

        return $data;
    }

    private function simpanKegiatan(LogbookEntry $entry, array $kegiatans): void
    {
        $master = LogbookTindakan::whereIn('id', collect($kegiatans)->flatMap(fn($k) => collect($k['tindakan'])->pluck('id')))
            ->get()->keyBy('id');

        foreach (array_values($kegiatans) as $k) {
            $kegiatan = $entry->kegiatans()->create(['nama' => $k['nama']]);
            foreach (array_values($k['tindakan']) as $t) {
                $m = $master[$t['id']];
                $kegiatan->tindakans()->create([
                    'tindakan_id' => $m->id,
                    'kode'        => $m->kode,
                    'nama'        => $m->nama,
                    'jumlah'      => $t['jumlah'],
                ]);
            }
        }
    }

    private function bolehLihatRekap(User $viewer, User $target): void
    {
        if ($viewer->id === $target->id || $viewer->role === 'admin' || LogbookService::isKasi($viewer)) return;
        $unitViewer = LogbookService::unitKerja($viewer->id);
        if (LogbookService::isKaru($viewer) && $unitViewer && $unitViewer === LogbookService::unitKerja($target->id)) return;
        abort(403, 'Anda tidak berhak melihat rekap ini.');
    }
}
