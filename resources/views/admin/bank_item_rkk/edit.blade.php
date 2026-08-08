@extends('layouts.app')

@php
    $pageTitle = 'Edit Item RKK';
    $pageSubtitle = 'Perbarui Rincian Kewenangan Klinis.';
@endphp

@section('title', 'Edit Item RKK – Admin DIKSERA')

@push('styles')
    <style>
        .content-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02); padding: 32px; }
        .form-control-custom, .form-select-custom { border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px 12px; font-size: 14px; transition: all 0.2s; }
        .form-control-custom:focus { border-color: #2563eb; box-shadow: 0 0 0 3px #eff6ff; }
        .form-label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px; }
    </style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('admin.bank-item-rkk.index') }}" class="btn btn-sm btn-outline-secondary px-3" style="border-radius: 8px;">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>

            <form action="{{ route('admin.bank-item-rkk.update', $item->id) }}" method="POST">
                @csrf
                <div class="content-card">
                    <h5 class="mb-4 fw-bold text-dark border-bottom pb-3">Edit Item RKK</h5>

                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <label class="form-label">KFK</label>
                            <input type="text" name="kfk" class="form-control form-control-custom" value="{{ old('kfk', $item->kfk) }}" placeholder="Contoh: Keperawatan">
                        </div>
                        
                        <div class="col-md-3 mb-4">
                            <label class="form-label">Jenjang Klinis</label>
                            <input type="text" name="jenjang_klinis" class="form-control form-control-custom" value="{{ old('jenjang_klinis', $item->jenjang_klinis) }}" placeholder="Contoh: PK I">
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <label class="form-label">Kategori Tugas</label>
                            <input type="text" name="kategori_tugas" class="form-control form-control-custom" value="{{ old('kategori_tugas', $item->kategori_tugas) }}" placeholder="Contoh: Tugas Pokok">
                        </div>
                        
                        <div class="col-md-2 mb-4">
                            <label class="form-label">No Item</label>
                            <input type="text" name="no_item" class="form-control form-control-custom" value="{{ old('no_item', $item->no_item) }}" placeholder="Contoh: 1">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Deskripsi Kompetensi <span class="text-danger">*</span></label>
                        <textarea name="deskripsi_kompetensi" class="form-control form-control-custom" rows="4" required>{{ old('deskripsi_kompetensi', $item->deskripsi_kompetensi) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 8px; background-color: #2563eb; border: none;">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection