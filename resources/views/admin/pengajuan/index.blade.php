@extends('layouts.app')

@section('title', 'Admin - Approval Perpanjangan')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <style>
        :root {
            --primary-blue: #2563eb;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bg-light: #f8fafc;
            --border-color: #e2e8f0;
            --input-height: 38px;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-size: 13.5px;
            padding-bottom: 100px;
        }

        /* --- Page Header --- */
        .page-header {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .page-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .page-subtitle {
            font-size: 0.85rem;
            color: var(--text-gray);
            margin-top: 2px;
            margin-bottom: 0;
        }

        /* --- Filter Card --- */
        .filter-card {
            background: white;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            padding: 16px;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        .filter-label {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--text-gray);
            text-transform: uppercase;
            margin-bottom: 4px;
            display: block;
        }

        .form-control {
            height: var(--input-height);
            border-radius: 6px;
            font-size: 0.9rem;
            border-color: #cbd5e1;
        }

        /* --- Choices JS Fix --- */
        .choices__inner {
            min-height: var(--input-height) !important;
            padding: 2px 8px !important;
            border-radius: 6px !important;
            background: white;
            border: 1px solid #cbd5e1;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }

        .choices {
            margin-bottom: 0;
        }

        .choices__list--single {
            padding: 0;
        }

        /* --- Table Styling --- */
        .table-card {
            background: white;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            overflow: hidden;
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.05);
        }

        .table-custom th {
            background: #f8fafc;
            color: var(--text-gray);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            padding: 10px 16px;
            border-bottom: 1px solid var(--border-color);
        }

        .table-custom td {
            padding: 10px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .table-custom tr:last-child td {
            border-bottom: none;
        }

        .table-custom tbody tr:hover {
            background-color: #f8fafc;
        }

        /* --- Components --- */
        .avatar-initial {
            width: 30px;
            height: 30px;
            background-color: #eff6ff;
            color: var(--primary-blue);
            font-weight: 700;
            font-size: 11px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dbeafe;
            flex-shrink: 0;
            margin-right: 10px;
        }

        /* Status Badge */
        .status-badge {
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .st-pending {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
        }

        .st-info {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
        }

        .st-success {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #dcfce7;
        }

        .st-danger {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fee2e2;
        }

        .st-purple {
            background: #f3e8ff;
            color: #7e22ce;
            border: 1px solid #e9d5ff;
        }

        /* Action Buttons */
        .btn-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            transition: all 0.2s;
            font-size: 0.85rem;
            cursor: pointer;
        }

        .btn-view {
            color: #64748b;
            background: transparent;
            border: 1px solid #e2e8f0;
        }

        .btn-view:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .btn-act-check {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }

        .btn-act-check:hover {
            background: #15803d;
            color: white;
            border-color: #15803d;
        }

        .btn-act-x {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .btn-act-x:hover {
            background: #991b1b;
            color: white;
            border-color: #991b1b;
        }

        .btn-act-blue {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .btn-act-blue:hover {
            background: #1e40af;
            color: white;
        }

        /* Style tombol copy */
        .btn-act-copy {
            background: #ffedd5;
            color: #9a3412;
            border: 1px solid #fed7aa;
        }

        .btn-act-copy:hover {
            background: #9a3412;
            color: white;
        }

        /* Floating Bulk Action Bar */
        .bulk-action-bar {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(150%);
            background: #1e293b;
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.25);
            z-index: 1050;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            max-width: 95%;
            width: max-content;
        }

        .bulk-action-bar.active {
            transform: translateX(-50%) translateY(0);
        }

        .bulk-separator {
            width: 1px;
            height: 20px;
            background: #475569;
            margin: 0 5px;
        }
    </style>
@endpush

@section('content')
    <div class=" py-4">

        {{-- HEADER --}}
        <div class="page-header">
            <div>
                <h1 class="page-title">Approval Pengajuan</h1>
                <p class="page-subtitle">Manajemen validasi lisensi baru dan perpanjangan.</p>
            </div>
{{-- BULK ACTIONS BAR --}}
    <div class="bulk-action-bar" id="bulkActionBar">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white text-dark rounded-pill px-2 py-1" id="selectedCountBadge">0</span>
            <span class="small fw-bold">Terpilih</span>
        </div>
        <div class="bulk-separator"></div>
        <div class="d-flex gap-2">
            {{-- Bulk Approve Pending (Modal Tanggal) --}}
            <button type="button" class="btn btn-success btn-sm fw-bold rounded-pill px-3" onclick="openApprovalModal('bulk')">
                <i class="bi bi-check-lg me-1"></i> Acc Pending
            </button>

            {{-- Bulk Approve Score --}}
            <form id="formBulkScore" action="{{ route('admin.pengajuan.bulk_approve_score') }}" method="POST">
                @csrf <div id="bulkScoreInputs"></div>
                <button type="button" class="btn btn-warning text-dark btn-sm fw-bold rounded-pill px-3" onclick="submitBulk('formBulkScore', 'Verifikasi nilai?')">
                    <i class="bi bi-check-all me-1"></i> Acc Nilai
                </button>
            </form>

            {{-- Bulk Approve Interview (Modal Jadwal) --}}
            <button type="button" class="btn btn-info text-white btn-sm fw-bold rounded-pill px-3" onclick="openBulkInterviewModal()">
                <i class="bi bi-calendar-check me-1"></i> Acc Jadwal
            </button>

            {{-- TAMBAHAN: Bulk Hapus Pengajuan --}}
            <form id="formBulkDelete" action="{{ route('admin.pengajuan.bulk_destroy') }}" method="POST">
                @csrf 
                @method('DELETE')
                <div id="bulkDeleteInputs"></div>
                <button type="button" class="btn btn-danger btn-sm fw-bold rounded-pill px-3" onclick="submitBulk('formBulkDelete', 'Yakin ingin menghapus permanen data yang dipilih?', 'bulkDeleteInputs')">
                    <i class="bi bi-trash me-1"></i> Hapus Terpilih
                </button>
            </form>
        </div>
    </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success mb-4 rounded-3 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger mb-4 rounded-3 d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
            </div>
        @endif

        {{-- FILTER SECTION --}}
        <div class="filter-card">
            <form action="{{ route('admin.pengajuan.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label class="filter-label">Cari Data</label>
                        <input type="text" name="search" class="form-control" placeholder="Nama / Email..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-lg-2 col-md-2">
    <button type="submit" class="btn btn-primary w-100" style="height: var(--input-height);">
        <i class="bi bi-search"></i> Cari
    </button>
</div>
                    <div class="col-lg-2 col-md-3">
                        <label class="filter-label">Status</label>
                        <select name="status" id="choice-status">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="method_selected" {{ request('status') == 'method_selected' ? 'selected' : '' }}>Sedang Ujian</option>
                            <option value="exam_passed" {{ request('status') == 'exam_passed' ? 'selected' : '' }}>Lulus / Siap Wawancara</option>
                            <option value="interview_scheduled" {{ request('status') == 'interview_scheduled' ? 'selected' : '' }}>Jadwal Uji Skill</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <label class="filter-label">Jenis Sertifikat</label>
                        <select name="sertifikat" id="choice-sertifikat">
                            <option value="">Semua Jenis</option>
                            @foreach ($listSertifikat as $sertif)
                                <option value="{{ $sertif }}" {{ request('sertifikat') == $sertif ? 'selected' : '' }}>{{ $sertif }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="filter-label">Status Ujian</label>
                        <select name="ujian" id="choice-ujian">
                            <option value="">Semua</option>
                            <option value="sudah" {{ request('ujian') == 'sudah' ? 'selected' : '' }}>Sudah Ada Nilai</option>
                            <option value="belum" {{ request('ujian') == 'belum' ? 'selected' : '' }}>Belum Ujian</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABLE SECTION --}}
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">
                                <input type="checkbox" id="checkAll" class="form-check-input" style="cursor: pointer;">
                            </th>
                            <th>Perawat</th>
                            <th>Sertifikat</th>
                            <th>Status</th>
                            <th>Metode/Tipe</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuan as $item)
                            <tr>
                                <td class="text-center">
                             
    <input type="checkbox" name="ids[]" value="{{ $item->id }}" class="form-check-input check-item" style="cursor: pointer;">
</td>
                           

                                {{-- Kolom Perawat --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        @php
                                            $initials = collect(explode(' ', $item->user->name))
                                                ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                                ->take(2)
                                                ->join('');
                                        @endphp
                                        <div class="avatar-initial">{{ $initials }}</div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $item->user->name }}</div>
                                            <div class="text-muted small" style="font-size: 11px;">{{ $item->user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kolom Nama Sertifikat --}}
                                <td>
                                    <span class="text-dark fw-medium">{{ $item->lisensiLama->nama ?? '-' }}</span>
                                </td>

                                {{-- Kolom Status --}}
                                <td>
                                    @if ($item->status == 'pending')
                                        <span class="status-badge st-pending"><i class="bi bi-clock"></i> Menunggu</span>
                                    @elseif($item->status == 'method_selected')
                                        <span class="status-badge st-info"><i class="bi bi-pencil-square"></i> Sedang Ujian</span>
                                    @elseif($item->status == 'exam_passed')
                                        <span class="status-badge st-info">
                                            <i class="bi bi-check-circle"></i>
                                            {{ $item->metode == 'interview_only' ? 'Siap Uji Skill' : 'Lulus Ujian' }}
                                        </span>
                                    @elseif($item->status == 'interview_scheduled')
                                        <span class="status-badge st-warning"><i class="bi bi-calendar-event"></i>    {{ $item->metode == 'interview_only' ? 'Kredensial / Re Kredensial' : 'Uji Skill' }}</span>
                                    @elseif($item->status == 'completed')
                                        <span class="status-badge st-success"><i class="bi bi-check-all"></i> Selesai</span>
                                    @elseif($item->status == 'rejected')
                                        <span class="status-badge st-danger"><i class="bi bi-x-circle"></i> Ditolak</span>
                                    @endif
                                </td>

                                {{-- Kolom Metode --}}
                                <td>
                                    <span class="text-muted small">
                                        @if ($item->metode == 'new_submission')
                                            <span class="badge bg-info text-dark border border-info" style="font-size: 10px;">
                                                <i class="bi bi-star-fill me-1"></i> Lisensi Baru
                                            </span>
                                        @elseif ($item->metode == 'pg_only')
                                            Perpanjangan (Uji Kompetensi)
                                        @elseif($item->metode == 'interview_only')
                                            <span class="status-badge st-purple" style="font-size: 10px;">Perpanjangan (Wawancara)</span>
                                        @elseif($item->metode == 'pg_interview')
                                            Perpanjangan (Uji Kompetensi + Uji Skill)
                                        @else
                                            @if ($item->jenis_pengajuan == 'baru')
                                                <span class="text-info fw-bold">Baru (Auto Ujikom)</span>
                                            @else
                                                <span class="text-secondary fw-bold">Perpanjangan</span>
                                            @endif
                                        @endif
                                    </span>
                                </td>

                                {{-- Kolom Aksi --}}
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
{{-- TAMBAHKAN TOMBOL EDIT INI --}}
        <a href="{{ route('admin.pengajuan.edit', $item->id) }}" class="btn-icon" style="color: #d97706; border: 1px solid #fcd34d; background: #fffbeb;" data-bs-toggle="tooltip" title="Edit Manual">
            <i class="bi bi-pencil"></i>
        </a>
                                        {{-- 1. AKSI APPROVAL AWAL (Status: pending) --}}
                                        @if ($item->status == 'pending')
                                            <a href="{{ route('admin.pengajuan.show', $item->id) }}" class="btn-icon btn-view" title="Detail"><i class="bi bi-eye"></i></a>

                                       {{-- [MODAL TANGGAL SINGLE] --}}
@php
    // Ambil tanggal lahir dari relasi profile (jika ada)
    $tglLahir = $item->user->perawatProfile->tanggal_lahir ?? $item->user->profile->tanggal_lahir ?? '';
@endphp
<button type="button" class="btn-icon btn-act-check"
    onclick="openApprovalModal('single', {{ $item->id }}, '{{ $item->metode }}', '{{ $tglLahir }}')"
    title="Approve & Set Tanggal">
    <i class="bi bi-check-lg"></i>
</button>

                                            <form action="{{ route('admin.pengajuan.reject', $item->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn-icon btn-act-x" onclick="return confirm('Tolak pengajuan ini?')" title="Tolak">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>

                                        {{-- 2. AKSI APPROVE NILAI UJIAN --}}
                                        @elseif ($item->status == 'method_selected' && $item->user->examResult && $item->metode != 'interview_only')
                                            <a href="{{ route('admin.pengajuan.show', $item->id) }}" class="btn-icon btn-view" title="Detail"><i class="bi bi-eye"></i></a>
                                            <a href="{{ route('admin.pengajuan.approve_score', $item->id) }}" class="btn-icon btn-act-check" onclick="return confirm('Verifikasi Nilai?')" title="Acc Nilai"><i class="bi bi-check-all"></i></a>

                                        {{-- 3. AKSI JADWAL WAWANCARA --}}
                                        @elseif ($item->status == 'interview_scheduled' && $item->jadwalWawancara)
                                            @php $jadwal = $item->jadwalWawancara; @endphp

                                            @if ($jadwal->status == 'pending')
                                                <a href="{{ route('admin.pengajuan.show', $item->id) }}" class="btn-icon btn-view" title="Detail"><i class="bi bi-eye"></i></a>

                                                {{-- Edit Jadwal (Modal Inline) --}}
                                                <button type="button" class="btn-icon btn-act-check"
                                                    data-bs-toggle="modal" data-bs-target="#modalApproveJadwal{{ $item->id }}"
                                                    title="Verifikasi & Edit Jadwal">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                {{-- Reject Jadwal --}}
                                                <button type="button" class="btn-icon btn-act-x" data-bs-toggle="modal"
                                                    data-bs-target="#rejectModal{{ $jadwal->id }}" title="Tolak Jadwal">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>

                                                {{-- INLINE MODAL: Acc Jadwal --}}
                                                <div class="modal fade text-start" id="modalApproveJadwal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content border-0 shadow-lg rounded-4">
                                                            <form action="{{ route('admin.pengajuan_wawancara.approve', $item->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-header border-0 pb-0">
                                                                    <h5 class="modal-title fw-bold text-dark">Verifikasi Jadwal</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body pt-4">
                                                                    @php $tgl = \Carbon\Carbon::parse($jadwal->waktu_wawancara); @endphp
                                                                    <div class="alert alert-primary bg-primary bg-opacity-10 border-0 text-primary d-flex align-items-center p-3 mb-4 rounded-3">
                                                                        <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                                                                        <div class="small">Pastikan <strong>Penguji</strong> bersedia.</div>
                                                                    </div>
                                                                    <div class="mb-4">
                                                                        <label class="form-label small fw-bold text-muted text-uppercase">Penanggung Jawab</label>
                                                                        <select name="penanggung_jawab_id" class="form-select text-dark fw-bold" required>
                                                                            @foreach ($pjs as $pj)
                                                                                <option value="{{ $pj->id }}" {{ $jadwal->penanggung_jawab_id == $pj->id ? 'selected' : '' }}>{{ $pj->nama }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="row g-3 mb-4">
                                                                        <div class="col-md-6">
                                                                            <label class="form-label small fw-bold text-muted text-uppercase">Tanggal</label>
                                                                            <input type="date" name="tgl_wawancara" class="form-control" value="{{ $tgl->format('Y-m-d') }}" required>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label class="form-label small fw-bold text-muted text-uppercase">Jam</label>
                                                                            <input type="time" name="jam_wawancara" class="form-control" value="{{ $tgl->format('H:i') }}" required>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <label class="form-label small fw-bold text-muted text-uppercase">Lokasi</label>
                                                                            <input type="text" name="lokasi" class="form-control" value="{{ $jadwal->lokasi }}" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="bg-light p-3 rounded-3 border border-secondary border-opacity-10">
                                                                        <label class="form-label small fw-bold text-dark mb-2">Materi / Topik</label>
                                                                        <textarea name="deskripsi_skill" class="form-control border-0 bg-white shadow-sm" rows="3">{{ $jadwal->deskripsi_skill }}</textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-success fw-bold px-4">Setujui Jadwal</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- INLINE MODAL: Reject Jadwal --}}
                                                <div class="modal fade text-start" id="rejectModal{{ $jadwal->id }}" tabindex="-1">
                                                    <div class="modal-dialog modal-sm modal-dialog-centered">
                                                        <form action="{{ route('admin.pengajuan_wawancara.reject', $item->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-content">
                                                                <div class="modal-header py-2 border-0">
                                                                    <h6 class="modal-title fw-bold">Tolak Jadwal</h6>
                                                                </div>
                                                                <div class="modal-body py-0">
                                                                    <textarea name="alasan" class="form-control form-control-sm" rows="3" required placeholder="Alasan..."></textarea>
                                                                </div>
                                                                <div class="modal-footer border-0 pt-2">
                                                                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-sm btn-danger">Kirim</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                            @elseif($jadwal->status == 'approved')
                                                <button type="button" class="btn-icon btn-act-copy" onclick="copyLink('{{ route('pewawancara.penilaian', $jadwal->id) }}')" data-bs-toggle="tooltip" title="Salin Link">
                                                    <i class="bi bi-link-45deg"></i>
                                                </button>
                                                <button class="btn-icon text-secondary" disabled title="Menunggu Pewawancara"><i class="bi bi-hourglass-split"></i></button>
                                            @endif

                                        {{-- 4. SELESAI / REJECTED --}}
                                        @elseif(in_array($item->status, ['completed', 'rejected']))
                                            <a href="{{ route('admin.pengajuan.show', $item->id) }}" class="btn-icon btn-view" title="Detail"><i class="bi bi-eye"></i></a>
                                            <form action="{{ route('admin.pengajuan.destroy', $item->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-icon btn-act-x" onclick="return confirm('Hapus permanen?')" title="Hapus"><i class="bi bi-trash"></i></button>
                                            </form>
                                    @else
    <a href="{{ route('admin.pengajuan.show', $item->id) }}" class="btn-icon btn-view" title="Detail"><i class="bi bi-eye"></i></a>
    <form action="{{ route('admin.pengajuan.destroy', $item->id) }}" method="POST" class="d-inline">
        @csrf @method('DELETE')
        <button type="submit" class="btn-icon btn-act-x" onclick="return confirm('Hapus permanen data ini?')" title="Hapus">
            <i class="bi bi-trash"></i>
        </button>
    </form>
@endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox display-4 opacity-25"></i>
                                    <div class="mt-2 small">Tidak ada data pengajuan.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $pengajuan->withQueryString()->links('vendor.pagination.diksera') }}
        </div>
    </div>

    {{-- BULK ACTIONS BAR --}}
    <div class="bulk-action-bar" id="bulkActionBar">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white text-dark rounded-pill px-2 py-1" id="selectedCountBadge">0</span>
            <span class="small fw-bold">Terpilih</span>
        </div>
        <div class="bulk-separator"></div>
        <div class="d-flex gap-2">
            {{-- Bulk Approve Pending (Modal Tanggal) --}}
            <button type="button" class="btn btn-success btn-sm fw-bold rounded-pill px-3" onclick="openApprovalModal('bulk')">
                <i class="bi bi-check-lg me-1"></i> Acc Pending
            </button>

            {{-- Bulk Approve Score --}}
            <form id="formBulkScore" action="{{ route('admin.pengajuan.bulk_approve_score') }}" method="POST">
                @csrf <div id="bulkScoreInputs"></div>
                <button type="button" class="btn btn-warning text-dark btn-sm fw-bold rounded-pill px-3" onclick="submitBulk('formBulkScore', 'Verifikasi nilai?')">
                    <i class="bi bi-check-all me-1"></i> Acc Nilai
                </button>
            </form>

            {{-- Bulk Approve Interview (Modal Jadwal) --}}
            <button type="button" class="btn btn-info text-white btn-sm fw-bold rounded-pill px-3" onclick="openBulkInterviewModal()">
                <i class="bi bi-calendar-check me-1"></i> Acc Jadwal
            </button>
        </div>
    </div>

    {{-- MODAL GLOBAL: SET TANGGAL (Untuk Single & Bulk Approval) --}}
    <div class="modal fade" id="modalDateApproval" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Konfirmasi & Set Tanggal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Tanggal yang diatur di sini akan menjadi acuan masa berlaku Lisensi/Sertifikat.
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">TANGGAL TERBIT</label>
                        <input type="date" id="inputTglMulai" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">TANGGAL BERAKHIR (1 TH)</label>
                        <input type="date" id="inputTglAkhir" class="form-control" value="{{ date('Y-m-d', strtotime('+1 years')) }}">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary fw-bold" onclick="submitDateApproval()">
                        <i class="bi bi-check-lg me-2"></i> Simpan & Setujui
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL GLOBAL: BULK EDIT JADWAL --}}
    <div class="modal fade" id="modalBulkInterview" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold text-dark">Atur Jadwal Massal</h5>
                        <p class="text-muted small mb-0">Jadwal yang dipilih akan diubah serentak.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-4">
                    <div class="alert alert-info bg-info bg-opacity-10 border-0 text-primary d-flex align-items-center p-2 mb-4 rounded-3 small">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        Data yang diinput di sini akan menimpa jadwal asli peserta.
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Penanggung Jawab</label>
                        <select id="input_bulk_pj" class="form-select text-dark fw-bold">
                            @foreach ($pjs as $pj)
                                <option value="{{ $pj->id }}">{{ $pj->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">TANGGAL</label>
                            <input type="date" id="input_bulk_tgl" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">JAM</label>
                            <input type="time" id="input_bulk_jam" class="form-control" value="09:00">
                        </div>
                        <div class="col-12 mt-2">
                            <label class="form-label small fw-bold text-muted">LOKASI</label>
                            <input type="text" id="input_bulk_lokasi" class="form-control" placeholder="Contoh: R. Komite Lt.2">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">MATERI / TOPIK</label>
                        <textarea id="input_bulk_deskripsi" class="form-control" rows="2" placeholder="Opsional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light text-muted fw-bold px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-info text-white fw-bold px-4" onclick="submitBulkInterview()">
                        <i class="bi bi-check-circle-fill me-2"></i> Update & Setujui
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- HIDDEN FORMS --}}
    {{-- 1. Single Approve --}}
    <form id="formSingleApprove" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="tgl_mulai_berlaku" id="singleTglMulai">
        <input type="hidden" name="tgl_akhir_berlaku" id="singleTglAkhir">
    </form>

    {{-- 2. Bulk Approve Pending --}}
    <form id="formBulkApproveReal" action="{{ route('admin.pengajuan.bulk_approve') }}" method="POST" style="display:none;">
        @csrf
        <div id="bulkApproveHiddenInputs"></div>
        <input type="hidden" name="tgl_mulai_berlaku" id="bulkTglMulai">
        <input type="hidden" name="tgl_akhir_berlaku" id="bulkTglAkhir">
    </form>

    {{-- 3. Bulk Approve Interview --}}
    <form id="formBulkInterviewReal" action="{{ route('admin.pengajuan.bulk_approve_interview') }}" method="POST" style="display:none;">
        @csrf
        <div id="bulkInterviewHiddenIds"></div>
        <input type="hidden" name="penanggung_jawab_id" id="bulk_pj_id">
        <input type="hidden" name="tgl_wawancara" id="bulk_tgl">
        <input type="hidden" name="jam_wawancara" id="bulk_jam">
        <input type="hidden" name="lokasi" id="bulk_lokasi">
        <textarea name="deskripsi_skill" id="bulk_deskripsi" style="display:none;"></textarea>
    </form>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let approvalType = ''; 
        let singleId = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Choices JS initialization code...
            const selects = ['#choice-status', '#choice-sertifikat', '#choice-ujian'];
            selects.forEach(selector => {
                const element = document.querySelector(selector);
                if (element) {
                    new Choices(selector, { searchEnabled: true, itemSelectText: '', shouldSort: false, placeholder: true, allowHTML: true });
                    element.addEventListener('change', () => document.querySelector('form[action="{{ route('admin.pengajuan.index') }}"]').submit());
                }
            });

            // Bulk Logic
            const checkAll = document.getElementById('checkAll');
            const checkItems = document.querySelectorAll('.check-item');
            const bulkActionBar = document.getElementById('bulkActionBar');
            const selectedCountBadge = document.getElementById('selectedCountBadge');

            function updateBulkBar() {
                const count = document.querySelectorAll('.check-item:checked').length;
                selectedCountBadge.innerText = count;
                bulkActionBar.classList.toggle('active', count > 0);
            }

            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    checkItems.forEach(item => { if (!item.disabled) item.checked = this.checked; });
                    updateBulkBar();
                });
            }
            checkItems.forEach(item => item.addEventListener('change', updateBulkBar));
        });

     function openApprovalModal(type, id = null, metode = '', tglLahir = '') {
    approvalType = type;
    singleId = id;

    if (type === 'bulk' && document.querySelectorAll('.check-item:checked').length === 0) {
        Swal.fire({icon: 'warning', title: 'Pilih Data', text: 'Centang data terlebih dahulu.'});
        return;
    }

    // Ambil elemen input tanggal dan labelnya
    const inputTglAkhir = document.getElementById('inputTglAkhir');
    const labelTglAkhir = inputTglAkhir.previousElementSibling;

    let today = new Date();
    let expDate = new Date();

    // LOGIKA RENTANG TANGGAL
    if (metode === 'interview_only') {
        // 1. Tambah 3 Tahun ke Depan
        expDate.setFullYear(today.getFullYear() + 3);
        
        // 2. Jika ada tanggal lahir, set bulan dan hari sesuai tanggal lahir
        if (tglLahir) {
            let birthDate = new Date(tglLahir);
            if (!isNaN(birthDate)) {
                expDate.setMonth(birthDate.getMonth());
                expDate.setDate(birthDate.getDate());
            }
        }
        
        // Ganti teks label di UI
        labelTglAkhir.innerText = "TANGGAL BERAKHIR (3 THN - SESUAI TGL LAHIR)";
    } else {
        // Default 1 Tahun
        expDate.setFullYear(today.getFullYear() + 1);
        labelTglAkhir.innerText = "TANGGAL BERAKHIR (1 TH)";
    }

    // Fungsi kecil untuk format tanggal JavaScript ke format input "YYYY-MM-DD"
    const formatYMD = (dateObj) => {
        let m = String(dateObj.getMonth() + 1).padStart(2, '0');
        let d = String(dateObj.getDate()).padStart(2, '0');
        return `${dateObj.getFullYear()}-${m}-${d}`;
    };

    // Tembak nilainya ke input
    inputTglAkhir.value = formatYMD(expDate);

    // Buka Modal
    new bootstrap.Modal(document.getElementById('modalDateApproval')).show();
}

        function submitDateApproval() {
            const tglMulai = document.getElementById('inputTglMulai').value;
            const tglAkhir = document.getElementById('inputTglAkhir').value;
            if (!tglMulai || !tglAkhir) { alert('Harap isi kedua tanggal!'); return; }

            if (approvalType === 'single') {
                const form = document.getElementById('formSingleApprove');
                let url = "{{ route('admin.pengajuan.approve', ':id') }}";
        url = url.replace(':id', singleId);
        
        form.action = url;
                document.getElementById('singleTglMulai').value = tglMulai;
                document.getElementById('singleTglAkhir').value = tglAkhir;
                form.submit();
            } else if (approvalType === 'bulk') {
                const form = document.getElementById('formBulkApproveReal');
                const container = document.getElementById('bulkApproveHiddenInputs');
                container.innerHTML = '';
                document.querySelectorAll('.check-item:checked').forEach(item => {
                    const input = document.createElement('input');
                    input.type = 'hidden'; input.name = 'ids[]'; input.value = item.value;
                    container.appendChild(input);
                });
                document.getElementById('bulkTglMulai').value = tglMulai;
                document.getElementById('bulkTglAkhir').value = tglAkhir;
                form.submit();
            }
        }

        // --- 2. MODAL BULK INTERVIEW ---
        function openBulkInterviewModal() {
            if (document.querySelectorAll('.check-item:checked').length === 0) {
                Swal.fire({icon: 'warning', title: 'Pilih Data', text: 'Centang peserta terlebih dahulu.'});
                return;
            }
            new bootstrap.Modal(document.getElementById('modalBulkInterview')).show();
        }

        function submitBulkInterview() {
            const container = document.getElementById('bulkInterviewHiddenIds');
            container.innerHTML = '';
            document.querySelectorAll('.check-item:checked').forEach(item => {
                const input = document.createElement('input');
                input.type = 'hidden'; input.name = 'ids[]'; input.value = item.value;
                container.appendChild(input);
            });

            document.getElementById('bulk_pj_id').value = document.getElementById('input_bulk_pj').value;
            document.getElementById('bulk_tgl').value = document.getElementById('input_bulk_tgl').value;
            document.getElementById('bulk_jam').value = document.getElementById('input_bulk_jam').value;
            document.getElementById('bulk_lokasi').value = document.getElementById('input_bulk_lokasi').value;
            document.getElementById('bulk_deskripsi').value = document.getElementById('input_bulk_deskripsi').value;

            if(confirm('Ubah jadwal secara serentak?')) {
                document.getElementById('formBulkInterviewReal').submit();
            }
        }

        // --- 3. HELPER FUNCTIONS ---
        function submitBulk(formId, msg) {
            if (!confirm(msg)) return;
            const form = document.getElementById(formId);
            let containerId = formId === 'formBulkScore' ? 'bulkScoreInputs' : 'bulkInterviewInputs';
            const container = document.getElementById(containerId);
            container.innerHTML = '';
            
            const checked = document.querySelectorAll('.check-item:checked');
            if (checked.length === 0) { alert('Pilih data dulu!'); return; }

            checked.forEach(item => {
                const i = document.createElement('input');
                i.type = 'hidden'; i.name = 'ids[]'; i.value = item.value;
                container.appendChild(i);
            });
            form.submit();
        }

       // --- FUNGSI COPY LINK (ROBUST / KEBAL HTTP) ---
    function copyLink(text) {
        // 1. Coba pakai API Modern (Khusus HTTPS)
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                tampilkanNotifikasiSukses();
            }).catch(err => {
                // Jika gagal (misal izin ditolak), pakai cara lama
                fallbackCopyTextToClipboard(text);
            });
        } else {
            // 2. Jika HTTP biasa, langsung pakai cara lama
            fallbackCopyTextToClipboard(text);
        }
    }

    // Fungsi Cara Lama (Kompatibel HTTP & Browser Lama)
    function fallbackCopyTextToClipboard(text) {
        var textArea = document.createElement("textarea");
        textArea.value = text;
        
        // Sembunyikan elemen agar tidak merusak tampilan
        textArea.style.top = "0";
        textArea.style.left = "0";
        textArea.style.position = "fixed";
        textArea.style.opacity = "0";

        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            var successful = document.execCommand('copy');
            if (successful) {
                tampilkanNotifikasiSukses();
            } else {
                alert('Browser memblokir aksi copy.');
            }
        } catch (err) {
            console.error('Fallback error:', err);
            alert('Gagal menyalin link.');
        }

        document.body.removeChild(textArea);
    }

    // Helper Notifikasi
    function tampilkanNotifikasiSukses() {
        Swal.fire({
            icon: 'success',
            title: 'Link Disalin!',
            text: 'Link penilaian berhasil disalin ke clipboard.',
            showConfirmButton: false,
            timer: 1500,
            toast: true,
            position: 'top-end',
            background: '#fff',
            iconColor: '#15803d'
        });
    }
    
    // --- 3. HELPER FUNCTIONS ---
        function submitBulk(formId, msg, customContainerId = null) {
            if (!confirm(msg)) return;
            const form = document.getElementById(formId);
            
            // Logika container yang mendukung form baru
            let containerId = customContainerId || (formId === 'formBulkScore' ? 'bulkScoreInputs' : 'bulkInterviewInputs');
            const container = document.getElementById(containerId);
            container.innerHTML = '';
            
            const checked = document.querySelectorAll('.check-item:checked');
            if (checked.length === 0) { alert('Pilih data dulu!'); return; }

            checked.forEach(item => {
                const i = document.createElement('input');
                i.type = 'hidden'; 
                i.name = 'ids[]'; 
                i.value = item.value;
                container.appendChild(i);
            });
            
            form.submit();
        }
    </script>
@endpush
