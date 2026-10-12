<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --border-soft: #cbd5e1; --blue-main: #1d4ed8; --blue-soft: #e0edff; --text-main: #111827; --text-muted: #6b7280; }
        body { background: #fff; color: #111827; font-size: 13px; }
        .sheet { max-width: 900px; margin: 24px auto; padding: 0 16px; }
        @media print {
            .no-print { display: none !important; }
            .sheet { margin: 0; max-width: none; padding: 0; }
            @page { size: A4; margin: 14mm; }
        }
    </style>
    @stack('styles')
</head>
<body onload="window.print()">
    <div class="sheet">
        <div class="no-print text-end mb-3">
            <button class="btn btn-sm btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> Cetak</button>
        </div>
        @yield('content')
    </div>
</body>
</html>
