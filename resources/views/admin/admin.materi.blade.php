<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Materi Kuliah — KampusLMS Admin</title>
    
    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/admin/admin.materi.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/admin/admin.materi.css') }}">
    @endif
</head>
<body>
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <div class="app-window">

        <!-- Navbar Khusus Admin -->
        <x-navbar-admin />

        <!-- Konten Utama Materi Admin -->
        <main class="admin-content">

            <!-- Topbar Header -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <h1>Manajemen Materi Perkuliahan</h1>
                    </div>
                </div>
                <div class="topbar-right">
                    <button type="button" class="btn-quick-action btn-primary" id="btnOpenAddModal">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Unggah Materi Baru</span>
                    </button>
                </div>
            </header>

            @php
                // Ambil daftar mata kuliah yang aktif di database
                $coursesList = \App\Models\Course::with('lecturer')->orderBy('code')->get();

                // Mockup initial materials data harmonized with existing courses for rich interactive frontend
                $initialMaterials = [
                    [
                        'id' => 1,
                        'course_id' => $coursesList->first()?->id ?? 1,
                        'course_code' => $coursesList->first()?->code ?? 'SI101',
                        'course_name' => $coursesList->first()?->name ?? 'Pemrograman Web',
                        'title' => 'Pengenalan Laravel 12 & Arsitektur MVC Modern',
                        'desc' => 'Slide presentasi pengantar framework Laravel 12, routing, request lifecycle, dan controller dasar.',
                        'type' => 'file',
                        'ext' => 'pdf',
                        'file_name' => 'Modul_01_Pengantar_Laravel12.pdf',
                        'file_size' => '3.8 MB',
                        'url' => '#',
                        'uploader' => 'Super Administrator',
                        'date' => '2 hari yang lalu',
                    ],
                    [
                        'id' => 2,
                        'course_id' => $coursesList->first()?->id ?? 1,
                        'course_code' => $coursesList->first()?->code ?? 'SI101',
                        'course_name' => $coursesList->first()?->name ?? 'Pemrograman Web',
                        'title' => 'Dokumentasi Resmi Laravel Sanctum & API Tokens',
                        'desc' => 'Tautan referensi teknis implementasi bearer token Sanctum untuk integrasi SPA dan endpoint v1.',
                        'type' => 'link',
                        'ext' => 'link',
                        'file_name' => 'laravel.com/docs/12.x/sanctum',
                        'file_size' => 'Web Link',
                        'url' => 'https://laravel.com/docs/sanctum',
                        'uploader' => 'Dosen Pengampu',
                        'date' => '4 hari yang lalu',
                    ],
                    [
                        'id' => 3,
                        'course_id' => $coursesList->skip(1)->first()?->id ?? 2,
                        'course_code' => $coursesList->skip(1)->first()?->code ?? 'SI102',
                        'course_name' => $coursesList->skip(1)->first()?->name ?? 'Basis Data Lanjut',
                        'title' => 'Slide Kuliah: Normalisasi & Indexing Optimization',
                        'desc' => 'Materi pembahasan teknik indexing B-Tree, composite index, dan pencegahan N+1 query.',
                        'type' => 'file',
                        'ext' => 'pptx',
                        'file_name' => 'Pertemuan_03_Database_Optimization.pptx',
                        'file_size' => '8.2 MB',
                        'url' => '#',
                        'uploader' => 'Dosen Pengampu',
                        'date' => '5 hari yang lalu',
                    ],
                    [
                        'id' => 4,
                        'course_id' => $coursesList->skip(2)->first()?->id ?? 3,
                        'course_code' => $coursesList->skip(2)->first()?->code ?? 'SI103',
                        'course_name' => $coursesList->skip(2)->first()?->name ?? 'Analisis & Desain SI',
                        'title' => 'Panduan Standar Penulisan SRS & UML Use Case',
                        'desc' => 'Format template dokumen kebutuhan sistem dan diagram analisis use case.',
                        'type' => 'file',
                        'ext' => 'docx',
                        'file_name' => 'Template_Dokumen_SRS_2026.docx',
                        'file_size' => '1.5 MB',
                        'url' => '#',
                        'uploader' => 'Super Administrator',
                        'date' => '1 minggu yang lalu',
                    ],
                ];
            @endphp

            <!-- Baris Statistik Cepat -->
            <section class="materi-stats-bar">
                <div class="materi-stat-card">
                    <div class="materi-stat-icon icon-blue">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                    <div class="materi-stat-info">
                        <span class="materi-stat-label">Total Materi</span>
                        <span class="materi-stat-value" id="statTotalMateri">{{ count($initialMaterials) }}</span>
                    </div>
                </div>

                <div class="materi-stat-card">
                    <div class="materi-stat-icon icon-orange">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                    </div>
                    <div class="materi-stat-info">
                        <span class="materi-stat-label">Berkas Dokumen</span>
                        <span class="materi-stat-value" id="statTotalDoc">3</span>
                    </div>
                </div>

                <div class="materi-stat-card">
                    <div class="materi-stat-icon icon-green">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                        </svg>
                    </div>
                    <div class="materi-stat-info">
                        <span class="materi-stat-label">Tautan Referensi</span>
                        <span class="materi-stat-value" id="statTotalLink">1</span>
                    </div>
                </div>

                <div class="materi-stat-card">
                    <div class="materi-stat-icon icon-gold">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="materi-stat-info">
                        <span class="materi-stat-label">MK Terlayani</span>
                        <span class="materi-stat-value">{{ $coursesList->count() }} MK</span>
                    </div>
                </div>
            </section>

            <!-- Toolbar Filter & Pencarian -->
            <section class="materi-filter-toolbar">
                <!-- Input Pencarian (Di Kiri) -->
                <div class="materi-search-box">
                    <svg class="materi-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="inputSearchMateri" placeholder="Cari modul, judul materi, atau berkas...">
                </div>

                <!-- Kelompok Filter Dropdown (Di Kanan) -->
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

                    <!-- Dropdown Filter Tipe Materi -->
                    <div class="filter-select-wrapper filter-type-wrapper">
                        <select id="selectFilterType">
                            <option value="all">Semua Tipe Materi</option>
                            <option value="file">📄 Berkas File</option>
                            <option value="link">🔗 Tautan URL</option>
                        </select>
                        <div class="filter-select-arrow">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Grid Kartu Materi -->
            <section class="materials-grid" id="materiCardsGrid">
                @foreach ($initialMaterials as $m)
                    @php
                        $badgeClass = match($m['ext']) {
                            'pdf' => 'badge-type-pdf',
                            'pptx' => 'badge-type-pptx',
                            'docx' => 'badge-type-docx',
                            default => 'badge-type-link',
                        };
                        $typeIcon = $m['type'] === 'file' ? '📄' : '🔗';
                    @endphp
                    <div class="material-card" 
                         data-id="{{ $m['id'] }}"
                         data-course-id="{{ $m['course_id'] }}"
                         data-type="{{ $m['type'] }}"
                         data-title="{{ $m['title'] }}"
                         data-desc="{{ $m['desc'] }}"
                         data-filename="{{ $m['file_name'] }}"
                         data-url="{{ $m['url'] }}">
                        <div>
                            <div class="material-card-top">
                                <div class="badge-tag-wrap">
                                    <span class="badge-mk-code">{{ $m['course_code'] }}</span>
                                    <span class="badge-file-type {{ $badgeClass }}">
                                        <span>{{ $typeIcon }}</span>
                                        <span>{{ strtoupper($m['ext']) }}</span>
                                    </span>
                                </div>
                                <div class="btn-actions">
                                    <button type="button" class="btn-icon btn-icon-edit btn-edit-material" title="Edit Materi">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    </button>
                                    <button type="button" class="btn-icon btn-icon-danger btn-delete-material" title="Hapus Materi">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <h3 class="material-card-title">{{ $m['title'] }}</h3>
                            <p class="material-card-desc">{{ $m['desc'] }}</p>

                            <div class="material-meta-pill">
                                <span class="file-name-text" title="{{ $m['file_name'] }}">{{ $m['file_name'] }}</span>
                                <span class="file-size-badge">{{ $m['file_size'] }}</span>
                            </div>
                        </div>

                        <div class="material-card-footer">
                            <div class="uploader-info">
                                <div class="uploader-avatar">
                                    {{ strtoupper(substr($m['uploader'], 0, 2)) }}
                                </div>
                                <div>
                                    <span class="uploader-name">{{ $m['uploader'] }}</span>
                                    <span class="uploader-date">{{ $m['date'] }}</span>
                                </div>
                            </div>

                            <div class="material-card-actions">
                                @if($m['type'] === 'file')
                                    <a href="#" class="btn-materi-view" onclick="event.preventDefault(); alert('Mengunduh berkas simulasi: {{ $m['file_name'] }}');">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        <span>Unduh</span>
                                    </a>
                                @else
                                    <a href="{{ $m['url'] }}" target="_blank" rel="noopener noreferrer" class="btn-materi-view">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                        <span>Buka Link</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Empty State (Hidden by default) -->
                <div class="materi-empty-state" id="materiEmptyState" style="display: none;">
                    <div class="materi-empty-icon">📂</div>
                    <div class="materi-empty-title">Tidak Ada Materi yang Cocok</div>
                    <p class="materi-empty-desc">Tidak ditemukan materi untuk filter atau kata kunci pencarian yang Anda pilih.</p>
                    <button type="button" class="btn-primary-action" onclick="resetFilters()" style="margin: 0 auto;">
                        Reset Filter
                    </button>
                </div>
            </section>

        </main>

        <x-footer />
    </div>

    <!-- MODAL POPUP: TAMBAH / EDIT MATERI -->
    <div class="materi-modal-overlay" id="materiModal">
        <div class="materi-modal-card">
            <div class="materi-modal-header">
                <h3 id="modalTitle">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Unggah / Tambah Materi Baru</span>
                </h3>
                <button type="button" class="btn-close-modal" id="btnCloseModal">&times;</button>
            </div>

            <form id="formMateri" class="materi-modal-body">
                <input type="hidden" id="editMateriId" value="">

                <!-- Pilih Mata Kuliah (Searchable Dropdown dengan Scrollbar) -->
                <div class="form-group" style="margin-bottom: 14px;">
                    <label style="font-size: 12.5px; font-weight: 800; color: #334155; margin-bottom: 6px; display: block;">Mata Kuliah Tujuan <span class="required" style="color: #EF4444;">*</span></label>
                    
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

                    <!-- Live Preview Info Dosen Pengampu & SKS (Opsi 2) -->
                    <div id="courseLecturerInfoBox" style="display: none; margin-top: 9px; padding: 10px 14px; background: #F8FAFC; border: 1px solid rgba(3, 159, 250, 0.22); border-radius: 10px; align-items: center; justify-content: space-between; gap: 10px; transition: all 0.2s ease;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(3, 159, 250, 0.12); color: #039FFA; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                                👨‍🏫
                            </div>
                            <div>
                                <span style="font-size: 10.5px; font-weight: 800; color: #64748B; text-transform: uppercase; letter-spacing: 0.3px; display: block;">Dosen Pengampu:</span>
                                <strong id="courseLecturerName" style="font-size: 13px; color: #0F172A; font-weight: 800;">-</strong>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span id="courseSksBadge" style="background: rgba(249, 184, 4, 0.14); color: #D97706; padding: 3px 9px; border-radius: 6px; font-size: 11px; font-weight: 800; border: 1px solid rgba(249, 184, 4, 0.3);">
                                3 SKS
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Judul Materi -->
                <div class="form-group" style="margin-bottom: 14px;">
                    <label style="font-size: 12.5px; font-weight: 800; color: #334155; margin-bottom: 6px; display: block;">Judul Materi <span class="required" style="color: #EF4444;">*</span></label>
                    <input type="text" id="modalMateriTitle" class="form-control" placeholder="Contoh: Modul 04 - Pengelolaan State & Validasi" required style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid rgba(3, 159, 250, 0.25); font-size: 13px;">
                </div>

                <!-- Tipe Materi (File vs Link) -->
                <div class="form-group" style="margin-bottom: 14px;">
                    <label style="font-size: 12.5px; font-weight: 800; color: #334155; margin-bottom: 6px; display: block;">Tipe Materi <span class="required" style="color: #EF4444;">*</span></label>
                    <div class="type-switch-container">
                        <label class="type-option-radio selected" id="optRadioFile">
                            <input type="radio" name="materiType" value="file" checked>
                            <div>
                                <strong style="font-size: 12.5px; display: block;">📄 Berkas Dokumen</strong>
                                <small style="color: #64748B; font-size: 11px;">PDF, PPTX, DOCX (Maks 20MB)</small>
                            </div>
                        </label>
                        <label class="type-option-radio" id="optRadioLink">
                            <input type="radio" name="materiType" value="link">
                            <div>
                                <strong style="font-size: 12.5px; display: block;">🔗 Tautan Eksternal</strong>
                                <small style="color: #64748B; font-size: 11px;">YouTube, Google Drive, dsb.</small>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Input Berkas File (Bila Tipe = File) -->
                <div class="form-group" id="groupFileInput" style="margin-bottom: 14px;">
                    <label style="font-size: 12.5px; font-weight: 800; color: #334155; margin-bottom: 6px; display: block;">Pilih Berkas Materi</label>
                    <div class="file-drop-area" onclick="document.getElementById('modalFileInput').click()">
                        <div style="font-size: 28px; margin-bottom: 6px;">📤</div>
                        <strong style="font-size: 13px; color: var(--admin-primary); display: block;" id="labelFileName">Klik untuk memilih berkas dokumen</strong>
                        <span style="font-size: 11px; color: #94A3B8;">Format didukung: .pdf, .pptx, .docx (Maksimal 20 MB)</span>
                        <input type="file" id="modalFileInput" accept=".pdf,.pptx,.docx,.zip,.txt">
                    </div>
                </div>

                <!-- Input URL Link (Bila Tipe = Link) -->
                <div class="form-group" id="groupLinkInput" style="margin-bottom: 14px; display: none;">
                    <label style="font-size: 12.5px; font-weight: 800; color: #334155; margin-bottom: 6px; display: block;">URL / Tautan Materi Eksternal <span class="required" style="color: #EF4444;">*</span></label>
                    <input type="url" id="modalMateriUrl" class="form-control" placeholder="https://example.com/slide-materi" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid rgba(3, 159, 250, 0.25); font-size: 13px;">
                </div>

                <!-- Deskripsi Materi -->
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-size: 12.5px; font-weight: 800; color: #334155; margin-bottom: 6px; display: block;">Deskripsi / Catatan Tambahan</label>
                    <textarea id="modalMateriDesc" rows="3" class="form-control" placeholder="Ringkasan isi materi untuk panduan mahasiswa..." style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid rgba(3, 159, 250, 0.25); font-size: 13px; resize: vertical;"></textarea>
                </div>

                <!-- Tombol Submit & Cancel -->
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn-icon" id="btnCancelModal" style="padding: 9px 18px; font-size: 13px;">Batal</button>
                    <button type="submit" class="btn-primary-action" style="font-size: 13px; padding: 9px 20px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span id="btnSubmitText">Simpan Materi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Interaktif Frontend -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentCourseFilter = 'all';
            let currentTypeFilter   = 'all';
            let currentSearchQuery  = '';

            const modalOverlay = document.getElementById('materiModal');
            const btnOpenAddModal = document.getElementById('btnOpenAddModal');
            const btnCloseModal = document.getElementById('btnCloseModal');
            const btnCancelModal = document.getElementById('btnCancelModal');
            const formMateri = document.getElementById('formMateri');
            const modalTitle = document.getElementById('modalTitle');
            const editMateriId = document.getElementById('editMateriId');
            const btnSubmitText = document.getElementById('btnSubmitText');

            const optRadioFile = document.getElementById('optRadioFile');
            const optRadioLink = document.getElementById('optRadioLink');
            const groupFileInput = document.getElementById('groupFileInput');
            const groupLinkInput = document.getElementById('groupLinkInput');
            const modalFileInput = document.getElementById('modalFileInput');
            const labelFileName = document.getElementById('labelFileName');

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
                return Array.from(document.querySelectorAll('.combobox-option-item')).filter(el => el.style.display !== 'none');
            }

            function updateHighlightedOption(visibleItems) {
                document.querySelectorAll('.combobox-option-item').forEach(el => el.classList.remove('highlighted'));
                if (highlightedIndex >= 0 && highlightedIndex < visibleItems.length) {
                    const target = visibleItems[highlightedIndex];
                    target.classList.add('highlighted');
                    target.scrollIntoView({ block: 'nearest' });
                }
            }

            function selectCourseItem(item) {
                highlightedIndex = -1;
                document.querySelectorAll('.combobox-option-item').forEach(el => el.classList.remove('highlighted'));

                if (!item) {
                    modalCourseSelect.value = '';
                    modalCourseSelect.dataset.code = '';
                    modalCourseSelect.dataset.name = '';
                    comboboxSearchInput.value = '';
                    courseLecturerInfoBox.style.display = 'none';
                    document.querySelectorAll('.combobox-option-item').forEach(el => el.classList.remove('selected'));
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

                document.querySelectorAll('.combobox-option-item').forEach(el => el.classList.remove('selected'));
                item.classList.add('selected');

                courseLecturerName.textContent = lecturer;
                courseSksBadge.textContent = `${sks} SKS`;
                courseLecturerInfoBox.style.display = 'flex';

                courseComboboxWrapper.classList.remove('open');
            }

            document.querySelectorAll('.combobox-option-item').forEach(item => {
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
                document.querySelectorAll('.combobox-option-item').forEach(item => {
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
                document.querySelectorAll('.combobox-option-item').forEach(item => item.style.display = 'flex');
                comboboxEmptyMessage.style.display = 'none';
            });

            comboboxSearchInput.addEventListener('click', function() {
                if (!courseComboboxWrapper.classList.contains('open')) {
                    courseComboboxWrapper.classList.add('open');
                    document.querySelectorAll('.combobox-option-item').forEach(item => item.style.display = 'flex');
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
                const cards = document.querySelectorAll('.material-card');
                let visibleCount = 0;

                cards.forEach(card => {
                    const courseId = card.dataset.courseId;
                    const type = card.dataset.type;
                    const text = card.textContent.toLowerCase();

                    const matchCourse = (currentCourseFilter === 'all' || courseId === currentCourseFilter);
                    const matchType   = (currentTypeFilter === 'all' || type === currentTypeFilter);
                    const matchSearch = (!currentSearchQuery || text.includes(currentSearchQuery));

                    if (matchCourse && matchType && matchSearch) {
                        card.style.display = 'flex';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                const emptyState = document.getElementById('materiEmptyState');
                if (emptyState) {
                    emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            }

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

            document.getElementById('selectFilterType').addEventListener('change', function() {
                currentTypeFilter = this.value;
                applyFilters();
            });

            document.getElementById('inputSearchMateri').addEventListener('input', function() {
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
                document.getElementById('selectFilterType').value = 'all';
                currentTypeFilter = 'all';
                document.getElementById('inputSearchMateri').value = '';
                currentSearchQuery = '';
                applyFilters();
            };

            // 2. MODAL TOGGLES
            function openModal(isEdit = false, card = null) {
                formMateri.reset();
                editMateriId.value = '';
                labelFileName.textContent = 'Klik untuk memilih berkas dokumen';

                if (isEdit && card) {
                    modalTitle.querySelector('span').textContent = 'Edit Materi Perkuliahan';
                    btnSubmitText.textContent = 'Perbarui Materi';
                    editMateriId.value = card.dataset.id;
                    document.getElementById('modalMateriTitle').value = card.dataset.title;
                    document.getElementById('modalMateriDesc').value = card.dataset.desc;

                    const type = card.dataset.type;
                    if (type === 'link') {
                        optRadioLink.querySelector('input').checked = true;
                        document.getElementById('modalMateriUrl').value = card.dataset.url;
                        switchType('link');
                    } else {
                        optRadioFile.querySelector('input').checked = true;
                        labelFileName.textContent = 'Berkas: ' + card.dataset.filename;
                        switchType('file');
                    }

                    const matchingItem = document.querySelector(`.combobox-option-item[data-id="${card.dataset.courseId}"]`);
                    if (matchingItem) {
                        selectCourseItem(matchingItem);
                    }
                } else {
                    modalTitle.querySelector('span').textContent = 'Unggah / Tambah Materi Baru';
                    btnSubmitText.textContent = 'Simpan Materi';
                    switchType('file');
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

            function switchType(type) {
                if (type === 'file') {
                    optRadioFile.classList.add('selected');
                    optRadioLink.classList.remove('selected');
                    groupFileInput.style.display = 'block';
                    groupLinkInput.style.display = 'none';
                    document.getElementById('modalMateriUrl').removeAttribute('required');
                } else {
                    optRadioLink.classList.add('selected');
                    optRadioFile.classList.remove('selected');
                    groupFileInput.style.display = 'none';
                    groupLinkInput.style.display = 'block';
                    document.getElementById('modalMateriUrl').setAttribute('required', 'required');
                }
            }

            optRadioFile.addEventListener('click', () => switchType('file'));
            optRadioLink.addEventListener('click', () => switchType('link'));

            modalFileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    labelFileName.textContent = `Berkas dipilih: ${this.files[0].name} (${(this.files[0].size / (1024*1024)).toFixed(1)} MB)`;
                }
            });

            // 3. SUBMIT FORM (CLIENT-SIDE SIMULATION)
            formMateri.addEventListener('submit', function(e) {
                e.preventDefault();

                const courseId = modalCourseSelect.value;
                if (!courseId) {
                    alert('Silakan pilih mata kuliah tujuan terlebih dahulu.');
                    comboboxSearchInput.focus();
                    courseComboboxWrapper.classList.add('open');
                    return;
                }
                const courseCode = modalCourseSelect.dataset.code || 'MK';
                const title = document.getElementById('modalMateriTitle').value;
                const desc = document.getElementById('modalMateriDesc').value;
                const isFile = optRadioFile.querySelector('input').checked;
                const type = isFile ? 'file' : 'link';

                let fileName = 'Dokumen_Materi.pdf';
                let fileSize = '2.5 MB';
                let ext = 'pdf';
                let url = '#';

                if (isFile) {
                    if (modalFileInput.files && modalFileInput.files[0]) {
                        fileName = modalFileInput.files[0].name;
                        fileSize = (modalFileInput.files[0].size / (1024*1024)).toFixed(1) + ' MB';
                        ext = fileName.split('.').pop().toLowerCase() || 'pdf';
                    }
                } else {
                    url = document.getElementById('modalMateriUrl').value;
                    fileName = url.replace('https://', '').replace('http://', '');
                    fileSize = 'Web Link';
                    ext = 'link';
                }

                const badgeClass = ext === 'pdf' ? 'badge-type-pdf' : (ext === 'pptx' ? 'badge-type-pptx' : (ext === 'docx' ? 'badge-type-docx' : 'badge-type-link'));
                const typeIcon = isFile ? '📄' : '🔗';

                const isEditing = Boolean(editMateriId.value);
                const grid = document.getElementById('materiCardsGrid');

                if (isEditing) {
                    const card = document.querySelector(`.material-card[data-id="${editMateriId.value}"]`);
                    if (card) {
                        card.dataset.courseId = courseId;
                        card.dataset.type = type;
                        card.dataset.title = title;
                        card.dataset.desc = desc;
                        card.dataset.filename = fileName;
                        card.dataset.url = url;

                        card.querySelector('.badge-mk-code').textContent = courseCode;
                        card.querySelector('.badge-file-type').className = `badge-file-type ${badgeClass}`;
                        card.querySelector('.badge-file-type').innerHTML = `<span>${typeIcon}</span><span>${ext.toUpperCase()}</span>`;
                        card.querySelector('.material-card-title').textContent = title;
                        card.querySelector('.material-card-desc').textContent = desc;
                        card.querySelector('.file-name-text').textContent = fileName;
                        card.querySelector('.file-name-text').title = fileName;
                        card.querySelector('.file-size-badge').textContent = fileSize;
                    }
                    showFloatingToast('Materi perkuliahan berhasil diperbarui!');
                } else {
                    const newId = Date.now();
                    const card = document.createElement('div');
                    card.className = 'material-card';
                    card.dataset.id = newId;
                    card.dataset.courseId = courseId;
                    card.dataset.type = type;
                    card.dataset.title = title;
                    card.dataset.desc = desc;
                    card.dataset.filename = fileName;
                    card.dataset.url = url;

                    card.innerHTML = `
                        <div>
                            <div class="material-card-top">
                                <div class="badge-tag-wrap">
                                    <span class="badge-mk-code">${courseCode}</span>
                                    <span class="badge-file-type ${badgeClass}">
                                        <span>${typeIcon}</span>
                                        <span>${ext.toUpperCase()}</span>
                                    </span>
                                </div>
                                <div class="btn-actions">
                                    <button type="button" class="btn-icon btn-icon-edit btn-edit-material" title="Edit Materi">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    </button>
                                    <button type="button" class="btn-icon btn-icon-danger btn-delete-material" title="Hapus Materi">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <h3 class="material-card-title">${title}</h3>
                            <p class="material-card-desc">${desc}</p>
                            <div class="material-meta-pill">
                                <span class="file-name-text" title="${fileName}">${fileName}</span>
                                <span class="file-size-badge">${fileSize}</span>
                            </div>
                        </div>
                        <div class="material-card-footer">
                            <div class="uploader-info">
                                <div class="uploader-avatar">AD</div>
                                <div>
                                    <span class="uploader-name">Super Administrator</span>
                                    <span class="uploader-date">Baru saja</span>
                                </div>
                            </div>
                            <div class="material-card-actions">
                                ${isFile ? `
                                    <a href="#" class="btn-materi-view" onclick="event.preventDefault(); alert('Mengunduh berkas: ${fileName}');">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        <span>Unduh</span>
                                    </a>
                                ` : `
                                    <a href="${url}" target="_blank" rel="noopener noreferrer" class="btn-materi-view">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                        <span>Buka Link</span>
                                    </a>
                                `}
                            </div>
                        </div>
                    `;

                    grid.prepend(card);
                    bindCardEvents(card);
                    updateCounters();
                    showFloatingToast('Materi baru berhasil dipublikasikan!');
                }

                closeModal();
                applyFilters();
            });

            // 4. BIND EDIT & DELETE BUTTONS
            function bindCardEvents(card) {
                const btnEdit = card.querySelector('.btn-edit-material');
                const btnDelete = card.querySelector('.btn-delete-material');

                if (btnEdit) {
                    btnEdit.onclick = () => openModal(true, card);
                }

                if (btnDelete) {
                    btnDelete.onclick = function() {
                        const title = card.querySelector('.material-card-title').textContent;
                        if (confirm(`Apakah Anda yakin ingin menghapus materi "${title}"?`)) {
                            card.style.transition = 'all 0.3s ease';
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.9)';
                            setTimeout(() => {
                                card.remove();
                                updateCounters();
                                applyFilters();
                                showFloatingToast('Materi berhasil dihapus dari sistem.');
                            }, 300);
                        }
                    };
                }
            }

            document.querySelectorAll('.material-card').forEach(bindCardEvents);

            // 5. UPDATE COUNTERS
            function updateCounters() {
                const all = document.querySelectorAll('.material-card');
                const docs = document.querySelectorAll('.material-card[data-type="file"]');
                const links = document.querySelectorAll('.material-card[data-type="link"]');

                document.getElementById('statTotalMateri').textContent = all.length;
                document.getElementById('statTotalDoc').textContent = docs.length;
                document.getElementById('statTotalLink').textContent = links.length;
            }

            // 6. FLOATING TOAST NOTIFICATION
            function showFloatingToast(message) {
                const toastContainer = document.getElementById('globalToastContainer');
                if (!toastContainer) return;

                const toast = document.createElement('div');
                toast.className = 'global-toast-msg';
                toast.style.cssText = 'background-color: #ffffff; color: #166534; padding: 14px 18px; border-radius: 10px; border-left: 5px solid #22c55e; font-family: "Nunito", sans-serif; font-size: 14px; font-weight: 600; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; animation: fadeIn 0.3s ease;';
                toast.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        <span>${message}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" style="background: transparent; border: none; color: #9ca3af; cursor: pointer; font-size: 18px; line-height: 1; padding-left: 10px;">&times;</button>
                `;

                toastContainer.prepend(toast);
                setTimeout(() => {
                    if (toast.parentElement) toast.remove();
                }, 4000);
            }
        });
    </script>
</body>
</html>
