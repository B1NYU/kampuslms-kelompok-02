@php
    $isDosen = auth()->user()->role === 'dosen';
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Kata Sandi — Portal KampusLMS</title>

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
        @if ($isDosen)
            <x-navbar-dosen />
        @else
            <x-layout />
        @endif

        <!-- [RESPONSIVE-MOBILE] Form Keamanan & Kata Sandi (Responsif Mobile & Desktop) -->
        <main class="flex-1 w-full max-w-[1360px] mx-auto px-3 sm:px-6 lg:px-8 py-6 sm:py-12 flex flex-col items-center justify-center box-border">
            <div class="w-full max-w-xl mx-auto">
                <section class="bg-white rounded-2xl sm:rounded-3xl border border-sky-100 shadow-xl shadow-sky-950/5 p-4 sm:p-8 relative">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-sky-50 text-sky-700 border border-sky-200 mb-3">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <span>KEAMANAN AKUN</span>
                    </div>

                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                        Ubah Kata Sandi
                    </h2>
                    <p class="text-xs sm:text-sm font-semibold text-slate-500 mt-1 mb-5 leading-relaxed">
                        Untuk keamanan akun Anda, masukkan kata sandi saat ini sebelum menetapkan kata sandi baru.
                    </p>

                    <div class="bg-slate-50/80 border border-slate-200 rounded-2xl p-3 sm:p-3.5 mb-6 flex items-center gap-3">
                        @php
                            $uName = auth()->user()->name ?? 'Pengguna';
                            $uParts = preg_split('/\s+/', trim($uName));
                            $uInit = strtoupper(mb_substr($uParts[0], 0, 1) . mb_substr($uParts[1] ?? '', 0, 1));
                        @endphp
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-500 to-sky-400 text-white flex items-center justify-center text-xs font-black shrink-0 shadow-xs">
                            {{ $uInit }}
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="text-xs sm:text-sm font-black text-slate-900 truncate">{{ $uName }}</span>
                            <span class="text-[11px] font-bold text-slate-500 truncate">
                                {{ auth()->user()->nim_nip ? (auth()->user()->role === 'dosen' ? 'NIP: ' : 'NIM: ') . auth()->user()->nim_nip : ucfirst(auth()->user()->role) }}
                                &bull; {{ auth()->user()->email }}
                            </span>
                        </div>
                    </div>

                    <form action="{{ route('akun.kata-sandi.update') }}" method="POST" novalidate class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="block text-xs sm:text-sm font-black text-slate-800 mb-1.5">
                                Kata Sandi Saat Ini
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-slate-400 pointer-events-none">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </span>
                                <input type="password" id="current_password" name="current_password"
                                       class="w-full h-11 pl-10 pr-11 rounded-xl border bg-slate-50/50 text-xs sm:text-sm font-bold text-slate-800 placeholder:text-slate-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-sky-500/20 focus:border-sky-400 transition @error('current_password') border-rose-300 bg-rose-50/50 @else border-slate-200 @enderror"
                                       placeholder="Masukkan kata sandi saat ini"
                                       autocomplete="current-password" required>
                                <button type="button" class="absolute right-2.5 text-slate-400 hover:text-sky-600 p-1.5 rounded-lg transition cursor-pointer text-sm leading-none" data-toggle-sandi="current_password" title="Lihat / sembunyikan">&#128065;</button>
                            </div>
                            @error('current_password')
                                <span class="block text-xs font-bold text-rose-600 mt-1.5" role="alert">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-xs sm:text-sm font-black text-slate-800 mb-1.5">
                                Kata Sandi Baru
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-slate-400 pointer-events-none">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 2l-2 2m-2-2l2 2m0 0l-3.5 3.5M7 11v4a2 2 0 0 0 2 2h4M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </span>
                                <input type="password" id="password" name="password"
                                       class="w-full h-11 pl-10 pr-11 rounded-xl border bg-slate-50/50 text-xs sm:text-sm font-bold text-slate-800 placeholder:text-slate-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-sky-500/20 focus:border-sky-400 transition @error('password') border-rose-300 bg-rose-50/50 @else border-slate-200 @enderror"
                                       placeholder="Minimal 8 karakter"
                                       autocomplete="new-password" required>
                                <button type="button" class="absolute right-2.5 text-slate-400 hover:text-sky-600 p-1.5 rounded-lg transition cursor-pointer text-sm leading-none" data-toggle-sandi="password" title="Lihat / sembunyikan">&#128065;</button>
                            </div>
                            @error('password')
                                <span class="block text-xs font-bold text-rose-600 mt-1.5" role="alert">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs sm:text-sm font-black text-slate-800 mb-1.5">
                                Ulangi Kata Sandi Baru
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-slate-400 pointer-events-none">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </span>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="w-full h-11 pl-10 pr-11 rounded-xl border border-slate-200 bg-slate-50/50 text-xs sm:text-sm font-bold text-slate-800 placeholder:text-slate-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-sky-500/20 focus:border-sky-400 transition"
                                       placeholder="Ulangi kata sandi baru yang sama"
                                       autocomplete="new-password" required>
                                <button type="button" class="absolute right-2.5 text-slate-400 hover:text-sky-600 p-1.5 rounded-lg transition cursor-pointer text-sm leading-none" data-toggle-sandi="password_confirmation" title="Lihat / sembunyikan">&#128065;</button>
                            </div>
                            @include('auth._aturan-sandi')
                        </div>

                        <div class="flex justify-end items-center gap-3 pt-4 mt-6 border-t border-slate-100">
                            <a href="{{ route(auth()->user()->dashboardRoute()) }}"
                               class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition cursor-pointer">
                                Batal
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-black text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 shadow-md shadow-sky-500/20 transition cursor-pointer">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                <span>Simpan Kata Sandi</span>
                            </button>
                        </div>
                    </form>

                    <div class="bg-sky-50/80 border border-sky-200 rounded-2xl p-4 mt-6 text-xs text-sky-900 font-semibold leading-relaxed flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-sky-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <span>Setelah kata sandi diubah, perangkat atau browser lain yang sedang login dengan akun ini akan otomatis dikeluarkan demi keamanan. Sesi Anda saat ini tetap aktif.</span>
                    </div>
                </section>
            </div>
        </main>

        <x-footer />
    </div>
</body>

</html>
