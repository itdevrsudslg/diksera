@extends('layouts.app')

@section('title', 'Edit Kredensialing')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <style>
        :root {
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bg-light: #f1f5f9;
            --input-border: #cbd5e1;
            /* Warna Tema: Ungu */
            --accent-color: #7c3aed;
            --accent-hover: #6d28d9;
            --accent-light: #f5f3ff;
            --accent-border: #ddd6fe;
        }
        body { background-color: var(--bg-light); color: var(--text-dark); font-size: 14px; }
        .page-header { margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .page-title { font-size: 1.5rem; font-weight: 700; color: var(--text-dark); margin: 0; }
        .form-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); padding: 30px; }
        .form-label { font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 6px; }
        .required-star { color: #ef4444; font-size: 12px; vertical-align: top; }
        .form-control, .form-select { border: 1px solid var(--input-border); border-radius: 8px; padding: 8px 12px; font-size: 0.9rem; line-height: 1.5; height: auto; }
        .form-control:focus, .form-select:focus { border-color: var(--accent-color); box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15); outline: none; }
        .input-group-text { background-color: #f8fafc; border: 1px solid var(--input-border); border-right: none; color: var(--text-gray); border-top-left-radius: 8px; border-bottom-left-radius: 8px; padding: 8px 12px; font-size: 0.9rem; }
        
        /* FIX CHOICES JS LAYOUT */
        .choices { width: 100%; margin-bottom: 0; }
        .choices__inner { background-color: #fff; border: 1px solid var(--input-border); border-radius: 8px; min-height: 40px; padding: 4px 8px !important; display: flex; align-items: center; font-size: 0.9rem; }
        .choices.is-focused .choices__inner { border-color: var(--accent-color); box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15); }
        .choices__list--multiple .choices__item { background-color: var(--accent-color); border-color: var(--accent-color); font-size: 0.85rem; border-radius: 4px;}
        .choices__list--single { padding: 0; }
        
        .metode-wrapper { background-color: var(--accent-light); border: 1px solid var(--accent-border); border-left: 4px solid var(--accent-color); border-radius: 8px; padding: 16px; }
        .btn-submit-edit { background-color: var(--accent-color); color: white; width: 100%; padding: 10px; border-radius: 8px; font-weight: 600; font-size: 0.95rem; border: none; box-shadow: 0 2px 4px rgba(124, 58, 237, 0.2); transition: all 0.2s; }
        .btn-submit-edit:hover { background-color: var(--accent-hover); transform: translateY(-1px); box-shadow: 0 4px 6px rgba(124, 58, 237, 0.3); color: white; }
        .btn-back { background: white; border: 1px solid #cbd5e1; color: var(--text-gray); padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s; }
        .btn-back:hover { background: #f1f5f9; color: var(--text-dark); border-color: #94a3b8; }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Edit Lisensi Kredensialing</h1>
                        <p class="text-muted small mb-0">Update informasi RKK / SPK / SPKK.</p>
                    </div>
                    <a href="{{ route('admin.lisensi_interview_only.index') }}" class="btn-back">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="form-card">
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger mb-4 rounded-2 py-2 px-3 small">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.lisensi_interview_only.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="metode-wrapper">
                                    <div class="d-flex gap-3 align-items-center">
                                        <i class="bi bi-person-video2 fs-5" style="color: var(--accent-color);"></i>
                                        <div class="flex-grow-1">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <label class="form-label text-dark mb-0">Metode Perpanjangan</label>
                                                    <div class="text-muted" style="font-size: 11px;">Mode edit dikunci sesuai tipe lisensi.</div>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <input type="hidden" name="metode_perpanjangan" value="interview_only">
                                                    <span class="badge" style="background-color: var(--accent-color); font-size: 12px; padding: 6px 12px;">Hanya Wawancara</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Pemilik Lisensi <span class="required-star">*</span></label>
                                {{-- FIX: Hilangkan .input-group agar select tunggal juga rapi --}}
                                <select name="user_id" id="choice-user-single" class="form-select" required>
                                    <option value="">Pilih Perawat...</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $data->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->unit_kerja ?? 'Unit Tidak Ada' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12"><hr class="border-light m-0"></div>

                            <div class="col-md-12">
                                <label class="form-label">Jenjang KFK<span class="required-star">*</span></label>
                                @php
                                    $rawKfk = old('kfk', $data->kfk ?? []);
                                    $selectedKfk = [];
                                    if (is_string($rawKfk)) {
                                        $decoded = json_decode($rawKfk, true);
                                        $selectedKfk = is_array($decoded) ? $decoded : [$rawKfk];
                                    } elseif (is_array($rawKfk)) {
                                        $selectedKfk = $rawKfk;
                                    }
                                @endphp
                                {{-- FIX: Hilangkan .input-group --}}
                                <select name="kfk[]" id="choice-kfk-edit" class="form-select @error('kfk') is-invalid @enderror" multiple required>
                                    <option value="" disabled>Pilih KFK...</option>
                                    <optgroup label="KFK KRITIS">
                                        @foreach(['Keperawatan','KFK Kritis Gawat Darurat', 'KFK Kritis ICU', 'KFK Kritis ICVCU', 'KFK Kritis Anestesi'] as $item)
                                            <option value="{{ $item }}" {{ in_array($item, $selectedKfk) ? 'selected' : '' }}>{{ $item }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="KFK MEDIKAL BEDAH">
                                        @foreach(['KFK Medikal Bedah Penyakit Dalam', 'KFK Medikal Bedah Bedah', 'KFK Medikal Bedah Perioperatif', 'KFK Medikal Bedah Isolasi (Keperawatan Khusus)'] as $item)
                                            <option value="{{ $item }}" {{ in_array($item, $selectedKfk) ? 'selected' : '' }}>{{ $item }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="KFK ANAK">
                                        @foreach(['KFK Anak NICU', 'KFK Anak PICU', 'KFK Anak Pediatri', 'KFK Anak Neonatus'] as $item)
                                            <option value="{{ $item }}" {{ in_array($item, $selectedKfk) ? 'selected' : '' }}>{{ $item }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="KFK MATERNITAS">
                                        <option value="KFK Maternitas" {{ in_array('KFK Maternitas', $selectedKfk) ? 'selected' : '' }}>KFK Maternitas</option>
                                    </optgroup>
                                    <optgroup label="JENIS PERAWAT KLINIS (PK)">
                                        @php
                                            $pks = [
                                                'PRA PK', 'PK 1',
                                                'PK 2 KEP GAWAT DARURAT', 'PK 3 KEP GAWAT DARURAT', 'PK 4 KEP GAWAT DARURAT', 'PK 5 KEP GAWAT DARURAT',
                                                'PK 2 KEP ICU', 'PK 3 KEP ICU', 'PK 4 KEP ICU', 'PK 5 KEP ICU',
                                                'PK 2 KEP ICVCU', 'PK 3 KEP ICVCU', 'PK 4 KEP ICVCU', 'PK 5 KEP ICVCU',
                                                'PK 2 KEP ANESTESI', 'PK 3 KEP ANESTESI', 'PK 4 KEP ANESTESI', 'PK 5 KEP ANESTESI',
                                                'PK 2 KEP PENYAKIT DALAM', 'PK 3 KEP PENYAKIT DALAM', 'PK 4 KEP PENYAKIT DALAM', 'PK 5 KEP PENYAKIT DALAM',
                                                'PK 2 KEP BEDAH', 'PK 3 KEP BEDAH', 'PK 4 KEP BEDAH', 'PK 5 KEP BEDAH',
                                                'PK 2 KEP PERIOPERATIF', 'PK 3 KEP PERIOPERATIF', 'PK 4 KEP PERIOPERATIF', 'PK 5 KEP PERIOPERATIF',
                                                'PK 2 KEP ISOLASI (KEPERAWATAN KHUSUS)', 'PK 3 KEP ISOLASI (KEPERAWATAN KHUSUS)', 'PK 4 KEP ISOLASI (KEPERAWATAN KHUSUS)', 'PK 5 KEP ISOLASI (KEPERAWATAN KHUSUS)',
                                                'PK 2 KEP NICU', 'PK 3 KEP NICU', 'PK 4 KEP NICU', 'PK 5 KEP NICU',
                                                'PK 2 KEP PICU', 'PK 3 KEP PICU', 'PK 4 KEP PICU', 'PK 5 KEP PICU',
                                                'PK 2 KEP PEDIATRI', 'PK 3 KEP PEDIATRI', 'PK 4 KEP PEDIATRI', 'PK 5 KEP PEDIATRI',
                                                'PK 2 KEP NEONATUS', 'PK 3 KEP NEONATUS', 'PK 4 KEP NEONATUS', 'PK 5 KEP NEONATUS'
                                            ];
                                        @endphp
                                        @foreach($pks as $pk)
                                            <option value="{{ $pk }}" {{ in_array($pk, $selectedKfk) ? 'selected' : '' }}>{{ $pk }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="JENIS BIDAN KLINIS (BK)">
                                        @foreach(['PRA BK', 'BK 1', 'BK 2 MATERNITAS', 'BK 3 MATERNITAS', 'BK 4 MATERNITAS', 'BK 5 MATERNITAS'] as $bk)
                                            <option value="{{ $bk }}" {{ in_array($bk, $selectedKfk) ? 'selected' : '' }}>{{ $bk }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('kfk')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Pelaksanaan (Mulai) <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                    <input type="date" name="tgl_mulai" class="form-control" value="{{ old('tgl_mulai', isset($data->tgl_mulai) ? \Carbon\Carbon::parse($data->tgl_mulai)->format('Y-m-d') : '') }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai Diselenggarakan <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                    <input type="date" name="tgl_diselenggarakan" class="form-control" value="{{ old('tgl_diselenggarakan', isset($data->tgl_diselenggarakan) ? \Carbon\Carbon::parse($data->tgl_diselenggarakan)->format('Y-m-d') : '') }}" required>
                                </div>
                            </div>

                            <div class="col-12"><hr class="border-light m-0"></div>

                            <div class="col-md-4">
                                <label class="form-label">Bidang <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-journal-medical"></i></span>
                                    <input type="text" name="bidang" class="form-control" value="{{ old('bidang', $data->bidang) }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Nama Lisensi <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
                                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $data->nama) }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Lembaga Penerbit <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <input type="text" name="lembaga" class="form-control" value="{{ old('lembaga', $data->lembaga) }}" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Nomor Lisensi <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                    <input type="text" name="nomor" class="form-control font-monospace" value="{{ old('nomor', $data->nomor) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Terbit <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                    <input type="date" name="tgl_terbit" class="form-control" value="{{ old('tgl_terbit', isset($data->tgl_terbit) ? \Carbon\Carbon::parse($data->tgl_terbit)->format('Y-m-d') : '') }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Expired <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-x"></i></span>
                                    <input type="date" name="tgl_expired" class="form-control" value="{{ old('tgl_expired', isset($data->tgl_expired) ? \Carbon\Carbon::parse($data->tgl_expired)->format('Y-m-d') : '') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-2">
                            <button type="submit" class="btn-submit-edit">
                                <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const elementUser = document.getElementById('choice-user-single');
            new Choices(elementUser, {
                searchEnabled: true, placeholderValue: 'Cari perawat...', noResultsText: 'Tidak ditemukan', itemSelectText: '', shouldSort: false, classNames: { containerInner: 'choices__inner', input: 'choices__input' }
            });

            const elementKfk = document.getElementById('choice-kfk-edit');
            new Choices(elementKfk, {
                removeItemButton: true, searchEnabled: true, placeholderValue: 'Ketik untuk mencari KFK...', itemSelectText: '', noResultsText: 'Data tidak ditemukan', shouldSort: false, classNames: { containerInner: 'choices__inner', input: 'choices__input' }
            });
        });
    </script>
@endpush