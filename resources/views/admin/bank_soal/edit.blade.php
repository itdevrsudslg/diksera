@extends('layouts.app')

@php
    $pageTitle = 'Edit Soal';
    $pageSubtitle = 'Perbarui pertanyaan, opsi jawaban, dan kunci jawaban.';
@endphp

@section('title', 'Edit Soal – Admin DIKSERA')

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

        /* Form Controls */
        .form-control-custom,
        .form-select-custom {
            border-radius: 8px;
            border: 1px solid var(--border-soft);
            padding: 10px 12px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-control-custom:focus,
        .form-select-custom:focus {
            border-color: var(--blue-main);
            box-shadow: 0 0 0 3px var(--blue-soft);
        }

        .form-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        /* Option Item Styling */
        .option-item {
            background: #f8fafc;
            border: 1px solid var(--border-soft);
            border-radius: 10px;
            padding: 10px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .option-item:focus-within {
            background: #fff;
            border-color: var(--blue-main);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Letter Box (A, B, C...) */
        .letter-box {
            width: 36px;
            height: 36px;
            background: #e2e8f0;
            color: #475569;
            font-weight: 700;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Radio Button Custom for Key Answer */
        .key-check-wrapper {
            display: flex;
            align-items: center;
            gap: 6px;
            padding-left: 12px;
            border-left: 1px solid var(--border-soft);
            white-space: nowrap;
        }

        .form-check-input:checked {
            background-color: #198754;
            /* Green for correct answer */
            border-color: #198754;
        }

        /* Highlight row if it is the key */
        .option-item.is-key {
            border-color: #198754;
            background-color: #f0fdf4;
        }

        .option-item.is-key .letter-box {
            background-color: #198754;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Tombol Kembali --}}
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('admin.bank-soal.index') }}" class="btn btn-sm btn-outline-secondary px-3"
                    style="border-radius: 8px;">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <form action="{{ route('admin.bank-soal.update', $soal->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST') 

                <div class="content-card">
                    <h5 class="mb-4 fw-bold text-dark border-bottom pb-3">Edit Soal</h5>

                    {{-- Pertanyaan --}}
                    <div class="mb-4">
                        <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                        <textarea name="pertanyaan" class="form-control form-control-custom" rows="4"
                            placeholder="Tuliskan pertanyaan disini..." required>{{ old('pertanyaan', $soal->pertanyaan) }}</textarea>
                        {{-- Di bawah textarea pertanyaan --}}
                        <div class="mb-4">
                            <label class="form-label">Gambar Pendukung (Opsional)</label>
                            @if ($soal->gambar)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $soal->gambar) }}" alt="Preview"
                                        style="max-width: 200px; max-height: 150px; object-fit: contain;" class="rounded border">
                                </div>
                            @endif
                            <input type="file" name="gambar" class="form-control" accept="image/*">
                        </div>
                    </div>

                    {{-- Kategori --}}
                    <div class="mb-4">
                        <label class="form-label">Kategori Soal <span class="text-danger">*</span></label>
                 <select name="kategori" id="choices-kategori" class="form-select form-select-custom">
    <option value="">-- Pilih Kategori --</option>
    @php
        $kategoriList = [
            // --- KFK KRITIS ---
            'KFK Kritis Gawat Darurat', 'KFK Kritis ICU', 'KFK Kritis ICVCU', 'KFK Kritis Anestesi',

            // --- KFK MEDIKAL BEDAH ---
            'KFK Medikal Bedah Penyakit Dalam', 'KFK Medikal Bedah Bedah', 'KFK Medikal Bedah Perioperatif', 'KFK Medikal Bedah Isolasi (Keperawatan Khusus)',

            // --- KFK ANAK ---
            'KFK Anak NICU', 'KFK Anak PICU', 'KFK Anak Pediatri', 'KFK Anak Neonatus',

            // --- KFK MATERNITAS ---
            'KFK Maternitas',

            // --- JENIS PERAWAT KLINIS (PK) ---
            'PRA PK', 'PK 1',
            'PK 2 KEP GAWAT DARURAT', 'PK 3 KEP GAWAT DARURAT', 'PK 4 KEP GAWAT DARURAT', 'PK 5 KEP GAWAT DARURAT',
            'PK 2 KEP ICU', 'PK 3 KEP ICU', 'PK 4 KEP ICU', 'PK 5 KEP ICU',
            'PK 2 KEP ICVCU', 'PK 3 KEP ICVCU', 'PK 4 KEP ICVCU', 'PK 5 KEP ICVCU',
            'PK 2 KEP ANESTESI', 'PK 3 KEP ANESTESI', 'PK 4 KEP ANESTESI', 'PK 5 KEP ANESTESI',
            'PK 2 KEP PENYAKIT DALAM', 'PK 3 KEP PENYAKIT DALAM', 'PK 4 KEP PENYAKIT DALAM', 'PK 5 KEP PENYAKIT DALAM',
            'PK 2 KEP BEDAH', 'PK 3 KEP BEDAH', 'PK 4 KEP BEDAH', 'PK 5 KEP BEDAH',
            'PK 2 KEP PERIOPERATIF', 'PK 3 KEP PERIOPERATIF', 'PK 4 KEP PERIOPERATIF', 'PK 5 KEP PERIOPERATIF',
            'PK 2 KEP ISOLASI (KEPERAWATAN KHUSUS)', 'PK 3 KEP ISOLASI (KEPERAWATAN KHUSUS)', 'PK 4 KEP ISOLASI (KEPERAWATAN KHUSUS)', 'PK 5 KEP ISOLASI (KEPERAWATAN KHUSUS)',
            'PK 2 KEP NICU', 'PK 3 KEP NICU', 'PK 4 KEP NICU', 'PK 5 KEP NICU',
            'PK 2 KEP PICU', 'PK 3 KEP PICU', 'PK 4 KEP PICU', 'PK 5 KEP PICU',
            'PK 2 KEP PEDIATRI', 'PK 3 KEP PEDIATRI', 'PK 4 KEP PEDIATRI', 'PK 5 KEP PEDIATRI',
            'PK 2 KEP NEONATUS', 'PK 3 KEP NEONATUS', 'PK 4 KEP NEONATUS', 'PK 5 KEP NEONATUS',

            // --- JENIS BIDAN KLINIS (BK) ---
            'PRA BK', 'BK 1', 'BK 2 MATERNITAS', 'BK 3 MATERNITAS', 'BK 4 MATERNITAS', 'BK 5 MATERNITAS'
        ];
    @endphp

    @foreach ($kategoriList as $kategori)
        <option value="{{ $kategori }}" {{ old('kategori') == $kategori ? 'selected' : '' }}>
            {{ $kategori }}
        </option>
    @endforeach
</select>
                    </div>

                    <hr class="border-light my-4">

                    {{-- Opsi Jawaban --}}
                    <div class="mb-2">
                        <label class="form-label mb-2">Opsi Jawaban & Kunci <span class="text-danger">*</span></label>
                        <p class="text-muted small mb-3">Isi teks jawaban dan tandai radio button di sebelah kanan untuk
                            jawaban yang <strong>Benar</strong>.</p>

                        <div class="d-flex flex-column gap-3">
                            @foreach (['a', 'b', 'c', 'd', 'e'] as $key)
                                @php
                                    $isKey = old('kunci_jawaban', $soal->kunci_jawaban) == $key;
                                @endphp
                                <div class="option-item {{ $isKey ? 'is-key' : '' }}" id="option-row-{{ $key }}">
                                    {{-- Huruf --}}
                                    <div class="letter-box">{{ strtoupper($key) }}</div>

                                    {{-- Input Teks --}}
                                    <div class="flex-grow-1">
                                        <input type="text" name="opsi[{{ $key }}]"
                                            class="form-control border-0 bg-transparent p-0 shadow-none"
                                            placeholder="Tulis jawaban opsi {{ strtoupper($key) }}..."
                                            value="{{ old('opsi.' . $key, $soal->opsi_jawaban[$key] ?? '') }}" required
                                            style="font-size: 14px;">
                                    </div>

                                    {{-- Radio Kunci Jawaban --}}
                                    <div class="key-check-wrapper">
                                        <input class="form-check-input key-radio" type="radio" name="kunci_jawaban"
                                            value="{{ $key }}" id="kunci_{{ $key }}"
                                            {{ $isKey ? 'checked' : '' }} required>
                                        <label class="form-check-label small fw-bold cursor-pointer"
                                            for="kunci_{{ $key }}">
                                            Benar
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Action --}}
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 8px;">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Script untuk Highlight Row saat Radio dipilih --}}
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
                   // --- A. Init Choices.js untuk Kategori ---
            const element = document.querySelector('#choices-kategori');
            const choices = new Choices(element, {
                searchEnabled: true,
                searchPlaceholderValue: 'Cari kategori...',
                itemSelectText: 'Tekan untuk memilih',
                shouldSort: false, // Penting! Agar urutan PK tidak berantakan (tetap sesuai PHP array)
                placeholder: true,
                placeholderValue: '-- Pilih Kategori --'
            });

            const radios = document.querySelectorAll('.key-radio');
            const rows = document.querySelectorAll('.option-item');

            function updateHighlight() {
                // Reset semua row
                rows.forEach(row => {
                    row.classList.remove('is-key');
                });

                // Highlight row yang dipilih
                const checkedRadio = document.querySelector('.key-radio:checked');
                if (checkedRadio) {
                    const parentRow = checkedRadio.closest('.option-item');
                    if (parentRow) {
                        parentRow.classList.add('is-key');
                    }
                }
            }

            radios.forEach(radio => {
                radio.addEventListener('change', updateHighlight);
            });

            // Jalankan sekali saat load
            updateHighlight();
        });
    </script>
@endsection
