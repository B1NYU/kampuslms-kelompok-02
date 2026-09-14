<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Tugas — Portal Dosen KampusLMS</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/dosen/dosen.dashboard.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/dosen/dosen.dashboard.css') }}">
    @endif
</head>

<body>

    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <div class="app-window">

        <!-- Navbar Khusus Dosen -->
        <x-navbar-dosen />

        <!-- Konten Utama Buat Tugas -->
        <main class="dosen-content">

            <!-- Topbar Header -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <span class="page-eyebrow">
                            <span class="page-eyebrow-dot"></span>
                            PORTAL DOSEN • PENUGASAN &amp; MONITORING
                        </span>
                        <h1>3. Pembuatan Tugas &amp; Manajemen Deadline</h1>
                    </div>
                </div>

                <div class="course-filter-bar">
                    <span class="course-filter-label">Mata Kuliah Aktif:</span>
                    <select id="selectCurrentCourse" class="course-select">
                        <option value="SI101" selected>SI101 &bull; Pemrograman Web (3 SKS)</option>
                        <option value="SI102">SI102 &bull; Basis Data Lanjut (3 SKS)</option>
                        <option value="SI103">SI103 &bull; Analisis &amp; Desain SI (4 SKS)</option>
                    </select>
                </div>
            </header>

            <!-- Section: Buat Tugas -->
            <section class="feature-section" id="buat-tugas">
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
                                <h2>Pembuatan Tugas &amp; Manajemen Deadline</h2>
                                <p>Tentukan tugas baru, instruksi pengerjaan, bobot penilaian, serta batas waktu akhir pengumpulan.</p>
                            </div>
                        </div>
                        <span class="section-header-badge">Penugasan &amp; Waktu</span>
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
                                    <option value="SI103">SI103 - Analisis &amp; Perancangan Sistem</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="taskTitle">Judul Tugas <span class="required">*</span></label>
                                <input type="text" id="taskTitle" class="form-control" placeholder="Contoh: Tugas 03 - Implementasi Blade &amp; Controller" required>
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
                                        <h4 class="assignment-card-title">Tugas 02: Desain Schema Database &amp; Migrations</h4>
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
                                        <a href="{{ route('dosen.penilaian') }}" class="btn-secondary-action" style="font-size: 11px;">Periksa Jawaban &rarr;</a>
                                    </div>
                                </div>

                                <!-- Tugas 2 -->
                                <div class="assignment-card">
                                    <div class="assignment-card-header">
                                        <h4 class="assignment-card-title">Tugas 01: Setup Laravel 11 &amp; Lingkungan Kerja</h4>
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
                                        <a href="{{ route('dosen.penilaian') }}" class="btn-secondary-action" style="font-size: 11px;">Lihat Rekap &rarr;</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </main>
    </div>

    <!-- Toast Notification Container -->
    <div class="dosen-toast-container" id="toastContainer"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

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

            const formCreateAssignment = document.getElementById('formCreateAssignment');
            const assignmentsGrid = document.getElementById('assignmentsGrid');
            const assignmentListCount = document.getElementById('assignmentListCount');

            formCreateAssignment.addEventListener('submit', (e) => {
                e.preventDefault();
                const title = document.getElementById('taskTitle').value.trim();
                const desc = document.getElementById('taskDesc').value.trim();
                const type = document.getElementById('taskType').value;
                const deadlineDate = document.getElementById('taskDeadlineDate').value;
                const deadlineTime = document.getElementById('taskDeadlineTime').value;

                if (!title || !desc) {
                    alert('Harap isi judul dan instruksi tugas!');
                    return;
                }

                const card = document.createElement('div');
                card.className = 'assignment-card';
                card.innerHTML = `
                    <div class="assignment-card-header">
                        <h4 class="assignment-card-title">${title}</h4>
                        <span class="card-subtitle-tag" style="padding:2px 8px; font-size:10px;">${type}</span>
                    </div>
                    <p style="font-size: 11.5px; color: #8E6570;">${desc}</p>
                    
                    <div class="assignment-deadline-box">
                        <svg class="deadline-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <div class="deadline-info">
                            <span class="deadline-label">Batas Waktu Pengumpulan</span>
                            <span class="deadline-value">${deadlineDate} &middot; ${deadlineTime}</span>
                        </div>
                    </div>

                    <div class="submission-progress-wrap">
                        <div class="submission-progress-labels">
                            <span>Progress Pengumpulan</span>
                            <span><strong>0</strong> dari 38 Mahasiswa (Baru Terbit)</span>
                        </div>
                        <div class="submission-progress-track">
                            <div class="submission-progress-fill" style="width: 0%;"></div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                        <span class="badge-status badge-status-active">Tugas Aktif</span>
                        <a href="{{ route('dosen.penilaian') }}" class="btn-secondary-action" style="font-size: 11px;">Periksa Jawaban &rarr;</a>
                    </div>
                `;

                assignmentsGrid.prepend(card);
                formCreateAssignment.reset();
                updateAssignmentCount();
                showToast(`Tugas "${title}" berhasil diterbitkan ke mahasiswa!`);
            });

            function updateAssignmentCount() {
                const total = assignmentsGrid.querySelectorAll('.assignment-card').length;
                if (assignmentListCount) assignmentListCount.textContent = total;
            }

        });
    </script>
</body>

</html>
