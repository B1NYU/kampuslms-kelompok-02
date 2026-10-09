<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rekapitulasi Nilai Mahasiswa — KampusLMS Admin</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/admin/admin.nilai.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/admin/admin.common.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/admin.nilai.css') }}">
    @endif
</head>
<body>
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <div class="app-window">

        <!-- Navbar Khusus Admin -->
        <x-navbar-admin />

        <!-- Konten Utama Rekap Nilai Admin -->
        <main class="admin-content">

            <!-- Topbar Header (Identik dengan format admin.materi dan admin.tugas) -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <h1>Rekapitulasi Nilai Mahasiswa</h1>
                    </div>
                </div>
                <div class="topbar-right">
                    <button type="button" class="btn-quick-action btn-outline" id="btnExportCsv" title="Unduh data dalam format CSV untuk diolah di Excel">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Ekspor Nilai (CSV)</span>
                    </button>
                </div>
            </header>

            @php
                // Query data asli dari database
                $dbSubmissions = \App\Models\Submission::with(['student', 'assignment.course.lecturer', 'grade.grader'])
                    ->latest('submitted_at')
                    ->get();
                $dbCourses = \App\Models\Course::with('lecturer')->orderBy('code')->get();

                // Helper kalkulasi predikat huruf mutu
                $getGradeLetter = function($score) {
                    if ($score === null) return '-';
                    $s = (float) $score;
                    if ($s >= 85) return 'A';
                    if ($s >= 80) return 'A-';
                    if ($s >= 75) return 'B+';
                    if ($s >= 70) return 'B';
                    if ($s >= 65) return 'B-';
                    if ($s >= 60) return 'C+';
                    if ($s >= 55) return 'C';
                    if ($s >= 40) return 'D';
                    return 'E';
                };

                $getGradeClass = function($letter) {
                    if (str_starts_with($letter, 'A')) return 'grade-pill-a';
                    if (str_starts_with($letter, 'B')) return 'grade-pill-b';
                    if (str_starts_with($letter, 'C')) return 'grade-pill-c';
                    if (in_array($letter, ['D', 'E'])) return 'grade-pill-d';
                    return 'grade-pill-pending';
                };

                // Susun dataset lengkap
                $gradeItems = collect();

                foreach ($dbSubmissions as $sub) {
                    $score = $sub->grade ? (float) $sub->grade->score : null;
                    $letter = $getGradeLetter($score);
                    $gradeItems->push([
                        'id' => $sub->id,
                        'student_name' => $sub->student?->name ?? 'Mahasiswa',
                        'student_nim' => $sub->student?->nim_nip ?? '10221000',
                        'course_code' => $sub->assignment?->course?->code ?? 'MK001',
                        'course_name' => $sub->assignment?->course?->name ?? 'Mata Kuliah',
                        'lecturer_name' => $sub->assignment?->course?->lecturer?->name ?? ($sub->grade?->grader?->name ?? 'Dosen Pengampu'),
                        'assignment_title' => $sub->assignment?->title ?? 'Tugas Kuliah',
                        'submitted_at' => $sub->submitted_at ? \Carbon\Carbon::parse($sub->submitted_at)->format('d M Y, H:i') : '-',
                        'is_late' => (bool) $sub->is_late,
                        'score' => $score !== null ? number_format($score, 1) : null,
                        'letter' => $letter,
                        'letter_class' => $getGradeClass($letter),
                        'status' => $sub->grade ? 'graded' : 'pending',
                        'feedback' => $sub->grade?->feedback ?? 'Belum ada catatan umpan balik.',
                        'graded_at' => $sub->grade?->graded_at ? \Carbon\Carbon::parse($sub->grade->graded_at)->format('d M Y, H:i') : '-',
                        'file_name' => $sub->original_name ?? 'tugas_mahasiswa.pdf',
                        'file_size' => $sub->file_size ? number_format($sub->file_size / 1024, 1) . ' KB' : '1.4 MB',
                    ]);
                }

                // Kalkulasi statistik KPI murni dari database
                $totalCount = $gradeItems->count();
                $gradedCount = $gradeItems->where('status', 'graded')->count();
                $pendingCount = $gradeItems->where('status', 'pending')->count();
                $gradedItems = $gradeItems->where('status', 'graded');
                $avgScore = $gradedItems->isNotEmpty() ? round($gradedItems->avg(fn($i) => (float)$i['score']), 1) : 0;
                $pctGraded = $totalCount > 0 ? round(($gradedCount / $totalCount) * 100) : 0;

                // Distribusi predikat
                $countA = $gradeItems->filter(fn($i) => str_starts_with($i['letter'], 'A'))->count();
                $countB = $gradeItems->filter(fn($i) => str_starts_with($i['letter'], 'B'))->count();

                // Daftar Mata Kuliah Unik untuk Filter
                $uniqueCourses = $gradeItems->pluck('course_name', 'course_code')->unique();

                $avatarColors = [
                    ['bg' => 'rgba(3, 159, 250, 0.1)', 'text' => '#039FFA'],
                    ['bg' => 'rgba(16, 185, 129, 0.14)', 'text' => '#059669'],
                    ['bg' => 'rgba(245, 158, 11, 0.12)', 'text' => '#D97706'],
                    ['bg' => 'rgba(139, 92, 246, 0.12)', 'text' => '#7C3AED'],
                    ['bg' => 'rgba(6, 182, 212, 0.12)', 'text' => '#0891B2'],
                ];
            @endphp

            <!-- Baris Statistik Cepat (Identik dengan materi-stats-bar) -->
            <section class="nilai-stats-bar">
                <!-- Stat 1: Rata-Rata Nilai -->
                <div class="nilai-stat-card">
                    <div class="nilai-stat-icon icon-blue">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20V10"></path>
                            <path d="M18 20V4"></path>
                            <path d="M6 20v-4"></path>
                        </svg>
                    </div>
                    <div class="nilai-stat-info">
                        <span class="nilai-stat-label">Rata-rata Nilai</span>
                        <span class="nilai-stat-value">{{ $avgScore }}</span>
                    </div>
                </div>

                <!-- Stat 2: Progres Penilaian -->
                <div class="nilai-stat-card">
                    <div class="nilai-stat-icon icon-green">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <div class="nilai-stat-info">
                        <span class="nilai-stat-label">Ternilai</span>
                        <span class="nilai-stat-value">{{ $pctGraded }}% <small style="font-size:12px;font-weight:700;color:var(--admin-muted);">({{ $gradedCount }}/{{ $totalCount }})</small></span>
                    </div>
                </div>

                <!-- Stat 3: Menunggu Penilaian -->
                <div class="nilai-stat-card">
                    <div class="nilai-stat-icon icon-orange">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="nilai-stat-info">
                        <span class="nilai-stat-label">Menunggu Dinilai</span>
                        <span class="nilai-stat-value">{{ $pendingCount }}</span>
                    </div>
                </div>

                <!-- Stat 4: Predikat Tertinggi -->
                <div class="nilai-stat-card">
                    <div class="nilai-stat-icon icon-gold">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    </div>
                    <div class="nilai-stat-info">
                        <span class="nilai-stat-label">Predikat A &bull; B</span>
                        <span class="nilai-stat-value">{{ $countA }} <small style="font-size:12px;font-weight:700;color:var(--admin-muted);">&bull; {{ $countB }}</small></span>
                    </div>
                </div>
            </section>

            <!-- Toolbar Filter & Pencarian (Identik dengan materi-filter-toolbar) -->
            <section class="nilai-filter-toolbar">
                <!-- Input Pencarian (Di Kiri) -->
                <div class="nilai-search-box">
                    <svg class="nilai-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="searchNilaiInput" placeholder="Cari nama mahasiswa, NIM, atau tugas...">
                </div>

                <!-- Kelompok Filter Dropdowns (Di Kanan) -->
                <div class="toolbar-right-filters">
                    <!-- Dropdown Mata Kuliah -->
                    <div class="filter-select-wrapper" style="min-width: 220px;">
                        <select id="filterCourseSelect">
                            <option value="all">Semua Mata Kuliah ({{ $uniqueCourses->count() }})</option>
                            @foreach ($uniqueCourses as $code => $name)
                                <option value="{{ $code }}">{{ $code }} &bull; {{ $name }}</option>
                            @endforeach
                        </select>
                        <div class="filter-select-arrow">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </div>

                    <!-- Dropdown Status -->
                    <div class="filter-select-wrapper filter-type-wrapper">
                        <select id="filterStatusSelect">
                            <option value="all">Semua Status</option>
                            <option value="graded">Sudah Dinilai</option>
                            <option value="pending">Menunggu Dinilai</option>
                        </select>
                        <div class="filter-select-arrow">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </div>

                    <!-- Dropdown Predikat -->
                    <div class="filter-select-wrapper filter-type-wrapper">
                        <select id="filterGradeSelect">
                            <option value="all">Semua Predikat</option>
                            <option value="A">Predikat A (&ge;80)</option>
                            <option value="B">Predikat B (65 - 79)</option>
                            <option value="C">Predikat C (55 - 64)</option>
                            <option value="remedial">Remedial (&lt;55)</option>
                        </select>
                        <div class="filter-select-arrow">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section Utama: Card Tabel Rekap Nilai -->
            <section>
                <div class="section-card">

                    <!-- Tabel Scrollable dengan Sticky Header -->
                    <div class="table-scrollable">
                        <table class="custom-admin-table" id="nilaiTable">
                            <thead class="table-header-sticky">
                                <tr>
                                    <th>Mahasiswa</th>
                                    <th>NIM</th>
                                    <th>Mata Kuliah</th>
                                    <th>Tugas Kuliah</th>
                                    <th style="text-align: center;">Nilai</th>
                                    <th>Feedback Dosen</th>
                                    <th style="text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="nilaiTableBody">
                                @forelse ($gradeItems as $idx => $item)
                                    <tr class="nilai-row"
                                        data-name="{{ strtolower($item['student_name']) }}"
                                        data-nim="{{ $item['student_nim'] }}"
                                        data-assignment="{{ strtolower($item['assignment_title']) }}"
                                        data-course-code="{{ $item['course_code'] }}"
                                        data-status="{{ $item['status'] }}"
                                        data-letter="{{ $item['letter'] }}"
                                        data-score="{{ $item['score'] ?? '' }}"
                                        data-item="{{ htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') }}">
                                        
                                        <!-- Mahasiswa -->
                                        <td>
                                            <span class="user-name">{{ $item['student_name'] }}</span>
                                        </td>

                                        <!-- NIM -->
                                        <td>
                                            <span class="user-id" style="font-size: 12px; font-weight: 600;">{{ $item['student_nim'] }}</span>
                                        </td>

                                        <!-- Mata Kuliah: Hanya Nama MK dan Dosen -->
                                        <td>
                                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                                <span class="td-mk-name" style="font-size: 12px; font-weight: 700; color: var(--admin-text);" title="{{ $item['course_name'] }}">{{ $item['course_name'] }}</span>
                                                <span style="font-size: 10.5px; color: var(--admin-muted);">{{ $item['lecturer_name'] }}</span>
                                            </div>
                                        </td>

                                        <!-- Tugas Kuliah: Nama Tugas dan Waktu dengan Keterangan dalam Kurung -->
                                        <td>
                                            <div style="display: flex; flex-direction: column; gap: 2px; max-width: 220px;">
                                                <span style="font-size: 12px; font-weight: 700; color: var(--admin-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $item['assignment_title'] }}">{{ $item['assignment_title'] }}</span>
                                                <span style="font-size: 10.5px; color: var(--admin-muted);">
                                                    {{ $item['submitted_at'] }} ({{ $item['is_late'] ? 'Terlambat' : 'Tepat Waktu' }})
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Nilai -->
                                        <td style="text-align: center;">
                                            @if ($item['score'] !== null)
                                                <span style="font-size: 13px; font-weight: 800; color: var(--admin-text);">
                                                    {{ $item['score'] }} ({{ $item['letter'] }})
                                                </span>
                                            @else
                                                <span style="font-size: 12px; color: #94A3B8; font-weight: 700;">-</span>
                                            @endif
                                        </td>

                                        <!-- Feedback Dosen -->
                                        <td>
                                            <div style="max-width: 170px; font-size: 11px; color: #475569; font-style: italic; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $item['feedback'] }}">
                                                @if ($item['status'] === 'graded')
                                                    "{{ $item['feedback'] }}"
                                                @else
                                                    <span style="color: #94A3B8; font-style: normal;">Menunggu evaluasi</span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Status -->
                                        <td style="text-align: center;">
                                            @if ($item['status'] === 'graded')
                                                <span class="badge-status badge-status-active">
                                                    ✓ Dinilai
                                                </span>
                                            @else
                                                <span class="badge-status badge-role-dosen">
                                                    Menunggu
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="emptyDbRow">
                                        <td colspan="7" style="text-align: center; padding: 48px 20px; color: #94A3B8;">
                                            <div style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
                                                <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(3, 159, 250, 0.08); display: flex; align-items: center; justify-content: center; color: var(--admin-primary); font-size: 22px;">
                                                    📝
                                                </div>
                                                <span style="font-size: 14px; font-weight: 800; color: #0F172A;">Belum Ada Data Pengumpulan &amp; Nilai</span>
                                                <span style="font-size: 12px; color: #64748B; max-width: 420px; line-height: 1.5;">Saat ini belum ada riwayat tugas yang dikumpulkan mahasiswa atau dievaluasi oleh dosen pengampu di database.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse

                                <tr id="emptySearchRow" style="display: none;">
                                    <td colspan="7" style="text-align: center; padding: 36px 20px; color: #94A3B8;">
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="11" cy="11" r="8"></circle>
                                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                            </svg>
                                            <span style="font-size: 13px; font-weight: 700; color: #475569;">Tidak ada data penilaian yang cocok dengan filter</span>
                                            <span style="font-size: 11.5px; color: #94A3B8;">Coba ubah kata kunci pencarian atau sesuaikan opsi filter di atas.</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>

        <x-footer />
    </div>

    <!-- MODAL DETAIL LEMBAR NILAI & FEEDBACK -->
    <!-- Desain Bersih: Hanya Tombol Tutup 'x' di Header, Tanpa Tombol Tutup Ganda di Bawah -->
    <div class="modal-overlay" id="modalGradeDetail">
        <div class="modal-content-nilai">
            <div class="modal-header-nilai">
                <h3>
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="7"></circle>
                        <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                    </svg>
                    Lembar Penilaian &amp; Evaluasi Mahasiswa
                </h3>
                <button type="button" class="btn-close-modal" id="btnCloseModal" title="Tutup Modal">&times;</button>
            </div>
            
            <div class="modal-body-nilai">
                <!-- Hero Skor & Mahasiswa -->
                <div class="grade-sheet-hero">
                    <div class="sheet-student-info">
                        <div class="user-avatar" id="modalAvatar" style="width: 42px; height: 42px; font-size: 14px; background: rgba(3, 159, 250, 0.1); color: var(--admin-primary);">
                            BS
                        </div>
                        <div>
                            <h4 id="modalStudentName" style="margin: 0; font-size: 14px; font-weight: 800; color: #0F172A;">Budi Santoso</h4>
                            <span id="modalStudentNim" style="font-size: 11.5px; color: var(--admin-muted);">NIM: 10221001</span>
                        </div>
                    </div>
                    <div class="sheet-score-badge">
                        <span class="sheet-score-val" id="modalScoreVal">92.5</span>
                        <div style="display: flex; align-items: center; gap: 5px; margin-top: 2px;">
                            <span style="font-size: 11px; color: var(--admin-muted); font-weight: 700;">Predikat:</span>
                            <span class="grade-pill grade-pill-a" id="modalGradePill">A</span>
                        </div>
                    </div>
                </div>

                <!-- Meta Informasi Perkuliahan -->
                <div class="sheet-meta-grid">
                    <div class="meta-field-box">
                        <div class="meta-field-label">Mata Kuliah</div>
                        <div class="meta-field-val" id="modalCourse">SI2514024 &bull; Pemrograman Web</div>
                    </div>
                    <div class="meta-field-box">
                        <div class="meta-field-label">Dosen Penilai</div>
                        <div class="meta-field-val" id="modalLecturer">Dr. Hendra Gunawan, M.T.</div>
                    </div>
                    <div class="meta-field-box">
                        <div class="meta-field-label">Tugas Kuliah</div>
                        <div class="meta-field-val" id="modalAssignment">Tugas 1: Implementasi Blade Template</div>
                    </div>
                    <div class="meta-field-box">
                        <div class="meta-field-label">Waktu Pengumpulan</div>
                        <div class="meta-field-val" id="modalSubmittedAt">04 Okt 2026, 21:15 (Tepat Waktu)</div>
                    </div>
                </div>

                <!-- Box Umpan Balik Dosen -->
                <div class="feedback-detail-box" id="modalFeedbackBox">
                    <div class="feedback-detail-header">
                        <span style="display: flex; align-items: center; gap: 5px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                            Catatan Umpan Balik &amp; Evaluasi Dosen
                        </span>
                        <span id="modalGradedAt" style="font-size: 10.5px; font-weight: 600; color: #15803D;">05 Okt 2026, 14:20</span>
                    </div>
                    <p class="feedback-detail-text" id="modalFeedbackText">
                        Implementasi arsitektur Blade rapi, pemisahan komponen dan styling terstruktur sangat baik.
                    </p>
                </div>

                <!-- Berkas yang Dikumpulkan -->
                <div class="submission-file-box">
                    <div class="file-info-group">
                        <div class="file-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                        </div>
                        <div>
                            <div class="file-name" id="modalFileName">Tugas1_BudiSantoso_10221001.zip</div>
                            <div class="file-size" id="modalFileSize">Ukuran berkas: 2.4 MB</div>
                        </div>
                    </div>
                    <button type="button" class="btn-action-view" id="btnDownloadFile" style="padding: 6px 12px; font-size: 11px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        Unduh Berkas
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notifikasi Ringan -->
    <div class="toast-notification" id="toastNilai">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
        <span id="toastNilaiText">Berhasil mengunduh rekap nilai.</span>
    </div>

    <!-- Interaktivitas Frontend & Filter Realtime -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchNilaiInput');
            const courseSelect = document.getElementById('filterCourseSelect');
            const statusSelect = document.getElementById('filterStatusSelect');
            const gradeSelect = document.getElementById('filterGradeSelect');
            const badgeTotalCount = document.getElementById('badgeTotalCount');
            const rows = document.querySelectorAll('.nilai-row');
            const emptyRow = document.getElementById('emptySearchRow');

            function applyFilters() {
                const searchVal = searchInput.value.trim().toLowerCase();
                const courseVal = courseSelect.value;
                const statusVal = statusSelect.value;
                const gradeVal = gradeSelect.value;

                let visibleCount = 0;

                rows.forEach(row => {
                    const name = row.getAttribute('data-name');
                    const nim = row.getAttribute('data-nim');
                    const assignment = row.getAttribute('data-assignment');
                    const courseCode = row.getAttribute('data-course-code');
                    const status = row.getAttribute('data-status');
                    const letter = row.getAttribute('data-letter');

                    const matchSearch = !searchVal || 
                        name.includes(searchVal) || 
                        nim.includes(searchVal) || 
                        assignment.includes(searchVal);

                    const matchCourse = (courseVal === 'all') || (courseCode === courseVal);
                    const matchStatus = (statusVal === 'all') || (status === statusVal);

                    let matchGrade = true;
                    if (gradeVal === 'A') {
                        matchGrade = letter.startsWith('A');
                    } else if (gradeVal === 'B') {
                        matchGrade = letter.startsWith('B');
                    } else if (gradeVal === 'C') {
                        matchGrade = letter.startsWith('C');
                    } else if (gradeVal === 'remedial') {
                        matchGrade = (letter === 'D' || letter === 'E');
                    }

                    if (matchSearch && matchCourse && matchStatus && matchGrade) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (badgeTotalCount) {
                    badgeTotalCount.textContent = `Menampilkan ${visibleCount} dari ${rows.length} Pengumpulan`;
                }
                emptyRow.style.display = visibleCount === 0 ? '' : 'none';
            }

            searchInput.addEventListener('input', applyFilters);
            courseSelect.addEventListener('change', applyFilters);
            statusSelect.addEventListener('change', applyFilters);
            gradeSelect.addEventListener('change', applyFilters);

            // Modal Detail Lembar Nilai
            const modal = document.getElementById('modalGradeDetail');
            const btnCloseModal = document.getElementById('btnCloseModal');

            function openModal(itemData) {
                document.getElementById('modalStudentName').textContent = itemData.student_name;
                document.getElementById('modalStudentNim').textContent = 'NIM: ' + itemData.student_nim;
                
                // Initials
                const parts = itemData.student_name.trim().split(' ');
                const initials = (parts[0] ? parts[0][0] : 'M') + (parts[1] ? parts[1][0] : '');
                document.getElementById('modalAvatar').textContent = initials.toUpperCase();

                // Score & Letter
                if (itemData.score !== null && itemData.score !== '') {
                    document.getElementById('modalScoreVal').textContent = itemData.score;
                    const pill = document.getElementById('modalGradePill');
                    pill.textContent = itemData.letter;
                    pill.className = 'grade-pill ' + itemData.letter_class;
                } else {
                    document.getElementById('modalScoreVal').textContent = '-';
                    const pill = document.getElementById('modalGradePill');
                    pill.textContent = 'Belum Dinilai';
                    pill.className = 'grade-pill grade-pill-pending';
                }

                document.getElementById('modalCourse').textContent = itemData.course_code + ' • ' + itemData.course_name;
                document.getElementById('modalLecturer').textContent = itemData.lecturer_name;
                document.getElementById('modalAssignment').textContent = itemData.assignment_title;
                document.getElementById('modalSubmittedAt').textContent = itemData.submitted_at + (itemData.is_late ? ' (Terlambat)' : ' (Tepat Waktu)');

                document.getElementById('modalGradedAt').textContent = itemData.graded_at !== '-' ? 'Dinilai: ' + itemData.graded_at : 'Menunggu Evaluasi';
                document.getElementById('modalFeedbackText').textContent = itemData.feedback;

                document.getElementById('modalFileName').textContent = itemData.file_name;
                document.getElementById('modalFileSize').textContent = 'Ukuran berkas: ' + itemData.file_size;

                modal.classList.add('active');
            }

            function closeModal() {
                modal.classList.remove('active');
            }

            document.querySelectorAll('.nilai-row').forEach(row => {
                row.addEventListener('click', function () {
                    const rawData = this.getAttribute('data-item');
                    try {
                        const itemData = JSON.parse(rawData);
                        openModal(itemData);
                    } catch (e) {
                        console.error('Failed to parse item data', e);
                    }
                });
            });

            btnCloseModal.addEventListener('click', closeModal);

            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && modal.classList.contains('active')) {
                    closeModal();
                }
            });

            function showToast(msg) {
                const toast = document.getElementById('toastNilai');
                document.getElementById('toastNilaiText').textContent = msg;
                toast.classList.add('show');
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 3000);
            }

            document.getElementById('btnDownloadFile').addEventListener('click', function () {
                const fileName = document.getElementById('modalFileName').textContent;
                showToast(`Memulai pengunduhan berkas: ${fileName}`);
            });

            // Ekspor CSV
            document.getElementById('btnExportCsv').addEventListener('click', function () {
                let csvRows = [];
                csvRows.push([
                    'No',
                    'NIM',
                    'Nama Mahasiswa',
                    'Kode MK',
                    'Mata Kuliah',
                    'Dosen Pengampu',
                    'Tugas Kuliah',
                    'Waktu Pengumpulan',
                    'Status Waktu',
                    'Nilai Angka',
                    'Predikat Huruf',
                    'Status Penilaian',
                    'Feedback Dosen'
                ].map(val => `"${val}"`).join(','));

                let visibleIdx = 1;
                rows.forEach(row => {
                    if (row.style.display !== 'none') {
                        try {
                            const data = JSON.parse(row.getAttribute('data-item'));
                            csvRows.push([
                                visibleIdx++,
                                data.student_nim,
                                data.student_name,
                                data.course_code,
                                data.course_name,
                                data.lecturer_name,
                                data.assignment_title,
                                data.submitted_at,
                                data.is_late ? 'Terlambat' : 'Tepat Waktu',
                                data.score ?? '-',
                                data.letter,
                                data.status === 'graded' ? 'Sudah Dinilai' : 'Menunggu Penilaian',
                                data.feedback.replace(/"/g, '""')
                            ].map(val => `"${val}"`).join(','));
                        } catch (e) {}
                    }
                });

                const csvString = '\uFEFF' + csvRows.join('\r\n');
                const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `rekap_nilai_mahasiswa_${new Date().toISOString().slice(0, 10)}.csv`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);

                showToast('Berkas rekap nilai CSV berhasil diunduh!');
            });
        });
    </script>
</body>
</html>
