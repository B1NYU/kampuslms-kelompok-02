<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Mahasiswa — KampusLMS Admin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/admin.pendaftaran.js'])
    @endif
    <style>
        #enrollModalOverlay { display: none; }
        #enrollModalOverlay.active { display: flex; }
        .btn-filter-mk.active {
            background-color: #039FFA !important;
            color: #ffffff !important;
            border-color: #039FFA !important;
            box-shadow: 0 2px 8px rgba(3, 159, 250, 0.25) !important;
        }
        .btn-filter-mk.active span:last-child {
            background-color: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
        }
    </style>
</head>
<body class="bg-[#F1F5F9] min-h-screen text-slate-800 antialiased font-['Nunito',sans-serif] relative overflow-x-hidden">

    <!-- Soft Decorative Atmospheric Glow -->
    <div class="fixed top-[-80px] right-[-80px] w-96 h-96 bg-sky-400/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed bottom-[-80px] left-[-80px] w-96 h-96 bg-amber-400/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed top-1/2 right-1/4 w-72 h-72 bg-orange-400/10 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <div class="min-h-screen flex flex-col justify-between relative z-10">

        <!-- Navbar Admin -->
        <x-navbar-admin />

        <!-- Konten Utama Pendaftaran Mahasiswa -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex flex-col gap-6 box-border">

            <!-- Topbar Header -->
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Pendaftaran Mahasiswa</h1>
                    </div>
                </div>
                <div>
                    <button type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-md shadow-sky-500/20 hover:shadow-lg hover:shadow-sky-500/30 hover:-translate-y-0.5 active:translate-y-0 transition-all cursor-pointer" id="btnOpenEnrollModal">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Daftarkan Mahasiswa</span>
                    </button>
                </div>
            </header>

            {{-- Pesan hasil aksi --}}
            @if (session('success'))
                <div class="text-xs sm:text-sm font-bold px-4 py-3 rounded-xl shadow-xs bg-emerald-50 text-emerald-700 border border-emerald-200/80 flex items-center gap-2">
                    <span>✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="text-xs sm:text-sm font-bold px-4 py-3 rounded-xl shadow-xs bg-rose-50 text-rose-700 border border-rose-200/80 flex flex-col gap-1">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center gap-2">
                            <span>⚠️</span>
                            <span>{{ $error }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @php
                // $courses, $mahasiswaList, $selectedCode dikirim AdminEnrollmentController.
                $allEnrollments = collect();
                foreach ($courses as $course) {
                    foreach ($course->students as $student) {
                        $allEnrollments->push(['course' => $course, 'student' => $student]);
                    }
                }

                // Hanya MK aktif yang bisa dipilih untuk pendaftaran baru.
                $activeCourses = $courses->where('status', 'active');
            @endphp

            <!-- Card Utama: Tabel Pendaftaran -->
            <section class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">

                <!-- Toolbar Atas: Ringkasan Total & Search Box -->
                <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xs sm:text-sm font-bold text-slate-500">
                            Total <strong id="enrollCount" class="font-black text-slate-900">{{ $allEnrollments->count() }}</strong> Pendaftaran Aktif
                        </span>
                    </div>

                    <!-- Search Input Box -->
                    <div class="relative w-full sm:w-72">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                        <input type="text" id="searchEnrollInput" placeholder="Cari mahasiswa / MK..."
                               class="w-full pl-10 pr-4 py-2 bg-slate-50/80 border border-slate-200/80 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all shadow-xs">
                    </div>
                </div>

                <!-- Filter per Mata Kuliah (Pill Buttons) -->
                <div class="filter-mk-container px-4 sm:px-5 py-3 border-b border-slate-100/80 flex flex-wrap gap-2 items-center bg-slate-50/40">
                    <button type="button" class="btn-filter-mk {{ $selectedCode === 'all' ? 'active' : '' }} inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border border-slate-200/80 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all cursor-pointer" data-mk="all">
                        <span>Semua MK</span>
                        <span class="px-1.5 py-0.5 rounded-md text-[10px] bg-slate-100 text-slate-600">{{ $allEnrollments->count() }}</span>
                    </button>
                    @foreach ($courses as $c)
                        <button type="button" class="btn-filter-mk {{ $selectedCode === $c->code ? 'active' : '' }} inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border border-slate-200/80 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all cursor-pointer" data-mk="{{ $c->code }}">
                            <span>{{ $c->code }}</span>
                            <span class="px-1.5 py-0.5 rounded-md text-[10px] bg-slate-100 text-slate-600">{{ $c->students_count }}</span>
                        </button>
                    @endforeach
                </div>

                <!-- Tabel Pendaftaran Aktif -->
                <div class="overflow-x-auto" style="max-height: 580px; overflow-y: auto;">
                    <table class="w-full text-left border-collapse" id="enrollTable">
                        <thead>
                            <tr class="sticky top-0 z-10 bg-slate-50/95 backdrop-blur-xs border-b border-slate-200 text-[11px] font-black uppercase tracking-wider text-slate-500">
                                <th class="py-3.5 px-5">Mahasiswa</th>
                                <th class="py-3.5 px-5">Mata Kuliah</th>
                                <th class="py-3.5 px-5">Tanggal Daftar</th>
                                <th class="py-3.5 px-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="enrollTableBody" class="divide-y divide-slate-100 text-xs sm:text-sm font-semibold">
                            @forelse ($allEnrollments as $e)
                            @php
                                $mhs   = $e['student'];
                                $mk    = $e['course'];
                                $inits = collect(explode(' ', $mhs->name))->map(fn ($w) => mb_substr($w, 0, 1))->join('');
                                $inits = strtoupper(mb_substr($inits, 0, 2));
                                $tgl   = $mhs->pivot->enrolled_at
                                    ? \Illuminate\Support\Carbon::parse($mhs->pivot->enrolled_at)->translatedFormat('d M Y')
                                    : '-';
                            @endphp
                            <tr data-mk="{{ $mk->code }}" class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs shrink-0 border bg-emerald-50 text-emerald-600 border-emerald-100">
                                            {{ $inits }}
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="font-bold text-slate-900 text-sm leading-snug">{{ $mhs->name }}</span>
                                            <span class="text-[11px] font-semibold text-slate-500">{{ $mhs->nim_nip }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="flex flex-col min-w-0">
                                        <div class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                            <span class="font-mono text-xs px-2 py-0.5 rounded-md bg-sky-50 text-[#039FFA] border border-sky-200/60">{{ $mk->code }}</span>
                                            <span>{{ $mk->name }}</span>
                                        </div>
                                        <small class="text-[11px] font-medium text-slate-400 mt-0.5">Dosen: {{ $mk->lecturer?->name ?? 'Belum ada dosen' }}</small>
                                    </div>
                                </td>
                                <td class="py-3.5 px-5">
                                    <span class="text-xs font-semibold text-slate-600">{{ $tgl }}</span>
                                </td>
                                <td class="py-3.5 px-5 text-right">
                                    <div class="inline-flex items-center justify-end">
                                        @can('manageEnrollment', $mk)
                                            <form method="POST" action="{{ route('admin.pendaftaran.destroy', [$mk, $mhs]) }}" class="inline-block m-0"
                                                  onsubmit="return confirm('Batalkan pendaftaran {{ e($mhs->name) }} dari {{ e($mk->code) }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 rounded-xl border border-slate-200/80 bg-white text-slate-600 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50/60 shadow-xs transition-all cursor-pointer" title="Batalkan pendaftaran">
                                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-12 text-slate-400 font-semibold">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="w-8 h-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <polyline points="16 11 18 13 22 9"></polyline>
                                        </svg>
                                        <span>Belum ada pendaftaran mata kuliah.</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer Ringkasan Kartu -->
                <div class="p-4 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-500">
                    <div class="flex items-center gap-1.5 font-bold text-slate-700">
                        <span>Total:</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-900 text-xs font-black">{{ $allEnrollments->count() }}</span>
                        <span>mahasiswa terdaftar</span>
                    </div>
                </div>
            </section>

        </main>

        <!-- MODAL FORM PENDAFTARAN MK -->
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 {{ $errors->any() ? 'active' : '' }}" id="enrollModalOverlay">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-lg w-full overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                <!-- Modal Header -->
                <div class="px-6 py-4.5 bg-gradient-to-r from-sky-50 via-white to-amber-50/40 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-sky-100/80 text-[#039FFA] shrink-0 border border-sky-200/60">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <polyline points="16 11 18 13 22 9"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900 m-0">Daftarkan Mahasiswa</h3>
                            <p class="text-xs font-semibold text-slate-500 m-0 mt-0.5">Pilih mahasiswa dan mata kuliah aktif</p>
                        </div>
                    </div>
                    <button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer" id="btnCloseEnrollModal" aria-label="Tutup modal">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    @if ($errors->any())
                        <div class="mb-4 text-xs font-bold px-3.5 py-2.5 rounded-xl bg-rose-50 text-rose-700 border border-rose-200">
                            @foreach ($errors->all() as $error)
                                <div class="flex items-center gap-2">
                                    <span>⚠️</span>
                                    <span>{{ $error }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <form id="formEnroll" method="POST" action="{{ route('admin.pendaftaran.store') }}" class="flex flex-col gap-4 m-0">
                        @csrf
                        <div>
                            <label for="enrollMahasiswa" class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Mahasiswa <span class="text-rose-500">*</span></label>
                            <select id="enrollMahasiswa" name="student_id" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all cursor-pointer" required>
                                <option value="">— Pilih Mahasiswa ({{ $mahasiswaList->count() }} Terdaftar) —</option>
                                @foreach ($mahasiswaList as $mhs)
                                    <option value="{{ $mhs->id }}" @selected((int) old('student_id') === $mhs->id)>{{ $mhs->name }} ({{ $mhs->nim_nip }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="enrollMatkul" class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Mata Kuliah <span class="text-rose-500">*</span></label>
                            <select id="enrollMatkul" name="course_id" class="w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-[#039FFA] focus:bg-white transition-all cursor-pointer" required>
                                <option value="">— Pilih Mata Kuliah —</option>
                                @foreach ($activeCourses as $c)
                                    <option value="{{ $c->id }}" @selected((int) old('course_id') === $c->id)>{{ $c->code }} • {{ $c->name }} ({{ $c->sks }} SKS - {{ $c->students_count }} Mhs)</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2.5 mt-2 pt-4 border-t border-slate-100">
                            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-sm cursor-pointer transition-all">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span>Daftarkan ke Mata Kuliah</span>
                            </button>
                            <button type="button" id="btnCancelEnrollModal" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-all cursor-pointer">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <x-footer />
    </div>

    <div id="enrollConfig" style="display:none;" data-selected-code="{{ $selectedCode }}"></div>
</body>
</html>
