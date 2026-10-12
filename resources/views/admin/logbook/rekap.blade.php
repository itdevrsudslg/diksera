@extends('layouts.app')

@php
    $pageTitle = 'Rekap Logbook';
    $pageSubtitle = 'Rekap logbook seluruh perawat · ' . $namaPeriode;
    $filter = array_filter(['jenis' => $jenis, 'tahun' => $tahun, 'ke' => $ke, 'unit' => $unit, 'q' => $q]);
@endphp

@section('title', 'Rekap Logbook – Admin DIKSERA')

@push('styles')
    @include('logbook._styles')
@endpush

@section('content')
    <div class="lb-card mb-3">
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                @include('logbook._periode_filter', ['extra' => array_filter(['unit' => $unit, 'q' => $q])])
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-sm btn-success fw-semibold" id="btnExcel" {{ $baris->isEmpty() ? 'disabled' : '' }}>
                    <i class="bi bi-file-earmark-excel"></i> Download Excel
                </button>
                <a href="{{ route('admin.logbook.rekap.cetak', $filter) }}" target="_blank"
                    class="btn btn-sm btn-primary fw-semibold {{ $baris->isEmpty() ? 'disabled' : '' }}">
                    <i class="bi bi-printer"></i> Cetak Semua (PDF)
                </a>
            </div>
        </div>

        <form method="GET" class="d-flex flex-wrap gap-2 mt-3">
            <input type="hidden" name="jenis" value="{{ $jenis }}">
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <input type="hidden" name="ke" value="{{ $ke }}">
            <div class="input-group input-group-sm" style="max-width: 320px;">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Cari nama / NIP perawat…">
            </div>
            <select name="unit" class="form-select form-select-sm" style="width:auto">
                <option value="">Semua unit</option>
                @foreach ($units as $u)
                    <option value="{{ $u }}" {{ $unit === $u ? 'selected' : '' }}>{{ $u }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-primary">Cari</button>
            @if ($q || $unit)
                <a href="{{ route('admin.logbook.rekap', ['jenis' => $jenis, 'tahun' => $tahun, 'ke' => $ke]) }}" class="btn btn-sm btn-light border">Reset</a>
            @endif
        </form>
    </div>

    <div class="row g-2 mb-3">
        @foreach ([[$baris->count(), 'Perawat mengisi'], [$baris->sum('pasien'), 'Pasien (valid)'], [$baris->sum('tindakan'), 'Tindakan (valid)'], [$baris->filter(fn($b) => optional($b->rekap)->kasi_signed_at)->count(), 'Rekap selesai (3 TTD)']] as [$v, $l])
            <div class="col-6 col-md-3"><div class="lb-stat"><div class="v">{{ $v }}</div><div class="l">{{ $l }}</div></div></div>
        @endforeach
    </div>

    <div class="lb-card">
        <div class="table-responsive">
            <table class="table lb-table align-middle mb-0">
                <thead>
                    <tr><th>Perawat</th><th>Unit</th><th class="text-center">Pasien</th><th class="text-center">Tindakan</th><th>Validasi</th><th>TTD (Perawat · Karu · Kasi)</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($baris as $b)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $b->user->name }}</div>
                                <div class="small text-muted">NIP {{ optional($b->user->profile)->nip ?? '-' }}</div>
                            </td>
                            <td class="small">{{ $b->unit ?? '-' }}</td>
                            <td class="text-center">{{ $b->pasien }}</td>
                            <td class="text-center">{{ $b->tindakan }}</td>
                            <td class="text-nowrap">
                                <span class="lb-pill valid">{{ $b->valid }} valid</span>
                                @if ($b->menunggu)<span class="lb-pill menunggu">{{ $b->menunggu }} menunggu</span>@endif
                                @if ($b->ditolak)<span class="lb-pill ditolak">{{ $b->ditolak }} ditolak</span>@endif
                            </td>
                            <td class="text-nowrap">
                                @foreach (['perawat_signed_at', 'karu_signed_at', 'kasi_signed_at'] as $c)
                                    <i class="bi {{ optional($b->rekap)->$c ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted' }} me-1"></i>
                                @endforeach
                            </td>
                            <td class="text-end">
                                <a href="{{ route('perawat.logbook.rekap', ['userId' => $b->user->id, 'jenis' => $jenis, 'tahun' => $tahun, 'ke' => $ke]) }}"
                                    class="btn btn-sm btn-light border" title="Lihat rekap"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="lb-empty">{{ $q ? 'Tidak ada perawat yang cocok dengan pencarian.' : 'Belum ada logbook pada periode ini.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
    <script>
        document.getElementById('btnExcel').addEventListener('click', async function () {
            const btn = this, label = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyusun…';
            try {
                const res = await fetch(@json(route('admin.logbook.rekap.export', $filter)));
                if (!res.ok) throw new Error('Gagal mengambil data');
                const d = await res.json();
                const wb = XLSX.utils.book_new();
                const sheet = (rows, name, widths) => {
                    const ws = XLSX.utils.json_to_sheet(rows.length ? rows : [{ Info: 'Tidak ada data' }]);
                    if (widths) ws['!cols'] = widths.map(w => ({ wch: w }));
                    XLSX.utils.book_append_sheet(wb, ws, name);
                };
                sheet(d.ringkasan, 'Ringkasan', [28, 22, 22, 8, 10, 15, 7, 9, 8, 17, 17, 17]);
                sheet(d.tindakan, 'Rekap Tindakan', [28, 22, 12, 40, 10, 8]);
                sheet(d.detail, 'Detail Harian', [11, 28, 22, 10, 24, 30, 12, 40, 8, 10]);
                XLSX.writeFile(wb, `Rekap_Logbook_${d.periode.replace(/\s+/g, '_')}.xlsx`);
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: e.message });
            } finally {
                btn.disabled = false;
                btn.innerHTML = label;
            }
        });
    </script>
@endpush
