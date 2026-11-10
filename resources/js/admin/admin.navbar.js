/**
 * Script untuk navbar portal admin (auto-dismiss toasts & dropdown profil)
 */
document.addEventListener('DOMContentLoaded', function() {
    const toasts = document.querySelectorAll('.global-toast-msg');
    toasts.forEach(function(toast) {
        setTimeout(function() {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px)';
            setTimeout(function() { toast.remove(); }, 300);
        }, 4000);
    });

    // Profile Dropdown Toggle
    const profileTrigger = document.getElementById('adminProfileTrigger');
    const profileDropdown = document.getElementById('adminProfileDropdown');
    const profileChevron = profileTrigger ? profileTrigger.querySelector('.admin-profile-chevron') : null;

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

    // [RESPONSIVE-MOBILE] Script interaksi hamburger dan mobile drawer navigasi admin
    const mobileMenuBtn = document.getElementById('adminMobileMenuBtn');
    const mobileDrawer = document.getElementById('adminMobileDrawer');
    const hamburgerIcon = document.getElementById('adminHamburgerIcon');
    const closeIcon = document.getElementById('adminCloseIcon');

    if (mobileMenuBtn && mobileDrawer) {
        mobileMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const isHidden = mobileDrawer.classList.contains('hidden');
            if (isHidden) {
                mobileDrawer.classList.remove('hidden');
                if (hamburgerIcon) hamburgerIcon.classList.add('hidden');
                if (closeIcon) closeIcon.classList.remove('hidden');
            } else {
                mobileDrawer.classList.add('hidden');
                if (hamburgerIcon) hamburgerIcon.classList.remove('hidden');
                if (closeIcon) closeIcon.classList.add('hidden');
            }
        });

        // Tutup mobile drawer saat klik di luar area navbar
        document.addEventListener('click', function(e) {
            if (!mobileMenuBtn.contains(e.target) && !mobileDrawer.contains(e.target)) {
                mobileDrawer.classList.add('hidden');
                if (hamburgerIcon) hamburgerIcon.classList.remove('hidden');
                if (closeIcon) closeIcon.classList.add('hidden');
            }
        });
    }
});
