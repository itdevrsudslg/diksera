@extends('layouts.app')

@php
    $pageTitle = 'Buat Form Baru';
    $pageSubtitle = 'Buat jadwal ujian atau formulir pengumpulan data.';
@endphp

@section('title', 'Buat Form – Admin DIKSERA')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <style>
        /* Global Card */
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-soft);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 32px;
        }

        /* Form Styles */
        .form-control-custom {
            border-radius: 8px;
            border: 1px solid var(--border-soft);
            padding: 10px 12px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-control-custom:focus {
            border-color: var(--blue-main);
            box-shadow: 0 0 0 3px var(--blue-soft);
        }

        .form-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        /* Target Selection Cards */
        .target-option {
            cursor: pointer;
        }

        .target-card {
            border: 1px solid var(--border-soft);
            border-radius: 10px;
            padding: 15px;
            background: #f8fafc;
            transition: all 0.2s;
            height: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .target-radio:checked+.target-card {
            border-color: var(--blue-main);
            background: var(--blue-soft);
            box-shadow: 0 0 0 2px var(--blue-soft);
        }

        .target-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--text-muted);
            border: 1px solid var(--border-soft);
        }

        .target-radio:checked+.target-card .target-icon {
            background: var(--blue-main);
            color: #fff;
            border-color: var(--blue-main);
        }

        /* Participant List Item Styling */
        .participant-item {
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            padding: 12px;
            transition: all 0.2s;
            background: #fff;
            cursor: pointer;
            height: 100%;
            position: relative;
        }

        .participant-item:hover {
            border-color: var(--blue-main);
            background: #f8fbff;
        }

        .participant-item.urgent {
            border-color: #fca5a5;
            background: #fef2f2;
        }

        .participant-item.urgent:hover {
            border-color: #dc2626;
        }

        .custom-scroll {
            max-height: 350px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .badge-doc {
            font-size: 10px;
            padding: 3px 6px;
            border-radius: 4px;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Choices JS Customization */
        .choices__inner {
            background-color: #fff;
            border-radius: 8px;
            border: 1px solid #ced4da;
            min-height: 45px;
            display: flex;
            align-items: center;
        }

        .choices__list--multiple .choices__item {
            background-color: var(--blue-main);
            border: 1px solid var(--blue-main);
        }

        .choices.is-focused .choices__inner {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
@endpush

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('admin.form.index') }}" class="btn btn-sm btn-outline-secondary px-3"
                    style="border-radius: 8px;">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Form
                </a>
            </div>

            <form action="{{ route('admin.form.store') }}" method="POST">
                @csrf
                <div class="content-card">
                    <h5 class="mb-4 fw-bold text-dark border-bottom pb-3">Buat Formulir Baru</h5>

                    {{-- 1. Informasi Dasar --}}
                    <div class="mb-4">
                        <label class="form-label">Judul Formulir / Ujian <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control form-control-custom fw-bold"
                            placeholder="Contoh: Ujian Kompetensi Perawat 2024" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Deskripsi / Petunjuk</label>
                        <textarea name="deskripsi" class="form-control form-control-custom" rows="4"
                            placeholder="Tuliskan deskripsi singkat atau instruksi pengerjaan..."></textarea>
                    </div>

                    {{-- 2. Jadwal --}}
                    {{-- 2. Jadwal --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="waktu_mulai" class="form-control form-control-custom" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="waktu_selesai" class="form-control form-control-custom" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Durasi (Menit) <span class="text-danger">*</span></label>
                            <input type="number" name="durasi" class="form-control form-control-custom" placeholder="Contoh: 90" required min="1">
                        </div>
                    </div>

                    <hr class="border-light my-4">
                    {{-- Penanggung Jawab --}}
                    <div class="mb-4">
                        <label class="form-label">Penanggung Jawab <span class="text-danger">*</span></label>
                        <select name="penanggung_jawab_id" id="choices-penanggung-jawab"
                            class="form-select form-control-custom" required>
                            <option value="">-- Pilih Penanggung Jawab --</option>
                            @foreach ($pjs as $pj)
                                <option value="{{ $pj->id }}">
                                    {{ $pj->nama }} ({{ $pj->jabatan }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <hr class="border-light my-4">

                    {{-- 3. Target Peserta (UPDATED 3 KOLOM) --}}
                    <div class="mb-4">
                        <label class="form-label mb-3">Siapa yang dapat mengakses form ini?</label>
                        <div class="row g-3">
                            {{-- Opsi 1: Semua --}}
                            <div class="col-md-4">
                                <label class="target-option w-100 h-100">
                                    <input class="form-check-input d-none target-radio" type="radio" name="target_peserta"
                                        value="semua" checked onclick="toggleTarget('semua')">
                                    <div class="target-card">
                                        <div class="target-icon"><i class="bi bi-people"></i></div>
                                        <div>
                                            <div class="fw-bold text-dark">Semua</div>
                                            <div class="text-muted small" style="font-size: 10px;">Semua perawat terdaftar.
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            {{-- Opsi 2: KFK (BARU) --}}
                            <div class="col-md-4">
                                <label class="target-option w-100 h-100">
                                    <input class="form-check-input d-none target-radio" type="radio" name="target_peserta"
                                        value="kfk" onclick="toggleTarget('kfk')">
                                    <div class="target-card">
                                        <div class="target-icon"><i class="bi bi-diagram-3"></i></div>
                                        <div>
                                            <div class="fw-bold text-dark">By KFK</div>
                                            <div class="text-muted small" style="font-size: 10px;">Sesuai level kompetensi.
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            {{-- Opsi 3: Khusus --}}
                            <div class="col-md-4">
                                <label class="target-option w-100 h-100">
                                    <input class="form-check-input d-none target-radio" type="radio" name="target_peserta"
                                        value="khusus" onclick="toggleTarget('khusus')">
                                    <div class="target-card">
                                        <div class="target-icon"><i class="bi bi-person-check"></i></div>
                                        <div>
                                            <div class="fw-bold text-dark">Manual</div>
                                            <div class="text-muted small" style="font-size: 10px;">Pilih orang tertentu.
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

{{-- A. Wrapper untuk KFK (BARU) --}}
<div id="list-kfk-container" class="mb-4" style="{{ (old('target_peserta', $form->target_peserta ?? '') == 'kfk') ? '' : 'display: none;' }}">
    <label class="form-label fw-bold">Pilih Level KFK Target <span class="text-danger">*</span></label>
    
    <select name="kfk_target[]" id="choices-kfk" multiple class="form-select @error('kfk_target') is-invalid @enderror">
        @foreach ($kfkOptions as $category => $kfks)
            <optgroup label="{{ $category }}">
                @foreach ($kfks as $kfk)
                    <option value="{{ $kfk }}" 
                        @if(isset($selectedKfk) && in_array($kfk, $selectedKfk))
                            selected
                        @elseif(is_array(old('kfk_target')) && in_array($kfk, old('kfk_target')))
                            selected
                        @endif>
                        {{ $kfk }}
                    </option>
                @endforeach
            </optgroup>
        @endforeach
    </select>

    @error('kfk_target')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    <div class="form-text text-muted small mt-2">
        <i class="bi bi-info-circle me-1"></i> Form hanya akan muncul pada perawat yang memiliki salah satu KFK yang dipilih di atas (berdasarkan Lisensi Terakhir).
    </div>
</div>

                    {{-- B. Wrapper untuk Peserta Khusus (LAMA) --}}
                    <div id="list-peserta-container" class="mb-4" style="display: none;">
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <label class="form-label mb-0">Pilih Perawat Manual <span class="text-danger">*</span></label>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25"
                                style="font-size: 11px;">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Merah = Dokumen Expired
                            </span>
                        </div>

                        {{-- Search Bar --}}
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="bi bi-search text-muted"></i></span>
                            <input type="text" id="search-peserta"
                                class="form-control form-control-custom border-start-0 ps-0"
                                placeholder="Cari nama, email, atau NIP...">
                        </div>

                        {{-- List Wrapper --}}
                        <div class="bg-light p-3 rounded-3 border">
                            <div class="custom-scroll">
                                <div class="row g-2" id="peserta-list-wrapper">
                                    @foreach ($users as $user)
                                        @php
                                            $warnings = $user->dokumen_warning ?? [];
                                            $isUrgent = count($warnings) > 0;
                                            $searchText = strtolower(
                                                $user->name . ' ' . ($user->email ?? '') . ' ' . ($user->nip ?? ''),
                                            );
                                        @endphp

                                        <div class="col-md-6 peserta-item-col" data-search="{{ $searchText }}">
                                            <label
                                                class="participant-item d-flex align-items-start gap-3 w-100 {{ $isUrgent ? 'urgent' : '' }}"
                                                for="user_{{ $user->id }}">
                                                <div class="pt-1">
                                                    <input class="form-check-input" type="checkbox" name="participants[]"
                                                        value="{{ $user->id }}" id="user_{{ $user->id }}"
                                                        style="cursor: pointer; transform: scale(1.1);">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <span
                                                            class="fw-bold {{ $isUrgent ? 'text-danger' : 'text-dark' }}"
                                                            style="font-size: 13px;">{{ $user->name }}</span>
                                                        @if ($isUrgent)
                                                            <i class="bi bi-exclamation-circle-fill text-danger"
                                                                style="font-size: 14px;"></i>
                                                        @endif
                                                    </div>
                                                    <div class="text-muted mb-1" style="font-size: 11px;">
                                                        {{ $user->email ?? ($user->nip ?? '-') }}</div>
                                                    @if ($isUrgent)
                                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                                            @foreach ($warnings as $docName)
                                                                <span class="badge-doc">{{ $docName }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div id="no-result-msg" class="text-center text-muted py-4" style="display: none;">
                                    <i class="bi bi-search display-6 opacity-25"></i>
                                    <p class="small mt-2">Data tidak ditemukan.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <button type="reset" class="btn btn-light px-4" style="border-radius: 8px;">Reset</button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 8px;">
                            <i class="bi bi-save me-1"></i> Simpan Form
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Load Choices JS --}}
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // --- 1. CHOICES JS: PENANGGUNG JAWAB ---
            const elementPj = document.getElementById('choices-penanggung-jawab');
            if (elementPj) {
                new Choices(elementPj, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholderValue: '-- Cari Penanggung Jawab --',
                    shouldSort: false,
                });
            }

            // --- 2. CHOICES JS: KFK (MULTI SELECT) ---
            const elementKfk = document.getElementById('choices-kfk');
            if (elementKfk) {
                new Choices(elementKfk, {
                    removeItemButton: true,
                    searchEnabled: true,
                    placeholderValue: 'Cari dan pilih KFK...',
                    itemSelectText: 'Tekan untuk memilih',
                    shouldSort: false,
                });
            }

            // --- 3. HANDLE SEARCH PESERTA ---
            const searchInput = document.getElementById('search-peserta');
            if (searchInput) {
                searchInput.addEventListener('keyup', function(e) {
                    const keyword = e.target.value.toLowerCase();
                    const items = document.querySelectorAll('.peserta-item-col');
                    let visibleCount = 0;

                    items.forEach(function(item) {
                        const searchData = item.getAttribute('data-search');
                        if (searchData.includes(keyword)) {
                            item.style.display = 'block';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    const noMsg = document.getElementById('no-result-msg');
                    if (noMsg) noMsg.style.display = (visibleCount === 0) ? 'block' : 'none';
                });
            }

            // --- 4. HANDLE INITIAL STATE (OLD INPUT) ---
            const selected = document.querySelector('input[name="target_peserta"]:checked');
            if (selected) {
                toggleTarget(selected.value);
            }
        });

        // --- 5. FUNCTION TOGGLE TARGET ---
        function toggleTarget(val) {
            const containerPeserta = document.getElementById('list-peserta-container');
            const containerKfk = document.getElementById('list-kfk-container');

            // Reset Display
            if (containerPeserta) containerPeserta.style.display = 'none';
            if (containerKfk) containerKfk.style.display = 'none';

            // Show Logic
            if (val === 'khusus') {
                containerPeserta.style.display = 'block';
                fadeIn(containerPeserta);
            } else if (val === 'kfk') {
                containerKfk.style.display = 'block';
                fadeIn(containerKfk);
            }
        }

        function fadeIn(element) {
            element.style.opacity = 0;
            setTimeout(() => {
                element.style.opacity = 1;
                element.style.transition = 'opacity 0.3s';
            }, 10);
        }

        // --- 6. SWEETALERT ---
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @endif
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}"
            });
        @endif
        @if ($errors->any())
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: "Mohon periksa kembali inputan Anda."
            });
        @endif
    </script>
@endpush
