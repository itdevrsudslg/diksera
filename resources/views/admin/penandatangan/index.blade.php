@extends('layouts.app')

@php
    $pageTitle = 'Penanda Tangan Dokumen';
    $pageSubtitle = 'Pejabat & label kolom tanda tangan pada RKK, SPK, SPKK, Sertifikat, OPPE, dan Logbook';
@endphp

@section('title', 'Penanda Tangan – Admin DIKSERA')

@push('styles')
    @include('logbook._styles')
@endpush

@section('content')
    <div class="alert alert-info small">
        <i class="bi bi-info-circle me-1"></i>
        Dokumen memakai pejabat yang <b>berlaku pada tanggal terbit dokumen</b>. Mengganti pejabat mulai tanggal tertentu
        tidak mengubah dokumen yang terbit sebelum tanggal itu.
    </div>

    <div class="row g-3 mb-4">
        @foreach ($slots as $slot => $def)
            @php $p = $aktif[$slot]; $hist = $riwayat[$slot] ?? collect(); @endphp
            <div class="col-lg-4">
                <div class="lb-card h-100 d-flex flex-column">
                    <div class="small text-muted text-uppercase fw-semibold" style="letter-spacing:.4px">{{ $def['jabatan'] }}</div>
                    <div class="fw-bold fs-6 mt-1">{{ $p->nama }}</div>
                    <div class="small text-muted">NIP. {{ $p->nip ?? '-' }}@if ($p->pangkat) · {{ $p->pangkat }}@endif</div>

                    <form method="POST" action="{{ route('admin.penandatangan.simpan') }}" class="mt-3 border-top pt-3">
                        @csrf
                        <input type="hidden" name="slot" value="{{ $slot }}">
                        <div class="small fw-semibold mb-2">Ganti pejabat</div>
                        <input name="nama" class="form-control form-control-sm mb-2" placeholder="Nama lengkap + gelar" required>
                        <div class="d-flex gap-2 mb-2">
                            <input name="nip" class="form-control form-control-sm" placeholder="NIP">
                            <input name="pangkat" class="form-control form-control-sm" placeholder="Pangkat / Gol.">
                        </div>
                        <label class="small text-muted">Berlaku mulai</label>
                        <div class="d-flex gap-2">
                            <input type="date" name="berlaku_mulai" class="form-control form-control-sm" value="{{ now()->toDateString() }}" required>
                            <button class="btn btn-sm btn-primary text-nowrap">Simpan</button>
                        </div>
                    </form>

                    @if ($hist->count())
                        <div class="small fw-semibold mt-3 mb-1">Riwayat</div>
                        <ul class="list-unstyled small mb-0">
                            @foreach ($hist as $h)
                                <li class="d-flex justify-content-between align-items-start gap-2 py-1 border-bottom">
                                    <span>
                                        <b>{{ $h->nama }}</b><br>
                                        <span class="text-muted">
                                            {{ $h->berlaku_mulai === '1900-01-01' ? 'Data awal' : 'Mulai ' . \Carbon\Carbon::parse($h->berlaku_mulai)->translatedFormat('d M Y') }}
                                            @if ($h->pembuat) · oleh {{ $h->pembuat }} @endif
                                        </span>
                                    </span>
                                    @if ($h->berlaku_mulai !== '1900-01-01')
                                        <form method="POST" action="{{ route('admin.penandatangan.hapus', $h->id) }}" class="m-0 js-confirm" data-confirm="Hapus riwayat {{ $h->nama }}?">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="lb-card">
        <div class="fw-bold mb-1"><i class="bi bi-type text-primary me-1"></i> Label Kolom Tanda Tangan</div>
        <div class="small text-muted mb-3">Judul di atas tiap kolom TTD. Kosongkan untuk kembali ke bawaan. Gunakan baris baru untuk dua baris.</div>
        <form method="POST" action="{{ route('admin.penandatangan.label') }}">
            @csrf
            <div class="row g-3">
                @foreach (['oppe' => 'Cetak OPPE', 'logbook' => 'Rekap Logbook'] as $grup => $judul)
                    <div class="col-md-6">
                        <div class="small fw-semibold mb-2">{{ $judul }}</div>
                        @foreach ([1, 2, 3] as $i)
                            @php $k = "$grup.ttd$i"; @endphp
                            <div class="input-group input-group-sm mb-2">
                                <span class="input-group-text" style="width:70px">TTD {{ $i }}</span>
                                <textarea name="label[{{ $k }}]" rows="1" class="form-control" placeholder="{{ $labelDefault[$k] }}">{{ $label[$k] !== $labelDefault[$k] ? $label[$k] : '' }}</textarea>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
            <div class="text-end"><button class="btn btn-sm btn-primary">Simpan Label</button></div>
        </form>
    </div>
@endsection

@push('scripts')
    @include('logbook._scripts')
@endpush
