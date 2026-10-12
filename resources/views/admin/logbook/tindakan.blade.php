@extends('layouts.app')

@php
    $pageTitle = 'Master Tindakan Logbook';
    $pageSubtitle = 'Daftar jenis tindakan dari SIMRS Khanza + tambahan manual';
@endphp

@section('title', 'Master Tindakan – Admin DIKSERA')

@push('styles')
    @include('logbook._styles')
@endpush

@section('content')
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="lb-card h-100">
                <div class="fw-bold mb-1"><i class="bi bi-database text-primary me-1"></i> Sinkron dari SIMRS Khanza</div>
                <div class="small text-muted mb-3">
                    @if ($khanza)
                        Total {{ $total }} tindakan. Sinkron terakhir: {{ $terakhirSinkron ? \Carbon\Carbon::parse($terakhirSinkron)->translatedFormat('d M Y H:i') : 'belum pernah' }}.
                    @else
                        Koneksi Khanza belum diatur. Isi <code>KHANZA_DB_HOST</code>, <code>KHANZA_DB_DATABASE</code>, <code>KHANZA_DB_USERNAME</code>, <code>KHANZA_DB_PASSWORD</code> di file <code>.env</code> server.
                    @endif
                </div>
                <form method="POST" action="{{ route('admin.logbook.tindakan.sinkron') }}" class="js-confirm" data-confirm="Ambil daftar tindakan terbaru dari Khanza?">
                    @csrf
                    <button class="btn btn-primary btn-sm" {{ $khanza ? '' : 'disabled' }}><i class="bi bi-arrow-repeat"></i> Sinkron Sekarang</button>
                </form>
            </div>
        </div>
        <div class="col-md-6">
            <div class="lb-card h-100">
                <div class="fw-bold mb-3"><i class="bi bi-plus-circle text-primary me-1"></i> Tambah Tindakan Manual</div>
                <form method="POST" action="{{ route('admin.logbook.tindakan.simpan') }}" class="d-flex flex-wrap gap-2">
                    @csrf
                    <input name="kode" class="form-control form-control-sm" style="max-width:120px" placeholder="Kode" required>
                    <input name="nama" class="form-control form-control-sm flex-grow-1" style="min-width:180px" placeholder="Nama tindakan" required>
                    <button class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> Tambah</button>
                </form>
            </div>
        </div>
    </div>

    <div class="lb-card">
        <form method="GET" class="d-flex flex-wrap gap-2 mb-3">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" style="max-width:260px" placeholder="Cari kode / nama…">
            <select name="kategori" class="form-select form-select-sm" style="width:auto">
                <option value="">Semua kategori</option>
                @foreach (['ralan' => 'Rawat Jalan', 'ranap' => 'Rawat Inap', 'manual' => 'Manual'] as $v => $l)
                    <option value="{{ $v }}" {{ request('kategori') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
            <select name="aktif" class="form-select form-select-sm" style="width:auto">
                <option value="1">Aktif</option>
                <option value="0" {{ request('aktif') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button class="btn btn-sm btn-primary">Filter</button>
        </form>

        <div class="table-responsive">
            <table class="table lb-table align-middle">
                <thead><tr><th>Kode</th><th>Nama Tindakan</th><th>Kategori</th><th>Sumber</th><th class="text-end">Status</th></tr></thead>
                <tbody>
                    @forelse ($tindakans as $t)
                        <tr>
                            <td class="fw-semibold">{{ $t->kode }}</td>
                            <td>{{ $t->nama }}</td>
                            <td><span class="lb-pill info">{{ strtoupper($t->kategori) }}</span></td>
                            <td class="small text-muted">{{ ucfirst($t->sumber) }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.logbook.tindakan.toggle', $t->id) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm py-0 {{ $t->aktif ? 'btn-outline-success' : 'btn-outline-secondary' }}">
                                        {{ $t->aktif ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="lb-empty">Belum ada tindakan. Lakukan sinkron Khanza atau tambah manual.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $tindakans->links('vendor.pagination.diksera') }}
    </div>
@endsection

@push('scripts')
    @include('logbook._scripts')
@endpush
