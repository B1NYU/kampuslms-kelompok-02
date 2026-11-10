<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi — Portal KampusLMS</title>

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

        <!-- [RESPONSIVE-MOBILE] Konten Utama Notifikasi (Responsif Mobile & Desktop) -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8 flex flex-col gap-4 sm:gap-6 box-border">

            <!-- Topbar Header -->
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 pb-2 border-b border-slate-200/80">
                <div class="flex flex-col gap-1 min-w-0">
                    <div class="flex items-center gap-2 text-[11px] sm:text-xs font-black uppercase tracking-wider text-sky-600">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                        KOTAK MASUK
                    </div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-slate-900 tracking-tight">
                        Notifikasi
                    </h1>
                    <p class="text-xs sm:text-sm font-bold text-slate-500">
                        {{ $unreadCount > 0 ? $unreadCount . ' notifikasi belum dibaca' : 'Semua notifikasi sudah dibaca' }}
                    </p>
                </div>

                <div class="flex items-center gap-2.5 shrink-0">
                    @if ($unreadCount > 0)
                        <form method="POST" action="{{ route('notifikasi.baca-semua') }}" class="m-0">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 shadow-xs transition cursor-pointer">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span>Tandai semua sudah dibaca</span>
                            </button>
                        </form>
                    @endif
                </div>
            </header>

            @if (session('success'))
                <div class="p-3.5 sm:p-4 rounded-xl text-xs sm:text-sm font-bold text-emerald-800 bg-emerald-50 border border-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            <section class="bg-white rounded-2xl border border-sky-100 shadow-xs p-5 sm:p-6 flex flex-col gap-5">

                <!-- Tabs Filter -->
                <div class="flex items-center gap-2 flex-wrap pb-4 border-b border-slate-100">
                    <a href="{{ route('notifikasi.index') }}"
                       class="inline-flex items-center px-4 py-2 rounded-xl text-xs sm:text-sm font-extrabold transition {{ $filter === 'semua' ? 'bg-sky-500 text-white shadow-xs shadow-sky-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua
                    </a>
                    <a href="{{ route('notifikasi.index', ['filter' => 'belum']) }}"
                       class="inline-flex items-center px-4 py-2 rounded-xl text-xs sm:text-sm font-extrabold transition {{ $filter === 'belum' ? 'bg-sky-500 text-white shadow-xs shadow-sky-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Belum dibaca{{ $unreadCount > 0 ? ' (' . $unreadCount . ')' : '' }}
                    </a>
                </div>

                @if ($notifications->isEmpty())
                    <div class="py-12 px-4 text-center text-slate-400 font-bold text-xs sm:text-sm">
                        {{ $filter === 'belum' ? 'Tidak ada notifikasi yang belum dibaca.' : 'Belum ada notifikasi.' }}
                    </div>
                @else
                    <ul class="flex flex-col gap-2.5 list-none m-0 p-0">
                        @foreach ($notifications as $n)
                            <li class="m-0 p-0">
                                {{-- Satu POST per baris: menandai dibaca lalu mengarahkan ke tujuan. --}}
                                <form method="POST" action="{{ route('notifikasi.baca', ['id' => $n['id']]) }}" class="m-0">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-start gap-3.5 text-left p-3.5 sm:p-4 rounded-2xl border transition-all cursor-pointer {{ $n['dibaca'] ? 'bg-white border-slate-200/80 hover:bg-slate-50/80' : 'bg-sky-50/40 border-sky-200 hover:bg-sky-50/70 border-l-4 border-l-sky-500' }}">
                                        <span class="w-2.5 h-2.5 rounded-full mt-1.5 shrink-0 {{ $n['dibaca'] ? 'bg-transparent' : 'bg-sky-500' }}" aria-hidden="true"></span>
                                        <div class="flex-1 min-w-0">
                                            <span class="block text-xs sm:text-sm leading-snug {{ $n['dibaca'] ? 'font-bold text-slate-700' : 'font-black text-slate-900' }}">
                                                {{ $n['message'] }}
                                            </span>
                                            <span class="block text-[11px] sm:text-xs font-semibold text-slate-500 mt-1">
                                                {{ $n['waktu'] }} &middot; {{ $n['time'] }}
                                                @if ($n['course']) &middot; <strong class="text-slate-600 font-bold">{{ $n['course'] }}</strong> @endif
                                                @unless ($n['has_url']) &middot; <span class="italic text-slate-400">tanpa tautan</span> @endunless
                                            </span>
                                        </div>
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-slate-100 text-xs font-bold text-slate-500">
                        <div>
                            @if ($notifications->onFirstPage())
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-400 bg-slate-50 border border-slate-200 opacity-60 cursor-not-allowed">
                                    &larr; Sebelumnya
                                </span>
                            @else
                                <a href="{{ $notifications->previousPageUrl() }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs transition">
                                    &larr; Sebelumnya
                                </a>
                            @endif
                        </div>

                        <span>Halaman {{ $notifications->currentPage() }} dari {{ $notifications->lastPage() }}</span>

                        <div>
                            @if ($notifications->hasMorePages())
                                <a href="{{ $notifications->nextPageUrl() }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs transition">
                                    Berikutnya &rarr;
                                </a>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-400 bg-slate-50 border border-slate-200 opacity-60 cursor-not-allowed">
                                    Berikutnya &rarr;
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

            </section>

        </main>

        <x-footer />
    </div>

</body>

</html>
