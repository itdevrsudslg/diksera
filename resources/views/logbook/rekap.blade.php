@extends($cetak ? 'logbook.print_layout' : 'layouts.app')

@php
    $pageTitle = 'Rekap Logbook';
    $pageSubtitle = $target->name . ' · ' . $namaPeriode;
    $profile = $target->profile;

    // Status TTD untuk rentang (bulan yang ada datanya harus sudah ditandatangani)
    $bulanAda = collect($per_bulan)->keys();
    $rekapByBulan = $rekaps->keyBy(fn($r) => sprintf('%04d-%02d', $r->tahun, $r->bulan));
    $statusTtd = function ($kolom) use ($bulanAda, $rekapByBulan) {
        if ($bulanAda->isEmpty()) return [false, null, '0/0'];
        $sudah = $bulanAda->filter(fn($b) => optional($rekapByBulan->get($b))->$kolom);
        $terakhir = $sudah->map(fn($b) => $rekapByBulan->get($b)->$kolom)->max();
        return [$sudah->count() === $bulanAda->count(), $terakhir, $sudah->count() . '/' . $bulanAda->count()];
    };
    [$okP, $tglP, $nP] = $statusTtd('perawat_signed_at');
    [$okK, $tglK, $nK] = $statusTtd('karu_signed_at');
    [$okS, $tglS, $nS] = $statusTtd('kasi_signed_at');
    $multi = $jenis !== 'bulan';
    $self = Auth::id() === $target->id;
    $routeArgs = $self ? [] : ['userId' => $target->id];
@endphp

@section('title', 'Rekap Logbook ' . $target->name . ' – ' . $namaPeriode)

@push('styles')
    @include('logbook._styles')
    <style>
        .kop { text-align: center; border-bottom: 3px double #111827; padding-bottom: 10px; margin-bottom: 14px; }
        .kop .rs { font-size: 18px; font-weight: 800; letter-spacing: .5px; }
        .kop .judul { font-size: 15px; font-weight: 700; margin-top: 6px; }
        .ident td { padding: 2px 8px 2px 0; vertical-align: top; }
        .ttd-print { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 28px; text-align: center; }
        .ttd-print .sig { height: 64px; display: flex; align-items: center; justify-content: center; }
        .ttd-print .stamp { border: 1.5px solid #16a34a; color: #166534; border-radius: 8px; padding: 4px 8px; font-size: 10.5px; line-height: 1.3; }
        .ttd-print .nm { font-weight: 700; text-decoration: underline; }
        .rekap-table th, .rekap-table td { padding: 6px 8px; }
        @media print { .lb-card { border: none; box-shadow: none; padding: 0; } }
    </style>
@endpush

@section('content')
    @unless ($cetak)
        {{-- PILIH PERIODE --}}
        <div class="lb-toolbar">
            <form method="GET" class="d-flex flex-wrap gap-2 m-0" id="formPeriode">
                <select name="jenis" class="form-select form-select-sm" style="width:auto" onchange="this.form.ke.value = 1; this.form.submit()">
                    @foreach (['bulan' => 'Bulanan', 'triwulan' => 'Triwulan', 'semester' => 'Semester'] as $v => $l)
                        <option value="{{ $v }}" {{ $jenis === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
                <select name="ke" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                    @if ($jenis === 'bulan')
                        @foreach (range(1, 12) as $b)
                            <option value="{{ $b }}" {{ $ke == $b ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $b, 1)->translatedFormat('F') }}</option>
                        @endforeach
                    @elseif ($jenis === 'triwulan')
                        @foreach ([1 => 'I (Jan–Mar)', 2 => 'II (Apr–Jun)', 3 => 'III (Jul–Sep)', 4 => 'IV (Okt–Des)'] as $v => $l)
                            <option value="{{ $v }}" {{ $ke == $v ? 'selected' : '' }}>Triwulan {{ $l }}</option>
                        @endforeach
                    @else
                        @foreach ([1 => 'I (Jan–Jun)', 2 => 'II (Jul–Des)'] as $v => $l)
                            <option value="{{ $v }}" {{ $ke == $v ? 'selected' : '' }}>Semester {{ $l }}</option>
                        @endforeach
                    @endif
                </select>
                <select name="tahun" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                    @foreach (range(now()->year, now()->year - 3) as $y)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
            <div class="d-flex gap-2">
                @if ($self)
                    <a href="{{ route('perawat.logbook.index', ['bulan' => $mulai->format('Y-m')]) }}" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left"></i> Logbook</a>
                @endif
                <a href="{{ route('perawat.logbook.rekap', array_merge($routeArgs, ['jenis' => $jenis, 'tahun' => $tahun, 'ke' => $ke, 'cetak' => 1])) }}"
                    target="_blank" class="btn btn-sm btn-primary"><i class="bi bi-printer"></i> Cetak / PDF</a>
            </div>
        </div>
        <div class="alert alert-info small py-2"><i class="bi bi-info-circle me-1"></i> Rekap hanya menghitung entri yang sudah <b>divalidasi kepala ruangan</b>.</div>
    @endunless

    <div class="lb-card">
        <div class="kop">
            <div class="rs">RSUD SIMPANG LIMA GUMUL KABUPATEN KEDIRI</div>
            <div class="small">Bidang Keperawatan · DIKSERA</div>
            <div class="judul">REKAP LOGBOOK HARIAN {{ strtoupper(optional($profile)->profesi ?? 'PERAWAT') }}</div>
            <div>Periode: <b>{{ $namaPeriode }}</b> ({{ $mulai->translatedFormat('d M Y') }} – {{ $selesai->translatedFormat('d M Y') }})</div>
        </div>

        <table class="ident mb-3">
            <tr><td>Nama</td><td>: <b>{{ $target->name }}</b></td></tr>
            <tr><td>NIP</td><td>: {{ optional($profile)->nip ?? '-' }}</td></tr>
            <tr><td>Unit Kerja</td><td>: {{ $unit ?? '-' }}</td></tr>
            <tr><td>Jabatan</td><td>: {{ optional($profile)->jabatan ?? '-' }}</td></tr>
        </table>

        {{-- RINGKASAN --}}
        <div class="row g-2 mb-3">
            @foreach ([[$total_pasien, 'Jumlah Pasien'], [$total_kunjungan, 'Kunjungan'], [$total_kegiatan, 'Kegiatan'], [$total_tindakan, 'Tindakan'], [$hari_aktif, 'Hari Aktif']] as [$v, $l])
                <div class="col"><div class="lb-stat text-center"><div class="v">{{ $v }}</div><div class="l">{{ $l }}</div></div></div>
            @endforeach
        </div>

        @if ($multi)
            <div class="fw-bold mb-2">Rekap per Bulan</div>
            <table class="table table-bordered lb-table rekap-table mb-3">
                <thead><tr><th>Bulan</th><th class="text-center">Pasien</th><th class="text-center">Kunjungan</th><th class="text-center">Tindakan</th><th class="text-center">TTD Karu</th><th class="text-center">TTD Kasi</th></tr></thead>
                <tbody>
                    @php $cur = $mulai->copy(); @endphp
                    @while ($cur->lte($selesai))
                        @php $key = $cur->format('Y-m'); $pb = $per_bulan[$key] ?? null; $rk = $rekapByBulan->get($key); @endphp
                        <tr>
                            <td>{{ $cur->translatedFormat('F Y') }}</td>
                            <td class="text-center">{{ $pb['pasien'] ?? 0 }}</td>
                            <td class="text-center">{{ $pb['entri'] ?? 0 }}</td>
                            <td class="text-center">{{ $pb['tindakan'] ?? 0 }}</td>
                            <td class="text-center">{!! optional($rk)->karu_signed_at ? '<i class="bi bi-check-lg text-success"></i>' : ($pb ? '–' : '') !!}</td>
                            <td class="text-center">{!! optional($rk)->kasi_signed_at ? '<i class="bi bi-check-lg text-success"></i>' : ($pb ? '–' : '') !!}</td>
                        </tr>
                        @php $cur->addMonth(); @endphp
                    @endwhile
                </tbody>
            </table>
        @endif

        <div class="fw-bold mb-2">Rekap Jenis Tindakan</div>
        <table class="table table-bordered lb-table rekap-table">
            <thead><tr><th style="width:40px">No</th><th style="width:110px">Kode</th><th>Jenis Tindakan</th><th class="text-center" style="width:90px">Frekuensi</th><th class="text-center" style="width:90px">Jumlah</th></tr></thead>
            <tbody>
                @forelse ($tindakan as $i => $t)
                    <tr><td>{{ $i + 1 }}</td><td>{{ $t['kode'] }}</td><td>{{ $t['nama'] }}</td><td class="text-center">{{ $t['frekuensi'] }}</td><td class="text-center fw-semibold">{{ $t['jumlah'] }}</td></tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada entri tervalidasi pada periode ini.</td></tr>
                @endforelse
            </tbody>
            @if ($tindakan->count())
                <tfoot><tr class="fw-bold"><td colspan="3" class="text-end">Total</td><td class="text-center">{{ $tindakan->sum('frekuensi') }}</td><td class="text-center">{{ $total_tindakan }}</td></tr></tfoot>
            @endif
        </table>

        {{-- TTD --}}
        <div class="ttd-print">
            @foreach ([
                ['Perawat Pelaksana', $target, $okP, $tglP, $nP],
                ['Kepala Ruangan', $ttdKaru, $okK, $tglK, $nK],
                ['Mengetahui,<br>Kepala Seksi Keperawatan', $ttdKasi, $okS, $tglS, $nS],
            ] as [$role, $org, $ok, $tgl, $n])
                <div>
                    <div>{!! $role !!}</div>
                    <div class="sig">
                        @if ($ok)
                            <div class="stamp"><i class="bi bi-patch-check-fill"></i> Ditandatangani elektronik<br>{{ \Carbon\Carbon::parse($tgl)->translatedFormat('d M Y H:i') }}</div>
                        @elseif ($multi && $n !== '0/0')
                            <span class="text-muted small">Belum lengkap ({{ $n }} bulan)</span>
                        @else
                            <span class="text-muted small">Belum ditandatangani</span>
                        @endif
                    </div>
                    <div class="nm">{{ optional($org)->name ?? '..............................' }}</div>
                    <div class="small">NIP. {{ optional(optional($org)->profile)->nip ?? '..............................' }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
