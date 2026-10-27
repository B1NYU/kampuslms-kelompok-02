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
        @vite(['resources/css/app.css', 'resources/css/dosen/dosen.mahasiswa.css', 'resources/js/app.js', 'resources/js/dosen/dosen.mahasiswa.js'])
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

            {{-- Data ($courses, $registeredStudents, $selectedCode) disiapkan oleh Dosen\StudentController@index --}}
            @php
                $dosenCourses = $courses;
                $firstCourse = $dosenCourses->firstWhere('code', $selectedCode);
                // MK yang pesertanya boleh diubah (CoursePolicy::manageEnrollment: dosen pengampu, MK draft/active).
                $enrollableCourses = $dosenCourses->filter(fn ($c) => auth()->user()->can('manageEnrollment', $c))->values();
            @endphp

            <!-- Topbar Header / Judul di Luar Container Utama -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <h1>Pendaftaran Mahasiswa ke Mata Kuliah</h1>
                    </div>
                </div>
            </header>

            <!-- Section: Form & Tabel Mahasiswa -->
            <section class="feature-section" id="kelola-mahasiswa">

                <!-- 1. Toolbar Kontrol di LUAR Card Utama (Search di Kiri, Filter MK & Tombol Daftarkan di Kanan) -->
                <div class="table-toolbar-row">
                    <!-- Kolom Mencari Mahasiswa (Kiri) -->
                    <div class="search-input-box">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" id="searchStudentInput" placeholder="Cari nama mahasiswa atau NIM...">
                    </div>

                    <!-- Sisi Kanan: Filter MK & Tombol Daftarkan Mahasiswa -->
                    <div class="toolbar-right-group">
                        <div class="course-filter-bar">
                            <select id="selectCurrentCourse" class="course-select" {{ $dosenCourses->isEmpty() ? 'disabled' : '' }}>
                                @forelse ($dosenCourses as $c)
                                    <option value="{{ $c->code }}" {{ $c->code === ($selectedCode ?? ($firstCourse?->code ?? '')) ? 'selected' : '' }}>
                                        {{ $c->code }} &bull; {{ $c->name }} ({{ $c->sks }} SKS - {{ $c->students->count() }} Mhs)
                                    </option>
                                @empty
                                    <option value="">Belum ada mata kuliah yang diampu</option>
                                @endforelse
                            </select>
                            <svg class="course-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>

                        @if ($enrollableCourses->isNotEmpty())
                        <button type="button" class="btn-primary-action" id="btnOpenAddStudentModal">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Daftarkan Mahasiswa</span>
                        </button>
                        @endif
                    </div>
                </div>

                <!-- 2. Card Utama Berisi Header Card ('All Deals' style & Sort by) dan Tabel Mahasiswa -->
                <div class="section-card">

                    <!-- Header Card: Info Mahasiswa Terdaftar (Kiri) & Sort By (Kanan) -->
                    <div class="table-card-header">
                        <div class="table-card-header-left">
                            <span class="table-summary-info">Menampilkan <strong id="studentTableCount">{{ $firstCourse?->students?->count() ?? 0 }}</strong> Mahasiswa Terdaftar</span>
                        </div>

                        <div class="table-card-header-right">
                            <div class="sort-control-wrap">
                                <span class="sort-label">Sort by:</span>
                                <div class="sort-select-box">
                                    <select id="sortStudentSelect" class="sort-select">
                                        <option value="default">Default</option>
                                        <option value="name-asc">Nama (A &rarr; Z)</option>
                                        <option value="name-desc">Nama (Z &rarr; A)</option>
                                        <option value="nim-asc">NIM (Terkecil)</option>
                                        <option value="nim-desc">NIM (Terbesar)</option>
                                    </select>
                                    <svg class="sort-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Mahasiswa Terdaftar (Full Width) -->
                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="custom-dosen-table table-clean-style" id="studentTable">
                                    <thead>
                                        <tr>
                                            <th>Mahasiswa</th>
                                            <th>NIM</th>
                                            <th>Program Studi</th>
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
                                                <td colspan="6" style="text-align:center;padding:36px 20px;color:#64748B;">
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
                                                <td colspan="6" style="text-align:center;padding:32px 20px;color:#94A3B8;">
                                                    Belum ada mahasiswa yang terdaftar di kelas mata kuliah ini.
                                                </td>
                                            </tr>
                                            @php $orderIdx = 0; @endphp
                                            @foreach ($dosenCourses as $course)
                                                @foreach ($course->students as $mhs)
                                                    @php
                                                        $orderIdx++;
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
                                                    <tr data-mk="{{ $course->code }}"
                                                        data-name="{{ strtolower($mhs->name) }}"
                                                        data-nim="{{ $mhs->nim_nip }}"
                                                        data-order="{{ $orderIdx }}"
                                                        style="{{ $course->code === $selectedCourseCode ? '' : 'display:none;' }}">
                                                        <td>
                                                            <div class="student-cell">
                                                                <div class="student-avatar" style="background:{{ $bg }}; color:{{ $c }};">{{ $initials }}</div>
                                                                <span class="student-name">{{ $mhs->name }}</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="student-nim">{{ $mhs->nim_nip }}</span>
                                                        </td>
                                                        <td>Sistem Informasi</td>
                                                        <td><span class="card-subtitle-tag" style="padding:2px 8px; font-size:10px;">SI-A</span></td>
                                                        <td><span class="badge-status badge-status-active">Aktif</span></td>
                                                        <td style="text-align: right;">
                                                            @can('manageEnrollment', $course)
                                                            <form method="POST"
                                                                  action="{{ route('dosen.mahasiswa.destroy', [$course, $mhs]) }}"
                                                                  onsubmit="return confirm(@js('Keluarkan ' . $mhs->name . ' dari mata kuliah ini?'))"
                                                                  style="display:inline; margin:0;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn-icon-danger btn-delete-student" title="Keluarkan dari kelas">
                                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                            @endcan
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

                <form id="formAddStudent" method="POST" action="{{ route('dosen.mahasiswa.store') }}" style="margin: 0; display: flex; flex-direction: column;">
                    @csrf
                    <div class="dosen-modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div class="form-group">
                            <label for="mhsMatkul">Mata Kuliah Target <span class="required">*</span></label>
                            <select id="mhsMatkul" name="course_id" class="form-select" required {{ $enrollableCourses->isEmpty() ? 'disabled' : '' }}>
                                @forelse ($enrollableCourses as $c)
                                    <option value="{{ $c->id }}" data-code="{{ $c->code }}" {{ (string) old('course_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->code }} - {{ $c->name }}</option>
                                @empty
                                    <option value="">Belum ada mata kuliah yang diampu</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="mhsNim">Nomor Induk Mahasiswa (NIM) <span class="required">*</span></label>
                            <input type="text" id="mhsNim" name="nim" value="{{ old('nim') }}" class="form-control" placeholder="Ketik NIM mahasiswa, cth: 10241001" required autocomplete="off">
                            <small id="nimLookupStatus" style="display:block; font-size:11px; margin-top:4px; color:#64748B; font-weight:600;">Masukkan NIM untuk melengkapi nama secara otomatis</small>
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
    <script type="application/json" id="registeredStudentsData">@json($registeredStudents)</script>
    <div id="dosenMahasiswaConfig" style="display:none;" data-toast-success="{{ session('success') }}" data-toast-error="{{ $errors->first() }}" data-has-errors="{{ $errors->any() ? '1' : '0' }}"></div>
</body>

</html>
