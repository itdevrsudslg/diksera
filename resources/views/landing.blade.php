@extends('layouts.landing', ['title' => 'DIKSERA – Digitalisasi Kompetensi Evaluasi Bidan dan Perawat'])

@push('styles')
<style>
    /* ==== GLOBAL & ANIMATIONS ==== */
    :root {
        --primary: #2563eb;
        --primary-light: #60a5fa;
        --secondary: #38bdf8;
        --dark: #0f172a;
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(255, 255, 255, 0.4);
    }

    html { scroll-behavior: smooth; }
    
    body {
        margin: 0;
        padding: 0;
        min-height: 100vh;
        background-color: transparent !important; 
    }

    /* ==== ANIMASI BACKGROUND BERJALAN & GLOW ==== */
    .bg-animated {
        position: fixed;
        top: 0; left: 0; width: 100vw; height: 100vh;
        z-index: -2;
        background: linear-gradient(-45deg, #f0f9ff, #e0f2fe, #dbeafe, #eff6ff);
        background-size: 400% 400%;
        animation: gradientMove 12s ease infinite;
    }

    .bg-mouse-glow {
        position: fixed;
        top: 0; left: 0; width: 100vw; height: 100vh;
        z-index: -1;
        pointer-events: none;
        background: radial-gradient(600px circle at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(56, 189, 248, 0.15), transparent 40%);
        transition: background 0.1s ease;
    }

    @keyframes gradientMove {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* ==== TOPBAR STICKY ON SCROLL ==== */
    .layout-header-override {
        position: fixed !important;
        top: 0 !important;
        width: 100% !important;
        z-index: 1050 !important;
        background: transparent !important;
        box-shadow: none !important;
        transition: all 0.3s ease-in-out !important;
    }

    .layout-header-override.is-scrolled {
        background: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.6) !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
    }

    /* ==== SCROLL REVEAL ==== */
    .reveal {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s cubic-bezier(0.5, 0, 0, 1);
    }
    .reveal.active {
        opacity: 1;
        transform: translateY(0);
    }
    .delay-1 { transition-delay: 0.1s; }
    .delay-2 { transition-delay: 0.2s; }
    .delay-3 { transition-delay: 0.3s; }

    @keyframes floating {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }
    .float-anim { animation: floating 4s ease-in-out infinite; }

    /* ==== LAYOUT SHELLS ==== */
    .hero-grid {
        width: 100%;
        max-width: 1120px;
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 40px;
        align-items: center;
        min-height: 85vh;
        margin: 0 auto;
        padding-top: 80px; 
    }
    @media (max-width: 900px){
        .hero-grid {
            grid-template-columns: 1fr;
            text-align: center;
            gap: 24px;
            padding-top: 100px;
        }
        .hero-grid .d-flex { justify-content: center; }
    }

    .section-shell {
        width: 100%;
        max-width: 1120px;
        margin: 80px auto;
        padding: 0 16px;
    }

    .section-title {
        font-size: 28px;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 8px;
        background: linear-gradient(135deg, var(--dark), var(--primary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .section-subtitle {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 32px;
        max-width: 600px;
    }

    /* ==== GACOR CARDS (FEATURES) ==== */
    .custom-grid-features {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
    }
    .gacor-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        padding: 24px;
        height: 100%;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 30px rgba(148, 163, 184, 0.1);
        position: relative;
        overflow: hidden;
    }
    .gacor-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 40px rgba(37, 99, 235, 0.15);
        border-color: #bfdbfe;
    }
    .gacor-icon-wrap {
        width: 48px; height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        margin-bottom: 16px;
        box-shadow: 0 8px 16px rgba(37,99,235,0.25);
    }

    /* ==== ANIMATED CHART BARS (BARU!) ==== */
    .chart-container {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(148, 163, 184, 0.1);
        height: 100%;
    }
    .chart-row {
        margin-bottom: 16px;
    }
    .chart-row:last-child {
        margin-bottom: 0;
    }
    .chart-label-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
    }
    .chart-track {
        width: 100%;
        height: 12px;
        background: #e2e8f0;
        border-radius: 99px;
        overflow: hidden;
    }
    /* Animasi Bar Mengisi */
    .chart-fill {
        height: 100%;
        border-radius: 99px;
        width: 0%; /* Default 0 */
        background: linear-gradient(90deg, #3b82f6, #38bdf8);
        transition: width 1.5s cubic-bezier(0.22, 1, 0.36, 1); /* Efek smooth meluncur */
    }
    /* Ketika class .active ditambahkan oleh scroll reveal, bar akan melebar sesuai variabel CSS */
    .reveal.active .chart-fill {
        width: var(--target-w);
    }
    /* Variasi warna */
    .chart-fill.purple { background: linear-gradient(90deg, #8b5cf6, #c084fc); }
    .chart-fill.emerald { background: linear-gradient(90deg, #10b981, #34d399); }
    .chart-fill.orange { background: linear-gradient(90deg, #f97316, #fb923c); }

    /* ==== PK CARDS ==== */
    .custom-grid-pk {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
        justify-content: center;
    }
    .pk-card-modern {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .pk-card-modern:hover {
        transform: translateY(-8px);
        box-shadow: 0 24px 48px rgba(37, 99, 235, 0.12);
        border-color: #bfdbfe;
    }
    .pk-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px dashed #e5e7eb; }
    .pk-icon {
        width: 44px; height: 44px; border-radius: 12px;
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        color: #2563eb; display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 16px; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.1);
    }
    .pk-title { font-size: 16px; font-weight: 700; color: #1e293b; margin: 0; line-height: 1.2; }
    .pk-subtitle { font-size: 11px; color: #64748b; margin-top: 4px; }
    
    .pk-req-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px; flex-grow: 1; }
    .pk-req-list li { 
        font-size: 12px; color: #475569; line-height: 1.5; 
        position: relative; padding-left: 28px; 
    }
    .pk-req-list li::before {
        content: "✓"; color: #ffffff; background: #3b82f6; border-radius: 50%;
        width: 18px; height: 18px; display: flex; align-items: center; justify-content: center;
        position: absolute; left: 0; top: 0; font-size: 10px; font-weight: bold;
        box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);
    }
    .pk-req-list li strong { color: #1d4ed8; font-weight: 700; }

    /* ==== STEPS ALUR & FAQ ==== */
    .step-card {
        display: flex; gap: 16px; align-items: flex-start;
        padding: 20px; border-radius: 16px; transition: background 0.3s ease;
    }
    .step-card:hover { background: rgba(239, 246, 255, 0.5); backdrop-filter: blur(5px); }
    .step-badge {
        width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg,#2563eb,#60a5fa); color: #fff; font-size: 16px; font-weight: bold;
        box-shadow: 0 10px 20px rgba(37,99,235,0.3); flex-shrink: 0;
    }
    .accordion-item { border: none; margin-bottom: 12px; border-radius: 16px !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03); overflow: hidden; background: var(--glass-bg); }
    .accordion-button { font-weight: 600; padding: 18px 24px; border-radius: 16px !important; background: transparent; }
    .accordion-button:not(.collapsed) { background-color: #eff6ff; color: #1d4ed8; box-shadow: none; }
    .accordion-button:focus { box-shadow: none; }

    /* ==== BUTTONS ==== */
    .btn-gacor {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white !important; border: none; padding: 12px 28px; border-radius: 99px;
        font-weight: 600; font-size: 14px; transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3); text-decoration: none !important; display: inline-block;
    }
    .btn-gacor:hover { transform: translateY(-3px); box-shadow: 0 15px 25px rgba(37, 99, 235, 0.4); }
    
    .btn-outline-gacor {
        background: transparent; color: var(--primary) !important; border: 2px solid var(--primary);
        padding: 10px 26px; border-radius: 99px; font-weight: 600; font-size: 14px;
        transition: all 0.3s ease; text-decoration: none !important; display: inline-block;
    }
    .btn-outline-gacor:hover { background: rgba(239, 246, 255, 0.8); transform: translateY(-3px); }

    /* ==== CHATBOT ==== */
    .diksera-chat-launcher { position: fixed; right: 24px; bottom: 24px; z-index: 50; }
    .diksera-chat-bubble-btn {
        width: 56px; height: 56px; border-radius: 50%; border: none;
        background: linear-gradient(135deg,#2563eb,#60a5fa); box-shadow: 0 10px 25px rgba(37,99,235,0.4);
        display: flex; align-items: center; justify-content: center; cursor: pointer; color: white; font-size: 24px;
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .diksera-chat-bubble-btn:hover { transform: scale(1.15) rotate(10deg); }
    .diksera-chat-window {
        position: fixed; right: 24px; bottom: 90px; width: 340px; max-height: 500px;
        background: #ffffff; border-radius: 20px; box-shadow: 0 20px 40px rgba(15,23,42,0.15);
        border: 1px solid #e2e8f0; display: none; flex-direction: column; overflow: hidden; z-index: 50;
        animation: chatPop 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    @keyframes chatPop { from { opacity: 0; transform: translateY(20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
    .diksera-chat-header { padding: 16px; background: linear-gradient(135deg,#1d4ed8,#3b82f6); color: #eff6ff; display: flex; align-items: flex-start; gap: 12px; }
    .diksera-chat-avatar{ width:36px; height:36px; border-radius:999px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; font-size:18px; }
    .diksera-chat-title{ font-size:14px; font-weight:700; line-height:1.2; }
    .diksera-chat-subtitle{ font-size:11px; opacity:0.9; margin-top:2px; }
    .diksera-chat-close{ margin-left:auto; border:none; background:transparent; color:#eff6ff; font-size:20px; cursor:pointer; }
    .diksera-chat-body{ padding:12px; font-size:11px; background:#f8fafc; border-bottom:1px solid #e2e8f0; }
    .diksera-chat-badge{ display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:99px; background:#e2e8f0; color:#475569; }
    .diksera-chat-suggestions{ margin-top:10px; display:flex; flex-wrap:wrap; gap:6px; }
    .diksera-chat-suggestion{ padding:6px 10px; border-radius:99px; background:#dbeafe; border:1px solid #bfdbfe; font-size:11px; color:#1d4ed8; cursor:pointer; transition:all 0.2s; }
    .diksera-chat-suggestion:hover{ background:#2563eb; color:white; }
    .diksera-chat-messages{ flex:1; padding:12px; overflow-y:auto; background:#f8fafc; min-height: 250px;}
    .diksera-chat-msg{ margin-bottom:10px; display:flex; }
    .diksera-chat-msg-bot{ justify-content:flex-start; }
    .diksera-chat-msg-user{ justify-content:flex-end; }
    .diksera-chat-bubble{ max-width:85%; padding:10px 14px; border-radius:14px; font-size:12px; line-height:1.5; white-space:pre-line; }
    .diksera-chat-bubble-bot{ background:#ffffff; border:1px solid #e2e8f0; color:#1e293b; box-shadow:0 2px 5px rgba(0,0,0,0.02);}
    .diksera-chat-bubble-user{ background:#2563eb; color:#eff6ff; border-bottom-right-radius: 4px; }
    .diksera-chat-footer{ padding:12px; background:#ffffff; border-top:1px solid #e2e8f0; }
    .diksera-chat-input-wrap{ display:flex; gap:8px; }
    .diksera-chat-input-wrap input{ flex:1; border-radius:99px; border:1px solid #cbd5e1; font-size:12px; padding:10px 16px; outline:none; transition:border 0.2s;}
    .diksera-chat-input-wrap input:focus{ border-color: #2563eb; }
    .diksera-chat-input-wrap button{ border-radius:99px; border:none; padding:10px 20px; font-size:12px; font-weight:600; background:#2563eb; color:white; cursor:pointer; transition:transform 0.2s;}
    .diksera-chat-input-wrap button:hover{ transform:scale(1.05); }
</style>
@endpush

@section('content')

{{-- DIV UNTUK ANIMASI BACKGROUND --}}
<div class="bg-animated"></div>
<div class="bg-mouse-glow"></div>

{{-- SECTION 1: HERO --}}
<div class="hero-grid reveal">
    <div>
        <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-4" style="background:rgba(239, 246, 255, 0.7); backdrop-filter: blur(5px); border:1px solid #dbeafe; color:#1d4ed8; font-size:12px; font-weight:600; box-shadow:0 4px 10px rgba(37,99,235,0.1);">
            <span class="pulse-dot" style="width:8px;height:8px;border-radius:50%;background:#3b82f6;box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); animation: pulse 2s infinite;"></span>
            Platform Kompetensi Terintegrasi
        </div>

        <h1 class="hero-title" style="font-size:3.5rem; font-weight:800; line-height:1.1; margin-bottom:20px; letter-spacing:-1px;">
            <span style="background:linear-gradient(135deg,#1d4ed8,#38bdf8);-webkit-background-clip:text;color:transparent;">DIKSERA</span><br>
            <span style="color:#0f172a; font-size:2.5rem;">Satu Pintu Kelola Karier Perawat & Bidan</span>
        </h1>

        <p class="hero-text mb-4" style="font-size:1.1rem; color:#475569; max-width:500px; line-height:1.6;">
            Tinggalkan cara manual. Kelola DRH, lisensi STR/SIP, ujian CBT, hingga wawancara kompetensi dengan sistem digital yang siap hadapi akreditasi.
        </p>

        <div class="d-flex flex-wrap gap-3 mb-5">
            <a href="{{ route('login') }}" class="btn-gacor">Masuk Sekarang</a>
            <a href="{{ route('register.perawat') }}" class="btn-outline-gacor">Registrasi Akun</a>
        </div>
    </div>

    <div class="float-anim d-none d-md-block">
        <div style="background:var(--glass-bg); backdrop-filter:blur(20px); border-radius:30px; border:1px solid var(--glass-border); padding:30px; box-shadow:0 25px 50px rgba(37,99,235,0.15); position:relative;">
            
            <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-4" style="background:#fff; box-shadow:0 10px 20px rgba(0,0,0,0.03);">
                <div style="width:50px;height:50px;border-radius:12px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:24px;">📝</div>
                <div>
                    <div style="font-weight:700; color:#1e293b; font-size:14px;">DRH Digital Tersinkronisasi</div>
                    <div style="font-size:12px; color:#64748b;">Update data mandiri, siap audit.</div>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-4" style="background:#fff; box-shadow:0 10px 20px rgba(0,0,0,0.03); transform:translateX(20px);">
                <div style="width:50px;height:50px;border-radius:12px;background:#fef2f2;color:#ef4444;display:flex;align-items:center;justify-content:center;font-size:24px;">🚨</div>
                <div>
                    <div style="font-weight:700; color:#1e293b; font-size:14px;">Warning STR/SIP Expired</div>
                    <div style="font-size:12px; color:#64748b;">Notifikasi otomatis masa berlaku.</div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 p-3 rounded-4" style="background:#fff; box-shadow:0 10px 20px rgba(0,0,0,0.03);">
                <div style="width:50px;height:50px;border-radius:12px;background:#f0fdf4;color:#22c55e;display:flex;align-items:center;justify-content:center;font-size:24px;">🎓</div>
                <div>
                    <div style="font-weight:700; color:#1e293b; font-size:14px;">Rekap Uji Kompetensi</div>
                    <div style="font-size:12px; color:#64748b;">Penetapan status klinik satu klik.</div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- SECTION 2: FITUR UTAMA --}}
<div class="section-shell">
    <div class="text-center mb-5 reveal">
        <h2 class="section-title">Fitur Unggulan</h2>
        <p class="section-subtitle mx-auto">Sistem yang dirancang mengerti alur kerja Komite Keperawatan dan kebutuhan praktis perawat klinis.</p>
    </div>

    <div class="custom-grid-features">
        <div class="reveal delay-1">
            <div class="gacor-card">
                <div class="gacor-icon-wrap">📋</div>
                <h4 style="font-size:18px; font-weight:700; color:#1e293b;">Daftar Riwayat Hidup</h4>
                <p style="font-size:13px; color:#64748b; margin-top:8px;">Sentralisasi profil perawat. Riwayat pendidikan, pelatihan, hingga pekerjaan terdokumentasi seragam dan mudah dilacak saat akreditasi.</p>
            </div>
        </div>
        <div class="reveal delay-2">
            <div class="gacor-card">
                <div class="gacor-icon-wrap">🛡️</div>
                <h4 style="font-size:18px; font-weight:700; color:#1e293b;">Monitoring Lisensi</h4>
                <p style="font-size:13px; color:#64748b; margin-top:8px;">Ucapkan selamat tinggal pada STR/SIP kedaluwarsa. Arsip digital aman dengan sistem pengingat masa berlaku otomatis.</p>
            </div>
        </div>
        <div class="reveal delay-3">
            <div class="gacor-card">
                <div class="gacor-icon-wrap">📊</div>
                <h4 style="font-size:18px; font-weight:700; color:#1e293b;">Penilaian Kompetensi</h4>
                <p style="font-size:13px; color:#64748b; margin-top:8px;">Modul ujian CBT terintegrasi dan form wawancara digital. Hasil direkap langsung menjadi rekomendasi status kompeten.</p>
            </div>
        </div>
    </div>
</div>

{{-- SECTION 3: ANIMATED CHART STATISTIK (BARU) --}}
@php
    // Kalkulasi persentase untuk animasi Chart
    $total_users = max($stats['profiles'] ?? 1, 1); 
    
    // Demografi
    $pct_perawat = min(100, (($stats['perawat_count'] ?? 0) / $total_users) * 100);
    $pct_bidan   = min(100, (($stats['bidan_count'] ?? 0) / $total_users) * 100);
    
    // Pendidikan (asumsi total user memiliki pendidikan)
    $pct_d3 = min(100, (($stats['pendidikan_d3'] ?? 0) / $total_users) * 100);
    $pct_s1 = min(100, (($stats['pendidikan_s1'] ?? 0) / $total_users) * 100);

    // Legalitas (asumsi terhadap total profil)
    $pct_str = min(100, (($stats['str_aktif'] ?? 0) / $total_users) * 100);
    $pct_sip = min(100, (($stats['sip_aktif'] ?? 0) / $total_users) * 100);
@endphp

<div class="section-shell">
    <div class="text-center mb-5 reveal">
        <h2 class="section-title">Sekilas Data DIKSERA</h2>
        <p class="section-subtitle mx-auto">Statistik SDM, legalitas, dan pencapaian kompetensi secara real-time.</p>
    </div>

    <div class="row g-4">
        <!-- Chart 1: Demografi Profesi -->
        <div class="col-lg-6 reveal delay-1">
            <div class="chart-container">
                <div class="mb-4">
                    <h5 style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">Demografi Profesi</h5>
                    <span style="font-size:12px; color:#64748b;">Distribusi perawat dan bidan aktif.</span>
                </div>
                
                <div class="chart-row">
                    <div class="chart-label-wrap">
                        <span>Perawat Klinik</span>
                        <span style="color:#2563eb;">{{ $stats['perawat_count'] ?? 0 }} Data</span>
                    </div>
                    <div class="chart-track">
                        <!-- Animasi diset dari variabel CSS --target-w -->
                        <div class="chart-fill" style="--target-w: {{ $pct_perawat }}%;"></div>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-label-wrap">
                        <span>Bidan</span>
                        <span style="color:#8b5cf6;">{{ $stats['bidan_count'] ?? 0 }} Data</span>
                    </div>
                    <div class="chart-track">
                        <div class="chart-fill purple" style="--target-w: {{ $pct_bidan }}%;"></div>
                    </div>
                </div>
                
                <div class="chart-row mt-4 pt-3" style="border-top:1px dashed #e2e8f0;">
                    <div class="chart-label-wrap">
                        <span>Lulusan D3</span>
                        <span style="color:#10b981;">{{ $stats['pendidikan_d3'] ?? 0 }} Data</span>
                    </div>
                    <div class="chart-track">
                        <div class="chart-fill emerald" style="--target-w: {{ $pct_d3 }}%;"></div>
                    </div>
                </div>
                
                <div class="chart-row">
                    <div class="chart-label-wrap">
                        <span>Lulusan S1/Profesi</span>
                        <span style="color:#f97316;">{{ $stats['pendidikan_s1'] ?? 0 }} Data</span>
                    </div>
                    <div class="chart-track">
                        <div class="chart-fill orange" style="--target-w: {{ $pct_s1 }}%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Legalitas & Kompetensi -->
        <div class="col-lg-6 reveal delay-2">
            <div class="chart-container">
                <div class="mb-4">
                    <h5 style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">Legalitas & Asesmen</h5>
                    <span style="font-size:12px; color:#64748b;">Monitoring dokumen aktif dan progres sertifikasi.</span>
                </div>
                
                <div class="chart-row">
                    <div class="chart-label-wrap">
                        <span>STR Aktif</span>
                        <span style="color:#2563eb;">{{ $stats['str_aktif'] ?? 0 }} Dokumen</span>
                    </div>
                    <div class="chart-track">
                        <div class="chart-fill" style="--target-w: {{ $pct_str }}%;"></div>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-label-wrap">
                        <span>SIP Aktif</span>
                        <span style="color:#8b5cf6;">{{ $stats['sip_aktif'] ?? 0 }} Dokumen</span>
                    </div>
                    <div class="chart-track">
                        <div class="chart-fill purple" style="--target-w: {{ $pct_sip }}%;"></div>
                    </div>
                </div>

                <!-- Modifikasi sedikit untuk angka absolut karena bukan persentase murni thd total profil -->
                <div class="chart-row mt-4 pt-3" style="border-top:1px dashed #e2e8f0;">
                    <div class="chart-label-wrap">
                        <span>Asesmen Berjalan</span>
                        <span style="color:#f97316;">{{ $stats['pengajuan_berjalan'] ?? 0 }} Proses</span>
                    </div>
                    <div class="chart-track">
                        <!-- Kita set visualisasi 50% max agar bar tidak penuh jika angkanya kecil -->
                        <div class="chart-fill orange" style="--target-w: {{ min(100, (($stats['pengajuan_berjalan'] ?? 0) / max(1, $stats['pengajuan_berjalan'] + $stats['total_terkompetensi'])) * 100) }}%;"></div>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-label-wrap">
                        <span>Terkompetensi</span>
                        <span style="color:#10b981;">{{ $stats['total_terkompetensi'] ?? 0 }} Perawat</span>
                    </div>
                    <div class="chart-track">
                        <div class="chart-fill emerald" style="--target-w: {{ min(100, (($stats['total_terkompetensi'] ?? 0) / max(1, $stats['pengajuan_berjalan'] + $stats['total_terkompetensi'])) * 100) }}%;"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- SECTION 4: PERSYARATAN JENJANG KARIER (PK) --}}
<div class="section-shell">
    <div class="reveal mb-4">
        <h2 class="section-title">Persyaratan Jenjang Karier (PK)</h2>
        <p class="section-subtitle">Syarat mutlak masa kerja dan perolehan SKP aktif untuk kenaikan jenjang karier klinis.</p>
    </div>
    
    <div class="custom-grid-pk">
        <div class="reveal delay-1">
            <div class="pk-card-modern">
                <div class="pk-header">
                    <div class="pk-icon">P1</div>
                    <div>
                        <h6 class="pk-title">Pra PK ke PK 1</h6>
                        <div class="pk-subtitle">Fase pemula klinik</div>
                    </div>
                </div>
                <ul class="pk-req-list">
                    <li>D3: Min. 2 Tahun Klinis</li>
                    <li>S1/D4/Profesi: Min. 1 Tahun Klinis</li>
                    <li><strong>SKP: 5</strong> (Status Aktif)</li>
                </ul>
            </div>
        </div>

        <div class="reveal delay-2">
            <div class="pk-card-modern">
                <div class="pk-header">
                    <div class="pk-icon">P2</div>
                    <div>
                        <h6 class="pk-title">PK 1 ke PK 2</h6>
                        <div class="pk-subtitle">Fase kompeten dasar</div>
                    </div>
                </div>
                <ul class="pk-req-list">
                    <li>D3: Min. 4 Tahun di PK 1</li>
                    <li>S1/D4/Profesi: Min. 3 Tahun di PK 1</li>
                    <li><strong>SKP: 10</strong> (Status Aktif)</li>
                </ul>
            </div>
        </div>

        <div class="reveal delay-3">
            <div class="pk-card-modern">
                <div class="pk-header">
                    <div class="pk-icon">P3</div>
                    <div>
                        <h6 class="pk-title">PK 2 ke PK 3</h6>
                        <div class="pk-subtitle">Fase mahir klinik</div>
                    </div>
                </div>
                <ul class="pk-req-list">
                    <li>D3/S1/D4/Profesi: Min. 4 Tahun di PK 2</li>
                    <li><strong>SKP: 20</strong> (Status Aktif)</li>
                    <li>Membuat PDSA / PDCA / RCA lengkap (<strong>1 Case</strong>)</li>
                </ul>
            </div>
        </div>

        <div class="reveal delay-1">
            <div class="pk-card-modern">
                <div class="pk-header">
                    <div class="pk-icon">P4</div>
                    <div>
                        <h6 class="pk-title">PK 3 ke PK 4</h6>
                        <div class="pk-subtitle">Fase spesialis/ahli</div>
                    </div>
                </div>
                <ul class="pk-req-list">
                    <li>S1/D4/Profesi: Min. 6 Tahun di PK 3</li>
                    <li>S2: Min. 4 Tahun di PK 3</li>
                    <li><strong>SKP: 40</strong> (Status Aktif)</li>
                    <li>Membuat PDSA / PDCA / RCA lengkap (<strong>3 Case</strong>)</li>
                </ul>
            </div>
        </div>

        <div class="reveal delay-2">
            <div class="pk-card-modern">
                <div class="pk-header">
                    <div class="pk-icon">P5</div>
                    <div>
                        <h6 class="pk-title">PK 4 ke PK 5</h6>
                        <div class="pk-subtitle">Fase konsultan klinik</div>
                    </div>
                </div>
                <ul class="pk-req-list">
                    <li>S1/D4/Profesi: Min. 6 Tahun di PK 4</li>
                    <li>S2: Min. 4 Tahun di PK 4</li>
                    <li><strong>SKP: 50</strong> (Status Aktif)</li>
                    <li>Membuat PDSA / PDCA / RCA lengkap (<strong>5 Case</strong>)</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="mt-4 p-4 rounded-4 reveal delay-3" style="background:var(--glass-bg); backdrop-filter: blur(5px); border-left:4px solid #3b82f6; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
        <div class="d-flex align-items-center gap-3">
            <div style="font-size:24px;">💡</div>
            <div style="font-size:13px; color:#1e293b;">
                <strong style="color:#1d4ed8;">Catatan Penting:</strong> SKP wajib dalam status aktif. Selalu perhatikan tanggal mulai dan tanggal selesai masa aktif SKP Anda di dalam sistem.
            </div>
        </div>
    </div>
</div>

{{-- SECTION 5: ALUR PENGGUNAAN --}}
<div class="section-shell">
    <div class="row align-items-center">
        <div class="col-lg-5 mb-4 reveal">
            <h2 class="section-title">3 Langkah Mudah</h2>
            <p class="section-subtitle">Proses dari awal mendaftar hingga penetapan kompetensi dibuat se-linear mungkin.</p>
        </div>
        <div class="col-lg-7">
            <div class="d-flex flex-column gap-2">
                <div class="step-card reveal delay-1">
                    <div class="step-badge">1</div>
                    <div>
                        <h5 style="font-size:16px; font-weight:700; margin-bottom:4px;">Registrasi & Aktivasi Akun</h5>
                        <p style="font-size:13px; color:#64748b; margin-bottom:0;">Perawat mendaftar, komite melakukan verifikasi dan aktivasi akun.</p>
                    </div>
                </div>
                <div class="step-card reveal delay-2">
                    <div class="step-badge">2</div>
                    <div>
                        <h5 style="font-size:16px; font-weight:700; margin-bottom:4px;">Lengkapi DRH & Unggah Berkas</h5>
                        <p style="font-size:13px; color:#64748b; margin-bottom:0;">Isi riwayat hidup digital, unggah scan STR/SIP dan sertifikat penunjang.</p>
                    </div>
                </div>
                <div class="step-card reveal delay-3">
                    <div class="step-badge">3</div>
                    <div>
                        <h5 style="font-size:16px; font-weight:700; margin-bottom:4px;">Asesmen & Penetapan</h5>
                        <p style="font-size:13px; color:#64748b; margin-bottom:0;">Ikuti ujian CBT & wawancara. Komite merekap dan merilis status kompetensi akhir.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SECTION 6: FAQ --}}
<div class="section-shell mb-5">
    <div class="text-center mb-4 reveal">
        <h2 class="section-title">Pertanyaan Umum</h2>
        <p class="section-subtitle mx-auto">Hal yang sering ditanyakan sebelum memulai.</p>
    </div>

    <div class="accordion reveal delay-1 mx-auto" id="faqDiksera" style="max-width: 800px;">
        <div class="accordion-item">
            <h2 class="accordion-header" id="faq1">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1Body">
                    Apa perbedaan DIKSERA dengan data perawat biasa?
                </button>
            </h2>
            <div id="faq1Body" class="accordion-collapse collapse" data-bs-parent="#faqDiksera">
                <div class="accordion-body text-secondary" style="font-size:14px; line-height:1.6;">
                    DIKSERA fokus pada <strong>kompetensi</strong> dan <strong>sertifikasi</strong> perawat: bukan hanya sekadar data identitas, tetapi juga memonitor pelatihan, pengalaman kerja, lisensi, serta hasil uji kompetensi dalam satu sistem yang terintegrasi penuh.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="faq2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2Body">
                    Siapa yang boleh mendaftar di DIKSERA?
                </button>
            </h2>
            <div id="faq2Body" class="accordion-collapse collapse" data-bs-parent="#faqDiksera">
                <div class="accordion-body text-secondary" style="font-size:14px; line-height:1.6;">
                    Akun ini diperuntukkan khusus bagi perawat dan bidan di lingkungan rumah sakit yang dikelola oleh <strong>Komite Keperawatan</strong>. Proses registrasi akan diverifikasi (diaktifkan) langsung oleh tim komite.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="faq3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3Body">
                    Apakah data saya aman?
                </button>
            </h2>
            <div id="faq3Body" class="accordion-collapse collapse" data-bs-parent="#faqDiksera">
                <div class="accordion-body text-secondary" style="font-size:14px; line-height:1.6;">
                    Sangat aman. Data dikelola oleh tim internal rumah sakit. Hak akses diatur ketat berdasarkan peran (Perawat, Asesor/Komite, dan Admin), sehingga kerahasiaan informasi sensitif tetap terjaga.
                </div>
            </div>
        </div>
    </div>
</div>

{{-- FLOATING CHATBOT --}}
<div class="diksera-chat-launcher">
    <div class="diksera-chat-window" id="diksera-chat">
        <div class="diksera-chat-header">
            <div class="diksera-chat-avatar">🤖</div>
            <div>
                <div class="diksera-chat-title">Asisten DIKSERA</div>
                <div class="diksera-chat-subtitle">Tanya singkat tentang alur & statistik</div>
            </div>
            <button class="diksera-chat-close" id="diksera-chat-close">&times;</button>
        </div>

        <div class="diksera-chat-body">
            <div class="diksera-chat-badge">
                <span>Mode AI Pintar</span>
                <span style="width:6px;height:6px;border-radius:50%;background:#22c55e; margin:0 4px; animation:pulse 2s infinite;"></span>
                <span>Online</span>
            </div>
            <div class="diksera-chat-suggestions">
                <button type="button" class="diksera-chat-suggestion" data-message="Apa itu DIKSERA?">Apa itu DIKSERA?</button>
                <button type="button" class="diksera-chat-suggestion" data-message="Berapa perbandingan perawat dan bidan?">Jumlah Perawat/Bidan</button>
                <button type="button" class="diksera-chat-suggestion" data-message="Bagaimana progres asesmen saat ini?">Progres Asesmen</button>
                <button type="button" class="diksera-chat-suggestion" data-message="Berapa jumlah STR dan SIP yang aktif?">Legalitas Aktif</button>
            </div>
        </div>

        <div class="diksera-chat-messages" id="diksera-chat-messages">
            <div class="diksera-chat-msg diksera-chat-msg-bot">
                <div class="diksera-chat-bubble diksera-chat-bubble-bot">
                    Halo 👋, aku asisten cerdas DIKSERA. Aku bisa bantu jelaskan cara daftar, atau membacakan data spesifik seperti jumlah perawat/bidan, status STR, dan progres asesmen. Ada yang bisa dibantu?
                </div>
            </div>
        </div>

        <div class="diksera-chat-footer">
            <form id="diksera-chat-form" class="diksera-chat-input-wrap">
                <input type="text" id="diksera-chat-input" placeholder="Ketik pertanyaan..." autocomplete="off">
                <button type="submit">Kirim</button>
            </form>
        </div>
    </div>

    <button class="diksera-chat-bubble-btn" id="diksera-chat-open" aria-label="Buka chat">💬</button>
</div>

@endsection

@push('scripts')
<script>
    // ===== LOGIKA TOPBAR STICKY ON SCROLL =====
    document.addEventListener('DOMContentLoaded', function() {
        const topbar = document.querySelector('nav') || document.querySelector('header');
        
        if (topbar) {
            topbar.classList.add('layout-header-override');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 20) {
                    topbar.classList.add('is-scrolled');
                } else {
                    topbar.classList.remove('is-scrolled');
                }
            });
        }
    });

    // ===== ANIMASI MOUSE GLOW =====
    document.addEventListener('mousemove', function(e) {
        const bgGlow = document.querySelector('.bg-mouse-glow');
        if (bgGlow) {
            bgGlow.style.setProperty('--mouse-x', e.clientX + 'px');
            bgGlow.style.setProperty('--mouse-y', e.clientY + 'px');
        }
    });

    // ===== SCROLL REVEAL (TERMASUK UNTUK CHART) =====
    document.addEventListener('DOMContentLoaded', function() {
        const observerOptions = { root: null, rootMargin: '0px', threshold: 0.15 };
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    });

    // ===== LOGIKA CHATBOT TERBARU DENGAN DATA KOMPREHENSIF =====
    window.dikseraStats = @json($stats ?? []);

    function normalizeText(text) {
        return (text || '').toLowerCase().replace(/[.,!?;:()"'`]/g, ' ').replace(/\s+/g, ' ').trim();
    }
    function createIntent(name, keywords, replyFn) { return { name, keywords, replyFn }; }
    function countMatches(text, keywords) {
        let score = 0; keywords.forEach(k => { if(k && text.includes(k)) score++; }); return score;
    }

    const dikseraIntents = (function () {
        const stats = window.dikseraStats || {};
        return [
            createIntent('greeting', ['halo','hai','assalam','pagi','siang','sore','malam','hi'], () => 'Halo 👋, aku asisten DIKSERA. Aku bisa bantu jelaskan fitur, cara registrasi, sampai bacain data statistik lengkap RS kita.'),
            createIntent('about', ['apa itu diksera','diksera itu','tentang diksera','platform diksera'], () => 'DIKSERA adalah platform digital terintegrasi untuk mengelola kompetensi perawat, dokumentasi DRH, lisensi STR/SIP, hingga asesmen komite.'),
            createIntent('register', ['registrasi','daftar','pendaftaran','register','buat akun'], () => 'Cara registrasi: Klik tombol "Registrasi Akun", isi formulir, dan tunggu verifikasi Komite Keperawatan agar akun aktif.'),
            
            // Intent Baru berdasarkan data LandingController
            createIntent('stats_profesi', ['berapa perbandingan', 'jumlah perawat', 'jumlah bidan', 'total perawat', 'profesi'], () => {
                return `Di sistem ini tercatat ada ${stats.perawat_count ?? 0} Perawat Klinik dan ${stats.bidan_count ?? 0} Bidan dari total ${stats.profiles ?? 0} profil yang terdaftar.`;
            }),
            createIntent('stats_pendidikan', ['pendidikan', 'lulusan', 'd3', 's1', 's2', 'sarjana'], () => {
                return `Untuk demografi pendidikan: terdapat ${stats.pendidikan_d3 ?? 0} lulusan D3, ${stats.pendidikan_s1 ?? 0} lulusan S1/Profesi, dan ${stats.pendidikan_s2 ?? 0} lulusan S2.`;
            }),
            createIntent('stats_legalitas', ['str', 'sip', 'lisensi', 'aktif', 'legalitas'], () => {
                return `Saat ini terdapat ${stats.str_aktif ?? 0} STR dan ${stats.sip_aktif ?? 0} SIP yang masih berstatus Aktif di dalam sistem kita.`;
            }),
            createIntent('stats_kompetensi', ['kompeten', 'asesmen', 'pengajuan', 'ujian', 'berjalan', 'progres'], () => {
                return `Progres asesmen saat ini: Ada ${stats.pengajuan_berjalan ?? 0} pengajuan yang sedang diproses/berjalan, dan ${stats.total_terkompetensi ?? 0} perawat/bidan yang telah ditetapkan Kompeten.`;
            }),
            
            createIntent('flow', ['alur','proses','step','langkah','cara pakai'], () => 'Alurnya simpel: 1. Registrasi -> 2. Lengkapi DRH & Berkas -> 3. Ikuti Asesmen CBT/Wawancara -> Status Kompetensi dirilis.'),
            createIntent('security', ['aman','keamanan','privasi'], () => 'Sangat aman. Server dan database dikelola internal RS dengan hierarki hak akses yang ketat (Admin, Komite, User).'),
            createIntent('fallback', [], () => 'Hmm, aku kurang mengerti. Coba tanya hal spesifik seperti "Berapa jumlah STR aktif?", "Jumlah perawat dan bidan", atau "Bagaimana progres asesmen?".')
        ];
    })();

    function detectIntentFromText(rawText) {
        const text = normalizeText(rawText);
        let bestIntent = null; let bestScore = 0;
        dikseraIntents.forEach(intent => {
            if (intent.name === 'fallback') return;
            const score = countMatches(text, intent.keywords);
            if (score > bestScore) { bestScore = score; bestIntent = intent; }
        });
        if (!bestIntent && /^(halo|hai|hi|assalam)/.test(text)) bestIntent = dikseraIntents.find(i => i.name === 'greeting');
        if (!bestIntent) bestIntent = dikseraIntents.find(i => i.name === 'fallback');
        return bestIntent;
    }

    function dikseraBotAnswer(message) {
        const intent = detectIntentFromText(message);
        return intent ? intent.replyFn() : 'Maaf, sistem AI sedang gangguan.';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const openBtn = document.getElementById('diksera-chat-open');
        const closeBtn = document.getElementById('diksera-chat-close');
        const chatWin = document.getElementById('diksera-chat');
        const form = document.getElementById('diksera-chat-form');
        const input = document.getElementById('diksera-chat-input');
        const messages = document.getElementById('diksera-chat-messages');
        const suggestionBtns = document.querySelectorAll('.diksera-chat-suggestion');

        function toggleChat(show) { chatWin.style.display = show ? 'flex' : 'none'; }
        
        openBtn.addEventListener('click', () => {
            const isHidden = chatWin.style.display === 'none' || chatWin.style.display === '';
            toggleChat(isHidden); if (isHidden) setTimeout(() => input && input.focus(), 200);
        });
        
        closeBtn.addEventListener('click', () => toggleChat(false));
        
        suggestionBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                input.value = this.getAttribute('data-message');
                form.dispatchEvent(new Event('submit', {cancelable: true, bubbles: true}));
            });
        });

        function appendMessage(text, type) {
            const wrapper = document.createElement('div');
            wrapper.className = 'diksera-chat-msg ' + (type === 'user' ? 'diksera-chat-msg-user' : 'diksera-chat-msg-bot');
            const bubble = document.createElement('div');
            bubble.className = 'diksera-chat-bubble ' + (type === 'user' ? 'diksera-chat-bubble-user' : 'diksera-chat-bubble-bot');
            bubble.textContent = text;
            wrapper.appendChild(bubble); messages.appendChild(wrapper);
            messages.scrollTop = messages.scrollHeight;
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const text = (input.value || '').trim();
            if (!text) return;
            appendMessage(text, 'user'); input.value = '';
            
            setTimeout(() => {
                appendMessage(dikseraBotAnswer(text), 'bot');
            }, 400);
        });
    });
</script>
@endpush