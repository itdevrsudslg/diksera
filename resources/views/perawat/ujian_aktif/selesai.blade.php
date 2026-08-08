@extends('layouts.app')

@section('title', 'Hasil Ujian')

@push('styles')
    <style>
        /* --- Animations --- */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- Card Styling (Landscape) --- */
        .result-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 20px 60px -15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background: #fff;
            animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        /* --- Left Side (Score Section) --- */
        .score-section {
            position: relative;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            height: 100%;
            color: #fff;
            overflow: hidden;
        }

        /* Background Pattern Abstrak */
        .score-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.2), transparent 70%);
            pointer-events: none;
        }

        /* Tema Warna */
        .theme-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .theme-danger {
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
        }

        /* Score Typography */
        .big-score {
            font-size: 6rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 10px;
            text-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .score-label {
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.9;
            font-weight: 600;
        }

        .status-badge {
            margin-top: 20px;
            padding: 8px 20px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* --- Right Side (Detail Section) --- */
        .detail-section {
            padding: 40px;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 25px 0;
        }

        .stat-item {
            padding: 15px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            text-align: center;
            transition: transform 0.2s;
        }

        .stat-item:hover {
            transform: translateY(-3px);
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
        }

        .stat-text {
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }

        /* Icon Colors */
        .icon-check {
            color: #10b981;
            font-size: 1.2rem;
            margin-bottom: 5px;
        }

        .icon-cross {
            color: #ef4444;
            font-size: 1.2rem;
            margin-bottom: 5px;
        }
    </style>
@endpush

@section('content')
    @php
        $isRemidi = $result->remidi ?? ($result->total_nilai < 75);
        $isPass = !$isRemidi && $result->total_nilai >= 70;
        $themeClass = $isPass ? 'theme-success' : 'theme-danger';
        $statusText = $isPass ? 'LULUS' : ($isRemidi ? 'REMEDIAL' : 'TIDAK LULUS');
        $statusIcon = $isPass ? 'bi-trophy-fill' : ($isRemidi ? 'bi-exclamation-triangle-fill' : 'bi-x-circle-fill');
        $message = $isPass
            ? 'Selamat! Hasil ujian Anda sangat memuaskan.'
            : ($isRemidi
                ? 'Nilai Anda di bawah 75. Anda otomatis masuk program remidi. Silakan hubungi admin atau cek jadwal remidi.'
                : 'Nilai Anda belum memenuhi standar kelulusan.');
    @endphp

    <div class="container py-5">
        <div class="row justify-content-center">
            {{-- Menggunakan col-lg-9 agar kartu LEBIH LEBAR di desktop --}}
            <div class="col-lg-9">

                <div class="result-card">
                    <div class="row g-0">

                        {{-- BAGIAN KIRI: NILAI & STATUS (Berwarna) --}}
                        <div class="col-md-5">
                            <div class="score-section {{ $themeClass }}">
                                <div class="mb-2 opacity-75">Skor Akhir Anda</div>
                                <div class="big-score">{{ $result->total_nilai }}</div>
                                <div class="score-label">/ 100 Poin</div>

                                <div class="status-badge">
                                    <i class="bi {{ $statusIcon }}"></i> {{ $statusText }}
                                    @if($isRemidi)
                                        <span class="badge bg-warning text-dark ms-2">Remidi Otomatis</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- BAGIAN KANAN: DETAIL & STATISTIK (Putih) --}}
                        <div class="col-md-7">
                            <div class="detail-section h-100">

                                {{-- Header Teks --}}
                                <div class="mb-4">
                                    <h5 class="text-muted text-uppercase small fw-bold mb-1 ls-1">Ringkasan Hasil</h5>
                                    <h3 class="fw-bold text-dark mb-2">{{ $form->judul }}</h3>
                                    <p class="text-muted small mb-0">{{ $message }}</p>
                                </div>

                                {{-- Grid Statistik Benar/Salah --}}
                                <div class="stat-grid">
                                    <div class="stat-item">
                                        <div class="icon-check"><i class="bi bi-check-circle-fill"></i></div>
                                        <div class="stat-number">{{ $result->total_benar }}</div>
                                        <div class="stat-text">Jawaban Benar</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="icon-cross"><i class="bi bi-x-circle-fill"></i></div>
                                        <div class="stat-number">{{ $result->total_salah }}</div>
                                        <div class="stat-text">Jawaban Salah</div>
                                    </div>
                                </div>

                                {{-- Info Tambahan --}}
                                <div class="d-flex align-items-center gap-3 mb-4 text-muted small border-top pt-3">
                                    <div>
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ \Carbon\Carbon::parse($result->created_at)->format('d M Y') }}
                                    </div>
                                    <div>
                                        <i class="bi bi-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($result->created_at)->format('H:i') }} WIB
                                    </div>
                                </div>

                                {{-- Tombol Aksi --}}
                                <div class="d-grid">
                                    <a href="{{ route('perawat.ujian.index') }}"
                                        class="btn btn-outline-dark rounded-pill py-2 fw-bold">
                                        <i class="bi bi-arrow-left me-2"></i> Kembali ke Dashboard
                                    </a>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
