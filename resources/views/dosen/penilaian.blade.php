{{-- Data ($courses, $allSubmissions, $totalSubmissions, $totalGraded, $totalPending, $persenGraded)
     disiapkan oleh Dosen\GradingController@index --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Penilaian & Feedback — Portal Dosen KampusLMS</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    <!-- Memuat CSS & JS via Vite (Hanya Tailwind app.css murni tanpa file CSS eksternal) -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/dosen/dosen.penilaian.js'])
    @endif
</head>

<body class="bg-[#F1F5F9] min-h-screen text-slate-800 antialiased font-['Nunito',sans-serif] relative overflow-x-hidden">

    <!-- Soft Decorative Atmospheric Glow -->
    <div class="fixed -top-24 -right-24 w-96 h-96 bg-sky-400/20 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed -bottom-24 -left-24 w-96 h-96 bg-amber-400/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed top-1/2 right-1/4 w-72 h-72 bg-orange-400/10 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <div class="min-h-screen flex flex-col justify-between relative z-10">

        <!-- Navbar Khusus Dosen -->
        <x-navbar-dosen />

        <!-- [RESPONSIVE-MOBILE] Konten Utama Penilaian & Feedback (Responsif Mobile & Desktop) -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8 flex flex-col gap-4 sm:gap-6 box-border">

            <!-- Topbar Header -->
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 pb-2 border-b border-slate-200/80">
                <div class="flex items-center gap-3">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <span class="inline-block w-1.5 h-5 sm:h-7 rounded-full bg-gradient-to-b from-sky-500 to-sky-400"></span>
                        Penilaian &amp; Feedback
                    </h1>
                </div>

                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-sky-100 shadow-xs w-full sm:w-auto overflow-hidden">
                    <span class="text-xs font-bold text-slate-500 shrink-0">MK:</span>
                    <select id="selectCurrentCourse" class="text-xs font-extrabold text-sky-700 bg-transparent border-none outline-none cursor-pointer focus:ring-0 truncate w-full sm:w-auto">
                        <option value="all">Semua MK Aktif ({{ $totalSubmissions }} Pengumpulan)</option>
                        @foreach ($courses as $c)
                            @php
                                $cSubmissionsCount = $allSubmissions->filter(fn ($s) => $s->assignment?->course_id === $c->id)->count();
                            @endphp
                            <option value="{{ $c->id }}">
                                {{ $c->code }} &bull; {{ $c->name }} ({{ $cSubmissionsCount }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </header>

            <!-- Section: Penilaian & Feedback -->
            <section class="flex flex-col gap-6" id="penilaian-tugas">
                <div class="bg-white rounded-2xl border border-sky-100 shadow-xs p-5 sm:p-6 flex flex-col gap-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-black text-slate-900 leading-tight">Penilaian &amp; Umpan Balik Pengumpulan Mahasiswa</h2>
                                <p class="text-xs text-slate-500 font-bold">Evaluasi tugas mahasiswa, berikan skor angka, dan cantumkan catatan konstruktif.</p>
                            </div>
                        </div>
                        <span class="text-xs font-extrabold px-3 py-1.5 rounded-full bg-sky-50 text-sky-700 border border-sky-100">
                            Evaluasi &amp; Grading
                        </span>
                    </div>

                    <!-- Tampilan Statistik KPI Tugas: Total Semua Tugas, Sudah Dinilai, Belum Dinilai -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- KPI 1: Total Semua Tugas -->
                        <div class="bg-white rounded-2xl border border-sky-200/80 p-5 shadow-xs flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-[11px] font-black text-slate-500 uppercase tracking-wider block">Total Semua Tugas</span>
                                <div class="flex items-baseline gap-1.5 my-0.5">
                                    <span id="kpiTotalAll" class="text-2xl sm:text-3xl font-black text-slate-900 leading-none">{{ $totalSubmissions }}</span>
                                    <span class="text-xs font-bold text-slate-500">Pengumpulan</span>
                                </div>
                                <span class="text-[11px] text-slate-400 font-semibold block truncate">Semua berkas tugas mahasiswa</span>
                            </div>
                        </div>

                        <!-- KPI 2: Tugas Sudah Dinilai -->
                        <div class="bg-white rounded-2xl border border-emerald-200/80 p-5 shadow-xs flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-[11px] font-black text-emerald-600 uppercase tracking-wider block">Tugas Sudah Dinilai</span>
                                <div class="flex items-baseline gap-2 my-0.5">
                                    <span id="kpiTotalGraded" class="text-2xl sm:text-3xl font-black text-emerald-600 leading-none">{{ $totalGraded }}</span>
                                    <span id="kpiGradedPercent" class="text-[11px] font-black text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-full">{{ $persenGraded }}%</span>
                                </div>
                                <span class="text-[11px] text-slate-400 font-semibold block truncate">Telah dievaluasi &amp; diberi nilai</span>
                            </div>
                        </div>

                        <!-- KPI 3: Tugas Belum Dinilai -->
                        <div class="bg-white rounded-2xl border border-amber-200/80 p-5 shadow-xs flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-[11px] font-black text-amber-600 uppercase tracking-wider block">Tugas Belum Dinilai</span>
                                <div class="flex items-baseline gap-1.5 my-0.5">
                                    <span id="kpiTotalPending" class="text-2xl sm:text-3xl font-black text-amber-600 leading-none">{{ $totalPending }}</span>
                                    <span class="text-xs font-bold text-amber-600">Menunggu</span>
                                </div>
                                <span class="text-[11px] text-slate-400 font-semibold block truncate">Perlu pemeriksaan dosen</span>
                            </div>
                        </div>
                    </div>

                    <!-- Filter & Summary Bar -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <button class="filter-submission-btn px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 [&.active]:bg-sky-50 [&.active]:text-sky-600 [&.active]:border-sky-300 [&.active]:font-black [&.active]:shadow-xs active" data-sub-filter="all">Semua Pengumpulan</button>
                            <button class="filter-submission-btn px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 [&.active]:bg-sky-50 [&.active]:text-sky-600 [&.active]:border-sky-300 [&.active]:font-black [&.active]:shadow-xs" data-sub-filter="pending">Belum Dinilai</button>
                            <button class="filter-submission-btn px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 [&.active]:bg-sky-50 [&.active]:text-sky-600 [&.active]:border-sky-300 [&.active]:font-black [&.active]:shadow-xs" data-sub-filter="graded">Sudah Dinilai</button>
                        </div>

                        <div class="flex items-center gap-3 flex-wrap">
                            <div class="relative">
                                <input type="text" id="filterSearchInput" placeholder="Cari nama / tugas / berkas..."
                                       class="pl-8 pr-3 py-1.5 text-xs font-semibold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400 w-52">
                                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </div>
                            <div class="text-xs font-extrabold text-slate-600">
                                Status: <span class="text-emerald-600" id="gradedSummary">{{ $totalGraded }} Dinilai</span> &middot; <span class="text-amber-600" id="ungradedSummary">{{ $totalPending }} Menunggu Review</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Pengumpulan & Penilaian -->
                    <div class="w-full overflow-x-auto border border-sky-100 rounded-2xl">
                        <table class="w-full text-left text-xs sm:text-sm" id="submissionTable">
                            <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-black uppercase tracking-wider text-slate-500 sticky top-0 z-10">
                                <tr>
                                    <th class="px-5 py-3.5">Mahasiswa</th>
                                    <th class="px-5 py-3.5">Tugas yang Dikumpulkan</th>
                                    <th class="px-5 py-3.5">Waktu Pengumpulan</th>
                                    <th class="px-5 py-3.5">Berkas Tugas</th>
                                    <th class="px-5 py-3.5">Nilai</th>
                                    <th class="px-5 py-3.5">Feedback Dosen</th>
                                    <th class="px-5 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="submissionTableBody" class="divide-y divide-slate-100">
                                @forelse ($allSubmissions as $sub)
                                    @php
                                        $student = $sub->student;
                                        $maxScore = (int) ($sub->assignment?->max_score ?? 100);
                                        $assignment = $sub->assignment;
                                        $course = $assignment?->course;
                                        $grade = $sub->grade;
                                        $isGraded = !is_null($grade);
                                        $scoreText = $isGraded ? rtrim(rtrim(number_format((float) $grade->score, 2, '.', ''), '0'), '.') : '';

                                        $name = $student?->name ?? ('Mahasiswa #' . $sub->user_id);
                                        $words = explode(' ', trim($name));
                                        $initials = strtoupper(substr($words[0] ?? 'M', 0, 1) . substr($words[1] ?? '', 0, 1));
                                        if (strlen($initials) === 1) $initials .= strtoupper(substr($words[0] ?? 'M', 1, 1));

                                        $palette = [
                                            ['bg' => 'rgba(3, 159, 250, 0.12)', 'color' => '#039FFA'],
                                            ['bg' => 'rgba(249, 184, 4, 0.14)', 'color' => '#D97706'],
                                            ['bg' => 'rgba(50, 179, 241, 0.14)', 'color' => '#0284C7'],
                                            ['bg' => 'rgba(16, 185, 129, 0.14)', 'color' => '#10B981'],
                                            ['bg' => 'rgba(249, 99, 5, 0.12)', 'color' => '#F96305'],
                                        ];
                                        $color = $palette[$sub->user_id % 5];

                                        $nim = $student?->nim_nip ?? '-';
                                        $filename = $sub->original_name ?? basename($sub->file_path);
                                        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                        $icon = match($ext) {
                                            'pdf' => '📄',
                                            'zip', 'rar', '7z' => '📦',
                                            'png', 'jpg', 'jpeg', 'webp' => '🖼️',
                                            'sql' => '💾',
                                            'doc', 'docx' => '📝',
                                            default => '📁'
                                        };
                                        $fileSizeKb = $sub->file_size > 0 ? number_format($sub->file_size / 1024, 1) . ' KB' : 'Lampiran';
                                    @endphp
                                    <tr data-status="{{ $isGraded ? 'graded' : 'pending' }}"
                                        data-course-id="{{ $course?->id }}"
                                        data-id="{{ $sub->id }}"
                                        data-search="{{ strtolower($name . ' ' . $nim . ' ' . ($assignment?->title ?? '') . ' ' . $filename . ' ' . ($course?->code ?? '')) }}"
                                        class="hover:bg-sky-50/30 transition">
                                        <td class="px-5 py-3.5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black shrink-0" style="background:{{ $color['bg'] }}; color:{{ $color['color'] }};">
                                                    {{ $initials }}
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="student-name font-bold text-slate-800">{{ $name }}</span>
                                                    <span class="student-nim text-[11px] font-semibold text-slate-500">{{ $nim }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            @can('view', $sub)
                                                <a href="{{ route('submissions.show', $sub) }}" class="font-bold text-slate-800 hover:text-sky-600 hover:underline">
                                                    {{ $assignment?->title ?? 'Tugas Perkuliahan' }}
                                                </a>
                                            @else
                                                <span class="font-bold text-slate-800">{{ $assignment?->title ?? 'Tugas Perkuliahan' }}</span>
                                            @endcan
                                            <div class="text-[11px] text-slate-500 font-semibold mt-0.5">
                                                {{ $course?->code }} &bull; {{ $course?->name }}
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            @if ($sub->is_late)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200">
                                                    Terlambat ({{ $sub->submitted_at ? $sub->submitted_at->format('d M, H:i') : '-' }})
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Tepat Waktu ({{ $sub->submitted_at ? $sub->submitted_at->format('d M, H:i') : '-' }})
                                                </span>
                                            @endif
                                            <div class="text-[11px] text-slate-500 font-semibold mt-1">
                                                {{ $sub->submitted_at ? $sub->submitted_at->translatedFormat('d M Y, H:i') : '-' }} WITA
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            @can('view', $sub)
                                            <div class="inline-flex items-center gap-1.5">
                                                <a href="{{ route('submissions.show', $sub) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold text-sky-600 bg-sky-50 hover:bg-sky-500 hover:text-white transition max-w-[170px] truncate" title="Buka Detail: {{ $filename }} ({{ $fileSizeKb }})">
                                                    <span>{{ $icon }}</span>
                                                    <span class="truncate">{{ $filename }}</span>
                                                </a>
                                                <a href="{{ route('submissions.download', $sub) }}" class="p-1 rounded-md text-slate-500 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition" title="Unduh berkas langsung" download>
                                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                        <polyline points="7 10 12 15 17 10"></polyline>
                                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                                    </svg>
                                                </a>
                                            </div>
                                            @endcan
                                            @if($sub->note)
                                                <div class="text-[10px] text-slate-500 italic mt-1 max-w-[180px] truncate" title="{{ $sub->note }}">
                                                    Catatan: {{ $sub->note }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5">
                                            @if ($isGraded)
                                                <span class="score-badge item-score inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-sky-50 text-sky-700 border border-sky-200">{{ $scoreText }} / {{ $maxScore }}</span>
                                            @else
                                                <span class="score-badge item-score inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-amber-50 text-amber-700 border border-amber-200">Belum Dinilai</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 max-w-[220px]">
                                            @if ($isGraded && !empty($grade->feedback))
                                                <span class="feedback-text item-feedback text-xs text-slate-700 font-semibold line-clamp-2" title="{{ $grade->feedback }}">
                                                    "{{ $grade->feedback }}"
                                                </span>
                                            @else
                                                <span class="feedback-text item-feedback text-xs text-slate-400 italic">
                                                    Belum ada catatan umpan balik.
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1.5 justify-end">
                                                @can('view', $sub)
                                                <a href="{{ route('submissions.show', $sub) }}" class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition" title="Buka detail pengumpulan">
                                                    Detail &rarr;
                                                </a>
                                                @endcan
                                                @can('grade', $sub)
                                                @if ($isGraded)
                                                    <button class="btn-grade-action px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-sky-500 hover:bg-sky-600 shadow-xs transition cursor-pointer"
                                                        data-id="{{ $sub->id }}"
                                                        data-student="{{ $name }} ({{ $nim }})"
                                                        data-title="{{ $assignment?->title ?? 'Tugas' }}"
                                                        data-max="{{ $maxScore }}"
                                                        data-score="{{ $scoreText }}"
                                                        data-feedback="{{ $grade->feedback }}">
                                                        Edit Nilai
                                                    </button>
                                                @else
                                                    <button class="btn-grade-action px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-xs transition cursor-pointer"
                                                        data-id="{{ $sub->id }}"
                                                        data-student="{{ $name }} ({{ $nim }})"
                                                        data-title="{{ $assignment?->title ?? 'Tugas' }}"
                                                        data-max="{{ $maxScore }}"
                                                        data-score=""
                                                        data-feedback="">
                                                        Beri Nilai
                                                    </button>
                                                @endif
                                                @else
                                                    <span class="text-[11px] text-slate-400 font-semibold">Tidak dapat dinilai</span>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-10 px-4 text-slate-400 font-bold">
                                            Belum ada pengumpulan tugas dari mahasiswa di mata kuliah Anda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Navigasi Pagination Laravel --}}
                    @if ($allSubmissions->hasPages())
                        <div class="px-5 py-3 border-t border-slate-100 bg-white flex items-center justify-between">
                            {{ $allSubmissions->links() }}
                        </div>
                    @endif
                </div>
            </section>

        </main>
        <x-footer />
    </div>

    <!-- MODAL PENILAIAN & FEEDBACK -->
    <div class="dosen-modal-overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs items-center justify-center p-4 transition-all duration-200" id="gradingModalOverlay" style="display: none;">
        <div class="w-full max-w-[560px] bg-white rounded-2xl shadow-2xl border border-sky-100 overflow-hidden flex flex-col" id="gradingModal">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-sm font-black text-slate-900">Form Penilaian &amp; Umpan Balik Dosen</h3>
                <button type="button" class="text-slate-400 hover:text-slate-700 text-xl font-black cursor-pointer leading-none" id="btnCloseGradingModal">&times;</button>
            </div>
            <div class="p-6 flex flex-col gap-4 max-h-[70vh] overflow-y-auto">
                <div class="p-3.5 bg-sky-50/60 border border-sky-100 rounded-xl flex flex-col gap-0.5">
                    <span class="text-xs font-black text-sky-900 truncate" id="modalAssignmentTitle">Tugas</span>
                    <span class="text-[11px] font-bold text-sky-700" id="modalStudentName">Mahasiswa: Clara Shinta (10241018)</span>
                </div>

                <div>
                    <label for="inputScore" class="block text-xs font-black text-slate-700 mb-1.5">
                        Nilai Akhir (Skala 0 - <span id="modalMaxLabel">100</span>) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="inputScore" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" placeholder="Masukkan nilai..." min="0" max="100" step="any" required>
                    <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                        <span class="text-[11px] font-bold text-slate-500 mr-1">Pintasan Nilai:</span>
                        <button type="button" class="btn-score-quick px-2 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-700 hover:bg-sky-100 hover:text-sky-700 transition cursor-pointer" data-pct="95">95% (A)</button>
                        <button type="button" class="btn-score-quick px-2 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-700 hover:bg-sky-100 hover:text-sky-700 transition cursor-pointer" data-pct="88">88% (A-)</button>
                        <button type="button" class="btn-score-quick px-2 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-700 hover:bg-sky-100 hover:text-sky-700 transition cursor-pointer" data-pct="82">82% (B+)</button>
                        <button type="button" class="btn-score-quick px-2 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-700 hover:bg-sky-100 hover:text-sky-700 transition cursor-pointer" data-pct="75">75% (B)</button>
                    </div>
                </div>

                <div>
                    <label for="inputFeedback" class="block text-xs font-black text-slate-700 mb-1.5">
                        Catatan &amp; Umpan Balik (Feedback) Konstruktif <span class="text-red-500">*</span>
                    </label>
                    <textarea id="inputFeedback" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" rows="4" placeholder="Tuliskan ulasan spesifik mengenai pengumpulan ini, kelebihan kode, serta aspek yang perlu ditingkatkan oleh mahasiswa..."></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition cursor-pointer" id="btnCancelGrading">Batal</button>
                <button type="button" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-xs shadow-sky-500/20 transition cursor-pointer" id="btnSaveGrading">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Simpan Nilai &amp; Berikan Feedback
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div class="fixed bottom-5 right-5 z-[99999] flex flex-col gap-2.5 max-w-sm pointer-events-none" id="toastContainer"></div>

    <div id="dosenPenilaianConfig" style="display:none;" data-grade-url-template="{{ route('dosen.penilaian.update', ['submission' => '__ID__']) }}"></div>
</body>

</html>
