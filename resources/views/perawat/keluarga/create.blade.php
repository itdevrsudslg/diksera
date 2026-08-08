@extends('layouts.app')

@section('title', 'Tambah Keluarga – DIKSERA')

@push('styles')
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

    .form-control, .form-select {
        border: 1px solid var(--input-border);
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 0.95rem;
        color: var(--text-dark);
        background-color: #fff;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        outline: none;
    }

    .form-control::placeholder {
        color: #cbd5e1;
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

            {{-- Header --}}
            <div class="page-header">
                <div>
                    <h1 class="page-title">Tambah Anggota Keluarga</h1>
                </div>
                <a href="{{ route('perawat.keluarga.index') }}" class="btn-back">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            {{-- Form Card --}}
            <div class="form-card">

                @if($errors->any())
                    <div class="alert alert-danger py-3 px-4 mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('perawat.keluarga.store') }}" method="POST">
                    @csrf

                    <div class="row g-4">
                        {{-- Hubungan & Nama Lengkap --}}
                        <div class="col-md-6">
                            <label class="form-label">Hubungan <span class="required-star">*</span></label>
                            <select name="hubungan" class="form-select" required>
                                <option value="">- Pilih Hubungan -</option>
                                <option value="Suami" {{ old('hubungan') == 'Suami' ? 'selected' : '' }}>Suami</option>
                                <option value="Istri" {{ old('hubungan') == 'Istri' ? 'selected' : '' }}>Istri</option>
                                <option value="Anak" {{ old('hubungan') == 'Anak' ? 'selected' : '' }}>Anak</option>
                                <option value="Ayah" {{ old('hubungan') == 'Ayah' ? 'selected' : '' }}>Ayah</option>
                                <option value="Ibu" {{ old('hubungan') == 'Ibu' ? 'selected' : '' }}>Ibu</option>
                                <option value="Saudara" {{ old('hubungan') == 'Saudara' ? 'selected' : '' }}>Saudara</option>
                                <option value="Mertua" {{ old('hubungan') == 'Mertua' ? 'selected' : '' }}>Mertua</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap <span class="required-star">*</span></label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" placeholder="Masukkan nama lengkap" required>
                        </div>

                        {{-- Tanggal Lahir & Pekerjaan --}}
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control" value="{{ old('pekerjaan') }}" placeholder="Contoh: Wiraswasta / PNS">
                        </div>
                
                    
                    {{-- NEW: No Darurat --}}
                    <div class="col-md-6">
                            <label class="form-label">No. Telepon / Darurat</label>
                            <input type="text" name="no_darurat" class="form-control" value="{{ old('no_darurat') }}" placeholder="Contoh: 081234567890">
                        </div>
<div class="col-md-6">
    <label class="form-label">Tempat Lahir</label>
    <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}" placeholder="Kota kelahiran">
</div>
<div class="col-md-6">
    <label class="form-label">Tanggal Lahir</label>
    <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}">
</div>
<div class="col-md-6">
    <label class="form-label">Jenis Kelamin</label>
    <select name="jenis_kelamin" class="form-select">
        <option value="">- Pilih -</option>
        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
    </select>
</div>
<div class="col-md-6">
    <label class="form-label">Pekerjaan</label>
    <input type="text" name="pekerjaan" class="form-control" value="{{ old('pekerjaan') }}" placeholder="Contoh: Wiraswasta / PNS">
</div>
<div class="col-md-6">
    <label class="form-label">Tanggal Menikah <small class="text-muted">(Khusus Suami/Istri)</small></label>
    <input type="date" name="tanggal_nikah" class="form-control" value="{{ old('tanggal_nikah') }}">
</div>
<div class="col-12">
    <label class="form-label">Keterangan Tambahan</label>
    <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan') }}" placeholder="Keterangan lain (opsional)">
</div>
    </div>
                    <div class="mt-5">
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-save2"></i> Simpan Data Keluarga
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
