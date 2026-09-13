<nav class="top-navbar dosen-navbar">
    <!-- Kiri: Brand Logo, Portal Dosen Badge, dan Menu Navigasi Khusus Dosen -->
    <div class="navbar-left-group">
        <div class="navbar-brand">
            <svg width="28" height="28" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="16" cy="20" r="11" stroke="#B0182D" stroke-width="3.5" fill="none" opacity="0.95" />
                <circle cx="24" cy="20" r="11" stroke="#FFD464" stroke-width="3.5" fill="none" opacity="0.95" />
            </svg>
            <div class="brand-info">
                <h2>KAMPUS LMS</h2>
                <span class="portal-badge portal-badge-dosen">PORTAL DOSEN</span>
            </div>
        </div>

        <!-- Navigation Menu Khusus Fitur Dosen -->
        <ul class="navbar-nav dosen-nav">
            <li class="{{ request()->is('dosen/dashboard') || request()->is('dosen') ? 'active' : '' }}">
                <a href="{{ route('dosen.dashboard') }}" class="nav-link-dosen">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li class="{{ request()->is('dosen/mahasiswa') ? 'active' : '' }}">
                <a href="{{ route('dosen.mahasiswa') }}" class="nav-link-dosen">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    Kelola Mahasiswa
                </a>
            </li>
            <li class="{{ request()->is('dosen/materi') ? 'active' : '' }}">
                <a href="{{ route('dosen.materi') }}" class="nav-link-dosen">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                        <path d="M12 12v9"></path>
                        <path d="m16 16-4-4-4 4"></path>
                    </svg>
                    Unggah Materi
                </a>
            </li>
            <li class="{{ request()->is('dosen/tugas') ? 'active' : '' }}">
                <a href="{{ route('dosen.tugas') }}" class="nav-link-dosen">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Buat Tugas
                </a>
            </li>
            <li class="{{ request()->is('dosen/penilaian') ? 'active' : '' }}">
                <a href="{{ route('dosen.penilaian') }}" class="nav-link-dosen">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    Penilaian & Feedback
                </a>
            </li>
        </ul>
    </div>

    <!-- Kanan: Identitas Dosen & Tombol Logout -->
    <div class="navbar-right-group">
        <div class="dosen-avatar-badge">
            <div class="dosen-avatar">BS</div>
            <div class="dosen-badge-info">
                <span class="dosen-name">Dr. Budi Santoso, M.Kom</span>
                <span class="dosen-role-tag">Dosen Pengampu Utama</span>
            </div>
        </div>

        <a href="{{ route('logout') }}" class="btn-logout" title="Keluar dari Portal Dosen">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            <span>Logout</span>
        </a>
    </div>
</nav>
