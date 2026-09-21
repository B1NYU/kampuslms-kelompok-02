<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Student Management Campus</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: #FFFDF8;
            color: #039FFA;
            height: 100vh;
            max-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

        a {
            text-decoration: none;
        }

        /* =========================
           BACKGROUND DECORATION
        ========================= */

        .background-shape {
            position: fixed;
            border-radius: 50%;
            z-index: -1;
            filter: blur(1px);
        }

        .shape-one {
            width: 320px;
            height: 320px;
            background: #32B3F1;
            top: -130px;
            right: -80px;
        }

        .shape-two {
            width: 230px;
            height: 230px;
            background: #F9B804;
            opacity: 0.35;
            bottom: -80px;
            left: -80px;
        }

        .shape-three {
            width: 120px;
            height: 120px;
            background: #F96305;
            opacity: 0.16;
            top: 42%;
            left: 44%;
        }

        /* =========================
           NAVBAR
        ========================= */

        .navbar {
            width: 100%;
            padding: 12px 8%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #039FFA;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(3, 159, 250, 0.28);
        }

        .brand-text h2 {
            font-size: 16px;
            font-weight: 800;
            color: #039FFA;
            line-height: 1.1;
        }

        .brand-text p {
            font-size: 10px;
            color: #F96305;
            margin-top: 2px;
            font-weight: 600;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link {
            padding: 7px 15px;
            border-radius: 10px;
            font-size: 12.5px;
            font-weight: 700;
            transition: all 0.25s ease;
        }

        .nav-login {
            color: #039FFA;
            background: #FFFDF8;
            border: 1px solid rgba(3, 159, 250, 0.25);
        }

        .nav-login:hover {
            background: #32B3F1;
            color: #FFFFFF;
            transform: translateY(-2px);
        }

        .nav-register {
            color: white;
            background: #F96305;
            box-shadow: 0 6px 15px rgba(249, 99, 5, 0.25);
        }

        .nav-register:hover {
            background: #039FFA;
            transform: translateY(-2px);
        }

        .nav-dashboard {
            color: white;
            background: #039FFA;
        }

        /* =========================
           MAIN
        ========================= */

        .main-container {
            width: 82%;
            max-width: 940px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.08fr 0.92fr;
            align-items: center;
            justify-content: center;
            gap: 44px;
            flex: 1;
            padding: 0;
            min-height: 0;
        }

        /* =========================
           LEFT CONTENT
        ========================= */

        .left-content {
            position: relative;
        }

        .welcome-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background: #F9B804;
            color: #FFFFFF;
            border-radius: 30px;
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: 0.6px;
            margin-bottom: 10px;
        }

        .welcome-dot {
            width: 6px;
            height: 6px;
            background: #F96305;
            border-radius: 50%;
        }

        .left-content h1 {
            font-size: clamp(26px, 3.2vw, 42px);
            line-height: 1.08;
            font-weight: 900;
            letter-spacing: -1.2px;
            color: #039FFA;
            max-width: 520px;
            margin-bottom: 10px;
        }

        .left-content h1 span {
            color: #F96305;
        }

        .description {
            max-width: 480px;
            color: #334155;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 14px;
        }

        /* =========================
           FEATURES
        ========================= */

        .features {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 0;
        }

        .feature {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #334155;
            font-size: 11.5px;
            font-weight: 700;
        }

        .feature-icon {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        .feature-blue {
            background: #32B3F1;
            color: #FFFFFF;
        }

        .feature-yellow {
            background: #F9B804;
            color: #FFFFFF;
        }

        .feature-purple {
            background: #F96305;
            color: #FFFFFF;
        }

        /* =========================
           RIGHT LOGIN CARD
        ========================= */

        .right-content {
            display: flex;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: 375px;
            background: #FFFFFF;
            border-radius: 20px;
            padding: 18px 22px;
            box-shadow:
                0 15px 40px rgba(3, 159, 250, 0.08),
                0 4px 15px rgba(249, 99, 5, 0.05);
            border: 1px solid rgba(3, 159, 250, 0.25);
            position: relative;
        }

        .card-decoration {
            position: absolute;
            width: 50px;
            height: 50px;
            background: #F9B804;
            border-radius: 50%;
            top: -14px;
            right: -10px;
            z-index: 0;
        }

        .card-decoration-two {
            position: absolute;
            width: 28px;
            height: 28px;
            background: #F96305;
            border-radius: 50%;
            bottom: -8px;
            left: -8px;
            opacity: 0.8;
        }

        .login-card-content {
            position: relative;
            z-index: 1;
        }

        .portal-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #FFFDF8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-bottom: 6px;
            border: 1px solid rgba(3, 159, 250, 0.25);
        }

        .login-card h2 {
            font-size: 18px;
            font-weight: 900;
            color: #039FFA;
            margin-bottom: 2px;
        }

        .login-subtitle {
            color: #F96305;
            font-size: 11.5px;
            line-height: 1.35;
            margin-bottom: 10px;
        }

        /* =========================
           ROLE SELECTOR
        ========================= */

        .role-switch-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 4px;
            background: #FFFDF8;
            padding: 3px;
            border-radius: 9px;
            border: 1px solid rgba(3, 159, 250, 0.2);
            margin-bottom: 10px;
        }

        .role-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 5px 6px;
            border-radius: 7px;
            border: none;
            background: transparent;
            color: #039FFA;
            font-size: 11.5px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.25s ease;
            font-family: 'Nunito', sans-serif;
        }

        .role-btn:hover {
            color: #F96305;
        }

        .role-btn.active {
            background: #FFFFFF;
            color: #039FFA;
            box-shadow: 0 2px 8px rgba(3, 159, 250, 0.12);
            border: 1px solid rgba(3, 159, 250, 0.35);
        }

        /* =========================
           FORM
        ========================= */

        .form-group {
            margin-bottom: 8px;
        }

        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 800;
            color: #039FFA;
            margin-bottom: 3px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            color: #039FFA;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            height: 36px;
            border: 1.5px solid rgba(3, 159, 250, 0.28);
            border-radius: 9px;
            padding: 0 12px;
            outline: none;
            font-family: 'Nunito', sans-serif;
            font-size: 12px;
            color: #0F172A;
            background: #FFFFFF;
            transition: all 0.25s ease;
        }

        .form-input::placeholder {
            color: #94A3B8;
        }

        .form-input:focus {
            border-color: #039FFA;
            background: white;
            box-shadow: 0 0 0 3px rgba(3, 159, 250, 0.18);
        }

        /* =========================
           LOGIN BUTTON
        ========================= */

        .login-submit {
            width: 100%;
            height: 37px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #F96305, #F9B804);
            color: white;
            font-family: 'Nunito', sans-serif;
            font-size: 12.5px;
            font-weight: 800;
            cursor: pointer;
            margin-top: 2px;
            box-shadow: 0 4px 12px rgba(249, 99, 5, 0.28);
            transition: all 0.25s ease;
        }

        .login-submit:hover {
            background: linear-gradient(135deg, #039FFA, #32B3F1);
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(3, 159, 250, 0.3);
        }

        .login-submit:active {
            transform: translateY(0);
        }

        /* =========================
           REGISTER
        ========================= */

        .register-area {
            text-align: center;
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px solid rgba(3, 159, 250, 0.18);
        }

        .register-area p {
            color: #64748B;
            font-size: 11px;
            margin-bottom: 2px;
        }

        .register-link {
            color: #F96305;
            font-size: 11px;
            font-weight: 800;
        }

        .register-link:hover {
            color: #D19F1F;
            text-decoration: underline;
        }

        /* =========================
           FOOTER
        ========================= */

        .footer {
            text-align: center;
            padding: 4px 20px 8px;
            color: #64748B;
            font-size: 10.5px;
            font-weight: 600;
            flex-shrink: 0;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 820px) {

            body {
                overflow-y: auto;
                height: auto;
                max-height: none;
            }

            .main-container {
                grid-template-columns: 1fr;
                gap: 28px;
                padding: 16px 0 24px;
            }

            .left-content {
                text-align: center;
            }

            .left-content h1,
            .description {
                margin-left: auto;
                margin-right: auto;
            }

            .features {
                justify-content: center;
            }

            .right-content {
                padding-bottom: 10px;
            }
        }

        @media (max-width: 600px) {

            .navbar {
                padding: 20px 5%;
            }

            .brand-text p {
                display: none;
            }

            .brand-text h2 {
                font-size: 15px;
            }

            .brand-icon {
                width: 42px;
                height: 42px;
                font-size: 20px;
            }

            .nav-links {
                gap: 5px;
            }

            .nav-link {
                padding: 8px 12px;
                font-size: 12px;
            }

            .main-container {
                width: 90%;
                margin-top: 25px;
            }

            .left-content h1 {
                font-size: 42px;
                letter-spacing: -1.5px;
            }

            .description {
                font-size: 14px;
            }

            .features {
                flex-direction: column;
                align-items: center;
            }

            .login-card {
                padding: 27px 22px;
                border-radius: 23px;
            }

            .login-card h2 {
                font-size: 24px;
            }
        }
    </style>
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
                <h2>Edupath</h2>
                <p>Campus Academic Management System</p>
            </div>

        </div>


        <nav class="nav-links">

            <a href="#loginCard" class="nav-link nav-login" onclick="document.getElementById('loginCard').scrollIntoView({behavior: 'smooth'}); return false;">
                Log in
            </a>

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
             LEFT SIDE
        ========================== -->

        <section class="left-content">

            <div class="welcome-label">
                <span class="welcome-dot"></span>
                STUDENT MANAGEMENT CAMPUS
            </div>


            <h1>
                Manage your
                <span>campus journey.</span>
            </h1>


            <p class="description">
                A centralized campus management system designed to help
                students manage academic information, courses, attendance,
                schedules, and campus activities in one place.
            </p>


            <div class="features">

                <div class="feature">

                    <div class="feature-icon feature-blue">
                        👨‍🎓
                    </div>

                    <span>
                        Student Information
                    </span>

                </div>


                <div class="feature">

                    <div class="feature-icon feature-yellow">
                        📚
                    </div>

                    <span>
                        Academic Management
                    </span>

                </div>


                <div class="feature">

                    <div class="feature-icon feature-purple">
                        🎯
                    </div>

                    <span>
                        Campus Activities
                    </span>

                </div>

            </div>

        </section>



        <!-- =========================
             RIGHT SIDE - LOGIN CARD
        ========================== -->

        <section class="right-content">

            <div class="login-card">

                <!-- Decorative circles -->
                <div class="card-decoration"></div>
                <div class="card-decoration-two"></div>


                <div class="login-card-content" id="loginCard">

                    <!-- Role Switcher: Mahasiswa vs Dosen -->
                    <div class="role-switch-container">
                        <button type="button" class="role-btn active" id="btnRoleMahasiswa" onclick="selectRole('mahasiswa')">
                            <span>Mahasiswa</span>
                        </button>
                        <button type="button" class="role-btn" id="btnRoleDosen" onclick="selectRole('dosen')">
                            <span>Dosen</span>
                        </button>
                        <button type="button" class="role-btn" id="btnRoleAdmin" onclick="selectRole('admin')">
                            <span>Admin</span>
                        </button>
                    </div>

                    @if(session('status'))
                        <div style="background: #EBF9F1; color: #1B8A5A; border: 1px solid #C4EED0; padding: 10px 14px; border-radius: 12px; font-size: 13px; font-weight: 800; margin-bottom: 16px; text-align: center;">
                            ✓ {{ session('status') }}
                        </div>
                    @endif


                    <!-- Title -->
                    <h2 id="portalTitle">
                        Student Portal
                    </h2>

                    <p class="login-subtitle" id="portalSubtitle">
                        Selamat datang! Masukkan informasi akun untuk mengakses perkuliahan Anda.
                    </p>

                    <!-- =========================
                         LOGIN FORM
                    ========================== -->
                    <form action="/login" method="POST" id="loginForm">
                        @csrf
                        <input type="hidden" name="role" id="inputRole" value="mahasiswa">

                        <!-- Nama -->
                        <div class="form-group">
                            <label for="nama" class="form-label" id="labelNama">
                                Nama Lengkap
                            </label>
                            <div class="input-wrapper">
                                <input
                                    type="text"
                                    id="nama"
                                    name="nama"
                                    class="form-input"
                                    placeholder="Masukkan nama Anda"
                                    autocomplete="name"
                                    required
                                >
                            </div>
                        </div>

                        <!-- NIM / NIP -->
                        <div class="form-group">
                            <label for="identifier" class="form-label" id="labelIdentifier">
                                NIM (Nomor Induk Mahasiswa)
                            </label>
                            <div class="input-wrapper">
                                <input
                                    type="text"
                                    id="identifier"
                                    name="identifier"
                                    class="form-input"
                                    placeholder="Contoh: 10241014"
                                    required
                                >
                            </div>
                        </div>

                        <!-- Login Button -->
                        <button
                            type="submit"
                            class="login-submit"
                            id="btnSubmitLogin"
                        >
                            Masuk sebagai Mahasiswa &rarr;
                        </button>

                    </form>



                    <!-- =========================
                         REGISTER
                    ========================== -->

                    <div class="register-area">

                        <p>
                            Belum memiliki akun?
                        </p>


                        <a
                            href="/register"
                            class="register-link"
                        >
                            Create an account
                        </a>

                    </div>

                </div>

            </div>

        </section>

    </main>



    <!-- =========================
         FOOTER
    ========================== -->

    <footer class="footer">
        Student Management Campus · Academic Management System
    </footer>

    <script>
        function selectRole(role) {
            const btnMhs   = document.getElementById('btnRoleMahasiswa');
            const btnDosen = document.getElementById('btnRoleDosen');
            const btnAdmin = document.getElementById('btnRoleAdmin');
            const inputRole = document.getElementById('inputRole');
            const portalTitle = document.getElementById('portalTitle');
            const portalSubtitle = document.getElementById('portalSubtitle');
            const labelIdentifier = document.getElementById('labelIdentifier');
            const inputIdentifier = document.getElementById('identifier');
            const btnSubmit = document.getElementById('btnSubmitLogin');

            // Reset semua tab
            btnMhs.classList.remove('active');
            btnDosen.classList.remove('active');
            btnAdmin.classList.remove('active');

            if (role === 'dosen') {
                btnDosen.classList.add('active');
                inputRole.value = 'dosen';
                portalTitle.textContent = 'Dosen Portal';
                portalSubtitle.textContent = 'Selamat datang Dosen! Masukkan identitas Anda untuk mengelola kelas, materi, dan perkuliahan.';
                labelIdentifier.textContent = 'NIP (Nomor Induk Pegawai)';
                inputIdentifier.placeholder = 'Contoh: 198503122010121002';
                btnSubmit.innerHTML = 'Masuk sebagai Dosen &rarr;';
                btnSubmit.style.background = 'linear-gradient(135deg, #D19F1F, #F9B804)';
            } else if (role === 'admin') {
                btnAdmin.classList.add('active');
                inputRole.value = 'admin';
                portalTitle.textContent = 'Admin Portal';
                portalSubtitle.textContent = 'Akses administrator penuh. Masukkan ID Admin Anda untuk melanjutkan.';
                labelIdentifier.textContent = 'ID Admin';
                inputIdentifier.placeholder = 'Contoh: ADMIN-001';
                btnSubmit.innerHTML = 'Masuk sebagai Admin &rarr;';
                btnSubmit.style.background = 'linear-gradient(135deg, #F96305, #F86306)';
            } else {
                btnMhs.classList.add('active');
                inputRole.value = 'mahasiswa';
                portalTitle.textContent = 'Student Portal';
                portalSubtitle.textContent = 'Selamat datang! Masukkan informasi akun untuk mengakses perkuliahan Anda.';
                labelIdentifier.textContent = 'NIM (Nomor Induk Mahasiswa)';
                inputIdentifier.placeholder = 'Contoh: 10241014';
                btnSubmit.innerHTML = 'Masuk sebagai Mahasiswa &rarr;';
                btnSubmit.style.background = 'linear-gradient(135deg, #039FFA, #32B3F1)';
            }
        }
    </script>
</body>

</html>