<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | Kampus LMS</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Nunito', sans-serif;
        }

        :root {
            --bg-page: #FCEDD8;
            --primary: #B0182D;
            --primary-dark: #8F1223;
            --secondary: #E23C64;
            --accent-coral: #FF5E5E;
            --accent-yellow: #FFD464;
            --text-dark: #5A2F39;
            --text-muted: #8E6570;
            --card-bg: #FFFFFF;
            --card-border: #F2DCD3;
            --tag-bg: #FFF5E8;
            --tag-border: #F4D9C1;
        }

        body {
            background-color: var(--bg-page);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* =========================================
           BACKGROUND DECORATIVE SHAPES
           ========================================= */
        .bg-shape {
            position: fixed;
            border-radius: 50%;
            z-index: 0;
            filter: blur(1px);
            pointer-events: none;
            animation: floatShape 8s ease-in-out infinite alternate;
        }

        .bg-shape-1 {
            width: 360px;
            height: 360px;
            background: var(--accent-yellow);
            top: -130px;
            right: -80px;
            opacity: 0.85;
            animation-duration: 9s;
        }

        .bg-shape-2 {
            width: 280px;
            height: 280px;
            background: var(--accent-coral);
            bottom: -90px;
            left: -80px;
            opacity: 0.35;
            animation-duration: 11s;
        }

        .bg-shape-3 {
            width: 140px;
            height: 140px;
            background: var(--secondary);
            top: 45%;
            left: 10%;
            opacity: 0.16;
            animation-duration: 7s;
        }

        @keyframes floatShape {
            0% {
                transform: translateY(0px) scale(1);
            }
            100% {
                transform: translateY(-20px) scale(1.05);
            }
        }

        /* =========================================
           TOP NAVBAR
           ========================================= */
        .navbar {
            width: 100%;
            padding: 16px 8%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 10;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #FFFFFF;
            padding: 6px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(176, 24, 45, 0.08);
            border: 1px solid var(--card-border);
        }

        .brand-info h2 {
            font-size: 17px;
            font-weight: 900;
            color: var(--primary);
            letter-spacing: -0.3px;
            line-height: 1.1;
        }

        .brand-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 800;
            color: var(--secondary);
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        .nav-home-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 10px;
            background: #FFFFFF;
            border: 1px solid var(--tag-border);
            font-size: 13px;
            font-weight: 800;
            color: var(--primary);
            box-shadow: 0 2px 8px rgba(176, 24, 45, 0.05);
            transition: all 0.25s ease;
        }

        .nav-home-link:hover {
            background: var(--accent-yellow);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(176, 24, 45, 0.12);
        }

        /* =========================================
           ERROR CONTENT CONTAINER
           ========================================= */
        .error-wrapper {
            width: 100%;
            max-width: 760px;
            margin: auto;
            padding: 24px 20px;
            position: relative;
            z-index: 10;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .error-card {
            width: 100%;
            background: var(--card-bg);
            border-radius: 28px;
            padding: 44px 36px 38px;
            border: 1px solid var(--card-border);
            box-shadow:
                0 20px 50px rgba(176, 24, 45, 0.08),
                0 6px 18px rgba(176, 24, 45, 0.04);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Card Decorative Elements */
        .card-circle-top {
            position: absolute;
            width: 70px;
            height: 70px;
            background: var(--accent-yellow);
            border-radius: 50%;
            top: -20px;
            right: -15px;
            opacity: 0.8;
            pointer-events: none;
        }

        .card-circle-bottom {
            position: absolute;
            width: 44px;
            height: 44px;
            background: var(--accent-coral);
            border-radius: 50%;
            bottom: -15px;
            left: -15px;
            opacity: 0.6;
            pointer-events: none;
        }

        /* Visual Illustration */
        .illustration-box {
            position: relative;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .badge-404 {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: var(--tag-bg);
            border: 1px solid var(--tag-border);
            border-radius: 30px;
            color: var(--primary);
            font-size: 11.5px;
            font-weight: 800;
            letter-spacing: 0.8px;
            margin-bottom: 14px;
        }

        .badge-pulse-dot {
            width: 8px;
            height: 8px;
            background: var(--secondary);
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(226, 60, 100, 0.7);
            animation: pulseDot 2s infinite;
        }

        @keyframes pulseDot {
            0% {
                box-shadow: 0 0 0 0 rgba(226, 60, 100, 0.7);
            }
            70% {
                box-shadow: 0 0 0 8px rgba(226, 60, 100, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(226, 60, 100, 0);
            }
        }

        .error-number {
            font-size: clamp(72px, 12vw, 110px);
            font-weight: 900;
            line-height: 0.95;
            letter-spacing: -3px;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-shadow: 0 4px 18px rgba(176, 24, 45, 0.12);
            margin-bottom: 6px;
        }

        .error-number .digit-accent {
            color: var(--secondary);
            display: inline-block;
            position: relative;
            animation: bounceSoft 3s ease-in-out infinite;
        }

        @keyframes bounceSoft {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-8px);
            }
        }

        .error-title {
            font-size: clamp(22px, 3.5vw, 30px);
            font-weight: 900;
            color: var(--primary);
            letter-spacing: -0.6px;
            margin-bottom: 10px;
        }

        .error-desc {
            font-size: clamp(13px, 2vw, 15px);
            line-height: 1.6;
            color: var(--text-muted);
            max-width: 520px;
            margin: 0 auto 28px;
        }

        /* Action Buttons */
        .error-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 28px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.25s ease;
            border: none;
            outline: none;
        }

        .btn-primary {
            background: var(--secondary);
            color: #FFFFFF;
            box-shadow: 0 6px 18px rgba(226, 60, 100, 0.28);
        }

        .btn-primary:hover {
            background: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(176, 24, 45, 0.32);
        }

        .btn-secondary {
            background: var(--tag-bg);
            color: var(--primary);
            border: 1.5px solid var(--tag-border);
        }

        .btn-secondary:hover {
            background: var(--accent-yellow);
            border-color: #EAC250;
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }

        /* Quick Navigation Shortcuts */
        .quick-nav {
            padding-top: 22px;
            border-top: 1px solid var(--card-border);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .quick-nav-title {
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted);
        }

        .quick-nav-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .quick-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--primary);
            background: #FFF9F2;
            padding: 5px 12px;
            border-radius: 8px;
            border: 1px solid var(--tag-border);
            transition: all 0.2s ease;
        }

        .quick-link:hover {
            background: var(--secondary);
            color: #FFFFFF;
            border-color: var(--secondary);
            transform: translateY(-1px);
        }

        /* =========================================
           FOOTER
           ========================================= */
        .footer {
            width: 100%;
            padding: 16px 20px 20px;
            text-align: center;
            position: relative;
            z-index: 10;
        }

        .footer-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            font-size: 11.5px;
            font-weight: 600;
        }

        .social-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
        }

        .social-links a {
            color: var(--primary);
            font-weight: 700;
            transition: color 0.2s ease;
        }

        .social-links a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }

        /* Responsive Adjustments */
        @media (max-width: 640px) {
            .navbar {
                padding: 14px 5%;
            }

            .error-card {
                padding: 32px 20px 28px;
                border-radius: 22px;
            }

            .error-actions {
                flex-direction: column;
                width: 100%;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <!-- Background Decorative Glowing Shapes -->
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <!-- Dynamic Session Role Checking -->
    @php
        $role = session('user_role');
        $dashUrl = route('dashboard');
        $dashLabel = 'Dashboard Mahasiswa';

        if ($role === 'dosen') {
            $dashUrl = route('dosen.dashboard');
            $dashLabel = 'Dashboard Dosen';
        } elseif ($role === 'admin') {
            $dashUrl = route('admin.dashboard');
            $dashLabel = 'Dashboard Admin';
        } elseif (!$role) {
            $dashUrl = url('/');
            $dashLabel = 'Halaman Utama';
        }
    @endphp

    <!-- Top Navbar -->
    <header class="navbar">
        <div class="brand">
            <div class="brand-logo">
                <svg width="28" height="28" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="16" cy="20" r="11" stroke="#B0182D" stroke-width="3.5" fill="none" opacity="0.95" />
                    <circle cx="24" cy="20" r="11" stroke="#FFD464" stroke-width="3.5" fill="none" opacity="0.95" />
                </svg>
            </div>
            <div class="brand-info">
                <h2>KAMPUS LMS</h2>
                <span class="brand-badge">PORTAL AKADEMIK</span>
            </div>
        </div>

        <a href="{{ $dashUrl }}" class="nav-home-link">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>{{ $role ? 'Dashboard' : 'Beranda' }}</span>
        </a>
    </header>

    <!-- Main Content Card -->
    <main class="error-wrapper">
        <div class="error-card">
            <!-- Decorative Card Bubbles -->
            <div class="card-circle-top"></div>
            <div class="card-circle-bottom"></div>

            <!-- Error Status Pill -->
            <div class="badge-404">
                <span class="badge-pulse-dot"></span>
                <span>STATUS 404 • NOT FOUND</span>
            </div>

            <!-- Big Number -->
            <div class="error-number">
                <span>4</span>
                <span class="digit-accent">0</span>
                <span>4</span>
            </div>

            <!-- Title & Description -->
            <h1 class="error-title">Oops! Halaman Tidak Ditemukan</h1>
            <p class="error-desc">
                Halaman atau konten akademik yang Anda tuju tidak tersedia, telah dipindahkan, atau alamat tautan yang dimasukkan kurang tepat.
            </p>

            <!-- Action Buttons -->
            <div class="error-actions">
                <a href="{{ $dashUrl }}" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span>Ke {{ $dashLabel }}</span>
                </a>

                <button type="button" onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href='{{ url('/') }}'; }" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Kembali ke Halaman Sebelumnya</span>
                </button>
            </div>

            <!-- Quick Navigation -->
            <div class="quick-nav">
                <span class="quick-nav-title">Tautan Cepat Navigasi</span>
                <div class="quick-nav-links">
                    <a href="{{ url('/') }}" class="quick-link">
                        <span>🏠 Beranda</span>
                    </a>
                    <a href="{{ route('mata-kuliah.index') }}" class="quick-link">
                        <span>📚 Mata Kuliah</span>
                    </a>
                    <a href="{{ url('/tentang') }}" class="quick-link">
                        <span>👥 Tentang Kami</span>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div>&copy; {{ date('Y') }} <strong>Kampus LMS</strong> • Kelompok 02 Pemrograman Web</div>
            <div class="social-links">
                <a href="https://instagram.com" target="_blank">@baihaqi</a>
                <a href="https://instagram.com" target="_blank">@calvin</a>
                <a href="https://instagram.com" target="_blank">@clara</a>
                <a href="https://instagram.com" target="_blank">@desta</a>
                <a href="https://instagram.com" target="_blank">@devina</a>
            </div>
        </div>
    </footer>

</body>
</html>
