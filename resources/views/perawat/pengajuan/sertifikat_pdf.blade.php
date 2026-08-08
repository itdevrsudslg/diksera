<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat {{ $lisensi->nomor }}</title>
<style>
    /* VARIABEL UTAMA 
       Mengambil data dari Controller, jika kosong default ke Ungu RSUD SLG 
    */
    @php 
        $main = $theme_color ?? '#512da8'; 
    @endphp

    /* --- PAGE SETUP --- */
    @page { margin: 0; size: 297mm 210mm; }

    body {
        margin: 0; padding: 0;
        font-family: 'Helvetica', 'Arial', sans-serif;
        background: #fff;
        -webkit-print-color-adjust: exact;
    }

    /* --- SIDEBAR (KIRI) --- */
    .container-sidebar {
        position: absolute;
        top: 0; left: 0; bottom: 0;
        width: 28%;
        height: 100%;
        background-color: {{ $main }}; /* DINAMIS */
        color: white;
        text-align: center;
        z-index: 1;
    }

    /* --- KONTEN UTAMA (KANAN) --- */
    .container-content {
        position: absolute;
        top: 0; left: 28%; bottom: 0; right: 0;
        width: 72%;
        height: 100%;
        background-color: #fff;
        padding: 40px 70px 40px 40px;
        box-sizing: border-box;
        z-index: 2;
    }

    /* --- ELEMEN SIDEBAR --- */
    .circle-decor {
        position: absolute; top: -50px; left: -50px;
        width: 150px; height: 150px;
        background-color: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .side-logo { 
    width: 200px;       /* Sesuaikan lebar yang diinginkan */
    height: 70px;    /* Ini adalah hasil 150 * 3/4 untuk dapet proporsi 3:4 */
    margin-top: 40px; 
    margin-bottom: 20px; 
    object-fit: contain; /* PENTING: Supaya logo tidak pecah/gepeng */
    display: block;
    margin-left: auto;
    margin-right: auto;
}

    .side-title {
        font-size: 8pt; text-transform: uppercase; letter-spacing: 1px;
        border-bottom: 1px solid rgba(255,255,255,0.3);
        padding-bottom: 10px; margin: 0 auto 30px auto;
        width: 80%; line-height: 1.4;
    }

    .photo-frame {
        width: 120px; height: 160px;
        border: 3px solid rgba(255,255,255,0.8);
        border-radius: 8px;
        margin: 0 auto 30px auto;
        background: #fff;
        overflow: hidden; position: relative;
    }
    .photo-frame img { width: 100%; height: 100%; object-fit: cover; display: block; }
    
    .no-photo {
        width: 100%; height: 100%; display: block;
        padding-top: 70px; box-sizing: border-box;
        background: rgba(255,255,255,0.2); 
        color: white; /* Putih agar kontras di sidebar warna apa pun */
        font-size: 8pt; font-weight: bold;
    }

    .badge-box {
        width: 85%; min-height: 90px;
        border: 2px solid #fff; border-radius: 10px; margin: 0 auto;
        background-color: rgba(0, 0, 0, 0.2); /* Gelap transparan agar otomatis masuk ke warna apa pun */
        text-align: center; padding: 10px 5px; box-sizing: border-box;
        display: table;
    }
    .badge-inner { display: table-cell; vertical-align: middle; }
    
    .badge-label { font-size: 8pt; display: block; margin-bottom: 5px; color: rgba(255,255,255,0.8); letter-spacing: 1px; }

    /* --- FONT PK --- */
    .badge-value { font-weight: bold; line-height: 1.1; display: block; word-wrap: break-word; color: white; }

    .font-xl { font-size: 36pt; }
    .font-lg { font-size: 26pt; }
    .font-md { font-size: 18pt; }
    .font-sm { font-size: 14pt; }

    .watermark { position: absolute; right: -20px; bottom: -20px; width: 350px; opacity: 0.05; z-index: -1; }

    /* --- HEADER TABLE --- */
    .header-table { width: 100%; margin-bottom: 10px; border-collapse: collapse; }
    .header-left-cell { text-align: left; vertical-align: top; width: 50%; }
    .header-right-cell { text-align: right; vertical-align: top; width: 50%; padding-left: 10px; }

    .header-title { margin: 0; font-size: 14pt; color: {{ $main }}; text-transform: uppercase; font-weight: bold; }
    .header-address { margin: 4px 0 0 0; font-size: 8pt; color: #546e7a; line-height: 1.3; }

    /* --- KOTAK NOMOR SERTIFIKAT --- */
    .cert-number-box {
        display: inline-block;
        font-family: 'Courier New', monospace;
        font-size: 10pt;
        font-weight: bold;
        color: {{ $main }}; 
        background: {{ $main }}10; /* Hex + 10 = Opacity 10% */
        border: 1px solid {{ $main }}40; 
        padding: 8px 10px;
        border-radius: 4px;
        text-align: center;
        width: 100%;
        box-sizing: border-box;
    }

    /* --- JUDUL & KANDIDAT --- */
    .main-title { font-size: 28pt; font-weight: 800; color: {{ $main }}; margin: 90px 0 5px 0; text-transform: uppercase; }
    .main-title span { opacity: 0.7; }
    .subtitle { font-size: 10pt; color: {{ $main }}; margin-bottom: 20px; opacity: 0.8; }

    .candidate-box { border-left: 4px solid {{ $main }}; padding-left: 15px; margin-bottom: 20px; }
    .candidate-name { font-size: 20pt; font-weight: bold; color: {{ $main }}; margin: 0; text-transform: uppercase; }
    .candidate-id { font-size: 10pt; color: {{ $main }}; margin-top: 3px; opacity: 0.7; }

    /* --- DESKRIPSI BOX --- */
    .desc-box { 
        background-color: {{ $main }}05; /* Sangat Pudar */
        border: 1px solid {{ $main }}20; 
        border-radius: 6px; padding: 15px; margin-bottom: 15px; color: #37474f; font-size: 9pt; line-height: 1.4; 
    }
    .highlight { color: {{ $main }}; font-weight: bold; text-decoration: underline; }

    .event-info { margin-top: 10px; padding-top: 8px; border-top: 1px dashed {{ $main }}40; font-size: 8.5pt; color: #455a64; }

    /* --- FOOTER / TANDA TANGAN --- */
    .footer-section { width: 100%; margin-top: 20px; }
    .footer-col-spacer { width: 50%; display: inline-block; }
    .footer-col-sig { width: 48%; display: inline-block; text-align: center; vertical-align: top; }
    
    .sig-place-date { font-size: 9pt; color: {{ $main }}; margin-bottom: 5px; }
    .sig-title { font-weight: bold; font-size: 9pt; color: {{ $main }}; margin-bottom: 60px; }
    .sig-name { 
        font-weight: bold; color: {{ $main }}; 
        border-top: 2px solid {{ $main }}; 
        padding-top: 5px; display: inline-block; min-width: 180px; font-size: 10pt; 
    }
    .sig-nip { font-size: 9pt; color: {{ $main }}; margin-top: 2px; opacity: 0.8; }
</style>
</head>
<body>

    <div class="container-sidebar">
        <div class="circle-decor"></div>
 <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/logo-white.png" 
     class="side-logo" 
     alt="Logo"style="margin-top:60px;">
        <div class="side-title" >KOMITE KEPERAWATAN<br>RSUD SIMPANG LIMA GUMUL</div>

        <div class="photo-frame">
          @if(isset($profile) && !empty($profile->foto_3x4) && file_exists(storage_path('app/public/' . $profile->foto_3x4)))
    <img src="{{ asset('storage/' . $profile->foto_3x4) }}" alt="Foto">
@else
    <div class="no-photo">FOTO 3x4</div>
@endif
        </div>


        @php
            $finalPkString = $jenjang_kompetensi ?? 'PK -';
            $charLength = strlen($finalPkString);
            $fontClass = 'font-xl'; 
            if ($charLength > 20) {
                $fontClass = 'font-sm';
            } elseif ($charLength > 10) {
                $fontClass = 'font-md';
            } elseif ($charLength > 5) {
                $fontClass = 'font-lg';
            }
        @endphp

        <div class="badge-box">
            <div class="badge-inner">
                <span class="badge-label">LEVEL KOMPETENSI</span>
                <span class="badge-value {{ $fontClass }}">{{ $finalPkString }}</span>
            </div>
        </div>
    </div>

    <div class="container-content">
        <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Logo.png" class="watermark" alt="Watermark">

        <table class="header-table">
            <tr>
                <td class="header-left-cell">
                    <h2 class="header-title">RSUD SIMPANG LIMA GUMUL</h2>
                    <p class="header-address">Jl. Galuh Candra Kirana Ds. Tugurejo Kec. Ngasem<br>Website: rsudslg.kedirikab.go.id</p>
                </td>
                <td class="header-right-cell">
                    <div class="cert-number-box">
                        NO:{{ $lisensi->nomor }}
                    </div>
                </td>
            </tr>
        </table>

        <h1 class="main-title">SERTIFIKAT <span>KOMPETENSI</span></h1>
        <div class="subtitle">Diberikan sebagai pengakuan kompetensi kepada:</div>

        <div class="candidate-box">
            <div class="candidate-name">{{ strtoupper($profile->nama_lengkap ?? $user->name) }}</div>
            <div class="candidate-id">
                @if(!empty($profile->nirp)) NIRP. {{ $profile->nirp }}
                @elseif(!empty($profile->nip)) NIP. {{ $profile->nip }}
                @else - @endif
            </div>
        </div>

        <div class="desc-box">
            Telah dinyatakan <strong>LULUS</strong> Uji Kompetensi.<br>
            Kompetensi: <span class="highlight">SERTIFIKAT KOMPETENSI</span><br>
            Area Klinik : <span class="highlight">{{ strtoupper($lisensi->bidang) }}</span>

            <div class="event-info">
                Diselenggarakan: {{ \Carbon\Carbon::parse($lisensi->tgl_mulai)->isoFormat('D MMMM Y') }}
                s/d {{ \Carbon\Carbon::parse($lisensi->tgl_diselenggarakan)->isoFormat('D MMMM Y') }}<br>
                Berlaku hingga: <strong>{{ \Carbon\Carbon::parse($lisensi->tgl_expired)->isoFormat('D MMMM Y') }}</strong>
            </div>
        </div>

        <div class="footer-section">
            <div class="footer-col-spacer"></div><div class="footer-col-sig">
    
    
    <div style="margin: 1px 0;">
        <div class="sig-place-date">Kediri, {{ $tgl_terbit_indo }}</div>
    <div class="sig-title">DIREKTUR UOBK RSUD SLG</div>
        <img src="{{ $qrCodeUrl }}" style="width: 80px; height: 80px;">
        <p style="font-size: 7pt; margin-top: 2px; color: {{ $main }}; font-family: sans-serif;">
            Validitas Elektronik<br>
            <!--<b>SCAN UNTUK VERIFIKASI</b>-->
        </p>
    </div>

    <div class="sig-name">dr. TONY WIDYANTO, Sp.OG (K)</div>
    <div class="sig-nip">NIP. 19750714 200212 1 006</div>
</div>
        </div>
    </div>
</body>
</html>