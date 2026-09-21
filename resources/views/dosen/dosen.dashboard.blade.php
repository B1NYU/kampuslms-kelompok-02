<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dosen — Portal KampusLMS</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/dosen/dosen.dashboard.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/dosen/dosen.dashboard.css') }}">
    @endif
</head>

<body>

    <!-- Background Decorative Glow -->
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <div class="app-window">

        <!-- 1. Navbar Khusus Dosen -->
        <x-navbar-dosen />

        <!-- 2. Konten Utama Dashboard Dosen -->
        <main class="dosen-content" id="overview">

            <!-- Topbar Header -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <span class="page-eyebrow">
                            <span class="page-eyebrow-dot"></span>
                            PORTAL AKADEMIK DOSEN &bull; KELOMPOK 02
                        </span>
                        <h1>Dashboard Manajemen Perkuliahan</h1>
                    </div>
                </div>

                <!-- Course Selector / Filter Kelas -->
                <div class="course-filter-bar">
                    <span class="course-filter-label">Mata Kuliah Aktif:</span>
                    <select id="selectCurrentCourse" class="course-select">
                        <option value="SI101" selected>SI101 &bull; Pemrograman Web (3 SKS)</option>
                        <option value="SI102">SI102 &bull; Basis Data Lanjut (3 SKS)</option>
                        <option value="SI103">SI103 &bull; Analisis &amp; Desain SI (4 SKS)</option>
                    </select>
                </div>
            </header>

            <!-- 3. Kartu Statistik Utama Dosen (4 Cards) -->
            <section class="dosen-stats-grid">
                <a href="{{ route('dosen.mahasiswa') }}" class="dosen-stat-card text-inherit">
                    <div class="stat-icon-wrap stat-icon-students">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="stat-details">
                        <span class="stat-label">Mahasiswa Bimbingan</span>
                        <span class="stat-value" id="statTotalStudents">38</span>
                        <span class="stat-desc">Terdaftar di kelas ini &rarr;</span>
                    </div>
                </a>

                <a href="{{ route('dosen.materi') }}" class="dosen-stat-card text-inherit">
                    <div class="stat-icon-wrap stat-icon-materials">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                    <div class="stat-details">
                        <span class="stat-label">Materi Perkuliahan</span>
                        <span class="stat-value" id="statTotalMaterials">6</span>
                        <span class="stat-desc">PDF, PPTX &amp; Tautan &rarr;</span>
                    </div>
                </a>

                <a href="{{ route('dosen.tugas') }}" class="dosen-stat-card text-inherit">
                    <div class="stat-icon-wrap stat-icon-assignments">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <div class="stat-details">
                        <span class="stat-label">Tugas Aktif</span>
                        <span class="stat-value" id="statActiveAssignments">3</span>
                        <span class="stat-desc">Dengan batas waktu &rarr;</span>
                    </div>
                </a>

                <a href="{{ route('dosen.penilaian') }}" class="dosen-stat-card text-inherit">
                    <div class="stat-icon-wrap stat-icon-grading">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="stat-details">
                        <span class="stat-label">Perlu Dinilai</span>
                        <span class="stat-value" id="statPendingGrading">4</span>
                        <span class="stat-desc">Pengumpulan mahasiswa &rarr;</span>
                    </div>
                </a>
            </section>

            <!-- 4. Quick Access Grid ke Halaman-Halaman Terpisah -->
            <section class="margin-top-24">
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-header-left">
                            <div class="section-header-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                                    <polyline points="2 17 12 22 22 17"></polyline>
                                    <polyline points="2 12 12 17 22 12"></polyline>
                                </svg>
                            </div>
                            <div class="section-header-text">
                                <h2>Modul Portal Dosen &bull; Akses Cepat</h2>
                                <p>Pilih menu navigasi di bawah atau melalui navbar di atas untuk mengakses modul masing-masing.</p>
                            </div>
                        </div>
                        <span class="section-header-badge">Navigasi Terpisah</span>
                    </div>

                    <div class="grid-container">
                        
                        <!-- Modul 1: Kelola Mahasiswa -->
                        <div class="modul-card">
                            <div>
                                <div class="modul-card-header">
                                    <span class="modul-badge-1">Modul 01</span>
                                    <span class="modul-participants">38 Peserta Aktif</span>
                                </div>
                                <h3 class="modul-title">1. Kelola &amp; Pendaftaran Mahasiswa</h3>
                                <p class="modul-desc">Daftarkan mahasiswa baru langsung ke kelas, monitor daftar mahasiswa yang mengambil mata kuliah, dan kelola status kepesertaan.</p>
                            </div>
                            <a href="{{ route('dosen.mahasiswa') }}" class="btn-primary-action btn-modul">
                                Buka Kelola Mahasiswa &rarr;
                            </a>
                        </div>

                        <!-- Modul 2: Unggah Materi -->
                        <div class="modul-card">
                            <div>
                                <div class="modul-card-header">
                                    <span class="modul-badge-2">Modul 02</span>
                                    <span class="modul-participants">6 Modul Terbit</span>
                                </div>
                                <h3 class="modul-title">2. Unggah &amp; Distribusi Materi</h3>
                                <p class="modul-desc">Publikasikan modul perkuliahan terstruktur per pertemuan kuliah dalam bentuk berkas PDF, slide presentasi PPTX, atau tautan artikel web.</p>
                            </div>
                            <a href="{{ route('dosen.materi') }}" class="btn-primary-action btn-modul-2">
                                Buka Unggah Materi &rarr;
                            </a>
                        </div>

                        <!-- Modul 3: Buat Tugas -->
                        <div class="modul-card">
                            <div>
                                <div class="modul-card-header">
                                    <span class="modul-badge-3">Modul 03</span>
                                    <span class="modul-participants">3 Tugas Berjalan</span>
                                </div>
                                <h3 class="modul-title">3. Pembuatan Tugas &amp; Deadline</h3>
                                <p class="modul-desc">Buat penugasan baru dengan instruksi lengkap, tentukan tenggat waktu pengumpulan mahasiswa, dan pantau statistik penyelesaian tugas.</p>
                            </div>
                            <a href="{{ route('dosen.tugas') }}" class="btn-primary-action btn-modul">
                                Buka Buat Tugas &rarr;
                            </a>
                        </div>

                        <!-- Modul 4: Penilaian & Feedback -->
                        <div class="modul-card">
                            <div>
                                <div class="modul-card-header">
                                    <span class="modul-badge-4">Modul 04</span>
                                    <span class="modul-participants-green">4 Menunggu Review</span>
                                </div>
                                <h3 class="modul-title">4. Penilaian &amp; Umpan Balik</h3>
                                <p class="modul-desc">Evaluasi jawaban dan berkas mahasiswa, berikan skor nilai (skala 0–100), dan sertakan catatan umpan balik yang konstruktif.</p>
                            </div>
                            <a href="{{ route('dosen.penilaian') }}" class="btn-primary-action btn-modul-4">
                                Buka Penilaian &amp; Ulasan &rarr;
                            </a>
                        </div>

                    </div>
                </div>
            </section>

            <!-- 5. Informasi Jadwal Kuliah & Pengumuman -->
            <section class="margin-top-18">
                <div class="section-card schedule-card">
                    <div class="schedule-container">
                        <div class="schedule-info-group">
                            <div class="schedule-icon-box">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            </div>
                            <div>
                                <h4 class="schedule-title">Jadwal Perkuliahan Berikutnya</h4>
                                <span class="schedule-desc">Senin, 08.00 - 10.30 WIB &bull; Lab Komputer 3 &bull; Pemrograman Web (SI-A)</span>
                            </div>
                        </div>
                        <span class="badge-status badge-status-active schedule-badge">Kelas Siap Dimulai</span>
                    </div>
                </div>
            </section>

        </main>
    </div>

</body>

</html>
