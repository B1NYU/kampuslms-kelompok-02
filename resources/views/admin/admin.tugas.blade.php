<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Tugas Perkuliahan — KampusLMS Admin</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/admin/admin.tugas.css', 'resources/css/dosen/dosen.tugas.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/admin/admin.tugas.css') }}">
        <link rel="stylesheet" href="{{ asset('css/dosen/dosen.tugas.css') }}">
    @endif
</head>
<body>
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <div class="app-window">

        <!-- Navbar Khusus Admin -->
        <x-navbar-admin />

        <!-- Konten Utama Manajemen Tugas Admin -->
        <main class="admin-content">

            <!-- Topbar Header -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <h1>Manajemen Tugas Perkuliahan</h1>
                    </div>
                </div>
                <div class="topbar-right">
                    <button type="button" class="btn-quick-action" id="btnOpenAddModal">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Buat Tugas Baru</span>
                    </button>
                </div>
            </header>

            <!-- Baris Statistik Metrik Cepat -->
            <section class="tugas-stats-bar">
                <div class="tugas-stat-card">
                    <div class="tugas-stat-icon icon-blue">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <div class="tugas-stat-info">
                        <span class="tugas-stat-label">Total Penugasan</span>
                        <span class="tugas-stat-value" id="statTotalTugas">{{ $totalAssignments }}</span>
                    </div>
                </div>

                <div class="tugas-stat-card">
                    <div class="tugas-stat-icon icon-green">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div class="tugas-stat-info">
                        <span class="tugas-stat-label">Dipublikasikan</span>
                        <span class="tugas-stat-value" id="statPublishedTugas">{{ $totalPublished }}</span>
                    </div>
                </div>

                <div class="tugas-stat-card">
                    <div class="tugas-stat-icon icon-gold">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <div class="tugas-stat-info">
                        <span class="tugas-stat-label">Draf Tugas</span>
                        <span class="tugas-stat-value" id="statDraftTugas">{{ $totalDraft }}</span>
                    </div>
                </div>

                <div class="tugas-stat-card">
                    <div class="tugas-stat-icon icon-orange">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <div class="tugas-stat-info">
                        <span class="tugas-stat-label">Pengumpulan Masuk</span>
                        <span class="tugas-stat-value" id="statTotalSubmissions">{{ $totalSubmissions }} Berkas</span>
                    </div>
                </div>
            </section>

            <!-- Toolbar Filter & Pencarian -->
            <section class="tugas-filter-toolbar">
                <!-- Input Pencarian (Di Kiri) -->
                <div class="tugas-search-box">
                    <svg class="tugas-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="inputSearchTugas" placeholder="Cari judul tugas, mata kuliah, atau instruksi...">
                </div>

                <!-- Kelompok Filter Dropdowns (Di Kanan) -->
                <div class="toolbar-right-filters">
                    <!-- Custom Dropdown Filter MK dengan Scrollbar -->
                    <div class="custom-filter-dropdown" id="courseFilterDropdown">
                        <button type="button" class="filter-dropdown-btn" id="courseFilterBtn">
                            <span class="filter-dropdown-text" id="courseFilterText">Semua Mata Kuliah ({{ $coursesList->count() }} Terdaftar)</span>
                            <svg class="filter-dropdown-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div class="filter-dropdown-menu" id="courseFilterMenu">
                            <ul class="filter-options-scroll" id="courseFilterOptions">
                                <li class="filter-option-item active" data-value="all" data-label="Semua Mata Kuliah ({{ $coursesList->count() }} Terdaftar)">
                                    <span class="filter-option-name">Semua Mata Kuliah ({{ $coursesList->count() }} Terdaftar)</span>
                                </li>
                                @foreach ($coursesList as $course)
                                    <li class="filter-option-item" data-value="{{ $course->id }}" data-label="{{ $course->code }} • {{ $course->name }}">
                                        <span class="filter-option-code">{{ $course->code }}</span>
                                        <span class="filter-option-name">{{ $course->name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <input type="hidden" id="selectFilterCourse" value="all">
                    </div>

                    <!-- Dropdown Filter Status Tugas -->
                    <div class="filter-select-wrapper">
                        <select id="selectFilterStatus">
                            <option value="all">Semua Status</option>
                            <option value="published">🟢 Dipublikasikan</option>
                            <option value="draft">⚪ Draf Tugas</option>
                        </select>
                        <div class="filter-select-arrow">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Grid Kartu Tugas -->
            <section class="assignments-grid" id="tugasCardsGrid">
                @forelse ($assignments as $t)
                    @php
                        $isPublished = $t->status === 'published';
                        $isPast = $t->due_at->isPast();
                        $isSoon = !$isPast && $t->due_at->isBefore(now()->addHours(48));

                        if (!$isPublished) {
                            $badgeClass = 'badge-status-draft';
                            $badgeText = 'Draft';
                            $badgeIcon = '○';
                        } elseif ($isPast) {
                            $badgeClass = 'badge-status-published';
                            $badgeText = 'Deadline Berakhir';
                            $badgeIcon = '●';
                        } elseif ($isSoon) {
                            $badgeClass = 'badge-status-warning';
                            $badgeText = 'Mendekati Deadline';
                            $badgeIcon = '●';
                        } else {
                            $badgeClass = 'badge-status-published';
                            $badgeText = 'Tugas Aktif';
                            $badgeIcon = '●';
                        }

                        // Penyesuaian informasi mode dosen: Pertemuan & Progress Pengumpulan
                        preg_match('/(Pertemuan|Minggu)\s*(\d+)/i', $t->title, $matches);
                        $weekNum = $matches[2] ?? null;

                        $studentCount = $t->course ? $t->course->students()->count() : 0;
                        $pct = $studentCount > 0 ? min(100, round($t->submissions_count / $studentCount * 100)) : 0;

                        $creatorName = $t->creator?->name ?? 'Administrator';
                        $creatorInitials = strtoupper(substr($creatorName, 0, 2));
                    @endphp

                    <div class="assignment-card"
                         data-id="{{ $t->id }}"
                         data-course-id="{{ $t->course_id }}"
                         data-course-code="{{ $t->course?->code ?? '' }}"
                         data-course-name="{{ $t->course?->name ?? '' }}"
                         data-course-lecturer="{{ $t->course?->lecturer?->name ?? 'Belum Ditugaskan' }}"
                         data-course-sks="{{ $t->course?->sks ?? 3 }}"
                         data-status="{{ $t->status }}"
                         data-title="{{ $t->title }}"
                         data-session="{{ $weekNum ?? '' }}"
                         data-instructions="{{ $t->instructions }}"
                         data-due-date="{{ $t->due_at->format('Y-m-d') }}"
                         data-due-time="{{ $t->due_at->format('H:i') }}"
                         data-score="{{ $t->max_score }}"
                         data-allow-late="{{ $t->allow_late ? 1 : 0 }}"
                         data-submissions="{{ $t->submissions_count }}">

                        <div>
                            <div class="assignment-card-top">
                                <div class="badge-tag-wrap">
                                    <span class="badge-mk-code">{{ $t->course?->code ?? 'MK' }}</span>
                                    @if ($weekNum)
                                        <span class="badge-mk-code" style="background: rgba(3, 159, 250, 0.1); color: #039FFA; border-color: rgba(3, 159, 250, 0.25);">Pertemuan {{ $weekNum }}</span>
                                    @endif
                                    <span class="badge-status {{ $badgeClass }}">
                                        <span>{{ $badgeIcon }}</span>
                                        <span>{{ $badgeText }}</span>
                                    </span>
                                </div>
                                <div class="btn-actions">
                                    <button type="button" class="btn-icon btn-icon-edit btn-edit-tugas" title="Edit Tugas">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    </button>
                                    <form action="{{ route('admin.tugas.destroy', $t) }}" method="POST"
                                          onsubmit="return confirm('Hapus tugas &quot;{{ addslashes($t->title) }}&quot;? Tindakan ini tidak dapat dibatalkan.')"
                                          style="margin: 0; display: inline-flex;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon btn-icon-danger btn-delete-tugas" title="Hapus Tugas">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; margin-bottom: 2px;">
                                <h3 class="assignment-card-title">{{ $t->title }}</h3>
                                <span class="badge-mk-code" style="background: #F8FAFC; color: #475569; font-size: 10px; flex-shrink: 0;">Maks. {{ $t->max_score }}</span>
                            </div>

                            <div class="assignment-course-name">
                                <span>📚 {{ $t->course?->name ?? 'Mata Kuliah' }}</span>
                                <small style="color: #94A3B8;">&bull; {{ $t->course?->lecturer?->name ?? 'Dosen' }}</small>
                            </div>

                            <p class="assignment-card-desc">{{ Str::limit($t->instructions, 140) }}</p>

                            <!-- Kotak Deadline Menyesuaikan Mode Dosen -->
                            <div class="assignment-deadline-box">
                                <svg class="deadline-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <div class="deadline-info">
                                    <span class="deadline-label">Batas Waktu Pengumpulan{{ $t->allow_late ? ' (boleh terlambat)' : '' }}</span>
                                    <span class="deadline-value">{{ $t->due_at->translatedFormat('d M Y') }} &middot; {{ $t->due_at->format('H:i') }} WITA</span>
                                </div>
                            </div>

                            <!-- Progress Pengumpulan Menyesuaikan Mode Dosen -->
                            <div class="submission-progress-wrap">
                                <div class="submission-progress-labels">
                                    <span>Progress Pengumpulan</span>
                                    <span><strong>{{ $t->submissions_count }}</strong> dari {{ $studentCount }} Mahasiswa ({{ $pct }}%)</span>
                                </div>
                                <div class="submission-progress-track">
                                    <div class="submission-progress-fill" style="width: {{ $pct }}%;{{ $pct === 100 ? ' background: #1B8A5A;' : '' }}"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="assignment-card-footer">
                            <div class="creator-info">
                                <div class="creator-avatar" title="{{ $creatorName }}">
                                    {{ $creatorInitials }}
                                </div>
                                <div>
                                    <span class="creator-name" title="{{ $creatorName }}">{{ $creatorName }}</span>
                                </div>
                            </div>

                            <a href="{{ route('assignments.show', $t) }}" class="btn-view-submissions">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                <span>Lihat Pengumpulan ({{ $t->submissions_count }}) &rarr;</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="tugas-empty-state" id="tugasEmptyState">
                        <div class="tugas-empty-icon">📝</div>
                        <div class="tugas-empty-title">Belum Ada Tugas Perkuliahan</div>
                        <p class="tugas-empty-desc">Belum ada penugasan yang dibuat pada sistem. Klik tombol di bawah untuk membuat tugas pertama.</p>
                        <button type="button" class="btn-primary-action" onclick="document.getElementById('btnOpenAddModal').click()" style="margin: 0 auto;">
                            + Buat Tugas Pertama
                        </button>
                    </div>
                @endforelse

                <!-- Filter Empty State (Hidden by default, shown by filter search) -->
                <div class="tugas-empty-state" id="tugasFilterEmptyState" style="display: none;">
                    <div class="tugas-empty-icon">🔍</div>
                    <div class="tugas-empty-title">Tidak Ada Tugas yang Cocok</div>
                    <p class="tugas-empty-desc">Tidak ditemukan penugasan kuliah untuk filter mata kuliah, status, atau kata kunci pencarian yang Anda pilih.</p>
                    <button type="button" class="btn-primary-action" onclick="resetFilters()" style="margin: 0 auto;">
                        Reset Filter
                    </button>
                </div>
            </section>

        </main>

        <x-footer />
    </div>

    <!-- MODAL POPUP: BUAT / EDIT TUGAS (SESUAI DESAIN ROLE DOSEN) -->
    <div class="dosen-modal-overlay {{ $errors->any() ? 'active show' : '' }}" id="tugasModal">
        <div class="dosen-modal-card" style="max-width: 620px; width: 95%;">
            <div class="dosen-modal-header">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(3, 159, 250, 0.12); color: #039FFA; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 id="modalTitle" style="margin: 0; font-size: 15px; font-weight: 800; color: #0F172A;">Buat Penugasan Baru</h3>
                        <span id="modalSubtitle" style="font-size: 11px; color: #64748B; font-weight: 600;">Kelola penugasan perkuliahan mahasiswa</span>
                    </div>
                </div>
                <button type="button" class="btn-close-modal" id="btnCloseModal" aria-label="Tutup modal">&times;</button>
            </div>

            <form id="formTugas" method="POST" action="{{ route('admin.tugas.store') }}" style="margin: 0; display: flex; flex-direction: column;">
                @csrf
                <input type="hidden" id="formMethodField" name="_method" value="">
                <input type="hidden" id="editTugasId" value="">

                <div class="dosen-modal-body" style="max-height: 70vh; overflow-y: auto;">
                    @if ($errors->any())
                        <div style="background: #FEF2F2; border: 1px solid #EF4444; color: #991B1B; padding: 10px 14px; border-radius: 8px; font-size: 11.5px; font-weight: 700; margin-bottom: 12px;">
                            Terdapat kesalahan pada input form:
                            <ul style="margin: 4px 0 0 16px; padding: 0;">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Baris 1: Mata Kuliah Target & Pertemuan -->
                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="modalCourseSelect">Mata Kuliah Target <span class="required">*</span></label>
                            <select id="modalCourseSelect" name="course_id" class="form-select" required>
                                <option value="">-- Pilih Mata Kuliah --</option>
                                @foreach ($coursesList as $c)
                                    <option value="{{ $c->id }}" @selected(old('course_id') == $c->id)>
                                        {{ $c->code }} - {{ $c->name }} ({{ $c->lecturer?->name ?? 'Belum Ditugaskan' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="taskSession">Pertemuan / Minggu Ke-</label>
                            <select id="taskSession" name="session" class="form-select">
                                <option value="">(Opsional)</option>
                                @for ($w = 1; $w <= 16; $w++)
                                    <option value="{{ $w }}" @selected(old('session') == $w)>Pertemuan {{ $w }} (Minggu ke-{{ $w }})</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Judul Tugas -->
                    <div class="form-group">
                        <label for="modalTugasTitle">Judul Tugas <span class="required">*</span></label>
                        <input type="text" id="modalTugasTitle" name="title" class="form-control"
                               placeholder="Contoh: Tugas 02 - Pembuatan REST API Sanctum"
                               value="{{ old('title') }}" required>
                        @error('title') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <!-- Petunjuk / Instruksi -->
                    <div class="form-group">
                        <label for="modalTugasInstructions">Instruksi / Petunjuk Pengerjaan <span class="required">*</span></label>
                        <textarea id="modalTugasInstructions" name="instructions" class="form-textarea" rows="4"
                                  placeholder="Jelaskan kebutuhan tugas, format berkas yang dikumpulkan, dan kriteria penilaian..."
                                  required>{{ old('instructions') }}</textarea>
                        @error('instructions') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <!-- Batas Tanggal & Batas Jam -->
                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="modalTugasDueDate">Batas Tanggal (Deadline) <span class="required">*</span></label>
                            <input type="date" id="modalTugasDueDate" name="due_date" class="form-control" required
                                   value="{{ old('due_date', now()->addWeek()->format('Y-m-d')) }}">
                        </div>
                        <div class="form-group">
                            <label for="modalTugasDueTime">Batas Jam <span class="required">*</span></label>
                            <input type="time" id="modalTugasDueTime" name="due_time" class="form-control" required
                                   value="{{ old('due_time', '23:59') }}">
                        </div>
                    </div>
                    @error('due_at') <span class="field-error" style="margin-top:-8px; margin-bottom:10px;">{{ $message }}</span> @enderror

                    <!-- Nilai Maksimal & Status Publikasi -->
                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="modalTugasMaxScore">Nilai Maksimal <span class="required">*</span></label>
                            <input type="number" id="modalTugasMaxScore" name="max_score" class="form-control" min="1" max="100" required
                                   value="{{ old('max_score', 100) }}">
                            @error('max_score') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="modalTugasStatus">Status Publikasi <span class="required">*</span></label>
                            <select id="modalTugasStatus" name="status" class="form-select" required>
                                <option value="published" @selected(old('status', 'published') === 'published')>Terbit (terlihat mahasiswa)</option>
                                <option value="draft" @selected(old('status') === 'draft')>Draft (belum terlihat mahasiswa)</option>
                            </select>
                            @error('status') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Izinkan Terlambat -->
                    <div class="form-group" style="margin-top: 4px;">
                        <label class="check-row" style="cursor: pointer;">
                            <input type="checkbox" id="modalTugasAllowLate" name="allow_late" value="1"
                                   @checked(old('allow_late', true))>
                            Izinkan pengumpulan terlambat (setelah deadline)
                        </label>
                    </div>
                </div>

                <div class="dosen-modal-footer">
                    <button type="button" class="btn-secondary-action" id="btnCancelModal">Batal</button>
                    <button type="submit" class="btn-primary-action" id="btnSubmitAssignment">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span id="btnSubmitText">Simpan Tugas</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div class="dosen-toast-container" id="toastContainer"></div>

    <!-- Script Interaktif Frontend & Backend Binding -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentCourseFilter = 'all';
            let currentStatusFilter = 'all';
            let currentSearchQuery  = '';

            const modalOverlay = document.getElementById('tugasModal');
            const btnOpenAddModal = document.getElementById('btnOpenAddModal');
            const btnCloseModal = document.getElementById('btnCloseModal');
            const btnCancelModal = document.getElementById('btnCancelModal');
            const formTugas = document.getElementById('formTugas');
            const formMethodField = document.getElementById('formMethodField');
            const modalTitle = document.getElementById('modalTitle');
            const editTugasId = document.getElementById('editTugasId');
            const btnSubmitText = document.getElementById('btnSubmitText');

            const modalCourseSelect = document.getElementById('modalCourseSelect');
            const modalTugasStatus = document.getElementById('modalTugasStatus');
            const taskSession = document.getElementById('taskSession');
            const modalTugasTitle = document.getElementById('modalTugasTitle');

            // Sesi Pertemuan generator
            if (taskSession) {
                taskSession.addEventListener('change', function() {
                    const sessionVal = this.value;
                    if (sessionVal && (!modalTugasTitle.value || modalTugasTitle.value.startsWith('Tugas Pertemuan'))) {
                        modalTugasTitle.value = `Tugas Pertemuan ${sessionVal}: `;
                        modalTugasTitle.focus();
                    }
                });
            }

            // FILTERING & SEARCH
            function applyFilters() {
                const cards = document.querySelectorAll('.assignment-card');
                let visibleCount = 0;

                cards.forEach(card => {
                    const courseId = card.dataset.courseId;
                    const status = card.dataset.status;
                    const text = card.textContent.toLowerCase();

                    const matchCourse = (currentCourseFilter === 'all' || courseId === currentCourseFilter);
                    const matchStatus = (currentStatusFilter === 'all' || status === currentStatusFilter);
                    const matchSearch = (!currentSearchQuery || text.includes(currentSearchQuery));

                    if (matchCourse && matchStatus && matchSearch) {
                        card.style.display = 'flex';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                const emptyState = document.getElementById('tugasFilterEmptyState');
                if (emptyState) {
                    emptyState.style.display = (cards.length > 0 && visibleCount === 0) ? 'block' : 'none';
                }
            }

            // Custom Course Filter Dropdown di Toolbar
            const courseFilterDropdown = document.getElementById('courseFilterDropdown');
            const courseFilterBtn = document.getElementById('courseFilterBtn');
            const courseFilterText = document.getElementById('courseFilterText');
            const hiddenCourseFilterInput = document.getElementById('selectFilterCourse');
            const courseFilterItems = document.querySelectorAll('#courseFilterOptions .filter-option-item');

            if (courseFilterBtn) {
                courseFilterBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    courseFilterDropdown.classList.toggle('open');
                });

                courseFilterItems.forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.stopPropagation();
                        courseFilterItems.forEach(el => el.classList.remove('active'));
                        this.classList.add('active');

                        const val = this.dataset.value;
                        const label = this.dataset.label;
                        hiddenCourseFilterInput.value = val;
                        courseFilterText.textContent = label;
                        currentCourseFilter = val;
                        courseFilterDropdown.classList.remove('open');
                        applyFilters();
                    });
                });

                document.addEventListener('click', function(e) {
                    if (courseFilterDropdown && !courseFilterDropdown.contains(e.target)) {
                        courseFilterDropdown.classList.remove('open');
                    }
                });
            }

            document.getElementById('selectFilterStatus').addEventListener('change', function() {
                currentStatusFilter = this.value;
                applyFilters();
            });

            document.getElementById('inputSearchTugas').addEventListener('input', function() {
                currentSearchQuery = this.value.toLowerCase().trim();
                applyFilters();
            });

            window.resetFilters = function() {
                if (hiddenCourseFilterInput) {
                    hiddenCourseFilterInput.value = 'all';
                    courseFilterText.textContent = "Semua Mata Kuliah ({{ $coursesList->count() }} Terdaftar)";
                    courseFilterItems.forEach(el => {
                        if (el.dataset.value === 'all') el.classList.add('active');
                        else el.classList.remove('active');
                    });
                }
                currentCourseFilter = 'all';
                document.getElementById('selectFilterStatus').value = 'all';
                currentStatusFilter = 'all';
                document.getElementById('inputSearchTugas').value = '';
                currentSearchQuery = '';
                applyFilters();
            };

            // MODAL CONTROLS (CREATE & EDIT)
            function openModal(isEdit = false, card = null) {
                if (isEdit && card) {
                    if (modalTitle) modalTitle.textContent = 'Edit Tugas Perkuliahan';
                    if (btnSubmitText) btnSubmitText.textContent = 'Perbarui Tugas';
                    editTugasId.value = card.dataset.id;
                    formTugas.action = `/admin/tugas/${card.dataset.id}`;
                    formMethodField.value = 'PUT';

                    if (modalCourseSelect) {
                        modalCourseSelect.value = card.dataset.courseId || '';
                    }

                    modalTugasTitle.value = card.dataset.title || '';
                    document.getElementById('modalTugasInstructions').value = card.dataset.instructions || '';
                    document.getElementById('modalTugasMaxScore').value = card.dataset.score || 100;
                    document.getElementById('modalTugasAllowLate').checked = card.dataset.allowLate == '1';

                    if (card.dataset.dueDate) {
                        document.getElementById('modalTugasDueDate').value = card.dataset.dueDate;
                    }
                    if (card.dataset.dueTime) {
                        document.getElementById('modalTugasDueTime').value = card.dataset.dueTime;
                    }

                    if (taskSession) {
                        taskSession.value = card.dataset.session || '';
                    }

                    if (modalTugasStatus) {
                        modalTugasStatus.value = card.dataset.status || 'published';
                    }
                } else {
                    if (modalTitle) modalTitle.textContent = 'Buat Penugasan Baru';
                    if (btnSubmitText) btnSubmitText.textContent = 'Simpan Tugas';
                    formTugas.action = @json(route('admin.tugas.store'));
                    formMethodField.value = '';
                    editTugasId.value = '';

                    formTugas.reset();

                    // Set default due date: 7 hari dari sekarang jam 23:59
                    const d = new Date();
                    d.setDate(d.getDate() + 7);
                    const yyyy = d.getFullYear();
                    const mm = String(d.getMonth() + 1).padStart(2, '0');
                    const dd = String(d.getDate()).padStart(2, '0');
                    document.getElementById('modalTugasDueDate').value = `${yyyy}-${mm}-${dd}`;
                    document.getElementById('modalTugasDueTime').value = '23:59';
                    document.getElementById('modalTugasMaxScore').value = 100;
                    document.getElementById('modalTugasAllowLate').checked = true;

                    if (modalTugasStatus) {
                        modalTugasStatus.value = 'published';
                    }
                    if (modalCourseSelect) {
                        modalCourseSelect.value = '';
                    }
                    if (taskSession) {
                        taskSession.value = '';
                    }
                }

                modalOverlay.classList.add('active');
                modalOverlay.classList.add('show');
            }

            function closeModal() {
                modalOverlay.classList.remove('active');
                modalOverlay.classList.remove('show');
            }

            if (btnOpenAddModal) {
                btnOpenAddModal.addEventListener('click', () => openModal(false));
            }
            if (btnCloseModal) {
                btnCloseModal.addEventListener('click', closeModal);
            }
            if (btnCancelModal) {
                btnCancelModal.addEventListener('click', closeModal);
            }
            if (modalOverlay) {
                modalOverlay.addEventListener('click', (e) => {
                    if (e.target === modalOverlay) closeModal();
                });
            }
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modalOverlay && (modalOverlay.classList.contains('active') || modalOverlay.classList.contains('show'))) {
                    closeModal();
                }
            });

            // Bind Edit Buttons on Cards
            document.querySelectorAll('.assignment-card').forEach(card => {
                const btnEdit = card.querySelector('.btn-edit-tugas');
                if (btnEdit) {
                    btnEdit.addEventListener('click', (e) => {
                        e.stopPropagation();
                        openModal(true, card);
                    });
                }
            });

            // Toast Notifications
            function showToast(message, isSuccess = true) {
                const container = document.getElementById('toastContainer');
                if (!container) return;

                const toast = document.createElement('div');
                toast.className = 'dosen-toast';
                if (!isSuccess) toast.style.borderLeftColor = '#EF4444';

                const icon = `
                    <svg class="toast-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="${isSuccess ? '#10B981' : '#EF4444'}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>`;
                toast.innerHTML = icon;

                const text = document.createElement('span');
                text.textContent = message;
                toast.appendChild(text);

                container.appendChild(toast);
                setTimeout(() => toast.classList.add('show'), 50);
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 350);
                }, 3500);
            }

            @if (session('success'))
                showToast(@json(session('success')), true);
            @endif
            @if (session('error'))
                showToast(@json(session('error')), false);
            @endif
        });
    </script>
</body>
</html>
