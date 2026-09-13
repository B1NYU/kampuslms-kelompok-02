<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian & Feedback — Portal Dosen KampusLMS</title>

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

    <div class="app-window">

        <!-- Navbar Khusus Dosen -->
        <x-navbar-dosen />

        <!-- Konten Utama Penilaian & Feedback -->
        <main class="dosen-content">

            <!-- Topbar Header -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <span class="page-eyebrow">
                            <span class="page-eyebrow-dot"></span>
                            PORTAL DOSEN • EVALUASI &amp; GRADING TUGAS
                        </span>
                        <h1>4. Penilaian &amp; Umpan Balik (Feedback) Mahasiswa</h1>
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

            <!-- Section: Penilaian & Feedback -->
            <section class="feature-section" id="penilaian-tugas">
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
                                <h2>Penilaian &amp; Umpan Balik (Feedback) Pengumpulan Mahasiswa</h2>
                                <p>Evaluasi kiriman mahasiswa, berikan skor angka (0-100), dan cantumkan catatan konstruktif.</p>
                            </div>
                        </div>
                        <span class="section-header-badge">Evaluasi &amp; Grading</span>
                    </div>

                    <!-- Filter & Summary Bar -->
                    <div class="table-header-tools">
                        <div style="display: flex; gap: 8px;">
                            <button class="btn-secondary-action filter-submission-btn active" data-sub-filter="all">Semua Pengumpulan</button>
                            <button class="btn-secondary-action filter-submission-btn" data-sub-filter="pending">Belum Dinilai</button>
                            <button class="btn-secondary-action filter-submission-btn" data-sub-filter="graded">Sudah Dinilai</button>
                        </div>

                        <div style="font-size: 12px; font-weight: 800; color: #8E6570;">
                            Status: <span style="color:#1B8A5A;" id="gradedSummary">2 Dinilai</span> &middot; <span style="color:#C98A1F;" id="ungradedSummary">3 Menunggu Review</span>
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
                                        <span class="feedback-text item-feedback" title="Struktur relasi tabel user dan course sudah rapi. Perhatikan foreign key indexing.">
                                            "Struktur relasi tabel user dan course sudah rapi..."
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <button class="btn-primary-action btn-grade-action" data-id="1" data-student="Baihaqi Abimanyu" data-title="Tugas 02: Desain Schema Database" data-score="92" data-feedback="Struktur relasi tabel user dan course sudah rapi. Perhatikan foreign key indexing." style="padding: 6px 14px; font-size: 11px;">
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
                                        <a href="#" onclick="alert('Membuka lampiran berkas: schema_calvin.sql'); return false;" class="btn-open-resource" style="font-size:11px;">
                                            📄 schema_calvin.sql
                                        </a>
                                    </td>
                                    <td>
                                        <span class="score-badge score-badge-graded item-score">88 / 100</span>
                                    </td>
                                    <td>
                                        <span class="feedback-text item-feedback" title="Normalisasi 3NF terpenuhi dengan sangat baik.">
                                            "Normalisasi 3NF terpenuhi dengan sangat baik."
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <button class="btn-primary-action btn-grade-action" data-id="2" data-student="Calvin Adithya" data-title="Tugas 02: Desain Schema Database" data-score="88" data-feedback="Normalisasi 3NF terpenuhi dengan sangat baik." style="padding: 6px 14px; font-size: 11px;">
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
                                    <td><span class="badge-status badge-status-active">Tepat Waktu (13 Sep, 19:12)</span></td>
                                    <td>
                                        <a href="#" onclick="alert('Membuka berkas: erd_kampuslms_clara.png'); return false;" class="btn-open-resource" style="font-size:11px;">
                                            🖼️ erd_clara.png
                                        </a>
                                    </td>
                                    <td>
                                        <span class="score-badge score-badge-pending item-score">Belum Dinilai</span>
                                    </td>
                                    <td>
                                        <span class="feedback-text item-feedback" style="color: #A37F89; font-style: italic;">
                                            Belum ada catatan umpan balik.
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <button class="btn-primary-action btn-grade-action" data-id="3" data-student="Clara Shinta" data-title="Tugas 02: Desain Schema Database" data-score="" data-feedback="" style="padding: 6px 14px; font-size: 11px; background:#1B8A5A;">
                                            Beri Nilai
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
                                    <td><span class="badge-status badge-status-active">Tepat Waktu (13 Sep, 20:30)</span></td>
                                    <td>
                                        <a href="#" onclick="alert('Membuka berkas: migration_desta.zip'); return false;" class="btn-open-resource" style="font-size:11px;">
                                            📦 migration_desta.zip
                                        </a>
                                    </td>
                                    <td>
                                        <span class="score-badge score-badge-pending item-score">Belum Dinilai</span>
                                    </td>
                                    <td>
                                        <span class="feedback-text item-feedback" style="color: #A37F89; font-style: italic;">
                                            Belum ada catatan umpan balik.
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <button class="btn-primary-action btn-grade-action" data-id="4" data-student="Desta Arkan" data-title="Tugas 02: Desain Schema Database" data-score="" data-feedback="" style="padding: 6px 14px; font-size: 11px; background:#1B8A5A;">
                                            Beri Nilai
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
                                    <td><span class="badge-status badge-status-warning">Terlambat 12 Menit</span></td>
                                    <td>
                                        <a href="#" onclick="alert('Membuka lampiran berkas: database_devina.pdf'); return false;" class="btn-open-resource" style="font-size:11px;">
                                            📄 database_devina.pdf
                                        </a>
                                    </td>
                                    <td>
                                        <span class="score-badge score-badge-pending item-score">Belum Dinilai</span>
                                    </td>
                                    <td>
                                        <span class="feedback-text item-feedback" style="color: #A37F89; font-style: italic;">
                                            Belum ada catatan umpan balik.
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <button class="btn-primary-action btn-grade-action" data-id="5" data-student="Devina Putri" data-title="Tugas 02: Desain Schema Database" data-score="" data-feedback="" style="padding: 6px 14px; font-size: 11px; background:#1B8A5A;">
                                            Beri Nilai
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

        </main>
    </div>

    <!-- MODAL PENILAIAN & FEEDBACK -->
    <div class="dosen-modal-overlay" id="gradingModalOverlay">
        <div class="dosen-modal-card" id="gradingModal">
            <div class="dosen-modal-header">
                <h3>Form Penilaian &amp; Umpan Balik Dosen</h3>
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
                    <label for="inputFeedback">Catatan &amp; Umpan Balik (Feedback) Konstruktif <span class="required">*</span></label>
                    <textarea id="inputFeedback" class="form-textarea" rows="4" placeholder="Tuliskan ulasan spesifik mengenai pengumpulan ini, kelebihan kode, serta aspek yang perlu ditingkatkan oleh mahasiswa..."></textarea>
                </div>
            </div>
            <div class="dosen-modal-footer">
                <button type="button" class="btn-secondary-action" id="btnCancelGrading">Batal</button>
                <button type="button" class="btn-primary-action" id="btnSaveGrading">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Simpan Nilai &amp; Berikan Feedback
                </button>
            </div>
        </div>
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

            const modalOverlay = document.getElementById('gradingModalOverlay');
            const btnCloseGradingModal = document.getElementById('btnCloseGradingModal');
            const btnCancelGrading = document.getElementById('btnCancelGrading');
            const btnSaveGrading = document.getElementById('btnSaveGrading');
            const modalAssignmentTitle = document.getElementById('modalAssignmentTitle');
            const modalStudentName = document.getElementById('modalStudentName');
            const inputScore = document.getElementById('inputScore');
            const inputFeedback = document.getElementById('inputFeedback');

            let activeSubmissionId = null;

            function attachGradingButtons() {
                const gradeBtns = document.querySelectorAll('.btn-grade-action');
                gradeBtns.forEach(btn => {
                    btn.onclick = function() {
                        activeSubmissionId = btn.getAttribute('data-id');
                        const student = btn.getAttribute('data-student');
                        const title = btn.getAttribute('data-title');
                        const score = btn.getAttribute('data-score') || '';
                        const feedback = btn.getAttribute('data-feedback') || '';

                        modalAssignmentTitle.textContent = title;
                        modalStudentName.textContent = `Mahasiswa: ${student}`;
                        inputScore.value = score;
                        inputFeedback.value = feedback;

                        modalOverlay.style.display = 'flex';
                        inputScore.focus();
                    };
                });
            }
            attachGradingButtons();

            function closeModal() {
                modalOverlay.style.display = 'none';
                activeSubmissionId = null;
            }

            btnCloseGradingModal.addEventListener('click', closeModal);
            btnCancelGrading.addEventListener('click', closeModal);

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
                    actionBtn.setAttribute('data-score', score);
                    actionBtn.setAttribute('data-feedback', feedback);
                }

                const pendingCount = document.querySelectorAll('#submissionTableBody tr[data-status="pending"]').length;
                const gradedCount = document.querySelectorAll('#submissionTableBody tr[data-status="graded"]').length;
                const ungradedSummary = document.getElementById('ungradedSummary');
                const gradedSummary = document.getElementById('gradedSummary');

                if (ungradedSummary) ungradedSummary.textContent = `${pendingCount} Menunggu Review`;
                if (gradedSummary) gradedSummary.textContent = `${gradedCount} Dinilai`;

                closeModal();
                showToast(`Nilai (${score}) & Feedback berhasil disimpan untuk ${modalStudentName.textContent}!`);
            });

            const filterSubBtns = document.querySelectorAll('.filter-submission-btn');
            const subRows = document.querySelectorAll('#submissionTableBody tr');

            filterSubBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    filterSubBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');

                    const filter = btn.getAttribute('data-sub-filter');
                    subRows.forEach(row => {
                        const status = row.getAttribute('data-status');
                        if (filter === 'all' || status === filter) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            });

        });
    </script>
</body>

</html>
