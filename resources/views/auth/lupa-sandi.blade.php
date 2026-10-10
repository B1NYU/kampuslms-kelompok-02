@extends('layouts.auth-tamu')

@section('title', 'Lupa Kata Sandi')

@section('content')
    <div class="card-header-badge">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
        </svg>
        <span>ATUR ULANG KATA SANDI</span>
    </div>

    @if (session('status'))
        <div class="auth-alert auth-alert-ok" role="status">✓ {{ session('status') }}</div>
    @endif

    <h2>Lupa Kata Sandi?</h2>
    <p class="login-subtitle">
        Masukkan NIM/NIP atau email akun Anda. Kami akan mengirim tautan untuk mengatur ulang kata sandi
        ke email yang terdaftar. Fitur ini untuk akun dosen dan mahasiswa.
    </p>

    <form action="{{ route('password.email') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="identifier" class="form-label">NIM / NIP / Email</label>
            <div class="input-wrapper">
                <span class="input-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </span>
                <input type="text" id="identifier" name="identifier" value="{{ old('identifier') }}"
                       class="form-input" placeholder="Contoh: 10241014 atau email@kampus.id"
                       required autofocus autocomplete="username">
            </div>
            @error('identifier')
                <span class="auth-field-error" role="alert">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="login-submit">
            <span>Kirim Tautan Reset</span>
        </button>
    </form>
@endsection
