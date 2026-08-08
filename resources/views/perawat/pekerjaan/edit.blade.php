@extends('layouts.app')

@section('title', 'Edit Pekerjaan – DIKSERA')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Ganti Choices.css dengan Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #2563eb;
            --text-dark: #0f172a;
            --text-gray: #64748b;
            --bg-light: #f8fafc;
            --input-border: #e2e8f0;
            --accent-orange: #f59e0b; /* Warna pembeda untuk konteks Edit */
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
        }

        .page-header { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .page-title { font-size: 1.5rem; font-weight: 700; color: var(--text-dark); margin: 0; letter-spacing: -0.5px; }
        
        .form-card { background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); padding: 40px; overflow: visible; }
        .form-label { font-size: 0.9rem; font-weight: 600; color: var(--text-dark); margin-bottom: 8px; display: block; }
        .required-star { color: #ef4444; }
        
        .form-control, .form-select { border: 1px solid var(--input-border); border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; color: var(--text-dark); background-color: #fff; transition: all 0.2s ease; }
        .form-control:focus, .form-select:focus { border-color: var(--accent-orange); box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1); outline: none; }
        .form-control.is-invalid, .form-select.is-invalid { border-color: #ef4444; }

        /* --- Custom Tom Select Styling (Tema Edit - Orange) --- */
        .ts-control {
            background-color: #fff;
            border: 1px solid var(--input-border);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            min-height: 48px;
            box-shadow: none;
            display: flex;
            align-items: center;
        }

        .ts-control.focus {
            border-color: var(--accent-orange);
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
        }

        .ts-dropdown {
            border-radius: 10px;
            border: 1px solid var(--input-border);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            font-family: 'Inter', sans-serif;
            margin-top: 5px;
        }
        
        .ts-dropdown .create {
            color: var(--accent-orange);
            font-weight: 600;
        }

        /* --- Buttons --- */
        .btn-submit-edit { background-color: var(--accent-orange); color: white; width: 100%; padding: 14px; border-radius: 10px; font-weight: 600; font-size: 1rem; border: none; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.25); transition: all 0.2s; display: flex; justify-content: center; align-items: center; gap: 8px; }
        .btn-submit-edit:hover { background-color: #d97706; transform: translateY(-2px); box-shadow: 0 8px 12px -1px rgba(245, 158, 11, 0.3); color: white; }
        .btn-back { background: white; border: 1px solid #e2e8f0; color: var(--text-gray); padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; text-decoration: none; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-back:hover { background-color: #f1f5f9; color: var(--text-dark); border-color: #cbd5e1; }
        
        .file-preview-box { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 10px 15px; margin-top: 8px; display: inline-flex; align-items: center; gap: 8px; font-size: 0.9rem; }
        .file-link { color: var(--primary-blue); text-decoration: none; font-weight: 600; }
        .file-link:hover { text-decoration: underline; }

        /* Error Text */
        .error-message { font-size: 0.85rem; color: #ef4444; margin-top: 5px; font-weight: 500; display: block; }
    </style>
@endpush

@section('content')
    @php
        // --- DATA REFERENSI UNTUK DROPDOWN ---
      // DATA UNIT KERJA (Sudah Disesuaikan)
        $daftarUnitKerja = [
            'INSTALASI GAWAT DARURAT (IGD)', 
            'NEONATUS (BAYI SEHAT) + NICU', 
            'PEDIATRI INTENSIVE CARE UNIT (PICU)', 
            'SMF OBGYN', 
            'IRNA PARKIT (ANAK)', 
            'INTENSIVE CARE UNIT (ICU)', 
            'INTENSIVE CARDIO VASKULER CARE UNIT (ICVCU)', 
            'RUANG PULIH SADAR (RR)',
            'IRNA CENDRAWASIH', 
            'IRNA PERKUTUT', 
            'IRNA PUNAI (BEDAH)', 
            'IRNA KASUARI (PENYAKIT DALAM)',
            'IRNA GELATIK (KHUSUS)', 
            'INSTALASI BEDAH SENTRAL (OK)', 
            'UNIT HEMODIALISA (CUCI DARAH)',
            'CENTRAL STERILE SUPPLY DEPARTMENT (CSSD)', 
            'IRNA VVIP DAN POLI VVIP',
            'IRNA (KIRANA)', 
            'IRNA VVIP', 
            'IRNA VVIP / VIP',
            'KOMITE KEPERAWATAN', 
            'TIM PENCEGAH DAN PENGENDALI INFEKSI (PPI)', 
            'CASE MANAGER / MPP (MANAJER PELAYANAN PASIEN)', 
            'DIKLAT (INSTRUKTUR KLINIS/CI)', 
            'Lainnya', 
            'INSTALASI RAWAT JALAN DAN MCU'
        ];

        $daftarInstansi = ['RSUD SIMPANG LIMA GUMUL', 'RSUD KABUPATEN KEDIRI', 'DINKES KABUPATEN KEDIRI'];
        $daftarStatus = ['ASN PNS', 'ASN PPPK', 'NON ASN / HONORER', 'BLUD', 'MAGANG', 'SUKARELAWAN'];
        
        $daftarJabatan = [
            'Perawat Terampil', 'Perawat Mahir', 'Perawat Penyelia', 
            'Perawat Ahli Pertama', 'Perawat Ahli Muda', 'Perawat Ahli Madya', 'Perawat Ahli Utama',
            'Bidan Terampil', 'Bidan Mahir', 'Bidan Penyelia',
            'Bidan Ahli Pertama', 'Bidan Ahli Muda', 'Bidan Ahli Madya', 'Bidan Ahli Utama'
        ];

        $daftarPangkat = [
            'Pengatur', 'Pengatur Tingkat I', 'Penata Muda', 'Penata Muda Tingkat I', 
            'Penata', 'Penata Tingkat I', 'Pembina', 'Pembina Tingkat I', 'Pembina Utama Muda'
        ];

        $daftarGolongan = [
            'II/a', 'II/b', 'II/c', 'II/d',
            'III/a', 'III/b', 'III/c', 'III/d',
            'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e'
        ];
    @endphp

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="page-header">
                    <div><h1 class="page-title">Edit Riwayat Pekerjaan</h1></div>
                    <a href="{{ route('perawat.pekerjaan.index') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>

                <div class="form-card">
                    @if ($errors->any())
                        <div class="alert alert-danger py-3 px-4 mb-4">
                            <strong>Gagal Mengupdate! Terdapat peringatan berikut:</strong>
                            <ul class="mb-0 ps-3 mt-1">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
                        </div>
                    @endif

                    <form action="{{ route('perawat.pekerjaan.update', $pekerjaan->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            {{-- Nama Instansi --}}
                            <div class="col-md-6">
                                <label class="form-label">Nama Instansi <span class="required-star">*</span></label>
                                <select name="nama_instansi" id="ts-instansi" class="form-control" required>
                                    <option value="">Pilih atau ketik instansi...</option>
                                    @php $currentInstansi = old('nama_instansi', $pekerjaan->nama_instansi ?? ''); @endphp
                                    @if($currentInstansi && !in_array($currentInstansi, $daftarInstansi))
                                        <option value="{{ $currentInstansi }}" selected>{{ $currentInstansi }}</option>
                                    @endif
                                    @foreach($daftarInstansi as $instansi)
                                        <option value="{{ $instansi }}" {{ $currentInstansi == $instansi ? 'selected' : '' }}>{{ $instansi }}</option>
                                    @endforeach
                                </select>
                                @error('nama_instansi')
                                    <div class="error-message"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                         {{-- Unit Kerja (Edit) --}}
<div class="col-md-6">
    <label class="form-label">Unit Kerja <span class="required-star">*</span></label>
    <!-- Hapus id="ts-unit-kerja" dan ganti class menjadi form-select -->
    <select name="unit_kerja" class="form-select @error('unit_kerja') is-invalid @enderror" required>
        <option value="">-- Pilih Unit Kerja --</option>
        @php $currentUnit = old('unit_kerja', $pekerjaan->unit_kerja ?? ''); @endphp
        @if($currentUnit && !in_array($currentUnit, $daftarUnitKerja))
            <option value="{{ $currentUnit }}" selected>{{ $currentUnit }}</option>
        @endif
        @foreach($daftarUnitKerja as $unit)
            <option value="{{ $unit }}" {{ $currentUnit == $unit ? 'selected' : '' }}>{{ $unit }}</option>
        @endforeach
    </select>
    @error('unit_kerja')
        <div class="error-message"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
    @enderror
</div>

                         {{-- STATUS KEPEGAWAIAN --}}
                            <div class="col-md-6">
                                <label class="form-label">Status Kepegawaian <span class="required-star">*</span></label>
                                <select name="status_kepegawaian" id="select_status" class="form-select @error('status_kepegawaian') is-invalid @enderror" required>
                                    <option value="">Pilih status...</option>
                                    @php 
                                        $statusOpts = ['ASN - PNS', 'ASN - PPPK', 'ASN - PPPK PARUH WAKTU', 'NON ASN - KARYAWAN TETAP', 'NON ASN - KONTRAK'];
                                        $currentStatus = old('status_kepegawaian', $pekerjaan->status_kepegawaian ?? '');
                                    @endphp
                                    @foreach($statusOpts as $opt)
                                        <option value="{{ $opt }}" {{ $currentStatus == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                </select>
                                @error('status_kepegawaian')
                                    <div class="error-message"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Jabatan --}}
                            <div class="col-md-6">
                                <label class="form-label">Jabatan <span class="required-star">*</span></label>
                                <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror" 
                                       value="{{ old('jabatan', $pekerjaan->jabatan ?? '') }}" required>
                                @error('jabatan')
                                    <div class="error-message"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Pangkat & Golongan (Akan disembunyikan otomatis oleh JS jika bukan ASN) --}}
                            <div id="wrapper_pangkat_golongan" class="col-12 row g-4 m-0 p-0 d-none">
                                <div class="col-md-6">
                                    <label class="form-label">Golongan</label>
                                    <select name="golongan" id="golongan_select" class="form-select @error('golongan') is-invalid @enderror">
                                        <option value="" data-pangkat="">-- Pilih --</option>
                                        <option value="None" data-pangkat="None" {{ old('golongan', $pekerjaan->golongan ?? '')=='None'?'selected':'' }}>None</option>
                                        @php
                                            $golonganMap = [
                                                'IIa' => 'Pengatur Muda', 'IIb' => 'Pengatur Muda Tingkat I', 'IIc' => 'Pengatur', 'IId' => 'Pengatur Tingkat I',
                                                'IIIa' => 'Penata Muda', 'IIIb' => 'Penata Muda Tingkat I', 'IIIc' => 'Penata', 'IIId' => 'Penata Tingkat I',
                                                'IVa' => 'Pembina', 'IVb' => 'Pembina Tingkat I', 'IVc' => 'Pembina Utama Muda', 'IVd' => 'Pembina Utama Madya', 'IVe' => 'Pembina Utama',
                                                'VII' => 'PPPK Gol VII', 'IX' => 'PPPK Gol IX', 'X' => 'PPPK Gol X'
                                            ];
                                        @endphp
                                        @foreach($golonganMap as $gol => $pang)
                                            <option value="{{ $gol }}" data-pangkat="{{ $pang }}" {{ old('golongan', $pekerjaan->golongan ?? '')==$gol?'selected':'' }}>{{ $gol }}</option>
                                        @endforeach
                                    </select>
                                    @error('golongan')
                                        <div class="error-message"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pangkat</label>
                                    <input type="text" name="pangkat" id="pangkat_input" class="form-control bg-light @error('pangkat') is-invalid @enderror" 
                                           value="{{ old('pangkat', $pekerjaan->pangkat ?? '') }}" readonly placeholder="Otomatis diisi berdasarkan golongan">
                                    @error('pangkat')
                                        <div class="error-message"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Golongan --}}
                            <div class="col-md-6">
                                <label class="form-label">Golongan <small class="text-muted">(Khusus ASN)</small></label>
                                <select name="golongan" id="ts-golongan" class="form-control">
                                    <option value="">Pilih atau ketik golongan...</option>
                                    @php $currentGol = old('golongan', $pekerjaan->golongan ?? ''); @endphp
                                    @if($currentGol && !in_array($currentGol, $daftarGolongan))
                                        <option value="{{ $currentGol }}" selected>{{ $currentGol }}</option>
                                    @endif
                                    @foreach($daftarGolongan as $gol)
                                        <option value="{{ $gol }}" {{ $currentGol == $gol ? 'selected' : '' }}>{{ $gol }}</option>
                                    @endforeach
                                </select>
                                @error('golongan')
                                    <div class="error-message"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                                @enderror
                            </div>
<div class="col-md-6">
    <label class="form-label">Jenjang PK / BK Saat Itu <small class="text-muted">(Opsional)</small></label>
    
    @php 
        // 1. Ambil data asli dari DB
        $rawPk = $pekerjaan->level_pk ?? '';
        
        // 2. Kamus konversi Romawi ke Angka Biasa
        $mapRomawi = [
            'PK I' => 'PK 1', 'PK II' => 'PK 2', 'PK III' => 'PK 3', 'PK IV' => 'PK 4', 'PK V' => 'PK 5',
            'BK I' => 'BK 1', 'BK II' => 'BK 2', 'BK III' => 'BK 3', 'BK IV' => 'BK 4', 'BK V' => 'BK 5'
        ];
        
        // Bersihkan spasi dan jadikan huruf besar
        $rawPkUpper = strtoupper(trim($rawPk));
        
        // Jika data dari DB pakai Romawi, ubah ke angka biasa. Jika tidak, biarkan.
        $normalizedPk = $mapRomawi[$rawPkUpper] ?? $rawPkUpper;
        
        // 3. Set current PK untuk dropdown
        $currentPk = old('level_pk', $normalizedPk); 
    @endphp
    
    <select name="level_pk" class="form-select @error('level_pk') is-invalid @enderror">
        <option value="">-- Belum Ada / Kosong --</option>
        
        <optgroup label="PERAWAT KLINIS (PK)">
            <option value="PRA PK" {{ $currentPk == 'PRA PK' ? 'selected' : '' }}>PRA PK</option>
            <option value="PK 1" {{ $currentPk == 'PK 1' ? 'selected' : '' }}>PK 1 (Novice)</option>
            <option value="PK 2" {{ $currentPk == 'PK 2' ? 'selected' : '' }}>PK 2 (Advanced Beginner)</option>
            <option value="PK 3" {{ $currentPk == 'PK 3' ? 'selected' : '' }}>PK 3 (Competent)</option>
            <option value="PK 4" {{ $currentPk == 'PK 4' ? 'selected' : '' }}>PK 4 (Proficient)</option>
            <option value="PK 5" {{ $currentPk == 'PK 5' ? 'selected' : '' }}>PK 5 (Expert)</option>
        </optgroup>

        <optgroup label="BIDAN KLINIS (BK)">
            <option value="PRA BK" {{ $currentPk == 'PRA BK' ? 'selected' : '' }}>PRA BK</option>
            <option value="BK 1" {{ $currentPk == 'BK 1' ? 'selected' : '' }}>BK 1</option>
            <option value="BK 2" {{ $currentPk == 'BK 2' ? 'selected' : '' }}>BK 2</option>
            <option value="BK 3" {{ $currentPk == 'BK 3' ? 'selected' : '' }}>BK 3</option>
            <option value="BK 4" {{ $currentPk == 'BK 4' ? 'selected' : '' }}>BK 4</option>
            <option value="BK 5" {{ $currentPk == 'BK 5' ? 'selected' : '' }}>BK 5</option>
        </optgroup>
    </select>
    
    @error('level_pk')
        <div class="error-message"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
    @enderror
</div>

                            <div class="col-md-6"></div> {{-- Spacer --}}

                            {{-- Tahun --}}
                            <div class="col-md-6">
                                <label class="form-label">Tahun Mulai</label>
                                <input type="number" name="tahun_mulai" class="form-control @error('tahun_mulai') is-invalid @enderror" value="{{ old('tahun_mulai', $pekerjaan->tahun_mulai ?? '') }}" placeholder="YYYY">
                                @error('tahun_mulai')
                                    <div class="error-message"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tahun Selesai</label>
                                <input type="number" name="tahun_selesai" class="form-control @error('tahun_selesai') is-invalid @enderror" value="{{ old('tahun_selesai', $pekerjaan->tahun_selesai ?? '') }}" placeholder="YYYY (Kosongkan jika masih aktif)">
                                @error('tahun_selesai')
                                    <div class="error-message"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Keterangan --}}
                            <div class="col-12">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" value="{{ old('keterangan', $pekerjaan->keterangan ?? '') }}" placeholder="Contoh: SK Pengangkatan Pertama">
                                @error('keterangan')
                                    <div class="error-message"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Upload Dokumen --}}
                            <div class="col-12">
                                <label class="form-label">Upload Dokumen / SK <small class="text-muted">(Biarkan kosong jika tidak ingin mengubah)</small></label>
                                <input type="file" name="dokumen" class="form-control @error('dokumen') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Format yang diizinkan: PDF / JPG / PNG (Max 4MB)</small>
                                
                                @error('dokumen')
                                    <div class="error-message d-block mt-2">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Peringatan File: {{ $message }}
                                    </div>
                                @enderror
                                
                                @if(isset($pekerjaan->dokumen_path))
                                <div class="file-preview-box mt-3">
                                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                                    <span>File Saat Ini:</span>
                                    <a href="{{ asset('storage/'.$pekerjaan->dokumen_path) }}" target="_blank" class="file-link">Lihat Dokumen</a>
                                </div>
                                @endif
                            </div>

<hr class="my-4 text-muted">
                            <h6 class="fw-bold mb-3">Detail Surat Keputusan (SK) & Penggajian <small class="text-muted">(Sesuai format BKN)</small></h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Gol. Ruang Penggajian</label>
                                    <input type="text" name="gol_ruang" class="form-control" value="{{ old('gol_ruang', $pekerjaan->gol_ruang) }}" placeholder="Contoh: III/a">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gaji Pokok</label>
                                    <input type="text" name="gaji_pokok" class="form-control" value="{{ old('gaji_pokok', $pekerjaan->gaji_pokok) }}" placeholder="Contoh: 3.500.000">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Pejabat Penandatangan SK</label>
                                    <input type="text" name="pejabat_sk" class="form-control" value="{{ old('pejabat_sk', $pekerjaan->pejabat_sk) }}" placeholder="Contoh: Bupati / Direktur">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nomor SK</label>
                                    <input type="text" name="nomor_sk" class="form-control" value="{{ old('nomor_sk', $pekerjaan->nomor_sk) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal SK</label>
                                    <input type="date" name="tanggal_sk" class="form-control" value="{{ old('tanggal_sk', $pekerjaan->tanggal_sk) }}">
                                </div>
                            </div>

                        <div class="mt-5">
                            <button type="submit" class="btn-submit-edit"><i class="bi bi-pencil-square"></i> Update Riwayat</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Ganti JS Choices dengan Tom Select -->
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // HANYA INSTANSI DAN UNIT KERJA YANG PAKAI TOM SELECT (Ketik Sendiri)
            const ids = [
                '#ts-instansi'
            ];

            ids.forEach(id => {
                const element = document.querySelector(id);
                if (element) {
                    new TomSelect(element, {
                        create: true, // KUNCI UTAMA: Izinkan user mengetik nilai baru
                        createOnBlur: true, // Jika user mengetik lalu klik di luar, otomatis jadi inputan
                        sortField: {
                            field: "text",
                            direction: "asc"
                        },
                        render: {
                            option_create: function(data, escape) {
                                return '<div class="create">Tambahkan: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                            }
                        }
                    });
                }
            });

            // === LOGIKA ASN & GOLONGAN ===
            const selectStatus = document.getElementById('select_status');
            const wrapperPangkatGolongan = document.getElementById('wrapper_pangkat_golongan');
            const selectGolongan = document.getElementById('golongan_select');
            const inputPangkat = document.getElementById('pangkat_input');

            // Fungsi untuk menampilkan/menyembunyikan baris Golongan
            function updateStatusLogic() {
                const status = selectStatus.value || '';
                // Jika status mengandung "ASN" dan TIDAK mengandung "NON"
                if (status.includes('ASN') && !status.includes('NON')) {
                    wrapperPangkatGolongan.classList.remove('d-none');
                } else {
                    wrapperPangkatGolongan.classList.add('d-none');
                    // Opsional: bersihkan isian jika user beralih ke Non ASN
                    if(selectGolongan) selectGolongan.value = "";
                    if(inputPangkat) inputPangkat.value = "";
                }
            }

            // Fungsi untuk otomatis mengisi nama pangkat
            function updatePangkat() {
                if (selectGolongan && selectGolongan.selectedIndex >= 0) {
                    const selectedOption = selectGolongan.options[selectGolongan.selectedIndex];
                    const pangkatText = selectedOption.getAttribute('data-pangkat');
                    inputPangkat.value = pangkatText || '';
                }
            }

            if (selectStatus) {
                selectStatus.addEventListener('change', updateStatusLogic);
                updateStatusLogic(); // Panggil saat halaman pertama dimuat
            }

            if (selectGolongan) {
                selectGolongan.addEventListener('change', updatePangkat);
            }
        });
    </script>
    </script>
@endpush