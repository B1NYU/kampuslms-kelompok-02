<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student Management Campus</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    <!-- Memuat CSS via Vite sesuai lokasi resources/css/mahasiswa/mahasiswa.dashboard.css -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/mahasiswa/mahasiswa.dashboard.css', 'resources/js/app.js'])
    @endif
</head>

<body>

    <!-- Background Decorative Glow (selaras dengan halaman Daftar Anggota) -->
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <!-- Main Window Canvas -->
    <div class="app-window">

        <!-- 1. Panggil Komponen Sidebar -->
        <x-layout />

        <!-- 2. Konten Utama Dashboard -->
        <main class="dashboard-content">

            <!-- Topbar (Hamburger & Profil) -->
            <header class="dash-topbar">
                <div class="topbar-left">
                    <div class="page-title">
                        <span class="page-eyebrow">
                            <span class="page-eyebrow-dot"></span>
                            OVERVIEW AKADEMIK • KELOMPOK 02
                        </span>
                        <h1>Dashboard LMS</h1>
                    </div>
                </div>

                <div class="topbar-right">
                    <div class="user-badge">
                        <div class="user-badge-avatar">BK</div>
                        <div class="user-badge-details">
                            <span class="user-badge-info">Baihaqi & Tim</span>
                            <span class="user-badge-role">Kelompok 02</span>
                        </div>
                    </div>
                </div>
            </header>

            @php
                $mhsUser = \App\Models\User::where('role', 'mahasiswa')->where('email', 'mahasiswa@kampuslms.test')->first()
                    ?? \App\Models\User::where('role', 'mahasiswa')->first();

                // Mata kuliah dari database
                $dbCourses = $mhsUser ? $mhsUser->courses()->with('lecturer')->get() : collect();
                if ($dbCourses->isEmpty()) {
                    $dbCourses = \App\Models\Course::with('lecturer')->get();
                }
                $totalCourseCount = $dbCourses->count();
                $totalSks = $dbCourses->sum('sks');

                // Tugas aktif & pending
                $dbAssignments = \App\Models\Assignment::whereIn('course_id', $dbCourses->pluck('id'))
                    ->where('status', 'published')
                    ->with('course')
                    ->orderBy('due_at')
                    ->get();
                $tugasPending = $dbAssignments->where('due_at', '>=', now())->count();
                $tugasSelesai = $mhsUser ? \App\Models\Submission::where('user_id', $mhsUser->id)->count() : 0;
            @endphp

            <!-- Row 1: 3 Stat Cards terhubung ke Database -->
            <section class="stats-grid">
                <div class="stat-card">
                    <span class="stat-label">Mata Kuliah Aktif</span>
                    <span class="stat-value stat-color-2">{{ $totalCourseCount }} MK</span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Total Beban SKS</span>
                    <span class="stat-value stat-color-3">{{ $totalSks }} SKS</span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Tugas Terjadwal</span>
                    <span class="stat-value stat-color-4">{{ $tugasPending }}</span>
                </div>
            </section>

            <!-- Row 2: Middle Section (List Mata Kuliah Diambil & Aktivitas Mingguan) -->
            <section class="middle-grid">

                <!-- Kiri: List Mata Kuliah yang Diambil dari Database -->
                <div class="card-box">
                    <div class="card-header-clean">
                        <div>
                            <h3 style="margin:0;">Mata Kuliah yang Diambil</h3>
                            <small style="color:#64748B;font-size:12px;">Data langsung dari database LMS</small>
                        </div>
                        <a href="{{ route('mata-kuliah.index') }}" class="card-subtitle-tag" style="text-decoration:none;">Lihat Semua &rarr;</a>
                    </div>

                    <div class="mk-list">
                        @forelse ($dbCourses as $mk)
                            <a href="{{ route('mata-kuliah.show', $mk->id) }}" class="mk-row" style="text-decoration:none;color:inherit;transition:background 0.2s;">
                                <span class="mk-code">{{ $mk->code }}</span>
                                <div class="mk-info">
                                    <span class="mk-name">{{ $mk->name }}</span>
                                    <span class="mk-dosen">{{ $mk->lecturer?->name ?? 'Dosen Pengampu' }}</span>
                                </div>
                                <span class="mk-sks">{{ $mk->sks }} SKS</span>
                                <span class="mk-status mk-status-aktif">{{ ucfirst($mk->status ?? 'Aktif') }}</span>
                            </a>
                        @empty
                            <div style="padding:20px;text-align:center;color:#94A3B8;">Belum ada mata kuliah yang diambil.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Kanan: Aktivitas Mingguan (Mata Kuliah Terpopuler dihapus) -->
                <div class="right-column-stack">
                    <div class="card-box">
                        <div class="card-header-clean">
                            <h3>Aktivitas Mingguan</h3>
                            <span class="card-subtitle-tag">+18%</span>
                        </div>
                        <div class="bars-container">
                            <div class="bar-pill" style="height: 35%;"></div>
                            <div class="bar-pill" style="height: 60%;"></div>
                            <div class="bar-pill" style="height: 45%;"></div>
                            <div class="bar-pill" style="height: 80%;"></div>
                            <div class="bar-pill" style="height: 55%;"></div>
                            <div class="bar-pill" style="height: 90%;"></div>
                            <div class="bar-pill" style="height: 65%;"></div>
                            <div class="bar-pill" style="height: 40%;"></div>
                            <div class="bar-pill" style="height: 75%;"></div>
                            <div class="bar-pill" style="height: 95%;"></div>
                            <div class="bar-pill" style="height: 50%;"></div>
                            <div class="bar-pill" style="height: 70%;"></div>
                        </div>
                    </div>
                </div>

            </section>

            <!-- Row 3: Bottom Section (Progres Bulat & Pengumuman) -->
            <section class="bottom-grid">

                <!-- Kiri: Donut Progress Gauges -->
                <div class="card-box">
                    <div class="card-header-clean">
                        <h3>Progres Akademik Mahasiswa</h3>
                    </div>
                    <div class="progress-gauges">

                        <div class="gauge-item">
                            <div class="gauge-circle-wrap">
                                <svg class="gauge-svg" viewBox="0 0 36 36">
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="rgba(3, 159, 250, 0.12)" stroke-width="3.2" />
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="#039FFA" stroke-width="3.2" stroke-dasharray="85, 100" stroke-linecap="round" />
                                </svg>
                                <span class="gauge-text">85%</span>
                            </div>
                            <span class="gauge-label">Presensi</span>
                        </div>

                        <div class="gauge-item">
                            <div class="gauge-circle-wrap">
                                <svg class="gauge-svg" viewBox="0 0 36 36">
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="rgba(3, 159, 250, 0.12)" stroke-width="3.2" />
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="#F96305" stroke-width="3.2" stroke-dasharray="70, 100" stroke-linecap="round" />
                                </svg>
                                <span class="gauge-text">70%</span>
                            </div>
                            <span class="gauge-label">Tugas Selesai</span>
                        </div>

                        <div class="gauge-item">
                            <div class="gauge-circle-wrap">
                                <svg class="gauge-svg" viewBox="0 0 36 36">
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="rgba(3, 159, 250, 0.12)" stroke-width="3.2" />
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="#F9B804" stroke-width="3.2" stroke-dasharray="92, 100" stroke-linecap="round" />
                                </svg>
                                <span class="gauge-text">92%</span>
                            </div>
                            <span class="gauge-label">Target SKS</span>
                        </div>

                    </div>
                </div>

                <!-- Kanan: Pesan & Pengumuman Terbaru -->
                <div class="card-box">
                    <div class="card-header-clean">
                        <h3>Tugas & Pengumuman Terkini</h3>
                        <a href="{{ route('mata-kuliah.index') }}" class="link-see-all">Semua MK</a>
                    </div>
                    <div class="message-list">
                        @forelse ($dbAssignments->take(3) as $assign)
                            @php
                                $isDuePast = $assign->due_at ? $assign->due_at->isPast() : false;
                            @endphp
                            <div class="message-item">
                                <span class="msg-sender">{{ $assign->course?->code ?? 'MK' }}</span>
                                <span class="msg-badge" style="background:#FFFDF8; color:{{ $isDuePast ? '#F96305' : '#039FFA' }}; border:1px solid {{ $isDuePast ? 'rgba(249, 99, 5, 0.35)' : 'rgba(3, 159, 250, 0.35)' }};">
                                    {{ $isDuePast ? 'Lewat Deadline' : 'Aktif' }}
                                </span>
                                <span class="msg-body"><strong>{{ $assign->title }}</strong> &bull; Batas: {{ $assign->due_at ? $assign->due_at->translatedFormat('d M Y, H:i') : '-' }}</span>
                            </div>
                        @empty
                            <div class="message-item">
                                <span class="msg-sender">Sistem</span>
                                <span class="msg-badge">Info</span>
                                <span class="msg-body">Belum ada tugas baru yang dipublikasikan.</span>
                            </div>
                        @endforelse
                    </div>
                </div>

            </section>

        </main>
        <x-footer />
    </div>
</body>
</html>
