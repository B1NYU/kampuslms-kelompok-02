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
        @vite(['resources/css/app.css', 'resources/css/admin/admin.tugas.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/admin/admin.tugas.css') }}">
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

            @php
                // Ambil daftar mata kuliah yang aktif di database
                $coursesList = \App\Models\Course::with('lecturer')->orderBy('code')->get();

                // Mockup initial assignments data terhubung dengan courses di sistem
                $c1 = $coursesList->first();
                $c2 = $coursesList->skip(1)->first() ?? $c1;
                $c3 = $coursesList->skip(2)->first() ?? $c1;

                $initialAssignments = [
                    [
                        'id' => 1,
                        'course_id' => $c1?->id ?? 1,
                        'course_code' => $c1?->code ?? 'SI101',
                        'course_name' => $c1?->name ?? 'Pemrograman Web',
                        'lecturer_name' => $c1?->lecturer?->name ?? 'Dosen Pengampu',
                        'title' => 'Tugas 01: Implementasi CRUD Blade & Sanitasi Input',
                        'instructions' => 'Buatlah antarmuka CRUD pengguna menggunakan framework Laravel 12 dan Blade templating engine. Pastikan seluruh input divalidasi dengan FormRequest dan terlindung dari kerentanan IDOR.',
                        'due_at' => now()->addDays(3)->setTime(23, 59)->format('Y-m-d H:i:s'),
                        'due_display' => now()->addDays(3)->translatedFormat('d M Y, 23:59'),
                        'countdown' => '3 Hari Lagi',
                        'countdown_type' => 'tag-upcoming',
                        'max_score' => 100,
                        'allow_late' => 1,
                        'status' => 'published',
                        'submissions_count' => 18,
                        'created_by' => 'Super Administrator',
                    ],
                    [
                        'id' => 2,
                        'course_id' => $c2?->id ?? 2,
                        'course_code' => $c2?->code ?? 'SI102',
                        'course_name' => $c2?->name ?? 'Basis Data Lanjut',
                        'lecturer_name' => $c2?->lecturer?->name ?? 'Dosen Pengampu',
                        'title' => 'Praktikum 03: Optimasi Query Index & Explain Plan',
                        'instructions' => 'Analisis performa query JOIN pada tabel berukuran besar menggunakan EXPLAIN. Buatlah composite index yang efisien dan dokumentasikan perbandingan execution time.',
                        'due_at' => now()->addDay()->setTime(17, 00)->format('Y-m-d H:i:s'),
                        'due_display' => now()->addDay()->translatedFormat('d M Y, 17:00'),
                        'countdown' => 'Besok, 17:00',
                        'countdown_type' => 'tag-urgent',
                        'max_score' => 100,
                        'allow_late' => 0,
                        'status' => 'published',
                        'submissions_count' => 24,
                        'created_by' => 'Dosen Pengampu',
                    ],
                    [
                        'id' => 3,
                        'course_id' => $c3?->id ?? 3,
                        'course_code' => $c3?->code ?? 'SI103',
                        'course_name' => $c3?->name ?? 'Analisis & Desain SI',
                        'lecturer_name' => $c3?->lecturer?->name ?? 'Dosen Pengampu',
                        'title' => 'Tugas Kelompok: Penyusunan Dokumen SRS & Use Case Matrix',
                        'instructions' => 'Setiap kelompok menyusun dokumen Software Requirements Specification (SRS) berstandar IEEE untuk topik aplikasi yang telah disepakati bersama dosen pengampu.',
                        'due_at' => now()->addDays(7)->setTime(23, 59)->format('Y-m-d H:i:s'),
                        'due_display' => now()->addDays(7)->translatedFormat('d M Y, 23:59'),
                        'countdown' => '7 Hari Lagi',
                        'countdown_type' => 'tag-upcoming',
                        'max_score' => 100,
                        'allow_late' => 1,
                        'status' => 'published',
                        'submissions_count' => 6,
                        'created_by' => 'Super Administrator',
                    ],
                    [
                        'id' => 4,
                        'course_id' => $c1?->id ?? 1,
                        'course_code' => $c1?->code ?? 'SI101',
                        'course_name' => $c1?->name ?? 'Pemrograman Web',
                        'lecturer_name' => $c1?->lecturer?->name ?? 'Dosen Pengampu',
                        'title' => 'Draf Tugas 02: RESTful API Sanctum & Dokumentasi Postman',
                        'instructions' => 'Implementasi API token-based authentication menggunakan Laravel Sanctum dengan proteksi throttle request dan dokumentasi OpenAPI/Postman collection.',
                        'due_at' => now()->addDays(14)->setTime(23, 59)->format('Y-m-d H:i:s'),
                        'due_display' => now()->addDays(14)->translatedFormat('d M Y, 23:59'),
                        'countdown' => 'Draf Penugasan',
                        'countdown_type' => 'tag-passed',
                        'max_score' => 100,
                        'allow_late' => 1,
                        'status' => 'draft',
                        'submissions_count' => 0,
                        'created_by' => 'Super Administrator',
                    ],
                ];
            @endphp

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
                        <span class="tugas-stat-value" id="statTotalTugas">{{ count($initialAssignments) }}</span>
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
                        <span class="tugas-stat-value" id="statPublishedTugas">3</span>
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
                        <span class="tugas-stat-value" id="statDraftTugas">1</span>
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
                        <span class="tugas-stat-value" id="statTotalSubmissions">48 Berkas</span>
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
                @foreach ($initialAssignments as $t)
                    @php
                        $isPublished = $t['status'] === 'published';
                        $statusBadgeClass = $isPublished ? 'badge-status-published' : 'badge-status-draft';
                        $statusBadgeText = $isPublished ? 'Dipublikasikan' : 'Draf';
                    @endphp
                    <div class="assignment-card"
                         data-id="{{ $t['id'] }}"
                         data-course-id="{{ $t['course_id'] }}"
                         data-status="{{ $t['status'] }}"
                         data-title="{{ $t['title'] }}"
                         data-instructions="{{ $t['instructions'] }}"
                         data-due="{{ $t['due_at'] }}"
                         data-score="{{ $t['max_score'] }}"
                         data-allow-late="{{ $t['allow_late'] }}"
                         data-submissions="{{ $t['submissions_count'] }}">

                        <div>
                            <div class="assignment-card-top">
                                <div class="badge-tag-wrap">
                                    <span class="badge-mk-code">{{ $t['course_code'] }}</span>
                                    <span class="badge-status {{ $statusBadgeClass }}">
                                        <span>{{ $isPublished ? '●' : '○' }}</span>
                                        <span>{{ $statusBadgeText }}</span>
                                    </span>
                                </div>
                                <div class="btn-actions">
                                    <button type="button" class="btn-icon btn-icon-edit btn-edit-tugas" title="Edit Tugas">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    </button>
                                    <button type="button" class="btn-icon btn-icon-danger btn-delete-tugas" title="Hapus Tugas">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <h3 class="assignment-card-title">{{ $t['title'] }}</h3>
                            <div class="assignment-course-name">
                                <span>📚 {{ $t['course_name'] }}</span>
                            </div>

                            <p class="assignment-card-desc">{{ $t['instructions'] }}</p>

                            <!-- Keterangan Deadline -->
                            <div class="assignment-deadline-row">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                <span>Deadline:</span>
                                <span class="meta-deadline-text">{{ $t['due_display'] }}</span>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="assignment-card-footer">
                            <div class="creator-info">
                                <div class="creator-avatar">
                                    {{ strtoupper(substr($t['created_by'], 0, 2)) }}
                                </div>
                                <div>
                                    <span class="creator-name" title="{{ $t['created_by'] }}">{{ $t['created_by'] }}</span>
                                </div>
                            </div>

                            <button type="button" class="btn-view-submissions" onclick="openSubmissionsModal('{{ $t['title'] }}', '{{ $t['course_code'] }}', {{ $t['submissions_count'] }})">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                <span>Pengumpulan ({{ $t['submissions_count'] }})</span>
                            </button>
                        </div>
                    </div>
                @endforeach

                <!-- Empty State (Hidden by default) -->
                <div class="tugas-empty-state" id="tugasEmptyState" style="display: none;">
                    <div class="tugas-empty-icon">📝</div>
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

    <!-- MODAL POPUP: BUAT / EDIT TUGAS -->
    <div class="tugas-modal-overlay" id="tugasModal">
        <div class="tugas-modal-card">
            <div class="tugas-modal-header">
                <h3 id="modalTitle">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="11" x2="12" y2="17"></line><line x1="9" y1="14" x2="15" y2="14"></line></svg>
                    <span>Buat / Tambah Tugas Baru</span>
                </h3>
                <button type="button" class="btn-close-modal" id="btnCloseModal">&times;</button>
            </div>

            <form id="formTugas" class="tugas-modal-body">
                <input type="hidden" id="editTugasId" value="">

                <!-- Pilih Mata Kuliah (Searchable Dropdown dengan Scrollbar) -->
                <div class="form-group" style="margin-bottom: 12px;">
                    <label style="font-size: 11.5px; font-weight: 800; color: #334155; margin-bottom: 5px; display: block;">Mata Kuliah Tujuan <span class="required" style="color: #EF4444;">*</span></label>
                    
                    <!-- Hidden input to store course ID -->
                    <input type="hidden" id="modalCourseSelect" name="course_id" value="" required>

                    <!-- Custom Searchable Combobox -->
                    <div class="custom-combobox-wrapper" id="courseComboboxWrapper">
                        <div class="combobox-input-box" id="comboboxTrigger">
                            <input type="text" id="comboboxSearchInput" class="form-control" 
                                   placeholder="Ketik untuk mencari atau klik untuk memilih MK..." 
                                   autocomplete="off">
                            <span class="combobox-arrow">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </span>
                        </div>

                        <div class="combobox-dropdown-menu" id="comboboxDropdownMenu">
                            <ul class="combobox-options-list" id="comboboxOptionsList">
                                @foreach ($coursesList as $c)
                                    <li class="combobox-option-item" 
                                        data-id="{{ $c->id }}" 
                                        data-code="{{ $c->code }}" 
                                        data-name="{{ $c->name }}"
                                        data-lecturer="{{ $c->lecturer?->name ?? 'Belum Ditugaskan' }}"
                                        data-sks="{{ $c->sks ?? 3 }}">
                                        <div class="option-main-text">
                                            <span class="option-code-pill">{{ $c->code }}</span>
                                            <strong class="option-name-text">{{ $c->name }}</strong>
                                        </div>
                                        <span class="option-sub-text">Pengampu: {{ $c->lecturer?->name ?? 'Belum Ditugaskan' }} &bull; {{ $c->sks }} SKS</span>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="combobox-empty-message" id="comboboxEmptyMessage" style="display: none;">
                                Mata kuliah tidak ditemukan.
                            </div>
                        </div>
                    </div>

                    <!-- Live Preview Info Dosen Pengampu & SKS -->
                    <div id="courseLecturerInfoBox" style="display: none; margin-top: 8px; padding: 7px 10px; background: #F8FAFC; border: 1px solid rgba(3, 159, 250, 0.22); border-radius: 9px; align-items: center; justify-content: space-between; gap: 10px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 26px; height: 26px; border-radius: 7px; background: rgba(3, 159, 250, 0.12); color: #039FFA; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0;">
                                👨‍🏫
                            </div>
                            <div>
                                <span style="font-size: 9.5px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.3px; display: block;">Dosen Pengampu:</span>
                                <strong id="courseLecturerName" style="font-size: 11.5px; color: #0F172A; font-weight: 800;">-</strong>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span id="courseSksBadge" style="background: rgba(249, 184, 4, 0.14); color: #D97706; padding: 2px 6px; border-radius: 5px; font-size: 10px; font-weight: 800; border: 1px solid rgba(249, 184, 4, 0.3);">
                                3 SKS
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Judul Tugas -->
                <div class="form-group" style="margin-bottom: 12px;">
                    <label style="font-size: 11.5px; font-weight: 800; color: #334155; margin-bottom: 5px; display: block;">Judul Penugasan <span class="required" style="color: #EF4444;">*</span></label>
                    <input type="text" id="modalTugasTitle" class="form-control" placeholder="Contoh: Tugas 02 - Pembuatan REST API Sanctum" required style="width: 100%; padding: 7.5px 10px; border-radius: 8px; border: 1px solid rgba(3, 159, 250, 0.25); font-size: 11.5px;">
                </div>

                <!-- Instruksi / Petunjuk Pengerjaan -->
                <div class="form-group" style="margin-bottom: 12px;">
                    <label style="font-size: 11.5px; font-weight: 800; color: #334155; margin-bottom: 5px; display: block;">Instruksi &amp; Panduan Pengerjaan <span class="required" style="color: #EF4444;">*</span></label>
                    <textarea id="modalTugasInstructions" rows="3" class="form-control" placeholder="Jelaskan kebutuhan tugas, format file pengumpulan, dan kriteria penilaian..." required style="width: 100%; padding: 7.5px 10px; border-radius: 8px; border: 1px solid rgba(3, 159, 250, 0.25); font-size: 11.5px; resize: vertical;"></textarea>
                </div>

                <!-- Grid Dua Kolom: Tenggat Waktu & Skor Maksimal -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                    <div>
                        <label style="font-size: 11.5px; font-weight: 800; color: #334155; margin-bottom: 5px; display: block;">Tenggat Waktu (Deadline) <span class="required" style="color: #EF4444;">*</span></label>
                        <input type="datetime-local" id="modalTugasDueAt" class="form-control" required style="width: 100%; padding: 7.5px 10px; border-radius: 8px; border: 1px solid rgba(3, 159, 250, 0.25); font-size: 11.5px;">
                    </div>
                    <div>
                        <label style="font-size: 11.5px; font-weight: 800; color: #334155; margin-bottom: 5px; display: block;">Skor Maksimal <span class="required" style="color: #EF4444;">*</span></label>
                        <input type="number" id="modalTugasMaxScore" class="form-control" min="10" max="100" value="100" required style="width: 100%; padding: 7.5px 10px; border-radius: 8px; border: 1px solid rgba(3, 159, 250, 0.25); font-size: 11.5px;">
                    </div>
                </div>

                <!-- Opsi Keterlambatan Pengumpulan -->
                <div class="form-group" style="margin-bottom: 12px; padding: 7px 10px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin: 0;">
                        <input type="checkbox" id="modalTugasAllowLate" checked style="width: 14px; height: 14px; accent-color: #039FFA; cursor: pointer;">
                        <div>
                            <strong style="font-size: 11.5px; color: #0F172A; display: block;">Izinkan Pengumpulan Terlambat</strong>
                            <small style="color: #64748B; font-size: 10px;">Mahasiswa tetap dapat mengunggah berkas setelah deadline (akan ditandai sebagai status terlambat).</small>
                        </div>
                    </label>
                </div>

                <!-- Status Publikasi Tugas -->
                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="font-size: 11.5px; font-weight: 800; color: #334155; margin-bottom: 5px; display: block;">Status Publikasi <span class="required" style="color: #EF4444;">*</span></label>
                    <div class="status-switch-container">
                        <label class="status-option-radio selected" id="optRadioPublished">
                            <input type="radio" name="tugasStatus" value="published" checked>
                            <div>
                                <strong style="font-size: 11.5px; display: block; color: #059669;">🚀 Dipublikasikan</strong>
                                <small style="color: #64748B; font-size: 10px;">Tugas aktif dan langsung dapat dilihat mahasiswa.</small>
                            </div>
                        </label>
                        <label class="status-option-radio" id="optRadioDraft">
                            <input type="radio" name="tugasStatus" value="draft">
                            <div>
                                <strong style="font-size: 11.5px; display: block; color: #64748B;">📋 Simpan Draf</strong>
                                <small style="color: #64748B; font-size: 10px;">Tugas belum terlihat oleh mahasiswa.</small>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Tombol Submit & Cancel -->
                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                    <button type="button" class="btn-icon" id="btnCancelModal" style="padding: 6px 14px; font-size: 11.5px; width: auto; height: auto;">Batal</button>
                    <button type="submit" class="btn-primary-action" style="font-size: 11.5px; padding: 7px 15px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span id="btnSubmitText">Simpan Tugas</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL POPUP: PRATINJAU PENGUMPULAN MAHASISWA -->
    <div class="tugas-modal-overlay" id="submissionsModal">
        <div class="tugas-modal-card modal-card-lg">
            <div class="tugas-modal-header">
                <h3>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <span id="subModalTitle">Daftar Pengumpulan Mahasiswa</span>
                </h3>
                <button type="button" class="btn-close-modal" id="btnCloseSubmissionsModal">&times;</button>
            </div>

            <div class="tugas-modal-body">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <div>
                        <strong style="font-size: 13px; color: #0F172A; display: block;" id="subModalCourseText">Mata Kuliah: SI101</strong>
                        <span style="font-size: 11px; color: #64748B;" id="subModalCountText">Total 18 Mahasiswa telah mengumpulkan tugas ini</span>
                    </div>
                </div>

                <div style="overflow-x: auto;">
                    <table class="submissions-table">
                        <thead>
                            <tr>
                                <th>Mahasiswa</th>
                                <th>Berkas Dikumpulkan</th>
                                <th>Waktu Kumpul</th>
                                <th>Status</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="submissionsTableBody">
                            <!-- Injected by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Interaktif Frontend -->
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
            const modalTitle = document.getElementById('modalTitle');
            const editTugasId = document.getElementById('editTugasId');
            const btnSubmitText = document.getElementById('btnSubmitText');

            const optRadioPublished = document.getElementById('optRadioPublished');
            const optRadioDraft = document.getElementById('optRadioDraft');

            // Custom Searchable Combobox & Live Preview Dosen
            const modalCourseSelect = document.getElementById('modalCourseSelect');
            const courseComboboxWrapper = document.getElementById('courseComboboxWrapper');
            const comboboxSearchInput = document.getElementById('comboboxSearchInput');
            const comboboxDropdownMenu = document.getElementById('comboboxDropdownMenu');
            const comboboxEmptyMessage = document.getElementById('comboboxEmptyMessage');
            const courseLecturerInfoBox = document.getElementById('courseLecturerInfoBox');
            const courseLecturerName = document.getElementById('courseLecturerName');
            const courseSksBadge = document.getElementById('courseSksBadge');

            let highlightedIndex = -1;

            function getVisibleOptions() {
                return Array.from(document.querySelectorAll('#comboboxOptionsList .combobox-option-item')).filter(el => el.style.display !== 'none');
            }

            function updateHighlightedOption(visibleItems) {
                document.querySelectorAll('#comboboxOptionsList .combobox-option-item').forEach(el => el.classList.remove('highlighted'));
                if (highlightedIndex >= 0 && highlightedIndex < visibleItems.length) {
                    const target = visibleItems[highlightedIndex];
                    target.classList.add('highlighted');
                    target.scrollIntoView({ block: 'nearest' });
                }
            }

            function selectCourseItem(item) {
                highlightedIndex = -1;
                document.querySelectorAll('#comboboxOptionsList .combobox-option-item').forEach(el => el.classList.remove('highlighted'));

                if (!item) {
                    modalCourseSelect.value = '';
                    modalCourseSelect.dataset.code = '';
                    modalCourseSelect.dataset.name = '';
                    comboboxSearchInput.value = '';
                    courseLecturerInfoBox.style.display = 'none';
                    document.querySelectorAll('#comboboxOptionsList .combobox-option-item').forEach(el => el.classList.remove('selected'));
                    return;
                }

                const id = item.dataset.id;
                const code = item.dataset.code;
                const name = item.dataset.name;
                const lecturer = item.dataset.lecturer || 'Belum Ditugaskan';
                const sks = item.dataset.sks || '3';

                modalCourseSelect.value = id;
                modalCourseSelect.dataset.code = code;
                modalCourseSelect.dataset.name = name;
                comboboxSearchInput.value = `${code} • ${name}`;

                document.querySelectorAll('#comboboxOptionsList .combobox-option-item').forEach(el => el.classList.remove('selected'));
                item.classList.add('selected');

                courseLecturerName.textContent = lecturer;
                courseSksBadge.textContent = `${sks} SKS`;
                courseLecturerInfoBox.style.display = 'flex';

                courseComboboxWrapper.classList.remove('open');
            }

            document.querySelectorAll('#comboboxOptionsList .combobox-option-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.stopPropagation();
                    selectCourseItem(this);
                });
            });

            comboboxSearchInput.addEventListener('input', function() {
                const q = this.value.toLowerCase().trim();
                courseComboboxWrapper.classList.add('open');
                highlightedIndex = -1;

                let matched = 0;
                document.querySelectorAll('#comboboxOptionsList .combobox-option-item').forEach(item => {
                    const code = (item.dataset.code || '').toLowerCase();
                    const name = (item.dataset.name || '').toLowerCase();
                    const lecturer = (item.dataset.lecturer || '').toLowerCase();

                    if (!q || code.includes(q) || name.includes(q) || lecturer.includes(q)) {
                        item.style.display = 'flex';
                        matched++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                comboboxEmptyMessage.style.display = matched === 0 ? 'block' : 'none';

                if (!q) {
                    modalCourseSelect.value = '';
                    modalCourseSelect.dataset.code = '';
                    courseLecturerInfoBox.style.display = 'none';
                }
            });

            comboboxSearchInput.addEventListener('focus', function() {
                courseComboboxWrapper.classList.add('open');
                document.querySelectorAll('#comboboxOptionsList .combobox-option-item').forEach(item => item.style.display = 'flex');
                comboboxEmptyMessage.style.display = 'none';
            });

            comboboxSearchInput.addEventListener('click', function() {
                if (!courseComboboxWrapper.classList.contains('open')) {
                    courseComboboxWrapper.classList.add('open');
                    document.querySelectorAll('#comboboxOptionsList .combobox-option-item').forEach(item => item.style.display = 'flex');
                    comboboxEmptyMessage.style.display = 'none';
                }
            });

            comboboxSearchInput.addEventListener('keydown', function(e) {
                const isOpen = courseComboboxWrapper.classList.contains('open');
                const visibleItems = getVisibleOptions();

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (!isOpen) {
                        courseComboboxWrapper.classList.add('open');
                        highlightedIndex = 0;
                    } else if (visibleItems.length > 0) {
                        highlightedIndex = (highlightedIndex + 1) % visibleItems.length;
                    }
                    updateHighlightedOption(visibleItems);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (!isOpen) {
                        courseComboboxWrapper.classList.add('open');
                        highlightedIndex = visibleItems.length - 1;
                    } else if (visibleItems.length > 0) {
                        highlightedIndex = (highlightedIndex - 1 + visibleItems.length) % visibleItems.length;
                    }
                    updateHighlightedOption(visibleItems);
                } else if (e.key === 'Enter') {
                    if (isOpen && highlightedIndex >= 0 && highlightedIndex < visibleItems.length) {
                        e.preventDefault();
                        selectCourseItem(visibleItems[highlightedIndex]);
                    }
                } else if (e.key === 'Escape') {
                    if (isOpen) {
                        e.preventDefault();
                        courseComboboxWrapper.classList.remove('open');
                    }
                }
            });

            document.getElementById('comboboxTrigger').addEventListener('click', function(e) {
                if (e.target !== comboboxSearchInput) {
                    const isOpen = courseComboboxWrapper.classList.contains('open');
                    if (isOpen) {
                        courseComboboxWrapper.classList.remove('open');
                    } else {
                        courseComboboxWrapper.classList.add('open');
                        comboboxSearchInput.focus();
                    }
                }
            });

            document.addEventListener('click', function(e) {
                if (courseComboboxWrapper && !courseComboboxWrapper.contains(e.target)) {
                    courseComboboxWrapper.classList.remove('open');
                }
            });

            // 1. FILTERING & SEARCH
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

                const emptyState = document.getElementById('tugasEmptyState');
                if (emptyState) {
                    emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
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

            // 2. MODAL TOGGLES
            function openModal(isEdit = false, card = null) {
                formTugas.reset();
                editTugasId.value = '';

                // Default due date: 7 hari dari sekarang jam 23:59
                const d = new Date();
                d.setDate(d.getDate() + 7);
                d.setHours(23, 59, 0, 0);
                const localISODate = new Date(d.getTime() - d.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
                document.getElementById('modalTugasDueAt').value = localISODate;

                if (isEdit && card) {
                    modalTitle.querySelector('span').textContent = 'Edit Tugas Perkuliahan';
                    btnSubmitText.textContent = 'Perbarui Tugas';
                    editTugasId.value = card.dataset.id;
                    document.getElementById('modalTugasTitle').value = card.dataset.title;
                    document.getElementById('modalTugasInstructions').value = card.dataset.instructions;
                    document.getElementById('modalTugasMaxScore').value = card.dataset.score || 100;
                    document.getElementById('modalTugasAllowLate').checked = card.dataset.allowLate == '1';

                    if (card.dataset.due) {
                        const parsedDate = new Date(card.dataset.due);
                        if (!isNaN(parsedDate.getTime())) {
                            const formatted = new Date(parsedDate.getTime() - parsedDate.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
                            document.getElementById('modalTugasDueAt').value = formatted;
                        }
                    }

                    const status = card.dataset.status;
                    if (status === 'draft') {
                        optRadioDraft.querySelector('input').checked = true;
                        optRadioDraft.classList.add('selected');
                        optRadioPublished.classList.remove('selected');
                    } else {
                        optRadioPublished.querySelector('input').checked = true;
                        optRadioPublished.classList.add('selected');
                        optRadioDraft.classList.remove('selected');
                    }

                    const matchingItem = document.querySelector(`#comboboxOptionsList .combobox-option-item[data-id="${card.dataset.courseId}"]`);
                    if (matchingItem) {
                        selectCourseItem(matchingItem);
                    }
                } else {
                    modalTitle.querySelector('span').textContent = 'Buat / Tambah Tugas Baru';
                    btnSubmitText.textContent = 'Simpan Tugas';
                    optRadioPublished.querySelector('input').checked = true;
                    optRadioPublished.classList.add('selected');
                    optRadioDraft.classList.remove('selected');
                    selectCourseItem(null);
                }

                modalOverlay.classList.add('show');
            }

            function closeModal() {
                modalOverlay.classList.remove('show');
                if (courseComboboxWrapper) {
                    courseComboboxWrapper.classList.remove('open');
                }
            }

            btnOpenAddModal.addEventListener('click', () => openModal(false));
            btnCloseModal.addEventListener('click', closeModal);
            btnCancelModal.addEventListener('click', closeModal);
            modalOverlay.addEventListener('click', (e) => {
                if (e.target === modalOverlay) closeModal();
            });

            // Radio options styling switch
            optRadioPublished.addEventListener('click', () => {
                optRadioPublished.classList.add('selected');
                optRadioDraft.classList.remove('selected');
            });
            optRadioDraft.addEventListener('click', () => {
                optRadioDraft.classList.add('selected');
                optRadioPublished.classList.remove('selected');
            });

            // 3. SUBMIT FORM (CLIENT-SIDE SIMULATION)
            formTugas.addEventListener('submit', function(e) {
                e.preventDefault();

                const courseId = modalCourseSelect.value;
                if (!courseId) {
                    alert('Silakan pilih mata kuliah tujuan terlebih dahulu.');
                    comboboxSearchInput.focus();
                    courseComboboxWrapper.classList.add('open');
                    return;
                }

                const courseCode = modalCourseSelect.dataset.code || 'SI101';
                const courseName = modalCourseSelect.dataset.name || 'Mata Kuliah';
                const title = document.getElementById('modalTugasTitle').value;
                const instructions = document.getElementById('modalTugasInstructions').value;
                const dueAt = document.getElementById('modalTugasDueAt').value;
                const maxScore = document.getElementById('modalTugasMaxScore').value;
                const allowLate = document.getElementById('modalTugasAllowLate').checked ? 1 : 0;
                const isPublished = optRadioPublished.querySelector('input').checked;
                const status = isPublished ? 'published' : 'draft';

                const dueDateObj = new Date(dueAt);
                const dueDisplay = dueDateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });

                const isEditing = Boolean(editTugasId.value);
                const grid = document.getElementById('tugasCardsGrid');

                if (isEditing) {
                    const card = document.querySelector(`.assignment-card[data-id="${editTugasId.value}"]`);
                    if (card) {
                        card.dataset.courseId = courseId;
                        card.dataset.status = status;
                        card.dataset.title = title;
                        card.dataset.instructions = instructions;
                        card.dataset.due = dueAt;
                        card.dataset.score = maxScore;
                        card.dataset.allowLate = allowLate;

                        card.querySelector('.badge-mk-code').textContent = courseCode;
                        const statusBadge = card.querySelector('.badge-status');
                        statusBadge.className = `badge-status ${isPublished ? 'badge-status-published' : 'badge-status-draft'}`;
                        statusBadge.innerHTML = `<span>${isPublished ? '●' : '○'}</span><span>${isPublished ? 'Dipublikasikan' : 'Draf'}</span>`;

                        card.querySelector('.assignment-card-title').textContent = title;
                        card.querySelector('.assignment-course-name span').textContent = `📚 ${courseName}`;
                        card.querySelector('.assignment-card-desc').textContent = instructions;

                        const deadlineText = card.querySelector('.meta-deadline-text');
                        if (deadlineText) deadlineText.textContent = dueDisplay;
                    }
                    alert('Penugasan berhasil diperbarui!');
                } else {
                    const newId = Date.now();
                    const newCardHtml = `
                        <div class="assignment-card"
                             data-id="${newId}"
                             data-course-id="${courseId}"
                             data-status="${status}"
                             data-title="${title}"
                             data-instructions="${instructions}"
                             data-due="${dueAt}"
                             data-score="${maxScore}"
                             data-allow-late="${allowLate}"
                             data-submissions="0">
                            <div>
                                <div class="assignment-card-top">
                                    <div class="badge-tag-wrap">
                                        <span class="badge-mk-code">${courseCode}</span>
                                        <span class="badge-status ${isPublished ? 'badge-status-published' : 'badge-status-draft'}">
                                            <span>${isPublished ? '●' : '○'}</span>
                                            <span>${isPublished ? 'Dipublikasikan' : 'Draf'}</span>
                                        </span>
                                    </div>
                                    <div class="btn-actions">
                                        <button type="button" class="btn-icon btn-icon-edit btn-edit-tugas" title="Edit Tugas">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        </button>
                                        <button type="button" class="btn-icon btn-icon-danger btn-delete-tugas" title="Hapus Tugas">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                        </button>
                                    </div>
                                </div>

                                <h3 class="assignment-card-title">${title}</h3>
                                <div class="assignment-course-name">
                                    <span>📚 ${courseName}</span>
                                </div>

                                <p class="assignment-card-desc">${instructions}</p>

                                <div class="assignment-deadline-row">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    <span>Deadline:</span>
                                    <span class="meta-deadline-text">${dueDisplay}</span>
                                </div>
                            </div>

                            <div class="assignment-card-footer">
                                <div class="creator-info">
                                    <div class="creator-avatar">SA</div>
                                    <div>
                                        <span class="creator-name">Super Administrator</span>
                                    </div>
                                </div>

                                <button type="button" class="btn-view-submissions" onclick="openSubmissionsModal('${title}', '${courseCode}', 0)">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <span>Pengumpulan (0)</span>
                                </button>
                            </div>
                        </div>
                    `;
                    grid.insertAdjacentHTML('afterbegin', newCardHtml);
                    bindCardEvents(grid.firstElementChild);
                    alert('Penugasan baru berhasil ditambahkan!');
                }

                updateStats();
                applyFilters();
                closeModal();
            });

            // 4. BIND ACTION BUTTONS (EDIT & DELETE)
            function bindCardEvents(card) {
                const btnEdit = card.querySelector('.btn-edit-tugas');
                if (btnEdit) {
                    btnEdit.addEventListener('click', () => openModal(true, card));
                }

                const btnDelete = card.querySelector('.btn-delete-tugas');
                if (btnDelete) {
                    btnDelete.addEventListener('click', () => {
                        if (confirm(`Yakin ingin menghapus penugasan "${card.dataset.title}"?`)) {
                            card.remove();
                            updateStats();
                            applyFilters();
                        }
                    });
                }
            }

            document.querySelectorAll('.assignment-card').forEach(card => bindCardEvents(card));

            function updateStats() {
                const cards = document.querySelectorAll('.assignment-card');
                let total = cards.length;
                let published = 0;
                let draft = 0;
                let submissions = 0;

                cards.forEach(c => {
                    if (c.dataset.status === 'published') published++;
                    else draft++;
                    submissions += parseInt(c.dataset.submissions || '0', 10);
                });

                document.getElementById('statTotalTugas').textContent = total;
                document.getElementById('statPublishedTugas').textContent = published;
                document.getElementById('statDraftTugas').textContent = draft;
                document.getElementById('statTotalSubmissions').textContent = `${submissions} Berkas`;
            }

            // 5. MODAL PRATINJAU PENGUMPULAN MAHASISWA
            const subModal = document.getElementById('submissionsModal');
            const subModalTitle = document.getElementById('subModalTitle');
            const subModalCourseText = document.getElementById('subModalCourseText');
            const subModalCountText = document.getElementById('subModalCountText');
            const submissionsTableBody = document.getElementById('submissionsTableBody');
            const btnCloseSubModal = document.getElementById('btnCloseSubmissionsModal');

            window.openSubmissionsModal = function(title, code, count) {
                subModalTitle.textContent = `Daftar Pengumpulan: ${title}`;
                subModalCourseText.textContent = `Mata Kuliah: ${code}`;
                subModalCountText.textContent = `Total ${count} Mahasiswa telah mengumpulkan berkas tugas ini`;

                // Dummy data mahasiswa yang mengumpulkan
                if (count === 0) {
                    submissionsTableBody.innerHTML = `
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: #94A3B8;">
                                Belum ada berkas tugas yang dikumpulkan oleh mahasiswa.
                            </td>
                        </tr>
                    `;
                } else {
                    const mockStudents = [
                        { name: 'Ahmad Fauzi', nim: '230101001', file: 'Tugas_AhmadFauzi.pdf', time: 'Hari ini, 14:20', isLate: false },
                        { name: 'Siti Nurhaliza', nim: '230101014', file: 'Laporan_Siti_Rev1.docx', time: 'Kemarin, 21:05', isLate: false },
                        { name: 'Budi Santoso', nim: '230101032', file: 'Proyek_Budi.zip', time: '2 hari lalu, 18:45', isLate: false },
                        { name: 'Rizky Pratama', nim: '230101045', file: 'Tugas02_Rizky.pdf', time: '3 hari lalu, 08:15', isLate: true },
                    ];

                    let html = '';
                    mockStudents.forEach(s => {
                        const lateBadge = s.isLate
                            ? '<span style="background: #FEF2F2; color: #DC2626; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 800;">Terlambat</span>'
                            : '<span style="background: #ECFDF5; color: #059669; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 800;">Tepat Waktu</span>';

                        html += `
                            <tr>
                                <td>
                                    <div class="student-info-cell">
                                        <div class="student-avatar">${s.name.substring(0, 2).toUpperCase()}</div>
                                        <div class="student-meta">
                                            <strong>${s.name}</strong>
                                            <small>${s.nim}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong style="font-size: 11.5px; color: #039FFA; display: block;">${s.file}</strong>
                                    <small style="color: #64748B; font-size: 10px;">2.4 MB</small>
                                </td>
                                <td style="font-size: 11px; color: #475569;">${s.time}</td>
                                <td>${lateBadge}</td>
                                <td style="text-align: right;">
                                    <button type="button" class="btn-primary-action" style="padding: 4px 9px; font-size: 10.5px;" onclick="alert('Mengunduh berkas simulasi: ${s.file}')">
                                        Unduh
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    submissionsTableBody.innerHTML = html;
                }

                subModal.classList.add('show');
            };

            function closeSubmissionsModal() {
                subModal.classList.remove('show');
            }

            btnCloseSubModal.addEventListener('click', closeSubmissionsModal);
            subModal.addEventListener('click', (e) => {
                if (e.target === subModal) closeSubmissionsModal();
            });
        });
    </script>
</body>
</html>
