@extends('layouts.app')

@php
    $pageTitle = 'Detail DRH Perawat';
    $pageSubtitle = 'Data lengkap Daftar Riwayat Hidup ' . $user->name;
@endphp

@section('title', 'Detail Perawat – Admin DIKSERA')

@push('styles')
    <style>
        /* Global Card Style */
      /* Global Card Style */
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-soft);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 24px;
        }
        /* Profile Specific */
        .profile-img-box {
            width: 140px;
            height: 180px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border-soft);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0; /* Mencegah foto menyusut di layar HP */
        }

        .profile-img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-avatar-fallback {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: 600;
            color: var(--blue-main);
            background: var(--blue-soft);
        }

        /* Label & Value Typography */
        .data-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 2px;
        }

        .data-value {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-main);
            word-wrap: break-word; /* Mencegah teks meluber */
        }

        /* Section Headers */
        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--blue-main);
            margin-bottom: 16px;
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

        /* Responsive Table Support untuk file _section.blade.php */
        .content-card .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .content-card .table-custom {
            min-width: 500px; /* Batas agar tabel tidak terlalu menyusut */
        }

        /* Responsiveness untuk layar kecil (HP) */
        @media (max-width: 768px) {
            .profile-header-wrapper {
                text-align: center;
                justify-content: center;
            }
            .profile-badges-wrapper {
                justify-content: center;
            }
            .col-md-auto {
                display: flex;
                justify-content: center;
                margin-bottom: 16px;
            }
        }
    </style>
@endpush

@section('content')

    {{-- Tombol Kembali --}}
    <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
        <a href="{{ route('admin.perawat.timeline', $user->id) }}" class="btn btn-sm btn-outline-primary px-3"
            style="border-radius: 8px; font-size: 12px;">
            <i class="bi bi-clock-history"></i> Riwayat Kredensialing
        </a>
        <a href="{{ route('admin.perawat.index') }}" class="btn btn-sm btn-outline-secondary px-3"
            style="border-radius: 8px; font-size: 12px;">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>
    
    {{-- 1. HEADER PROFIL UTAMA --}}
    <div class="content-card mb-4">
        <div class="row g-4">
            {{-- Kolom Foto --}}
            <div class="col-md-auto">
                <div class="profile-img-box">
                    @if ($profile && $profile->foto_3x4)
                        <img src="{{ asset('storage/' . $profile->foto_3x4) }}" alt="Foto">
                    @else
                        <div class="profile-avatar-fallback">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Kolom Info --}}
            <div class="col-md profile-header-wrapper">
                <div class="mb-3">
                    <h4 class="mb-1 fw-bold">{{ $user->name }}</h4>
                    <div class="d-flex flex-wrap gap-2 mt-2 profile-badges-wrapper">
                        {{-- Badge Profesi --}}
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 px-3 rounded-pill">
                            {{ optional($profile)->profesi ?? 'Perawat' }}
                        </span>
                        {{-- Badge Status Kepegawaian --}}
                        @if(optional($profile)->status_kepegawaian)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-3 rounded-pill">
                                {{ $profile->status_kepegawaian }}
                            </span>
                        @endif
                    </div>
                </div>

                <hr class="border-light my-3">

                <div class="row g-3 text-start">
                    {{-- A. KOLOM KIRI: Identitas & Kepegawaian --}}
                    <div class="col-md-6">
                        
                        {{-- IDENTITAS --}}
                        <h6 class="text-secondary fw-bold small mb-3 border-bottom pb-1">IDENTITAS UTAMA</h6>
                        
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="data-label">NIK</div>
                                <div class="data-value font-monospace">{{ optional($profile)->nik ?? '-' }}</div>
                            </div>
                            <div class="col-6 mb-3">
                                {{-- Logika Tampilan NIP / NIRP --}}
                                @if(optional($profile)->nip)
                                    <div class="data-label">NIP</div>
                                    <div class="data-value font-monospace">{{ $profile->nip }}</div>
                                @elseif(optional($profile)->nirp)
                                    <div class="data-label">NIRP</div>
                                    <div class="data-value font-monospace">{{ $profile->nirp }}</div>
                                @else
                                    <div class="data-label">NIP/NIRP</div>
                                    <div class="data-value">-</div>
                                @endif
                            </div>
                        </div>

                        {{-- KEPEGAWAIAN --}}
                        <h6 class="text-secondary fw-bold small mb-3 mt-2 border-bottom pb-1">DATA JABATAN</h6>

                        <div class="mb-3">
                            <div class="data-label">Jabatan Saat Ini</div>
                            <div class="data-value text-primary fw-bold">{{ optional($profile)->jabatan ?? '-' }}</div>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="data-label">Pangkat</div>
                                <div class="data-value">{{ optional($profile)->pangkat ?? '-' }}</div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="data-label">Golongan</div>
                                <div class="data-value">{{ optional($profile)->golongan ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="data-label">Tipe Perawat / KFK</div>
                            <div class="data-value">
                                @if(!empty($profile->type_perawat) && is_array($profile->type_perawat))
                                    @foreach($profile->type_perawat as $tipe)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 me-1 mb-1" style="font-weight: 500;">
                                            {{ $tipe }}
                                        </span>
                                    @endforeach
                                @elseif(!empty($profile->type_perawat) && is_string($profile->type_perawat))
                                     {{ $profile->type_perawat }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        
                        {{-- TAMBAHAN: DATA TMT & MASA KERJA --}}
<h6 class="text-secondary fw-bold small mb-3 mt-4 border-bottom pb-1">TMT & MASA KERJA</h6>
<div class="row">
    <div class="col-6 mb-3">
        <div class="data-label">TMT CPNS / PNS</div>
        <div class="data-value text-primary">
            {{ optional($profile)->tmt_cpns ? date('d M Y', strtotime($profile->tmt_cpns)) : '-' }} / 
            {{ optional($profile)->tmt_pns ? date('d M Y', strtotime($profile->tmt_pns)) : '-' }}
        </div>
    </div>
    <div class="col-6 mb-3">
        <div class="data-label">TMT Jabatan Saat Ini</div>
        <div class="data-value">{{ optional($profile)->tmt_jabatan ? date('d M Y', strtotime($profile->tmt_jabatan)) : '-' }}</div>
    </div>
    <div class="col-12 mb-3">
        <div class="data-label">Masa Kerja Golongan</div>
        <div class="data-value">{{ optional($profile)->masa_kerja_tahun ?? '0' }} Tahun, {{ optional($profile)->masa_kerja_bulan ?? '0' }} Bulan</div>
    </div>
</div>

{{-- TAMBAHAN: SURAT KETERANGAN --}}
<h6 class="text-secondary fw-bold small mb-3 mt-2 border-bottom pb-1">SURAT KETERANGAN</h6>
<div class="mb-3">
    <div class="data-label">SKCK</div>
    <div class="data-value">{{ optional($profile)->skck_nomor ?? '-' }} <br><small class="text-muted">{{ optional($profile)->skck_pejabat ?? '' }}</small></div>
</div>
<div class="mb-3">
    <div class="data-label">Keterangan Sehat</div>
    <div class="data-value">{{ optional($profile)->sehat_nomor ?? '-' }} <br><small class="text-muted">{{ optional($profile)->sehat_pejabat ?? '' }}</small></div>
</div>
                    </div>

                    {{-- B. KOLOM KANAN: Biodata & Kontak --}}
                    <div class="col-md-6">
                        <h6 class="text-secondary fw-bold small mb-3 border-bottom pb-1">BIODATA & KONTAK</h6>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="data-label">Tempat Lahir</div>
                                <div class="data-value">{{ optional($profile)->tempat_lahir ?? '-' }}</div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="data-label">Tanggal Lahir</div>
                                <div class="data-value">
                                    {{ optional($profile)->tanggal_lahir ? date('d M Y', strtotime($profile->tanggal_lahir)) : '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="data-label">Jenis Kelamin</div>
                                <div class="data-value">
                                    @if(optional($profile)->jenis_kelamin == 'L') Laki-laki 
                                    @elseif(optional($profile)->jenis_kelamin == 'P') Perempuan 
                                    @else - @endif
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="data-label">Agama</div>
                                <div class="data-value">{{ optional($profile)->agama ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="data-label">Alamat Domisili</div>
                            <div class="data-value" style="line-height: 1.4;">{{ optional($profile)->alamat ?? '-' }}</div>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="data-label">No. HP / WA</div>
                                <div class="data-value text-success">
                                    @if(optional($profile)->no_hp)
                                        @php
                                            $nomor_bersih = preg_replace('/[^0-9]/', '', $profile->no_hp);
                                            if (substr($nomor_bersih, 0, 1) == '0') {
                                                $nomor_bersih = '62' . substr($nomor_bersih, 1);
                                            }
                                        @endphp
                                        <a href="https://wa.me/{{ $nomor_bersih }}" target="_blank" class="text-success text-decoration-none">
                                            <i class="bi bi-whatsapp me-1"></i> {{ $profile->no_hp }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="data-label">Status Nikah</div>
                                <div class="data-value">{{ optional($profile)->status_perkawinan ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="data-label">Fisik (TB / BB)</div>
                                <div class="data-value">
                                    {{ optional($profile)->tinggi_badan ?? '-' }} cm / 
                                    {{ optional($profile)->berat_badan ?? '-' }} kg
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="data-label">Gol. Darah</div>
                                <div class="data-value">{{ optional($profile)->golongan_darah ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. DETAIL SECTIONS (Grid Layout) --}}
    <div class="row g-4">
   {{-- Kolom Kiri --}}
<div class="col-lg-6 d-flex flex-column gap-4">
    @include('admin.perawat._section', [
        'title' => 'Riwayat Pendidikan',
        'icon' => 'bi-mortarboard',
        'data' => $pendidikan,
        'cols' => ['Jenjang' => 'jenjang', 'Institusi' => 'nama_institusi', 'Kepsek/Rektor' => 'nama_kepala_institusi', 'Lulus' => 'tahun_lulus'],
    ])

    @include('admin.perawat._section', [
        'title' => 'Kursus / Pelatihan',
        'icon' => 'bi-journal-bookmark',
        'data' => $pelatihan,
        'cols' => ['Pelatihan' => 'nama_pelatihan', 'Penyelenggara' => 'penyelenggara', 'Tahun' => 'tanggal_mulai'],
    ])

    @include('admin.perawat._section', [
        'title' => 'Riwayat Keluarga',
        'icon' => 'bi-people',
        'data' => $keluarga,
        'cols' => ['Hubungan' => 'hubungan', 'Nama' => 'nama', 'L/P' => 'jenis_kelamin', 'Pekerjaan' => 'pekerjaan'],
    ])
</div>

{{-- Kolom Kanan --}}
<div class="col-lg-6 d-flex flex-column gap-4">
    @include('admin.perawat._section', [
        'title' => 'Riwayat Pekerjaan & SK',
        'icon' => 'bi-briefcase',
        'data' => $pekerjaan,
        'cols' => ['Instansi' => 'nama_instansi', 'Jabatan' => 'jabatan', 'Gol. Ruang' => 'gol_ruang', 'No SK' => 'nomor_sk'],
    ])

    @include('admin.perawat._section', [
        'title' => 'Pengalaman Organisasi',
        'icon' => 'bi-diagram-3',
        'data' => $organisasi,
        'cols' => ['Organisasi' => 'nama_organisasi', 'Jabatan' => 'jabatan', 'Masa' => 'masa_organisasi'],
    ])

    @include('admin.perawat._section', [
        'title' => 'Tanda Jasa / Penghargaan',
        'icon' => 'bi-trophy',
        'data' => $tandajasa,
        'cols' => ['Penghargaan' => 'nama_penghargaan', 'Tahun' => 'tahun', 'Instansi' => 'instansi_pemberi'],
    ])
</div>
    </div>

@endsection