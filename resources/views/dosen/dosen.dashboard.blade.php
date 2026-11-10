<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dosen — Portal KampusLMS</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    <!-- Memuat CSS & JS via Vite (Hanya Tailwind app.css murni tanpa file CSS eksternal) -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="bg-[#F1F5F9] min-h-screen text-slate-800 antialiased font-['Nunito',sans-serif] relative overflow-x-hidden">

    <!-- Soft Decorative Atmospheric Glow -->
    <div class="fixed -top-24 -right-24 w-96 h-96 bg-sky-400/20 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed -bottom-24 -left-24 w-96 h-96 bg-amber-400/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed top-1/2 right-1/4 w-72 h-72 bg-orange-400/10 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <div class="min-h-screen flex flex-col justify-between relative z-10">

        <!-- 1. Navbar Khusus Dosen -->
        <x-navbar-dosen />

        <!-- [RESPONSIVE-MOBILE] Konten Utama Dashboard Dosen (Responsif Mobile & Desktop) -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8 flex flex-col gap-4 sm:gap-6 box-border" id="overview">

            @php
                // Perhitungan dipindah ke App\Support\DosenDashboardStats (COUNT di database + cache 10 menit,
                // kecuali "perlu dinilai" yang sengaja selalu segar). Lihat docs/performa.md bagian Cache.
                $stats = \App\Support\DosenDashboardStats::for(auth()->user());
                $dashCourses = $stats['courses'];
                $totalStudents = $stats['totalStudents'];
                $totalMaterials = $stats['totalMaterials'];
                $totalAssignments = $stats['totalAssignments'];
                $pendingGrading = $stats['pendingGrading'];
            @endphp

            <!-- Topbar Header -->
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 pb-2 border-b border-slate-200/80">
                <div class="flex items-center gap-3">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <span class="inline-block w-1.5 h-5 sm:h-7 rounded-full bg-gradient-to-b from-sky-500 to-sky-400"></span>
                        Dashboard Manajemen Perkuliahan
                    </h1>
                </div>

                <!-- Course Selector / Filter Kelas -->
                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-sky-100 shadow-xs w-full sm:w-auto overflow-hidden">
                    <span class="text-xs font-bold text-slate-500 shrink-0">MK Aktif:</span>
                    <select id="selectCurrentCourse" class="text-xs font-extrabold text-sky-700 bg-transparent border-none outline-none cursor-pointer focus:ring-0 truncate w-full sm:w-auto">
                        @forelse ($dashCourses as $c)
                            <option value="{{ $c->id }}">
                                {{ $c->code }} &bull; {{ $c->name }} ({{ $c->sks }} SKS)
                            </option>
                        @empty
                            <option value="">Belum ada mata kuliah aktif</option>
                        @endforelse
                    </select>
                </div>
            </header>

            <!-- 3. Kartu Statistik Utama Dosen (4 Cards) -->
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- 1. Mahasiswa Bimbingan -->
                <a href="{{ route('dosen.mahasiswa') }}" class="bg-white rounded-2xl p-5 border border-sky-100 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition duration-200 flex flex-col justify-between gap-3 group no-underline text-inherit">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Mahasiswa Bimbingan</span>
                        <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 group-hover:bg-sky-500 group-hover:text-white transition duration-200">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <span class="text-3xl font-black text-slate-900 tracking-tight" id="statTotalStudents">{{ $totalStudents }}</span>
                        <div class="mt-2 text-xs font-bold text-sky-600 flex items-center gap-1 group-hover:gap-1.5 transition-all">
                            <span>Terdaftar di kelas ini</span>
                            <span>&rarr;</span>
                        </div>
                    </div>
                </a>

                <!-- 2. Materi Perkuliahan -->
                <a href="{{ route('dosen.materi') }}" class="bg-white rounded-2xl p-5 border border-sky-100 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition duration-200 flex flex-col justify-between gap-3 group no-underline text-inherit">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Materi Perkuliahan</span>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:bg-amber-500 group-hover:text-white transition duration-200">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <span class="text-3xl font-black text-slate-900 tracking-tight" id="statTotalMaterials">{{ $totalMaterials }}</span>
                        <div class="mt-2 text-xs font-bold text-amber-600 flex items-center gap-1 group-hover:gap-1.5 transition-all">
                            <span>PDF, PPTX &amp; Tautan</span>
                            <span>&rarr;</span>
                        </div>
                    </div>
                </a>

                <!-- 3. Tugas Aktif -->
                <a href="{{ route('dosen.tugas') }}" class="bg-white rounded-2xl p-5 border border-sky-100 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition duration-200 flex flex-col justify-between gap-3 group no-underline text-inherit">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tugas Aktif</span>
                        <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center shrink-0 group-hover:bg-orange-500 group-hover:text-white transition duration-200">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <span class="text-3xl font-black text-slate-900 tracking-tight" id="statActiveAssignments">{{ $totalAssignments }}</span>
                        <div class="mt-2 text-xs font-bold text-orange-600 flex items-center gap-1 group-hover:gap-1.5 transition-all">
                            <span>Dengan batas waktu</span>
                            <span>&rarr;</span>
                        </div>
                    </div>
                </a>

                <!-- 4. Perlu Dinilai -->
                <a href="{{ route('dosen.penilaian') }}" class="bg-white rounded-2xl p-5 border border-sky-100 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition duration-200 flex flex-col justify-between gap-3 group no-underline text-inherit">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Perlu Dinilai</span>
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0 group-hover:bg-red-500 group-hover:text-white transition duration-200">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <span class="text-3xl font-black text-slate-900 tracking-tight" id="statPendingGrading">{{ $pendingGrading }}</span>
                        <div class="mt-2 text-xs font-bold text-red-600 flex items-center gap-1 group-hover:gap-1.5 transition-all">
                            <span>Pengumpulan mahasiswa</span>
                            <span>&rarr;</span>
                        </div>
                    </div>
                </a>
            </section>

            <!-- 4. Informasi Jadwal Kuliah & Pengumuman -->
            <section class="mt-2">
                <div class="bg-white rounded-2xl p-5 border border-sky-100 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-slate-900 leading-tight">Jadwal Perkuliahan Berikutnya</h4>
                            <span class="text-xs font-bold text-slate-500 mt-0.5 inline-block">Senin, 08.00 - 10.30 WIB &bull; Lab Komputer 3 &bull; Pemrograman Web (SI-A)</span>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-600 border border-emerald-200/60 w-fit shrink-0">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Kelas Siap Dimulai
                    </span>
                </div>
            </section>

        </main>
        <x-footer />
    </div>

</body>

</html>
