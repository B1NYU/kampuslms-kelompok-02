/**
 * Script untuk navbar portal mahasiswa (dropdown profil & toggle)
 */
document.addEventListener('DOMContentLoaded', function() {
    const profileTrigger = document.getElementById('mhsProfileTrigger');
    const profileDropdown = document.getElementById('mhsProfileDropdown');
    const profileChevron = profileTrigger ? profileTrigger.querySelector('.mhs-profile-chevron') : null;

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

    // [RESPONSIVE-MOBILE] Script interaksi hamburger dan mobile drawer navigasi mahasiswa
    const mobileMenuBtn = document.getElementById('mhsMobileMenuBtn');
    const mobileDrawer = document.getElementById('mhsMobileDrawer');
    const hamburgerIcon = document.getElementById('mhsHamburgerIcon');
    const closeIcon = document.getElementById('mhsCloseIcon');

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
