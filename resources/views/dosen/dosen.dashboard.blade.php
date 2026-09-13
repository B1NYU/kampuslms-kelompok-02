<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dosen - Student Management Campus</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    <!-- Memuat CSS Dosen via Vite sesuai resources/dosen/dosen.dashboard.css -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/dosen/dosen.dashboard.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/dosen/dosen.dashboard.css') }}">
    @endif
</head>

<body>

    <!-- Background Decorative Glow (selaras dengan laman lainnya) -->
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>

    <!-- Main Window Canvas -->
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
                            PORTAL AKADEMIK DOSEN • KELOMPOK 02
                        </span>
                        <h1>Dashboard Manajemen Perkuliahan</h1>
                    </div>
                </div>

                <!-- Course Selector / Filter Kelas -->
                <div class="course-filter-bar">
                    <span class="course-filter-label">Mata Kuliah Aktif:</span>
                    <select class="course-select" id="selectedCourse">
                        <option value="SI101" selected>SI101 • Pemrograman Web (3 SKS)</option>
                        <option value="SI102">SI102 • Basis Data (3 SKS)</option>
                        <option value="SI103">SI103 • Analisis & Perancangan Sistem (3 SKS)</option>
                    </select>
                </div>
            </header>

            <!-- Row 1: 4 Stat Cards Dosen -->
            <section class="dosen-stats-grid">
                <div class="dosen-stat-card">
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
                        <span class="stat-value" id="statStudentCount">38</span>
                        <span class="stat-desc">Terdaftar di kelas ini</span>
                    </div>
                </div>

                <div class="dosen-stat-card">
                    <div class="stat-icon-wrap stat-icon-materials">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <div class="stat-details">
                        <span class="stat-label">Materi Perkuliahan</span>
                        <span class="stat-value" id="statMaterialCount">6</span>
                        <span class="stat-desc">PDF, PPTX & Tautan</span>
                    </div>
                </div>

                <div class="dosen-stat-card">
                    <div class="stat-icon-wrap stat-icon-assignments">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                            <path d="M9 14l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="stat-details">
                        <span class="stat-label">Tugas Aktif</span>
                        <span class="stat-value" id="statAssignmentCount">3</span>
                        <span class="stat-desc">Dengan batas waktu</span>
                    </div>
                </div>

                <div class="dosen-stat-card">
                    <div class="stat-icon-wrap stat-icon-grading">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 14 14"></polyline>
                        </svg>
                    </div>
                    <div class="stat-details">
                        <span class="stat-label">Perlu Dinilai</span>
                        <span class="stat-value" id="statPendingGrading">4</span>
                        <span class="stat-desc">Pengumpulan mahasiswa</span>
                    </div>
                </div>
            </section>

            <!-- Quick Filter / Section Jump Tabs -->
            <div class="feature-tabs-bar">
                <button class="tab-btn active" data-filter="all">Semua Fitur</button>
                <button class="tab-btn" data-filter="mahasiswa">1. Pendaftaran Mahasiswa</button>
                <button class="tab-btn" data-filter="materi">2. Unggah Materi</button>
                <button class="tab-btn" data-filter="tugas">3. Buat Tugas</button>
                <button class="tab-btn" data-filter="penilaian">4. Penilaian & Feedback</button>
            </div>

            <!-- ===================================================================
                 FITUR 1: DOSEN MENDAFTARKAN MAHASISWA KE MATA KULIAH
                 =================================================================== -->
            <section class="feature-section" id="kelola-mahasiswa" data-section="mahasiswa">
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-header-left">
                            <div class="section-header-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <polyline points="16 11 18 13 22 9"></polyline>
                                </svg>
                            </div>
                            <div class="section-header-text">
                                <h2>1. Pendaftaran Mahasiswa ke Mata Kuliah</h2>
                                <p>Dosen dapat menambahkan mahasiswa baru ke kelas secara langsung dan mengelola daftar peserta.</p>
                            </div>
                        </div>
                        <span class="section-header-badge">Kelola Peserta Kelas</span>
                    </div>

                    <div class="two-col-grid">
                        <!-- Form Tambah / Daftarkan Mahasiswa -->
                        <form id="formAddStudent" class="card-form">
                            <h4 class="card-form-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="16"></line>
                                    <line x1="8" y1="12" x2="16" y2="12"></line>
                                </svg>
                                Form Pendaftaran Mahasiswa
                            </h4>

                            <div class="form-group">
                                <label for="mhsMatkul">Mata Kuliah Target <span class="required">*</span></label>
                                <select id="mhsMatkul" class="form-select" required>
                                    <option value="SI101 - Pemrograman Web">SI101 - Pemrograman Web</option>
                                    <option value="SI102 - Basis Data">SI102 - Basis Data</option>
                                    <option value="SI103 - Analisis & Perancangan Sistem">SI103 - Analisis & Perancangan Sistem</option>
                                </select>
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label for="mhsNim">Nomor Induk (NIM) <span class="required">*</span></label>
                                    <input type="text" id="mhsNim" class="form-control" placeholder="Contoh: 10241022" required>
                                </div>
                                <div class="form-group">
                                    <label for="mhsKelas">Kelas <span class="required">*</span></label>
                                    <select id="mhsKelas" class="form-select" required>
                                        <option value="SI-A">SI-A</option>
                                        <option value="SI-B">SI-B</option>
                                        <option value="TI-A">TI-A</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="mhsNama">Nama Lengkap Mahasiswa <span class="required">*</span></label>
                                <input type="text" id="mhsNama" class="form-control" placeholder="Nama mahasiswa..." required>
                            </div>

                            <div class="form-group">
                                <label for="mhsProdi">Program Studi</label>
                                <select id="mhsProdi" class="form-select">
                                    <option value="Sistem Informasi">Sistem Informasi</option>
                                    <option value="Teknologi Informasi">Teknologi Informasi</option>
                                    <option value="Informatika">Informatika</option>
                                </select>
                            </div>

                            <button type="submit" class="btn-primary-action">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                Daftarkan Mahasiswa
                            </button>
                        </form>

                        <!-- Tabel Mahasiswa Terdaftar -->
                        <div class="table-container">
                            <div class="table-header-tools">
                                <span class="table-summary-info">Menampilkan <strong id="studentTableCount">5</strong> Mahasiswa Terdaftar</span>
                                <div class="search-input-box">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                    <input type="text" id="searchStudentInput" placeholder="Cari mahasiswa/NIM...">
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="custom-dosen-table" id="studentTable">
                                    <thead>
                                        <tr>
                                            <th>Mahasiswa</th>
                                            <th>Program Studi</th>
                                            <th>Kelas</th>
                                            <th>Status</th>
                                            <th style="text-align: right;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="studentTableBody">
                                        <!-- Data Awal Mahasiswa (disesuaikan dengan kelompok 02) -->
                                        <tr>
                                            <td>
                                                <div class="student-cell">
                                                    <div class="student-avatar" style="background:#FFE2E8; color:#B0182D;">BK</div>
                                                    <div class="student-meta">
                                                        <span class="student-name">Baihaqi Abimanyu</span>
                                                        <span class="student-nim">10241014</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Sistem Informasi</td>
                                            <td><span class="card-subtitle-tag" style="padding:2px 8px; font-size:10px;">SI-A</span></td>
                                            <td><span class="badge-status badge-status-active">Aktif</span></td>
                                            <td style="text-align: right;">
                                                <button class="btn-icon-danger btn-delete-student" title="Keluarkan dari kelas">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="student-cell">
                                                    <div class="student-avatar" style="background:#FFF0DE; color:#C98A1F;">CA</div>
                                                    <div class="student-meta">
                                                        <span class="student-name">Calvin Adithya</span>
                                                        <span class="student-nim">10241016</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Sistem Informasi</td>
                                            <td><span class="card-subtitle-tag" style="padding:2px 8px; font-size:10px;">SI-A</span></td>
                                            <td><span class="badge-status badge-status-active">Aktif</span></td>
                                            <td style="text-align: right;">
                                                <button class="btn-icon-danger btn-delete-student" title="Keluarkan dari kelas">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="student-cell">
                                                    <div class="student-avatar" style="background:#EBF3FF; color:#1971C2;">CL</div>
                                                    <div class="student-meta">
                                                        <span class="student-name">Clara Shinta</span>
                                                        <span class="student-nim">10241018</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Sistem Informasi</td>
                                            <td><span class="card-subtitle-tag" style="padding:2px 8px; font-size:10px;">SI-A</span></td>
                                            <td><span class="badge-status badge-status-active">Aktif</span></td>
                                            <td style="text-align: right;">
                                                <button class="btn-icon-danger btn-delete-student" title="Keluarkan dari kelas">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="student-cell">
                                                    <div class="student-avatar" style="background:#EBF9F1; color:#1B8A5A;">DE</div>
                                                    <div class="student-meta">
                                                        <span class="student-name">Desta Arkan</span>
                                                        <span class="student-nim">10241020</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Sistem Informasi</td>
                                            <td><span class="card-subtitle-tag" style="padding:2px 8px; font-size:10px;">SI-A</span></td>
                                            <td><span class="badge-status badge-status-active">Aktif</span></td>
                                            <td style="text-align: right;">
                                                <button class="btn-icon-danger btn-delete-student" title="Keluarkan dari kelas">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="student-cell">
                                                    <div class="student-avatar" style="background:#F2EBF9; color:#8E44AD;">DV</div>
                                                    <div class="student-meta">
                                                        <span class="student-name">Devina Putri</span>
                                                        <span class="student-nim">10241022</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Sistem Informasi</td>
                                            <td><span class="card-subtitle-tag" style="padding:2px 8px; font-size:10px;">SI-A</span></td>
                                            <td><span class="badge-status badge-status-active">Aktif</span></td>
                                            <td style="text-align: right;">
                                                <button class="btn-icon-danger btn-delete-student" title="Keluarkan dari kelas">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ===================================================================
                 FITUR 2: DOSEN UNGGAH MATERI (PDF / PPTX / LINK)
                 =================================================================== -->
            <section class="feature-section" id="unggah-materi" data-section="materi">
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-header-left">
                            <div class="section-header-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                                    <path d="M12 12v9"></path>
                                    <path d="m16 16-4-4-4 4"></path>
                                </svg>
                            </div>
                            <div class="section-header-text">
                                <h2>2. Unggah Materi Kuliah (PDF / PPTX / Link)</h2>
                                <p>Publikasikan modul perkuliahan, presentasi kelas, atau materi referensi web ke mahasiswa.</p>
                            </div>
                        </div>
                        <span class="section-header-badge">Distribusi Modul</span>
                    </div>

                    <div class="two-col-grid">
                        <!-- Form Unggah Materi -->
                        <form id="formUploadMaterial" class="card-form">
                            <h4 class="card-form-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                Form Publikasi Materi Baru
                            </h4>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label for="materialCourse">Mata Kuliah</label>
                                    <select id="materialCourse" class="form-select">
                                        <option value="SI101">SI101 - Pemrograman Web</option>
                                        <option value="SI102">SI102 - Basis Data</option>
                                        <option value="SI103">SI103 - Analisis Sistem</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="materialSession">Pertemuan Ke-</label>
                                    <select id="materialSession" class="form-select">
                                        <option value="1">Pertemuan 1</option>
                                        <option value="2">Pertemuan 2</option>
                                        <option value="3">Pertemuan 3</option>
                                        <option value="4">Pertemuan 4</option>
                                        <option value="5">Pertemuan 5</option>
                                        <option value="6" selected>Pertemuan 6</option>
                                        <option value="7">Pertemuan 7</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="materialTitle">Judul Materi <span class="required">*</span></label>
                                <input type="text" id="materialTitle" class="form-control" placeholder="Contoh: Modul 06 - Autentikasi Multi-Role Laravel" required>
                            </div>

                            <!-- Pilihan Tipe Materi (PDF, PPTX, Link) -->
                            <div class="form-group">
                                <label>Pilih Format Materi <span class="required">*</span></label>
                                <div class="type-selector-group">
                                    <div class="type-pill active" data-type="pdf">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                        </svg>
                                        Dokumen PDF
                                    </div>
                                    <div class="type-pill" data-type="pptx">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                            <line x1="8" y1="21" x2="16" y2="21"></line>
                                            <line x1="12" y1="17" x2="12" y2="21"></line>
                                        </svg>
                                        Slide PPTX
                                    </div>
                                    <div class="type-pill" data-type="link">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                                        </svg>
                                        Tautan / Link
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic File Upload or URL Input -->
                            <div id="fileUploadContainer" class="form-group">
                                <label>Unggah Berkas (PDF / PPTX)</label>
                                <div class="file-dropzone" id="materialDropzone">
                                    <svg class="file-dropzone-icon" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    <span class="file-dropzone-text" id="dropzoneText">Klik untuk memilih file PDF atau seret berkas ke sini</span>
                                    <span class="file-dropzone-sub">Maksimal ukuran file: 50MB</span>
                                    <span class="file-selected-info" id="fileSelectedInfo"></span>
                                    <input type="file" id="materialFileInput" accept=".pdf" style="display: none;">
                                </div>
                            </div>

                            <div id="linkInputContainer" class="form-group" style="display: none;">
                                <label for="materialUrl">URL Tautan / Link Materi <span class="required">*</span></label>
                                <input type="url" id="materialUrl" class="form-control" placeholder="https://laravel.com/docs/11.x/authentication atau link video">
                            </div>

                            <div class="form-group">
                                <label for="materialDesc">Catatan Pembelajaran / Petunjuk</label>
                                <textarea id="materialDesc" class="form-textarea" rows="2" placeholder="Tulis ringkasan atau instruksi bagi mahasiswa..."></textarea>
                            </div>

                            <button type="submit" class="btn-primary-action">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                                    <path d="M12 12v9"></path>
                                    <path d="m16 16-4-4-4 4"></path>
                                </svg>
                                Unggah & Publikasikan
                            </button>
                        </form>

                        <!-- Grid Materi yang Terpublikasi -->
                        <div class="materials-published-wrap">
                            <div class="table-header-tools">
                                <span class="table-summary-info">Koleksi Materi Perkuliahan (<strong id="materialListCount">3</strong> Modul)</span>
                                <span class="card-subtitle-tag">Semester Genap 2026</span>
                            </div>

                            <div class="materials-grid" id="materialsGrid">
                                <!-- Card Materi 1: PDF -->
                                <div class="material-card">
                                    <div class="material-card-top">
                                        <span class="material-type-tag tag-pdf">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg>
                                            PDF &middot; 4.2 MB
                                        </span>
                                        <span class="card-subtitle-tag" style="padding:2px 7px; font-size:10px;">Pertemuan 1</span>
                                    </div>
                                    <h4 class="material-card-title">Pengantar Arsitektur Laravel & MVC Routing</h4>
                                    <p class="material-card-desc">Konsep dasar alur request lifecycle, route parameters, dan controller action pada Laravel 11.</p>
                                    <div class="material-card-footer">
                                        <span class="material-meta-date">Diunggah: 28 Feb 2026</span>
                                        <div class="material-card-actions">
                                            <a href="#" class="btn-open-resource" onclick="alert('Membuka pratinjau dokumen PDF...'); return false;">Unduh PDF</a>
                                            <button class="btn-icon-danger btn-delete-material" title="Hapus materi">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Materi 2: PPTX -->
                                <div class="material-card">
                                    <div class="material-card-top">
                                        <span class="material-type-tag tag-pptx">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="3" width="20" height="14" rx="2"></rect></svg>
                                            PPTX &middot; 8.7 MB
                                        </span>
                                        <span class="card-subtitle-tag" style="padding:2px 7px; font-size:10px;">Pertemuan 3</span>
                                    </div>
                                    <h4 class="material-card-title">Slide Presentasi Blade Templating & Components</h4>
                                    <p class="material-card-desc">Penggunaan reusable UI components, slot, inheritance layout, dan direktif custom blade.</p>
                                    <div class="material-card-footer">
                                        <span class="material-meta-date">Diunggah: 04 Mar 2026</span>
                                        <div class="material-card-actions">
                                            <a href="#" class="btn-open-resource" onclick="alert('Mengunduh slide presentasi PPTX...'); return false;">Unduh Slide</a>
                                            <button class="btn-icon-danger btn-delete-material" title="Hapus materi">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Materi 3: LINK -->
                                <div class="material-card">
                                    <div class="material-card-top">
                                        <span class="material-type-tag tag-link">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path></svg>
                                            LINK &middot; Referensi
                                        </span>
                                        <span class="card-subtitle-tag" style="padding:2px 7px; font-size:10px;">Pertemuan 5</span>
                                    </div>
                                    <h4 class="material-card-title">Dokumentasi Otorisasi & Role-Based Access Control</h4>
                                    <p class="material-card-desc">Panduan resmi Laravel Documentation mengenai Middleware, Guards, dan Session Management.</p>
                                    <div class="material-card-footer">
                                        <span class="material-meta-date">Diunggah: 10 Mar 2026</span>
                                        <div class="material-card-actions">
                                            <a href="https://laravel.com/docs/11.x/authorization" target="_blank" class="btn-open-resource">Kunjungi Link</a>
                                            <button class="btn-icon-danger btn-delete-material" title="Hapus materi">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ===================================================================
                 FITUR 3: DOSEN MEMBUAT TUGAS DENGAN DEADLINE
                 =================================================================== -->
            <section class="feature-section" id="buat-tugas" data-section="tugas">
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-header-left">
                            <div class="section-header-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <div class="section-header-text">
                                <h2>3. Pembuatan Tugas & Manajemen Deadline</h2>
                                <p>Tentukan tugas baru, instruksi pengerjaan, bobot penilaian, serta batas waktu akhir pengumpulan.</p>
                            </div>
                        </div>
                        <span class="section-header-badge">Penugasan & Waktu</span>
                    </div>

                    <div class="two-col-grid">
                        <!-- Form Buat Tugas -->
                        <form id="formCreateAssignment" class="card-form">
                            <h4 class="card-form-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                                Buat Penugasan Baru
                            </h4>

                            <div class="form-group">
                                <label for="taskCourse">Mata Kuliah Target</label>
                                <select id="taskCourse" class="form-select">
                                    <option value="SI101">SI101 - Pemrograman Web</option>
                                    <option value="SI102">SI102 - Basis Data</option>
                                    <option value="SI103">SI103 - Analisis & Perancangan Sistem</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="taskTitle">Judul Tugas <span class="required">*</span></label>
                                <input type="text" id="taskTitle" class="form-control" placeholder="Contoh: Tugas 03 - Implementasi Blade & Controller" required>
                            </div>

                            <div class="form-group">
                                <label for="taskDesc">Instruksi / Petunjuk Pengerjaan <span class="required">*</span></label>
                                <textarea id="taskDesc" class="form-textarea" rows="3" placeholder="Jelaskan kebutuhan tugas, format file yang dikumpulkan, dan kriteria penilaian..." required></textarea>
                            </div>

                            <div class="form-group">
                                <label for="taskType">Tipe Tugas <span class="required">*</span></label>
                                <select id="taskType" class="form-select" required>
                                    <option value="Tugas Kelompok">Tugas Kelompok</option>
                                    <option value="Tugas Individu">Tugas Individu</option>
                                </select>
                            </div>

                            <!-- Input Tanggal & Jam Deadline -->
                            <div class="form-row-2">
                                <div class="form-group">
                                    <label for="taskDeadlineDate">Batas Tanggal (Deadline) <span class="required">*</span></label>
                                    <input type="date" id="taskDeadlineDate" class="form-control" required value="2026-09-20">
                                </div>
                                <div class="form-group">
                                    <label for="taskDeadlineTime">Batas Jam <span class="required">*</span></label>
                                    <input type="time" id="taskDeadlineTime" class="form-control" required value="23:59">
                                </div>
                            </div>

                            <button type="submit" class="btn-primary-action">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Terbitkan Tugas
                            </button>
                        </form>

                        <!-- Daftar Tugas Berjalan & Status Deadline -->
                        <div class="assignments-list-wrap">
                            <div class="table-header-tools">
                                <span class="table-summary-info">Daftar Tugas Aktif (<strong id="assignmentListCount">2</strong> Tugas Berjalan)</span>
                                <span class="card-subtitle-tag" style="background:#FFEBEF; color:#B0182D; border-color:#FFD2DC;">Monitoring Deadline</span>
                            </div>

                            <div class="assignments-grid" id="assignmentsGrid">
                                <!-- Tugas 1 -->
                                <div class="assignment-card">
                                    <div class="assignment-card-header">
                                        <h4 class="assignment-card-title">Tugas 02: Desain Schema Database & Migrations</h4>
                                        <span class="card-subtitle-tag" style="padding:2px 8px; font-size:10px;">Tugas Kelompok</span>
                                    </div>
                                    <p style="font-size: 11.5px; color: #8E6570;">Membuat migration schema tabel pengguna, mata kuliah, dan relasi enrollment pada MariaDB.</p>
                                    
                                    <div class="assignment-deadline-box">
                                        <svg class="deadline-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        <div class="deadline-info">
                                            <span class="deadline-label">Batas Waktu Pengumpulan</span>
                                            <span class="deadline-value">15 Sep 2026 &middot; 23:59 WITA</span>
                                        </div>
                                    </div>

                                    <div class="submission-progress-wrap">
                                        <div class="submission-progress-labels">
                                            <span>Progress Pengumpulan</span>
                                            <span><strong>34</strong> dari 38 Mahasiswa</span>
                                        </div>
                                        <div class="submission-progress-track">
                                            <div class="submission-progress-fill" style="width: 89%;"></div>
                                        </div>
                                    </div>

                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                                        <span class="badge-status badge-status-warning">Mendekati Deadline</span>
                                        <a href="#penilaian-tugas" class="btn-secondary-action" style="font-size: 11px;">Periksa Jawaban &rarr;</a>
                                    </div>
                                </div>

                                <!-- Tugas 2 -->
                                <div class="assignment-card">
                                    <div class="assignment-card-header">
                                        <h4 class="assignment-card-title">Tugas 01: Setup Laravel 11 & Lingkungan Kerja</h4>
                                        <span class="card-subtitle-tag" style="padding:2px 8px; font-size:10px;">Tugas Individu</span>
                                    </div>
                                    <p style="font-size: 11.5px; color: #8E6570;">Instalasi project Laravel, konfigurasi database .env, dan penataan Git repository.</p>
                                    
                                    <div class="assignment-deadline-box">
                                        <svg class="deadline-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        <div class="deadline-info">
                                            <span class="deadline-label">Batas Waktu Pengumpulan</span>
                                            <span class="deadline-value">05 Sep 2026 &middot; 23:59 WITA (Selesai)</span>
                                        </div>
                                    </div>

                                    <div class="submission-progress-wrap">
                                        <div class="submission-progress-labels">
                                            <span>Progress Pengumpulan</span>
                                            <span><strong>38</strong> dari 38 Mahasiswa (100%)</span>
                                        </div>
                                        <div class="submission-progress-track">
                                            <div class="submission-progress-fill" style="width: 100%; background: #1B8A5A;"></div>
                                        </div>
                                    </div>

                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                                        <span class="badge-status badge-status-active">Tuntas Dinilai</span>
                                        <a href="#penilaian-tugas" class="btn-secondary-action" style="font-size: 11px;">Lihat Rekap &rarr;</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ===================================================================
                 FITUR 4: DOSEN MEMBERI NILAI + FEEDBACK PADA PENGUMPULAN
                 =================================================================== -->
            <section class="feature-section" id="penilaian-tugas" data-section="penilaian">
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-header-left">
                            <div class="section-header-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                            <div class="section-header-text">
                                <h2>4. Penilaian & Umpan Balik (Feedback) Pengumpulan Mahasiswa</h2>
                                <p>Evaluasi kiriman mahasiswa, berikan skor angka (0-100), dan cantumkan catatan konstruktif.</p>
                            </div>
                        </div>
                        <span class="section-header-badge">Evaluasi & Grading</span>
                    </div>

                    <!-- Filter & Summary Bar -->
                    <div class="table-header-tools">
                        <div style="display: flex; gap: 8px;">
                            <button class="btn-secondary-action filter-submission-btn active" data-sub-filter="all">Semua Pengumpulan</button>
                            <button class="btn-secondary-action filter-submission-btn" data-sub-filter="pending">Belum Dinilai</button>
                            <button class="btn-secondary-action filter-submission-btn" data-sub-filter="graded">Sudah Dinilai</button>
                        </div>

                        <div style="font-size: 12px; font-weight: 800; color: #8E6570;">
                            Status: <span style="color:#1B8A5A;">2 Dinilai</span> &middot; <span style="color:#C98A1F;" id="ungradedSummary">3 Menunggu Review</span>
                        </div>
                    </div>

                    <!-- Tabel Pengumpulan & Penilaian -->
                    <div class="table-responsive">
                        <table class="custom-dosen-table" id="submissionTable">
                            <thead>
                                <tr>
                                    <th>Mahasiswa</th>
                                    <th>Tugas yang Dikumpulkan</th>
                                    <th>Waktu Pengumpulan</th>
                                    <th>Berkas Tugas</th>
                                    <th>Nilai</th>
                                    <th>Feedback Dosen</th>
                                    <th style="text-align: right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="submissionTableBody">
                                <!-- Submission 1: Baihaqi Abimanyu (Sudah Dinilai) -->
                                <tr data-status="graded" data-id="1">
                                    <td>
                                        <div class="student-cell">
                                            <div class="student-avatar" style="background:#FFE2E8; color:#B0182D;">BK</div>
                                            <div class="student-meta">
                                                <span class="student-name">Baihaqi Abimanyu</span>
                                                <span class="student-nim">10241014 &middot; SI-A</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><strong>Tugas 02: Desain Schema Database</strong></td>
                                    <td><span class="badge-status badge-status-active">Tepat Waktu (13 Sep, 14:20)</span></td>
                                    <td>
                                        <a href="#" onclick="alert('Membuka lampiran berkas: schema_db_baihaqi.pdf'); return false;" class="btn-open-resource" style="font-size:11px;">
                                            📄 schema_db.pdf
                                        </a>
                                    </td>
                                    <td>
                                        <span class="score-badge score-badge-graded item-score">92 / 100</span>
                                    </td>
                                    <td>
                                        <div class="feedback-snippet item-feedback" title="Struktur normalisasi data sudah 3NF dengan sangat rapi, dokumentasi relasi foreign key lengkap.">
                                            "Struktur normalisasi data sudah 3NF dengan sangat rapi, dokumentasi..."
                                        </div>
                                    </td>
                                    <td style="text-align: right;">
                                        <button class="btn-grade-action" onclick="openGradingModal(1, 'Baihaqi Abimanyu (10241014)', 'Tugas 02: Desain Schema Database', 92, 'Struktur normalisasi data sudah 3NF dengan sangat rapi, dokumentasi relasi foreign key lengkap.')">
                                            Edit Nilai
                                        </button>
                                    </td>
                                </tr>

                                <!-- Submission 2: Calvin Adithya (Sudah Dinilai) -->
                                <tr data-status="graded" data-id="2">
                                    <td>
                                        <div class="student-cell">
                                            <div class="student-avatar" style="background:#FFF0DE; color:#C98A1F;">CA</div>
                                            <div class="student-meta">
                                                <span class="student-name">Calvin Adithya</span>
                                                <span class="student-nim">10241016 &middot; SI-A</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><strong>Tugas 02: Desain Schema Database</strong></td>
                                    <td><span class="badge-status badge-status-active">Tepat Waktu (13 Sep, 16:05)</span></td>
                                    <td>
                                        <a href="#" onclick="alert('Membuka lampiran berkas: database_erd_calvin.pdf'); return false;" class="btn-open-resource" style="font-size:11px;">
                                            📄 erd_design.pdf
                                        </a>
                                    </td>
                                    <td>
                                        <span class="score-badge score-badge-graded item-score">88 / 100</span>
                                    </td>
                                    <td>
                                        <div class="feedback-snippet item-feedback" title="Visualisasi diagram ERD sangat jelas, tambahkan index komposit pada tabel transaksi.">
                                            "Visualisasi diagram ERD sangat jelas, tambahkan index komposit..."
                                        </div>
                                    </td>
                                    <td style="text-align: right;">
                                        <button class="btn-grade-action" onclick="openGradingModal(2, 'Calvin Adithya (10241016)', 'Tugas 02: Desain Schema Database', 88, 'Visualisasi diagram ERD sangat jelas, tambahkan index komposit pada tabel transaksi.')">
                                            Edit Nilai
                                        </button>
                                    </td>
                                </tr>

                                <!-- Submission 3: Clara Shinta (Belum Dinilai) -->
                                <tr data-status="pending" data-id="3">
                                    <td>
                                        <div class="student-cell">
                                            <div class="student-avatar" style="background:#EBF3FF; color:#1971C2;">CL</div>
                                            <div class="student-meta">
                                                <span class="student-name">Clara Shinta</span>
                                                <span class="student-nim">10241018 &middot; SI-A</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><strong>Tugas 02: Desain Schema Database</strong></td>
                                    <td><span class="badge-status badge-status-active">Tepat Waktu (13 Sep, 17:45)</span></td>
                                    <td>
                                        <a href="#" onclick="alert('Membuka lampiran berkas: migrasi_tabel_clara.zip'); return false;" class="btn-open-resource" style="font-size:11px;">
                                            📦 migrasi_tabel.zip
                                        </a>
                                    </td>
                                    <td>
                                        <span class="score-badge score-badge-ungraded item-score">Belum Dinilai</span>
                                    </td>
                                    <td>
                                        <div class="feedback-snippet item-feedback" style="color: #8E6570;" title="Menunggu evaluasi dosen">
                                            <em>Belum ada catatan feedback</em>
                                        </div>
                                    </td>
                                    <td style="text-align: right;">
                                        <button class="btn-grade-action" style="background:#E23C64;" onclick="openGradingModal(3, 'Clara Shinta (10241018)', 'Tugas 02: Desain Schema Database', '', '')">
                                            Beri Nilai &rarr;
                                        </button>
                                    </td>
                                </tr>

                                <!-- Submission 4: Desta Arkan (Belum Dinilai) -->
                                <tr data-status="pending" data-id="4">
                                    <td>
                                        <div class="student-cell">
                                            <div class="student-avatar" style="background:#EBF9F1; color:#1B8A5A;">DE</div>
                                            <div class="student-meta">
                                                <span class="student-name">Desta Arkan</span>
                                                <span class="student-nim">10241020 &middot; SI-A</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><strong>Tugas 02: Desain Schema Database</strong></td>
                                    <td><span class="badge-status badge-status-warning">Terlambat 1 Jam (14 Sep, 00:45)</span></td>
                                    <td>
                                        <a href="#" onclick="alert('Membuka lampiran berkas: database_desta.sql'); return false;" class="btn-open-resource" style="font-size:11px;">
                                            📄 schema_desta.sql
                                        </a>
                                    </td>
                                    <td>
                                        <span class="score-badge score-badge-ungraded item-score">Belum Dinilai</span>
                                    </td>
                                    <td>
                                        <div class="feedback-snippet item-feedback" style="color: #8E6570;" title="Menunggu evaluasi dosen">
                                            <em>Belum ada catatan feedback</em>
                                        </div>
                                    </td>
                                    <td style="text-align: right;">
                                        <button class="btn-grade-action" style="background:#E23C64;" onclick="openGradingModal(4, 'Desta Arkan (10241020)', 'Tugas 02: Desain Schema Database', '', '')">
                                            Beri Nilai &rarr;
                                        </button>
                                    </td>
                                </tr>

                                <!-- Submission 5: Devina Putri (Belum Dinilai) -->
                                <tr data-status="pending" data-id="5">
                                    <td>
                                        <div class="student-cell">
                                            <div class="student-avatar" style="background:#F2EBF9; color:#8E44AD;">DV</div>
                                            <div class="student-meta">
                                                <span class="student-name">Devina Putri</span>
                                                <span class="student-nim">10241022 &middot; SI-A</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><strong>Tugas 02: Desain Schema Database</strong></td>
                                    <td><span class="badge-status badge-status-active">Tepat Waktu (13 Sep, 18:30)</span></td>
                                    <td>
                                        <a href="#" onclick="alert('Membuka repository GitHub mahasiswa: https://github.com/devina/lms-db'); return false;" class="btn-open-resource" style="font-size:11px;">
                                            🔗 github.com/devina
                                        </a>
                                    </td>
                                    <td>
                                        <span class="score-badge score-badge-ungraded item-score">Belum Dinilai</span>
                                    </td>
                                    <td>
                                        <div class="feedback-snippet item-feedback" style="color: #8E6570;" title="Menunggu evaluasi dosen">
                                            <em>Belum ada catatan feedback</em>
                                        </div>
                                    </td>
                                    <td style="text-align: right;">
                                        <button class="btn-grade-action" style="background:#E23C64;" onclick="openGradingModal(5, 'Devina Putri (10241022)', 'Tugas 02: Desain Schema Database', '', '')">
                                            Beri Nilai &rarr;
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

        </main>

        <!-- 3. Footer Global (mengikuti footer.blade.php) -->
        <x-footer />
    </div>

    <!-- ===================================================================
         MODAL INTERAKTIF: PENILAIAN & FEEDBACK DOSEN
         =================================================================== -->
    <div class="dosen-modal-overlay" id="gradingModalOverlay">
        <div class="dosen-modal-card">
            <div class="dosen-modal-header">
                <h3>Form Penilaian & Umpan Balik Dosen</h3>
                <button type="button" class="btn-close-modal" id="btnCloseGradingModal">&times;</button>
            </div>
            <div class="dosen-modal-body">
                <div class="submission-info-box">
                    <span class="submission-info-title" id="modalAssignmentTitle">Tugas 02: Desain Schema Database</span>
                    <span class="submission-info-student" id="modalStudentName">Mahasiswa: Clara Shinta (10241018)</span>
                </div>

                <div class="form-group">
                    <label for="inputScore">Nilai Akhir (Skala 0 - 100) <span class="required">*</span></label>
                    <input type="number" id="inputScore" class="form-control" placeholder="Masukkan nilai 0-100..." min="0" max="100" required>
                    <div class="score-quick-buttons">
                        <span style="font-size: 11px; color: #8E6570; align-self: center;">Pintasan Nilai:</span>
                        <button type="button" class="btn-score-quick" onclick="document.getElementById('inputScore').value = 95">95 (A)</button>
                        <button type="button" class="btn-score-quick" onclick="document.getElementById('inputScore').value = 88">88 (A-)</button>
                        <button type="button" class="btn-score-quick" onclick="document.getElementById('inputScore').value = 82">82 (B+)</button>
                        <button type="button" class="btn-score-quick" onclick="document.getElementById('inputScore').value = 75">75 (B)</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputFeedback">Catatan & Umpan Balik (Feedback) Konstruktif <span class="required">*</span></label>
                    <textarea id="inputFeedback" class="form-textarea" rows="4" placeholder="Tuliskan ulasan spesifik mengenai pengumpulan ini, kelebihan kode, serta aspek yang perlu ditingkatkan oleh mahasiswa..."></textarea>
                </div>
            </div>
            <div class="dosen-modal-footer">
                <button type="button" class="btn-secondary-action" id="btnCancelGrading">Batal</button>
                <button type="button" class="btn-primary-action" id="btnSaveGrading">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Simpan Nilai & Berikan Feedback
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div class="dosen-toast-container" id="toastContainer"></div>

    <!-- ===================================================================
         INTERACTIVE JAVASCRIPT: FITUR-FITUR DOSEN
         =================================================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // Toast Helper
            function showToast(message, isSuccess = true) {
                const container = document.getElementById('toastContainer');
                const toast = document.createElement('div');
                toast.className = 'dosen-toast';
                if (!isSuccess) toast.style.borderLeftColor = '#E23C64';

                toast.innerHTML = `
                    <svg class="toast-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="${isSuccess ? '#1B8A5A' : '#E23C64'}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>${message}</span>
                `;
                container.appendChild(toast);

                setTimeout(() => toast.classList.add('show'), 50);

                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 350);
                }, 3500);
            }

            // -------------------------------------------------------------
            // Feature Jump Navigation & Navbar Links
            // -------------------------------------------------------------
            const navLinks = document.querySelectorAll('.nav-link-dosen');
            navLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    const targetId = link.getAttribute('data-target');
                    const targetElem = document.getElementById(targetId);
                    if (targetElem) {
                        e.preventDefault();
                        targetElem.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        document.querySelectorAll('.navbar-nav.dosen-nav li').forEach(li => li.classList.remove('active'));
                        link.parentElement.classList.add('active');
                    }
                });
            });

            const filterTabs = document.querySelectorAll('.tab-btn');
            const sections = document.querySelectorAll('.feature-section');

            filterTabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    filterTabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');

                    const filter = tab.getAttribute('data-filter');
                    sections.forEach(sec => {
                        if (filter === 'all' || sec.getAttribute('data-section') === filter) {
                            sec.style.display = 'flex';
                        } else {
                            sec.style.display = 'none';
                        }
                    });
                });
            });

            // -------------------------------------------------------------
            // 1. FITUR PENDAFTARAN MAHASISWA
            // -------------------------------------------------------------
            const formAddStudent = document.getElementById('formAddStudent');
            const studentTableBody = document.getElementById('studentTableBody');
            const studentTableCount = document.getElementById('studentTableCount');
            const statStudentCount = document.getElementById('statStudentCount');

            function updateStudentCounts() {
                const count = studentTableBody.querySelectorAll('tr').length;
                if (studentTableCount) studentTableCount.textContent = count;
                if (statStudentCount) statStudentCount.textContent = 33 + count; // realistic class size
            }

            formAddStudent.addEventListener('submit', (e) => {
                e.preventDefault();
                const nim = document.getElementById('mhsNim').value.trim();
                const nama = document.getElementById('mhsNama').value.trim();
                const kelas = document.getElementById('mhsKelas').value;
                const prodi = document.getElementById('mhsProdi').value;
                const matkul = document.getElementById('mhsMatkul').value;

                if (!nim || !nama) return;

                // Create initials for avatar
                const initials = nama.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() || 'MH';

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <div class="student-cell">
                            <div class="student-avatar" style="background:#FFF0ED; color:#B0182D;">${initials}</div>
                            <div class="student-meta">
                                <span class="student-name">${nama}</span>
                                <span class="student-nim">${nim}</span>
                            </div>
                        </div>
                    </td>
                    <td>${prodi}</td>
                    <td><span class="card-subtitle-tag" style="padding:2px 8px; font-size:10px;">${kelas}</span></td>
                    <td><span class="badge-status badge-status-active">Aktif</span></td>
                    <td style="text-align: right;">
                        <button class="btn-icon-danger btn-delete-student" title="Keluarkan dari kelas">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </td>
                `;

                studentTableBody.prepend(tr);
                formAddStudent.reset();
                updateStudentCounts();
                showToast(`Mahasiswa ${nama} (${nim}) berhasil didaftarkan ke ${matkul}!`);
            });

            // Delegate student deletion
            studentTableBody.addEventListener('click', (e) => {
                const btn = e.target.closest('.btn-delete-student');
                if (btn) {
                    const row = btn.closest('tr');
                    const studentName = row.querySelector('.student-name').textContent;
                    if (confirm(`Apakah Anda yakin ingin membatalkan pendaftaran mahasiswa ${studentName}?`)) {
                        row.remove();
                        updateStudentCounts();
                        showToast(`Pendaftaran mahasiswa ${studentName} telah dihapus.`, false);
                    }
                }
            });

            // Student Search
            const searchStudentInput = document.getElementById('searchStudentInput');
            if (searchStudentInput) {
                searchStudentInput.addEventListener('input', (e) => {
                    const term = e.target.value.toLowerCase();
                    const rows = studentTableBody.querySelectorAll('tr');
                    rows.forEach(r => {
                        const text = r.textContent.toLowerCase();
                        r.style.display = text.includes(term) ? '' : 'none';
                    });
                });
            }

            // -------------------------------------------------------------
            // 2. FITUR UNGGAH MATERI (PDF / PPTX / LINK)
            // -------------------------------------------------------------
            const typePills = document.querySelectorAll('.type-pill');
            const fileUploadContainer = document.getElementById('fileUploadContainer');
            const linkInputContainer = document.getElementById('linkInputContainer');
            const materialDropzone = document.getElementById('materialDropzone');
            const materialFileInput = document.getElementById('materialFileInput');
            const dropzoneText = document.getElementById('dropzoneText');
            const fileSelectedInfo = document.getElementById('fileSelectedInfo');
            const formUploadMaterial = document.getElementById('formUploadMaterial');
            const materialsGrid = document.getElementById('materialsGrid');
            const materialListCount = document.getElementById('materialListCount');
            const statMaterialCount = document.getElementById('statMaterialCount');

            let currentMaterialType = 'pdf';

            typePills.forEach(pill => {
                pill.addEventListener('click', () => {
                    typePills.forEach(p => p.classList.remove('active'));
                    pill.classList.add('active');
                    currentMaterialType = pill.getAttribute('data-type');

                    if (currentMaterialType === 'link') {
                        fileUploadContainer.style.display = 'none';
                        linkInputContainer.style.display = 'flex';
                    } else {
                        fileUploadContainer.style.display = 'flex';
                        linkInputContainer.style.display = 'none';

                        if (currentMaterialType === 'pdf') {
                            materialFileInput.setAttribute('accept', '.pdf');
                            dropzoneText.textContent = 'Klik untuk memilih file PDF atau seret berkas ke sini';
                        } else {
                            materialFileInput.setAttribute('accept', '.pptx,.ppt');
                            dropzoneText.textContent = 'Klik untuk memilih file presentasi PPTX atau seret ke sini';
                        }
                    }
                });
            });

            materialDropzone.addEventListener('click', () => materialFileInput.click());

            materialFileInput.addEventListener('change', () => {
                if (materialFileInput.files.length > 0) {
                    const file = materialFileInput.files[0];
                    fileSelectedInfo.style.display = 'inline-block';
                    fileSelectedInfo.textContent = `✓ Terpilih: ${file.name} (${(file.size / (1024 * 1024)).toFixed(2)} MB)`;
                }
            });

            formUploadMaterial.addEventListener('submit', (e) => {
                e.preventDefault();
                const session = document.getElementById('materialSession').value;
                const title = document.getElementById('materialTitle').value.trim();
                const desc = document.getElementById('materialDesc').value.trim() || 'Materi perkuliahan untuk dipelajari oleh seluruh mahasiswa.';
                const url = document.getElementById('materialUrl').value.trim();

                if (!title) return;

                let tagClass = 'tag-pdf';
                let tagLabel = 'PDF &middot; Berkas';
                let actionBtnText = 'Unduh PDF';
                let actionHref = '#';

                if (currentMaterialType === 'pptx') {
                    tagClass = 'tag-pptx';
                    tagLabel = 'PPTX &middot; Slide';
                    actionBtnText = 'Unduh Slide';
                } else if (currentMaterialType === 'link') {
                    tagClass = 'tag-link';
                    tagLabel = 'LINK &middot; Tautan Web';
                    actionBtnText = 'Buka Tautan';
                    actionHref = url || 'https://laravel.com';
                }

                const card = document.createElement('div');
                card.className = 'material-card';
                card.innerHTML = `
                    <div class="material-card-top">
                        <span class="material-type-tag ${tagClass}">
                            ${tagLabel}
                        </span>
                        <span class="card-subtitle-tag" style="padding:2px 7px; font-size:10px;">Pertemuan ${session}</span>
                    </div>
                    <h4 class="material-card-title">${title}</h4>
                    <p class="material-card-desc">${desc}</p>
                    <div class="material-card-footer">
                        <span class="material-meta-date">Diunggah: Baru saja</span>
                        <div class="material-card-actions">
                            <a href="${actionHref}" target="${currentMaterialType === 'link' ? '_blank' : '_self'}" class="btn-open-resource" onclick="${currentMaterialType === 'link' ? '' : 'alert(\'Mengunduh berkas materi...\'); return false;'}">
                                ${actionBtnText}
                            </a>
                            <button class="btn-icon-danger btn-delete-material" title="Hapus materi">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                            </button>
                        </div>
                    </div>
                `;

                materialsGrid.prepend(card);
                formUploadMaterial.reset();
                fileSelectedInfo.style.display = 'none';

                const total = materialsGrid.querySelectorAll('.material-card').length;
                if (materialListCount) materialListCount.textContent = total;
                if (statMaterialCount) statMaterialCount.textContent = total;

                showToast(`Materi "${title}" berhasil diunggah & dipublikasikan!`);
            });

            // Delegate Material Deletion
            materialsGrid.addEventListener('click', (e) => {
                const btn = e.target.closest('.btn-delete-material');
                if (btn) {
                    const card = btn.closest('.material-card');
                    const title = card.querySelector('.material-card-title').textContent;
                    if (confirm(`Apakah Anda yakin ingin menghapus materi "${title}"?`)) {
                        card.remove();
                        const total = materialsGrid.querySelectorAll('.material-card').length;
                        if (materialListCount) materialListCount.textContent = total;
                        if (statMaterialCount) statMaterialCount.textContent = total;
                        showToast(`Materi "${title}" telah dihapus.`, false);
                    }
                }
            });

            // -------------------------------------------------------------
            // 3. FITUR PEMBUATAN TUGAS DENGAN DEADLINE
            // -------------------------------------------------------------
            const formCreateAssignment = document.getElementById('formCreateAssignment');
            const assignmentsGrid = document.getElementById('assignmentsGrid');
            const assignmentListCount = document.getElementById('assignmentListCount');
            const statAssignmentCount = document.getElementById('statAssignmentCount');

            formCreateAssignment.addEventListener('submit', (e) => {
                e.preventDefault();
                const title = document.getElementById('taskTitle').value.trim();
                const desc = document.getElementById('taskDesc').value.trim();
                const taskType = document.getElementById('taskType').value || 'Tugas Kelompok';
                const deadlineDate = document.getElementById('taskDeadlineDate').value;
                const deadlineTime = document.getElementById('taskDeadlineTime').value;

                if (!title || !desc || !deadlineDate) return;

                const card = document.createElement('div');
                card.className = 'assignment-card';
                card.innerHTML = `
                    <div class="assignment-card-header">
                        <h4 class="assignment-card-title">${title}</h4>
                        <span class="card-subtitle-tag" style="padding:2px 8px; font-size:10px;">${taskType}</span>
                    </div>
                    <p style="font-size: 11.5px; color: #8E6570;">${desc}</p>
                    
                    <div class="assignment-deadline-box">
                        <svg class="deadline-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <div class="deadline-info">
                            <span class="deadline-label">Batas Waktu Pengumpulan</span>
                            <span class="deadline-value">${deadlineDate} &middot; ${deadlineTime} WITA</span>
                        </div>
                    </div>

                    <div class="submission-progress-wrap">
                        <div class="submission-progress-labels">
                            <span>Progress Pengumpulan</span>
                            <span><strong>0</strong> dari 38 Mahasiswa</span>
                        </div>
                        <div class="submission-progress-track">
                            <div class="submission-progress-fill" style="width: 0%;"></div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                        <span class="badge-status badge-status-active">Tugas Aktif</span>
                        <a href="#penilaian-tugas" class="btn-secondary-action" style="font-size: 11px;">Periksa Jawaban &rarr;</a>
                    </div>
                `;

                assignmentsGrid.prepend(card);
                formCreateAssignment.reset();

                const total = assignmentsGrid.querySelectorAll('.assignment-card').length;
                if (assignmentListCount) assignmentListCount.textContent = total;
                if (statAssignmentCount) statAssignmentCount.textContent = total;

                showToast(`Tugas "${title}" berhasil dibuat dengan deadline ${deadlineDate}!`);
            });

            // -------------------------------------------------------------
            // 4. FITUR PENILAIAN & FEEDBACK
            // -------------------------------------------------------------
            const filterSubmissionBtns = document.querySelectorAll('.filter-submission-btn');
            const submissionRows = document.querySelectorAll('#submissionTableBody tr');

            filterSubmissionBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    filterSubmissionBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');

                    const filter = btn.getAttribute('data-sub-filter');
                    submissionRows.forEach(row => {
                        const status = row.getAttribute('data-status');
                        if (filter === 'all' || status === filter) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            });

            // Modal Logic
            const modalOverlay = document.getElementById('gradingModalOverlay');
            const btnCloseModal = document.getElementById('btnCloseGradingModal');
            const btnCancelGrading = document.getElementById('btnCancelGrading');
            const btnSaveGrading = document.getElementById('btnSaveGrading');

            const modalStudentName = document.getElementById('modalStudentName');
            const modalAssignmentTitle = document.getElementById('modalAssignmentTitle');
            const inputScore = document.getElementById('inputScore');
            const inputFeedback = document.getElementById('inputFeedback');

            let activeSubmissionId = null;

            window.openGradingModal = function(id, student, assignment, currentScore, currentFeedback) {
                activeSubmissionId = id;
                modalStudentName.textContent = `Mahasiswa: ${student}`;
                modalAssignmentTitle.textContent = assignment;
                inputScore.value = currentScore || '';
                inputFeedback.value = currentFeedback || '';
                modalOverlay.classList.add('active');
            };

            function closeModal() {
                modalOverlay.classList.remove('active');
                activeSubmissionId = null;
            }

            if (btnCloseModal) btnCloseModal.addEventListener('click', closeModal);
            if (btnCancelGrading) btnCancelGrading.addEventListener('click', closeModal);

            modalOverlay.addEventListener('click', (e) => {
                if (e.target === modalOverlay) closeModal();
            });

            btnSaveGrading.addEventListener('click', () => {
                const score = parseInt(inputScore.value);
                const feedback = inputFeedback.value.trim();

                if (isNaN(score) || score < 0 || score > 100) {
                    alert('Harap masukkan nilai valid antara 0 hingga 100!');
                    return;
                }

                if (!feedback) {
                    alert('Harap masukkan feedback/ulasan konstruktif bagi mahasiswa!');
                    return;
                }

                // Update Row in Table
                const targetRow = document.querySelector(`#submissionTableBody tr[data-id="${activeSubmissionId}"]`);
                if (targetRow) {
                    targetRow.setAttribute('data-status', 'graded');

                    const scoreElem = targetRow.querySelector('.item-score');
                    scoreElem.className = 'score-badge score-badge-graded item-score';
                    scoreElem.textContent = `${score} / 100`;

                    const feedbackElem = targetRow.querySelector('.item-feedback');
                    feedbackElem.style.color = '#5F3540';
                    feedbackElem.title = feedback;
                    feedbackElem.textContent = `"${feedback.length > 55 ? feedback.substring(0, 55) + '...' : feedback}"`;

                    const actionBtn = targetRow.querySelector('.btn-grade-action');
                    actionBtn.style.background = '#B0182D';
                    actionBtn.textContent = 'Edit Nilai';
                }

                // Update pending count stat
                const pendingCount = document.querySelectorAll('#submissionTableBody tr[data-status="pending"]').length;
                const statPendingGrading = document.getElementById('statPendingGrading');
                const ungradedSummary = document.getElementById('ungradedSummary');
                if (statPendingGrading) statPendingGrading.textContent = pendingCount;
                if (ungradedSummary) ungradedSummary.textContent = `${pendingCount} Menunggu Review`;

                closeModal();
                showToast(`Nilai (${score}) & Feedback berhasil disimpan untuk ${modalStudentName.textContent}!`);
            });

        });
    </script>
</body>

</html>
