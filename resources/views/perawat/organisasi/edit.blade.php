@extends('layouts.app')

@section('title', 'Edit Organisasi – DIKSERA')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --primary-blue: #2563eb;
        --text-dark: #0f172a;
        --text-gray: #64748b;
        --bg-light: #f8fafc;
        --input-border: #e2e8f0;
        --accent-orange: #f59e0b; /* Warna tema Edit */
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

    /* --- Inputs --- */
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

    .form-control {
        border: 1px solid var(--input-border);
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 0.95rem;
        color: var(--text-dark);
        background-color: #fff;
        transition: all 0.2s ease;
    }

    /* Focus Orange untuk Edit */
    .form-control:focus {
        border-color: var(--accent-orange);
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
        outline: none;
    }

    /* --- Buttons --- */
    .btn-submit-edit {
        background-color: var(--accent-orange);
        color: white;
        width: 100%;
        padding: 14px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.25);
        transition: all 0.2s;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }

    .btn-submit-edit:hover {
        background-color: #d97706;
        transform: translateY(-2px);
        box-shadow: 0 8px 12px -1px rgba(245, 158, 11, 0.3);
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

    /* --- File Preview Style --- */
    .file-preview-box {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 10px 15px;
        margin-top: 8px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
    }
    .file-link {
        color: var(--primary-blue);
        text-decoration: none;
        font-weight: 600;
    }
    .file-link:hover { text-decoration: underline; }

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
                    <h1 class="page-title">Edit Pengalaman Organisasi</h1>
                </div>
                <a href="{{ route('perawat.organisasi.index') }}" class="btn-back">
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

                <form action="{{ route('perawat.organisasi.update', $organisasi->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        {{-- Nama & Jabatan --}}
                        <div class="col-md-6">
                            <label class="form-label">Nama Organisasi <span class="required-star">*</span></label>
                            <input type="text" name="nama_organisasi" class="form-control" value="{{ old('nama_organisasi', $organisasi->nama_organisasi) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan <span class="required-star">*</span></label>
                            <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $organisasi->jabatan) }}" required>
                        </div>

                        {{-- Tempat & Pimpinan --}}
                        <div class="col-md-6">
                            <label class="form-label">Tempat / Kota</label>
                            <input type="text" name="tempat" class="form-control" value="{{ old('tempat', $organisasi->tempat) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Pimpinan</label>
                            <input type="text" name="pemimpin" class="form-control" value="{{ old('pemimpin', $organisasi->pemimpin) }}">
                        </div>

                        {{-- Tanggal --}}
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tahun_mulai" class="form-control" value="{{ old('tahun_mulai', $organisasi->tahun_mulai) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tahun_selesai" class="form-control" value="{{ old('tahun_selesai', $organisasi->tahun_selesai) }}">
                            <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">* Kosongi jika masih aktif.</small>
                        </div>
<div class="col-md-12">
    <label class="form-label">Kategori Masa Organisasi <span class="required-star">*</span></label>
    <select name="masa_organisasi" class="form-select" required>
        <option value="">- Pilih Kategori Waktu Organisasi -</option>
        <option value="SLTA" {{ old('masa_organisasi') == 'SLTA' ? 'selected' : '' }}>Semasa Pendidikan SLTA ke bawah</option>
        <option value="PT" {{ old('masa_organisasi') == 'PT' ? 'selected' : '' }}>Semasa Pendidikan Perguruan Tinggi</option>
        <option value="Bekerja" {{ old('masa_organisasi') == 'Bekerja' ? 'selected' : '' }}>Sesudah selesai pendidikan / selama menjadi pegawai</option>
    </select>
</div>
                        {{-- Upload Dokumen --}}
                        <div class="col-12">
                            <label class="form-label">Update SK / Dokumen (Opsional)</label>
                            <input type="file" name="dokumen" class="form-control">

                            @if($organisasi->dokumen_path)
                                <div class="file-preview-box">
                                    <i class="bi bi-file-earmark-pdf text-danger"></i>
                                    <span class="text-muted">File saat ini:</span>
                                    <a href="{{ asset('storage/'.$organisasi->dokumen_path) }}" target="_blank" class="file-link">
                                        Lihat Dokumen
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn-submit-edit">
                            <i class="bi bi-check-lg"></i> Update Data Organisasi
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
