{{-- Data ($rekap, $overallAverage, $totalGraded) disiapkan oleh Mahasiswa\RekapNilaiController@index --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai Saya — Portal KampusLMS</title>

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

    @php
        $fmt = fn ($n) => rtrim(rtrim(number_format((float) $n, 2, '.', ''), '0'), '.');
        $totalTugas = $rekap->sum('total');
    @endphp

    <div class="min-h-screen flex flex-col justify-between relative z-10">

        <!-- 1. Navbar Utama Mahasiswa -->
        <x-layout />

        <!-- [RESPONSIVE-MOBILE] Konten Utama Rekap Nilai Mahasiswa (Responsif Mobile & Desktop) -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8 flex flex-col gap-4 sm:gap-6 box-border">

            <!-- Topbar Header -->
            <header class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col gap-1">
                        <span class="inline-flex items-center gap-1.5 w-fit text-[10px] sm:text-[11px] font-black tracking-wider uppercase px-2.5 py-0.5 rounded-full border border-sky-400/30 bg-white text-sky-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                            PORTAL MAHASISWA &bull; REKAP NILAI
                        </span>
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <span class="inline-block w-1.5 h-5 sm:h-7 rounded-full bg-gradient-to-b from-sky-500 to-sky-400"></span>
                            Nilai Saya
                        </h1>
                    </div>
                </div>
            </header>

            <!-- Stat Cards KPI Ringkasan Nilai -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md hover:border-sky-300 transition-all flex flex-col justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-500">Mata Kuliah</span>
                    <span class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">{{ $rekap->count() }}</span>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md hover:border-sky-300 transition-all flex flex-col justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-500">Tugas Dinilai</span>
                    <span class="text-2xl sm:text-3xl font-black text-sky-600 mt-2">
                        {{ $totalGraded }} <span class="text-sm font-bold text-slate-400">/ {{ $totalTugas }}</span>
                    </span>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md hover:border-sky-300 transition-all flex flex-col justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-500">Rata-rata Keseluruhan</span>
                    <span class="text-2xl sm:text-3xl font-black text-emerald-600 mt-2">
                        {{ $overallAverage !== null ? $fmt($overallAverage) . '%' : '-' }}
                    </span>
                </div>
            </div>

            <!-- Card Utama Daftar Nilai Per Mata Kuliah -->
            <section class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 sm:p-6 flex flex-col gap-6">

                @forelse ($rekap as $r)
                    <div class="pb-6 border-b border-slate-100 last:border-b-0 last:pb-0 flex flex-col gap-3">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-2 border-b border-slate-100">
                            <div>
                                <h3 class="text-base sm:text-lg font-black text-slate-900 flex items-center gap-2">
                                    <span class="px-2 py-0.5 text-xs font-black rounded-md bg-sky-100 text-sky-700 border border-sky-200">{{ $r['course']->code }}</span>
                                    <span>{{ $r['course']->name }}</span>
                                </h3>
                                <small class="text-xs font-semibold text-slate-500 mt-0.5 block">{{ $r['graded'] }} dari {{ $r['total'] }} tugas sudah dinilai</small>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-sky-50 text-sky-700 border border-sky-200 w-fit">
                                Rata-rata: {{ $r['average'] !== null ? $fmt($r['average']) . '%' : '-' }}
                            </span>
                        </div>

                        @if ($r['total'] === 0)
                            <p class="text-xs sm:text-sm font-bold text-slate-400 py-3">Belum ada tugas yang dipublikasikan.</p>
                        @else
                            <div class="overflow-x-auto rounded-xl border border-slate-200/80">
                                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                                    <thead>
                                        <tr class="bg-slate-50/80 text-slate-600 uppercase font-black tracking-wider text-[11px] border-b border-slate-200">
                                            <th class="p-3.5">Tugas</th>
                                            <th class="p-3.5">Batas Waktu</th>
                                            <th class="p-3.5">Status</th>
                                            <th class="p-3.5 text-right">Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($r['items'] as $it)
                                            <tr class="border-b border-slate-100 last:border-b-0 hover:bg-slate-50/60 transition-colors">
                                                <td class="p-3.5">
                                                    <strong class="font-extrabold text-slate-900">{{ $it['assignment']->title }}</strong>
                                                </td>
                                                <td class="p-3.5 text-xs text-slate-500 font-semibold whitespace-nowrap">
                                                    {{ $it['assignment']->due_at->translatedFormat('d M Y, H:i') }} WITA
                                                </td>
                                                <td class="p-3.5">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        @if ($it['state'] === 'graded')
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800">
                                                                Dinilai
                                                            </span>
                                                        @elseif ($it['state'] === 'waiting')
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black bg-amber-100 text-amber-800">
                                                                Menunggu penilaian
                                                            </span>
                                                        @elseif ($it['state'] === 'missed')
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black bg-rose-100 text-rose-800">
                                                                Tidak dikumpulkan
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black bg-slate-100 text-slate-600">
                                                                Belum dikumpulkan
                                                            </span>
                                                        @endif

<<<<<<< HEAD
                                                        @if ($it['late'])
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300">
                                                                Terlambat
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="p-3.5 text-right font-black">
                                                    @if ($it['score'] !== null)
                                                        <a href="{{ route('submissions.show', $it['submission_id']) }}" class="text-sky-600 hover:text-sky-800 hover:underline">
                                                            {{ $fmt($it['score']) }} <span class="text-xs text-slate-400 font-bold">/ {{ $it['assignment']->max_score }}</span>
                                                        </a>
                                                    @elseif ($it['submission_id'])
                                                        <a href="{{ route('submissions.show', $it['submission_id']) }}" class="text-xs font-bold text-slate-500 hover:text-sky-600 hover:underline">
                                                            Lihat pengumpulan &rarr;
                                                        </a>
                                                    @else
                                                        <span class="text-slate-300">&mdash;</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-12 text-center text-xs sm:text-sm font-semibold text-slate-400">
                        Anda belum terdaftar di mata kuliah aktif mana pun.
                    </div>
                @endforelse

                {{-- Navigasi Pagination Laravel --}}
                @if ($rekap->hasPages())
                    <div class="mt-4">
                        {{ $rekap->links() }}
                    </div>
                @endif

                <p class="text-xs font-semibold text-slate-500 mt-2">
                    Rata-rata dihitung dari persentase (nilai &divide; nilai maksimal tugas) pada tugas yang sudah dinilai. Klik nilai untuk melihat umpan balik dosen.
                </p>
            </section>

        </main>

        <!-- 3. Footer Global -->
        <x-footer />
    </div>

</body>

</html>
