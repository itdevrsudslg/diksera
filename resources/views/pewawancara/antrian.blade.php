@extends('layouts.app')

@section('title', 'Antrian Wawancara')

@push('styles')
    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-hover: #1d4ed8;
            --text-dark: #0f172a;
            --text-gray: #64748b;
            --bg-light: #f1f5f9;
            --blue-soft: #eff6ff;
            --blue-border: #dbeafe;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: 'Inter', sans-serif;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -0.5px;
            margin-bottom: 0.25rem;
        }

        .card-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* Styling Filter Bar */
        .filter-bar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px -3px rgba(0, 0, 0, 0.02);
        }
        
        .search-input-group .input-group-text {
            background-color: transparent;
            border-right: none;
        }
        .search-input-group .form-control {
            border-left: none;
        }
        .search-input-group .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }

        .table-custom th {
            background-color: #f8fafc;
            color: var(--text-gray);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 16px 24px;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-custom td {
            padding: 16px 24px;
            vertical-align: middle;
            border-bottom: 1px solid #f8fafc;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .table-custom tr:hover td {
            background-color: #f8fafc;
        }

        .avatar-circle {
            width: 40px;
            height: 40px;
            background-color: var(--blue-soft);
            color: var(--primary-blue);
            border: 1px solid var(--blue-border);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .date-box { display: flex; flex-direction: column; line-height: 1.3; }
        .date-main { font-weight: 700; color: var(--text-dark); font-size: 0.95rem; }
        .date-sub { font-size: 0.8rem; color: var(--text-gray); }

        .badge-soft { padding: 6px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.3px; text-transform: uppercase; }
        .bs-warning { background: #fffbeb; color: #b45309; border: 1px solid #fcd34d; }
        .bs-danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .bs-info { background: var(--blue-soft); color: var(--primary-blue); border: 1px solid var(--blue-border); }
        .bs-kredensial { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .bs-ujiskill { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }

        .btn-action {
            background-color: white;
            color: var(--primary-blue);
            border: 1px solid #e2e8f0;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-action:hover {
            background-color: var(--primary-blue);
            color: white;
            border-color: var(--primary-blue);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.15);
        }

        .btn-back {
            color: var(--text-gray);
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
            padding: 8px 12px;
            border-radius: 8px;
        }

        .btn-back:hover {
            background-color: white;
            color: var(--primary-blue);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4 px-4">

        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="page-title">Antrian Penilaian</h2>
                <p class="text-muted mb-0 small">Daftar peserta yang menunggu giliran untuk dinilai.</p>
            </div>
            <a href="{{ route('pewawancara.dashboard') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>

        <!-- FILTER BAR -->
        <div class="filter-bar">
            <form action="{{ route('pewawancara.antrian') }}" method="GET" id="filterForm">
                <div class="row g-3">
                    <!-- Search -->
                    <div class="col-12 col-md-4">
                        <div class="input-group search-input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama / email peserta..." value="{{ request('search') }}">
                        </div>
                    </div>
                    
                    <!-- Filter Status Waktu -->
                    <div class="col-12 col-md-2">
                        <select name="waktu" class="form-select text-secondary" onchange="document.getElementById('filterForm').submit();">
                            <option value="">Semua Waktu</option>
                            <option value="hari_ini" {{ request('waktu') == 'hari_ini' ? 'selected' : '' }}>Waktu: Hari Ini</option>
                            <option value="terlewat" {{ request('waktu') == 'terlewat' ? 'selected' : '' }}>Waktu: Terlewat</option>
                            <option value="akan_datang" {{ request('waktu') == 'akan_datang' ? 'selected' : '' }}>Waktu: Akan Datang</option>
                        </select>
                    </div>

                    <!-- Filter Metode -->
                    <div class="col-12 col-md-3">
                        <select name="metode" class="form-select text-secondary" onchange="document.getElementById('filterForm').submit();">
                            <option value="">Semua Metode</option>
                            <option value="interview_only" {{ request('metode') == 'interview_only' ? 'selected' : '' }}>Kredensialing</option>
                            <!-- Sesuaikan value pg_interview atau metode lain yang Anda gunakan untuk Uji Skill -->
                            <option value="pg_interview" {{ request('metode') == 'pg_interview' ? 'selected' : '' }}>Uji Skill</option>
                        </select>
                    </div>

                    <!-- Sorting -->
                    <div class="col-12 col-md-2">
                        <select name="sort" class="form-select text-secondary" onchange="document.getElementById('filterForm').submit();">
                            <option value="terdekat" {{ request('sort') == 'terdekat' ? 'selected' : '' }}>Waktu Terdekat</option>
                            <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Waktu Terlama</option>
                        </select>
                    </div>

                    <!-- Button Submit (Jika onchange select dirasa kurang) -->
                    <div class="col-12 col-md-1">
                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                    </div>
                </div>
                
                <!-- Reset Filter jika ada parameter -->
                @if(request()->anyFilled(['search', 'waktu', 'metode']) || request('sort') == 'terlama')
                    <div class="mt-2 text-end">
                        <a href="{{ route('pewawancara.antrian') }}" class="text-danger small text-decoration-none">
                            <i class="bi bi-x-circle"></i> Hapus Filter
                        </a>
                    </div>
                @endif
            </form>
        </div>
        <!-- END FILTER BAR -->

        <div class="card card-custom">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th width="20%">Jadwal & Metode</th>
                                <th width="30%">Identitas Peserta</th>
                                <th width="20%">Status Antrian</th>
                                <th width="20%">Lisensi Diajukan</th>
                                <th width="10%" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($antrian as $jadwal)
                                <tr>
                                    <td>
                                        <div class="date-box mb-2">
                                            <span class="date-main">{{ $jadwal->waktu_wawancara->format('d M Y') }}</span>
                                            <div class="d-flex align-items-center gap-1 date-sub">
                                                <i class="bi bi-clock"></i> {{ $jadwal->waktu_wawancara->format('H:i') }} WIB
                                            </div>
                                        </div>
                                        @if(($jadwal->pengajuan->metode ?? '') === 'interview_only')
                                            <span class="badge-soft bs-kredensial"><i class="bi bi-person-vcard me-1"></i> Kredensialing</span>
                                        @else
                                            <span class="badge-soft bs-ujiskill"><i class="bi bi-ui-checks me-1"></i> Uji Skill</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $name = $jadwal->pengajuan->user->name ?? 'User';
                                                $initials = collect(explode(' ', $name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                                            @endphp
                                            <div class="avatar-circle">{{ $initials }}</div>
                                            <div class="d-flex flex-column" style="line-height: 1.2;">
                                                <span class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $name }}</span>
                                                <span class="text-muted small" style="font-size: 0.8rem;">
                                                    {{ $jadwal->pengajuan->user->email ?? '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        @if ($jadwal->waktu_wawancara->isToday())
                                            <span class="badge badge-soft bs-warning">
                                                <i class="bi bi-exclamation-circle me-1"></i> Hari Ini
                                            </span>
                                        @elseif($jadwal->waktu_wawancara->lt(now()))
                                            <span class="badge badge-soft bs-danger">
                                                <i class="bi bi-x-circle me-1"></i> Terlewat
                                            </span>
                                        @else
                                            <span class="badge badge-soft bs-info">
                                                <i class="bi bi-calendar-event me-1"></i> Akan Datang
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="d-flex flex-column" style="line-height: 1.2;">
                                            <span class="fw-bold text-dark" style="font-size: 0.9rem;">
                                                {{ $jadwal->pengajuan->lisensiLama->nama ?? 'Pengajuan Baru' }}
                                            </span>
                                            <span class="text-muted small" style="font-size: 0.75rem;">
                                                ID: #{{ $jadwal->pengajuan->id ?? '-' }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="text-end">
                                        <a href="{{ route('pewawancara.penilaian', $jadwal->id) }}" class="btn-action">
                                            <i class="bi bi-pencil-square"></i> Nilai
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center opacity-50">
                                            <div class="bg-light rounded-circle p-3 mb-3">
                                                <i class="bi bi-search text-muted" style="font-size: 2.5rem;"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1">Tidak Ada Data Ditemukan</h6>
                                            <p class="small text-muted mb-0">Tidak ada antrian yang sesuai dengan filter/pencarian Anda.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($antrian->hasPages())
                <div class="card-footer bg-white border-top p-3 d-flex justify-content-end">
                    {{ $antrian->withQueryString()->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
@endsection