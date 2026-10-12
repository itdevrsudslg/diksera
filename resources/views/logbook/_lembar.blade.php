{{-- Satu lembar rekap logbook (format surat resmi). Variabel dari LogbookService::lembarRekap() --}}
@php
    $profile = $target->profile;
    $profesi = strtoupper(optional($profile)->profesi ?: 'Perawat');

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

    $orang = fn($u) => $u ? (object) ['nama' => $u->name, 'nip' => optional($u->profile)->nip] : null;

    // Kasi: nama dari Pengaturan > Penanda Tangan; NIP dari pengaturan, bila kosong dari akun Kasi
    $pejabatKasi = \App\Services\Penandatangan::untuk('kasi_keperawatan', $selesai);
    $akunKasi = optional($rekaps->whereNotNull('kasi_signed_at')->last())->kasi
        ?? optional(\App\Models\PerawatProfile::with('user')->where('jabatan_struktural', 'kepala_seksi')->first())->user;
    $kasiTtd = (object) [
        'nama' => $pejabatKasi->nama,
        'nip'  => $pejabatKasi->nip ?: optional(optional($akunKasi)->profile)->nip,
    ];

    $label = fn($k) => nl2br(e(\App\Services\Penandatangan::label($k)));
    $isKaruTarget = \App\Services\LogbookService::isKaru($target);

    // Logbook kepala ruangan cukup 2 TTD: kepala ruangan (penyusun) & kasi
    $kolomTtd = $isKaruTarget
        ? [[$label('logbook.ttd2'), $orang($target), $okK, $tglK, $nK], [$label('logbook.ttd3'), $kasiTtd, $okS, $tglS, $nS]]
        : [[$label('logbook.ttd1'), $orang($target), $okP, $tglP, $nP], [$label('logbook.ttd2'), $orang($ttdKaru), $okK, $tglK, $nK], [$label('logbook.ttd3'), $kasiTtd, $okS, $tglS, $nS]];

    $qr = function ($role, $org, $tgl) use ($target, $namaPeriode) {
        $teks = 'Rekap Logbook ' . $target->name . ' ' . $namaPeriode . '. Ditandatangani elektronik oleh '
            . strip_tags(str_replace('<br />', ' ', $role)) . ': ' . optional($org)->nama
            . (optional($org)->nip ? ' (NIP ' . $org->nip . ')' : '') . ' pada ' . \Carbon\Carbon::parse($tgl)->format('d-m-Y H:i') . '. DIKSERA RSUD SLG';
        return 'https://api.qrserver.com/v1/create-qr-code/?size=110x110&data=' . urlencode($teks);
    };

    $tanggalTtd = collect([$tglS, $tglK, $tglP])->filter()->max();
    $no = 0;
@endphp
<div class="surat">
    {{-- KOP --}}
    <table class="kop">
        <tr>
            <td style="width: 80px;"><img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Pemkab.png" alt="Logo" class="kop-logo"></td>
            <td class="kop-teks">
                <div class="k1">PEMERINTAH KABUPATEN KEDIRI</div>
                <div class="k1">DINAS KESEHATAN</div>
                <div class="k2">UOBK RSUD SIMPANG LIMA GUMUL</div>
                <div class="k3">Jl. Galuh Candra Kirana Ds. Tugurejo Kec. Ngasem Telp. (0354) 2891400</div>
                <div class="k3">Fax. (0354) 2891414 Email: rsud_slg@kedirikab.go.id Website: rsudslg.kedirikab.go.id</div>
                <div class="k4">K E D I R I</div>
            </td>
            <td style="width: 80px;"></td>
        </tr>
    </table>

    <div class="judul">
        <div class="j1">REKAP LOGBOOK HARIAN {{ $profesi }}</div>
        <div>Periode {{ $namaPeriode }} ({{ $mulai->translatedFormat('d F Y') }} s.d. {{ $selesai->translatedFormat('d F Y') }})</div>
    </div>

    <table class="ident">
        <tr><td style="width: 120px;">Nama</td><td style="width: 10px;">:</td><td><b>{{ strtoupper($target->name) }}</b></td></tr>
        <tr><td>NIP</td><td>:</td><td>{{ optional($profile)->nip ?: '-' }}</td></tr>
        <tr><td>Jabatan</td><td>:</td><td>{{ optional($profile)->jabatan ?: '-' }}</td></tr>
        <tr><td>Unit Kerja</td><td>:</td><td>{{ $unit ?: '-' }}</td></tr>
    </table>

    {{-- A. RINGKASAN --}}
    <div class="sub">A. Ringkasan</div>
    <table class="tbl">
        <thead>
            <tr><th>Jumlah Pasien</th><th>Kunjungan</th><th>Kegiatan</th><th>Tindakan</th><th>Hari Aktif</th></tr>
        </thead>
        <tbody>
            <tr class="c"><td>{{ $total_pasien }}</td><td>{{ $total_kunjungan }}</td><td>{{ $total_kegiatan }}</td><td>{{ $total_tindakan }}</td><td>{{ $hari_aktif }}</td></tr>
        </tbody>
    </table>

    {{-- B. RINCIAN PER PASIEN --}}
    <div class="sub">B. Rincian per Pasien</div>
    <table class="tbl">
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th style="width: 82px;">Tanggal</th>
                <th style="width: 70px;">No. RM</th>
                <th style="width: 130px;">Nama Pasien</th>
                <th style="width: 130px;">Kegiatan</th>
                <th>Tindakan</th>
                <th style="width: 40px;">Jml</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($entries as $e)
                @php $rows = max(1, $e->kegiatans->count()); $no++; @endphp
                @forelse ($e->kegiatans as $ki => $k)
                    <tr>
                        @if ($ki === 0)
                            <td class="c" rowspan="{{ $rows }}">{{ $no }}</td>
                            <td class="c" rowspan="{{ $rows }}" style="white-space: nowrap;">{{ $e->tanggal->format('d-m-Y') }}</td>
                            <td class="c" rowspan="{{ $rows }}">{{ $e->no_rm }}</td>
                            <td rowspan="{{ $rows }}">{{ $e->nama_pasien ?: '-' }}</td>
                        @endif
                        <td>{{ $k->nama }}</td>
                        <td>
                            @foreach ($k->tindakans as $t)
                                <div>{{ $t->kode }} – {{ $t->nama }}@if ($t->jumlah > 1) <b>({{ $t->jumlah }}×)</b>@endif</div>
                            @endforeach
                        </td>
                        <td class="c">{{ $k->tindakans->sum('jumlah') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="c">{{ $no }}</td><td class="c">{{ $e->tanggal->format('d-m-Y') }}</td>
                        <td class="c">{{ $e->no_rm }}</td><td>{{ $e->nama_pasien ?: '-' }}</td><td colspan="3">-</td>
                    </tr>
                @endforelse
            @empty
                <tr><td colspan="7" class="c kosong">Belum ada entri yang divalidasi pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- C. REKAP JENIS TINDAKAN --}}
    <div class="sub">C. Rekap Jenis Tindakan</div>
    <table class="tbl">
        <thead>
            <tr><th style="width: 28px;">No</th><th style="width: 90px;">Kode</th><th>Jenis Tindakan</th><th style="width: 70px;">Frekuensi</th><th style="width: 60px;">Jumlah</th></tr>
        </thead>
        <tbody>
            @forelse ($tindakan as $i => $t)
                <tr><td class="c">{{ $i + 1 }}</td><td>{{ $t['kode'] }}</td><td>{{ $t['nama'] }}</td><td class="c">{{ $t['frekuensi'] }}</td><td class="c">{{ $t['jumlah'] }}</td></tr>
            @empty
                <tr><td colspan="5" class="c kosong">-</td></tr>
            @endforelse
        </tbody>
        @if ($tindakan->count())
            <tfoot><tr><th colspan="3" style="text-align: right;">TOTAL</th><th>{{ $tindakan->sum('frekuensi') }}</th><th>{{ $total_tindakan }}</th></tr></tfoot>
        @endif
    </table>

    @if ($multi)
        {{-- D. REKAP PER BULAN --}}
        <div class="sub">D. Rekap per Bulan</div>
        <table class="tbl">
            <thead><tr><th>Bulan</th><th>Pasien</th><th>Kunjungan</th><th>Tindakan</th><th>TTD Kepala Ruangan</th><th>TTD Kasi</th></tr></thead>
            <tbody>
                @php $cur = $mulai->copy(); @endphp
                @while ($cur->lte($selesai))
                    @php $key = $cur->format('Y-m'); $pb = $per_bulan[$key] ?? null; $rk = $rekapByBulan->get($key); @endphp
                    <tr class="c">
                        <td style="text-align: left;">{{ $cur->translatedFormat('F Y') }}</td>
                        <td>{{ $pb['pasien'] ?? 0 }}</td><td>{{ $pb['entri'] ?? 0 }}</td><td>{{ $pb['tindakan'] ?? 0 }}</td>
                        <td>{{ optional($rk)->karu_signed_at ? 'Sudah' : ($pb ? 'Belum' : '-') }}</td>
                        <td>{{ optional($rk)->kasi_signed_at ? 'Sudah' : ($pb ? 'Belum' : '-') }}</td>
                    </tr>
                    @php $cur->addMonth(); @endphp
                @endwhile
            </tbody>
        </table>
    @endif

    {{-- TANDA TANGAN --}}
    <div class="tempat">Kediri, {{ $tanggalTtd ? \Carbon\Carbon::parse($tanggalTtd)->translatedFormat('d F Y') : '..............................' }}</div>
    <table class="ttd">
        <tr>
            @foreach ($kolomTtd as [$role, $org, $ok, $tgl, $n])
                <td style="width: {{ round(100 / count($kolomTtd)) }}%;">
                    <div class="role">{!! $role !!}</div>
                    <div class="sig">
                        @if ($ok)
                            <img src="{{ $qr($role, $org, $tgl) }}" alt="QR TTD" class="qr">
                        @elseif ($multi && $n !== '0/0')
                            <span class="belum">Belum lengkap ({{ $n }} bulan)</span>
                        @else
                            <span class="belum">Belum ditandatangani</span>
                        @endif
                    </div>
                    <div class="nm">{{ optional($org)->nama ? strtoupper($org->nama) : '..............................' }}</div>
                    <div>NIP. {{ optional($org)->nip ?: '..............................' }}</div>
                </td>
            @endforeach
        </tr>
    </table>
    <div class="catatan">Dokumen ini ditandatangani secara elektronik melalui DIKSERA. Pindai QR untuk keterangan penandatangan.</div>
</div>
