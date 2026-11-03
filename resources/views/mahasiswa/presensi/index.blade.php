<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Perkuliahan — Portal Mahasiswa KampusLMS</title>

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

        <!-- 1. Navbar Mahasiswa -->
        <x-layout />

        <!-- 2. Konten Utama Presensi Mahasiswa -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex flex-col gap-6 box-border">

            <!-- Topbar Header -->
            <header class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                <div class="flex items-center gap-4">
                    <div class="flex flex-col gap-1">
                        <span class="inline-flex items-center gap-1.5 w-fit text-[11px] font-black tracking-wider uppercase px-2.5 py-0.5 rounded-full border border-sky-400/30 bg-white text-sky-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            PORTAL MAHASISWA &bull; PRESENSI
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                            <span class="inline-block w-1.5 h-6 sm:h-7 rounded-full bg-gradient-to-b from-sky-500 to-sky-400"></span>
                            Presensi Kehadiran Kelas
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 font-semibold">
                            Masukkan kode presensi unik yang diberikan oleh dosen pengampu di kelas Anda.
                        </p>
                    </div>
                </div>
            </header>

            @if (session('success'))
                <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl p-4 font-bold text-xs sm:text-sm flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-rose-50 text-rose-800 border border-rose-200 rounded-xl p-4 font-bold text-xs sm:text-sm flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <div>
                        @foreach ($errors->all() as $err)
                            <div>{{ $err }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- HERO CARD: FORM INPUT KODE UNIK PRESENSI -->
            <div class="bg-gradient-to-r from-sky-600 via-sky-500 to-sky-400 rounded-2xl p-6 sm:p-8 text-white shadow-lg shadow-sky-500/20 relative overflow-hidden">
                <div class="max-w-xl">
                    <span class="text-xs font-black uppercase tracking-wider opacity-90">
                        Portal Kehadiran Realtime
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black mt-1 mb-2 text-white">
                        Punya Kode Presensi Hari Ini?
                    </h2>
                    <p class="text-xs sm:text-sm opacity-90 leading-relaxed">
                        Setiap kode presensi memiliki batas masa berlaku tertentu. Pastikan Anda memasukkan kode sebelum sesi ditutup oleh dosen.
                    </p>
                </div>

                <div class="bg-white/20 backdrop-blur-md border-2 border-dashed border-white/60 rounded-2xl p-5 sm:p-6 mt-6 max-w-lg">
                    <form method="POST" action="{{ route('mahasiswa.presensi.store') }}">
                        @csrf
                        <label for="code" class="block text-xs font-black uppercase tracking-wider text-white mb-2 text-center">
                            Masukkan Kode Unik:
                        </label>

                        <input type="text" name="code" id="code" placeholder="CONTOH: 8K9P2X" maxlength="16" autocomplete="off" autofocus required value="{{ old('code') }}" class="w-full font-mono text-2xl sm:text-3xl font-black text-center tracking-widest uppercase py-3 px-4 rounded-xl border-2 border-white/80 bg-white text-slate-900 shadow-inner outline-none focus:ring-4 focus:ring-white/40">

                        <button type="submit" class="w-full mt-3 py-3 px-6 rounded-xl font-black text-amber-950 bg-amber-400 hover:bg-amber-300 shadow-md transition-all active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M9 11l3 3L22 4"></path>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                            Kirim &amp; Konfirmasi Kehadiran
                        </button>
                    </form>
                </div>
            </div>

            <!-- SESI PRESENSI AKTIF DI KELAS YANG DIIKUTI -->
            <div class="flex flex-col gap-3">
                <h3 class="text-base sm:text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    Sesi Presensi yang Sedang Berlangsung di Kelas Anda
                </h3>

                @if ($activeSessions->isNotEmpty())
                    @foreach ($activeSessions as $active)
                        @php
                            $alreadyAttended = in_array($active->id, $attendedSessionIds);
                        @endphp
                        <div class="bg-white rounded-xl border border-slate-200/80 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs hover:border-sky-300 transition-all">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-black bg-sky-100 text-sky-700 px-2 py-0.5 rounded-md border border-sky-200">
                                        {{ $active->course->code }}
                                    </span>
                                    <span class="text-sm sm:text-base font-black text-slate-900">
                                        {{ $active->course->name }}
                                    </span>
                                </div>
                                <div class="text-sm font-bold text-slate-800 mt-1">
                                    {{ $active->title }}
                                </div>
                                <div class="text-xs text-slate-500 font-semibold mt-1">
                                    Dosen: {{ $active->course->lecturer?->name ?? 'Dosen Pengampu' }} &bull;
                                    Masa berlaku s/d <strong>{{ $active->expires_at->format('H:i') }} WIB</strong> ({{ $active->remainingTimeForHumans() }})
                                </div>
                            </div>

                            <div>
                                @if ($alreadyAttended)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800">
                                        <svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        Anda Sudah Hadir
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black bg-amber-100 text-amber-800 border border-amber-200">
                                        <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                        </svg>
                                        Belum Hadir &bull; Minta Kode ke Dosen
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="bg-white border border-dashed border-slate-200 rounded-2xl p-6 sm:p-8 text-center text-slate-500 flex flex-col items-center gap-2">
                        <svg class="w-9 h-9 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <div class="font-extrabold text-sm text-slate-700">Tidak ada sesi presensi yang sedang aktif saat ini.</div>
                        <div class="text-xs text-slate-400">Sesi presensi akan muncul saat dosen pengampu membuka presensi di perkuliahan.</div>
                    </div>
                @endif
            </div>

            <!-- RIWAYAT PRESENSI MAHASISWA -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        Riwayat Kehadiran Anda
                    </h2>
                    <span class="text-xs font-bold text-slate-500">
                        Total: {{ $attendanceHistory->count() }} Kali Presensi
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead>
                            <tr class="bg-slate-50/80 text-slate-600 uppercase font-black tracking-wider text-[11px] border-b border-slate-200">
                                <th class="p-4">Mata Kuliah</th>
                                <th class="p-4">Pertemuan / Sesi</th>
                                <th class="p-4">Waktu Kehadiran</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($attendanceHistory as $att)
                                @php
                                    $badgeClass = match($att->status) {
                                        'hadir' => 'bg-emerald-100 text-emerald-800',
                                        'izin' => 'bg-sky-100 text-sky-800',
                                        'sakit' => 'bg-amber-100 text-amber-800',
                                        default => 'bg-rose-100 text-rose-800',
                                    };
                                @endphp
                                <tr class="border-b border-slate-100 hover:bg-slate-50/60 transition-colors">
                                    <td class="p-4">
                                        <div class="font-black text-slate-900">
                                            {{ $att->session?->course?->code }} &bull; {{ $att->session?->course?->name }}
                                        </div>
                                        <div class="text-xs text-slate-500 font-semibold mt-0.5">
                                            Dosen: {{ $att->session?->course?->lecturer?->name ?? 'Dosen Pengampu' }}
                                        </div>
                                    </td>
                                    <td class="p-4 font-bold text-slate-800">
                                        {{ $att->session?->title ?? '-' }}
                                    </td>
                                    <td class="p-4 text-slate-600 font-semibold whitespace-nowrap">
                                        {{ $att->attended_at ? $att->attended_at->format('d/m/Y H:i') . ' WIB' : '-' }}
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-black uppercase {{ $badgeClass }}">
                                            {{ strtoupper($att->status) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-slate-500 font-medium">
                                        {{ $att->notes ?: 'Tercatat melalui sistem' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-slate-400">
                                        <div class="font-extrabold text-sm text-slate-700 mb-1">Belum ada riwayat kehadiran yang tercatat.</div>
                                        <div class="text-xs">Masukkan kode presensi dari dosen pada form di atas saat perkuliahan berlangsung.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </main>

        <x-footer />
    </div>

</body>

</html>
