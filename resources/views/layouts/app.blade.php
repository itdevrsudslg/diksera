<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'DIKSERA - Digitalisasi Kompetensi, Sertifikasi & Evaluasi Bidan dan Perawat' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">

    {{-- Fonts, Bootstrap & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <style>
        :root {
            --blue-main: #1d4ed8;
            --blue-soft: #e0edff;
            --blue-soft-2: #f3f6ff;
            --border-soft: #d0d7ee;
            --text-main: #111827;
            --text-muted: #6b7280;
            --sidebar-width: 260px;
            --sidebar-width-collapsed: 80px;
            --header-height: 70px;
            --transition-speed: 0.3s;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Poppins', system-ui, sans-serif;
            background: radial-gradient(circle at top, #e0ebff 0, #f9fbff 45%, #ffffff 100%);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* --- LAYOUT --- */
        .app-shell {
            min-height: 100vh;
            display: flex;
            gap: 16px;
            padding: 16px;
            position: relative;
        }

        .app-main {
            flex: 1;
            background: #ffffff;
            border: 1px solid var(--border-soft);
            border-radius: 24px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            width: 100%;
            overflow: hidden;
            transition: margin-left var(--transition-speed);
        }

        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        /* --- SIDEBAR --- */
        .app-sidebar {
            width: var(--sidebar-width);
            background: #ffffff;
            border: 1px solid var(--border-soft);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            padding: 18px 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            position: sticky;
            top: 16px;
            height: calc(100vh - 32px);
            border-radius: 24px;
            z-index: 100;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .brand-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-left: 4px;
            overflow: hidden;
        }

        .brand-logo {
            width: 40px;
            height: 40px;
            min-width: 40px;
            border-radius: 12px;
            background: radial-gradient(circle at 30% 0, #eff6ff, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }

        .brand-info {
            white-space: nowrap;
            transition: opacity 0.2s;
        }

        .brand-name {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-main);
        }

        .brand-caption {
            font-size: 10px;
            color: var(--text-muted);
            line-height: 1.2;
        }

        .nav-section-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9ca3af;
            margin: 10px 0 5px 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        /* --- NAVIGATION ITEMS --- */
        .nav-linkx,
        .nav-dropdown {
            display: flex;
            align-items: center;
            width: 100%;
            justify-content: flex-start;
            gap: 12px;
            font-size: 13px;
            padding: 10px 12px;
            border-radius: 14px;
            color: #4b5563;
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
            cursor: pointer;
            position: relative;
        }

        /* Ikon Menu */
        .nav-linkx i:not(.dropdown-icon),
        .nav-dropdown i:not(.dropdown-icon) {
            font-size: 18px;
            min-width: 24px;
            text-align: center;
            color: #6b7280;
        }

        /* Ikon Panah */
        .dropdown-icon {
            margin-left: auto;
            font-size: 12px;
            transition: transform 0.3s ease;
        }

        /* Hover & Active States */
        .nav-linkx:hover,
        .nav-dropdown:hover {
            background: #f3f6ff;
            color: #1d4ed8;
        }

        .nav-linkx:hover i,
        .nav-dropdown:hover i {
            color: #1d4ed8;
        }

        .nav-linkx.active {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: #fff;
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.25);
        }

        .nav-linkx.active i {
            color: #fff;
        }

        /* Submenu */
        .nav-submenu {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            padding-left: 20px;
            transform: translateY(-10px);
            transition: all 0.4s ease;
        }

        .nav-submenu.show {
            max-height: 500px;
            opacity: 1;
            transform: translateY(0);
            margin-top: 5px;
            display: block;
        }

        .nav-sublink {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 12px;
            padding: 8px 12px;
            border-radius: 12px;
            color: #4b5563;
            text-decoration: none;
            transition: all 0.2s;
        }

        .nav-sublink:hover {
            color: var(--blue-main);
            background: var(--blue-soft-2);
        }

        /* Badge & Footer */
        .badge-notification {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            font-size: 11px;
            padding: 3px 8px;
            min-width: 22px;
            border-radius: 12px;
            margin-left: auto;
            font-weight: 700;
            box-shadow: 0 4px 6px rgba(220, 38, 38, 0.4);
            border: 2px solid #fff;
            animation: pulse-red 1.5s infinite;
        }

        .sidebar-footer {
            margin-top: auto;
            font-size: 10px;
            color: #9ca3af;
            padding-left: 12px;
            white-space: nowrap;
        }

        @keyframes pulse-red {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 6px rgba(239, 68, 68, 0);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        /* --- DESKTOP (Min 992px) --- */
        @media (min-width: 992px) {
            .sidebar-wrapper {
                position: relative;
            }

            .sidebar-toggle-btn {
                position: absolute;
                top: 20px;
                right: -16px;
                width: 32px;
                height: 32px;
                background: #fff;
                border: 1px solid #d1d5db;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                z-index: 999;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transition: all .3s ease;
            }

            .sidebar-toggle-btn:hover {
                transform: scale(1.15);
                border-color: var(--blue-main);
            }

            .header-toggle,
            .mobile-overlay {
                display: none !important;
            }

            /* Collapsed State */
            .app-shell.is-collapsed .app-sidebar {
                width: var(--sidebar-width-collapsed);
                padding: 18px 8px;
            }

            .app-shell.is-collapsed .brand-info,
            .app-shell.is-collapsed .link-text,
            .app-shell.is-collapsed .nav-section-title,
            .app-shell.is-collapsed .sidebar-footer,
            .app-shell.is-collapsed .dropdown-icon {
                opacity: 0;
                width: 0;
                display: none;
            }

            .app-shell.is-collapsed .brand-row,
            .app-shell.is-collapsed .nav-linkx,
            .app-shell.is-collapsed .nav-dropdown {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }

            .app-shell.is-collapsed .nav-submenu {
                display: none !important;
            }

            .app-shell.is-collapsed .nav-linkx i {
                margin: 0;
            }

            .app-shell.is-collapsed .badge-notification {
                position: absolute;
                top: 6px;
                right: 6px;
                width: 10px;
                height: 10px;
                padding: 0;
                font-size: 0;
                min-width: auto;
                margin: 0;
            }
        }

        /* --- MOBILE (Max 991px) --- */
        @media (max-width: 991px) {
            .app-shell {
                display: block;
                padding: 0;
            }

            .app-sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                height: 100vh;
                width: 280px !important;
                border-radius: 0;
                z-index: 1051;
                transition: left 0.3s ease-in-out;
            }

            .app-sidebar.mobile-active {
                left: 0;
            }

            .sidebar-toggle-btn {
                display: none;
            }

            .header-toggle {
                display: block;
                font-size: 24px;
                cursor: pointer;
            }

            .app-main {
                border-radius: 0;
                border: none;
                min-height: 100vh;
                padding: 16px;
            }

            .mobile-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.4);
                z-index: 1040;
                display: none;
                backdrop-filter: blur(2px);
                transition: opacity 0.3s;
            }

            .mobile-overlay.show {
                display: block;
                opacity: 1;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    {{-- Overlay Gelap untuk Mobile --}}
    <div class="mobile-overlay" id="mobileOverlay" onclick="closeMobileSidebar()"></div>

    <div class="app-shell" id="appShell">

       <div class="sidebar-wrapper">
            <div class="sidebar-toggle-btn" onclick="toggleDesktopSidebar()">
                <i class="bi bi-chevron-left"></i>
            </div>

<aside class="app-sidebar" id="appSidebar">
    <div>
        {{-- BRAND LOGO --}}
        <div class="brand-row">
            <div class="brand-logo">
                <img src="{{ asset('icon.png') }}" alt="Logo">
            </div>
            <div class="brand-info">
                <div class="brand-name">DIKSERA</div>
                <div class="brand-caption">Digitalisasi Kompetensi, <br> Sertifikasi & Evaluasi Evaluasi Bidan dan Perawat</div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- MENU PERAWAT (ROLE: PERAWAT) --}}
        {{-- ========================================= --}}
        @if (Auth::check() && Auth::user()->role === 'perawat')
            <div class="nav-section-title">Umum</div>
            <a href="{{ route('dashboard') }}"
                class="nav-linkx {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i> <span class="link-text">Dashboard</span>
            </a>

            <div class="nav-section-title">Menu Utama</div>

            {{-- DROPDOWN: PELENGKAPAN DATA (MODUL 3) --}}
            <div class="nav-linkx nav-dropdown {{ request()->routeIs('perawat.drh') || request()->routeIs('perawat.pelatihan.*') || request()->routeIs('perawat.pendidikan.*') || request()->routeIs('perawat.pekerjaan.*') || request()->routeIs('perawat.keluarga.*') || request()->routeIs('perawat.organisasi.*') || request()->routeIs('perawat.tandajasa.*') ? 'active' : '' }}" 
                data-dropdown="#submenu-master">
                <i class="bi bi-folder-fill"></i>
                <span class="link-text">Profil</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </div>

            <div id="submenu-master" class="nav-submenu {{ request()->routeIs('perawat.drh') || request()->routeIs('perawat.pelatihan.*') || request()->routeIs('perawat.pendidikan.*') || request()->routeIs('perawat.pekerjaan.*') || request()->routeIs('perawat.keluarga.*') || request()->routeIs('perawat.organisasi.*') || request()->routeIs('perawat.tandajasa.*') ? 'show' : '' }}">
                <a href="{{ route('perawat.drh') }}" class="nav-linkx {{ request()->routeIs('perawat.drh') ? 'active' : '' }}">
                    <i class="bi bi-person-vcard-fill"></i> DRH & Profil
                </a>
             
             <a href="{{ route('perawat.kunjungan.index') }}" class="nav-linkx {{ request()->routeIs('perawat.kunjungan.*') ? 'active' : '' }}">
    <i class="bi bi-airplane-fill"></i> Kunjungan
</a>
                <a href="{{ route('perawat.pendidikan.index') }}" class="nav-linkx {{ request()->routeIs('perawat.pendidikan.*') ? 'active' : '' }}">
                    <i class="bi bi-book-fill"></i> Pendidikan
                </a>
                <a href="{{ route('perawat.pekerjaan.index') }}" class="nav-linkx {{ request()->routeIs('perawat.pekerjaan.*') ? 'active' : '' }}">
                    <i class="bi bi-briefcase-fill"></i> Pekerjaan
                </a>
                <a href="{{ route('perawat.keluarga.index') }}" class="nav-linkx {{ request()->routeIs('perawat.keluarga.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Keluarga
                </a>
                <a href="{{ route('perawat.organisasi.index') }}" class="nav-linkx {{ request()->routeIs('perawat.organisasi.*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3-fill"></i> Organisasi
                </a>
                <a href="{{ route('perawat.tandajasa.index') }}" class="nav-linkx {{ request()->routeIs('perawat.tandajasa.*') ? 'active' : '' }}">
                    <i class="bi bi-star-fill"></i> Tanda Jasa
                </a>
            </div>

            {{-- DROPDOWN: DOKUMEN (MODUL 4) --}}
            @php
                $isDokumenActive = request()->routeIs('perawat.str.*') || request()->routeIs('perawat.sip.*') || request()->routeIs('perawat.tambahan.*');
            @endphp

            <div class="nav-linkx nav-dropdown {{ $isDokumenActive ? 'active' : '' }}" data-dropdown="#submenu-dokumen">
                <i class="bi bi-folder-fill"></i>
                <span class="link-text">Dokumen</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </div>

            <div id="submenu-dokumen" class="nav-submenu {{ $isDokumenActive ? 'show' : '' }}">
                <a href="{{ route('perawat.str.index') }}" class="nav-linkx {{ request()->routeIs('perawat.str.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text-fill"></i> Dokumen STR
                </a>
                <a href="{{ route('perawat.sip.index') }}" class="nav-linkx {{ request()->routeIs('perawat.sip.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-check-fill"></i> Dokumen SIP
                </a>
                 <a href="{{ route('perawat.arsip.index') }}" class="nav-linkx {{ request()->routeIs('perawat.arsip.*') ? 'active' : '' }}">
    <i class="bi bi-archive-fill"></i>Data Arsip
</a>
                <a href="{{ route('perawat.tambahan.index') }}" class="nav-linkx {{ request()->routeIs('perawat.tambahan.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-plus-fill"></i> Data Tambahan
                </a>
            </div>

            {{-- ============================================================== --}}
            {{-- DROPDOWN: PENGAJUAN LISENSI (LOGIKA KREDENSIALING) --}}
            {{-- ============================================================== --}}
            @php
                // Cek apakah User sudah pernah Lulus Uji Kompetensi (pg_interview)
                $hasUjiKomp = \App\Models\PerawatLisensi::where('user_id', Auth::id())
                    ->where('metode', 'pg_interview')
                    ->where('status', 'active') // Pastikan statusnya aktif/lulus
                    ->exists();

                // Logic agar menu tetap terbuka saat membuka create ATAU index
                $isKredensialActive = request()->routeIs('perawat.lisensi.create') || request()->routeIs('perawat.lisensi.index');
            @endphp

            <div class="nav-linkx nav-dropdown {{ $isKredensialActive ? 'active' : '' }}" data-dropdown="#submenu-kredensial">
                <i class="bi bi-file-earmark-arrow-up-fill"></i>
                <span class="link-text">Pengajuan Lisensi</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </div>

<div id="submenu-kredensial" class="nav-submenu {{ $isKredensialActive || request()->routeIs('oppe.*') ? 'show' : '' }}">

  
    
     <a href="{{ route('perawat.lisensi.create', 'pg_interview') }}" class="nav-linkx">
        <i class="bi bi-clipboard-data-fill"></i> Uji Kompetensi
    </a>

    {{-- 2. Kredensialing --}}
    @if ($hasUjiKomp)
        <a href="{{ route('perawat.lisensi.create', 'interview_only') }}" class="nav-linkx">
            <i class="bi bi-person-check-fill"></i> Kredensialing
        </a>
    @endif

  {{-- 3. Pengajuan OPPE (Untuk Staf Pelaksana) --}}
    <div class="nav-section-title" style="margin-top: 15px;">Manajemen OPPE</div>
    
@php
        $oppeStatus = Auth::user()->profile->oppe_status ?? 'auto';
        $isOppeActive = false;
        $tooltipText = '';
        $today = now();
        
        // Cek Pembukaan Serentak (7 - 10 Januari)
        $isGlobalPeriod = ($today->month == 7 && $today->day >= 1 && $today->day <= 30);
        $isAutoOpen = $isGlobalPeriod;

        // Cek Pembukaan by RKK
        $rkk = \App\Models\PerawatLisensi::where('user_id', Auth::id())
            ->where('status', 'active') 
            ->orderBy('tgl_terbit', 'desc')
            ->first();

        if ($rkk && $rkk->tgl_terbit) {
            $monthTerbit = \Carbon\Carbon::parse($rkk->tgl_terbit)->month;
            $monthAfter = ($monthTerbit % 12) + 1; 

            if ($today->month == $monthTerbit || $today->month == $monthAfter) {
                $isAutoOpen = true;
            }
        }

        // Tentukan Akses
        if ($oppeStatus === 'on') {
            $isOppeActive = true;
            $tooltipText = 'Akses OPPE dibuka manual oleh Admin.';
        } elseif ($oppeStatus === 'off') {
            $isOppeActive = false;
        } else {
            $isOppeActive = $isAutoOpen;
            
            // Set Tooltip Text jika Auto
            if ($isAutoOpen) {
                if ($isGlobalPeriod) {
                    $tooltipText = "Periode Pengajuan OPPE Serentak (7-10 Januari)";
                } else {
                    $tglTerbit = \Carbon\Carbon::parse($rkk->tgl_terbit)->format('d M Y');
                    $tglBatas = \Carbon\Carbon::parse($rkk->tgl_terbit)->addMonth()->endOfMonth()->translatedFormat('d F Y');
                    $tooltipText = "Terbit RKK: $tglTerbit | Batas Pengajuan: $tglBatas";
                }
            }
        }
    @endphp

    @if($isOppeActive)
        <a href="{{ route('perawat.oppe.create') }}" 
           class="nav-linkx {{ request()->routeIs('perawat.oppe.create') ? 'active' : '' }}"
           data-bs-toggle="tooltip" 
           data-bs-placement="right" 
           title="{{ $tooltipText }}">
            <i class="bi bi-file-earmark-plus"></i> 
            <span class="link-text">Pengajuan OPPE</span>
            
            @if($oppeStatus === 'on')
                <span class="badge bg-success link-text ms-auto" style="font-size: 9px; padding: 3px 6px;">ON</span>
            @else
                <i class="bi bi-info-circle text-muted ms-auto link-text" style="font-size: 14px;"></i>
            @endif
        </a>
    @endif

    {{-- 2. Daftar Penilaian / Kotak Masuk (Untuk Self, Peer, dan Karu) --}}
    <a href="{{ route('perawat.oppe.list_evaluasi') }}" class="nav-linkx {{ request()->routeIs('perawat.oppe.list_evaluasi') ? 'active' : '' }}">
        <i class="bi bi-inbox-fill "></i> 
        <span class="link-text">Kotak Masuk OPPE</span>
    </a>
    {{-- 4. EVALUASI BAWAHAN (Hanya untuk Karu/Kasi) --}}
    @php
        $userRole = Auth::user()->profile->jabatan_struktural ?? 'pelaksana';
    @endphp
    
    @if(in_array($userRole, ['kepala_ruangan', 'kepala_seksi']))
        <div class="nav-section-title" style="margin-top: 15px;">Evaluasi Bawahan</div>
        <a href="{{ route('perawat.oppe.list_evaluasi') }}" class="nav-linkx {{ request()->routeIs('oppe.list_evaluasi') ? 'active' : '' }}">
            <i class="bi bi-check2-square "></i> 
            <span class="link-text">Evaluasi OPPE</span>
        </a>
    @endif
</div>
            {{-- ============================================================== --}}


            {{-- LOGBOOK HARIAN --}}
            <div class="nav-section-title">Logbook</div>
            <a href="{{ route('perawat.logbook.index') }}"
                class="nav-linkx {{ request()->routeIs('perawat.logbook.index', 'perawat.logbook.create', 'perawat.logbook.edit', 'perawat.logbook.rekap') ? 'active' : '' }}">
                <i class="bi bi-journal-medical"></i>
                <span class="link-text">Logbook Harian</span>
            </a>
            @if (in_array(Auth::user()->profile->jabatan_struktural ?? 'pelaksana', ['kepala_ruangan', 'kepala_seksi']))
                <a href="{{ route('perawat.logbook.validasi') }}"
                    class="nav-linkx {{ request()->routeIs('perawat.logbook.validasi') ? 'active' : '' }}">
                    <i class="bi bi-patch-check"></i>
                    <span class="link-text">{{ (Auth::user()->profile->jabatan_struktural ?? '') === 'kepala_seksi' ? 'Rekap Logbook' : 'Validasi Logbook' }}</span>
                </a>
            @endif

            {{-- UJIAN & EVALUASI --}}
            <a href="{{ route('perawat.ujian.index') }}"
                class="nav-linkx {{ request()->routeIs('perawat.ujian.*') ? 'active' : '' }} nav-with-badge">
                <i class="bi bi-clipboard-check-fill"></i>
                <span class="link-text">Ujian & Evaluasi</span>
                @if (isset($ujianActiveCount) && $ujianActiveCount > 0)
                    <span class="badge-notification">{{ $ujianActiveCount }}</span>
                @endif
            </a>

            <div class="nav-section-title">Riwayat</div>

            <a href="{{ route('perawat.pengajuan.index') }}"
                class="nav-linkx {{ request()->routeIs('perawat.pengajuan.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-arrow-up-fill"></i>
                <span class="link-text">Status Pengajuan</span>
            </a>

            <div class="nav-section-title">Lainnya</div>
            <a href="{{ route('perawat.telegram.link') }}" class="nav-linkx {{ request()->routeIs('perawat.telegram.link') ? 'active' : '' }}">
                <i class="bi bi-telegram"></i>
                <span class="link-text">Notifikasi Telegram</span>
            </a>
            <a href="{{ route('panduan') }}" class="nav-linkx {{ request()->routeIs('panduan') ? 'active' : '' }}">
                <i class="bi bi-book"></i> <span class="link-text">Buku Panduan</span>
            </a>
        @endif


        {{-- ========================================= --}}
        {{-- MENU PEWAWANCARA & ADMIN (TETAP SAMA) --}}
        {{-- ========================================= --}}
     {{-- ========================================= --}}
        {{-- MENU PEWAWANCARA --}}
        {{-- ========================================= --}}
        @if (Auth::check() && Auth::user()->role === 'pewawancara')
             <div class="nav-section-title">Menu Utama</div>
             <a href="{{ route('dashboard.pewawancara') }}" class="nav-linkx {{ request()->routeIs('dashboard.pewawancara') ? 'active' : '' }}">
                 <i class="bi bi-grid-fill"></i> <span class="link-text">Dashboard & Tugas</span>
             </a>
             <a href="{{ route('pewawancara.antrian') }}" class="nav-linkx {{ request()->routeIs('pewawancara.antrian') ? 'active' : '' }}">
                 <i class="bi bi-list-ul"></i> <span class="link-text">Antrian</span>
             </a>
             <a href="{{ route('pewawancara.riwayat') }}" class="nav-linkx {{ request()->routeIs('pewawancara.riwayat') ? 'active' : '' }}">
                 <i class="bi bi-clock-history"></i> <span class="link-text">Riwayat Penilaian</span>
             </a>
             <div class="nav-section-title">Lainnya</div>
             <a href="{{ route('pewawancara.settings') }}" class="nav-linkx {{ request()->routeIs('pewawancara.settings') ? 'active' : '' }}">
                 <i class="bi bi-gear-fill"></i> <span class="link-text">Pengaturan</span>
             </a>
            <a href="{{ route('panduan') }}" class="nav-linkx {{ request()->routeIs('panduan') ? 'active' : '' }}">
                <i class="bi bi-book"></i> <span class="link-text">Buku Panduan</span>
            </a>
        @endif

        {{-- ========================================= --}}
        {{-- MENU ADMIN --}}
        {{-- ========================================= --}}
        @if (Auth::check() && Auth::user()->role === 'admin')
            <div class="nav-section-title">Admin Panel</div>
            <a href="{{ route('dashboard.admin') }}" class="nav-linkx {{ request()->routeIs('dashboard.admin') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> <span class="link-text">Dashboard</span>
            </a>
            <a href="{{ route('admin.manajemen_akun.index') }}" class="nav-linkx {{ request()->routeIs('admin.manajemen_akun*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> <span class="link-text">Manajemen Akun</span>
            </a>
            <a href="{{ route('admin.perawat.index') }}" class="nav-linkx {{ request()->routeIs('admin.perawat.*') ? 'active' : '' }}">
                <i class="bi bi-person-lines-fill"></i> <span class="link-text">Bidan Dan Perawat</span>
            </a>

            <div class="nav-section-title">Kredensialing</div>
            <a href="{{ route('admin.pengajuan.index') }}" class="nav-linkx {{ request()->routeIs('admin.pengajuan.*') ? 'active' : '' }}">
                <i class="bi bi-check-square-fill"></i> <span class="link-text">Approval Pengajuan</span>
            </a>
            <a href="{{ route('admin.lisensi_pg_interview.index') }}" class="nav-linkx {{ request()->routeIs('admin.lisensi_pg_interview.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> <span class="link-text">Uji Kompetensi</span>
            </a>
            <a href="{{ route('admin.lisensi_interview_only.index') }}" class="nav-linkx {{ request()->routeIs('admin.lisensi_interview_only.*') ? 'active' : '' }}">
                <i class="bi bi-person-video2"></i> <span class="link-text">Kredensialing</span>
            </a>
             <a href="{{ route('perawat.oppe.list_evaluasi') }}" class="nav-linkx {{ request()->routeIs('perawat.oppe.list_evaluasi') ? 'active' : '' }}">
                <i class="bi bi-inbox-fill"></i> <span class="link-text">OPPE</span>
            </a>

            <a href="{{ route('admin.penandatangan.index') }}" class="nav-linkx {{ request()->routeIs('admin.penandatangan.*') ? 'active' : '' }}">
                <i class="bi bi-pen"></i> <span class="link-text">Penanda Tangan</span>
            </a>

            <div class="nav-section-title">Logbook</div>
            <a href="{{ route('admin.logbook.rekap') }}" class="nav-linkx {{ request()->routeIs('admin.logbook.rekap') ? 'active' : '' }}">
                <i class="bi bi-journal-medical"></i> <span class="link-text">Rekap Logbook</span>
            </a>
            <a href="{{ route('admin.logbook.periode') }}" class="nav-linkx {{ request()->routeIs('admin.logbook.periode') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> <span class="link-text">Periode Logbook</span>
            </a>
            <a href="{{ route('admin.logbook.tindakan') }}" class="nav-linkx {{ request()->routeIs('admin.logbook.tindakan') ? 'active' : '' }}">
                <i class="bi bi-list-check"></i> <span class="link-text">Master Tindakan</span>
            </a>

            <div class="nav-section-title">Manajemen Ujian</div>
            <a href="{{ route('admin.penanggung-jawab.index') }}" class="nav-linkx {{ request()->routeIs('admin.penanggung-jawab*') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i> <span class="link-text">Penanggung Jawab</span>
            </a>
            <a href="{{ route('admin.form.index') }}" class="nav-linkx {{ request()->routeIs('admin.form.*') ? 'active' : '' }}">
                <i class="bi bi-ui-checks"></i> <span class="link-text">Form Ujian</span>
            </a>
            <a href="{{ route('admin.bank-soal.index') }}" class="nav-linkx {{ request()->routeIs('admin.bank-soal.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i> <span class="link-text">Bank Soal</span>
            </a>
            <a href="{{ route('admin.bank-item-rkk.index') }}" class="nav-linkx {{ request()->routeIs('admin.bank-item-rkk.*') ? 'active' : '' }}">
                <i class="bi bi-card-list"></i> <span class="link-text">Bank Item RKK</span>
            </a>
            
            <div class="nav-section-title">Lainnya</div>
            <a href="{{ route('admin.profile.index') }}" class="nav-linkx {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i> <span class="link-text">Pengaturan</span>
            </a>
            <a href="{{ route('panduan') }}" class="nav-linkx {{ request()->routeIs('panduan') ? 'active' : '' }}">
                <i class="bi bi-book"></i> <span class="link-text">Buku Panduan</span>
            </a>
        @endif
    </div>

    <div class="sidebar-footer">
        &copy; {{ date('Y') }} DIKSERA<br>
        <span>Komite Keperawatan <br>RSUD Simpang Lima Gumul</span>
    </div>
</aside>
        </div>

        <main class="app-main">
            <div class="main-header">
                <div class="d-flex align-items-center gap-3">
                    {{-- Tombol Hamburger Mobile --}}
                    <i class="bi bi-list header-toggle" onclick="openMobileSidebar()"></i>

                    <div>
                        <div class="page-title">{{ $pageTitle ?? 'Dashboard' }}</div>
                        @isset($pageSubtitle)
                            <div class="page-subtitle">{{ $pageSubtitle }}</div>
                        @endisset
                    </div>
                </div>

               @php
    $user = Auth::user();
    
    // Definisikan pemetaan nama role di sini
    $roleMap = [
        'pewawancara' => ['ASESOR', '#ede9fe', '#5b21b6'],
        'admin'       => ['ADMIN', '#fee2e2', '#991b1b'],
    ];

    // Akun perawat/bidan: tampilkan jabatan struktural (Karu/Kasi/Komite) atau profesinya
    if ($user && $user->role === 'perawat') {
        $jabatanStruktural = optional($user->profile)->jabatan_struktural;
        $profesi = strtoupper(optional($user->profile)->profesi ?: 'Perawat');
        $roleMap['perawat'] = [
            'kepala_ruangan' => ['KARU', '#dbeafe', '#1e40af'],
            'kepala_seksi'   => ['KASI', '#dcfce7', '#166534'],
            'komite'         => ['KOMITE', '#fef3c7', '#92400e'],
        ][$jabatanStruktural] ?? [$profesi, $profesi === 'BIDAN' ? '#fce7f3' : '#e0f2fe', $profesi === 'BIDAN' ? '#9d174d' : '#075985'];
    }

    [$displayName, $badgeBg, $badgeFg] = $roleMap[optional($user)->role] ?? [strtoupper(optional($user)->role), '#f1f5f9', '#475569'];
@endphp

@if ($user)
    <div class="d-flex align-items-center gap-2">
        <div class="user-pill d-none d-md-flex">
            <span class="user-dot"></span>
            <span class="fw-medium">{{ $user->name }}</span>
            <span class="ms-2" style="font-size:10.5px;font-weight:700;letter-spacing:.4px;padding:3px 9px;border-radius:20px;background:{{ $badgeBg }};color:{{ $badgeFg }};">{{ $displayName }}</span>
        </div>

        <form method="GET" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
@endif
            </div>

            <div class="main-body">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    @if (session('swal'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire(@json(session('swal')));
            });
        </script>
    @endif

    <script>
        const appShell = document.getElementById('appShell');
        const appSidebar = document.getElementById('appSidebar');
        const mobileOverlay = document.getElementById('mobileOverlay');
        const STORAGE_KEY = 'diksera_sidebar_state';

        // 1. Load Desktop State
        if (window.innerWidth > 991) {
            const savedState = localStorage.getItem(STORAGE_KEY);
            if (savedState === 'collapsed') {
                appShell?.classList.add('is-collapsed');
            }
        }

        // 2. Desktop Toggle Logic
        function toggleDesktopSidebar() {
            appShell.classList.toggle('is-collapsed');
            if (appShell.classList.contains('is-collapsed')) {
                localStorage.setItem(STORAGE_KEY, 'collapsed');
            } else {
                localStorage.setItem(STORAGE_KEY, 'expanded');
            }
        }

        // 3. Mobile Toggle Logic
        function openMobileSidebar() {
            appSidebar.classList.add('mobile-active');
            mobileOverlay.classList.add('show');
        }

        function closeMobileSidebar() {
            appSidebar.classList.remove('mobile-active');
            mobileOverlay.classList.remove('show');
        }

        window.addEventListener('resize', () => {
            if (window.innerWidth > 991) {
                closeMobileSidebar();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-dropdown]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();

                    const targetSelector = btn.dataset.dropdown;
                    const target = document.querySelector(targetSelector);

                    if (target) {
                        target.classList.toggle('show');
                    } else {
                        console.warn('Target dropdown tidak ditemukan:', targetSelector);
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
