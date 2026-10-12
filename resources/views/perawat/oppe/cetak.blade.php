<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mengunduh OPPE - {{ $oppe->user->name }}...</title>
    
    <!-- Panggil Library html2pdf.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        /* Reset dasar untuk memastikan ukuran presisi dan tidak melebar */
        *, *::before, *::after {
            box-sizing: border-box;
        }
        body { 
            font-family: "Times New Roman", Times, serif; 
            font-size: 12px; 
            color: #000; 
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        
        /* Layar Loading - Menutupi halaman saat proses generate PDF */
        #loading-screen {
            position: fixed; top: 0; left: 0; width: 100%; height: 100vh;
            background: #ffffff; z-index: 9999;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            font-family: sans-serif; color: #334155;
        }

        /* Container Dokumen PDF */
        #dokumen-oppe { 
            width: 100%;
            background: white; 
        }

        /* --- STYLING HEADER BIRU RESMI --- */
        .doc-header { 
            background-color: #f0f9ff; 
            border-top: 4px solid #0284c7; 
            border-bottom: 2px solid #bae6fd;
            padding: 20px 15px;
            text-align: center; 
            margin-bottom: 25px; 
        }
        .doc-title { 
            font-weight: bold; 
            font-size: 15px; 
            line-height: 1.5; 
            color: #0f172a;
            text-transform: uppercase;
        }
        
        /* Identitas Table */
        .table-identitas { width: 100%; margin-bottom: 20px; font-size: 13px; }
        .table-identitas td { padding: 4px 5px; vertical-align: top; }
        
        /* Main Data Table */
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-data th, .table-data td { border: 1px solid #000; padding: 6px; vertical-align: middle; }
        .table-data th { text-align: center; font-weight: bold; background-color: #f0f9ff; color: #0f172a; } 
        .kategori-row td { font-weight: bold; background-color: #e2e8f0; }
        
        /* --- MENCEGAH TABEL TERPOTONG (PAGE BREAK FIX) --- */
        tr { page-break-inside: avoid !important; }
        
        .petunjuk { margin-bottom: 15px; line-height: 1.4; padding: 10px; background: #f8fafc; border: 1px dashed #cbd5e1; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        
        /* --- PERBAIKAN TANDA TANGAN (FLEXBOX) --- */
        .footer-wrapper {
            page-break-inside: avoid !important; /* Menjaga catatan, petunjuk, dan TTD tetap menyatu */
            margin-top: 20px;
        }
        .signature-container { 
            width: 100%; 
            margin-top: 20px; 
            display: flex; 
            justify-content: space-between; 
            align-items: flex-end;
        }
        .signature-box { 
            width: 32%; 
            text-align: center; 
        }
        .qr-code { margin: 10px auto; width: 80px; height: 80px; display: block; }
        .nama-terang { text-decoration: underline; font-weight: bold; margin-bottom: 0; }
    </style>
</head>
<body>

    <!-- Layar Loading Sementara -->
    <div id="loading-screen">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation: spin 1s linear infinite; margin-bottom: 15px;">
            <line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
        </svg>
        <style>@keyframes spin { 100% { transform: rotate(360deg); } }</style>
        <h2>Memproses Unduhan...</h2>
        <p>Mohon tunggu, dokumen PDF sedang dibuat. Tab ini akan menutup otomatis.</p>
    </div>

    <!-- Dokumen Asli -->
    <div id="dokumen-oppe">
        
        <!-- Header Resmi Biru Muda -->
        <div class="doc-header">
            <div class="doc-title">
                PENILAIAN KINERJA PERAWAT (PRAKTIK PROFESIONAL BERKELANJUTAN)<br>
                ON GOING PROFESIONAL PRACTICE EVALUATION (OPPE)<br>
                PERIODE {{ $oppe->periode }}
            </div>
        </div>

        @php
            // PERBAIKAN: Mengonversi array ke string sebelum dicetak
            
            // 1. Unit Kerja
            $unitKerja = optional($oppe->user->pekerjaans->first())->unit_kerja ?? '-';
            $unitKerjaText = is_array($unitKerja) ? implode(', ', $unitKerja) : $unitKerja;

            // 2. Kualifikasi (Bidang & KFK)
            $bidang = $oppe->lisensi->bidang ?? '';
            $kfk = $oppe->lisensi->kfk ?? '';
            
            $bidangText = is_array($bidang) ? implode(', ', $bidang) : $bidang;
            $kfkText = is_array($kfk) ? implode(', ', $kfk) : $kfk;
            $kualifikasiText = trim($bidangText . ' ' . $kfkText);
        @endphp

        <!-- Identitas Pegawai -->
        <table class="table-identitas">
            <tr>
                <td width="15%" class="fw-bold">Nama Perawat/Bidan</td>
                <td width="2%">:</td>
                <td width="83%">{{ $oppe->user->profile->nama_lengkap ?? $oppe->user->name }}</td>
            </tr>
            <tr>
                <td class="fw-bold">NIP / NIRP</td>
                <td>:</td>
                <td>{{ $oppe->user->profile->nip ?? $oppe->user->profile->nirp ?? '-' }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Unit</td>
                <td>:</td>
                <td>{{ $unitKerjaText }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Kualifikasi</td>
                <td>:</td>
                <td>{{ $kualifikasiText }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Tanggal Periode</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($oppe->periode)->translatedFormat('d F Y') }}</td>
            </tr>
        </table>

        <!-- Petunjuk Pengisian -->
        <div class="petunjuk">
            <strong>Petunjuk Pengisian :</strong><br>
            Isilah tabel di bawah ini dengan menggunakan angka 1, 2, dan 3<br>
            1 = indikator kinerja belum ditunjukan | 2 = indikator kinerja yang ditunjukkan perlu diperbaiki | 3 = indikator kinerja yang ditunjukkan sesuai dengan standar
        </div>

        <!-- Tabel Penilaian -->
        <table class="table-data">
            <thead>
                <tr>
                    <th width="5%">NO</th>
                    <th width="50%">KOMPETENSI KLINIK {{ $kfkText }}</th>
                    <th width="15%">DIRI SENDIRI</th>
                    <th width="15%">ATASAN/KARU</th>
                    <th width="15%">TEMAN SEJAWAT</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($instrumenGrouped as $kategori => $items)
                    <tr class="kategori-row">
                        <td></td>
                        <td colspan="4">{{ $kategori }}</td>
                    </tr>
                    @foreach($items as $item)
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ $item['deskripsi'] }}</td>
                            <td class="text-center">{{ $item['skor_self'] ?? '-' }}</td>
                            <td class="text-center">{{ $item['skor_karu'] ?? '-' }}</td>
                            <td class="text-center">{{ $item['skor_peer'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                @endforeach
                
                <!-- Baris Total -->
                <tr>
                    <td colspan="2" class="fw-bold text-center">NILAI TOTAL</td>
                    <td class="text-center fw-bold">{{ $totalSelf }}</td>
                    <td class="text-center fw-bold">{{ $totalKaru }}</td>
                    <td class="text-center fw-bold">{{ $totalPeer }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="fw-bold text-center">NILAI AKHIR</td>
                    <td colspan="3" class="text-center fw-bold" style="font-size: 15px; color: #0284c7;">
                        {{ $oppe->nilai_akhir }}%
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- BUNGKUSAN FOOTER: Menyatukan Catatan, Petunjuk Akhir, dan TTD -->
        <div class="footer-wrapper">
            <!-- Catatan & Keterangan -->
            <div style="margin-bottom: 20px;">
                <strong>Catatan Evaluator:</strong><br>
                <i>{{ $oppe->catatan_evaluator ?? '-' }}</i>
            </div>
            
            <div class="petunjuk" style="margin-bottom: 10px;">
                Nilai Akhir = [Jumlah Penilaian Indikator Kinerja Perawatan dan Bidan / Nilai Maksimum (seluruh indikator kinerja x 3)] X 100%<br>
                80-100% : Kinerja Memuaskan/Baik &nbsp; | &nbsp; 50-79% : Perlu Peningkatan/Cukup Baik &nbsp; | &nbsp; 10-49% : Tidak Memuaskan/Kurang Baik
            </div>

            <!-- Kolom Tanda Tangan -->
            <div class="signature-container">
                <div class="signature-box">
                    <div class="mb-2">{{ \App\Services\Penandatangan::label('oppe.ttd1') }}</div>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode('Telah dievaluasi oleh Sejawat: ' . ($oppe->peer->profile->nama_lengkap ?? $oppe->peer->name)) }}" alt="QR Sejawat" class="qr-code">
                    <p class="nama-terang">{{ $oppe->peer->profile->nama_lengkap ?? $oppe->peer->name }}</p>
                    <small>NIP/NIRP. {{ $oppe->peer->profile->nip ?? $oppe->peer->profile->nirp ?? '-' }}</small>
                </div>
                <div class="signature-box">
                    <div class="mb-2">{{ \App\Services\Penandatangan::label('oppe.ttd2') }}</div>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode('Telah diisi oleh: ' . ($oppe->user->profile->nama_lengkap ?? $oppe->user->name)) }}" alt="QR Perawat" class="qr-code">
                    <p class="nama-terang">{{ $oppe->user->profile->nama_lengkap ?? $oppe->user->name }}</p>
                    <small>NIP/NIRP. {{ $oppe->user->profile->nip ?? $oppe->user->profile->nirp ?? '-' }}</small>
                </div>
                <div class="signature-box">
                    <div class="mb-2">{{ \App\Services\Penandatangan::label('oppe.ttd3') }}</div>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode('Telah disetujui dan dievaluasi oleh Atasan: ' . ($oppe->evaluator->profile->nama_lengkap ?? $oppe->evaluator->name)) }}" alt="QR Atasan" class="qr-code">
                    <p class="nama-terang">{{ $oppe->evaluator->profile->nama_lengkap ?? $oppe->evaluator->name }}</p>
                    <small>NIP/NIRP. {{ $oppe->evaluator->profile->nip ?? $oppe->evaluator->profile->nirp ?? '-' }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Auto-Download & Auto-Close -->
    <script>
        // Jalan otomatis begitu halaman selesai dimuat
        window.onload = function() {
            const element = document.getElementById('dokumen-oppe');
            
            const opt = {
                margin:       15, // Memberikan margin 15mm secara merata di semua sisi halaman PDF
                filename:     'OPPE_{{ str_replace(" ", "_", $oppe->user->profile->nama_lengkap ?? $oppe->user->name) }}_{{ $oppe->periode }}.pdf',
                image:        { type: 'jpeg', quality: 1 },
                html2canvas:  { 
                    scale: 2, 
                    useCORS: true
                },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' },
                // Aturan jeda halaman disederhanakan agar diserahkan pada CSS `page-break-inside: avoid`
                pagebreak:    { mode: ['css', 'legacy'], avoid: 'tr' } 
            };

            // Beri sedikit jeda 500ms agar font & styling render utuh ke kanvas
            setTimeout(() => {
                html2pdf().set(opt).from(element).save().then(() => {
                    // Setelah PDF selesai terunduh, tutup tab secara otomatis
                    window.close();
                });
            }, 500);
        }
    </script>
</body>
</html>