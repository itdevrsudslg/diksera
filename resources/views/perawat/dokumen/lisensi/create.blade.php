@extends('layouts.app')

@section('title', 'Buat Pengajuan')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />
    <style>
        .form-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.05);
            padding: 32px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 8px;
            color: #334155;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
        }

        .btn-submit {
            background: #0ea5e9;
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 10px;
            border: none;
            width: 100%;
            transition: 0.2s;
        }

        .btn-submit:hover {
            background: #0284c7;
            transform: translateY(-2px);
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-1">
                            {{ $metode == 'interview_only' ? 'Pengajuan Kredensialing' : 'Pengajuan Uji Kompetensi' }}
                        </h2>
                        <p class="text-muted mb-0">Lengkapi data di bawah ini</p>
                    </div>
                    <a href="{{ route('perawat.pengajuan.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <div class="form-card">
                    <form action="{{ route('perawat.pengajuan.store') }}" method="POST" enctype="multipart/form-data" id="formPengajuan">
                        @csrf

                        {{-- Kirim Metode ke Controller --}}
                        <input type="hidden" name="metode" value="{{ $metode }}">

                        {{-- 1. STATUS PENGAJUAN --}}
                        <div class="mb-4">
                            <label class="form-label">Jenis Pengajuan</label>

                            <select name="jenis_pengajuan" class="form-select form-select-lg bg-light @error('jenis_pengajuan') is-invalid @enderror" required>
                                {{-- LOGIKA: Jika belum punya lisensi tipe ini, tampilkan BARU --}}
                                @if (!$sudahPunyaLisensi)
                                    <option value="baru" selected>Pengajuan Baru</option>
                                @else
                                    {{-- Jika sudah punya, paksa PERPANJANGAN --}}
                                    <option value="perpanjangan" selected>Perpanjangan / Re-Sertifikasi</option>
                                @endif
                            </select>

                            @error('jenis_pengajuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            {{-- Info Helper --}}
                            @if ($sudahPunyaLisensi)
                                <div class="alert alert-warning mt-2 small d-flex align-items-center">
                                    <i class="bi bi-exclamation-circle me-2"></i>
                                    Anda sudah terdaftar di kategori ini. Silakan lakukan perpanjangan apabila sudah expired.
                                </div>
                            @else
                                <div class="form-text text-muted mt-2">
                                    Anda belum memiliki dokumen kategori ini, silakan ajukan baru.
                                </div>
                            @endif
                        </div>

                        <hr class="border-light my-4">

                        {{-- 2. FORM DINAMIS --}}

                        {{-- A. FORM BARU (Render jika belum punya) --}}
                        @if (!$sudahPunyaLisensi)
                            <div id="area-baru">
                                <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    @if ($metode == 'interview_only')
                                        <strong>Syarat Kredensialing:</strong> Upload Sertifikat PK (Ujikom), Logbook, dan
                                        Surat Permohonan dalam 1 file PDF.
                                    @else
                                        Silakan upload dokumen persyaratan lengkap dalam 1 file PDF.
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">  {{ $metode == 'interview_only' ? 'Surat Rekomendasi Kredensialing' : 'Surat Rekomendasi Uji Kompetensi' }}<span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="file_dokumen_baru" class="form-control @error('file_dokumen_baru') is-invalid @enderror"
                                        accept="application/pdf" required>
                                    @error('file_dokumen_baru')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Maksimal 5MB</small>
                                </div>
                            </div>
                        @endif

                        {{-- B. FORM PERPANJANGAN (Render jika sudah punya) --}}
                        @if ($sudahPunyaLisensi)
                            <div id="area-perpanjangan">
                                <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-warning mb-3">
                                    <i class="bi bi-arrow-repeat me-2"></i>
                                    Upload dokumen lama untuk verifikasi perpanjangan.
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                {{ $metode == 'interview_only' ? 'SPK / RKK Lama' : 'Sertifikat Kompetensi Lama' }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="file" name="file_sertifikat_lama" class="form-control @error('file_sertifikat_lama') is-invalid @enderror"
                                                accept=".pdf,.jpg,.jpeg,.png" required>
                                            @error('file_sertifikat_lama')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">  {{ $metode == 'interview_only' ? 'Surat Rekomendasi Kredensialing / Re-Kredensialing ' : 'Surat Rekomendasi Uji Kompetensi' }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="file" name="file_surat_rekomendasi" class="form-control @error('file_surat_rekomendasi') is-invalid @enderror"
                                                accept=".pdf,.jpg,.jpeg,.png" required>
                                            @error('file_surat_rekomendasi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <hr class="border-light my-4">

                        {{-- 3. LINK GDRIVE --}}
                        <div class="mb-4">
                            <label class="form-label">  {{ $metode == 'interview_only' ? 'Link Google Drive Berkas Pendukung Kredensialing / Re-Kredensialing' : 'Link Google Drive Berkas Pendukung Uji Kompetensi' }} <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-success border-end-0"><i
                                        class="bi bi-google"></i></span>
                                <input type="url" name="link_gdrive" class="form-control border-start-0 ps-0 @error('link_gdrive') is-invalid @enderror"
                                    placeholder="https://drive.google.com/..." required value="{{ old('link_gdrive') }}">
                            </div>
                            @error('link_gdrive')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 4. KFK --}}
                      {{-- 4. KFK --}}
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
                                    <option value="PK 1">PK 1</option>
                                    
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

@if($sedangMengajukan)
    {{-- Jika ada pengajuan yang masih jalan, kunci button-nya --}}
    <button type="button" class="btn btn-secondary w-100 p-3" disabled>
        <i class="bi bi-hourglass-split me-2"></i>Anda Sedang Mengajukan
    </button>
    <div class="alert alert-info mt-3 small">
        <i class="bi bi-info-circle me-2"></i> 
        Mohon tunggu proses pengajuan sebelumnya selesai sebelum dapat mengirimkan kembali.
    </div>
@else
    {{-- Jika tidak ada pengajuan gantung, tampilkan button kirim --}}
    <button type="submit" class="btn btn-submit" id="btnSubmit">
        <i class="bi bi-send-fill me-2"></i>Kirim Pengajuan
    </button>
@endif

                    </form>
                      <small class="text-muted">Pilih KFK dan level PK</small>
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

            const pkAutoOptions = ['PRA PK', 'PK 1'];
            const bkAutoOptions = ['PRA BK', 'BK 1'];

            // ==========================================
            // LOGIKA AUTO-SELECT & PENGUNCIAN KFK
            // ==========================================
            elementKfk.addEventListener('addItem', function(event) {
                const addedValue = event.detail.value;
                const selectedValues = choicesInstance.getValue(true);

                // Aturan 1: PK 1 / PRA PK -> KFK otomatis "Keperawatan"
                if (pkAutoOptions.includes(addedValue)) {
                    // Hapus KFK lain yang mungkin terlanjur dipilih user
                    selectedValues.forEach(val => {
                        if (val !== addedValue && (val.includes('KFK') || val === 'Keperawatan')) {
                            choicesInstance.removeActiveItemsByValue(val);
                        }
                    });
                    choicesInstance.setChoiceByValue('Keperawatan');
                    Swal.fire({icon: 'success', title: 'Otomatis Dipilih', text: 'KFK otomatis diatur ke "Keperawatan"', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
                } 
                // Aturan 2: BK 1 / PRA BK -> KFK otomatis "KFK Maternitas"
                else if (bkAutoOptions.includes(addedValue)) {
                    selectedValues.forEach(val => {
                        if (val !== addedValue && (val.includes('KFK') || val === 'Keperawatan')) {
                            choicesInstance.removeActiveItemsByValue(val);
                        }
                    });
                    choicesInstance.setChoiceByValue('KFK Maternitas');
                    Swal.fire({icon: 'success', title: 'Otomatis Dipilih', text: 'KFK otomatis diatur ke "KFK Maternitas"', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
                }
                // Jika user iseng pilih KFK lain padahal PK1/BK1 sudah kepilih (dikunci)
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

                // Alert batas maksimal biasa
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

            // Mencegah user menghapus (x) KFK yang sudah dilock
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
            // ==========================================


            // Tampilkan error dari Laravel
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    html: '<ul style="text-align:left">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                    confirmButtonColor: '#0ea5e9'
                });
            @endif

            @if(session('swal'))
                Swal.fire({
                    icon: '{{ session('swal.icon') }}',
                    title: '{{ session('swal.title') }}',
                    text: '{{ session('swal.text') }}',
                    confirmButtonColor: '#0ea5e9'
                });
            @endif

            // Form validation Submit
            const form = document.getElementById('formPengajuan');
            const btnSubmit = document.getElementById('btnSubmit');

            form.addEventListener('submit', function(e) {
                let isValid = true;
                let errorMessages = [];

                // Validasi file upload
                @if (!$sudahPunyaLisensi)
                    const fileBaru = document.querySelector('input[name="file_dokumen_baru"]');
                    if (fileBaru && !fileBaru.files.length) {
                        isValid = false;
                        errorMessages.push('File dokumen pengajuan wajib diupload');
                    } else if (fileBaru && fileBaru.files[0]) {
                        const maxSize = 5 * 1024 * 1024; // 5MB
                        if (fileBaru.files[0].size > maxSize) {
                            isValid = false;
                            errorMessages.push('Ukuran file dokumen maksimal 5MB');
                        }
                    }
                @else
                    const fileSertifikat = document.querySelector('input[name="file_sertifikat_lama"]');
                    const fileRekomendasi = document.querySelector('input[name="file_surat_rekomendasi"]');
                    
                    if (!fileSertifikat.files.length) {
                        isValid = false;
                        errorMessages.push('File sertifikat lama wajib diupload');
                    }
                    if (!fileRekomendasi.files.length) {
                        isValid = false;
                        errorMessages.push('File surat rekomendasi wajib diupload');
                    }
                @endif

                // Validasi Google Drive link
                const linkGdrive = document.querySelector('input[name="link_gdrive"]').value;
                if (!linkGdrive.includes('drive.google.com')) {
                    isValid = false;
                    errorMessages.push('Link harus valid dan bersumber dari Google Drive');
                }

                const selectedKfk = choicesInstance.getValue(true); 
    
                if (selectedKfk.length !== 2) {
                    isValid = false;
                    errorMessages.push('Anda harus memilih tepat 2 item (1 KFK dan 1 Jenjang PK/BK)');
                } else {
                    // Update validasi: Menerima "KFK" ATAU "Keperawatan"
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
                    btnSubmit.innerHTML = '<i class="bi bi-send-fill me-2"></i>Kirim Pengajuan';
                    return false;
                }
                
                // Loading state
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
            });
        });
    </script>
@endpush