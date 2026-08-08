@extends('layouts.app')

@php
    $pageTitle = 'Bank Item RKK';
    $pageSubtitle = 'Kelola repositori Rincian Kewenangan Klinis (Import 5 Kolom).';
@endphp

@section('title', 'Bank Item RKK – Admin DIKSERA')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
        .content-card {
            background: #ffffff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 24px;
        }
        .toolbar-container {
            background: var(--bg-light);
            border-radius: var(--radius-md);
            padding: 16px;
            border: 1px solid var(--border-color);
        }
        .btn-theme {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .btn-theme-primary { background: var(--primary-color); color: white; border: none; }
        .btn-theme-success { background: var(--success-color); color: white; border: none; }
        .btn-theme-outline { background: white; color: #64748b; border: 1px solid var(--border-color); }
        .table-custom thead th {
            background-color: #f1f5f9; color: #475569; font-size: 11px;
            text-transform: uppercase; padding: 14px; border: none; white-space: nowrap;
        }
        .table-custom td { padding: 16px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    </style>
@endpush

@section('content')
    <div class="content-card">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold mb-1">Bank Item RKK</h4>
                <p class="text-muted small mb-0">Total <span class="badge bg-light text-dark border">{{ $items->total() }}</span> item RKK tersedia.</p>
            </div>
            <a href="{{ route('admin.bank-item-rkk.create') }}" class="btn btn-theme btn-theme-primary shadow-sm">
                <i class="bi bi-plus-lg"></i> Tambah Item Manual
            </a>
        </div>

        <div class="toolbar-container mb-4">
            <form action="{{ route('admin.bank-item-rkk.index') }}" method="GET">
                <div class="row g-3 align-items-center">
                    <div class="col-lg-8 d-flex gap-2 flex-wrap">
                        <select name="per_page" class="form-select" style="width: auto; min-width: 80px;" onchange="this.form.submit()">
                            @foreach([10, 20, 30, 40, 50, 100] as $limit)
                                <option value="{{ $limit }}" {{ request('per_page', 10) == $limit ? 'selected' : '' }}>{{ $limit }}</option>
                            @endforeach
                        </select>
                        
                        <div class="position-relative flex-grow-1" style="min-width: 150px;">
                            <i class="bi bi-search position-absolute text-muted" style="top: 10px; left: 12px;"></i>
                            <input type="text" name="search" class="form-control ps-5" placeholder="Cari deskripsi kompetensi..." value="{{ request('search') }}">
                        </div>
                        
                        <select name="jenjang_klinis" id="jenjang-select" class="form-select" style="min-width: 150px;">
                            <option value="">Semua Jenjang</option>
                            @foreach ($jenjangKlinis as $jenjang)
                                <option value="{{ $jenjang }}" {{ request('jenjang_klinis') == $jenjang ? 'selected' : '' }}>{{ $jenjang }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="d-none">Cari</button>
                    </div>

                    <div class="col-lg-4 d-flex justify-content-lg-end gap-2 flex-wrap">
                        <button type="button" class="btn btn-theme btn-theme-outline" onclick="downloadTemplate()">
                            <i class="bi bi-file-earmark-arrow-down"></i> Template
                        </button>
                        <div class="h-divider mx-1 d-none d-lg-block" style="width: 1px; background: #e2e8f0; height: 30px;"></div>
                        <button type="button" class="btn btn-theme btn-theme-success" onclick="exportExcel()">
                            <i class="bi bi-file-earmark-spreadsheet"></i> Export
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
                        <th>KFK</th>
                        <th>Jenjang Klinis</th>
                        <th>Kategori Tugas</th>
                        <th>Deskripsi Kompetensi</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td class="text-center text-muted fw-bold">
                                {{ $item->no_item ?? ($loop->iteration + ($items->firstItem() ? $items->firstItem() - 1 : 0)) }}
                            </td>
                            <td>
                                @if($item->kfk)
                                    <span class="badge bg-light text-dark border">{{ $item->kfk }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->jenjang_klinis)
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded">{{ $item->jenjang_klinis }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $item->kategori_tugas ?? '-' }}</td>
                            <td>
                                <div class="fw-semibold text-dark mb-1" style="font-size: 14px;">{{ $item->deskripsi_kompetensi }}</div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.bank-item-rkk.edit', $item->id) }}" class="btn btn-sm btn-light border text-warning"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('admin.bank-item-rkk.delete', $item->id) }}" method="POST" class="delete-form">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-light border text-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Data Item RKK tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $items->withQueryString()->links('vendor.pagination.diksera') }}
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="fw-bold">Import Data Excel (RKK)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center p-4 border border-2 border-dashed rounded-4 bg-light mb-3">
                        <i class="bi bi-file-earmark-excel text-success fs-1 mb-2 d-block"></i>
                        <input type="file" id="fileExcel" class="form-control" accept=".xlsx, .xls">
                        <p class="text-muted smallest mt-2 mb-0">Pastikan 5 format kolom sesuai dengan template Excel.</p>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-theme btn-theme-success px-4" onclick="processImport()">Proses Sekarang</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2 untuk Filter
            $('#jenjang-select').select2({ placeholder: "Cari Jenjang Klinis...", allowClear: true });
            $('#jenjang-select').on('change', function(e) {
                if (e.originalEvent || e.type === 'change') $(this).closest('form').submit();
            });
        });

        function downloadTemplate() {
            const templateData = [{
                "KFK": "Keperawatan",
                "Jenjang_Klinis": "PK I",
                "Kategori_Tugas": "Tugas Pokok",
                "No_Item": 1,
                "Deskripsi_Kompetensi": "Terlaksananya pengkajian pasien"
            }];
            const ws = XLSX.utils.json_to_sheet(templateData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Data Import Kompetensi");
            XLSX.writeFile(wb, 'Template_Item_RKK.xlsx');
        }

        async function exportExcel() {
            try {
                Swal.fire({ title: 'Menyiapkan Data...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                let exportUrl = '{{ route("admin.bank-item-rkk.export-json") }}' + window.location.search;
                let response = await axios.get(exportUrl);
                
                if (response.data.length === 0) return Swal.fire('Kosong', 'Tidak ada data untuk diexport', 'info');

                const ws = XLSX.utils.json_to_sheet(response.data);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Bank Item RKK");
                XLSX.writeFile(wb, 'Export_Bank_Item_RKK.xlsx');
                Swal.close();
            } catch (error) {
                Swal.fire('Error', 'Gagal mengekspor data', 'error');
            }
        }

        function processImport() {
            const fileInput = document.getElementById('fileExcel');
            if (!fileInput.files.length) return Swal.fire('Oops', 'Pilih file dahulu', 'info');

            Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            const reader = new FileReader();
            reader.onload = function(e) {
                const workbook = XLSX.read(new Uint8Array(e.target.result), { type: 'array' });
                const jsonData = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]]);
                
                axios.post('{{ route('admin.bank-item-rkk.import-json') }}', jsonData)
                    .then(() => location.reload())
                    .catch(() => Swal.fire('Error', 'Format tidak sesuai atau terjadi kesalahan internal', 'error'));
            };
            reader.readAsArrayBuffer(fileInput.files[0]);
        }
    </script>
@endpush