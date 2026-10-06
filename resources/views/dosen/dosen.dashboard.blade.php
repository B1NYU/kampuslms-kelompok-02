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

            <!-- 4. Informasi Jadwal Kuliah & Pengumuman -->
            <section class="margin-top-24">
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
        <x-footer />
    </div>

</body>

</html>
