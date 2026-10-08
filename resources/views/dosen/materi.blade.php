<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unggah Materi — Portal Dosen KampusLMS</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/dosen/dosen.materi.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/dosen/dosen.materi.css') }}">
    @endif
</head>

<body>

    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <div class="app-window">

        <!-- Navbar Khusus Dosen -->
        <x-navbar-dosen />

        <!-- Konten Utama Unggah Materi -->
        <main class="dosen-content">

            @php
                // $courses dikirim controller: hanya MK yang diampu dosen yang login.
                $activeCourses  = $courses;
                $allDbMaterials = $courses->flatMap->materials->sortByDesc('created_at');
            @endphp

            <!-- Topbar Header -->
            <header class="dash-topbar justify-end">
                <div class="course-filter-bar">
                    <span class="course-filter-label">Mata Kuliah Aktif:</span>
                    <select id="selectCurrentCourse" class="course-select" {{ $activeCourses->isEmpty() ? 'disabled' : '' }}>
                        @forelse ($activeCourses as $idx => $c)
                            <option value="{{ $c->id }}" {{ $idx === 0 ? 'selected' : '' }}>
                                {{ $c->code }} &bull; {{ $c->name }} ({{ $c->sks }} SKS)
                            </option>
                        @empty
                            <option value="">Belum ada mata kuliah aktif</option>
                        @endforelse
                    </select>
                </div>
            </header>

            <!-- Section: Unggah Materi -->
            <section class="feature-section" id="unggah-materi">
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-header-left">
                            <div class="section-header-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                                    <path d="M12 12v9"></path>
                                    <path d="m16 16-4-4-4 4"></path>
                                </svg>
                            </div>
                            <div class="section-header-text">
                                <h2>Publikasi &amp; Distribusi Modul Perkuliahan</h2>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                            <button type="button" class="btn-primary-action" id="btnOpenUploadMaterialModal" style="padding: 9px 18px; font-size: 13px;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                <span>+ Unggah Materi Baru</span>
                            </button>
                        </div>
                    </div>

                    <!-- Grid Materi yang Terpublikasi -->
                    <div class="materials-published-wrap">
                        <div class="table-header-tools">
                            <span class="table-summary-info">Koleksi Materi Perkuliahan (<strong id="materialListCount">{{ $allDbMaterials->count() }}</strong> Modul)</span>
                            <span class="card-subtitle-tag">Semester Genap 2026</span>
                        </div>

                        <div class="materials-grid" id="materialsGrid">
                            @forelse ($allDbMaterials as $material)
                                <div class="material-card" data-course-id="{{ $material->course_id }}">
                                    <div class="material-card-top">
                                        @if ($material->type === 'file')
                                            <span class="material-type-tag tag-pdf">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg>
                                                {{ strtoupper(pathinfo($material->original_name ?? '', PATHINFO_EXTENSION) ?: 'FILE') }} &bull; Berkas File
                                            </span>
                                        @else
                                            <span class="material-type-tag tag-link">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path></svg>
                                                LINK &bull; Tautan
                                            </span>
                                        @endif
                                    </div>
                                    <h4 class="material-card-title">{{ $material->title }}</h4>
                                    <p class="material-card-desc">{{ $material->description ?? 'Tidak ada catatan.' }}</p>
                                    <div class="material-card-footer">
                                        <span class="material-meta-date">Diunggah: {{ $material->created_at ? $material->created_at->diffForHumans() : '-' }}</span>
                                        <div class="material-card-actions">
                                            @if ($material->type === 'file')
                                                <a href="{{ route('dosen.materi.download', $material) }}" class="btn-open-resource">Unduh File</a>
                                            @else
                                                <a href="{{ $material->external_url }}" target="_blank" rel="noopener noreferrer" class="btn-open-resource">Buka Link</a>
                                            @endif
                                            <button type="button" class="btn-icon-edit btn-edit-material" title="Edit materi"
                                                    data-id="{{ $material->id }}"
                                                    data-type="{{ $material->type }}"
                                                    data-title="{{ $material->title }}"
                                                    data-description="{{ $material->description }}"
                                                    data-url="{{ $material->external_url }}"
                                                    data-filename="{{ $material->original_name }}"
                                                    style="display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:8px; border:1px solid rgba(3,159,250,0.25); background:rgba(3,159,250,0.08); color:#039FFA; cursor:pointer;">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                            </button>
                                            <form action="{{ route('dosen.materi.destroy', $material) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-icon-danger" title="Hapus materi">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state" id="materialEmptyState" style="padding: 48px 16px; text-align: center; width: 100%; grid-column: 1 / -1; background: #F8FAFC; border-radius: 16px; border: 1.5px dashed rgba(3, 159, 250, 0.25);">
                                    <div style="font-size: 36px; margin-bottom: 8px;">📂</div>
                                    <div style="font-size: 15px; font-weight: 800; color: #0F172A; margin-bottom: 4px;">Belum Ada Materi Perkuliahan</div>
                                    <p style="font-size: 12.5px; color: #64748B; margin-bottom: 14px;">Klik tombol di atas untuk mempublikasikan materi perkuliahan baru (PDF, PPTX, atau tautan referensi).</p>
                                    <button type="button" class="btn-primary-action" onclick="document.getElementById('btnOpenUploadMaterialModal').click()" style="margin: 0 auto; font-size: 12.5px;">
                                        + Unggah Materi Pertama
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

        </main>

        <!-- MODAL FORM UNGGAH MATERI -->
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
                <form id="formUploadMaterial" action="{{ route('dosen.materi.store') }}" method="POST" enctype="multipart/form-data" style="margin: 0; display: flex; flex-direction: column;">
                    @csrf
                    <input type="hidden" name="type" id="materialType" value="{{ old('type', 'file') }}">

                    <div class="dosen-modal-body" style="max-height: 70vh; overflow-y: auto;">

                        @if ($errors->any())
                            <div style="background:#FEF2F2; border:1px solid #FECACA; color:#B91C1C; border-radius:10px; padding:10px 12px; font-size:12.5px; font-weight:600; margin-bottom:12px;">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <div class="form-row-2">
                            <div class="form-group">
                                <label for="materialCourse">Mata Kuliah Aktif <span class="required">*</span></label>
                                <select id="materialCourse" name="course_id" class="form-select" required {{ $activeCourses->isEmpty() ? 'disabled' : '' }}>
                                    @forelse ($activeCourses as $c)
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
                      data-action-template="{{ route('dosen.materi.update', ['material' => '__ID__']) }}">
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

        <x-footer />
    </div>

    <!-- Toast Notification Container -->
    <div class="dosen-toast-container" id="toastContainer"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const typePills = document.querySelectorAll('.type-pill');
            const fileUploadContainer = document.getElementById('fileUploadContainer');
            const linkInputContainer = document.getElementById('linkInputContainer');
            const materialDropzone = document.getElementById('materialDropzone');
            const materialFileInput = document.getElementById('materialFileInput');
            const dropzoneText = document.getElementById('dropzoneText');
            const fileStatus = document.getElementById('fileStatus');
            const materialTypeInput = document.getElementById('materialType');

            const defaultDropzoneText = dropzoneText.textContent;

            // Tampilkan status berkas di bawah dropzone (hijau = berhasil, merah = ditolak)
            function showFileStatus(ok, message) {
                fileStatus.textContent = message;
                fileStatus.style.display = 'block';
                fileStatus.style.background = ok ? '#ECFDF5' : '#FEF2F2';
                fileStatus.style.border = '1px solid ' + (ok ? '#A7F3D0' : '#FECACA');
                fileStatus.style.color = ok ? '#047857' : '#B91C1C';
            }

            function resetFileStatus() {
                fileStatus.textContent = '';
                fileStatus.style.display = 'none';
                dropzoneText.textContent = defaultDropzoneText;
            }

            function setType(activeType) {
                typePills.forEach(p => p.classList.toggle('active', p.getAttribute('data-type') === activeType));
                materialTypeInput.value = activeType;

                if (activeType === 'link') {
                    fileUploadContainer.style.display = 'none';
                    linkInputContainer.style.display = 'block';
                    // Kosongkan berkas agar tidak ikut terkirim
                    materialFileInput.value = '';
                    resetFileStatus();
                } else {
                    fileUploadContainer.style.display = 'block';
                    linkInputContainer.style.display = 'none';
                }
            }

            typePills.forEach(pill => {
                pill.addEventListener('click', () => setType(pill.getAttribute('data-type')));
            });

            // Pulihkan pilihan format setelah validasi gagal
            setType(materialTypeInput.value === 'link' ? 'link' : 'file');

            // ===== Dropzone: klik & seret ditangani langsung oleh <input type="file"> transparan =====

            // Efek visual saat berkas diseret di atas dropzone
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

            // Jika berkas terlepas di luar dropzone, jangan biarkan browser membukanya
            ['dragover', 'drop'].forEach(evt => {
                window.addEventListener(evt, (e) => {
                    if (!materialDropzone.contains(e.target)) e.preventDefault();
                });
            });

            // Dijalankan setiap kali berkas dipilih (lewat klik maupun seret).
            // Atribut accept tidak berlaku untuk berkas yang diseret, jadi dicek di sini juga
            // (server tetap memvalidasi ulang di StoreMaterialRequest).
            materialFileInput.addEventListener('change', () => {
                const file = materialFileInput.files[0];

                if (!file) {
                    resetFileStatus();
                    return;
                }

                const ext = file.name.split('.').pop().toLowerCase();

                if (!['pdf', 'ppt', 'pptx'].includes(ext)) {
                    materialFileInput.value = '';
                    dropzoneText.textContent = defaultDropzoneText;
                    showFileStatus(false, '✕ Format tidak didukung. Pilih berkas PDF, PPT, atau PPTX.');
                    return;
                }

                if (file.size > 50 * 1024 * 1024) {
                    materialFileInput.value = '';
                    dropzoneText.textContent = defaultDropzoneText;
                    showFileStatus(false, '✕ Ukuran berkas maksimal 50 MB.');
                    return;
                }

                const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
                dropzoneText.textContent = file.name;
                showFileStatus(true, `✓ Berkas dipilih: ${file.name} (${sizeMb} MB)`);
            });

            // ===== Modal Controls =====
            const materialModal = document.getElementById('materialModalOverlay');
            const btnOpenUpload = document.getElementById('btnOpenUploadMaterialModal');
            const btnCloseMaterialModal = document.getElementById('btnCloseMaterialModal');
            const btnCancelMaterialModal = document.getElementById('btnCancelMaterialModal');

            function openMaterialModal() {
                if (materialModal) materialModal.classList.add('active');
            }

            function closeMaterialModal() {
                if (materialModal) materialModal.classList.remove('active');
            }

            if (btnOpenUpload) btnOpenUpload.addEventListener('click', openMaterialModal);
            if (btnCloseMaterialModal) btnCloseMaterialModal.addEventListener('click', closeMaterialModal);
            if (btnCancelMaterialModal) btnCancelMaterialModal.addEventListener('click', closeMaterialModal);
            if (materialModal) {
                materialModal.addEventListener('click', (e) => {
                    if (e.target === materialModal) closeMaterialModal();
                });
            }
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && materialModal && materialModal.classList.contains('active')) {
                    closeMaterialModal();
                }
            });

            // Validasi server gagal -> buka lagi modal agar pesan error terlihat
            @if ($errors->any())
                openMaterialModal();
            @endif

            // ===== Edit Materi =====
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

            function openEditModal(card) {
                const isLink = card.dataset.type === 'link';

                formEdit.action = formEdit.dataset.actionTemplate.replace('__ID__', card.dataset.id);
                editId.value = card.dataset.id;
                editTitle.value = card.dataset.title || '';
                editDesc.value = card.dataset.description || '';
                editUrl.value = card.dataset.url || '';
                editFile.value = '';

                editFileContainer.style.display = isLink ? 'none' : 'block';
                editLinkContainer.style.display = isLink ? 'block' : 'none';
                // Field yang disembunyikan dinonaktifkan agar tidak ikut tervalidasi/terkirim
                editFile.disabled = isLink;
                editUrl.disabled = !isLink;
                editUrl.required = isLink;
                editCurrentFile.textContent = isLink ? '' : 'Berkas saat ini: ' + (card.dataset.filename || '-');

                editModal.classList.add('active');
            }

            function closeEditModal() {
                editModal.classList.remove('active');
            }

            document.querySelectorAll('.btn-edit-material').forEach(btn => {
                btn.addEventListener('click', () => openEditModal(btn));
            });
            document.getElementById('btnCloseEditModal').addEventListener('click', closeEditModal);
            document.getElementById('btnCancelEditModal').addEventListener('click', closeEditModal);
            editModal.addEventListener('click', (e) => { if (e.target === editModal) closeEditModal(); });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && editModal.classList.contains('active')) closeEditModal();
            });

            // Validasi edit gagal -> buka lagi modal edit dengan isian terakhir
            @if ($errors->editMaterial->any() && old('edit_id'))
                (function () {
                    const btn = document.querySelector('.btn-edit-material[data-id="{{ old('edit_id') }}"]');
                    if (!btn) return;
                    openEditModal(btn);
                    editTitle.value = @json(old('title', ''));
                    editDesc.value = @json(old('description', ''));
                    if (!editUrl.disabled) editUrl.value = @json(old('external_url', ''));
                })();
            @endif

            // ===== Course Filter =====
            const selectCurrentCourse = document.getElementById('selectCurrentCourse');
            const materialCourse = document.getElementById('materialCourse');
            const materialsGrid = document.getElementById('materialsGrid');
            const materialListCount = document.getElementById('materialListCount');

            function applyCourseFilter() {
                if (!selectCurrentCourse || !materialsGrid) return;

                const selectedVal = selectCurrentCourse.value;
                let visibleCount = 0;

                materialsGrid.querySelectorAll('.material-card').forEach(card => {
                    const show = !selectedVal || card.getAttribute('data-course-id') === selectedVal;
                    card.style.display = show ? '' : 'none';
                    if (show) visibleCount++;
                });

                if (materialListCount) materialListCount.textContent = visibleCount;

                // Dropdown di modal ikut mata kuliah yang sedang dilihat
                // (kecuali baru kembali dari validasi gagal, supaya pilihan lama tidak tertimpa)
                @if (! $errors->any())
                    if (materialCourse && selectedVal) materialCourse.value = selectedVal;
                @endif
            }

            if (selectCurrentCourse) {
                selectCurrentCourse.addEventListener('change', applyCourseFilter);
                applyCourseFilter();
            }

        });
    </script>
</body>

</html>