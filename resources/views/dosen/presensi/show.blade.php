<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $session->title }} — Presensi Kelas KampusLMS</title>

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

        <!-- [RESPONSIVE-MOBILE] Konten Utama Detail Presensi (Responsif Mobile & Desktop) -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8 flex flex-col gap-4 sm:gap-6 box-border">

            <!-- Breadcrumb Navigation -->
            <div>
                <a href="{{ route('dosen.presensi.index', ['course_id' => $course->id]) }}"
                   class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-sky-600 hover:text-sky-700 transition">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Kembali ke Daftar Presensi ({{ $course->code }})</span>
                </a>
            </div>

            @if (session('success'))
                <div class="p-4 rounded-xl text-sm font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-xl text-sm font-bold text-rose-800 bg-rose-50 border border-rose-200 flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <div class="space-y-1">
                        @foreach ($errors->all() as $err)
                            <div>{{ $err }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @php
                $isOpen = $session->isOpen();
                $isExpired = $session->isExpired();
            @endphp

            <!-- Card Utama: Showcase Kode Unik Presensi -->
            <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl p-6 sm:p-8 text-white bg-gradient-to-br from-sky-600 via-sky-500 to-sky-400 shadow-xl shadow-sky-500/25">
                <!-- Background Accent Glow Shape -->
                <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full bg-white/10 pointer-events-none"></div>

                <div class="relative z-10 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div>
                        <div class="text-xs sm:text-sm font-bold uppercase tracking-wider text-sky-100">
                            {{ $course->code }} &bull; {{ $course->name }} ({{ $course->sks }} SKS)
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black text-white mt-1 mb-2 tracking-tight">
                            {{ $session->title }}
                        </h1>
                        @if ($session->description)
                            <div class="text-xs sm:text-sm text-sky-50/90 max-w-2xl leading-relaxed">
                                {{ $session->description }}
                            </div>
                        @endif
                    </div>

                    <div>
                        @if ($isOpen)
                            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-white/20 text-white border border-white/30 backdrop-blur-xs">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 ring-2 ring-emerald-300/50 animate-pulse"></span>
                                Sesi Aktif
                            </span>
                        @elseif ($isExpired)
                            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-200 shadow-xs">
                                <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                                Masa Berlaku Habis
                            </span>
                        @else
                            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200 shadow-xs">
                                <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                                Sesi Ditutup
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Bagian Kode Besar & Salin -->
                <div class="relative z-10 mt-5 flex items-center flex-wrap gap-4">
                    <div class="bg-white/15 backdrop-blur-md border-2 border-dashed border-white/60 rounded-2xl px-6 sm:px-8 py-3 sm:py-4 inline-flex items-center gap-4">
                        <span class="font-mono text-3xl sm:text-5xl font-black tracking-widest text-white drop-shadow-sm select-all" id="presensiCodeVal">{{ $session->code }}</span>
                    </div>

                    <button type="button"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-extrabold text-xs sm:text-sm text-sky-600 bg-white hover:bg-sky-50 active:scale-95 shadow-md shadow-sky-950/10 transition cursor-pointer"
                            onclick="copyCode('{{ $session->code }}')">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        <span id="copyBtnText">Salin Kode</span>
                    </button>
                </div>

                <!-- Info Masa Berlaku & Aksi Cepat Dosen -->
                <div class="relative z-10 mt-6 pt-5 border-t border-white/20 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="text-sm font-semibold text-white">
                        <div>Waktu Presensi: <strong>{{ $session->start_time->format('H:i') }} &ndash; {{ $session->expires_at->format('H:i') }} WIB</strong> ({{ $session->start_time->format('d M Y') }})</div>
                        <div class="text-xs sm:text-sm text-sky-100 mt-0.5">
                            Status waktu: <strong>{{ $session->remainingTimeForHumans() }}</strong>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 flex-wrap">
                        <!-- Perpanjang +15 Menit -->
                        <form method="POST" action="{{ route('dosen.presensi.extend', $session) }}" class="inline">
                            @csrf
                            <input type="hidden" name="minutes" value="15">
                            <button type="submit" class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-white bg-white/20 hover:bg-white/30 border border-white/40 backdrop-blur-xs transition cursor-pointer">
                                +15 Menit
                            </button>
                        </form>

                        <!-- Perpanjang +30 Menit -->
                        <form method="POST" action="{{ route('dosen.presensi.extend', $session) }}" class="inline">
                            @csrf
                            <input type="hidden" name="minutes" value="30">
                            <button type="submit" class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-white bg-white/20 hover:bg-white/30 border border-white/40 backdrop-blur-xs transition cursor-pointer">
                                +30 Menit
                            </button>
                        </form>

                        <!-- Toggle Tutup/Buka Sesi -->
                        <form method="POST" action="{{ route('dosen.presensi.toggle', $session) }}" class="inline">
                            @csrf
                            @if ($session->is_active)
                                <button type="submit" class="px-4 py-2 rounded-xl text-xs sm:text-sm font-extrabold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-950/20 transition cursor-pointer">
                                    Tutup Sesi Sekarang
                                </button>
                            @else
                                <button type="submit" class="px-4 py-2 rounded-xl text-xs sm:text-sm font-extrabold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-950/20 transition cursor-pointer">
                                    Buka Kembali Sesi
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <!-- Statistik Kehadiran -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-sky-100 shadow-xs">
                    <div class="text-2xl sm:text-3xl font-black text-slate-900 leading-none">{{ $totalEnrolled }}</div>
                    <div class="text-xs sm:text-sm font-bold text-slate-500 mt-2">Total Mahasiswa di Kelas</div>
                </div>
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-sky-100 shadow-xs">
                    <div class="text-2xl sm:text-3xl font-black text-emerald-600 leading-none">{{ $totalAttended }}</div>
                    <div class="text-xs sm:text-sm font-bold text-slate-500 mt-2">Sudah Melakukan Presensi</div>
                </div>
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-sky-100 shadow-xs">
                    <div class="text-2xl sm:text-3xl font-black text-amber-500 leading-none">{{ count($unattendedList) }}</div>
                    <div class="text-xs sm:text-sm font-bold text-slate-500 mt-2">Belum Hadir</div>
                </div>
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-sky-100 shadow-xs">
                    <div class="text-2xl sm:text-3xl font-black text-sky-500 leading-none">{{ $percentAttended }}%</div>
                    <div class="text-xs sm:text-sm font-bold text-slate-500 mt-2">Tingkat Kehadiran Kelas</div>
                </div>
            </div>

            <!-- Nav Tabs Hadir vs Belum Hadir -->
            <div class="bg-white rounded-2xl border border-sky-100 shadow-xs overflow-hidden flex flex-col">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 bg-slate-50/70 px-4 sm:px-6">
                    <div class="flex gap-2 sm:gap-4 overflow-x-auto">
                        <button type="button"
                                class="py-3 sm:py-3.5 px-3 sm:px-4 text-xs sm:text-sm font-extrabold border-b-2 text-sky-600 border-sky-500 cursor-pointer transition whitespace-nowrap"
                                id="tabBtnHadir"
                                onclick="switchTab('hadir')">
                            Mahasiswa Hadir ({{ count($attendedList) }})
                        </button>
                        <button type="button"
                                class="py-3 sm:py-3.5 px-3 sm:px-4 text-xs sm:text-sm font-bold border-b-2 text-slate-500 border-transparent hover:text-slate-700 cursor-pointer transition whitespace-nowrap"
                                id="tabBtnBelum"
                                onclick="switchTab('belum')">
                            Belum Presensi ({{ count($unattendedList) }})
                        </button>
                    </div>

                    <div class="py-2 sm:py-0 text-xs sm:text-sm text-slate-500 font-semibold">
                        Presensi Kelas: <strong class="text-slate-700">{{ $session->title }}</strong>
                    </div>
                </div>

                <!-- TAB 1: MAHASISWA HADIR -->
                <div id="panelHadir" class="block">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 text-xs font-black uppercase tracking-wider">
                                    <th class="px-4 sm:px-6 py-3.5">No</th>
                                    <th class="px-4 sm:px-6 py-3.5">NIM</th>
                                    <th class="px-4 sm:px-6 py-3.5">Nama Mahasiswa</th>
                                    <th class="px-4 sm:px-6 py-3.5">Waktu Hadir</th>
                                    <th class="px-4 sm:px-6 py-3.5">Status</th>
                                    <th class="px-4 sm:px-6 py-3.5">Keterangan</th>
                                    <th class="px-4 sm:px-6 py-3.5 text-right">Ubah Status Manual</th>
                                </tr>
                            </thead>
                            <tbody id="attendedTbody" class="divide-y divide-slate-100">
                                @forelse ($attendedList as $idx => $row)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-4 sm:px-6 py-3.5 font-bold text-slate-400 text-xs sm:text-sm">{{ $idx + 1 }}</td>
                                        <td class="px-4 sm:px-6 py-3.5 font-mono font-bold text-slate-900 text-xs sm:text-sm">
                                            {{ $row['student']->nim_nip ?: '-' }}
                                        </td>
                                        <td class="px-4 sm:px-6 py-3.5 font-extrabold text-slate-900 text-xs sm:text-sm">
                                            {{ $row['student']->name }}
                                        </td>
                                        <td class="px-4 sm:px-6 py-3.5 text-slate-600 font-semibold text-xs sm:text-sm">
                                            {{ $row['attended_at'] ? $row['attended_at']->format('H:i:s') . ' WIB' : '-' }}
                                        </td>
                                        <td class="px-4 sm:px-6 py-3.5">
                                            @php
                                                $statusClasses = [
                                                    'hadir' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                    'izin' => 'bg-sky-100 text-sky-800 border-sky-200',
                                                    'sakit' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                    'alpa' => 'bg-rose-100 text-rose-800 border-rose-200',
                                                ][$row['status']] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black uppercase border {{ $statusClasses }}">
                                                {{ strtoupper($row['status']) }}
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3.5 text-xs text-slate-500">
                                            {{ $row['notes'] ?: 'Melalui kode unik' }}
                                        </td>
                                        <td class="px-4 sm:px-6 py-3.5 text-right">
                                            <form method="POST" action="{{ route('dosen.presensi.manual', $session) }}" class="inline-flex items-center gap-1.5 justify-end">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $row['student']->id }}">
                                                <select name="status" onchange="this.form.submit()" class="text-xs font-bold px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-700 hover:border-sky-400 focus:outline-hidden focus:ring-2 focus:ring-sky-500/20 cursor-pointer">
                                                    <option value="hadir" {{ $row['status'] === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                                    <option value="izin" {{ $row['status'] === 'izin' ? 'selected' : '' }}>Izin</option>
                                                    <option value="sakit" {{ $row['status'] === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                                    <option value="alpa" {{ $row['status'] === 'alpa' ? 'selected' : '' }}>Alpa</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                            <div class="text-sm sm:text-base font-bold text-slate-600">Belum ada mahasiswa yang memasukkan kode presensi.</div>
                                            <div class="text-xs sm:text-sm mt-1">Bagikan kode unik di atas kepada mahasiswa Anda.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginator Client-side --}}
                    <div class="px-4 sm:px-6 py-3 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <span id="attendedPaginatorInfo" class="text-xs font-bold text-slate-500"></span>
                        <div id="attendedPaginator" class="flex items-center gap-1.5 flex-wrap"></div>
                    </div>
                </div>

                <!-- TAB 2: MAHASISWA BELUM HADIR -->
                <div id="panelBelum" class="hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 text-xs font-black uppercase tracking-wider">
                                    <th class="px-4 sm:px-6 py-3.5">No</th>
                                    <th class="px-4 sm:px-6 py-3.5">NIM</th>
                                    <th class="px-4 sm:px-6 py-3.5">Nama Mahasiswa</th>
                                    <th class="px-4 sm:px-6 py-3.5">Status Saat Ini</th>
                                    <th class="px-4 sm:px-6 py-3.5 text-right">Presensi Cepat Manual oleh Dosen</th>
                                </tr>
                            </thead>
                            <tbody id="unattendedTbody" class="divide-y divide-slate-100">
                                @forelse ($unattendedList as $idx => $row)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-4 sm:px-6 py-3.5 font-bold text-slate-400 text-xs sm:text-sm">{{ $idx + 1 }}</td>
                                        <td class="px-4 sm:px-6 py-3.5 font-mono font-bold text-slate-900 text-xs sm:text-sm">
                                            {{ $row['student']->nim_nip ?: '-' }}
                                        </td>
                                        <td class="px-4 sm:px-6 py-3.5 font-extrabold text-slate-900 text-xs sm:text-sm">
                                            {{ $row['student']->name }}
                                        </td>
                                        <td class="px-4 sm:px-6 py-3.5">
                                            <span class="inline-flex items-center text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-md">
                                                Belum Presensi
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3.5 text-right">
                                            <div class="inline-flex items-center gap-1.5 justify-end flex-wrap">
                                                <!-- Tandai Hadir -->
                                                <form method="POST" action="{{ route('dosen.presensi.manual', $session) }}">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $row['student']->id }}">
                                                    <input type="hidden" name="status" value="hadir">
                                                    <input type="hidden" name="notes" value="Ditandai manual oleh dosen">
                                                    <button type="submit" class="px-2.5 py-1 text-xs font-extrabold rounded-md bg-emerald-100 text-emerald-800 border border-emerald-300 hover:bg-emerald-200 transition cursor-pointer">Hadir</button>
                                                </form>

                                                <!-- Tandai Izin -->
                                                <form method="POST" action="{{ route('dosen.presensi.manual', $session) }}">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $row['student']->id }}">
                                                    <input type="hidden" name="status" value="izin">
                                                    <input type="hidden" name="notes" value="Izin dikonfirmasi dosen">
                                                    <button type="submit" class="px-2.5 py-1 text-xs font-extrabold rounded-md bg-sky-100 text-sky-800 border border-sky-300 hover:bg-sky-200 transition cursor-pointer">Izin</button>
                                                </form>

                                                <!-- Tandai Sakit -->
                                                <form method="POST" action="{{ route('dosen.presensi.manual', $session) }}">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $row['student']->id }}">
                                                    <input type="hidden" name="status" value="sakit">
                                                    <input type="hidden" name="notes" value="Sakit dikonfirmasi dosen">
                                                    <button type="submit" class="px-2.5 py-1 text-xs font-extrabold rounded-md bg-amber-100 text-amber-800 border border-amber-300 hover:bg-amber-200 transition cursor-pointer">Sakit</button>
                                                </form>

                                                <!-- Tandai Alpa -->
                                                <form method="POST" action="{{ route('dosen.presensi.manual', $session) }}">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $row['student']->id }}">
                                                    <input type="hidden" name="status" value="alpa">
                                                    <input type="hidden" name="notes" value="Tidak hadir tanpa keterangan">
                                                    <button type="submit" class="px-2.5 py-1 text-xs font-extrabold rounded-md bg-rose-100 text-rose-800 border border-rose-300 hover:bg-rose-200 transition cursor-pointer">Alpa</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-emerald-600">
                                            <svg class="w-10 h-10 mx-auto mb-2 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                            </svg>
                                            <div class="text-sm sm:text-base font-extrabold text-slate-800">Luar biasa! Seluruh mahasiswa di kelas ini telah melakukan presensi.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginator Client-side --}}
                    <div class="px-4 sm:px-6 py-3 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <span id="unattendedPaginatorInfo" class="text-xs font-bold text-slate-500"></span>
                        <div id="unattendedPaginator" class="flex items-center gap-1.5 flex-wrap"></div>
                    </div>
                </div>
            </div>

        </main>

    </div>

    <script>
        function copyCode(code) {
            navigator.clipboard.writeText(code).then(function() {
                const btnText = document.getElementById('copyBtnText');
                const orig = btnText.innerText;
                btnText.innerText = 'Tersalin!';
                setTimeout(() => {
                    btnText.innerText = orig;
                }, 2000);
            }).catch(function() {
                alert('Kode presensi: ' + code);
            });
        }

        function switchTab(tab) {
            const btnHadir = document.getElementById('tabBtnHadir');
            const btnBelum = document.getElementById('tabBtnBelum');
            const panelHadir = document.getElementById('panelHadir');
            const panelBelum = document.getElementById('panelBelum');

            if (tab === 'hadir') {
                btnHadir.className = 'py-3 sm:py-3.5 px-3 sm:px-4 text-xs sm:text-sm font-extrabold border-b-2 text-sky-600 border-sky-500 cursor-pointer transition whitespace-nowrap';
                btnBelum.className = 'py-3 sm:py-3.5 px-3 sm:px-4 text-xs sm:text-sm font-bold border-b-2 text-slate-500 border-transparent hover:text-slate-700 cursor-pointer transition whitespace-nowrap';
                panelHadir.classList.remove('hidden');
                panelHadir.classList.add('block');
                panelBelum.classList.remove('block');
                panelBelum.classList.add('hidden');
                if (window.renderAttended) window.renderAttended();
            } else {
                btnBelum.className = 'py-3 sm:py-3.5 px-3 sm:px-4 text-xs sm:text-sm font-extrabold border-b-2 text-sky-600 border-sky-500 cursor-pointer transition whitespace-nowrap';
                btnHadir.className = 'py-3 sm:py-3.5 px-3 sm:px-4 text-xs sm:text-sm font-bold border-b-2 text-slate-500 border-transparent hover:text-slate-700 cursor-pointer transition whitespace-nowrap';
                panelBelum.classList.remove('hidden');
                panelBelum.classList.add('block');
                panelHadir.classList.remove('block');
                panelHadir.classList.add('hidden');
                if (window.renderUnattended) window.renderUnattended();
            }
        }

        // ── Client-side Pagination — Presensi Show (Dosen) ─────────────────
        (function() {
            function setupPager(tbodyId, paginatorId, infoId, perPage = 10) {
                let currentPage = 1;
                function getRows() {
                    return Array.from(document.querySelectorAll('#' + tbodyId + ' tr'))
                        .filter(r => !r.querySelector('td[colspan]'));
                }
                function render() {
                    const rows = getRows();
                    const total = rows.length;
                    const totalPages = Math.max(1, Math.ceil(total / perPage));
                    if (currentPage > totalPages) currentPage = 1;
                    const start = (currentPage - 1) * perPage;
                    const end = Math.min(start + perPage, total);
                    rows.forEach((r, i) => { r.style.display = (i >= start && i < end) ? '' : 'none'; });
                    const infoEl = document.getElementById(infoId);
                    if (infoEl) infoEl.textContent = total > 0 ? `Menampilkan ${start + 1}–${end} dari ${total} mahasiswa` : '';
                    renderControls(totalPages);
                }
                function goTo(p) {
                    const rows = getRows();
                    const totalPages = Math.max(1, Math.ceil(rows.length / perPage));
                    currentPage = Math.max(1, Math.min(p, totalPages));
                    render();
                }
                function renderControls(totalPages) {
                    const el = document.getElementById(paginatorId);
                    if (!el) return;
                    if (totalPages <= 1) { el.innerHTML = ''; return; }
                    const btnStyle = (active, disabled) =>
                        `style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;font-size:13px;font-weight:700;cursor:${disabled ? 'default' : 'pointer'};border:1px solid ${active ? 'transparent' : (disabled ? '#E2E8F0' : '#CBD5E1')};background:${active ? '#039FFA' : (disabled ? '#F8FAFC' : '#FFF')};color:${active ? '#FFF' : (disabled ? '#CBD5E1' : '#334155')};transition:all .15s;"`;
                    let html = '';
                    const prevDis = currentPage === 1;
                    html += `<button type="button" ${btnStyle(false, prevDis)} data-p="${currentPage - 1}" ${prevDis ? 'disabled' : ''}>&#8249;</button>`;
                    const delta = 2;
                    for (let i = Math.max(1, currentPage - delta); i <= Math.min(totalPages, currentPage + delta); i++) {
                        html += `<button type="button" ${btnStyle(i === currentPage, false)} data-p="${i}">${i}</button>`;
                    }
                    const nextDis = currentPage === totalPages;
                    html += `<button type="button" ${btnStyle(false, nextDis)} data-p="${currentPage + 1}" ${nextDis ? 'disabled' : ''}>&#8250;</button>`;
                    el.innerHTML = html;
                    el.querySelectorAll('button[data-p]').forEach(btn => {
                        if (!btn.disabled) btn.addEventListener('click', () => goTo(Number(btn.dataset.p)));
                    });
                }
                return render;
            }

            window.renderAttended = setupPager('attendedTbody', 'attendedPaginator', 'attendedPaginatorInfo', 10);
            window.renderUnattended = setupPager('unattendedTbody', 'unattendedPaginator', 'unattendedPaginatorInfo', 10);

            document.addEventListener('DOMContentLoaded', () => {
                window.renderAttended();
            });
        })();
        // ──────────────────────────────────────────────────────────────────
    </script>

</body>

</html>
