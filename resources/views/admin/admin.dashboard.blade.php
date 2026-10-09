<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — KampusLMS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/admin/admin.dashboard.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <div class="app-window">

        <!-- Navbar Admin -->
        <x-navbar-admin />

        <!-- Konten Utama -->
        <main class="admin-content">

            <!-- Topbar -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <h1>Dashboard Admin</h1>
                    </div>
                </div>
                <div class="topbar-right">
                    <a href="{{ route('admin.pengguna') }}" class="btn-quick-action btn-outline">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <line x1="19" y1="8" x2="19" y2="14"></line>
                            <line x1="22" y1="11" x2="16" y2="11"></line>
                        </svg>
                        Tambah Pengguna
                    </a>
                    <a href="{{ route('admin.matkul') }}" class="btn-quick-action btn-primary">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Tambah Mata Kuliah
                    </a>
                </div>
            </header>

            @php
                $dbMahasiswaCount = \App\Models\User::where('role', 'mahasiswa')->count();
                $dbDosenCount     = \App\Models\User::where('role', 'dosen')->count();
                $dbAdminCount     = \App\Models\User::where('role', 'admin')->count();
                $dbCourseCount    = \App\Models\Course::count();
                $dbEnrollCount    = \Illuminate\Support\Facades\DB::table('course_user')->count();
                $dbMaterialCount  = \App\Models\Material::count();
                $dbAssignCount    = \App\Models\Assignment::count();
                $dbSubmissCount   = \App\Models\Submission::count();
                $dbGradeCount     = \App\Models\Grade::count();
                $gradePercent     = $dbSubmissCount > 0 ? round(($dbGradeCount / $dbSubmissCount) * 100) : 0;

                // Mengumpulkan riwayat aktivitas dinamis dari database
                $activities = collect();

                foreach (\App\Models\User::latest()->take(3)->get() as $u) {
                    $activities->push([
                        'time_sort' => $u->created_at ?? now(),
                        'dot' => '#039FFA',
                        'title' => 'Pengguna baru terdaftar',
                        'desc' => e($u->name) . ' (' . ucfirst($u->role) . ')' . ($u->nim_nip ? ' &mdash; ' . e($u->nim_nip) : ''),
                        'time' => $u->created_at ? $u->created_at->diffForHumans() : 'Baru saja',
                    ]);
                }

                foreach (\App\Models\Course::with('lecturer')->latest()->take(3)->get() as $c) {
                    $activities->push([
                        'time_sort' => $c->created_at ?? now(),
                        'dot' => '#10B981',
                        'title' => 'Mata kuliah ditambahkan',
                        'desc' => e($c->code) . ' &bull; ' . e($c->name) . ' (' . $c->sks . ' SKS)',
                        'time' => $c->created_at ? $c->created_at->diffForHumans() : 'Baru saja',
                    ]);
                }

                foreach (\App\Models\Material::with('course')->latest()->take(3)->get() as $m) {
                    $activities->push([
                        'time_sort' => $m->created_at ?? now(),
                        'dot' => '#F59E0B',
                        'title' => 'Materi kuliah diunggah',
                        'desc' => e($m->title) . ($m->course ? ' pada ' . e($m->course->code) : ''),
                        'time' => $m->created_at ? $m->created_at->diffForHumans() : 'Baru saja',
                    ]);
                }

                foreach (\App\Models\Assignment::with('course')->latest()->take(3)->get() as $a) {
                    $activities->push([
                        'time_sort' => $a->created_at ?? now(),
                        'dot' => '#F96305',
                        'title' => 'Tugas kuliah dibuat',
                        'desc' => e($a->title) . ($a->course ? ' &bull; ' . e($a->course->code) : ''),
                        'time' => $a->created_at ? $a->created_at->diffForHumans() : 'Baru saja',
                    ]);
                }

                foreach (\App\Models\Submission::with(['student', 'assignment'])->latest('submitted_at')->take(3)->get() as $s) {
                    $subTime = $s->submitted_at ? \Carbon\Carbon::parse($s->submitted_at) : ($s->created_at ?? now());
                    $activities->push([
                        'time_sort' => $subTime,
                        'dot' => '#8B5CF6',
                        'title' => 'Pengumpulan tugas masuk',
                        'desc' => e($s->student?->name ?? 'Mahasiswa') . ' &mdash; ' . e($s->assignment?->title ?? 'Tugas'),
                        'time' => $subTime->diffForHumans(),
                    ]);
                }

                $recentActivities = $activities->sortByDesc('time_sort')->take(6);
            @endphp

            <!-- Stat Cards -->
            <section class="admin-stats-grid">
                <div class="admin-stat-card">
                    <div class="stat-icon-box stat-icon-users">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="stat-details">
                        <span class="stat-label">Total Mahasiswa</span>
                        <span class="stat-value">{{ $dbMahasiswaCount }}</span>
                        <span class="stat-desc">Mahasiswa terdaftar di database</span>
                    </div>
                </div>

                <div class="admin-stat-card">
                    <div class="stat-icon-box stat-icon-dosen">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="stat-details">
                        <span class="stat-label">Total Dosen</span>
                        <span class="stat-value">{{ $dbDosenCount }}</span>
                        <span class="stat-desc">Dosen pengampu aktif</span>
                    </div>
                </div>

                <div class="admin-stat-card">
                    <div class="stat-icon-box stat-icon-matkul">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                    </div>
                    <div class="stat-details">
                        <span class="stat-label">Mata Kuliah</span>
                        <span class="stat-value">{{ $dbCourseCount }}</span>
                        <span class="stat-desc">MK aktif semester ini</span>
                    </div>
                </div>

                <div class="admin-stat-card">
                    <div class="stat-icon-box stat-icon-enroll">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                    </div>
                    <div class="stat-details">
                        <span class="stat-label">Pendaftaran Aktif</span>
                        <span class="stat-value">{{ $dbEnrollCount }}</span>
                        <span class="stat-desc">Total enrollment mahasiswa</span>
                    </div>
                </div>
            </section>

            <!-- Middle: Aktivitas + Right Column -->
            <section class="admin-middle-grid">

                <!-- Aktivitas Terkini (Real-time Database Log) -->
                <div class="card-box">
                    <div class="card-header-clean">
                        <h3>Aktivitas Sistem Terkini</h3>
                        <span class="card-subtitle-tag">Real-time Log</span>
                    </div>
                    <div class="activity-list">
                        @forelse ($recentActivities as $act)
                            <div class="activity-item">
                                <div class="activity-dot" style="background: {{ $act['dot'] }}; box-shadow: 0 0 6px {{ $act['dot'] }}66;"></div>
                                <div class="activity-info">
                                    <strong>{{ $act['title'] }}</strong>
                                    <span>{!! $act['desc'] !!}</span>
                                </div>
                                <span class="activity-time">{{ $act['time'] }}</span>
                            </div>
                        @empty
                            <div style="padding: 24px; text-align: center; color: #64748B; font-size: 13px;">
                                Belum ada aktivitas baru tercatat pada sistem.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Right Column: Status Sistem -->
                <div class="admin-side-col">
                    <!-- Status Sistem & Akademik (Real-time Metric) -->
                    <div class="card-box sys-status-card">
                        <div class="card-header-clean">
                            <h3>Status Sistem & Akademik</h3>
                            <span class="status-indicator-badge">
                                <span class="status-pulse-dot"></span> Server Normal
                            </span>
                        </div>
                        <div class="sys-status-list">
                            <div class="sys-item">
                                <div class="sys-item-text">
                                    <span class="sys-item-label">Server & Framework</span>
                                    <strong class="sys-item-val">PHP {{ PHP_VERSION }} &bull; Laravel {{ app()->version() }}</strong>
                                </div>
                                <span class="sys-pill sys-pill-primary">Aktif</span>
                            </div>
                            <div class="sys-item">
                                <div class="sys-item-text">
                                    <span class="sys-item-label">Progres Penilaian</span>
                                    <strong class="sys-item-val">{{ $dbGradeCount }} dari {{ $dbSubmissCount }} berkas dinilai ({{ $gradePercent }}%)</strong>
                                </div>
                                <span class="sys-pill sys-pill-success">{{ $gradePercent }}% Selesai</span>
                            </div>
                            <div class="sys-item">
                                <div class="sys-item-text">
                                    <span class="sys-item-label">Database & Penyimpanan</span>
                                    <strong class="sys-item-val">{{ config('database.default') == 'sqlite' ? 'SQLite' : 'MySQL' }} &bull; {{ $dbMaterialCount }} Materi &bull; {{ $dbAssignCount }} Tugas</strong>
                                </div>
                                <span class="sys-pill sys-pill-ok">Optimal</span>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

        </main>

        <x-footer />
    </div>
</body>
</html>
