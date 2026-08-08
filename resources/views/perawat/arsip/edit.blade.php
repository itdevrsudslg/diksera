@extends('layouts.app')

@section('title', 'Edit Arsip Dokumen')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --text-dark: #0f172a;
        --text-gray: #64748b;
        --bg-light: #f8fafc;
        --input-border: #e2e8f0;
        --accent-orange: #f59e0b; /* Tema warna edit */
    }

    body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; color: var(--text-dark); }
    .page-header { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
    .page-title { font-size: 1.5rem; font-weight: 700; color: var(--text-dark); margin: 0; }
    
    .form-card { background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); padding: 40px; }
    .form-label { font-size: 0.9rem; font-weight: 600; color: var(--text-dark); margin-bottom: 8px; display: block; }
    .required-star { color: #ef4444; }
    .form-control { border: 1px solid var(--input-border); border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; width: 100%; }
    .form-control:focus { border-color: var(--accent-orange); box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1); outline: none; }
    
    .btn-submit-edit { background-color: var(--accent-orange); color: white; width: 100%; padding: 14px; border-radius: 10px; font-weight: 600; border: none; transition: 0.2s; display: flex; justify-content: center; align-items: center; gap: 8px; }
    .btn-submit-edit:hover { background-color: #d97706; transform: translateY(-2px); color: white; }
    .btn-back { background: white; border: 1px solid #e2e8f0; color: var(--text-gray); padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; gap: 6px; }
    .btn-back:hover { background-color: #f1f5f9; color: var(--text-dark); }
    
    .alert-danger { background-color: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 10px; }

    /* Dynamic Inputs Styling */
    .input-group-dynamic { display: flex; gap: 10px; margin-bottom: 15px; }
    .btn-remove-row { background: #fee2e2; color: #ef4444; border: none; border-radius: 10px; width: 48px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; }
    .btn-remove-row:hover { background: #fca5a5; color: white; }
    .btn-add-row { background: #fffbeb; color: var(--accent-orange); border: 1px dashed var(--accent-orange); border-radius: 10px; padding: 10px; width: 100%; font-weight: 600; transition: 0.2s; }
    .btn-add-row:hover { background: #fef3c7; }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="page-header">
                <div><h1 class="page-title">Edit Arsip Dokumen</h1></div>
                <a href="{{ route('perawat.arsip.index') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>

            <div class="form-card">
                @if($errors->any())
                    <div class="alert alert-danger py-3 px-4 mb-4">
                        <ul class="mb-0 ps-3">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
                    </div>
                @endif

                <form action="{{ route('perawat.arsip.update', $data->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label">Tahun <span class="required-star">*</span></label>
                            <input type="number" name="tahun" class="form-control" value="{{ old('tahun', $data->tahun) }}" required>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Link Google Drive <span class="required-star">*</span></label>
                            <input type="url" name="link_drive" class="form-control" value="{{ old('link_drive', $data->link_drive) }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Isian Dokumen <span class="required-star">*</span></label>
                            
                            <div id="isian-wrapper">
                                @php
                                    $isianList = old('isian_dokumen', $data->isian_dokumen ?? []);
                                @endphp
                                
                                @if(is_array($isianList) && count($isianList) > 0)
                                    @foreach($isianList as $isian)
                                        <div class="input-group-dynamic">
                                            <input type="text" name="isian_dokumen[]" class="form-control" value="{{ $isian }}" required>
                                            <button type="button" class="btn-remove-row"><i class="bi bi-trash"></i></button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group-dynamic">
                                        <input type="text" name="isian_dokumen[]" class="form-control" placeholder="Contoh: Fotokopi KTP" required>
                                        <button type="button" class="btn-remove-row"><i class="bi bi-trash"></i></button>
                                    </div>
                                @endif
                            </div>

                            <button type="button" id="btn-add-isian" class="btn-add-row mt-2">
                                <i class="bi bi-plus-circle"></i> Tambah Isian Dokumen Lainnya
                            </button>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn-submit-edit">
                            <i class="bi bi-check-lg"></i> Update Arsip Dokumen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const wrapper = document.getElementById('isian-wrapper');
        const btnAdd = document.getElementById('btn-add-isian');

        btnAdd.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'input-group-dynamic';
            row.innerHTML = `
                <input type="text" name="isian_dokumen[]" class="form-control" placeholder="Nama Dokumen (Isian)" required>
                <button type="button" class="btn-remove-row"><i class="bi bi-trash"></i></button>
            `;
            wrapper.appendChild(row);
            toggleRemoveButtons();
        });

        wrapper.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-row')) {
                e.target.closest('.input-group-dynamic').remove();
                toggleRemoveButtons();
            }
        });

        function toggleRemoveButtons() {
            const rows = wrapper.querySelectorAll('.input-group-dynamic');
            const deleteBtns = wrapper.querySelectorAll('.btn-remove-row');
            if (rows.length === 1) {
                deleteBtns[0].style.display = 'none';
            } else {
                deleteBtns.forEach(btn => btn.style.display = 'flex');
            }
        }
        toggleRemoveButtons();
    });
</script>
@endsection