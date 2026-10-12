<?php

namespace App\Http\Controllers;

use App\Models\LogbookEntry;
use App\Models\LogbookRekap;
use App\Services\LogbookService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Validasi logbook oleh Kepala Ruangan (per entri + TTD rekap bulanan)
 * dan "Mengetahui" oleh Kepala Seksi Keperawatan (TTD rekap bulanan).
 */
class LogbookValidasiController extends Controller
{
    public function __construct()
    {
        // Nama hari & bulan berbahasa Indonesia di semua halaman logbook
        Carbon::setLocale('id');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isKaru = LogbookService::isKaru($user);
        $isKasi = LogbookService::isKasi($user);
        abort_unless($isKaru || $isKasi, 403, 'Menu ini khusus Kepala Ruangan dan Kepala Seksi.');

        $bulanStr = $request->get('bulan', now()->format('Y-m'));
        $bulan = preg_match('/^\d{4}-\d{2}$/', $bulanStr) ? Carbon::createFromFormat('Y-m-d', $bulanStr . '-01') : now()->startOfMonth();
        $unit = LogbookService::unitKerja($user->id);

        // Rekap bulanan yang perlu / sudah ditandatangani
        $rekaps = LogbookRekap::with('user.profile', 'karu', 'kasi')
            ->where(['tahun' => $bulan->year, 'bulan' => $bulan->month])
            ->when($isKaru, fn($q) => $q->where('unit_kerja', $unit)->where('user_id', '!=', $user->id))
            ->when($isKasi, fn($q) => $q->whereNotNull('karu_signed_at')->when($request->filled('unit'), fn($w) => $w->where('unit_kerja', $request->get('unit'))))
            ->get()
            ->sortBy(fn($r) => [$r->unit_kerja, optional($r->user)->name]);

        // Karu memvalidasi entri perawat di unitnya; Kasi memvalidasi entri para kepala ruangan
        $status = $request->get('status', 'menunggu');
        $entries = $this->entriBolehDivalidasi($user)
            ->with('user', 'kegiatans.tindakans')
            ->whereYear('tanggal', $bulan->year)->whereMonth('tanggal', $bulan->month)
            ->when($status !== 'semua', fn($q) => $q->where('status', $status))
            ->orderBy('tanggal')->orderBy('user_id')
            ->get();

        $units = $isKasi
            ? LogbookRekap::whereNotNull('karu_signed_at')->whereNotNull('unit_kerja')->distinct()->orderBy('unit_kerja')->pluck('unit_kerja')
            : collect();

        return view('perawat.logbook.validasi', compact('isKaru', 'isKasi', 'bulan', 'unit', 'rekaps', 'entries', 'units'));
    }

    /** Query entri yang boleh divalidasi user ini */
    private function entriBolehDivalidasi($user)
    {
        if (LogbookService::isKaru($user)) {
            $unit = LogbookService::unitKerja($user->id);
            return LogbookEntry::where('unit_kerja', $unit ?? '__tanpa_unit__')->where('user_id', '!=', $user->id);
        }
        if (LogbookService::isKasi($user)) {
            $karuIds = \App\Models\PerawatProfile::where('jabatan_struktural', 'kepala_ruangan')->pluck('user_id');
            return LogbookEntry::whereIn('user_id', $karuIds);
        }
        return LogbookEntry::whereRaw('1 = 0');
    }

    /** Karu (entri unitnya) / Kasi (entri kepala ruangan): validasi / tolak banyak entri sekaligus */
    public function validasiEntri(Request $request)
    {
        $user = Auth::user();
        abort_unless(LogbookService::isKaru($user) || LogbookService::isKasi($user), 403);

        $data = $request->validate([
            'ids'     => 'required|array|min:1',
            'ids.*'   => 'integer',
            'aksi'    => 'required|in:valid,ditolak',
            'catatan' => 'nullable|string|max:1000|required_if:aksi,ditolak',
        ], ['catatan.required_if' => 'Tuliskan alasan penolakan.']);

        // Entri pada bulan yang rekapnya sudah ditandatangani karu tidak bisa diubah lagi
        $n = $this->entriBolehDivalidasi($user)
            ->whereIn('id', $data['ids'])
            ->get()
            ->reject(fn($e) => LogbookRekap::where(['user_id' => $e->user_id, 'tahun' => $e->tanggal->year, 'bulan' => $e->tanggal->month])
                ->whereNotNull('karu_signed_at')->exists())
            ->each(fn($e) => $e->update([
                'status'            => $data['aksi'],
                'validator_id'      => $user->id,
                'validated_at'      => now(),
                'catatan_validator' => $data['aksi'] === 'ditolak' ? $data['catatan'] : null,
            ]))
            ->count();

        $label = $data['aksi'] === 'valid' ? 'divalidasi' : 'ditolak';
        return back()->with('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => "$n entri $label."]);
    }

    /** TTD rekap bulanan: karu (setelah semua entri valid) lalu kasi (mengetahui) */
    public function tandaTangan($id)
    {
        $user = Auth::user();
        $rekap = LogbookRekap::findOrFail($id);

        if (LogbookService::isKaru($user)) {
            abort_unless($rekap->unit_kerja === LogbookService::unitKerja($user->id), 403);
            abort_unless($rekap->perawat_signed_at, 422, 'Perawat belum menandatangani rekap.');

            $belum = LogbookEntry::where('user_id', $rekap->user_id)
                ->whereYear('tanggal', $rekap->tahun)->whereMonth('tanggal', $rekap->bulan)
                ->where('status', '!=', 'valid')->count();
            if ($belum) {
                return back()->with('swal', ['icon' => 'warning', 'title' => 'Belum Lengkap', 'text' => "Masih ada $belum entri yang belum divalidasi."]);
            }

            $rekap->update(['karu_id' => $user->id, 'karu_signed_at' => now()]);
            return back()->with('swal', ['icon' => 'success', 'title' => 'Ditandatangani', 'text' => 'Rekap diteruskan ke Kepala Seksi.']);
        }

        if (LogbookService::isKasi($user)) {
            abort_unless($rekap->karu_signed_at, 422, 'Rekap belum ditandatangani kepala ruangan.');
            $rekap->update(['kasi_id' => $user->id, 'kasi_signed_at' => now()]);
            return back()->with('swal', ['icon' => 'success', 'title' => 'Mengetahui', 'text' => 'Rekap telah ditandatangani (mengetahui).']);
        }

        abort(403);
    }

    /** Karu: kembalikan rekap ke perawat (batalkan TTD perawat) */
    public function kembalikan($id)
    {
        $user = Auth::user();
        $rekap = LogbookRekap::findOrFail($id);
        abort_unless(LogbookService::isKaru($user) && $rekap->unit_kerja === LogbookService::unitKerja($user->id), 403);
        abort_if($rekap->karu_signed_at, 422, 'Rekap sudah ditandatangani.');

        $rekap->update(['perawat_signed_at' => null]);
        return back()->with('swal', ['icon' => 'success', 'title' => 'Dikembalikan', 'text' => 'Rekap dikembalikan ke perawat untuk diperbaiki.']);
    }
}
