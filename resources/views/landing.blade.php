@extends('layouts.landing', ['title' => 'DIKSERA – Digitalisasi Kompetensi Evaluasi Bidan dan Perawat'])

@push('styles')
<style>
    .hero-grid{
        width:100%;
        max-width:1120px;
        display:grid;
        grid-template-columns:minmax(0,1.4fr) minmax(0,1fr);
        gap:32px;
        align-items:center;
    }
    @media (max-width: 900px){
        .hero-grid{
            grid-template-columns:1fr;
            gap:24px;
        }
    }

    .section-shell{
        width:100%;
        max-width:1120px;
        margin:0 auto;
    }

    .section-title{
        font-size:20px;
        font-weight:600;
        margin-bottom:4px;
    }

    .section-subtitle{
        font-size:12px;
        color:#6b7280;
        margin-bottom:16px;
    }

    .feature-card{
        border-radius:18px;
        padding:14px 16px;
        background:#ffffff;
        border:1px solid #e5e7eb;
        box-shadow:0 10px 26px rgba(148,163,184,0.25);
        height:100%;
    }

    .feature-chip{
        display:inline-flex;
        align-items:center;
        gap:6px;
        padding:3px 10px;
        border-radius:999px;
        background:#eff6ff;
        font-size:10px;
        color:#2563eb;
        margin-bottom:6px;
    }

    .step-badge{
        width:32px;
        height:32px;
        border-radius:999px;
        display:flex;
        align-items:center;
        justify-content:center;
        background:linear-gradient(135deg,#2563eb,#60a5fa);
        color:#f9fafb;
        font-size:14px;
        font-weight:600;
        box-shadow:0 12px 30px rgba(37,99,235,0.45);
        flex-shrink:0;
    }

    .stat-pill{
        border-radius:18px;
        padding:10px 14px;
        background:#eef2ff;
        border:1px dashed #c7d2fe;
        font-size:11px;
    }

    .benefit-dot{
        width:8px;
        height:8px;
        border-radius:999px;
        background:#2563eb;
        flex-shrink:0;
        margin-top:5px;
    }

    .benefit-row{
        display:flex;
        gap:8px;
        margin-bottom:6px;
    }

    /* Chatbot */
    .diksera-chat-launcher{
        position:fixed;
        right:18px;
        bottom:18px;
        z-index:50;
    }
    .diksera-chat-bubble-btn{
        width:52px;
        height:52px;
        border-radius:999px;
        border:none;
        background:linear-gradient(135deg,#2563eb,#60a5fa);
        box-shadow:0 18px 38px rgba(37,99,235,0.55);
        display:flex;
        align-items:center;
        justify-content:center;
        cursor:pointer;
        color:#eff6ff;
        font-size:24px;
    }
    .diksera-chat-window{
        position:fixed;
        right:18px;
        bottom:80px;
        width:320px;
        max-height:480px;
        background:#ffffff;
        border-radius:20px;
        box-shadow:0 26px 64px rgba(15,23,42,0.55);
        border:1px solid #dbeafe;
        display:none;
        flex-direction:column;
        overflow:hidden;
        z-index:50;
    }
    .diksera-chat-header{
        padding:10px 12px;
        background:linear-gradient(135deg,#1d4ed8,#3b82f6);
        color:#eff6ff;
        display:flex;
        align-items:flex-start;
        gap:8px;
    }
    .diksera-chat-avatar{
        width:32px;
        height:32px;
        border-radius:999px;
        background:radial-gradient(circle at 20% 0,#eff6ff,#1d4ed8);
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:16px;
        box-shadow:0 10px 24px rgba(15,23,42,0.55);
        flex-shrink:0;
    }
    .diksera-chat-title{
        font-size:13px;
        font-weight:600;
        line-height:1.2;
    }
    .diksera-chat-subtitle{
        font-size:11px;
        opacity:.95;
    }
    .diksera-chat-close{
        margin-left:auto;
        border:none;
        background:transparent;
        color:#eff6ff;
        font-size:16px;
        cursor:pointer;
        padding:0 0 0 6px;
    }
    .diksera-chat-body{
        padding:10px 10px 6px;
        font-size:11px;
        background:#f9fafb;
        border-bottom:1px solid #e5e7eb;
    }
    .diksera-chat-badge{
        display:inline-flex;
        align-items:center;
        gap:5px;
        padding:3px 9px;
        border-radius:999px;
        background:rgba(15,23,42,0.08);
        color:#111827;
    }
    .diksera-chat-suggestions{
        margin-top:8px;
        display:flex;
        flex-wrap:wrap;
        gap:6px;
    }
    .diksera-chat-suggestion{
        padding:4px 8px;
        border-radius:999px;
        background:#eef2ff;
        border:none;
        font-size:10px;
        color:#1d4ed8;
        cursor:pointer;
    }
    .diksera-chat-messages{
        flex:1;
        padding:8px 10px 8px;
        overflow-y:auto;
        background:#f9fafb;
    }
    .diksera-chat-msg{
        margin-bottom:6px;
        display:flex;
    }
    .diksera-chat-msg-bot{
        justify-content:flex-start;
    }
    .diksera-chat-msg-user{
        justify-content:flex-end;
    }
    .diksera-chat-bubble{
        max-width:80%;
        padding:6px 9px;
        border-radius:12px;
        font-size:11px;
        line-height:1.35;
        white-space:pre-line;
    }
    .diksera-chat-bubble-bot{
        background:#ffffff;
        border:1px solid #e5e7eb;
        color:#111827;
    }
    .diksera-chat-bubble-user{
        background:#2563eb;
        color:#eff6ff;
    }
    .diksera-chat-footer{
        padding:8px;
        background:#f3f4f6;
        border-top:1px solid #e5e7eb;
    }
    .diksera-chat-input-wrap{
        display:flex;
        gap:6px;
    }
    .diksera-chat-input-wrap input{
        flex:1;
        border-radius:999px;
        border:1px solid #d1d5db;
        font-size:11px;
        padding:6px 10px;
        outline:none;
    }
    .diksera-chat-input-wrap button{
        border-radius:999px;
        border:none;
        padding:6px 10px;
        font-size:11px;
        background:#2563eb;
        color:#eff6ff;
        cursor:pointer;
    }
    
    .section-pk {
    width: 100%;
    max-width: 1000px;
    margin-top: 40px;
}
.pk-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    border: 1px solid #e5e7eb;
}
.pk-list {
    font-size: 0.9rem;
}
.pk-item {
    padding: 8px 0;
    border-bottom: 1px solid #f3f4f6;
}
.badge-pk {
    background: #eff6ff;
    color: #2563eb;
    padding: 4px 12px;
    border-radius: 6px;
    font-weight: 600;
}
</style>
@endpush

@section('content')

{{-- SECTION 1: HERO --}}
<div class="hero-grid">
    <div>
        <div class="hero-badge mb-2" style="display:inline-flex;align-items:center;gap:8px;padding:4px 12px;border-radius:999px;background:#eff6ff;font-size:11px;color:#2563eb;">
            <span>🩺 Platform Kompetensi Perawat & Bidan</span>
            <span style="width:4px;height:4px;border-radius:999px;background:#2563eb;"></span>
            <span>Terukur & terdokumentasi</span>
        </div>

        <h1 class="hero-title" style="font-size:32px;font-weight:600;margin-bottom:10px;">
            <span class="hero-highlight" style="background:linear-gradient(135deg,#1d4ed8,#38bdf8);-webkit-background-clip:text;color:transparent;">
                DIKSERA
            </span><br>
            Satu pintu pengelolaan<br>kompetensi, sertifikasi perawat & bidan.
        </h1>

        <p class="hero-text mb-3" style="font-size:13px;color:#4b5563;max-width:460px;">
            Kelola data, riwayat pelatihan, lisensi STR/SIP, ujian tertulis,
            hingga wawancara kompetensi dalam satu aplikasi terintegrasi. 
            Memudahkan komite keperawatan dan mendukung standar akreditasi rumah sakit.
        </p>

        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ route('login') }}" class="btn btn-solid-blue">
                Masuk ke DIKSERA
            </a>
            <a href="{{ route('register.perawat') }}" class="btn btn-outline-blue">
                Registrasi
            </a>
        </div>

        <div class="d-flex flex-wrap gap-3 mt-2">
            <div class="hero-metric">
                <strong style="font-size:16px;color:#1d4ed8;">DRH & Lisensi</strong><br>
                <span style="font-size:12px;color:#4b5563;">
                    Data perawat & bidan lengkap, valid & terdokumentasi.
                </span>
            </div>
            <div class="hero-metric">
                <strong style="font-size:16px;color:#1d4ed8;">CBT & Wawancara</strong><br>
                <span style="font-size:12px;color:#4b5563;">
                    Bank soal, ujian online, dan penilaian kompetensi.
                </span>
            </div>
        </div>
    </div>

    <div>
        <div class="hero-panel" style="border-radius:24px;background:#ffffff;border:1px solid #d1ddff;box-shadow:0 18px 40px rgba(15,23,42,0.12);padding:18px 20px;">
            <div class="mb-2" style="font-size:13px;font-weight:600;color:#1d4ed8;">
                Ringkasan Singkat DIKSERA
            </div>
            <ul class="small mb-2" style="padding-left:16px;color:#4b5563;font-size:12px;">
                <li>Registrasi perawat & bidan + pengisian Daftar Riwayat Hidup (DRH) secara digital.</li>
                <li>Upload & pemantauan masa berlaku STR/SIP dan sertifikat pelatihan.</li>
                <li>Penjadwalan ujian tertulis dan wawancara oleh komite keperawatan.</li>
                <li>Rekap penilaian kompetensi dan status akhir perawat & bidan.</li>
            </ul>
            <div class="alert alert-primary py-2 px-3 small mb-0">
                Setelah registrasi, akun perawat & bidan langsung terhubung dengan DRH dan siap digunakan
                untuk proses kompetensi berikutnya.
            </div>
        </div>
    </div>
</div>
<div class="section-shell mt-5">
    <div class="section-title">Persyaratan Jenjang Karier (PK)</div>
    <div class="section-subtitle">Detail persyaratan masa kerja dan SKP untuk kenaikan jenjang karier.</div>
    
    <div class="pk-card">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- PK 1 -->
            <div class="col">
                <div class="p-3 border-start border-primary border-4 bg-light h-100">
                    <h6 class="fw-bold">Pra PK ke PK 1</h6>
                    <ul class="pk-list ps-3">
                        <li>D3: Min. 2 Thn Klinis</li>
                        <li>S1/D4/Profesi: Min. 1 Thn Klinis</li>
                        <li><strong>SKP: 5</strong> (Aktif)</li>
                    </ul>
                </div>
            </div>
            <!-- PK 2 -->
            <div class="col">
                <div class="p-3 border-start border-primary border-4 bg-light h-100">
                    <h6 class="fw-bold">PK 1 ke PK 2</h6>
                    <ul class="pk-list ps-3">
                        <li>D3: Min. 4 Thn (PK 1)</li>
                        <li>S1/D4/Profesi: Min. 3 Thn (PK 1)</li>
                        <li><strong>SKP: 10</strong> (Aktif)</li>
                    </ul>
                </div>
            </div>
            <!-- PK 3 -->
            <div class="col">
                <div class="p-3 border-start border-primary border-4 bg-light h-100">
                    <h6 class="fw-bold">PK 2 ke PK 3</h6>
                    <ul class="pk-list ps-3">
                        <li>D3/S1/D4/Profesi: Min. 4 Thn (PK 2)</li>
                        <li><strong>SKP: 20</strong> (Aktif)</li>
                    </ul>
                </div>
            </div>
            <!-- PK 4 -->
            <div class="col">
                <div class="p-3 border-start border-primary border-4 bg-light h-100">
                    <h6 class="fw-bold">PK 3 ke PK 4</h6>
                    <ul class="pk-list ps-3">
                        <li>S1/D4/Profesi: Min. 6 Thn (PK 3)</li>
                        <li>S2: Min. 4 Thn (PK 3)</li>
                        <li><strong>SKP: 40</strong> (Aktif)</li>
                            <li>Membuat PDSA / PDCA / RCA lengkap sebanyak 3 Case</li>
                    </ul>
                </div>
            </div>
            <!-- PK 5 -->
            <div class="col">
                <div class="p-3 border-start border-primary border-4 bg-light h-100">
                    <h6 class="fw-bold">PK 4 ke PK 5</h6>
                    <ul class="pk-list ps-3">
                        <li>S1/D4/Profesi: Min. 6 Thn (PK 4)</li>
                        <li>S2: Min. 4 Thn (PK 4)</li>
                        <li><strong>SKP: 50</strong> (Aktif)</li>
                           <li>Membuat PDSA / PDCA / RCA lengkap sebanyak 5 Case</li>
                        
                    </ul>
                </div>
            </div>
        </div>
        <div class="mt-4 p-2 text-muted small bg-white border rounded">
            * <strong>Catatan:</strong> SKP wajib dalam status aktif. Selalu perhatikan tanggal mulai dan tanggal selesai masa aktif SKP.
        </div>
    </div>
</div>
{{-- SECTION 2: FITUR UTAMA --}}
<div class="section-shell mt-5">
    <div class="section-title">
        Fitur utama DIKSERA
    </div>
    <div class="section-subtitle">
        Dirancang khusus untuk kebutuhan Komite Keperawatan dan perawat klinis.
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="feature-card">
                <div class="feature-chip">
                    <span>DRH Perawat & Bidan</span>
                </div>
                <div style="font-size:14px;font-weight:600;margin-bottom:4px;">Daftar Riwayat Hidup Digital</div>
                <p style="font-size:12px;color:#4b5563;margin-bottom:8px;">
                    Profil perawat & bidan, riwayat pendidikan, pelatihan, organisasi, pekerjaan,
                    hingga keluarga terdokumentasi dalam satu sistem.
                </p>
                <ul style="padding-left:16px;font-size:11px;color:#6b7280;margin-bottom:0;">
                    <li>Format seragam sesuai kebutuhan komite.</li>
                    <li>Update data mandiri oleh perawat & bidan.</li>
                    <li>Memudahkan saat audit & akreditasi.</li>
                </ul>
            </div>
        </div>

        <div class="col-md-4">
            <div class="feature-card">
                <div class="feature-chip">
                    <span>Lisensi & Legal</span>
                </div>
                <div style="font-size:14px;font-weight:600;margin-bottom:4px;">Monitoring STR & SIP</div>
                <p style="font-size:12px;color:#4b5563;margin-bottom:8px;">
                    Penyimpanan bukti STR/SIP dan sertifikat pelatihan dengan pemantauan masa berlaku.
                </p>
                <ul style="padding-left:16px;font-size:11px;color:#6b7280;margin-bottom:0;">
                    <li>Pengingat masa berlaku dokumen.</li>
                    <li>Riwayat sertifikat tersimpan rapi.</li>
                    <li>Mendukung pemenuhan regulasi.</li>
                </ul>
            </div>
        </div>

        <div class="col-md-4">
            <div class="feature-card">
                <div class="feature-chip">
                    <span>Kompetensi Klinik</span>
                </div>
                <div style="font-size:14px;font-weight:600;margin-bottom:4px;">CBT, Wawancara & Penilaian</div>
                <p style="font-size:12px;color:#4b5563;margin-bottom:8px;">
                    Uji tertulis, wawancara, dan penilaian praktik dikonsolidasikan menjadi status akhir perawat.
                </p>
                <ul style="padding-left:16px;font-size:11px;color:#6b7280;margin-bottom:0;">
                    <li>Bank soal terpusat.</li>
                    <li>Rekap nilai per perawat & bidan.</li>
                    <li>Status kompeten/observasi jelas.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- SECTION 3: ALUR PENGGUNAAN --}}
<div class="section-shell mt-5">
    <div class="section-title">
        Alur penggunaan DIKSERA
    </div>
    <div class="section-subtitle">
        Tiga langkah sederhana dari registrasi hingga penetapan kompetensi.
    </div>

    <div class="d-flex flex-column gap-3">
        <div class="d-flex gap-3 align-items-start">
            <div class="step-badge">1</div>
            <div>
                <div style="font-size:13px;font-weight:600;">Registrasi & aktivasi akun perawat</div>
                <p style="font-size:12px;color:#4b5563;margin-bottom:4px;">
                    Perawat membuat akun, mengisi data dasar, dan mengajukan registrasi ke komite keperawatan.
                </p>
                <span class="stat-pill">
                    Klik tombol <strong>"Registrasi Perawat & Bidan"</strong> di bagian atas halaman untuk memulai.
                </span>
            </div>
        </div>

        <div class="d-flex gap-3 align-items-start">
            <div class="step-badge">2</div>
            <div>
                <div style="font-size:13px;font-weight:600;">Pengisian DRH & unggah dokumen</div>
                <p style="font-size:12px;color:#4b5563;margin-bottom:4px;">
                    Perawat melengkapi DRH, mengunggah STR/SIP, sertifikat pelatihan, riwayat pekerjaan,
                    serta dokumen pendukung lainnya.
                </p>
                <span class="stat-pill">
                    Seluruh riwayat tersimpan sehingga mudah ditinjau kapan saja.
                </span>
            </div>
        </div>

        <div class="d-flex gap-3 align-items-start">
            <div class="step-badge">3</div>
            <div>
                <div style="font-size:13px;font-weight:600;">Ujian, wawancara & penetapan status</div>
                <p style="font-size:12px;color:#4b5563;margin-bottom:4px;">
                    Komite menjadwalkan ujian tertulis dan wawancara. Hasil penilaian disatukan
                    dalam satu rekap, hingga penetapan status kompetensi perawat & bidan.
                </p>
                <span class="stat-pill">
                    Membantu komite menyusun rekomendasi penempatan dan pengembangan perawat & bidan.
                </span>
            </div>
        </div>
    </div>
</div>

{{-- SECTION 4: UNTUK SIAPA --}}
<div class="section-shell mt-5">
    <div class="section-title">
        Untuk siapa DIKSERA dibuat?
    </div>
    <div class="section-subtitle">
        Menghubungkan perawat & bidan dan komite keperawatan dalam satu ekosistem data.
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="feature-card">
                <div class="feature-chip">
                    <span>SDM</span>
                </div>
                <div style="font-size:13px;font-weight:600;margin-bottom:4px;">Rapi mengelola karier</div>
                <ul style="padding-left:16px;font-size:11px;color:#4b5563;margin-bottom:0;">
                    <li>DRH dan dokumen karier tersimpan aman.</li>
                    <li>Riwayat pelatihan dan sertifikat mudah dilacak.</li>
                    <li>Transparansi proses uji kompetensi.</li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="feature-card">
                <div class="feature-chip">
                    <span>Komite Keperawatan</span>
                </div>
                <div style="font-size:13px;font-weight:600;margin-bottom:4px;">Pengambilan keputusan lebih mudah</div>
                <ul style="padding-left:16px;font-size:11px;color:#4b5563;margin-bottom:0;">
                    <li>Rekap data perawat & bidan dalam satu dashboard.</li>
                    <li>Dukungan data untuk rekomendasi penempatan.</li>
                    <li>Memudahkan pemenuhan standar akreditasi.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- SECTION 5: BENEFIT RUMAH SAKIT --}}
<div class="section-shell mt-5">
    <div class="section-title">
        Manfaat bagi rumah sakit
    </div>
    <div class="section-subtitle">
        Tidak hanya membantu perawat, tetapi juga tata kelola SDM keperawatan.
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="feature-card">
                <div style="font-size:13px;font-weight:600;margin-bottom:6px;">Tata kelola SDM lebih rapi</div>
                <div class="benefit-row">
                    <div class="benefit-dot"></div>
                    <div style="font-size:11px;color:#4b5563;">
                        Data kompetensi perawat terkonsolidasi, memudahkan perencanaan penempatan dan pengembangan.
                    </div>
                </div>
                <div class="benefit-row">
                    <div class="benefit-dot"></div>
                    <div style="font-size:11px;color:#4b5563;">
                        Riwayat pelatihan dan lisensi mudah ditelusuri saat dibutuhkan.
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card">
                <div style="font-size:13px;font-weight:600;margin-bottom:6px;">Dukungan akreditasi</div>
                <div class="benefit-row">
                    <div class="benefit-dot"></div>
                    <div style="font-size:11px;color:#4b5563;">
                        Dokumen dan bukti kompetensi tersusun rapi, siap ditunjukkan kepada surveyor.
                    </div>
                </div>
                <div class="benefit-row">
                    <div class="benefit-dot"></div>
                    <div style="font-size:11px;color:#4b5563;">
                        Mempercepat penyiapan dokumen saat penilaian eksternal.
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card">
                <div style="font-size:13px;font-weight:600;margin-bottom:6px;">Transparansi & akuntabilitas</div>
                <div class="benefit-row">
                    <div class="benefit-dot"></div>
                    <div style="font-size:11px;color:#4b5563;">
                        Proses penilaian kompetensi tercatat dengan jelas, mendukung pengambilan keputusan yang adil.
                    </div>
                </div>
                <div class="benefit-row">
                    <div class="benefit-dot"></div>
                    <div style="font-size:11px;color:#4b5563;">
                        Histori keputusan komite mudah ditelusuri kembali.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SECTION 6: STATISTIK RINGKAS (PAKAI DATA DARI CONTROLLER) --}}
@php
    $stats = $stats ?? [];
@endphp

<!--<div class="section-shell mt-5">-->
<!--    <div class="section-title">-->
<!--        Sekilas data di DIKSERA-->
<!--    </div>-->
<!--    <div class="section-subtitle">-->
<!--        Angka ini akan bertambah seiring implementasi DIKSERA di lingkungan rumah sakit.-->
<!--    </div>-->

<!--    <div class="row g-3">-->
<!--        <div class="col-6 col-md-3">-->
<!--            <div class="feature-card" style="text-align:center;">-->
<!--                <div style="font-size:22px;font-weight:700;color:#1d4ed8;">-->
<!--                    {{ number_format($stats['users'] ?? 0) }}-->
<!--                </div>-->
<!--                <div style="font-size:11px;color:#4b5563;">Akun terdaftar</div>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="col-6 col-md-3">-->
<!--            <div class="feature-card" style="text-align:center;">-->
<!--                <div style="font-size:22px;font-weight:700;color:#1d4ed8;">-->
<!--                    {{ number_format($stats['profiles'] ?? 0) }}-->
<!--                </div>-->
<!--                <div style="font-size:11px;color:#4b5563;">Profil perawat</div>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="col-6 col-md-3">-->
<!--            <div class="feature-card" style="text-align:center;">-->
<!--                <div style="font-size:22px;font-weight:700;color:#1d4ed8;">-->
<!--                    {{ number_format($stats['pelatihan'] ?? 0) }}-->
<!--                </div>-->
<!--                <div style="font-size:11px;color:#4b5563;">Riwayat pelatihan</div>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="col-6 col-md-3">-->
<!--            <div class="feature-card" style="text-align:center;">-->
<!--                <div style="font-size:22px;font-weight:700;color:#1d4ed8;">-->
<!--                    {{ number_format($stats['pekerjaan'] ?? 0) }}-->
<!--                </div>-->
<!--                <div style="font-size:11px;color:#4b5563;">Riwayat pekerjaan</div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

{{-- SECTION 7: FAQ SINGKAT --}}
<div class="section-shell mt-5 mb-3">
    <div class="section-title">
        Pertanyaan yang sering diajukan
    </div>
    <div class="section-subtitle">
        Beberapa hal dasar sebelum mulai menggunakan DIKSERA.
    </div>

    <div class="accordion" id="faqDiksera">
        <div class="accordion-item">
            <h2 class="accordion-header" id="faq1">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1Body">
                    Apa perbedaan DIKSERA dengan data perawat biasa?
                </button>
            </h2>
            <div id="faq1Body" class="accordion-collapse collapse" data-bs-parent="#faqDiksera">
                <div class="accordion-body" style="font-size:12px;">
                    DIKSERA fokus pada <strong>kompetensi</strong> dan <strong>sertifikasi</strong> perawat:
                    bukan hanya data identitas, tetapi juga pelatihan, pengalaman kerja,
                    lisensi, serta hasil uji kompetensi dalam satu sistem terintegrasi.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="faq2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2Body">
                    Siapa yang boleh mendaftar di DIKSERA?
                </button>
            </h2>
            <div id="faq2Body" class="accordion-collapse collapse" data-bs-parent="#faqDiksera">
                <div class="accordion-body" style="font-size:12px;">
                    Akun diperuntukkan bagi perawat di lingkungan rumah sakit yang dikelola oleh
                    <strong>Komite Keperawatan</strong>. Registrasi dilakukan melalui halaman
                    <strong>Registrasi Perawat</strong> dan akan ditinjau oleh komite.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="faq3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3Body">
                    Apakah data saya aman?
                </button>
            </h2>
            <div id="faq3Body" class="accordion-collapse collapse" data-bs-parent="#faqDiksera">
                <div class="accordion-body" style="font-size:12px;">
                    Data dikelola oleh tim internal rumah sakit. Hak akses diatur berdasarkan peran:
                    perawat, komite keperawatan, dan admin sistem, sehingga informasi sensitif tetap terjaga.
                </div>
            </div>
        </div>
    </div>
</div>

{{-- FLOATING CHATBOT --}}
<div class="diksera-chat-launcher">
    {{-- Panel Chat --}}
    <div class="diksera-chat-window" id="diksera-chat">
        <div class="diksera-chat-header">
            <div class="diksera-chat-avatar">🤖</div>
            <div>
                <div class="diksera-chat-title">Asisten DIKSERA</div>
                <div class="diksera-chat-subtitle">
                    Tanya singkat tentang DIKSERA, alur registrasi, atau statistik ringkas di halaman ini.
                </div>
            </div>
            <button class="diksera-chat-close" id="diksera-chat-close">&times;</button>
        </div>

        <div class="diksera-chat-body">
            <div class="diksera-chat-badge">
                <span style="font-size:10px;">Mode “AI ringkas”</span>
                <span style="width:4px;height:4px;border-radius:999px;background:#22c55e;"></span>
                <span style="font-size:10px;">Intent-based + baca statistik</span>
            </div>
            <div class="diksera-chat-suggestions">
                <button type="button" class="diksera-chat-suggestion" data-message="Apa itu DIKSERA?">
                    Apa itu DIKSERA?
                </button>
                <button type="button" class="diksera-chat-suggestion" data-message="Bagaimana cara registrasi perawat?">
                    Cara registrasi
                </button>
                <button type="button" class="diksera-chat-suggestion" data-message="Berapa jumlah perawat yang sudah terdata?">
                    Jumlah perawat
                </button>
                <button type="button" class="diksera-chat-suggestion" data-message="Apa itu DRH perawat di DIKSERA?">
                    DRH perawat
                </button>
            </div>
        </div>

        <div class="diksera-chat-messages" id="diksera-chat-messages">
            <div class="diksera-chat-msg diksera-chat-msg-bot">
                <div class="diksera-chat-bubble diksera-chat-bubble-bot">
                    Halo 👋, aku asisten DIKSERA versi ringkas.
Kamu bisa tanya:
• Apa itu DIKSERA?
• Cara registrasi perawat
• DRH perawat di DIKSERA
• Berapa jumlah perawat / akun / pelatihan yang sudah terdata
                </div>
            </div>
        </div>

        <div class="diksera-chat-footer">
            <form id="diksera-chat-form" class="diksera-chat-input-wrap">
                <input type="text" id="diksera-chat-input" placeholder="Ketik pertanyaan singkat..." autocomplete="off">
                <button type="submit">Kirim</button>
            </form>
        </div>
    </div>

    {{-- Tombol bulat --}}
    <button class="diksera-chat-bubble-btn" id="diksera-chat-open" aria-label="Buka chat DIKSERA">
        💬
    </button>
</div>

@endsection

@push('scripts')
<script>
    // inject statistik dari PHP ke JS (kalau ada)
    window.dikseraStats = @json($stats ?? []);

    function normalizeText(text) {
        return (text || '')
            .toLowerCase()
            .replace(/[.,!?;:()"'`]/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function createIntent(name, keywords, replyFn) {
        return { name, keywords, replyFn };
    }

    function countMatches(text, keywords) {
        let score = 0;
        keywords.forEach(k => {
            if (!k) return;
            if (text.includes(k)) {
                score += 1;
            }
        });
        return score;
    }

    const dikseraIntents = (function () {
        const stats = window.dikseraStats || {};

        return [
            createIntent('greeting',
                ['halo','hai','assalam','assalamu','pagi','siang','sore','malam','hi'],
                function () {
                    return 'Halo 👋, aku asisten DIKSERA. Aku bisa bantu jelasin singkat tentang DIKSERA, cara registrasi, DRH, STR/SIP, dan statistik ringkas yang ada di halaman ini.';
                }
            ),

            createIntent('about',
                ['apa itu diksera','diksera itu','tentang diksera','platform diksera'],
                function () {
                    return 'DIKSERA adalah platform digital untuk mengelola kompetensi dan sertifikasi perawat: mulai dari DRH (Daftar Riwayat Hidup), riwayat pelatihan, lisensi STR/SIP, sampai hasil ujian tertulis dan wawancara yang dilakukan Komite Keperawatan.';
                }
            ),

            createIntent('register',
                ['registrasi','daftar','pendaftaran','register','buat akun'],
                function () {
                    return 'Untuk registrasi perawat:\n1️⃣ Klik tombol "Registrasi Perawat" di bagian atas halaman ini.\n2️⃣ Isi data dasar dan kirimkan.\n3️⃣ Komite Keperawatan akan meninjau dan mengaktifkan akunmu.\nSetelah aktif, kamu bisa lanjut mengisi DRH dan mengunggah dokumen.';
                }
            ),

            createIntent('drh',
                ['drh','daftar riwayat hidup'],
                function () {
                    return 'DRH di DIKSERA memuat identitas perawat, riwayat pendidikan, pelatihan, pekerjaan, organisasi, keluarga, dan tanda jasa. Semuanya terdokumentasi dalam format digital yang seragam untuk memudahkan penilaian kompetensi.';
                }
            ),

            createIntent('license',
                ['str','sip','lisensi','izin praktek','izin praktik'],
                function () {
                    return 'Di DIKSERA, STR/SIP dan sertifikat pelatihan bisa diunggah sebagai bukti legal. Komite Keperawatan dapat memantau masa berlaku dan kelengkapan dokumen sehingga proses penempatan dan uji kompetensi lebih terukur.';
                }
            ),

            createIntent('stats_perawat',
                ['berapa','jumlah','banyak','total','perawat','profil'],
                function () {
                    const totalProfiles = stats.profiles ?? 0;
                    const totalUsers = stats.users ?? 0;
                    return 'Saat ini DIKSERA mencatat sekitar ' + totalProfiles +
                        ' profil perawat dan ' + totalUsers +
                        ' akun terdaftar (termasuk perawat dan user lain yang diizinkan). Angka ini akan bertambah seiring implementasi.';
                }
            ),

            createIntent('stats_pelatihan',
                ['berapa','jumlah','banyak','total','pelatihan','training'],
                function () {
                    const total = stats.pelatihan ?? 0;
                    return 'Di DIKSERA sudah tercatat sekitar ' + total +
                        ' riwayat pelatihan perawat. Data ini membantu komite melihat jejak pengembangan kompetensi setiap perawat.';
                }
            ),

            createIntent('stats_pekerjaan',
                ['berapa','jumlah','banyak','total','pekerjaan','riwayat kerja','pengalaman kerja'],
                function () {
                    const total = stats.pekerjaan ?? 0;
                    return 'Saat ini ada sekitar ' + total +
                        ' entri riwayat pekerjaan perawat di DIKSERA, sehingga perjalanan karier perawat dapat ditelusuri dengan lebih jelas.';
                }
            ),

            createIntent('flow',
                ['alur','proses','step','langkah','cara pakai','cara menggunakan'],
                function () {
                    return 'Alur singkat DIKSERA:\n1️⃣ Perawat registrasi dan akun diaktifkan komite.\n2️⃣ Perawat mengisi DRH lengkap dan mengunggah STR/SIP serta sertifikat.\n3️⃣ Komite menjadwalkan dan menilai ujian tertulis + wawancara.\n4️⃣ Hasil penilaian direkap dan ditetapkan status kompetensinya.';
                }
            ),

            createIntent('security',
                ['aman','keamanan','privasi','data saya aman'],
                function () {
                    return 'Data di DIKSERA dikelola oleh tim internal rumah sakit. Hak akses diatur sesuai peran (perawat, komite, admin), sehingga informasi sensitif tetap terjaga dan hanya pihak berwenang yang bisa melihat detail tertentu.';
                }
            ),

            createIntent('fallback',
                [], // fallback tidak pakai keyword, dipilih terakhir
                function () {
                    return 'Untuk saat ini aku baru bisa menjawab seputar:\n• Apa itu DIKSERA\n• Cara registrasi\n• DRH, STR/SIP, dan alur dasar\n• Statistik ringkas (jumlah akun, perawat, pelatihan, pekerjaan)\n\nCoba tanya misalnya:\n- "Apa itu DIKSERA?"\n- "Bagaimana cara registrasi perawat?"\n- "Berapa jumlah perawat yang sudah terdata?"';
                }
            ),
        ];
    })();

    function detectIntentFromText(rawText) {
        const text = normalizeText(rawText);
        let bestIntent = null;
        let bestScore = 0;

        dikseraIntents.forEach(intent => {
            if (intent.name === 'fallback') return; // fallback nanti aja
            const score = countMatches(text, intent.keywords);
            if (score > bestScore) {
                bestScore = score;
                bestIntent = intent;
            }
        });

        // heuristik tambahan: kalau ada kata salam tapi score kecil
        if (!bestIntent && /^(halo|hai|hi|assalam|assalamu)/.test(text)) {
            bestIntent = dikseraIntents.find(i => i.name === 'greeting');
        }

        // kalau tidak ada yang cocok sama sekali → fallback
        if (!bestIntent) {
            bestIntent = dikseraIntents.find(i => i.name === 'fallback');
        }

        return bestIntent;
    }

    function dikseraBotAnswer(message) {
        const intent = detectIntentFromText(message);
        if (!intent || typeof intent.replyFn !== 'function') {
            return 'Maaf, aku belum paham pertanyaannya. Coba tanya hal yang spesifik tentang DIKSERA, misalnya: "Apa itu DIKSERA?" atau "Cara registrasi perawat".';
        }
        return intent.replyFn();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const openBtn = document.getElementById('diksera-chat-open');
        const closeBtn = document.getElementById('diksera-chat-close');
        const chatWin = document.getElementById('diksera-chat');
        const form = document.getElementById('diksera-chat-form');
        const input = document.getElementById('diksera-chat-input');
        const messages = document.getElementById('diksera-chat-messages');
        const suggestionBtns = document.querySelectorAll('.diksera-chat-suggestion');

        function toggleChat(show) {
            chatWin.style.display = show ? 'flex' : 'none';
        }

        openBtn.addEventListener('click', function () {
            const isHidden = chatWin.style.display === 'none' || chatWin.style.display === '';
            toggleChat(isHidden);
            if (isHidden) {
                setTimeout(() => input && input.focus(), 200);
            }
        });

        closeBtn.addEventListener('click', function () {
            toggleChat(false);
        });

        suggestionBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const text = this.getAttribute('data-message') || '';
                if (!text) return;
                input.value = text;
                form.dispatchEvent(new Event('submit', {cancelable: true, bubbles: true}));
            });
        });

        function appendMessage(text, type) {
            if (!text) return;
            const wrapper = document.createElement('div');
            wrapper.className = 'diksera-chat-msg ' + (type === 'user' ? 'diksera-chat-msg-user' : 'diksera-chat-msg-bot');

            const bubble = document.createElement('div');
            bubble.className = 'diksera-chat-bubble ' + (type === 'user' ? 'diksera-chat-bubble-user' : 'diksera-chat-bubble-bot');
            bubble.textContent = text;

            wrapper.appendChild(bubble);
            messages.appendChild(wrapper);
            messages.scrollTop = messages.scrollHeight;
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const text = (input.value || '').trim();
            if (!text) return;

            appendMessage(text, 'user');
            input.value = '';

            setTimeout(() => {
                const answer = dikseraBotAnswer(text);
                appendMessage(answer, 'bot');
            }, 200);
        });
    });
</script>
@endpush
