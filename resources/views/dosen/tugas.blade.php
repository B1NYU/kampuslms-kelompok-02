<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Tugas — Portal Dosen KampusLMS</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    <!-- Memuat CSS & JS via Vite (Hanya Tailwind app.css murni tanpa file CSS eksternal) -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/dosen/dosen.tugas.js'])
    @endif
</head>

<body class="bg-[#F1F5F9] min-h-screen text-slate-800 antialiased font-['Nunito',sans-serif] relative overflow-x-hidden">

    <!-- Soft Decorative Atmospheric Glow -->
    <div class="fixed -top-24 -right-24 w-96 h-96 bg-sky-400/20 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed -bottom-24 -left-24 w-96 h-96 bg-amber-400/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed top-1/2 right-1/4 w-72 h-72 bg-orange-400/10 rounded-full blur-3xl pointer-events-none -z-10"></div>

    @php
        $isEdit = $assignment->exists;
        $dueDefault = $assignment->due_at ?? now()->addWeek()->setTime(23, 59);
        // Policy membaca $assignment->course; isi relasinya dari $course agar tidak N+1.
        $assignments->each(fn ($a) => $a->setRelation('course', $course));
    @endphp

    <div class="min-h-screen flex flex-col justify-between relative z-10">

        <!-- Navbar Khusus Dosen -->
        <x-navbar-dosen />

        <!-- Konten Utama Buat Tugas -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex flex-col gap-6 box-border">

            <!-- Topbar Header -->
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span class="inline-block w-1.5 h-6 sm:h-7 rounded-full bg-gradient-to-b from-sky-500 to-sky-400"></span>
                        Penugasan Kuliah
                    </h1>
                </div>

                <div class="flex items-center gap-2.5 bg-white px-3.5 py-2 rounded-xl border border-sky-100 shadow-xs self-start sm:self-auto">
                    <span class="text-xs font-bold text-slate-500 shrink-0">Mata Kuliah Aktif:</span>
                    <select id="selectCurrentCourse"
                            class="text-xs font-extrabold text-sky-700 bg-transparent border-none outline-none cursor-pointer focus:ring-0"
                            onchange="if (this.value) window.location.href = this.value">
                        @foreach ($courses as $c)
                            <option value="{{ route('dosen.courses.assignments.index', $c) }}" @selected($c->id === $course->id)>
                                {{ $c->code }} &bull; {{ $c->name }} ({{ $c->sks }} SKS)
                            </option>
                        @endforeach
                    </select>
                </div>
            </header>

            <!-- Section: Daftar Tugas & Manajemen Deadline -->
            <section class="flex flex-col gap-6" id="buat-tugas">
                <div class="bg-white rounded-2xl border border-sky-100 shadow-xs p-5 sm:p-6 flex flex-col gap-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-black text-slate-900 leading-tight">Daftar Tugas &amp; Manajemen Deadline</h2>
                                <span class="text-xs text-slate-500 font-bold">Kelola instruksi, tenggat waktu, dan pantau pengumpulan berkas</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <span class="text-xs font-extrabold px-3 py-1.5 rounded-full bg-sky-50 text-sky-700 border border-sky-100">
                                {{ $assignments->count() }} Tugas Terdaftar
                            </span>
                            @can('create', [\App\Models\Assignment::class, $course])
                            <button type="button"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-xs shadow-sky-500/20 transition cursor-pointer"
                                    id="btnOpenCreateAssignmentModal">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                <span>+ Buat Tugas Baru</span>
                            </button>
                            @endcan
                        </div>
                    </div>

                    <!-- Daftar Tugas & Status Deadline -->
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <span class="text-xs sm:text-sm font-extrabold text-slate-700">
                                Penugasan Aktif: <strong class="text-slate-900">{{ $course->code }} &bull; {{ $course->name }}</strong> ({{ $assignments->count() }} Tugas)
                            </span>
                            <span class="text-[11px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-full bg-orange-50 text-orange-600 border border-orange-200/60">
                                Monitoring Deadline
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="assignmentsGrid">
                            @forelse ($assignments as $a)
                                @php
                                    $pct = $studentCount > 0 ? min(100, round($a->submissions_count / $studentCount * 100)) : 0;
                                    $isPast = $a->due_at->isPast();
                                    $isSoon = ! $isPast && $a->due_at->isBefore(now()->addHours(48));

                                    if ($a->status === 'draft') {
                                        [$badgeBg, $badgeText] = ['bg-amber-50 text-amber-600 border-amber-200', 'Draft'];
                                    } elseif ($isPast) {
                                        [$badgeBg, $badgeText] = ['bg-slate-100 text-slate-600 border-slate-200', 'Deadline Berakhir'];
                                    } elseif ($isSoon) {
                                        [$badgeBg, $badgeText] = ['bg-orange-50 text-orange-600 border-orange-200', 'Mendekati Deadline'];
                                    } else {
                                        [$badgeBg, $badgeText] = ['bg-emerald-50 text-emerald-600 border-emerald-200', 'Tugas Aktif'];
                                    }

                                    preg_match('/(Pertemuan|Minggu)\s*(\d+)/i', $a->title, $matches);
                                    $weekNum = $matches[2] ?? $loop->iteration;
                                @endphp

                                <div class="assignment-card bg-white rounded-2xl border p-5 shadow-xs hover:shadow-md hover:-translate-y-0.5 transition duration-200 flex flex-col justify-between gap-4 {{ $assignment->id === $a->id ? 'border-sky-500 ring-2 ring-sky-400/30 is-editing' : 'border-sky-100' }}">
                                    <div class="flex flex-col gap-3">
                                        <div class="flex items-center justify-between gap-2 flex-wrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-sky-50 text-sky-600 border border-sky-200/60">
                                                Pertemuan {{ $weekNum }}
                                            </span>
                                            <span class="text-[10px] font-extrabold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                                                Maks. {{ $a->max_score }}
                                            </span>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-black text-slate-900 leading-snug line-clamp-2">{{ $a->title }}</h4>
                                            <p class="text-xs text-slate-500 line-clamp-2 mt-1 font-medium">{{ \Illuminate\Support\Str::limit($a->instructions, 140) }}</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-3">
                                        <div class="flex items-center gap-2.5 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                            <svg class="w-4 h-4 text-sky-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            </svg>
                                            <div class="flex flex-col min-w-0">
                                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                    Batas Waktu{{ $a->allow_late ? ' (boleh terlambat)' : '' }}
                                                </span>
                                                <span class="text-xs font-black text-slate-800 truncate">
                                                    {{ $a->due_at->translatedFormat('d M Y') }} &middot; {{ $a->due_at->format('H:i') }} WITA
                                                </span>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="flex items-center justify-between text-[11px] font-bold mb-1.5">
                                                <span class="text-slate-500">Progress Pengumpulan</span>
                                                <span class="text-slate-700"><strong>{{ $a->submissions_count }}</strong>/{{ $studentCount }} Mhs ({{ $pct }}%)</span>
                                            </div>
                                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-300 {{ $pct === 100 ? 'bg-emerald-500' : 'bg-sky-500' }}" style="width: {{ $pct }}%;"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2 flex-wrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black border {{ $badgeBg }}">
                                            {{ $badgeText }}
                                        </span>

                                        <div class="flex items-center gap-1.5 flex-wrap justify-end">
                                            @can('view', $a)
                                            <a href="{{ route('assignments.show', $a) }}"
                                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-sky-600 bg-sky-50 hover:bg-sky-500 hover:text-white transition">
                                                <span>Pengumpulan</span>
                                                <span>&rarr;</span>
                                            </a>
                                            @endcan

                                            @can('update', $a)
                                            <a href="{{ route('dosen.assignments.edit', $a) }}"
                                               class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
                                                Ubah
                                            </a>
                                            @endcan

                                            @if ($a->submissions_count === 0)
                                                @can('delete', $a)
                                                <form action="{{ route('dosen.assignments.destroy', $a) }}" method="POST"
                                                      onsubmit="return confirm('Hapus tugas ini? Tindakan ini tidak dapat dibatalkan.')"
                                                      class="inline m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                                                        Hapus
                                                    </button>
                                                </form>
                                                @endcan
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state text-center py-12 px-4 col-span-full bg-slate-50 rounded-2xl border-2 border-dashed border-sky-200/80">
                                    <div class="text-4xl mb-2">📝</div>
                                    <div class="text-sm font-black text-slate-800 mb-1">Belum Ada Tugas untuk Mata Kuliah Ini</div>
                                    <p class="text-xs text-slate-500 mb-4 max-w-md mx-auto">Mata kuliah ini belum memiliki penugasan aktif. Klik tombol di bawah untuk membuat tugas pertama.</p>
                                    @can('create', [\App\Models\Assignment::class, $course])
                                    <button type="button"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-xs cursor-pointer mx-auto"
                                            onclick="document.getElementById('btnOpenCreateAssignmentModal').click()">
                                        + Buat Tugas Pertama
                                    </button>
                                    @endcan
                                </div>
                            @endforelse
                        </div>

                        {{-- Navigasi Pagination Laravel --}}
                        @if ($assignments->hasPages())
                            <div class="mt-4">
                                {{ $assignments->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </section>

        </main>

        <!-- MODAL FORM BUAT / UBAH TUGAS -->
        <div class="dosen-modal-overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs items-center justify-center p-4 transition-all duration-200 {{ ($isEdit || $errors->any()) ? 'flex' : 'hidden [&.active]:flex' }}" id="assignmentModalOverlay">
            <div class="w-full max-w-[620px] bg-white rounded-2xl shadow-2xl border border-sky-100 overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900" id="modalAssignmentTitle">{{ $isEdit ? 'Ubah Penugasan Kuliah' : 'Buat Penugasan Baru' }}</h3>
                            <span class="text-[11px] text-slate-500 font-bold">{{ $course->code }} &bull; {{ $course->name }}</span>
                        </div>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-700 text-xl font-black cursor-pointer leading-none" id="btnCloseAssignmentModal" aria-label="Tutup modal">&times;</button>
                </div>

                <form id="formAssignment" method="POST"
                      action="{{ $isEdit ? route('dosen.assignments.update', $assignment) : route('dosen.courses.assignments.store', $course) }}"
                      class="m-0 flex flex-col">
                    @csrf
                    @if ($isEdit)
                        @method('PUT')
                    @endif

                    <div class="p-6 flex flex-col gap-4 max-h-[70vh] overflow-y-auto">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-slate-700 mb-1.5">Mata Kuliah Target</label>
                                <input type="text" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-500 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed" value="{{ $course->code }} - {{ $course->name }}" disabled>
                            </div>
                            <div>
                                <label for="taskSession" class="block text-xs font-black text-slate-700 mb-1.5">Pertemuan / Minggu Ke- <span class="text-red-500">*</span></label>
                                <select id="taskSession" name="session" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" required>
                                    @for ($w = 1; $w <= 16; $w++)
                                        <option value="{{ $w }}" @selected(old('session') == $w || Str::contains($assignment->title ?? '', ['Pertemuan ' . $w, 'Minggu ' . $w, 'Tugas 0' . $w, 'Tugas ' . $w]))>
                                            Pertemuan {{ $w }} (Minggu ke-{{ $w }})
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="taskTitle" class="block text-xs font-black text-slate-700 mb-1.5">Judul Tugas <span class="text-red-500">*</span></label>
                            <input type="text" id="taskTitle" name="title"
                                   class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400"
                                   value="{{ old('title', $assignment->title) }}"
                                   placeholder="Contoh: Tugas 03 - Implementasi Blade &amp; Controller" required>
                            @error('title') <span class="block text-[11px] font-bold text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="taskDesc" class="block text-xs font-black text-slate-700 mb-1.5">Instruksi / Petunjuk Pengerjaan <span class="text-red-500">*</span></label>
                            <textarea id="taskDesc" name="instructions" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" rows="4"
                                      placeholder="Jelaskan kebutuhan tugas, format berkas yang dikumpulkan, dan kriteria penilaian..."
                                      required>{{ old('instructions', $assignment->instructions) }}</textarea>
                            @error('instructions') <span class="block text-[11px] font-bold text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Input Tanggal & Jam Deadline -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="taskDeadlineDate" class="block text-xs font-black text-slate-700 mb-1.5">Batas Tanggal (Deadline) <span class="text-red-500">*</span></label>
                                <input type="date" id="taskDeadlineDate" name="due_date" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" required
                                       value="{{ old('due_date', $dueDefault->format('Y-m-d')) }}">
                            </div>
                            <div>
                                <label for="taskDeadlineTime" class="block text-xs font-black text-slate-700 mb-1.5">Batas Jam <span class="text-red-500">*</span></label>
                                <input type="time" id="taskDeadlineTime" name="due_time" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" required
                                       value="{{ old('due_time', $dueDefault->format('H:i')) }}">
                            </div>
                        </div>
                        @error('due_at') <span class="block text-[11px] font-bold text-red-500 -mt-2">{{ $message }}</span> @enderror

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="taskMaxScore" class="block text-xs font-black text-slate-700 mb-1.5">Nilai Maksimal <span class="text-red-500">*</span></label>
                                <input type="number" id="taskMaxScore" name="max_score" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" min="1" max="100" required
                                       value="{{ old('max_score', $assignment->max_score ?? 100) }}">
                                @error('max_score') <span class="block text-[11px] font-bold text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="taskStatus" class="block text-xs font-black text-slate-700 mb-1.5">Status Publikasi <span class="text-red-500">*</span></label>
                                <select id="taskStatus" name="status" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" required>
                                    @foreach (['draft' => 'Draft (belum terlihat mahasiswa)', 'published' => 'Terbit (terlihat mahasiswa)'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', $assignment->status ?? 'published') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status') <span class="block text-[11px] font-bold text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-1">
                            <label class="flex items-center gap-2.5 text-xs font-bold text-slate-700 cursor-pointer select-none">
                                <input type="checkbox" name="allow_late" value="1"
                                       class="w-4 h-4 rounded text-sky-600 focus:ring-sky-400 border-slate-300"
                                       @checked(old('allow_late', $isEdit ? $assignment->allow_late : true))>
                                Izinkan pengumpulan terlambat (setelah deadline)
                            </label>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end gap-3">
                        @if ($isEdit)
                            <a href="{{ route('dosen.courses.assignments.index', $course) }}" class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition cursor-pointer">
                                Batal Ubah
                            </a>
                        @else
                            <button type="button" class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition cursor-pointer" id="btnCancelAssignmentModal">
                                Batal
                            </button>
                        @endif
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-xs shadow-sky-500/20 transition cursor-pointer">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            {{ $isEdit ? 'Simpan Perubahan' : 'Publikasikan Tugas' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <x-footer />
    </div>

    <!-- Toast Notification Container -->
    <div class="fixed bottom-5 right-5 z-[99999] flex flex-col gap-2.5 max-w-sm pointer-events-none" id="toastContainer"></div>

    <div id="dosenTugasConfig" style="display:none;" data-is-edit="{{ $isEdit ? '1' : '0' }}" data-redirect-url="{{ $isEdit ? route('dosen.courses.assignments.index', $course) : '' }}" data-toast-success="{{ session('success') }}" data-toast-error="{{ session('error') }}"></div>
</body>

</html>
