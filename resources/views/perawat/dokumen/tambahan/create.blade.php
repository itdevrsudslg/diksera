@extends('layouts.app')

@section('title', 'Tambah Dokumen Tambahan – DIKSERA')

@push('styles')
    {{-- Style seragam untuk tipografi, input, dan card --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-hover: #1d4ed8;
            --text-dark: #0f172a;
            --text-gray: #64748b;
            --bg-light: #f8fafc;
            --input-border: #e2e8f0;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
        }

        /* --- Header --- */
        .page-header {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            letter-spacing: -0.5px;
        }

        /* --- Form Card --- */
        .form-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            padding: 40px;
        }

        /* --- Inputs & Select --- */
        .form-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
            display: block;
        }

        .required-star {
            color: #ef4444;
        }

        .form-control,
        .form-select {
            border: 1px solid var(--input-border);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.95rem;
            color: var(--text-dark);
            background-color: #fff;
            transition: all 0.2s ease;
            width: 100%;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        /* Helper text untuk datalist */
        .form-text {
            font-size: 0.85rem;
            color: var(--text-gray);
            margin-top: 5px;
        }

        /* --- Buttons --- */
        .btn-submit {
            background-color: var(--primary-blue);
            color: white;
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.25);
            transition: all 0.2s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 12px -1px rgba(37, 99, 235, 0.3);
            color: white;
        }

        .btn-back {
            background: white;
            border: 1px solid #e2e8f0;
            color: var(--text-gray);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-back:hover {
            background-color: #f1f5f9;
            color: var(--text-dark);
            border-color: #cbd5e1;
        }

        /* Alert Style */
        .alert-danger {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            border-radius: 10px;
            font-size: 0.9rem;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Tambah Dokumen Tambahan</h1>
                    </div>
                    <a href="{{ route('perawat.tambahan.index') }}" class="btn-back"><i class="bi bi-arrow-left"></i>
                        Kembali</a>
                </div>

                <div class="form-card">
                    @if ($errors->any())
                        <div class="alert alert-danger py-3 px-4 mb-4">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('perawat.tambahan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">

                            {{-- Nama Dokumen --}}
                            <div class="col-md-6">
                                <label class="form-label">Nama Dokumen <span class="required-star">*</span></label>
                                <input type="text" name="nama"
                                    class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}"
                                    placeholder="Contoh: ACLS, Vaksin Covid, RKK" required>
                            </div>

                            {{-- Lembaga & Nomor --}}
                            <div class="col-md-6">
                                <label class="form-label">Lembaga Penerbit <span class="required-star">*</span></label>
                                <input type="text" name="lembaga"
                                    class="form-control @error('lembaga') is-invalid @enderror" value="{{ old('lembaga') }}"
                                    placeholder="Contoh: PERKI, RSUD, PPNI" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nomor Dokumen <span class="required-star">*</span></label>
                                <input type="text" name="nomor"
                                    class="form-control @error('nomor') is-invalid @enderror" value="{{ old('nomor') }}"
                                    placeholder="Masukkan nomor dokumen" required>
                            </div>

                            {{-- Tanggal --}}
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Terbit <span class="required-star">*</span></label>
                                <input type="date" name="tgl_terbit"
                                    class="form-control @error('tgl_terbit') is-invalid @enderror"
                                    value="{{ old('tgl_terbit') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Expired <span class="required-star"
                                        id="expired-star">*</span></label>
                                <input type="date" id="tgl_expired" name="tgl_expired"
                                    class="form-control @error('tgl_expired') is-invalid @enderror"
                                    value="{{ old('tgl_expired') }}" required>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" value="1" id="is_lifetime"
                                        name="is_lifetime" {{ old('is_lifetime') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_lifetime">Seumur Hidup (Lifetime) — membutuhkan
                                        ACC Admin</label>
                                </div>
                            </div>

                            {{-- Upload --}}
                         {{-- SKP & Upload --}}
                            <div class="col-md-6">
                                <label class="form-label">Total SKP</label>
                                <input type="number" name="skp" class="form-control @error('skp') is-invalid @enderror" 
                                    value="{{ old('skp', 0) }}" min="0" placeholder="Contoh: 5">
                                <div class="form-text text-muted mt-2 small">
                                    <i class="bi bi-info-circle me-1"></i> Kosongkan (0) jika tidak memiliki SKP.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Upload Dokumen (PDF/Gambar, Max 5MB) <span
                                        class="required-star">*</span></label>
                                <input type="file" name="dokumen"
                                    class="form-control @error('dokumen') is-invalid @enderror" style="padding-top: 9px;"
                                    required>
                            </div>
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn-submit">
                                <i class="bi bi-save2"></i> Simpan Dokumen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function() {
            const chk = document.getElementById('is_lifetime');
            const expired = document.getElementById('tgl_expired');
            const star = document.getElementById('expired-star');

            function toggle() {
                if (!chk) return;
                if (chk.checked) {
                    expired.value = '';
                    expired.disabled = true;
                    if (star) star.style.visibility = 'hidden';
                } else {
                    expired.disabled = false;
                    if (star) star.style.visibility = 'visible';
                }
            }

            if (chk) {
                chk.addEventListener('change', toggle);
                // initial
                toggle();
            }
        })();
    </script>
@endsection
