<aside class="sidebar">
    <!-- Brand / Logo -->
    <div class="sidebar-brand">
        <div class="brand-logo">
            <svg width="28" height="28" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="16" cy="20" r="11" stroke="white" stroke-width="3.5" fill="none" opacity="0.95" />
                <circle cx="24" cy="20" r="11" stroke="#FFD464" stroke-width="3.5" fill="none" opacity="0.95" />
            </svg>
        </div>
        <div class="brand-info">
            <h2>KAMPUS LMS</h2>
            <span>Student Management</span>
        </div>
    </div>

    <!-- Category Label -->
    <div class="sidebar-category">
        <span>MENU UTAMA</span>
        <div class="category-divider"></div>
    </div>

    <!-- Navigation Menu with Dots -->
    <nav class="sidebar-nav">
        <ul>
            <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                <a href="/dashboard">
                    <span class="nav-dot"></span>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="{{ request()->is('tentang') ? 'active' : '' }}">
                <a href="/tentang">
                    <span class="nav-dot"></span>
                    <span class="nav-text">Anggota Kelompok</span>
                </a>
            </li>
            <li class="{{ request()->is('mata-kuliah*') ? 'active' : '' }}">
                <a href="/mata-kuliah">
                    <span class="nav-dot"></span>
                    <span class="nav-text">Mata Kuliah</span>
                </a>
            </li>
            <li class="{{ request()->is('jadwal*') ? 'active' : '' }}">
                <a href="#">
                    <span class="nav-dot"></span>
                    <span class="nav-text">Jadwal Kuliah</span>
                </a>
            </li>
            <li class="{{ request()->is('tugas*') ? 'active' : '' }}">
                <a href="#">
                    <span class="nav-dot"></span>
                    <span class="nav-text">Tugas & Kuis</span>
                </a>
            </li>
            <li class="{{ request()->is('nilai*') ? 'active' : '' }}">
                <a href="#">
                    <span class="nav-dot"></span>
                    <span class="nav-text">Nilai Akademik</span>
                </a>
            </li>
            <li class="{{ request()->is('pengaturan*') ? 'active' : '' }}">
                <a href="#">
                    <span class="nav-dot"></span>
                    <span class="nav-text">Pengaturan</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Sidebar Footer / User Profile Preview -->
    <div class="sidebar-footer">
        <div class="footer-user">
            <div class="user-avatar">
                <span>02</span>
            </div>
            <div class="user-details">
                <strong>Kelompok 02</strong>
                <small>Semester 4 - IT</small>
            </div>
        </div>
        <a href="/" class="footer-exit" title="Kembali ke Beranda">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
        </a>
    </div>
</aside>

<style>
    /* =========================================
       SIDEBAR STYLING (Matching Reference & Palette)
       ========================================= */
    .sidebar {
        width: 260px;
        min-width: 260px;
        background: linear-gradient(185deg, #8A1022 0%, #B0182D 45%, #E23C64 100%);
        color: #ffffff;
        padding: 32px 24px 28px;
        display: flex;
        flex-direction: column;
        border-radius: 28px 0 0 28px;
        box-shadow: 6px 0 25px rgba(176, 24, 45, 0.12);
        position: relative;
        z-index: 10;
    }

    /* Brand Logo */
    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 36px;
        padding-left: 6px;
    }

    .brand-logo {
        width: 44px;
        height: 44px;
        background: rgba(255, 255, 255, 0.14);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .brand-info h2 {
        font-size: 16px;
        font-weight: 900;
        letter-spacing: 0.5px;
        color: #ffffff;
        margin: 0;
        line-height: 1.2;
    }

    .brand-info span {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.72);
        font-weight: 600;
        letter-spacing: 0.2px;
    }

    /* Category Divider */
    .sidebar-category {
        margin-bottom: 20px;
        padding-left: 8px;
    }

    .sidebar-category span {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.2px;
        color: rgba(255, 255, 255, 0.65);
        text-transform: uppercase;
    }

    .category-divider {
        height: 1px;
        background: rgba(255, 255, 255, 0.15);
        margin-top: 10px;
    }

    /* Navigation List */
    .sidebar-nav {
        flex: 1;
    }

    .sidebar-nav ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .sidebar-nav li a {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 11px 16px;
        border-radius: 16px;
        color: rgba(255, 255, 255, 0.78);
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .nav-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        transition: all 0.25s ease;
    }

    .sidebar-nav li a:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.12);
        transform: translateX(4px);
    }

    .sidebar-nav li a:hover .nav-dot {
        background: #FFD464;
        box-shadow: 0 0 8px #FFD464;
    }

    /* Active Item Highlight */
    .sidebar-nav li.active a {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.18);
        font-weight: 800;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .sidebar-nav li.active .nav-dot {
        background: #FFD464;
        box-shadow: 0 0 10px #FFD464;
        transform: scale(1.3);
    }

    /* Footer */
    .sidebar-footer {
        margin-top: auto;
        padding-top: 18px;
        border-top: 1px solid rgba(255, 255, 255, 0.16);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .footer-user {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #FFD464;
        color: #B0182D;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 900;
    }

    .user-details {
        display: flex;
        flex-direction: column;
    }

    .user-details strong {
        font-size: 13px;
        color: #ffffff;
        line-height: 1.2;
    }

    .user-details small {
        font-size: 10px;
        color: rgba(255, 255, 255, 0.7);
    }

    .footer-exit {
        color: rgba(255, 255, 255, 0.7);
        padding: 6px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .footer-exit:hover {
        color: #FFD464;
        background: rgba(255, 255, 255, 0.12);
    }

    @media (max-width: 900px) {
        .sidebar {
            border-radius: 28px 28px 0 0;
            width: 100%;
            min-width: 100%;
        }
    }
</style>
