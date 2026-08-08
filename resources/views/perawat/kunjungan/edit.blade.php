@extends('layouts.app')
@section('title', 'Edit Kunjungan – DIKSERA')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* Paste CSS Edit dengan warna aksen orange */
    :root { --primary-blue: #2563eb; --accent-orange: #f59e0b; --text-dark: #0f172a; --bg-light: #f8fafc; --input-border: #e2e8f0; }
    body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; color: var(--text-dark); }
    .page-header { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
    .page-title { font-size: 1.5rem; font-weight: 700; color: var(--text-dark); margin: 0; letter-spacing: -0.5px; }
    .form-card { background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); padding: 40px; }
    .form-label { font-size: 0.9rem; font-weight: 600; color: var(--text-dark); margin-bottom: 8px; display: block; }
    .form-control { border: 1px solid var(--input-border); border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; color: var(--text-dark); transition: all 0.2s ease; }
    .form-control:focus { border-color: var(--accent-orange); box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1); outline: none; }
    .btn-submit-edit { background-color: var(--accent-orange); color: white; width: 100%; padding: 14px; border-radius: 10px; font-weight: 600; border: none; display: flex; justify-content: center; align-items: center; gap: 8px; }
    .btn-back { background: white; border: 1px solid #e2e8f0; color: #64748b; padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="page-header">
                <h1 class="page-title">Edit Kunjungan Luar Negeri</h1>
                <a href="{{ route('perawat.kunjungan.index') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>

            <div class="form-card">
                <form action="{{ route('perawat.kunjungan.update', $kunjungan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Negara Tujuan <span class="text-danger">*</span></label>
                            <input type="text" name="negara" class="form-control" value="{{ old('negara', $kunjungan->negara) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lamanya (Waktu)</label>
                            <input type="text" name="lamanya" class="form-control" value="{{ old('lamanya', $kunjungan->lamanya) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tujuan Kunjungan <span class="text-danger">*</span></label>
                            <input type="text" name="tujuan_kunjungan" class="form-control" value="{{ old('tujuan_kunjungan', $kunjungan->tujuan_kunjungan) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Yang Membiayai</label>
                            <input type="text" name="yang_membiayai" class="form-control" value="{{ old('yang_membiayai', $kunjungan->yang_membiayai) }}">
                        </div>
                    </div>
                    <div class="mt-5">
                        <button type="submit" class="btn-submit-edit"><i class="bi bi-check-lg"></i> Update Kunjungan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection