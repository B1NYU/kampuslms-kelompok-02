@php
    $courseDb = $course;
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
    @vite(['resources/css/app.css', 'resources/css/course-show.css', 'resources/js/app.js'])
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

                    <!-- TABS: DETAIL & NILAI -->
                    <div class="course-subnav-tabs">
                        <button class="subnav-tab-btn {{ request('tab') !== 'nilai' ? 'active' : '' }}" id="tabBtnDetail" onclick="switchCourseTab('detail')">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                            </svg>
                            <span>Detail</span>
                        </button>
                        <button class="subnav-tab-btn {{ request('tab') === 'nilai' ? 'active' : '' }}" id="tabBtnNilai" onclick="switchCourseTab('nilai')">
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
                <div class="course-subnav-right">
                    <a href="{{ route('dashboard') }}" class="btn-subnav-back">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                        <span>Kembali ke Kelas</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- 3. KONTEN MATA KULIAH -->
        <main class="course-main-content">
            
            <!-- VIEW 1: DETAIL (Alur 16 Sesi & Info Matkul) -->
            <div id="panelCourseDetail" style="{{ request('tab') === 'nilai' ? 'display: none;' : '' }}">
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

                            for ($minggu = 1; $minggu <= 16; $minggu++) {
                                $isExam = ($minggu == 8 || $minggu == 16);
                                $judul = $minggu == 8 ? 'Minggu 8 (UTS)' : ($minggu == 16 ? 'Minggu 16 (UAS)' : 'Minggu ' . $minggu);

                                // 1. Hubungkan berkas materi jika ada di database untuk sesi ini
                                $matFiles = $course->materials ? $course->materials->where('session', $minggu)->pluck('original_name')->toArray() : [];

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
                                    $materiList = (array) $item['materi'];
                                    $materiJson = htmlspecialchars(json_encode($materiList), ENT_QUOTES, 'UTF-8');
                                    $tasksJson = htmlspecialchars(json_encode($item['tasks']), ENT_QUOTES, 'UTF-8');
                                @endphp
                                <div class="timeline-item">
                                    <div class="timeline-node {{ $item['status'] }} {{ $isExam ? 'exam' : '' }}">
                                        <div class="timeline-node-inner"></div>
                                    </div>
                                    <div class="session-card" id="cardWeek{{ $minggu }}"
                                         onclick="switchRightToDetail({{ $minggu }}, '{{ addslashes($item['judul']) }}', '{!! $materiJson !!}', '{!! $tasksJson !!}')">                               
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
            <div id="panelCourseNilai" style="{{ request('tab') === 'nilai' ? '' : 'display: none;' }}">
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
                                                    <span class="task-title-text">{{ $item['judul'] }}</span>
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

        </main>

        <!-- FOOTER GLOBAL -->
        <x-footer />
    </div>

    <!-- JAVASCRIPT SWITCHER -->
    <script>
        // Switcher Tab Detail & Nilai
        function switchCourseTab(tab) {
            const btnDetail   = document.getElementById('tabBtnDetail');
            const btnNilai    = document.getElementById('tabBtnNilai');
            const panelDetail = document.getElementById('panelCourseDetail');
            const panelNilai  = document.getElementById('panelCourseNilai');

            if (tab === 'detail') {
                btnDetail.classList.add('active');
                btnNilai.classList.remove('active');
                panelDetail.style.display = 'block';
                panelNilai.style.display  = 'none';
                if (window.history.replaceState) {
                    const url = new URL(window.location);
                    url.searchParams.delete('tab');
                    window.history.replaceState({}, '', url);
                }
            } else {
                btnNilai.classList.add('active');
                btnDetail.classList.remove('active');
                panelNilai.style.display  = 'block';
                panelDetail.style.display = 'none';
                if (window.history.replaceState) {
                    const url = new URL(window.location);
                    url.searchParams.set('tab', 'nilai');
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

        // Switcher Pertemuan Mingguan (Left -> Right)
        function switchRightToDetail(minggu, judul, materiJsonStr, tasksJsonStr) {
            document.getElementById('defaultRightPanel').style.display = 'none';
            const detailPanel = document.getElementById('detailRightPanel');
            detailPanel.style.display = 'block';
            
            document.getElementById('detailBadgeSession').innerText = 'Minggu ' + minggu;
            document.getElementById('detailHeading').innerText = judul;
            
            // Render Materi
            const materiList = JSON.parse(materiJsonStr);
            const materiContainer = document.getElementById('materiListContainer');
            materiContainer.innerHTML = '';
            
            if (materiList.length === 0) {
                materiContainer.innerHTML = `
                    <div style="padding: 14px 16px; background: #F0F9FF; border: 1px dashed rgba(3, 159, 250, 0.3); border-radius: 12px; color: #0284C7; font-size: 12.5px; text-align: center; font-weight: 600;">
                        Belum ada berkas materi diunggah di database untuk pertemuan ini.
                    </div>
                `;
            } else {
                materiList.forEach(materi => {
                    const resourceCard = document.createElement('div');
                    resourceCard.className = 'resource-card';
                    resourceCard.innerHTML = `
                        <div class="resource-left">
                            <div class="resource-icon">&#128196;</div>
                            <div>
                                <div class="resource-name">${materi}</div>
                                <div class="resource-meta">Materi Dosen &middot; Format Dokumen</div>
                            </div>
                        </div>
                        <button class="btn-mini-download" onclick="alert('Mengunduh ${materi}...')">
                            Unduh
                        </button>
                    `;
                    materiContainer.appendChild(resourceCard);
                });
            }

            // Render Tasks
            const tasks = JSON.parse(tasksJsonStr);
            const tasksContainer = document.getElementById('tasksListContainer');
            tasksContainer.innerHTML = '';
            document.getElementById('tasksHeaderTitle').innerText = 'Aktivitas & Tugas (' + tasks.length + ' Penugasan)';
            
            if (tasks.length === 0) {
                tasksContainer.innerHTML = `
                    <div style="padding: 14px 16px; background: #FFFBEB; border: 1px dashed rgba(245, 158, 11, 0.35); border-radius: 12px; color: #92400E; font-size: 12.5px; text-align: center; font-weight: 600;">
                        Tidak ada penugasan terjadwal untuk pertemuan ini.
                    </div>
                `;
            } else {
                tasks.forEach((task, index) => {
                    const typeLower = task.tipe.toLowerCase();
                    let badgeClass = '';
                    if (typeLower.includes('kuis')) badgeClass = 'quiz';
                    else if (typeLower.includes('praktikum') || typeLower.includes('latihan')) badgeClass = 'praktikum';
                    else if (typeLower.includes('milestone')) badgeClass = 'milestone';
                    else if (typeLower.includes('ujian') || typeLower.includes('uts') || typeLower.includes('uas')) badgeClass = 'exam';
                    
                    const isLihatNilai = task.btn.includes('Lihat Nilai');
                    const clickAction = isLihatNilai ? "switchCourseTab('nilai')" : "alert('Penugasan: " + task.judul.replace(/'/g, "\\'") + "')";

                    const taskCard = document.createElement('div');
                    taskCard.className = 'task-card-box';
                    taskCard.innerHTML = `
                        <div class="task-card-header">
                            <span class="task-badge-tag ${badgeClass}">${task.tipe}</span>
                            <span style="font-size: 11px; font-weight: 800; color: #F96305;">Penugasan #${index + 1}</span>
                        </div>
                        <div class="task-card-title">${task.judul}</div>
                        <div class="task-deadline">${task.deadline}</div>
                        <button class="btn-task-action" onclick="${clickAction}">
                            ${task.btn}
                        </button>
                    `;
                    tasksContainer.appendChild(taskCard);
                });
            }

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