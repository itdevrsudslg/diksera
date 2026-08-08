@extends('layouts.app')

@php
    $pageTitle = 'Kredensialing & Sertifikasi';
    $pageSubtitle = 'Kelola data STR, SIP, dan sertifikat kompetensi milik ' . $user->name;
@endphp

@section('title', 'Detail Sertifikat – Admin DIKSERA')

@push('styles')
    <style>
        /* Card Style */
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-soft);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 24px;
            height: 100%;
        }

        /* Section Title */
        .section-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--blue-main);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--blue-soft-2);
        }

        .section-title i { font-size: 18px; }

        /* Custom Table */
        .table-custom th {
            background-color: var(--blue-soft-2);
            color: var(--text-main);
            font-weight: 600;
            font-size: 12px;
            border-bottom: 2px solid #dbeafe;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            vertical-align: middle;
        }

        .table-custom td {
            vertical-align: middle;
            padding: 12px 16px;
            border-bottom: 1px solid var(--blue-soft-2);
            font-size: 13px;
            color: var(--text-main);
        }

        /* Status Badges */
        .badge-soft {
            padding: 6px 10px; border-radius: 6px; font-size: 11px;
            font-weight: 600; display: inline-flex; align-items: center; gap: 4px;
        }
        .badge-soft-success { background: #dcfce7; color: #166534; }
        .badge-soft-warning { background: #fef9c3; color: #854d0e; }
        .badge-soft-danger { background: #fee2e2; color: #991b1b; }
        .badge-soft-secondary { background: #f1f5f9; color: #475569; }

        /* Buttons */
        .btn-file {
            font-size: 11px; padding: 5px 10px; border-radius: 6px; display: inline-flex;
            align-items: center; gap: 5px; text-decoration: none; border: 1px solid var(--border-soft);
            background: #fff; color: var(--text-muted); transition: all 0.2s;
        }
        .btn-file:hover { border-color: var(--blue-main); color: var(--blue-main); background: var(--blue-soft); }

        .btn-action-edit {
            font-size: 11px; padding: 5px 10px; border-radius: 6px;
            display: inline-flex; align-items: center; gap: 5px; border: none;
            background: #fef3c7; color: #d97706; transition: all 0.2s;
        }
        .btn-action-edit:hover { background: #f59e0b; color: #fff; }

        .btn-verify {
            border: none; border-radius: 8px; padding: 6px 10px; font-size: 11px;
            font-weight: 600; display: inline-flex; align-items: center;
            justify-content: center; gap: 6px; transition: all 0.2s ease; cursor: pointer; line-height: 1;
        }
        .btn-verify-success { background: #dcfce7; color: #166534; }
        .btn-verify-success:hover { background: #166534; color: #ffffff; }
        .btn-verify-danger { background: #fee2e2; color: #991b1b; }
        .btn-verify-danger:hover { background: #991b1b; color: #ffffff; }
        .btn-verify-secondary { background: #f1f5f9; color: #475569; }
        .btn-verify-secondary:hover { background: #475569; color: #ffffff; }

        .verify-actions { display: flex; gap: 6px; margin-top: 8px; }
    </style>
@endpush

@section('content')

    {{-- Top Navigation --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 px-3 rounded-pill mb-2">
                {{ $user->name }}
            </span>
        </div>
        <a href="{{ route('admin.perawat.show', $user->id) }}" class="btn btn-sm btn-outline-secondary px-3" style="border-radius: 8px;">
            <i class="bi bi-arrow-left"></i> Kembali ke Profil
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- ============================== 1. STR ============================== --}}
        <div class="col-12">
            <div class="content-card">
                <div class="section-title"><i class="bi bi-card-heading text-success"></i> Surat Tanda Registrasi (STR)</div>
                <div class="table-responsive">
                    <table class="table table-custom table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nomor Registrasi</th>
                                <th>Periode Berlaku</th>
                                <th>Status Berkas</th>
                                <th class="text-end">Dokumen</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($str as $item)
                                @php
                                    $now = \Carbon\Carbon::now();
                                    $expired = $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired) : null;
                                    
                                    if ($expired) {
                                        $daysLeft = $now->diffInDays($expired, false);
                                        if ($daysLeft < 0) {
                                            $badgeClass = 'badge-soft-danger'; $icon = 'bi-x-circle'; $text = 'Expired (' . abs(intval($daysLeft)) . ' hari)';
                                        } elseif ($daysLeft <= 90) {
                                            $badgeClass = 'badge-soft-warning'; $icon = 'bi-exclamation-circle'; $text = 'Exp: ' . intval($daysLeft) . ' hari lagi';
                                        } else {
                                            $badgeClass = 'badge-soft-success'; $icon = 'bi-check-circle'; $text = 'Aktif';
                                        }
                                    } else {
                                        $badgeClass = 'badge-soft-secondary'; $icon = 'bi-dash-circle'; $text = 'Non-Expired'; $daysLeft = 1;
                                    }
                                @endphp
                                <tr>
                                    <td class="font-monospace fw-bold">{{ $item->nomor ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex flex-column" style="font-size: 11px;">
                                            <span class="text-muted">Mulai: {{ $item->tgl_terbit ? \Carbon\Carbon::parse($item->tgl_terbit)->format('d M Y') : '-' }}</span>
                                            <span class="{{ $daysLeft < 0 ? 'text-danger fw-bold' : 'text-dark' }}">Akhir: {{ $item->tgl_expired ? $expired->format('d M Y') : '-' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-soft {{ $badgeClass }} mb-2 d-inline-flex"><i class="bi {{ $icon }}"></i> {{ $text }}</span>
                                        <div class="border-top border-light pt-2 mt-1">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="text-muted" style="font-size: 10px;">Verifikasi:</span>
                                                <span class="badge-soft badge-soft-secondary"><span class="fw-bold text-uppercase">{{ $item->kelayakan ?? 'pending' }}</span></span>
                                            </div>
                                            <div class="verify-actions">
                                                @if ($item->kelayakan == 'pending' || is_null($item->kelayakan))
                                                    <form action="{{ route('admin.perawat.verifikasi.kelayakan') }}" method="POST">
                                                        @csrf <input type="hidden" name="id" value="{{ $item->id }}"> <input type="hidden" name="tipe" value="str"> <input type="hidden" name="kelayakan" value="layak">
                                                        <button type="submit" class="btn-verify btn-verify-success" title="Set Layak"><i class="bi bi-check-lg"></i></button>
                                                    </form>
                                                    <form action="{{ route('admin.perawat.verifikasi.kelayakan') }}" method="POST">
                                                        @csrf <input type="hidden" name="id" value="{{ $item->id }}"> <input type="hidden" name="tipe" value="str"> <input type="hidden" name="kelayakan" value="tidak_layak">
                                                        <button type="submit" class="btn-verify btn-verify-danger" title="Set Tidak Layak"><i class="bi bi-x-lg"></i></button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.perawat.verifikasi.kelayakan') }}" method="POST">
                                                        @csrf <input type="hidden" name="id" value="{{ $item->id }}"> <input type="hidden" name="tipe" value="str"> <input type="hidden" name="kelayakan" value="pending">
                                                        <button type="submit" class="btn-verify btn-verify-secondary" title="Reset ke Pending"><i class="bi bi-arrow-counterclockwise"></i></button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        @if ($item->file_path) <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="btn-file"><i class="bi bi-file-earmark-pdf text-danger"></i> Lihat</a>
                                        @else <span class="text-muted small opacity-50">Kosong</span> @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn-action-edit" data-bs-toggle="modal" data-bs-target="#editStr-{{$item->id}}"><i class="bi bi-pencil-square"></i> Edit</button>
                                    </td>
                                </tr>
                                {{-- Modal Edit STR --}}
                                <div class="modal fade" id="editStr-{{$item->id}}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.perawat.sertifikat.update_str', $item->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf @method('PUT')
                                                <div class="modal-header"><h5 class="modal-title fs-6 fw-bold">Edit STR</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                <div class="modal-body">
                                                    <div class="mb-3"><label class="form-label small fw-bold">Nomor STR</label><input type="text" name="nomor" class="form-control form-control-sm" value="{{ $item->nomor }}"></div>
                                                    <div class="row">
                                                        <div class="col-6 mb-3"><label class="form-label small fw-bold">Tgl Terbit</label><input type="date" name="tgl_terbit" class="form-control form-control-sm" value="{{ $item->tgl_terbit ? \Carbon\Carbon::parse($item->tgl_terbit)->format('Y-m-d') : '' }}"></div>
                                                        <div class="col-6 mb-3"><label class="form-label small fw-bold">Tgl Expired</label><input type="date" name="tgl_expired" class="form-control form-control-sm" value="{{ $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired)->format('Y-m-d') : '' }}"></div>
                                                    </div>
                                                    <div class="mb-3"><label class="form-label small fw-bold">Ganti Dokumen <span class="text-muted fw-normal">(opsional)</span></label><input type="file" name="dokumen" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png"></div>
                                                </div>
                                                <div class="modal-footer"><button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-sm btn-primary">Simpan Perubahan</button></div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted small fst-italic">Tidak ada data STR.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============================== 2. SIP ============================== --}}
        <div class="col-12">
            <div class="content-card">
                <div class="section-title"><i class="bi bi-file-earmark-medical text-primary"></i> Surat Izin Praktik (SIP)</div>
                <div class="table-responsive">
                    <table class="table table-custom table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nomor SIP</th>
                                <th>Periode Berlaku</th>
                                <th>Status Berkas</th>
                                <th class="text-end">Dokumen</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sip as $item)
                                @php
                                    $now = \Carbon\Carbon::now(); $expired = $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired) : null;
                                    if ($expired) {
                                        $daysLeft = $now->diffInDays($expired, false);
                                        if ($daysLeft < 0) { $badgeClass = 'badge-soft-danger'; $icon = 'bi-x-circle'; $text = 'Expired (' . abs(intval($daysLeft)) . ' hari)'; } 
                                        elseif ($daysLeft <= 90) { $badgeClass = 'badge-soft-warning'; $icon = 'bi-exclamation-circle'; $text = 'Exp: ' . intval($daysLeft) . ' hari lagi'; } 
                                        else { $badgeClass = 'badge-soft-success'; $icon = 'bi-check-circle'; $text = 'Aktif'; }
                                    } else { $badgeClass = 'badge-soft-secondary'; $icon = 'bi-dash-circle'; $text = 'Non-Expired'; $daysLeft = 1; }
                                @endphp
                                <tr>
                                    <td class="font-monospace fw-bold">{{ $item->nomor ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex flex-column" style="font-size: 11px;">
                                            <span class="text-muted">Mulai: {{ $item->tgl_terbit ? \Carbon\Carbon::parse($item->tgl_terbit)->format('d M Y') : '-' }}</span>
                                            <span class="{{ $daysLeft < 0 ? 'text-danger fw-bold' : 'text-dark' }}">Akhir: {{ $item->tgl_expired ? $expired->format('d M Y') : '-' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-soft {{ $badgeClass }} mb-2 d-inline-flex"><i class="bi {{ $icon }}"></i> {{ $text }}</span>
                                        <div class="border-top border-light pt-2 mt-1">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="text-muted" style="font-size: 10px;">Verifikasi:</span>
                                                <span class="badge-soft badge-soft-secondary"><span class="fw-bold text-uppercase">{{ $item->kelayakan ?? 'pending' }}</span></span>
                                            </div>
                                            <div class="verify-actions">
                                                @if ($item->kelayakan == 'pending' || is_null($item->kelayakan))
                                                    <form action="{{ route('admin.perawat.verifikasi.kelayakan') }}" method="POST">
                                                        @csrf <input type="hidden" name="id" value="{{ $item->id }}"> <input type="hidden" name="tipe" value="sip"> <input type="hidden" name="kelayakan" value="layak">
                                                        <button type="submit" class="btn-verify btn-verify-success" title="Set Layak"><i class="bi bi-check-lg"></i></button>
                                                    </form>
                                                    <form action="{{ route('admin.perawat.verifikasi.kelayakan') }}" method="POST">
                                                        @csrf <input type="hidden" name="id" value="{{ $item->id }}"> <input type="hidden" name="tipe" value="sip"> <input type="hidden" name="kelayakan" value="tidak_layak">
                                                        <button type="submit" class="btn-verify btn-verify-danger" title="Set Tidak Layak"><i class="bi bi-x-lg"></i></button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.perawat.verifikasi.kelayakan') }}" method="POST">
                                                        @csrf <input type="hidden" name="id" value="{{ $item->id }}"> <input type="hidden" name="tipe" value="sip"> <input type="hidden" name="kelayakan" value="pending">
                                                        <button type="submit" class="btn-verify btn-verify-secondary" title="Reset ke Pending"><i class="bi bi-arrow-counterclockwise"></i></button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        @if ($item->file_path) <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="btn-file"><i class="bi bi-file-earmark-pdf text-danger"></i> Lihat</a>
                                        @else <span class="text-muted small opacity-50">Kosong</span> @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn-action-edit" data-bs-toggle="modal" data-bs-target="#editSip-{{$item->id}}"><i class="bi bi-pencil-square"></i> Edit</button>
                                    </td>
                                </tr>
                                {{-- Modal Edit SIP --}}
                                <div class="modal fade" id="editSip-{{$item->id}}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.perawat.sertifikat.update_sip', $item->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf @method('PUT')
                                                <div class="modal-header"><h5 class="modal-title fs-6 fw-bold">Edit SIP</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                <div class="modal-body">
                                                    <div class="mb-3"><label class="form-label small fw-bold">Nomor SIP</label><input type="text" name="nomor" class="form-control form-control-sm" value="{{ $item->nomor }}"></div>
                                                    <div class="row">
                                                        <div class="col-6 mb-3"><label class="form-label small fw-bold">Tgl Terbit</label><input type="date" name="tgl_terbit" class="form-control form-control-sm" value="{{ $item->tgl_terbit ? \Carbon\Carbon::parse($item->tgl_terbit)->format('Y-m-d') : '' }}"></div>
                                                        <div class="col-6 mb-3"><label class="form-label small fw-bold">Tgl Expired</label><input type="date" name="tgl_expired" class="form-control form-control-sm" value="{{ $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired)->format('Y-m-d') : '' }}"></div>
                                                    </div>
                                                    <div class="mb-3"><label class="form-label small fw-bold">Ganti Dokumen <span class="text-muted fw-normal">(opsional)</span></label><input type="file" name="dokumen" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png"></div>
                                                </div>
                                                <div class="modal-footer"><button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-sm btn-primary">Simpan Perubahan</button></div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted small fst-italic">Tidak ada data SIP.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============================== 3. LISENSI ============================== --}}
        <div class="col-12">
            <div class="content-card">
                <div class="section-title"><i class="bi bi-award text-warning"></i> Kredensialing Lainnya</div>
                <div class="table-responsive">
                    <table class="table table-custom table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nomor</th>
                                <th>Nama Sertifikat & Penerbit</th>
                                <th>Periode Berlaku</th>
                                <th>Status Berkas</th>
                                <th class="text-end">Dokumen</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lisensi as $item)
                                @php
                                    $now = \Carbon\Carbon::now(); $expired = $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired) : null;
                                    if ($expired) {
                                        $daysLeft = $now->diffInDays($expired, false);
                                        if ($daysLeft < 0) { $badgeClass = 'badge-soft-danger'; $icon = 'bi-x-circle'; $text = 'Expired (' . abs(intval($daysLeft)) . ' hari)'; } 
                                        elseif ($daysLeft <= 90) { $badgeClass = 'badge-soft-warning'; $icon = 'bi-exclamation-circle'; $text = 'Exp: ' . intval($daysLeft) . ' hari lagi'; } 
                                        else { $badgeClass = 'badge-soft-success'; $icon = 'bi-check-circle'; $text = 'Aktif'; }
                                    } else { $badgeClass = 'badge-soft-secondary'; $icon = 'bi-dash-circle'; $text = 'Non-Expired'; $daysLeft = 1; }
                                @endphp
                                <tr>
                                    <td class="font-monospace fw-bold">{{ $item->nomor ?? '-' }}</td>
                                    <td><div class="fw-bold">{{ $item->nama }}</div><div class="text-muted" style="font-size: 11px;">{{ $item->lembaga }}</div></td>
                                    <td>
                                        <div class="d-flex flex-column" style="font-size: 11px;">
                                            <span class="text-muted">Mulai: {{ $item->tgl_terbit ? \Carbon\Carbon::parse($item->tgl_terbit)->format('d M Y') : '-' }}</span>
                                            <span class="{{ $daysLeft < 0 ? 'text-danger fw-bold' : 'text-dark' }}">Akhir: {{ $item->tgl_expired ? $expired->format('d M Y') : '-' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-soft {{ $badgeClass }} mb-2 d-inline-flex"><i class="bi {{ $icon }}"></i> {{ $text }}</span>
                                        <div class="border-top border-light pt-2 mt-1">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="text-muted" style="font-size: 10px;">Verifikasi:</span>
                                                <span class="badge-soft badge-soft-secondary"><span class="fw-bold text-uppercase">{{ $item->kelayakan ?? 'pending' }}</span></span>
                                            </div>
                                            <div class="verify-actions">
                                                @if ($item->kelayakan == 'pending' || is_null($item->kelayakan))
                                                    <form action="{{ route('admin.perawat.verifikasi.kelayakan') }}" method="POST">
                                                        @csrf <input type="hidden" name="id" value="{{ $item->id }}"> <input type="hidden" name="tipe" value="lisensi"> <input type="hidden" name="kelayakan" value="layak">
                                                        <button type="submit" class="btn-verify btn-verify-success" title="Set Layak"><i class="bi bi-check-lg"></i></button>
                                                    </form>
                                                    <form action="{{ route('admin.perawat.verifikasi.kelayakan') }}" method="POST">
                                                        @csrf <input type="hidden" name="id" value="{{ $item->id }}"> <input type="hidden" name="tipe" value="lisensi"> <input type="hidden" name="kelayakan" value="tidak_layak">
                                                        <button type="submit" class="btn-verify btn-verify-danger" title="Set Tidak Layak"><i class="bi bi-x-lg"></i></button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.perawat.verifikasi.kelayakan') }}" method="POST">
                                                        @csrf <input type="hidden" name="id" value="{{ $item->id }}"> <input type="hidden" name="tipe" value="lisensi"> <input type="hidden" name="kelayakan" value="pending">
                                                        <button type="submit" class="btn-verify btn-verify-secondary" title="Reset ke Pending"><i class="bi bi-arrow-counterclockwise"></i></button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        @if ($item->file_path) <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="btn-file"><i class="bi bi-file-earmark-pdf text-danger"></i> Lihat</a>
                                        @else <span class="text-muted small opacity-50">Kosong</span> @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn-action-edit" data-bs-toggle="modal" data-bs-target="#editLisensi-{{$item->id}}"><i class="bi bi-pencil-square"></i> Edit</button>
                                    </td>
                                </tr>
                                {{-- Modal Edit Lisensi --}}
                                <div class="modal fade" id="editLisensi-{{$item->id}}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.perawat.sertifikat.update_lisensi', $item->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf @method('PUT')
                                                <div class="modal-header"><h5 class="modal-title fs-6 fw-bold">Edit Lisensi</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                <div class="modal-body">
                                                    <div class="mb-3"><label class="form-label small fw-bold">Nomor</label><input type="text" name="nomor" class="form-control form-control-sm" value="{{ $item->nomor }}"></div>
                                                    <div class="mb-3"><label class="form-label small fw-bold">Nama Sertifikat</label><input type="text" name="nama" class="form-control form-control-sm" value="{{ $item->nama }}"></div>
                                                    <div class="mb-3"><label class="form-label small fw-bold">Lembaga Penerbit</label><input type="text" name="lembaga" class="form-control form-control-sm" value="{{ $item->lembaga }}"></div>
                                                    <div class="row">
                                                        <div class="col-6 mb-3"><label class="form-label small fw-bold">Tgl Terbit</label><input type="date" name="tgl_terbit" class="form-control form-control-sm" value="{{ $item->tgl_terbit ? \Carbon\Carbon::parse($item->tgl_terbit)->format('Y-m-d') : '' }}"></div>
                                                        <div class="col-6 mb-3"><label class="form-label small fw-bold">Tgl Expired</label><input type="date" name="tgl_expired" class="form-control form-control-sm" value="{{ $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired)->format('Y-m-d') : '' }}"></div>
                                                    </div>
                                                    <div class="mb-3"><label class="form-label small fw-bold">Ganti Dokumen <span class="text-muted fw-normal">(opsional)</span></label><input type="file" name="dokumen" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png"></div>
                                                </div>
                                                <div class="modal-footer"><button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-sm btn-primary">Simpan Perubahan</button></div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr><td colspan="6" class="text-center py-4 text-muted small fst-italic">Tidak ada data Lisensi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============================== 4. DATA TAMBAHAN ============================== --}}
        <div class="col-12">
            <div class="content-card">
                <div class="section-title"><i class="bi bi-folder2-open text-info"></i> Data Tambahan</div>
                <div class="table-responsive">
                    <table class="table table-custom table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Jenis & Nomor</th>
                                <th>Nama Dokumen & Penerbit</th>
                                <th>Periode Berlaku</th>
                                <th>Status Berkas</th>
                                <th class="text-end">Dokumen</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dataTambahan as $item)
                                @php
                                    $now = \Carbon\Carbon::now(); 
                                    $expired = $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired) : null;
                                    
                                    if ($item->is_lifetime) {
                                        $badgeClass = 'badge-soft-secondary'; $icon = 'bi-infinity'; $text = 'Seumur Hidup'; $daysLeft = 1;
                                    } elseif ($expired) {
                                        $daysLeft = $now->diffInDays($expired, false);
                                        if ($daysLeft < 0) { $badgeClass = 'badge-soft-danger'; $icon = 'bi-x-circle'; $text = 'Expired (' . abs(intval($daysLeft)) . ' hari)'; } 
                                        elseif ($daysLeft <= 90) { $badgeClass = 'badge-soft-warning'; $icon = 'bi-exclamation-circle'; $text = 'Exp: ' . intval($daysLeft) . ' hari lagi'; } 
                                        else { $badgeClass = 'badge-soft-success'; $icon = 'bi-check-circle'; $text = 'Aktif'; }
                                    } else {
                                        $badgeClass = 'badge-soft-secondary'; $icon = 'bi-dash-circle'; $text = 'Non-Expired'; $daysLeft = 1;
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 d-block mb-1 text-start" style="width: fit-content;">{{ strtoupper($item->jenis ?? 'Lainnya') }}</span>
                                        <span class="font-monospace fw-bold">{{ $item->nomor ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $item->nama }}</div>
                                        <div class="text-muted" style="font-size: 11px;">{{ $item->lembaga }}</div>
                                        @if($item->skp) <div class="badge bg-info mt-1">{{ $item->skp }} SKP</div> @endif
                                    </td>
                                    <td>
                                        @if($item->is_lifetime)
                                            <span class="text-muted fst-italic" style="font-size: 11px;">Berlaku seumur hidup</span>
                                        @else
                                            <div class="d-flex flex-column" style="font-size: 11px;">
                                                <span class="text-muted">Mulai: {{ $item->tgl_terbit ? \Carbon\Carbon::parse($item->tgl_terbit)->format('d M Y') : '-' }}</span>
                                                <span class="{{ $daysLeft < 0 ? 'text-danger fw-bold' : 'text-dark' }}">Akhir: {{ $item->tgl_expired ? $expired->format('d M Y') : '-' }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge-soft {{ $badgeClass }} mb-2 d-inline-flex"><i class="bi {{ $icon }}"></i> {{ $text }}</span>
                                        <div class="border-top border-light pt-2 mt-1">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="text-muted" style="font-size: 10px;">Verifikasi:</span>
                                                <span class="badge-soft badge-soft-secondary"><span class="fw-bold text-uppercase">{{ $item->kelayakan ?? 'pending' }}</span></span>
                                            </div>
                                            <div class="verify-actions">
                                                @if ($item->kelayakan == 'pending' || is_null($item->kelayakan))
                                                    <form action="{{ route('admin.perawat.verifikasi.kelayakan') }}" method="POST">
                                                        @csrf <input type="hidden" name="id" value="{{ $item->id }}"> <input type="hidden" name="tipe" value="tambahan"> <input type="hidden" name="kelayakan" value="layak">
                                                        <button type="submit" class="btn-verify btn-verify-success" title="Set Layak"><i class="bi bi-check-lg"></i></button>
                                                    </form>
                                                    <form action="{{ route('admin.perawat.verifikasi.kelayakan') }}" method="POST">
                                                        @csrf <input type="hidden" name="id" value="{{ $item->id }}"> <input type="hidden" name="tipe" value="tambahan"> <input type="hidden" name="kelayakan" value="tidak_layak">
                                                        <button type="submit" class="btn-verify btn-verify-danger" title="Set Tidak Layak"><i class="bi bi-x-lg"></i></button>
                                                    </form>
                                                    {{-- Tombol Khusus Approve Lifetime --}}
                                                    @if ($item->is_lifetime && !$item->lifetime_approved)
                                                        <form action="{{ route('admin.perawat.verifikasi.kelayakan') }}" method="POST">
                                                            @csrf <input type="hidden" name="id" value="{{ $item->id }}"> <input type="hidden" name="tipe" value="tambahan"> <input type="hidden" name="kelayakan" value="layak"> <input type="hidden" name="approve_lifetime" value="1">
                                                            <button type="submit" class="btn-verify btn-verify-success" title="Approve Seumur Hidup"><i class="bi bi-award"></i></button>
                                                        </form>
                                                    @endif
                                                @else
                                                    <form action="{{ route('admin.perawat.verifikasi.kelayakan') }}" method="POST">
                                                        @csrf <input type="hidden" name="id" value="{{ $item->id }}"> <input type="hidden" name="tipe" value="tambahan"> <input type="hidden" name="kelayakan" value="pending">
                                                        <button type="submit" class="btn-verify btn-verify-secondary" title="Reset ke Pending"><i class="bi bi-arrow-counterclockwise"></i></button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        @if ($item->file_path) <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="btn-file"><i class="bi bi-file-earmark-pdf text-danger"></i> Lihat</a>
                                        @else <span class="text-muted small opacity-50">Kosong</span> @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn-action-edit" data-bs-toggle="modal" data-bs-target="#editTambahan-{{$item->id}}"><i class="bi bi-pencil-square"></i> Edit</button>
                                    </td>
                                </tr>
                                {{-- Modal Edit Data Tambahan --}}
                                <div class="modal fade" id="editTambahan-{{$item->id}}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.perawat.sertifikat.update_tambahan', $item->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf @method('PUT')
                                                <div class="modal-header"><h5 class="modal-title fs-6 fw-bold">Edit Data Tambahan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                <div class="modal-body">
                                                    <div class="mb-3"><label class="form-label small fw-bold">Nomor Dokumen</label><input type="text" name="nomor" class="form-control form-control-sm" value="{{ $item->nomor }}"></div>
                                                    <div class="mb-3"><label class="form-label small fw-bold">Nama Dokumen</label><input type="text" name="nama" class="form-control form-control-sm" value="{{ $item->nama }}"></div>
                                                    <div class="mb-3"><label class="form-label small fw-bold">Lembaga Penerbit</label><input type="text" name="lembaga" class="form-control form-control-sm" value="{{ $item->lembaga }}"></div>
                                                    <div class="row">
                                                        <div class="col-6 mb-3"><label class="form-label small fw-bold">Tgl Terbit</label><input type="date" name="tgl_terbit" class="form-control form-control-sm" value="{{ $item->tgl_terbit ? \Carbon\Carbon::parse($item->tgl_terbit)->format('Y-m-d') : '' }}"></div>
                                                        <div class="col-6 mb-3"><label class="form-label small fw-bold">Tgl Expired</label><input type="date" name="tgl_expired" class="form-control form-control-sm" value="{{ $item->tgl_expired ? \Carbon\Carbon::parse($item->tgl_expired)->format('Y-m-d') : '' }}"></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-6 mb-3"><label class="form-label small fw-bold">Jumlah SKP</label><input type="number" name="skp" class="form-control form-control-sm" value="{{ $item->skp }}"></div>
                                                        <div class="col-6 mb-3">
                                                            <label class="form-label small fw-bold d-block">Status Berlaku</label>
                                                            <div class="form-check mt-2">
                                                                <input class="form-check-input" type="checkbox" name="is_lifetime" value="1" id="lifetime-{{$item->id}}" {{ $item->is_lifetime ? 'checked' : '' }}>
                                                                <label class="form-check-label small" for="lifetime-{{$item->id}}">Seumur Hidup</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3"><label class="form-label small fw-bold">Ganti Dokumen <span class="text-muted fw-normal">(opsional)</span></label><input type="file" name="dokumen" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png"></div>
                                                </div>
                                                <div class="modal-footer"><button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-sm btn-primary">Simpan Perubahan</button></div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr><td colspan="6" class="text-center py-4 text-muted small fst-italic">Tidak ada data tambahan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection