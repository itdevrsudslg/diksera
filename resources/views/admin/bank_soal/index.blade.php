@extends('layouts.app')

@php
    $pageTitle = 'Bank Soal';
    $pageSubtitle = 'Kelola repositori pertanyaan, kunci jawaban, dan kategori soal.';
@endphp

@section('title', 'Bank Soal – Admin DIKSERA')

@push('styles')
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Tambahkan CSS Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary-color: #2563eb;
            --success-color: #10b981;
            --bg-light: #f8fafc;
            --border-color: #e2e8f0;
            --radius-md: 10px;
            --radius-lg: 14px;
        }
.select2-container .select2-selection--single {
            height: 38px !important;
            border-radius: 8px !important;
            border: 1px solid var(--border-color) !important;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #475569 !important;
            font-size: 14px;
        }
        .content-card {
            background: #ffffff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 24px;
        }

        /* Unified Toolbar Theme */
        .toolbar-container {
            background: var(--bg-light);
            border-radius: var(--radius-md);
            padding: 16px;
            border: 1px solid var(--border-color);
        }

        .form-control,
        .btn {
            border-radius: 8px;
            font-size: 14px;
        }

        /* Button Theme Consistency */
        .btn-theme {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            padding: 8px 16px;
            transition: all 0.2s;
        }

        .btn-theme-primary {
            background: var(--primary-color);
            color: white;
            border: none;
        }

        .btn-theme-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .btn-theme-success {
            background: var(--success-color);
            color: white;
            border: none;
        }

        .btn-theme-success:hover {
            background: #059669;
            transform: translateY(-1px);
        }

        .btn-theme-outline {
            background: white;
            color: #64748b;
            border: 1px solid var(--border-color);
        }

        .btn-theme-outline:hover {
            background: #f1f5f9;
            color: var(--primary-color);
        }

        /* Table Theme */
        .table-custom thead th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px;
            border: none;
        }

        .table-custom td {
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .option-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 11px;
            background: #f1f5f9;
            color: #64748b;
            margin: 2px;
            border: 1px solid transparent;
        }

        .option-badge.active {
            background: #ecfdf5;
            color: #059669;
            border-color: #a7f3d0;
            font-weight: 600;
        }

        .badge-key {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: var(--primary-color);
            color: white;
            font-weight: 700;
        }
    </style>
@endpush

@section('content')
    <div class="content-card">
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold mb-1">Bank Soal</h4>
                <p class="text-muted small mb-0">Total <span
                        class="badge bg-light text-dark border">{{ $soals->total() }}</span> data pertanyaan tersedia.</p>
            </div>
            <a href="{{ route('admin.bank-soal.create') }}" class="btn btn-theme btn-theme-primary shadow-sm">
                <i class="bi bi-plus-lg"></i> Tambah Soal Manual
            </a>
        </div>
<div class="toolbar-container mb-4">
    <form action="{{ route('admin.bank-soal.index') }}" method="GET">
        <div class="row g-3 align-items-center">
            
            <div class="col-lg-8 d-flex gap-2 flex-wrap">
                <!-- 1. Select Per Page -->
                <select name="per_page" class="form-select" style="width: auto; min-width: 80px;" onchange="this.form.submit()">
                    @foreach([10, 20, 30, 40, 50, 100] as $limit)
                        <option value="{{ $limit }}" {{ request('per_page', 10) == $limit ? 'selected' : '' }}>
                            {{ $limit }}
                        </option>
                    @endforeach
                </select>

                <!-- 2. Search Box -->
                <div class="position-relative flex-grow-1" style="min-width: 150px;">
                    <i class="bi bi-search position-absolute text-muted" style="top: 10px; left: 12px;"></i>
                    <input type="text" name="search" class="form-control ps-5" placeholder="Cari soal..." value="{{ request('search') }}">
                </div>
                
                <!-- 3. Kategori Select -->
                <select name="kategori" id="kategori-select" class="form-select" style="min-width: 150px;">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoris as $kategori)
                        <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                            {{ $kategori }}
                        </option>
                    @endforeach
                </select>

                <!-- 4. Filter Duplikat -->
                <select name="filter_tipe" class="form-select text-danger border-danger-subtle fw-semibold" style="min-width: 130px;" onchange="this.form.submit()">
                    <option value="">Semua Soal</option>
                    <option value="unik" {{ request('filter_tipe') == 'unik' ? 'selected' : '' }}>Soal Unik</option>
                    <option value="duplikat" {{ request('filter_tipe') == 'duplikat' ? 'selected' : '' }}>Soal Duplikat</option>
                </select>
                
                <button type="submit" class="d-none">Cari</button>
            </div>

            <div class="col-lg-4 d-flex justify-content-lg-end gap-2 flex-wrap">
                <button type="button" class="btn btn-theme btn-theme-outline" onclick="downloadTemplate()">
                    <i class="bi bi-file-earmark-arrow-down"></i> Template
                </button>
                <div class="h-divider mx-1 d-none d-lg-block" style="width: 1px; background: #e2e8f0; height: 30px;"></div>
                <button type="button" class="btn btn-theme btn-theme-success" onclick="exportExcel()">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Export Semua
                </button>
                <button type="button" class="btn btn-theme btn-theme-outline text-success border-success" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="bi bi-upload"></i> Import
                </button>
            </div>
            
        </div>
    </form>
</div>
        <div class="table-responsive border rounded-3">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th>Konten & Opsi</th>
                        <th style="width: 140px;">Kategori</th>
                        <th class="text-center" style="width: 80px;">Kunci</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($soals as $soal)
                        <tr>
                            <td class="text-center text-muted fw-bold">
                                {{ $loop->iteration + ($soals->firstItem() ? $soals->firstItem() - 1 : 0) }}</td>
                            <td>
                                <div class="fw-semibold text-dark mb-2" style="font-size: 14px;">
                                    {{ Str::limit($soal->pertanyaan, 100) }}</div>
                                <div class="d-flex flex-wrap">
                                    @foreach (['a', 'b', 'c', 'd', 'e'] as $opt)
                                        @if (isset($soal->opsi_jawaban[$opt]))
                                            <span
                                                class="option-badge {{ strtolower($soal->kunci_jawaban) == $opt ? 'active' : '' }}">
                                                {{ strtoupper($opt) }}. {{ Str::limit($soal->opsi_jawaban[$opt], 20) }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                            <td><span
                                    class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded">{{ $soal->kategori ?? 'Umum' }}</span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <div class="badge-key">{{ strtoupper($soal->kunci_jawaban) }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.bank-soal.edit', $soal->id) }}"
                                        class="btn btn-sm btn-light border text-warning"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('admin.bank-soal.delete', $soal->id) }}" method="POST"
                                        class="delete-form">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-light border text-danger"><i
                                                class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Data soal tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $soals->withQueryString()->links('vendor.pagination.diksera') }}
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="fw-bold">Import Data Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center p-4 border border-2 border-dashed rounded-4 bg-light mb-3">
                        <i class="bi bi-file-earmark-excel text-success fs-1 mb-2 d-block"></i>
                        <input type="file" id="fileExcel" class="form-control" accept=".xlsx, .xls">
                        <p class="text-muted smallest mt-2 mb-0">Pastikan format kolom sesuai dengan template.</p>
                    </div>
                    <button class="btn btn-theme btn-theme-outline w-100" onclick="downloadTemplate()">
                        <i class="bi bi-download"></i> Belum punya template? Unduh di sini
                    </button>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-theme btn-theme-success px-4" onclick="processImport()">Proses
                        Sekarang</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {
            // Inisialisasi Select2 pada dropdown kategori
            $('#kategori-select').select2({
                placeholder: "Cari Kategori...",
                allowClear: true
            });

            // Otomatis submit form ketika kategori dipilih atau dihapus
            $('#kategori-select').on('change', function(e) {
                // Pastikan yang men-trigger adalah user, bukan inisialisasi awal
                if (e.originalEvent || e.type === 'change') {
                    $(this).closest('form').submit();
                }
            });
        });
        // Gunakan script yang sama dengan sebelumnya, fungsi processImport dan downloadTemplate tetap valid.
        function downloadTemplate() {
            const templateData = [{
                "pertanyaan": "...",
                "kategori": "...",
                "opsi_a": "...",
                "opsi_b": "...",
                "opsi_c": "...",
                "opsi_d": "...",
                "opsi_e": "...",
                "kunci_jawaban": "a"
            }];
            const ws = XLSX.utils.json_to_sheet(templateData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Template");
            XLSX.writeFile(wb, 'Template_Soal.xlsx');
        }

    async function exportExcel() {
    try {
        Swal.fire({
            title: 'Menyiapkan Data...',
            text: 'Mengekstrak seluruh soal ke Excel',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        // Ambil query string dari URL saat ini agar data Excel sesuai dengan filter pencarian yang sedang aktif
        let currentUrlParams = window.location.search;
        let exportUrl = '{{ route("admin.bank-soal.export-json") }}' + currentUrlParams;

        let response = await axios.get(exportUrl);
        let dataToExport = response.data;

        if (dataToExport.length === 0) {
            Swal.fire('Kosong', 'Tidak ada data untuk diexport', 'info');
            return;
        }

        // Generate Excel dengan SheetJS
        const ws = XLSX.utils.json_to_sheet(dataToExport);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Bank Soal Lengkap");
        XLSX.writeFile(wb, 'Export_Bank_Soal_Full.xlsx');

        Swal.close();
    } catch (error) {
        Swal.fire('Error', 'Gagal mengekspor data', 'error');
        console.error(error);
    }
}

        function processImport() {
            const fileInput = document.getElementById('fileExcel');
            if (!fileInput.files.length) return Swal.fire('Oops', 'Pilih file dahulu', 'info');

            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            const reader = new FileReader();
            reader.onload = function(e) {
                const workbook = XLSX.read(new Uint8Array(e.target.result), {
                    type: 'array'
                });
                const jsonData = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]]);
                axios.post('{{ route('admin.bank-soal.import-json') }}', jsonData)
                    .then(() => location.reload())
                    .catch(() => Swal.fire('Error', 'Format tidak sesuai', 'error'));
            };
            reader.readAsArrayBuffer(fileInput.files[0]);
        }
    </script>
@endsection
