<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Tugas Perkuliahan — KampusLMS Admin</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/admin.tugas.js'])
    @endif

    <style>
        #tugasModal { display: none; }
        #tugasModal.active { display: flex; }
        #courseFilterDropdown.open #courseFilterMenu { display: block; }
        .filter-option-item.active { background-color: #F0F9FF; color: #0284c7; font-weight: 800; }
        .dosen-toast {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #ffffff;
            color: #0f172a;
            padding: 12px 18px;
            border-radius: 14px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.05);
            border-left: 4px solid #10B981;
            font-size: 13px;
            font-weight: 700;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .dosen-toast.show {
            transform: translateY(0);
            opacity: 1;
        }
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

        <!-- Konten Utama Manajemen Tugas Admin -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex flex-col gap-6 box-border">

            <!-- Topbar Header -->
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Manajemen Tugas Perkuliahan</h1>
                </div>
                <div>
                    <button type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-md shadow-sky-500/20 hover:shadow-lg hover:shadow-sky-500/30 hover:-translate-y-0.5 active:translate-y-0 transition-all cursor-pointer" id="btnOpenAddModal">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Buat Tugas Baru</span>
                    </button>
                </div>
            </header>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="text-xs sm:text-sm font-bold px-4 py-3 rounded-xl shadow-xs bg-emerald-50 text-emerald-700 border border-emerald-200/80 flex items-center gap-2">
                    <span>✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="text-xs sm:text-sm font-bold px-4 py-3 rounded-xl shadow-xs bg-rose-50 text-rose-700 border border-rose-200/80 flex items-center gap-2">
                    <span>⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Baris Statistik Metrik Cepat (4 Stat Asli) -->
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- 1. Total Penugasan -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-sky-500/10 text-sky-500 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Penugasan</span>
                        <span class="text-2xl font-black text-slate-800" id="statTotalTugas">{{ $totalAssignments }}</span>
                    </div>
                </div>

                <!-- 2. Dipublikasikan -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Dipublikasikan</span>
                        <span class="text-2xl font-black text-slate-800" id="statPublishedTugas">{{ $totalPublished }}</span>
                    </div>
                </div>

                <!-- 3. Draf Tugas -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Draf Tugas</span>
                        <span class="text-2xl font-black text-slate-800" id="statDraftTugas">{{ $totalDraft }}</span>
                    </div>
                </div>

                <!-- 4. Pengumpulan Masuk -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-orange-500/10 text-orange-500 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pengumpulan Masuk</span>
                        <span class="text-2xl font-black text-slate-800" id="statTotalSubmissions">{{ $totalSubmissions }} <span class="text-sm font-bold text-slate-400">Berkas</span></span>
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
                    <input type="text" id="inputSearchTugas"
                           placeholder="Cari judul tugas, mata kuliah, atau instruksi..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-all placeholder:text-slate-400">
                </div>

                <!-- Kelompok Filter Dropdowns (Di Kanan) -->
                <div class="flex flex-wrap sm:flex-nowrap items-center gap-3">
                    <!-- Custom Dropdown Filter MK dengan Scrollbar -->
                    <div class="relative" id="courseFilterDropdown">
                        <button type="button" class="flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs sm:text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500/30 transition-all cursor-pointer min-w-[200px] sm:min-w-[240px]" id="courseFilterBtn">
                            <span class="truncate" id="courseFilterText" data-default-label="Semua Mata Kuliah ({{ $coursesList->count() }} Terdaftar)">Semua Mata Kuliah ({{ $coursesList->count() }} Terdaftar)</span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div class="hidden absolute right-0 top-full mt-1.5 w-72 sm:w-80 bg-white rounded-xl shadow-xl border border-slate-200 z-50 overflow-hidden" id="courseFilterMenu">
                            <ul class="max-h-60 overflow-y-auto divide-y divide-slate-100 py-1" id="courseFilterOptions">
                                <li class="filter-option-item active px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-sky-50 hover:text-sky-600 cursor-pointer flex items-center justify-between transition-colors"
                                    data-value="all" data-label="Semua Mata Kuliah ({{ $coursesList->count() }} Terdaftar)">
                                    <span>Semua Mata Kuliah ({{ $coursesList->count() }} Terdaftar)</span>
                                </li>
                                @foreach ($coursesList as $course)
                                    <li class="filter-option-item px-3.5 py-2.5 text-xs text-slate-700 hover:bg-sky-50 hover:text-sky-600 cursor-pointer flex items-center justify-between gap-2 transition-colors"
                                        data-value="{{ $course->id }}" data-label="{{ $course->code }} • {{ $course->name }}">
                                        <span class="font-bold text-sky-600 bg-sky-50 px-1.5 py-0.5 rounded text-[10.5px] shrink-0">{{ $course->code }}</span>
                                        <span class="truncate font-semibold text-slate-800">{{ $course->name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <input type="hidden" id="selectFilterCourse" value="all">
                    </div>

                    <!-- Dropdown Filter Status Tugas -->
                    <div class="relative">
                        <select id="selectFilterStatus"
                                class="appearance-none pl-3.5 pr-8 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs sm:text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500/30 transition-all cursor-pointer">
                            <option value="all">Semua Status</option>
                            <option value="published">🟢 Dipublikasikan</option>
                            <option value="draft">⚪ Draf Tugas</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Grid Kartu Tugas -->
            <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="tugasCardsGrid">
                @forelse ($assignments as $t)
                    @php
                        $isPublished = $t->status === 'published';
                        $isPast = $t->due_at->isPast();
                        $isSoon = !$isPast && $t->due_at->isBefore(now()->addHours(48));

                        if (!$isPublished) {
                            $badgeClass = 'bg-slate-100 text-slate-600 border-slate-200';
                            $badgeText = 'Draft';
                            $badgeIcon = '○';
                        } elseif ($isPast) {
                            $badgeClass = 'bg-slate-100 text-slate-600 border-slate-200';
                            $badgeText = 'Deadline Berakhir';
                            $badgeIcon = '●';
                        } elseif ($isSoon) {
                            $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                            $badgeText = 'Mendekati Deadline';
                            $badgeIcon = '●';
                        } else {
                            $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            $badgeText = 'Tugas Aktif';
                            $badgeIcon = '●';
                        }

                        // Penyesuaian informasi mode dosen: Pertemuan & Progress Pengumpulan
                        preg_match('/(Pertemuan|Minggu)\s*(\d+)/i', $t->title, $matches);
                        $weekNum = $matches[2] ?? null;

                        $studentCount = $t->course ? $t->course->students()->count() : 0;
                        $pct = $studentCount > 0 ? min(100, round($t->submissions_count / $studentCount * 100)) : 0;

                        $creatorName = $t->creator?->name ?? 'Administrator';
                        $creatorInitials = strtoupper(substr($creatorName, 0, 2));
                    @endphp

                    <div class="assignment-card bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-lg hover:-translate-y-1 transition-all duration-200 p-5 flex flex-col justify-between relative"
                         data-id="{{ $t->id }}"
                         data-course-id="{{ $t->course_id }}"
                         data-course-code="{{ $t->course?->code ?? '' }}"
                         data-course-name="{{ $t->course?->name ?? '' }}"
                         data-course-lecturer="{{ $t->course?->lecturer?->name ?? 'Belum Ditugaskan' }}"
                         data-course-sks="{{ $t->course?->sks ?? 3 }}"
                         data-status="{{ $t->status }}"
                         data-title="{{ $t->title }}"
                         data-session="{{ $weekNum ?? '' }}"
                         data-instructions="{{ $t->instructions }}"
                         data-due-date="{{ $t->due_at->format('Y-m-d') }}"
                         data-due-time="{{ $t->due_at->format('H:i') }}"
                         data-score="{{ $t->max_score }}"
                         data-allow-late="{{ $t->allow_late ? 1 : 0 }}"
                         data-submissions="{{ $t->submissions_count }}">

                        <div>
                            <!-- Card Top Bar: Badges & Actions -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200/80 text-[10.5px] font-bold">{{ $t->course?->code ?? 'MK' }}</span>
                                    @if ($weekNum)
                                        <span class="px-2 py-0.5 rounded-md bg-sky-50 text-sky-600 border border-sky-200 text-[10.5px] font-bold">Pertemuan {{ $weekNum }}</span>
                                    @endif
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold border {{ $badgeClass }}">
                                        <span>{{ $badgeIcon }}</span>
                                        <span>{{ $badgeText }}</span>
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    @can('update', $t)
                                        <button type="button" class="btn-edit-tugas w-7 h-7 rounded-lg bg-sky-50 hover:bg-sky-500 text-sky-600 hover:text-white transition-all flex items-center justify-center cursor-pointer shadow-xs" title="Edit Tugas">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        </button>
                                    @endcan
                                    @can('delete', $t)
                                        <form action="{{ route('admin.tugas.destroy', $t) }}" method="POST"
                                              onsubmit="return confirm('Hapus tugas &quot;{{ addslashes($t->title) }}&quot;? Tindakan ini tidak dapat dibatalkan.')"
                                              class="m-0 inline-flex">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete-tugas w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-500 text-rose-600 hover:text-white transition-all flex items-center justify-center cursor-pointer shadow-xs" title="Hapus Tugas">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>

                            <!-- Judul & Skor Maksimal -->
                            <div class="flex items-start justify-between gap-2 mb-1.5">
                                <h3 class="text-base font-extrabold text-slate-900 line-clamp-1 leading-snug">{{ $t->title }}</h3>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200/80 text-[10px] font-bold shrink-0">Maks. {{ $t->max_score }}</span>
                            </div>

                            <!-- Mata Kuliah & Dosen -->
                            <div class="text-xs font-bold text-slate-700 flex items-center gap-1.5 mb-2.5">
                                <span>📚 {{ $t->course?->name ?? 'Mata Kuliah' }}</span>
                                <small class="text-slate-400 font-normal">&bull; {{ $t->course?->lecturer?->name ?? 'Dosen' }}</small>
                            </div>

                            <!-- Deskripsi Instruksi -->
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed mb-3.5">{{ Str::limit($t->instructions, 140) }}</p>

                            <!-- Kotak Batas Waktu Pengumpulan -->
                            <div class="bg-slate-50/80 rounded-xl p-3 border border-slate-200/60 flex items-center gap-3 mb-3.5">
                                <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-600 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10.5px] font-bold uppercase tracking-wider text-slate-400">Batas Waktu{{ $t->allow_late ? ' (boleh terlambat)' : '' }}</span>
                                    <span class="text-xs font-extrabold text-slate-800">{{ $t->due_at->translatedFormat('d M Y') }} &middot; {{ $t->due_at->format('H:i') }} WITA</span>
                                </div>
                            </div>

                            <!-- Progress Pengumpulan -->
                            <div class="mb-2">
                                <div class="flex justify-between items-center text-[11px] font-bold text-slate-500 mb-1.5">
                                    <span>Progress Pengumpulan</span>
                                    <span><strong class="text-slate-800">{{ $t->submissions_count }}</strong> / {{ $studentCount }} Mhs ({{ $pct }}%)</span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-300 {{ $pct === 100 ? 'bg-emerald-500' : 'bg-sky-500' }}" style="width: {{ $pct }}%;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-sky-500 to-indigo-500 text-white font-black text-[11px] flex items-center justify-center shadow-xs shrink-0" title="{{ $creatorName }}">
                                    {{ $creatorInitials }}
                                </div>
                                <span class="text-xs font-bold text-slate-700 truncate" title="{{ $creatorName }}">{{ $creatorName }}</span>
                            </div>

                            @can('view', $t)
                                <a href="{{ route('assignments.show', $t) }}" class="btn-view-submissions inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-sky-600 bg-sky-50 hover:bg-sky-500 hover:text-white transition-all shadow-xs shrink-0">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <span>Lihat Pengumpulan ({{ $t->submissions_count }}) &rarr;</span>
                                </a>
                            @endcan
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-2xl border border-slate-200/80 p-8 sm:p-12 text-center shadow-xs flex flex-col items-center justify-center gap-3" id="tugasEmptyState">
                        <div class="w-14 h-14 rounded-2xl bg-sky-500/10 text-sky-500 text-2xl flex items-center justify-center">📝</div>
                        <h3 class="text-base font-extrabold text-slate-800">Belum Ada Tugas Perkuliahan</h3>
                        <p class="text-xs sm:text-sm text-slate-500 max-w-md leading-relaxed">Belum ada penugasan yang dibuat pada sistem. Klik tombol di bawah untuk membuat tugas pertama.</p>
                        <button type="button" class="mt-2 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-md shadow-sky-500/20 cursor-pointer transition-all" onclick="document.getElementById('btnOpenAddModal').click()">
                            + Buat Tugas Pertama
                        </button>
                    </div>
                @endforelse

                <!-- Filter Empty State (Hidden by default, shown by filter search) -->
                <div class="col-span-full bg-white rounded-2xl border border-slate-200/80 p-8 sm:p-12 text-center shadow-xs flex flex-col items-center justify-center gap-3" id="tugasFilterEmptyState" style="display: none;">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 text-2xl flex items-center justify-center">🔍</div>
                    <h3 class="text-base font-extrabold text-slate-800">Tidak Ada Tugas yang Cocok</h3>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-md leading-relaxed">Tidak ditemukan penugasan kuliah untuk filter mata kuliah, status, atau kata kunci pencarian yang Anda pilih.</p>
                    <button type="button" class="mt-2 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-slate-700 bg-slate-100 hover:bg-slate-200 cursor-pointer transition-all" onclick="resetFilters()">
                        Reset Filter
                    </button>
                </div>
            </section>

        </main>

        <x-footer />
    </div>

    <!-- MODAL POPUP: BUAT / EDIT TUGAS -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 items-center justify-center p-4 {{ $errors->any() ? 'active' : '' }}" id="tugasModal">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-[620px] overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900" id="modalTitle">Buat Penugasan Baru</h3>
                        <span class="text-xs text-slate-500 font-semibold" id="modalSubtitle">Kelola penugasan perkuliahan mahasiswa</span>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center text-xl font-bold cursor-pointer transition-colors" id="btnCloseModal" aria-label="Tutup modal">&times;</button>
            </div>

            <form id="formTugas" method="POST" action="{{ route('admin.tugas.store') }}" class="m-0 flex flex-col">
                @csrf
                <input type="hidden" id="formMethodField" name="_method" value="">
                <input type="hidden" id="editTugasId" value="">

                <div class="p-5 flex flex-col gap-4 max-h-[70vh] overflow-y-auto">
                    @if ($errors->any())
                        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-3 rounded-xl text-xs font-bold">
                            Terdapat kesalahan pada input form:
                            <ul class="mt-1 list-disc list-inside font-semibold text-rose-700">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Baris 1: Mata Kuliah Target & Pertemuan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="modalCourseSelect" class="block text-xs font-extrabold text-slate-700 mb-1.5">Mata Kuliah Target <span class="text-rose-500">*</span></label>
                            <select id="modalCourseSelect" name="course_id"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-all cursor-pointer" required>
                                <option value="">-- Pilih Mata Kuliah --</option>
                                @foreach ($coursesList as $c)
                                    <option value="{{ $c->id }}" @selected(old('course_id') == $c->id)>
                                        {{ $c->code }} - {{ $c->name }} ({{ $c->lecturer?->name ?? 'Belum Ditugaskan' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id') <span class="text-xs text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="taskSession" class="block text-xs font-extrabold text-slate-700 mb-1.5">Pertemuan / Minggu Ke-</label>
                            <select id="taskSession" name="session"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-all cursor-pointer">
                                <option value="">(Opsional)</option>
                                @for ($w = 1; $w <= 16; $w++)
                                    <option value="{{ $w }}" @selected(old('session') == $w)>Pertemuan {{ $w }} (Minggu ke-{{ $w }})</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Judul Tugas -->
                    <div>
                        <label for="modalTugasTitle" class="block text-xs font-extrabold text-slate-700 mb-1.5">Judul Tugas <span class="text-rose-500">*</span></label>
                        <input type="text" id="modalTugasTitle" name="title"
                               placeholder="Contoh: Tugas 02 - Pembuatan REST API Sanctum"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-all placeholder:text-slate-400"
                               value="{{ old('title') }}" required>
                        @error('title') <span class="text-xs text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Petunjuk / Instruksi -->
                    <div>
                        <label for="modalTugasInstructions" class="block text-xs font-extrabold text-slate-700 mb-1.5">Instruksi / Petunjuk Pengerjaan <span class="text-rose-500">*</span></label>
                        <textarea id="modalTugasInstructions" name="instructions" rows="4"
                                  placeholder="Jelaskan kebutuhan tugas, format berkas yang dikumpulkan, dan kriteria penilaian..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-all placeholder:text-slate-400"
                                  required>{{ old('instructions') }}</textarea>
                        @error('instructions') <span class="text-xs text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Batas Tanggal & Batas Jam -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="modalTugasDueDate" class="block text-xs font-extrabold text-slate-700 mb-1.5">Batas Tanggal (Deadline) <span class="text-rose-500">*</span></label>
                            <input type="date" id="modalTugasDueDate" name="due_date"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-all cursor-pointer" required
                                   value="{{ old('due_date', now()->addWeek()->format('Y-m-d')) }}">
                        </div>
                        <div>
                            <label for="modalTugasDueTime" class="block text-xs font-extrabold text-slate-700 mb-1.5">Batas Jam <span class="text-rose-500">*</span></label>
                            <input type="time" id="modalTugasDueTime" name="due_time"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-all cursor-pointer" required
                                   value="{{ old('due_time', '23:59') }}">
                        </div>
                    </div>
                    @error('due_at') <span class="text-xs text-rose-500 font-bold -mt-2 block">{{ $message }}</span> @enderror

                    <!-- Nilai Maksimal & Status Publikasi -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="modalTugasMaxScore" class="block text-xs font-extrabold text-slate-700 mb-1.5">Nilai Maksimal <span class="text-rose-500">*</span></label>
                            <input type="number" id="modalTugasMaxScore" name="max_score" min="1" max="100"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-all" required
                                   value="{{ old('max_score', 100) }}">
                            @error('max_score') <span class="text-xs text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="modalTugasStatus" class="block text-xs font-extrabold text-slate-700 mb-1.5">Status Publikasi <span class="text-rose-500">*</span></label>
                            <select id="modalTugasStatus" name="status"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-all cursor-pointer" required>
                                <option value="published" @selected(old('status', 'published') === 'published')>Terbit (terlihat mahasiswa)</option>
                                <option value="draft" @selected(old('status') === 'draft')>Draft (belum terlihat mahasiswa)</option>
                            </select>
                            @error('status') <span class="text-xs text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Izinkan Terlambat -->
                    <div class="mt-1">
                        <label class="flex items-center gap-2.5 text-xs font-bold text-slate-700 cursor-pointer">
                            <input type="checkbox" id="modalTugasAllowLate" name="allow_late" value="1"
                                   class="w-4 h-4 rounded text-sky-600 focus:ring-sky-500 border-slate-300 transition-all"
                                   @checked(old('allow_late', true))>
                            <span>Izinkan pengumpulan terlambat (setelah deadline)</span>
                        </label>
                    </div>
                </div>

                <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/50 flex items-center justify-end gap-2.5">
                    <button type="button" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all cursor-pointer" id="btnCancelModal">Batal</button>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-md shadow-sky-500/20 hover:shadow-lg hover:shadow-sky-500/30 cursor-pointer transition-all" id="btnSubmitAssignment">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span id="btnSubmitText">Simpan Tugas</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div class="fixed bottom-6 right-6 z-50 flex flex-col gap-2.5 pointer-events-none" id="toastContainer"></div>

    <div id="tugasConfig" style="display:none;" data-store-url="{{ route('admin.tugas.store') }}" data-toast-success="{{ session('success') }}" data-toast-error="{{ session('error') }}"></div>
</body>
</html>
