@extends('layouts.auth-tamu')

@section('title', 'Atur Ulang Kata Sandi')

@section('content')
    <div class="card-header-badge">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
        </svg>
        <span>KATA SANDI BARU</span>
    </div>

    @if ($errors->has('email'))
        <div class="auth-alert auth-alert-err" role="alert">
            ⚠ {{ $errors->first('email') }}
            <div style="margin-top: 8px;"><a href="{{ route('password.request') }}" style="color: #B42318; text-decoration: underline;">Minta tautan baru</a></div>
        </div>
    @endif

    <h2>Atur Kata Sandi Baru</h2>
    <p class="login-subtitle">Buat kata sandi baru untuk akun Anda. Setelah berhasil, semua perangkat yang sedang login akan dikeluarkan.</p>

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ old('email', $email) }}">

        <div class="form-group">
            <label for="password" class="form-label">Kata Sandi Baru</label>
            <div class="input-wrapper">
                <span class="input-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </span>
                <input type="password" id="password" name="password" class="form-input"
                       placeholder="Minimal 8 karakter" autocomplete="new-password" required autofocus>
                <button type="button" class="toggle-password" data-toggle-sandi="password" title="Lihat / sembunyikan">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
            @error('password')
                <span class="auth-field-error" role="alert">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Ulangi Kata Sandi Baru</label>
            <div class="input-wrapper">
                <span class="input-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </span>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input"
                       placeholder="Ketik ulang kata sandi baru" autocomplete="new-password" required>
            </div>
            @include('auth._aturan-sandi')
        </div>

        <button type="submit" class="login-submit"><span>Simpan Kata Sandi Baru</span></button>
    </form>

    <a href="{{ url('/') }}" class="auth-back">&larr; Kembali ke halaman login</a>
@endsection
