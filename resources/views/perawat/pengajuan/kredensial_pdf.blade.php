<!DOCTYPE html>
<html>
<head>
    <title>RKK (Clinical Privilege)</title>
    <style>
        @page { margin: 25px 40px; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #000; line-height: 1.3; }
        .header-table { width: 100%; border-bottom: 3px solid #000; margin-bottom: 10px; padding-bottom: 10px; }
        .header-table td { text-align: center; }
        .logo { width: 85px; }
        .anti-potong { page-break-inside: avoid; }
        .box-tanggal { width: 80%; margin: 15px auto; border: 2px solid #000; padding: 5px; text-align: center; font-weight: bold; font-size: 12pt; }
        
        .identitas-table { width: 100%; margin-bottom: 15px; font-weight: bold; }
        .identitas-table td { vertical-align: top; padding: 2px; }
        
        .rkk-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10pt; }
        .rkk-table th, .rkk-table td { border: 1px solid #000; padding: 5px; }
        .rkk-table th { text-align: center; vertical-align: middle; }
        .bg-grey { background-color: #f2f2f2; font-weight: bold; }
        
        .checkmark { font-family: DejaVu Sans, sans-serif; font-weight: normal; font-size: 11pt; }
        
        .footer-text { text-align: justify; margin-bottom: 25px; }
        .ttd-table { width: 100%; text-align: center; }
        .ttd-table td { padding: 5px; vertical-align: bottom; }
        .qr-img { width: 75px; margin: 10px 0; }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <table class="header-table">
        <tr>
            <td width="15%" style="vertical-align: middle;">
                <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Pemkab.png" class="logo">
            </td>
            <td width="85%" style="line-height: 1.1;">
                <strong style="font-size: 14pt; ">PEMERINTAH KABUPATEN KEDIRI</strong><br>
                <strong style="font-size: 14pt;">DINAS KESEHATAN</strong><br>
                <strong style="font-size: 15pt; ">UOBK RSUD SLG</strong><br>
                <span style="font-size: 10pt; ">JL. Galuh Candra Kirana Ds. Tugurejo Kec. Ngasem Telp.(0354)2891400</span><br>
                <span style="font-size: 10pt; ">Fax.(0354)2891414 Email : rsud_slg@kedirikab.go.id Website : rsudslg.kedirikab.go.id</span><br>
                <strong style="font-size: 11pt;  letter-spacing: 2px;">K E D I R I</strong>
            </td>
        </tr>
    </table>

    <!-- JUDUL -->
    <div style="text-align: center; margin-top: 15px; font-weight: bold;">
        <div style="font-size: 13pt;">KEWENANGAN KLINIK</div>
        <div style="font-size: 13pt;">(CLINICAL PREVILAGE)</div>
    </div>

    <!-- TANGGAL BERLAKU -->
    <div class="box-tanggal">
        TANGGAL BERLAKU MULAI : {{ $tglTerbit }} s/d {{ $tglAkhir }}
    </div>

    <!-- IDENTITAS -->
    <table class="identitas-table">
        <tr>
            <td width="18%">BIDANG</td><td width="2%">:</td><td width="35%">{{ $bidang }}</td>
            <td width="15%">NAMA</td><td width="2%">:</td><td width="28%">{{ $namaLengkap }}</td>
        </tr>
        <tr>
            <td>UNIT KERJA</td><td>:</td><td>{{ $unitKerja }}</td>
            <td>NIP / NIRP</td><td>:</td><td>{{ $nip }}</td>
        </tr>
        <tr><td colspan="6" style="height: 5px;"></td></tr>
        <tr>
            <td>PENDIDIKAN FORMAL</td><td>:</td><td colspan="4">{{ $pendidikanFormal }}</td>
        </tr>
        <tr>
            <td>KUALIFIKASI</td><td>:</td><td colspan="4">{{ $kualifikasi }}</td>
        </tr>
    </table>

    <!-- TABEL RKK -->
    <table class="rkk-table">
        <thead>
            <tr>
                <th rowspan="3" width="5%">NO</th>
                <th rowspan="3" width="37%">KOMPETENSI KLINIK {{ strtoupper($namaJenjang) }}</th>
                <th colspan="3">JENIS KOMPETENSI</th>
                <th colspan="2">KEWENANGAN</th>
            </tr>
            <tr>
                <th rowspan="2" width="10%">MANDIRI</th>
                <th colspan="2">KOLABORASI</th>
                <th rowspan="2" width="14%">BERWENANG<br>SENDIRI</th>
                <th rowspan="2" width="14%">DENGAN<br>SUPERVISI</th>
            </tr>
            <tr>
                <th width="10%">DELEGASI</th>
                <th width="10%">MANDAT</th>
            </tr>
       </thead>
  <tbody>
            @php $noUrutPdf = 1; @endphp {{-- Tambahkan counter --}}
            
            @if($rkkGrouped->isEmpty())
                <tr><td colspan="7" style="text-align:center; font-style:italic;">Data RKK Kosong</td></tr>
            @else
                @foreach($rkkGrouped as $kategori => $items)
                    <tr><td colspan="7" class="bg-grey">&nbsp;&nbsp;{{ $kategori }}</td></tr>
                    @foreach($items as $item)
                        @php 
                            $id = $item->id;
                            $ansJenis = $rkkDraft[$id]['jenis'] ?? '';
                            $ansKw = $rkkDraft[$id]['kewenangan'] ?? '';
                        @endphp
                        <tr>
                            {{-- Gunakan counter untuk menomori secara urut --}}
                            <td style="text-align: center;">{{ $noUrutPdf++ }}</td>
                            <td>{{ $item->deskripsi_kompetensi }}</td>
                            <td style="text-align: center;" class="checkmark">{!! $ansJenis == 'mandiri' ? '&#10003;' : '' !!}</td>
                            <td style="text-align: center;" class="checkmark">{!! $ansJenis == 'delegasi' ? '&#10003;' : '' !!}</td>
                            <td style="text-align: center;" class="checkmark">{!! $ansJenis == 'mandat' ? '&#10003;' : '' !!}</td>
                            <td style="text-align: center;" class="checkmark">{!! $ansKw == 'sendiri' ? '&#10003;' : '' !!}</td>
                            <td style="text-align: center;" class="checkmark">{!! $ansKw == 'supervisi' ? '&#10003;' : '' !!}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endif

            <!-- BAGIAN MPP DISINI -->
            @if(isset($isMpp) && $isMpp && !empty($rkkMpp))
                <tr>
                    <td colspan="7" class="bg-grey">&nbsp;&nbsp;MANAJER PELAYANAN PASIEN (MPP)</td>
                </tr>
                @foreach($rkkMpp as $item)
                    @php 
                        $mppKey = $item['no_item'];
                        $ansJenis = $rkkDraft[$mppKey]['jenis'] ?? '';
                        $ansKw = $rkkDraft[$mppKey]['kewenangan'] ?? '';
                    @endphp
                    <tr>
                        {{-- Gunakan counter lanjutan agar terhubung nomornya --}}
                        <td style="text-align: center;">{{ $noUrutPdf++ }}</td>
                        <td>{{ $item['deskripsi_kompetensi'] }}</td>
                        <td style="text-align: center;" class="checkmark">{!! $ansJenis == 'mandiri' ? '&#10003;' : '' !!}</td>
                        <td style="text-align: center;" class="checkmark">{!! $ansJenis == 'delegasi' ? '&#10003;' : '' !!}</td>
                        <td style="text-align: center;" class="checkmark">{!! $ansJenis == 'mandat' ? '&#10003;' : '' !!}</td>
                        <td style="text-align: center;" class="checkmark">{!! $ansKw == 'sendiri' ? '&#10003;' : '' !!}</td>
                        <td style="text-align: center;" class="checkmark">{!! $ansKw == 'supervisi' ? '&#10003;' : '' !!}</td>
                    </tr>
                @endforeach
            @endif
            
            @if(isset($isPpi) && $isPpi && !empty($rkkPpi))
    <tr>
        <td colspan="7" class="bg-grey">&nbsp;&nbsp;PENCEGAHAN DAN PENGENDALIAN INFEKSI (PPI)</td>
    </tr>
    @foreach($rkkPpi as $item)
        @php 
            $ppiKey = $item['no_item'];
            $ansJenis = $rkkDraft[$ppiKey]['jenis'] ?? '';
            $ansKw = $rkkDraft[$ppiKey]['kewenangan'] ?? '';
        @endphp
        <tr>
            <td style="text-align: center;">{{ $noUrutPdf++ }}</td>
            <td>{{ $item['deskripsi_kompetensi'] }}</td>
            <td style="text-align: center;" class="checkmark">{!! $ansJenis == 'mandiri' ? '&#10003;' : '' !!}</td>
            <td style="text-align: center;" class="checkmark">{!! $ansJenis == 'delegasi' ? '&#10003;' : '' !!}</td>
            <td style="text-align: center;" class="checkmark">{!! $ansJenis == 'mandat' ? '&#10003;' : '' !!}</td>
            <td style="text-align: center;" class="checkmark">{!! $ansKw == 'sendiri' ? '&#10003;' : '' !!}</td>
            <td style="text-align: center;" class="checkmark">{!! $ansKw == 'supervisi' ? '&#10003;' : '' !!}</td>
        </tr>
    @endforeach
@endif
        </tbody>
        
    </table> <!-- HANYA SATU INI SAJA -->

    <!-- FOOTER / TTD -->
    <div style="page-break-inside: avoid;">
        <div class="footer-text">
            Demikianlah Kewenangan Klinis Keperawatan / Kebidanan ini ditetapkan dengan berorientasi pada <i>White Paper</i> Komite Keperawatan UOBK RSUD Simpang Lima Gumul Kediri. Kewenangan Klinis Keperawatan / Kebidanan ini secara berkala akan dievaluasi dan disempurnakan sesuai dengan perkembangan ilmu pengetahuan dan teknologi Keperawatan / Kebidanan yang ada.
        </div>

        <table class="ttd-table">
            <tr>
                <td width="50%">
                    Disetujui : {{ $tglMulai }}<br>
                    Ketua Komite Keperawatan<br>
                    <img src="{{ $qrBambang }}" class="qr-img"><br>
                    <u>BAMBANG WAHYU SETYONO, S.Kep.,Ns</u><br>
                    NIP. 19841025 200901 1 003
                </td>
                <td width="50%">
                    Ditetapkan di Kediri<br>
                    <br>
                    Ketua Sub Komite Kredensial<br>
                    <img src="{{ $qrHaris }}" class="qr-img"><br>
                    <u>HARIS LISTYAWAN, S.Kep.,Ns</u><br>
                    NIP. 19820214 200901 1 003
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 20px;">
                    Mengetahui,<br>
                    DIREKTUR<br>
                    UOBK RSUD SIMPANG LIMA GUMUL<br>
                    KEDIRI<br>
                    <img src="{{ $qrDirektur }}" class="qr-img"><br>
                    <u>dr. TONY WIDYANTO, Sp.OG (K)</u><br>
                    NIP. 19750714 200212 1 006
                </td>
            </tr>
        </table>
    </div>

</body>
</html>