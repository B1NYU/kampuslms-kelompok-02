{{-- Data ($course, $courses, $assignments, $rows, $stats) disiapkan oleh Dosen\RekapNilaiController@show --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai — Portal Dosen KampusLMS</title>

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
        // 85 → "85", 87.5 → "87.5"
        $fmt = fn ($n) => rtrim(rtrim(number_format((float) $n, 2, '.', ''), '0'), '.');
    @endphp

    <div class="min-h-screen flex flex-col justify-between relative z-10">

        <!-- Navbar Khusus Dosen -->
        <x-navbar-dosen />

        <!-- Konten Utama Rekap Nilai -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex flex-col gap-6 box-border">

            <!-- Topbar Header -->
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span class="inline-block w-1.5 h-6 sm:h-7 rounded-full bg-gradient-to-b from-sky-500 to-sky-400"></span>
                        Rekapitulasi Nilai
                    </h1>
                </div>

                <div class="flex items-center gap-2.5 bg-white px-3.5 py-2 rounded-xl border border-sky-100 shadow-xs self-start sm:self-auto">
                    <span class="text-xs font-bold text-slate-500 shrink-0">Mata Kuliah:</span>
                    <select id="selectCurrentCourse"
                            class="text-xs font-extrabold text-sky-700 bg-transparent border-none outline-none cursor-pointer focus:ring-0"
                            onchange="if (this.value) window.location.href = this.value">
                        @foreach ($courses as $c)
                            <option value="{{ route('dosen.rekap.show', $c) }}" @selected($c->id === $course->id)>
                                {{ $c->code }} &bull; {{ $c->name }} ({{ $c->sks }} SKS)
                            </option>
                        @endforeach
                    </select>
                </div>
            </header>

            <section class="flex flex-col gap-6">
                <div class="bg-white rounded-2xl border border-sky-100 shadow-xs p-5 sm:p-6 flex flex-col gap-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="8" r="7"></circle>
                                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-black text-slate-900 leading-tight">Rekap Nilai &mdash; {{ $course->code }} &bull; {{ $course->name }}</h2>
                                <p class="text-xs text-slate-500 font-bold">Nilai seluruh mahasiswa terdaftar pada tiap tugas yang sudah dipublikasikan.</p>
                            </div>
                        </div>
                        <a href="{{ route('dosen.penilaian') }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-sky-600 bg-sky-50 hover:bg-sky-500 hover:text-white transition w-fit">
                            <span>Ke Penilaian</span>
                            <span>&rarr;</span>
                        </a>
                    </div>

                    <!-- 4 Kartu Ringkasan KPI Rekap -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-white border border-sky-100 rounded-2xl p-4 shadow-xs flex flex-col justify-between gap-1">
                            <span class="text-[11px] font-black text-slate-500 uppercase tracking-wider">Mahasiswa</span>
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 leading-none">{{ $stats['students'] }}</span>
                        </div>
                        <div class="bg-white border border-sky-100 rounded-2xl p-4 shadow-xs flex flex-col justify-between gap-1">
                            <span class="text-[11px] font-black text-slate-500 uppercase tracking-wider">Tugas Published</span>
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 leading-none">{{ $stats['assignments'] }}</span>
                        </div>
                        <div class="bg-white border border-sky-100 rounded-2xl p-4 shadow-xs flex flex-col justify-between gap-1">
                            <span class="text-[11px] font-black text-slate-500 uppercase tracking-wider">Dinilai / Menunggu</span>
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 leading-none">{{ $stats['graded'] }} / {{ $stats['waiting'] }}</span>
                        </div>
                        <div class="bg-white border border-sky-100 rounded-2xl p-4 shadow-xs flex flex-col justify-between gap-1">
                            <span class="text-[11px] font-black text-slate-500 uppercase tracking-wider">Rata-rata Kelas</span>
                            <span class="text-2xl sm:text-3xl font-black text-sky-600 leading-none">{{ $stats['average'] !== null ? $fmt($stats['average']) . '%' : '-' }}</span>
                        </div>
                    </div>

                    @if ($rows->isEmpty() || $assignments->isEmpty())
                        <div class="text-center py-10 px-4 text-slate-400 font-bold text-xs sm:text-sm bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            @if ($rows->isEmpty())
                                Belum ada mahasiswa terdaftar di mata kuliah ini.
                            @else
                                Belum ada tugas yang dipublikasikan di mata kuliah ini.
                            @endif
                        </div>
                    @else
                        <!-- Tabel Matriks Nilai (Sticky Header & First Column) -->
                        <div class="w-full overflow-x-auto overflow-y-auto max-h-[620px] border border-sky-100 rounded-2xl shadow-xs">
                            <table class="w-full text-left text-xs sm:text-sm border-separate border-spacing-0" id="rekapTable">
                                <thead class="text-[11px] font-black uppercase tracking-wider text-slate-600">
                                    <tr>
                                        <th class="sticky top-0 left-0 z-30 bg-slate-50 min-w-[210px] px-4 py-3 shadow-[1px_0_0_#E2E8F0] border-b border-slate-200/80">
                                            Mahasiswa
                                        </th>
                                        @foreach ($assignments as $a)
                                            <th class="sticky top-0 z-20 bg-slate-50 min-w-[120px] px-3 py-3 text-center align-bottom border-b border-slate-200/80" title="{{ $a->title }}">
                                                <span class="block truncate max-w-[140px] mx-auto">{{ \Illuminate\Support\Str::limit($a->title, 22) }}</span>
                                                <span class="block text-[10px] font-bold text-slate-400 lowercase tracking-normal mt-0.5">maks {{ $a->max_score }}</span>
                                            </th>
                                        @endforeach
                                        <th class="sticky top-0 z-20 bg-slate-50 text-center min-w-[120px] px-3 py-3 border-b border-slate-200/80">
                                            Tugas Terkumpul
                                        </th>
                                        <th class="sticky top-0 z-20 bg-slate-50 text-center min-w-[110px] px-3 py-3 border-b border-slate-200/80">
                                            Rata-rata
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="rekapDosenTbody">
                                    @foreach ($rows as $row)
                                        <tr class="hover:bg-sky-50/20 transition">
                                            <td class="sticky left-0 z-10 bg-white min-w-[210px] px-4 py-3 shadow-[1px_0_0_#E2E8F0] border-b border-slate-100">
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-slate-800">{{ $row['student']->name }}</span>
                                                    <span class="text-[11px] font-semibold text-slate-500">{{ $row['student']->nim_nip ?? '-' }}</span>
                                                </div>
                                            </td>
                                            @foreach ($row['cells'] as $cell)
                                                <td class="px-3 py-3 text-center font-extrabold text-xs whitespace-nowrap border-b border-slate-100">
                                                    @if ($cell['state'] === 'graded')
                                                        <span class="inline-flex items-center gap-1">
                                                            <a href="{{ route('submissions.show', $cell['submission_id']) }}" class="text-sky-700 hover:text-sky-900 hover:underline">
                                                                {{ $fmt($cell['score']) }}
                                                            </a>
                                                            @if ($cell['late'])
                                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-black bg-amber-100 text-amber-800" title="Terlambat">T</span>
                                                            @endif
                                                        </span>
                                                    @elseif ($cell['state'] === 'waiting')
                                                        <span class="inline-flex items-center gap-1">
                                                            <a href="{{ route('submissions.show', $cell['submission_id']) }}" class="text-amber-600 hover:text-amber-800 hover:underline text-[11px]">
                                                                Menunggu
                                                            </a>
                                                            @if ($cell['late'])
                                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-black bg-amber-100 text-amber-800" title="Terlambat">T</span>
                                                            @endif
                                                        </span>
                                                    @else
                                                        <span class="text-slate-300 select-none" title="Belum mengumpulkan">&mdash;</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                            @php
                                                $submittedCount = $row['submitted'] ?? collect($row['cells'])->filter(fn($c) => in_array($c['state'], ['graded', 'waiting']))->count();
                                            @endphp
                                            <td class="px-3 py-3 text-center text-xs font-black text-slate-800 border-b border-slate-100 whitespace-nowrap">
                                                {{ $submittedCount }}/{{ $assignments->count() }}
                                            </td>
                                            <td class="px-3 py-3 text-center font-black text-slate-900 bg-slate-50/50 border-b border-slate-100 whitespace-nowrap">
                                                {{ $row['average'] !== null ? $fmt($row['average']) . '%' : '-' }}
                                                <div class="text-[10px] font-bold text-slate-500">{{ $row['graded'] }}/{{ $assignments->count() }} dinilai</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Navigasi Pagination Laravel --}}
                        @if ($rows->hasPages())
                            <div class="px-5 py-3 border-t border-slate-100 bg-white flex items-center justify-between">
                                {{ $rows->links() }}
                            </div>
                        @endif

                        <p class="text-xs font-semibold text-slate-500 mt-1 leading-relaxed">
                            Angka = nilai &bull; <strong class="text-slate-700">Menunggu</strong> = sudah dikumpulkan, belum dinilai &bull; &mdash; = belum mengumpulkan &bull;
                            <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-black bg-amber-100 text-amber-800">T</span> = terlambat &bull; Rata-rata dihitung dari persentase (nilai &divide; nilai maksimal tugas) pada tugas yang sudah dinilai.
                        </p>
                    @endif
                </div>
            </section>

        </main>

        <x-footer />
    </div>

</body>

</html>
