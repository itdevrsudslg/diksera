<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Lisensi - {{ $lisensi->nomor }}</title>
    <style>
        /* --- COPY STYLE DARI NOMOR 2 --- */
        @page { margin: 0; size: 297mm 210mm; } /* Landscape A4 */

        body {
            margin: 0; padding: 0;
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: #fff;
            -webkit-print-color-adjust: exact;
        }

        /* SIDEBAR (KIRI) */
        .container-sidebar {
            position: absolute;
            top: 0; left: 0; bottom: 0;
            width: 28%;
            height: 100%;
            background-color: #1565c0;
            color: white;
            text-align: center;
            z-index: 1;
        }

        /* KONTEN UTAMA (KANAN) */
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

        /* ELEMEN DEKORASI */
        .circle-decor {
            position: absolute; top: -50px; left: -50px;
            width: 150px; height: 150px;
            background-color: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .side-logo { width: 90px; height: auto; margin-top: 40px; margin-bottom: 20px; }

        .side-title {
            font-size: 8pt; text-transform: uppercase; letter-spacing: 1px;
            border-bottom: 1px solid rgba(255,255,255,0.3);
            padding-bottom: 10px; margin: 0 auto 30px auto;
            width: 80%; line-height: 1.4;
        }

        /* FOTO PROFILE */
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
            background: rgba(255,255,255,0.2); color: #1565c0; font-size: 8pt; font-weight: bold;
        }

        /* BADGE PK */
        .badge-box {
            width: 85%; min-height: 90px;
            border: 2px solid #fff; border-radius: 10px; margin: 0 auto;
            background-color: rgba(13, 71, 161, 0.5);
            text-align: center; padding: 10px 5px; box-sizing: border-box;
            display: table;
        }
        .badge-inner { display: table-cell; vertical-align: middle; }
        .badge-label { font-size: 8pt; display: block; margin-bottom: 5px; color: #bbdefb; letter-spacing: 1px; }
        .badge-value { font-weight: bold; line-height: 1.1; display: block; word-wrap: break-word; }

        /* UKURAN FONT DINAMIS */
        .font-xl { font-size: 36pt; }
        .font-lg { font-size: 26pt; }
        .font-md { font-size: 18pt; }
        .font-sm { font-size: 14pt; }

        .watermark { position: absolute; right: -20px; bottom: -20px; width: 350px; opacity: 0.05; z-index: -1; }

        /* HEADER & KONTEN */
        .header-table { width: 100%; margin-bottom: 10px; border-collapse: collapse; }
        .header-left-cell { text-align: left; vertical-align: top; width: 50%; }
        .header-right-cell { text-align: right; vertical-align: top; width: 50%; padding-left: 10px; }
        .header-title { margin: 0; font-size: 14pt; color: #1565c0; text-transform: uppercase; font-weight: bold; }
        .header-address { margin: 4px 0 0 0; font-size: 8pt; color: #546e7a; line-height: 1.3; }

        /* NOMOR SERTIFIKAT BOX */
        .cert-number-box {
            display: inline-block;
            font-family: 'Courier New', monospace;
            font-size: 11pt;
            font-weight: bold;
            color: #0d47a1;
            background: #e3f2fd;
            padding: 8px 10px;
            border-radius: 4px;
            border: 1px solid #bbdefb;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
            word-wrap: break-word;
            white-space: normal;
        }

        .main-title { font-size: 28pt; font-weight: 800; color: #0d47a1; margin: 10px 0 5px 0; text-transform: uppercase; }
        .main-title span { color: #2196f3; }
        .subtitle { font-size: 10pt; color: #1976d2; margin-bottom: 20px; }

        .candidate-box { border-left: 4px solid #2979ff; padding-left: 15px; margin-bottom: 20px; }
        .candidate-name { font-size: 20pt; font-weight: bold; color: #01579b; margin: 0; text-transform: uppercase; }
        .candidate-id { font-size: 10pt; color: #0277bd; margin-top: 3px; }

        .desc-box { background-color: #f1f8ff; border: 1px solid #bbdefb; border-radius: 6px; padding: 15px; margin-bottom: 15px; color: #37474f; font-size: 9pt; line-height: 1.4; }
        .highlight { color: #01579b; font-weight: bold; text-decoration: underline; }

        .event-info { margin-top: 10px; padding-top: 8px; border-top: 1px dashed #90caf9; font-size: 8.5pt; color: #455a64; }

        .footer-section { width: 100%; margin-top: 20px; }
        .footer-col-spacer { width: 50%; display: inline-block; }
        .footer-col-sig { width: 48%; display: inline-block; text-align: center; vertical-align: top; }
        .sig-place-date { font-size: 9pt; color: #1565c0; margin-bottom: 5px; }
        .sig-title { font-weight: bold; font-size: 9pt; color: #0d47a1; margin-bottom: 60px; }
        .sig-name { font-weight: bold; color: #0d47a1; border-top: 2px solid #1565c0; padding-top: 5px; display: inline-block; min-width: 180px; font-size: 10pt; }
        .sig-nip { font-size: 9pt; color: #1976d2; margin-top: 2px; }
    </style>
</head>
<body>

    <div class="container-sidebar">
        <div class="circle-decor"></div>

    <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Pemkab.png" class="side-logo">

        <div class="side-title">PEMERINTAH KABUPATEN KEDIRI<br>DINAS KESEHATAN<br>UOBK RSUD SLG</div>

        <div class="photo-frame">
            @if(isset($profile) && !empty($profile->foto_3x4) && file_exists(storage_path('app/public/' . $profile->foto_3x4)))
                <img src="{{ storage_path('app/public/' . $profile->foto_3x4) }}" alt="Foto">
            @else
                <div class="no-photo">FOTO 3x4</div>
            @endif
        </div>

        @php
            $pkRaw = $lisensi->kfk;
            $finalPkString = 'PK -';
            if (!empty($pkRaw)) {
                if (is_string($pkRaw) && (str_contains($pkRaw, '[') || str_contains($pkRaw, '"'))) {
                     $decoded = json_decode($pkRaw, true);
                     $finalPkString = (json_last_error() === JSON_ERROR_NONE && is_array($decoded))
                        ? implode(', ', $decoded)
                        : str_replace(['[', ']', '"'], '', $pkRaw);
                } elseif (is_array($pkRaw)) {
                    $finalPkString = implode(', ', $pkRaw);
                } else {
                    $finalPkString = $pkRaw;
                }
            }
            $charLength = strlen($finalPkString);

            // Logika Font Size
            $fontClass = 'font-xl';
            if ($charLength > 20) { $fontClass = 'font-sm'; }
            elseif ($charLength > 10) { $fontClass = 'font-md'; }
            elseif ($charLength > 5) { $fontClass = 'font-lg'; }
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
                    <h2 class="header-title">SURAT KETERANGAN LISENSI</h2>
                    <p class="header-address">Jl. Galuh Candra Kirana Ds. Tugurejo Kec. Ngasem<br>website: rsudslg.kedirikab.go.id</p>
                </td>
                <td class="header-right-cell">
                    <div class="cert-number-box">
                        NO: {{ $lisensi->nomor }}
                    </div>
                </td>
            </tr>
        </table>

        <h1 class="main-title">LISENSI <span>KOMPETENSI</span></h1>
        <div class="subtitle">Diberikan sebagai bukti kewenangan klinis kepada:</div>

        <div class="candidate-box">
            <div class="candidate-name">{{ strtoupper($profile->nama_lengkap ?? $user->name) }}</div>
            <div class="candidate-id">
                @if(!empty($profile->nirp)) NIRP. {{ $profile->nirp }}
                @elseif(!empty($profile->nip)) NIP. {{ $profile->nip }}
                @else - @endif
            </div>
        </div>

        <div class="desc-box">
            Telah memenuhi persyaratan kredensial dan diberikan kewenangan klinis.<br>
            Jenis Lisensi: <span class="highlight">{{ strtoupper($lisensi->nama) }}</span><br>
            Bidang Keahlian: <span class="highlight">{{ strtoupper($lisensi->bidang) }}</span>

            <div class="event-info">
                Diterbitkan: {{ \Carbon\Carbon::parse($lisensi->tgl_terbit)->isoFormat('D MMMM Y') }}<br>
                Berlaku hingga: <strong>{{ \Carbon\Carbon::parse($lisensi->tgl_expired)->isoFormat('D MMMM Y') }}</strong>
            </div>
        </div>

        <div class="footer-section">
            <div class="footer-col-spacer"></div><div class="footer-col-sig">
                <div class="sig-place-date">Kediri, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</div>
                <div class="sig-title">DIREKTUR UOBK RSUD SLG</div>
                <div class="sig-name">dr. TONY WIDYANTO, Sp.OG (K)</div>
                <div class="sig-nip">NIP. 19750714 200212 1 006</div>
            </div>
        </div>
    </div>
</body>
</html>
