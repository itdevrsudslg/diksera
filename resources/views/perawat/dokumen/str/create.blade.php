@extends('layouts.app')

@section('title', 'Tambah STR – DIKSERA')

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

    .form-control, .form-select {
        border: 1px solid var(--input-border);
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 0.95rem;
        color: var(--text-dark);
        background-color: #fff;
        transition: all 0.2s ease;
        width: 100%;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        outline: none;
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
                <div><h1 class="page-title">Tambah STR Baru</h1></div>
                <a href="{{ route('perawat.str.index') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>

            <div class="form-card">
                @if($errors->any())
                    <div class="alert alert-danger py-3 px-4 mb-4">
                        <ul class="mb-0 ps-3">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
                    </div>
                @endif

                <form action="{{ route('perawat.str.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-4">

                        {{-- NEW: Nama & Lembaga --}}
                        {{--<div class="col-md-6">--}}
                        {{--    <label class="form-label">Nama STR <span class="required-star">*</span></label>--}}
                        {{--    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" placeholder="Contoh: STR Baru, STR Perpanjangan" required>--}}
                        {{--</div>--}}
                        {{-- Nomor STR --}}
                        <div class="col-12">
                            <label class="form-label">Nomor STR <span class="required-star">*</span></label>
                            <input type="text" name="nomor" class="form-control @error('nomor') is-invalid @enderror" value="{{ old('nomor') }}" placeholder="Masukkan nomor STR" required>
                        </div>

                        {{-- Tanggal --}}
                       <div class="col-md-6">
    <label class="form-label">Tanggal Terbit <span class="required-star">*</span></label>
    <input type="date" name="tgl_terbit" class="form-control @error('tgl_terbit') is-invalid @enderror" value="{{ old('tgl_terbit', $data->tgl_terbit ?? '') }}" required>
</div>

<div class="col-md-6">
    {{-- HAPUS required-star dan tambahkan keterangan --}}
    <label class="form-label">Tanggal Expired <small class="text-muted">(Kosongkan jika Seumur Hidup)</small></label>
    
    {{-- HAPUS attribute required di input --}}
    <input type="date" name="tgl_expired" class="form-control @error('tgl_expired') is-invalid @enderror" value="{{ old('tgl_expired', $data->tgl_expired ?? '') }}">
</div>

                        {{-- Upload --}}
                        <div class="col-12">
                            <label class="form-label">Upload Dokumen STR (PDF/Gambar, Max 5MB) <span class="required-star">*</span></label>
                            <input type="file" name="dokumen" class="form-control @error('dokumen') is-invalid @enderror" style="padding-top: 9px;" required>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-save2"></i> Simpan Data STR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
