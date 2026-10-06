<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Portal Masuk — Edupath KampusLMS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/welcome.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    @endif
</head>

<body>

    <!-- Background Decoration -->
    <div class="background-shape shape-one"></div>
    <div class="background-shape shape-two"></div>
    <div class="background-shape shape-three"></div>


    <!-- =========================
         NAVBAR
    ========================== -->

    <header class="navbar">

        <div class="brand">
            <div class="brand-icon">
                🎓
            </div>
            <div class="brand-text">
                <h2>Edupath LMS</h2>
                <p>Campus Academic Management System</p>
            </div>
        </div>

        <nav class="nav-links">
            <a href="/tentang" class="nav-link nav-register">
                Tentang Kami
            </a>
        </nav>

    </header>


    <!-- =========================
         MAIN CONTENT
    ========================== -->

    <main class="main-container">

        <!-- =========================
             LEFT SIDE (DESKRIPSI & FITUR)
        ========================== -->

        <section class="left-content">

            <div class="welcome-label">
                <span class="welcome-dot"></span>
                PORTAL AKADEMIK TERPADU &bull; KELOMPOK 02
            </div>

            <h1>
                Kelola Aktivitas
                <span>Perkuliahan Anda</span>
                dalam Satu Tempat.
            </h1>

            <p class="description">
                Sistem manajemen perkuliahan daring terpusat untuk seluruh civitas akademika. 
                Mempermudah mahasiswa, dosen, dan administrator dalam mengakses materi pembelajaran, 
                jadwal perkuliahan, pengumpulan tugas, dan evaluasi akademik secara transparan dan efisien.
            </p>

            <div class="features-grid">

                <div class="feature-card">
                    <div class="feature-icon-box feature-blue">
                        👨‍🎓
                    </div>
                    <div class="feature-info">
                        <h4>Mahasiswa</h4>
                        <p>Akses materi, kumpulkan tugas, dan pantau nilai mata kuliah.</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-box feature-yellow">
                        👨‍🏫
                    </div>
                    <div class="feature-info">
                        <h4>Dosen</h4>
                        <p>Unggah materi ajar, buat tugas, dan berikan penilaian berkas.</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-box feature-orange">
                        🛡️
                    </div>
                    <div class="feature-info">
                        <h4>Administrator</h4>
                        <p>Kelola data akun, kurikulum prodi, dan pendaftaran perkuliahan.</p>
                    </div>
                </div>

            </div>

        </section>


        <!-- =========================
             RIGHT SIDE (FORM LOGIN TERPADU)
        ========================== -->

        <section class="right-content">

            <div class="login-card" id="loginCard">

                <!-- Decorative circles -->
                <div class="card-decoration"></div>
                <div class="card-decoration-two"></div>

                <div class="login-card-content">

                    <div class="card-header-badge">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <span>PORTAL MASUK TUNGGAL</span>
                    </div>

                    @if(session('status'))
                        <div style="background: #EBF9F1; color: #1B8A5A; border: 1.5px solid #C4EED0; padding: 12px 16px; border-radius: 12px; font-size: 13.5px; font-weight: 800; margin-bottom: 20px; text-align: center;">
                            ✓ {{ session('status') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div style="background: #FDECEC; color: #B42318; border: 1.5px solid #F8C9C4; padding: 12px 16px; border-radius: 12px; font-size: 13.5px; font-weight: 800; margin-bottom: 20px; text-align: center;">
                            ⚠ {{ $errors->first() }}
                        </div>
                    @endif

                    <h2>Masuk ke Akun</h2>

                    <p class="login-subtitle">
                        Silakan masukkan kredensial akun Anda untuk mengakses sistem pembelajaran kampus.
                    </p>

                    <!-- =========================
                         LOGIN FORM (GENERAL UNTUK SEMUA ROLE)
                    ========================== -->
                    <form action="/login" method="POST" id="loginForm">
                        @csrf

                        <!-- Identitas: NIM / NIP / Email -->
                        <div class="form-group">
                            <label for="identifier" class="form-label">
                                NIM / NIP / Email
                            </label>
                            <div class="input-wrapper">
                                <span class="input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </span>
                                <input
                                    type="text"
                                    id="identifier"
                                    name="identifier"
                                    value="{{ old('identifier') }}"
                                    class="form-input"
                                    placeholder="Contoh: 10241014 atau email@kampus.id"
                                    required
                                    autofocus
                                >
                            </div>
                        </div>

                        <!-- Kata Sandi -->
                        <div class="form-group">
                            <label for="password" class="form-label">
                                Kata Sandi
                            </label>
                            <div class="input-wrapper">
                                <span class="input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </span>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-input"
                                    placeholder="Masukkan kata sandi akun"
                                    autocomplete="current-password"
                                    required
                                >
                                <button type="button" class="toggle-password" onclick="togglePasswordVisibility()" title="Lihat / Sembunyikan Kata Sandi">
                                    <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <button
                            type="submit"
                            class="login-submit"
                            id="btnSubmitLogin"
                        >
                            <span>Log In</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>

                    </form>

                </div>

            </div>

        </section>

    </main>


    <!-- =========================
         FOOTER
    ========================== -->

    <footer class="footer">
        &copy; {{ date('Y') }} Edupath Kampus LMS &bull; Kelompok 02 Pemrograman Web
    </footer>

    <script>
        function togglePasswordVisibility() {
            const pwdInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                `;
            } else {
                pwdInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                `;
            }
        }
    </script>
</body>

</html>