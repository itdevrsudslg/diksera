@extends('layouts.auth', ['title' => 'Reset Password DIKSERA'])

@section('auth-left')
    <div class="auth-left-inner">
        <div class="brand-pill">
            <span class="brand-dot"></span>
            <span>Keamanan Akun RSUD SLG</span>
        </div>

        <div class="auth-left-title">Atur Ulang Password</div>
        <div class="auth-left-subtitle">
            Gunakan kombinasi password yang kuat untuk melindungi data kompetensi Anda di platform DIKSERA.
        </div>

        <div class="auth-left-highlight">
            <i class="bi bi-shield-lock"></i>
            <span>Tips Password Aman:</span>
        </div>

        <ul class="auth-left-list">
            <li>
                <span class="bullet"></span>
                <span>Minimal 6 karakter dengan kombinasi huruf dan angka.</span>
            </li>
            <li>
                <span class="bullet"></span>
                <span>Hindari menggunakan tanggal lahir atau nama lengkap.</span>
            </li>
            <li>
                <span class="bullet"></span>
                <span>Jangan berikan password Anda kepada siapapun, termasuk admin.</span>
            </li>
        </ul>

        <div class="auth-left-meta mt-auto">
            <div class="auth-left-badge">
                Security System v2.1
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="text-center mb-4">
    <div class="logo-big mx-auto">
        <img src="{{ asset('icon.png') }}" alt="DIKSERA">
    </div>
    <div class="auth-title">Buat Password Baru</div>
    <p class="text-muted small">Silakan masukkan password baru Anda dua kali untuk konfirmasi.</p>
</div>

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">

    <div class="mb-3">
        <label class="form-label small text-muted">Password Baru</label>
        <input type="password" name="password" class="form-control form-control-light @error('password') is-invalid @enderror" required autofocus>
        @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label small text-muted">Konfirmasi Password Baru</label>
        <input type="password" name="password_confirmation" class="form-control form-control-light" required>
    </div>

    <div class="d-grid mt-4">
        <button type="submit" class="btn btn-solid-blue py-2">
            Perbarui Password
        </button>
    </div>
</form>
@endsection