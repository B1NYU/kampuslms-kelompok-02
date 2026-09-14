<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Mahasiswa — Portal Dosen KampusLMS</title>

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

        <!-- Konten Utama Kelola Mahasiswa -->
        <main class="dosen-content">

            <!-- Topbar Header -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <span class="page-eyebrow">
                            <span class="page-eyebrow-dot"></span>
                            PORTAL DOSEN • MANAJEMEN KELAS & PESERTA
                        </span>
                        <h1>1. Pendaftaran & Kelola Mahasiswa</h1>
                    </div>
                </div>

                @php
                    $dosenCourses = \App\Models\Course::with(['students', 'lecturer'])->get();
                    $firstCourse = $dosenCourses->first();
                @endphp
                <div class="course-filter-bar">
                    <span class="course-filter-label">Mata Kuliah Aktif:</span>
                    <select id="selectCurrentCourse" class="course-select">
                        @foreach ($dosenCourses as $idx => $c)
                            <option value="{{ $c->code }}" {{ $idx === 0 ? 'selected' : '' }}>
                                {{ $c->code }} &bull; {{ $c->name }} ({{ $c->sks }} SKS - {{ $c->students->count() }} Mhs)
                            </option>
                        @endforeach
                    </select>
                </div>
            </header>

            <!-- Section: Form & Tabel Mahasiswa -->
            <section class="feature-section" id="kelola-mahasiswa">
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-header-left">
                            <div class="section-header-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <polyline points="16 11 18 13 22 9"></polyline>
                                </svg>
                            </div>
                            <div class="section-header-text">
                                <h2>Pendaftaran Mahasiswa ke Mata Kuliah</h2>
                                <p>Dosen dapat menambahkan mahasiswa baru ke kelas dan memantau seluruh peserta aktif yang terdaftar di database.</p>
                            </div>
                        </div>
                        <span class="section-header-badge">Kelola Peserta Kelas</span>
                    </div>

                    <div class="two-col-grid">
                        <!-- Form Tambah / Daftarkan Mahasiswa -->
                        <form id="formAddStudent" class="card-form">
                            <h4 class="card-form-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="16"></line>
                                    <line x1="8" y1="12" x2="16" y2="12"></line>
                                </svg>
                                Form Pendaftaran Mahasiswa
                            </h4>

                            <div class="form-group">
                                <label for="mhsMatkul">Mata Kuliah Target <span class="required">*</span></label>
                                <select id="mhsMatkul" class="form-select" required>
                                    @foreach ($dosenCourses as $c)
                                        <option value="{{ $c->code }} - {{ $c->name }}">{{ $c->code }} - {{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label for="mhsNim">Nomor Induk (NIM) <span class="required">*</span></label>
                                    <input type="text" id="mhsNim" class="form-control" placeholder="Contoh: 10241022" required>
                                </div>
                                <div class="form-group">
                                    <label for="mhsKelas">Kelas <span class="required">*</span></label>
                                    <select id="mhsKelas" class="form-select" required>
                                        <option value="SI-A">SI-A</option>
                                        <option value="SI-B">SI-B</option>
                                        <option value="TI-A">TI-A</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="mhsNama">Nama Lengkap Mahasiswa <span class="required">*</span></label>
                                <input type="text" id="mhsNama" class="form-control" placeholder="Nama mahasiswa..." required>
                            </div>

                            <div class="form-group">
                                <label for="mhsProdi">Program Studi</label>
                                <select id="mhsProdi" class="form-select">
                                    <option value="Sistem Informasi">Sistem Informasi</option>
                                    <option value="Teknologi Informasi">Teknologi Informasi</option>
                                    <option value="Informatika">Informatika</option>
                                </select>
                            </div>

                            <button type="submit" class="btn-primary-action">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                Daftarkan Mahasiswa
                            </button>
                        </form>

                        <!-- Tabel Mahasiswa Terdaftar -->
                        <div class="table-container">
                            <div class="table-header-tools">
                                <span class="table-summary-info">Menampilkan <strong id="studentTableCount">5</strong> Mahasiswa Terdaftar</span>
                                <div class="search-input-box">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                    <input type="text" id="searchStudentInput" placeholder="Cari mahasiswa/NIM...">
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="custom-dosen-table" id="studentTable">
                                    <thead>
                                        <tr>
                                            <th>Mahasiswa</th>
                                            <th>Program Studi</th>
                                            <th>Kelas</th>
                                            <th>Status</th>
                                            <th style="text-align: right;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="studentTableBody">
                                        @php
                                            $selectedCourseCode = $firstCourse?->code ?? '';
                                        @endphp
                                        @forelse ($dosenCourses as $course)
                                            @foreach ($course->students as $mhs)
                                                @php
                                                    $initials = collect(explode(' ', $mhs->name))->map(fn($w)=>mb_substr($w,0,1))->join('');
                                                    $initials = strtoupper(mb_substr($initials, 0, 2));
                                                    $colors = [
                                                        ['#FFE2E8', '#B0182D'],
                                                        ['#EBF3FF', '#1971C2'],
                                                        ['#EBF9F1', '#1B8A5A'],
                                                        ['#FFF0DE', '#C98A1F'],
                                                        ['#F2EBF9', '#8E44AD']
                                                    ];
                                                    [$bg, $c] = $colors[$mhs->id % count($colors)];
                                                @endphp
                                                <tr data-mk="{{ $course->code }}" style="{{ $course->code === $selectedCourseCode ? '' : 'display:none;' }}">
                                                    <td>
                                                        <div class="student-cell">
                                                            <div class="student-avatar" style="background:{{ $bg }}; color:{{ $c }};">{{ $initials }}</div>
                                                            <div class="student-meta">
                                                                <span class="student-name">{{ $mhs->name }}</span>
                                                                <span class="student-nim">{{ $mhs->nim_nip }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>Sistem Informasi</td>
                                                    <td><span class="card-subtitle-tag" style="padding:2px 8px; font-size:10px;">SI-A</span></td>
                                                    <td><span class="badge-status badge-status-active">Aktif</span></td>
                                                    <td style="text-align: right;">
                                                        <button class="btn-icon-danger btn-delete-student" title="Keluarkan dari kelas">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                            </svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @empty
                                            <tr>
                                                <td colspan="5" style="text-align:center;padding:20px;color:#94A3B8;">Belum ada mahasiswa terdaftar.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
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

            const formAddStudent = document.getElementById('formAddStudent');
            const studentTableBody = document.getElementById('studentTableBody');
            const studentTableCount = document.getElementById('studentTableCount');
            const searchStudentInput = document.getElementById('searchStudentInput');

            formAddStudent.addEventListener('submit', (e) => {
                e.preventDefault();
                const nim = document.getElementById('mhsNim').value.trim();
                const nama = document.getElementById('mhsNama').value.trim();
                const kelas = document.getElementById('mhsKelas').value;
                const prodi = document.getElementById('mhsProdi').value;

                if (!nim || !nama) {
                    alert('Harap lengkapi data NIM dan Nama Mahasiswa!');
                    return;
                }

                const initials = nama.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
                const colors = [
                    { bg: '#FFE2E8', text: '#B0182D' },
                    { bg: '#EBF3FF', text: '#1971C2' },
                    { bg: '#EBF9F1', text: '#1B8A5A' },
                    { bg: '#FFF0DE', text: '#C98A1F' },
                    { bg: '#F2EBF9', text: '#8E44AD' }
                ];
                const pickedColor = colors[Math.floor(Math.random() * colors.length)];

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>
                        <div class="student-cell">
                            <div class="student-avatar" style="background:${pickedColor.bg}; color:${pickedColor.text};">${initials}</div>
                            <div class="student-meta">
                                <span class="student-name">${nama}</span>
                                <span class="student-nim">${nim}</span>
                            </div>
                        </div>
                    </td>
                    <td>${prodi}</td>
                    <td><span class="card-subtitle-tag" style="padding:2px 8px; font-size:10px;">${kelas}</span></td>
                    <td><span class="badge-status badge-status-active">Aktif</span></td>
                    <td style="text-align: right;">
                        <button class="btn-icon-danger btn-delete-student" title="Keluarkan dari kelas">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </td>
                `;

                studentTableBody.prepend(newRow);
                formAddStudent.reset();
                updateStudentCount();
                attachDeleteStudentEvents();
                showToast(`Mahasiswa ${nama} (${nim}) berhasil didaftarkan ke kelas!`);
            });

            const selectCurrentCourse = document.getElementById('selectCurrentCourse');

            function applyCourseFilter() {
                const currentMk = selectCurrentCourse ? selectCurrentCourse.value : '';
                const query = (searchStudentInput ? searchStudentInput.value : '').toLowerCase().trim();
                let visibleCount = 0;

                const rows = studentTableBody.querySelectorAll('tr[data-mk]');
                rows.forEach(row => {
                    const matchMk = (!currentMk || row.dataset.mk === currentMk);
                    const text = row.textContent.toLowerCase();
                    const matchSearch = (!query || text.includes(query));

                    if (matchMk && matchSearch) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (studentTableCount) studentTableCount.textContent = visibleCount;
            }

            if (selectCurrentCourse) {
                selectCurrentCourse.addEventListener('change', applyCourseFilter);
            }

            if (searchStudentInput) {
                searchStudentInput.addEventListener('input', applyCourseFilter);
            }

            function attachDeleteStudentEvents() {
                const deleteBtns = document.querySelectorAll('.btn-delete-student');
                deleteBtns.forEach(btn => {
                    btn.onclick = function() {
                        const tr = btn.closest('tr');
                        const studentName = tr.querySelector('.student-name').textContent;
                        if (confirm(`Keluarkan ${studentName} dari mata kuliah ini?`)) {
                            tr.remove();
                            applyCourseFilter();
                            showToast(`${studentName} telah dikeluarkan dari kelas.`, false);
                        }
                    };
                });
            }
            attachDeleteStudentEvents();
            applyCourseFilter();

        });
    </script>
</body>

</html>
