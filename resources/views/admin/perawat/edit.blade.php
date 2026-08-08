@extends('layouts.app')

@section('title', 'Edit Data Perawat – Admin DIKSERA')

@push('styles')
    <style>
        /* Global Card Style */
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-soft);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 24px;
        }

        /* Profile Image Box */
        .profile-img-box {
            width: 140px;
            height: 180px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border-soft);
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .profile-img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-avatar-fallback {
            font-size: 48px;
            font-weight: 600;
            color: var(--blue-main);
            background: var(--blue-soft);
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Section Headers */
        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--blue-main);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title::before {
            content: '';
            display: block;
            width: 4px;
            height: 16px;
            background: var(--blue-main);
            border-radius: 4px;
        }

        /* Form Styles */
        .form-label-group {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            font-weight: 700;
            margin-bottom: 12px;
            border-bottom: 1px solid var(--blue-soft-2);
            padding-bottom: 4px;
            display: block;
        }

        .form-control,
        .form-select {
            font-size: 13px;
            padding: 8px 12px;
            border-radius: 8px;
        }

        .btn-dashed {
            border: 1px dashed var(--blue-main);
            color: var(--blue-main);
            background: #f8fafc;
            border-radius: 8px;
            font-size: 13px;
            transition: all 0.2s;
        }

        .btn-dashed:hover {
            background: var(--blue-soft);
            border-color: var(--blue-main);
        }
    </style>
@endpush

@section('content')

    <form action="{{ route('admin.perawat.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Top Bar: Navigasi & Save --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.perawat.index') }}" class="btn btn-sm btn-outline-secondary px-3"
                style="border-radius: 8px; font-size: 12px;">
                <i class="bi bi-arrow-left"></i> Batal & Kembali
            </a>
            <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 8px; font-size: 13px;">
                <i class="bi bi-save me-1"></i> Simpan Perubahan
            </button>
        </div>

        {{-- 1. IDENTITAS UTAMA (FOTO & AKUN) --}}
        <div class="content-card mb-4">
            <div class="row g-4">
                {{-- Kolom Foto --}}
                <div class="col-md-auto" style="min-width: 180px;">
                    <div class="profile-img-box mb-3">
                        @if ($profile && $profile->foto_3x4)
                            <img src="{{ asset('storage/' . $profile->foto_3x4) }}" alt="Foto" id="preview-foto">
                        @else
                            <div class="profile-avatar-fallback" id="preview-fallback">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            {{-- Image hidden untuk preview JS --}}
                            <img src="" alt="Preview" id="preview-foto" class="d-none"
                                style="width:100%; height:100%; object-fit:cover;">
                        @endif
                    </div>

                    <input type="file" class="d-none" id="foto_3x4" name="foto_3x4" onchange="previewImage(this)">
                    <label for="foto_3x4"
                        class="btn btn-sm btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-camera"></i> Upload Foto
                    </label>
                    <div class="text-muted text-center mt-2" style="font-size: 10px;">
                        Format: JPG/PNG, Max 2MB.<br>Rasio 3:4 disarankan.
                    </div>
                </div>

                {{-- Kolom Data Utama --}}
                <div class="col-md">
                    <div class="row g-3">
                        <div class="col-12">
                            <span class="form-label-group">Informasi Akun Login</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Nama Lengkap</label>
                            <input type="text" class="form-control fw-bold" id="name" name="name"
                                value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Alamat Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-12">
                            <span class="form-label-group">Data Pribadi & Kontak</span>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small text-muted">NIK (KTP)</label>
                            <input type="text" class="form-control font-monospace" id="nik" name="nik"
                                value="{{ old('nik', $profile->nik ?? '') }}" placeholder="16 digit NIK">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Tempat Lahir</label>
                            <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir"
                                value="{{ old('tempat_lahir', $profile->tempat_lahir ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir"
                                value="{{ old('tanggal_lahir', $profile->tanggal_lahir ?? '') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small text-muted">Jenis Kelamin</label>
                            <select class="form-select" id="jenis_kelamin" name="jenis_kelamin">
                                <option value="">- Pilih -</option>
                                <option value="Laki-laki"
                                    {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="Perempuan"
                                    {{ old('jenis_kelamin', $profile->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Agama</label>
                            <select class="form-select" id="agama" name="agama">
                                <option value="">- Pilih -</option>
                                @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agm)
                                    <option value="{{ $agm }}"
                                        {{ old('agama', $profile->agama ?? '') == $agm ? 'selected' : '' }}>
                                        {{ $agm }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">No. HP / WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-success"><i
                                        class="bi bi-whatsapp"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0" id="no_hp"
                                    name="no_hp" value="{{ old('no_hp', $profile->no_hp ?? '') }}">
                            </div>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label small text-muted">Alamat Domisili</label>
                            <input type="text" class="form-control" id="alamat" name="alamat"
                                value="{{ old('alamat', $profile->alamat ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <div class="row g-2">
                                <div class="col-4">
                                    <label class="form-label small text-muted">TB (cm)</label>
                                    <input type="number" class="form-control px-2 text-center" name="tinggi_badan"
                                        value="{{ old('tinggi_badan', $profile->tinggi_badan ?? '') }}">
                                </div>
                                <div class="col-4">
                                    <label class="form-label small text-muted">BB (kg)</label>
                                    <input type="number" class="form-control px-2 text-center" name="berat_badan"
                                        value="{{ old('berat_badan', $profile->berat_badan ?? '') }}">
                                </div>
                                <div class="col-4">
                                    <label class="form-label small text-muted">Gol.Dar</label>
                                    <select class="form-select px-1 text-center" name="golongan_darah">
                                        <option value="">-</option>
                                        @foreach (['A', 'B', 'AB', 'O'] as $goldar)
                                            <option value="{{ $goldar }}"
                                                {{ old('golongan_darah', $profile->golongan_darah ?? '') == $goldar ? 'selected' : '' }}>
                                                {{ $goldar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- 2. DYNAMIC FORM SECTIONS --}}

        {{-- PENDIDIKAN --}}
        <div class="content-card mb-4">
            <div class="section-title"><i class="bi bi-mortarboard"></i> Riwayat Pendidikan</div>
            <div id="pendidikan-list">
                @foreach ($pendidikan as $i => $row)
                    <div class="row g-2 mb-3 align-items-end pendidikan-row">
                        <div class="col-md-3">
                            <label class="small text-muted mb-1">Jenjang</label>
                            <input type="text" class="form-control" name="pendidikan[{{ $i }}][jenjang]"
                                value="{{ old('pendidikan.' . $i . '.jenjang', $row->jenjang) }}" placeholder="D3/S1">
                        </div>
                        <div class="col-md-5">
                            <label class="small text-muted mb-1">Nama Institusi</label>
                            <input type="text" class="form-control"
                                name="pendidikan[{{ $i }}][nama_institusi]"
                                value="{{ old('pendidikan.' . $i . '.nama_institusi', $row->nama_institusi) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted mb-1">Tahun Lulus</label>
                            <input type="text" class="form-control"
                                name="pendidikan[{{ $i }}][tahun_lulus]"
                                value="{{ old('pendidikan.' . $i . '.tahun_lulus', $row->tahun_lulus) }}">
                        </div>
                        <div class="col-md-1">
                            <label class="small text-muted mb-1 d-block">&nbsp;</label>
                            <button type="button" class="btn btn-outline-danger w-100" onclick="removeRow(this)"><i
                                    class="bi bi-trash"></i></button>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-dashed w-100 py-2" onclick="addPendidikanRow()">
                <i class="bi bi-plus-circle me-1"></i> Tambah Riwayat Pendidikan
            </button>
        </div>

        {{-- PELATIHAN --}}
        <div class="content-card mb-4">
            <div class="section-title"><i class="bi bi-journal-bookmark"></i> Kursus / Pelatihan</div>
            <div id="pelatihan-list">
                @foreach ($pelatihan as $i => $row)
                    <div class="row g-2 mb-3 align-items-end pelatihan-row">
                        <div class="col-md-4">
                            <label class="small text-muted mb-1">Nama Pelatihan</label>
                            <input type="text" class="form-control"
                                name="pelatihan[{{ $i }}][nama_pelatihan]"
                                value="{{ old('pelatihan.' . $i . '.nama_pelatihan', $row->nama_pelatihan) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted mb-1">Penyelenggara</label>
                            <input type="text" class="form-control"
                                name="pelatihan[{{ $i }}][penyelenggara]"
                                value="{{ old('pelatihan.' . $i . '.penyelenggara', $row->penyelenggara) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted mb-1">Tahun</label>
                            <input type="text" class="form-control"
                                name="pelatihan[{{ $i }}][tanggal_mulai]"
                                value="{{ old('pelatihan.' . $i . '.tanggal_mulai', $row->tanggal_mulai) }}">
                        </div>
                        <div class="col-md-1">
                            <label class="small text-muted mb-1 d-block">&nbsp;</label>
                            <button type="button" class="btn btn-outline-danger w-100" onclick="removeRow(this)"><i
                                    class="bi bi-trash"></i></button>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-dashed w-100 py-2" onclick="addPelatihanRow()">
                <i class="bi bi-plus-circle me-1"></i> Tambah Pelatihan
            </button>
        </div>

        {{-- PEKERJAAN --}}
        <div class="content-card mb-4">
            <div class="section-title"><i class="bi bi-briefcase"></i> Riwayat Pekerjaan</div>
            <div id="pekerjaan-list">
                @foreach ($pekerjaan as $i => $row)
                    <div class="row g-2 mb-3 align-items-end pekerjaan-row">
                        <div class="col-md-3">
                            <label class="small text-muted mb-1">Instansi</label>
                            <input type="text" class="form-control"
                                name="pekerjaan[{{ $i }}][nama_instansi]"
                                value="{{ old('pekerjaan.' . $i . '.nama_instansi', $row->nama_instansi) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted mb-1">Jabatan</label>
                            <input type="text" class="form-control" name="pekerjaan[{{ $i }}][jabatan]"
                                value="{{ old('pekerjaan.' . $i . '.jabatan', $row->jabatan) }}">
                        </div>
                        <div class="col-md-2">
                            <label class="small text-muted mb-1">Mulai</label>
                            <input type="text" class="form-control"
                                name="pekerjaan[{{ $i }}][tahun_mulai]"
                                value="{{ old('pekerjaan.' . $i . '.tahun_mulai', $row->tahun_mulai) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted mb-1">Selesai</label>
                            <input type="text" class="form-control"
                                name="pekerjaan[{{ $i }}][tahun_selesai]"
                                value="{{ old('pekerjaan.' . $i . '.tahun_selesai', $row->tahun_selesai) }}">
                        </div>
                        <div class="col-md-1">
                            <label class="small text-muted mb-1 d-block">&nbsp;</label>
                            <button type="button" class="btn btn-outline-danger w-100" onclick="removeRow(this)"><i
                                    class="bi bi-trash"></i></button>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-dashed w-100 py-2" onclick="addPekerjaanRow()">
                <i class="bi bi-plus-circle me-1"></i> Tambah Pekerjaan
            </button>
        </div>

        {{-- ORGANISASI --}}
        <div class="content-card mb-4">
            <div class="section-title"><i class="bi bi-diagram-3"></i> Pengalaman Organisasi</div>
            <div id="organisasi-list">
                @foreach ($organisasi as $i => $row)
                    <div class="row g-2 mb-3 align-items-end organisasi-row">
                        <div class="col-md-4">
                            <label class="small text-muted mb-1">Nama Organisasi</label>
                            <input type="text" class="form-control"
                                name="organisasi[{{ $i }}][nama_organisasi]"
                                value="{{ old('organisasi.' . $i . '.nama_organisasi', $row->nama_organisasi) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted mb-1">Jabatan</label>
                            <input type="text" class="form-control" name="organisasi[{{ $i }}][jabatan]"
                                value="{{ old('organisasi.' . $i . '.jabatan', $row->jabatan) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted mb-1">Periode</label>
                            <input type="text" class="form-control"
                                name="organisasi[{{ $i }}][tahun_mulai]"
                                value="{{ old('organisasi.' . $i . '.tahun_mulai', $row->tahun_mulai) }}">
                        </div>
                        <div class="col-md-1">
                            <label class="small text-muted mb-1 d-block">&nbsp;</label>
                            <button type="button" class="btn btn-outline-danger w-100" onclick="removeRow(this)"><i
                                    class="bi bi-trash"></i></button>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-dashed w-100 py-2" onclick="addOrganisasiRow()">
                <i class="bi bi-plus-circle me-1"></i> Tambah Organisasi
            </button>
        </div>

    </form>


    {{-- JAVASCRIPT --}}
    <script>
        function removeRow(btn) {
            btn.closest('.row').remove();
        }

        // --- PENDIDIKAN ---
        function addPendidikanRow() {
            let idx = document.querySelectorAll('#pendidikan-list .pendidikan-row').length;
            let html = `
                <div class="row g-2 mb-3 align-items-end pendidikan-row">
                    <div class="col-md-3">
                        <label class="small text-muted mb-1">Jenjang</label>
                        <input type="text" class="form-control" name="pendidikan[${idx}][jenjang]" placeholder="Jenjang">
                    </div>
                    <div class="col-md-5">
                        <label class="small text-muted mb-1">Nama Institusi</label>
                        <input type="text" class="form-control" name="pendidikan[${idx}][nama_institusi]" placeholder="Institusi">
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted mb-1">Tahun Lulus</label>
                        <input type="text" class="form-control" name="pendidikan[${idx}][tahun_lulus]" placeholder="Lulus">
                    </div>
                    <div class="col-md-1">
                        <label class="small text-muted mb-1 d-block">&nbsp;</label>
                        <button type="button" class="btn btn-outline-danger w-100" onclick="removeRow(this)"><i class="bi bi-trash"></i></button>
                    </div>
                </div>`;
            document.getElementById('pendidikan-list').insertAdjacentHTML('beforeend', html);
        }

        // --- PELATIHAN ---
        function addPelatihanRow() {
            let idx = document.querySelectorAll('#pelatihan-list .pelatihan-row').length;
            let html = `
                <div class="row g-2 mb-3 align-items-end pelatihan-row">
                    <div class="col-md-4">
                        <label class="small text-muted mb-1">Nama Pelatihan</label>
                        <input type="text" class="form-control" name="pelatihan[${idx}][nama_pelatihan]" placeholder="Pelatihan">
                    </div>
                    <div class="col-md-4">
                        <label class="small text-muted mb-1">Penyelenggara</label>
                        <input type="text" class="form-control" name="pelatihan[${idx}][penyelenggara]" placeholder="Penyelenggara">
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted mb-1">Tahun</label>
                        <input type="text" class="form-control" name="pelatihan[${idx}][tanggal_mulai]" placeholder="Tahun">
                    </div>
                    <div class="col-md-1">
                        <label class="small text-muted mb-1 d-block">&nbsp;</label>
                        <button type="button" class="btn btn-outline-danger w-100" onclick="removeRow(this)"><i class="bi bi-trash"></i></button>
                    </div>
                </div>`;
            document.getElementById('pelatihan-list').insertAdjacentHTML('beforeend', html);
        }

        // --- PEKERJAAN ---
        function addPekerjaanRow() {
            let idx = document.querySelectorAll('#pekerjaan-list .pekerjaan-row').length;
            let html = `
                <div class="row g-2 mb-3 align-items-end pekerjaan-row">
                    <div class="col-md-3">
                        <label class="small text-muted mb-1">Instansi</label>
                        <input type="text" class="form-control" name="pekerjaan[${idx}][nama_instansi]" placeholder="Instansi">
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted mb-1">Jabatan</label>
                        <input type="text" class="form-control" name="pekerjaan[${idx}][jabatan]" placeholder="Jabatan">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-1">Mulai</label>
                        <input type="text" class="form-control" name="pekerjaan[${idx}][tahun_mulai]" placeholder="Mulai">
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted mb-1">Selesai</label>
                        <input type="text" class="form-control" name="pekerjaan[${idx}][tahun_selesai]" placeholder="Selesai">
                    </div>
                    <div class="col-md-1">
                        <label class="small text-muted mb-1 d-block">&nbsp;</label>
                        <button type="button" class="btn btn-outline-danger w-100" onclick="removeRow(this)"><i class="bi bi-trash"></i></button>
                    </div>
                </div>`;
            document.getElementById('pekerjaan-list').insertAdjacentHTML('beforeend', html);
        }

        // --- ORGANISASI ---
        function addOrganisasiRow() {
            let idx = document.querySelectorAll('#organisasi-list .organisasi-row').length;
            let html = `
                <div class="row g-2 mb-3 align-items-end organisasi-row">
                    <div class="col-md-4">
                        <label class="small text-muted mb-1">Nama Organisasi</label>
                        <input type="text" class="form-control" name="organisasi[${idx}][nama_organisasi]" placeholder="Organisasi">
                    </div>
                    <div class="col-md-4">
                        <label class="small text-muted mb-1">Jabatan</label>
                        <input type="text" class="form-control" name="organisasi[${idx}][jabatan]" placeholder="Jabatan">
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted mb-1">Periode</label>
                        <input type="text" class="form-control" name="organisasi[${idx}][tahun_mulai]" placeholder="Periode">
                    </div>
                    <div class="col-md-1">
                        <label class="small text-muted mb-1 d-block">&nbsp;</label>
                        <button type="button" class="btn btn-outline-danger w-100" onclick="removeRow(this)"><i class="bi bi-trash"></i></button>
                    </div>
                </div>`;
            document.getElementById('organisasi-list').insertAdjacentHTML('beforeend', html);
        }

        // --- IMAGE PREVIEW ---
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    let img = document.getElementById('preview-foto');
                    let fallback = document.getElementById('preview-fallback');

                    if (img) {
                        img.src = e.target.result;
                        img.classList.remove('d-none');
                        if (fallback) fallback.classList.add('d-none');
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
