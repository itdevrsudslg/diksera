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
            margin: 0;
            min-height: 100vh;
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(180deg,#e0ebff 0,#f9fbff 40%,#ffffff 100%);
            color: #111827;
        }

        .landing-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        

        .landing-nav {
            padding: 14px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-logo {
            width: 72px;
            height: 72px;
            border-radius: 24px;
            background: radial-gradient(circle at 20% 0,#eff6ff,#3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(37,99,235,0.35);
        }

        .nav-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .nav-title {
            font-size: 20px;
            font-weight: 600;
        }

        .nav-subtitle {
            font-size: 11px;
            color: #6b7280;
        }

        .nav-actions a {
            font-size: 13px;
        }

        .btn-outline-blue {
            border-radius: 999px;
            padding: 8px 16px;
            border: 1px solid #2563eb;
            color: #2563eb;
        }

        .btn-outline-blue:hover {
            background: #2563eb;
            color: #ffffff;
        }

        .btn-solid-blue {
            border-radius: 999px;
            padding: 9px 18px;
            background: linear-gradient(135deg,#2563eb,#60a5fa);
            border: none;
            color: #f9fafb;
            box-shadow: 0 16px 32px rgba(37,99,235,0.4);
        }

        /* FIX DI SINI: jangan flex-row center, pakai column / block */
        .landing-main {
            flex: 1;
            padding: 10px 32px 32px;
            display: flex;
            flex-direction: column;   /* biar anak-anaknya (hero, section, dll) turun ke bawah */
            align-items: center;      /* center secara horizontal */
            justify-content: flex-start; /* dari atas ke bawah, bukan di tengah */
        }

        .landing-footer {
            border-top: 1px solid #e5e7eb;
            padding: 10px 32px;
            font-size: 11px;
            color: #6b7280;
            text-align: right;
        }

        @media (max-width: 900px) {
            .landing-nav { padding: 12px 16px; }
            .landing-main { padding: 14px 16px 24px; }
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="landing-shell">

    {{-- NAVBAR --}}
    <header class="landing-nav">
        <div class="nav-brand">
            <div class="nav-logo">
                <img src="{{ asset('icon.png') }}" alt="DIKSERA">
            </div>
            <div>
                <div class="nav-title">DIKSERA</div>
                <div class="nav-subtitle">
                    Digitalisasi Kompetensi, Sertifikasi &amp; Evaluasi Bidan dan Perawat
                </div>
            </div>
        </div>

        <div class="nav-actions d-flex align-items-center gap-2">
            <a href="{{ route('login') }}" class="btn btn-outline-blue">
                Masuk
            </a>
            <a href="{{ route('register.perawat') }}" class="btn btn-solid-blue">
                Registrasi
            </a>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <main class="landing-main">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="landing-footer">
        &copy; {{ date('Y') }} DIKSERA · Komite Keperawatan <br> RSUD Simpang Lima Gumul
    </footer>
</div>

{{-- JS --}}
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
