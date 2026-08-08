@extends('layouts.app')

@php
    $pageTitle = 'Atur Soal Ujian';
    $pageSubtitle = 'Pilih pertanyaan dari bank soal atau generate secara acak.';
@endphp

@section('title', 'Kelola Soal – Admin DIKSERA')

@push('styles')
    <style>
        /* Container Utama yang Flexibel */
        .page-container {
            height: calc(100vh - 120px);
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* Card Container Full Height */
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-soft);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            flex: 1;
            overflow: hidden;
            padding: 0;
        }

        /* Toolbar Area (Generator & Search) */
        .toolbar-section {
            padding: 20px;
            background: #f8fafc;
            border-bottom: 1px solid var(--border-soft);
        }

        /* Generator Box */
        .generator-box {
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 15px;
            transition: all 0.2s;
        }

        .generator-box:hover {
            border-color: var(--blue-main);
            background: #f0f9ff;
        }

        /* Search Input Styles */
        .search-group {
            position: relative;
        }

        .search-input {
            border-radius: 8px;
            padding-left: 38px;
            height: 40px;
            font-size: 13px;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        /* Table Area */
        .table-wrapper {
            flex: 1;
            overflow-y: auto;
            position: relative;
        }

        .table-custom th {
            background-color: #ffffff;
            color: var(--text-main);
            font-weight: 700;
            font-size: 12px;
            border-bottom: 2px solid #e2e8f0;
            text-transform: uppercase;
            padding: 14px 16px;
            vertical-align: middle;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .table-custom td {
            vertical-align: middle;
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }

        /* Row Interaction */
        .table-custom tbody tr {
            cursor: pointer;
            transition: background 0.1s;
        }

        .table-custom tbody tr:hover {
            background-color: #f8fafc;
        }

        .table-custom tbody tr.selected {
            background-color: #eff6ff;
        }

        .table-custom tbody tr.selected td {
            border-bottom-color: #dbeafe;
        }

        /* Footer Action Sticky */
        .action-footer {
            padding: 16px 24px;
            background: #ffffff;
            border-top: 1px solid var(--border-soft);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 20;
        }
    </style>
@endpush

@section('content')

    <div class="page-container">

        {{-- Header Navigation --}}
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1">Konfigurasi Soal</h4>
                <div class="text-muted small">
                    Form: <span class="fw-bold text-dark">{{ $form->judul }}</span>
                </div>
            </div>
            <a href="{{ route('admin.form.index') }}" class="btn btn-sm btn-outline-secondary px-3 rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="content-card">

            {{-- SECTION 1: TOOLBAR (Generator & Search/Filter) --}}
            <div class="toolbar-section">
                <div class="row g-3 align-items-center">

                    {{-- Kolom Kiri: Generator Multi-Kategori --}}
                    <div class="col-lg-7">
                        <div class="generator-box">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-magic"></i>
                                    Generator</span>
                                <span class="text-muted small fw-bold">Ambil Soal Acak</span>
                            </div>

                            <form action="{{ route('admin.form.generate-soal', $form->id) }}" method="POST"
                                class="row g-2">
                                @csrf
                                <div class="col-md-5">
                                    {{-- Dropdown Checkbox untuk Multi-Kategori Generator --}}
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary text-start w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                            Pilih Kategori...
                                        </button>
                                        <ul class="dropdown-menu w-100 p-2 shadow-sm" style="max-height: 200px; overflow-y: auto;">
                                            <li>
                                                <div class="form-check">
                                                    <input class="form-check-input gen-kategori-semua" type="checkbox" value="Semua" name="kategori[]" id="gen_semua" checked>
                                                    <label class="form-check-label small fw-bold" for="gen_semua">Semua Kategori</label>
                                                </div>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            @if(isset($kategoris))
                                                @foreach($kategoris as $kat)
                                                <li>
                                                    <div class="form-check">
                                                        <input class="form-check-input gen-kategori-item" type="checkbox" value="{{ $kat }}" name="kategori[]" id="gen_{{ Str::slug($kat) }}">
                                                        <label class="form-check-label small" for="gen_{{ Str::slug($kat) }}">{{ $kat }}</label>
                                                    </div>
                                                </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="jumlah_soal" class="form-control form-control-sm"
                                        placeholder="Jml" min="1" required>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-sm btn-dark w-100">
                                        <i class="bi bi-plus-lg me-1"></i> Generate
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Search & Filters --}}
                    <div class="col-lg-5">
                        <div class="d-flex flex-column gap-2 h-100 justify-content-center">
                            {{-- Search Input --}}
                            <div class="search-group">
                                <i class="bi bi-search search-icon"></i>
                                <input type="text" id="searchInput" class="form-control search-input"
                                    placeholder="Cari pertanyaan...">
                                <button
                                    class="btn btn-sm btn-link text-muted position-absolute end-0 top-50 translate-middle-y text-decoration-none"
                                    id="resetSearch" style="display: none;">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                            </div>

                            {{-- Filter Tools (Kategori & Hanya Terpilih) --}}
                            <div class="d-flex gap-2 align-items-center justify-content-between">
                                {{-- Switch Hanya Terpilih --}}
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" id="filterSelectedOnly">
                                    <label class="form-check-label small fw-bold" for="filterSelectedOnly">Lihat Terpilih Saja</label>
                                </div>
                                
                                {{-- Dropdown Filter Kategori Multi-select --}}
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                        <i class="bi bi-funnel"></i> Kategori
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2 shadow-sm" style="max-height: 250px; overflow-y: auto;">
                                        @if(isset($kategoris))
                                            @foreach($kategoris as $kat)
                                            <li>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-kategori" type="checkbox" value="{{ $kat }}" id="kat_{{ Str::slug($kat) }}">
                                                    <label class="form-check-label small" for="kat_{{ Str::slug($kat) }}">{{ $kat }}</label>
                                                </div>
                                            </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- SECTION 2: TABLE LIST & FORM SAVE --}}
            <form action="{{ route('admin.form.simpan-soal', $form->id) }}" method="POST"
                class="d-flex flex-column flex-grow-1 overflow-hidden">
                @csrf

                {{-- Scrollable Table Wrapper --}}
                <div class="table-wrapper custom-scroll">
                    <table class="table table-custom mb-0" id="soalTable">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">
                                    <input type="checkbox" class="form-check-input" id="checkAll">
                                </th>
                                <th>Pertanyaan</th>
                                <th width="150">Kategori</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allSoals as $soal)
                                @php $isSelected = in_array($soal->id, $existingSoalIds); @endphp
                                <tr class="{{ $isSelected ? 'selected' : '' }} searchable-row" 
                                    data-kategori="{{ $soal->kategori }}" 
                                    onclick="toggleRow(event, this)">
                                    <td class="text-center">
                                        <input type="checkbox" name="soal_ids[]" value="{{ $soal->id }}"
                                            class="form-check-input soal-checkbox" {{ $isSelected ? 'checked' : '' }}
                                            onchange="highlightRow(this)">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark mb-1" style="line-height: 1.4;">
                                            {{ $soal->pertanyaan }}
                                        </div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="badge bg-light text-dark border px-2 py-1"
                                                style="font-size: 10px;">
                                                <i class="bi bi-key me-1"></i> {{ strtoupper($soal->kunci_jawaban) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-10">
                                            {{ $soal->kategori }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <div
                                            class="d-flex flex-column align-items-center justify-content-center opacity-50">
                                            <i class="bi bi-inbox display-4 mb-2"></i>
                                            <p class="m-0">Belum ada data soal.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Sticky Footer --}}
                <div class="action-footer shadow-sm">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill text-primary"></i>
                        <span class="text-muted small">Terpilih:</span>
                        <span class="fw-bold fs-5 text-dark" id="selectedCount">{{ count($existingSoalIds) }}</span>
                        <span class="text-muted small">Soal</span>
                    </div>

                    <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm rounded-3 fw-bold">
                        <i class="bi bi-save me-2"></i> Simpan Konfigurasi
                    </button>
                </div>

            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const table = document.getElementById('soalTable');
            const searchInput = document.getElementById('searchInput');
            const resetSearchBtn = document.getElementById('resetSearch');
            const rows = document.querySelectorAll('.searchable-row');
            const checkAll = document.getElementById('checkAll');
            const selectedCountSpan = document.getElementById('selectedCount');
            const filterSelectedOnly = document.getElementById('filterSelectedOnly');
            const kategoriFilters = document.querySelectorAll('.filter-kategori');

            // --- 1. Selection Logic ---
            function updateCounter() {
                const checkedBoxes = document.querySelectorAll('.soal-checkbox:checked');
                selectedCountSpan.innerText = checkedBoxes.length;
            }

            window.highlightRow = function(checkbox) {
                const row = checkbox.closest('tr');
                if (checkbox.checked) {
                    row.classList.add('selected');
                } else {
                    row.classList.remove('selected');
                }
                updateCounter();
            }

            window.toggleRow = function(event, row) {
                // Mencegah double select saat user klik checkbox langsung
                if (event && event.target.tagName.toLowerCase() === 'input') {
                    highlightRow(event.target);
                    // Refresh view jika switch filter aktif
                    if (filterSelectedOnly.checked) performSearch(); 
                    return;
                }

                const checkbox = row.querySelector('.soal-checkbox');
                checkbox.checked = !checkbox.checked;
                highlightRow(checkbox);
                
                // Refresh view jika switch filter aktif
                if (filterSelectedOnly.checked) performSearch();
            }

            checkAll.addEventListener('change', function() {
                const isChecked = this.checked;
                rows.forEach(row => {
                    // Hanya centang baris yang visible (hasil search/filter)
                    if (row.style.display !== 'none') {
                        const cb = row.querySelector('.soal-checkbox');
                        cb.checked = isChecked;
                        highlightRow(cb);
                    }
                });
            });

            // --- 2. Advanced Search & Filter Logic ---
            window.performSearch = function() {
                const textFilter = searchInput.value.toLowerCase();
                const showSelectedOnly = filterSelectedOnly.checked;
                
                // Ambil kategori yang dicentang
                const checkedCategories = Array.from(document.querySelectorAll('.filter-kategori:checked')).map(cb => cb.value);

                resetSearchBtn.style.display = textFilter.length > 0 ? 'block' : 'none';

                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    const isSelected = row.querySelector('.soal-checkbox').checked;
                    const category = row.getAttribute('data-kategori');

                    // Syarat 1: Cocok dengan teks
                    let matchText = text.includes(textFilter);
                    
                    // Syarat 2: Cocok dengan kategori (jika kosong, anggap cocok semua)
                    let matchCategory = checkedCategories.length === 0 || checkedCategories.includes(category);
                    
                    // Syarat 3: Cocok dengan status terpilih
                    let matchSelected = !showSelectedOnly || isSelected;

                    // Tampilkan hanya jika KETIGA syarat terpenuhi
                    if (matchText && matchCategory && matchSelected) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // --- 3. Event Listeners (Table Filtering) ---
            searchInput.addEventListener('keyup', performSearch);
            
            resetSearchBtn.addEventListener('click', function() {
                searchInput.value = '';
                performSearch();
                searchInput.focus();
            });

            kategoriFilters.forEach(cb => cb.addEventListener('change', performSearch));
            filterSelectedOnly.addEventListener('change', performSearch);

            // --- 4. Logic Multi-Select Generator ---
            const genSemua = document.getElementById('gen_semua');
            const genItems = document.querySelectorAll('.gen-kategori-item');

            if(genSemua && genItems.length > 0) {
                genSemua.addEventListener('change', function() {
                    if (this.checked) {
                        genItems.forEach(cb => cb.checked = false);
                    }
                });

                genItems.forEach(cb => {
                    cb.addEventListener('change', function() {
                        if (this.checked) {
                            genSemua.checked = false;
                        }
                        const anyChecked = Array.from(genItems).some(item => item.checked);
                        if (!anyChecked) {
                            genSemua.checked = true;
                        }
                    });
                });
            }

            // Init
            updateCounter();
        });
    </script>
@endpush