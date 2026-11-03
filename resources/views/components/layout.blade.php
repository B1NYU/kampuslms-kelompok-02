<nav class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-sky-100 shadow-xs">
    <div class="w-full max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
        <!-- Kiri: Brand Logo + Menu Navigasi Mahasiswa -->
        <div class="flex items-center gap-4 lg:gap-8 min-w-0">
            <!-- Brand Logo -->
            <a href="{{ route('mahasiswa.dashboard') }}" class="flex items-center gap-2.5 shrink-0 no-underline group">
                <svg width="28" height="28" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="group-hover:scale-105 transition-transform duration-200">
                    <circle cx="16" cy="20" r="11" stroke="#039FFA" stroke-width="3.5" fill="none" opacity="0.95" />
                    <circle cx="24" cy="20" r="11" stroke="#F9B804" stroke-width="3.5" fill="none" opacity="0.95" />
                </svg>
                <div class="flex flex-col shrink-0">
                    <span class="text-sm font-black text-slate-900 tracking-wider leading-none">KAMPUS LMS</span>
                    <span class="text-[9px] font-extrabold tracking-widest uppercase bg-gradient-to-r from-sky-500 to-sky-400 text-white px-2 py-0.5 rounded-full w-fit mt-1 leading-none shadow-xs">
                        PORTAL MAHASISWA
                    </span>
                </div>
            </a>

            <!-- Navigation Menu Mahasiswa -->
            <ul class="flex items-center gap-1 sm:gap-2 list-none m-0 p-0 overflow-x-auto">
                <li>
                    <a href="{{ route('mahasiswa.dashboard') }}"
                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-150 whitespace-nowrap {{ request()->routeIs('mahasiswa.dashboard') ? 'bg-sky-50 text-sky-600 font-black shadow-xs' : 'text-slate-600 hover:text-sky-600 hover:bg-sky-50/60' }}">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('mahasiswa.mata-kuliah.index') }}"
                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-150 whitespace-nowrap {{ request()->routeIs('mahasiswa.mata-kuliah.*') ? 'bg-sky-50 text-sky-600 font-black shadow-xs' : 'text-slate-600 hover:text-sky-600 hover:bg-sky-50/60' }}">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                        <span>Mata Kuliah</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('mahasiswa.nilai') }}"
                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-150 whitespace-nowrap {{ request()->routeIs('mahasiswa.nilai') ? 'bg-sky-50 text-sky-600 font-black shadow-xs' : 'text-slate-600 hover:text-sky-600 hover:bg-sky-50/60' }}">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="7"></circle>
                            <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                        </svg>
                        <span>Nilai</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Kanan: Profil Mahasiswa & Dropdown Menu -->
        <div class="flex items-center gap-3 shrink-0">
            <x-notification-bell />

            @php
                $mhsAuth = auth()->user();
                $mhsParts = preg_split('/\s+/', trim($mhsAuth?->name ?? 'Mahasiswa'));
                $mhsInitials = strtoupper(mb_substr($mhsParts[0], 0, 1) . mb_substr($mhsParts[1] ?? '', 0, 1));
            @endphp
            <div class="relative">
                <div class="flex items-center gap-2.5 bg-white pl-1.5 pr-3 py-1 rounded-full border border-sky-200/80 shadow-xs hover:border-sky-400 cursor-pointer select-none transition-all duration-150"
                     id="mhsProfileTrigger"
                     title="Buka menu profil">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-sky-500 to-sky-400 text-white flex items-center justify-center text-xs font-black shrink-0 shadow-xs">
                        {{ $mhsInitials }}
                    </div>
                    <div class="hidden sm:flex flex-col leading-tight">
                        <span class="text-xs font-extrabold text-slate-800 leading-tight">{{ $mhsAuth?->name ?? 'Mahasiswa' }}</span>
                        <span class="text-[10px] font-bold text-orange-500 leading-none mt-0.5">{{ $mhsAuth?->nim_nip ? 'NIM: ' . $mhsAuth->nim_nip : 'Mahasiswa Aktif' }}</span>
                    </div>
                    <svg class="mhs-profile-chevron w-3 h-3 text-slate-400 ml-0.5 transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>

                <!-- Dropdown Profil Mahasiswa -->
                <div class="absolute right-0 top-[calc(100%+8px)] bg-white border border-sky-100 rounded-xl shadow-xl p-2 min-w-48 z-50 flex flex-col gap-1.5"
                     id="mhsProfileDropdown"
                     style="display: none;">
                    <a href="{{ route('akun.kata-sandi') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 transition-colors"
                       title="Ubah kata sandi akun">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <span>Ubah Kata Sandi</span>
                    </a>
                    <form id="logout-form-mhs" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                    <a href="#"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors cursor-pointer"
                       title="Keluar dari Akun Mahasiswa"
                       onclick="event.preventDefault(); document.getElementById('logout-form-mhs').submit();">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

@if(session('success'))
    <div class="max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        <div class="bg-emerald-50 text-emerald-800 border-l-4 border-emerald-500 rounded-xl p-4 font-bold text-xs sm:text-sm flex items-center justify-between shadow-xs">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.parentElement.style.display='none'" class="text-emerald-800 hover:text-emerald-950 text-lg font-black cursor-pointer">&times;</button>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        <div class="bg-rose-50 text-rose-800 border-l-4 border-rose-500 rounded-xl p-4 font-bold text-xs sm:text-sm flex items-center justify-between shadow-xs">
            <span>{{ session('error') }}</span>
            <button type="button" onclick="this.parentElement.parentElement.style.display='none'" class="text-rose-800 hover:text-rose-950 text-lg font-black cursor-pointer">&times;</button>
        </div>
    </div>
@endif

@if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/js/mahasiswa/mahasiswa.navbar.js'])
@endif
