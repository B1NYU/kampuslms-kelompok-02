<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $assignment->title }} — Portal KampusLMS</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    <!-- Memuat CSS & JS via Vite (Tailwind app.css murni tanpa file CSS eksternal) -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/mahasiswa/mahasiswa.tugas.js'])
    @endif
</head>

<body class="bg-[#F1F5F9] min-h-screen text-slate-800 antialiased font-['Nunito',sans-serif] relative overflow-x-hidden">

    <!-- Soft Decorative Atmospheric Glow -->
    <div class="fixed -top-24 -right-24 w-96 h-96 bg-sky-400/20 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed -bottom-24 -left-24 w-96 h-96 bg-amber-400/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed top-1/2 right-1/4 w-72 h-72 bg-orange-400/10 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <div class="min-h-screen flex flex-col justify-between relative z-10">

        <!-- Navbar Dinamis Sesuai Peran -->
        @if(auth()->user()->role === 'dosen')
            <x-navbar-dosen />
        @elseif(auth()->user()->role === 'admin')
            <x-navbar-admin />
        @else
            <x-layout />
        @endif

        <!-- [RESPONSIVE-MOBILE] Konten Utama Detail Tugas & Pengumpulan (Responsif Mobile & Desktop) -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8 flex flex-col gap-4 sm:gap-6 box-border">

            <!-- Topbar Header -->
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 pb-2 border-b border-slate-200/80">
                <div class="flex flex-col gap-1 min-w-0">
                    <div class="flex items-center gap-2 text-[11px] sm:text-xs font-black uppercase tracking-wider text-sky-600">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                        @if(auth()->user()->role === 'dosen')
                            PORTAL DOSEN &bull; DETAIL PENGUMPULAN TUGAS
                        @elseif(auth()->user()->role === 'admin')
                            PANEL ADMIN &bull; DETAIL TUGAS &amp; PENGUMPULAN
                        @else
                            PORTAL MAHASISWA &bull; DETAIL PENUGASAN KULIAH
                        @endif
                    </div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-slate-900 tracking-tight truncate">
                        {{ $assignment->title }}
                    </h1>
                </div>

                <div class="flex items-center gap-2.5 shrink-0">
                    @if(auth()->user()->role === 'dosen')
                        <a href="{{ route('dosen.tugas') }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs transition">
                            &larr; Kembali ke Kelola Tugas
                        </a>
                    @elseif(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs transition">
                            &larr; Kembali ke Dashboard Admin
                        </a>
                    @else
                        <a href="{{ route('mahasiswa.mata-kuliah.show', $assignment->course_id) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs transition">
                            &larr; Kembali ke Mata Kuliah
                        </a>
                    @endif
                </div>
            </header>

            <!-- 1. Kartu Informasi & Instruksi Tugas -->
            <section class="bg-white rounded-2xl border border-sky-100 shadow-xs p-5 sm:p-6 flex flex-col gap-5">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-4 border-b border-slate-100">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900 leading-snug">Informasi &amp; Instruksi Tugas</h2>
                            <p class="text-xs sm:text-sm font-bold text-slate-500 mt-0.5">
                                <strong class="text-slate-700">{{ $assignment->course->code }}</strong> &middot; {{ $assignment->course->name }} &middot; Dosen: {{ $assignment->course->lecturer?->name ?? 'Dosen Pengampu' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        @if ($assignment->due_at->isPast())
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">
                                Batas Waktu Lewat
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Tugas Berjalan
                            </span>
                        @endif
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black text-sky-700 bg-sky-50 border border-sky-100">
                            Nilai Maks: {{ $assignment->max_score }}
                        </span>
                    </div>
                </div>

                <!-- Ringkasan Info Detail -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div class="bg-slate-50/80 border border-sky-100/80 rounded-xl p-3.5 sm:p-4">
                        <span class="block text-[11px] font-black text-slate-400 uppercase tracking-wider">Tenggat Pengumpulan</span>
                        <span class="block text-sm sm:text-base font-black text-slate-800 mt-1">{{ $assignment->due_at->format('d M Y, H:i') }} WIB</span>
                    </div>
                    <div class="bg-slate-50/80 border border-sky-100/80 rounded-xl p-3.5 sm:p-4">
                        <span class="block text-[11px] font-black text-slate-400 uppercase tracking-wider">Skor Maksimal</span>
                        <span class="block text-sm sm:text-base font-black text-sky-600 mt-1">{{ $assignment->max_score }} Poin</span>
                    </div>
                    <div class="bg-slate-50/80 border border-sky-100/80 rounded-xl p-3.5 sm:p-4">
                        <span class="block text-[11px] font-black text-slate-400 uppercase tracking-wider">Status Pengumpulan</span>
                        <span class="block text-sm sm:text-base font-black text-emerald-600 mt-1">
                            @if(auth()->user()->role === 'mahasiswa')
                                {{ $mySubmission ? 'Sudah Dikumpulkan' : 'Belum Dikumpulkan' }}
                            @else
                                {{ $submissions->count() }} Berkas Masuk
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Petunjuk / Instruksi -->
                <div class="bg-white border border-sky-100 rounded-xl p-4 sm:p-5">
                    <span class="block text-xs font-black text-sky-600 uppercase tracking-wider mb-2">
                        Deskripsi &amp; Instruksi Pengerjaan:
                    </span>
                    <div class="text-xs sm:text-sm text-slate-700 leading-relaxed font-semibold">
                        {!! nl2br(e($assignment->instructions)) !!}
                    </div>
                </div>
            </section>

            <!-- 2. Bagian Pengumpulan (Berdasarkan Role) -->
            @if(auth()->user()->role === 'mahasiswa')
                <section class="bg-white rounded-2xl border border-sky-100 shadow-xs p-5 sm:p-6 flex flex-col gap-5">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-black text-slate-900 leading-snug">Pengumpulan Saya</h2>
                                <p class="text-xs sm:text-sm font-bold text-slate-500 mt-0.5">Unggah atau perbarui berkas jawaban tugas Anda sebelum batas tenggat berakhir.</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black text-sky-700 bg-sky-50 border border-sky-100">
                            {{ $mySubmission ? 'Sudah Dikumpulkan' : 'Menunggu Pengumpulan' }}
                        </span>
                    </div>

                    @php
                        $tenggatLewat = $assignment->due_at->isPast();
                        $ditutup = $tenggatLewat && ! $assignment->allow_late;
                        $nilaiSaya = $mySubmission?->grade;
                    @endphp

                    @if (session('success'))
                        <div role="status" class="p-3.5 sm:p-4 rounded-xl text-xs sm:text-sm font-bold text-emerald-800 bg-emerald-50 border border-emerald-200">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div role="alert" class="p-3.5 sm:p-4 rounded-xl text-xs sm:text-sm font-bold text-rose-800 bg-rose-50 border border-rose-200 space-y-1">
                            @foreach ($errors->all() as $pesan)
                                <div>{{ $pesan }}</div>
                            @endforeach
                        </div>
                    @endif

                    @if($mySubmission)
                        <div class="bg-emerald-50/80 border border-emerald-200 rounded-xl p-3.5 sm:p-4 flex items-center justify-between flex-wrap gap-2.5">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="text-emerald-600 font-black text-base sm:text-lg">✓</span>
                                <span class="text-xs sm:text-sm font-bold text-emerald-900 truncate">
                                    Sudah dikumpulkan: <strong>{{ $mySubmission->original_name }}</strong>
                                    (<a href="{{ route('submissions.show', $mySubmission) }}" class="text-sky-600 font-extrabold underline hover:text-sky-700">detail</a>
                                    &middot; <a href="{{ route('submissions.download', $mySubmission) }}" class="text-sky-600 font-extrabold underline hover:text-sky-700">unduh</a>)
                                </span>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-200 shrink-0">
                                Terkumpul {{ $mySubmission->submitted_at ? $mySubmission->submitted_at->format('d M Y, H:i') : '' }}
                            </span>
                        </div>
                    @endif

                    @if ($nilaiSaya)
                        <div class="bg-sky-50 border border-sky-200 rounded-xl p-4">
                            <div class="text-xs sm:text-sm font-black text-sky-900">
                                Nilai: {{ rtrim(rtrim(number_format((float) $nilaiSaya->score, 2), '0'), '.') }} / {{ $assignment->max_score }}
                            </div>
                            @if ($nilaiSaya->feedback)
                                <div class="text-xs sm:text-sm text-slate-700 mt-1.5 font-semibold leading-relaxed">{!! nl2br(e($nilaiSaya->feedback)) !!}</div>
                            @endif
                            <div class="text-xs text-slate-500 mt-2 font-bold">Tugas yang sudah dinilai tidak dapat dikumpulkan ulang.</div>
                        </div>
                    @elseif ($ditutup)
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-xs sm:text-sm font-bold text-amber-900">
                            Batas waktu pengumpulan telah berakhir dan tugas ini tidak menerima pengumpulan terlambat.
                            Hubungi dosen pengampu bila memerlukan dispensasi.
                        </div>
                    @else
                        @if ($tenggatLewat)
                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 sm:p-3.5 text-xs sm:text-sm font-bold text-amber-900">
                                Tenggat sudah lewat. Pengumpulan tetap diterima tetapi akan ditandai terlambat.
                            </div>
                        @endif

                        <form id="submissionForm" action="{{ route('mahasiswa.assignments.submissions.store', $assignment) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                                <!-- KOLOM KIRI: UNGGAH BERKAS TUGAS -->
                                <div class="lg:col-span-7 flex flex-col gap-2">
                                    <label class="block text-xs sm:text-sm font-black text-slate-800">
                                        Unggah Berkas Tugas <span class="text-rose-500">*</span>
                                    </label>

                                    <!-- DROPZONE INTERAKTIF -->
                                    <div class="w-full border-2 border-dashed border-sky-300 rounded-2xl p-6 sm:p-8 bg-slate-50/70 text-center cursor-pointer transition hover:bg-sky-50/40 relative"
                                         id="submissionDropzone">
                                        <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                            </svg>
                                        </div>
                                        <div class="text-xs sm:text-sm font-extrabold text-slate-900 mb-1">
                                            Tarik &amp; letakkan berkas di sini, atau <span class="text-sky-600 underline">Pilih File</span>
                                        </div>
                                        <div class="text-xs font-semibold text-slate-500">
                                            Mendukung: <strong class="text-sky-700">PDF, DOC, DOCX, ZIP, TXT</strong> &bull; Maksimal 10 MB
                                        </div>

                                        <input type="file" id="submissionFileInput" name="file" required accept=".pdf,.doc,.docx,.zip,.txt"
                                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer block">
                                    </div>

                                    <!-- PREVIEW CONTAINER BERKAS TUGAS -->
                                    <div id="submissionPreviewContainer" class="hidden w-full bg-white border border-sky-200 rounded-2xl p-4 shadow-xs">
                                        <div class="flex items-center justify-between gap-3 flex-wrap">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div id="fileTypeIcon" class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 text-xs font-black">
                                                    DOC
                                                </div>
                                                <div class="min-w-0">
                                                    <div id="previewFileName" class="text-xs sm:text-sm font-black text-slate-900 break-all">-</div>
                                                    <div class="flex items-center gap-2 mt-0.5 text-xs font-bold">
                                                        <span id="previewFileSize" class="text-slate-500">-</span>
                                                        <span class="text-slate-300">&bull;</span>
                                                        <span class="bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded border border-emerald-200">✓ Siap Dikumpulkan</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-1.5">
                                                <button type="button" id="btnChangeFile" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition cursor-pointer">
                                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path>
                                                    </svg>
                                                    Ganti
                                                </button>
                                                <button type="button" id="btnRemoveFile" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition cursor-pointer">
                                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>

                                        <!-- PRATINJAU DOKUMEN INTERAKTIF (PDF / TEKS) -->
                                        <div id="interactivePreviewBox" class="hidden mt-3 pt-3 border-t border-slate-100">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-xs font-black text-slate-600 flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                    Pratinjau Dokumen
                                                </span>
                                            </div>
                                            <div id="docPreviewHolder" class="w-full h-96 rounded-xl overflow-hidden bg-slate-50 border border-slate-200"></div>
                                        </div>
                                    </div>

                                    <div id="fileUploadError" role="alert" class="hidden mt-2 p-2.5 rounded-xl text-xs font-bold text-rose-800 bg-rose-50 border border-rose-200"></div>
                                    @error('file')<small class="text-rose-600 font-bold block mt-1 text-xs">{{ $message }}</small>@enderror
                                </div>

                                <!-- KOLOM KANAN: CATATAN PENGUMPULAN & SUBMIT -->
                                <div class="lg:col-span-5 flex flex-col gap-2">
                                    <label for="note" class="block text-xs sm:text-sm font-black text-slate-800">
                                        Catatan Pengumpulan
                                    </label>
                                    <textarea id="note" name="note" rows="5" maxlength="1000"
                                              placeholder="Tuliskan catatan atau keterangan pengumpulan bila diperlukan..."
                                              class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-sky-500/20 focus:border-sky-400 transition resize-y min-h-[140px]">{{ old('note') }}</textarea>

                                    <div class="flex justify-end mt-2">
                                        <button type="submit"
                                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-black text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-xs shadow-sky-500/20 transition cursor-pointer">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                            <span>{{ $mySubmission ? 'Kumpulkan Ulang' : 'Kumpulkan Tugas' }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif
                </section>
            @else
                <!-- Tabel Pengumpulan untuk Dosen / Admin -->
                <section class="bg-white rounded-2xl border border-sky-100 shadow-xs p-5 sm:p-6 flex flex-col gap-5">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-black text-slate-900 leading-snug">Pengumpulan Mahasiswa ({{ $submissions->count() }})</h2>
                                <p class="text-xs sm:text-sm font-bold text-slate-500 mt-0.5">Daftar seluruh mahasiswa yang telah mengirimkan berkas pengumpulan tugas ini.</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black text-slate-600 bg-slate-100 border border-slate-200">
                            Tabel Pengumpulan
                        </span>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-100">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 text-xs font-black uppercase tracking-wider">
                                    <th class="px-4 sm:px-6 py-3.5">Mahasiswa</th>
                                    <th class="px-4 sm:px-6 py-3.5">Berkas</th>
                                    <th class="px-4 sm:px-6 py-3.5">Waktu Pengumpulan</th>
                                    <th class="px-4 sm:px-6 py-3.5">Nilai</th>
                                    <th class="px-4 sm:px-6 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="submissionShowTbody" class="divide-y divide-slate-100">
                                @forelse($submissions as $s)
                                    @php
                                        $name = $s->student->name;
                                        $words = explode(' ', trim($name));
                                        $initials = strtoupper(substr($words[0] ?? 'M', 0, 1) . substr($words[1] ?? '', 0, 1));
                                        if (strlen($initials) === 1) $initials .= strtoupper(substr($words[0] ?? 'M', 1, 1));
                                        $palette = [
                                            ['bg' => 'bg-sky-50', 'color' => 'text-sky-600'],
                                            ['bg' => 'bg-amber-50', 'color' => 'text-amber-600'],
                                            ['bg' => 'bg-cyan-50', 'color' => 'text-cyan-600'],
                                            ['bg' => 'bg-emerald-50', 'color' => 'text-emerald-600'],
                                            ['bg' => 'bg-orange-50', 'color' => 'text-orange-600'],
                                        ];
                                        $color = $palette[$s->user_id % 5];
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-4 sm:px-6 py-3.5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full {{ $color['bg'] }} {{ $color['color'] }} flex items-center justify-center text-xs font-black shrink-0">
                                                    {{ $initials }}
                                                </div>
                                                <div>
                                                    <span class="block font-extrabold text-slate-900 text-xs sm:text-sm">{{ $s->student->name }}</span>
                                                    <span class="block text-[11px] font-mono text-slate-500 font-semibold">{{ $s->student->nim_nip ?? ('NIM: 1024' . str_pad($s->user_id, 4, '0', STR_PAD_LEFT)) }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3.5">
                                            <div class="flex items-center gap-2">
                                                <span class="text-base">📄</span>
                                                <div>
                                                    <span class="font-bold text-slate-800 block text-xs sm:text-sm">{{ $s->original_name }}</span>
                                                    @if($s->file_size)
                                                        <span class="text-[11px] text-slate-500 font-semibold">{{ number_format($s->file_size / 1024, 1) }} KB</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3.5 text-xs sm:text-sm">
                                            <span class="font-bold text-slate-700">
                                                {{ $s->submitted_at ? $s->submitted_at->format('d M Y, H:i') : '-' }}
                                            </span>
                                            @if($s->is_late)
                                                <span class="ml-1.5 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-800 border border-amber-200">
                                                    Terlambat
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 sm:px-6 py-3.5">
                                            @if($s->grade)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    {{ $s->grade->score }} / {{ $assignment->max_score }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 font-bold text-xs sm:text-sm">{{ $s->grade?->score ?? '-' }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 sm:px-6 py-3.5 text-right">
                                            <a href="{{ route('submissions.show', $s) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs transition">
                                                Lihat &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center px-6 py-12 text-slate-400 text-xs sm:text-sm font-bold">
                                            Belum ada pengumpulan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Navigasi Pagination Laravel --}}
                        @if ($submissions->hasPages())
                            <div class="px-4 sm:px-6 py-3 border-t border-slate-100 bg-slate-50/50">
                                {{ $submissions->links() }}
                            </div>
                        @endif
                    </div>
                </section>
            @endif

        </main>

        <x-footer />
    </div>

</body>

</html>
