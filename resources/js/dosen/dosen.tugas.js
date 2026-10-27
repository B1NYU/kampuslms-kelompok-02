/**
 * Script untuk Manajemen Tugas Perkuliahan Dosen
 */
document.addEventListener('DOMContentLoaded', () => {
    const configEl = document.getElementById('dosenTugasConfig');
    const isEdit = configEl ? configEl.dataset.isEdit === '1' : false;
    const redirectUrl = configEl ? configEl.dataset.redirectUrl : '';

    // Modal Controls
    const assignmentModal = document.getElementById('assignmentModalOverlay');
    const btnOpenCreate = document.getElementById('btnOpenCreateAssignmentModal');
    const btnCloseModal = document.getElementById('btnCloseAssignmentModal');
    const btnCancelModal = document.getElementById('btnCancelAssignmentModal');

    function openModal() {
        if (assignmentModal) {
            assignmentModal.classList.add('active');
        }
    }

    function closeModal() {
        if (assignmentModal) {
            assignmentModal.classList.remove('active');
            if (isEdit && redirectUrl) {
                window.location.href = redirectUrl;
            }
        }
    }

    if (btnOpenCreate) {
        btnOpenCreate.addEventListener('click', openModal);
    }
    if (btnCloseModal) {
        btnCloseModal.addEventListener('click', closeModal);
    }
    if (btnCancelModal) {
        btnCancelModal.addEventListener('click', closeModal);
    }
    if (assignmentModal) {
        assignmentModal.addEventListener('click', (e) => {
            if (e.target === assignmentModal) closeModal();
        });
    }
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && assignmentModal && assignmentModal.classList.contains('active')) {
            closeModal();
        }
    });

    function showToast(message, isSuccess = true) {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = 'dosen-toast';
        if (!isSuccess) toast.style.borderLeftColor = '#EF4444';

        const icon = `
            <svg class="toast-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="${isSuccess ? '#10B981' : '#EF4444'}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>`;
        toast.innerHTML = icon;

        const text = document.createElement('span');
        text.textContent = message;
        toast.appendChild(text);

        container.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 50);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 350);
        }, 3500);
    }

    if (configEl) {
        if (configEl.dataset.toastSuccess) {
            showToast(configEl.dataset.toastSuccess, true);
        }
        if (configEl.dataset.toastError) {
            showToast(configEl.dataset.toastError, false);
        }
    }
});
