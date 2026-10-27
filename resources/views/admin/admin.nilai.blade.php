<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rekapitulasi Nilai Mahasiswa — KampusLMS Admin</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/admin.nilai.js'])
    @endif

    <style>
        #modalGradeDetail { display: none; }
        #modalGradeDetail.active { display: flex; }
        #toastNilai {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #0f172a;
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2);
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 9999;
            pointer-events: none;
        }
        #toastNilai.show {
            transform: translateY(0);
            opacity: 1;
        }
        .grade-pill {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 800;
        }
        .grade-pill-a { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }
        .grade-pill-b { background: #EFF6FF; color: #2563EB; border: 1px solid #BFDBFE; }
        .grade-pill-c { background: #FFFBEB; color: #D97706; border: 1px solid #FDE68A; }
        .grade-pill-d, .grade-pill-e, .grade-pill-remedial { background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; }
        .grade-pill-pending { background: #F1F5F9; color: #64748B; border: 1px solid #CBD5E1; }
    </style>
</head>
<body class="bg-[#F1F5F9] min-h-screen text-slate-800 antialiased font-['Nunito',sans-serif] relative overflow-x-hidden">

    <!-- Soft Decorative Atmospheric Glow -->
    <div class="fixed top-[-80px] right-[-80px] w-96 h-96 bg-sky-400/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed bottom-[-80px] left-[-80px] w-96 h-96 bg-amber-400/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed top-1/2 right-1/4 w-72 h-72 bg-orange-400/10 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <div class="min-h-screen flex flex-col justify-between relative z-10">

        <!-- Navbar Khusus Admin -->
        <x-navbar-admin />

        <!-- Konten Utama Rekap Nilai Admin -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex flex-col gap-6 box-border">

            <!-- Topbar Header -->
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Rekapitulasi Nilai Mahasiswa</h1>
                </div>
                <div>
                    <button type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/80 shadow-xs hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 transition-all cursor-pointer" id="btnExportCsv" title="Unduh data dalam format CSV untuk diolah di Excel">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Ekspor Nilai (CSV)</span>
                    </button>
                </div>
            </header>

            @php
                // Data disediakan oleh AdminGradeController
                $gradeItems    = $gradeItems ?? collect();
                $totalCount    = $totalCount ?? $gradeItems->count();
                $gradedCount   = $gradedCount ?? $gradeItems->where('status', 'graded')->count();
                $pendingCount  = $pendingCount ?? $gradeItems->where('status', 'pending')->count();
                $avgScore      = $avgScore ?? 0;
                $pctGraded     = $pctGraded ?? 0;
                $countA        = $countA ?? 0;
                $countB        = $countB ?? 0;
                $uniqueCourses = $uniqueCourses ?? $gradeItems->pluck('course_name', 'course_code')->unique();
            @endphp

            <!-- Baris Statistik Cepat (4 Stat Asli) -->
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Stat 1: Rata-Rata Nilai -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-sky-500/10 text-sky-500 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20V10"></path>
                            <path d="M18 20V4"></path>
                            <path d="M6 20v-4"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Rata-rata Nilai</span>
                        <span class="text-2xl font-black text-slate-800">{{ $avgScore }}</span>
                    </div>
                </div>

                <!-- Stat 2: Progres Penilaian -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Ternilai</span>
                        <span class="text-2xl font-black text-slate-800">{{ $pctGraded }}% <small class="text-xs font-bold text-slate-400 font-sans">({{ $gradedCount }}/{{ $totalCount }})</small></span>
                    </div>
                </div>

                <!-- Stat 3: Menunggu Penilaian -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-orange-500/10 text-orange-500 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Menunggu Dinilai</span>
                        <span class="text-2xl font-black text-slate-800">{{ $pendingCount }}</span>
                    </div>
                </div>

                <!-- Stat 4: Predikat Tertinggi -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Predikat A &bull; B</span>
                        <span class="text-2xl font-black text-slate-800">{{ $countA }} <small class="text-xs font-bold text-slate-400 font-sans">&bull; {{ $countB }}</small></span>
                    </div>
                </div>
            </section>

            <!-- Toolbar Filter & Pencarian -->
            <section class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                <!-- Input Pencarian (Di Kiri) -->
                <div class="relative flex-1 max-w-md">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>
                    <input type="text" id="searchNilaiInput"
                           placeholder="Cari nama mahasiswa, NIM, atau tugas..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-all placeholder:text-slate-400">
                </div>

                <!-- Kelompok Filter Dropdowns (Di Kanan) -->
                <div class="flex flex-wrap sm:flex-nowrap items-center gap-3">
                    <!-- Dropdown Mata Kuliah -->
                    <div class="relative min-w-[180px] sm:min-w-[220px]">
                        <select id="filterCourseSelect"
                                class="w-full appearance-none pl-3.5 pr-8 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs sm:text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500/30 transition-all cursor-pointer">
                            <option value="all">Semua Mata Kuliah ({{ $uniqueCourses->count() }})</option>
                            @foreach ($uniqueCourses as $code => $name)
                                <option value="{{ $code }}">{{ $code }} &bull; {{ $name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </div>

                    <!-- Dropdown Status -->
                    <div class="relative min-w-[130px]">
                        <select id="filterStatusSelect"
                                class="w-full appearance-none pl-3.5 pr-8 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs sm:text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500/30 transition-all cursor-pointer">
                            <option value="all">Semua Status</option>
                            <option value="graded">Sudah Dinilai</option>
                            <option value="pending">Menunggu Dinilai</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </div>

                    <!-- Dropdown Predikat -->
                    <div class="relative min-w-[140px]">
                        <select id="filterGradeSelect"
                                class="w-full appearance-none pl-3.5 pr-8 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs sm:text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500/30 transition-all cursor-pointer">
                            <option value="all">Semua Predikat</option>
                            <option value="A">Predikat A (&ge;80)</option>
                            <option value="B">Predikat B (65 - 79)</option>
                            <option value="C">Predikat C (55 - 64)</option>
                            <option value="remedial">Remedial (&lt;55)</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section Utama: Card Tabel Rekap Nilai -->
            <section class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <!-- Header Card Tabel -->
                <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 bg-slate-50/50">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">Daftar Evaluasi &amp; Penilaian</h2>
                        <p class="text-xs text-slate-500 font-semibold">Klik baris tabel untuk melihat detail lembar penilaian dan catatan umpan balik dosen</p>
                    </div>
                    <div>
                        <span id="badgeTotalCount" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200/80">
                            Menampilkan {{ $totalCount }} Pengumpulan
                        </span>
                    </div>
                </div>

                <!-- Tabel Scrollable -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[850px]" id="nilaiTable">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-black uppercase tracking-wider text-slate-500">
                                <th class="py-3 px-4 sm:px-5">Mahasiswa</th>
                                <th class="py-3 px-4">NIM</th>
                                <th class="py-3 px-4">Mata Kuliah</th>
                                <th class="py-3 px-4">Tugas Kuliah</th>
                                <th class="py-3 px-4 text-center">Nilai</th>
                                <th class="py-3 px-4">Feedback Dosen</th>
                                <th class="py-3 px-4 sm:px-5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="nilaiTableBody" class="divide-y divide-slate-100">
                            @forelse ($gradeItems as $idx => $item)
                                <tr class="nilai-row hover:bg-sky-50/40 transition-colors cursor-pointer text-xs"
                                    data-name="{{ strtolower($item['student_name']) }}"
                                    data-nim="{{ $item['student_nim'] }}"
                                    data-assignment="{{ strtolower($item['assignment_title']) }}"
                                    data-course-code="{{ $item['course_code'] }}"
                                    data-status="{{ $item['status'] }}"
                                    data-letter="{{ $item['letter'] }}"
                                    data-score="{{ $item['score'] ?? '' }}"
                                    data-item="{{ htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') }}">
                                    
                                    <!-- Mahasiswa -->
                                    <td class="py-3 px-4 sm:px-5">
                                        <span class="font-extrabold text-slate-800 text-xs sm:text-sm">{{ $item['student_name'] }}</span>
                                    </td>

                                    <!-- NIM -->
                                    <td class="py-3 px-4">
                                        <span class="font-mono text-xs font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">{{ $item['student_nim'] }}</span>
                                    </td>

                                    <!-- Mata Kuliah: Nama MK dan Dosen -->
                                    <td class="py-3 px-4">
                                        <div class="flex flex-col gap-0.5 max-w-[200px]">
                                            <span class="font-bold text-slate-800 truncate text-xs" title="{{ $item['course_name'] }}">{{ $item['course_name'] }}</span>
                                            <span class="text-[11px] text-slate-400 truncate">{{ $item['lecturer_name'] }}</span>
                                        </div>
                                    </td>

                                    <!-- Tugas Kuliah: Judul & Waktu -->
                                    <td class="py-3 px-4">
                                        <div class="flex flex-col gap-0.5 max-w-[220px]">
                                            <span class="font-bold text-slate-800 truncate text-xs" title="{{ $item['assignment_title'] }}">{{ $item['assignment_title'] }}</span>
                                            <span class="text-[11px] text-slate-400">
                                                {{ $item['submitted_at'] }} ({{ $item['is_late'] ? 'Terlambat' : 'Tepat Waktu' }})
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Nilai -->
                                    <td class="py-3 px-4 text-center">
                                        @if ($item['score'] !== null)
                                            <span class="font-black text-slate-800 text-xs sm:text-sm">
                                                {{ $item['score'] }} ({{ $item['letter'] }})
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400 font-bold">-</span>
                                        @endif
                                    </td>

                                    <!-- Feedback Dosen -->
                                    <td class="py-3 px-4">
                                        <div class="max-w-[180px] truncate text-xs" title="{{ $item['feedback'] }}">
                                            @if ($item['status'] === 'graded')
                                                <span class="text-slate-600 italic font-medium">"{{ $item['feedback'] }}"</span>
                                            @else
                                                <span class="text-slate-400">Menunggu evaluasi</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-3 px-4 sm:px-5 text-center">
                                        @if ($item['status'] === 'graded')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                                ✓ Dinilai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/80">
                                                Menunggu
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyDbRow">
                                    <td colspan="7" class="text-center py-12 px-4 text-slate-400">
                                        <div class="flex flex-col items-center gap-2.5 max-w-sm mx-auto">
                                            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-500 flex items-center justify-center text-2xl">
                                                📝
                                            </div>
                                            <span class="text-sm font-extrabold text-slate-800">Belum Ada Data Pengumpulan &amp; Nilai</span>
                                            <span class="text-xs text-slate-500 leading-relaxed">Saat ini belum ada riwayat tugas yang dikumpulkan mahasiswa atau dievaluasi oleh dosen pengampu di database.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                            <tr id="emptySearchRow" style="display: none;">
                                <td colspan="7" class="text-center py-10 px-4 text-slate-400">
                                    <div class="flex flex-col items-center gap-2 max-w-sm mx-auto">
                                        <svg class="w-8 h-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                        <span class="text-xs font-bold text-slate-700">Tidak ada data penilaian yang cocok dengan filter</span>
                                        <span class="text-[11.5px] text-slate-400">Coba ubah kata kunci pencarian atau sesuaikan opsi filter di atas.</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <x-footer />
    </div>

    <!-- MODAL DETAIL LEMBAR NILAI & FEEDBACK -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 items-center justify-center p-4" id="modalGradeDetail">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-[620px] overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-sky-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="7"></circle>
                        <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                    </svg>
                    Lembar Penilaian &amp; Evaluasi Mahasiswa
                </h3>
                <button type="button" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center text-xl font-bold cursor-pointer transition-colors" id="btnCloseModal" title="Tutup Modal">&times;</button>
            </div>
            
            <div class="p-5 flex flex-col gap-4 max-h-[75vh] overflow-y-auto">
                <!-- Hero Skor & Mahasiswa -->
                <div class="bg-slate-50/80 p-4 rounded-xl border border-slate-200/60 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-full bg-sky-50 text-sky-600 font-black text-sm flex items-center justify-center shrink-0 border border-sky-100" id="modalAvatar">
                            BS
                        </div>
                        <div>
                            <h4 id="modalStudentName" class="text-sm font-extrabold text-slate-900">Budi Santoso</h4>
                            <span id="modalStudentNim" class="text-xs font-mono font-bold text-slate-500">NIM: 10221001</span>
                        </div>
                    </div>
                    <div class="text-right flex flex-col items-end">
                        <span class="text-2xl font-black text-slate-900" id="modalScoreVal">92.5</span>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-[11px] text-slate-400 font-bold">Predikat:</span>
                            <span class="grade-pill grade-pill-a" id="modalGradePill">A</span>
                        </div>
                    </div>
                </div>

                <!-- Meta Informasi Perkuliahan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3 bg-slate-50/60 rounded-xl border border-slate-100 flex flex-col gap-0.5">
                        <div class="text-[10.5px] font-bold uppercase tracking-wider text-slate-400">Mata Kuliah</div>
                        <div class="text-xs font-extrabold text-slate-800" id="modalCourse">SI2514024 &bull; Pemrograman Web</div>
                    </div>
                    <div class="p-3 bg-slate-50/60 rounded-xl border border-slate-100 flex flex-col gap-0.5">
                        <div class="text-[10.5px] font-bold uppercase tracking-wider text-slate-400">Dosen Penilai</div>
                        <div class="text-xs font-extrabold text-slate-800" id="modalLecturer">Dr. Hendra Gunawan, M.T.</div>
                    </div>
                    <div class="p-3 bg-slate-50/60 rounded-xl border border-slate-100 flex flex-col gap-0.5">
                        <div class="text-[10.5px] font-bold uppercase tracking-wider text-slate-400">Tugas Kuliah</div>
                        <div class="text-xs font-extrabold text-slate-800" id="modalAssignment">Tugas 1: Implementasi Blade Template</div>
                    </div>
                    <div class="p-3 bg-slate-50/60 rounded-xl border border-slate-100 flex flex-col gap-0.5">
                        <div class="text-[10.5px] font-bold uppercase tracking-wider text-slate-400">Waktu Pengumpulan</div>
                        <div class="text-xs font-extrabold text-slate-800" id="modalSubmittedAt">04 Okt 2026, 21:15 (Tepat Waktu)</div>
                    </div>
                </div>

                <!-- Box Umpan Balik Dosen -->
                <div class="p-4 bg-emerald-50/50 rounded-xl border border-emerald-100 flex flex-col gap-2" id="modalFeedbackBox">
                    <div class="flex items-center justify-between text-xs font-bold text-emerald-800">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                            Catatan Umpan Balik &amp; Evaluasi Dosen
                        </span>
                        <span id="modalGradedAt" class="text-[11px] font-semibold text-emerald-700">05 Okt 2026, 14:20</span>
                    </div>
                    <p class="text-xs text-emerald-900 leading-relaxed font-medium italic" id="modalFeedbackText">
                        Implementasi arsitektur Blade rapi, pemisahan komponen dan styling terstruktur sangat baik.
                    </p>
                </div>

                <!-- Berkas yang Dikumpulkan -->
                <div class="p-3.5 bg-slate-50/80 rounded-xl border border-slate-200/60 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-extrabold text-slate-800 truncate" id="modalFileName">Tugas1_BudiSantoso_10221001.zip</div>
                            <div class="text-[11px] text-slate-400 font-semibold" id="modalFileSize">Ukuran berkas: 2.4 MB</div>
                        </div>
                    </div>
                    <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-sky-600 bg-sky-50 hover:bg-sky-500 hover:text-white transition-all shadow-xs cursor-pointer shrink-0" id="btnDownloadFile">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        Unduh Berkas
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notifikasi Ringan -->
    <div id="toastNilai">
        <svg class="w-4 h-4 text-emerald-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
        <span id="toastNilaiText">Berhasil mengunduh rekap nilai.</span>
    </div>

</body>
</html>
