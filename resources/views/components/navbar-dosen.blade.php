<nav class="top-navbar dosen-navbar">
    <!-- Kiri: Brand Logo, Portal Dosen Badge, dan Menu Navigasi Khusus Dosen -->
    <div class="navbar-left-group">
        <div class="navbar-brand">
            <svg width="28" height="28" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="16" cy="20" r="11" stroke="#039FFA" stroke-width="3.5" fill="none" opacity="0.95" />
                <circle cx="24" cy="20" r="11" stroke="#F9B804" stroke-width="3.5" fill="none" opacity="0.95" />
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

    <!-- Kanan: Identitas Dosen dengan Dropdown Profil untuk Fitur Keluar -->
    <div class="navbar-right-group">
        <div class="dosen-profile-wrapper" style="position: relative;">
            <div class="dosen-avatar-badge" id="dosenProfileTrigger" style="cursor: pointer; user-select: none;" title="Buka menu profil">
                <div class="dosen-avatar">{{ $initials }}</div>
                <div class="dosen-badge-info">
                    <span class="dosen-name">{{ $authName }}</span>
                    <span class="dosen-role-tag">{{ $roleTag }}</span>
                </div>
                <svg class="dosen-profile-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 2px; color: var(--dosen-muted); transition: transform 0.2s ease;">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </div>

            <!-- Extension / Dropdown Profil untuk Fitur Keluar -->
            <div class="dosen-profile-dropdown" id="dosenProfileDropdown" style="display: none; position: absolute; right: 0; top: calc(100% + 8px); background: #FFFFFF; border: 1px solid rgba(3, 159, 250, 0.2); border-radius: 12px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08); padding: 8px; min-width: 140px; z-index: 1000;">
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
                <a href="#" class="btn-logout" title="Keluar dari Portal Dosen" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="width: 100%; justify-content: center;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Logout</span>
                </a>
            </div>
        </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileTrigger = document.getElementById('dosenProfileTrigger');
        const profileDropdown = document.getElementById('dosenProfileDropdown');
        const profileChevron = profileTrigger ? profileTrigger.querySelector('.dosen-profile-chevron') : null;

        if (profileTrigger && profileDropdown) {
            profileTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = profileDropdown.style.display === 'block';
                profileDropdown.style.display = isOpen ? 'none' : 'block';
                if (profileChevron) {
                    profileChevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
                }
            });

            document.addEventListener('click', function(e) {
                if (!profileTrigger.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.style.display = 'none';
                    if (profileChevron) profileChevron.style.transform = 'rotate(0deg)';
                }
            });
        }
    });
</script>
