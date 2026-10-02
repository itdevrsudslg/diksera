@extends('layouts.app')

@section('title', 'Status Pengajuan')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-hover: #1d4ed8;
            --text-dark: #0f172a;
            --text-gray: #64748b;
            --bg-body: #f8fafc;
            --card-border: #e2e8f0;

            /* Status Variables */
            --st-pending-bg: #f1f5f9;
            --st-pending-text: #475569;
            --st-primary-bg: #eff6ff;
            --st-primary-text: #2563eb;
            --st-success-bg: #f0fdf4;
            --st-success-text: #166534;
            --st-warning-bg: #fffbeb;
            --st-warning-text: #b45309;
            --st-danger-bg: #fef2f2;
            --st-danger-text: #b91c1c;
            --st-info-bg: #f0f9ff;
            --st-info-text: #0369a1;
            --st-purple-bg: #f3e8ff;
            --st-purple-text: #7e22ce;
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* --- ANIMATIONS DEFINITIONS --- */
        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes subtlePulseYellow {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 251, 235, 0.7), inset 0 0 0 1px #fef3c7;
            }

            70% {
                box-shadow: 0 0 0 10px rgba(255, 251, 235, 0), inset 0 0 0 1px #fef3c7;
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 251, 235, 0), inset 0 0 0 1px #fef3c7;
            }
        }

        @keyframes popInBouncy {
            0% {
                opacity: 0;
                transform: scale(0.9);
            }

            70% {
                opacity: 1;
                transform: scale(1.02);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .animate-enter {
            animation: slideUpFade 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) both;
        }

        .stagger-item {
            opacity: 0;
        }

        /* --- Page Header --- */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-dark);
            letter-spacing: -0.025em;
        }

        .page-subtitle {
            color: var(--text-gray);
            font-size: 0.95rem;
            margin-top: 0.5rem;
        }

        /* --- Cards --- */
        .request-card {
            background: white;
            border: 1px solid var(--card-border);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
        }

        .request-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 20px -3px rgba(0, 0, 0, 0.07);
        }

        .card-header-custom {
            padding: 24px;
            background: white;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
            flex-wrap: wrap;
        }

        .card-body-custom {
            padding: 24px;
        }

        /* --- Typography & Badges --- */
        .lisensi-name {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .lisensi-meta {
            font-size: 0.875rem;
            color: var(--text-gray);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nomor-pending {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
            padding: 2px 10px;
            border-radius: 6px;
            display: inline-block;
        }

        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }

        .request-card:hover .status-badge {
            transform: scale(1.05);
        }

        /* Color Variants */
        .badge-pending {
            background: var(--st-pending-bg);
            color: var(--st-pending-text);
        }

        .badge-primary {
            background: var(--st-primary-bg);
            color: var(--st-primary-text);
        }

        .badge-success {
            background: var(--st-success-bg);
            color: var(--st-success-text);
        }

        .badge-warning {
            background: var(--st-warning-bg);
            color: var(--st-warning-text);
        }

        .badge-danger {
            background: var(--st-danger-bg);
            color: var(--st-danger-text);
        }

        .badge-info {
            background: var(--st-info-bg);
            color: var(--st-info-text);
        }

        .badge-purple {
            background: var(--st-purple-bg);
            color: var(--st-purple-text);
        }

        /* --- Action Boxes & Alerts --- */
        .alert-modern {
            padding: 16px;
            border-radius: 12px;
            font-size: 0.925rem;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            animation: fadeIn 0.5s ease-in-out both;
        }

        .alert-modern.blue {
            background: #eff6ff;
            border-color: #dbeafe;
            color: #1e40af;
        }

        .alert-modern.yellow {
            background: #fffbeb;
            border-color: #fef3c7;
            color: #92400e;
        }

        .alert-modern.red {
            background: #fef2f2;
            border-color: #fee2e2;
            color: #991b1b;
        }

        .alert-modern.yellow.action-required {
            animation: subtlePulseYellow 2s infinite;
        }

        .action-container {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 24px;
            margin-top: 10px;
            animation: slideUpFade 0.5s ease-out both;
        }

        .section-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #94a3b8;
            margin-bottom: 12px;
            display: block;
            letter-spacing: 0.05em;
        }

        /* --- Form Elements --- */
        .form-label-sm {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            font-size: 0.9rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .btn {
            transition: all 0.2s;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        /* --- Certificate Box --- */
        .certificate-box {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #bbf7d0;
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: popInBouncy 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) both;
            animation-delay: 0.2s;
        }

        .certificate-icon {
            background: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            color: var(--st-success-text);
            font-size: 1.75rem;
        }

        /* --- Empty State --- */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 16px;
            border: 1px dashed var(--card-border);
            animation: fadeIn 0.8s ease-out both;
        }

        .empty-icon {
            font-size: 3.5rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }

        /* Progress Info Box */
        .progress-info-box {
            background: #f0f9ff;
            border: 1px solid #bfdbfe;
            border-left: 4px solid #2563eb;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .progress-info-box i {
            color: #2563eb;
            font-size: 1.2rem;
        }

        .progress-info-box .text-content {
            flex: 1;
        }

        .progress-info-box .text-content small {
            display: block;
            color: #1e40af;
            font-size: 0.8rem;
            line-height: 1.4;
        }
    </style>
@endpush


@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="page-header animate-enter">
                <h1 class="page-title">Status Pengajuan</h1>
                <p class="page-subtitle">
                    Pantau progres perpanjangan lisensi dan jadwal evaluasi Anda secara real-time.
                </p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            @forelse ($pengajuan as $item)
    @php
        $metodeAktif = $item->metode ?? optional($item->lisensiLama)->metode;
        $status = $item->status;
        $hasJadwal = $item->jadwalWawancara ? true : false;
        $bolehAjukanJadwal = false;

        // LOGIKA 1: Belum punya jadwal sama sekali
        if (!$hasJadwal) {
            if ($metodeAktif === 'pg_interview' && $status === 'exam_passed') {
                $bolehAjukanJadwal = true;
            } elseif ($metodeAktif === 'interview_only' && $status === 'interview_scheduled') {
                $bolehAjukanJadwal = true;
            }
        }
        
        // LOGIKA 2: Gagal Wawancara (Remedial) -> Boleh ajukan ulang
        // Pastikan status di DB adalah 'interview_failed'
        if ($status === 'interview_failed') {
            $bolehAjukanJadwal = true;
        }
    @endphp

    <div class="request-card stagger-item animate-enter" style="animation-delay: {{ $loop->index * 0.15 + 0.2 }}s">
        
        {{-- HEADER CARD (Sama seperti sebelumnya) --}}
        <div class="card-header-custom">
            <div>
                <h5 class="lisensi-name">
                    {{ $metodeAktif === 'interview_only' ? '[Kredensialing]' : '[Uji Kompetensi]' }}
                    {{ $item->lisensiLama->nama ?? 'Lisensi Perawat' }}
                </h5>
                <div class="lisensi-meta">
                    <i class="bi bi-upc-scan"></i> Nomor: 
                    @if ($item->status === 'completed')
                  <span class="fw-bold text-success">
    @if($item->lisensiLama)
  @php
    // 1. Ambil ID Lisensi sebagai nomor urut
    $nomorUrut = str_pad($item->lisensiLama->id, 3, '0', STR_PAD_LEFT);
    
    // 2. Ambil tahun terbit
    $tahun = \Carbon\Carbon::parse($item->lisensiLama->tgl_terbit)->format('Y');
    
    // 3. Tentukan jenis surat
    $jenisSurat = ($metodeAktif === 'interview_only') ? 'KRED' : 'SERKOM';
@endphp

{{-- 4. Tampilkan menggunakan sintaks kurung kurawal Blade --}}
800.2.4.2.1/{{ $nomorUrut }}/418.101/{{ $jenisSurat }}/{{ $tahun }}
    @else
        -
    @endif
</span>
                    @else
                        <span class="nomor-pending">Menunggu</span>
                    @endif
                </div>
            </div>
            
            {{-- STATUS BADGE --}}
            <div>
                @switch($item->status)
                    @case('pending') <span class="status-badge badge-pending">Menunggu Verifikasi</span> @break
                    @case('method_selected') <span class="status-badge badge-primary">Siap Ujian</span> @break
                    @case('exam_passed') <span class="status-badge badge-info">Lanjut Uji Skill</span> @break
                    @case('interview_scheduled') <span class="status-badge badge-warning">Jadwal Diproses</span> @break
                    
                    {{-- Badge Khusus Gagal Wawancara --}}
                    @case('interview_failed') <span class="status-badge badge-danger">Tidak Lulus Wawancara</span> @break
                    
                    @case('completed') <span class="status-badge badge-success">Selesai</span> @break
                    @case('rejected') <span class="status-badge badge-danger">Ditolak Admin</span> @break
                @endswitch
            </div>
        </div>

        <div class="card-body-custom">
            
            {{-- FORM INPUT JADWAL (Muncul jika baru atau remedial) --}}
            @if ($bolehAjukanJadwal)
                <div class="action-container">
                    <span class="section-label"><i class="bi bi-calendar-plus me-1"></i> Ajukan Jadwal </span>

                    {{-- ALERT KHUSUS REMEDIAL --}}
                    @if ($status === 'interview_failed')
                        <div class="alert-modern red mb-4">
                            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                            <div>
                                <strong>Hasil: Tidak Lulus</strong>
                                <p class="mb-0 mt-1 small">Anda belum memenuhi standar kompetensi. Silakan ajukan jadwal ulang untuk evaluasi remedial.</p>
                            </div>
                        </div>
                    @endif

                    {{-- ALERT JIKA JADWAL DITOLAK ADMIN --}}
                    @php $lastJadwal = $item->jadwalWawancara; @endphp
                    @if ($lastJadwal && $lastJadwal->status == 'rejected')
                        <div class="alert-modern red mb-4">
                            <i class="bi bi-x-octagon-fill fs-5"></i>
                            <div><strong>Jadwal Ditolak:</strong> <p class="mb-0 mt-1">"{{ $lastJadwal->catatan_admin }}"</p></div>
                        </div>
                    @endif

                    <form action="{{ route('perawat.pengajuan.store_wawancara', $item->id) }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            {{--<div class="col-md-6">--}}
                            {{--    <label class="form-label-sm">Pilih Pewawancara</label>--}}
                            {{--    <select name="penanggung_jawab_id" class="form-select" required>--}}
                            {{--        <option value="">-- Pilih --</option>--}}
                            {{--        @foreach ($pjs as $pj) <option value="{{ $pj->id }}">{{ $pj->nama }}</option> @endforeach--}}
                            {{--    </select>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-6">--}}
                            {{--    <label class="form-label-sm">Usulan Lokasi</label>--}}
                            {{--    <input type="text" name="lokasi_wawancara" class="form-control" placeholder="Contoh: R. Komite" required>--}}
                            {{--</div>--}}
                            <div class="col-md-3"><label class="form-label-sm">Tanggal</label><input type="date" name="tgl_wawancara" class="form-control" required></div>
                            <div class="col-md-3"><label class="form-label-sm">Jam</label><input type="time" name="jam_wawancara" class="form-control" required></div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                    <i class="bi bi-send me-2"></i> {{ $status === 'interview_failed' ? 'Ajukan Remedial' : 'Kirim Pengajuan' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            {{-- DETAIL JADWAL (READ ONLY - Jika sedang menunggu acc admin) --}}
            @if ($item->jadwalWawancara && !$bolehAjukanJadwal)
                @php $jadwal = $item->jadwalWawancara; @endphp
                {{-- ... (Kode Tampilan Detail Jadwal Sama Seperti Sebelumnya) ... --}}
                 @if ($jadwal->status == 'pending')
                    <div class="action-container bg-white border border-secondary border-opacity-25 mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="section-label mb-0 text-dark">Detail Jadwal </span>
                            <span class="badge bg-warning text-dark rounded-pill px-3">Menunggu Konfirmasi</span>
                        </div>
                        <div class="row g-4 mt-1">
                            {{--<div class="col-sm-4"><small class="text-muted d-block mb-1">Pewawancara</small><div class="fw-semibold text-dark">{{ $jadwal->pewawancara->nama ?? '-' }}</div></div>--}}
                            <div class="col-sm-4"><small class="text-muted d-block mb-1">Waktu</small><div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($jadwal->waktu_wawancara)->format('d M Y, H:i') }} WIB</div></div>
                            {{--<div class="col-sm-4"><small class="text-muted d-block mb-1">Lokasi</small><div class="fw-semibold text-dark">{{ $jadwal->lokasi }}</div></div>--}}
                        </div>
                    </div>
                @elseif ($jadwal->status == 'approved')
                    {{-- ... Tampilan Approved ... --}}
                     <div class="action-container bg-white border border-success border-opacity-25 shadow-sm mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="section-label mb-0 text-dark fw-bold"><i class="bi bi-calendar-check-fill text-success me-2"></i> Jadwal Terkonfirmasi</span>
                            <span class="badge bg-success text-white rounded-pill px-3">Disetujui</span>
                        </div>
                        <div class="row g-4">
                            <div class="col-sm-4"><small class="text-muted d-block mb-1 text-uppercase">Asesor</small><div class="fw-bold text-dark">{{ $jadwal->pewawancara->nama ?? '-' }}</div></div>
                            <div class="col-sm-4"><small class="text-muted d-block mb-1 text-uppercase">Waktu</small><div class="fw-bold text-dark"><span class="text-primary">{{ \Carbon\Carbon::parse($jadwal->waktu_wawancara)->format('d M Y') }}</span> • {{ \Carbon\Carbon::parse($jadwal->waktu_wawancara)->format('H:i') }} WIB</div></div>
                            <div class="col-sm-4"><small class="text-muted d-block mb-1 text-uppercase">Lokasi</small><div class="fw-bold text-dark">{{ $jadwal->lokasi }}</div></div>
                            @if (!empty($jadwal->deskripsi_skill))
                                <div class="col-12 mt-2"><div class="p-3 bg-light rounded border border-secondary border-opacity-10"><small class="text-primary fw-bold text-uppercase d-block mb-2">Fokus Uji Skill</small><p class="mb-0 text-dark" style="font-size: 0.95rem;">{!! nl2br(e($jadwal->deskripsi_skill)) !!}</p></div></div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif

            {{-- COMPLETED (DOWNLOAD) --}}
            @if ($item->status === 'completed')
                <div class="certificate-box mt-3">
                    <div class="certificate-icon"><i class="bi bi-trophy-fill"></i></div>
                    <h4 class="fw-bold text-success">{{ $metodeAktif === 'interview_only' ? 'UNDUH SPK, SPKK & RKK' : 'Uji Kompetensi Lulus' }}</h4>
                    <a href="{{ route('perawat.pengajuan.print', $item->id) }}" class="btn btn-success fw-bold px-4 py-2"><i class="bi bi-award-fill me-2"></i> {{ $metodeAktif === 'interview_only' ? 'Unduh' : 'Unduh Sertifikat' }}</a>
                </div>
            @endif
            {{-- ========================================== --}}
                    {{-- HASIL PENILAIAN (MUNCUL JIKA SUDAH DINILAI) --}}
                    {{-- ========================================== --}}
                    @if ($item->jadwalWawancara && $item->jadwalWawancara->penilaian)
                        @php
                            $penilaian = $item->jadwalWawancara->penilaian;
                            $isUjiSkill = $metodeAktif !== 'interview_only';
                        @endphp

                        <div class="action-container bg-white border border-info border-opacity-50 shadow-sm mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="section-label mb-0 text-dark fw-bold">
                                    <i class="bi bi-clipboard2-data-fill text-info me-2"></i> Hasil Penilaian Asesor
                                </span>
                                @if($penilaian->keputusan === 'lulus' || $penilaian->keputusan === 'valid')
                                    <span class="badge bg-success text-white rounded-pill px-3"><i class="bi bi-check-circle me-1"></i> Memenuhi Syarat</span>
                                @else
                                    <span class="badge bg-danger text-white rounded-pill px-3"><i class="bi bi-x-circle me-1"></i> Tidak Memenuhi Syarat</span>
                                @endif
                            </div>

                            {{-- TAMPILAN JIKA UJI SKILL --}}
                            @if($isUjiSkill && $penilaian->nilai_pertanyaan && $penilaian->uji_skill)
                                @php
                                    // Hitung ulang untuk ditampilkan
                                    $totalTema = 0;
                                    foreach($penilaian->nilai_pertanyaan as $np) { $totalTema += (float) ($np['skor'] ?? 0); }
                                    
                                    $totalSkill = 0;
                                    foreach($penilaian->uji_skill as $us) { $totalSkill += (float) ($us['skor'] ?? 0); }
                                    $rataSkill = $totalSkill / 3;
                                @endphp
                                <div class="row g-4">
                                    <div class="col-sm-4">
                                        <div class="p-3 bg-light rounded border border-secondary border-opacity-10 h-100">
                                            <small class="text-muted d-block mb-1 text-uppercase fw-bold">Total Skor Tema</small>
                                            <div class="fw-bolder fs-4 {{ $totalTema >= 75 ? 'text-success' : 'text-danger' }}">
                                                {{ $totalTema }} <span class="fs-6 text-muted fw-normal">/ 100</span>
                                            </div>
                                            <small class="text-muted" style="font-size: 0.75rem;">Minimal Lulus: 75</small>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="p-3 bg-light rounded border border-secondary border-opacity-10 h-100">
                                            <small class="text-muted d-block mb-1 text-uppercase fw-bold">Rata-rata Uji Skill</small>
                                            <div class="fw-bolder fs-4 {{ $rataSkill >= 90 ? 'text-success' : 'text-danger' }}">
                                                {{ fmod($rataSkill, 1) !== 0.0 ? number_format($rataSkill, 2) : $rataSkill }} <span class="fs-6 text-muted fw-normal">/ 100</span>
                                            </div>
                                            <small class="text-muted" style="font-size: 0.75rem;">Minimal Lulus: 90</small>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="p-3 bg-light rounded border border-secondary border-opacity-10 h-100">
                                            <small class="text-muted d-block mb-1 text-uppercase fw-bold">Catatan Penguji</small>
                                            <p class="mb-0 text-dark small" style="line-height: 1.4;">
                                                {{ $penilaian->catatan_pewawancara ?: 'Tidak ada catatan tambahan.' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                            {{-- TAMPILAN JIKA KREDENSIALING --}}
                            @else
                                <div class="p-3 bg-light rounded border border-secondary border-opacity-10">
                                    <small class="text-muted d-block mb-1 text-uppercase fw-bold">Catatan Validasi</small>
                                    <p class="mb-0 text-dark small">
                                        {{ $penilaian->catatan_pewawancara ?: 'Berkas divalidasi dengan baik.' }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif
                    {{-- ========================================== --}}
        </div>
    </div>
@empty
    <div class="empty-state"><i class="bi bi-clipboard-x empty-icon"></i><h5 class="fw-bold">Belum Ada Pengajuan</h5></div>
@endforelse
        </div>
    </div>
</div>
@endsection
