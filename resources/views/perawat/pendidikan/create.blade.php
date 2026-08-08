@extends('layouts.app')

@section('title', 'Tambah Pendidikan – DIKSERA')

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

    .invalid-feedback {
        font-size: 0.85rem;
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
                    <h1 class="page-title">Tambah Data Pendidikan</h1>
                </div>
                <a href="{{ route('perawat.pendidikan.index') }}" class="btn-back">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            {{-- Form Card --}}
            <div class="form-card">

                @if($errors->any())
                    <div class="alert alert-danger py-3 px-4 mb-4">
                        <strong>Terdapat kesalahan:</strong>
                        <ul class="mb-0 ps-3 mt-1">
                            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('perawat.pendidikan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-4">
                        {{-- Jenjang --}}
                        <div class="col-md-4">
                            <label class="form-label">Jenjang <span class="required-star">*</span></label>
                            <select name="jenjang" class="form-select @error('jenjang') is-invalid @enderror" required>
                                <option value="">- Pilih Jenjang -</option>
                                <option value="SD" {{ old('jenjang') == 'SD' ? 'selected' : '' }}>SD</option>
                                <option value="SMP" {{ old('jenjang') == 'SMP' ? 'selected' : '' }}>SMP</option>
                                <option value="SMA" {{ old('jenjang') == 'SMA' ? 'selected' : '' }}>SMA</option>
                                <option value="SMK" {{ old('jenjang') == 'SMK' ? 'selected' : '' }}>SMK</option>
                                <option value="D3" {{ old('jenjang') == 'D3' ? 'selected' : '' }}>D3</option>
                                <option value="D4" {{ old('jenjang') == 'D4' ? 'selected' : '' }}>D4</option>
                                <option value="S1" {{ old('jenjang') == 'S1' ? 'selected' : '' }}>S1</option>
                                <option value="S2" {{ old('jenjang') == 'S2' ? 'selected' : '' }}>S2</option>
                                <option value="S3" {{ old('jenjang') == 'S3' ? 'selected' : '' }}>S3</option>
                                <option value="Profesi" {{ old('jenjang') == 'Profesi' ? 'selected' : '' }}>Profesi</option>
                            </select>
                            @error('jenjang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Nama Institusi --}}
                        <div class="col-md-8">
                            <label class="form-label">Nama Institusi <span class="required-star">*</span></label>
                            <input type="text" name="nama_institusi" class="form-control @error('nama_institusi') is-invalid @enderror" 
                                   value="{{ old('nama_institusi') }}" placeholder="Nama Universitas / Sekolah" maxlength="255" required>
                            @error('nama_institusi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Jurusan --}}
                        <div class="col-md-6">
                            <label class="form-label">Jurusan</label>
                            <input type="text" name="jurusan" class="form-control @error('jurusan') is-invalid @enderror" 
                                   value="{{ old('jurusan') }}" placeholder="Contoh: Ilmu Keperawatan" maxlength="255">
                            @error('jurusan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Tempat --}}
                        <div class="col-md-6">
                            <label class="form-label">Tempat / Kota</label>
                            <input type="text" name="tempat" class="form-control @error('tempat') is-invalid @enderror" 
                                   value="{{ old('tempat') }}" placeholder="Contoh: Jakarta" maxlength="255">
                            @error('tempat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Akreditasi --}}
                        <div class="col-md-4">
                            <label class="form-label">Akreditasi</label>
                            <select name="akreditasi" class="form-select @error('akreditasi') is-invalid @enderror">
                                <option value="">- Pilih -</option>
                                <option value="Unggul" {{ old('akreditasi') == 'Unggul' ? 'selected' : '' }}>Unggul</option>
                                <option value="A" {{ old('akreditasi') == 'A' ? 'selected' : '' }}>A</option>
                                <option value="B" {{ old('akreditasi') == 'B' ? 'selected' : '' }}>B</option>
                                <option value="C" {{ old('akreditasi') == 'C' ? 'selected' : '' }}>C</option>
                                <option value="Baik Sekali" {{ old('akreditasi') == 'Baik Sekali' ? 'selected' : '' }}>Baik Sekali</option>
                                <option value="Baik" {{ old('akreditasi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                            </select>
                            @error('akreditasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Tahun Masuk (Validasi 4 Digit) --}}
                        <div class="col-md-4">
                            <label class="form-label">Thn Masuk</label>
                            <input type="number" name="tahun_masuk" class="form-control @error('tahun_masuk') is-invalid @enderror" 
                                   value="{{ old('tahun_masuk') }}" placeholder="YYYY"
                                   min="1950" max="{{ date('Y') + 5 }}" 
                                   oninput="if(this.value.length > 4) this.value = this.value.slice(0, 4);">
                            @error('tahun_masuk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Tahun Lulus (Validasi 4 Digit) --}}
                        <div class="col-md-4">
                            <label class="form-label">Thn Lulus</label>
                            <input type="number" name="tahun_lulus" class="form-control @error('tahun_lulus') is-invalid @enderror" 
                                   value="{{ old('tahun_lulus') }}" placeholder="YYYY"
                                   min="1950" max="{{ date('Y') + 10 }}" 
                                   oninput="if(this.value.length > 4) this.value = this.value.slice(0, 4);">
                            @error('tahun_lulus')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Nomor Ijazah --}}
                        <div class="col-md-6">
                            <label class="form-label">Nomor Ijazah</label>
                            <input type="text" name="nomor_ijazah" class="form-control @error('nomor_ijazah') is-invalid @enderror" 
                                   value="{{ old('nomor_ijazah') }}" placeholder="No. Seri Ijazah" maxlength="100">
                            @error('nomor_ijazah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Nama Kepala Institusi --}}
                        <div class="col-md-6">
                            <label class="form-label">Nama Kepala / Rektor <span class="required-star">*</span></label>
                            <input type="text" name="nama_kepala_institusi" class="form-control @error('nama_kepala_institusi') is-invalid @enderror" 
                                   value="{{ old('nama_kepala_institusi') }}" placeholder="Penandatangan ijazah" maxlength="255" required>
                            @error('nama_kepala_institusi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Upload Dokumen --}}
                        <div class="col-12">
                            <label class="form-label">Upload Ijazah (PDF/Image)</label>
                            <input type="file" name="dokumen" class="form-control @error('dokumen') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text text-muted mt-2 small">
                                <i class="bi bi-info-circle me-1"></i> Format PDF atau JPG/PNG. Pastikan tulisan terbaca jelas. (Maks 2MB)
                            </div>
                            @error('dokumen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-save2"></i> Simpan Data Pendidikan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection