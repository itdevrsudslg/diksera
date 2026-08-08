@extends('layouts.app')

@php
    $pageTitle = 'Manajemen Form';
    $pageSubtitle = 'Kelola tautan survei, presensi, dan pengumpulan data lainnya.';
@endphp

@section('title', 'Manajemen Form – Admin DIKSERA')

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
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-soft-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-soft-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-soft-secondary {
            background: #f1f5f9;
            color: #475569;
        }

        .badge-soft-info {
            background: #e0f2fe;
            color: #075985;
        }

        /* Action Buttons (Icon Only) */
        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            border: 1px solid transparent;
            text-decoration: none;
            /* Fix untuk tag <a> */
        }

        .btn-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Search Input */
        .search-input {
            border-radius: 8px;
            border: 1px solid var(--border-soft);
            font-size: 13px;
            padding-left: 12px;
        }

        .search-input:focus {
            border-color: var(--blue-main);
            box-shadow: 0 0 0 3px var(--blue-soft);
        }

        /* Modal Radio Option Style */
        .status-option-label {
            cursor: pointer;
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            padding: 12px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-option-label:hover {
            background: #f8fafc;
        }

        .status-radio:checked+.status-option-label {
            border-color: var(--blue-main);
            background: #eff6ff;
            color: var(--blue-main);
            font-weight: 600;
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
                    class="form-control form-control-sm search-input" placeholder="Cari judul form..."
                    style="width: 240px;">
                <button class="btn btn-sm btn-light border" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>

            {{-- Create Button --}}
            <a href="{{ route('admin.form.create') }}" class="btn btn-sm btn-primary px-3 shadow-sm"
                style="border-radius: 8px;">
                <i class="bi bi-plus-lg me-1"></i> Buat Form Baru
            </a>
        </div>

        {{-- Table Content --}}
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th>Informasi Form</th>
                        <th>Target & Jadwal</th>
                        <th>Status</th>
                        {{-- Lebar diperbesar agar 5 tombol muat --}}
                        <th style="width: 200px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($forms as $form)
                        @php
                            $map = [
                                'publish' => [
                                    'class' => 'badge-soft-success',
                                    'icon' => 'bi-check-circle',
                                    'label' => 'Published',
                                ],
                                'closed' => [
                                    'class' => 'badge-soft-danger',
                                    'icon' => 'bi-x-circle',
                                    'label' => 'Closed',
                                ],
                            ];
                            $statusConfig = $map[$form->status] ?? [
                                'class' => 'badge-soft-secondary',
                                'icon' => 'bi-pencil-square',
                                'label' => 'Draft',
                            ];
                        @endphp
                        <tr>
                            <td class="text-center text-muted">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-start gap-2">
                                    <div class="bg-light rounded p-2 text-primary">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $form->judul }}</div>
                                        <div class="text-muted small text-truncate" style="max-width: 250px;">
                                            {{ $form->deskripsi ?? 'Tidak ada deskripsi' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <div>
                                        <span class="badge-soft badge-soft-info py-1" style="font-size: 10px;">
                                            <i class="bi bi-people"></i> {{ ucfirst($form->target_peserta) }}
                                        </span>
                                    </div>
                                  <div class="text-muted" style="font-size: 11px;">
    <div><i class="bi bi-calendar-event me-1"></i>
        {{ $form->waktu_mulai->format('d M/y H:i') }}</div>
    <div><i class="bi bi-arrow-right me-1"></i>
        {{ $form->waktu_selesai->format('d M/y H:i') }}</div>
    {{-- TAMBAHAN DURASI DI SINI --}}
    <div class="mt-1 text-primary fw-bold">
        <i class="bi bi-stopwatch me-1"></i> Durasi: {{ $form->durasi ?? 90 }} Menit
    </div>
</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-soft {{ $statusConfig['class'] }}">
                                    <i class="bi {{ $statusConfig['icon'] }}"></i> {{ $statusConfig['label'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">

                                    {{-- 1. Kelola Soal (Primary Action) --}}
                                    <a href="{{ route('admin.form.kelola-soal', $form->id) }}"
                                        class="btn btn-icon btn-outline-primary" title="Atur Soal" data-bs-toggle="tooltip">
                                        <i class="bi bi-list-check"></i>
                                    </a>

                                    {{-- 2. Ubah Status (Modal) --}}
                                    <button type="button" class="btn btn-icon btn-outline-secondary btn-status-modal"
                                        data-id="{{ $form->id }}" data-judul="{{ $form->judul }}"
                                        data-status="{{ $form->status }}" title="Ubah Status" data-bs-toggle="tooltip">
                                        <i class="bi bi-gear"></i>
                                    </button>

                                    {{-- 3. Edit Detail --}}
                                    <a href="{{ route('admin.form.edit', $form->id) }}"
                                        class="btn btn-icon btn-outline-warning" title="Edit Detail"
                                        data-bs-toggle="tooltip">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    {{-- 4. Hasil Ujian --}}
                                    <a href="{{ route('admin.form.hasil', $form->id) }}"
                                        class="btn btn-icon btn-outline-success" title="Lihat Hasil & Nilai"
                                        data-bs-toggle="tooltip">
                                        <i class="bi bi-trophy"></i>
                                    </a>

                                    {{-- 5. Hapus --}}
                                    <form action="{{ route('admin.form.destroy', $form->id) }}" method="POST"
                                        class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-outline-danger" title="Hapus"
                                            data-bs-toggle="tooltip">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted mb-2">
                                    <i class="bi bi-clipboard-x display-6 opacity-25"></i>
                                </div>
                                <span class="text-muted small">Belum ada data form yang dibuat.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL UBAH STATUS --}}
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="statusModalLabel">Ubah Status Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-4">Pilih status baru untuk form: <br><strong id="modal-form-title"
                            class="text-dark">...</strong></p>

                    {{-- Form dalam Modal --}}
                    <form id="statusForm" action="" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="d-flex flex-column gap-2">
                            {{-- Option: Publish --}}
                            <div>
                                <input type="radio" class="d-none status-radio" name="status" value="publish"
                                    id="status_publish">
                                <label for="status_publish" class="status-option-label">
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <div>
                                        <div class="small fw-bold">Publish (Aktif)</div>
                                        <div class="text-muted" style="font-size: 10px;">Peserta dapat melihat dan mengisi
                                            form ini.</div>
                                    </div>
                                </label>
                            </div>

                            {{-- Option: Draft --}}
                            <div>
                                <input type="radio" class="d-none status-radio" name="status" value="draft"
                                    id="status_draft">
                                <label for="status_draft" class="status-option-label">
                                    <i class="bi bi-pencil-square text-secondary fs-5"></i>
                                    <div>
                                        <div class="small fw-bold">Draft (Konsep)</div>
                                        <div class="text-muted" style="font-size: 10px;">Form disembunyikan dari peserta.
                                        </div>
                                    </div>
                                </label>
                            </div>

                            {{-- Option: Closed --}}
                            <div>
                                <input type="radio" class="d-none status-radio" name="status" value="closed"
                                    id="status_closed">
                                <label for="status_closed" class="status-option-label">
                                    <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                    <div>
                                        <div class="small fw-bold">Closed (Tutup)</div>
                                        <div class="text-muted" style="font-size: 10px;">Form tidak lagi menerima respons
                                            baru.</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="mt-4 d-grid">
                            <button type="submit" class="btn btn-primary" style="border-radius: 8px;">Simpan Perubahan
                                Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- SweetAlert JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Init Tooltip
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // 1. Inisialisasi Modal
            var statusModal = new bootstrap.Modal(document.getElementById('statusModal'));

            // 2. Event Listener untuk Tombol Trigger Modal
            document.querySelectorAll('.btn-status-modal').forEach(button => {
                button.addEventListener('click', function() {
                    let id = this.getAttribute('data-id');
                    let judul = this.getAttribute('data-judul');
                    let currentStatus = this.getAttribute('data-status');

                    // Set URL Form Action (Ganti ID dummy dengan ID asli)
                    let formAction = "{{ route('admin.form.update-status', ':id') }}";
                    formAction = formAction.replace(':id', id);
                    document.getElementById('statusForm').action = formAction;

                    // Set Judul di Modal
                    document.getElementById('modal-form-title').innerText = judul;

                    // Set Radio Button yang sesuai
                    // Reset dulu
                    document.querySelectorAll('.status-radio').forEach(r => r.checked = false);

                    // Cek radio yang valuenya sama dengan status saat ini
                    let radioToCheck = document.querySelector(
                        `.status-radio[value="${currentStatus}"]`);
                    if (radioToCheck) radioToCheck.checked = true;

                    // Tampilkan Modal
                    statusModal.show();
                });
            });

            // 3. Handle Flash Messages
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            // 4. Handle Konfirmasi Hapus
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Form ini beserta data peserta & nilai terkait akan dihapus secara permanen!",
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
