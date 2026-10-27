<nav class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-sky-100 shadow-sm">
    <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-9 h-16 flex items-center justify-between gap-3">
        <!-- Kiri: Brand + Menu Navigasi Admin -->
        <div class="flex items-center gap-4 lg:gap-6 min-w-0">
            <!-- Brand Logo -->
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 shrink-0 text-decoration-none group">
                <svg width="28" height="28" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="group-hover:scale-105 transition-transform duration-200">
                    <circle cx="16" cy="20" r="11" stroke="#039FFA" stroke-width="3.5" fill="none" opacity="0.95" />
                    <circle cx="24" cy="20" r="11" stroke="#F9B804" stroke-width="3.5" fill="none" opacity="0.95" />
                </svg>
                <div class="flex flex-col shrink-0">
                    <span class="text-sm font-black text-slate-900 tracking-wider leading-none">KAMPUS LMS</span>
                    <span class="text-[9px] font-extrabold tracking-widest uppercase bg-gradient-to-r from-[#039FFA] to-[#32B3F1] text-white px-2 py-0.5 rounded-full w-fit mt-1 leading-none shadow-sm">
                        PORTAL ADMIN
                    </span>
                </div>
            </a>

            <!-- Navigation Menu Admin (Tanpa Horizontal Scrollbar) -->
            <ul class="hidden lg:flex items-center gap-0.5 xl:gap-1 list-none m-0 p-0">
                @php
                    $navItems = [
                        ['route' => 'admin.dashboard', 'is_active' => request()->is('admin/dashboard'), 'label' => 'Dashboard', 'icon' => 'dashboard'],
                        ['route' => 'admin.pengguna', 'is_active' => request()->is('admin/pengguna*'), 'label' => 'Manajemen Pengguna', 'icon' => 'users'],
                        ['route' => 'admin.matkul', 'is_active' => request()->is('admin/mata-kuliah*'), 'label' => 'Mata Kuliah', 'icon' => 'book'],
                        ['route' => 'admin.pendaftaran', 'is_active' => request()->is('admin/pendaftaran*'), 'label' => 'Pendaftaran MK', 'icon' => 'check-circle'],
                        ['route' => 'admin.materi', 'is_active' => request()->is('admin/materi*'), 'label' => 'Materi Kuliah', 'icon' => 'file-text'],
                        ['route' => 'admin.tugas', 'is_active' => request()->is('admin/tugas*'), 'label' => 'Tugas Kuliah', 'icon' => 'calendar'],
                        ['route' => Route::has('admin.nilai') ? 'admin.nilai' : null, 'url' => url('/admin/nilai'), 'is_active' => request()->is('admin/nilai*'), 'label' => 'Rekap Nilai', 'icon' => 'award'],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    <li>
                        <a href="{{ $item['route'] ? route($item['route']) : $item['url'] }}" 
                           class="flex items-center gap-1.5 px-2 xl:px-2.5 py-1.5 rounded-lg text-[11.5px] xl:text-xs transition duration-150 whitespace-nowrap {{ $item['is_active'] ? 'bg-sky-50 text-[#039FFA] font-extrabold shadow-sm' : 'text-slate-600 hover:text-[#039FFA] hover:bg-sky-50/60 font-bold' }}">
                            @if ($item['icon'] === 'dashboard')
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                            @elseif ($item['icon'] === 'users')
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            @elseif ($item['icon'] === 'book')
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                </svg>
                            @elseif ($item['icon'] === 'check-circle')
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline>
                                </svg>
                            @elseif ($item['icon'] === 'file-text')
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                            @elseif ($item['icon'] === 'calendar')
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            @elseif ($item['icon'] === 'award')
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                                </svg>
                            @endif
                            <span>{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Kanan: Identitas Admin dengan Menu Dropdown Keluar -->
        <div class="flex items-center gap-3 shrink-0">
            @php
                $adminUser = auth()->user();
                $adminName = $adminUser?->name ?? 'Super Administrator';
                $words = explode(' ', trim($adminName));
                $initials = strtoupper(substr($words[0] ?? 'A', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : 'D'));
            @endphp
            <div class="relative">
                <div class="flex items-center gap-2.5 bg-white pl-1.5 pr-3 py-1 rounded-full border border-sky-200/80 shadow-sm hover:border-[#039FFA] cursor-pointer select-none transition-all duration-150" 
                     id="adminProfileTrigger" 
                     title="Buka menu profil">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-[#039FFA] to-[#32B3F1] text-white flex items-center justify-center text-xs font-black shrink-0 shadow-sm">
                        {{ $initials }}
                    </div>
                    <div class="hidden sm:flex flex-col leading-tight">
                        <span class="text-xs font-extrabold text-slate-800 leading-tight">{{ $adminName }}</span>
                        <span class="text-[10px] font-bold text-[#F96305] leading-none mt-0.5">Akses Penuh Sistem</span>
                    </div>
                    <svg class="admin-profile-chevron w-3 h-3 text-slate-400 ml-0.5 transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>

                <!-- Dropdown Profil -->
                <div class="admin-profile-dropdown hidden absolute right-0 top-[calc(100%+8px)] bg-white border border-sky-100 rounded-xl shadow-xl p-1.5 min-w-[150px] z-50 animate-in fade-in zoom-in-95 duration-150" 
                     id="adminProfileDropdown">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                    <a href="#" 
                       class="flex items-center justify-center gap-2 px-3 py-2 text-xs font-extrabold text-red-500 bg-red-50 hover:bg-red-500 hover:text-white rounded-lg border border-red-100 hover:border-red-500 transition duration-150" 
                       title="Keluar dari Portal Admin" 
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
        </div>
    </div>
</nav>

<!-- Container Notifikasi Global Floating Toast (Top-Right) -->
<div id="globalToastContainer" class="fixed top-5 right-5 z-[99999] flex flex-col gap-2.5 max-w-sm w-[calc(100%-40px)] pointer-events-none">
    @if(session('success'))
        <div class="global-toast-msg pointer-events-auto bg-white text-emerald-800 p-3.5 sm:p-4 rounded-xl border-l-4 border-emerald-500 shadow-xl flex items-center justify-between gap-3 text-sm font-semibold transition-all duration-300">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-emerald-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 text-lg leading-none cursor-pointer p-1">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="global-toast-msg pointer-events-auto bg-white text-red-800 p-3.5 sm:p-4 rounded-xl border-l-4 border-red-500 shadow-xl flex items-center justify-between gap-3 text-sm font-semibold transition-all duration-300">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-red-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 text-lg leading-none cursor-pointer p-1">&times;</button>
        </div>
    @endif
</div>

@if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/js/admin/admin.navbar.js'])
@endif
