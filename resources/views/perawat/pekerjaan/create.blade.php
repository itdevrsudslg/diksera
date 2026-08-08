@extends('layouts.app')

@section('title', 'Tambah Pekerjaan – DIKSERA')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Ganti Choices.js dengan Tom Select untuk fitur ketik sendiri yang lebih mulus -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-hover: #1d4ed8;
            --text-dark: #0f172a;
            --text-gray: #64748b;
            --bg-light: #f8fafc;
            --input-border: #e2e8f0;
        }
        .uppercase-input {
            text-transform: uppercase;
        }
        body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; color: var(--text-dark); }
        .page-header { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .page-title { font-size: 1.5rem; font-weight: 700; color: var(--text-dark); margin: 0; letter-spacing: -0.5px; }
        .form-card { background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); padding: 40px; overflow: visible; }
        .form-label { font-size: 0.9rem; font-weight: 600; color: var(--text-dark); margin-bottom: 8px; display: block; }
        .required-star { color: #ef4444; }
        
        .form-control, .form-select { border: 1px solid var(--input-border); border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; color: var(--text-dark); background-color: #fff; transition: all 0.2s ease; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-blue); box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); outline: none; }
        .form-control.is-invalid, .form-select.is-invalid { border-color: #ef4444; } 
        
        /* Custom Tom Select Styling agar rapi menyatu dengan desain */
        .ts-control {
            border: 1px solid var(--input-border) !important; 
            border-radius: 10px !important; 
            padding: 12px 16px !important; 
            font-size: 0.95rem; 
            font-family: 'Inter', sans-serif;
            background-color: #fff; 
            min-height: 48px;
            display: flex;
            align-items: center;
        }
        .ts-wrapper.focus .ts-control { 
            border-color: var(--primary-blue) !important; 
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1) !important; 
        }
        .ts-dropdown { 
            border: 1px solid var(--input-border); 
            border-radius: 10px; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); 
            margin-top: 5px; 
            z-index: 100; 
            font-size: 0.95rem;
        }
        .ts-dropdown .create {
            font-weight: 600;
            color: var(--primary-blue);
            background-color: #eff6ff;
        }

        /* Style Readonly */
        .form-control[readonly] { background-color: #f1f5f9; color: #64748b; cursor: not-allowed; border-color: #e2e8f0; }

        .btn-submit { background-color: var(--primary-blue); color: white; width: 100%; padding: 14px; border-radius: 10px; font-weight: 600; font-size: 1rem; border: none; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.25); transition: all 0.2s; display: flex; justify-content: center; align-items: center; gap: 8px; }
        .btn-submit:hover { background-color: var(--primary-hover); transform: translateY(-2px); box-shadow: 0 8px 12px -1px rgba(37, 99, 235, 0.3); color: white; }
        .btn-back { background: white; border: 1px solid #e2e8f0; color: var(--text-gray); padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; text-decoration: none; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-back:hover { background-color: #f1f5f9; color: var(--text-dark); border-color: #cbd5e1; }
        
        .error-message { font-size: 0.85rem; color: #ef4444; margin-top: 5px; font-weight: 500; }
    </style>
@endpush

@section('content')
    @php
        // --- LOGIKA PENGAMBILAN DATA (LANGSUNG DARI DB) ---
        $valStatus = old('status_kepegawaian', $user->status_kepegawaian ?? $user->drh->status_kepegawaian ?? $user->profile->status_kepegawaian ?? '-');
        $valJabatan = old('jabatan', $user->jabatan ?? $user->drh->jabatan ?? $user->profile->jabatan ?? '-');
        $valPangkat = old('pangkat', $user->pangkat ?? $user->drh->pangkat ?? $user->profile->pangkat ?? '-');
        $valGolongan = old('golongan', $user->golongan ?? $user->drh->golongan ?? $user->profile->golongan ?? '-');
        $isASN = str_contains($valStatus, 'ASN') && !str_contains($valStatus, 'NON');

        // DATA INSTANSI
        $daftarInstansi = [
            'RSUD SIMPANG LIMA GUMUL',
            'RSUD KABUPATEN KEDIRI',
            'DINKES KABUPATEN KEDIRI'
        ];

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
    @endphp

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="page-header">
                    <div><h1 class="page-title">Tambah Riwayat Pekerjaan</h1></div>
                    <a href="{{ route('perawat.pekerjaan.index') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>

                <div class="form-card">
                    <div class="alert alert-info border-0 shadow-sm mb-4" style="background-color: #e0f2fe; color: #0369a1;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Informasi Pekerjaan Saat Ini</h6>
                                <p class="mb-0 small">Data di bawah ini ditarik dari profil DRH Anda. Jika instansi/jabatan saat ini <strong>berbeda</strong>, silakan langsung edit di formulir ini.</p>
                            </div>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger py-3 px-4 mb-4">
                            <strong>Gagal Menyimpan! Terdapat peringatan berikut:</strong>
                            <ul class="mb-0 ps-3 mt-1">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
                        </div>
                    @endif

                    <form action="{{ route('perawat.pekerjaan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            
                            {{-- Nama Instansi --}}
                            <div class="col-md-6">
                                <label class="form-label">Nama Instansi <span class="required-star">*</span></label>
                                <select name="nama_instansi" id="select-instansi" placeholder="Pilih atau ketik instansi..." required>
                                    <option value="">Pilih atau ketik instansi...</option>
                                    @php $currentInstansi = old('nama_instansi'); @endphp
                                    @if($currentInstansi && !in_array($currentInstansi, $daftarInstansi))
                                        <option value="{{ $currentInstansi }}" selected>{{ $currentInstansi }}</option>
                                    @endif
                                    @foreach($daftarInstansi as $instansi)
                                        <option value="{{ $instansi }}" {{ old('nama_instansi') == $instansi ? 'selected' : '' }}>
                                            {{ $instansi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('nama_instansi')
                                    <div class="error-message"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Unit Kerja --}}
                 {{-- Unit Kerja (Create) --}}
<div class="col-md-6">
    <label class="form-label">Unit Kerja <span class="required-star">*</span></label>
    <!-- Hapus id="select-unit-kerja" dan ganti class menjadi form-select -->
    <select name="unit_kerja" class="form-select @error('unit_kerja') is-invalid @enderror" required>
        <option value="">-- Pilih Unit Kerja --</option>
        @php $currentUnit = old('unit_kerja', $user->unit_kerja ?? ''); @endphp
        @if($currentUnit && !in_array($currentUnit, $daftarUnitKerja))
            <option value="{{ $currentUnit }}" selected>{{ $currentUnit }}</option>
        @endif
        @foreach($daftarUnitKerja as $unit)
            <option value="{{ $unit }}" {{ $currentUnit == $unit ? 'selected' : '' }}>
                {{ $unit }}
            </option>
        @endforeach
    </select>
    @error('unit_kerja')
        <div class="error-message"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
    @enderror
</div>

                            {{-- STATUS KEPEGAWAIAN --}}
                          {{-- STATUS KEPEGAWAIAN --}}
                            <div class="col-md-6">
                                <label class="form-label">Status Kepegawaian <span class="required-star">*</span></label>
                                <select name="status_kepegawaian" id="select_status" class="form-select @error('status_kepegawaian') is-invalid @enderror" required>
                                    <option value="">-- Pilih --</option>
                                    @php 
                                        $statusOpts = ['ASN - PNS', 'ASN - PPPK', 'ASN - PPPK PARUH WAKTU', 'NON ASN - KARYAWAN TETAP', 'NON ASN - KONTRAK'];
                                    @endphp
                                    @foreach($statusOpts as $opt)
                                        <option value="{{ $opt }}" {{ old('status_kepegawaian', $valStatus) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
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
                                       value="{{ old('jabatan', $valJabatan) }}" required>
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
                                        <option value="None" data-pangkat="None" {{ old('golongan', $valGolongan)=='None'?'selected':'' }}>None</option>
                                        @php
                                            $golonganMap = [
                                                'IIa' => 'Pengatur Muda', 'IIb' => 'Pengatur Muda Tingkat I', 'IIc' => 'Pengatur', 'IId' => 'Pengatur Tingkat I',
                                                'IIIa' => 'Penata Muda', 'IIIb' => 'Penata Muda Tingkat I', 'IIIc' => 'Penata', 'IIId' => 'Penata Tingkat I',
                                                'IVa' => 'Pembina', 'IVb' => 'Pembina Tingkat I', 'IVc' => 'Pembina Utama Muda', 'IVd' => 'Pembina Utama Madya', 'IVe' => 'Pembina Utama',
                                                'VII' => 'PPPK Gol VII', 'IX' => 'PPPK Gol IX', 'X' => 'PPPK Gol X'
                                            ];
                                        @endphp
                                        @foreach($golonganMap as $gol => $pang)
                                            <option value="{{ $gol }}" data-pangkat="{{ $pang }}" {{ old('golongan', $valGolongan)==$gol?'selected':'' }}>{{ $gol }}</option>
                                        @endforeach
                                    </select>
                                    @error('golongan')
                                        <div class="error-message"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pangkat</label>
                                    <input type="text" name="pangkat" id="pangkat_input" class="form-control bg-light @error('pangkat') is-invalid @enderror" value="{{ old('pangkat', $valPangkat) }}" readonly placeholder="Otomatis diisi berdasarkan golongan">
                                    @error('pangkat')
                                        <div class="error-message"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                  {{-- Jenjang PK Saat Itu --}}
                            <div class="col-md-6">
                                <label class="form-label">Jenjang PK / BK Saat Itu <small class="text-muted">(Opsional)</small></label>
                                <select name="level_pk" class="form-select @error('level_pk') is-invalid @enderror">
                                    <option value="">-- Belum Ada / Kosong --</option>
                                    
                                    <optgroup label="PERAWAT KLINIS (PK)">
                                        <option value="PRA PK" {{ old('level_pk') == 'PRA PK' ? 'selected' : '' }}>PRA PK</option>
                                        <option value="PK 1" {{ old('level_pk') == 'PK 1' ? 'selected' : '' }}>PK 1 (Novice)</option>
                                        <option value="PK 2" {{ old('level_pk') == 'PK 2' ? 'selected' : '' }}>PK 2 (Advanced Beginner)</option>
                                        <option value="PK 3" {{ old('level_pk') == 'PK 3' ? 'selected' : '' }}>PK 3 (Competent)</option>
                                        <option value="PK 4" {{ old('level_pk') == 'PK 4' ? 'selected' : '' }}>PK 4 (Proficient)</option>
                                        <option value="PK 5" {{ old('level_pk') == 'PK 5' ? 'selected' : '' }}>PK 5 (Expert)</option>
                                    </optgroup>

                                    <optgroup label="BIDAN KLINIS (BK)">
                                        <option value="PRA BK" {{ old('level_pk') == 'PRA BK' ? 'selected' : '' }}>PRA BK</option>
                                        <option value="BK 1" {{ old('level_pk') == 'BK 1' ? 'selected' : '' }}>BK 1</option>
                                        <option value="BK 2" {{ old('level_pk') == 'BK 2' ? 'selected' : '' }}>BK 2</option>
                                        <option value="BK 3" {{ old('level_pk') == 'BK 3' ? 'selected' : '' }}>BK 3</option>
                                        <option value="BK 4" {{ old('level_pk') == 'BK 4' ? 'selected' : '' }}>BK 4</option>
                                        <option value="BK 5" {{ old('level_pk') == 'BK 5' ? 'selected' : '' }}>BK 5</option>
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
                                <input type="number" name="tahun_mulai" class="form-control @error('tahun_mulai') is-invalid @enderror" value="{{ old('tahun_mulai', date('Y')) }}" placeholder="YYYY">
                                @error('tahun_mulai')
                                    <div class="error-message"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Tahun Selesai</label>
                                <input type="number" name="tahun_selesai" class="form-control @error('tahun_selesai') is-invalid @enderror" value="{{ old('tahun_selesai') }}" placeholder="YYYY (Kosongkan jika aktif)">
                                @error('tahun_selesai')
                                    <div class="error-message"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Keterangan --}}
                            <div class="col-12">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" value="{{ old('keterangan', 'SK Pengangkatan') }}" placeholder="Contoh: SK Pengangkatan Pertama">
                                @error('keterangan')
                                    <div class="error-message"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Upload Dokumen --}}
                            <div class="col-12">
                                <label class="form-label">Upload Dokumen / SK</label>
                                <input type="file" name="dokumen" class="form-control @error('dokumen') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Format yang diizinkan: PDF / JPG / PNG (Max 4MB)</small>
                                
                                @error('dokumen')
                                    <div class="error-message d-block mt-2">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Peringatan File: {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            
                            <hr class="my-4 text-muted">
<h6 class="fw-bold mb-3">Detail Surat Keputusan (SK) & Penggajian <small class="text-muted">(Sesuai format BKN)</small></h6>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Gol. Ruang Penggajian</label>
        <input type="text" name="gol_ruang" class="form-control" value="{{ old('gol_ruang') }}" placeholder="Contoh: III/a">
    </div>
    <div class="col-md-6">
        <label class="form-label">Gaji Pokok</label>
        <input type="text" name="gaji_pokok" class="form-control" value="{{ old('gaji_pokok') }}" placeholder="Contoh: 3.500.000">
    </div>
    <div class="col-md-4">
        <label class="form-label">Pejabat Penandatangan SK</label>
        <input type="text" name="pejabat_sk" class="form-control" value="{{ old('pejabat_sk') }}" placeholder="Contoh: Bupati / Direktur">
    </div>
    <div class="col-md-4">
        <label class="form-label">Nomor SK</label>
        <input type="text" name="nomor_sk" class="form-control" value="{{ old('nomor_sk') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Tanggal SK</label>
        <input type="date" name="tanggal_sk" class="form-control" value="{{ old('tanggal_sk') }}">
    </div>
</div>
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn-submit"><i class="bi bi-save2"></i> Simpan Riwayat</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Gunakan Tom Select JS (Vanilla JS ringan) -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
    
        document.addEventListener('DOMContentLoaded', function() {
            
            // Konfigurasi Tom Select
            const tomSelectConfig = {
                create: true, // KUNCI UTAMA: Mengizinkan user mengetik nilai custom (baru)
                createOnBlur: true, // Teks yang diketik otomatis disubmit jika user klik luar area
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                render: {
                    // Kustomisasi tulisan saat user mau nambah opsi baru
                    option_create: function(data, escape) {
                        return '<div class="create px-3 py-2">+ Tambahkan opsi: <strong>' + escape(data.input) + '</strong></div>';
                    },
                    no_results: function(data, escape) {
                        return '<div class="no-results px-3 py-2 text-muted">Ketikan teks lalu tekan Enter untuk menambah data baru.</div>';
                    },
                }
            };

            // Inisialisasi untuk Instansi dan Unit Kerja
            new TomSelect('#select-instansi', tomSelectConfig);
           
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
                    selectGolongan.value = "";
                    inputPangkat.value = "";
                }
            }

            // Fungsi untuk otomatis mengisi nama pangkat
            function updatePangkat() {
                if (selectGolongan.selectedIndex >= 0) {
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
@endpush