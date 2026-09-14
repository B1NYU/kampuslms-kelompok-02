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
        @vite(['resources/css/app.css', 'resources/dosen/dosen.dashboard.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/dosen/dosen.dashboard.css') }}">
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

            <!-- Topbar Header -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <span class="page-eyebrow">
                            <span class="page-eyebrow-dot"></span>
                            PORTAL DOSEN • DISTRIBUSI MATERI &amp; MODUL
                        </span>
                        <h1>2. Unggah Materi Kuliah (PDF / PPTX / Link)</h1>
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
                                <p>Publikasikan modul perkuliahan, slide presentasi kelas, atau materi referensi web ke mahasiswa.</p>
                            </div>
                        </div>
                        <span class="section-header-badge">Distribusi Modul</span>
                    </div>

                    <div class="two-col-grid">
                        <!-- Form Unggah Materi -->
                        <form id="formUploadMaterial" class="card-form">
                            <h4 class="card-form-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                Form Publikasi Materi Baru
                            </h4>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label for="materialCourse">Mata Kuliah</label>
                                    <select id="materialCourse" class="form-select">
                                        <option value="SI101">SI101 - Pemrograman Web</option>
                                        <option value="SI102">SI102 - Basis Data</option>
                                        <option value="SI103">SI103 - Analisis Sistem</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="materialSession">Pertemuan Ke-</label>
                                    <select id="materialSession" class="form-select">
                                        <option value="1">Pertemuan 1</option>
                                        <option value="2">Pertemuan 2</option>
                                        <option value="3">Pertemuan 3</option>
                                        <option value="4">Pertemuan 4</option>
                                        <option value="5">Pertemuan 5</option>
                                        <option value="6" selected>Pertemuan 6</option>
                                        <option value="7">Pertemuan 7</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="materialTitle">Judul Materi <span class="required">*</span></label>
                                <input type="text" id="materialTitle" class="form-control" placeholder="Contoh: Modul 06 - Autentikasi Multi-Role Laravel" required>
                            </div>

                            <!-- Pilihan Tipe Materi (PDF, PPTX, Link) -->
                            <div class="form-group">
                                <label>Pilih Format Materi <span class="required">*</span></label>
                                <div class="type-selector-group">
                                    <div class="type-pill active" data-type="pdf">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                        </svg>
                                        Dokumen PDF
                                    </div>
                                    <div class="type-pill" data-type="pptx">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                            <line x1="8" y1="21" x2="16" y2="21"></line>
                                            <line x1="12" y1="17" x2="12" y2="21"></line>
                                        </svg>
                                        Slide PPTX
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

                            <!-- Dynamic File Upload or URL Input -->
                            <div id="fileUploadContainer" class="form-group">
                                <label>Unggah Berkas (PDF / PPTX)</label>
                                <div class="file-dropzone" id="materialDropzone">
                                    <svg class="file-dropzone-icon" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    <span class="file-dropzone-text" id="dropzoneText">Klik untuk memilih file PDF atau seret berkas ke sini</span>
                                    <span class="file-dropzone-sub">Maksimal ukuran file: 50MB</span>
                                    <span class="file-selected-info" id="fileSelectedInfo"></span>
                                    <input type="file" id="materialFileInput" accept=".pdf" style="display: none;">
                                </div>
                            </div>

                            <div id="linkInputContainer" class="form-group" style="display: none;">
                                <label for="materialUrl">URL Tautan / Link Materi <span class="required">*</span></label>
                                <input type="url" id="materialUrl" class="form-control" placeholder="https://laravel.com/docs/11.x/authentication atau link video">
                            </div>

                            <div class="form-group">
                                <label for="materialDesc">Catatan Pembelajaran / Petunjuk</label>
                                <textarea id="materialDesc" class="form-textarea" rows="2" placeholder="Tulis ringkasan atau instruksi bagi mahasiswa..."></textarea>
                            </div>

                            <button type="submit" class="btn-primary-action">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                                    <path d="M12 12v9"></path>
                                    <path d="m16 16-4-4-4 4"></path>
                                </svg>
                                Unggah &amp; Publikasikan
                            </button>
                        </form>

                        <!-- Grid Materi yang Terpublikasi -->
                        <div class="materials-published-wrap">
                            <div class="table-header-tools">
                                <span class="table-summary-info">Koleksi Materi Perkuliahan (<strong id="materialListCount">3</strong> Modul)</span>
                                <span class="card-subtitle-tag">Semester Genap 2026</span>
                            </div>

                            <div class="materials-grid" id="materialsGrid">
                                <!-- Card Materi 1: PDF -->
                                <div class="material-card">
                                    <div class="material-card-top">
                                        <span class="material-type-tag tag-pdf">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg>
                                            PDF &middot; 4.2 MB
                                        </span>
                                        <span class="card-subtitle-tag" style="padding:2px 7px; font-size:10px;">Pertemuan 1</span>
                                    </div>
                                    <h4 class="material-card-title">Pengantar Arsitektur Laravel &amp; MVC Routing</h4>
                                    <p class="material-card-desc">Konsep dasar alur request lifecycle, route parameters, dan controller action pada Laravel 11.</p>
                                    <div class="material-card-footer">
                                        <span class="material-meta-date">Diunggah: 28 Feb 2026</span>
                                        <div class="material-card-actions">
                                            <a href="#" class="btn-open-resource" onclick="alert('Membuka pratinjau dokumen PDF...'); return false;">Unduh PDF</a>
                                            <button class="btn-icon-danger btn-delete-material" title="Hapus materi">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Materi 2: PPTX -->
                                <div class="material-card">
                                    <div class="material-card-top">
                                        <span class="material-type-tag tag-pptx">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="3" width="20" height="14" rx="2"></rect></svg>
                                            PPTX &middot; 8.7 MB
                                        </span>
                                        <span class="card-subtitle-tag" style="padding:2px 7px; font-size:10px;">Pertemuan 3</span>
                                    </div>
                                    <h4 class="material-card-title">Slide Presentasi Blade Templating &amp; Components</h4>
                                    <p class="material-card-desc">Penggunaan reusable UI components, slot, inheritance layout, dan direktif custom blade.</p>
                                    <div class="material-card-footer">
                                        <span class="material-meta-date">Diunggah: 04 Mar 2026</span>
                                        <div class="material-card-actions">
                                            <a href="#" class="btn-open-resource" onclick="alert('Mengunduh slide presentasi PPTX...'); return false;">Unduh Slide</a>
                                            <button class="btn-icon-danger btn-delete-material" title="Hapus materi">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Materi 3: LINK -->
                                <div class="material-card">
                                    <div class="material-card-top">
                                        <span class="material-type-tag tag-link">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path></svg>
                                            LINK &middot; Referensi
                                        </span>
                                        <span class="card-subtitle-tag" style="padding:2px 7px; font-size:10px;">Pertemuan 5</span>
                                    </div>
                                    <h4 class="material-card-title">Dokumentasi Otorisasi &amp; Role-Based Access Control</h4>
                                    <p class="material-card-desc">Panduan resmi Laravel Documentation mengenai Middleware, Guards, dan Session Management.</p>
                                    <div class="material-card-footer">
                                        <span class="material-meta-date">Diunggah: 10 Mar 2026</span>
                                        <div class="material-card-actions">
                                            <a href="https://laravel.com/docs/11.x/authorization" target="_blank" class="btn-open-resource">Kunjungi Link</a>
                                            <button class="btn-icon-danger btn-delete-material" title="Hapus materi">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </main>
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

            const typePills = document.querySelectorAll('.type-pill');
            const fileUploadContainer = document.getElementById('fileUploadContainer');
            const linkInputContainer = document.getElementById('linkInputContainer');
            const materialDropzone = document.getElementById('materialDropzone');
            const materialFileInput = document.getElementById('materialFileInput');
            const dropzoneText = document.getElementById('dropzoneText');
            const fileSelectedInfo = document.getElementById('fileSelectedInfo');
            const formUploadMaterial = document.getElementById('formUploadMaterial');
            const materialsGrid = document.getElementById('materialsGrid');
            const materialListCount = document.getElementById('materialListCount');

            let activeMaterialType = 'pdf';

            typePills.forEach(pill => {
                pill.addEventListener('click', () => {
                    typePills.forEach(p => p.classList.remove('active'));
                    pill.classList.add('active');

                    activeMaterialType = pill.getAttribute('data-type');
                    if (activeMaterialType === 'link') {
                        fileUploadContainer.style.display = 'none';
                        linkInputContainer.style.display = 'block';
                    } else {
                        fileUploadContainer.style.display = 'block';
                        linkInputContainer.style.display = 'none';
                        if (activeMaterialType === 'pdf') {
                            materialFileInput.accept = '.pdf';
                            dropzoneText.textContent = 'Klik untuk memilih file PDF atau seret berkas ke sini';
                        } else {
                            materialFileInput.accept = '.pptx,.ppt';
                            dropzoneText.textContent = 'Klik untuk memilih file PPTX / Presentasi atau seret berkas ke sini';
                        }
                    }
                });
            });

            materialDropzone.addEventListener('click', () => {
                materialFileInput.click();
            });

            materialFileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    const file = e.target.files[0];
                    fileSelectedInfo.textContent = `✓ Berkas dipilih: ${file.name} (${(file.size / (1024 * 1024)).toFixed(1)} MB)`;
                }
            });

            formUploadMaterial.addEventListener('submit', (e) => {
                e.preventDefault();
                const session = document.getElementById('materialSession').value;
                const title = document.getElementById('materialTitle').value.trim();
                const desc = document.getElementById('materialDesc').value.trim() || 'Materi perkuliahan baru.';

                if (!title) {
                    alert('Harap masukkan judul materi!');
                    return;
                }

                let badgeHtml = '';
                let actionBtnHtml = '';

                if (activeMaterialType === 'pdf') {
                    badgeHtml = `<span class="material-type-tag tag-pdf"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg> PDF &bull; Terlampir</span>`;
                    actionBtnHtml = `<a href="#" class="btn-open-resource" onclick="alert('Membuka materi ${title}...'); return false;">Unduh PDF</a>`;
                } else if (activeMaterialType === 'pptx') {
                    badgeHtml = `<span class="material-type-tag tag-pptx"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="3" width="20" height="14" rx="2"></rect></svg> PPTX &bull; Slide</span>`;
                    actionBtnHtml = `<a href="#" class="btn-open-resource" onclick="alert('Mengunduh presentasi...'); return false;">Unduh Slide</a>`;
                } else {
                    const url = document.getElementById('materialUrl').value.trim() || '#';
                    badgeHtml = `<span class="material-type-tag tag-link"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path></svg> LINK &bull; Tautan</span>`;
                    actionBtnHtml = `<a href="${url}" target="_blank" class="btn-open-resource">Buka Link</a>`;
                }

                const card = document.createElement('div');
                card.className = 'material-card';
                card.innerHTML = `
                    <div class="material-card-top">
                        ${badgeHtml}
                        <span class="card-subtitle-tag" style="padding:2px 7px; font-size:10px;">Pertemuan ${session}</span>
                    </div>
                    <h4 class="material-card-title">${title}</h4>
                    <p class="material-card-desc">${desc}</p>
                    <div class="material-card-footer">
                        <span class="material-meta-date">Diunggah: Baru saja</span>
                        <div class="material-card-actions">
                            ${actionBtnHtml}
                            <button class="btn-icon-danger btn-delete-material" title="Hapus materi">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                            </button>
                        </div>
                    </div>
                `;

                materialsGrid.prepend(card);
                formUploadMaterial.reset();
                fileSelectedInfo.textContent = '';
                updateMaterialCount();
                attachDeleteMaterialEvents();
                showToast(`Materi "${title}" berhasil diunggah untuk Pertemuan ${session}!`);
            });

            function updateMaterialCount() {
                const total = materialsGrid.querySelectorAll('.material-card').length;
                if (materialListCount) materialListCount.textContent = total;
            }

            function attachDeleteMaterialEvents() {
                const deleteBtns = document.querySelectorAll('.btn-delete-material');
                deleteBtns.forEach(btn => {
                    btn.onclick = function() {
                        const card = btn.closest('.material-card');
                        const title = card.querySelector('.material-card-title').textContent;
                        if (confirm(`Hapus materi "${title}"?`)) {
                            card.remove();
                            updateMaterialCount();
                            showToast(`Materi "${title}" telah dihapus.`, false);
                        }
                    };
                });
            }
            attachDeleteMaterialEvents();

        });
    </script>
</body>

</html>
