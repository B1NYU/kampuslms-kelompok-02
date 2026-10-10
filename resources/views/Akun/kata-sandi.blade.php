@php
    $isDosen = auth()->user()->role === 'dosen';
    $cssPeran = $isDosen ? 'resources/css/dosen/dosen.common.css' : 'resources/css/mahasiswa/mahasiswa.dashboard.css';
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Kata Sandi — Portal KampusLMS</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', $cssPeran, 'resources/js/app.js'])
    @endif

    <style>
        .pw-card { background: #FFFFFF; border: 1px solid rgba(3, 159, 250, 0.18); border-radius: 20px; padding: 28px 30px; max-width: 560px; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05); font-family: 'Nunito', sans-serif; }
        .pw-card h2 { margin: 0 0 4px; font-size: 20px; font-weight: 900; color: #0F172A; }
        .pw-card p.pw-sub { margin: 0 0 22px; font-size: 13.5px; font-weight: 600; color: #64748B; line-height: 1.55; }
        .pw-field { margin-bottom: 18px; }
        .pw-field label { display: block; font-size: 13px; font-weight: 800; color: #334155; margin-bottom: 6px; }
        .pw-input-wrap { position: relative; }
        .pw-input { width: 100%; box-sizing: border-box; height: 46px; padding: 0 44px 0 14px; border: 1.5px solid rgba(3, 159, 250, 0.28); border-radius: 12px; font: 600 14px 'Nunito', sans-serif; color: #0F172A; background: #F8FAFC; outline: none; }
        .pw-input:focus { border-color: #039FFA; background: #FFF; box-shadow: 0 0 0 3.5px rgba(3, 159, 250, 0.16); }
        .pw-input.is-invalid { border-color: #F87171; }
        .pw-eye { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: 0; cursor: pointer; color: #64748B; padding: 6px; display: flex; }
        .pw-error { display: block; margin-top: 6px; font-size: 12.5px; font-weight: 700; color: #B42318; }
        .pw-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 6px; }
        .pw-btn { height: 44px; padding: 0 22px; border-radius: 12px; border: 0; font: 800 14px 'Nunito', sans-serif; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; }
        .pw-btn-primary { background: #039FFA; color: #FFF; }
        .pw-btn-primary:hover { background: #0288D8; }
        .pw-btn-ghost { background: #F1F5F9; color: #334155; }
        .pw-info { margin-top: 20px; padding: 12px 14px; background: #F0F9FF; border: 1px dashed rgba(3, 159, 250, 0.35); border-radius: 12px; font-size: 12.5px; font-weight: 600; color: #0369A1; line-height: 1.55; }
    </style>
</head>

<body>
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <div class="app-window">
        @if ($isDosen)
            <x-navbar-dosen />
        @else
            <x-layout />
        @endif

        <main class="{{ $isDosen ? 'dosen-content' : 'dashboard-content' }}">
            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <h1>Ubah Kata Sandi</h1>
                    </div>
                </div>
            </header>

            <section class="pw-card">
                <h2>Keamanan Akun</h2>
                <p class="pw-sub">
                    Untuk keamanan, masukkan kata sandi Anda saat ini sebelum membuat yang baru.
                    Akun: <strong>{{ auth()->user()->name }}</strong>
                    @if (auth()->user()->nim_nip) ({{ auth()->user()->nim_nip }}) @endif
                </p>

                <form action="{{ route('akun.kata-sandi.update') }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="pw-field">
                        <label for="current_password">Kata Sandi Saat Ini</label>
                        <div class="pw-input-wrap">
                            <input type="password" id="current_password" name="current_password"
                                   class="pw-input @error('current_password') is-invalid @enderror"
                                   autocomplete="current-password" required>
                            <button type="button" class="pw-eye" data-toggle-sandi="current_password" title="Lihat / sembunyikan">&#128065;</button>
                        </div>
                        @error('current_password')
                            <span class="pw-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="pw-field">
                        <label for="password">Kata Sandi Baru</label>
                        <div class="pw-input-wrap">
                            <input type="password" id="password" name="password"
                                   class="pw-input @error('password') is-invalid @enderror"
                                   autocomplete="new-password" required>
                            <button type="button" class="pw-eye" data-toggle-sandi="password" title="Lihat / sembunyikan">&#128065;</button>
                        </div>
                        @error('password')
                            <span class="pw-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="pw-field">
                        <label for="password_confirmation">Ulangi Kata Sandi Baru</label>
                        <div class="pw-input-wrap">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="pw-input" autocomplete="new-password" required>
                            <button type="button" class="pw-eye" data-toggle-sandi="password_confirmation" title="Lihat / sembunyikan">&#128065;</button>
                        </div>
                        @include('auth._aturan-sandi')
                    </div>

                    <div class="pw-actions">
                        <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="pw-btn pw-btn-ghost">Batal</a>
                        <button type="submit" class="pw-btn pw-btn-primary">Simpan Kata Sandi</button>
                    </div>
                </form>

                <div class="pw-info">
                    Setelah kata sandi diubah, perangkat atau browser lain yang sedang login dengan akun ini
                    akan otomatis dikeluarkan. Sesi Anda saat ini tetap aktif.
                </div>
            </section>
        </main>

        <x-footer />
    </div>
</body>

</html>
