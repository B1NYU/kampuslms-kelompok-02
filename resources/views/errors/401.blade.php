<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>401 - Autentikasi Dibutuhkan | Kampus LMS</title>

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
            --bg-page: #FFFDF8;
            --primary: #039FFA;
            --primary-dark: #0284C7;
            --secondary: #F96305;
            --accent-coral: #F96305;
            --accent-yellow: #F9B804;
            --accent-sky: #32B3F1;
            --text-dark: #0F172A;
            --text-muted: #64748B;
            --card-bg: #FFFFFF;
            --card-border: rgba(3, 159, 250, 0.18);
            --tag-bg: rgba(3, 159, 250, 0.08);
            --tag-border: rgba(3, 159, 250, 0.22);
        }

        body {
            background-color: var(--bg-page);
            background-image: 
                radial-gradient(at 0% 0%, rgba(3, 159, 250, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(50, 179, 241, 0.10) 0px, transparent 45%),
                radial-gradient(at 100% 100%, rgba(249, 99, 5, 0.08) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(249, 184, 4, 0.08) 0px, transparent 50%),
                linear-gradient(170deg, #F8FAFD 0%, #FDFBF7 45%, #FFFDF8 100%);
            background-attachment: fixed;
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
            filter: blur(40px);
            pointer-events: none;
            animation: floatShape 8s ease-in-out infinite alternate;
        }

        .bg-shape-1 {
            width: 360px;
            height: 360px;
            background: var(--accent-sky);
            top: -130px;
            right: -80px;
            opacity: 0.45;
            animation-duration: 9s;
        }

        .bg-shape-2 {
            width: 280px;
            height: 280px;
            background: var(--accent-yellow);
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
            opacity: 0.20;
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
            box-shadow: 0 4px 14px rgba(3, 159, 250, 0.12);
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
                0 20px 50px rgba(3, 159, 250, 0.08),
                0 6px 18px rgba(3, 159, 250, 0.04);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Card Decorative Elements */
        .card-circle-top {
            position: absolute;
            width: 70px;
            height: 70px;
            background: var(--accent-sky);
            border-radius: 50%;
            top: -20px;
            right: -15px;
            opacity: 0.25;
            pointer-events: none;
        }

        .card-circle-bottom {
            position: absolute;
            width: 44px;
            height: 44px;
            background: var(--accent-yellow);
            border-radius: 50%;
            bottom: -15px;
            left: -15px;
            opacity: 0.35;
            pointer-events: none;
        }

        /* Visual Illustration */
        .illustration-box {
            position: relative;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 12px;
        }

        .badge-401 {
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
            box-shadow: 0 0 0 0 rgba(249, 99, 5, 0.7);
            animation: pulseDot 2s infinite;
        }

        @keyframes pulseDot {
            0% {
                box-shadow: 0 0 0 0 rgba(249, 99, 5, 0.7);
            }
            70% {
                box-shadow: 0 0 0 8px rgba(249, 99, 5, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(249, 99, 5, 0);
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
            text-shadow: 0 4px 18px rgba(3, 159, 250, 0.15);
            margin-bottom: 12px;
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
            color: var(--text-dark);
            letter-spacing: -0.6px;
            margin-bottom: 10px;
        }

        .error-desc {
            font-size: clamp(13px, 2vw, 15px);
            line-height: 1.6;
            color: var(--text-muted);
            max-width: 540px;
            margin: 0 auto 20px;
        }

        /* Informative safe notice box */
        .info-notice-box {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #F0F9FF;
            border: 1px solid rgba(3, 159, 250, 0.22);
            border-radius: 12px;
            padding: 12px 18px;
            margin: 0 auto 28px;
            max-width: 520px;
            color: var(--text-dark);
            font-size: 13px;
            font-weight: 700;
            line-height: 1.5;
        }

        .info-notice-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: rgba(3, 159, 250, 0.12);
            color: var(--primary);
            flex-shrink: 0;
        }

        /* Action Buttons */
        .error-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 0;
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
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #FFFFFF;
            box-shadow: 0 6px 18px rgba(3, 159, 250, 0.28);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #0369A1 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(3, 159, 250, 0.35);
        }

        .btn-secondary {
            background: #FFFFFF;
            color: var(--primary);
            border: 1.5px solid var(--card-border);
            box-shadow: 0 2px 8px rgba(3, 159, 250, 0.06);
        }

        .btn-secondary:hover {
            background: #F0F9FF;
            border-color: var(--primary);
            color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(3, 159, 250, 0.12);
        }

        .btn:active {
            transform: translateY(0);
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
        $rawMessage = isset($exception) ? $exception->getMessage() : null;
        $safeMessage = ($rawMessage && !in_array($rawMessage, ['', 'Unauthorized', 'Unauthenticated.', 'This action is unauthorized.']))
            ? $rawMessage
            : 'Sesi Anda telah kedaluwarsa atau belum terautentikasi. Silakan masuk terlebih dahulu.';
        $loginUrl = route('login');
    @endphp

    <!-- Top Navbar -->
    <header class="navbar">
        <div class="brand">
            <div class="brand-logo">
                <svg width="28" height="28" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="16" cy="20" r="11" stroke="#039FFA" stroke-width="3.5" fill="none" opacity="0.95" />
                    <circle cx="24" cy="20" r="11" stroke="#F9B804" stroke-width="3.5" fill="none" opacity="0.95" />
                </svg>
            </div>
            <div class="brand-info">
                <h2>KAMPUS LMS</h2>
                <span class="brand-badge">PORTAL AKADEMIK</span>
            </div>
        </div>
    </header>

    <!-- Main Content Card -->
    <main class="error-wrapper">
        <div class="error-card">
            <!-- Decorative Card Bubbles -->
            <div class="card-circle-top"></div>
            <div class="card-circle-bottom"></div>

            <!-- Error Status Pill -->
            <div class="badge-401">
                <span class="badge-pulse-dot"></span>
                <span>STATUS 401 • AUTENTIKASI DIBUTUHKAN</span>
            </div>

            <!-- Big Number -->
            <div class="error-number">
                <span>4</span>
                <span class="digit-accent">0</span>
                <span>1</span>
            </div>

            <!-- Title & Description -->
            <h1 class="error-title">Sesi Berakhir atau Belum Masuk</h1>
            <p class="error-desc">
                Halaman akademik ini memerlukan autentikasi akun terdaftar. Silakan masuk dengan kredensial Anda untuk melanjutkan aktivitas perkuliahan.
            </p>

            <!-- Safe Informative Reason / Tip Box -->
            <div class="info-notice-box">
                <div class="info-notice-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <span>{{ $safeMessage }}</span>
            </div>

            <!-- Action Buttons -->
            <div class="error-actions">
                <a href="{{ $loginUrl }}" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    <span>Masuk ke Akun Anda</span>
                </a>

                <button type="button" onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href='{{ url('/') }}'; }" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Kembali ke Halaman Sebelumnya</span>
                </button>
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
