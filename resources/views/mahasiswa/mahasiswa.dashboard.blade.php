<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student Management Campus</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    <!-- Memuat CSS via Vite sesuai lokasi resources/css/mahasiswa/mahasiswa.dashboard.css -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/mahasiswa/mahasiswa.dashboard.css', 'resources/js/app.js'])
    @endif
</head>

<body>

    <!-- Background Decorative Glow (selaras dengan halaman Daftar Anggota) -->
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>

    <!-- Main Window Canvas -->
    <div class="app-window">

        <!-- 1. Panggil Komponen Sidebar -->
        <x-layout />

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
                        <span class="page-eyebrow">
                            <span class="page-eyebrow-dot"></span>
                            OVERVIEW AKADEMIK • KELOMPOK 02
                        </span>
                        <h1>Dashboard LMS</h1>
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

            <!-- Row 1: 3 Stat Cards (Total Mahasiswa dihapus) -->
            <section class="stats-grid">
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

            <!-- Row 2: Middle Section (List Mata Kuliah Diambil & Aktivitas Mingguan) -->
            <section class="middle-grid">

                <!-- Kiri: List Mata Kuliah yang Diambil (menggantikan grafik) -->
                <div class="card-box">
                    <div class="card-header-clean">
                        <h3>Mata Kuliah yang Diambil</h3>
                        <span class="card-subtitle-tag">Semester Genap 2026</span>
                    </div>

                    @php
                        $mataKuliah = [
                            [
                                'kode' => 'IF301',
                                'nama' => 'Pemrograman Web Lanjut',
                                'sks' => 3,
                                'dosen' => 'Dr. Ahmad Fauzan',
                                'status' => 'Aktif',
                            ],
                            [
                                'kode' => 'IF302',
                                'nama' => 'Basis Data & Relasional',
                                'sks' => 3,
                                'dosen' => 'Rina Marlina, M.Kom',
                                'status' => 'Aktif',
                            ],
                            [
                                'kode' => 'IF305',
                                'nama' => 'Kecerdasan Buatan (AI)',
                                'sks' => 3,
                                'dosen' => 'Dr. Yusuf Pratama',
                                'status' => 'Aktif',
                            ],
                            [
                                'kode' => 'IF310',
                                'nama' => 'Rekayasa Perangkat Lunak',
                                'sks' => 3,
                                'dosen' => 'Siti Nurhaliza, M.T',
                                'status' => 'Aktif',
                            ],
                            [
                                'kode' => 'IF312',
                                'nama' => 'Jaringan Komputer',
                                'sks' => 2,
                                'dosen' => 'Budi Santoso, M.Kom',
                                'status' => 'Tidak Aktif    ',
                            ],
                            [
                                'kode' => 'IF318',
                                'nama' => 'Manajemen Proyek TI',
                                'sks' => 2,
                                'dosen' => 'Dr. Lestari Wibowo',
                                'status' => 'Aktif',
                            ],
                        ];
                    @endphp

                    <div class="mk-list">
                        @foreach ($mataKuliah as $mk)
                            <div class="mk-row">
                                <span class="mk-code">{{ $mk['kode'] }}</span>
                                <div class="mk-info">
                                    <span class="mk-name">{{ $mk['nama'] }}</span>
                                    <span class="mk-dosen">{{ $mk['dosen'] }}</span>
                                </div>
                                <span class="mk-sks">{{ $mk['sks'] }} SKS</span>
                                <span class="mk-status mk-status-{{ Str::slug($mk['status']) }}">{{ $mk['status'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Kanan: Aktivitas Mingguan (Mata Kuliah Terpopuler dihapus) -->
                <div class="right-column-stack">
                    <div class="card-box">
                        <div class="card-header-clean">
                            <h3>Aktivitas Mingguan</h3>
                            <span class="card-subtitle-tag">+18%</span>
                        </div>
                        <div class="bars-container">
                            <div class="bar-pill" style="height: 35%;"></div>
                            <div class="bar-pill" style="height: 60%;"></div>
                            <div class="bar-pill" style="height: 45%;"></div>
                            <div class="bar-pill" style="height: 80%;"></div>
                            <div class="bar-pill" style="height: 55%;"></div>
                            <div class="bar-pill" style="height: 90%;"></div>
                            <div class="bar-pill" style="height: 65%;"></div>
                            <div class="bar-pill" style="height: 40%;"></div>
                            <div class="bar-pill" style="height: 75%;"></div>
                            <div class="bar-pill" style="height: 95%;"></div>
                            <div class="bar-pill" style="height: 50%;"></div>
                            <div class="bar-pill" style="height: 70%;"></div>
                        </div>
                    </div>
                </div>

            </section>

            <!-- Row 3: Bottom Section (Progres Bulat & Pengumuman) -->
            <section class="bottom-grid">

                <!-- Kiri: Donut Progress Gauges -->
                <div class="card-box">
                    <div class="card-header-clean">
                        <h3>Progres Akademik Mahasiswa</h3>
                    </div>
                    <div class="progress-gauges">

                        <div class="gauge-item">
                            <div class="gauge-circle-wrap">
                                <svg class="gauge-svg" viewBox="0 0 36 36">
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="rgba(252,237,216,0.12)" stroke-width="3.2" />
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="#7311D4" stroke-width="3.2" stroke-dasharray="85, 100" stroke-linecap="round" />
                                </svg>
                                <span class="gauge-text">85%</span>
                            </div>
                            <span class="gauge-label">Presensi</span>
                        </div>

                        <div class="gauge-item">
                            <div class="gauge-circle-wrap">
                                <svg class="gauge-svg" viewBox="0 0 36 36">
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="rgba(252,237,216,0.12)" stroke-width="3.2" />
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="#FF5E5E" stroke-width="3.2" stroke-dasharray="70, 100" stroke-linecap="round" />
                                </svg>
                                <span class="gauge-text">70%</span>
                            </div>
                            <span class="gauge-label">Tugas Selesai</span>
                        </div>

                        <div class="gauge-item">
                            <div class="gauge-circle-wrap">
                                <svg class="gauge-svg" viewBox="0 0 36 36">
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="rgba(252,237,216,0.12)" stroke-width="3.2" />
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="#FFD464" stroke-width="3.2" stroke-dasharray="92, 100" stroke-linecap="round" />
                                </svg>
                                <span class="gauge-text">92%</span>
                            </div>
                            <span class="gauge-label">Target SKS</span>
                        </div>

                    </div>
                </div>

                <!-- Kanan: Pesan & Pengumuman Terbaru -->
                <div class="card-box">
                    <div class="card-header-clean">
                        <h3>Pengumuman & Pesan Terkini</h3>
                        <a href="#" class="link-see-all">Semua</a>
                    </div>
                    <div class="message-list">
                        <div class="message-item">
                            <span class="msg-sender">Biro Akademik</span>
                            <span class="msg-badge">Jadwal</span>
                            <span class="msg-body">Jadwal Ujian Tengah Semester Genap 2026 telah diterbitkan.</span>
                        </div>
                        <div class="message-item">
                            <span class="msg-sender">Dosen Web</span>
                            <span class="msg-badge">Tugas</span>
                            <span class="msg-body">Pengumpulan laporan Proyek Laravel Kelompok 02 dibuka hingga Jumat.</span>
                        </div>
                        <div class="message-item">
                            <span class="msg-sender">Kemahasiswaan</span>
                            <span class="msg-badge">Info</span>
                            <span class="msg-body">Sosialisasi beasiswa berprestasi di auditorium utama pk 09:00.</span>
                        </div>
                    </div>
                </div>

            </section>

        </main>
        <x-footer />
    </div>
</body>
</html>
