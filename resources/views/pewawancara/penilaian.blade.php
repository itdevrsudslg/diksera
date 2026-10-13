@extends('layouts.app')

@section('title', 'Penilaian Uji Skill')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bg-body: #f8fafc;
            --border-color: #e2e8f0;
            --success-bg: #ecfdf5;
            --success-border: #10b981;
            --success-text: #047857;
            --danger-bg: #fef2f2;
            --danger-border: #ef4444;
            --danger-text: #b91c1c;
        }
   /* Style Khusus Tombol Biru */
        .btn-custom-blue {
            background-color: var(--primary-color);
            color: #ffffff;
            border: none;
            transition: all 0.2s ease-in-out;
        }
        .btn-custom-blue:hover {
            background-color: #1d4ed8; /* Biru yang sedikit lebih gelap untuk efek hover */
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.2);
        }
        .kategori-header {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
        }

        .page-header {
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .form-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            /*max-width: 900px;*/
            margin: 0 auto;
        }

        .ticket-header {
            background: #f1f5f9;
            padding: 24px 32px;
            border-bottom: 1px dashed #cbd5e1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .info-block {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .info-icon {
            width: 48px;
            height: 48px;
            background: #fff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-gray);
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .form-body {
            padding: 32px;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f5f9;
        }

        .section-icon {
            color: var(--primary-color);
        }

        /* Styles Khusus Checklist Kredensial */
        .checklist-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }

        .check-item {
            background: #fff;
            border: 1px solid var(--border-color);
            padding: 12px 16px;
            border-radius: 8px;
            display: flex;
            align-items: flex-start;
            transition: 0.2s;
        }

        .check-item:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .check-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-top: 2px;
            margin-right: 12px;
            accent-color: var(--primary-color);
            cursor: pointer;
            flex-shrink: 0;
        }

        .check-label {
            cursor: pointer;
            width: 100%;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        /* Styles Khusus Area Template List */
        .template-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .template-search-wrapper {
            position: relative;
            margin-bottom: 16px;
        }

        .template-search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-gray);
        }

        .template-search {
            width: 100%;
            padding: 10px 16px 10px 40px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .template-search:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .template-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 12px;
            max-height: 250px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .template-list::-webkit-scrollbar { width: 6px; }
        .template-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        .template-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            padding: 12px 16px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            text-decoration: none;
            color: var(--text-dark);
            transition: all 0.2s;
        }

        .template-item:hover {
            border-color: var(--primary-color);
            background: #eff6ff;
            color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.05);
        }

        .template-title { font-weight: 600; font-size: 0.9rem; }

        /* Style Upload & Decision */
        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            background: #f8fafc;
            transition: 0.2s;
        }

        .upload-area:hover {
            border-color: var(--primary-color);
            background: #eff6ff;
        }

        .decision-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .decision-item { position: relative; }
        .decision-item input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }

        .decision-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #fff;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            height: 100%;
            color: var(--text-gray);
        }

        .decision-box i { font-size: 2rem; margin-bottom: 8px; transition: transform 0.3s; }
        .decision-title { font-size: 1rem; font-weight: 700; text-transform: uppercase; }
        .decision-desc { font-size: 0.8rem; font-weight: 400; opacity: 0.8; text-align: center; }

        .decision-box:hover { background: #f8fafc; border-color: #cbd5e1; }
        .decision-item input:checked+.decision-box { transform: translateY(-2px); }

        .decision-item input[value="valid"]:checked+.decision-box,
        .decision-item input[value="lulus"]:checked+.decision-box {
            background: var(--success-bg);
            border-color: var(--success-border);
            color: var(--success-text);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .decision-item input[value="tidak_valid"]:checked+.decision-box,
        .decision-item input[value="tidak_lulus"]:checked+.decision-box {
            background: var(--danger-bg);
            border-color: var(--danger-border);
            color: var(--danger-text);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        .btn-save {
            background: var(--primary-color);
            color: #fff;
            padding: 12px 32px;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            font-size: 1rem;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.3);
            transition: all 0.2s;
        }
        .btn-save:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.4);
        }
    </style>
@endpush

@section('content')

    @php
        $metode = $jadwal->pengajuan->metode ?? '';
        $isKredensial = $metode === 'interview_only';
        // ==========================================
        // EXTRACT DATA DRAFT (Jika ada)
        // ==========================================
        $draftNilaiPertanyaan = isset($penilaian) && $penilaian->nilai_pertanyaan ? $penilaian->nilai_pertanyaan : [];
        $draftUjiSkill = isset($penilaian) && $penilaian->uji_skill ? $penilaian->uji_skill : [];
        
        // FIX: Extract data Kredensial terpisah (Poin dan RKK)
   // FIX: Extract data Kredensial terpisah (Poin dan RKK) dengan aman
$detailPenilaian = [];
if (isset($penilaian) && $penilaian->detail_penilaian) {
    $detailPenilaian = is_string($penilaian->detail_penilaian) ? json_decode($penilaian->detail_penilaian, true) : $penilaian->detail_penilaian;
}

$draftPoin = [];
$draftRkk = [];

// Deteksi apakah data menggunakan format JSON yang baru
if (isset($detailPenilaian['poin_penilaian']) || isset($detailPenilaian['rkk']) || isset($detailPenilaian['is_mpp'])) {
    $draftPoin = $detailPenilaian['poin_penilaian'] ?? [];
    $draftRkk = $detailPenilaian['rkk'] ?? [];
} else {
    // Kompatibilitas format JSON lama:
    // Format lama misal ["1", "3", "4"] adalah data poin kelengkapan, BUKAN rkk.
    $draftPoin = $detailPenilaian;
}

        $draftCatatan = isset($penilaian) ? $penilaian->catatan_pewawancara : '';
        $keputusanAkhir = isset($penilaian) ? $penilaian->keputusan : '';
        
        // Normalisasi index (antisipasi 0-indexed json object ke 1-indexed view)
        $nilaiPertanyaanClean = [];
        $ujiSkillClean = [];
        
        if (!empty($draftNilaiPertanyaan)) {
            $i = 1;
            foreach($draftNilaiPertanyaan as $item) {
                $nilaiPertanyaanClean[$i] = $item;
                $i++;
            }
        }
        if (!empty($draftUjiSkill)) {
            $i = 1;
            foreach($draftUjiSkill as $item) {
                $ujiSkillClean[$i] = $item;
                $i++;
            }
        }
    @endphp
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <div class="container py-5">
        <div class="page-header">
            <div>
                <h1 class="page-title">
                    {{ $isKredensial ? 'Asesmen Kredensialing' : 'Penilaian Uji Skill' }}
                </h1>
                <p class="text-muted small mb-0 mt-1">
                    {{ $isKredensial ? 'Validasi kompetensi dan berkas.' : 'Hasil akhir seleksi Uji Skill.' }}
                </p>
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-light border fw-bold shadow-sm" style="border-radius: 8px;">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('pewawancara.penilaian.store', $jadwal->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-card">
                <div class="ticket-header">
                    <div class="info-block border-end border-light pe-4">
                        <div class="info-icon text-primary"><i class="bi bi-person-badge"></i></div>
                        <div>
                            <div class="info-label">Peserta</div>
                            <div class="info-value">{{ $jadwal->pengajuan->user->name ?? 'User' }}</div>
                        </div>
                    </div>
                    <div class="info-block ps-4">
                        <div class="info-icon text-info"><i class="bi bi-calendar-event"></i></div>
                        <div>
                            <div class="info-label">Metode</div>
                            <div class="info-value">
                                @if ($isKredensial)
                                    <span class="badge bg-secondary">Kredensial</span>
                                @else
                                    <span class="badge bg-primary">Uji Kompetensi</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-body">

                   {{-- === TAMPILAN KHUSUS KREDENSIALING === --}}
                    @if ($isKredensial)
                        <div class="section-header">
                            <i class="bi bi-list-check section-icon"></i> A. Daftar Tilik Kelengkapan (Checklist)
                        </div>
                        
                        {{-- TAMPILKAN LINK GOOGLE DRIVE PESERTA --}}
                        @if(!empty($jadwal->pengajuan->link_gdrive))
                            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 16px; display: flex; align-items: center; margin-bottom: 24px;">
                                <div style="width: 48px; height: 48px; background: #fff; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #2563eb; margin-right: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <i class="bi bi-google"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem;">Arsip Kredensial Peserta</div>
                                    <div style="font-size: 0.8rem; color: #64748b;">Periksa kelengkapan dan keaslian berkas via Google Drive.</div>
                                </div>
                                <a href="{{ $jadwal->pengajuan->link_gdrive }}" target="_blank" class="btn btn-primary ms-auto shadow-sm" style="border-radius: 8px; font-weight: 600; font-size: 0.9rem;">
                                    Buka G-Drive <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            </div>
                        @else
                            <div class="alert alert-warning d-flex align-items-center mb-4" style="border-radius: 12px; font-size: 0.9rem;">
                                <i class="bi bi-exclamation-triangle-fill fs-5 me-3 text-warning"></i>
                                <div>
                                    <strong>Perhatian:</strong> Peserta ini tidak melampirkan tautan Google Drive pada form pengajuan.
                                </div>
                            </div>
                        @endif

                        @php
                            $daftar_kompetensi = [
                                "Form K.01. Aplikasi Kredensialing Tenaga Perawat / Bidan",
                                "Form K.02 Catatan Etik dan Disiplin Perawat / Bidan",
                                "Form K.03 Proses Kredensialing Tenaga Perawat / Bidan",
                                "Formulir Self Asesmen Kredensial dan Rekomendasi",
                                "Daftar riwayat hidup",
                                "Surat rekomendasi dari Ka Instalasi atau Satuan Kerja (Bidwat)",
                                "Surat Keterangan sehat terbaru dari dokter yang memiliki SIP",
                                "Surat Pengalaman kerja (bila pindahan)",
                                "Foto copy Ijazah yang berhubungan dengan profesi Perawat / Bidan",
                                "Foto copy STR Perawat / Bidan yang masih berlaku",
                                "Foto copy Surat Ijin Praktek Perawat / Bidan (SIP) yang masih berlaku",
                                "Foto copy SK CPNS/PNS atau SK kenaikan pangkat terbaru",
                                "Foto Copy Sertifikat pelatihan dasar wajib",
                                "Penilaian kinerja klinik 2 semester pada tahun terakhir",
                                "Pas foto 3 x 4 = 2 lembar",
                                "Log Book Perawat / Bidan yang telah di verifikasi",
                                "Form Ringkasan Data peserta kredensial"
                            ];
                        @endphp

                        <div class="checklist-grid mb-5">
                            @foreach ($daftar_kompetensi as $index => $nama)
                                @php $id_poin = $index + 1; @endphp
                                <div class="check-item">
                                    <input type="checkbox" name="poin_penilaian[]" value="{{ $id_poin }}" id="poin_{{ $id_poin }}"
                                        {{ (is_array(old('poin_penilaian', $draftPoin)) && in_array($id_poin, old('poin_penilaian', $draftPoin))) ? 'checked' : '' }}>
                                    <label for="poin_{{ $id_poin }}" class="check-label">
                                        <span class="fw-bold me-1">{{ $id_poin }}.</span> {{ $nama }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        {{-- ============================================================== --}}
                        {{-- TAMPILAN BANK RKK BERDASARKAN KFK & JENJANG PESERTA --}}
                        {{-- ============================================================== --}}
                        <div class="section-header mt-5">
                            <i class="bi bi-card-list section-icon"></i> B. Rincian Kewenangan Klinis (RKK) Diajukan
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-muted small mb-3">
                                Rincian RKK di bawah ini ditarik secara otomatis berdasarkan pengajuan peserta: <br>
                                <span class="badge bg-light text-dark border me-1">KFK: {{ $namaKfk }}</span> 
                                <span class="badge bg-info-subtle text-info border border-info-subtle">Jenjang: {{ $namaJenjang }}</span>
                            </p>

                            <input type="hidden" name="total_rkk" value="{{ $rkkItems->count() }}">
                            
                            @if($rkkGrouped->isEmpty())
                                <div class="alert alert-info d-flex align-items-center" style="border-radius: 12px; font-size: 0.9rem;">
                                    <i class="bi bi-info-circle-fill fs-5 me-3 text-info"></i>
                                    <div>
                                        <strong>Info:</strong> Data Bank Item RKK untuk <b>{{ $namaKfk }}</b> jenjang <b>{{ $namaJenjang }}</b> belum tersedia.
                                    </div>
                                </div>
                            @else
   <div class="mb-3 d-flex gap-3 flex-wrap">
    <!-- Switch MPP -->
    <div class="form-check form-switch p-3 bg-white border rounded shadow-sm flex-fill custom-switch-card">
        <input class="form-check-input auto-reload-switch" type="checkbox" name="is_mpp" value="1" id="isMpp" 
            {{ (isset($detailPenilaian['is_mpp']) && $detailPenilaian['is_mpp'] == '1') ? 'checked' : '' }}>
        <label class="form-check-label fw-bold ms-2" for="isMpp">
            <i class="bi bi-person-workspace text-primary"></i> Tandai sebagai MPP
        </label>
        <small class="text-muted d-block mt-1">Tambahkan item kompetensi Manajer Pelayanan Pasien.</small>
    </div>

    <!-- Switch PPI -->
    <div class="form-check form-switch p-3 bg-white border rounded shadow-sm flex-fill custom-switch-card">
        <input class="form-check-input auto-reload-switch" type="checkbox" name="is_ppi" value="1" id="isPpi" 
            {{ (isset($detailPenilaian['is_ppi']) && $detailPenilaian['is_ppi'] == '1') ? 'checked' : '' }}>
        <label class="form-check-label fw-bold ms-2" for="isPpi">
            <i class="bi bi-shield-plus text-danger"></i> Tandai sebagai PPI / IPCN
        </label>
        <small class="text-muted d-block mt-1">Tambahkan item kompetensi Pengendalian Infeksi.</small>
    </div>

    {{-- Kelompok RKK tugas spesifik (App\Services\RkkKhusus) --}}
    @php $rkkKhususAktif = \App\Services\RkkKhusus::aktif($detailPenilaian ?? []); @endphp
    @foreach (\App\Services\RkkKhusus::KELOMPOK as $kodeKhusus => $kk)
        <div class="form-check form-switch p-3 bg-white border rounded shadow-sm flex-fill custom-switch-card">
            <input class="form-check-input auto-reload-switch" type="checkbox" name="is_{{ $kodeKhusus }}" value="1" id="is_{{ $kodeKhusus }}"
                {{ in_array($kodeKhusus, $rkkKhususAktif) ? 'checked' : '' }}>
            <label class="form-check-label fw-bold ms-2" for="is_{{ $kodeKhusus }}">
                <i class="bi {{ $kk['ikon'] }} {{ $kk['warna'] }}"></i> {{ $kk['label'] }}
            </label>
            <small class="text-muted d-block mt-1">{{ $kk['keterangan'] }}</small>
        </div>
    @endforeach
</div>

<style>
    .custom-switch-card { transition: all 0.3s ease; cursor: pointer; }
    .custom-switch-card:hover { border-color: #0d6efd !important; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important; }
</style>
<style>
    .mpp-switch {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .mpp-switch:hover {
        border-color: #0d6efd !important; /* Warna biru primary saat di-hover */
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important;
    }
</style>
                                <div class="table-responsive border rounded-3 mb-5">
                                    <table class="table table-hover table-bordered mb-0" style="font-size: 0.9rem;">
                  <thead class="bg-light align-middle text-center">
    <tr>
        <th rowspan="3" style="width: 5%;">NO</th>
        <th rowspan="3" style="width: 30%;">KOMPETENSI KLINIK {{ strtoupper($namaJenjang) }}</th>
        <th colspan="3">JENIS KOMPETENSI</th>
        <th colspan="2">KEWENANGAN</th>
    </tr>
    <tr>
        <th rowspan="2" style="width: 10%;">Mandiri</th>
        <th colspan="2">Kolaborasi</th>
        <th rowspan="2" style="width: 12%;">Berwenang<br>Sendiri</th>
        <th rowspan="2" style="width: 12%;">Dengan<br>Supervisi</th>
    </tr>
    <tr>
        <th style="width: 10%;">Delegasi</th>
        <th style="width: 10%;">Mandat</th>
    </tr>
</thead>
               <tbody class="align-middle">
    @php $noUrut = 1; @endphp {{-- Tambahkan inisiasi nomor urut disini --}}
    @foreach($rkkGrouped as $kategori => $items)
        <tr class="kategori-header">
            <td colspan="7" class="p-0">
                <div class="d-flex justify-content-between align-items-center py-3 px-4">
                    <div class="fw-bold text-dark text-uppercase" style="letter-spacing: 0.5px; font-size: 0.9rem;">
                        <i class="bi bi-bookmark-fill me-2" style="color: var(--primary-color);"></i> {{ $kategori }}
                    </div>
                    
                    <!-- Tombol Select All per Kategori -->
                    <button type="button" class="btn btn-sm btn-custom-blue rounded-pill px-3 shadow-sm d-flex align-items-center gap-1" onclick="selectAll('{{ md5($kategori) }}')" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.3px;">
                        <i class="bi bi-check2-all" style="font-size: 1.1rem;"></i> Pilih Semua (Mandiri & Sendiri)
                    </button>
                </div>
            </td>
        </tr>
        
        @foreach($items as $item)
            @php 
                $id = $item->id;
                $catId = md5($kategori); // ID unik untuk group
                $ansJenis = $draftRkk[$id]['jenis'] ?? '';    
                $ansKw = $draftRkk[$id]['kewenangan'] ?? ''; 
            @endphp
            <tr class="rkk-row-{{$catId}}">
                {{-- Paksa cetak nomor urut lalu increment (++) --}}
                <td class="text-center fw-bold text-muted">{{ $noUrut++ }}</td>
                <td class="ps-3">{{ $item->deskripsi_kompetensi }}</td>
                
                <td class="text-center"><input type="radio" class="form-check-input select-mandiri-{{$catId}}" name="rkk[{{$id}}][jenis]" value="mandiri" {{ $ansJenis == 'mandiri' ? 'checked' : '' }}></td>
                <td class="text-center"><input type="radio" class="form-check-input" name="rkk[{{$id}}][jenis]" value="delegasi" {{ $ansJenis == 'delegasi' ? 'checked' : '' }}></td>
                <td class="text-center"><input type="radio" class="form-check-input" name="rkk[{{$id}}][jenis]" value="mandat" {{ $ansJenis == 'mandat' ? 'checked' : '' }}></td>
                
                <td class="text-center border-start"><input type="radio" class="form-check-input select-sendiri-{{$catId}}" name="rkk[{{$id}}][kewenangan]" value="sendiri" {{ $ansKw == 'sendiri' ? 'checked' : '' }}></td>
                <td class="text-center"><input type="radio" class="form-check-input" name="rkk[{{$id}}][kewenangan]" value="supervisi" {{ $ansKw == 'supervisi' ? 'checked' : '' }}></td>
            </tr>
        @endforeach
    @endforeach
</tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
            

                    {{-- === TAMPILAN KHUSUS UJI KOMPETENSI === --}}
                 {{-- === TAMPILAN KHUSUS UJI KOMPETENSI === --}}
                    @else
                        <div class="section-header">
                            <i class="bi bi-ui-checks section-icon"></i>A. Penilaian Parameter Uji Lisan (Interview)
                        </div>
                        
                      <div class="mb-4">
    @foreach($soalTerpilih as $i => $soal)
    @php 
        // Buat key tema untuk JS (tema_1, tema_2, dst)
        $temaKey = 'tema_' . $i; 
    @endphp
    <div class="card mb-4 border-primary shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
            <span>
                <i class="bi bi-question-circle me-2"></i> Tema {{ $i }}: 
                <span id="judul_tema_{{$i}}">{{ $soal['tema'] }}</span>
            </span>
            <!-- TOMBOL GANTI SOAL -->
            <button type="button" class="btn btn-sm btn-light text-primary fw-bold shadow-sm" onclick="gantiSoal({{ $i }}, '{{ $temaKey }}')">
                <i class="bi bi-arrow-repeat"></i> Acak Soal Lain
            </button>
        </div>
        <div class="card-body bg-white">
            
            <!-- TAMBAHKAN ID PADA INPUT HIDDEN -->
            <input type="hidden" id="input_tema_{{$i}}" name="nilai_pertanyaan[{{$i}}][tema]" value="{{ $soal['tema'] }}">
            <input type="hidden" id="input_pertanyaan_{{$i}}" name="nilai_pertanyaan[{{$i}}][pertanyaan]" value="{{ $soal['pertanyaan'] }}">
            <input type="hidden" id="input_jawaban_{{$i}}" name="nilai_pertanyaan[{{$i}}][jawaban]" value="{{ $soal['jawaban'] }}">

            <div class="row">
                <div class="col-md-12 mb-3">
                    <!-- TAMBAHKAN ID PADA TEKS PERTANYAAN -->
                    <div id="teks_pertanyaan_{{$i}}" class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                        {!! $soal['pertanyaan'] !!}
                    </div>
                </div>
                <div class="col-md-12 mb-4">
                    <!-- TAMBAHKAN ID PADA TEKS JAWABAN -->
                    <div id="teks_jawaban_{{$i}}" class="p-3" style="background: #ecfdf5; border: 1px solid #10b981; border-radius: 8px; color: #047857;">
                        {!! $soal['jawaban'] !!}
                    </div>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-4 p-3 bg-light rounded border">
                <h6 class="fw-bold mb-0 me-3">Nilai Asesor:</h6>
                @php 
                    $skorTersimpan = old('nilai_pertanyaan.'.$i.'.skor', $nilaiPertanyaanClean[$i]['skor'] ?? ''); 
                @endphp
                                        
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input input-skor-radio" style="width: 20px; height: 20px;" type="radio" name="nilai_pertanyaan[{{$i}}][skor]" id="skor_{{$i}}_0" value="0" {{ $skorTersimpan == '0' ? 'checked' : '' }} required>
                                            <label class="form-check-label text-danger fw-bold ms-1" for="skor_{{$i}}_0" style="padding-top: 2px;">Tidak Sesuai (0)</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input input-skor-radio" style="width: 20px; height: 20px;" type="radio" name="nilai_pertanyaan[{{$i}}][skor]" id="skor_{{$i}}_14" value="14.5" {{ $skorTersimpan == '14.5' ? 'checked' : '' }} required>
                                            <label class="form-check-label text-warning fw-bold ms-1" for="skor_{{$i}}_14" style="padding-top: 2px;">Cukup (14.5)</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input input-skor-radio" style="width: 20px; height: 20px;" type="radio" name="nilai_pertanyaan[{{$i}}][skor]" id="skor_{{$i}}_20" value="20" {{ $skorTersimpan == '20' ? 'checked' : '' }} required>
                                            <label class="form-check-label text-success fw-bold ms-1" for="skor_{{$i}}_20" style="padding-top: 2px;">Sesuai (20)</label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            @endforeach
                            
                            <div class="d-flex justify-content-end align-items-center mb-2 px-3 py-2 bg-light border rounded">
                                <span class="fw-bold me-3">Total Skor Lisan :</span>
                                <span class="fs-4 fw-bold text-primary" id="totalSkorDisplay">0</span>
                                <small class="text-muted ms-3">(Syarat Lulus: Min 75)</small>
                            </div>
                        </div>


                        {{-- TABEL KEDUA: UJI SKILL --}}
                        <div class="section-header mt-5">
                            <i class="bi bi-star-fill section-icon"></i> B. Penilaian Uji Skill (Praktek) Kompetensi Klinis
                        </div>
                        
                        <div class="mb-4">
                            @php $idxSkill = 1; @endphp
                            @foreach($formatUjiSkill as $keySkill => $dataSkill)
                            <div class="card mb-4 border-info shadow-sm" style="border-radius: 12px; overflow: hidden;">
                                <div class="card-header bg-info text-white fw-bold">
                                    <i class="bi bi-clipboard-check me-2"></i> {{ $dataSkill['judul'] }}
                                </div>
                                <div class="card-body p-0">
                                    <input type="hidden" name="uji_skill[{{$idxSkill}}][kode]" value="{{ $dataSkill['judul'] }}">
                                    <input type="hidden" name="uji_skill[{{$idxSkill}}][skor]" id="skor_skill_hidden_{{$idxSkill}}" value="{{ old('uji_skill.'.$idxSkill.'.skor', $ujiSkillClean[$idxSkill]['skor'] ?? 0) }}">
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-4">Komponen Penilaian</th>
                                                    <th width="15%" class="text-center border-start">Ya / Dilakukan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($dataSkill['komponen'] as $k => $komponen)
                                                @php 
                                                    // Mengecek centangan jika ada di draft
                                                    $checked = '';
                                                    if(isset($ujiSkillClean[$idxSkill]['detail']) && is_array($ujiSkillClean[$idxSkill]['detail'])) {
                                                        if(in_array($k, $ujiSkillClean[$idxSkill]['detail'])) $checked = 'checked';
                                                    }
                                                @endphp
                                                <tr>
                                                    <td class="ps-4">{{ $komponen }}</td>
                                                    <td class="text-center align-middle border-start">
                                                        <input type="checkbox" style="width: 22px; height: 22px; cursor: pointer;" 
                                                               class="form-check-input skill-checkbox skill-group-{{$idxSkill}}" 
                                                               name="uji_skill[{{$idxSkill}}][detail][]" 
                                                               value="{{ $k }}" {{ $checked }}>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-white">
                                                <tr>
                                                    <td class="text-end fw-bold py-3 pe-3">Nilai Akumulasi Skill Ini :</td>
                                                    <td class="text-center border-start py-3 fs-5 fw-bold text-info">
                                                        <span id="display_skor_skill_{{$idxSkill}}">0</span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @php $idxSkill++; @endphp
                            @endforeach
                            
                            <div class="d-flex justify-content-end align-items-center px-3 py-2 bg-light border rounded">
                                <span class="fw-bold me-3">Rata-Rata Skor Skill :</span>
                                <span class="fs-4 fw-bold text-info" id="rataSkorDisplay">0</span>
                                <small class="text-muted ms-3">(Syarat Lulus: Min 90)</small>
                            </div>
                        </div>
                        
                    @endif

                    {{-- === CATATAN UMUM (ADA DI KEDUA METODE) === --}}
                    <div class="section-header mt-4">
                        <i class="bi bi-pencil-square section-icon"></i> Catatan Tambahan Penguji
                    </div>
                    <div class="mb-5">
                        <textarea name="catatan" class="form-control p-3" rows="4"
                            placeholder="Tuliskan catatan tambahan (opsional)..."
                            style="background: #f8fafc; border-color: #cbd5e1; border-radius: 12px;">{{ old('catatan', $draftCatatan) }}</textarea>
                    </div>

                    {{-- === KEPUTUSAN AKHIR === --}}
                    <div class="section-header">
                        <i class="bi bi-gavel section-icon"></i>
                        {{ $isKredensial ? 'Hasil Validasi' : 'Keputusan Akhir' }}
                    </div>

                    <div class="decision-grid mb-5">
                        <div class="decision-item">
                            <input type="radio" name="keputusan" value="{{ $isKredensial ? 'valid' : 'lulus' }}"
                                id="dec_pass" required
                                {{ old('keputusan', $keputusanAkhir) == 'lulus' || old('keputusan', $keputusanAkhir) == 'valid' ? 'checked' : '' }}>

                            <label for="dec_pass" class="decision-box">
                                <i class="bi bi-check-circle-fill"></i>
                                <div class="decision-title">{{ $isKredensial ? 'VALID' : 'LULUS' }}</div>
                                <div class="decision-desc">
                                    {{ $isKredensial ? 'Berkas & Kompetensi Sesuai' : 'Direkomendasikan Lulus' }}
                                </div>
                            </label>
                        </div>

                        <div class="decision-item">
                            <input type="radio" name="keputusan" value="{{ $isKredensial ? 'tidak_valid' : 'tidak_lulus' }}" 
                                id="dec_fail" required
                                {{ old('keputusan', $keputusanAkhir) == 'tidak_lulus' || old('keputusan', $keputusanAkhir) == 'tidak_valid' ? 'checked' : '' }}>

                            <label for="dec_fail" class="decision-box">
                                <i class="bi bi-x-circle-fill"></i>
                                <div class="decision-title">{{ $isKredensial ? 'TIDAK VALID' : 'TIDAK LULUS' }}</div>
                                <div class="decision-desc">
                                    {{ $isKredensial ? 'Perlu Revisi / Tidak Lengkap' : 'Belum Memenuhi Syarat' }}
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- === DUAL ACTION BUTTONS (DRAFT & FINAL) === --}}
                    <div class="d-flex justify-content-end align-items-center mt-4 pt-4 border-top">
                        <!-- Tombol Draft: Memiliki attribute `formnovalidate` agar bypass required HTML5 -->
                        <button type="submit" name="action" value="draft" class="btn btn-secondary me-3 px-4 rounded-pill fw-semibold" formnovalidate>
                            <i class="bi bi-clock-history me-2"></i> Simpan Draft
                        </button>
                        
                        <!-- Tombol Final: Wajib semua input lengkap -->
                        <button type="submit" name="action" value="final" class="btn-save"
                            onclick="return confirm('Selesaikan dan Terbitkan Hasil Final? Setelah ini sistem akan menggenerate sertifikat peserta.')">
                            <i class="bi bi-send-check me-2"></i> Kunci Hasil Final
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>
<script>
document.querySelectorAll('.auto-reload-switch').forEach(function(switchElement) {
    switchElement.addEventListener('change', function() {
        if(confirm('Halaman akan dimuat ulang untuk memperbarui daftar kompetensi. Pastikan data lain (jika ada) sudah tersimpan sebagai draft.')) {
            let form = this.form;
            let actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'draft';
            form.appendChild(actionInput);
            
            form.submit();
        } else {
            // Jika user batal (Cancel alert)
            this.checked = !this.checked;
        }
    });
});
    function selectAll(catId) {
        // Cari semua radio button di kategori tersebut
        const mandiriRadios = document.querySelectorAll('.select-mandiri-' + catId);
        const sendiriRadios = document.querySelectorAll('.select-sendiri-' + catId);

        // Centang Mandiri
        mandiriRadios.forEach(radio => radio.checked = true);
        // Centang Berwenang Sendiri
        sendiriRadios.forEach(radio => radio.checked = true);
        
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Berhasil dipilih semua (Mandiri/Sendiri)',
            showConfirmButton: false,
            timer: 1500
        });
    }
</script>
    {{-- SCRIPT UNTUK FITUR LIVE SEARCH TEMPLATE & KALKULASI --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // -- Live Search Template --
            const searchInput = document.getElementById('searchTemplate');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const filter = this.value.toLowerCase();
                    const items = document.querySelectorAll('#templateList .template-item');
                    
                    items.forEach(item => {
                        const title = item.querySelector('.template-title').textContent.toLowerCase();
                        if (title.includes(filter)) {
                            item.style.display = 'flex';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }

            // -- Kalkulasi Uji Skill Gabungan --
            const skorTemaInputs = document.querySelectorAll('.input-skor');
            const skorSkillInputs = document.querySelectorAll('.input-skor-skill');
            
            const totalTemaDisplay = document.getElementById('totalSkorDisplay');
            const rataSkillDisplay = document.getElementById('rataSkorDisplay');
            
            const decPass = document.getElementById('dec_pass');
            const decFail = document.getElementById('dec_fail');
            
            const isKredensial = {{ $isKredensial ? 'true' : 'false' }};

        if (!isKredensial) {
                function hitungSemua() {
                    // 1. Hitung Total Uji Lisan (Dari Radio Button)
                    let totalTema = 0;
                    const radioTerpilih = document.querySelectorAll('.input-skor-radio:checked');
                    radioTerpilih.forEach(radio => {
                        totalTema += parseFloat(radio.value);
                    });
                    
                    // Update tampilan angka Lisan
                    totalTemaDisplay.textContent = totalTema % 1 === 0 ? totalTema : totalTema.toFixed(2);

                    // 2. Hitung Rata-Rata Uji Skill (Dari Checkbox)
                    let totalSemuaSkill = 0;
                    // Loop 1 sampai 3 (Karena ada 3 Kategori Skill)
                    for(let i = 1; i <= 3; i++) {
                        const totalCheckbox = document.querySelectorAll('.skill-group-' + i).length;
                        const checkboxDicentang = document.querySelectorAll('.skill-group-' + i + ':checked').length;
                        
                        let skorSkill = 0;
                        if(totalCheckbox > 0) {
                            skorSkill = (checkboxDicentang / totalCheckbox) * 100;
                        }

                        // Update tampilan angka dan input hidden per skill
                        document.getElementById('display_skor_skill_' + i).textContent = skorSkill % 1 === 0 ? skorSkill : skorSkill.toFixed(2);
                        document.getElementById('skor_skill_hidden_' + i).value = skorSkill.toFixed(2);
                        
                        totalSemuaSkill += skorSkill;
                    }
                    
                    let rataSkill = totalSemuaSkill / 3;
                    rataSkillDisplay.textContent = rataSkill % 1 === 0 ? rataSkill : rataSkill.toFixed(2);

                    // 3. Logika Kelulusan Otomatis
                    if (totalTema >= 75 && rataSkill >= 90) {
                        decPass.checked = true;
                    } else {
                        decFail.checked = true;
                    }
                }

                // Pasang event listener: Tiap kali radio/checkbox di-klik, sistem langsung menghitung ulang
                document.querySelectorAll('.input-skor-radio').forEach(input => input.addEventListener('change', hitungSemua));
                document.querySelectorAll('.skill-checkbox').forEach(input => input.addEventListener('change', hitungSemua));
                
                // Jalankan perhitungan saat halaman pertama kali diload (Untuk meload draft)
                hitungSemua();
            }
        });

    // Konversi bank soal dari PHP ke objek JavaScript
    const bankSoalData = @json($bankSoal ?? []);

    function gantiSoal(index, temaKey) {
        if (!bankSoalData[temaKey] || bankSoalData[temaKey].length === 0) {
            alert('Bank soal tidak ditemukan untuk tema ini.');
            return;
        }

        const daftarSoal = bankSoalData[temaKey];
        const currentPertanyaan = document.getElementById('input_pertanyaan_' + index).value;
        
        let soalBaru;
        let randomIndex;
        
        // Looping kecil agar soal yang diacak tidak sama persis dengan soal yang sedang tampil
        let attempts = 0;
        do {
            randomIndex = Math.floor(Math.random() * daftarSoal.length);
            soalBaru = daftarSoal[randomIndex];
            attempts++;
        } while (soalBaru.pertanyaan === currentPertanyaan && attempts < 5);

        // 1. Update teks visual di layar
        document.getElementById('judul_tema_' + index).innerText = soalBaru.tema;
        document.getElementById('teks_pertanyaan_' + index).innerHTML = soalBaru.pertanyaan;
        document.getElementById('teks_jawaban_' + index).innerHTML = soalBaru.jawaban;

        // 2. Update value input hidden agar yang terkirim ke database adalah soal baru
        document.getElementById('input_tema_' + index).value = soalBaru.tema;
        document.getElementById('input_pertanyaan_' + index).value = soalBaru.pertanyaan;
        document.getElementById('input_jawaban_' + index).value = soalBaru.jawaban;

        // 3. (Opsional) Reset pilihan skor radio button agar asesor menilai ulang soal yang baru
        const radios = document.querySelectorAll(`input[name="nilai_pertanyaan[${index}][skor]"]`);
        radios.forEach(radio => radio.checked = false);
        
        // 4. Panggil ulang fungsi kalkulasi total agar skor langsung menyesuaikan
        if (typeof hitungSemua === 'function') {
            hitungSemua();
        }
    }
</script>
@endsection