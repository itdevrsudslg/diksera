<!DOCTYPE html>
<html>
<head>
    <title>Surat Penugasan Kerja Klinis</title>
    <style>
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 11pt; 
            line-height: 1.5; 
            margin: 0 1cm; 
        }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .anti-potong { page-break-inside: avoid; }
        
        /* Kop Surat */
        .kop-surat { width: 100%; border-collapse: collapse; border-bottom: 3px solid black; margin-bottom: 2px; }
        .kop-surat td { padding-bottom: 10px; vertical-align: middle; }
        .kop-surat-2 { border-bottom: 1px solid black; margin-bottom: 25px; }
        
        /* Pembatas Halaman PDF */
        .page-break { page-break-after: always; }
        
        /* Container Tanda Tangan */
        .ttd-container { width: 100%; margin-top: 30px; }
        .ttd-box { 
            width: 320px; 
            float: right; 
            text-align: center; 
            line-height: 1.3;
        }
        .ttd-box p { margin: 2px 0; }
        
        /* Tabel Lampiran */
        .table-lampiran { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
            font-size: 10pt; 
        }
        .table-lampiran th, .table-lampiran td { 
            border: 1px solid black; 
            padding: 6px 4px; 
            text-align: center; 
            vertical-align: middle;
        }
        .table-lampiran th { font-weight: bold; }
        .text-left { text-align: left !important; padding-left: 8px !important; }
    </style>
</head>
<body>

    <!-- ================= HALAMAN 1 (SURAT UTAMA) ================= -->

    <!-- KOP SURAT -->
    <table class="kop-surat">
        <tr>
            <td width="15%" class="text-center">
                <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Pemkab.png" width="90">
            </td>
            <td width="85%" class="text-center" style="line-height: 1.1;">
                <div style="font-size: 14pt; margin-bottom: 2px;">PEMERINTAH KABUPATEN KEDIRI</div>
                <div class="bold" style="font-size: 16pt; margin-bottom: 2px;">DINAS KESEHATAN</div>
                <div class="bold" style="font-size: 18pt; margin-bottom: 5px;">RSUD SIMPANG LIMA GUMUL</div>
                <div style="font-size: 10pt; line-height: 1.2;">
                    Jalan Galuh Candra Kirana Nomor 8, Kecamatan Ngasem, Kabupaten Kediri 64182<br>
                    Telepon (0354) 2891400 Fax. (0354) 2891414<br>
                    Pos-el : rsudslg@kedirikab.go.id Laman : rsudslg.kedirikab.go.id
                </div>
            </td>
        </tr>
    </table>
    <div class="kop-surat-2"></div>

    <!-- JUDUL SURAT -->
    <div class="text-center bold" style="margin-bottom: 30px;">
        <span style="text-decoration: underline; font-size: 12pt;">SURAT PENUGASAN KERJA KLINIS</span><br>
        <span style="font-size: 11pt;">NOMOR : {{ $no_spk }}</span>
    </div>

    <div>
        <p style="margin-bottom: 15px;">Yang bertanda tangan dibawah ini :</p>

        <!-- Tabel Biodata Direktur -->
        <table style="margin-left: 20px; border:none; margin-bottom: 20px;" cellpadding="4">
            <tr>
                <td width="160">Nama</td>
                <td width="15">:</td>
                <td>{{ $direktur_nama }}</td>
            </tr>
            <tr>
                <td>Pangkat/ Golongan</td>
                <td>:</td>
                <td>{{ $direktur_pangkat }}</td>
            </tr>
            <tr>
                <td>NIP</td>
                <td>:</td>
                <td>{{ $direktur_nip }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>Direktur UOBK RSUD Simpang Lima Gumul Kediri</td>
            </tr>
        </table>

        <!-- Paragraf Isi -->
        <p style="text-align: justify; margin-bottom: 15px;">
        @if(isset($is_individu) && $is_individu)
            Dengan ini memberi Kewenangan Klinis sebagaimana tercantum dalam Rincian Kewenangan Klinis kepada {{ $nama_kualifikasi }} atas nama <span class="bold">{{ $nama_pegawai_individu }}</span>.
        @else
            Dengan ini memberi Kewenangan Klinis sebagaimana tercantum dalam Rincian Kewenangan Klinis kepada {{ $nama_kualifikasi }} (Nama Terlampir).
        @endif
        </p>

        <p style="text-align: justify; margin-bottom: 25px;">
            Kepada yang bersangkutan berhak dan dapat memberikan Asuhan Keperawatan / Kebidanan kepada pasien sesuai Rincian Kewenangan Klinis Keperawatan / Kebidanan (terlampir). Surat Penugasan Kerja Klinis berlaku mulai tanggal {{ $tgl_terbit }} sampai dengan {{ $tgl_expired }}.
        </p>

        <!-- Blok Tanda Tangan Halaman 1 -->
        <div class="anti-potong">
            <p style="margin-bottom: 0;">Demikian Surat Penugasan Kerja Klinis ini untuk dilaksanakan.</p>
            
            <div class="ttd-container">
                <div class="ttd-box">
                    <p>Ditetapkan di : Kediri</p>
                    <p style="text-decoration: underline; margin-bottom: 15px;">Pada Tanggal : {{ $tgl_terbit }}</p>
                    
                    <p>DIREKTUR</p>
                    <p>UOBK RSUD SIMPANG LIMA GUMUL</p>
                    <p>KEDIRI</p>
                    
                    <div style="margin: 15px 0;">
                        <img src="{{ $qr_url }}" alt="QR Code" width="100">
                    </div>
                    
                    <p class="bold" style="text-decoration: underline;">{{ $direktur_nama }}</p>
                    <p>NIP. {{ $direktur_nip }}</p>
                </div>
                <div style="clear: both;"></div> 
            </div>
        </div>
    </div>


    <!-- ================= HALAMAN 2 (LAMPIRAN TABEL KOLEKTIF) ================= -->
    <!-- (Hanya akan dirender oleh DomPDF apabila mode yang dipilih BUKAN individu) -->

    @if(!isset($is_individu) || !$is_individu)
    
    <!-- Pembatas Halaman untuk memulai halaman baru -->
    <div class="page-break"></div>

    <!-- Header Lampiran -->
    <table style="width: 100%; border:none; margin-bottom: 15px;" cellpadding="3">
        <tr>
            <td width="100">Lampiran</td>
            <td width="15">:</td>
            <td>Surat Penugasan Kerja Klinis</td>
        </tr>
        <tr>
            <td>Nomor</td>
            <td>:</td>
            <td>{{ $no_spk }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ $tgl_terbit }}</td>
        </tr>
    </table>

    <!-- Tabel Daftar Pegawai -->
    <table class="table-lampiran">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="22%">NAMA PEGAWAI</th>
                <th width="15%">RUANG</th>
                <th width="13%">PROFESI</th>
                <th width="5%">JK</th>
                <th width="25%">PANGKAT /<br>GOLONGAN</th>
                <th width="15%">PK</th>
            </tr>
        </thead>
        <tbody>
            @foreach($list_peserta as $peserta)
            <tr>
                <td>{{ $peserta['no'] }}</td>
                <td class="text-left">{{ $peserta['nama_pegawai'] }}</td>
                <td>{{ $peserta['ruang'] }}</td>
                <td>{{ $peserta['profesi'] }}</td>
                <td>{{ $peserta['jk'] }}</td>
                <td>{{ $peserta['pangkat_golongan'] }}</td>
                <td>{{ $peserta['pk'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Blok Tanda Tangan Lampiran -->
    <div class="anti-potong">
        <div class="ttd-container" style="margin-top: 40px;">
            <div class="ttd-box">
                <p>Ditetapkan di : Kediri</p>
                <p style="text-decoration: underline; margin-bottom: 15px;">Pada Tanggal : {{ $tgl_terbit }}</p>
                
                <p>DIREKTUR</p>
                <p>UOBK RSUD SIMPANG LIMA GUMUL</p>
                <p>KEDIRI</p>
                
                <div style="margin: 15px 0;">
                    <img src="{{ $qr_url }}" alt="QR Code" width="100">
                </div>
                
                <p class="bold" style="text-decoration: underline;">{{ $direktur_nama }}</p>
                <p>NIP. {{ $direktur_nip }}</p>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
    @endif
  
</body>
</html>