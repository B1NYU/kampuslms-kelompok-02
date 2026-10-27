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
});
