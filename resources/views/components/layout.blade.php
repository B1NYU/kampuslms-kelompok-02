<nav class="top-navbar">
    <!-- Kiri: Brand Logo, Judul, dan Menu Navigasi -->
    <div class="navbar-left-group">
        <div class="navbar-brand">
            <svg width="28" height="28" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="16" cy="20" r="11" stroke="#B0182D" stroke-width="3.5" fill="none" opacity="0.95" />
                <circle cx="24" cy="20" r="11" stroke="#FFD464" stroke-width="3.5" fill="none" opacity="0.95" />
            </svg>
            <div class="brand-info">
                <h2>KAMPUS LMS</h2>
            </div>
        </div>

        <!-- Navigation Menu dipindah ke sini -->
        <ul class="navbar-nav">
            <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li class="{{ request()->is('tentang') ? 'active' : '' }}">
                <a href="/tentang">Anggota</a>
            </li>
            <li class="{{ request()->is('mata-kuliah*') ? 'active' : '' }}">
                <a href="{{ route('mata-kuliah.index') }}">Mata Kuliah</a>
            </li>
        </ul>
    </div>

    <!-- Kanan: Tombol Logout Mahasiswa -->
    <div class="navbar-right-group">
        <a href="{{ route('logout') }}" class="btn-logout" title="Keluar dari Akun Mahasiswa">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            <span>Logout</span>
        </a>
    </div>
</nav>