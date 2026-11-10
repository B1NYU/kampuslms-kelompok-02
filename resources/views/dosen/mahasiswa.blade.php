<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Mahasiswa — Portal Dosen KampusLMS</title>

    <!-- Google Fonts Nunito -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">

    <!-- Memuat CSS & JS via Vite (Hanya Tailwind app.css murni tanpa file CSS eksternal) -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/dosen/dosen.mahasiswa.js'])
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

        <!-- [RESPONSIVE-MOBILE] Konten Utama Kelola Mahasiswa (Responsif Mobile & Desktop) -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8 flex flex-col gap-4 sm:gap-6 box-border">

            {{-- Data ($courses, $registeredStudents, $selectedCode) disiapkan oleh Dosen\StudentController@index --}}
            @php
                $dosenCourses = $courses;
                $firstCourse = $dosenCourses->firstWhere('code', $selectedCode);
                // MK yang pesertanya boleh diubah (CoursePolicy::manageEnrollment: dosen pengampu, MK draft/active).
                $enrollableCourses = $dosenCourses->filter(fn ($c) => auth()->user()->can('manageEnrollment', $c))->values();
            @endphp

            <!-- Topbar Header -->
            <header class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                <div class="flex items-center gap-3">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <span class="inline-block w-1.5 h-5 sm:h-7 rounded-full bg-gradient-to-b from-sky-500 to-sky-400"></span>
                        Pendaftaran Mahasiswa ke Mata Kuliah
                    </h1>
                </div>
            </header>

            <!-- Section: Form & Tabel Mahasiswa -->
            <section class="flex flex-col gap-4" id="kelola-mahasiswa">

                <!-- 1. Toolbar Kontrol (Search di Kiri, Filter MK & Tombol Daftarkan di Kanan) -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <!-- Kolom Mencari Mahasiswa (Kiri) -->
                    <div class="relative flex-1 max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                        <input type="text"
                               id="searchStudentInput"
                               class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-sky-100 rounded-xl shadow-xs focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-transparent transition"
                               placeholder="Cari nama mahasiswa atau NIM...">
                    </div>

                    <!-- Sisi Kanan: Filter MK & Tombol Daftarkan Mahasiswa -->
                    <div class="flex flex-wrap items-center gap-2.5">
                        <div class="flex items-center gap-2 bg-white px-3.5 py-2 rounded-xl border border-sky-100 shadow-xs">
                            <select id="selectCurrentCourse"
                                    class="text-xs font-extrabold text-sky-700 bg-transparent border-none outline-none cursor-pointer focus:ring-0"
                                    {{ $dosenCourses->isEmpty() ? 'disabled' : '' }}>
                                @forelse ($dosenCourses as $c)
                                    <option value="{{ $c->code }}" {{ $c->code === ($selectedCode ?? ($firstCourse?->code ?? '')) ? 'selected' : '' }}>
                                        {{ $c->code }} &bull; {{ $c->name }} ({{ $c->sks }} SKS - {{ $c->students->count() }} Mhs)
                                    </option>
                                @empty
                                    <option value="">Belum ada mata kuliah yang diampu</option>
                                @endforelse
                            </select>
                        </div>

                        @if ($enrollableCourses->isNotEmpty())
                        <button type="button"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-xs shadow-sky-500/20 transition cursor-pointer shrink-0"
                                id="btnOpenAddStudentModal">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Daftarkan Mahasiswa</span>
                        </button>
                        @endif
                    </div>
                </div>

                <!-- 2. Card Utama Berisi Header Card & Tabel Mahasiswa -->
                <div class="bg-white rounded-2xl border border-sky-100 shadow-xs overflow-hidden">

                    <!-- Header Card: Info Mahasiswa Terdaftar (Kiri) & Sort By (Kanan) -->
                    <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/50">
                        <div>
                            <span class="text-xs sm:text-sm font-extrabold text-slate-700">
                                Menampilkan <strong id="studentTableCount" class="text-sky-600 font-black">{{ $firstCourse?->students?->count() ?? 0 }}</strong> Mahasiswa Terdaftar
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-500">Sort by:</span>
                            <div class="relative">
                                <select id="sortStudentSelect"
                                        class="text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 shadow-xs focus:outline-none focus:border-sky-400 cursor-pointer">
                                    <option value="default">Default</option>
                                    <option value="name-asc">Nama (A &rarr; Z)</option>
                                    <option value="name-desc">Nama (Z &rarr; A)</option>
                                    <option value="nim-asc">NIM (Terkecil)</option>
                                    <option value="nim-desc">NIM (Terbesar)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Mahasiswa Terdaftar (Full Width) -->
                    <div class="w-full overflow-x-auto">
                        <table class="w-full text-left text-xs sm:text-sm" id="studentTable">
                            <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-black uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="px-5 py-3.5">Mahasiswa</th>
                                    <th class="px-5 py-3.5">NIM</th>
                                    <th class="px-5 py-3.5">Program Studi</th>
                                    <th class="px-5 py-3.5">Status</th>
                                    <th class="px-5 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="studentTableBody" class="divide-y divide-slate-100">
                                @php
                                    $selectedCourseCode = $firstCourse?->code ?? '';
                                    $hasStudentsInFirstCourse = $firstCourse && $firstCourse->students->isNotEmpty();
                                @endphp
                                @if ($dosenCourses->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center py-12 px-4 text-slate-500">
                                            <div class="flex flex-col items-center gap-2">
                                                <svg class="w-8 h-8 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                                </svg>
                                                <strong class="text-slate-800 text-sm">Anda belum mengampu mata kuliah apa pun</strong>
                                                <span class="text-xs text-slate-500">Hubungi Administrator untuk penugasan mata kuliah ke akun Anda.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    @php $orderIdx = ($students->currentPage() - 1) * $students->perPage(); @endphp
                                    @forelse ($students as $mhs)
                                        @php
                                            $orderIdx++;
                                            $initials = collect(explode(' ', $mhs->name))->map(fn($w)=>mb_substr($w,0,1))->join('');
                                            $initials = strtoupper(mb_substr($initials, 0, 2));
                                            $colors = [
                                                ['rgba(3, 159, 250, 0.12)', '#039FFA'],
                                                ['rgba(50, 179, 241, 0.14)', '#0284C7'],
                                                ['rgba(16, 185, 129, 0.14)', '#10B981'],
                                                ['rgba(249, 184, 4, 0.14)', '#D97706'],
                                                ['rgba(249, 99, 5, 0.12)', '#F96305']
                                            ];
                                            [$bg, $c] = $colors[$mhs->id % count($colors)];
                                        @endphp
                                        <tr data-mk="{{ $firstCourse?->code ?? '' }}"
                                            data-name="{{ strtolower($mhs->name) }}"
                                            data-nim="{{ $mhs->nim_nip }}"
                                            data-order="{{ $orderIdx }}"
                                            class="hover:bg-sky-50/30 transition">
                                            <td class="px-5 py-3.5">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black shrink-0" style="background:{{ $bg }}; color:{{ $c }};">
                                                        {{ $initials }}
                                                    </div>
                                                    <span class="student-name font-bold text-slate-800">{{ $mhs->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3.5 font-bold text-slate-600">
                                                <span class="student-nim">{{ $mhs->nim_nip }}</span>
                                            </td>
                                            <td class="px-5 py-3.5 text-slate-600 font-semibold">
                                                Sistem Informasi
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-600 border border-emerald-200">
                                                    Aktif
                                                </span>
                                            </td>
                                            <td class="px-5 py-3.5 text-right">
                                                @if ($firstCourse)
                                                @can('manageEnrollment', $firstCourse)
                                                <form method="POST"
                                                      action="{{ route('dosen.mahasiswa.destroy', [$firstCourse, $mhs]) }}"
                                                      onsubmit="return confirm(@js('Keluarkan ' . $mhs->name . ' dari mata kuliah ini?'))"
                                                      class="inline m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn-icon-danger btn-delete-student p-1.5 rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700 border border-transparent hover:border-red-200 transition cursor-pointer inline-flex items-center justify-center"
                                                            title="Keluarkan dari mata kuliah">
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                                @endcan
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="emptyCourseRow">
                                            <td colspan="5" class="text-center py-10 px-4 text-slate-400 font-bold">
                                                Belum ada mahasiswa yang terdaftar di mata kuliah ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- Navigasi Pagination Laravel --}}
                    @if ($students->hasPages())
                        <div class="px-5 py-3 border-t border-slate-100 bg-white flex items-center justify-between">
                            {{ $students->links() }}
                        </div>
                    @endif
                </div>
            </section>

        </main>

        <!-- MODAL FORM DAFTARKAN MAHASISWA -->
        <div class="dosen-modal-overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs items-center justify-center p-4 transition-all duration-200 hidden [&.active]:flex" id="studentModalOverlay">
            <div class="w-full max-w-[560px] bg-white rounded-2xl shadow-2xl border border-sky-100 overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <polyline points="16 11 18 13 22 9"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900">Pendaftaran Mahasiswa Baru</h3>
                            <span class="text-[11px] text-slate-500 font-bold">Daftarkan peserta ke mata kuliah</span>
                        </div>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-700 text-xl font-black cursor-pointer leading-none" id="btnCloseStudentModal" aria-label="Tutup modal">&times;</button>
                </div>

                <form id="formAddStudent" method="POST" action="{{ route('dosen.mahasiswa.store') }}" class="m-0 flex flex-col">
                    @csrf
                    <div class="p-6 flex flex-col gap-4 max-h-[70vh] overflow-y-auto">
                        <div>
                            <label for="mhsMatkul" class="block text-xs font-black text-slate-700 mb-1.5">
                                Mata Kuliah Target <span class="text-red-500">*</span>
                            </label>
                            <select id="mhsMatkul" name="course_id" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" required {{ $enrollableCourses->isEmpty() ? 'disabled' : '' }}>
                                @forelse ($enrollableCourses as $c)
                                    <option value="{{ $c->id }}" data-code="{{ $c->code }}" {{ (string) old('course_id') === (string) $c->id ? 'selected' : '' }}>
                                        {{ $c->code }} - {{ $c->name }}
                                    </option>
                                @empty
                                    <option value="">Belum ada mata kuliah yang diampu</option>
                                @endforelse
                            </select>
                        </div>

                        <div>
                            <label for="mhsNim" class="block text-xs font-black text-slate-700 mb-1.5">
                                Nomor Induk Mahasiswa (NIM) <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="mhsNim"
                                   name="nim"
                                   value="{{ old('nim') }}"
                                   class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400"
                                   placeholder="Ketik NIM mahasiswa, cth: 10241001"
                                   required
                                   autocomplete="off">
                            <small id="nimLookupStatus" class="block text-[11px] font-bold text-slate-500 mt-1.5">
                                Masukkan NIM untuk melengkapi nama secara otomatis
                            </small>
                        </div>

                        <div>
                            <label for="mhsNama" class="block text-xs font-black text-slate-700 mb-1.5">
                                Nama Lengkap Mahasiswa <span class="text-red-500">*</span>
                                <span class="text-[11px] font-normal text-slate-500">(Otomatis terisi dari identitas NIM)</span>
                            </label>
                            <input type="text"
                                   id="mhsNama"
                                   class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl cursor-not-allowed"
                                   placeholder="Nama otomatis terisi sesuai NIM..."
                                   readonly
                                   required>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button"
                                class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition cursor-pointer"
                                id="btnCancelStudentModal">
                            Batal
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-xs shadow-sky-500/20 transition cursor-pointer">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Daftarkan Mahasiswa
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <x-footer />
    </div>

    <!-- Toast Notification Container -->
    <div class="fixed bottom-5 right-5 z-[99999] flex flex-col gap-2.5 max-w-sm pointer-events-none" id="toastContainer"></div>
    <script type="application/json" id="registeredStudentsData">@json($registeredStudents)</script>
    <div id="dosenMahasiswaConfig" style="display:none;" data-toast-success="{{ session('success') }}" data-toast-error="{{ $errors->first() }}" data-has-errors="{{ $errors->any() ? '1' : '0' }}"></div>
</body>

</html>
