<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengumpulan: {{ $submission->assignment->title }} — Portal KampusLMS</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    <!-- Memuat CSS & JS via Vite (Tailwind app.css murni tanpa file CSS eksternal) -->
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

        <!-- Navbar Dinamis Sesuai Peran -->
        @if(auth()->user()->role === 'dosen')
            <x-navbar-dosen />
        @elseif(auth()->user()->role === 'admin')
            <x-navbar-admin />
        @else
            <x-layout />
        @endif

        <!-- Konten Utama Detail Pengumpulan -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex flex-col gap-6 box-border">

            <!-- Topbar Header -->
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
                <div class="flex flex-col gap-1 min-w-0">
                    <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-sky-600">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                        @if(auth()->user()->role === 'dosen')
                            PORTAL DOSEN &bull; EVALUASI PENGUMPULAN
                        @elseif(auth()->user()->role === 'admin')
                            PANEL ADMIN &bull; DETAIL PENGUMPULAN
                        @else
                            PORTAL MAHASISWA &bull; STATUS PENGUMPULAN
                        @endif
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight truncate">
                        {{ $submission->assignment->title }}
                    </h1>
                    <p class="text-xs sm:text-sm font-bold text-slate-500">
                        {{ $submission->assignment->course->code }} &middot; {{ $submission->assignment->course->name }}
                    </p>
                </div>

                <div class="flex items-center gap-2 flex-wrap shrink-0">
                    @if(auth()->user()->role === 'dosen')
                        <a href="{{ route('dosen.penilaian') }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs transition">
                            &larr; Kembali ke Penilaian
                        </a>
                        <a href="{{ route('assignments.show', $submission->assignment) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs transition">
                            Detail Tugas
                        </a>
                    @else
                        <a href="{{ route('assignments.show', $submission->assignment) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs transition">
                            &larr; Kembali ke Detail Tugas
                        </a>
                    @endif
                </div>
            </header>

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

            <!-- Kartu Detail Pengumpulan -->
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
                            <h2 class="text-base sm:text-lg font-black text-slate-900 leading-snug">Informasi Pengumpulan &amp; Nilai</h2>
                            <p class="text-xs sm:text-sm font-bold text-slate-500 mt-0.5">Detail berkas yang dikirimkan beserta status evaluasi dosen pengampu.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($submission->is_late)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">
                                Terlambat
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Tepat Waktu
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Tabel Metadata Pengumpulan -->
                <div class="overflow-x-auto rounded-xl border border-slate-100">
                    <table class="w-full text-left text-xs sm:text-sm border-collapse">
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <th class="w-48 sm:w-56 px-4 sm:px-6 py-3.5 bg-slate-50/80 font-black text-slate-600 uppercase tracking-wider text-[11px] sm:text-xs">
                                    Mahasiswa
                                </th>
                                <td class="px-4 sm:px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center text-xs font-black shrink-0">
                                            {{ strtoupper(substr($submission->student->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <span class="block font-black text-slate-900 text-xs sm:text-sm">{{ $submission->student->name }}</span>
                                            <span class="block text-[11px] font-mono font-bold text-slate-500">{{ $submission->student->nim_nip ?? 'Mahasiswa' }}</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="px-4 sm:px-6 py-3.5 bg-slate-50/80 font-black text-slate-600 uppercase tracking-wider text-[11px] sm:text-xs">
                                    Berkas
                                </th>
                                <td class="px-4 sm:px-6 py-3.5">
                                    <div class="flex items-center gap-2.5 flex-wrap">
                                        <span class="text-base sm:text-lg">📄</span>
                                        <strong class="text-slate-900 font-bold text-xs sm:text-sm">{{ $submission->original_name }}</strong>
                                        <span class="text-[11px] sm:text-xs text-slate-500 font-semibold">
                                            ({{ number_format($submission->file_size / 1024, 1) }} KB)
                                        </span>
                                        <a href="{{ route('submissions.download', $submission) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 transition ml-1">
                                            Unduh Berkas
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="px-4 sm:px-6 py-3.5 bg-slate-50/80 font-black text-slate-600 uppercase tracking-wider text-[11px] sm:text-xs">
                                    Dikumpulkan
                                </th>
                                <td class="px-4 sm:px-6 py-3.5">
                                    <span class="font-bold text-slate-700">
                                        {{ $submission->submitted_at ? $submission->submitted_at->translatedFormat('d M Y, H:i') : '-' }} WITA
                                        @if($submission->is_late)
                                            <span class="text-amber-600 ml-1 font-extrabold">(terlambat)</span>
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="px-4 sm:px-6 py-3.5 bg-slate-50/80 font-black text-slate-600 uppercase tracking-wider text-[11px] sm:text-xs">
                                    Catatan Mahasiswa
                                </th>
                                <td class="px-4 sm:px-6 py-3.5 text-slate-700 font-medium">
                                    {{ $submission->note ?: '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th class="px-4 sm:px-6 py-3.5 bg-slate-50/80 font-black text-slate-600 uppercase tracking-wider text-[11px] sm:text-xs">
                                    Nilai
                                </th>
                                <td class="px-4 sm:px-6 py-3.5">
                                    @if($submission->grade)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            {{ $submission->grade->score }} / {{ $submission->assignment->max_score }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-bold">Belum dinilai</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="px-4 sm:px-6 py-3.5 bg-slate-50/80 font-black text-slate-600 uppercase tracking-wider text-[11px] sm:text-xs">
                                    Umpan balik
                                </th>
                                <td class="px-4 sm:px-6 py-3.5 text-slate-700 font-medium leading-relaxed">
                                    {{ $submission->grade?->feedback ?: '-' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            @can('grade', $submission)
            <!-- Form Evaluasi & Penilaian Dosen -->
            <section class="bg-white rounded-2xl border border-sky-100 shadow-xs p-5 sm:p-6 flex flex-col gap-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900 leading-snug">Form Penilaian &amp; Umpan Balik Dosen</h2>
                    </div>
                </div>

                <form action="{{ route('dosen.penilaian.update', $submission) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-start">
                        <!-- Kolom Nilai -->
                        <div class="md:col-span-4 flex flex-col gap-2">
                            <label for="scoreInput" class="block text-xs sm:text-sm font-black text-slate-800">
                                Nilai Akhir (Skala 0 - {{ $submission->assignment->max_score }}) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number"
                                   id="scoreInput"
                                   name="score"
                                   value="{{ old('score', $submission->grade ? (float) $submission->grade->score : '') }}"
                                   placeholder="0 - {{ $submission->assignment->max_score }}"
                                   min="0"
                                   max="{{ $submission->assignment->max_score }}"
                                   step="any"
                                   required
                                   class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-900 bg-white border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-sky-500/20 focus:border-sky-400 transition">
                        </div>

                        <!-- Kolom Feedback -->
                        <div class="md:col-span-8 flex flex-col gap-2">
                            <label for="feedbackInput" class="block text-xs sm:text-sm font-black text-slate-800">
                                Catatan &amp; Umpan Balik (Feedback) Konstruktif <span class="text-rose-500">*</span>
                            </label>
                            <textarea id="feedbackInput"
                                      name="feedback"
                                      rows="5"
                                      required
                                      placeholder="Tuliskan ulasan spesifik mengenai pengumpulan ini, kelebihan pengerjaan, serta aspek yang perlu ditingkatkan oleh mahasiswa..."
                                      class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-hidden focus:ring-2 focus:ring-sky-500/20 focus:border-sky-400 transition resize-y min-h-[120px]">{{ old('feedback', $submission->grade?->feedback) }}</textarea>

                            <div class="flex justify-end mt-2">
                                <button type="submit"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-black text-white bg-emerald-600 hover:bg-emerald-700 shadow-xs shadow-emerald-600/20 transition cursor-pointer">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>{{ $submission->grade ? 'Simpan Perubahan Nilai' : 'Simpan Nilai & Berikan Feedback' }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </section>

            <script>
                document.querySelectorAll('.btn-score-shortcut').forEach(b => {
                    b.addEventListener('click', () => {
                        const scoreInput = document.getElementById('scoreInput');
                        if (scoreInput) {
                            scoreInput.value = b.dataset.val;
                            scoreInput.focus();
                        }
                    });
                });
            </script>
            @endcan

        </main>

        <x-footer />
    </div>

</body>

</html>
