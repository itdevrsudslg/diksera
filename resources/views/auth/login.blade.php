@extends('layouts.auth', ['title' => 'Login DIKSERA'])

@section('auth-left')
    <div class="auth-left-inner">
        <div class="brand-pill">
            <span class="brand-dot"></span>
            <span>Platform internal RSUD SLG</span>
        </div>

        <div class="auth-left-title">DIKSERA</div>
        <div class="auth-left-subtitle">
            Digitalisasi kompetensi, sertifikasi, dan evaluasi perawat dalam satu portal terpadu.
        </div>

        <div class="auth-left-highlight">
            <i class="bi bi-shield-check">✓</i>
            <span>Single sign-on untuk admin, perawat, dan pewawancara</span>
        </div>

        <ul class="auth-left-list">
            <li>
                <span class="bullet"></span>
                <span>Monitoring progres kompetensi dan sertifikasi perawat secara real-time.</span>
            </li>
            <li>
                <span class="bullet"></span>
                <span>Integrasi data evaluasi dan rekam jejak pengembangan SDM keperawatan.</span>
            </li>
            <li>
                <span class="bullet"></span>
                <span>Dashboard ringkas untuk kebutuhan Diklat, Komite Keperawatan, dan Manajemen.</span>
            </li>
        </ul>

        <div class="auth-left-meta">
            <div class="auth-left-badge">
                Versi beta · RSUD SLG Kediri
            </div>
            <div class="auth-left-hint">
                Butuh bantuan? Hubungi tim Diklat.
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="text-center mb-3">
    <div class="logo-big mx-auto">
        <img src="{{ asset('icon.png') }}" alt="DIKSERA">
    </div>
    <div class="auth-title">Masuk ke DIKSERA</div>
    <div class="auth-subtitle">
        Gunakan akun yang telah terdaftar (admin / perawat / pewawancara).
    </div>
</div>

<form method="POST" action="{{ route('login.process') }}" class="mt-2">
    @csrf

    <div class="mb-2">
        <label class="form-label small text-muted mb-1">Email</label>
        <input
            type="email"
            name="email"
            value="{{ old('email') }}"
            class="form-control form-control-sm form-control-light @error('email') is-invalid @enderror"
            required
            autofocus>
        @error('email')
            <div class="small text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-2">
        <label class="form-label small text-muted mb-1">Password</label>
        <div class="password-wrapper">
            <input
                id="password-input"
                type="password"
                name="password"
                class="form-control form-control-sm form-control-light @error('password') is-invalid @enderror"
                required>
            <button type="button" id="toggle-password" class="toggle-password-btn" data-target="password-input">
                Lihat
            </button>
        </div>
        @error('password')
            <div class="small text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-2 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember">
        <label for="remember" class="form-check-label small text-muted">Ingat saya</label>
    </div>
    
    

    <div class="d-grid mt-3">
        <button type="submit" class="btn btn-solid-blue">
            Masuk
        </button>
    </div>
    
    {{-- Di bawah input password --}}
<div class="text-end mt-1">
    <a href="{{ route('forget.password.get') }}" class="small text-muted text-decoration-none">Lupa password?</a>
</div>
</form>

<div class="bottom-link">
    Belum punya akun perawat?
    <a href="{{ route('register.perawat') }}">Registrasi di sini</a>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('password-input');
    const btn   = document.getElementById('toggle-password');

    if (!input || !btn) return;

    btn.addEventListener('click', function () {
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        this.textContent = isPassword ? 'Sembunyikan' : 'Lihat';
    });
});
</script>
@endpush
