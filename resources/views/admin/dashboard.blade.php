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
                        <span class="page-eyebrow">
                            <span class="page-eyebrow-dot"></span>
                            PANEL KONTROL SISTEM • KAMPUSLMS
                        </span>
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
                        <span class="stat-value">248</span>
                        <span class="stat-desc">Pengguna terdaftar</span>
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
                        <span class="stat-value">32</span>
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
                        <span class="stat-value">54</span>
                        <span class="stat-desc">MK semester ini</span>
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
                        <span class="stat-value">1,024</span>
                        <span class="stat-desc">Total enrollment MK</span>
                    </div>
                </div>
            </section>

            <!-- Middle: Aktivitas + Right Column -->
            <section class="admin-middle-grid">

                <!-- Aktivitas Terkini -->
                <div class="card-box">
                    <div class="card-header-clean">
                        <h3>Aktivitas Sistem Terkini</h3>
                        <span class="card-subtitle-tag">Real-time Log</span>
                    </div>
                    <div class="activity-list">
                        @php
                            $activities = [
                                ['dot' => '#B0182D', 'title' => 'Pengguna baru didaftarkan', 'desc' => 'Baihaqi Abimanyu (Mahasiswa) &mdash; NIM 10241014', 'time' => '2 mnt lalu'],
                                ['dot' => '#22C55E', 'title' => 'Mata kuliah ditambahkan', 'desc' => 'SI104 &bull; Pemrograman Mobile (3 SKS)', 'time' => '14 mnt lalu'],
                                ['dot' => '#F59E0B', 'title' => 'Pendaftaran mahasiswa', 'desc' => 'Calvin Adithya didaftarkan ke SI101', 'time' => '31 mnt lalu'],
                                ['dot' => '#EF4444', 'title' => 'Pengguna dinonaktifkan', 'desc' => 'Akun mahasiswa IF312-C dibekukan admin', 'time' => '1 jam lalu'],
                                ['dot' => '#B0182D', 'title' => 'Role diperbarui', 'desc' => 'Dr. Rina Marlina &mdash; role diubah menjadi Dosen', 'time' => '2 jam lalu'],
                                ['dot' => '#22C55E', 'title' => 'Data mata kuliah diperbarui', 'desc' => 'IF305 &bull; Kecerdasan Buatan &mdash; SKS diubah ke 4', 'time' => '3 jam lalu'],
                            ];
                        @endphp
                        @foreach ($activities as $act)
                            <div class="activity-item">
                                <div class="activity-dot" style="background: {{ $act['dot'] }}; box-shadow: 0 0 6px {{ $act['dot'] }}66;"></div>
                                <div class="activity-info">
                                    <strong>{{ $act['title'] }}</strong>
                                    <span>{!! $act['desc'] !!}</span>
                                </div>
                                <span class="activity-time">{{ $act['time'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Right Column: Akses Cepat + Status Sistem -->
                <div class="admin-side-col">
                    <!-- Akses Cepat Admin -->
                    <div class="card-box">
                        <div class="card-header-clean">
                            <h3>Akses Cepat Admin</h3>
                            <span class="card-subtitle-tag">Pintasan</span>
                        </div>
                        <div class="quick-actions-grid">
                            <a href="{{ route('admin.pengguna') }}" class="qa-card">
                                <div class="qa-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <line x1="19" y1="8" x2="19" y2="14"></line>
                                        <line x1="22" y1="11" x2="16" y2="11"></line>
                                    </svg>
                                </div>
                                Tambah Pengguna
                            </a>
                            <a href="{{ route('admin.matkul') }}" class="qa-card">
                                <div class="qa-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                    </svg>
                                </div>
                                CRUD Mata Kuliah
                            </a>
                            <a href="{{ route('admin.pendaftaran') }}" class="qa-card">
                                <div class="qa-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <polyline points="16 11 18 13 22 9"></polyline>
                                    </svg>
                                </div>
                                Daftarkan Mahasiswa
                            </a>
                            <a href="#laporan" class="qa-card">
                                <div class="qa-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="18" y1="20" x2="18" y2="10"></line>
                                        <line x1="12" y1="20" x2="12" y2="4"></line>
                                        <line x1="6" y1="20" x2="6" y2="14"></line>
                                    </svg>
                                </div>
                                Laporan & Log
                            </a>
                        </div>
                    </div>

                    <!-- Status Sistem & Akademik -->
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
                                    <span class="sys-item-label">Tahun Akademik</span>
                                    <strong class="sys-item-val">Ganjil 2026/2027</strong>
                                </div>
                                <span class="sys-pill sys-pill-primary">Aktif</span>
                            </div>
                            <div class="sys-item">
                                <div class="sys-item-text">
                                    <span class="sys-item-label">Periode KRS Mahasiswa</span>
                                    <strong class="sys-item-val">01 Sep &mdash; 30 Sep 2026</strong>
                                </div>
                                <span class="sys-pill sys-pill-success">Berjalan</span>
                            </div>
                            <div class="sys-item">
                                <div class="sys-item-text">
                                    <span class="sys-item-label">Server & Database</span>
                                    <strong class="sys-item-val">MySQL 8.0 &bull; PHP 8.5 &bull; Laravel 12</strong>
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
