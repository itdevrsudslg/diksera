@extends('layouts.app')

@php
    $pageTitle = 'Penanggung Jawab';
    $pageSubtitle = 'Kelola data pengawas ujian dan pewawancara.';
@endphp

@section('title', 'Penanggung Jawab – Admin DIKSERA')

@push('styles')
    {{-- CSS SweetAlert --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        /* Card Container */
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-soft);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 24px;
        }

        /* Search Input */
        .search-input {
            border-radius: 8px;
            border: 1px solid var(--border-soft);
            font-size: 13px;
            padding-left: 12px;
            height: 38px;
        }

        .search-input:focus {
            border-color: var(--blue-main);
            box-shadow: 0 0 0 3px var(--blue-soft);
        }

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

        /* Avatar Inisial */
        .avatar-initial {
            width: 36px;
            height: 36px;
            background: #eff6ff;
            color: #3b82f6;
            font-weight: 700;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            margin-right: 12px;
            border: 1px solid #dbeafe;
        }

        /* Action Buttons */
        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .btn-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('content')

    <div class="content-card">

        {{-- Header Tools --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
            {{-- Search Bar --}}
            <form action="" method="GET" class="d-flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="form-control form-control-sm search-input" placeholder="Cari nama atau jabatan..."
                    style="width: 260px;">
                <button class="btn btn-sm btn-light border" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>

            {{-- Create Button --}}
            <a href="{{ route('admin.penanggung-jawab.create') }}" class="btn btn-sm btn-primary px-3 shadow-sm"
                style="border-radius: 8px; height: 38px; display: flex; align-items: center;">
                <i class="bi bi-plus-lg me-2"></i> Tambah Data
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
              <thead class="bg-light">
    <tr>
        <th width="5%" class="text-center">No</th>
        <th>Nama & Akun Login</th>
        <th>Jabatan</th>
        <th class="text-center">Statistik Ujian</th> <!-- Tambah Kolom Ini -->
        <th>Kontak</th>
        <th class="text-center" width="120">Aksi</th>
    </tr>
</thead>
<tbody>
    @forelse($data as $key => $item)
        <tr>
            <td class="text-center text-muted align-middle">{{ $loop->iteration }}</td>
            <td class="align-middle">
                <div class="d-flex align-items-center">
                    <!-- ... (Script inisial avatar biarkan seperti aslinya) ... -->
                    
                    <div>
                        <!-- NAMA DIBUAT JADI LINK -->
                        <a href="{{ route('admin.penanggung-jawab.show', $item->id) }}" class="fw-bold text-primary text-decoration-none hover-dark">
                            {{ $item->nama }}
                        </a>
                        <div class="text-muted small" style="font-size: 11px;">
                            <i class="bi bi-envelope me-1"></i> {{ $item->user->email ?? 'Belum ada akun' }}
                        </div>
                    </div>
                </div>
            </td>

            <td class="align-middle">
                <span class="text-dark small fw-medium">{{ $item->jabatan }}</span>
            </td>

            <!-- TAMBAH TD INI UNTUK SPILL DATA -->
            <td class="text-center align-middle">
                <div class="d-flex flex-column gap-1 align-items-center">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 11px;">
                        Uji Komp: {{ $item->total_uji_komp }}
                    </span>
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle" style="font-size: 11px;">
                        Kredensial: {{ $item->total_kredensial }}
                    </span>
                </div>
            </td>

            <!-- ... (Sisa kolom Kontak dan Aksi biarkan seperti aslinya) ... -->

                            <td class="align-middle">
                                @if ($item->no_hp)
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $item->no_hp)) }}"
                                        target="_blank"
                                        class="text-decoration-none text-muted small d-flex align-items-center gap-1 hover-text-success">
                                        <i class="bi bi-whatsapp text-success"></i> {{ $item->no_hp }}
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            <td class="text-center align-middle">
                                <div class="d-flex justify-content-center gap-1">
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.penanggung-jawab.edit', $item->id) }}"
                                        class="btn btn-sm btn-light border text-primary" data-bs-toggle="tooltip"
                                        title="Edit Data">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.penanggung-jawab.destroy', $item->id) }}" method="POST"
                                        class="d-inline delete-form"
                                        onsubmit="return confirm('Hapus data dan akun login ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger"
                                            data-bs-toggle="tooltip" title="Hapus Data">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5"> {{-- Colspan dikurangi jadi 5 --}}
                                <div class="text-muted mb-2">
                                    <i class="bi bi-person-slash display-6 opacity-25"></i>
                                </div>
                                <span class="text-muted small">Belum ada data penanggung jawab.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $data->withQueryString()->links('vendor.pagination.diksera') }}
        </div>
    </div>
@endsection

@push('scripts')
    {{-- SweetAlert JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Init Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Handle Flash Message Success
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            // Handle Delete Confirmation
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Hapus Data?',
                        text: "Data penanggung jawab ini akan dihapus permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
