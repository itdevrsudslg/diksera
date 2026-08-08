@extends('layouts.app')

@php
    $pageTitle = 'Integrasi Telegram';
    $pageSubtitle = 'Hubungkan akun Anda untuk notifikasi sertifikat dan pengingat sistem.';
@endphp

@section('title', 'Link Telegram – DIKSERA')

@push('styles')
    <style>
        /* Global Card */
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-soft);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 32px 24px;
            height: 100%;
        }

        /* Telegram Theme */
        :root {
            --telegram-color: #24A1DE;
            --telegram-bg: #f2faff;
        }

        /* Icon Box */
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

        /* OTP Code Display */
        .otp-box {
            background: #f8fafc;
            border: 2px dashed var(--border-soft);
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
            color: var(--text-main);
            display: block;
        }

        .otp-timer {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* Layout */
        .step-container {
            max-width: 450px; /* Sedikit diperlebar untuk list instruksi */
            margin: 0 auto;
            text-align: center;
        }

        /* Instruction List */
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
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        {{-- Tombol Kembali --}}
        <div class="d-flex justify-content-start mb-3">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary px-3"
                style="border-radius: 8px; font-size: 12px;">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="content-card text-center">

                    {{-- KONDISI 1: SUDAH TERHUBUNG --}}
                    @if (auth()->user()->telegram_chat_id)
                        <div class="py-4">
                            <div class="icon-box success mb-4">
                                <i class="bi bi-check-lg"></i>
                            </div>

                            <h4 class="fw-bold mb-2">Akun Terhubung</h4>
                            <p class="text-muted mb-4 small px-4">
                                Telegram Anda aktif. Anda akan menerima notifikasi jika ada sertifikat yang akan kadaluarsa.
                            </p>

                            <form action="{{ route('perawat.telegram.unlink') }}" method="POST"
                                onsubmit="return confirm('Yakin ingin memutuskan koneksi Telegram?');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger px-4" style="border-radius: 8px;">
                                    <i class="bi bi-link-45deg me-1"></i> Putuskan Koneksi
                                </button>
                            </form>
                        </div>

                    {{-- KONDISI 2: BELUM TERHUBUNG --}}
                    @else
                        {{-- STEP 1: TOMBOL GENERATE & INSTRUKSI --}}
                        <div id="step-start" class="step-container py-3">
                            <div class="icon-box telegram mb-3">
                                <i class="bi bi-telegram"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Hubungkan Telegram</h5>
                            <p class="text-muted small mb-4">
                                Dapatkan pengingat otomatis untuk masa berlaku STR, SIP, dan Sertifikat Anda.
                            </p>

                            <div class="instruction-list shadow-sm">
                                <div class="fw-bold mb-3 border-bottom pb-2" style="font-size: 14px; color: var(--telegram-color);">
                                    <i class="bi bi-info-circle me-1"></i> Cara Menghubungkan:
                                </div>
                                <div class="instruction-item">
                                    <div class="instruction-number">1</div>
                                    <div>Buka Bot Telegram <a href="https://t.me/diksera_bot" target="_blank" class="fw-bold text-decoration-none">@diksera_bot</a></div>
                                </div>
                                <div class="instruction-item">
                                    <div class="instruction-number">2</div>
                                    <div>Klik tombol <b>Mulai (Start)</b> atau ketik <code>/start</code> pada chat bot.</div>
                                </div>
                                <div class="instruction-item">
                                    <div class="instruction-number">3</div>
                                    <div>Klik tombol <b>Generate Kode</b> di bawah ini untuk mendapatkan kode verifikasi.</div>
                                </div>
                                <div class="instruction-item">
                                    <div class="instruction-number">4</div>
                                    <div>Kirimkan kode tersebut ke dalam chat bot <b>@diksera_bot</b>.</div>
                                </div>
                            </div>

                            <button id="generateBtn" class="btn btn-primary px-4 py-2 w-100 shadow-sm"
                                style="background-color: var(--telegram-color); border:none; border-radius: 8px;">
                                <i class="bi bi-magic me-2"></i> Generate Kode
                            </button>
                        </div>

                        {{-- STEP 2: TAMPILKAN KODE --}}
                        <div id="step-verify" class="step-container py-3" style="display: none;">
                            <div class="mb-3">
                                <span
                                    class="badge bg-warning text-dark bg-opacity-25 text-opacity-75 px-3 py-2 rounded-pill small">
                                    <i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi
                                </span>
                            </div>

                            <p class="small text-muted mb-0">Segera kirimkan kode verifikasi di bawah ini ke <br> <a href="https://t.me/diksera_bot" target="_blank" class="fw-bold text-decoration-none">@diksera_bot</a>:</p>

                            {{-- Area Kode OTP --}}
                            <div class="otp-box">
                                <span id="codeDisplay" class="otp-code">...</span>
                                <div class="mt-2 border-top pt-2">
                                    <span class="otp-timer">Berlaku sampai: <span id="expiresAt"
                                            class="fw-bold text-dark">-</span></span>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="https://t.me/diksera_bot" target="_blank"
                                    class="btn btn-primary"
                                    style="background-color: var(--telegram-color); border:none; border-radius: 8px;">
                                    <i class="bi bi-telegram me-2"></i> Buka Chat Bot Sekarang
                                </a>
                                <button id="checkBtn" class="btn btn-light text-muted border" style="border-radius: 8px;">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Saya sudah kirim, Cek Status
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Generate Kode
        document.getElementById('generateBtn')?.addEventListener('click', async function() {
            const btn = this;
            const originalText = btn.innerHTML;

            // Loading state
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sedang memproses...';
            btn.disabled = true;

            try {
                const response = await fetch('{{ route('perawat.telegram.generate-code') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    document.getElementById('codeDisplay').textContent = data.code;
                    document.getElementById('expiresAt').textContent = data.expires_at;

                    document.getElementById('step-start').style.display = 'none';
                    document.getElementById('step-verify').style.display = 'block';
                } else {
                    alert('Gagal generate kode. Silakan coba lagi.');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan jaringan.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });

        // Cek Status (Reload)
        document.getElementById('checkBtn')?.addEventListener('click', function() {
            location.reload();
        });
    </script>
@endsection