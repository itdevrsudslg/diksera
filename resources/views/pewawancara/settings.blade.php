@extends('layouts.app')

@section('title', 'Pengaturan Akun')

@push('styles')
    <style>
        /* Style Dasar Card */
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-soft, #e2e8f0);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 32px 24px;
            height: 100%;
        }

        /* Telegram Brand Colors */
        :root {
            --telegram-color: #24A1DE;
            --telegram-bg: #f2faff;
        }

        /* Icon Box Styles */
        .icon-box {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px;
        }

        .icon-box.telegram {
            background-color: var(--telegram-bg);
            color: var(--telegram-color);
        }

        .icon-box.success {
            background-color: #dcfce7;
            color: #166534;
        }

        /* Instruction List (Sama dengan halaman Perawat) */
        .instruction-list {
            text-align: left;
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            border: 1px solid #e2e8f0;
        }
        .instruction-item {
            display: flex;
            gap: 12px;
            margin-bottom: 10px;
            font-size: 13px;
            color: #475569;
        }
        .instruction-number {
            background: var(--telegram-color);
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* OTP Code Display */
        .otp-box {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }

        .otp-code {
            font-family: 'Courier New', monospace;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 4px;
            color: #1e293b;
            display: block;
        }

        /* Steps Container */
        .step-container {
            max-width: 450px;
            margin: 0 auto;
            text-align: center;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1">Pengaturan Akun</h4>
                <span class="text-muted small">Kelola notifikasi dan koneksi akun Anda.</span>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="content-card text-center">

                    {{-- KONDISI 1: SUDAH TERHUBUNG --}}
                    @if ($user->telegram_chat_id)
                        <div class="py-4 animate__animated animate__fadeIn">
                            <div class="icon-box success mb-4">
                                <i class="bi bi-check-lg"></i>
                            </div>

                            <h4 class="fw-bold mb-2 text-dark">Terhubung!</h4>
                            <p class="text-muted mb-4 small px-4">
                                Akun Anda sudah terhubung dengan Telegram.<br>
                                Notifikasi jadwal wawancara akan dikirim secara otomatis.
                            </p>

                            <div class="mb-4">
                                <span class="badge bg-light text-dark border fw-normal font-monospace px-3 py-2">
                                    ID Chat: {{ $user->telegram_chat_id }}
                                </span>
                            </div>

                            <form action="{{ route('pewawancara.telegram.unlink') }}" method="POST"
                                onsubmit="return confirm('Yakin ingin memutus sambungan Telegram?');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger px-4 shadow-sm" style="border-radius: 8px;">
                                    <i class="bi bi-link-45deg me-1"></i> Putuskan Sambungan
                                </button>
                            </form>
                        </div>

                    {{-- KONDISI 2: BELUM TERHUBUNG --}}
                    @else
                        {{-- STEP 1: TOMBOL MULAI & PANDUAN --}}
                        <div id="step-start" class="step-container py-3">
                            <div class="icon-box telegram mb-3">
                                <i class="bi bi-telegram"></i>
                            </div>
                            <h5 class="fw-bold mb-2 text-dark">Hubungkan Telegram</h5>
                            <p class="text-muted small mb-4">Dapatkan notifikasi jadwal wawancara secara realtime.</p>

                            <div class="instruction-list shadow-sm">
                                <div class="fw-bold mb-3 border-bottom pb-2" style="font-size: 14px; color: var(--telegram-color);">
                                    <i class="bi bi-info-circle me-1"></i> Langkah Koneksi:
                                </div>
                                <div class="instruction-item">
                                    <div class="instruction-number">1</div>
                                    <div>
                                        <a href="https://t.me/diksera_bot" target="_blank" class="fw-bold text-decoration-none" style="color: #1d4ed8;">
                                            KLIK DISINI UNTUK MASUK DIKSERA BOT
                                        </a>
                                    </div>
                                </div>
                                <div class="instruction-item">
                                    <div class="instruction-number">2</div>
                                    <div>Klik tombol <b>Mulai (Start)</b> atau ketik <code>/start</code> di bot.</div>
                                </div>
                                <div class="instruction-item">
                                    <div class="instruction-number">3</div>
                                    <div>Klik tombol <b>Buat Kode Koneksi</b> di bawah ini.</div>
                                </div>
                                <div class="instruction-item">
                                    <div class="instruction-number">4</div>
                                    <div>Masukkan kode OTP yang muncul ke dalam chat bot.</div>
                                </div>
                            </div>

                            <button id="btn-generate" class="btn btn-primary px-4 py-2 w-100 shadow-sm"
                                style="background-color: var(--telegram-color); border:none; border-radius: 8px;">
                                <i class="bi bi-link me-2"></i> Buat Kode Koneksi
                            </button>
                        </div>

                        {{-- STEP 2: TAMPILKAN KODE --}}
                        <div id="step-verify" class="step-container py-3" style="display: none;">
                            <div class="mb-3">
                                <span class="badge bg-warning text-dark bg-opacity-25 text-opacity-75 px-3 py-2 rounded-pill small">
                                    <i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi
                                </span>
                            </div>

                            <p class="small text-muted mb-0">Segera masukkan kode berikut ke:</p>
                            <a href="https://t.me/diksera_bot" target="_blank" class="fw-bold text-decoration-none d-block mb-3">@diksera_bot</a>

                            <div class="otp-box">
                                <span id="display-code" class="otp-code">---</span>
                                <div class="mt-2 border-top pt-2">
                                    <span class="otp-timer">Berlaku sampai: <span id="display-time" class="fw-bold text-dark">-</span></span>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="https://t.me/diksera_bot" target="_blank" class="btn btn-primary shadow-sm fw-bold"
                                    style="background-color: var(--telegram-color); border:none; border-radius: 8px;">
                                    <i class="bi bi-telegram me-2"></i> KLIK DISINI UNTUK MASUK DIKSERA BOT
                                </a>

                                <button onclick="location.reload()" class="btn btn-light text-muted border" style="border-radius: 8px;">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Cek Status Koneksi
                                </button>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const btnGenerate = document.getElementById('btn-generate');
                const stepStart = document.getElementById('step-start');
                const stepVerify = document.getElementById('step-verify');
                const displayCode = document.getElementById('display-code');
                const displayTime = document.getElementById('display-time');

                if (btnGenerate) {
                    btnGenerate.addEventListener('click', function() {
                        const originalText = btnGenerate.innerHTML;
                        btnGenerate.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
                        btnGenerate.disabled = true;

                        fetch("{{ route('pewawancara.telegram.generate-code') }}", {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    stepStart.style.display = 'none';
                                    stepVerify.style.display = 'block';
                                    displayCode.innerText = data.code;
                                    displayTime.innerText = data.expires_at;
                                } else {
                                    alert('Gagal membuat kode: ' + (data.message || 'Terjadi kesalahan'));
                                    btnGenerate.innerHTML = originalText;
                                    btnGenerate.disabled = false;
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                alert('Terjadi kesalahan koneksi.');
                                btnGenerate.innerHTML = originalText;
                                btnGenerate.disabled = false;
                            });
                    });
                }
            });
        </script>
    @endpush
@endsection