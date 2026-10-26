@php
    $courseDb = $course;
    $activeTab = request('tab');
    if (!$activeTab) {
        $activeTab = ($errors->has('code') || session('tab') === 'presensi') ? 'presensi' : 'detail';
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mataKuliah['nama'] }} — Portal Mahasiswa KampusLMS</title>
    <!-- Font Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">
    <!-- CSS via Vite -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/mahasiswa/mahasiswa.matkul.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    <!-- Background Decorative Elements (Matching Dashboard & Login) -->
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <div class="app-window">
        <!-- 1. NAVBAR UTAMA PORTAL MAHASISWA -->
        <x-layout />

        <!-- 2. NAVBAR KEDUA (SUB-NAVBAR DETAIL & NILAI MATA KULIAH) -->
        <nav class="course-subnav">
            <div class="course-subnav-inner">
                <div class="course-subnav-left">
                    <div class="course-title-crumb">
                        <span class="crumb-badge">{{ $mataKuliah['kode'] }}</span>
                        <span class="crumb-course-name">{{ $mataKuliah['nama'] }}</span>
                    </div>

                    <!-- TABS: DETAIL, PRESENSI & NILAI -->
                    <div class="course-subnav-tabs">
                        <button class="subnav-tab-btn {{ $activeTab === 'detail' ? 'active' : '' }}" id="tabBtnDetail" onclick="switchCourseTab('detail')">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                            </svg>
                            <span>Detail</span>
                        </button>
                        <button class="subnav-tab-btn {{ $activeTab === 'presensi' ? 'active' : '' }}" id="tabBtnPresensi" onclick="switchCourseTab('presensi')">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 11l3 3L22 4"></path>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                            <span>Presensi</span>
                            @if (isset($activeSessions) && $activeSessions->isNotEmpty())
                                <span class="tab-badge-pill" style="background: #10B981; color: #FFF;">{{ $activeSessions->count() }} Aktif</span>
                            @endif
                        </button>
                        <button class="subnav-tab-btn {{ $activeTab === 'nilai' ? 'active' : '' }}" id="tabBtnNilai" onclick="switchCourseTab('nilai')">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <polyline points="9 15 11 17 15 13"></polyline>
                            </svg>
                            <span>Nilai</span>
                            <span class="tab-badge-pill">{{ $nilaiData['stats']['rata_rata'] }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- 3. KONTEN MATA KULIAH -->
        <main class="course-main-content">

            <!-- VIEW 1: DETAIL (Alur 16 Sesi & Info Matkul) -->
            <div id="panelCourseDetail" style="{{ $activeTab !== 'detail' ? 'display: none;' : '' }}">
                <div class="split-layout">
                    <!-- ==============================================
                         KOLOM KIRI: TIMELINE PERTEMUAN (DENGAN SCROLL INTERNAL)
                         ============================================== -->
                    <section class="left-panel">
                        <div class="panel-header">
                            <h2>Rencana Pertemuan</h2>
                        </div>
                        @php
                            $courseAssignments = $course->assignments->sortBy('due_at')->values();
                            $pertemuanList = [];

                            // Format ukuran berkas tanpa ekstensi intl (Number::fileSize butuh intl).
                            $fmtUkuran = function ($byte) {
                                $byte = (int) $byte;
                                if ($byte >= 1048576) return number_format($byte / 1048576, 1, ',', '.') . ' MB';
                                if ($byte >= 1024)    return number_format($byte / 1024, 0, ',', '.') . ' KB';
                                return $byte . ' B';
                            };

                            // Bentuk data materi untuk ditampilkan (semua unduhan lewat route berpolicy).
                            $fmtMateri = fn ($m) => [
                                'id'        => $m->id,
                                'judul'     => $m->title,
                                'nama'      => $m->isLink() ? 'Tautan eksternal' : ($m->original_name ?: 'Berkas'),
                                'meta'      => $m->isLink()
                                    ? 'Tautan'
                                    : (strtoupper(pathinfo((string) $m->original_name, PATHINFO_EXTENSION) ?: 'file')
                                        . ($m->file_size ? ' · ' . $fmtUkuran($m->file_size) : '')),
                                'deskripsi' => $m->description,
                                'kind'      => $m->type,
                                'url'       => route('mahasiswa.materi.download', $m),
                            ];
                            // Materi yang tidak terikat pertemuan tertentu (session = NULL).
                            $materiUmum = $course->materials->whereNull('session')->map($fmtMateri)->values();

                            for ($minggu = 1; $minggu <= 16; $minggu++) {
                                $isExam = ($minggu == 8 || $minggu == 16);
                                $judul = $minggu == 8 ? 'Minggu 8 (UTS)' : ($minggu == 16 ? 'Minggu 16 (UAS)' : 'Minggu ' . $minggu);

                                // 1. Hubungkan berkas materi jika ada di database untuk sesi ini
                                $matFiles = $course->materials->where('session', $minggu)->map($fmtMateri)->values()->all();

                                // 2. Hubungkan tugas jika ada di database
                                $assignIdx = $minggu - 1;
                                $dbAssign = $courseAssignments->get($assignIdx);
                                $tasks = [];
                                $studentSub = null;

                                if ($dbAssign) {
                                    $studentSub = $student ? $dbAssign->submissions->firstWhere('user_id', $student->id) : null;
                                    $btnAction = 'Kumpulkan Tugas &rarr;';
                                    if ($studentSub && $studentSub->grade) {
                                        $btnAction = 'Lihat Nilai (' . round((float) $studentSub->grade->score, 1) . ') &rarr;';
                                    } elseif ($studentSub) {
                                        $btnAction = 'Sudah Dikumpulkan &rarr;';
                                    } elseif ($dbAssign->status === 'draft') {
                                        $btnAction = 'Belum Dibuka';
                                    } elseif ($dbAssign->due_at && $dbAssign->due_at->isPast()) {
                                        $btnAction = 'Lewat Batas Waktu';
                                    }

                                    $tasks[] = [
                                        'tipe' => 'Tugas Kuliah',
                                        'judul' => $dbAssign->title,
                                        'deadline' => 'Batas: ' . ($dbAssign->due_at ? $dbAssign->due_at->translatedFormat('d M Y, H:i') . ' WITA' : 'Jadwal fleksibel'),
                                        'btn' => $btnAction,
                                        'url' => route('assignments.show', $dbAssign),
                                    ];
                                }

                                // 3. Tentukan status & label dinamis berbasis data tugas & materi di database
                                if ($isExam) {
                                    $status = 'exam';
                                    $label = $minggu == 8 ? 'Ujian UTS' : 'Ujian UAS';
                                } elseif ($dbAssign) {
                                    if ($studentSub || ($dbAssign->due_at && $dbAssign->due_at->isPast())) {
                                        $status = 'done';
                                        $label = 'Selesai';
                                    } elseif ($dbAssign->status === 'draft') {
                                        $status = 'locked';
                                        $label = 'Mendatang';
                                    } else {
                                        $status = 'active';
                                        $label = 'Berjalan';
                                    }
                                } elseif (!empty($matFiles)) {
                                    $status = 'done';
                                    $label = 'Selesai';
                                } else {
                                    $status = 'locked';
                                    $label = 'Mendatang';
                                }

                                $pertemuanList[$minggu] = [
                                    'judul' => $judul,
                                    'status' => $status,
                                    'label' => $label,
                                    'materi' => $matFiles,
                                    'tasks' => $tasks,
                                ];
                            }

                            // Pastikan jika ada mata kuliah aktif dan belum ada sesi aktif, tentukan sesi berjalan berikutnya
                            $hasActive = collect($pertemuanList)->contains('status', 'active');
                            if (!$hasActive && ($mataKuliah['status'] ?? 'active') === 'active') {
                                foreach ($pertemuanList as $m => &$pItem) {
                                    if ($pItem['status'] === 'locked' && $m != 8 && $m != 16) {
                                        $pItem['status'] = 'active';
                                        $pItem['label'] = 'Berjalan';
                                        break;
                                    }
                                }
                                unset($pItem);
                            }
                        @endphp
                        <!-- KOTAK SCROLL HANYA DI BAGIAN INI -->
                        <div class="timeline-scroll-box">
                            @foreach ($pertemuanList as $minggu => $item)
                                @php
                                    $isExam = ($minggu == 8 || $minggu == 16);
                                @endphp
                                <div class="timeline-item">
                                    <div class="timeline-node {{ $item['status'] }} {{ $isExam ? 'exam' : '' }}">
                                        <div class="timeline-node-inner"></div>
                                    </div>
                                    <div class="session-card" id="cardWeek{{ $minggu }}"
                                         data-judul="{{ $item['judul'] }}"
                                         data-materi="{{ json_encode($item['materi']) }}"
                                         data-tasks="{{ json_encode($item['tasks']) }}"
                                         onclick="switchRightToDetail({{ $minggu }}, this)">
                                        <div class="session-left">
                                            <div class="session-icon-circle {{ $item['status'] }} {{ $isExam ? 'exam-icon' : '' }}">
                                                {{ $minggu }}
                                            </div>
                                            <div class="session-text">
                                                <h4>{{ $item['judul'] }}</h4>
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
                         KOLOM KANAN: INFO MATA KULIAH & DETAIL SESI
                         ============================================== -->
                    <aside class="right-panel">
                        <!-- 1. TAMPILAN DEFAULT (INFO MATA KULIAH & CPMK) -->
                        <div id="defaultRightPanel" class="content-fade" style="display: flex; flex-direction: column; gap: 18px;">
                            <div class="card-course-info">
                                <h3>Informasi Mata Kuliah</h3>
                                <div class="lecturer-box">
                                    <div class="lecturer-avatar">
                                        {{ strtoupper(substr($mataKuliah['dosen'], 0, 2)) }}
                                    </div>
                                    <div class="lecturer-info">
                                        <h5>{{ $mataKuliah['dosen'] }}</h5>
                                        <span>Dosen Pengampu Utama</span>
                                    </div>
                                </div>
                                <div class="meta-list">
                                    <div class="meta-row">
                                        <span>Kode Mata Kuliah</span>
                                        <span class="meta-chip chip-cyan">{{ $mataKuliah['kode'] }}</span>
                                    </div>
                                    <div class="meta-row">
                                        <span>Beban Kredit</span>
                                        <span class="meta-chip chip-amber">{{ $mataKuliah['sks'] }} SKS</span>
                                    </div>
                                    <div class="meta-row">
                                        <span>Peserta Terdaftar</span>
                                        <span class="meta-chip chip-green">
                                            {{ $courseDb ? $courseDb->students->count() : 0 }} Mahasiswa
                                        </span>
                                    </div>
                                    <div class="meta-row">
                                        <span>Tugas Terjadwal</span>
                                        <span class="meta-chip chip-orange">
                                            {{ $courseDb ? $courseDb->assignments->count() : 0 }} Tugas Terdata
                                        </span>
                                    </div>
                                    <div class="meta-row">
                                        <span>Materi Tersedia</span>
                                        <span class="meta-chip chip-cyan">{{ $courseDb->materials->count() }} Materi</span>
                                    </div>
                                    <div class="meta-row">
                                        <span>Status Kelas</span>
                                        <span class="meta-chip chip-green">{{ ucfirst($mataKuliah['status'] ?? 'Aktif') }}</span>
                                    </div>
                                    <div class="meta-row" style="align-items: flex-start;">
                                        <span>Deskripsi</span>
                                        <span style="font-size:12px;color:#0F172A;text-align:right;max-width:62%;font-weight:600;">{{ $mataKuliah['deskripsi'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 1b. MATERI UMUM (tidak terikat pertemuan) -->
                        @if ($materiUmum->isNotEmpty())
                            <div class="card-course-info" style="margin-top: 18px;">
                                <h3>Materi Umum</h3>
                                <div style="display: flex; flex-direction: column; gap: 12px;">
                                    @foreach ($materiUmum as $m)
                                        <div class="resource-card">
                                            <div class="resource-left">
                                                <div class="resource-icon">{{ $m['kind'] === 'link' ? '🔗' : '📄' }}</div>
                                                <div>
                                                    <div class="resource-name">{{ $m['judul'] }}</div>
                                                    <div class="resource-meta">{{ $m['meta'] }}</div>
                                                </div>
                                            </div>
                                            <a class="btn-mini-download" href="{{ $m['url'] }}" @if($m['kind'] === 'link') target="_blank" rel="noopener noreferrer" @endif style="text-decoration:none;">
                                                {{ $m['kind'] === 'link' ? 'Buka' : 'Unduh' }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

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
                                    <h3 id="detailHeading">Minggu 1</h3>
                                </div>

                                <div class="detail-block">
                                    <span class="detail-block-title detail-title-materi">Materi & Berkas Perkuliahan</span>
                                    <div class="materi-container" id="materiListContainer" style="display: flex; flex-direction: column; gap: 12px;"></div>
                                </div>

                                <div class="detail-block">
                                    <span class="detail-block-title detail-title-tasks" id="tasksHeaderTitle">Aktivitas & Tugas</span>
                                    <div class="tasks-container" id="tasksListContainer"></div>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>

            <!-- VIEW 2: NILAI (Rekapitulasi Nilai Tugas Mahasiswa) -->
            <div id="panelCourseNilai" style="{{ $activeTab === 'nilai' ? '' : 'display: none;' }}">
                <!-- Stat Cards Ringkasan Nilai -->
                <div class="grades-stat-grid">
                    <div class="grade-summary-card">
                        <div class="grade-card-icon" style="background:#E0F2FE;color:#039FFA;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        </div>
                        <div class="grade-card-info">
                            <span class="grade-card-label">RATA-RATA NILAI TUGAS</span>
                            <div class="grade-card-num">{{ $nilaiData['stats']['rata_rata'] }} <span style="font-size:14px;color:#64748B;font-weight:700;">/ 100</span></div>
                            <span class="grade-card-sub">Perhitungan kumulatif semester berjalan</span>
                        </div>
                    </div>

                    <div class="grade-summary-card">
                        <div class="grade-card-icon" style="background:#EBF9F1;color:#1B8A5A;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="7"></circle>
                                <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                            </svg>
                        </div>
                        <div class="grade-card-info">
                            <span class="grade-card-label">PREDIKAT AKADEMIK</span>
                            <div class="grade-card-num" style="color:#1B8A5A;">{{ $nilaiData['stats']['indeks'] }} <span style="font-size:13px;color:#1B8A5A;font-weight:700;">({{ $nilaiData['stats']['predikat'] }})</span></div>
                            <span class="grade-card-sub">Skala huruf penilaian standar kampus</span>
                        </div>
                    </div>

                    <div class="grade-summary-card">
                        <div class="grade-card-icon" style="background:#FFF7ED;color:#F96305;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 11l3 3L22 4"></path>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                        </div>
                        <div class="grade-card-info">
                            <span class="grade-card-label">PROGRES TUGAS DINILAI</span>
                            <div class="grade-card-num">{{ $nilaiData['stats']['tugas_dinilai'] }}</div>
                            <span class="grade-card-sub">{{ $nilaiData['stats']['rata_rata'] !== '-' ? 'Perhitungan dari tugas yang dinilai' : 'Menunggu proses penilaian dosen' }}</span>
                        </div>
                    </div>

                    <div class="grade-summary-card">
                        <div class="grade-card-icon" style="background:#E0F2FE;color:#039FFA;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 20V10"></path>
                                <path d="M12 20V4"></path>
                                <path d="M6 20v-6"></path>
                            </svg>
                        </div>
                        <div class="grade-card-info">
                            <span class="grade-card-label">BOBOT NILAI TERCAPAI</span>
                            <div class="grade-card-num" style="color:#039FFA;">{{ $nilaiData['stats']['bobot_tercapai'] }} <span style="font-size:13px;color:#64748B;font-weight:700;">/ 100% Total</span></div>
                            <span class="grade-card-sub">Akumulasi persentase SKS mata kuliah</span>
                        </div>
                    </div>
                </div>

                <!-- Card Tabel Rincian Nilai -->
                <div class="grade-table-card">
                    <div class="grade-table-header">
                        <div>
                            <h3 class="grade-table-title">Daftar Nilai & Evaluasi Tugas</h3>
                            <p class="grade-table-desc">Hasil penilaian, bobot persentase, dan catatan umpan balik resmi dari dosen pengampu: <strong>{{ $mataKuliah['dosen'] }}</strong></p>
                        </div>
                        <div class="grade-search-box">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="gradeSearchInput" placeholder="Cari nama tugas...">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="custom-grade-table" id="gradeTable">
                            <thead>
                                <tr>
                                    <th>Aktivitas / Tugas</th>
                                    <th>Waktu Pengumpulan</th>
                                    <th>Status</th>
                                    <th>Bobot</th>
                                    <th>Nilai Akhir</th>
                                    <th>Catatan &amp; Feedback Dosen</th>
                                </tr>
                            </thead>
                            <tbody id="gradeTableBody">
                                @forelse ($nilaiData['items'] as $item)
                                    <tr>
                                        <td>
                                            <div class="task-name-cell">
                                                <div class="task-info">
                                                    <a href="{{ $item['url'] }}" class="task-title-text" style="text-decoration:none;color:inherit;">{{ $item['judul'] }}</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="font-size:12.5px;color:#0F172A;font-weight:700;white-space:nowrap;">
                                            {{ $item['tanggal_kumpul'] }}
                                        </td>
                                        <td>
                                            @if ($item['status'] === 'Dinilai')
                                                <span class="status-pill status-pill-success">
                                                    <span class="status-dot-green"></span>
                                                    Dinilai
                                                </span>
                                            @elseif ($item['status'] === 'Menunggu Penilaian')
                                                <span class="status-pill status-pill-warning">
                                                    <span class="status-dot-orange"></span>
                                                    Sedang Dievaluasi
                                                </span>
                                            @elseif ($item['status'] === 'Lewat Deadline')
                                                <span class="status-pill status-pill-danger">
                                                    <span class="status-dot-red"></span>
                                                    Lewat Deadline
                                                </span>
                                            @elseif ($item['status'] === 'Belum Dikumpulkan')
                                                <span class="status-pill status-pill-warning">
                                                    <span class="status-dot-orange"></span>
                                                    Belum Dikumpulkan
                                                </span>
                                            @else
                                                <span class="status-pill status-pill-muted">
                                                    Belum Dibuka
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="weight-badge">{{ $item['bobot'] }}</span>
                                        </td>
                                        <td>
                                            @if ($item['nilai'] !== null)
                                                <div class="score-display">
                                                    <span class="score-number">{{ $item['nilai'] }}</span>
                                                    <span class="score-max">/ 100</span>
                                                </div>
                                            @else
                                                <span class="score-pending">&mdash;</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="feedback-bubble">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#039FFA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px;">
                                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                                </svg>
                                                <span>{{ $item['feedback'] }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 36px 20px; color: #64748B;">
                                            <div style="font-weight: 700; font-size: 14px; margin-bottom: 4px; color: #0F172A;">Belum Ada Tugas di Database</div>
                                            <div style="font-size: 12.5px; color: #64748B;">Mata kuliah ini belum memiliki penugasan atau rekaman nilai aktif.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- VIEW 3: PRESENSI KELAS KHUSUS MATA KULIAH INI -->
            <div id="panelCoursePresensi" style="{{ $activeTab === 'presensi' ? '' : 'display: none;' }}; padding-top: 10px;">
                @if (session('success'))
                    <div style="background: #DCFCE7; color: #166534; border: 1px solid #86EFAC; border-radius: 14px; padding: 16px 20px; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div style="background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; border-radius: 14px; padding: 16px 20px; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.1);">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink: 0;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <div>
                            @foreach ($errors->all() as $err)
                                <div>{{ $err }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Hero Input Card Khusus Mata Kuliah Ini -->
                <div style="background: linear-gradient(135deg, #0284C7 0%, #039FFA 50%, #38BDF8 100%); border-radius: 20px; padding: 32px; color: #FFF; margin-bottom: 24px; box-shadow: 0 10px 30px rgba(3, 159, 250, 0.22); position: relative; overflow: hidden;">
                    <div style="max-width: 650px;">
                        <span style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; opacity: 0.95;">
                            Presensi Kehadiran Kelas
                        </span>
                        <h2 style="font-size: 26px; font-weight: 900; margin: 4px 0 8px 0; color: #FFF;">
                            {{ $mataKuliah['kode'] }} &bull; {{ $mataKuliah['nama'] }}
                        </h2>
                        <p style="font-size: 14px; opacity: 0.92; line-height: 1.5; margin: 0;">
                            Presensi kehadiran pada halaman ini hanya berlaku untuk mata kuliah ini. Masukkan kode unik yang diberikan oleh dosen pengampu di kelas.
                        </p>
                    </div>

                    <div style="background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(8px); border: 2px dashed rgba(255, 255, 255, 0.5); border-radius: 16px; padding: 20px 24px; margin-top: 20px; max-width: 580px;">
                        <form method="POST" action="{{ route('mahasiswa.mata-kuliah.presensi.store', $course) }}">
                            @csrf
                            <label for="inputPresensiCode" style="display: block; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #FFF; margin-bottom: 8px; text-align: center;">
                                Masukkan Kode Presensi:
                            </label>

                            <input type="text" name="code" id="inputPresensiCode" placeholder="CONTOH: 8K9P2X" maxlength="16" autocomplete="off" required value="{{ old('code') }}" style="width: 100%; font-family: 'Consolas', monospace; font-size: 24px; font-weight: 900; letter-spacing: 5px; text-align: center; text-transform: uppercase; padding: 12px 18px; border-radius: 12px; border: 2px solid rgba(255, 255, 255, 0.8); background: #FFF; color: #0F172A; outline: none; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);">

                            <button type="submit" style="width: 100%; margin-top: 10px; background: #F9B804; color: #78350F; border: none; font-size: 15px; font-weight: 900; padding: 12px 24px; border-radius: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 14px rgba(249, 184, 4, 0.4); transition: transform 0.15s;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M9 11l3 3L22 4"></path>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                </svg>
                                Kirim Presensi Kelas
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Sesi Presensi Aktif di Mata Kuliah Ini -->
                <div style="margin-bottom: 24px;">
                    <h3 style="font-size: 16px; font-weight: 800; color: #0F172A; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #10B981; display: inline-block;"></span>
                        Sesi Presensi yang Sedang Berlangsung di Mata Kuliah Ini
                    </h3>

                    @if (isset($activeSessions) && $activeSessions->isNotEmpty())
                        @foreach ($activeSessions as $active)
                            @php
                                $alreadyAttended = in_array($active->id, $attendedSessionIds ?? []);
                            @endphp
                            <div style="background: #FFF; border: 1px solid rgba(3, 159, 250, 0.2); border-radius: 16px; padding: 18px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);">
                                <div>
                                    <div style="font-size: 15px; font-weight: 800; color: #0F172A;">
                                        {{ $active->title }}
                                    </div>
                                    <div style="font-size: 12.5px; color: #64748B; margin-top: 3px;">
                                        Masa berlaku sampai <strong>{{ $active->expires_at->format('H:i') }} WIB</strong> ({{ $active->remainingTimeForHumans() }})
                                    </div>
                                </div>

                                <div>
                                    @if ($alreadyAttended)
                                        <span style="font-size: 12px; font-weight: 800; padding: 6px 14px; border-radius: 20px; background: #DCFCE7; color: #166534; display: inline-flex; align-items: center; gap: 6px;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                            Anda Sudah Hadir
                                        </span>
                                    @else
                                        <span style="font-size: 12px; font-weight: 800; padding: 6px 14px; border-radius: 20px; background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; display: inline-flex; align-items: center; gap: 6px;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                            </svg>
                                            Belum Hadir &bull; Minta Kode ke Dosen
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div style="background: #FFF; border: 1px dashed #CBD5E1; border-radius: 14px; padding: 20px; text-align: center; color: #64748B; font-size: 13.5px; font-weight: 600;">
                            Tidak ada sesi presensi yang sedang berlangsung untuk mata kuliah ini saat ini.
                        </div>
                    @endif
                </div>

                <!-- Riwayat Kehadiran Mahasiswa di Mata Kuliah Ini -->
                <div style="background: #FFF; border-radius: 18px; border: 1px solid rgba(3, 159, 250, 0.18); box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); overflow: hidden;">
                    <div style="padding: 18px 24px; border-bottom: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between;">
                        <h3 style="font-size: 16px; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#039FFA" stroke-width="2.3">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            Riwayat Kehadiran di Mata Kuliah Ini
                        </h3>
                        <span style="font-size: 12.5px; font-weight: 700; color: #64748B;">
                            Total: {{ isset($attendanceHistory) ? $attendanceHistory->count() : 0 }} Pertemuan
                        </span>
                    </div>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px;">
                            <thead>
                                <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0; color: #475569; font-size: 12px; font-weight: 800; text-transform: uppercase;">
                                    <th style="padding: 12px 20px;">Pertemuan / Sesi</th>
                                    <th style="padding: 12px 16px;">Waktu Presensi</th>
                                    <th style="padding: 12px 16px;">Status</th>
                                    <th style="padding: 12px 20px;">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attendanceHistory ?? [] as $att)
                                    @php
                                        $badgeBg = match($att->status) {
                                            'hadir' => '#DCFCE7',
                                            'izin' => '#E0F2FE',
                                            'sakit' => '#FEF3C7',
                                            default => '#FEE2E2',
                                        };
                                        $badgeColor = match($att->status) {
                                            'hadir' => '#166534',
                                            'izin' => '#0369A1',
                                            'sakit' => '#92400E',
                                            default => '#991B1B',
                                        };
                                    @endphp
                                    <tr style="border-bottom: 1px solid #F1F5F9;">
                                        <td style="padding: 14px 20px; font-weight: 800; color: #0F172A;">
                                            {{ $att->session?->title ?? '-' }}
                                        </td>
                                        <td style="padding: 14px 16px; color: #475569; font-weight: 600;">
                                            {{ $att->attended_at ? $att->attended_at->format('d/m/Y H:i') . ' WIB' : '-' }}
                                        </td>
                                        <td style="padding: 14px 16px;">
                                            <span style="font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; background: {{ $badgeBg }}; color: {{ $badgeColor }};">
                                                {{ strtoupper($att->status) }}
                                            </span>
                                        </td>
                                        <td style="padding: 14px 20px; font-size: 12.5px; color: #64748B;">
                                            {{ $att->notes ?: 'Tercatat di sistem' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="padding: 36px 20px; text-align: center; color: #94A3B8;">
                                            <div style="font-weight: 700; font-size: 14px; color: #475569;">Belum ada riwayat kehadiran pada mata kuliah ini.</div>
                                            <div style="font-size: 12.5px; margin-top: 4px;">Masukkan kode presensi dari dosen pada formulir di atas.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>

        <!-- FOOTER GLOBAL -->
        <x-footer />
    </div>

    <!-- JAVASCRIPT SWITCHER -->
    <script>
        // Switcher Tab Detail, Presensi & Nilai
        function switchCourseTab(tab) {
            const btnDetail    = document.getElementById('tabBtnDetail');
            const btnNilai     = document.getElementById('tabBtnNilai');
            const btnPresensi  = document.getElementById('tabBtnPresensi');
            const panelDetail  = document.getElementById('panelCourseDetail');
            const panelNilai   = document.getElementById('panelCourseNilai');
            const panelPresensi = document.getElementById('panelCoursePresensi');

            if (btnDetail) btnDetail.classList.remove('active');
            if (btnNilai) btnNilai.classList.remove('active');
            if (btnPresensi) btnPresensi.classList.remove('active');

            if (panelDetail) panelDetail.style.display   = 'none';
            if (panelNilai) panelNilai.style.display    = 'none';
            if (panelPresensi) panelPresensi.style.display = 'none';

            if (tab === 'nilai') {
                if (btnNilai) btnNilai.classList.add('active');
                if (panelNilai) panelNilai.style.display = 'block';
                if (window.history.replaceState) {
                    const url = new URL(window.location);
                    url.searchParams.set('tab', 'nilai');
                    window.history.replaceState({}, '', url);
                }
            } else if (tab === 'presensi') {
                if (btnPresensi) btnPresensi.classList.add('active');
                if (panelPresensi) panelPresensi.style.display = 'block';
                if (window.history.replaceState) {
                    const url = new URL(window.location);
                    url.searchParams.set('tab', 'presensi');
                    window.history.replaceState({}, '', url);
                }
            } else {
                if (btnDetail) btnDetail.classList.add('active');
                if (panelDetail) panelDetail.style.display = 'block';
                if (window.history.replaceState) {
                    const url = new URL(window.location);
                    url.searchParams.delete('tab');
                    window.history.replaceState({}, '', url);
                }
            }
        }

        // Live Search Tabel Nilai
        const searchInput = document.getElementById('gradeSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const q = this.value.toLowerCase();
                document.querySelectorAll('#gradeTableBody tr').forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
                });
            });
        }

        // Pembantu: buat elemen dengan teks aman (tanpa innerHTML => tidak ada XSS dari judul/nama berkas).
        function el(tag, className, text) {
            const node = document.createElement(tag);
            if (className) node.className = className;
            if (text !== undefined && text !== null) node.textContent = text;
            return node;
        }

        function emptyNote(message, bg, border, color) {
            const box = el('div', null, message);
            box.style.cssText = `padding:14px 16px;background:${bg};border:1px dashed ${border};border-radius:12px;color:${color};font-size:12.5px;text-align:center;font-weight:600;`;
            return box;
        }

        // Switcher Pertemuan Mingguan (Left -> Right). Data dibaca dari atribut data-* kartu.
        function switchRightToDetail(minggu, card) {
            document.getElementById('defaultRightPanel').style.display = 'none';
            document.getElementById('detailRightPanel').style.display = 'block';

            document.getElementById('detailBadgeSession').innerText = 'Minggu ' + minggu;
            document.getElementById('detailHeading').innerText = card.dataset.judul;

            const materiList = JSON.parse(card.dataset.materi || '[]');
            const tasks = JSON.parse(card.dataset.tasks || '[]');

            // --- Materi: tombol Unduh/Buka mengarah ke route berpolicy ---
            const materiContainer = document.getElementById('materiListContainer');
            materiContainer.replaceChildren();

            if (materiList.length === 0) {
                materiContainer.appendChild(emptyNote('Belum ada materi untuk pertemuan ini.', '#F0F9FF', 'rgba(3,159,250,0.3)', '#0284C7'));
            } else {
                materiList.forEach(m => {
                    const item = el('div', 'resource-card');
                    const left = el('div', 'resource-left');
                    left.appendChild(el('div', 'resource-icon', m.kind === 'link' ? '\u{1F517}' : '\u{1F4C4}'));
                    const info = el('div');
                    info.appendChild(el('div', 'resource-name', m.judul));
                    info.appendChild(el('div', 'resource-meta', m.nama + ' \u00B7 ' + m.meta));
                    left.appendChild(info);

                    const btn = el('a', 'btn-mini-download', m.kind === 'link' ? 'Buka' : 'Unduh');
                    btn.href = m.url;
                    btn.style.textDecoration = 'none';
                    if (m.kind === 'link') { btn.target = '_blank'; btn.rel = 'noopener noreferrer'; }

                    item.append(left, btn);
                    materiContainer.appendChild(item);
                });
            }

            // --- Tugas: tombol membuka halaman detail & pengumpulan ---
            const tasksContainer = document.getElementById('tasksListContainer');
            tasksContainer.replaceChildren();
            document.getElementById('tasksHeaderTitle').innerText = 'Aktivitas & Tugas (' + tasks.length + ' Penugasan)';

            if (tasks.length === 0) {
                tasksContainer.appendChild(emptyNote('Tidak ada penugasan terjadwal untuk pertemuan ini.', '#FFFBEB', 'rgba(245,158,11,0.35)', '#92400E'));
            } else {
                tasks.forEach((task, index) => {
                    const typeLower = task.tipe.toLowerCase();
                    let badgeClass = '';
                    if (typeLower.includes('kuis')) badgeClass = 'quiz';
                    else if (typeLower.includes('praktikum') || typeLower.includes('latihan')) badgeClass = 'praktikum';
                    else if (typeLower.includes('milestone')) badgeClass = 'milestone';
                    else if (typeLower.includes('ujian') || typeLower.includes('uts') || typeLower.includes('uas')) badgeClass = 'exam';

                    const box = el('div', 'task-card-box');
                    const head = el('div', 'task-card-header');
                    head.appendChild(el('span', 'task-badge-tag ' + badgeClass, task.tipe));
                    const num = el('span', null, 'Penugasan #' + (index + 1));
                    num.style.cssText = 'font-size:11px;font-weight:800;color:#F96305;';
                    head.appendChild(num);

                    const action = el('a', 'btn-task-action');
                    action.innerHTML = task.btn; // teks tombol disusun server (berisi &rarr;), bukan input pengguna
                    action.href = task.url;
                    action.style.cssText = 'text-decoration:none;display:block;text-align:center;';

                    box.append(head, el('div', 'task-card-title', task.judul), el('div', 'task-deadline', task.deadline), action);
                    tasksContainer.appendChild(box);
                });
            }

            document.querySelectorAll('.session-card').forEach(c => c.classList.remove('selected-active'));
            const activeCard = document.getElementById('cardWeek' + minggu);
            if (activeCard) activeCard.classList.add('selected-active');
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
