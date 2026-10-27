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
});
