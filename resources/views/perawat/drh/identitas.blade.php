@extends('layouts.app')

@section('title', 'Edit Identitas – DIKSERA')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">

                {{-- Header Page --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Edit Profil</h4>
                        <p class="text-muted small mb-0">Perbarui data identitas dan riwayat hidup perawat.</p>
                    </div>
                    <a href="{{ route('perawat.drh') }}" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                        <div class="d-flex align-items-center mb-1">
                            <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                            <h6 class="mb-0 fw-bold">Gagal Menyimpan Data</h6>
                        </div>
                        <ul class="mb-0 small ps-4 mt-2">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('perawat.identitas.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    {{-- Method POST sesuai route --}}

                    <div class="dash-card shadow-sm mb-4">
                        <div class="card-body p-4">

                            {{-- SECTION A: DATA PRIBADI --}}
                            <div class="section-header mb-4">
                                <i class="bi bi-person-badge me-2"></i>A. Data Pribadi & Identitas
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-8">
                                    <label class="form-label">Nama Lengkap (Tanpa Gelar) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="nama_lengkap" class="form-control"
                                        value="{{ old('nama_lengkap', $profile->nama_lengkap ?? $user->name) }}"
                                        placeholder="Contoh: Ns. Siti Aminah, S.Kep">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jenjang Klinis (Pilih KFK dan Level PK)<span class="text-danger">*</span></label>
                                    <select name="type_perawat[]" id="type-perawat" class="form-select" multiple>
                                        <option value="" disabled>Pilih KFK...</option>
                                        @php
                                            $typeList = [
                                                'KFK KRITIS' => [
                                                    'KFK Kritis Gawat Darurat', 'KFK Kritis ICU', 'KFK Kritis ICVCU', 'KFK Kritis Anestesi'
                                                ],
                                                'KFK MEDIKAL BEDAH' => [
                                                    'KFK Medikal Bedah Penyakit Dalam', 'KFK Medikal Bedah Bedah', 'KFK Medikal Bedah Perioperatif', 'KFK Medikal Bedah Isolasi (Keperawatan Khusus)'
                                                ],
                                                'KFK ANAK' => [
                                                    'KFK Anak NICU', 'KFK Anak PICU', 'KFK Anak Pediatri', 'KFK Anak Neonatus'
                                                ],
                                                'KFK MATERNITAS' => ['KFK Maternitas'],
                                                'JENIS PERAWAT KLINIS (PK)' => [
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
                                                    'PK 2 KEP NEONATUS', 'PK 3 KEP NEONATUS', 'PK 4 KEP NEONATUS', 'PK 5 KEP NEONATUS',
                                                ],
                                                'JENIS BIDAN KLINIS (BK)' => [
                                                    'PRA BK', 'BK 1', 'BK 2 MATERNITAS', 'BK 3 MATERNITAS', 'BK 4 MATERNITAS', 'BK 5 MATERNITAS'
                                                ],
                                            ];

                                            $selectedValue = old('type_perawat', $profile->type_perawat ?? []);
                                            if (is_string($selectedValue)) {
                                                $decoded = json_decode($selectedValue, true);
                                                $selectedValue = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : ($selectedValue ? [$selectedValue] : []);
                                            } elseif (!is_array($selectedValue)) {
                                                $selectedValue = $selectedValue ? [$selectedValue] : [];
                                            }
                                        @endphp

                                        @foreach ($typeList as $groupLabel => $options)
                                            <optgroup label="{{ $groupLabel }}">
                                                @foreach ($options as $type)
                                                    <option value="{{ $type }}" {{ in_array($type, $selectedValue) ? 'selected' : '' }}>
                                                        {{ $type }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>

                              <div class="col-md-4">
    <label class="form-label">NIK (KTP)</label>
    <input type="text" name="nik" class="form-control" value="{{ old('nik', $profile->nik ?? '') }}" placeholder="16 digit angka">
</div>
<!-- TAMBAHKAN INI -->
<div class="col-md-4">
    <label class="form-label">NPWP</label>
    <input type="text" name="npwp" class="form-control" value="{{ old('npwp', $profile->npwp ?? '') }}" placeholder="Nomor NPWP">
</div>
                                
                              <div class="col-md-8">
        <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control"
                value="{{ old('email', $user->email) }}" placeholder="email@contoh.com">
        </div>
        <div class="form-text small">Email digunakan untuk login ke aplikasi.</div>
    </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" class="form-control"
                                        value="{{ old('tempat_lahir', $profile->tempat_lahir ?? '') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" class="form-control"
                                        value="{{ old('tanggal_lahir', $profile->tanggal_lahir ?? '') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-select">
                                        <option value="">Pilih...</option>
                                        <option value="L" {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Agama</label>
                                    <select name="agama" class="form-select">
                                        <option value="">Pilih...</option>
                                        @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $agm)
                                            <option value="{{ $agm }}" {{ old('agama', $profile->agama ?? '') == $agm ? 'selected' : '' }}>{{ $agm }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Status Perkawinan</label>
                                    <select name="status_perkawinan" class="form-select">
                                        <option value="">Pilih...</option>
                                        @foreach (['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $sts)
                                            <option value="{{ $sts }}" {{ old('status_perkawinan', $profile->status_perkawinan ?? '') == $sts ? 'selected' : '' }}>{{ $sts }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!--<div class="col-md-4">-->
                                <!--    <label class="form-label">Aliran Kepercayaan <small class="text-muted">(Opsional)</small></label>-->
                                <!--    <input type="text" name="aliran_kepercayaan" class="form-control"-->
                                <!--        value="{{ old('aliran_kepercayaan', $profile->aliran_kepercayaan ?? '') }}">-->
                                <!--</div>-->
                                <div class="col-md-12">
                                    <label class="form-label">Hobi / Kegemaran</label>
                                    <input type="text" name="hobby" class="form-control"
                                        value="{{ old('hobby', $profile->hobby ?? '') }}"
                                        placeholder="Contoh: Membaca Jurnal, Jogging, Renang">
                                </div>
                            </div>

                            {{-- SECTION B: KONTAK --}}
                            <div class="section-header mb-4 mt-5">
                                <i class="bi bi-geo-alt-fill me-2"></i>B. Kontak & Domisili
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Nomor HP (WhatsApp) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted">+62</span>
                                        <input type="text" name="no_hp" class="form-control"
                                            value="{{ old('no_hp', $profile->no_hp ?? '') }}" placeholder="812xxxx">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kota Domisili</label>
                                    <input type="text" name="kota" class="form-control"
                                        value="{{ old('kota', $profile->kota ?? '') }}"
                                        placeholder="Contoh: Jakarta Selatan">
                                </div>
                               <div class="col-12">
    <label class="form-label">Alamat Lengkap (Jalan / RT RW)</label>
    <textarea name="alamat" rows="2" class="form-control" placeholder="Jalan, RT/RW">{{ old('alamat', $profile->alamat ?? '') }}</textarea>
</div>
<!-- TAMBAHKAN INI -->
<div class="col-md-6">
    <label class="form-label">Kelurahan / Desa</label>
    <input type="text" name="kelurahan_desa" class="form-control" value="{{ old('kelurahan_desa', $profile->kelurahan_desa ?? '') }}">
</div>
<div class="col-md-6">
    <label class="form-label">Kecamatan</label>
    <input type="text" name="kecamatan" class="form-control" value="{{ old('kecamatan', $profile->kecamatan ?? '') }}">
</div>
                            </div>

                            {{-- SECTION C: FISIK --}}
                            <div class="section-header mb-4 mt-5">
                                <i class="bi bi-heart-pulse-fill me-2"></i>C. Keterangan Fisik
                            </div>
                            <div class="bg-light p-3 rounded-3 mb-4">
                                <div class="row g-3">
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small text-muted">Tinggi (cm)</label>
                                        <input type="number" name="tinggi_badan" class="form-control"
                                            value="{{ old('tinggi_badan', $profile->tinggi_badan ?? '') }}">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small text-muted">Berat (kg)</label>
                                        <input type="number" name="berat_badan" class="form-control"
                                            value="{{ old('berat_badan', $profile->berat_badan ?? '') }}">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small text-muted">Gol. Darah</label>
                                        <select name="golongan_darah" class="form-select">
                                            <option value="">-</option>
                                            @foreach (['A', 'B', 'AB', 'O'] as $gd)
                                                <option value="{{ $gd }}" {{ old('golongan_darah', $profile->golongan_darah ?? '') == $gd ? 'selected' : '' }}>{{ $gd }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small text-muted">Warna Kulit</label>
                                        <input type="text" name="warna_kulit" class="form-control"
                                            value="{{ old('warna_kulit', $profile->warna_kulit ?? '') }}"
                                            placeholder="Sawo Matang">
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <label class="form-label small text-muted">Rambut</label>
                                        <input type="text" name="rambut" class="form-control"
                                            value="{{ old('rambut', $profile->rambut ?? '') }}"
                                            placeholder="Hitam, Ikal">
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <label class="form-label small text-muted">Bentuk Muka</label>
                                        <input type="text" name="bentuk_muka" class="form-control"
                                            value="{{ old('bentuk_muka', $profile->bentuk_muka ?? '') }}"
                                            placeholder="Oval">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label small text-muted">Cacat Tubuh</label>
                                        <input type="text" name="cacat_tubuh" class="form-control"
                                            value="{{ old('cacat_tubuh', $profile->cacat_tubuh ?? '') }}"
                                            placeholder="-">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small text-muted">Ciri Khas</label>
                                        <input type="text" name="ciri_khas" class="form-control"
                                            value="{{ old('ciri_khas', $profile->ciri_khas ?? '') }}"
                                            placeholder="Contoh: Tahi lalat di pipi kanan">
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION D: KEPEGAWAIAN (UPDATE) --}}
                            <div class="section-header mb-4 mt-5">
                                <i class="bi bi-briefcase-fill me-2"></i>D. Data Kepegawaian & Profesi
                            </div>
                            
                            {{-- Baris 1: Profesi & Status --}}
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Profesi <span class="text-danger">*</span></label>
                                    <select name="profesi" id="select_profesi" class="form-select">
                                        <option value="">-- Pilih --</option>
                                        <option value="Perawat" {{ old('profesi', $profile->profesi ?? '') == 'Perawat' ? 'selected' : '' }}>Perawat</option>
                                        <option value="Bidan" {{ old('profesi', $profile->profesi ?? '') == 'Bidan' ? 'selected' : '' }}>Bidan</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status Kepegawaian <span class="text-danger">*</span></label>
                                    <select name="status_kepegawaian" id="select_status" class="form-select">
                                        <option value="">-- Pilih --</option>
                                        @foreach(['ASN - PNS', 'ASN - PPPK', 'ASN - PPPK PARUH WAKTU', 'NON ASN - KARYAWAN TETAP', 'NON ASN - KONTRAK'] as $st)
                                            <option value="{{ $st }}" {{ old('status_kepegawaian', $profile->status_kepegawaian ?? '') == $st ? 'selected' : '' }}>{{ $st }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Baris 2: NIP / NIRP (Conditional) --}}
                            <div class="row g-3 mb-3">
                                {{-- NIP (Muncul jika ASN) --}}
                                <div class="col-12 d-none" id="wrapper_nip">
                                    <label class="form-label">NIP <span class="text-danger">*</span></label>
                                    <input type="text" name="nip" id="input_nip" class="form-control"
                                        value="{{ old('nip', $profile->nip ?? '') }}">
                                </div>
                                {{-- NIRP (Muncul jika NON ASN) --}}
                                <div class="col-12 d-none" id="wrapper_nirp">
                                    <label class="form-label">NIRP <span class="text-danger">*</span></label>
                                    <input type="text" name="nirp" id="input_nirp" class="form-control"
                                        value="{{ old('nirp', $profile->nirp ?? '') }}">
                                </div>
                            </div>

                            {{-- Baris 3: Jabatan, Pangkat, Golongan --}}
                     <div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Jabatan Fungsional <span class="text-danger">*</span></label>
        <select name="jabatan" id="select_jabatan" class="form-select">
            <option value="">-- Pilih Profesi & Status Dulu --</option>
        </select>
        <input type="hidden" id="old_jabatan" value="{{ old('jabatan', $profile->jabatan ?? '') }}">
    </div>
    
    <!-- TAMBAHAN: JABATAN STRUKTURAL -->
    <div class="col-md-4">
        <label class="form-label">Jabatan Struktural <span class="text-danger">*</span></label>
        <select name="jabatan_struktural" class="form-select" required>
            <option value="pelaksana" {{ old('jabatan_struktural', $profile->jabatan_struktural ?? '') == 'pelaksana' ? 'selected' : '' }}>Staf / Pelaksana</option>
            <option value="kepala_ruangan" {{ old('jabatan_struktural', $profile->jabatan_struktural ?? '') == 'kepala_ruangan' ? 'selected' : '' }}>Kepala Ruangan (Karu)</option>
            <option value="kepala_seksi" {{ old('jabatan_struktural', $profile->jabatan_struktural ?? '') == 'kepala_seksi' ? 'selected' : '' }}>Kepala Seksi (Kasi)</option>
        </select>
        <small class="text-muted" style="font-size:10px;">Penentu akses evaluasi OPPE</small>
    </div>
    
    <div class="col-6 col-md-2">
        <label class="form-label">Pangkat</label>
        <input type="text" name="pangkat" id="pangkat_input" class="form-control" value="{{ old('pangkat', $profile->pangkat ?? '') }}" placeholder="Otomatis" readonly>
    </div>
    
    <div class="col-6 col-md-2">
        <label class="form-label">Golongan</label>
        <select name="golongan" id="golongan_select" class="form-select">
                                        <option value="" data-pangkat="">-- Pilih --</option>
                                        <option value="None" data-pangkat="None" {{ old('golongan', $profile->golongan ?? '') == 'None' ? 'selected' : '' }}>None</option>
                                        @php
                                            $golonganMap = [
                                                'IIa' => 'Pengatur Muda', 'IIb' => 'Pengatur Muda Tingkat I', 'IIc' => 'Pengatur', 'IId' => 'Pengatur Tingkat I',
                                                'IIIa' => 'Penata Muda', 'IIIb' => 'Penata Muda Tingkat I', 'IIIc' => 'Penata', 'IIId' => 'Penata Tingkat I',
                                                'IVa' => 'Pembina', 'IVb' => 'Pembina Tingkat I', 'IVc' => 'Pembina Utama Muda', 'IVd' => 'Pembina Utama Madya', 'IVe' => 'Pembina Utama',
                                                'VII' => 'PPPK Gol VII', 'IX' => 'PPPK Gol IX', 'X' => 'PPPK Gol X'
                                            ];
                                        @endphp
                                        @foreach($golonganMap as $gol => $pang)
                                            <option value="{{ $gol }}" data-pangkat="{{ $pang }}" 
                                                {{ old('golongan', $profile->golongan ?? '') == $gol ? 'selected' : '' }}>
                                                {{ $gol }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
<!-- TAMBAHKAN INI DI DALAM SECTION D -->
<div class="row g-3 mt-2">
    <div class="col-md-4">
        <label class="form-label">TMT CPNS <small class="text-muted">(Jika ASN)</small></label>
        <input type="date" name="tmt_cpns" class="form-control" value="{{ old('tmt_cpns', $profile->tmt_cpns ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">TMT PNS / PPPK</label>
        <input type="date" name="tmt_pns" class="form-control" value="{{ old('tmt_pns', $profile->tmt_pns ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">TMT Jabatan Saat Ini</label>
        <input type="date" name="tmt_jabatan" class="form-control" value="{{ old('tmt_jabatan', $profile->tmt_jabatan ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Masa Kerja Golongan (Tahun)</label>
        <input type="number" name="masa_kerja_tahun" class="form-control" value="{{ old('masa_kerja_tahun', $profile->masa_kerja_tahun ?? '') }}" placeholder="Contoh: 5">
    </div>
    <div class="col-md-6">
        <label class="form-label">Masa Kerja Golongan (Bulan)</label>
        <input type="number" name="masa_kerja_bulan" class="form-control" value="{{ old('masa_kerja_bulan', $profile->masa_kerja_bulan ?? '') }}" placeholder="Contoh: 2">
    </div>
</div>
                            {{-- SECTION E: FOTO --}}
                            <div class="section-header mb-4 mt-5">
                                <i class="bi bi-camera-fill me-2"></i>E. Foto Profil
                            </div>
                            <div class="row align-items-center bg-light border rounded-3 p-3 mx-1">
                                <div class="col-auto">
                                    @if ($profile && $profile->foto_3x4)
                                        <img src="{{ asset('storage/' . $profile->foto_3x4) }}"
                                            class="avatar-preview shadow-sm">
                                    @else
                                        <div class="avatar-placeholder shadow-sm">
                                            <i class="bi bi-person text-secondary"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col">
                                    <label class="form-label fw-bold">Upload Foto Terbaru (3x4)</label>
                                    <input type="file" name="foto_3x4" class="form-control">
                                    <div class="form-text mt-1">
                                        Format: JPG, JPEG, PNG. Ukuran maksimal 2MB. Disarankan latar belakang Putih.
                                    </div>
                                </div>
                            </div>
{{-- SECTION F: KETERANGAN LAIN-LAIN --}}
<div class="section-header mb-4 mt-5">
    <i class="bi bi-file-earmark-text-fill me-2"></i>F. Surat Keterangan (SKCK, Sehat, dll)
</div>
<div class="row g-3">
    <h6 class="mb-0 fw-bold">1. Surat Keterangan Berkelakuan Baik (SKCK)</h6>
    <div class="col-md-4"><label class="form-label small text-muted">Pejabat Penandatangan</label><input type="text" name="skck_pejabat" class="form-control" value="{{ old('skck_pejabat', $profile->skck_pejabat ?? '') }}" placeholder="Kapolres..."></div>
    <div class="col-md-4"><label class="form-label small text-muted">Nomor Surat</label><input type="text" name="skck_nomor" class="form-control" value="{{ old('skck_nomor', $profile->skck_nomor ?? '') }}"></div>
    <div class="col-md-4"><label class="form-label small text-muted">Tanggal Surat</label><input type="date" name="skck_tanggal" class="form-control" value="{{ old('skck_tanggal', $profile->skck_tanggal ?? '') }}"></div>

    <h6 class="mb-0 fw-bold mt-4">2. Surat Keterangan Sehat Jasmani & Rohani</h6>
    <div class="col-md-4"><label class="form-label small text-muted">Pejabat Penandatangan</label><input type="text" name="sehat_pejabat" class="form-control" value="{{ old('sehat_pejabat', $profile->sehat_pejabat ?? '') }}" placeholder="Dokter..."></div>
    <div class="col-md-4"><label class="form-label small text-muted">Nomor Surat</label><input type="text" name="sehat_nomor" class="form-control" value="{{ old('sehat_nomor', $profile->sehat_nomor ?? '') }}"></div>
    <div class="col-md-4"><label class="form-label small text-muted">Tanggal Surat</label><input type="date" name="sehat_tanggal" class="form-control" value="{{ old('sehat_tanggal', $profile->sehat_tanggal ?? '') }}"></div>

    <h6 class="mb-0 fw-bold mt-4">3. Keterangan Lain yang Dianggap Perlu</h6>
    <div class="col-md-4"><label class="form-label small text-muted">Pejabat Penandatangan</label><input type="text" name="ket_lain_pejabat" class="form-control" value="{{ old('ket_lain_pejabat', $profile->ket_lain_pejabat ?? '') }}"></div>
    <div class="col-md-4"><label class="form-label small text-muted">Nomor Surat</label><input type="text" name="ket_lain_nomor" class="form-control" value="{{ old('ket_lain_nomor', $profile->ket_lain_nomor ?? '') }}"></div>
    <div class="col-md-4"><label class="form-label small text-muted">Tanggal Surat</label><input type="date" name="ket_lain_tanggal" class="form-control" value="{{ old('ket_lain_tanggal', $profile->ket_lain_tanggal ?? '') }}"></div>
</div>


                        </div> {{-- End Card Body --}}

                        <div class="card-footer bg-white p-4 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('perawat.drh') }}" class="btn btn-light border px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                            </button>
                        </div>

                    </div> {{-- End Dash Card --}}
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Choices JS untuk KFK
           const typePerawatElement = document.getElementById('type-perawat');
    const choicesInstance = new Choices(typePerawatElement, {
        searchEnabled: true,
        searchPlaceholderValue: 'Cari KFK atau PK/BK...',
        removeItemButton: true,
        maxItemCount: 2, // Batasi tepat 2
        shouldSort: false,
    });

    // Alert saat user mencoba menambah item ke-3
    typePerawatElement.addEventListener('addItem', function() {
        if (choicesInstance.getValue(true).length >= 2) {
             Swal.fire({
                icon: 'info',
                title: 'Batas Terpenuhi',
                text: 'Silakan pilih tepat 1 KFK dan 1 Jenjang (PK/BK).',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }
    });

    // Validasi saat Form Submit
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const selected = choicesInstance.getValue(true);
        const hasKfk = selected.some(val => val.includes('KFK'));
        const hasJenjang = selected.some(val => val.includes('PK') || val.includes('BK'));

        if (selected.length !== 2 || !hasKfk || !hasJenjang) {
            e.preventDefault(); // Batalkan submit
            Swal.fire({
                icon: 'warning',
                title: 'Pilihan Tidak Lengkap',
                text: 'Anda wajib memilih 1 KFK dan 1 Level Klinis (PK/BK).',
                confirmButtonColor: '#3b82f6'
            });
        }
    });

            // 2. Logic Kepegawaian (Profesi, Status, NIP/NIRP, Jabatan)
            const selectProfesi = document.getElementById('select_profesi');
            const selectStatus  = document.getElementById('select_status');
            const selectJabatan = document.getElementById('select_jabatan');
            const wrapperNip    = document.getElementById('wrapper_nip');
            const wrapperNirp   = document.getElementById('wrapper_nirp');
            const oldJabatanVal = document.getElementById('old_jabatan').value;

            // Pangkat Otomatis
            const golonganSelect = document.getElementById('golongan_select');
            const pangkatInput   = document.getElementById('pangkat_input');

            // Data Map Jabatan
            const jabatanData = {
                'Perawat_ASN': [
                    'Perawat Terampil', 'Perawat Mahir', 'Perawat Penyelia',
                    'Perawat Ahli Pertama', 'Perawat Ahli Muda', 'Perawat Ahli Madya', 'Perawat Ahli Utama'
                ],
                'Bidan_ASN': [
                    'Bidan Terampil', 'Bidan Mahir', 'Bidan Penyelia',
                    'Bidan Ahli Pertama', 'Bidan Ahli Muda', 'Bidan Ahli Madya', 'Bidan Ahli Utama'
                ],
                'Perawat_NonASN': ['Perawat Pelaksana'],
                'Bidan_NonASN': ['Bidan Pelaksana']
            };

            function updateKepegawaianLogic() {
                const profesi = selectProfesi.value;
                const status  = selectStatus.value;

                // A. Show/Hide NIP vs NIRP
                wrapperNip.classList.add('d-none');
                wrapperNirp.classList.add('d-none');

                if (status.includes('ASN') && !status.includes('NON')) {
                    wrapperNip.classList.remove('d-none');
                } else if (status.includes('NON ASN')) {
                    wrapperNirp.classList.remove('d-none');
                }

                // B. Populate Jabatan Dropdown
                // Simpan value saat ini sebelum dihapus (jika user belum ganti, pakai oldJabatanVal)
                const currentVal = selectJabatan.value || oldJabatanVal;
                
                selectJabatan.innerHTML = '<option value="">-- Pilih Jabatan --</option>';

                if (profesi && status) {
                    let key = '';
                    if (profesi === 'Perawat') {
                        key = (status.includes('ASN') && !status.includes('NON')) ? 'Perawat_ASN' : 'Perawat_NonASN';
                    } else if (profesi === 'Bidan') {
                        key = (status.includes('ASN') && !status.includes('NON')) ? 'Bidan_ASN' : 'Bidan_NonASN';
                    }

                    if (key && jabatanData[key]) {
                        jabatanData[key].forEach(function(jab) {
                            const option = document.createElement('option');
                            option.value = jab;
                            option.textContent = jab;
                            // Jika value sama dengan data lama, set selected
                            if (currentVal === jab) {
                                option.selected = true;
                            }
                            selectJabatan.appendChild(option);
                        });
                    }
                }
            }

            // Logic Pangkat Otomatis
            function updatePangkat() {
                const selectedOption = golonganSelect.options[golonganSelect.selectedIndex];
                const pangkatText = selectedOption.getAttribute('data-pangkat');
                pangkatInput.value = pangkatText || '';
            }

            // Event Listeners
            selectProfesi.addEventListener('change', updateKepegawaianLogic);
            selectStatus.addEventListener('change', updateKepegawaianLogic);
            golonganSelect.addEventListener('change', updatePangkat);

            // Run on Load (untuk mengisi data saat mode Edit)
            updateKepegawaianLogic();
            if(!pangkatInput.value) { // Update pangkat on load only if empty
                updatePangkat();
            }
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">

    <style>
        /* Card Styling */
        .dash-card {
            background: #fff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* Section Header Styling */
        .section-header {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }

        .section-header i {
            color: #3b82f6;
            /* Warna Primary Bootstrap */
        }

        /* Form Control Styling */
        .form-label {
            font-weight: 600;
            color: #475569;
            font-size: 0.85rem;
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 0.6rem 0.8rem;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        textarea.form-control {
            resize: vertical;
        }

        /* Avatar Preview Styling */
        .avatar-preview {
            width: 100px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 3px solid #fff;
        }

        .avatar-placeholder {
            width: 100px;
            height: 120px;
            background-color: #e2e8f0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            border: 3px solid #fff;
        }
    </style>
@endpush