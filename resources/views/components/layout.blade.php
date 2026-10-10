<style>
    .btn-ubah-sandi { display: inline-flex; align-items: center; gap: 6px; background: #EFF6FF; border: 1px solid rgba(3, 159, 250, 0.3); color: #0369A1; text-decoration: none; font-size: 12px; font-weight: 800; padding: 7px 14px; border-radius: 10px; transition: background .2s, color .2s; }
    .btn-ubah-sandi:hover { background: #039FFA; color: #FFFFFF; border-color: #039FFA; }
</style>
<nav class="top-navbar">
    <!-- Kiri: Brand Logo, Judul, dan Menu Navigasi -->
    <div class="navbar-left-group">
        <div class="navbar-brand">
            <svg width="28" height="28" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="16" cy="20" r="11" stroke="#039FFA" stroke-width="3.5" fill="none" opacity="0.95" />
                <circle cx="24" cy="20" r="11" stroke="#F9B804" stroke-width="3.5" fill="none" opacity="0.95" />
            </svg>
            <div class="brand-info">
                <h2>KAMPUS LMS</h2>
                <span class="portal-badge portal-badge-mhs">PORTAL MAHASISWA</span>
            </div>
        </div>

        <!-- Navigation Menu Mahasiswa -->
        <ul class="navbar-nav mhs-nav">
            <li class="{{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}">
                <a href="{{ route('mahasiswa.dashboard') }}" class="nav-link-mhs">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li class="{{ request()->routeIs('mahasiswa.mata-kuliah.*') ? 'active' : '' }}">
                <a href="{{ route('mahasiswa.mata-kuliah.index') }}" class="nav-link-mhs">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                    </svg>
                    Mata Kuliah
                </a>
            </li>
        </ul>
    </div>

    <!-- Kanan: Profil Mahasiswa & Tombol Logout -->
    <div class="navbar-right-group">
        @php
            $mhsAuth = auth()->user();
            $mhsParts = preg_split('/\s+/', trim($mhsAuth?->name ?? 'Mahasiswa'));
            $mhsInitials = strtoupper(mb_substr($mhsParts[0], 0, 1) . mb_substr($mhsParts[1] ?? '', 0, 1));
        @endphp
        <div class="mhs-avatar-badge">
            <div class="mhs-avatar">{{ $mhsInitials }}</div>
            <div class="mhs-badge-info">
                <span class="mhs-name">{{ $mhsAuth?->name ?? 'Mahasiswa' }}</span>
                <span class="mhs-role">{{ $mhsAuth?->nim_nip ? 'NIM: ' . $mhsAuth->nim_nip : 'Mahasiswa Aktif' }}</span>
            </div>
        </div>

        <a href="{{ route('akun.kata-sandi') }}" class="btn-ubah-sandi" title="Ubah kata sandi akun"
           style="margin-right: 8px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <span>Kata Sandi</span>
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
        <a href="#" class="btn-logout" title="Keluar dari Akun Mahasiswa" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            <span>Logout</span>
        </a>
    </div>
</nav>

@if(session('success'))
    <div style="background-color: #d1fae5; color: #065f46; padding: 12px 20px; border-radius: 8px; margin: 16px 20px 0; border-left: 4px solid #10b981; font-family: 'Nunito', sans-serif; font-weight: 600; display: flex; justify-content: space-between; align-items: center; z-index: 9999; position: relative; max-width: 1200px; margin-left: auto; margin-right: auto;">
        <span>{{ session('success') }}</span>
        <button onclick="this.parentElement.style.display='none'" style="background: transparent; border: none; color: #065f46; cursor: pointer; font-size: 16px; font-weight: bold;">&times;</button>
    </div>
@endif

@if(session('error'))
    <div style="background-color: #fee2e2; color: #991b1b; padding: 12px 20px; border-radius: 8px; margin: 16px 20px 0; border-left: 4px solid #ef4444; font-family: 'Nunito', sans-serif; font-weight: 600; display: flex; justify-content: space-between; align-items: center; z-index: 9999; position: relative; max-width: 1200px; margin-left: auto; margin-right: auto;">
        <span>{{ session('error') }}</span>
        <button onclick="this.parentElement.style.display='none'" style="background: transparent; border: none; color: #991b1b; cursor: pointer; font-size: 16px; font-weight: bold;">&times;</button>
    </div>
@endif
