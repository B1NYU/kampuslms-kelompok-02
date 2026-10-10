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
        @vite(['resources/css/app.css', 'resources/css/admin/admin.materi.css', 'resources/css/dosen/dosen.materi.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/admin/admin.materi.css') }}">
        <link rel="stylesheet" href="{{ asset('css/dosen/dosen.materi.css') }}">
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
                    <button type="button" class="btn-primary-action" id="btnOpenUploadMaterialModal" style="padding: 9px 18px; font-size: 13px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Unggah Materi Baru</span>
                    </button>
                </div>
            </header>

            @php
                $materials = $materials ?? \App\Models\Material::with(['course', 'uploader'])->latest()->get();
                $coursesList = $coursesList ?? \App\Models\Course::with('lecturer')->orderBy('code')->get();
                $totalDoc = $materials->where('type', 'file')->count();
                $totalLink = $materials->where('type', 'link')->count();
            @endphp

            @if(session('success'))
                <div style="background:#ECFDF5; border:1px solid #10B981; color:#065F46; padding: 12px 16px; border-radius:10px; font-weight:700; margin-bottom:20px; font-size:13px;">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if($errors->any() && !old('edit_id'))
                <div style="background:#FEF2F2; border:1px solid #EF4444; color:#991B1B; padding: 12px 16px; border-radius:10px; font-weight:700; margin-bottom:20px; font-size:13px;">
                    ❌ Terdapat kesalahan pada form.
                    <ul style="margin-top: 6px; margin-bottom: 0; padding-left: 20px; font-weight: 600;">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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
                        <span class="materi-stat-value" id="statTotalMateri">{{ $materials->count() }}</span>
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
                        <span class="materi-stat-value" id="statTotalDoc">{{ $totalDoc }}</span>
                    </div>
                </div>

                <div class="materi-stat-card">
                    <div class="materi-stat-icon icon-gold">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                        </svg>
                    </div>
                    <div class="materi-stat-info">
                        <span class="materi-stat-label">Tautan / Link</span>
                        <span class="materi-stat-value" id="statTotalLink">{{ $totalLink }}</span>
                    </div>
                </div>

                <div class="materi-stat-card">
                    <div class="materi-stat-icon icon-green">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <div class="materi-stat-info">
                        <span class="materi-stat-label">Mata Kuliah Aktif</span>
                        <span class="materi-stat-value" id="statTotalMk">{{ $coursesList->count() }}</span>
                    </div>
                </div>
            </section>

            <!-- Toolbar & Filter -->
            <section class="materi-filter-toolbar">
                <div class="materi-search-box">
                    <svg class="materi-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="inputSearchMateri" placeholder="Cari judul materi perkuliahan..." autocomplete="off">
                </div>

                <div class="toolbar-right-filters">
                    <!-- Dropdown Custom Filter Mata Kuliah -->
                    <div class="custom-filter-dropdown" id="courseFilterDropdown">
                        <button type="button" class="filter-dropdown-btn" id="courseFilterBtn">
                            <span class="filter-dropdown-text" id="courseFilterText">Semua Mata Kuliah ({{ $coursesList->count() }} Terdaftar)</span>
                            <svg class="filter-dropdown-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div class="filter-dropdown-menu">
                            <ul class="filter-options-scroll" id="courseFilterOptions">
                                <li class="filter-option-item active" data-value="all" data-label="Semua Mata Kuliah ({{ $coursesList->count() }} Terdaftar)">
                                    <span class="filter-option-name">Semua Mata Kuliah</span>
                                </li>
                                @foreach ($coursesList as $c)
                                    <li class="filter-option-item" data-value="{{ $c->id }}" data-label="{{ $c->code }} • {{ $c->name }}">
                                        <span class="filter-option-code">{{ $c->code }}</span>
                                        <span class="filter-option-name">{{ $c->name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <input type="hidden" id="selectFilterCourse" value="all">

                    <!-- Filter Tipe -->
                    <div class="filter-select-wrapper filter-type-wrapper">
                        <select id="selectFilterType">
                            <option value="all">Semua Tipe Format</option>
                            <option value="file">📄 Berkas Dokumen</option>
                            <option value="link">🔗 Tautan / URL</option>
                        </select>
                        <svg class="filter-select-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                </div>
            </section>

            <!-- Grid Kartu Materi -->
            <section class="materials-grid" id="materialsGrid">
                @foreach ($materials as $m)
                    @php
                        $courseCode = $m->course ? $m->course->code : 'UMUM';
                        $courseName = $m->course ? $m->course->name : 'Mata Kuliah Umum';
                        $uploaderName = $m->uploader ? $m->uploader->name : 'Admin LMS';

                        $badgeClass = 'badge-type-pdf';
                        $typeIcon = '📄';
                        $ext = 'PDF';

                        if ($m->type === 'link') {
                            $badgeClass = 'badge-type-link';
                            $typeIcon = '🔗';
                            $ext = 'LINK';
                        } elseif ($m->original_name) {
                            $fileExt = strtolower(pathinfo($m->original_name, PATHINFO_EXTENSION));
                            if (in_array($fileExt, ['ppt', 'pptx'])) {
                                $badgeClass = 'badge-type-pptx';
                                $typeIcon = '📊';
                                $ext = 'PPTX';
                            } elseif (in_array($fileExt, ['doc', 'docx'])) {
                                $badgeClass = 'badge-type-docx';
                                $typeIcon = '📝';
                                $ext = 'DOCX';
                            }
                        }

                        $fileSizeStr = $m->file_size ? number_format($m->file_size / (1024 * 1024), 1) . ' MB' : '-';
                        $fileNameStr = $m->type === 'file' ? ($m->original_name ?? 'Berkas Dokumen') : $m->external_url;
                    @endphp

                    <div class="material-card" 
                          data-course-id="{{ $m->course_id }}" 
                          data-type="{{ $m->type }}" 
                          data-title="{{ $m->title }}"
                          data-desc="{{ $m->description }}"
                          data-filename="{{ $fileNameStr }}"
                          data-url="{{ $m->external_url }}">
                        <div>
                            <div class="material-card-top">
                                <div class="badge-tag-wrap">
                                    <span class="badge-mk-code" title="{{ $courseName }}">{{ $courseCode }}</span>
                                    <span class="badge-file-type {{ $badgeClass }}">
                                        <span>{{ $typeIcon }}</span>
                                        <span>{{ strtoupper($ext) }}</span>
                                    </span>
                                </div>
                                <div class="btn-actions" style="display:flex; gap:6px;">
                                    <button type="button" class="btn-icon btn-icon-edit btn-edit-material" title="Edit Materi"
                                            data-id="{{ $m->id }}"
                                            data-type="{{ $m->type }}"
                                            data-title="{{ $m->title }}"
                                            data-description="{{ $m->description }}"
                                            data-url="{{ $m->external_url }}"
                                            data-filename="{{ $m->original_name }}">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    </button>
                                    <form action="{{ route('admin.materi.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')" style="margin:0; padding:0; display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon btn-icon-danger" title="Hapus Materi" style="border:none; cursor:pointer;">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <h3 class="material-card-title">{{ $m->title }}</h3>
                            <p class="material-card-desc">{{ $m->description }}</p>

                            <div class="material-meta-pill">
                                <span class="file-name-text" title="{{ $fileNameStr }}">{{ $fileNameStr }}</span>
                                <span class="file-size-badge">{{ $fileSizeStr }}</span>
                            </div>
                        </div>

                        <div class="material-card-footer">
                            <div class="uploader-info">
                                <div class="uploader-avatar">
                                    {{ strtoupper(substr($uploaderName, 0, 2)) }}
                                </div>
                                <div>
                                    <span class="uploader-name">{{ $uploaderName }}</span>
                                    <span class="uploader-date">{{ $m->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div class="material-card-actions">
                                @if($m->type === 'file')
                                    <a href="{{ route('admin.materi.download', $m->id) }}" class="btn-materi-view">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        <span>Unduh</span>
                                    </a>
                                @else
                                    <a href="{{ $m->external_url }}" target="_blank" rel="noopener noreferrer" class="btn-materi-view">
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

    <!-- MODAL FORM UNGGAH MATERI (Menyesuaikan Role Dosen) -->
    <div class="dosen-modal-overlay" id="materialModalOverlay">
        <div class="dosen-modal-card" style="max-width: 620px; width: 95%;">
            <div class="dosen-modal-header">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="section-header-icon" style="width: 36px; height: 36px; background: rgba(3, 159, 250, 0.1); color: #039FFA;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                    </div>
                    <div>
                        <h3>Publikasi Materi Perkuliahan</h3>
                        <span style="font-size: 11px; color: #64748B; font-weight: 600;">Unggah dokumen PDF, slide PPTX, atau tautan web</span>
                    </div>
                </div>
                <button type="button" class="btn-close-modal" id="btnCloseMaterialModal" aria-label="Tutup modal">&times;</button>
            </div>

            <!-- FORM TERHUBUNG DENGAN LARAVEL -->
            <form id="formUploadMaterial" action="{{ route('admin.materi.store') }}" method="POST" enctype="multipart/form-data" style="margin: 0; display: flex; flex-direction: column;">
                @csrf
                <input type="hidden" name="type" id="materialType" value="{{ old('type', 'file') }}">

                <div class="dosen-modal-body" style="max-height: 70vh; overflow-y: auto;">

                    @if ($errors->any() && !old('edit_id'))
                        <div style="background:#FEF2F2; border:1px solid #FECACA; color:#B91C1C; border-radius:10px; padding:10px 12px; font-size:12.5px; font-weight:600; margin-bottom:12px;">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="materialCourse">Mata Kuliah Aktif <span class="required">*</span></label>
                            <select id="materialCourse" name="course_id" class="form-select" required {{ $coursesList->isEmpty() ? 'disabled' : '' }}>
                                <option value="">Pilih Mata Kuliah...</option>
                                @forelse ($coursesList as $c)
                                    <option value="{{ $c->id }}" data-code="{{ $c->code }}" @selected((int) old('course_id') === $c->id)>{{ $c->code }} - {{ $c->name }}</option>
                                @empty
                                    <option value="">Belum ada mata kuliah aktif</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="materialSession">Pertemuan Ke-</label>
                            {{-- Tidak ber-name: tabel materials tidak punya kolom pertemuan, jadi nilainya tidak dikirim. --}}
                            <select id="materialSession" class="form-select">
                                @for ($i = 1; $i <= 16; $i++)
                                    <option value="{{ $i }}" {{ $i === 6 ? 'selected' : '' }}>Pertemuan {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="materialTitle">Judul Materi <span class="required">*</span></label>
                        <input type="text" id="materialTitle" name="title" class="form-control" placeholder="Contoh: Modul 06 - Autentikasi Multi-Role Laravel" value="{{ old('title') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Pilih Format Materi <span class="required">*</span></label>
                        <div class="type-selector-group">
                            <div class="type-pill active" data-type="file">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                Berkas File (PDF / PPTX)
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

                    <div id="fileUploadContainer" class="form-group">
                        <label>Unggah Berkas File (PDF / PPTX)</label>
                        <div class="file-dropzone" id="materialDropzone" style="position: relative;">
                            <svg class="file-dropzone-icon" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            <span class="file-dropzone-text" id="dropzoneText">Klik untuk memilih file PDF atau PPTX, atau seret berkas ke sini</span>
                            <span class="file-dropzone-sub">Maksimal ukuran file: 50MB (Format PDF, PPT, PPTX)</span>

                            {{-- Input menutupi seluruh dropzone (transparan): klik & seret ditangani browser --}}
                            <input type="file" id="materialFileInput" name="file" accept=".pdf,.pptx,.ppt" title=" "
                                   style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; display: block;">
                        </div>

                        {{-- Kotak status berkas: gaya inline agar tidak bergantung pada CSS lain --}}
                        <div id="fileStatus" role="status" aria-live="polite"
                             style="display: none; margin-top: 10px; padding: 10px 14px; border-radius: 10px; font-size: 13px; font-weight: 700; word-break: break-all;"></div>
                    </div>

                    <div id="linkInputContainer" class="form-group" style="display: none;">
                        <label for="materialUrl">URL Tautan / Link Materi <span class="required">*</span></label>
                        <input type="url" id="materialUrl" name="external_url" class="form-control" placeholder="https://laravel.com/docs/12.x/authentication" value="{{ old('external_url') }}">
                    </div>

                    <div class="form-group">
                        <label for="materialDesc">Catatan Pembelajaran / Petunjuk</label>
                        <textarea id="materialDesc" name="description" class="form-textarea" rows="2" placeholder="Tulis ringkasan atau instruksi bagi mahasiswa...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="dosen-modal-footer">
                    <button type="button" class="btn-secondary-action" id="btnCancelMaterialModal">Batal</button>
                    <button type="submit" class="btn-primary-action">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Unggah &amp; Publikasikan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL FORM EDIT MATERI -->
    <div class="dosen-modal-overlay" id="editMaterialModalOverlay">
        <div class="dosen-modal-card" style="max-width: 620px; width: 95%;">
            <div class="dosen-modal-header">
                <div>
                    <h3>Edit Materi Perkuliahan</h3>
                    <span style="font-size: 11px; color: #64748B; font-weight: 600;">Ubah judul, catatan, atau ganti berkas / tautan</span>
                </div>
                <button type="button" class="btn-close-modal" id="btnCloseEditModal" aria-label="Tutup modal">&times;</button>
            </div>

            <form id="formEditMaterial" action="#" method="POST" enctype="multipart/form-data" style="margin: 0; display: flex; flex-direction: column;"
                  data-action-template="{{ route('admin.materi.update', ['material' => '__ID__']) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="edit_id" id="editMaterialId" value="{{ old('edit_id') }}">

                <div class="dosen-modal-body" style="max-height: 70vh; overflow-y: auto;">

                    @if ($errors->editMaterial->any())
                        <div style="background:#FEF2F2; border:1px solid #FECACA; color:#B91C1C; border-radius:10px; padding:10px 12px; font-size:12.5px; font-weight:600; margin-bottom:12px;">
                            @foreach ($errors->editMaterial->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="editMaterialTitle">Judul Materi <span class="required">*</span></label>
                        <input type="text" id="editMaterialTitle" name="title" class="form-control" required>
                    </div>

                    <div id="editFileContainer" class="form-group">
                        <label for="editMaterialFile">Ganti Berkas (opsional)</label>
                        <div id="editCurrentFile" style="font-size:12.5px; color:#64748B; font-weight:600; margin-bottom:6px; word-break:break-all;"></div>
                        <input type="file" id="editMaterialFile" name="file" accept=".pdf,.pptx,.ppt" class="form-control">
                        <span style="font-size:11.5px; color:#64748B;">Kosongkan jika tidak ingin mengganti berkas. Maks 50MB (PDF, PPT, PPTX).</span>
                    </div>

                    <div id="editLinkContainer" class="form-group" style="display: none;">
                        <label for="editMaterialUrl">URL Tautan / Link Materi <span class="required">*</span></label>
                        <input type="url" id="editMaterialUrl" name="external_url" class="form-control" placeholder="https://...">
                    </div>

                    <div class="form-group">
                        <label for="editMaterialDesc">Catatan Pembelajaran / Petunjuk</label>
                        <textarea id="editMaterialDesc" name="description" class="form-textarea" rows="2"></textarea>
                    </div>
                </div>

                <div class="dosen-modal-footer">
                    <button type="button" class="btn-secondary-action" id="btnCancelEditModal">Batal</button>
                    <button type="submit" class="btn-primary-action">Simpan Perubahan</button>
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

            // 1. FILTERING & SEARCH KARTU MATERI
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

            const selectFilterType = document.getElementById('selectFilterType');
            if (selectFilterType) {
                selectFilterType.addEventListener('change', function() {
                    currentTypeFilter = this.value;
                    applyFilters();
                });
            }

            const inputSearchMateri = document.getElementById('inputSearchMateri');
            if (inputSearchMateri) {
                inputSearchMateri.addEventListener('input', function() {
                    currentSearchQuery = this.value.toLowerCase().trim();
                    applyFilters();
                });
            }

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
                if (selectFilterType) selectFilterType.value = 'all';
                currentTypeFilter = 'all';
                if (inputSearchMateri) inputSearchMateri.value = '';
                currentSearchQuery = '';
                applyFilters();
            };

            // 2. MODAL UNGGAH MATERI (SESUAI ROLE DOSEN)
            const materialModal = document.getElementById('materialModalOverlay');
            const btnOpenUpload = document.getElementById('btnOpenUploadMaterialModal');
            const btnCloseMaterialModal = document.getElementById('btnCloseMaterialModal');
            const btnCancelMaterialModal = document.getElementById('btnCancelMaterialModal');
            const formUploadMaterial = document.getElementById('formUploadMaterial');

            const typePills = document.querySelectorAll('.type-pill');
            const fileUploadContainer = document.getElementById('fileUploadContainer');
            const linkInputContainer = document.getElementById('linkInputContainer');
            const materialDropzone = document.getElementById('materialDropzone');
            const materialFileInput = document.getElementById('materialFileInput');
            const dropzoneText = document.getElementById('dropzoneText');
            const fileStatus = document.getElementById('fileStatus');
            const materialTypeInput = document.getElementById('materialType');

            const defaultDropzoneText = dropzoneText ? dropzoneText.textContent : 'Klik untuk memilih file PDF atau PPTX, atau seret berkas ke sini';

            function showFileStatus(ok, message) {
                if (!fileStatus) return;
                fileStatus.textContent = message;
                fileStatus.style.display = 'block';
                fileStatus.style.background = ok ? '#ECFDF5' : '#FEF2F2';
                fileStatus.style.border = '1px solid ' + (ok ? '#A7F3D0' : '#FECACA');
                fileStatus.style.color = ok ? '#047857' : '#B91C1C';
            }

            function resetFileStatus() {
                if (!fileStatus) return;
                fileStatus.textContent = '';
                fileStatus.style.display = 'none';
                if (dropzoneText) dropzoneText.textContent = defaultDropzoneText;
            }

            function setType(activeType) {
                typePills.forEach(p => p.classList.toggle('active', p.getAttribute('data-type') === activeType));
                if (materialTypeInput) materialTypeInput.value = activeType;

                if (activeType === 'link') {
                    if (fileUploadContainer) fileUploadContainer.style.display = 'none';
                    if (linkInputContainer) linkInputContainer.style.display = 'block';
                    if (materialFileInput) materialFileInput.value = '';
                    resetFileStatus();
                } else {
                    if (fileUploadContainer) fileUploadContainer.style.display = 'block';
                    if (linkInputContainer) linkInputContainer.style.display = 'none';
                }
            }

            typePills.forEach(pill => {
                pill.addEventListener('click', () => setType(pill.getAttribute('data-type')));
            });

            if (materialTypeInput) {
                setType(materialTypeInput.value === 'link' ? 'link' : 'file');
            }

            if (materialDropzone && materialFileInput) {
                ['dragenter', 'dragover'].forEach(evt => {
                    materialDropzone.addEventListener(evt, () => {
                        materialDropzone.style.borderColor = '#039FFA';
                        materialDropzone.style.background = 'rgba(3, 159, 250, 0.06)';
                    });
                });
                ['dragleave', 'drop'].forEach(evt => {
                    materialDropzone.addEventListener(evt, () => {
                        materialDropzone.style.borderColor = '';
                        materialDropzone.style.background = '';
                    });
                });

                materialFileInput.addEventListener('change', () => {
                    const file = materialFileInput.files[0];
                    if (!file) {
                        resetFileStatus();
                        return;
                    }

                    const ext = file.name.split('.').pop().toLowerCase();
                    if (!['pdf', 'ppt', 'pptx'].includes(ext)) {
                        materialFileInput.value = '';
                        if (dropzoneText) dropzoneText.textContent = defaultDropzoneText;
                        showFileStatus(false, '✕ Format tidak didukung. Pilih berkas PDF, PPT, atau PPTX.');
                        return;
                    }

                    if (file.size > 50 * 1024 * 1024) {
                        materialFileInput.value = '';
                        if (dropzoneText) dropzoneText.textContent = defaultDropzoneText;
                        showFileStatus(false, '✕ Ukuran berkas maksimal 50 MB.');
                        return;
                    }

                    const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
                    if (dropzoneText) dropzoneText.textContent = file.name;
                    showFileStatus(true, `✓ Berkas dipilih: ${file.name} (${sizeMb} MB)`);
                });
            }

            function openMaterialModal() {
                if (materialModal) {
                    materialModal.classList.add('active');
                }
            }

            function closeMaterialModal() {
                if (materialModal) {
                    materialModal.classList.remove('active');
                }
            }

            if (btnOpenUpload) btnOpenUpload.addEventListener('click', openMaterialModal);
            if (btnCloseMaterialModal) btnCloseMaterialModal.addEventListener('click', closeMaterialModal);
            if (btnCancelMaterialModal) btnCancelMaterialModal.addEventListener('click', closeMaterialModal);

            if (materialModal) {
                materialModal.addEventListener('click', (e) => {
                    if (e.target === materialModal) closeMaterialModal();
                });
            }

            // 3. MODAL EDIT MATERI (SESUAI ROLE DOSEN)
            const editModal = document.getElementById('editMaterialModalOverlay');
            const formEdit = document.getElementById('formEditMaterial');
            const editId = document.getElementById('editMaterialId');
            const editTitle = document.getElementById('editMaterialTitle');
            const editDesc = document.getElementById('editMaterialDesc');
            const editUrl = document.getElementById('editMaterialUrl');
            const editFile = document.getElementById('editMaterialFile');
            const editFileContainer = document.getElementById('editFileContainer');
            const editLinkContainer = document.getElementById('editLinkContainer');
            const editCurrentFile = document.getElementById('editCurrentFile');

            function openEditModal(btn) {
                if (!formEdit) return;
                const isLink = btn.dataset.type === 'link';

                formEdit.action = formEdit.dataset.actionTemplate.replace('__ID__', btn.dataset.id);
                if (editId) editId.value = btn.dataset.id;
                if (editTitle) editTitle.value = btn.dataset.title || '';
                if (editDesc) editDesc.value = btn.dataset.description || '';
                if (editUrl) editUrl.value = btn.dataset.url || '';
                if (editFile) editFile.value = '';

                if (editFileContainer) editFileContainer.style.display = isLink ? 'none' : 'block';
                if (editLinkContainer) editLinkContainer.style.display = isLink ? 'block' : 'none';

                if (editFile) editFile.disabled = isLink;
                if (editUrl) {
                    editUrl.disabled = !isLink;
                    editUrl.required = isLink;
                }
                if (editCurrentFile) {
                    editCurrentFile.textContent = isLink ? '' : 'Berkas saat ini: ' + (btn.dataset.filename || '-');
                }

                if (editModal) {
                    editModal.classList.add('active');
                }
            }

            function closeEditModal() {
                if (editModal) {
                    editModal.classList.remove('active');
                }
            }

            document.querySelectorAll('.btn-edit-material').forEach(btn => {
                btn.addEventListener('click', () => openEditModal(btn));
            });
            const btnCloseEditModal = document.getElementById('btnCloseEditModal');
            const btnCancelEditModal = document.getElementById('btnCancelEditModal');
            if (btnCloseEditModal) btnCloseEditModal.addEventListener('click', closeEditModal);
            if (btnCancelEditModal) btnCancelEditModal.addEventListener('click', closeEditModal);
            if (editModal) {
                editModal.addEventListener('click', (e) => {
                    if (e.target === editModal) closeEditModal();
                });
            }

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (materialModal && materialModal.classList.contains('active')) {
                        closeMaterialModal();
                    }
                    if (editModal && editModal.classList.contains('active')) {
                        closeEditModal();
                    }
                }
            });

            @if ($errors->editMaterial->any() && old('edit_id'))
                (function () {
                    const btn = document.querySelector('.btn-edit-material[data-id="{{ old('edit_id') }}"]');
                    if (!btn) return;
                    openEditModal(btn);
                    if (editTitle) editTitle.value = @json(old('title', ''));
                    if (editDesc) editDesc.value = @json(old('description', ''));
                    if (editUrl && !editUrl.disabled) editUrl.value = @json(old('external_url', ''));
                })();
            @endif

            @if ($errors->any() && !old('edit_id'))
                openMaterialModal();
                if (@json(old('type')) === 'link') {
                    setType('link');
                }
            @endif
        });
    </script>
</body>
</html>
