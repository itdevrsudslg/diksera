<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'DIKSERA - Digitalisasi Kompetensi, Sertifikasi & Evaluasi Evaluasi Bidan dan Perawat' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">

    {{-- Fonts & CSS --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons (untuk bi bi-shield-check) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        :root{
            --blue-main: #2563eb;
            --blue-deep: #1d4ed8;
            --blue-soft: #e0ecff;
            --blue-soft-2: #f3f6ff;
            --text-main: #0f172a;
            --text-muted: #6b7280;
            --border-soft: #d1d5db;
            --card-radius: 16px;
            --transition-fast: 0.18s ease-out;
            --transition-slow: 0.35s ease;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
        }

        /* ================================
           BODY BACKGROUND + ANIMASI HALUS
           ================================ */
        body {
            min-height: 100vh;
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background:
                radial-gradient(circle at 0% 0%, #dbeafe 0, transparent 55%),
                radial-gradient(circle at 100% 100%, #e0f2fe 0, transparent 55%),
                linear-gradient(135deg, #f9fbff, #ffffff);
            position: relative;
            overflow: hidden;
        }

        /* Blob lembut kiri */
        body::before {
            content: "";
            position: fixed;
            width: 420px;
            height: 420px;
            top: -160px;
            left: -120px;
            background: radial-gradient(circle, rgba(37,99,235,0.36), transparent 68%);
            opacity: 0.8;
            filter: blur(8px);
            z-index: 0;
            transform: translate3d(0,0,0);
            animation: floatBlobLeft 26s ease-in-out infinite alternate;
        }

        /* Blob lembut kanan */
        body::after {
            content: "";
            position: fixed;
            width: 480px;
            height: 480px;
            bottom: -190px;
            right: -140px;
            background: radial-gradient(circle, rgba(56,189,248,0.32), transparent 72%);
            opacity: 0.85;
            filter: blur(10px);
            z-index: 0;
            transform: translate3d(0,0,0);
            animation: floatBlobRight 32s ease-in-out infinite alternate;
        }

        @keyframes floatBlobLeft {
            0%   { transform: translate3d(0, 0, 0) scale(1); }
            50%  { transform: translate3d(24px, 18px, 0) scale(1.06); }
            100% { transform: translate3d(16px, -10px, 0) scale(1.02); }
        }

        @keyframes floatBlobRight {
            0%   { transform: translate3d(0, 0, 0) scale(1); }
            50%  { transform: translate3d(-26px, -16px, 0) scale(1.04); }
            100% { transform: translate3d(-18px, 12px, 0) scale(1.01); }
        }

        /* ================================
           LAYOUT SHELL
           ================================ */
        .auth-shell {
            width: 100%;
            max-width: 980px;
            position: relative;
            z-index: 1;
        }

        .auth-layout {
            border-radius: 22px;
            background: radial-gradient(circle at top left, rgba(255,255,255,0.94), rgba(241,245,249,0.96));
            box-shadow:
                0 18px 55px rgba(15,23,42,0.16),
                0 0 0 1px rgba(255,255,255,0.9);
            overflow: hidden;
            border: 1px solid rgba(148,163,184,0.4);
            backdrop-filter: blur(16px);
        }

        /* ================================
           PANEL KIRI (INFO DIKSERA)
           (Hanya muncul bila @section('auth-left') ADA dan fullWidth tidak diset)
           ================================ */
        .auth-left {
            position: relative;
            padding: 28px 28px 26px 28px;
            background:
                radial-gradient(circle at top left, rgba(37,99,235,0.25), transparent 65%),
                radial-gradient(circle at 110% 120%, rgba(56,189,248,0.22), transparent 60%),
                linear-gradient(145deg, #0f172a, #1d4ed8);
            color: #e5edff;
            overflow: hidden;
        }

        .auth-left::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(130deg, rgba(255,255,255,0.06) 0%, transparent 55%),
                radial-gradient(circle, rgba(148,163,184,0.36) 1px, transparent 1px);
            background-size: 100% 100%, 30px 30px;
            opacity: 0.75;
            pointer-events: none;
        }

        .auth-left::after {
            content: "";
            position: absolute;
            top: -36px;
            left: -40px;
            width: 160px;
            height: 160px;
            background: radial-gradient(circle at 30% 30%, rgba(248,250,252,0.9), transparent 60%);
            opacity: 0.18;
            pointer-events: none;
        }

        .auth-left-inner {
            position: relative;
            z-index: 1;
        }

        .brand-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            background: linear-gradient(135deg, rgba(15,23,42,0.7), rgba(37,99,235,0.95));
            box-shadow: 0 10px 28px rgba(15,23,42,0.4);
            margin-bottom: 16px;
            position: relative;
            overflow: hidden;
        }

        .brand-pill::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(120deg, transparent 0%, rgba(255,255,255,0.42) 50%, transparent 100%);
            transform: translateX(-120%);
            animation: pillShimmer 8s ease-in-out infinite;
            opacity: 0.8;
        }

        @keyframes pillShimmer {
            0%   { transform: translateX(-160%); }
            35%  { transform: translateX(120%); }
            100% { transform: translateX(120%); }
        }

        .brand-dot {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: #22c55e;
            box-shadow: 0 0 14px rgba(34,197,94,0.9);
        }

        .brand-pill span {
            font-size: 11px;
            color: #e5edff;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .auth-left-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .auth-left-subtitle {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 16px;
        }

        .auth-left-highlight {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: linear-gradient(120deg, rgba(15,23,42,0.75), rgba(37,99,235,0.9));
            font-size: 11px;
            margin-bottom: 16px;
        }

        .auth-left-highlight i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: rgba(37,99,235,0.15);
            font-style: normal;
            font-size: 11px;
        }

        .auth-left-list {
            list-style: none;
            padding: 0;
            margin: 0 0 18px 0;
            font-size: 12px;
        }

        .auth-left-list li {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 8px;
        }

        .auth-left-list span.bullet {
            margin-top: 2px;
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: #bfdbfe;
        }

        .auth-left-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-size: 11px;
            opacity: 0.9;
        }

        .auth-left-badge {
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(15,23,42,0.7);
            border: 1px solid rgba(148,163,184,0.6);
        }

        .auth-left-hint {
            opacity: 0.8;
        }

        /* ================================
           PANEL KANAN (CARD LOGIN / REGISTER)
           ================================ */
        .auth-right {
            padding: 22px 22px 22px 22px;
            background: linear-gradient(135deg, #f9fbff, #ffffff);
        }

        .auth-card {
            position: relative;
            border-radius: var(--card-radius);
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(148,163,184,0.55);
            box-shadow:
                0 14px 30px rgba(15,23,42,0.08),
                0 0 0 1px rgba(255,255,255,0.85);
            padding: 22px 22px 18px;
            overflow: hidden;
        }

        .auth-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 12px;
            right: 12px;
            height: 3px;
            border-radius: 999px;
            background: linear-gradient(90deg, #2563eb, #60a5fa, #38bdf8);
            opacity: 0.9;
        }

        .auth-card::after {
            content: "";
            position: absolute;
            inset: 10px;
            border-radius: calc(var(--card-radius) - 6px);
            border: 1px solid rgba(226,232,240,0.8);
            pointer-events: none;
        }

        .auth-card-inner {
            position: relative;
            z-index: 1;
        }

        /* LOGO + TITLE DI KANAN (dipakai login & register) */
        .logo-big {
            width: 70px;
            height: 70px;
            border-radius: 18px;
            background: radial-gradient(circle at 20% 0, #eff6ff, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow:
                0 14px 30px rgba(37,99,235,0.45),
                0 0 18px rgba(37,99,235,0.25);
            margin-bottom: 8px;
        }

        .logo-big img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .auth-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-main);
        }

        .auth-subtitle {
            font-size: 12px;
            color: var(--text-muted);
        }

        .form-label {
            font-size: 12px;
            color: var(--text-muted);
        }

        .form-control-light,
        .form-select.form-control-light {
            background: #f9fafb;
            border: 1px solid var(--border-soft);
            font-size: 13px;
            border-radius: 10px;
        }

        .form-control-light:focus,
        .form-select.form-control-light:focus {
            background: #ffffff;
            border-color: var(--blue-main);
            box-shadow: 0 0 0 1px rgba(37,99,235,0.28);
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: linear-gradient(120deg, rgba(37,99,235,0.06), rgba(59,130,246,0.12));
            font-size: 11px;
            color: #2563eb;
            padding: 3px 7px;
            border-radius: 999px;
        }

        .toggle-password-btn:hover {
            filter: brightness(1.04);
        }

        .btn-solid-blue {
            border-radius: 999px;
            padding: 9px 18px;
            background: linear-gradient(135deg, #2563eb, #60a5fa);
            border: none;
            color: #f9fafb;
            font-size: 13px;
            font-weight: 500;
            box-shadow: 0 14px 28px rgba(37,99,235,0.45);
            transition: transform var(--transition-fast), box-shadow var(--transition-fast), filter var(--transition-fast);
        }

        .btn-solid-blue:hover {
            filter: brightness(1.04);
            transform: translateY(-1px);
            box-shadow: 0 18px 34px rgba(37,99,235,0.52);
        }

        .bottom-link {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 10px;
            text-align: center;
        }

        .bottom-link a {
            color: #2563eb;
            text-decoration: none;
        }

        .bottom-link a:hover {
            text-decoration: underline;
        }

        .is-invalid {
            border-color: #dc2626 !important;
        }

        .alert {
            border-radius: 14px;
        }

        /* ================================
           RESPONSIVE
           ================================ */
        @media (max-width: 992px) {
            body {
                padding: 18px;
            }

            .auth-layout {
                border-radius: 18px;
            }

            .auth-left {
                padding: 24px 22px;
            }

            .auth-right {
                padding: 20px 18px 20px;
            }

            .auth-card {
                padding: 20px 18px 16px;
            }

            body::before {
                width: 320px;
                height: 320px;
                top: -120px;
                left: -90px;
            }

            body::after {
                width: 360px;
                height: 360px;
                bottom: -140px;
                right: -110px;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 14px;
            }

            .auth-right {
                padding: 18px 14px;
            }

            .auth-layout {
                border-radius: 16px;
            }

            .auth-card {
                border-radius: 14px;
                padding: 18px 16px 14px;
            }

            .auth-card::after {
                inset: 8px;
                border-radius: 10px;
            }

            .logo-big {
                width: 66px;
                height: 66px;
            }

            .auth-title {
                font-size: 18px;
            }

            body::before {
                width: 260px;
                height: 260px;
                top: -90px;
                left: -70px;
                opacity: 0.75;
            }

            body::after {
                width: 300px;
                height: 300px;
                bottom: -110px;
                right: -80px;
                opacity: 0.8;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .auth-layout {
                border-radius: 14px;
            }

            .auth-card {
                border-radius: 12px;
                padding: 16px 12px 12px;
            }

            .auth-card::after {
                inset: 6px;
                border-radius: 9px;
            }

            .auth-title {
                font-size: 17px;
            }

            .auth-subtitle {
                font-size: 11px;
            }

            body::before {
                width: 220px;
                height: 220px;
                top: -70px;
                left: -60px;
            }

            body::after {
                width: 260px;
                height: 260px;
                bottom: -90px;
                right: -70px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="auth-shell">
    <div class="auth-layout row g-0">

        {{-- LEFT SIDE: hanya muncul jika TIDAK fullWidth dan ada section auth-left --}}
        @if(!isset($fullWidth))
            @hasSection('auth-left')
                <div class="auth-left col-lg-6 d-none d-lg-block">
                    @yield('auth-left')
                </div>
            @endif
        @endif

        {{-- RIGHT SIDE: col-12 kalau fullWidth, kalau tidak col-lg-6 --}}
        @php
            $rightColClass = (isset($fullWidth) && $fullWidth)
                ? 'col-12'
                : 'col-lg-6 col-12';
        @endphp

        <div class="auth-right {{ $rightColClass }}">
            <div class="auth-card">
                <div class="auth-card-inner">
                    @yield('content')
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('swal'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire(@json(session('swal')));
    });
</script>
@endif

@stack('scripts')
</body>
</html>
