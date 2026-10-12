@extends('logbook.print_layout')

@section('title', 'Rekap Logbook Semua Perawat – ' . $namaPeriode)

@push('styles')
    @include('logbook._styles')
    @include('logbook._lembar_styles')
    <style>
        .lembar + .lembar { margin-top: 32px; }
        @media print { .lembar { page-break-after: always; } .lembar:last-child { page-break-after: auto; } }
    </style>
@endpush

@section('content')
    <div class="no-print alert alert-info small">
        {{ $lembar->count() }} lembar rekap · {{ $namaPeriode }}. Gunakan "Simpan sebagai PDF" pada dialog cetak untuk mengunduh semuanya sekaligus.
    </div>
    @forelse ($lembar as $data)
        <div class="lembar">
            @include('logbook._lembar', $data)
        </div>
    @empty
        <p class="text-center text-muted">Tidak ada data.</p>
    @endforelse
@endsection
