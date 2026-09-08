<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student Management Campus</title>

    <!-- Google Fonts Nunito (Sesuai dengan welcome.blade.php) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Nunito', sans-serif;
        }

        body {
            background-color: #FCEDD8;
            color: #5F3540;
            min-height: 100vh;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        /* =========================================
           BACKGROUND DECORATIVE SHAPES (From welcome)
           ========================================= */
        .bg-shape {
            position: fixed;
            border-radius: 50%;
            z-index: 0;
            filter: blur(2px);
            pointer-events: none;
        }

        .bg-shape-1 {
            width: 380px;
            height: 380px;
            background: #FFD464;
            top: -120px;
            right: -80px;
            opacity: 0.7;
        }

        .bg-shape-2 {
            width: 300px;
            height: 300px;
            background: #FF5E5E;
            bottom: -90px;
            left: -80px;
            opacity: 0.35;
        }

        /* =========================================
           MAIN APP CONTAINER (Tablet/Canvas Frame)
           ========================================= */
        .app-window {
            width: 100%;
            max-width: 1380px;
            min-height: 92vh;
            background: #F8F3ED;
            border-radius: 28px;
            box-shadow: 0 30px 80px rgba(176, 24, 45, 0.12),
                        0 10px 25px rgba(176, 24, 45, 0.05);
            display: flex;
            position: relative;
            z-index: 1;
            overflow: hidden;
            border: 1px solid #F3DFD5;
        }

        /* =========================================
           MAIN CONTENT DASHBOARD
           ========================================= */
        .dashboard-content {
            flex: 1;
            padding: 32px 36px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Top Navbar */
        .dash-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 8px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-hamburger {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: #FFFFFF;
            border: 1px solid #F2DCD3;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(176, 24, 45, 0.04);
            transition: all 0.2s ease;
        }

        .btn-hamburger:hover {
            background: #FFF5E8;
            transform: translateY(-2px);
        }

        .btn-hamburger span {
            width: 18px;
            height: 2.5px;
            background: #B0182D;
            border-radius: 4px;
        }

        .page-title h1 {
            font-size: 22px;
            font-weight: 900;
            color: #B0182D;
            line-height: 1.1;
        }

        .page-title p {
            font-size: 12px;
            color: #8E6570;
            font-weight: 600;
            margin-top: 2px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .user-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #FFFFFF;
            padding: 6px 14px 6px 6px;
            border-radius: 30px;
            border: 1px solid #F2DCD3;
            box-shadow: 0 4px 14px rgba(176, 24, 45, 0.04);
        }

        .user-badge-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #E23C64;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
        }

        .user-badge-info {
            font-size: 12px;
            font-weight: 800;
            color: #5F3540;
        }

        .user-badge-role {
            font-size: 10px;
            color: #8E6570;
            display: block;
            margin-top: -2px;
        }

        /* =========================================
           ROW 1: 4 STAT CARDS (PILL CARDS)
           ========================================= */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .stat-card {
            background: #FFFFFF;
            border-radius: 22px;
            padding: 22px 24px;
            border: 1px solid #F2DCD3;
            box-shadow: 0 8px 24px rgba(176, 24, 45, 0.04);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: all 0.25s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 30px rgba(176, 24, 45, 0.08);
            border-color: #EBD6CE;
        }

        .stat-card .stat-label {
            font-size: 13px;
            font-weight: 800;
            color: #8E5360;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }

        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -0.5px;
        }

        .stat-color-1 { color: #FF5E5E; }
        .stat-color-2 { color: #E23C64; }
        .stat-color-3 { color: #B0182D; }
        .stat-color-4 { color: #7311D4; }

        /* =========================================
           ROW 2: MIDDLE SECTION (CHART & WIDGETS)
           ========================================= */
        .middle-grid {
            display: grid;
            grid-template-columns: 1.85fr 1fr;
            gap: 22px;
        }

        .card-box {
            background: #FFFFFF;
            border-radius: 22px;
            padding: 24px 26px;
            border: 1px solid #F2DCD3;
            box-shadow: 0 8px 24px rgba(176, 24, 45, 0.04);
        }

        .card-header-clean {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        .card-header-clean h3 {
            font-size: 15px;
            font-weight: 800;
            color: #5F3540;
            letter-spacing: -0.2px;
        }

        .card-subtitle-tag {
            font-size: 11px;
            font-weight: 800;
            color: #B0182D;
            background: #FFF5E8;
            padding: 4px 10px;
            border-radius: 20px;
            border: 1px solid #F4D9C1;
        }

        /* SVG Curved Mountain Wave Chart */
        .chart-wrapper {
            position: relative;
            width: 100%;
        }

        .chart-svg {
            width: 100%;
            height: auto;
            overflow: visible;
        }

        .chart-axis-text {
            font-size: 10px;
            font-weight: 700;
            fill: #987982;
        }

        .chart-grid-line {
            stroke: #F4E8E2;
            stroke-dasharray: 4, 4;
            stroke-width: 1;
        }

        /* Right Column Stack */
        .right-column-stack {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Most View Items List */
        .items-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
        }

        .item-id {
            color: #987982;
            font-weight: 800;
            font-size: 11px;
            width: 55px;
        }

        .item-name {
            flex: 1;
            color: #5F3540;
            font-weight: 700;
            padding: 0 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .btn-view-pill {
            background: #FF5E5E;
            color: #ffffff;
            font-size: 10px;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(255, 94, 94, 0.3);
        }

        .btn-view-pill:hover {
            background: #E23C64;
            transform: scale(1.05);
        }

        /* Growth Mini Bars */
        .bars-container {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            height: 65px;
            padding-top: 10px;
            gap: 4px;
        }

        .bar-pill {
            flex: 1;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .bar-pill:hover {
            transform: scaleY(1.1);
            filter: brightness(1.1);
        }

        /* =========================================
           ROW 3: BOTTOM SECTION (PROGRESS & MESSAGES)
           ========================================= */
        .bottom-grid {
            display: grid;
            grid-template-columns: 1fr 1.25fr;
            gap: 22px;
        }

        /* Progress Donut Rings */
        .progress-gauges {
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding: 10px 0;
        }

        .gauge-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .gauge-circle-wrap {
            position: relative;
            width: 76px;
            height: 76px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gauge-svg {
            transform: rotate(-90deg);
            width: 100%;
            height: 100%;
        }

        .gauge-text {
            position: absolute;
            font-size: 13px;
            font-weight: 900;
            color: #5F3540;
        }

        .gauge-label {
            font-size: 11px;
            font-weight: 800;
            color: #8E6570;
        }

        /* Message List */
        .message-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .message-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #F2DCD3;
        }

        .message-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .msg-sender {
            font-size: 12px;
            font-weight: 800;
            color: #B0182D;
            min-width: 85px;
        }

        .msg-badge {
            font-size: 10px;
            font-weight: 800;
            background: #FFF5E8;
            color: #E23C64;
            padding: 2px 7px;
            border-radius: 6px;
            border: 1px solid #F4D9C1;
            white-space: nowrap;
        }

        .msg-body {
            font-size: 12px;
            color: #69424C;
            font-weight: 600;
            line-height: 1.4;
            flex: 1;
        }

        /* =========================================
           RESPONSIVE DESIGN
           ========================================= */
        @media (max-width: 1100px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .middle-grid {
                grid-template-columns: 1fr;
            }
            .bottom-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            body {
                padding: 12px;
            }
            .app-window {
                flex-direction: column;
                border-radius: 20px;
            }
            .dashboard-content {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <!-- Background Decorative Elements (Style dari welcome.blade.php) -->
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>

    <!-- Main Window Canvas (Layout seperti gambar referensi) -->
    <div class="app-window">

        <!-- 1. Panggil Komponen Sidebar -->
        <x-sidebar />

        <!-- 2. Konten Utama Dashboard -->
        <main class="dashboard-content">

            <!-- Topbar (Hamburger & Profil) -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <button class="btn-hamburger" title="Menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <div class="page-title">
                        <h1>Dashboard LMS</h1>
                        <p>Student Management Campus · Overview Akademik</p>
                    </div>
                </div>

                <div class="topbar-right">
                    <div class="user-badge">
                        <div class="user-badge-avatar">BK</div>
                        <div class="user-badge-details">
                            <span class="user-badge-info">Baihaqi & Tim</span>
                            <span class="user-badge-role">Kelompok 02</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Row 1: 4 Stat Cards (Pill Boxes) -->
            <section class="stats-grid">
                <div class="stat-card">
                    <span class="stat-label">Total Mahasiswa</span>
                    <span class="stat-value stat-color-1">118</span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Mata Kuliah</span>
                    <span class="stat-value stat-color-2">24</span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Rata-rata Nilai</span>
                    <span class="stat-value stat-color-3">3.82</span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Tugas Pending</span>
                    <span class="stat-value stat-color-4">5</span>
                </div>
            </section>

            <!-- Row 2: Middle Section (Grafik Gelombang & Widget Kanan) -->
            <section class="middle-grid">

                <!-- Kiri: Grafik Gelombang Utama (Mountain Wave SVG) -->
                <div class="card-box">
                    <div class="card-header-clean">
                        <h3>Grafik Aktivitas Belajar Mahasiswa</h3>
                        <span class="card-subtitle-tag">Semester Genap 2026</span>
                    </div>

                    <div class="chart-wrapper">
                        <svg class="chart-svg" viewBox="0 0 680 230" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <!-- Gradien Area Gelombang (Pinkish Crimson ke Coral Orange) -->
                                <linearGradient id="waveFill" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#E23C64" stop-opacity="0.88" />
                                    <stop offset="60%" stop-color="#FF5E5E" stop-opacity="0.75" />
                                    <stop offset="100%" stop-color="#FF9234" stop-opacity="0.85" />
                                </linearGradient>

                                <!-- Gradien Garis Puncak -->
                                <linearGradient id="waveStroke" x1="0" y1="0" x2="1" y2="0">
                                    <stop offset="0%" stop-color="#B0182D" />
                                    <stop offset="50%" stop-color="#E23C64" />
                                    <stop offset="100%" stop-color="#FF5E5E" />
                                </linearGradient>
                            </defs>

                            <!-- Garis Horizontal Grid -->
                            <line x1="45" y1="35" x2="650" y2="35" class="chart-grid-line" />
                            <line x1="45" y1="80" x2="650" y2="80" class="chart-grid-line" />
                            <line x1="45" y1="125" x2="650" y2="125" class="chart-grid-line" />
                            <line x1="45" y1="170" x2="650" y2="170" class="chart-grid-line" />

                            <!-- Sumbu Y -->
                            <text x="10" y="40" class="chart-axis-text">1250</text>
                            <text x="10" y="85" class="chart-axis-text">1000</text>
                            <text x="15" y="130" class="chart-axis-text">750</text>
                            <text x="15" y="175" class="chart-axis-text">500</text>

                            <!-- Area Grafik Gelombang Kurva (Mountain Wave) -->
                            <path d="M 50,195 
                                     C 100,195 130,150 180,140 
                                     C 230,130 250,155 290,120 
                                     C 340,75 365,42 410,75 
                                     C 455,108 480,180 535,185 
                                     C 570,188 590,135 625,140 
                                     C 640,142 645,170 650,195 
                                     L 650,195 L 50,195 Z" 
                                  fill="url(#waveFill)" />

                            <!-- Garis Puncak Gelombang -->
                            <path d="M 50,195 
                                     C 100,195 130,150 180,140 
                                     C 230,130 250,155 290,120 
                                     C 340,75 365,42 410,75 
                                     C 455,108 480,180 535,185 
                                     C 570,188 590,135 625,140 
                                     C 640,142 645,170 650,195" 
                                  stroke="url(#waveStroke)" 
                                  stroke-width="3" 
                                  fill="none" 
                                  stroke-linecap="round" />

                            <!-- Sumbu X (Bulan) -->
                            <text x="75" y="215" class="chart-axis-text">Jan</text>
                            <text x="165" y="215" class="chart-axis-text">Feb</text>
                            <text x="255" y="215" class="chart-axis-text">Mar</text>
                            <text x="345" y="215" class="chart-axis-text">Apr</text>
                            <text x="435" y="215" class="chart-axis-text">Mei</text>
                            <text x="525" y="215" class="chart-axis-text">Jun</text>
                            <text x="615" y="215" class="chart-axis-text">Jul</text>
                        </svg>
                    </div>
                </div>

                <!-- Kanan: 2 Kartu (Mata Kuliah Terpopuler & Growth) -->
                <div class="right-column-stack">

                    <!-- Kartu Mata Kuliah Terpopuler -->
                    <div class="card-box">
                        <div class="card-header-clean">
                            <h3>Mata Kuliah Terpopuler</h3>
                        </div>
                        <div class="items-list">
                            <div class="item-row">
                                <span class="item-id">#MK001</span>
                                <span class="item-name">Pemrograman Web</span>
                                <a href="#" class="btn-view-pill">Lihat</a>
                            </div>
                            <div class="item-row">
                                <span class="item-id">#MK002</span>
                                <span class="item-name">Basis Data & Relasional</span>
                                <a href="#" class="btn-view-pill">Lihat</a>
                            </div>
                            <div class="item-row">
                                <span class="item-id">#MK003</span>
                                <span class="item-name">Kecerdasan Buatan (AI)</span>
                                <a href="#" class="btn-view-pill">Lihat</a>
                            </div>
                        </div>
                    </div>

                    <!-- Kartu Pertumbuhan / Aktivitas Belajar (Growth) -->
                    <div class="card-box">
                        <div class="card-header-clean">
                            <h3>Aktivitas Mingguan</h3>
                            <span class="card-subtitle-tag">+18%</span>
                        </div>
                        <div class="bars-container">
                            <div class="bar-pill" style="height: 35%; background: #E23C64;"></div>
                            <div class="bar-pill" style="height: 60%; background: #FFD464;"></div>
                            <div class="bar-pill" style="height: 45%; background: #FF5E5E;"></div>
                            <div class="bar-pill" style="height: 80%; background: #B0182D;"></div>
                            <div class="bar-pill" style="height: 55%; background: #7311D4;"></div>
                            <div class="bar-pill" style="height: 90%; background: #E23C64;"></div>
                            <div class="bar-pill" style="height: 65%; background: #FFD464;"></div>
                            <div class="bar-pill" style="height: 40%; background: #FF5E5E;"></div>
                            <div class="bar-pill" style="height: 75%; background: #B0182D;"></div>
                            <div class="bar-pill" style="height: 95%; background: #7311D4;"></div>
                            <div class="bar-pill" style="height: 50%; background: #E23C64;"></div>
                            <div class="bar-pill" style="height: 70%; background: #FFD464;"></div>
                        </div>
                    </div>

                </div>

            </section>

            <!-- Row 3: Bottom Section (Progres Bulat & Pengumuman) -->
            <section class="bottom-grid">

                <!-- Kiri: Donut Progress Gauges (Persis Lingkaran Expense di Gambar) -->
                <div class="card-box">
                    <div class="card-header-clean">
                        <h3>Progres Akademik Mahasiswa</h3>
                    </div>
                    <div class="progress-gauges">

                        <!-- Gauge 1: 85% Presensi -->
                        <div class="gauge-item">
                            <div class="gauge-circle-wrap">
                                <svg class="gauge-svg" viewBox="0 0 36 36">
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                                          fill="none" stroke="#F4E0DC" stroke-width="3.2" />
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                                          fill="none" stroke="#7311D4" stroke-width="3.2" stroke-dasharray="85, 100" stroke-linecap="round" />
                                </svg>
                                <span class="gauge-text">85%</span>
                            </div>
                            <span class="gauge-label">Presensi</span>
                        </div>

                        <!-- Gauge 2: 70% Tugas -->
                        <div class="gauge-item">
                            <div class="gauge-circle-wrap">
                                <svg class="gauge-svg" viewBox="0 0 36 36">
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                                          fill="none" stroke="#F4E0DC" stroke-width="3.2" />
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                                          fill="none" stroke="#FF5E5E" stroke-width="3.2" stroke-dasharray="70, 100" stroke-linecap="round" />
                                </svg>
                                <span class="gauge-text">70%</span>
                            </div>
                            <span class="gauge-label">Tugas Selesai</span>
                        </div>

                        <!-- Gauge 3: 92% SKS -->
                        <div class="gauge-item">
                            <div class="gauge-circle-wrap">
                                <svg class="gauge-svg" viewBox="0 0 36 36">
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                                          fill="none" stroke="#F4E0DC" stroke-width="3.2" />
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                                          fill="none" stroke="#E23C64" stroke-width="3.2" stroke-dasharray="92, 100" stroke-linecap="round" />
                                </svg>
                                <span class="gauge-text">92%</span>
                            </div>
                            <span class="gauge-label">Target SKS</span>
                        </div>

                    </div>
                </div>

                <!-- Kanan: Pesan & Pengumuman Terbaru (Message) -->
                <div class="card-box">
                    <div class="card-header-clean">
                        <h3>Pengumuman & Pesan Terkini</h3>
                        <a href="#" style="color: #E23C64; font-size: 11px; font-weight: 800; text-decoration: none;">Semua</a>
                    </div>
                    <div class="message-list">
                        <div class="message-item">
                            <span class="msg-sender">Biro Akademik</span>
                            <span class="msg-badge">[Jadwal]</span>
                            <span class="msg-body">Jadwal Ujian Tengah Semester Genap 2026 telah diterbitkan.</span>
                        </div>
                        <div class="message-item">
                            <span class="msg-sender">Dosen Web</span>
                            <span class="msg-badge">[Tugas]</span>
                            <span class="msg-body">Pengumpulan laporan Proyek Laravel Kelompok 02 dibuka hingga Jumat.</span>
                        </div>
                        <div class="message-item">
                            <span class="msg-sender">Kemahasiswaan</span>
                            <span class="msg-badge">[Info]</span>
                            <span class="msg-body">Sosialisasi beasiswa berprestasi di auditorium utama pk 09:00.</span>
                        </div>
                    </div>
                </div>

            </section>

        </main>
    </div>

</body>

</html>
