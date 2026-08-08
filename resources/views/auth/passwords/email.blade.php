@extends('layouts.auth', ['title' => 'Lupa Password DIKSERA'])

@section('auth-left')
    <div class="auth-left-inner">
        <div class="brand-pill">
            <span class="brand-dot"></span>
            <span>Pemulihan Akun RSUD SLG</span>
        </div>

        <div class="auth-left-title">Lupa Password?</div>
        <div class="auth-left-subtitle">
            Jangan khawatir, kami akan membantu Anda mendapatkan akses kembali ke platform DIKSERA.
        </div>

        <div class="auth-left-highlight">
            <i class="bi bi-info-circle"></i>
            <span>Prosedur Pemulihan:</span>
        </div>

        <ul class="auth-left-list">
            <li>
                <span class="bullet"></span>
                <span>Pastikan email yang Anda masukkan adalah email yang terdaftar di sistem.</span>
            </li>
            <li>
                <span class="bullet"></span>
                <span>Cek folder <strong>Inbox</strong> atau <strong>Spam</strong> setelah menekan tombol kirim.</span>
            </li>
            <li>
                <span class="bullet"></span>
                <span>Link reset password hanya berlaku selama 60 menit demi keamanan data Anda.</span>
            </li>
        </ul>

        <div class="auth-left-meta mt-auto">
            <div class="auth-left-badge">
                Layanan IT RSUD Simpang Lima Gumul
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="text-center mb-4">
    <div class="logo-big mx-auto">
        <img src="{{ asset('icon.png') }}" alt="DIKSERA">
    </div>
    <div class="auth-title">Atur Ulang Password</div>
    <p class="text-muted small">Masukkan email untuk menerima link pemulihan akun.</p>
</div>

<form method="POST" action="{{ route('forget.password.post') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label small text-muted">Email Terdaftar</label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control form-control-light @error('email') is-invalid @enderror" 
                placeholder="email@rsudslg.com" required autofocus>
        </div>
        @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="d-grid mt-4">
        <button type="submit" class="btn btn-solid-blue py-2">
            Kirim Link Pemulihan
        </button>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('login') }}" class="text-decoration-none small text-primary fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Halaman Login
        </a>
    </div>
</form>
@endsection