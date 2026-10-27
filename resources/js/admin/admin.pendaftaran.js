/**
 * Script untuk Manajemen Pendaftaran Mahasiswa ke Mata Kuliah oleh Admin
 */
document.addEventListener('DOMContentLoaded', function () {
    const enrollConfig = document.getElementById('enrollConfig');
    let currentMkFilter = enrollConfig ? (enrollConfig.dataset.selectedCode || 'all') : 'all';

    // Modal Daftarkan Mahasiswa
    const enrollModal = document.getElementById('enrollModalOverlay');
    const btnOpenEnroll = document.getElementById('btnOpenEnrollModal');
    const btnCloseEnrollModal = document.getElementById('btnCloseEnrollModal');
    const btnCancelEnrollModal = document.getElementById('btnCancelEnrollModal');

    function openEnrollModal() {
        if (enrollModal) enrollModal.classList.add('active');
    }

    function closeEnrollModal() {
        if (enrollModal) enrollModal.classList.remove('active');
    }

    if (btnOpenEnroll) btnOpenEnroll.addEventListener('click', openEnrollModal);
    if (btnCloseEnrollModal) btnCloseEnrollModal.addEventListener('click', closeEnrollModal);
    if (btnCancelEnrollModal) btnCancelEnrollModal.addEventListener('click', closeEnrollModal);

    if (enrollModal) {
        enrollModal.addEventListener('click', (e) => {
            if (e.target === enrollModal) closeEnrollModal();
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && enrollModal && enrollModal.classList.contains('active')) {
            closeEnrollModal();
        }
    });

    function applyEnrollFilters() {
        const q = (document.getElementById('searchEnrollInput')?.value || '').toLowerCase().trim();
        let visible = 0;

        document.querySelectorAll('#enrollTableBody tr[data-mk]').forEach(row => {
            const matchMk = (currentMkFilter === 'all' || row.dataset.mk === currentMkFilter);
            const matchSearch = !q || row.textContent.toLowerCase().includes(q);
            const show = matchMk && matchSearch;

            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        const countEl = document.getElementById('enrollCount');
        if (countEl) countEl.textContent = visible;
    }

    document.querySelectorAll('.btn-filter-mk').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.btn-filter-mk').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            currentMkFilter = this.dataset.mk;
            applyEnrollFilters();
        });
    });

    const searchInput = document.getElementById('searchEnrollInput');
    if (searchInput) {
        searchInput.addEventListener('input', applyEnrollFilters);
    }

    // Terapkan filter awal saat halaman dimuat
    applyEnrollFilters();
});
