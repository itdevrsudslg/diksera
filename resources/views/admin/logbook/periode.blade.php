@extends('layouts.app')

@php
    $pageTitle = 'Periode Logbook';
    $pageSubtitle = 'Buka / tutup pengisian logbook per bulan (otomatis tutup H+' . \App\Models\LogbookPeriode::HARI_TOLERANSI . ')';
@endphp

@section('title', 'Periode Logbook – Admin DIKSERA')

@push('styles')
    @include('logbook._styles')
@endpush

@section('content')
    <div class="lb-toolbar">
        <form method="GET" class="m-0">
            <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach (range(now()->year + 1, now()->year - 3) as $y)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.logbook.tindakan') }}" class="btn btn-sm btn-light border"><i class="bi bi-list-ul"></i> Master Tindakan</a>
            <a href="{{ route('admin.logbook.rekap') }}" class="btn btn-sm btn-light border"><i class="bi bi-bar-chart"></i> Rekap</a>
        </div>
    </div>

    <div class="lb-card">
        <div class="table-responsive">
            <table class="table lb-table align-middle mb-0">
                <thead>
                    <tr><th>Bulan</th><th>Status</th><th>Keterangan</th><th class="text-center">Perawat</th><th class="text-center">Entri</th><th class="text-center">Valid</th><th class="text-end">Atur</th></tr>
                </thead>
                <tbody>
                    @foreach ($periodes as $p)
                        @php $st = $statistik[$p->bulan] ?? null; @endphp
                        <tr class="{{ $p->awal->isSameMonth(now()) ? 'table-primary' : '' }}">
                            <td class="fw-semibold">{{ $p->awal->translatedFormat('F') }}</td>
                            <td>
                                <span class="lb-pill {{ $p->terbuka ? 'success' : 'muted' }}">
                                    <i class="bi {{ $p->terbuka ? 'bi-unlock' : 'bi-lock' }}"></i> {{ $p->terbuka ? 'Terbuka' : 'Ditutup' }}
                                </span>
                                @if ($p->status_manual)<span class="lb-pill info ms-1">Manual</span>@endif
                            </td>
                            <td class="small text-muted">
                                {{ $p->keterangan_status }}
                                @if ($p->pengubah)<br>oleh {{ $p->pengubah->name }}, {{ optional($p->diubah_at)->translatedFormat('d M Y H:i') }}@endif
                                @if ($p->catatan)<br><i>{{ $p->catatan }}</i>@endif
                            </td>
                            <td class="text-center">{{ $st->perawat ?? 0 }}</td>
                            <td class="text-center">{{ $st->entri ?? 0 }}</td>
                            <td class="text-center">{{ (int) ($st->valid ?? 0) }}</td>
                            <td class="text-end text-nowrap">
                                @foreach (['open' => ['Buka', 'btn-outline-success', 'bi-unlock'], 'closed' => ['Tutup', 'btn-outline-danger', 'bi-lock'], 'auto' => ['Otomatis', 'btn-outline-secondary', 'bi-clock']] as $st2 => [$lbl, $cls, $ic])
                                    @php $aktif = ($st2 === 'auto' && !$p->status_manual) || $p->status_manual === $st2; @endphp
                                    @unless ($aktif)
                                        <form method="POST" action="{{ route('admin.logbook.periode.ubah') }}" class="d-inline js-confirm"
                                            data-confirm="{{ $lbl }} logbook {{ $p->nama }}?">
                                            @csrf
                                            <input type="hidden" name="tahun" value="{{ $p->tahun }}">
                                            <input type="hidden" name="bulan" value="{{ $p->bulan }}">
                                            <input type="hidden" name="status" value="{{ $st2 }}">
                                            <button class="btn btn-sm {{ $cls }} py-0"><i class="bi {{ $ic }}"></i> {{ $lbl }}</button>
                                        </form>
                                    @endunless
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    @include('logbook._scripts')
@endpush
