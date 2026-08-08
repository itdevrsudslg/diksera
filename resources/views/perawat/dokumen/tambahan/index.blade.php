@extends('layouts.app')

@section('title', 'Data Dokumen Tambahan')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-hover: #1d4ed8;
            --text-dark: #0f172a;
            --text-gray: #64748b;
            --bg-light: #f1f5f9;
        }

        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
        }

        /* --- Header Area --- */
        .page-header {
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            color: var(--text-gray);
            font-size: 0.9rem;
            margin-top: 4px;
        }

        /* --- Buttons --- */
        .btn-blue {
            background-color: var(--primary-blue);
            color: white;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid var(--primary-blue);
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-blue:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 10px -1px rgba(37, 99, 235, 0.3);
        }

        .btn-white {
            background: white;
            color: var(--text-gray);
            border: 1px solid #e2e8f0;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-white:hover {
            background-color: #f8fafc;
            color: var(--text-dark);
            border-color: #cbd5e1;
        }

        /* --- Table Card --- */
        .table-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            overflow: hidden;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
        }

        .custom-table thead th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 16px 24px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        .custom-table tbody tr {
            transition: background-color 0.2s;
            border-bottom: 1px solid #f1f5f9;
        }

        .custom-table tbody tr:last-child {
            border-bottom: none;
        }

        .custom-table tbody tr:hover {
            background-color: #eff6ff;
        }

        .custom-table td {
            padding: 20px 24px;
            vertical-align: middle;
            font-size: 0.95rem;
            color: var(--text-gray);
        }

        /* --- Typography & Elements --- */
        .data-title {
            font-weight: 600;
            color: var(--text-dark);
            display: block;
            margin-bottom: 4px;
            font-size: 1rem;
        }

        .data-sub {
            font-size: 0.85rem;
            color: var(--text-gray);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* --- Custom Status Badges --- */
        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-active {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .status-warning {
            background-color: #fef9c3;
            color: #854d0e;
            border: 1px solid #fde047;
        }

        .status-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Action Buttons */
        .action-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            transition: 0.2s;
            background: transparent;
            border: 1px solid transparent;
        }
  .stat-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 18px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: transform 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.05);
    }
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.7rem;
    }
    .icon-active {
        background: #dcfce7; /* Hijau muda */
        color: #16a34a; /* Hijau solid */
    }
    .icon-inactive {
        background: #fee2e2; /* Merah muda */
        color: #ef4444; /* Merah solid */
    }
    .stat-info p {
        margin: 0 0 4px 0;
        font-size: 0.9rem;
        color: var(--text-gray);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-info h3 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 700;
     
        .action-btn:hover {
            background: #dbeafe;
            color: var(--primary-blue);
        }

        .action-btn.delete:hover {
            background: #fee2e2;
            color: #ef4444;
        }

        /* Alert */
        .alert-blue {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="page-header">
            <div>
 <h1 class="page-title">Pemenuhan SKP</h1>
<p class="page-subtitle">Monitor perolehan SKP dari dokumen sertifikasi Anda.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('perawat.drh') }}" class="btn-white"><i class="bi bi-arrow-left"></i> Kembali</a>
                <a href="{{ route('perawat.tambahan.create') }}" class="btn-blue"><i class="bi bi-plus-lg"></i> Tambah
                    Dokumen</a>
            </div>
        </div>



   <div class="row mb-4">
        <div class="col-md-6 mb-3 mb-md-0">
            <div class="stat-card">
                <div class="stat-icon icon-active">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="stat-info">
                    <p>Total SKP Aktif </p>
                    <h3>{{ $totalSkpAktif }} <span style="font-size: 1rem; color: #64748b; font-weight: 500;">SKP</span></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card">
                <div class="stat-icon icon-inactive">
                    <i class="bi bi-shield-x"></i>
                </div>
                <div class="stat-info">
                    <p>Total SKP Non-Aktif (Kadaluarsa)</p>
                    <h3>{{ $totalSkpNonAktif }}<span style="font-size: 1rem; color: #64748b; font-weight: 500;">SKP</span></h3>
                </div>
            </div>
        </div>
    </div>
        @if (session('success'))
            <div class="alert-blue"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif

        <div class="table-card">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="30%">Dokumen</th>
                            <th width="20%">Nomor Dokumen</th>
                            <th width="10%">SKP</th> <!-- Tambah ini -->
                            <th width="20%">Masa Berlaku</th>
                            <th width="15%">Status</th>
                            <th width="10%" class="text-center">File</th>
                            <th width="5%" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    {{-- Nama (Tebal) --}}
                                    <span class="data-title">{{ $item->nama }}</span>

                                    {{-- Jenis (Badge) & Lembaga (Building Icon) --}}
                                    <div class="text-muted" style="font-size: 0.8rem; margin-top: 2px;">
                                        <span><i class="bi bi-building"></i> {{ $item->lembaga }}</span>
                                    </div>
                                </td>

                                <td><span class="text-dark">{{ $item->nomor }}</span></td>
<td>
    <span class="badge bg-light text-dark border">{{ $item->skp }}</span>
</td>
                                <td>
                                    <span class="data-sub">
                                        <i class="bi bi-calendar-event"></i>
                                        @if ($item->is_lifetime)
                                            @if ($item->lifetime_approved)
                                                Seumur Hidup
                                            @else
                                                Seumur Hidup (Menunggu ACC Admin)
                                            @endif
                                        @else
                                            @if ($item->tgl_expired)
                                                Exp: {{ \Carbon\Carbon::parse($item->tgl_expired)->format('d-m-Y') }}
                                            @else
                                                -
                                            @endif
                                        @endif
                                    </span>
                                </td>

                                <td>
                                    @php
                                        $today = \Carbon\Carbon::now();
                                    @endphp

                                    @if ($item->is_lifetime)
                                        @if ($item->lifetime_approved)
                                            <span class="status-badge status-active"><i class="bi bi-check-circle-fill"></i>
                                                Aktif</span>
                                        @else
                                            <span class="status-badge status-warning"><i class="bi bi-clock-fill"></i>
                                                Menunggu ACC Admin</span>
                                        @endif
                                    @else
                                        @if ($item->tgl_expired)
                                            @php
                                                $expired = \Carbon\Carbon::parse($item->tgl_expired);
                                                $diff = $today->diffInDays($expired, false);
                                            @endphp

                                            @if ($diff < 0)
                                                <span class="status-badge status-danger"><i class="bi bi-x-circle-fill"></i>
                                                    Expired</span>
                                            @elseif($diff < 90)
                                                <span class="status-badge status-warning"><i
                                                        class="bi bi-exclamation-triangle-fill"></i> Hampir Expired</span>
                                            @else
                                                <span class="status-badge status-active"><i
                                                        class="bi bi-check-circle-fill"></i> Aktif</span>
                                            @endif
                                        @else
                                            <span class="status-badge status-warning"><i class="bi bi-dash-circle"></i>
                                                Tidak Ada Tanggal</span>
                                        @endif
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if ($item->file_path)
                                        <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="action-btn"
                                            title="Lihat File" style="color: var(--primary-blue);">
                                            <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                                        </a>
                                    @else
                                        <span class="text-muted text-xs">-</span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('perawat.tambahan.edit', $item->id) }}" class="action-btn"
                                            title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                        <form action="{{ route('perawat.tambahan.destroy', $item->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Hapus dokumen ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="action-btn delete" title="Hapus"><i
                                                    class="bi bi-trash-fill"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted" style="opacity: 0.6;">
                                        <i class="bi bi-folder-x fs-1 d-block mb-2"></i> Belum ada dokumen tambahan.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
