@extends('layouts.auth', [
    'title' => 'Registrasi Perawat – DIKSERA',
    'fullWidth' => true
])

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<div class="text-center mb-3">
    <div class="logo-big mx-auto">
        <img src="{{ asset('icon.png') }}" alt="DIKSERA">
    </div>
    <div class="auth-title">Registrasi Perawat</div>
    <div class="auth-subtitle">
        Lengkapi akun dan Daftar Riwayat Hidup (DRH) sesuai format.
    </div>
    <div style="font-size:11px;margin-top:4px;">
        <a href="{{ route('landing') }}">← Kembali ke halaman utama</a>
    </div>
</div>

{{-- ALERT ERROR VALIDASI SERVER --}}
@if ($errors->any())
    <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:12px;">
        <strong>Terjadi kesalahan pada isian Anda:</strong>
        <ul class="mb-0 mt-1" style="padding-left:18px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST"
      action="{{ route('register.perawat.process') }}"
      id="registerForm"
      enctype="multipart/form-data">
    @csrf

    {{-- STEP INDICATOR --}}
    <div class="mb-3">
        <div class="step-dots-wrapper d-flex justify-content-between flex-wrap">
            <div class="step-dot step-dot-active" data-step="1">
                <span class="step-dot-index">1</span>
                <span class="step-dot-label">Akun &amp; Identitas</span>
            </div>
            <div class="step-dot" data-step="2">
                <span class="step-dot-index">2</span>
                <span class="step-dot-label">Alamat &amp; Badan</span>
            </div>
            <div class="step-dot" data-step="3">
                <span class="step-dot-index">3</span>
                <span class="step-dot-label">Pendidikan &amp; Kerja</span>
            </div>
            <div class="step-dot" data-step="4">
                <span class="step-dot-index">4</span>
                <span class="step-dot-label">Keluarga &amp; Organisasi</span>
            </div>
        </div>
    </div>

    {{-- STEP 1 – I. KETERANGAN PERORANGAN (IDENTITAS + JABATAN) --}}
<div class="step-pane" data-step="1">
    <div class="step-title">I. Keterangan Perorangan – Akun & Identitas</div>
    <div class="row g-3">
        <div class="col-lg-7">
            {{-- Nama --}}
            <div class="mb-2">
                <label class="form-label small text-muted mb-1">Nama Lengkap (Tanpa Gelar) *</label>
                <input type="text" name="name" value="{{ old('name') }}" data-required="true" class="form-control form-control-sm form-control-light @error('name') is-invalid @enderror">
            </div>

            {{-- Email --}}
            <div class="mb-2">
                <label class="form-label small text-muted mb-1">E-mail *</label>
                <input type="email" name="email" value="{{ old('email') }}" data-required="true" class="form-control form-control-sm form-control-light @error('email') is-invalid @enderror">
            </div>

            {{-- Password --}}
            <div class="mb-2">
                <label class="form-label small text-muted mb-1">Password *</label>
                <div class="password-wrapper">
                    <input id="password_reg" type="password" name="password" data-required="true" class="form-control form-control-sm form-control-light @error('password') is-invalid @enderror">
                    <button type="button" class="toggle-password-btn" data-target="password_reg">Lihat</button>
                </div>
                <small class="text-muted" style="font-size:11px;">Min. 6 karakter.</small>
            </div>

            <div class="mb-2">
                <label class="form-label small text-muted mb-1">Konfirmasi Password *</label>
                <div class="password-wrapper">
                    <input id="password_reg_confirmation" type="password" name="password_confirmation" data-required="true" class="form-control form-control-sm form-control-light">
                    <button type="button" class="toggle-password-btn" data-target="password_reg_confirmation">Lihat</button>
                </div>
            </div>

             {{-- Hobby --}}
             <div class="mb-2">
                <label class="form-label small text-muted mb-1">Kegemaran / Hobby</label>
                <input type="text" name="hobby" value="{{ old('hobby') }}" class="form-control form-control-sm form-control-light">
            </div>
        </div>

        <div class="col-lg-5">
            {{-- NIK --}}
            <div class="mb-2">
                <label class="form-label small text-muted mb-1">NIK (KTP) *</label>
                <input type="text" name="nik" value="{{ old('nik') }}" data-required="true" class="form-control form-control-sm form-control-light @error('nik') is-invalid @enderror">
            </div>

            {{-- LOGIKA BARU: PROFESI & STATUS --}}
            <div class="mb-2 row g-2">
                <div class="col-6">
                    <label class="form-label small text-muted mb-1">Profesi *</label>
                    <select name="profesi" id="select_profesi" data-required="true" class="form-select form-select-sm form-control-light @error('profesi') is-invalid @enderror">
                        <option value="">-- Pilih --</option>
                        <option value="Perawat" {{ old('profesi')=='Perawat'?'selected':'' }}>Perawat</option>
                        <option value="Bidan" {{ old('profesi')=='Bidan'?'selected':'' }}>Bidan</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label small text-muted mb-1">Status Kepegawaian *</label>
                    <select name="status_kepegawaian" id="select_status" data-required="true" class="form-select form-select-sm form-control-light @error('status_kepegawaian') is-invalid @enderror">
                        <option value="">-- Pilih --</option>
                        <option value="ASN - PNS" {{ old('status_kepegawaian')=='ASN - PNS'?'selected':'' }}>ASN - PNS</option>
                        <option value="ASN - PPPK" {{ old('status_kepegawaian')=='ASN - PPPK'?'selected':'' }}>ASN - PPPK</option>
                        <option value="ASN - PPPK PARUH WAKTU" {{ old('status_kepegawaian')=='ASN - PPPK PARUH WAKTU'?'selected':'' }}>ASN - PPPK PARUH WAKTU</option>
                        <option value="NON ASN - KARYAWAN TETAP" {{ old('status_kepegawaian')=='NON ASN - KARYAWAN TETAP'?'selected':'' }}>NON ASN - KARYAWAN TETAP</option>
                        <option value="NON ASN - KONTRAK" {{ old('status_kepegawaian')=='NON ASN - KONTRAK'?'selected':'' }}>NON ASN - KONTRAK</option>
                    </select>
                </div>
            </div>

            {{-- LOGIKA BARU: NIP / NIRP (Muncul salah satu) --}}
         <div class="mb-2">
    {{-- Input NIP (Tanpa Spasi) --}}
    <div id="wrapper_nip" class="d-none">
        <label class="form-label small text-muted mb-1">NIP * (Tanpa Spasi)</label>
        <input type="text" 
               name="nip" 
               id="input_nip" 
               value="{{ old('nip') }}" 
               class="form-control form-control-sm form-control-light @error('nip') is-invalid @enderror"
               oninput="this.value = this.value.replace(/\s/g, '')"> {{-- Hapus spasi otomatis --}}
    </div>

    {{-- Input NIRP (Tanpa Spasi) --}}
    <div id="wrapper_nirp" class="d-none">
        <label class="form-label small text-muted mb-1">NIRP * (Tanpa Spasi)</label>
        <input type="text" 
               name="nirp" 
               id="input_nirp" 
               value="{{ old('nirp') }}" 
               class="form-control form-control-sm form-control-light @error('nirp') is-invalid @enderror"
               oninput="this.value = this.value.replace(/\s/g, '')"> {{-- Hapus spasi otomatis --}}
    </div>
</div>

            {{-- LOGIKA BARU: JABATAN OTOMATIS --}}
            <div class="mb-2">
                <label class="form-label small text-muted mb-1">Jabatan (Otomatis) *</label>
                <select name="jabatan" id="select_jabatan" data-required="true" class="form-select form-select-sm form-control-light @error('jabatan') is-invalid @enderror">
                    <option value="">-- Pilih Profesi & Status Dulu --</option>
                    {{-- Options diisi via Javascript --}}
                </select>
                {{-- Hidden input untuk menyimpan old value jabatan agar tidak hilang saat error --}}
                <input type="hidden" id="old_jabatan" value="{{ old('jabatan') }}">
            </div>

            {{-- Pangkat & Golongan (Auto Fill) --}}
            <div class="mb-2 row g-2">
                <div class="col-6">
                    <label class="form-label small text-muted mb-1">Golongan</label>
                    <select name="golongan" id="golongan_select" class="form-select form-select-sm form-control-light">
                        <option value="" data-pangkat="">-- Pilih --</option>
                        <option value="None" data-pangkat="None" {{ old('golongan')=='None'?'selected':'' }}>None</option>
                        @php
                            $golonganMap = [
                                'IIa' => 'Pengatur Muda', 'IIb' => 'Pengatur Muda Tingkat I', 'IIc' => 'Pengatur', 'IId' => 'Pengatur Tingkat I',
                                'IIIa' => 'Penata Muda', 'IIIb' => 'Penata Muda Tingkat I', 'IIIc' => 'Penata', 'IIId' => 'Penata Tingkat I',
                                'IVa' => 'Pembina', 'IVb' => 'Pembina Tingkat I', 'IVc' => 'Pembina Utama Muda', 'IVd' => 'Pembina Utama Madya', 'IVe' => 'Pembina Utama',
                                // Tambahan untuk PPPK (Contoh IX, X, dll jika diperlukan, sesuaikan)
                                'VII' => 'PPPK Gol VII', 'IX' => 'PPPK Gol IX', 'X' => 'PPPK Gol X'
                            ];
                        @endphp
                        @foreach($golonganMap as $gol => $pang)
                            <option value="{{ $gol }}" data-pangkat="{{ $pang }}" {{ old('golongan')==$gol?'selected':'' }}>{{ $gol }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label small text-muted mb-1">Pangkat</label>
                    <input type="text" name="pangkat" id="pangkat_input" value="{{ old('pangkat') }}" readonly placeholder="Otomatis" class="form-control form-control-sm form-control-light bg-light">
                </div>
            </div>

            {{-- TTL & Lainnya --}}
            <div class="mb-2 row g-2">
<div class="col-12">
    <label class="form-label small text-muted mb-1">Kabupaten Lahir</label>
    <select name="tempat_lahir" id="kabupaten_lahir" class="form-control form-control-sm">
        <option value="">Cari Kabupaten/Kota...</option>
    </select>
</div>
                <div class="col-12">
                    <label class="form-label small text-muted mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="form-control form-control-sm form-control-light">
                </div>
            </div>

            <div class="mb-2 row g-2">
                <div class="col-6">
                    <label class="form-label small text-muted mb-1">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select form-select-sm form-control-light">
                        <option value="">-- Pilih --</option>
                        <option value="L" {{ old('jenis_kelamin')=='L'?'selected':'' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin')=='P'?'selected':'' }}>Perempuan</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label small text-muted mb-1">Agama</label>
                    <select name="agama" class="form-select form-select-sm form-control-light">
                        <option value="">-- Pilih --</option>
                        @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu','Lainnya'] as $ag)
                            <option value="{{ $ag }}" {{ old('agama')==$ag?'selected':'' }}>{{ $ag }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-2">
                <label class="form-label small text-muted mb-1">Status Perkawinan</label>
                <select name="status_perkawinan" class="form-select form-select-sm form-control-light">
                    <option value="">-- Pilih --</option>
                    @foreach(['Belum Menikah','Menikah','Cerai Hidup','Cerai Mati'] as $st)
                        <option value="{{ $st }}" {{ old('status_perkawinan')==$st?'selected':'' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

    {{-- STEP 2 – ALAMAT, BADAN, FOTO --}}
    <div class="step-pane d-none" data-step="2">
        <div class="step-title">I. Keterangan Perorangan – Alamat &amp; Keterangan Badan</div>
        <div class="row g-3">
             <div class="col-lg-7">
                {{-- 9. No HP --}}
                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Nomor Handphone (Aktif WA) *</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" data-required="true"
                           class="form-control form-control-sm form-control-light @error('no_hp') is-invalid @enderror">
                </div>
                {{-- Alamat --}}
          {{-- Alamat --}}
<div class="mb-2">
    <label class="form-label small text-muted mb-1">Alamat – Jalan (Detail RT/RW) *</label>
    <input type="text" name="alamat_jalan" value="{{ old('alamat_jalan') }}" data-required="true"
           class="form-control form-control-sm form-control-light @error('alamat_jalan') is-invalid @enderror" 
           placeholder="Jl. Mawar No. 10, RT 01 RW 02">
</div>

{{-- DROPDOWN WILAYAH BERJENJANG --}}
<div class="mb-2 row g-2">
    <div class="col-6">
        <label class="form-label small text-muted mb-1">Provinsi *</label>
        {{-- Select untuk UI --}}
        <select id="select_provinsi" class="form-select form-select-sm select2-wilayah">
            <option value="">Pilih Provinsi</option>
        </select>
        {{-- Input Hidden untuk dikirim ke Controller (Menyimpan Nama) --}}
        <input type="hidden" name="alamat_provinsi" id="val_provinsi" value="{{ old('alamat_provinsi') }}">
    </div>
    <div class="col-6">
        <label class="form-label small text-muted mb-1">Kabupaten / Kota *</label>
        <select id="select_kota" class="form-select form-select-sm select2-wilayah" disabled>
            <option value="">Pilih Kota/Kab</option>
        </select>
        <input type="hidden" name="alamat_kabkota" id="val_kota" value="{{ old('alamat_kabkota') }}">
    </div>
</div>

<div class="mb-2 row g-2">
    <div class="col-6">
        <label class="form-label small text-muted mb-1">Kecamatan *</label>
        <select id="select_kecamatan" class="form-select form-select-sm select2-wilayah" disabled>
            <option value="">Pilih Kecamatan</option>
        </select>
        <input type="hidden" name="alamat_kecamatan" id="val_kecamatan" value="{{ old('alamat_kecamatan') }}">
    </div>
    <div class="col-6">
        <label class="form-label small text-muted mb-1">Kelurahan / Desa *</label>
        <select id="select_kelurahan" class="form-select form-select-sm select2-wilayah" disabled>
            <option value="">Pilih Kelurahan</option>
        </select>
        <input type="hidden" name="alamat_kelurahan" id="val_kelurahan" value="{{ old('alamat_kelurahan') }}">
    </div>
</div>
            </div>
            <div class="col-lg-5">
                 {{-- Badan --}}
                <div class="mb-2 row g-2">
                    <div class="col-4"><label class="form-label small text-muted mb-1">Tinggi (cm)</label><input type="number" name="tinggi_badan" value="{{ old('tinggi_badan') }}" class="form-control form-control-sm form-control-light"></div>
                    <div class="col-4"><label class="form-label small text-muted mb-1">Berat</label><input type="number" name="berat_badan" value="{{ old('berat_badan') }}" class="form-control form-control-sm form-control-light"></div>
                    <div class="col-4">
                        <label class="form-label small text-muted mb-1">Gol. Darah</label>
                        <select name="golongan_darah" class="form-select form-select-sm form-control-light">
                            <option value="">--</option>
                            @foreach(['A','B','AB','O','Tidak Tahu'] as $gd) <option value="{{ $gd }}" {{ old('golongan_darah')==$gd?'selected':'' }}>{{ $gd }}</option> @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-2 row g-2">
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">Rambut</label>
                        <select name="rambut" class="form-select form-select-sm form-control-light">
                            <option value="">--</option>
                            @foreach(['Pendek','Sedang','Panjang','Beruban','Lainnya'] as $rb) <option value="{{ $rb }}" {{ old('rambut')==$rb?'selected':'' }}>{{ $rb }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">Bentuk Muka</label>
                        <select name="bentuk_muka" class="form-select form-select-sm form-control-light">
                            <option value="">--</option>
                            @foreach(['Oval','Bulat','Persegi','Hati','Lainnya'] as $mk) <option value="{{ $mk }}" {{ old('bentuk_muka')==$mk?'selected':'' }}>{{ $mk }}</option> @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-2 row g-2">
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">Warna Kulit</label>
                        <select name="warna_kulit" class="form-select form-select-sm form-control-light">
                             <option value="">--</option>
                             @foreach(['Putih','Kuning Langsat','Sawo Matang','Gelap','Lainnya'] as $kl) <option value="{{ $kl }}" {{ old('warna_kulit')==$kl?'selected':'' }}>{{ $kl }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-6"><label class="form-label small text-muted mb-1">Ciri Khas</label><input type="text" name="ciri_khas" value="{{ old('ciri_khas') }}" class="form-control form-control-sm form-control-light"></div>
                </div>
                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Cacat Tubuh</label>
                    <select name="cacat_tubuh" class="form-select form-select-sm form-control-light">
                        <option value="">--</option>
                        @foreach(['Tidak Ada','Ada'] as $ct) <option value="{{ $ct }}" {{ old('cacat_tubuh')==$ct?'selected':'' }}>{{ $ct }}</option> @endforeach
                    </select>
                </div>
                {{-- Foto 3x4 --}}
                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Foto 3x4 (jpg/png) *</label>
                    <div>
                        <input type="file" name="foto_3x4" class="form-control form-control-sm" accept="image/*" data-show-filename="true" data-show-preview="true"data-required="true">
                        <small class="text-muted file-name" style="font-size:10px;"></small>
                        <div class="mt-2">
                            <img class="img-preview" src="#" alt="Preview" style="max-height:130px;border-radius:8px;display:none;border:1px solid #e5e7eb;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   {{-- STEP 3 – II & III: Pendidikan / Pelatihan / Riwayat Pekerjaan --}}
<div class="step-pane d-none" data-step="3">
    <div class="step-title">II. Pendidikan &amp; Pelatihan · III. Riwayat Pekerjaan</div>

    {{-- Pendidikan (II.1) --}}
    <div class="sub-section-head">1. Pendidikan di Dalam dan Luar Negeri</div>
    <div id="pendidikan-wrapper" class="mb-2">
        <div class="row g-1 mb-1 pendidikan-row dynamic-row">
            {{-- UBAH: Dropdown Jenjang / Tingkat --}}
            <div class="col-6 col-md-2">
                <select name="pendidikan_jenjang[]" class="form-select form-select-sm form-control-light">
                    <option value="" selected disabled>Pilih Tingkat</option>
                    <option value="SD">SD</option>
                    <option value="SMP">SMP</option>
                    <option value="SMA/SMK">SMA/SMK</option>
                    <option value="D1">D1</option>
                    <option value="D2">D2</option>
                    <option value="D3">D3</option>
                    <option value="D4">D4</option>
                    <option value="S1">S1</option>
                    <option value="S2">S2</option>
                    <option value="S3">S3</option>
                    <option value="Profesi">Profesi</option>
                </select>
            </div>

            <div class="col-6 col-md-3">
                <input type="text" name="pendidikan_nama[]"
                       class="form-control form-control-sm form-control-light"
                       placeholder="Nama Sekolah / PT">
            </div>

            {{-- UBAH: Dropdown Akreditasi --}}
            <div class="col-6 col-md-2">
                <select name="pendidikan_akreditasi[]" class="form-select form-select-sm form-control-light">
                    <option value="" selected disabled>Akreditasi</option>
                    <option value="Unggul">Unggul</option>
                    <option value="A">A</option>
                    <option value="Baik Sekali">Baik Sekali</option>
                    <option value="B">B</option>
                    <option value="Baik">Baik</option>
                    <option value="C">C</option>
                    <option value="Tidak Terakreditasi">Tidak Terakreditasi</option>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <input type="text" name="pendidikan_tempat[]"
                       class="form-control form-control-sm form-control-light"
                       placeholder="Tempat">
            </div>

            <div class="col-12 col-md-3 d-flex flex-column gap-1">
                <input type="file"
                       name="pendidikan_file[]"
                       class="form-control form-control-sm form-control-light"
                       accept=".pdf,image/*"
                       data-show-filename="true"
                       data-required="true"
                       data-show-preview="true">
                <small class="text-muted file-name" style="font-size:10px;">STTB/Ijazah (pdf/jpg)</small>
                <img class="img-preview"
                     src="#"
                     alt="Preview"
                     style="max-height:60px;border-radius:6px;display:none;border:1px solid #e5e7eb;">
                <button type="button"
                        class="btn btn-link text-danger p-0 align-self-start remove-row-btn"
                        style="font-size:11px;">Hapus</button>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-sm btn-outline-primary mb-3" id="add-pendidikan">+ Tambah Pendidikan</button>

        {{-- II.2 Pelatihan --}}
        <div class="sub-section-head mt-2">2. Pelatihan</div>
        <div id="pelatihan-wrapper" class="mb-2">
            <div class="row g-1 mb-1 pelatihan-row dynamic-row">
                <div class="col-12 col-md-3"><input type="text" name="pelatihan_nama[]" class="form-control form-control-sm form-control-light" placeholder="Nama Pelatihan"></div>
                <div class="col-6 col-md-2"><input type="text" name="pelatihan_durasi[]" class="form-control form-control-sm form-control-light" placeholder="Lama"></div>
                <div class="col-6 col-md-2"><input type="date" name="pelatihan_mulai[]" class="form-control form-control-sm form-control-light"></div>
                <div class="col-6 col-md-2"><input type="date" name="pelatihan_selesai[]" class="form-control form-control-sm form-control-light"></div>
                <div class="col-6 col-md-3 d-flex flex-column gap-1">
                    <input type="file" name="pelatihan_file[]" class="form-control form-control-sm form-control-light" accept=".pdf,image/*" data-show-filename="true" data-show-preview="true" data-required="true">
                    <small class="text-muted file-name" style="font-size:10px;">Sertifikat</small>
                    <img class="img-preview" src="#" style="max-height:60px;display:none;">
                    <button type="button" class="btn btn-link text-danger p-0 align-self-start remove-row-btn" style="font-size:11px;">Hapus</button>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary mb-3" id="add-pelatihan">+ Tambah Pelatihan</button>

        {{-- III. Riwayat Pekerjaan --}}
        <div class="sub-section-head mt-2">III. Riwayat Pekerjaan</div>
        <div id="pekerjaan-wrapper" class="mb-2">
            <div class="row g-1 mb-1 pekerjaan-row dynamic-row">
                <div class="col-12 col-md-3"><input type="text" name="pekerjaan_instansi[]" class="form-control form-control-sm form-control-light" placeholder="Instansi"></div>
                <div class="col-12 col-md-3"><input type="text" name="pekerjaan_jabatan[]" class="form-control form-control-sm form-control-light" placeholder="Jabatan"></div>
                <div class="col-6 col-md-2"><input type="date" name="pekerjaan_mulai[]" class="form-control form-control-sm form-control-light"></div>
                <div class="col-6 col-md-2"><input type="date" name="pekerjaan_selesai[]" class="form-control form-control-sm form-control-light"></div>
                <div class="col-12 col-md-2 d-flex flex-column gap-1">
                    <input type="file" name="pekerjaan_file[]" class="form-control form-control-sm form-control-light" accept=".pdf,image/*" data-show-filename="true" data-show-preview="true" data-required="true">
                    <small class="text-muted file-name" style="font-size:10px;">SK</small>
                    <img class="img-preview" src="#" style="max-height:60px;display:none;">
                    <button type="button" class="btn btn-link text-danger p-0 align-self-start remove-row-btn" style="font-size:11px;">Hapus</button>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" id="add-pekerjaan">+ Tambah Pekerjaan</button>
    </div>

    {{-- STEP 4 – IV, V, VI --}}
    <div class="step-pane d-none" data-step="4">
        <div class="step-title">IV. Tanda Jasa · V. Keluarga · VI. Organisasi</div>

        {{-- IV. Tanda Jasa --}}
        <div class="sub-section-head">IV. Tanda Jasa / Penghargaan</div>
        <div id="tandajasa-wrapper" class="mb-2">
            <div class="row g-1 mb-1 tandajasa-row dynamic-row">
                <div class="col-12 col-md-3"><input type="text" name="tandajasa_nama[]" class="form-control form-control-sm form-control-light" placeholder="Nama Penghargaan"></div>
                <div class="col-12 col-md-3"><input type="text" name="tandajasa_instansi[]" class="form-control form-control-sm form-control-light" placeholder="Pemberi"></div>
                <div class="col-6 col-md-2"><input type="text" name="tandajasa_tahun[]" class="form-control form-control-sm form-control-light" placeholder="Tahun"></div>
                <div class="col-6 col-md-4 d-flex flex-column gap-1">
                     <input type="file" name="tandajasa_file[]" class="form-control form-control-sm form-control-light" accept=".pdf,image/*" data-show-filename="true" data-show-preview="true">
                     <small class="text-muted file-name" style="font-size:10px;">SK/Piagam</small>
                     <img class="img-preview" src="#" style="max-height:60px;display:none;">
                     <button type="button" class="btn btn-link text-danger p-0 align-self-start remove-row-btn" style="font-size:11px;">Hapus</button>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary mb-3" id="add-tandajasa">+ Tambah Tanda Jasa</button>

        {{-- V. Keluarga --}}
        <div class="sub-section-head mt-2">V. Riwayat Keluarga</div>
        <div id="keluarga-wrapper" class="mb-2">
            <div class="row g-1 mb-1 keluarga-row dynamic-row">
                <div class="col-12 col-md-2">
            <select name="keluarga_hubungan[]" class="form-select form-select-sm form-control-light">
                <option value="" selected disabled>Hubungan</option>
                <option value="Suami">Suami</option>
                <option value="Istri">Istri</option>
                <option value="Anak">Anak</option>
                <option value="Orang Tua">Orang Tua</option>
                <option value="Saudara Kandung">Saudara Kandung</option>
                 <option value="Lainnya">Lainnya</option>
            </select>
        </div>
                <div class="col-12 col-md-3"><input type="text" name="keluarga_nama[]" class="form-control form-control-sm form-control-light" placeholder="Nama"></div>
                <div class="col-12 col-md-3"><input type="text" name="keluarga_ttl[]" class="form-control form-control-sm form-control-light" placeholder="TTL"></div>
                <div class="col-12 col-md-4 d-flex align-items-start gap-1">
                    <input type="text" name="keluarga_pekerjaan[]" class="form-control form-control-sm form-control-light" placeholder="Pekerjaan">
                    <button type="button" class="btn btn-link text-danger p-0 remove-row-btn" style="font-size:11px;">Hapus</button>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary mb-3" id="add-keluarga">+ Tambah Keluarga</button>

        {{-- VI. Organisasi --}}
        <div class="sub-section-head mt-2">VI. Organisasi</div>
        <div id="organisasi-wrapper" class="mb-2">
            <div class="row g-1 mb-1 organisasi-row dynamic-row">
                 <div class="col-12 col-md-3"><input type="text" name="organisasi_nama[]" class="form-control form-control-sm form-control-light" placeholder="Organisasi"></div>
                 <div class="col-12 col-md-3"><input type="text" name="organisasi_jabatan[]" class="form-control form-control-sm form-control-light" placeholder="Jabatan"></div>
                 <div class="col-6 col-md-2"><input type="date" name="organisasi_mulai[]" class="form-control form-control-sm form-control-light"></div>
                 <div class="col-6 col-md-2"><input type="date" name="organisasi_selesai[]" class="form-control form-control-sm form-control-light"></div>
                 <div class="col-12 col-md-2 d-flex flex-column gap-1">
                     <input type="file" name="organisasi_file[]" class="form-control form-control-sm form-control-light" accept=".pdf,image/*" data-show-filename="true" data-show-preview="true">
                     <small class="text-muted file-name" style="font-size:10px;">Kartu/SK</small>
                     <img class="img-preview" src="#" style="max-height:60px;display:none;">
                     <button type="button" class="btn btn-link text-danger p-0 align-self-start remove-row-btn" style="font-size:11px;">Hapus</button>
                 </div>
            </div>
        </div>
        <p class="small text-muted mt-1">
            Pastikan data sudah terisi dengan benar sebelum menekan tombol <strong>Daftar</strong>.
        </p>
    </div>

    {{-- STEP NAV BUTTONS --}}
    <div class="d-flex justify-content-between mt-3">
        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnPrev">Sebelumnya</button>
        <div>
            <button type="button" class="btn btn-sm btn-solid-blue" id="btnNext">Berikutnya</button>
            <button type="submit" class="btn btn-sm btn-solid-blue d-none" id="btnSubmit">Daftar</button>
        </div>
    </div>
</form>
@endsection

@push('styles')
<style>
    /* --- PERBAIKAN SCROLLING HALAMAN --- */
    html, body {
        height: auto !important;
        min-height: 100% !important;
        overflow-y: auto !important;
    }
    /* Asumsi template utama Anda memakai ID/Class ini untuk wrapper */
    .auth-page, .authentication-bg, #app, .main-wrapper {
        height: auto !important;
        min-height: 100vh;
        overflow: visible !important;
    }

    /* CSS Step Pane & Responsiveness */
    .step-pane {
        position: relative;
        border-radius: 18px;
        background: linear-gradient(145deg,#ffffff,#f3f6ff);
        border: 1px solid rgba(209,213,219,0.9);
        padding: 16px 18px;
        box-shadow: 0 10px 24px rgba(15,23,42,0.07);
        margin-bottom: 4px;
        /* Scroll Overflow Visible */
        overflow: visible !important;
    }
    
    .step-dots-wrapper{
        font-size:11px;
        gap:6px;
        background: linear-gradient(135deg,#eef2ff,#e0ecff);
        border-radius: 999px;
        padding: 6px 6px;
        border: 1px solid rgba(148,163,184,0.6);
        box-shadow: 0 8px 18px rgba(15,23,42,0.06);
    }
    .step-dot {
        flex: 1 1 0;
        min-width: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 6px 4px;
        border-radius: 999px;
        background: rgba(255,255,255,0.85);
        color: #4b5563;
        font-weight: 500;
        border: 1px solid rgba(209,213,219,0.9);
        transition: background 0.2s ease, box-shadow 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        white-space: nowrap;
    }
    .step-dot-index{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        width:18px;
        height:18px;
        border-radius:999px;
        background:#e5edff;
        font-size:11px;
        font-weight:600;
        color:#1d4ed8;
    }
    .step-dot-label{
        overflow:hidden;
        text-overflow:ellipsis;
    }
    .step-dot-active {
        background: linear-gradient(135deg,#2563eb,#60a5fa);
        color: #f9fafb;
        box-shadow: 0 8px 20px rgba(37,99,235,0.45);
        border-color: transparent;
    }
    .step-dot-active .step-dot-index{
        background:rgba(248,250,252,0.95);
        color:#1d4ed8;
    }

    .step-title {
        position: relative;
        font-size: 13px;
        font-weight: 600;
        color: #1d4ed8;
        margin-bottom: 10px;
        padding-left: 10px;
    }
    .step-title::before{
        content:"";
        position:absolute;
        left:0;
        top:50%;
        transform:translateY(-50%);
        width:4px;
        height:16px;
        border-radius:999px;
        background: linear-gradient(180deg,#2563eb,#38bdf8);
    }

    .sub-section-head {
        font-size: 12px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 4px;
        padding-left: 6px;
        border-left: 3px solid #2563eb;
    }

    /* Mobile Responsive Tweaks */
    @media (max-width: 576px){
        .step-dots-wrapper{
            gap:4px !important;
            padding:4px 4px;
            border-radius:14px;
        }
        .step-dot{
            flex: 1 1 calc(50% - 4px);
            font-size:10px;
            padding:5px 3px;
        }
        .step-dot-label{
            max-width:80px;
        }
        .step-pane{
            padding:10px 10px;
        }
        .auth-card .form-control-sm,
        .auth-card .form-select-sm {
            min-height: 44px;
            font-size: 13px;
        }
    }
    
    .auth-card .form-control-sm,
    .auth-card .form-select-sm {
        min-height: 42px;
        padding: 0.4rem 0.75rem;
        font-size: 13px;
        border-radius: 10px;
    }
    .auth-card .form-label {
        font-size: 12px;
    }
    .auth-card .mb-2 {
        margin-bottom: 0.6rem !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const element = document.getElementById('kabupaten_lahir');
    
    // 1. Inisialisasi Choices.js
    const choices = new Choices(element, {
        searchEnabled: true,
        itemSelectText: '',
        placeholder: true,
        placeholderValue: 'Ketik namas kabupaten...',
        noResultsText: 'Data tidak ditemukan',
        loadingText: 'Memuat data wilayah...',
        shouldSort: true
    });

    // 2. Samakan Source: Ambil dari API Lokal kamu (seperti bagian Alamat)
    const localApi = "{{ url('/api-wilayah-lokal') }}";

    // Kita ambil data provinsi dulu, lalu loop untuk ambil semua kabupaten
    // Ini agar datanya selengkap di bagian Alamat
    async function loadKabupatenTTL() {
        try {
            const resProv = await fetch(`${localApi}/provinces`);
            const provJson = await resProv.json();
            const daftarProvinsi = provJson.data;

            // Ambil semua kabupaten secara paralel
            const promises = daftarProvinsi.map(prov => 
                fetch(`${localApi}/regencies/${prov.code}`).then(r => r.json())
            );

            const hasilRegencies = await Promise.all(promises);

            let listSemuaKabupaten = [];
            hasilRegencies.forEach(res => {
                if(res.data) {
                    res.data.forEach(kab => {
                        listSemuaKabupaten.push({
                            value: kab.name,
                            label: kab.name,
                            selected: kab.name === "{{ old('tempat_lahir') }}"
                        });
                    });
                }
            });

            // Masukkan ke Choices.js
            choices.clearChoices();
            choices.setChoices(listSemuaKabupaten, 'value', 'label', true);

        } catch (error) {
            console.error("Gagal sinkronisasi data wilayah:", error);
            choices.setMessage('Gagal memuat data wilayah lokal.');
        }
    }

    loadKabupatenTTL();
});
document.addEventListener('DOMContentLoaded', function () {
    let currentStep = 1;
    const maxStep = 4;

    const stepDots  = document.querySelectorAll('.step-dot');
    const stepPanes = document.querySelectorAll('.step-pane');
    const btnPrev   = document.getElementById('btnPrev');
    const btnNext   = document.getElementById('btnNext');
    const btnSubmit = document.getElementById('btnSubmit');

    // LOGIKA PANGKAT & GOLONGAN OTOMATIS
    const golonganSelect = document.getElementById('golongan_select');
    const pangkatInput   = document.getElementById('pangkat_input');

    if(golonganSelect && pangkatInput) {
        function updatePangkat() {
            const selectedOption = golonganSelect.options[golonganSelect.selectedIndex];
            const pangkatText = selectedOption.getAttribute('data-pangkat');
            pangkatInput.value = pangkatText || '';
        }
        golonganSelect.addEventListener('change', updatePangkat);
        updatePangkat();
    }

    // --- LOGIKA STEP FORM ---
    function renderSteps() {
        stepPanes.forEach(pane => {
            pane.classList.toggle('d-none', parseInt(pane.dataset.step) !== currentStep);
        });
        stepDots.forEach(dot => {
            dot.classList.toggle('step-dot-active', parseInt(dot.dataset.step) === currentStep);
        });

        if (btnPrev) btnPrev.disabled = currentStep === 1;
        if (btnNext) btnNext.classList.toggle('d-none', currentStep === maxStep);
        if (btnSubmit) btnSubmit.classList.toggle('d-none', currentStep !== maxStep);
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function hasEmptyRequired(step) {
        const pane = document.querySelector('.step-pane[data-step="' + step + '"]');
        if (!pane) return false;

        let empty = false;
        const requiredFields = pane.querySelectorAll('[data-required="true"]');
        requiredFields.forEach(el => {
            const val = (el.value || '').trim();
            if (!val) {
                empty = true;
                el.classList.add('is-invalid');
            } else {
                el.classList.remove('is-invalid');
            }
        });

        if (empty && typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Data belum lengkap',
                text: 'Silakan isi semua data wajib pada langkah ini terlebih dahulu.',
            });
        }
        return empty;
    }

    if (btnPrev) {
        btnPrev.addEventListener('click', function () {
            if (currentStep > 1) {
                currentStep--;
                renderSteps();
            }
        });
    }

    if (btnNext) {
        btnNext.addEventListener('click', function () {
            if (hasEmptyRequired(currentStep)) {
                return;
            }
            if (currentStep < maxStep) {
                currentStep++;
                renderSteps();
            }
        });
    }

    stepDots.forEach(dot => {
        dot.addEventListener('click', function () {
            const targetStep = parseInt(this.dataset.step);
            if (!targetStep) return;
            if (targetStep > currentStep && hasEmptyRequired(currentStep)) {
                return;
            }
            currentStep = targetStep;
            renderSteps();
        });
    });

    document.querySelectorAll('.toggle-password-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            if (!input) return;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            this.textContent = isPassword ? 'Sembunyikan' : 'Lihat';
        });
    });

    // File Preview & Dynamic Rows
    function bindFilePreview(input) {
        const parent = input.parentElement;
        const label = parent ? parent.querySelector('.file-name') : null;
        const imgEl = parent ? parent.querySelector('.img-preview') : null;

        input.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (label && this.dataset.showFilename === 'true') {
                label.textContent = file ? file.name : '';
            }
            if (imgEl && this.dataset.showPreview === 'true') {
                if (file && file.type && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        imgEl.src = e.target.result;
                        imgEl.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    imgEl.src = '#';
                    imgEl.style.display = 'none';
                }
            }
        });
    }

    function initFileInputs(root) {
        (root || document).querySelectorAll('input[type="file"]').forEach(input => {
            bindFilePreview(input);
        });
    }
    initFileInputs(document);

    function cloneRow(wrapperId, rowClass) {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return;
        const row = wrapper.querySelector('.' + rowClass);
        if (!row) return;
        const clone = row.cloneNode(true);
        clone.querySelectorAll('input').forEach(i => {
            if (i.type === 'file') { i.value = null; } else { i.value = ''; }
        });
        clone.querySelectorAll('select').forEach(s => {
        s.selectedIndex = 0; // Kembali ke "Pilih Hubungan"
    });
        clone.querySelectorAll('.file-name').forEach(fn => fn.textContent = '');
        clone.querySelectorAll('.img-preview').forEach(img => {
            img.src = '#'; img.style.display = 'none';
        });
        wrapper.appendChild(clone);
        initFileInputs(clone);
    }

    ['pendidikan','pelatihan','pekerjaan','keluarga','organisasi','tandajasa'].forEach(type => {
        const btn = document.getElementById('add-' + type);
        if(btn) btn.addEventListener('click', () => cloneRow(type + '-wrapper', type + '-row'));
    });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-row-btn');
        if (!btn) return;
        const row = btn.closest('.dynamic-row');
        if (!row) return;
        let wrapper = row.closest('[id$="-wrapper"]');
        if (!wrapper) wrapper = row.parentElement;
        const rows = wrapper.querySelectorAll('.dynamic-row');
        if (rows.length <= 1) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'info', title: 'Tidak dapat dihapus', text: 'Minimal satu baris data harus tersedia.' });
            }
            return;
        }
        row.remove();
    });

    @if($errors->any())
    const errorKeys = @json($errors->keys());
    (function jumpToFirstError() {
        let firstField = null;
        errorKeys.some(function (key) {
            let el = document.querySelector('[name="' + key + '"]');
            if (!el) el = document.querySelector('[name^="' + key + '["]');
            if (el) { firstField = el; return true; }
            return false;
        });
        if (firstField) {
            const pane = firstField.closest('.step-pane');
            if (pane && pane.dataset.step) {
                currentStep = parseInt(pane.dataset.step);
                renderSteps();
            }
            setTimeout(function () {
                firstField.scrollIntoView({behavior: 'smooth', block: 'center'});
                firstField.focus();
                firstField.classList.add('is-invalid');
            }, 200);
        }
    })();
    @endif

    renderSteps();

    // === LOGIKA BARU: PROFESI, STATUS, NIP/NIRP, JABATAN ===
    const selectProfesi = document.getElementById('select_profesi');
    const selectStatus  = document.getElementById('select_status');
    const selectJabatan = document.getElementById('select_jabatan');
    const wrapperNip    = document.getElementById('wrapper_nip');
    const wrapperNirp   = document.getElementById('wrapper_nirp');
    const inputNip      = document.getElementById('input_nip');
    const inputNirp     = document.getElementById('input_nirp');
    const oldJabatanVal = document.getElementById('old_jabatan').value;

    // Data Jabatan
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

    function updateFormLogics() {
        const profesi = selectProfesi.value;
        const status  = selectStatus.value;

        // 1. Logic NIP vs NIRP
        wrapperNip.classList.add('d-none');
        wrapperNirp.classList.add('d-none');
        // Reset required state to prevent validation error on hidden fields logic (optional for HTML5)
        inputNip.removeAttribute('data-required');
        inputNirp.removeAttribute('data-required');

        if (status.includes('ASN') && !status.includes('NON')) {
            // Case ASN (PNS, PPPK, PPPK Paruh Waktu)
            wrapperNip.classList.remove('d-none');
            inputNip.setAttribute('data-required', 'true');
        } else if (status.includes('NON ASN')) {
            // Case NON ASN
            wrapperNirp.classList.remove('d-none');
            inputNirp.setAttribute('data-required', 'true');
        }

        // 2. Logic Jabatan Dropdown
        selectJabatan.innerHTML = '<option value="">-- Pilih Jabatan --</option>';

        if (profesi && status) {
            let key = '';
            // Tentukan Key
            if (profesi === 'Perawat') {
                key = (status.includes('ASN') && !status.includes('NON')) ? 'Perawat_ASN' : 'Perawat_NonASN';
            } else if (profesi === 'Bidan') {
                key = (status.includes('ASN') && !status.includes('NON')) ? 'Bidan_ASN' : 'Bidan_NonASN';
            }

            // Populate Options
            if (key && jabatanData[key]) {
                jabatanData[key].forEach(function(jab) {
                    const option = document.createElement('option');
                    option.value = jab;
                    option.textContent = jab;
                    if (oldJabatanVal === jab) {
                        option.selected = true;
                    }
                    selectJabatan.appendChild(option);
                });
            }
        }
    }

    // Listener
    selectProfesi.addEventListener('change', updateFormLogics);
    selectStatus.addEventListener('change', updateFormLogics);

    // Trigger on load (for edit/validation error state)
    if(selectProfesi.value || selectStatus.value) {
        updateFormLogics();
    }
});

$(document).ready(function() {
    // 1. Inisialisasi Select2 agar bisa search
   // 1. Inisialisasi Select2
    $('.select2-wilayah').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Cari...',
    });

    // Panggil Route Laravel kita sendiri (Tanpa Proxy luar)
    const localApi = "{{ url('/api-wilayah-lokal') }}";
    // ==========================================================
    // LOGIC LOAD PROVINSI
    // ==========================================================
    $.getJSON(`${localApi}/provinces`, function(res) {
        let options = '<option value="">Pilih Provinsi</option>';
        res.data.forEach(function(item) {
            options += `<option value="${item.code}" data-name="${item.name}">${item.name}</option>`;
        });
        $('#select_provinsi').html(options);
        
        // PENTING: Jalankan muat draft setelah provinsi terisi
        if (typeof loadProgress === 'function') loadProgress();
    });

    // ==========================================================
    // EVENT CHANGE PROVINSI -> LOAD KOTA
    // ==========================================================
    $('#select_provinsi').on('change', function() {
        const code = $(this).val();
        $('#val_provinsi').val($(this).find(':selected').data('name') || '');
        
        if(code) {
            $('#select_kota').html('<option value="">Loading...</option>').prop('disabled', true);
            $.getJSON(`${localApi}/regencies/${code}`, function(res) {
                let options = '<option value="">Pilih Kota/Kab</option>';
                res.data.forEach(item => options += `<option value="${item.code}" data-name="${item.name}">${item.name}</option>`);
                $('#select_kota').html(options).prop('disabled', false).trigger('change');
            });
        }
    });

    // ==========================================================
    // EVENT CHANGE KOTA -> LOAD KECAMATAN
    // ==========================================================
    $('#select_kota').on('change', function() {
        const code = $(this).val();
        $('#val_kota').val($(this).find(':selected').data('name') || '');
        
        if(code) {
            $('#select_kecamatan').html('<option value="">Loading...</option>').prop('disabled', true);
            $.getJSON(`${localApi}/districts/${code}`, function(res) {
                let options = '<option value="">Pilih Kecamatan</option>';
                res.data.forEach(item => options += `<option value="${item.code}" data-name="${item.name}">${item.name}</option>`);
                $('#select_kecamatan').html(options).prop('disabled', false).trigger('change');
            });
        }
    });

    // ==========================================================
    // EVENT CHANGE KECAMATAN -> LOAD KELURAHAN
    // ==========================================================
    $('#select_kecamatan').on('change', function() {
        const code = $(this).val();
        $('#val_kecamatan').val($(this).find(':selected').data('name') || '');
        
        if(code) {
            $('#select_kelurahan').html('<option value="">Loading...</option>').prop('disabled', true);
            $.getJSON(`${localApi}/villages/${code}`, function(res) {
                let options = '<option value="">Pilih Kelurahan</option>';
                res.data.forEach(item => options += `<option value="${item.code}" data-name="${item.name}">${item.name}</option>`);
                $('#select_kelurahan').html(options).prop('disabled', false);
            });
        }
    });

    $('#select_kelurahan').on('change', function() {
        $('#val_kelurahan').val($(this).find(':selected').data('name') || '');
    });
    // ==========================================================
    // LOGIKA AUTO-SAVE KE LOCAL STORAGE
    // ==========================================================
    



    const storageKey = 'DRAFT_DIKSERA_AWANG_STEP_SAVE';
    const form = $('#registerForm');

    // ==========================================================
    // 1. FUNGSI SIMPAN (Semua Kolom + Posisi Step)
    // ==========================================================
   function saveProgress() {
    let draft = {};
    const dataArray = form.serializeArray();
    dataArray.forEach(item => {
        // JANGAN SIMPAN TOKEN DAN PASSWORD
        if (!item.name.includes('password') && item.name !== '_token') { 
            if (draft[item.name]) {
                if (!Array.isArray(draft[item.name])) draft[item.name] = [draft[item.name]];
                draft[item.name].push(item.value);
            } else {
                draft[item.name] = item.value;
            }
        }
    });
    draft['_saved_step'] = window.currentStep;
    localStorage.setItem(storageKey, JSON.stringify(draft));
}

function loadProgress() {
    const json = localStorage.getItem(storageKey);
    if (!json) return;
    const data = JSON.parse(json);

    if (data._saved_step) {
        window.currentStep = parseInt(data._saved_step);
        if (typeof renderSteps === 'function') renderSteps();
    }

    Object.keys(data).forEach(key => {
        if (key === '_token') return; // JANGAN OVERWRITE TOKEN ASLI HALAMAN
        const val = data[key];
        const $el = $(`[name="${key}"]`);
        if ($el.length && $el.attr('type') !== 'file') {
            $el.val(val).trigger('change.select2');
        }
    });
}

    // ==========================================================
    // 3. EVENT LISTENERS
    // ==========================================================

    // Simpan tiap ada perubahan ketikan atau pilihan
    $(document).on('input change keyup', '#registerForm input, #registerForm select, #registerForm textarea', function() {
        saveProgress();
    });

    // Simpan khusus saat tombol "Berikutnya" atau "Sebelumnya" diklik
    $('#btnNext, #btnPrev, .step-dot').on('click', function() {
        // Kasih delay dikit biar variabel currentStep update dulu di script navigasi
        setTimeout(saveProgress, 100);
    });

    // Jalankan load saat halaman direfresh
    // Delay 500ms biar script wilayah & step Bapak siap dulu
    setTimeout(loadProgress, 500);

    // Hapus draft kalau sukses submit
    form.on('submit', function() {
        localStorage.removeItem(storageKey);
    });

});
</script>

@endpush