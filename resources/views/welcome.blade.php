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
            background: #FCEDD8;
            color: #B0182D;
            min-height: 100vh;
            overflow-x: hidden;
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
            background: #FFD464;
            top: -130px;
            right: -80px;
        }

        .shape-two {
            width: 230px;
            height: 230px;
            background: #FF5E5E;
            opacity: 0.35;
            bottom: -80px;
            left: -80px;
        }

        .shape-three {
            width: 120px;
            height: 120px;
            background: #E23C64;
            opacity: 0.16;
            top: 42%;
            left: 44%;
        }

        /* =========================
           NAVBAR
        ========================= */

        .navbar {
            width: 100%;
            padding: 28px 7%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 13px;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: #E23C64;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 8px 20px rgba(226, 60, 100, 0.25);
        }

        .brand-text h2 {
            font-size: 18px;
            font-weight: 800;
            color: #B0182D;
            line-height: 1.1;
        }

        .brand-text p {
            font-size: 11px;
            color: #8E5360;
            margin-top: 4px;
            font-weight: 600;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-link {
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            transition: all 0.25s ease;
        }

        .nav-login {
            color: #B0182D;
            background: #FFF5E8;
            border: 1px solid #F4D9C1;
        }

        .nav-login:hover {
            background: #FFD464;
            transform: translateY(-2px);
        }

        .nav-register {
            color: white;
            background: #E23C64;
            box-shadow: 0 6px 15px rgba(226, 60, 100, 0.22);
        }

        .nav-register:hover {
            background: #B0182D;
            transform: translateY(-2px);
        }

        .nav-dashboard {
            color: white;
            background: #E23C64;
        }

        /* =========================
           MAIN
        ========================= */

        .main-container {
            width: 86%;
            max-width: 1250px;
            margin: 45px auto 0;
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            align-items: center;
            gap: 80px;
            min-height: calc(100vh - 150px);
            padding-bottom: 50px;
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
            gap: 8px;
            padding: 8px 15px;
            background: #FFD464;
            color: #B0182D;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.7px;
            margin-bottom: 20px;
        }

        .welcome-dot {
            width: 8px;
            height: 8px;
            background: #E23C64;
            border-radius: 50%;
        }

        .left-content h1 {
            font-size: clamp(45px, 5vw, 70px);
            line-height: 1.02;
            font-weight: 900;
            letter-spacing: -2.5px;
            color: #B0182D;
            max-width: 650px;
            margin-bottom: 24px;
        }

        .left-content h1 span {
            color: #E23C64;
        }

        .description {
            max-width: 590px;
            color: #80525D;
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 32px;
        }

        /* =========================
           FEATURES
        ========================= */

        .features {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            margin-bottom: 35px;
        }

        .feature {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #69424C;
            font-size: 13px;
            font-weight: 700;
        }

        .feature-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .feature-blue {
            background: #FFD464;
        }

        .feature-yellow {
            background: #FF5E5E;
        }

        .feature-purple {
            background: #E23C64;
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
            max-width: 430px;
            background: #FFFFFF;
            border-radius: 28px;
            padding: 35px;
            box-shadow:
                0 25px 60px rgba(176, 24, 45, 0.10),
                0 8px 25px rgba(176, 24, 45, 0.06);
            border: 1px solid #F2DCD3;
            position: relative;
        }

        .card-decoration {
            position: absolute;
            width: 75px;
            height: 75px;
            background: #FFD464;
            border-radius: 50%;
            top: -28px;
            right: -20px;
            z-index: 0;
        }

        .card-decoration-two {
            position: absolute;
            width: 45px;
            height: 45px;
            background: #FF5E5E;
            border-radius: 50%;
            bottom: -15px;
            left: -15px;
            opacity: 0.8;
        }

        .login-card-content {
            position: relative;
            z-index: 1;
        }

        .portal-icon {
            width: 58px;
            height: 58px;
            border-radius: 17px;
            background: #FCEDD8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 27px;
            margin-bottom: 20px;
            border: 1px solid #F5D9C3;
        }

        .login-card h2 {
            font-size: 27px;
            font-weight: 900;
            color: #B0182D;
            margin-bottom: 7px;
        }

        .login-subtitle {
            color: #8E6570;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 27px;
        }

        /* =========================
           FORM
        ========================= */

        .form-group {
            margin-bottom: 19px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 800;
            color: #5F3540;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 17px;
            color: #E23C64;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            height: 50px;
            border: 1.5px solid #EBD6CE;
            border-radius: 13px;
            padding: 0 15px 0 45px;
            outline: none;
            font-family: 'Nunito', sans-serif;
            font-size: 14px;
            color: #5A2F39;
            background: #FFFDFB;
            transition: all 0.25s ease;
        }

        .form-input::placeholder {
            color: #B89CA2;
        }

        .form-input:focus {
            border-color: #E23C64;
            background: white;
            box-shadow: 0 0 0 4px rgba(226, 60, 100, 0.10);
        }

        /* =========================
           LOGIN BUTTON
        ========================= */

        .login-submit {
            width: 100%;
            height: 51px;
            border: none;
            border-radius: 13px;
            background: #E23C64;
            color: white;
            font-family: 'Nunito', sans-serif;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            margin-top: 5px;
            box-shadow: 0 8px 18px rgba(226, 60, 100, 0.22);
            transition: all 0.25s ease;
        }

        .login-submit:hover {
            background: #B0182D;
            transform: translateY(-2px);
            box-shadow: 0 11px 22px rgba(176, 24, 45, 0.25);
        }

        .login-submit:active {
            transform: translateY(0);
        }

        /* =========================
           REGISTER
        ========================= */

        .register-area {
            text-align: center;
            margin-top: 22px;
            padding-top: 20px;
            border-top: 1px solid #F1E1DA;
        }

        .register-area p {
            color: #987982;
            font-size: 13px;
            margin-bottom: 9px;
        }

        .register-link {
            color: #E23C64;
            font-size: 13px;
            font-weight: 800;
        }

        .register-link:hover {
            color: #B0182D;
            text-decoration: underline;
        }

        /* =========================
           FOOTER
        ========================= */

        .footer {
            text-align: center;
            padding: 0 20px 25px;
            color: #9C737B;
            font-size: 12px;
            font-weight: 600;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 950px) {

            .main-container {
                grid-template-columns: 1fr;
                gap: 50px;
                margin-top: 35px;
            }

            .left-content {
                text-align: center;
            }

            .left-content h1 {
                margin-left: auto;
                margin-right: auto;
            }

            .description {
                margin-left: auto;
                margin-right: auto;
            }

            .features {
                justify-content: center;
            }

            .right-content {
                padding-bottom: 20px;
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

            <a href="/login" class="nav-link nav-login">
                Log in
            </a>

            <a href="/register" class="nav-link nav-register">
                Register
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


                <div class="login-card-content">


                    <!-- Portal Icon -->
                    <div class="portal-icon">
                        🎓
                    </div>


                    <!-- Title -->
                    <h2>
                        Student Portal
                    </h2>


                    <p class="login-subtitle">
                        Welcome back! Enter your information
                        to access your campus account.
                    </p>



                    <!-- =========================
                         LOGIN FORM
                    ========================== -->

                    <form action="/login" method="GET">


                        <!-- Nama -->
                        <div class="form-group">

                            <label
                                for="nama"
                                class="form-label"
                            >
                                Nama
                            </label>


                            <div class="input-wrapper">

                                <span class="input-icon">
                                    👤
                                </span>


                                <input
                                    type="text"
                                    id="nama"
                                    name="nama"
                                    class="form-input"
                                    placeholder="Masukkan nama"
                                    autocomplete="name"
                                >

                            </div>

                        </div>



                        <!-- NIM -->
                        <div class="form-group">

                            <label
                                for="nim"
                                class="form-label"
                            >
                                NIM
                            </label>


                            <div class="input-wrapper">

                                <span class="input-icon">
                                    🪪
                                </span>


                                <input
                                    type="text"
                                    id="nim"
                                    name="nim"
                                    class="form-input"
                                    placeholder="Masukkan NIM"
                                >

                            </div>

                        </div>



                        <!-- Login Button -->
                        <button
                            type="submit"
                            class="login-submit"
                        >
                            Log In
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

</body>

</html>