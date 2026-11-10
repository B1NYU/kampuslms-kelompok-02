<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Perkuliahan — Portal Dosen KampusLMS</title>

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

    <div class="min-h-screen flex flex-col justify-between relative z-10">

        <!-- 1. Navbar Dosen -->
        <x-navbar-dosen />

        <!-- 2. Konten Utama Presensi Dosen -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex flex-col gap-6 box-border">

            <!-- Topbar Header -->
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span class="inline-block w-1.5 h-6 sm:h-7 rounded-full bg-gradient-to-b from-sky-500 to-sky-400"></span>
                        Presensi Perkuliahan Berbasis Kode Unik
                    </h1>
                </div>

                <!-- Tombol Buka Sesi Baru -->
                <button type="button"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-xs shadow-sky-500/20 transition cursor-pointer self-start sm:self-auto"
                        onclick="openCreateModal()">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Buka Sesi Presensi Baru</span>
                </button>
            </header>

            @php
                $activeSessionsCount = $sessions->filter(fn ($s) => $s->isOpen())->count();
                $totalSessionsCount = $sessions->count();
                $totalAttendeesCount = $sessions->sum('attendances_count');
                $selectedCourse = $courses->firstWhere('id', $selectedCourseId);
            @endphp

            <!-- Statistik Singkat (3 Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl border border-sky-100 p-5 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-black text-slate-900 leading-none">{{ $totalSessionsCount }}</div>
                        <div class="text-xs font-bold text-slate-500 mt-1">Total Sesi Presensi</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-emerald-100 p-5 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-black text-emerald-600 leading-none">{{ $activeSessionsCount }}</div>
                        <div class="text-xs font-bold text-slate-500 mt-1">Sesi Sedang Aktif</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-purple-100 p-5 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-black text-purple-600 leading-none">{{ $totalAttendeesCount }}</div>
                        <div class="text-xs font-bold text-slate-500 mt-1">Total Mahasiswa Hadir</div>
                    </div>
                </div>
            </div>

            <!-- Filter Mata Kuliah & Informasi -->
            <div class="bg-white rounded-2xl border border-sky-100 p-4 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="text-xs font-bold text-slate-500">Pilih Mata Kuliah:</span>
                    <form method="GET" action="{{ route('dosen.presensi.index') }}" id="filterForm">
                        <select name="course_id"
                                class="text-xs font-bold text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 cursor-pointer focus:outline-none focus:ring-2 focus:ring-sky-400"
                                onchange="document.getElementById('filterForm').submit()">
                            @foreach ($courses as $c)
                                <option value="{{ $c->id }}" {{ (string)$selectedCourseId === (string)$c->id ? 'selected' : '' }}>
                                    {{ $c->code }} — {{ $c->name }} ({{ $c->students_count }} Mhs)
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="text-xs font-bold text-slate-500">
                    Menampilkan <strong class="text-slate-800">{{ $sessions->count() }}</strong> sesi presensi
                </div>
            </div>

            <!-- Tabel Daftar Sesi Presensi -->
            <div class="bg-white rounded-2xl border border-sky-100 shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h2 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                            <path d="M9 11l3 3L22 4"></path>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                        Daftar Riwayat Sesi Presensi Kelas
                    </h2>
                </div>

                <div class="w-full overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-black uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-3.5">Pertemuan / Sesi</th>
                                <th class="px-5 py-3.5">Kode Unik</th>
                                <th class="px-5 py-3.5">Waktu &amp; Masa Berlaku</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5">Kehadiran</th>
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="presensiDosenTbody" class="divide-y divide-slate-100">
                            @forelse ($sessions as $session)
                                @php
                                    $isOpen = $session->isOpen();
                                    $isExpired = $session->isExpired();
                                    $enrolledCount = $session->course->students_count ?? $session->course->students()->count();
                                @endphp
                                <tr class="hover:bg-sky-50/30 transition">
                                    <td class="px-5 py-4">
                                        <div class="font-extrabold text-slate-900 text-sm">
                                            {{ $session->title }}
                                        </div>
                                        <div class="text-[11px] font-semibold text-slate-500 mt-0.5">
                                            {{ $session->course->code }} &bull; {{ $session->course->name }}
                                        </div>
                                        @if ($session->description)
                                            <div class="text-[11px] text-slate-400 mt-1 italic line-clamp-1">
                                                "{{ Str::limit($session->description, 50) }}"
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if ($isOpen)
                                            <span class="font-mono text-xs sm:text-sm font-black tracking-widest px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-dashed border-emerald-300 inline-block" title="Kode presensi mahasiswa">
                                                {{ $session->code }}
                                            </span>
                                        @elseif ($isExpired)
                                            <span class="font-mono text-xs sm:text-sm font-black tracking-widest px-2.5 py-1 rounded-lg bg-red-50 text-red-700 border border-dashed border-red-300 inline-block" title="Kode presensi kedaluwarsa">
                                                {{ $session->code }}
                                            </span>
                                        @else
                                            <span class="font-mono text-xs sm:text-sm font-black tracking-widest px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 border border-dashed border-slate-300 inline-block" title="Kode presensi ditutup">
                                                {{ $session->code }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-xs font-black text-slate-800">
                                            {{ $session->start_time->format('d/m/Y') }} ({{ $session->start_time->format('H:i') }} - {{ $session->expires_at->format('H:i') }})
                                        </div>
                                        <div class="text-[11px] font-bold mt-0.5 {{ $isOpen ? 'text-emerald-600' : ($isExpired ? 'text-red-600' : 'text-slate-500') }}">
                                            {{ $session->remainingTimeForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        @if ($isOpen)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Aktif
                                            </span>
                                        @elseif ($isExpired)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-red-50 text-red-700 border border-red-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                Kedaluwarsa
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-slate-100 text-slate-600 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                Ditutup
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-baseline gap-1 font-bold">
                                            <span class="text-sm font-black text-slate-900">{{ $session->attendances_count }}</span>
                                            <span class="text-xs text-slate-400">/ {{ $enrolledCount }} mhs</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5 justify-end">
                                            <a href="{{ route('dosen.presensi.show', $session) }}"
                                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-white bg-sky-500 hover:bg-sky-600 transition shadow-xs"
                                               title="Buka Detail &amp; Daftar Kehadiran">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                <span>Detail</span>
                                            </a>

                                            <!-- Toggle Tutup/Buka -->
                                            <form method="POST" action="{{ route('dosen.presensi.toggle', $session) }}" class="inline m-0">
                                                @csrf
                                                @if ($session->is_active)
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition cursor-pointer" title="Tutup Sesi Sekarang">
                                                        Tutup
                                                    </button>
                                                @else
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 transition cursor-pointer" title="Buka Kembali Sesi">
                                                        Buka
                                                    </button>
                                                @endif
                                            </form>

                                            <!-- Hapus -->
                                            <form method="POST" action="{{ route('dosen.presensi.destroy', $session) }}" class="inline m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sesi presensi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700 border border-transparent hover:border-red-200 transition cursor-pointer" title="Hapus Sesi">
                                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-12 px-4 text-slate-400 font-bold">
                                        <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <div class="text-sm font-black text-slate-800">Belum ada sesi presensi untuk mata kuliah ini.</div>
                                        <div class="text-xs text-slate-400 mt-1">Klik tombol "Buka Sesi Presensi Baru" di atas untuk membuat sesi presensi.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Navigasi Pagination Laravel --}}
                @if ($sessions->hasPages())
                    <div class="px-5 py-3 border-t border-slate-100 bg-white flex items-center justify-between">
                        {{ $sessions->links() }}
                    </div>
                @endif
            </div>

        </main>

        <x-footer />
    </div>

    <!-- Modal Buka Sesi Presensi Baru -->
    <div class="modal-overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs items-center justify-center p-4 transition-all duration-200 hidden [&.open]:flex" id="createModal">
        <div class="w-full max-w-[540px] bg-white rounded-2xl shadow-2xl border border-sky-100 overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-sm font-black text-slate-900">Buka Sesi Presensi Baru</h3>
                <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-700 text-xl font-black cursor-pointer leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('dosen.presensi.store') }}" class="m-0 flex flex-col">
                @csrf
                <div class="p-6 flex flex-col gap-4 max-h-[70vh] overflow-y-auto">
                    <div>
                        <label for="course_id" class="block text-xs font-black text-slate-700 mb-1.5">Mata Kuliah <span class="text-red-500">*</span></label>
                        <select name="course_id" id="course_id" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" required>
                            @foreach ($courses as $c)
                                <option value="{{ $c->id }}" {{ (string)$selectedCourseId === (string)$c->id ? 'selected' : '' }}>
                                    {{ $c->code }} — {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="title" class="block text-xs font-black text-slate-700 mb-1.5">Judul / Topik Pertemuan <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" placeholder="Contoh: Pertemuan 1 — Kontrak Perkuliahan" required>
                    </div>

                    <div>
                        <label for="code" class="block text-xs font-black text-slate-700 mb-1.5">Kode Unik Presensi</label>
                        <div class="flex gap-2">
                            <input type="text" name="code" id="code" class="flex-1 px-3.5 py-2.5 text-xs sm:text-sm font-mono font-black tracking-widest text-slate-800 bg-white border border-slate-200 rounded-xl uppercase focus:outline-none focus:ring-2 focus:ring-sky-400" value="{{ $generatedCode }}" maxlength="16" placeholder="Kode Unik">
                            <button type="button" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-sky-600 bg-sky-50 hover:bg-sky-100 border border-sky-200 transition cursor-pointer shrink-0" onclick="generateNewCode()" title="Acak kode baru">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <polyline points="23 4 23 10 17 10"></polyline>
                                    <polyline points="1 20 1 14 7 14"></polyline>
                                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                                </svg>
                                <span>Acak</span>
                            </button>
                        </div>
                        <small class="block text-[11px] font-semibold text-slate-400 mt-1">
                            Mahasiswa wajib memasukkan kode ini untuk mencatat kehadiran.
                        </small>
                    </div>

                    <div>
                        <label for="duration_minutes" class="block text-xs font-black text-slate-700 mb-1.5">Masa Berlaku Presensi <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-4 gap-2 mb-2">
                            <button type="button" class="duration-preset px-2.5 py-1.5 text-xs font-bold rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition cursor-pointer text-center" onclick="setDuration(15, this)">15 Menit</button>
                            <button type="button" class="duration-preset px-2.5 py-1.5 text-xs font-bold rounded-xl border border-sky-300 bg-sky-50 text-sky-700 transition cursor-pointer text-center" onclick="setDuration(30, this)">30 Menit</button>
                            <button type="button" class="duration-preset px-2.5 py-1.5 text-xs font-bold rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition cursor-pointer text-center" onclick="setDuration(45, this)">45 Menit</button>
                            <button type="button" class="duration-preset px-2.5 py-1.5 text-xs font-bold rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition cursor-pointer text-center" onclick="setDuration(60, this)">60 Menit</button>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="number" name="duration_minutes" id="duration_minutes" class="w-28 px-3.5 py-2 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" value="30" min="1" max="1440" required>
                            <span class="text-xs font-bold text-slate-500">Menit sejak dibuka</span>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-black text-slate-700 mb-1.5">Catatan Tambahan (Opsional)</label>
                        <textarea name="description" id="description" class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-400" rows="2" placeholder="Catatan instruksi untuk mahasiswa..."></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition cursor-pointer" onclick="closeCreateModal()">Batal</button>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-xs shadow-sky-500/20 transition cursor-pointer">
                        Buka Sesi Presensi Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('createModal').classList.add('open');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.remove('open');
        }

        function generateNewCode() {
            const chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
            let res = '';
            for (let i = 0; i < 6; i++) {
                res += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('code').value = res;
        }

        function setDuration(mins, btn) {
            document.getElementById('duration_minutes').value = mins;
            document.querySelectorAll('.duration-preset').forEach(b => {
                b.className = 'duration-preset px-2.5 py-1.5 text-xs font-bold rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition cursor-pointer text-center';
            });
            btn.className = 'duration-preset px-2.5 py-1.5 text-xs font-bold rounded-xl border border-sky-300 bg-sky-50 text-sky-700 transition cursor-pointer text-center';
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeCreateModal();
        });
    </script>

</body>

</html>
