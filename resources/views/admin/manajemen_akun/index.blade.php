@extends('layouts.app')

@section('title', 'Manajemen Akun Pengguna')

@push('styles')
    {{-- SweetAlert CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary-blue: #2563eb;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bg-light: #f8fafc;
            --border-color: #e2e8f0;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-size: 0.85rem;
            /* ~13.6px Default lebih kecil */
        }

        /* --- Page Header --- */
        .page-header {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 1.25rem;
            /* 20px */
            font-weight: 700;
            margin: 0;
            color: var(--text-dark);
        }

        /* --- Cards --- */
        .content-card {
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .filter-card {
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 16px;
        }

        /* --- Inputs (Compact) --- */
        .form-label {
            font-size: 0.75rem;
            /* 12px */
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-gray);
            margin-bottom: 4px;
            letter-spacing: 0.3px;
        }

        .form-control,
        .form-select {
            font-size: 0.85rem;
            /* 13px */
            border-radius: 6px;
            border-color: #cbd5e1;
            padding: 6px 10px;
            /* Padding tipis */
            min-height: 36px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* --- Table Styling (Compact) --- */
        .table-custom th {
            background-color: #f8fafc;
            text-transform: uppercase;
            font-size: 0.7rem;
            /* 11px */
            font-weight: 700;
            color: var(--text-gray);
            letter-spacing: 0.5px;
            padding: 10px 16px;
            /* Padding Header Tipis */
            border-bottom: 1px solid var(--border-color);
        }

        .table-custom td {
            padding: 10px 16px;
            /* Padding Cell Tipis */
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.85rem;
            /* 13.6px */
        }

        .table-custom tr:last-child td {
            border-bottom: none;
        }

        .table-custom tbody tr:hover {
            background-color: #f1f5f9;
        }

        /* --- Avatar (Compact) --- */
        .avatar-initial {
            width: 32px;
            /* Diperkecil dari 40px */
            height: 32px;
            background-color: #eff6ff;
            color: var(--primary-blue);
            font-weight: 700;
            font-size: 13px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dbeafe;
            flex-shrink: 0;
        }

        /* --- Badges (Compact) --- */
        .badge-soft {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.7rem;
            /* 11px */
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background: #fef9c3;
            color: #854d0e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-role {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        /* --- Action Buttons (Compact) --- */
        .btn-icon {
            width: 30px;
            /* Diperkecil dari 34px */
            height: 30px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            transition: all 0.2s;
            color: var(--text-gray);
            background: transparent;
            font-size: 0.9rem;
        }

        .btn-icon:hover {
            background: #e2e8f0;
            transform: translateY(-1px);
        }

        .btn-icon-check:hover {
            background: #dcfce7;
            color: #166534;
        }

        .btn-icon-x:hover {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-icon-trash:hover {
            background: #fee2e2;
            color: #991b1b;
        }

        .row-link {
            cursor: pointer;
        }

        .row-link:hover .user-link {
            color: var(--bs-primary) !important;
            text-decoration: underline !important;
        }
    </style>
@endpush

@section('content')
    <div class=" py-4">

        {{-- HEADER --}}
        <div class="page-header">
            <div>
                <h1 class="page-title">Manajemen Akun</h1>
                <p class="text-muted small mb-0">Verifikasi pendaftaran dan kelola akses pengguna.</p>
            </div>

            {{-- Total Stats (Lebih Compact) --}}
            <div class="d-flex gap-3">
                <div class="px-3 py-1 bg-white rounded-3 border d-flex align-items-center gap-2 shadow-sm">
                    <i class="bi bi-people-fill text-primary"></i>
                    <span class="fw-bold text-dark">{{ $users->total() }}</span> <span class="text-muted"
                        style="font-size: 11px;">USERS</span>
                </div>
            </div>
        </div>

        {{-- FILTER CARD --}}
        <div class="filter-card shadow-sm">
            <form method="GET" action="{{ route('admin.manajemen_akun.index') }}" class="row g-2">
                <div class="col-md-5">
                    <label class="form-label">Pencarian</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                            placeholder="Cari Nama / Email / NIK..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status_akun" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status_akun') == 'pending' ? 'selected' : '' }}>Menunggu
                        </option>
                        <option value="active" {{ request('status_akun') == 'active' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status_akun') == 'rejected' ? 'selected' : '' }}>Ditolak
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select form-select-sm">
                        <option value="">Semua Role</option>
                        @foreach (['admin', 'perawat', 'pewawancara'] as $r)
                            <option value="{{ $r }}" {{ request('role') == $r ? 'selected' : '' }}>
                                {{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-flex gap-1 w-100">
                        <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">Filter</button>
                        <a href="{{ route('admin.manajemen_akun.index') }}" class="btn btn-sm btn-light border px-2"
                            title="Reset">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABLE CARD --}}
        <div class="content-card">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="30%">Identitas Pengguna</th>
                            <th width="15%">Role</th>
                            <th width="18%">Tanggal Daftar</th>
                            <th width="15%">Status</th>
                            <th width="17%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            @php $detailUrl = $user->role === 'perawat' ? route('admin.perawat.show', $user->id) : null; @endphp
                            <tr @if ($detailUrl) class="row-link" data-href="{{ $detailUrl }}" title="Lihat detail pegawai" @endif>
                                <td class="text-center text-muted" style="font-size: 11px;">
                                    {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>

                                {{-- Identitas --}}
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @php
                                            $initials = collect(explode(' ', $user->name))
                                                ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                                ->take(2)
                                                ->join('');
                                        @endphp
                                        <div class="avatar-initial">{{ $initials }}</div>
                                        <div style="line-height: 1.3;">
                                            @if ($detailUrl)
                                                <a href="{{ $detailUrl }}" class="fw-bold text-dark text-decoration-none user-link">{{ $user->name }}</a>
                                            @else
                                                <div class="fw-bold text-dark">{{ $user->name }}</div>
                                            @endif
                                            <div class="text-muted" style="font-size: 11px;">
                                                {{ $user->email }}
                                            </div>
                                            @if ($user->profile && $user->profile->nik)
                                                <div class="text-secondary" style="font-size: 10px;">NIK:
                                                    {{ $user->profile->nik }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Role --}}
                                <td>
                                    <span class="badge-soft badge-role">{{ $user->role }}</span>
                                </td>

                                {{-- Tanggal --}}
                                <td>
                                    <div style="line-height: 1.2;">
                                        <div class="text-dark" style="font-size: 12px;">
                                            {{ $user->created_at->format('d M Y') }}</div>
                                        <div class="text-muted" style="font-size: 10px;">
                                            {{ $user->created_at->format('H:i') }} WIB</div>
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if ($user->status_akun == 'active')
                                        <span class="badge-soft badge-success"><i class="bi bi-check"></i> Disetujui</span>
                                    @elseif($user->status_akun == 'rejected')
                                        <span class="badge-soft badge-danger"><i class="bi bi-x"></i> Ditolak</span>
                                    @else
                                        <span class="badge-soft badge-warning"><i class="bi bi-clock"></i> Menunggu</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">

                                        {{-- Tombol Approve --}}
                                        @if ($user->status_akun != 'active')
                                            <form action="{{ route('admin.manajemen_akun.update', $user->id) }}"
                                                method="POST" class="action-form">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status_akun" value="active">
                                                <button type="button" class="btn-icon btn-icon-check btn-approve"
                                                    data-bs-toggle="tooltip" title="Setujui">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Tombol Reject --}}
                                        @if ($user->status_akun != 'rejected')
                                            <form action="{{ route('admin.manajemen_akun.update', $user->id) }}"
                                                method="POST" class="action-form">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status_akun" value="rejected">
                                                <button type="button" class="btn-icon btn-icon-x btn-reject"
                                                    data-bs-toggle="tooltip" title="Tolak">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Tombol Delete --}}
                                        <form action="{{ route('admin.manajemen_akun.destroy', $user->id) }}"
                                            method="POST" class="action-form">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn-icon btn-icon-trash btn-delete"
                                                data-bs-toggle="tooltip" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center opacity-50">
                                        <i class="bi bi-inbox display-4 mb-2"></i>
                                        <h6 class="text-muted small">Data tidak ditemukan</h6>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $users->withQueryString()->links('vendor.pagination.diksera') }}
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Init Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Klik baris -> detail pegawai (abaikan klik di tombol aksi / link)
            document.querySelectorAll('tr.row-link').forEach(row => {
                row.addEventListener('click', function(e) {
                    if (e.target.closest('a, button, form, input, select')) return;
                    if (e.ctrlKey || e.metaKey) {
                        window.open(this.dataset.href, '_blank');
                    } else {
                        window.location.href = this.dataset.href;
                    }
                });
            });

            // Handle Flash Message
            @if (session('swal'))
                Swal.fire({
                    icon: "{{ session('swal')['type'] ?? 'success' }}",
                    title: "{{ session('swal')['title'] ?? 'Berhasil' }}",
                    text: "{{ session('swal')['text'] }}",
                    timer: 2000,
                    showConfirmButton: false,
                    position: 'top-end', // Toast style agar tidak ganggu
                    toast: true
                });
            @endif

            // Logic SweetAlert Approve
            document.querySelectorAll('.btn-approve').forEach(btn => {
                btn.addEventListener('click', function() {
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Setujui Akun?',
                        text: "User akan mendapatkan akses.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#10b981',
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Batal',
                        width: '300px' // Alert lebih kecil
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });

            // Logic SweetAlert Reject
            document.querySelectorAll('.btn-reject').forEach(btn => {
                btn.addEventListener('click', function() {
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Tolak Akun?',
                        text: "User tidak akan bisa login.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'Tolak',
                        cancelButtonText: 'Batal',
                        width: '300px'
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });

            // Logic SweetAlert Delete
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Hapus Permanen?',
                        text: "Data tidak bisa dikembalikan.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Hapus',
                        cancelButtonText: 'Batal',
                        width: '300px'
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        });
    </script>
@endpush
