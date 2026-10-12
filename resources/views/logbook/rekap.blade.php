@extends($cetak ? 'logbook.print_layout' : 'layouts.app')

@php
    $pageTitle = 'Rekap Logbook';
    $pageSubtitle = $target->name . ' · ' . $namaPeriode;
    $profile = $target->profile;
    $multi = $jenis !== 'bulan';
    $self = Auth::id() === $target->id;
    $routeArgs = $self ? [] : ['userId' => $target->id];
@endphp

@section('title', 'Rekap Logbook ' . $target->name . ' – ' . $namaPeriode)

@push('styles')
    @include('logbook._styles')
    @include('logbook._lembar_styles')
@endpush

@section('content')
    @unless ($cetak)
        {{-- PILIH PERIODE --}}
        <div class="lb-toolbar">
            @include('logbook._periode_filter')
            <div class="d-flex gap-2">
                @if ($self)
                    <a href="{{ route('perawat.logbook.index', ['bulan' => $mulai->format('Y-m')]) }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left"></i> Logbook</a>
                @endif
                <a href="{{ route('perawat.logbook.rekap', array_merge($routeArgs, ['jenis' => $jenis, 'tahun' => $tahun, 'ke' => $ke, 'cetak' => 1])) }}"
                    target="_blank" class="btn btn-sm btn-primary"><i class="bi bi-printer"></i> Cetak / PDF</a>
            </div>
        </div>
        <div class="alert alert-info small py-2"><i class="bi bi-info-circle me-1"></i> Rekap hanya menghitung entri yang sudah <b>divalidasi kepala ruangan</b>.</div>
    @endunless

    @include('logbook._lembar')
@endsection
