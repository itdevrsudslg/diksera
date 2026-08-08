@extends('layouts.app')
@section('title', 'Super Edit Pengajuan Sertifikat')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

<style>
    :root {
        --primary-color: #2563eb;
    }
    .timeline {
        border-left: 3px solid #3b82f6;
        padding-left: 30px;
        position: relative;
        margin-left: 15px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -40px;
        top: 0;
        width: 18px;
        height: 18px;
        background: #fff;
        border: 4px solid #3b82f6;
        border-radius: 50%;
    }
    .card-timeline {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }
    .section-title {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .checklist-grid { display: grid; grid-template-columns: 1fr; gap: 12px; }
    .check-item { background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px 16px; border-radius: 8px; display: flex; align-items: flex-start; }
    .check-item input[type="checkbox"] { width: 18px; height: 18px; margin-top: 2px; margin-right: 12px; accent-color: var(--primary-color); cursor: pointer; }
    .check-label { cursor: pointer; width: 100%; font-size: 0.95rem; }
    .kategori-header { background-color: #f1f5f9; border-bottom: 2px solid #e2e8f0; }
    .btn-custom-blue { background-color: var(--primary-color); color: #fff; border: none; }
    .ts-control { border-radius: 8px; padding: 10px 12px; border-color: #dee2e6; }
</style>

@php
    $detailPenilaian = [];
    if (isset($penilaian) && $penilaian->detail_penilaian) {
        $detailPenilaian = is_string($penilaian->detail_penilaian) ? json_decode($penilaian->detail_penilaian, true) : $penilaian->detail_penilaian;
    }
    $draftPoin = $detailPenilaian['poin_penilaian'] ?? (is_array($detailPenilaian) ? $detailPenilaian : []);
    $draftRkk = $detailPenilaian['rkk'] ?? [];

    $draftNilaiPertanyaan = isset($penilaian) && $penilaian->nilai_pertanyaan ? $penilaian->nilai_pertanyaan : [];
    $draftUjiSkill = isset($penilaian) && $penilaian->uji_skill ? $penilaian->uji_skill : [];

    $nilaiPertanyaanClean = [];
    $ujiSkillClean = [];
    
    if (!empty($draftNilaiPertanyaan)) {
        $i = 1; foreach($draftNilaiPertanyaan as $item) { $nilaiPertanyaanClean[$i] = $item; $i++; }
    }
    if (!empty($draftUjiSkill)) {
        $i = 1; foreach($draftUjiSkill as $item) { $ujiSkillClean[$i] = $item; $i++; }
    }
@endphp

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Super Edit Pengajuan</h3>
            <p class="text-muted small">Kelola seluruh aspek pengajuan milik <strong>{{ $pengajuan->user->name }}</strong> secara penuh.</p>
        </div>
        <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-light border shadow-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.pengajuan.update', $pengajuan->id) }}" method="POST" enctype="multipart/form-data" id="superEditForm">
        @csrf
        @method('PUT')

        <div class="timeline">
            
            {{-- TAHAP 1: DATA PENGAJUAN --}}
            <div class="timeline-item">
                <div class="card card-timeline">
                    <div class="card-body p-4">
                        <div class="section-title text-primary">
                            <i class="bi bi-file-earmark-text fs-4"></i> Tahap 1: Data Dasar & Berkas Pengajuan
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status Pengajuan</label>
                                <select name="status" class="form-select bg-light fw-bold text-primary">
                                    <option value="pending" {{ $pengajuan->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="method_selected" {{ $pengajuan->status == 'method_selected' ? 'selected' : '' }}>Menunggu Ujian</option>
                                    <option value="exam_passed" {{ $pengajuan->status == 'exam_passed' ? 'selected' : '' }}>Lulus Ujian (Menunggu Jadwal)</option>
                                    <option value="interview_scheduled" {{ $pengajuan->status == 'interview_scheduled' ? 'selected' : '' }}>Jadwal Wawancara Diatur</option>
                                    <option value="completed" {{ $pengajuan->status == 'completed' ? 'selected' : '' }}>Selesai / Lisensi Terbit</option>
                                    <option value="rejected" {{ $pengajuan->status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Jenis Pengajuan</label>
                                <select name="jenis_pengajuan" class="form-select">
                                    <option value="baru" {{ $pengajuan->jenis_pengajuan == 'baru' ? 'selected' : '' }}>Baru</option>
                                    <option value="perpanjangan" {{ $pengajuan->jenis_pengajuan == 'perpanjangan' ? 'selected' : '' }}>Perpanjangan</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Metode</label>
                                <select name="metode" class="form-select">
                                    <option value="pg_interview" {{ $pengajuan->metode == 'pg_interview' ? 'selected' : '' }}>Uji Kompetensi dan Skill</option>
                                    <option value="interview_only" {{ $pengajuan->metode == 'interview_only' ? 'selected' : '' }}>Kredensialing (Hanya Wawancara)</option>
                                </select>
                            </div>

                            {{-- Edit Lisensi Lama / Terkait jika ada --}}
                            @if($pengajuan->lisensiLama)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Edit Nama Lisensi Terkait</label>
                                <input type="text" name="lisensi_lama_nama" class="form-control" value="{{ $pengajuan->lisensiLama->nama }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Edit Nomor Lisensi Terkait</label>
                                <input type="text" name="lisensi_lama_nomor" class="form-control" value="{{ $pengajuan->lisensiLama->nomor }}">
                            </div>
                            @endif

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Edit Bidang / Jenjang KFK (Termasuk Opsi Keperawatan)</label>
                                <select name="kfk[]" id="choice-kfk" class="form-select" multiple autocomplete="off">
                                    <optgroup label="KFK KRITIS">
                                        @foreach(['KFK Kritis Gawat Darurat', 'KFK Kritis ICU', 'KFK Kritis ICVCU', 'KFK Kritis Anestesi'] as $item)
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
                                            $pks = ['PRA PK', 'PK 1', 'PK 2 KEP GAWAT DARURAT', 'PK 3 KEP GAWAT DARURAT', 'PK 4 KEP GAWAT DARURAT', 'PK 5 KEP GAWAT DARURAT', 'PK 2 KEP ICU', 'PK 3 KEP ICU', 'PK 4 KEP ICU', 'PK 5 KEP ICU', 'PK 2 KEP ICVCU', 'PK 3 KEP ICVCU', 'PK 4 KEP ICVCU', 'PK 5 KEP ICVCU', 'PK 2 KEP ANESTESI', 'PK 3 KEP ANESTESI', 'PK 4 KEP ANESTESI', 'PK 5 KEP ANESTESI', 'PK 2 KEP PENYAKIT DALAM', 'PK 3 KEP PENYAKIT DALAM', 'PK 4 KEP PENYAKIT DALAM', 'PK 5 KEP PENYAKIT DALAM', 'PK 2 KEP BEDAH', 'PK 3 KEP BEDAH', 'PK 4 KEP BEDAH', 'PK 5 KEP BEDAH', 'PK 2 KEP PERIOPERATIF', 'PK 3 KEP PERIOPERATIF', 'PK 4 KEP PERIOPERATIF', 'PK 5 KEP PERIOPERATIF', 'PK 2 KEP ISOLASI (KEPERAWATAN KHUSUS)', 'PK 3 KEP ISOLASI (KEPERAWATAN KHUSUS)', 'PK 4 KEP ISOLASI (KEPERAWATAN KHUSUS)', 'PK 5 KEP ISOLASI (KEPERAWATAN KHUSUS)', 'PK 2 KEP NICU', 'PK 3 KEP NICU', 'PK 4 KEP NICU', 'PK 5 KEP NICU', 'PK 2 KEP PICU', 'PK 3 KEP PICU', 'PK 4 KEP PICU', 'PK 5 KEP PICU', 'PK 2 KEP PEDIATRI', 'PK 3 KEP PEDIATRI', 'PK 4 KEP PEDIATRI', 'PK 5 KEP PEDIATRI', 'PK 2 KEP NEONATUS', 'PK 3 KEP NEONATUS', 'PK 4 KEP NEONATUS', 'PK 5 KEP NEONATUS','Keperawatan'];
                                        @endphp
                                        @foreach($pks as $pk)
                                            <option value="{{ $pk }}" {{ in_array($pk, $selectedKfk) ? 'selected' : '' }}>{{ $pk }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="JENIS KLINIS DENGAN KEPERAWATAN (BK / Lainnya)">
                                        @php
                                            $bks = ['PRA BK','BK 1', 'BK 2 MATERNITAS', 'BK 3 MATERNITAS', 'BK 4 MATERNITAS', 'BK 5 MATERNITAS'];
                                        @endphp
                                        @foreach($bks as $bk)
                                            <option value="{{ $bk }}" {{ in_array($bk, $selectedKfk) ? 'selected' : '' }}>{{ $bk }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Link G-Drive Berkas</label>
                                <input type="url" name="link_gdrive" class="form-control" value="{{ $pengajuan->link_gdrive }}">
                            </div>

                            <div class="col-12 mt-3"><h6 class="fw-bold mb-2">Override Dokumen Fisik & Preview</h6></div>
                            
                            <!-- Dokumen Baru -->
                            <div class="col-md-4">
                                <label class="form-label small d-flex justify-content-between w-100">
                                    <span>Dokumen Baru</span>
                                    @if($pengajuan->file_dokumen_baru)
                                        <a href="javascript:void(0)" onclick="showDocument('{{ asset('storage/' . $pengajuan->file_dokumen_baru) }}')" class="text-primary fw-bold text-decoration-none"><i class="bi bi-eye"></i> Lihat File</a>
                                    @endif
                                </label>
                                <input type="file" name="file_dokumen_baru" class="form-control form-control-sm">
                            </div>

                            <!-- Sertifikat Lama -->
                            <div class="col-md-4">
                                <label class="form-label small d-flex justify-content-between w-100">
                                    <span>Sertifikat Lama</span>
                                    @if($pengajuan->file_sertifikat_lama)
                                        <a href="javascript:void(0)" onclick="showDocument('{{ asset('storage/' . $pengajuan->file_sertifikat_lama) }}')" class="text-primary fw-bold text-decoration-none"><i class="bi bi-eye"></i> Lihat File</a>
                                    @endif
                                </label>
                                <input type="file" name="file_sertifikat_lama" class="form-control form-control-sm">
                            </div>

                            <!-- Surat Rekomendasi -->
                            <div class="col-md-4">
                                <label class="form-label small d-flex justify-content-between w-100">
                                    <span>Surat Rekomendasi</span>
                                    @if($pengajuan->file_surat_rekomendasi)
                                        <a href="javascript:void(0)" onclick="showDocument('{{ asset('storage/' . $pengajuan->file_surat_rekomendasi) }}')" class="text-primary fw-bold text-decoration-none"><i class="bi bi-eye"></i> Lihat File</a>
                                    @endif
                                </label>
                                <input type="file" name="file_surat_rekomendasi" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-12 mt-3">
                                <label class="form-label fw-semibold">Catatan Pengajuan</label>
                                <textarea name="keterangan" rows="2" class="form-control">{{ $pengajuan->keterangan }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAHAP 2: JADWAL WAWANCARA --}}
            <div class="timeline-item">
                <div class="card card-timeline">
                    <div class="card-body p-4">
                        <div class="section-title text-success">
                            <i class="bi bi-calendar-event fs-4"></i> Tahap 2: Jadwal Wawancara / Uji Praktik
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Status Jadwal</label>
                                <select name="jadwal_status" class="form-select">
                                    <option value="pending" {{ ($jadwal->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ ($jadwal->status ?? '') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="completed" {{ ($jadwal->status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Waktu Pelaksanaan</label>
                                <input type="datetime-local" name="waktu_wawancara" class="form-control" 
                                       value="{{ $jadwal && $jadwal->waktu_wawancara ? \Carbon\Carbon::parse($jadwal->waktu_wawancara)->format('Y-m-d\TH:i') : '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Lokasi</label>
                                <input type="text" name="lokasi" class="form-control" value="{{ $jadwal->lokasi ?? '' }}" placeholder="Nama Ruang / Lokasi">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Pilih Asesor</label>
                                <select name="penanggung_jawab_id" class="form-select">
                                    <option value="">-- Pilih Asesor --</option>
                                    @foreach($pjs as $pj)
                                        <option value="{{ $pj->id }}" {{ ($jadwal->penanggung_jawab_id ?? '') == $pj->id ? 'selected' : '' }}>
                                            {{ $pj->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAHAP 3: KEPUTUSAN & PENILAIAN ASESOR (INTERAKTIF) --}}
            <div class="timeline-item">
                <div class="card card-timeline">
                    <div class="card-body p-4">
                        <div class="section-title text-danger mb-4 border-bottom pb-2">
                            <i class="bi bi-award fs-4"></i> Tahap 3: Keputusan & Form Penilaian Asesor
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Keputusan Akhir Asesor</label>
                                <select name="keputusan_penilaian" class="form-select bg-danger-subtle text-danger fw-bold border-danger">
                                    <option value="">-- Kosong (Belum Dinilai) --</option>
                                    <option value="lulus" {{ ($penilaian->keputusan ?? '') == 'lulus' || ($penilaian->keputusan ?? '') == 'valid' ? 'selected' : '' }}>LULUS / VALID</option>
                                    <option value="tidak_lulus" {{ ($penilaian->keputusan ?? '') == 'tidak_lulus' || ($penilaian->keputusan ?? '') == 'tidak_valid' ? 'selected' : '' }}>TIDAK LULUS / TIDAK VALID</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Catatan Pewawancara</label>
                                <textarea name="catatan_pewawancara" rows="2" class="form-control bg-light">{{ $penilaian->catatan_pewawancara ?? '' }}</textarea>
                            </div>
                        </div>

                        {{-- === KREDENSIALING === --}}
                        @if ($isKredensial)
                            <h5 class="fw-bold mt-4 text-primary"><i class="bi bi-list-check"></i> Daftar Tilik Kelengkapan (Checklist)</h5>
                            @php
                                $daftar_kompetensi = [
                                    "Form K.01. Aplikasi Kredensialing Tenaga Perawat / Bidan",
                                    "Form K.02 Catatan Etik dan Disiplin Perawat / Bidan",
                                    "Form K.03 Proses Kredensialing Tenaga Perawat / Bidan",
                                    "Formulir Self Asesmen Kredensial dan Rekomendasi",
                                    "Daftar riwayat hidup",
                                    "Surat rekomendasi dari Ka Instalasi atau Satuan Kerja",
                                    "Surat Keterangan sehat terbaru dari dokter yang memiliki SIP",
                                    "Surat Pengalaman kerja (bila pindahan)",
                                    "Foto copy Ijazah yang berhubungan dengan profesi",
                                    "Foto copy STR Perawat / Bidan yang masih berlaku",
                                    "Foto copy Surat Ijin Praktek Perawat / Bidan (SIP)",
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
                                            {{ in_array($id_poin, $draftPoin) ? 'checked' : '' }}>
                                        <label for="poin_{{ $id_poin }}" class="check-label">
                                            <span class="fw-bold me-1">{{ $id_poin }}.</span> {{ $nama }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <h5 class="fw-bold mt-5 text-primary"><i class="bi bi-card-list"></i> Rincian Kewenangan Klinis (RKK)</h5>
                            <div class="d-flex gap-3 mb-3">
                                <div class="form-check form-switch p-2 bg-light border rounded">
                                    <input class="form-check-input" type="checkbox" name="is_mpp" value="1" id="isMpp" {{ (isset($detailPenilaian['is_mpp']) && $detailPenilaian['is_mpp'] == '1') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold ms-2" for="isMpp">Tandai MPP</label>
                                </div>
                                <div class="form-check form-switch p-2 bg-light border rounded">
                                    <input class="form-check-input" type="checkbox" name="is_ppi" value="1" id="isPpi" {{ (isset($detailPenilaian['is_ppi']) && $detailPenilaian['is_ppi'] == '1') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold ms-2" for="isPpi">Tandai PPI</label>
                                </div>
                                <small class="text-muted d-flex align-items-center">*(Simpan perubahan jika mengubah switch ini untuk me-load ulang item table)*</small>
                            </div>

                            <div class="table-responsive border rounded-3 mb-4">
                                <table class="table table-hover table-bordered mb-0" style="font-size: 0.85rem;">
                                    <thead class="bg-light align-middle text-center">
                                        <tr>
                                            <th rowspan="3" style="width: 5%;">NO</th>
                                            <th rowspan="3" style="width: 30%;">KOMPETENSI KLINIK</th>
                                            <th colspan="3">JENIS KOMPETENSI</th>
                                            <th colspan="2">KEWENANGAN</th>
                                        </tr>
                                        <tr>
                                            <th rowspan="2">Mandiri</th>
                                            <th colspan="2">Kolaborasi</th>
                                            <th rowspan="2">Berwenang<br>Sendiri</th>
                                            <th rowspan="2">Dengan<br>Supervisi</th>
                                        </tr>
                                        <tr>
                                            <th>Delegasi</th>
                                            <th>Mandat</th>
                                        </tr>
                                    </thead>
                                    <tbody class="align-middle">
                                        @php $noUrut = 1; @endphp
                                        @foreach($rkkGrouped as $kategori => $items)
                                            <tr class="kategori-header">
                                                <td colspan="7" class="p-0">
                                                    <div class="d-flex justify-content-between align-items-center py-2 px-3">
                                                        <div class="fw-bold text-dark text-uppercase"><i class="bi bi-bookmark-fill me-2 text-primary"></i> {{ $kategori }}</div>
                                                        <button type="button" class="btn btn-sm btn-custom-blue rounded-pill px-3 py-1" onclick="selectAll('{{ md5($kategori) }}')">
                                                            <i class="bi bi-check2-all"></i> Pilih Semua (Mandiri/Sendiri)
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @foreach($items as $item)
                                                @php 
                                                    $id = $item->id;
                                                    $catId = md5($kategori);
                                                    $ansJenis = $draftRkk[$id]['jenis'] ?? '';    
                                                    $ansKw = $draftRkk[$id]['kewenangan'] ?? ''; 
                                                @endphp
                                                <tr class="rkk-row-{{$catId}}">
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

                        {{-- === UJI KOMPETENSI === --}}
                        @else
                            <h5 class="fw-bold mt-4 text-primary"><i class="bi bi-ui-checks"></i> Penilaian Uji Lisan (Interview)</h5>
                            @foreach($soalTerpilih as $i => $soal)
                                <div class="card mb-3 border bg-light shadow-none">
                                    <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                                        <span>Tema {{ $i }}: {{ $soal['tema'] }}</span>
                                    </div>
                                    <div class="card-body py-2">
                                        <input type="hidden" name="nilai_pertanyaan[{{$i}}][tema]" value="{{ $soal['tema'] }}">
                                        <input type="hidden" name="nilai_pertanyaan[{{$i}}][pertanyaan]" value="{{ $soal['pertanyaan'] }}">
                                        <input type="hidden" name="nilai_pertanyaan[{{$i}}][jawaban]" value="{{ $soal['jawaban'] }}">

                                        @php $skorTersimpan = $nilaiPertanyaanClean[$i]['skor'] ?? ''; @endphp
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="fw-bold">Skor Diberikan:</span>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input input-skor-radio" type="radio" name="nilai_pertanyaan[{{$i}}][skor]" id="skor_{{$i}}_0" value="0" {{ $skorTersimpan == '0' ? 'checked' : '' }}>
                                                <label class="form-check-label text-danger" for="skor_{{$i}}_0">Tidak Sesuai (0)</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input input-skor-radio" type="radio" name="nilai_pertanyaan[{{$i}}][skor]" id="skor_{{$i}}_14" value="14.5" {{ $skorTersimpan == '14.5' ? 'checked' : '' }}>
                                                <label class="form-check-label text-warning" for="skor_{{$i}}_14">Cukup (14.5)</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input input-skor-radio" type="radio" name="nilai_pertanyaan[{{$i}}][skor]" id="skor_{{$i}}_20" value="20" {{ $skorTersimpan == '20' ? 'checked' : '' }}>
                                                <label class="form-check-label text-success" for="skor_{{$i}}_20">Sesuai (20)</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="d-flex justify-content-end mb-4">
                                <h5 class="fw-bold">Total Skor Lisan: <span id="totalSkorDisplay" class="text-primary">0</span></h5>
                            </div>

                            <h5 class="fw-bold mt-5 text-primary"><i class="bi bi-star-fill"></i> Penilaian Uji Skill (Praktek)</h5>
                            @php $idxSkill = 1; @endphp
                            @foreach($formatUjiSkill as $keySkill => $dataSkill)
                                <div class="card mb-3 border">
                                    <div class="card-header bg-white fw-bold"><i class="bi bi-check2-square text-success"></i> {{ $dataSkill['judul'] }}</div>
                                    <div class="card-body p-0">
                                        <input type="hidden" name="uji_skill[{{$idxSkill}}][kode]" value="{{ $dataSkill['judul'] }}">
                                        <input type="hidden" name="uji_skill[{{$idxSkill}}][skor]" id="skor_skill_hidden_{{$idxSkill}}" value="{{ $ujiSkillClean[$idxSkill]['skor'] ?? 0 }}">
                                        
                                        <table class="table table-hover mb-0 font-sm">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-3">Komponen Penilaian</th>
                                                    <th width="15%" class="text-center">Ya / Dilakukan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($dataSkill['komponen'] as $k => $komponen)
                                                @php 
                                                    $checked = '';
                                                    if(isset($ujiSkillClean[$idxSkill]['detail']) && is_array($ujiSkillClean[$idxSkill]['detail'])) {
                                                        if(in_array($k, $ujiSkillClean[$idxSkill]['detail'])) $checked = 'checked';
                                                    }
                                                @endphp
                                                <tr>
                                                    <td class="ps-3">{{ $komponen }}</td>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="form-check-input skill-checkbox skill-group-{{$idxSkill}}" name="uji_skill[{{$idxSkill}}][detail][]" value="{{ $k }}" {{ $checked }} style="width: 20px; height: 20px;">
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="text-end p-2 bg-light border-top">
                                            <span class="fw-bold me-2">Skor: <span id="display_skor_skill_{{$idxSkill}}" class="text-info fs-5">0</span></span>
                                        </div>
                                    </div>
                                </div>
                                @php $idxSkill++; @endphp
                            @endforeach
                            <div class="d-flex justify-content-end mb-4">
                                <h5 class="fw-bold">Rata-Rata Uji Skill: <span id="rataSkorDisplay" class="text-info">0</span></h5>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

        </div>

        <div class="d-flex justify-content-end mt-4 pt-3 border-top gap-3">
            <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-light px-4 border">Batal</a>
            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                <i class="bi bi-save me-1"></i> Simpan Semua Perubahan
            </button>
        </div>
    </form>
</div>

<!-- Modal Preview Dokumen -->
<div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="documentModalLabel"><i class="bi bi-file-earmark-text text-primary"></i> Preview Dokumen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <iframe id="documentIframe" src="" style="width: 100%; height: 80vh; border: none;"></iframe>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const el = document.getElementById('choice-kfk');
        if (el) {
            new TomSelect(el, {
                plugins: ['remove_button'],
                create: false,
                placeholder: 'Ketik untuk mencari KFK/Jenjang...',
                sortField: { field: "text", direction: "asc" }
            });
        }

        const isKredensial = {{ $isKredensial ? 'true' : 'false' }};
        if (!isKredensial) {
            const totalTemaDisplay = document.getElementById('totalSkorDisplay');
            const rataSkillDisplay = document.getElementById('rataSkorDisplay');
            
            function hitungSemua() {
                let totalTema = 0;
                document.querySelectorAll('.input-skor-radio:checked').forEach(radio => {
                    totalTema += parseFloat(radio.value);
                });
                if(totalTemaDisplay) totalTemaDisplay.textContent = totalTema % 1 === 0 ? totalTema : totalTema.toFixed(2);

                let totalSemuaSkill = 0;
                for(let i = 1; i <= 3; i++) {
                    const totalCheckbox = document.querySelectorAll('.skill-group-' + i).length;
                    const checkboxDicentang = document.querySelectorAll('.skill-group-' + i + ':checked').length;
                    
                    let skorSkill = 0;
                    if(totalCheckbox > 0) {
                        skorSkill = (checkboxDicentang / totalCheckbox) * 100;
                    }
                    
                    let targetDisplay = document.getElementById('display_skor_skill_' + i);
                    let targetHidden = document.getElementById('skor_skill_hidden_' + i);
                    
                    if(targetDisplay) targetDisplay.textContent = skorSkill % 1 === 0 ? skorSkill : skorSkill.toFixed(2);
                    if(targetHidden) targetHidden.value = skorSkill.toFixed(2);
                    
                    totalSemuaSkill += skorSkill;
                }
                
                let rataSkill = totalSemuaSkill / 3;
                if(rataSkillDisplay) rataSkillDisplay.textContent = rataSkill % 1 === 0 ? rataSkill : rataSkill.toFixed(2);
            }

            document.querySelectorAll('.input-skor-radio, .skill-checkbox').forEach(input => input.addEventListener('change', hitungSemua));
            hitungSemua(); 
        }

        document.getElementById('documentModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('documentIframe').src = '';
        });
    });

    function showDocument(url) {
        document.getElementById('documentIframe').src = url;
        var myModal = new bootstrap.Modal(document.getElementById('documentModal'));
        myModal.show();
    }

    function selectAll(catId) {
        const mandiriRadios = document.querySelectorAll('.select-mandiri-' + catId);
        const sendiriRadios = document.querySelectorAll('.select-sendiri-' + catId);

        mandiriRadios.forEach(radio => radio.checked = true);
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
@endsection