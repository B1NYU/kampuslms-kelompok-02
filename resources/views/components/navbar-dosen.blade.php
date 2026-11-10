<nav class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-sky-100 shadow-xs">
    <!-- [RESPONSIVE-MOBILE] Navbar Dosen Layout Responsif & Anti-Tabrakan di Layar Tablet/Laptop (lg/xl) -->
    <div class="w-full max-w-[1400px] mx-auto px-3 sm:px-6 lg:px-4 xl:px-8 h-16 flex items-center justify-between gap-2 sm:gap-4">
        <!-- Kiri: Brand Logo, Portal Dosen Badge, dan Menu Navigasi Khusus Dosen -->
        <div class="flex items-center gap-2 sm:gap-3 lg:gap-3 xl:gap-5 min-w-0">
            <!-- Brand Logo -->
            <a href="{{ route('dosen.dashboard') }}" class="flex items-center gap-2 xl:gap-2.5 shrink-0 no-underline group">
                <svg width="28" height="28" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="group-hover:scale-105 transition-transform duration-200">
                    <circle cx="16" cy="20" r="11" stroke="#039FFA" stroke-width="3.5" fill="none" opacity="0.95" />
                    <circle cx="24" cy="20" r="11" stroke="#F9B804" stroke-width="3.5" fill="none" opacity="0.95" />
                </svg>
                <div class="flex flex-col shrink-0">
                    <span class="text-xs sm:text-sm font-black text-slate-900 tracking-wider leading-none">KAMPUS LMS</span>
                    <span class="text-[8.5px] sm:text-[9px] font-extrabold tracking-widest uppercase bg-gradient-to-r from-sky-500 to-sky-400 text-white px-1.5 sm:px-2 py-0.5 rounded-full w-fit mt-1 leading-none shadow-xs">
                        PORTAL DOSEN
                    </span>
                </div>
            </a>

            <!-- [RESPONSIVE-MOBILE] Navigation Menu Khusus Fitur Dosen (Icon tersembunyi pada lg agar tidak tabrakan dengan profil, muncul pada xl) -->
            <ul class="hidden lg:flex items-center gap-0.5 xl:gap-1 2xl:gap-1.5 list-none m-0 p-0">
                <li>
                    <a href="{{ route('dosen.dashboard') }}"
                       class="flex items-center gap-1 xl:gap-1.5 px-1.5 xl:px-2.5 py-1.5 rounded-xl text-[11px] xl:text-xs 2xl:text-sm transition-all duration-150 whitespace-nowrap {{ request()->is('dosen/dashboard') || request()->is('dosen') || request()->routeIs('dosen.dashboard') ? 'bg-sky-50 text-sky-600 font-black shadow-xs' : 'text-slate-600 hover:text-sky-600 hover:bg-sky-50/60 font-bold' }}">
                        <svg class="hidden xl:block w-3.5 h-3.5 xl:w-4 xl:h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('dosen.mahasiswa') }}"
                       class="flex items-center gap-1 xl:gap-1.5 px-1.5 xl:px-2.5 py-1.5 rounded-xl text-[11px] xl:text-xs 2xl:text-sm transition-all duration-150 whitespace-nowrap {{ request()->is('dosen/mahasiswa*') || request()->routeIs('dosen.mahasiswa*') ? 'bg-sky-50 text-sky-600 font-black shadow-xs' : 'text-slate-600 hover:text-sky-600 hover:bg-sky-50/60 font-bold' }}">
                        <svg class="hidden xl:block w-3.5 h-3.5 xl:w-4 xl:h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span>Kelola Mahasiswa</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('dosen.materi') }}"
                       class="flex items-center gap-1 xl:gap-1.5 px-1.5 xl:px-2.5 py-1.5 rounded-xl text-[11px] xl:text-xs 2xl:text-sm transition-all duration-150 whitespace-nowrap {{ request()->is('dosen/materi*') || request()->routeIs('dosen.materi*') ? 'bg-sky-50 text-sky-600 font-black shadow-xs' : 'text-slate-600 hover:text-sky-600 hover:bg-sky-50/60 font-bold' }}">
                        <svg class="hidden xl:block w-3.5 h-3.5 xl:w-4 xl:h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                            <path d="M12 12v9"></path>
                            <path d="m16 16-4-4-4 4"></path>
                        </svg>
                        <span>Unggah Materi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('dosen.tugas') }}"
                       class="flex items-center gap-1 xl:gap-1.5 px-1.5 xl:px-2.5 py-1.5 rounded-xl text-[11px] xl:text-xs 2xl:text-sm transition-all duration-150 whitespace-nowrap {{ request()->is('dosen/tugas*') || request()->routeIs('dosen.tugas*') || request()->is('dosen/courses/*/assignments*') ? 'bg-sky-50 text-sky-600 font-black shadow-xs' : 'text-slate-600 hover:text-sky-600 hover:bg-sky-50/60 font-bold' }}">
                        <svg class="hidden xl:block w-3.5 h-3.5 xl:w-4 xl:h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        <span>Buat Tugas</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('dosen.presensi.index') }}"
                       class="flex items-center gap-1 xl:gap-1.5 px-1.5 xl:px-2.5 py-1.5 rounded-xl text-[11px] xl:text-xs 2xl:text-sm transition-all duration-150 whitespace-nowrap {{ request()->is('dosen/presensi*') || request()->routeIs('dosen.presensi*') ? 'bg-sky-50 text-sky-600 font-black shadow-xs' : 'text-slate-600 hover:text-sky-600 hover:bg-sky-50/60 font-bold' }}">
                        <svg class="hidden xl:block w-3.5 h-3.5 xl:w-4 xl:h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 11l3 3L22 4"></path>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                        <span>Presensi Kelas</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('dosen.penilaian') }}"
                       class="flex items-center gap-1 xl:gap-1.5 px-1.5 xl:px-2.5 py-1.5 rounded-xl text-[11px] xl:text-xs 2xl:text-sm transition-all duration-150 whitespace-nowrap {{ request()->is('dosen/penilaian*') || request()->routeIs('dosen.penilaian*') ? 'bg-sky-50 text-sky-600 font-black shadow-xs' : 'text-slate-600 hover:text-sky-600 hover:bg-sky-50/60 font-bold' }}">
                        <svg class="hidden xl:block w-3.5 h-3.5 xl:w-4 xl:h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <span>Penilaian & Feedback</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('dosen.rekap') }}"
                       class="flex items-center gap-1 xl:gap-1.5 px-1.5 xl:px-2.5 py-1.5 rounded-xl text-[11px] xl:text-xs 2xl:text-sm transition-all duration-150 whitespace-nowrap {{ request()->is('dosen/rekap-nilai*') || request()->is('dosen/rekap*') || request()->routeIs('dosen.rekap*') ? 'bg-sky-50 text-sky-600 font-black shadow-xs' : 'text-slate-600 hover:text-sky-600 hover:bg-sky-50/60 font-bold' }}">
                        <svg class="hidden xl:block w-3.5 h-3.5 xl:w-4 xl:h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="20" x2="18" y2="10"></line>
                            <line x1="12" y1="20" x2="12" y2="4"></line>
                            <line x1="6" y1="20" x2="6" y2="14"></line>
                        </svg>
                        <span>Rekap Nilai</span>
                    </a>
                </li>
            </ul>
        </div>

        @php
            $authUser = auth()->user();
            $authName = $authUser?->name ?? 'Dosen Pengampu';
            $nameParts = array_values(array_filter(explode(' ', trim($authName))));
            $initials = '';
            if (count($nameParts) >= 2) {
                $initials = strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1));
            } elseif (count($nameParts) === 1) {
                $initials = strtoupper(mb_substr($nameParts[0], 0, 2));
            } else {
                $initials = 'DS';
            }
            $nip = $authUser?->nim_nip ?? null;
            $roleTag = $nip ? 'NIP: ' . $nip : ($authUser ? ucfirst($authUser->role ?? 'Dosen Pengampu') : 'Dosen Pengampu');
        @endphp

        <!-- [RESPONSIVE-MOBILE] Kanan: Identitas Dosen dengan Dropdown Profil (shrink-0 & truncate agar tidak mendorong menu navbar) -->
        <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
            <div class="relative">
                <div class="flex items-center gap-2 bg-white pl-1.5 pr-2.5 sm:pr-3 py-1 rounded-full border border-sky-200/80 shadow-xs hover:border-sky-400 cursor-pointer select-none transition-all duration-150"
                     id="dosenProfileTrigger"
                     title="Buka menu profil">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-sky-500 to-sky-400 text-white flex items-center justify-center text-xs font-black shrink-0 shadow-xs">
                        {{ $initials }}
                    </div>
                    <div class="hidden sm:flex flex-col leading-tight max-w-[100px] xl:max-w-[150px]">
                        <span class="text-xs font-extrabold text-slate-800 leading-tight truncate">{{ $authName }}</span>
                        <span class="text-[10px] font-bold text-sky-600 leading-none mt-0.5 truncate">{{ $roleTag }}</span>
                    </div>
                    <svg class="dosen-profile-chevron w-3 h-3 text-slate-400 ml-0.5 transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>

                <!-- Dropdown Profil -->
                <div class="dosen-profile-dropdown absolute right-0 top-[calc(100%+8px)] bg-white border border-sky-100 rounded-xl shadow-xl p-1.5 min-w-[180px] z-50 transition duration-150"
                     id="dosenProfileDropdown"
                     style="display: none;">
                    <a href="{{ route('akun.kata-sandi') }}"
                       class="flex items-center gap-2 px-3 py-2 text-xs font-extrabold text-sky-700 bg-sky-50 hover:bg-sky-500 hover:text-white rounded-lg border border-sky-100 hover:border-sky-500 transition duration-150 mb-1"
                       title="Ubah kata sandi akun">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <span>Ubah Kata Sandi</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                    <a href="#"
                       class="flex items-center gap-2 px-3 py-2 text-xs font-extrabold text-red-500 bg-red-50 hover:bg-red-500 hover:text-white rounded-lg border border-red-100 hover:border-red-500 transition duration-150"
                       title="Keluar dari Portal Dosen"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span>Logout</span>
                    </a>
                </div>
            </div>

            <!-- [RESPONSIVE-MOBILE] Tombol Hamburger Navigasi Mobile Dosen -->
            <button type="button" 
                    id="dosenMobileMenuBtn" 
                    class="lg:hidden inline-flex items-center justify-center w-9 h-9 rounded-xl bg-slate-50 hover:bg-sky-50 text-slate-600 hover:text-sky-600 border border-slate-200/80 transition-all cursor-pointer focus:outline-none"
                    aria-label="Buka menu navigasi mobile dosen">
                <svg id="dosenHamburgerIcon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
                <svg id="dosenCloseIcon" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>

    <!-- [RESPONSIVE-MOBILE] Drawer Menu Navigasi Dosen untuk Layar Mobile & Tablet (< 1024px) -->
    <div id="dosenMobileDrawer" class="hidden lg:hidden border-t border-sky-100 bg-white/95 backdrop-blur-md px-4 py-3 shadow-lg transition-all animate-in slide-in-from-top-2 duration-150">
        <div class="flex flex-col gap-1 pb-2">
            <a href="{{ route('dosen.dashboard') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm transition duration-150 {{ request()->is('dosen/dashboard') || request()->is('dosen') || request()->routeIs('dosen.dashboard') ? 'bg-sky-50 text-sky-600 font-black shadow-xs' : 'text-slate-600 hover:text-sky-600 hover:bg-sky-50/60 font-bold' }}">
                <span class="p-1 rounded-lg {{ request()->is('dosen/dashboard') || request()->is('dosen') ? 'bg-sky-100/80 text-sky-600' : 'bg-slate-100 text-slate-500' }}">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                </span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('dosen.mahasiswa') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm transition duration-150 {{ request()->is('dosen/mahasiswa*') || request()->routeIs('dosen.mahasiswa*') ? 'bg-sky-50 text-sky-600 font-black shadow-xs' : 'text-slate-600 hover:text-sky-600 hover:bg-sky-50/60 font-bold' }}">
                <span class="p-1 rounded-lg {{ request()->is('dosen/mahasiswa*') ? 'bg-sky-100/80 text-sky-600' : 'bg-slate-100 text-slate-500' }}">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </span>
                <span>Kelola Mahasiswa</span>
            </a>
            <a href="{{ route('dosen.materi') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm transition duration-150 {{ request()->is('dosen/materi*') || request()->routeIs('dosen.materi*') ? 'bg-sky-50 text-sky-600 font-black shadow-xs' : 'text-slate-600 hover:text-sky-600 hover:bg-sky-50/60 font-bold' }}">
                <span class="p-1 rounded-lg {{ request()->is('dosen/materi*') ? 'bg-sky-100/80 text-sky-600' : 'bg-slate-100 text-slate-500' }}">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path><path d="M12 12v9"></path><path d="m16 16-4-4-4 4"></path>
                    </svg>
                </span>
                <span>Unggah Materi</span>
            </a>
            <a href="{{ route('dosen.tugas') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm transition duration-150 {{ request()->is('dosen/tugas*') || request()->routeIs('dosen.tugas*') || request()->is('dosen/courses/*/assignments*') ? 'bg-sky-50 text-sky-600 font-black shadow-xs' : 'text-slate-600 hover:text-sky-600 hover:bg-sky-50/60 font-bold' }}">
                <span class="p-1 rounded-lg {{ request()->is('dosen/tugas*') ? 'bg-sky-100/80 text-sky-600' : 'bg-slate-100 text-slate-500' }}">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </span>
                <span>Buat Tugas</span>
            </a>
            <a href="{{ route('dosen.presensi.index') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm transition duration-150 {{ request()->is('dosen/presensi*') || request()->routeIs('dosen.presensi*') ? 'bg-sky-50 text-sky-600 font-black shadow-xs' : 'text-slate-600 hover:text-sky-600 hover:bg-sky-50/60 font-bold' }}">
                <span class="p-1 rounded-lg {{ request()->is('dosen/presensi*') ? 'bg-sky-100/80 text-sky-600' : 'bg-slate-100 text-slate-500' }}">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                </span>
                <span>Presensi Kelas</span>
            </a>
            <a href="{{ route('dosen.penilaian') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm transition duration-150 {{ request()->is('dosen/penilaian*') || request()->routeIs('dosen.penilaian*') ? 'bg-sky-50 text-sky-600 font-black shadow-xs' : 'text-slate-600 hover:text-sky-600 hover:bg-sky-50/60 font-bold' }}">
                <span class="p-1 rounded-lg {{ request()->is('dosen/penilaian*') ? 'bg-sky-100/80 text-sky-600' : 'bg-slate-100 text-slate-500' }}">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </span>
                <span>Penilaian & Feedback</span>
            </a>
            <a href="{{ route('dosen.rekap') }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm transition duration-150 {{ request()->is('dosen/rekap-nilai*') || request()->is('dosen/rekap*') || request()->routeIs('dosen.rekap*') ? 'bg-sky-50 text-sky-600 font-black shadow-xs' : 'text-slate-600 hover:text-sky-600 hover:bg-sky-50/60 font-bold' }}">
                <span class="p-1 rounded-lg {{ request()->is('dosen/rekap-nilai*') || request()->is('dosen/rekap*') ? 'bg-sky-100/80 text-sky-600' : 'bg-slate-100 text-slate-500' }}">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                </span>
                <span>Rekap Nilai</span>
            </a>
        </div>
        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
            <span class="text-[11px] font-bold text-slate-500">{{ $authName }}</span>
            <div class="flex items-center gap-2">
                <a href="{{ route('akun.kata-sandi') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 py-1 px-2.5 rounded-lg bg-sky-50">
                    Sandi
                </a>
                <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-xs font-bold text-red-600 hover:text-red-700 py-1 px-2.5 rounded-lg bg-red-50 cursor-pointer">
                    Keluar
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- Notifikasi Alert Session -->
@if(session('success'))
    <div class="w-full max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-bold px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.closest('div.bg-emerald-50').parentElement.style.display='none'" class="text-emerald-700 hover:text-emerald-900 font-black text-base cursor-pointer leading-none">&times;</button>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="w-full max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-red-50 border border-red-200 text-red-800 text-xs sm:text-sm font-bold px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.closest('div.bg-red-50').parentElement.style.display='none'" class="text-red-700 hover:text-red-900 font-black text-base cursor-pointer leading-none">&times;</button>
        </div>
    </div>
@endif

@if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/js/dosen/dosen.navbar.js'])
@endif
