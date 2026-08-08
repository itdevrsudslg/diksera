@extends('layouts.app')

@section('title', 'Tambah Arsip Dokumen')

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

    body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; color: var(--text-dark); }
    .page-header { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
    .page-title { font-size: 1.5rem; font-weight: 700; color: var(--text-dark); margin: 0; }
    
    .form-card { background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); padding: 40px; }
    .form-label { font-size: 0.9rem; font-weight: 600; color: var(--text-dark); margin-bottom: 8px; display: block; }
    .required-star { color: #ef4444; }
    .form-control { border: 1px solid var(--input-border); border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; width: 100%; }
    .form-control:focus { border-color: var(--primary-blue); box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); outline: none; }
    
    .btn-submit { background-color: var(--primary-blue); color: white; width: 100%; padding: 14px; border-radius: 10px; font-weight: 600; border: none; transition: 0.2s; display: flex; justify-content: center; align-items: center; gap: 8px; }
    .btn-submit:hover { background-color: var(--primary-hover); transform: translateY(-2px); color: white; }
    .btn-back { background: white; border: 1px solid #e2e8f0; color: var(--text-gray); padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; gap: 6px; }
    .btn-back:hover { background-color: #f1f5f9; color: var(--text-dark); }
    
    .alert-danger { background-color: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 10px; }
    
    /* Dynamic Inputs Styling */
    .input-group-dynamic { display: flex; gap: 10px; margin-bottom: 15px; }
    .btn-remove-row { background: #fee2e2; color: #ef4444; border: none; border-radius: 10px; width: 48px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; }
    .btn-remove-row:hover { background: #fca5a5; color: white; }
    .btn-add-row { background: #f1f5f9; color: var(--primary-blue); border: 1px dashed var(--primary-blue); border-radius: 10px; padding: 10px; width: 100%; font-weight: 600; transition: 0.2s; }
    .btn-add-row:hover { background: #e0f2fe; }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="page-header">
                <div><h1 class="page-title">Tambah Arsip Dokumen</h1></div>
                <a href="{{ route('perawat.arsip.index') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>

            <div class="form-card">
                @if($errors->any())
                    <div class="alert alert-danger py-3 px-4 mb-4">
                        <ul class="mb-0 ps-3">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
                    </div>
                @endif

                <form action="{{ route('perawat.arsip.store') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        
                        <div class="col-md-4">
                            <label class="form-label">Tahun <span class="required-star">*</span></label>
                            <input type="number" name="tahun" class="form-control" value="{{ old('tahun', date('Y')) }}" placeholder="Contoh: 2024" required>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Link Google Drive <span class="required-star">*</span></label>
                            <input type="url" name="link_drive" class="form-control" value="{{ old('link_drive') }}" placeholder="https://drive.google.com/..." required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Isian Dokumen <span class="required-star">*</span></label>
                            <p class="text-muted" style="font-size: 0.85rem; margin-top:-5px;">Tuliskan daftar dokumen yang ada di dalam link tersebut.</p>
                            
                            <div id="isian-wrapper">
                                @if(old('isian_dokumen'))
                                    @foreach(old('isian_dokumen') as $isian)
                                        <div class="input-group-dynamic">
                                            <input type="text" name="isian_dokumen[]" class="form-control" value="{{ $isian }}" placeholder="Contoh: Fotokopi Ijazah" required>
                                            <button type="button" class="btn-remove-row"><i class="bi bi-trash"></i></button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group-dynamic">
                                        <input type="text" name="isian_dokumen[]" class="form-control" placeholder="Contoh: Fotokopi KTP" required>
                                        <button type="button" class="btn-remove-row" style="display: none;"><i class="bi bi-trash"></i></button>
                                    </div>
                                @endif
                            </div>

                            <button type="button" id="btn-add-isian" class="btn-add-row mt-2">
                                <i class="bi bi-plus-circle"></i> Tambah Isian Dokumen Lainnya
                            </button>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-save2"></i> Simpan Arsip Dokumen
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

        // Fungsi Tambah Baris
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

        // Fungsi Hapus Baris
        wrapper.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-row')) {
                e.target.closest('.input-group-dynamic').remove();
                toggleRemoveButtons();
            }
        });

        // Sembunyikan tombol hapus jika hanya ada 1 input
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