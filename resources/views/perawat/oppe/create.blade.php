@extends('layouts.app')

@section('title', 'Buat Pengajuan OPPE')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />
    <style>
        .form-card {
            background: white; border-radius: 16px; border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.05); padding: 32px;
        }
        .form-label { font-weight: 600; font-size: 0.9rem; margin-bottom: 8px; color: #334155; }
        .form-control, .form-select { border-radius: 10px; padding: 10px 14px; border: 1px solid #cbd5e1; }
        .form-control:focus, .form-select:focus { border-color: #0ea5e9; box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1); }
        .btn-submit { background: #0ea5e9; color: white; font-weight: 600; padding: 12px 24px; border-radius: 10px; border: none; width: 100%; transition: 0.2s; }
        .btn-submit:hover { background: #0284c7; transform: translateY(-2px); }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-1">Pengajuan OPPE</h2>
                        <p class="text-muted mb-0">Unit Kerja Anda: <strong>{{ $myUnit }}</strong></p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <div class="form-card">
                    <form action="{{ route('perawat.oppe.store') }}" method="POST" id="formPengajuan">
                        @csrf
{{-- INPUT PERIODE BERUPA TANGGAL (BISA BACKDATE) --}}
<div class="mb-4">
    <label class="form-label">Tanggal / Periode OPPE <span class="text-danger">*</span></label>
    <input type="date" name="periode" class="form-control @error('periode') is-invalid @enderror" 
           value="{{ old('periode', date('Y-m-d')) }}" required>
    @error('periode')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="text-muted">Pilih tanggal atau ubah ke tanggal lampau (backdate) jika diperlukan.</small>
</div>

<hr class="border-light my-4">
                     {{-- PILIH KFK & JENJANG --}}
                        <div class="mb-4">
                            <label class="form-label">Jenjang Kompetensi yang Diajukan (Pilih KFK dan Level PK) <span class="text-danger">*</span></label>
                            <select name="kfk[]" id="choice-kfk" class="form-select @error('kfk') is-invalid @enderror" multiple required>
                                <option value="" disabled>Pilih KFK...</option>
                                
                                <optgroup label="KFK DASAR / UMUM">
                                    <option value="Keperawatan">Keperawatan</option>
                                </optgroup>

                                <optgroup label="KFK KRITIS">
                                    <option value="KFK Kritis Gawat Darurat">KFK Kritis Gawat Darurat</option>
                                    <option value="KFK Kritis ICU">KFK Kritis ICU</option>
                                    <option value="KFK Kritis ICVCU">KFK Kritis ICVCU</option>
                                    <option value="KFK Kritis Anestesi">KFK Kritis Anestesi</option>
                                </optgroup>

                                <optgroup label="KFK MEDIKAL BEDAH">
                                    <option value="KFK Medikal Bedah Penyakit Dalam">KFK Medikal Bedah Penyakit Dalam</option>
                                    <option value="KFK Medikal Bedah Bedah">KFK Medikal Bedah Bedah</option>
                                    <option value="KFK Medikal Bedah Perioperatif">KFK Medikal Bedah Perioperatif</option>
                                    <option value="KFK Medikal Bedah Isolasi (Keperawatan Khusus)">KFK Medikal Bedah Isolasi (Keperawatan Khusus)</option>
                                </optgroup>

                                <optgroup label="KFK ANAK">
                                    <option value="KFK Anak NICU">KFK Anak NICU</option>
                                    <option value="KFK Anak PICU">KFK Anak PICU</option>
                                    <option value="KFK Anak Pediatri">KFK Anak Pediatri</option>
                                    <option value="KFK Anak Neonatus">KFK Anak Neonatus</option>
                                </optgroup>

                                <optgroup label="KFK MATERNITAS">
                                    <option value="KFK Maternitas">KFK Maternitas</option>
                                </optgroup>

                                <optgroup label="JENIS PERAWAT KLINIS (PK)">
                                    <option value="PRA PK">PRA PK</option>
                                   <option value="PK I">PK 1</option>
                                    
                                    <option value="PK 2 KEP GAWAT DARURAT">PK 2 KEP GAWAT DARURAT</option>
                                    <option value="PK 3 KEP GAWAT DARURAT">PK 3 KEP GAWAT DARURAT</option>
                                    <option value="PK 4 KEP GAWAT DARURAT">PK 4 KEP GAWAT DARURAT</option>
                                    <option value="PK 5 KEP GAWAT DARURAT">PK 5 KEP GAWAT DARURAT</option>
                                    
                                    <option value="PK 2 KEP ICU">PK 2 KEP ICU</option>
                                    <option value="PK 3 KEP ICU">PK 3 KEP ICU</option>
                                    <option value="PK 4 KEP ICU">PK 4 KEP ICU</option>
                                    <option value="PK 5 KEP ICU">PK 5 KEP ICU</option>
                                    
                                    <option value="PK 2 KEP ICVCU">PK 2 KEP ICVCU</option>
                                    <option value="PK 3 KEP ICVCU">PK 3 KEP ICVCU</option>
                                    <option value="PK 4 KEP ICVCU">PK 4 KEP ICVCU</option>
                                    <option value="PK 5 KEP ICVCU">PK 5 KEP ICVCU</option>
                                    
                                    <option value="PK 2 KEP ANESTESI">PK 2 KEP ANESTESI</option>
                                    <option value="PK 3 KEP ANESTESI">PK 3 KEP ANESTESI</option>
                                    <option value="PK 4 KEP ANESTESI">PK 4 KEP ANESTESI</option>
                                    <option value="PK 5 KEP ANESTESI">PK 5 KEP ANESTESI</option>
                                    
                                    <option value="PK 2 KEP PENYAKIT DALAM">PK 2 KEP PENYAKIT DALAM</option>
                                    <option value="PK 3 KEP PENYAKIT DALAM">PK 3 KEP PENYAKIT DALAM</option>
                                    <option value="PK 4 KEP PENYAKIT DALAM">PK 4 KEP PENYAKIT DALAM</option>
                                    <option value="PK 5 KEP PENYAKIT DALAM">PK 5 KEP PENYAKIT DALAM</option>
                                    
                                    <option value="PK 2 KEP BEDAH">PK 2 KEP BEDAH</option>
                                    <option value="PK 3 KEP BEDAH">PK 3 KEP BEDAH</option>
                                    <option value="PK 4 KEP BEDAH">PK 4 KEP BEDAH</option>
                                    <option value="PK 5 KEP BEDAH">PK 5 KEP BEDAH</option>
                                    
                                    <option value="PK 2 KEP PERIOPERATIF">PK 2 KEP PERIOPERATIF</option>
                                    <option value="PK 3 KEP PERIOPERATIF">PK 3 KEP PERIOPERATIF</option>
                                    <option value="PK 4 KEP PERIOPERATIF">PK 4 KEP PERIOPERATIF</option>
                                    <option value="PK 5 KEP PERIOPERATIF">PK 5 KEP PERIOPERATIF</option>
                                    
                                    <option value="PK 2 KEP ISOLASI (KEPERAWATAN KHUSUS)">PK 2 KEP ISOLASI (KEPERAWATAN KHUSUS)</option>
                                    <option value="PK 3 KEP ISOLASI (KEPERAWATAN KHUSUS)">PK 3 KEP ISOLASI (KEPERAWATAN KHUSUS)</option>
                                    <option value="PK 4 KEP ISOLASI (KEPERAWATAN KHUSUS)">PK 4 KEP ISOLASI (KEPERAWATAN KHUSUS)</option>
                                    <option value="PK 5 KEP ISOLASI (KEPERAWATAN KHUSUS)">PK 5 KEP ISOLASI (KEPERAWATAN KHUSUS)</option>
                                    
                                    <option value="PK 2 KEP NICU">PK 2 KEP NICU</option>
                                    <option value="PK 3 KEP NICU">PK 3 KEP NICU</option>
                                    <option value="PK 4 KEP NICU">PK 4 KEP NICU</option>
                                    <option value="PK 5 KEP NICU">PK 5 KEP NICU</option>
                                    
                                    <option value="PK 2 KEP PICU">PK 2 KEP PICU</option>
                                    <option value="PK 3 KEP PICU">PK 3 KEP PICU</option>
                                    <option value="PK 4 KEP PICU">PK 4 KEP PICU</option>
                                    <option value="PK 5 KEP PICU">PK 5 KEP PICU</option>
                                    
                                    <option value="PK 2 KEP PEDIATRI">PK 2 KEP PEDIATRI</option>
                                    <option value="PK 3 KEP PEDIATRI">PK 3 KEP PEDIATRI</option>
                                    <option value="PK 4 KEP PEDIATRI">PK 4 KEP PEDIATRI</option>
                                    <option value="PK 5 KEP PEDIATRI">PK 5 KEP PEDIATRI</option>
                                    
                                    <option value="PK 2 KEP NEONATUS">PK 2 KEP NEONATUS</option>
                                    <option value="PK 3 KEP NEONATUS">PK 3 KEP NEONATUS</option>
                                    <option value="PK 4 KEP NEONATUS">PK 4 KEP NEONATUS</option>
                                    <option value="PK 5 KEP NEONATUS">PK 5 KEP NEONATUS</option>
                                </optgroup>

                                <optgroup label="JENIS BIDAN KLINIS (BK)">
                                    <option value="PRA BK">PRA BK</option>
                                    <option value="BK 1">BK 1</option>
                                    <option value="BK 2 MATERNITAS">BK 2 MATERNITAS</option>
                                    <option value="BK 3 MATERNITAS">BK 3 MATERNITAS</option>
                                    <option value="BK 4 MATERNITAS">BK 4 MATERNITAS</option>
                                    <option value="BK 5 MATERNITAS">BK 5 MATERNITAS</option>
                                </optgroup>
                            </select>
                            @error('kfk')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="border-light my-4">

                        {{-- 2. PILIH TIM PENILAI --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Pilih Teman Sejawat (Peer) <span class="text-danger">*</span></label>
                                <select name="peer_id" class="form-select" required>
                                    <option value="" selected disabled>-- Pilih Teman Sejawat --</option>
                                    @foreach($peers as $peer)
                                        <option value="{{ $peer->user_id }}">{{ $peer->nama_lengkap ?? $peer->user->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Rekan kerja di unit <strong>{{ $myUnit }}</strong></small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Pilih {{ $evaluatorLabel }} <span class="text-danger">*</span></label>
                                <select name="evaluator_id" class="form-select" required>
                                    <option value="" selected disabled>-- Pilih Evaluator --</option>
                                    @foreach($evaluators as $ev)
                                        <option value="{{ $ev->user_id }}">{{ $ev->nama_lengkap ?? $ev->user->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Atasan yang akan mengevaluasi Anda</small>
                            </div>
                        </div>

                        @if($sedangMengajukan)
                            <button type="button" class="btn btn-secondary w-100 p-3" disabled>
                                <i class="bi bi-hourglass-split me-2"></i>Anda Sedang Dalam Proses OPPE
                            </button>
                            <div class="alert alert-info mt-3 small">
                                <i class="bi bi-info-circle me-2"></i> OPPE hanya bisa diajukan 1 kali dalam setahun atau tunggu proses sebelumnya selesai.
                            </div>
                        @else
                            <button type="submit" class="btn btn-submit" id="btnSubmit">
                                <i class="bi bi-arrow-right-circle-fill me-2"></i>Buat Instrumen OPPE
                            </button>
                        @endif

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Init Choices.js
            const elementKfk = document.getElementById('choice-kfk');
            let choicesInstance;
            
            if (elementKfk) {
                choicesInstance = new Choices(elementKfk, {
                    removeItemButton: true,
                    placeholderValue: 'Pilih KFK dan Jenjang (PK/BK)...',
                    maxItemCount: 2,
                    searchEnabled: true
                });
            }

         const pkAutoOptions = ['PRA PK', 'PK I']; // <--- Sesuaikan dengan value baru
const bkAutoOptions = ['PRA BK', 'BK I']; // <--- (Jika BK 1 juga pakai Romawi di database)

            // LOGIKA AUTO-SELECT & PENGUNCIAN KFK
            elementKfk.addEventListener('addItem', function(event) {
                const addedValue = event.detail.value;
                const selectedValues = choicesInstance.getValue(true);

                if (pkAutoOptions.includes(addedValue)) {
                    selectedValues.forEach(val => {
                        if (val !== addedValue && (val.includes('KFK') || val === 'Keperawatan')) {
                            choicesInstance.removeActiveItemsByValue(val);
                        }
                    });
                    choicesInstance.setChoiceByValue('Keperawatan');
                    Swal.fire({icon: 'success', title: 'Otomatis Dipilih', text: 'KFK otomatis diatur ke "Keperawatan"', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
                } 
                else if (bkAutoOptions.includes(addedValue)) {
                    selectedValues.forEach(val => {
                        if (val !== addedValue && (val.includes('KFK') || val === 'Keperawatan')) {
                            choicesInstance.removeActiveItemsByValue(val);
                        }
                    });
                    choicesInstance.setChoiceByValue('KFK Maternitas');
                    Swal.fire({icon: 'success', title: 'Otomatis Dipilih', text: 'KFK otomatis diatur ke "KFK Maternitas"', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
                }
                else if (addedValue.includes('KFK') || addedValue === 'Keperawatan') {
                    const hasPkLocked = selectedValues.some(v => pkAutoOptions.includes(v));
                    const hasBkLocked = selectedValues.some(v => bkAutoOptions.includes(v));

                    if (hasPkLocked && addedValue !== 'Keperawatan') {
                        choicesInstance.removeActiveItemsByValue(addedValue);
                        choicesInstance.setChoiceByValue('Keperawatan');
                        Swal.fire({icon: 'error', title: 'Terkunci', text: 'Untuk PRA PK / PK 1, KFK otomatis Keperawatan dan tidak bisa diganti.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
                    } else if (hasBkLocked && addedValue !== 'KFK Maternitas') {
                        choicesInstance.removeActiveItemsByValue(addedValue);
                        choicesInstance.setChoiceByValue('KFK Maternitas');
                        Swal.fire({icon: 'error', title: 'Terkunci', text: 'Untuk PRA BK / BK 1, KFK otomatis KFK Maternitas dan tidak bisa diganti.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
                    }
                }

                const selectedCount = choicesInstance.getValue(true).length;
                if (selectedCount >= 2 && !pkAutoOptions.includes(addedValue) && !bkAutoOptions.includes(addedValue)) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Batas Maksimal Terpenuhi',
                        text: 'Pastikan anda memilih 1 KFK dan 1 Jenjang (PK/BK).',
                        confirmButtonColor: '#0ea5e9',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3500
                    });
                }
            });

            elementKfk.addEventListener('removeItem', function(event) {
                const removedValue = event.detail.value;
                const selectedValues = choicesInstance.getValue(true);
                
                const hasPkLocked = selectedValues.some(v => pkAutoOptions.includes(v));
                const hasBkLocked = selectedValues.some(v => bkAutoOptions.includes(v));

                if (hasPkLocked && removedValue === 'Keperawatan') {
                    choicesInstance.setChoiceByValue('Keperawatan');
                    Swal.fire({icon: 'error', title: 'Tidak Bisa Dihapus', text: 'KFK Keperawatan wajib untuk jenjang ini!', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
                } else if (hasBkLocked && removedValue === 'KFK Maternitas') {
                    choicesInstance.setChoiceByValue('KFK Maternitas');
                    Swal.fire({icon: 'error', title: 'Tidak Bisa Dihapus', text: 'KFK Maternitas wajib untuk jenjang ini!', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
                }
            });

            // Form validation Submit
            const form = document.getElementById('formPengajuan');
            const btnSubmit = document.getElementById('btnSubmit');

            form.addEventListener('submit', function(e) {
                let isValid = true;
                let errorMessages = [];
                const selectedKfk = choicesInstance.getValue(true); 
    
                if (selectedKfk.length !== 2) {
                    isValid = false;
                    errorMessages.push('Anda harus memilih tepat 2 item (1 KFK dan 1 Jenjang PK/BK)');
                } else {
                    const hasKfk = selectedKfk.some(val => val.includes('KFK') || val === 'Keperawatan');
                    const hasJenjang = selectedKfk.some(val => val.includes('PK') || val.includes('BK'));
                    
                    if (!hasKfk || !hasJenjang) {
                        isValid = false;
                        errorMessages.push('Kombinasi salah! Harus terdiri dari 1 KFK/Keperawatan dan 1 Jenjang (PK/BK)');
                    }
                    if (selectedKfk.every(val => val.includes('KFK') || val === 'Keperawatan')) {
                        isValid = false;
                        errorMessages.push('Keduanya tidak boleh berupa KFK. Pilih 1 KFK dan 1 Jenjang.');
                    }
                    if (selectedKfk.every(val => val.includes('PK') || val.includes('BK'))) {
                        isValid = false;
                        errorMessages.push('Keduanya tidak boleh berupa Jenjang. Pilih 1 KFK dan 1 Jenjang.');
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilihan Tidak Sesuai',
                        html: '<ul style="text-align:left">' + errorMessages.map(msg => '<li>' + msg + '</li>').join('') + '</ul>',
                        confirmButtonColor: '#0ea5e9'
                    });
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="bi bi-arrow-right-circle-fill me-2"></i>Buat Instrumen OPPE';
                    return false;
                }
                
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Membuat Instrumen...';
            });
        });
    </script>
@endpush