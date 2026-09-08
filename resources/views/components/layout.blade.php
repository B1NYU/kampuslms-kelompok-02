<nav class="top-navbar">
    <!-- Kiri: Brand Logo, Judul, dan Menu Navigasi -->
    <div class="navbar-left-group">
        <div class="navbar-brand">
            <svg width="28" height="28" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="16" cy="20" r="11" stroke="white" stroke-width="3.5" fill="none" opacity="0.95" />
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
</nav>