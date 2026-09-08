<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mataKuliah['nama'] }} - Alur Perkuliahan</title>

    <!-- Font Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    <!-- CSS Khusus Show via Vite -->
    @vite(['resources/css/course-show.css'])
</head>

<body>

    <!-- Background Decorative Elements (Matching Dashboard LMS) -->
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>

    <main class="main-container">

        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="top-brand">
                <a href="{{ route('mata-kuliah.index') }}" class="brand-logo">
                    KAMPUS <span>LMS</span>
                </a>
                <span class="brand-divider">/</span>
                <span class="brand-sub">{{ $mataKuliah['nama'] }}</span>
            </div>

            <a href="{{ route('mata-kuliah.index') }}" class="btn-back">
                &larr; Kembali ke Daftar Mata Kuliah
            </a>
        </nav>

        <!-- TATA LETAK 2 KOLOM -->
        <div class="split-layout">

            <!-- ==============================================
                 KOLOM KIRI: SATU-SATUNYA KOTAK DENGAN SCROLLBAR INTERNAL
                 ============================================== -->
            <section class="left-panel">
                <div class="panel-header">
                    <h2>Rencana Pertemuan</h2>
                    <span class="header-pill">16 Sesi Terstruktur</span>
                </div>

                @php
                    $pertemuanList = [
                        1 => [
                            'judul' => 'Pengenalan & Instalasi Laravel 12',
                            'snippet' => 'Arsitektur MVC, struktur direktori & tools',
                            'status' => 'done', 'label' => 'Selesai',
                            'materi' => 'Slide 01 - Pengantar Laravel 12.pdf',
                            'tasks' => [
                                ['tipe' => 'Tugas Mandiri', 'judul' => 'Setup Repo GitHub & Laporan Bacaan 1', 'deadline' => 'Batas: Minggu 1, 23.59 WITA', 'btn' => 'Kumpulkan Tugas &rarr;']
                            ]
                        ],
                        2 => [
                            'judul' => 'Routing & Arsitektur Controller',
                            'snippet' => 'Route list, prefix, dan controller aksi',
                            'status' => 'active', 'label' => 'Berjalan',
                            'materi' => 'Slide 02 - Routing & Controller.pdf',
                            'tasks' => [
                                ['tipe' => 'Tugas Praktikum', 'judul' => 'Tugas Praktikum: Route Parameter & Show', 'deadline' => 'Batas: Minggu 2, 23.59 WITA', 'btn' => 'Kumpulkan Praktikum &rarr;'],
                                ['tipe' => 'Kuis Teori', 'judul' => 'Kuis 1: Konsep Routing & Controller di Laravel', 'deadline' => 'Waktu pengerjaan: 30 Menit (10 Soal)', 'btn' => 'Mulai Kuis Online &rarr;']
                            ]
                        ],
                        3 => [
                            'judul' => 'Perancangan Database & Migration',
                            'snippet' => 'Skema 8 tabel wajib & constraint composite',
                            'status' => 'locked', 'label' => 'Mendatang',
                            'materi' => 'ERD Diagram & Migration Guide.pdf',
                            'tasks' => [
                                ['tipe' => 'Tugas Milestone 1', 'judul' => 'Tugas 1: Database Migration & Seeder 8 Tabel', 'deadline' => 'Batas: Minggu 3 (Interview Rekaman)', 'btn' => 'Kumpulkan Berkas &rarr;']
                            ]
                        ],
                        4 => [
                            'judul' => 'Eloquent ORM & Relasi Database',
                            'snippet' => 'hasMany, belongsTo & withPivot',
                            'status' => 'locked', 'label' => 'Mendatang',
                            'materi' => 'Slide 04 - Eloquent Relations.pdf',
                            'tasks' => [
                                ['tipe' => 'Latihan Praktikum', 'judul' => 'Implementasi Relasi Model Course & User', 'deadline' => 'Batas: Minggu 4, 23.59 WITA', 'btn' => 'Kumpulkan Tugas &rarr;'],
                                ['tipe' => 'Kuis Teori', 'judul' => 'Kuis 2: Eloquent ORM & Query Builder', 'deadline' => 'Waktu pengerjaan: 25 Menit', 'btn' => 'Mulai Kuis Online &rarr;']
                            ]
                        ],
                        5 => ['judul' => 'Form Handling & Server Validation', 'snippet' => 'Request validation & error handling aman', 'status' => 'locked', 'label' => 'Mendatang', 'materi' => 'Slide Form Request Rules.pdf', 'tasks' => [['tipe' => 'Tugas Praktikum', 'judul' => 'Latihan CRUD Mata Kuliah & Form Request', 'deadline' => 'Batas: Minggu 5, 23.59 WITA', 'btn' => 'Kumpulkan Tugas &rarr;']]],
                        6 => ['judul' => 'Autentikasi & Multi-Role Access', 'snippet' => 'Role admin, dosen, dan mahasiswa', 'status' => 'locked', 'label' => 'Mendatang', 'materi' => 'Middleware & Session Auth Guide.pdf', 'tasks' => [['tipe' => 'Tugas Kelompok', 'judul' => 'Implementasi Middleware Role Akses', 'deadline' => 'Batas: Minggu 6, 23.59 WITA', 'btn' => 'Kumpulkan Tugas &rarr;']]],
                        7 => ['judul' => 'Review Fondasi & Keamanan Sistem', 'snippet' => 'Checklist persiapan Milestone 2', 'status' => 'locked', 'label' => 'Mendatang', 'materi' => 'Evaluasi Milestone 2 Guide.pdf', 'tasks' => [['tipe' => 'Tugas Milestone 2', 'judul' => 'Tugas 2: Akses & Keamanan Aplikasi (Role & CRUD)', 'deadline' => 'Batas: Minggu 7 (Interview Rekaman)', 'btn' => 'Kumpulkan Berkas &rarr;']]],
                        8 => ['judul' => 'Ujian Tengah Semester (UTS)', 'snippet' => 'Demo aplikasi & interview pemahaman kode', 'status' => 'exam', 'label' => 'Ujian UTS', 'materi' => 'Rubrik Penilaian UTS (Bobot 20%).pdf', 'tasks' => [['tipe' => 'Ujian UTS (20%)', 'judul' => 'Presentasi & Walkthrough Code Kelompok', 'deadline' => 'Sesi Ujian Minggu 8', 'btn' => 'Lihat Jadwal Ujian &rarr;']]],
                        9 => ['judul' => 'Pengelolaan Berkas & File Storage', 'snippet' => 'Penyimpanan materi dan berkas privat', 'status' => 'locked', 'label' => 'Mendatang', 'materi' => 'Panduan Private File Storage.pdf', 'tasks' => [['tipe' => 'Tugas Praktikum', 'judul' => 'Fitur Unggah Berkas Materi Kuliah', 'deadline' => 'Batas: Minggu 9, 23.59 WITA', 'btn' => 'Kumpulkan Tugas &rarr;']]],
                        10 => ['judul' => 'Alur Penugasan & Pengumpulan Tugas', 'snippet' => 'Submissions, deadline & late flag', 'status' => 'locked', 'label' => 'Mendatang', 'materi' => 'Alur Submissions & Penilaian.pdf', 'tasks' => [['tipe' => 'Tugas Milestone 3', 'judul' => 'Tugas 3: Modul Penugasan, Submissions & Penilaian', 'deadline' => 'Batas: Minggu 10 (Interview Rekaman)', 'btn' => 'Kumpulkan Tugas &rarr;']]],
                        11 => ['judul' => 'Optimalisasi Query & Penilaian', 'snippet' => 'Pencegahan N+1 query & rekap nilai', 'status' => 'locked', 'label' => 'Mendatang', 'materi' => 'Slide Eager Loading & Debugbar.pdf', 'tasks' => [['tipe' => 'Latihan Praktikum', 'judul' => 'Deteksi N+1 Query dengan Laravel Debugbar', 'deadline' => 'Batas: Minggu 11, 23.59 WITA', 'btn' => 'Kumpulkan Tugas &rarr;']]],
                        12 => ['judul' => 'Queue System & Notifikasi In-App', 'snippet' => 'Asynchronous queue worker & notifications', 'status' => 'locked', 'label' => 'Mendatang', 'materi' => 'Database Queue Worker Guide.pdf', 'tasks' => [['tipe' => 'Tugas Praktikum', 'judul' => 'Simulasi Notifikasi Email & Database Queue', 'deadline' => 'Batas: Minggu 12, 23.59 WITA', 'btn' => 'Kumpulkan Tugas &rarr;']]],
                        13 => ['judul' => 'Peer Review Antar Kelompok', 'snippet' => 'Evaluasi Pull Request kelompok lain', 'status' => 'locked', 'label' => 'Mendatang', 'materi' => 'Checklist Resmi Peer Review PR.pdf', 'tasks' => [['tipe' => 'Review (10%)', 'judul' => 'Peer Review Pull Request Kelompok Lain', 'deadline' => 'Batas: Minggu 13, 23.59 WITA', 'btn' => 'Submit Review PR &rarr;']]],
                        14 => ['judul' => 'Deployment Publik & Konfigurasi SSL', 'snippet' => 'Live deployment pada server VPS publik', 'status' => 'locked', 'label' => 'Mendatang', 'materi' => 'Panduan Setup Nginx & HTTPS.pdf', 'tasks' => [['tipe' => 'Tugas Milestone 4', 'judul' => 'Tugas 4: Live Deployment Aplikasi di Domain Publik', 'deadline' => 'Batas: Minggu 14 (Interview Rekaman)', 'btn' => 'Kumpulkan Link Live &rarr;']]],
                        15 => ['judul' => 'Finalisasi Fitur Diferensiasi', 'snippet' => 'Penyempurnaan fitur unggulan kelompok', 'status' => 'locked', 'label' => 'Mendatang', 'materi' => 'Dokumentasi Fitur Khusus.pdf', 'tasks' => [['tipe' => 'Laporan Proyek', 'judul' => 'Penyusunan Laporan Proyek & Dokumentasi API', 'deadline' => 'Batas: Minggu 15, 23.59 WITA', 'btn' => 'Unggah Draft Laporan &rarr;']]],
                        16 => ['judul' => 'Ujian Akhir Semester (UAS)', 'snippet' => 'Presentasi final & evaluasi individu', 'status' => 'exam', 'label' => 'Ujian UAS', 'materi' => 'Rubrik Evaluasi Proyek (20%).pdf', 'tasks' => [['tipe' => 'Proyek Akhir (20%)', 'judul' => 'Presentasi Akhir + Interview Individu + Laporan', 'deadline' => 'Sesi Ujian Minggu 16', 'btn' => 'Kumpulkan Berkas Final &rarr;']]],
                    ];
                @endphp

                <!-- KOTAK SCROLL HANYA DI BAGIAN INI -->
                <div class="timeline-scroll-box">
                    @foreach ($pertemuanList as $minggu => $item)
                        @php
                            $isExam = ($minggu == 8 || $minggu == 16);
                            $tasksJson = htmlspecialchars(json_encode($item['tasks']), ENT_QUOTES, 'UTF-8');
                        @endphp
                        <div class="timeline-item">
                            <div class="timeline-node {{ $item['status'] }} {{ $isExam ? 'exam' : '' }}">
                                <div class="timeline-node-inner"></div>
                            </div>

                            <div class="session-card" id="cardWeek{{ $minggu }}"
                                 onclick="switchRightToDetail({{ $minggu }}, '{{ addslashes($item['judul']) }}', '{{ addslashes($item['snippet']) }}', '{{ addslashes($item['materi']) }}', '{{ $tasksJson }}')">
                                
                                <div class="session-left">
                                    <div class="session-icon-circle {{ $item['status'] }} {{ $isExam ? 'exam-icon' : '' }}">
                                        {{ $minggu }}
                                    </div>
                                    <div class="session-text">
                                        <h4>{{ $item['judul'] }}</h4>
                                        <p>{{ $item['snippet'] }}</p>
                                    </div>
                                </div>

                                <div class="session-right">
                                    <span class="badge-status {{ $item['status'] }}">
                                        {{ $item['label'] }}
                                    </span>
                                    <div class="arrow-btn">&rsaquo;</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- ==============================================
                 KOLOM KANAN: ALAMI/BAWAAN (STICKY TANPA INNER SCROLLBAR)
                 ============================================== -->
            <aside class="right-panel">

                <!-- 1. TAMPILAN DEFAULT (INFO MATA KULIAH & CPMK) -->
                <div id="defaultRightPanel" class="content-fade" style="display: flex; flex-direction: column; gap: 18px;">
                    
                    <div class="card-course-info">
                        <h3>Informasi Mata Kuliah</h3>

                        <div class="lecturer-box">
                            <div class="lecturer-avatar">
                                {{ substr($mataKuliah['dosen'], 0, 2) }}
                            </div>
                            <div class="lecturer-info">
                                <h5>{{ $mataKuliah['dosen'] }}</h5>
                                <span>Dosen Pengampu Utama</span>
                            </div>
                        </div>

                        <div class="meta-list">
                            <div class="meta-row">
                                <span>Kode Mata Kuliah</span>
                                <span class="meta-chip chip-gold">{{ $mataKuliah['kode'] }}</span>
                            </div>
                            <div class="meta-row">
                                <span>Beban Kredit</span>
                                <span class="meta-chip chip-cream">{{ $mataKuliah['sks'] }} SKS</span>
                            </div>
                            <div class="meta-row">
                                <span>Status Kelas</span>
                                <span class="meta-chip chip-gold">Aktif (Genap 2026)</span>
                            </div>
                            <div class="meta-row">
                                <span>Jadwal Kuliah</span>
                                <span class="meta-chip chip-coral">Senin, 08.00 - 10.30</span>
                            </div>
                            <div class="meta-row">
                                <span>Ruang Pertemuan</span>
                                <span class="meta-chip chip-crimson">Lab Komputer SI-01</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-cpmk-premium">
                        <h3>Capaian Pembelajaran (CPMK)</h3>

                        <ul class="cpmk-list">
                            <li class="cpmk-item">
                                <span class="check-icon">&#10003;</span>
                                <span>Menguasai arsitektur MVC & alur kerja framework Laravel 12.</span>
                            </li>
                            <li class="cpmk-item">
                                <span class="check-icon">&#10003;</span>
                                <span>Mampu merancang database relasional dengan 8 tabel wajib & seeder.</span>
                            </li>
                            <li class="cpmk-item">
                                <span class="check-icon">&#10003;</span>
                                <span>Penerapan otorisasi multi-role (Admin, Dosen, Mahasiswa).</span>
                            </li>
                            <li class="cpmk-item">
                                <span class="check-icon">&#10003;</span>
                                <span>Pengelolaan upload berkas dan pengumpulan tugas aman.</span>
                            </li>
                            <li class="cpmk-item">
                                <span class="check-icon">&#10003;</span>
                                <span>Deployment aplikasi pada server VPS publik dengan domain SSL.</span>
                            </li>
                        </ul>

                        <button class="btn-gradient-action" onclick="alert('Silabus RPS Lengkap Semester Genap 2026')">
                            Unduh RPS & Silabus (PDF)
                        </button>
                    </div>

                </div>

                <!-- 2. TAMPILAN PENGGANTI (DETAIL SESI MINGGUAN) -->
                <div id="detailRightPanel" class="content-fade" style="display: none;">
                    
                    <div class="card-week-detail-panel">
                        <div class="detail-top-bar">
                            <button class="btn-restore-info" onclick="restoreDefaultInfo()">
                                &larr; Kembali ke Info Matkul
                            </button>
                            <span id="detailBadgeSession" class="header-pill">Minggu 1</span>
                        </div>

                        <div class="detail-title-group">
                            <span class="detail-badge-session">Rincian Pertemuan</span>
                            <h3 id="detailHeading">Pengenalan & Instalasi Laravel 12</h3>
                        </div>

                        <div class="detail-block">
                            <span class="detail-block-title">Pokok Bahasan Perkuliahan</span>
                            <div class="detail-desc-box" id="detailDescription">
                                Mempelajari pengantar framework Laravel 12, arsitektur MVC, dan setup awal.
                            </div>
                        </div>

                        <div class="detail-block">
                            <span class="detail-block-title">Materi & Berkas Perkuliahan</span>
                            <div class="resource-card">
                                <div class="resource-left">
                                    <span class="resource-icon">&#128196;</span>
                                    <div>
                                        <div class="resource-name" id="detailResourceTitle">Slide 01.pdf</div>
                                        <div class="resource-meta">Materi Dosen &middot; PDF</div>
                                    </div>
                                </div>
                                <button class="btn-mini-download" onclick="alert('Mengunduh materi...')">
                                    Unduh
                                </button>
                            </div>
                        </div>

                        <!-- DAFTAR PENUGASAN (OTOMATIS TERSUSUN KE BAWAH) -->
                        <div class="detail-block">
                            <span class="detail-block-title" id="tasksHeaderTitle">Aktivitas & Tugas</span>
                            <div class="tasks-container" id="tasksListContainer">
                                <!-- Kartu-kartu tugas dirender di sini oleh JavaScript -->
                            </div>
                        </div>

                    </div>

                </div>

            </aside>

        </div>

    </main>

    <!-- JAVASCRIPT SWITCHER KONTEN KANAN -->
    <script>
        function switchRightToDetail(minggu, judul, snippet, materi, tasksJsonStr) {
            document.getElementById('defaultRightPanel').style.display = 'none';

            const detailPanel = document.getElementById('detailRightPanel');
            detailPanel.style.display = 'block';

            document.getElementById('detailBadgeSession').innerText = 'Minggu Ke-' + minggu;
            document.getElementById('detailHeading').innerText = 'Pertemuan ' + minggu + ': ' + judul;
            document.getElementById('detailDescription').innerText = snippet;
            document.getElementById('detailResourceTitle').innerText = materi;

            const tasks = JSON.parse(tasksJsonStr);
            const tasksContainer = document.getElementById('tasksListContainer');
            tasksContainer.innerHTML = '';

            document.getElementById('tasksHeaderTitle').innerText = 'Aktivitas & Tugas (' + tasks.length + ' Penugasan)';

            tasks.forEach((task, index) => {
                const typeLower = task.tipe.toLowerCase();
                let badgeClass = '';
                if (typeLower.includes('kuis')) badgeClass = 'quiz';
                else if (typeLower.includes('praktikum') || typeLower.includes('latihan')) badgeClass = 'praktikum';
                else if (typeLower.includes('milestone')) badgeClass = 'milestone';
                else if (typeLower.includes('ujian') || typeLower.includes('uts') || typeLower.includes('uas')) badgeClass = 'exam';

                const taskCard = document.createElement('div');
                taskCard.className = 'task-card-box';
                taskCard.innerHTML = `
                    <div class="task-card-header">
                        <span class="task-badge-tag ${badgeClass}">${task.tipe}</span>
                        <span style="font-size: 11px; font-weight: 800; color: #FF5E5E;">Penugasan #${index + 1}</span>
                    </div>
                    <div class="task-card-title">${task.judul}</div>
                    <div class="task-deadline">${task.deadline}</div>
                    <button class="btn-task-action" onclick="alert('Aksi untuk: ${task.judul}')">
                        ${task.btn}
                    </button>
                `;
                tasksContainer.appendChild(taskCard);
            });

            document.querySelectorAll('.session-card').forEach(card => {
                card.classList.remove('selected-active');
            });
            const activeCard = document.getElementById('cardWeek' + minggu);
            if (activeCard) {
                activeCard.classList.add('selected-active');
            }
        }

        function restoreDefaultInfo() {
            document.getElementById('detailRightPanel').style.display = 'none';
            document.getElementById('defaultRightPanel').style.display = 'flex';
            document.querySelectorAll('.session-card').forEach(card => {
                card.classList.remove('selected-active');
            });
        }
    </script>

</body>

</html>
