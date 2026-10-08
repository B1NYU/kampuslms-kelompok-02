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
        @vite(['resources/css/app.css', 'resources/css/dosen/dosen.mahasiswa.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/dosen/dosen.mahasiswa.css') }}">
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

            @php
                $dosenUser = auth()->user();
                $dosenCourses = $dosenUser
                    ? $dosenUser->taughtCourses()->with('students')->get()
                    : collect();
                if ($dosenCourses->isEmpty()) {
                    $dosenCourses = \App\Models\Course::where('status', 'active')->with('students')->get();
                }
                $firstCourse = $dosenCourses->first();
                $registeredStudents = \App\Models\User::where('role', 'mahasiswa')
                    ->select('id', 'name', 'nim_nip')
                    ->get();
            @endphp

            <!-- Topbar Header / Judul di Luar Container Utama -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <h1>Pendaftaran Mahasiswa ke Mata Kuliah</h1>
                        <span class="table-summary-info">Menampilkan <strong id="studentTableCount">{{ $firstCourse?->students?->count() ?? 0 }}</strong> Mahasiswa Terdaftar</span>
                    </div>
                </div>
            </header>

            <!-- Section: Form & Tabel Mahasiswa -->
            <section class="feature-section" id="kelola-mahasiswa">
                <div class="section-card">

                    <!-- Toolbar Kontrol: Pencarian di Kiri, Filter MK di sebelah kiri Tombol Daftarkan Mahasiswa -->
                    <div class="table-toolbar-row">
                        <!-- Kolom Mencari Mahasiswa (Kiri) -->
                        <div class="search-input-box">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="searchStudentInput" placeholder="Cari mahasiswa / NIM...">
                        </div>

                        <!-- Sisi Kanan: Filter MK Aktif di sebelah kiri Button Daftarkan Mahasiswa -->
                        <div class="toolbar-right-group">
                            <div class="course-filter-bar">
                                <select id="selectCurrentCourse" class="course-select" {{ $dosenCourses->isEmpty() ? 'disabled' : '' }}>
                                    @forelse ($dosenCourses as $idx => $c)
                                        <option value="{{ $c->code }}" {{ $idx === 0 ? 'selected' : '' }}>
                                            {{ $c->code }} &bull; {{ $c->name }} ({{ $c->sks }} SKS - {{ $c->students->count() }} Mhs)
                                        </option>
                                    @empty
                                        <option value="">Belum ada mata kuliah yang diampu</option>
                                    @endforelse
                                </select>
                            </div>

                            <button type="button" class="btn-primary-action" id="btnOpenAddStudentModal">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                <span>+ Daftarkan Mahasiswa</span>
                            </button>
                        </div>
                    </div>

                    <!-- Tabel Mahasiswa Terdaftar (Full Width) -->
                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="custom-dosen-table table-clean-style" id="studentTable">
                                    <thead>
                                        <tr>
                                            <th>Mahasiswa</th>
                                            <th>Kelas</th>
                                            <th>Status</th>
                                            <th style="text-align: right;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="studentTableBody">
                                        @php
                                            $selectedCourseCode = $firstCourse?->code ?? '';
                                            $hasStudentsInFirstCourse = $firstCourse && $firstCourse->students->isNotEmpty();
                                        @endphp
                                        @if ($dosenCourses->isEmpty())
                                            <tr>
                                                <td colspan="4" style="text-align:center;padding:36px 20px;color:#64748B;">
                                                    <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                                        </svg>
                                                        <strong style="color:#0F172A;font-size:14px;">Anda belum mengampu mata kuliah apa pun</strong>
                                                        <span style="font-size:12.5px;color:#64748B;">Hubungi Administrator untuk penugasan mata kuliah ke akun Anda.</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @else
                                            <tr id="emptyCourseRow" style="{{ $hasStudentsInFirstCourse ? 'display:none;' : '' }}">
                                                <td colspan="4" style="text-align:center;padding:32px 20px;color:#94A3B8;">
                                                    Belum ada mahasiswa yang terdaftar di kelas mata kuliah ini.
                                                </td>
                                            </tr>
                                            @foreach ($dosenCourses as $course)
                                                @foreach ($course->students as $mhs)
                                                    @php
                                                        $initials = collect(explode(' ', $mhs->name))->map(fn($w)=>mb_substr($w,0,1))->join('');
                                                        $initials = strtoupper(mb_substr($initials, 0, 2));
                                                        $colors = [
                                                            ['rgba(3, 159, 250, 0.12)', '#039FFA'],
                                                            ['rgba(50, 179, 241, 0.14)', '#0284C7'],
                                                            ['rgba(16, 185, 129, 0.14)', '#10B981'],
                                                            ['rgba(249, 184, 4, 0.14)', '#D97706'],
                                                            ['rgba(249, 99, 5, 0.12)', '#F96305']
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
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
            </section>

        </main>

        <!-- MODAL FORM DAFTARKAN MAHASISWA -->
        <div class="dosen-modal-overlay" id="studentModalOverlay">
            <div class="dosen-modal-card" style="max-width: 580px; width: 95%;">
                <div class="dosen-modal-header">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="section-header-icon" style="width: 36px; height: 36px; background: rgba(3, 159, 250, 0.1); color: #039FFA;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <polyline points="16 11 18 13 22 9"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h3>Pendaftaran Mahasiswa Baru</h3>
                            <span style="font-size: 11px; color: #64748B; font-weight: 600;">Daftarkan peserta ke kelas mata kuliah</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close-modal" id="btnCloseStudentModal" aria-label="Tutup modal">&times;</button>
                </div>

                <form id="formAddStudent" style="margin: 0; display: flex; flex-direction: column;">
                    <div class="dosen-modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div class="form-group">
                            <label for="mhsMatkul">Mata Kuliah Target <span class="required">*</span></label>
                            <select id="mhsMatkul" class="form-select" required {{ $dosenCourses->isEmpty() ? 'disabled' : '' }}>
                                @forelse ($dosenCourses as $c)
                                    <option value="{{ $c->code }} - {{ $c->name }}">{{ $c->code }} - {{ $c->name }}</option>
                                @empty
                                    <option value="">Belum ada mata kuliah yang diampu</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label for="mhsNim">Nomor Induk Mahasiswa (NIM) <span class="required">*</span></label>
                                <input type="text" id="mhsNim" class="form-control" placeholder="Ketik NIM mahasiswa, cth: 10241001" required autocomplete="off">
                                <small id="nimLookupStatus" style="display:block; font-size:11px; margin-top:4px; color:#64748B; font-weight:600;">Masukkan NIM untuk melengkapi nama secara otomatis</small>
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
                            <label for="mhsNama">Nama Lengkap Mahasiswa <span class="required">*</span> <span style="font-size:11px; font-weight:normal; color:#64748B;">(Otomatis terisi dari identitas NIM)</span></label>
                            <input type="text" id="mhsNama" class="form-control" placeholder="Nama otomatis terisi sesuai NIM..." readonly required style="background-color: #F8FAFC; border-color: #CBD5E1; color: #1E293B; cursor: not-allowed; font-weight: 700;">
                        </div>
                    </div>

                    <div class="dosen-modal-footer">
                        <button type="button" class="btn-secondary-action" id="btnCancelStudentModal">Batal</button>
                        <button type="submit" class="btn-primary-action">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Daftarkan Mahasiswa
                        </button>
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

            function showToast(message, isSuccess = true) {
                const container = document.getElementById('toastContainer');
                const toast = document.createElement('div');
                toast.className = 'dosen-toast';
                if (!isSuccess) toast.style.borderLeftColor = '#EF4444';

                toast.innerHTML = `
                    <svg class="toast-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="${isSuccess ? '#10B981' : '#EF4444'}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
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

            // Modal Controls
            const studentModal = document.getElementById('studentModalOverlay');
            const btnOpenAddStudent = document.getElementById('btnOpenAddStudentModal');
            const btnCloseStudentModal = document.getElementById('btnCloseStudentModal');
            const btnCancelStudentModal = document.getElementById('btnCancelStudentModal');

            function openStudentModal() {
                if (!selectCurrentCourse || !selectCurrentCourse.value) {
                    alert('Anda belum memiliki mata kuliah yang diampu untuk mendaftarkan mahasiswa.');
                    return;
                }
                if (studentModal) studentModal.classList.add('active');
            }

            function closeStudentModal() {
                if (studentModal) studentModal.classList.remove('active');
            }

            if (btnOpenAddStudent) btnOpenAddStudent.addEventListener('click', openStudentModal);
            if (btnCloseStudentModal) btnCloseStudentModal.addEventListener('click', closeStudentModal);
            if (btnCancelStudentModal) btnCancelStudentModal.addEventListener('click', closeStudentModal);
            if (studentModal) {
                studentModal.addEventListener('click', (e) => {
                    if (e.target === studentModal) closeStudentModal();
                });
            }
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && studentModal && studentModal.classList.contains('active')) {
                    closeStudentModal();
                }
            });

            const registeredStudentsDb = @json($registeredStudents);
            const mhsNimInput = document.getElementById('mhsNim');
            const mhsNamaInput = document.getElementById('mhsNama');
            const nimLookupStatus = document.getElementById('nimLookupStatus');

            function handleNimLookup() {
                const query = mhsNimInput ? mhsNimInput.value.trim().toLowerCase() : '';
                if (!query) {
                    if (mhsNamaInput) mhsNamaInput.value = '';
                    if (nimLookupStatus) {
                        nimLookupStatus.textContent = 'Masukkan NIM untuk melengkapi nama secara otomatis';
                        nimLookupStatus.style.color = '#64748B';
                    }
                    return;
                }

                // Match exact or contains
                const match = registeredStudentsDb.find(s => s.nim_nip && s.nim_nip.toLowerCase() === query);
                if (match) {
                    mhsNamaInput.value = match.name;
                    if (nimLookupStatus) {
                        nimLookupStatus.textContent = `✓ Mahasiswa teridentifikasi: ${match.name}`;
                        nimLookupStatus.style.color = '#10B981';
                    }
                } else {
                    const partialMatch = registeredStudentsDb.find(s => s.nim_nip && s.nim_nip.toLowerCase().includes(query));
                    if (partialMatch && query.length >= 6) {
                        mhsNamaInput.value = partialMatch.name;
                        if (nimLookupStatus) {
                            nimLookupStatus.textContent = `✓ Mahasiswa teridentifikasi: ${partialMatch.name} (${partialMatch.nim_nip})`;
                            nimLookupStatus.style.color = '#10B981';
                        }
                    } else {
                        mhsNamaInput.value = '';
                        if (nimLookupStatus) {
                            nimLookupStatus.textContent = 'NIM tidak terdaftar di sistem. Periksa kembali NIM.';
                            nimLookupStatus.style.color = '#EF4444';
                        }
                    }
                }
            }

            if (mhsNimInput) {
                mhsNimInput.addEventListener('input', handleNimLookup);
                mhsNimInput.addEventListener('change', handleNimLookup);
            }

            formAddStudent.addEventListener('submit', (e) => {
                e.preventDefault();
                const nim = document.getElementById('mhsNim').value.trim();
                const nama = document.getElementById('mhsNama').value.trim();
                const kelas = document.getElementById('mhsKelas').value;
                const matkulSelect = document.getElementById('mhsMatkul').value;
                const currentMkVal = selectCurrentCourse ? selectCurrentCourse.value : '';

                if (!nim || !nama) {
                    alert('Harap masukkan NIM yang valid dan terdaftar di database agar nama terisi otomatis!');
                    return;
                }

                const initials = nama.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
                const colors = [
                    { bg: 'rgba(3, 159, 250, 0.12)', text: '#039FFA' },
                    { bg: 'rgba(50, 179, 241, 0.14)', text: '#0284C7' },
                    { bg: 'rgba(16, 185, 129, 0.14)', text: '#10B981' },
                    { bg: 'rgba(249, 184, 4, 0.14)', text: '#D97706' },
                    { bg: 'rgba(249, 99, 5, 0.12)', text: '#F96305' }
                ];
                const pickedColor = colors[Math.floor(Math.random() * colors.length)];

                const newRow = document.createElement('tr');
                if (currentMkVal) {
                    newRow.setAttribute('data-mk', currentMkVal);
                }
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
                if (nimLookupStatus) {
                    nimLookupStatus.textContent = 'Masukkan NIM untuk melengkapi nama secara otomatis';
                    nimLookupStatus.style.color = '#64748B';
                }
                closeStudentModal();
                applyCourseFilter();
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

                const emptyRow = document.getElementById('emptyCourseRow');
                if (emptyRow) {
                    emptyRow.style.display = visibleCount === 0 ? '' : 'none';
                }
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
