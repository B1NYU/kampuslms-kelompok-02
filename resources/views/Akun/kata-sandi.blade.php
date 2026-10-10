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
        .pw-page-container {
            width: 100%;
            max-width: 580px;
            margin: 10px auto 36px auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .pw-card {
            background: #FFFFFF;
            border: 1px solid rgba(3, 159, 250, 0.2);
            border-radius: 20px;
            padding: 30px 32px;
            box-shadow: 0 14px 34px -4px rgba(15, 23, 42, 0.07), 0 4px 14px -2px rgba(3, 159, 250, 0.05);
            font-family: 'Nunito', sans-serif;
            position: relative;
        }
        .pw-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #EFF6FF;
            color: #0284C7;
            font-size: 11px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            border: 1px solid rgba(3, 159, 250, 0.2);
        }
        .pw-card h2 { margin: 0 0 6px; font-size: 22px; font-weight: 900; color: #0F172A; display: flex; align-items: center; gap: 8px; }
        .pw-card p.pw-sub { margin: 0 0 16px; font-size: 13.5px; font-weight: 600; color: #64748B; line-height: 1.55; }
        .pw-user-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 10px 14px;
            margin-bottom: 22px;
        }
        .pw-user-pill-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #039FFA, #32B3F1);
            color: #FFFFFF;
            font-size: 12px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .pw-user-pill-text {
            display: flex;
            flex-direction: column;
            font-size: 12px;
            line-height: 1.3;
        }
        .pw-user-pill-name {
            font-weight: 800;
            color: #0F172A;
            font-size: 13px;
        }
        .pw-user-pill-meta {
            color: #64748B;
            font-weight: 600;
        }
        .pw-field { margin-bottom: 18px; }
        .pw-field label { display: block; font-size: 13px; font-weight: 800; color: #334155; margin-bottom: 6px; }
        .pw-input-wrap { position: relative; display: flex; align-items: center; }
        .pw-input-icon { position: absolute; left: 14px; color: #94A3B8; pointer-events: none; display: flex; align-items: center; }
        .pw-input { width: 100%; box-sizing: border-box; height: 46px; padding: 0 44px 0 40px; border: 1.5px solid rgba(3, 159, 250, 0.28); border-radius: 12px; font: 600 14px 'Nunito', sans-serif; color: #0F172A; background: #F8FAFC; outline: none; transition: all 0.2s ease; }
        .pw-input:focus { border-color: #039FFA; background: #FFF; box-shadow: 0 0 0 3.5px rgba(3, 159, 250, 0.16); }
        .pw-input.is-invalid { border-color: #F87171; background: #FFF5F5; }
        .pw-eye { position: absolute; right: 10px; background: none; border: 0; cursor: pointer; color: #64748B; padding: 6px; display: flex; border-radius: 6px; transition: color 0.2s; }
        .pw-eye:hover { color: #039FFA; }
        .pw-error { display: block; margin-top: 6px; font-size: 12.5px; font-weight: 700; color: #B42318; }
        .pw-actions { display: flex; justify-content: flex-end; align-items: center; gap: 12px; margin-top: 22px; padding-top: 16px; border-top: 1px solid #F1F5F9; }
        .pw-btn { height: 44px; padding: 0 22px; border-radius: 12px; border: 0; font: 800 14px 'Nunito', sans-serif; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; }
        .pw-btn-primary { background: linear-gradient(135deg, #039FFA, #0284C7); color: #FFF; box-shadow: 0 4px 12px rgba(3, 159, 250, 0.25); }
        .pw-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(3, 159, 250, 0.35); }
        .pw-btn-ghost { background: #F1F5F9; color: #475569; }
        .pw-btn-ghost:hover { background: #E2E8F0; color: #1E293B; }
        .pw-info { margin-top: 20px; padding: 14px 16px; background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 14px; font-size: 12.5px; font-weight: 600; color: #1E40AF; line-height: 1.55; display: flex; align-items: flex-start; gap: 10px; }
        .pw-info svg { flex-shrink: 0; margin-top: 2px; }
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

        <main class="{{ $isDosen ? 'dosen-content' : 'dashboard-content' }}" style="display: flex; flex-direction: column; align-items: center; justify-content: flex-start; min-height: calc(100vh - 160px); padding-top: 24px; padding-bottom: 40px;">
            <div class="pw-page-container">
                <section class="pw-card">
                    <div class="pw-header-badge">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <span>KEAMANAN AKUN</span>
                    </div>

                    <h2>Ubah Kata Sandi</h2>
                    <p class="pw-sub">
                        Untuk keamanan akun Anda, masukkan kata sandi saat ini sebelum menetapkan kata sandi baru.
                    </p>

                    <div class="pw-user-pill">
                        @php
                            $uName = auth()->user()->name ?? 'Pengguna';
                            $uParts = preg_split('/\s+/', trim($uName));
                            $uInit = strtoupper(mb_substr($uParts[0], 0, 1) . mb_substr($uParts[1] ?? '', 0, 1));
                        @endphp
                        <div class="pw-user-pill-avatar">{{ $uInit }}</div>
                        <div class="pw-user-pill-text">
                            <span class="pw-user-pill-name">{{ $uName }}</span>
                            <span class="pw-user-pill-meta">
                                {{ auth()->user()->nim_nip ? (auth()->user()->role === 'dosen' ? 'NIP: ' : 'NIM: ') . auth()->user()->nim_nip : ucfirst(auth()->user()->role) }}
                                &bull; {{ auth()->user()->email }}
                            </span>
                        </div>
                    </div>

                    <form action="{{ route('akun.kata-sandi.update') }}" method="POST" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="pw-field">
                            <label for="current_password">Kata Sandi Saat Ini</label>
                            <div class="pw-input-wrap">
                                <span class="pw-input-icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </span>
                                <input type="password" id="current_password" name="current_password"
                                       class="pw-input @error('current_password') is-invalid @enderror"
                                       placeholder="Masukkan kata sandi saat ini"
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
                                <span class="pw-input-icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 2l-2 2m-2-2l2 2m0 0l-3.5 3.5M7 11v4a2 2 0 0 0 2 2h4M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </span>
                                <input type="password" id="password" name="password"
                                       class="pw-input @error('password') is-invalid @enderror"
                                       placeholder="Minimal 8 karakter"
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
                                <span class="pw-input-icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </span>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="pw-input"
                                       placeholder="Ulangi kata sandi baru yang sama"
                                       autocomplete="new-password" required>
                                <button type="button" class="pw-eye" data-toggle-sandi="password_confirmation" title="Lihat / sembunyikan">&#128065;</button>
                            </div>
                            @include('auth._aturan-sandi')
                        </div>

                        <div class="pw-actions">
                            <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="pw-btn pw-btn-ghost">
                                Batal
                            </a>
                            <button type="submit" class="pw-btn pw-btn-primary">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                <span>Simpan Kata Sandi</span>
                            </button>
                        </div>
                    </form>

                    <div class="pw-info">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <span>Setelah kata sandi diubah, perangkat atau browser lain yang sedang login dengan akun ini akan otomatis dikeluarkan demi keamanan. Sesi Anda saat ini tetap aktif.</span>
                    </div>
                </section>
            </div>
        </main>

        <x-footer />
    </div>
</body>

</html>
