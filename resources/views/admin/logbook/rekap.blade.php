@extends('layouts.app')

@php
    $pageTitle = 'Rekap Logbook';
    $pageSubtitle = 'Rekap logbook seluruh perawat · ' . $bulan->translatedFormat('F Y');
@endphp

@section('title', 'Rekap Logbook – Admin DIKSERA')

@push('styles')
    @include('logbook._styles')
@endpush

@section('content')
    <div class="lb-toolbar">
        <form method="GET" class="d-flex flex-wrap gap-2 m-0">
            <input type="month" name="bulan" value="{{ $bulan->format('Y-m') }}" class="form-control form-control-sm" style="width:auto" onchange="this.form.submit()">
            <select name="unit" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                <option value="">Semua unit</option>
                @foreach ($units as $u)
                    <option value="{{ $u }}" {{ $unit === $u ? 'selected' : '' }}>{{ $u }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('admin.logbook.periode') }}" class="btn btn-sm btn-light border"><i class="bi bi-calendar-check"></i> Periode</a>
    </div>

    <div class="row g-2 mb-3">
        @foreach ([[$baris->count(), 'Perawat mengisi'], [$baris->sum('pasien'), 'Pasien'], [$baris->sum('entri'), 'Entri'], [$baris->filter(fn($b) => optional($b->rekap)->kasi_signed_at)->count(), 'Rekap selesai (3 TTD)']] as [$v, $l])
            <div class="col-6 col-md-3"><div class="lb-stat"><div class="v">{{ $v }}</div><div class="l">{{ $l }}</div></div></div>
        @endforeach
    </div>

    <div class="lb-card">
        <div class="table-responsive">
            <table class="table lb-table align-middle mb-0">
                <thead>
                    <tr><th>Perawat</th><th>Unit</th><th class="text-center">Pasien</th><th class="text-center">Entri</th><th>Validasi</th><th>TTD (Perawat · Karu · Kasi)</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($baris as $b)
                        <tr>
                            <td class="fw-semibold">{{ $b->user->name }}</td>
                            <td class="small">{{ $b->unit ?? '-' }}</td>
                            <td class="text-center">{{ $b->pasien }}</td>
                            <td class="text-center">{{ $b->entri }}</td>
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
                                <a href="{{ route('perawat.logbook.rekap', ['userId' => $b->user->id, 'jenis' => 'bulan', 'tahun' => $bulan->year, 'ke' => $bulan->month]) }}"
                                    class="btn btn-sm btn-light border"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="lb-empty">Belum ada logbook pada bulan ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
