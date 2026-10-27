<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — KampusLMS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-[#F1F5F9] min-h-screen text-slate-800 antialiased font-['Nunito',sans-serif] relative overflow-x-hidden">

    <!-- Soft Decorative Atmospheric Glow -->
    <div class="fixed top-[-80px] right-[-80px] w-96 h-96 bg-sky-400/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed bottom-[-80px] left-[-80px] w-96 h-96 bg-amber-400/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed top-1/2 right-1/4 w-72 h-72 bg-orange-400/10 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <div class="min-h-screen flex flex-col justify-between relative z-10">

        <!-- Navbar Admin -->
        <x-navbar-admin />

        <!-- Konten Utama Dashboard (Ukuran Proporsional & Padat) -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex flex-col gap-6 box-border">

            <!-- Topbar Header -->
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Selamat Datang, Admin :)</h1>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-sky-50 text-[#039FFA] border border-sky-200/80 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-[#039FFA] animate-pulse"></span>
                            Portal Aktif
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-semibold text-slate-500 mt-1">Ringkasan aktivitas terkini dan metrik operasional KampusLMS</p>
                </div>
                <div class="inline-flex items-center gap-2.5 text-xs font-extrabold text-slate-600 bg-white px-4 py-2.5 rounded-xl border border-sky-100 shadow-sm w-fit self-start sm:self-auto">
                    <svg class="w-4 h-4 text-[#039FFA]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>{{ now()->translatedFormat('l, d F Y') }}</span>
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

            <!-- 1. 4 Stat Cards (Padat & Proporsional) -->
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">

                <!-- 1. Total Mahasiswa -->
                <div class="bg-white rounded-2xl p-5 border border-sky-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition duration-200 flex items-center justify-between group">
                    <div class="flex flex-col min-w-0 pr-3">
                        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Mahasiswa</span>
                        <span class="text-3xl font-black text-slate-900 leading-tight mt-1">{{ $dbMahasiswaCount }}</span>
                        <span class="text-[11px] font-semibold text-slate-400 truncate mt-0.5">Mahasiswa terdaftar</span>
                    </div>
                    <div class="w-13 h-13 rounded-2xl bg-sky-50 text-[#039FFA] flex items-center justify-center shrink-0 border border-sky-100 group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-6 h-6 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                </div>

                <!-- 2. Total Dosen -->
                <div class="bg-white rounded-2xl p-5 border border-amber-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition duration-200 flex items-center justify-between group">
                    <div class="flex flex-col min-w-0 pr-3">
                        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Dosen</span>
                        <span class="text-3xl font-black text-slate-900 leading-tight mt-1">{{ $dbDosenCount }}</span>
                        <span class="text-[11px] font-semibold text-slate-400 truncate mt-0.5">Dosen pengampu aktif</span>
                    </div>
                    <div class="w-13 h-13 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0 border border-amber-100 group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-6 h-6 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                </div>

                <!-- 3. Mata Kuliah -->
                <div class="bg-white rounded-2xl p-5 border border-emerald-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition duration-200 flex items-center justify-between group">
                    <div class="flex flex-col min-w-0 pr-3">
                        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Mata Kuliah</span>
                        <span class="text-3xl font-black text-slate-900 leading-tight mt-1">{{ $dbCourseCount }}</span>
                        <span class="text-[11px] font-semibold text-slate-400 truncate mt-0.5">MK aktif semester ini</span>
                    </div>
                    <div class="w-13 h-13 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0 border border-emerald-100 group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-6 h-6 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                    </div>
                </div>

                <!-- 4. Pendaftaran Aktif -->
                <div class="bg-white rounded-2xl p-5 border border-orange-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition duration-200 flex items-center justify-between group">
                    <div class="flex flex-col min-w-0 pr-3">
                        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Pendaftaran</span>
                        <span class="text-3xl font-black text-slate-900 leading-tight mt-1">{{ $dbEnrollCount }}</span>
                        <span class="text-[11px] font-semibold text-slate-400 truncate mt-0.5">Total enrollment aktif</span>
                    </div>
                    <div class="w-13 h-13 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center shrink-0 border border-orange-100 group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-6 h-6 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                    </div>
                </div>

            </section>

            <!-- 2. Middle Grid: Aktivitas (7 Kolom) vs Status & Aksi Cepat (5 Kolom) -->
            <section class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Kolom Kiri: Aktivitas Sistem Terkini (7 Kolom) -->
                <div class="lg:col-span-7 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-sky-50 text-[#039FFA] flex items-center justify-center border border-sky-100">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                                    </svg>
                                </div>
                                <h3 class="text-base font-extrabold text-slate-900">Aktivitas Sistem Terkini</h3>
                            </div>
                            <span class="text-[11px] font-extrabold text-slate-400 bg-slate-50 px-3 py-1 rounded-full border border-slate-200/70">
                                Real-time Log
                            </span>
                        </div>

                        <div class="flex flex-col gap-2.5">
                            @forelse ($recentActivities as $act)
                                <div class="flex items-center gap-3.5 p-3.5 rounded-xl bg-slate-50/70 border border-slate-100 hover:bg-sky-50/40 hover:border-sky-300/60 hover:translate-x-1 transition-all duration-150">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0 shadow-sm" style="background: {{ $act['dot'] }}; box-shadow: 0 0 8px {{ $act['dot'] }}88;"></span>
                                    <div class="flex-1 min-w-0">
                                        <strong class="block text-xs sm:text-[13px] font-extrabold text-slate-800 truncate">{{ $act['title'] }}</strong>
                                        <span class="block text-xs font-semibold text-slate-500 truncate mt-0.5">{!! $act['desc'] !!}</span>
                                    </div>
                                    <span class="text-[11px] font-bold text-slate-400 whitespace-nowrap bg-white px-2 py-0.5 rounded-md border border-slate-200/60">{{ $act['time'] }}</span>
                                </div>
                            @empty
                                <div class="py-12 text-center text-slate-400 text-xs font-semibold">
                                    Belum ada aktivitas baru tercatat pada sistem.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Status Sistem & Aksi Cepat (5 Kolom) -->
                <div class="lg:col-span-5 flex flex-col gap-6">

                    <!-- Kotak 1: Status Sistem & Akademik -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
                        <div class="flex items-center gap-2.5 pb-4 mb-4 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-[#10B981] flex items-center justify-center border border-emerald-100">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
                                    <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
                                    <line x1="6" y1="6" x2="6.01" y2="6"></line>
                                    <line x1="6" y1="18" x2="6.01" y2="18"></line>
                                </svg>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-900">Status Sistem & Akademik</h3>
                        </div>

                        <div class="flex flex-col gap-3">
                            <!-- Item 1: Server & Framework -->
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between gap-3">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Server & Framework</span>
                                    <strong class="text-xs sm:text-[13px] font-extrabold text-slate-800 mt-0.5">PHP {{ PHP_VERSION }} &bull; Laravel {{ app()->version() }}</strong>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-sky-50 text-[#039FFA] border border-sky-200 shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#039FFA]"></span>
                                    Aktif
                                </span>
                            </div>

                            <!-- Item 2: Progres Penilaian -->
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex flex-col gap-2">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Progres Penilaian</span>
                                        <strong class="text-xs sm:text-[13px] font-extrabold text-slate-800 mt-0.5">{{ $dbGradeCount }} dari {{ $dbSubmissCount }} berkas</strong>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-50 text-[#10B981] border border-emerald-200 shrink-0">
                                        {{ $gradePercent }}% Selesai
                                    </span>
                                </div>
                                <!-- Progress Bar -->
                                <div class="w-full bg-slate-200/70 h-2 rounded-full overflow-hidden">
                                    <div class="bg-gradient-to-r from-emerald-400 to-[#10B981] h-full rounded-full transition-all duration-500" style="width: {{ min(100, max(5, $gradePercent)) }}%"></div>
                                </div>
                            </div>

                            <!-- Item 3: Database & Konten -->
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between gap-3">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Database & Konten</span>
                                    <strong class="text-xs sm:text-[13px] font-extrabold text-slate-800 mt-0.5">{{ config('database.default') == 'sqlite' ? 'SQLite' : 'MySQL' }} &bull; {{ $dbMaterialCount }} Materi</strong>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-600 border border-slate-200 shrink-0">
                                    Optimal
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Kotak 2: Aksi Cepat Admin (Mengisi Ruang agar Seimbang) -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Pusat Aksi Cepat</h3>
                            <span class="text-xs text-slate-400 font-semibold">Pintasan Fitur</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('admin.pengguna') }}" class="p-3 rounded-xl bg-slate-50 border border-slate-100 hover:border-sky-300 hover:bg-sky-50/50 hover:-translate-y-0.5 transition duration-150 flex flex-col items-center justify-center text-center gap-1.5 group">
                                <div class="w-8 h-8 rounded-lg bg-sky-100/70 text-[#039FFA] flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <span class="text-xs font-bold text-slate-700 group-hover:text-[#039FFA]">Kelola Pengguna</span>
                            </a>
                            <a href="{{ route('admin.matkul') }}" class="p-3 rounded-xl bg-slate-50 border border-slate-100 hover:border-emerald-300 hover:bg-emerald-50/50 hover:-translate-y-0.5 transition duration-150 flex flex-col items-center justify-center text-center gap-1.5 group">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100/70 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                    </svg>
                                </div>
                                <span class="text-xs font-bold text-slate-700 group-hover:text-emerald-600">Mata Kuliah</span>
                            </a>
                            <a href="{{ route('admin.materi') }}" class="p-3 rounded-xl bg-slate-50 border border-slate-100 hover:border-amber-300 hover:bg-amber-50/50 hover:-translate-y-0.5 transition duration-150 flex flex-col items-center justify-center text-center gap-1.5 group">
                                <div class="w-8 h-8 rounded-lg bg-amber-100/70 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                    </svg>
                                </div>
                                <span class="text-xs font-bold text-slate-700 group-hover:text-amber-600">Unggah Materi</span>
                            </a>
                            <a href="{{ route('admin.tugas') }}" class="p-3 rounded-xl bg-slate-50 border border-slate-100 hover:border-orange-300 hover:bg-orange-50/50 hover:-translate-y-0.5 transition duration-150 flex flex-col items-center justify-center text-center gap-1.5 group">
                                <div class="w-8 h-8 rounded-lg bg-orange-100/70 text-orange-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line>
                                    </svg>
                                </div>
                                <span class="text-xs font-bold text-slate-700 group-hover:text-orange-600">Tugas Kuliah</span>
                            </a>
                        </div>
                    </div>

                </div>

            </section>

        </main>

        <!-- Footer Global -->
        <x-footer />
    </div>
</body>
</html>
