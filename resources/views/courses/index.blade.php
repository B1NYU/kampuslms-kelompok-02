<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mata Kuliah - Student Management Campus</title>

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

        <!-- 1. Panggil Komponen Layout/Sidebar -->
        <x-layout />

        <!-- 2. Konten Utama Mata Kuliah -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex flex-col gap-6 box-border">

            <!-- Topbar Header -->
            <header class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                <div class="flex items-center gap-4">
                    <div class="flex flex-col gap-1">
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                            <span class="inline-block w-1.5 h-6 sm:h-7 rounded-full bg-gradient-to-b from-sky-500 to-sky-400"></span>
                            Mata Kuliah Saya
                        </h1>
                    </div>
                </div>
            </header>

            <!-- Grid Kartu Mata Kuliah -->
            <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                @php
                    // Dikirim MahasiswaCourseController::index(): hanya MK yang diikuti mahasiswa ini.
                    $dbCourses = $courses ?? collect();
                    $terkumpul = $terkumpul ?? collect();
                    $gradientList = [
                        'from-sky-500 to-sky-400',
                        'from-amber-400 to-orange-500',
                        'from-orange-500 to-sky-500',
                        'from-cyan-500 to-orange-400',
                        'from-sky-500 to-amber-400',
                        'from-orange-500 to-yellow-500',
                    ];
                @endphp

                @forelse ($dbCourses as $idx => $item)
                    @php
                        $gradient = $gradientList[$idx % count($gradientList)];
                        $studentCount = $item->students_count ?? 0;
                        $assignCount = $item->published_assignments_count ?? 0;
                        $doneCount = (int) ($terkumpul[$item->id] ?? 0);
                        $materiCount = $item->materials_count ?? 0;
                    @endphp
                    <a href="{{ route('mahasiswa.mata-kuliah.show', ['mata_kuliah' => $item->id]) }}" class="group relative bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-lg hover:border-sky-300 transition-all duration-300 overflow-hidden flex flex-col">
                        <!-- Card Banner Header -->
                        <div class="h-28 bg-gradient-to-br {{ $gradient }} p-4 flex items-start justify-between relative overflow-hidden">
                            <span class="text-xs font-black px-2.5 py-1 rounded-full bg-white/90 text-slate-800 shadow-sm backdrop-blur-xs">
                                {{ $studentCount }} Mahasiswa
                            </span>
                            <span class="w-8 h-8 rounded-full bg-white/90 text-slate-800 flex items-center justify-center shadow-sm group-hover:translate-x-1 transition-transform">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 18l6-6-6-6" />
                                </svg>
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 flex-1 flex flex-col justify-between gap-3">
                            <div>
                                <span class="inline-block px-2.5 py-0.5 text-xs font-black text-sky-600 bg-sky-50 border border-sky-200 rounded-md mb-2">
                                    {{ $item->code }}
                                </span>
                                <h3 class="text-lg font-black text-slate-900 group-hover:text-sky-600 transition-colors line-clamp-2">
                                    {{ $item->name }}
                                </h3>
                                <p class="text-xs font-semibold text-slate-500 mt-1">
                                    {{ $item->lecturer?->name ?? 'Dosen Pengampu' }} &bull; {{ $item->sks }} SKS
                                </p>
                            </div>

                            <div class="pt-3 border-t border-slate-100 text-xs font-bold text-slate-600 flex items-center gap-2">
                                <span>📝 {{ $doneCount }}/{{ $assignCount }} Tugas Terkumpul</span>
                                <span>&bull;</span>
                                <span>📄 {{ $materiCount }} Materi</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-2xl border border-dashed border-slate-200 p-8">
                        Anda belum terdaftar di mata kuliah mana pun. Hubungi admin atau dosen pengampu untuk didaftarkan.
                    </div>
                @endforelse

                {{-- Navigasi Pagination Laravel --}}
                @if ($courses->hasPages())
                    <div class="col-span-full mt-4">
                        {{ $courses->links() }}
                    </div>
                @endif

            </section>

        </main>
        <x-footer />
    </div>

</body>
</html>
