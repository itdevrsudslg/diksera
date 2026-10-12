@extends('layouts.app')

@php
    $pageTitle = 'Logbook Harian';
    $pageSubtitle = 'Catatan pasien, kegiatan & tindakan harian' . ($unit ? ' · ' . $unit : '');
    $bulanIni = sprintf('%04d-%02d', $tahun, $bulan);
    $prev = \Carbon\Carbon::create($tahun, $bulan, 1)->subMonth()->format('Y-m');
    $next = \Carbon\Carbon::create($tahun, $bulan, 1)->addMonth()->format('Y-m');
    $tahap = $rekap ? $rekap->tahap : 'draft';
@endphp

@section('title', 'Logbook Harian – DIKSERA')

@push('styles')
    @include('logbook._styles')
@endpush

@section('content')
    {{-- TOOLBAR BULAN --}}
    <div class="lb-toolbar">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('perawat.logbook.index', ['bulan' => $prev]) }}" class="btn btn-sm btn-light border"><i class="bi bi-chevron-left"></i></a>
            <form method="GET" class="m-0">
                <input type="month" name="bulan" value="{{ $bulanIni }}" class="form-control form-control-sm fw-semibold" onchange="this.form.submit()">
            </form>
            <a href="{{ route('perawat.logbook.index', ['bulan' => $next]) }}" class="btn btn-sm btn-light border"><i class="bi bi-chevron-right"></i></a>
            <span class="lb-pill {{ $periode->terbuka ? 'success' : 'muted' }}" title="{{ $periode->keterangan_status }}">
                <i class="bi {{ $periode->terbuka ? 'bi-unlock' : 'bi-lock' }}"></i> {{ $periode->terbuka ? 'Terbuka' : 'Ditutup' }}
            </span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('perawat.logbook.rekap', ['jenis' => 'bulan', 'tahun' => $tahun, 'ke' => $bulan]) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-file-earmark-bar-graph"></i> Rekap
            </a>
            @if ($periode->terbuka)
                <a href="{{ route('perawat.logbook.create', ['tanggal' => $periode->awal->isSameMonth(now()) ? now()->toDateString() : $periode->awal->copy()->endOfMonth()->toDateString()]) }}"
                    class="btn btn-sm btn-primary fw-semibold">
                    <i class="bi bi-plus-lg"></i> Tambah Logbook
                </a>
            @endif
        </div>
    </div>

    @if (!$unit)
        <div class="alert alert-warning small">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Unit kerja Anda belum terisi. Lengkapi <a href="{{ route('perawat.pekerjaan.index') }}">riwayat pekerjaan</a> agar logbook bisa divalidasi kepala ruangan.
        </div>
    @endif

    {{-- RINGKASAN --}}
    <div class="row g-2 mb-3">
        @foreach ([['pasien', 'Pasien'], ['entri', 'Kunjungan'], ['tindakan', 'Tindakan'], ['valid', 'Tervalidasi']] as [$k, $l])
            <div class="col-6 col-md-3">
                <div class="lb-stat"><div class="v">{{ $ringkas[$k] }}</div><div class="l">{{ $l }}</div></div>
            </div>
        @endforeach
    </div>

    {{-- STATUS REKAP & TTD --}}
    <div class="lb-card mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <div class="fw-bold"><i class="bi bi-pen text-primary me-1"></i> Rekap {{ $periode->nama }}</div>
                <div class="small text-muted">
                    @if ($ringkas['menunggu']) {{ $ringkas['menunggu'] }} entri menunggu validasi kepala ruangan. @endif
                    @if ($ringkas['ditolak']) <span class="text-danger fw-semibold">{{ $ringkas['ditolak'] }} entri ditolak, perlu diperbaiki.</span> @endif
                    @if (!$ringkas['menunggu'] && !$ringkas['ditolak'] && $ringkas['entri']) Semua entri sudah divalidasi. @endif
                </div>
            </div>
            @if ($ringkas['entri'] && in_array($tahap, ['draft']))
                <form method="POST" action="{{ route('perawat.logbook.ttd') }}" class="m-0 js-confirm"
                    data-confirm="Tandatangani rekap {{ $periode->nama }} dan ajukan ke kepala ruangan?">
                    @csrf
                    <input type="hidden" name="bulan" value="{{ $bulanIni }}">
                    <button class="btn btn-sm btn-success fw-semibold" {{ $ringkas['ditolak'] ? 'disabled' : '' }}>
                        <i class="bi bi-pen-fill"></i> Tandatangani & Ajukan
                    </button>
                </form>
            @endif
        </div>
        <div class="lb-ttd">
            <div class="box {{ optional($rekap)->perawat_signed_at ? 'done' : '' }}">
                <div class="role">Perawat</div>
                <div class="nm">{{ Auth::user()->name }}</div>
                <div class="text-muted">{{ optional(optional($rekap)->perawat_signed_at)->translatedFormat('d M Y H:i') ?? 'Belum' }}</div>
            </div>
            <div class="box {{ optional($rekap)->karu_signed_at ? 'done' : '' }}">
                <div class="role">Kepala Ruangan</div>
                <div class="nm">{{ optional(optional($rekap)->karu)->name ?? '—' }}</div>
                <div class="text-muted">{{ optional(optional($rekap)->karu_signed_at)->translatedFormat('d M Y H:i') ?? 'Belum' }}</div>
            </div>
            <div class="box {{ optional($rekap)->kasi_signed_at ? 'done' : '' }}">
                <div class="role">Mengetahui, Kasi Keperawatan</div>
                <div class="nm">{{ optional(optional($rekap)->kasi)->name ?? '—' }}</div>
                <div class="text-muted">{{ optional(optional($rekap)->kasi_signed_at)->translatedFormat('d M Y H:i') ?? 'Belum' }}</div>
            </div>
        </div>
    </div>

    {{-- DAFTAR ENTRI PER HARI --}}
    <div class="lb-card">
        @forelse ($entries as $tgl => $list)
            <div class="lb-day">
                {{ \Carbon\Carbon::parse($tgl)->translatedFormat('l, d F Y') }}
                <span class="fw-normal">· {{ $list->count() }} pasien</span>
            </div>
            @foreach ($list as $e)
                <div class="lb-entry {{ $e->status }}">
                    <div class="d-flex flex-wrap justify-content-between gap-2">
                        <div>
                            <span class="lb-rm">RM {{ $e->no_rm }}</span>
                            @if ($e->nama_pasien) <span class="fw-semibold ms-1">{{ $e->nama_pasien }}</span> @endif
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="lb-pill {{ $e->status }}">
                                {{ ['valid' => 'Tervalidasi', 'menunggu' => 'Menunggu Karu', 'ditolak' => 'Ditolak'][$e->status] ?? $e->status }}
                            </span>
                            @if ($e->bisaDiubah())
                                <a href="{{ route('perawat.logbook.edit', $e->id) }}" class="btn btn-sm btn-light border py-0 px-2" title="Ubah"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('perawat.logbook.destroy', $e->id) }}" class="m-0 js-confirm" data-confirm="Hapus logbook pasien RM {{ $e->no_rm }}?">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light border py-0 px-2 text-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @foreach ($e->kegiatans as $k)
                        <div class="lb-keg">
                            <span class="k"><i class="bi bi-dot"></i>{{ $k->nama }}</span>
                            <div class="ms-3">
                                @foreach ($k->tindakans as $t)
                                    <span class="lb-tag"><b>{{ $t->jumlah }}×</b> {{ $t->kode }} {{ $t->nama }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    @if ($e->catatan)
                        <div class="small text-muted mt-2"><i class="bi bi-chat-left-text me-1"></i>{{ $e->catatan }}</div>
                    @endif
                    @if ($e->status === 'ditolak' && $e->catatan_validator)
                        <div class="small text-danger mt-2"><i class="bi bi-exclamation-circle me-1"></i>Alasan ditolak: {{ $e->catatan_validator }}</div>
                    @endif
                </div>
            @endforeach
        @empty
            <div class="lb-empty">
                <i class="bi bi-journal-plus display-6 d-block mb-2 opacity-50"></i>
                Belum ada logbook di {{ $periode->nama }}.
                @if ($periode->terbuka)
                    <div class="mt-2"><a href="{{ route('perawat.logbook.create') }}" class="btn btn-sm btn-primary">Isi logbook hari ini</a></div>
                @endif
            </div>
        @endforelse
    </div>
@endsection

@push('scripts')
    @include('logbook._scripts')
@endpush
