/**
 * Script untuk navbar portal dosen (dropdown profil & toggle)
 */
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

    // [RESPONSIVE-MOBILE] Script interaksi hamburger dan mobile drawer navigasi dosen
    const mobileMenuBtn = document.getElementById('dosenMobileMenuBtn');
    const mobileDrawer = document.getElementById('dosenMobileDrawer');
    const hamburgerIcon = document.getElementById('dosenHamburgerIcon');
    const closeIcon = document.getElementById('dosenCloseIcon');

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
