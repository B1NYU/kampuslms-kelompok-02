/**
 * Script untuk Manajemen Mata Kuliah oleh Admin
 */
document.addEventListener('DOMContentLoaded', function () {
    const routesEl = document.getElementById('matkulRoutes');
    if (!routesEl) return;

    const storeUrl          = routesEl.dataset.storeUrl || '';
    const updateUrlTemplate = routesEl.dataset.updateUrlTemplate || '';
    const isEditing         = routesEl.dataset.isEditing === '1';

    const form         = document.getElementById('formAddMatkul');
    const methodField  = document.getElementById('mkMethodField');
    const submitBtn    = document.getElementById('mkSubmitBtn');
    const cancelBtn    = document.getElementById('mkCancelEditBtn');
    const formTitle    = document.getElementById('formMkTitle')?.querySelector('span');
    const submitLabel  = submitBtn?.querySelector('span');
    const tableBody    = document.getElementById('mkTableBody');

    const matkulModal        = document.getElementById('matkulModalOverlay');
    const btnOpenAddMatkul   = document.getElementById('btnOpenAddMatkulModal');
    const btnCloseMatkulModal= document.getElementById('btnCloseMatkulModal');

    function openMatkulModal() {
        if (matkulModal) matkulModal.classList.add('active');
    }

    function closeMatkulModal() {
        if (matkulModal) matkulModal.classList.remove('active');
    }

    if (btnOpenAddMatkul) {
        btnOpenAddMatkul.addEventListener('click', () => {
            resetForm();
            openMatkulModal();
        });
    }

    if (btnCloseMatkulModal) btnCloseMatkulModal.addEventListener('click', closeMatkulModal);

    if (matkulModal) {
        matkulModal.addEventListener('click', (e) => {
            if (e.target === matkulModal) closeMatkulModal();
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && matkulModal && matkulModal.classList.contains('active')) {
            closeMatkulModal();
        }
    });

    function resetForm() {
        if (!form) return;
        form.reset();
        form.action = storeUrl;
        if (methodField) methodField.value = '';
        const mkId = document.getElementById('mkId');
        if (mkId) mkId.value = '';
        if (submitLabel) submitLabel.textContent = 'Simpan Mata Kuliah';
        if (formTitle) formTitle.textContent = 'Form Tambah / Edit MK';
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            resetForm();
            closeMatkulModal();
        });
    }

    // Event Delegation untuk penanganan tombol Edit
    if (tableBody) {
        tableBody.addEventListener('click', function (e) {
            const editBtn = e.target.closest('.btn-edit-mk');
            if (!editBtn) return;

            const tr = editBtn.closest('tr');
            if (!tr) return;

            const mkId = document.getElementById('mkId');
            const mkKode = document.getElementById('mkKode');
            const mkNama = document.getElementById('mkNama');
            const mkSks = document.getElementById('mkSks');
            const mkDosen = document.getElementById('mkDosen');
            const mkStatus = document.getElementById('mkStatus');
            const mkDeskripsi = document.getElementById('mkDeskripsi');

            if (mkId) mkId.value = tr.dataset.id || '';
            if (mkKode) mkKode.value = tr.dataset.kode || '';
            if (mkNama) mkNama.value = tr.dataset.nama || '';
            if (mkSks) mkSks.value = tr.dataset.sks || 3;
            if (mkDosen) mkDosen.value = tr.dataset.lecturerId || '';
            if (mkStatus) mkStatus.value = tr.dataset.status || 'active';
            if (mkDeskripsi) mkDeskripsi.value = tr.dataset.deskripsi || '';

            if (form) form.action = updateUrlTemplate.replace('__ID__', tr.dataset.id);
            if (methodField) methodField.value = 'PUT';

            if (submitLabel) submitLabel.textContent = 'Update Mata Kuliah';
            if (formTitle) formTitle.textContent = 'Form Edit Mata Kuliah';

            openMatkulModal();
        });
    }

    // Jika halaman ini adalah hasil redirect setelah validasi gagal saat mengedit
    if (isEditing) {
        if (submitLabel) submitLabel.textContent = 'Update Mata Kuliah';
        if (formTitle) formTitle.textContent = 'Form Edit Mata Kuliah';
        openMatkulModal();
    }
});
