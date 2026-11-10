<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student Management Campus</title>

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

    <!-- Main Window Canvas -->
    <div class="min-h-screen flex flex-col justify-between relative z-10">

        <!-- 1. Panggil Komponen Navbar/Sidebar Mahasiswa -->
        <x-layout />

        <!-- [RESPONSIVE-MOBILE] Konten Utama Dashboard Mahasiswa (Responsif Mobile & Desktop) -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8 flex flex-col gap-4 sm:gap-6 box-border">

            <!-- Topbar Header -->
            <header class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-1">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <span class="inline-block w-1.5 h-5 sm:h-7 rounded-full bg-gradient-to-b from-sky-500 to-sky-400"></span>
                            Selamat Datang, {{ auth()->user()?->name ?? 'Mahasiswa' }}
                        </h1>
                    </div>
                </div>
            </header>

            @php
                $mhsUser = auth()->user();

                // Mata kuliah dari database khusus mahasiswa yang sedang login
                $dbCourses = $mhsUser ? $mhsUser->courses()->with('lecturer')->get() : collect();
                $totalCourseCount = $dbCourses->count();
                $totalSks = $dbCourses->sum('sks');

                // Tugas published dari mata kuliah yang diikuti, urut tenggat.
                $dbAssignments = $dbCourses->isNotEmpty()
                    ? \App\Models\Assignment::whereIn('course_id', $dbCourses->pluck('id'))
                        ->where('status', 'published')
                        ->with('course')
                        ->orderBy('due_at')
                        ->get()
                    : collect();

                // Tugas yang sudah dikumpulkan mahasiswa ini (hanya dari tugas di atas).
                $idTerkumpul = $mhsUser && $dbAssignments->isNotEmpty()
                    ? \App\Models\Submission::where('user_id', $mhsUser->id)
                        ->whereIn('assignment_id', $dbAssignments->pluck('id'))
                        ->pluck('assignment_id')
                    : collect();

                $belumDikumpulkan = $dbAssignments->reject(fn ($a) => $idTerkumpul->contains($a->id));
                $tugasPending = $belumDikumpulkan->where('due_at', '>=', now())->count();
                $tugasSelesai = $idTerkumpul->count();
                $persenTugasSelesai = $dbAssignments->count() > 0
                    ? (int) round($tugasSelesai / $dbAssignments->count() * 100)
                    : 0;

                // Daftar "Tugas Terkini": yang belum dikumpulkan dan belum lewat tenggat tampil duluan.
                $tugasTerkini = $belumDikumpulkan->sortBy(fn ($a) => [$a->due_at->isPast() ? 1 : 0, $a->due_at->timestamp])->values();
            @endphp

            <!-- Row 1: 3 Stat Cards terhubung ke Database -->
            <section class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="relative bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-sky-300 transition-all duration-200 flex flex-col items-center text-center gap-1.5 overflow-hidden group">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-sky-400 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <span class="text-xs font-black tracking-wider uppercase text-slate-500">Mata Kuliah Aktif</span>
                    <span class="text-3xl font-black text-sky-500 tracking-tight">{{ $totalCourseCount }} MK</span>
                </div>
                <div class="relative bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-orange-300 transition-all duration-200 flex flex-col items-center text-center gap-1.5 overflow-hidden group">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-orange-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <span class="text-xs font-black tracking-wider uppercase text-slate-500">Total Beban SKS</span>
                    <span class="text-3xl font-black text-orange-500 tracking-tight">{{ $totalSks }} SKS</span>
                </div>
                <div class="relative bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-amber-300 transition-all duration-200 flex flex-col items-center text-center gap-1.5 overflow-hidden group">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-amber-400 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <span class="text-xs font-black tracking-wider uppercase text-slate-500">Tugas Belum Dikumpulkan</span>
                    <span class="text-3xl font-black text-amber-500 tracking-tight">{{ $tugasPending }}</span>
                </div>
            </section>

            <!-- Row 2: Middle Section (List Mata Kuliah Diambil & Aktivitas Mingguan) -->
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Kiri: List Mata Kuliah yang Diambil dari Database -->
                <div class="lg:col-span-2 bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                        <div>
                            <h3 class="text-base sm:text-lg font-black text-sky-600">Mata Kuliah yang Diambil</h3>
                            <small class="text-xs font-semibold text-slate-500">Data langsung dari database LMS</small>
                        </div>
                        <a href="{{ route('mahasiswa.mata-kuliah.index') }}" class="text-xs font-extrabold text-sky-600 bg-[#FFFDF8] hover:bg-sky-50 px-3 py-1.5 rounded-full border border-sky-400/30 transition-colors">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    <div class="flex flex-col gap-2.5 max-h-72 overflow-y-auto pr-1">
                        @forelse ($dbCourses as $mk)
                            <a href="{{ route('mahasiswa.mata-kuliah.show', $mk->id) }}" class="flex items-center gap-3.5 p-3 rounded-xl bg-[#FFFDF8] hover:bg-white border border-sky-400/20 border-l-[3px] border-l-sky-500 hover:border-l-orange-500 hover:border-sky-400/40 hover:translate-x-0.5 transition-all duration-200 group">
                                <span class="shrink-0 text-xs font-extrabold text-sky-600 bg-white border border-sky-400/30 px-2 py-1 rounded-lg w-16 text-center">
                                    {{ $mk->code }}
                                </span>
                                <div class="flex-1 min-w-0 flex flex-col gap-0.5">
                                    <span class="text-sm font-extrabold text-slate-900 truncate group-hover:text-sky-600 transition-colors">
                                        {{ $mk->name }}
                                    </span>
                                    <span class="text-xs font-semibold text-slate-500 truncate">
                                        {{ $mk->lecturer?->name ?? 'Dosen Pengampu' }}
                                    </span>
                                </div>
                                <span class="shrink-0 text-xs font-extrabold text-slate-700">
                                    {{ $mk->sks }} SKS
                                </span>
                                <span class="shrink-0 text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full text-sky-600 bg-sky-500/10 border border-sky-400/30">
                                    {{ ucfirst($mk->status ?? 'Aktif') }}
                                </span>
                            </a>
                        @empty
                            <div class="py-8 text-center text-sm font-semibold text-slate-400">
                                Belum ada mata kuliah yang diambil.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Kanan: Aktivitas Mingguan -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                        <h3 class="text-base sm:text-lg font-black text-sky-600">Aktivitas Mingguan</h3>
                        <span class="text-xs font-extrabold text-sky-600 bg-[#FFFDF8] px-2.5 py-1 rounded-full border border-sky-400/30">+18%</span>
                    </div>
                    <div class="flex items-end justify-between gap-1.5 h-36 pt-2 px-1">
                        <div class="flex-1 rounded-t-md bg-gradient-to-b from-sky-400 to-orange-500 hover:brightness-110 transition-all duration-200" style="height: 35%;"></div>
                        <div class="flex-1 rounded-t-md bg-gradient-to-b from-sky-400 to-orange-500 hover:brightness-110 transition-all duration-200" style="height: 60%;"></div>
                        <div class="flex-1 rounded-t-md bg-gradient-to-b from-sky-400 to-orange-500 hover:brightness-110 transition-all duration-200" style="height: 45%;"></div>
                        <div class="flex-1 rounded-t-md bg-gradient-to-b from-sky-400 to-orange-500 hover:brightness-110 transition-all duration-200" style="height: 80%;"></div>
                        <div class="flex-1 rounded-t-md bg-gradient-to-b from-sky-400 to-orange-500 hover:brightness-110 transition-all duration-200" style="height: 55%;"></div>
                        <div class="flex-1 rounded-t-md bg-gradient-to-b from-sky-400 to-orange-500 hover:brightness-110 transition-all duration-200" style="height: 90%;"></div>
                        <div class="flex-1 rounded-t-md bg-gradient-to-b from-sky-400 to-orange-500 hover:brightness-110 transition-all duration-200" style="height: 65%;"></div>
                        <div class="flex-1 rounded-t-md bg-gradient-to-b from-sky-400 to-orange-500 hover:brightness-110 transition-all duration-200" style="height: 40%;"></div>
                        <div class="flex-1 rounded-t-md bg-gradient-to-b from-sky-400 to-orange-500 hover:brightness-110 transition-all duration-200" style="height: 75%;"></div>
                        <div class="flex-1 rounded-t-md bg-gradient-to-b from-sky-400 to-orange-500 hover:brightness-110 transition-all duration-200" style="height: 95%;"></div>
                        <div class="flex-1 rounded-t-md bg-gradient-to-b from-sky-400 to-orange-500 hover:brightness-110 transition-all duration-200" style="height: 50%;"></div>
                        <div class="flex-1 rounded-t-md bg-gradient-to-b from-sky-400 to-orange-500 hover:brightness-110 transition-all duration-200" style="height: 70%;"></div>
                    </div>
                </div>

            </section>

            <!-- Row 3: Bottom Section (Progres Bulat & Pengumuman) -->
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Kiri: Donut Progress Gauges -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                        <h3 class="text-base sm:text-lg font-black text-sky-600">Progres Akademik Mahasiswa</h3>
                    </div>
                    <div class="flex items-center justify-around py-3">

                        <div class="flex flex-col items-center gap-2">
                            <div class="relative w-20 h-20 flex items-center justify-center">
                                <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="rgba(3, 159, 250, 0.12)" stroke-width="3.2" />
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="#039FFA" stroke-width="3.2" stroke-dasharray="85, 100" stroke-linecap="round" />
                                </svg>
                                <span class="absolute text-sm font-black text-sky-600">85%</span>
                            </div>
                            <span class="text-xs font-bold text-slate-500">Presensi</span>
                        </div>

                        <div class="flex flex-col items-center gap-2">
                            <div class="relative w-20 h-20 flex items-center justify-center">
                                <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="rgba(3, 159, 250, 0.12)" stroke-width="3.2" />
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="#F96305" stroke-width="3.2" stroke-dasharray="{{ $persenTugasSelesai }}, 100" stroke-linecap="round" />
                                </svg>
                                <span class="absolute text-sm font-black text-orange-500">{{ $persenTugasSelesai }}%</span>
                            </div>
                            <span class="text-xs font-bold text-slate-500">Tugas Selesai</span>
                        </div>

                        <div class="flex flex-col items-center gap-2">
                            <div class="relative w-20 h-20 flex items-center justify-center">
                                <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="rgba(3, 159, 250, 0.12)" stroke-width="3.2" />
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="#F9B804" stroke-width="3.2" stroke-dasharray="92, 100" stroke-linecap="round" />
                                </svg>
                                <span class="absolute text-sm font-black text-amber-500">92%</span>
                            </div>
                            <span class="text-xs font-bold text-slate-500">Target SKS</span>
                        </div>

                    </div>
                </div>

                <!-- Kanan: Tugas & Pengumuman Terkini -->
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                        <h3 class="text-base sm:text-lg font-black text-sky-600">Tugas &amp; Pengumuman Terkini</h3>
                        <a href="{{ route('mahasiswa.mata-kuliah.index') }}" class="text-xs font-extrabold text-orange-500 hover:text-sky-600 transition-colors">
                            Semua MK
                        </a>
                    </div>
                    <div class="flex flex-col gap-3">
                        @forelse ($tugasTerkini->take(3) as $assign)
                            @php
                                $isDuePast = $assign->due_at->isPast();
                            @endphp
                            <a href="{{ route('assignments.show', $assign) }}" class="flex flex-col sm:flex-row sm:items-center gap-2.5 sm:gap-3.5 pb-3 border-b border-dashed border-sky-400/20 last:border-b-0 last:pb-0 hover:bg-slate-50/60 p-2 rounded-xl transition-colors group">
                                <span class="shrink-0 text-xs font-black text-sky-600 min-w-20">
                                    {{ $assign->course?->code ?? 'MK' }}
                                </span>
                                <span class="shrink-0 text-[10px] font-extrabold px-2 py-0.5 rounded-md border w-fit {{ $isDuePast ? 'bg-[#FFFDF8] text-orange-500 border-orange-500/40' : 'bg-[#FFFDF8] text-sky-600 border-sky-400/40' }}">
                                    {{ $isDuePast ? 'Lewat Deadline' : 'Belum Dikumpulkan' }}
                                </span>
                                <span class="text-xs font-medium text-slate-600 flex-1 leading-relaxed">
                                    <strong class="font-extrabold text-slate-800 group-hover:text-sky-600 transition-colors">{{ $assign->title }}</strong> &bull; Batas: {{ $assign->due_at->translatedFormat('d M Y, H:i') }}
                                </span>
                            </a>
                        @empty
                            <div class="flex items-start gap-3 p-2">
                                <span class="text-xs font-black text-sky-600 min-w-20">Sistem</span>
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-sky-50 text-sky-600 border border-sky-200">Info</span>
                                <span class="text-xs text-slate-600 font-medium">Tidak ada tugas yang menunggu. Semua tugas sudah dikumpulkan.</span>
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
