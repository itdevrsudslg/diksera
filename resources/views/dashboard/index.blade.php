@extends('layouts.app')

@section('title', 'Dashboard Perawat')

@section('content')
<div class="container-fluid py-4 bg-light-subtle">

    {{-- 1. HEADER SECTION (Minimalist Welcome) --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold text-dark mb-1">Halo, {{ $profile->nama_lengkap ?? explode(' ', $user->name)[0] }}! 👋</h4>
                <p class="text-muted small mb-0">Selamat datang kembali di panel profesional Anda.</p>
            </div>
            <div>
                <span class="badge bg-white text-secondary border px-3 py-2 rounded-pill fw-normal shadow-sm">
                    <i class="bi bi-calendar-event me-2"></i> {{ now()->format('d F Y') }}
                </span>
            </div>
        </div>
    </div>
    
@if($lastMessage)
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden {{ $unreadCount > 0 ? 'bg-danger text-white animate__animated animate__headShake' : 'bg-primary text-white' }}" 
     style="box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-9 mb-3 mb-md-0">
                <div class="d-flex align-items-start">
                    <div class="bg-white text-{{ $unreadCount > 0 ? 'danger' : 'primary' }} rounded-circle d-flex align-items-center justify-content-center me-3 shadow" 
                         style="min-width: 60px; height: 60px;">
                        <i class="bi {{ $unreadCount > 0 ? 'bi-chat-right-text-fill' : 'bi-chat-left-dots-fill' }} fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 {{ $unreadCount > 0 ? 'text-white' : '' }}">
                            {{ $unreadCount > 0 ? '📢 ADA PESAN PENTING DARI ADMIN!' : 'Percakapan Admin' }}
                        </h5>
                        <p class="mb-0 opacity-90" style="font-size: 15px; font-style: italic; line-height: 1.4;">
                            "{{ $lastMessage->message }}"
                        </p>
                        <small class="opacity-75 mt-2 d-block" style="font-size: 11px;">
                            <i class="bi bi-clock me-1"></i> Diterima pada: {{ $lastMessage->created_at->format('d M Y, H:i') }}
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 text-md-end">
                <button class="btn btn-light btn-lg text-{{ $unreadCount > 0 ? 'danger' : 'primary' }} fw-bold rounded-pill px-4 py-2 shadow-sm w-100 w-md-auto" 
                        onclick="openChatModal({{ $lastMessage->sender_id }}, '{{ $adminName ?? 'Admin' }}')" 
                        data-bs-toggle="modal" data-bs-target="#chatModal">
                    <i class="bi bi-reply-fill me-1"></i> Buka & Balas
                </button>
            </div>
        </div>
    </div>
</div>
@endif
{{-- ALERT KHUSUS TYPE PERAWAT --}}
@if(!$isTypePerawatLengkap)
<div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4 rounded-3" role="alert">
    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle me-3">
        <i class="bi bi-info-circle-fill fs-4"></i>
    </div>
    <div>
        <strong class="d-block text-dark">Data Kompetensi Belum Lengkap!</strong>
        <span class="small text-muted">Anda wajib mengisi <strong>KFK</strong> dan <strong>Tingkat PK/BK</strong> sebelum dapat melanjutkan proses sertifikasi.</span>
        <a href="{{ route('perawat.identitas.edit') }}" class="d-block mt-1 fw-bold text-decoration-none small">
            Lengkapi di Menu Identitas <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</div>
@endif
    {{-- ALERT: HANYA MUNCUL JIKA KRITIKAL --}}
    @if(count($warnings) > 0)
    <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-4 rounded-3" role="alert">
        <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-circle me-3">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div>
            <strong class="d-block text-danger">Tindakan Diperlukan</strong>
            <span class="small text-muted">Dokumen berikut mendekati kadaluwarsa: {{ implode(', ', $warnings) }}</span>
        </div>
    </div>
    @endif

    <div class="row g-4">
        {{-- KOLOM KIRI (MAIN CONTENT) --}}
        <div class="col-lg-8">
            
            {{-- 2. QUICK STATS (Clean Cards) --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon-square bg-primary-subtle text-primary me-3">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">{{ $counts['pendidikan'] }}</h5>
                                <span class="text-muted small">Riwayat Pendidikan</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon-square bg-success-subtle text-success me-3">
                                <i class="bi bi-award-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">{{ $counts['pelatihan'] }}</h5>
                                <span class="text-muted small">Sertifikat Pelatihan</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon-square bg-warning-subtle text-warning me-3">
                                <i class="bi bi-briefcase-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">{{ $counts['pekerjaan'] }}</h5>
                                <span class="text-muted small">Pengalaman Kerja</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. PROGRESS DRH (Clean List) --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0">Kelengkapan Data Diri (DRH)</h6>
                        <p class="text-muted extra-small mb-0">Lengkapi data ini untuk mengajukan sertifikasi.</p>
                    </div>
                    <div class="text-end">
                        <h4 class="fw-bold text-primary mb-0">{{ $progressPercent }}%</h4>
                    </div>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    {{-- Progress Line --}}
                    <div class="progress bg-light mb-4" style="height: 6px; border-radius: 10px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progressPercent }}%; border-radius: 10px;"></div>
                    </div>

                    <div class="row g-3">
                        @foreach($sections as $sec)
<div class="col-md-6">
    <div class="d-flex align-items-center p-3 rounded-3 border h-100 section-item {{ $sec['status'] ? 'border-success-subtle bg-success-subtle bg-opacity-10' : 'bg-white' }}">
        <div class="me-3">
            @if($sec['status'])
                <i class="bi bi-check-circle-fill text-success fs-4"></i>
            @else
                <div class="rounded-circle border border-2 border-secondary d-flex align-items-center justify-content-center text-secondary" style="width: 24px; height: 24px; font-size: 10px;">
                    <i class="bi bi-circle-fill text-white"></i>
                </div>
            @endif
        </div>
        <div class="flex-grow-1">
            <h6 class="mb-0 fw-bold small text-dark">{{ $sec['nama'] }}</h6>
            <small class="{{ $sec['status'] ? 'text-success' : 'text-muted' }}" style="font-size: 11px;">
                {{ $sec['status'] ? 'Lengkap' : ($sec['wajib'] ? 'Wajib Diisi' : 'Opsional') }}
            </small>
        </div>
        @if(!$sec['status'])
            @php
                $routes = [
                    'bio'   => route('perawat.identitas.edit'),
                    'edu'   => route('perawat.pendidikan.index'),
                    'job'   => route('perawat.pekerjaan.index'),
                    'train' => route('perawat.pelatihan.index'),
                    'fam'   => route('perawat.keluarga.index'),
                    'doc'   => route('perawat.str.index'),
                ];
            @endphp
            <a href="{{ $routes[$sec['id']] ?? '#' }}" class="btn btn-sm btn-light border text-primary rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0; line-height: 30px;">
                <i class="bi bi-arrow-right"></i>
            </a>
        @endif
    </div>
</div>
@endforeach

                    </div>
                </div>
            </div>

            {{-- 4. CHART PORTFOLIO (Simple) --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="fw-bold mb-1">Visualisasi Portfolio</h6>
                            <p class="text-muted extra-small mb-3">Sebaran data kompetensi yang Anda miliki.</p>
                            <div style="height: 180px;">
                                <canvas id="cleanChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-4 text-center border-start">
                            <h2 class="fw-bold mb-0 text-dark">{{ array_sum($chartData['data']) }}</h2>
                            <p class="text-muted small mb-0">Total Item Data</p>
                            <hr class="w-25 mx-auto my-3 text-muted">
                            <a href="{{ route('perawat.data.lengkap') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN (SIDEBAR) --}}
        <div class="col-lg-4">
            
            {{-- 1. PROFILE CARD (Clean Vertical) --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 text-center overflow-hidden">
                <div class="card-body p-4">
                    <div class="mb-3 position-relative d-inline-block">
                        @if($profile && $profile->foto_3x4)
                            <img src="{{ asset('storage/'.$profile->foto_3x4) }}" class="rounded-circle shadow-sm" width="100" height="100" style="object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto text-secondary display-6 fw-bold" style="width: 100px; height: 100px;">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                        <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-white rounded-circle"></span>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $profile->nama_lengkap ?? $user->name }}</h5>
                    <p class="text-muted small mb-3">{{ $profile->nik ?? 'NIK Belum Lengkap' }}</p>
                    <div class="mb-3">
  <div class="mt-2 mb-3">
    @if(!empty($typePerawat) && count($typePerawat) >= 2)
        {{-- Tampilan untuk KFK --}}
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="text-muted ">KFK:</span>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2" style="font-size: 15px;">
                {{ $typePerawat[0] }}
            </span>
        </div>

        {{-- Tampilan untuk Jenjang (PK/BK) --}}
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted extra-small">Jenjang:</span>
            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2" style="font-size: 15px;">
                {{ $typePerawat[1] }}
            </span>
        </div>
    @elseif(!empty($typePerawat) && count($typePerawat) == 1)
        {{-- Jika Baru Terisi Satu --}}
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted extra-small">KFK:</span>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2" style="font-size: 15px;">
                {{ $typePerawat[0] }}
            </span>
        </div>
        <small class="text-danger d-block mt-1">Jenjang Belum Diisi!</small>
    @else
        {{-- Jika Kosong Sama Sekali --}}
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 w-100 py-1" style="font-size: 15px;">
            Tipe Perawat Belum Diisi
        </span>
    @endif
</div>
</div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('perawat.drh') }}" class="btn btn-light border btn-sm">Edit Profil</a>
                    </div>
                </div>
            </div>

           <div class="card border-0 shadow-sm rounded-4 mb-4 text-white overflow-hidden position-relative" 
     style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); transition: all 0.3s ease;">
    
    {{-- Efek Glow Halus (Opsional, agar tidak flat) --}}
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-white opacity-0" 
         style="background: radial-gradient(circle at top right, rgba(255,255,255,0.15), transparent 60%);"></div>
<!--<div class="card-body p-4 position-relative z-1">-->
<!--    <div class="d-flex justify-content-between align-items-start mb-3">-->
<!--        <h6 class="fw-bold text-white-50 text-uppercase" style="font-size: 11px; letter-spacing: 1.5px;">-->
<!--            Status Pengajuan Kredensialing-->
<!--        </h6>-->
        
<!--        @if($latestPengajuan && in_array($latestPengajuan->status, ['pending', 'method_selected', 'exam_passed', 'interview_scheduled']))-->
<!--            <span class="position-relative d-flex h-10 w-10">-->
<!--              <span class="animate-ping position-absolute d-inline-flex h-100 w-100 rounded-circle bg-warning opacity-75"></span>-->
<!--              <span class="position-relative d-inline-flex rounded-circle h-10 w-10 bg-warning" style="width:10px; height:10px;"></span>-->
<!--            </span>-->
<!--        @endif-->
<!--    </div>-->

<!--    @if($latestPengajuan)-->
<!--        <h3 class="fw-bold mb-2">{{ ucfirst($latestPengajuan->tipe_sertifikat ?? 'Kredensialing') }}</h3>-->
        
<!--        <div class="mb-4">-->
<!--            {{-- JIKA SUDAH SELESAI 100% --}}-->
<!--            @if($latestPengajuan->status == 'completed' || $latestPengajuan->status == 'disetujui')-->
<!--                <div class="bg-white bg-opacity-25 p-3 rounded-3 mt-3">-->
<!--                    <p class="small mb-2 fw-bold"><i class="bi bi-check-circle-fill text-success me-1"></i> Proses Selesai!</p>-->
<!--                    <p class="small mb-3 text-white-50">Sertifikat Penugasan Klinis Anda sudah diterbitkan.</p>-->
                    
<!--                    <div class="d-flex gap-2">-->
<!--                        <a href="{{ route('perawat.pengajuan.print_spk', $latestPengajuan->id) }}" target="_blank" class="btn btn-sm btn-light text-primary fw-bold rounded-pill w-100 shadow-sm">-->
<!--                            <i class="bi bi-download"></i> Unduh SPK-->
<!--                        </a>-->
<!--                        <a href="{{ route('perawat.pengajuan.print_spkk', $latestPengajuan->id) }}" target="_blank" class="btn btn-sm btn-light text-primary fw-bold rounded-pill w-100 shadow-sm">-->
<!--                            <i class="bi bi-download"></i> Unduh SPKK-->
<!--                        </a>-->
<!--                    </div>-->
<!--                </div>-->

<!--            {{-- JIKA MASIH PROSES --}}-->
<!--            @else-->
<!--                <span class="badge bg-white text-warning border border-white px-3 py-2 rounded-pill fw-bold mb-3">-->
<!--                    <i class="bi bi-hourglass-split me-1"></i> Sedang Diproses-->
<!--                </span>-->
                
                <!-- Tombol Trigger Modal Tracking -->
<!--                <button type="button" class="btn btn-sm btn-outline-light w-100 rounded-pill py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#trackingModal">-->
<!--                    <i class="bi bi-search me-1"></i> Lihat Status Persetujuan-->
<!--                </button>-->
<!--            @endif-->
<!--        </div>-->
<!--    @else-->
        <!-- Tampilan Belum Ada Pengajuan -->
<!--    @endif-->
<!--</div>-->

<!-- Modal Tracking Progress (Taruh di paling bawah sejajar dengan chatModal) -->
@if($latestPengajuan && $latestPengajuan->status != 'completed')
<div class="modal fade" id="trackingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-light border-0">
                <h6 class="modal-title fw-bold text-dark">Tracking Kredensialing</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-dark">
                <p class="small text-muted mb-4">Pengajuan Anda saat ini tertahan di tahapan berikut:</p>
                
                <div class="d-flex align-items-center mb-3 p-3 bg-warning bg-opacity-10 rounded-3 border border-warning border-opacity-25">
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 40px; height: 40px;">
                        <i class="bi bi-person-fill-exclamation fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Menunggu Assesor / Pewawancara</h6>
                        <small class="text-muted" style="font-size: 11px;">Belum memberikan penilaian akhir (Kunci Final)</small>
                    </div>
                </div>
                
                <div class="d-flex align-items-center p-3 bg-light rounded-3 opacity-50">
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="bi bi-check2-all fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Penerbitan Direktur</h6>
                        <small class="text-muted" style="font-size: 11px;">Menunggu tahapan sebelumnya selesai</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
</div>

            {{-- 3. DOKUMEN LEGALITAS (Clean List) --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold mb-0">Legalitas Dokumen</h6>
                </div>
                <div class="card-body p-4">
                    {{-- STR --}}
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="rounded p-2 me-3 {{ $legalitas['str']['data'] ? ($legalitas['str']['status'] == 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger') : 'bg-light text-muted' }}">
                            <i class="bi bi-card-heading fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold small">STR (Registrasi)</h6>
                            <small class="text-muted" style="font-size: 11px;">
                                {{ $legalitas['str']['data'] ? 'Exp: '.\Carbon\Carbon::parse($legalitas['str']['data']->tgl_expired)->format('d M Y') : 'Belum diupload' }}
                            </small>
                        </div>
                        <span class="badge {{ $legalitas['str']['data'] ? ($legalitas['str']['status'] == 'active' ? 'bg-success' : 'bg-danger') : 'bg-secondary' }} rounded-pill" style="font-size: 10px;">
                            {{ $legalitas['str']['msg'] }}
                        </span>
                    </div>

                    {{-- SIP --}}
                    <div class="d-flex align-items-center">
                        <div class="rounded p-2 me-3 {{ $legalitas['sip']['data'] ? ($legalitas['sip']['status'] == 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger') : 'bg-light text-muted' }}">
                            <i class="bi bi-file-medical fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold small">SIP (Praktik)</h6>
                            <small class="text-muted" style="font-size: 11px;">
                                {{ $legalitas['sip']['data'] ? 'Exp: '.\Carbon\Carbon::parse($legalitas['sip']['data']->tgl_expired)->format('d M Y') : 'Belum diupload' }}
                            </small>
                        </div>
                        <span class="badge {{ $legalitas['sip']['data'] ? ($legalitas['sip']['status'] == 'active' ? 'bg-success' : 'bg-danger') : 'bg-secondary' }} rounded-pill" style="font-size: 10px;">
                            {{ $legalitas['sip']['msg'] }}
                        </span>
                    </div>
                </div>
            </div>

        </div>
{{-- 4. DOWNLOAD CORNER (Split Kanan Kiri) --}}
<div class="card border-0 shadow-sm rounded-4 mt-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
        <div class="d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0">Download Corner</h6>
            <i class="bi bi-folder-symlink text-primary"></i>
        </div>
        <p class="text-muted extra-small mb-0">Dokumen pendukung Uji Kompetensi & Kredensialing.</p>
    </div>
    
    <div class="card-body p-0">
        
        {{-- Helper function untuk mendata item --}}
        @php
            $ukomItems = [
                ['title' => 'Form-01', 'desc' => 'Permohonan Asesmen SLG', 'slug' => '01. FORM-01 Permohonan Asesmen SLG'],
                ['title' => 'Form-02', 'desc' => 'Asesmen Mandiri SLG', 'slug' => '02. FORM-02 Asesmen Mandiri SLG'],
                ['title' => 'Form-03', 'desc' => 'Perencanaan Asesmen SLG', 'slug' => '03. FORM-03 Perencanaan asesmen SLG'],
                ['title' => 'Surat Rekomendasi', 'desc' => 'Ka Instalasi, KaRu, Satuan Kerja Bidwat', 'slug' => '04. Surat rekomendasi UKOM dari Ka Instalasi  KaRu  Satuan Kerja_Bidwat'],
            ];

            $kredensialItems = [
                ['title' => 'Form K.01', 'desc' => 'Aplikasi Kredensialing', 'slug' => '1. Form K.01 Aplikasi Kredensialing Perawat_Bidan'],
                ['title' => 'Form RK.01', 'desc' => 'Aplikasi Re-Kredensialing', 'slug' => '1. Form RK.01 Aplikasi Re-Kredensialing Perawat_Bidan'],
                ['title' => 'Form K.02', 'desc' => 'Catatan Etik Dan Disiplin', 'slug' => '2. Form K.02 Catatan Etik Dan Disiplin Perawat_Bidan'],
                ['title' => 'Form K.03', 'desc' => 'Rekomendasi Mitra Bestari', 'slug' => '3. Form K.03 Rekomendasi Mitra Bestari'],
                ['title' => 'Self Asesmen', 'desc' => 'Kredensial PK BK Umum', 'slug' => '4. Form self asesmen kredensial PK_BK_I umum'],
                ['title' => 'DRH', 'desc' => 'Daftar Riwayat Hidup', 'slug' => '5. FORM Daftar Riwayat Hidup'],
                ['title' => 'Surat Rekomendasi', 'desc' => 'Ka Instalasi, KaRu, Bidwat', 'slug' => '6.RekomKaru'],
                ['title' => 'Checklist', 'desc' => 'Check List Syarat Kelengkapan', 'slug' => '17. FORM Daftar Check List Syarat Kelengkapan Rekredensial dan Kredensialing'],
            ];
        @endphp

        {{-- BUNGKUS DENGAN ROW UNTUK MEMBAGI DUA KOLOM --}}
        <div class="row g-0">
            
            {{-- KOLOM KIRI: DOKUMEN UJI KOMPETENSI --}}
            <div class="col-md-6 border-end">
                <div class="bg-light px-4 py-2 border-top border-bottom">
                    <span class="fw-bold text-primary" style="font-size: 12px;">
                        <i class="bi bi-journal-check me-1"></i> Dokumen Uji Kompetensi
                    </span>
                </div>
                {{-- Samakan max-height agar tingginya seimbang --}}
                <div class="list-group list-group-flush" style="max-height: 450px; overflow-y: auto;">
                    @foreach($ukomItems as $item)
                    <div class="list-group-item py-3 px-3 border-0 border-bottom d-flex align-items-center">
                        <i class="bi bi-file-earmark-text text-secondary fs-5 me-2"></i>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark small">{{ $item['title'] }}</div>
                            <div class="text-muted" style="font-size: 10px;">{{ $item['desc'] }}</div>
                        </div>
                        
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle rounded-pill px-2 py-1" style="font-size: 11px;" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-download"></i> Unduh
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <a class="dropdown-item" href="{{ asset('templates/' . $item['slug'] . '.pdf') }}" target="_blank">
                                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i> PDF
                                    </a>
                                </li>
                                <li>
                                  
                                    @php
    // Cek apakah file .docx ada di folder public/templates/
    $pathDocx = public_path('templates/' . $item['slug'] . '.docx');
    $extension = file_exists($pathDocx) ? '.docx' : '.doc';
@endphp

<a class="dropdown-item" href="{{ asset('templates/' . $item['slug'] . $extension) }}" target="_blank">
   <i class="bi bi-file-earmark-word text-primary me-2"></i> Word
</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- KOLOM KANAN: DOKUMEN KREDENSIALING --}}
            <div class="col-md-6">
                <div class="bg-light px-4 py-2 border-top border-bottom">
                    <span class="fw-bold text-primary" style="font-size: 12px;">
                        <i class="bi bi-person-badge me-1"></i> Dokumen Kredensialing
                    </span>
                </div>
                <div class="list-group list-group-flush" style="max-height: 450px; overflow-y: auto;">
                    @foreach($kredensialItems as $item)
                    <div class="list-group-item py-3 px-3 border-0 border-bottom d-flex align-items-center">
                        <i class="bi bi-file-earmark-text text-secondary fs-5 me-2"></i>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark small">{{ $item['title'] }}</div>
                            <div class="text-muted" style="font-size: 10px;">{{ $item['desc'] }}</div>
                        </div>
                        
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle rounded-pill px-2 py-1" style="font-size: 11px;" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-download"></i> Unduh
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <a class="dropdown-item" href="{{ asset('templates/' . $item['slug'] . '.pdf') }}" target="_blank">
                                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i> PDF
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ asset('templates/' . $item['slug'] . '.docx') }}" target="_blank">
                                        <i class="bi bi-file-earmark-word text-primary me-2"></i> Word
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div> {{-- END ROW --}}

    </div>
    
    <div class="card-footer bg-light border-top py-2 px-4 rounded-bottom-4 text-center">
        <small class="text-muted fst-italic" style="font-size: 10px;">Klik tombol unduh untuk memilih format file</small>
    </div>
</div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Styling khusus agar lebih clean */
    body {
        background-color: #f8f9fa; /* Light Gray Background */
    }
    .stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        border: 1px solid rgba(0,0,0,0.02);
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
    }
    .icon-square {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .extra-small {
        font-size: 0.75rem;
    }
    .section-item {
        transition: background-color 0.2s;
    }
    .section-item:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush
<div class="modal fade" id="chatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-chat-custom">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            {{-- Header Chat --}}
            <div class="modal-header bg-primary text-white border-0 py-3 px-4">
                <div class="d-flex align-items-center">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="bi bi-person-fill fs-4"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0" id="chatTargetName">Admin Support</h6>
                        <small class="opacity-75" style="font-size: 10px;">
                            <span class="online-indicator me-1"></span> Aktif
                        </small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Area Pesan --}}
            <div class="modal-body p-0" style="background-color: #f8f9fa;">
                <div id="chatBox" class="chat-container p-4">
                    </div>
            </div>

            {{-- Input Balasan --}}
            <div class="modal-footer border-0 p-3 bg-white">
                <form action="{{ route('messages.send') }}" method="POST" class="w-100" id="chatForm">
                    @csrf
                    <input type="hidden" name="receiver_id" id="chat_receiver_id">
                    <div class="input-group shadow-sm" style="border-radius: 30px; overflow: hidden; border: 1px solid #e0e0e0;">
                        <input name="message" id="chatInput" class="form-control border-0 bg-white px-4 py-2" 
                               placeholder="Tulis balasan Anda..." required autocomplete="off">
                        <button class="btn btn-primary px-4" type="submit">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling Chat Box */
    .modal-chat-custom { max-width: 480px; }
    .chat-container {
        height: 400px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
        scroll-behavior: smooth;
    }
    .msg-bubble {
        max-width: 85%;
        padding: 10px 15px;
        font-size: 13px;
        line-height: 1.5;
        position: relative;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .msg-me {
        align-self: flex-end;
        background: #0d6efd;
        color: white;
        border-radius: 15px 15px 2px 15px;
    }
    .msg-other {
        align-self: flex-start;
        background: white;
        color: #333;
        border-radius: 15px 15px 15px 2px;
        border: 1px solid #efefef;
    }
    .msg-time { font-size: 9px; opacity: 0.6; display: block; margin-top: 4px; text-align: right; }
    .online-indicator { width: 8px; height: 8px; background: #4df14d; border-radius: 50%; display: inline-block; }

    /* Responsif HP (Full Screen) */
    @media (max-width: 576px) {
        .modal-chat-custom { margin: 0; max-width: 100%; height: 100%; }
        .modal-chat-custom .modal-content { height: 100vh; border-radius: 0; }
        .chat-container { height: calc(100vh - 150px); }
    }
</style>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function checkNewMessages() {
    if ($('#chatModal').is(':visible')) return;

    // Route ini harus mengembalikan apakah ada pesan admin yang belum dibaca
    fetch("{{ url('/messages/check-unread') }}") 
        .then(res => res.json())
        .then(data => {
            if (data.unread > 0) {
                // Ada pesan masuk! Refresh dashboard buat munculin card merah
                location.reload();
            }
        });
}

// Cek tiap 15 detik biar server ga bengkak
setInterval(checkNewMessages, 15000);
    const ctx = document.getElementById('cleanChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar', // Bar lebih mudah dibaca untuk clean UI
        data: {
            labels: ['Pend.', 'Latih', 'Kerja', 'Org', 'Jasa'],
            datasets: [{
                label: 'Jumlah Data',
                data: {!! json_encode($chartData['data']) !!},
                backgroundColor: '#0d6efd',
                borderRadius: 4,
                barThickness: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 4], drawBorder: false } },
                x: { grid: { display: false } }
            }
        }
    });
    
    function openChatModal(userId, userName) {
    document.getElementById('chatTargetName').innerText = userName;
    document.getElementById('chat_receiver_id').value = userId;
    const chatBox = document.getElementById('chatBox');
    
    // Loader sederhana
    chatBox.innerHTML = `
        <div class="text-center my-auto">
            <div class="spinner-border spinner-border-sm text-primary"></div>
            <p class="small text-muted mt-2">Sinkronisasi pesan...</p>
        </div>`;

    // Ambil history
    const url = "{{ url('/messages/history') }}/" + userId;

    fetch(url)
        .then(res => res.json())
        .then(data => {
            chatBox.innerHTML = '';
            if (data.length === 0) {
                chatBox.innerHTML = '<div class="text-center my-auto opacity-50 small">Belum ada obrolan.</div>';
                return;
            }

            data.forEach(msg => {
                const isMe = msg.sender_id == "{{ Auth::id() }}";
                const msgDiv = document.createElement('div');
                msgDiv.className = `msg-bubble ${isMe ? 'msg-me' : 'msg-other'}`;
                const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                msgDiv.innerHTML = `<div>${msg.message}</div><span class="msg-time">${time}</span>`;
                chatBox.appendChild(msgDiv);
            });
            
            // Geser ke pesan paling bawah
            chatBox.scrollTop = chatBox.scrollHeight;

            // --- LOGIKA HILANGKAN ALERT MERAH ---
            // Setelah dibuka, kita anggap sudah dibaca, hilangkan warna merah di dashboard
            const alertCard = document.querySelector('.border-danger');
            if(alertCard) {
                alertCard.classList.remove('border-danger', 'animate__pulse');
                alertCard.classList.add('border-primary');
                alertCard.querySelector('.btn-danger').className = 'btn btn-sm btn-primary rounded-pill px-3 shadow-sm';
                alertCard.querySelector('.bg-danger-subtle').className = 'icon-square bg-primary-subtle text-primary me-3';
                alertCard.querySelector('.bi-envelope-exclamation-fill').className = 'bi bi-chat-left-dots-fill';
            }
        })
        .catch(err => {
            chatBox.innerHTML = `<div class="text-center my-auto text-danger small">Gagal memuat pesan.</div>`;
        });
}
</script>
@endpush