<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Tautan reset memuat token di URL: jangan bocorkan lewat header Referer ke pihak lain (font, dll). --}}
    <meta name="referrer" content="no-referrer">
    <title>@yield('title', 'Kata Sandi') — Edupath KampusLMS</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/welcome.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    @endif

    <style>
        .auth-note { font-size: 12.5px; color: #64748B; font-weight: 600; margin-top: 6px; line-height: 1.5; }
        .auth-alert { padding: 12px 16px; border-radius: 12px; font-size: 13.5px; font-weight: 800; margin-bottom: 20px; line-height: 1.5; }
        .auth-alert-ok  { background: #EBF9F1; color: #1B8A5A; border: 1.5px solid #C4EED0; }
        .auth-alert-err { background: #FDECEC; color: #B42318; border: 1.5px solid #F8C9C4; }
        .auth-field-error { display: block; margin-top: 6px; font-size: 12.5px; font-weight: 700; color: #B42318; }
        .auth-back { display: block; text-align: center; margin-top: 18px; font-size: 13.5px; font-weight: 800; color: #039FFA; text-decoration: none; }
        .auth-back:hover { text-decoration: underline; }
    </style>
</head>

<body>
    <div class="background-shape shape-one"></div>
    <div class="background-shape shape-two"></div>
    <div class="background-shape shape-three"></div>

    <header class="navbar">
        <div class="brand">
            <div class="brand-icon">🎓</div>
            <div class="brand-text">
                <h2>Edupath LMS</h2>
                <p>Campus Academic Management System</p>
            </div>
        </div>
        <nav class="nav-links">
            <a href="{{ url('/') }}" class="nav-link nav-register">Kembali ke Login</a>
        </nav>
    </header>

    <main class="main-container" style="grid-template-columns: 1fr;">
        <section class="right-content">
            <div class="login-card" style="max-width: 500px;">
                <div class="card-decoration"></div>
                <div class="card-decoration-two"></div>

                <div class="login-card-content">
                    @yield('content')
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        &copy; {{ date('Y') }} Edupath Kampus LMS &bull; Kelompok 02 Pemrograman Web
    </footer>

    @stack('scripts')
</body>

</html>
