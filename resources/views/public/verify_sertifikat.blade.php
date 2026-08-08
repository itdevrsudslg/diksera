<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Sertifikat - DIKSERA RSUD SLG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* Menangkap warna dari Controller, default ke Ungu RSUD SLG */
            --main-theme: {{ $themeColor ?? '#512da8' }};
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f4f8;
            background-image: url("https://www.transparenttextures.com/patterns/cubes.png");
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }

        .card-main {
            border: none;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
            margin: auto;
        }

        .header-gradient {
            /* Warna Header dinamis mengikuti tema bidang */
            background: linear-gradient(135deg, var(--main-theme) 0%, #212529 100%) !important;
            padding: 40px 30px;
            position: relative;
            color: white;
            text-align: center;
        }

        .shield-icon-bg {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 4rem;
            opacity: 0.1;
        }

        .logo-container {
            background: white;
            padding: 12px 20px;
            border-radius: 15px;
            display: inline-block;
            margin-bottom: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .glass-body {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            margin-top: -25px;
            border-radius: 30px 30px 0 0;
            padding: 35px 30px;
            position: relative;
        }

        /* Warna teks dan border dinamis */
        .text-primary { color: var(--main-theme) !important; }
        .bg-primary { background-color: var(--main-theme) !important; }
        .border-primary { border-color: var(--main-theme) !important; }

        .badge-verified {
            background-color: #e6f7ed;
            color: #198754;
            border: 1px solid #d1e7dd;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 1px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
        }

        .dot-pulse {
            width: 8px;
            height: 8px;
            background-color: #198754;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
        }

        .info-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .info-box {
            background: #f8f9fa;
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 12px;
        }

        .authority-divider {
            position: relative;
            text-align: center;
            margin: 30px 0 20px 0;
        }
        .authority-divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            border-top: 1px dashed #dee2e6;
        }
        .authority-divider span {
            position: relative;
            background: #fff;
            padding: 0 15px;
            font-size: 10px;
            font-weight: 800;
            color: #adb5bd;
            text-transform: uppercase;
        }

        .footer-info {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #adb5bd;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card-main mx-auto">
        <div class="header-gradient">
            <div class="shield-icon-bg"><i class="fas fa-shield-alt"></i></div>
            <div class="logo-container">
                <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Logo.png" alt="Logo" height="45">
            </div>
            <h1 class="h4 fw-bolder mb-1">DIKSERA VALIDASI</h1>
            <p class="small opacity-75 mb-0">Sistem Informasi Pendidikan & Sertifikasi Perawat</p>
        </div>

        <div class="glass-body">
            <div class="text-center mb-4">
                <div class="badge-verified">
                    <span class="dot-pulse"></span>
                    Sertifikat Terverifikasi Asli
                </div>
            </div>

            <div class="mb-4">
                <label class="info-label text-primary">Nama Lengkap Pemilik</label>
                <h2 class="h4 fw-bold text-dark mb-0">
                    {{ strtoupper($lisensi->user->perawatProfile->nama_lengkap ?? $lisensi->user->name) }}
                </h2>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div>
                        <label class="info-label text-muted">Nomor Sertifikat</label>
                        <p class="mb-0 small fw-bold text-dark font-monospace">{{ $lisensi->nomor }}</p>
                    </div>
                </div>
                <div class="col-12">
                 <div class="info-box">
                        <label class="info-label text-primary">Jenis Dokumen / Layanan</label>
                        <p class="mb-0 small fw-bold text-dark">
                            @if(request()->query('doc') === 'spk')
                                Surat Penugasan Kerja Klinis (SPK & SPKK)
                            @else
                                {{ $lisensi->nama ?? 'PK -' }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="border-start border-4 border-primary ps-3 mb-4">
                <label class="info-label text-primary">Jenjang Klinis / KFK</label>
                <p class="fw-bold mb-0 text-dark">
                    @if(is_array($lisensi->kfk))
                        {{ strtoupper(implode(', ', $lisensi->kfk)) }}
                    @elseif(is_string($lisensi->kfk))
                        {{ strtoupper(str_replace(['[', ']', '"'], '', $lisensi->kfk)) }}
                    @else
                        -
                    @endif
                </p>
            </div>

            <div class="info-box bg-danger bg-opacity-10 border-danger border-opacity-10 d-flex justify-content-between align-items-center mb-4">
                <div>
                    <label class="info-label text-danger">Masa Berlaku Hingga</label>
                    <p class="mb-0 fw-bold text-danger">{{ \Carbon\Carbon::parse($lisensi->tgl_expired)->isoFormat('D MMMM Y') }}</p>
                </div>
                <i class="fas fa-calendar-check text-danger opacity-25 fs-3"></i>
            </div>

          <div class="authority-divider">
                <span>Otoritas Pengesah</span>
            </div>

            <!-- BLOK PENGESAH -->
            <!-- Jika kredensialing DAN yang di-scan BUKAN qr code milik spk, maka tampil 3 TTD -->
            @if($lisensi->metode === 'interview_only' && request()->query('doc') !== 'spk')
                
                <!-- JIKA KREDENSIALING (RKK): TAMPIL 3 TTD -->
                <div class="d-flex flex-column gap-3 mb-2">
                    
                    <!-- Direktur -->
                    <div class="p-3 rounded-4 border border-secondary border-opacity-25 bg-white shadow-sm d-flex align-items-start gap-3">
                        <div class="bg-light p-2 rounded-3 border">
                            <i class="fas fa-user-tie fs-3 text-secondary"></i>
                        </div>
                        <div>
                            <p class="info-label text-muted mb-1">Direktur UOBK RSUD Simpang Lima Gumul</p>
                            <p class="fw-bold mb-0 text-dark small">dr. TONY WIDYANTO, Sp.OG (K)</p>
                            <p class="font-monospace mb-0 text-muted" style="font-size: 10px;">NIP. 19750714 200212 1 006</p>
                        </div>
                    </div>
                    
                    <!-- Ketua Komite Keperawatan -->
                    <div class="p-3 rounded-4 border border-secondary border-opacity-25 bg-white shadow-sm d-flex align-items-start gap-3">
                        <div class="bg-light p-2 rounded-3 border">
                            <i class="fas fa-user-md fs-3 text-secondary"></i>
                        </div>
                        <div>
                            <p class="info-label text-muted mb-1">Ketua Komite Keperawatan</p>
                            <p class="fw-bold mb-0 text-dark small">BAMBANG WAHYU SETYONO, S.Kep.,Ns</p>
                            <p class="font-monospace mb-0 text-muted" style="font-size: 10px;">NIP. 19841025 200901 1 003</p>
                        </div>
                    </div>

                    <!-- Ketua Sub Komite Kredensial -->
                    <div class="p-3 rounded-4 border border-secondary border-opacity-25 bg-white shadow-sm d-flex align-items-start gap-3">
                        <div class="bg-light p-2 rounded-3 border">
                            <i class="fas fa-user-check fs-3 text-secondary"></i>
                        </div>
                        <div>
                            <p class="info-label text-muted mb-1">Ketua Sub Komite Kredensial</p>
                            <p class="fw-bold mb-0 text-dark small">HARIS LISTYAWAN, S.Kep.,Ns</p>
                            <p class="font-monospace mb-0 text-muted" style="font-size: 10px;">NIP. 19820214 200901 1 003</p>
                        </div>
                    </div>

                </div>
            @else
                <!-- JIKA SPK/SPKK ATAU UJI KOMPETENSI (SERKOM): TAMPIL 1 TTD SAJA -->
                <div class="p-3 rounded-4 border border-secondary border-opacity-25 bg-white shadow-sm d-flex align-items-start gap-3 mb-2">
                    <div class="bg-light p-2 rounded-3 border">
                        <i class="fas fa-user-tie fs-3 text-secondary"></i>
                    </div>
                    <div>
                        <p class="info-label text-muted mb-1">Direktur UOBK RSUD Simpang Lima Gumul</p>
                        <p class="fw-bold mb-0 text-dark small">dr. TONY WIDYANTO, Sp.OG (K)</p>
                        <p class="font-monospace mb-0 text-muted" style="font-size: 10px;">NIP. 19750714 200212 1 006</p>
                    </div>
                </div>
            @endif

        </div>

    

        <div class="footer-info">
            UOBK RSUD SIMPANG LIMA GUMUL<br>
            KABUPATEN KEDIRI 
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="https://rsudslg.kedirikab.go.id" class="text-decoration-none text-muted small fw-bold text-uppercase tracking-widest">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Portal Utama
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>