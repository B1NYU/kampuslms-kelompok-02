<nav class="admin-navbar">
    <!-- Kiri: Brand + Menu Navigasi Admin -->
    <div class="navbar-left-group">
        <div class="navbar-brand">
            <svg width="28" height="28" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="16" cy="20" r="11" stroke="#3B5BDB" stroke-width="3.5" fill="none" opacity="0.95" />
                <circle cx="24" cy="20" r="11" stroke="#4C6EF5" stroke-width="3.5" fill="none" opacity="0.95" />
            </svg>
            <div class="brand-info">
                <h2>KAMPUS LMS</h2>
                <span class="portal-badge portal-badge-admin">PORTAL ADMIN</span>
            </div>
        </div>

        <!-- Navigation Menu Admin -->
        <ul class="navbar-nav admin-nav">
            <li class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" class="nav-link-admin">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li class="{{ request()->is('admin/pengguna') ? 'active' : '' }}">
                <a href="{{ route('admin.pengguna') }}" class="nav-link-admin">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    Manajemen Pengguna
                </a>
            </li>
            <li class="{{ request()->is('admin/mata-kuliah') ? 'active' : '' }}">
                <a href="{{ route('admin.matkul') }}" class="nav-link-admin">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                    </svg>
                    Mata Kuliah
                </a>
            </li>
            <li class="{{ request()->is('admin/pendaftaran') ? 'active' : '' }}">
                <a href="{{ route('admin.pendaftaran') }}" class="nav-link-admin">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <polyline points="16 11 18 13 22 9"></polyline>
                    </svg>
                    Pendaftaran MK
                </a>
            </li>
            <li>
                <a href="#laporan" class="nav-link-admin">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                    Laporan & Log
                </a>
            </li>
        </ul>
    </div>

    <!-- Kanan: Identitas Admin & Tombol Logout -->
    <div class="navbar-right-group">
        <div class="admin-avatar-badge">
            <div class="admin-avatar">AD</div>
            <div class="admin-badge-info">
                <span class="admin-name">Super Administrator</span>
                <span class="admin-role-tag">Akses Penuh Sistem</span>
            </div>
        </div>

        <a href="{{ route('logout') }}" class="btn-logout" title="Keluar dari Portal Admin">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            <span>Logout</span>
        </a>
    </div>
</nav>
