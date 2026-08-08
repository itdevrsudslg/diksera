<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'DIKSERA - Digitalisasi Kompetensi, Sertifikasi & Evaluasi Bidan dan Perawat' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">

    {{-- Fonts & Bootstrap --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background:
                radial-gradient(circle at top left, #1f4f8a 0, #0b1f3a 40%, #020617 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: #e5e7eb;
        }

        .guest-shell {
            width: 100%;
            max-width: 1120px;
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr);
            gap: 24px;
            align-items: center;
        }

        .hero-card {
            border-radius: 28px;
            padding: 28px;
            background: radial-gradient(circle at top left, rgba(59,130,246,0.28), rgba(15,23,42,0.88));
            border: 1px solid rgba(148,163,184,0.4);
            box-shadow: 0 30px 70px rgba(15,23,42,0.9);
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(15,23,42,0.8);
            border: 1px solid rgba(148,163,184,0.5);
            font-size: 11px;
            color: #bfdbfe;
        }

        .hero-title {
            font-size: 32px;
            font-weight: 600;
            letter-spacing: 0.01em;
            margin-top: 14px;
            margin-bottom: 8px;
        }

        .hero-highlight {
            background: linear-gradient(135deg,#bfdbfe,#38bdf8);
            -webkit-background-clip: text;
            color: transparent;
        }

        .hero-text {
            font-size: 13px;
            color: #9ca3af;
            max-width: 420px;
        }

        .hero-metrics {
            display: flex;
            gap: 16px;
            margin-top: 18px;
        }

        .metric-card {
            flex: 1;
            border-radius: 18px;
            padding: 12px 14px;
            background: rgba(15,23,42,0.8);
            border: 1px solid rgba(148,163,184,0.35);
            font-size: 11px;
            color: #e5e7eb;
        }

        .metric-label {
            color: #9ca3af;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .metric-value {
            font-size: 16px;
            font-weight: 600;
        }

        .btn-primary-soft {
            background: linear-gradient(135deg,#2563eb,#0ea5e9);
            border: none;
            color: #f9fafb;
            border-radius: 999px;
            padding: 10px 22px;
            font-size: 13px;
            font-weight: 500;
            box-shadow: 0 18px 40px rgba(37,99,235,0.7);
        }

        .btn-primary-soft:hover {
            filter: brightness(1.08);
            color: #f9fafb;
        }

        .btn-ghost {
            border-radius: 999px;
            padding: 9px 18px;
            background: rgba(15,23,42,0.7);
            border: 1px solid rgba(148,163,184,0.6);
            font-size: 13px;
            color: #e5e7eb;
        }

        .auth-card {
            border-radius: 24px;
            padding: 20px 22px;
            background: rgba(15,23,42,0.92);
            border: 1px solid rgba(148,163,184,0.5);
            box-shadow: 0 25px 60px rgba(0,0,0,0.9);
        }

        .logo-circle {
            width: 42px;
            height: 42px;
            border-radius: 16px;
            background: radial-gradient(circle at 10% 0,#e0f2fe,#1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .logo-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .auth-title {
            font-size: 18px;
            font-weight: 600;
        }

        .auth-subtitle {
            font-size: 12px;
            color: #9ca3af;
        }

        .form-control-dark {
            background: rgba(15,23,42,0.85);
            border: 1px solid rgba(55,65,81,0.9);
            color: #e5e7eb;
            font-size: 13px;
        }

        .form-control-dark:focus {
            background: rgba(15,23,42,0.95);
            border-color: #38bdf8;
            box-shadow: 0 0 0 1px rgba(56,189,248,0.6);
            color: #e5e7eb;
        }

        .footer-text {
            margin-top: 16px;
            font-size: 11px;
            color: #9ca3af;
        }

        @media (max-width: 900px) {
            .guest-shell {
                grid-template-columns: minmax(0, 1fr);
                max-width: 640px;
            }

            .hero-card {
                order: 2;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="guest-shell">
    {{-- MAIN CONTENT --}}
    <div>
        @yield('content')
    </div>

    {{-- AUTH / SIDE --}}
    <div>
        @yield('auth')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
