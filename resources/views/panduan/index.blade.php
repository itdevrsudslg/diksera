@extends('layouts.app')

@php
    $pageTitle = 'Buku Panduan';
    $pageSubtitle = 'Panduan penggunaan DIKSERA · diperbarui ' . \Carbon\Carbon::createFromTimestamp($terakhir)->locale('id')->translatedFormat('d F Y');
@endphp

@section('title', 'Buku Panduan – DIKSERA')

@push('styles')
    <style>
        /* overflow:hidden pada .app-main mematikan position:sticky -> dibuka khusus halaman ini */
        .app-main { overflow: visible; }
        .pd-wrap { display: grid; grid-template-columns: 270px 1fr; gap: 20px; align-items: start; }
        .pd-nav { position: sticky; top: 16px; z-index: 5; background: #fff; border: 1px solid var(--border-soft); border-radius: 16px; padding: 14px; max-height: calc(100vh - 32px); overflow-y: auto; }
        .pd-nav .grp { font-weight: 700; font-size: 13px; display: flex; gap: 8px; align-items: center; padding: 8px 10px; border-radius: 10px; color: var(--text-main); text-decoration: none; }
        .pd-nav .grp:hover, .pd-nav .grp.aktif { background: var(--blue-soft); color: var(--blue-main); }
        .pd-nav .sub { display: block; font-size: 12.5px; color: var(--text-muted); text-decoration: none; padding: 3px 10px 3px 32px; }
        .pd-nav .sub.l3 { padding-left: 44px; font-size: 12px; }
        .pd-nav .sub:hover { color: var(--blue-main); }
        .pd-isi { min-width: 0; }
        .pd-bagian { background: #fff; border: 1px solid var(--border-soft); border-radius: 16px; padding: 24px 28px; margin-bottom: 20px; }
        .pd-bagian h1 { font-size: 22px; font-weight: 700; color: var(--blue-main); margin-bottom: 14px; padding-bottom: 10px; border-bottom: 2px solid var(--blue-soft); }
        .pd-bagian h2 { font-size: 17px; font-weight: 700; margin-top: 26px; margin-bottom: 10px; scroll-margin-top: 16px; }
        .pd-bagian h3 { font-size: 15px; font-weight: 700; margin-top: 18px; scroll-margin-top: 16px; }
        .pd-bagian p, .pd-bagian li { font-size: 14px; line-height: 1.65; color: #1f2937; }
        .pd-bagian table { font-size: 13px; margin: 8px 0 14px; }
        .pd-bagian table th { background: #f8fafc; white-space: nowrap; }
        .pd-bagian blockquote { border-left: 4px solid #f59e0b; background: #fffbeb; padding: 10px 14px; border-radius: 0 10px 10px 0; margin: 12px 0; }
        .pd-bagian blockquote p { margin: 0; font-size: 13.5px; }
        .pd-bagian code { background: #f1f5f9; padding: 1px 6px; border-radius: 4px; color: #be185d; }
        .pd-bagian mark { background: #fde68a; padding: 0 2px; }
        .pd-peran { background: var(--blue-soft); border-radius: 12px; padding: 8px 10px; margin-bottom: 10px; }
        .pd-cari { position: relative; margin-bottom: 10px; }
        .pd-cari input { padding-left: 32px; }
        .pd-cari i { position: absolute; left: 11px; top: 8px; color: var(--text-muted); }
        .pd-kosong { display: none; text-align: center; color: var(--text-muted); padding: 30px; }
        @media (max-width: 992px) {
            .pd-wrap { grid-template-columns: 1fr; }
            .pd-nav { position: static; max-height: none; }
            .pd-nav .sub { display: none; }
            .pd-bagian { padding: 16px; }
        }
        @media print {
            .sidebar, .topbar, header, .pd-nav, .no-print { display: none !important; }
            .pd-wrap { display: block; }
            .pd-bagian { border: none; page-break-after: always; padding: 0; }
        }
    </style>
@endpush

@section('content')
    <div class="pd-wrap">
        <nav class="pd-nav no-print">
            <div class="pd-peran">
                <div class="small text-muted">Panduan untuk</div>
                @if ($isAdmin)
                    <form method="GET" class="m-0">
                        <select name="sebagai" class="form-select form-select-sm fw-semibold" onchange="this.form.submit()" title="Lihat panduan sebagai peran lain">
                            @foreach ($daftarPeran as $k => $v)
                                <option value="{{ $k }}" {{ $peran === $k ? 'selected' : '' }}>{{ $v }}{{ $k === 'admin' ? ' (semua bagian)' : '' }}</option>
                            @endforeach
                        </select>
                    </form>
                @else
                    <div class="fw-bold" style="color: var(--blue-main);"><i class="bi bi-person-circle"></i> {{ $namaPeran }}</div>
                @endif
            </div>
            <div class="pd-cari">
                <i class="bi bi-search"></i>
                <input type="search" id="pdCari" class="form-control form-control-sm" placeholder="Cari di panduan…">
            </div>
            @foreach ($bagian as $b)
                <a href="#{{ $b['id'] }}" class="grp" data-grp="{{ $b['id'] }}"><i class="bi {{ $b['ikon'] }}"></i> {{ $b['judul'] }}</a>
                @foreach ($b['toc'] as $t)
                    <a href="#{{ $t['id'] }}" class="sub {{ $t['level'] === 3 ? 'l3' : '' }}">{{ $t['teks'] }}</a>
                @endforeach
            @endforeach
            <button type="button" class="btn btn-sm btn-outline-secondary w-100 mt-3" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak / Simpan PDF
            </button>
        </nav>

        <div class="pd-isi">
            @foreach ($bagian as $b)
                <section class="pd-bagian" id="{{ $b['id'] }}">
                    {!! $b['html'] !!}
                </section>
            @endforeach
            <div class="pd-kosong" id="pdKosong"><i class="bi bi-search display-6 d-block mb-2 opacity-50"></i>Tidak ada hasil untuk pencarian ini.</div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const input = document.getElementById('pdCari');
            const bagian = [...document.querySelectorAll('.pd-bagian')];
            const asli = new Map(bagian.map(b => [b, b.innerHTML]));
            const esc = s => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

            // Pencarian: tampilkan bagian yang memuat kata kunci & tandai hasilnya
            input.addEventListener('input', function () {
                const q = this.value.trim();
                let ada = 0;
                bagian.forEach(b => {
                    b.innerHTML = asli.get(b);
                    if (!q) { b.style.display = ''; ada++; return; }
                    const cocok = b.textContent.toLowerCase().includes(q.toLowerCase());
                    b.style.display = cocok ? '' : 'none';
                    if (!cocok) return;
                    ada++;
                    const re = new RegExp('(' + esc(q) + ')', 'gi');
                    const reCek = new RegExp(esc(q), 'i');
                    const walker = document.createTreeWalker(b, NodeFilter.SHOW_TEXT);
                    const nodes = [];
                    while (walker.nextNode()) if (reCek.test(walker.currentNode.nodeValue)) nodes.push(walker.currentNode);
                    nodes.forEach(n => {
                        const span = document.createElement('span');
                        span.innerHTML = n.nodeValue.replace(/[&<>]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[c])).replace(re, '<mark>$1</mark>');
                        n.replaceWith(span);
                    });
                });
                document.getElementById('pdKosong').style.display = ada ? 'none' : 'block';
                const m = document.querySelector('.pd-bagian mark');
                if (m) m.scrollIntoView({ block: 'center' });
            });

            // Tandai bagian yang sedang dibaca di daftar isi
            const links = [...document.querySelectorAll('.pd-nav .grp')];
            const obs = new IntersectionObserver(es => es.forEach(e => {
                if (e.isIntersecting) links.forEach(l => l.classList.toggle('aktif', l.dataset.grp === e.target.id));
            }), { rootMargin: '-30% 0px -60% 0px' });
            bagian.forEach(b => obs.observe(b));
        })();
    </script>
@endpush
