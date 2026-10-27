/**
 * Script untuk Manajemen Materi Kuliah oleh Admin
 */
document.addEventListener('DOMContentLoaded', function() {
    let currentCourseFilter = 'all';
    let currentTypeFilter   = 'all';
    let currentSearchQuery  = '';

    // 1. FILTERING & SEARCH KARTU MATERI
    function applyFilters() {
        const cards = document.querySelectorAll('.material-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const courseId = card.dataset.courseId;
            const type = card.dataset.type;
            const text = card.textContent.toLowerCase();

            const matchCourse = (currentCourseFilter === 'all' || courseId === currentCourseFilter);
            const matchType   = (currentTypeFilter === 'all' || type === currentTypeFilter);
            const matchSearch = (!currentSearchQuery || text.includes(currentSearchQuery));

            if (matchCourse && matchType && matchSearch) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const emptyState = document.getElementById('materiEmptyState');
        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    const courseFilterDropdown = document.getElementById('courseFilterDropdown');
    const courseFilterBtn = document.getElementById('courseFilterBtn');
    const courseFilterText = document.getElementById('courseFilterText');
    const defaultCourseFilterLabel = courseFilterText ? (courseFilterText.dataset.defaultLabel || courseFilterText.textContent) : '';
    const hiddenCourseFilterInput = document.getElementById('selectFilterCourse');
    const courseFilterItems = document.querySelectorAll('#courseFilterOptions .filter-option-item');

    if (courseFilterBtn) {
        courseFilterBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (courseFilterDropdown) courseFilterDropdown.classList.toggle('open');
        });

        courseFilterItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                courseFilterItems.forEach(el => el.classList.remove('active'));
                this.classList.add('active');

                const val = this.dataset.value;
                const label = this.dataset.label;
                if (hiddenCourseFilterInput) hiddenCourseFilterInput.value = val;
                if (courseFilterText) courseFilterText.textContent = label;
                currentCourseFilter = val;
                if (courseFilterDropdown) courseFilterDropdown.classList.remove('open');
                applyFilters();
            });
        });

        document.addEventListener('click', function(e) {
            if (courseFilterDropdown && !courseFilterDropdown.contains(e.target)) {
                courseFilterDropdown.classList.remove('open');
            }
        });
    }

    const selectFilterType = document.getElementById('selectFilterType');
    if (selectFilterType) {
        selectFilterType.addEventListener('change', function() {
            currentTypeFilter = this.value;
            applyFilters();
        });
    }

    const inputSearchMateri = document.getElementById('inputSearchMateri');
    if (inputSearchMateri) {
        inputSearchMateri.addEventListener('input', function() {
            currentSearchQuery = this.value.toLowerCase().trim();
            applyFilters();
        });
    }

    window.resetFilters = function() {
        if (hiddenCourseFilterInput) {
            hiddenCourseFilterInput.value = 'all';
            if (courseFilterText) courseFilterText.textContent = defaultCourseFilterLabel;
            courseFilterItems.forEach(el => {
                if (el.dataset.value === 'all') el.classList.add('active');
                else el.classList.remove('active');
            });
        }
        currentCourseFilter = 'all';
        if (selectFilterType) selectFilterType.value = 'all';
        currentTypeFilter = 'all';
        if (inputSearchMateri) inputSearchMateri.value = '';
        currentSearchQuery = '';
        applyFilters();
    };

    // 2. MODAL UNGGAH MATERI
    const materialModal = document.getElementById('materialModalOverlay');
    const btnOpenUpload = document.getElementById('btnOpenUploadMaterialModal');
    const btnCloseMaterialModal = document.getElementById('btnCloseMaterialModal');
    const btnCancelMaterialModal = document.getElementById('btnCancelMaterialModal');

    const typePills = document.querySelectorAll('.type-pill');
    const fileUploadContainer = document.getElementById('fileUploadContainer');
    const linkInputContainer = document.getElementById('linkInputContainer');
    const materialDropzone = document.getElementById('materialDropzone');
    const materialFileInput = document.getElementById('materialFileInput');
    const dropzoneText = document.getElementById('dropzoneText');
    const fileStatus = document.getElementById('fileStatus');
    const materialTypeInput = document.getElementById('materialType');

    const defaultDropzoneText = dropzoneText ? dropzoneText.textContent : 'Klik untuk memilih file PDF atau PPTX, atau seret berkas ke sini';

    function showFileStatus(ok, message) {
        if (!fileStatus) return;
        fileStatus.textContent = message;
        fileStatus.style.display = 'block';
        fileStatus.style.background = ok ? '#ECFDF5' : '#FEF2F2';
        fileStatus.style.border = '1px solid ' + (ok ? '#A7F3D0' : '#FECACA');
        fileStatus.style.color = ok ? '#047857' : '#B91C1C';
    }

    function resetFileStatus() {
        if (!fileStatus) return;
        fileStatus.textContent = '';
        fileStatus.style.display = 'none';
        if (dropzoneText) dropzoneText.textContent = defaultDropzoneText;
    }

    function setType(activeType) {
        typePills.forEach(p => p.classList.toggle('active', p.getAttribute('data-type') === activeType));
        if (materialTypeInput) materialTypeInput.value = activeType;

        if (activeType === 'link') {
            if (fileUploadContainer) fileUploadContainer.style.display = 'none';
            if (linkInputContainer) linkInputContainer.style.display = 'block';
            if (materialFileInput) materialFileInput.value = '';
            resetFileStatus();
        } else {
            if (fileUploadContainer) fileUploadContainer.style.display = 'block';
            if (linkInputContainer) linkInputContainer.style.display = 'none';
        }
    }

    typePills.forEach(pill => {
        pill.addEventListener('click', () => setType(pill.getAttribute('data-type')));
    });

    if (materialTypeInput) {
        setType(materialTypeInput.value === 'link' ? 'link' : 'file');
    }

    if (materialDropzone && materialFileInput) {
        materialFileInput.addEventListener('change', () => {
            const file = materialFileInput.files[0];
            if (!file) {
                resetFileStatus();
                return;
            }

            const ext = file.name.split('.').pop().toLowerCase();
            if (!['pdf', 'ppt', 'pptx'].includes(ext)) {
                materialFileInput.value = '';
                if (dropzoneText) dropzoneText.textContent = defaultDropzoneText;
                showFileStatus(false, '✕ Format tidak didukung. Pilih berkas PDF, PPT, atau PPTX.');
                return;
            }

            if (file.size > 50 * 1024 * 1024) {
                materialFileInput.value = '';
                if (dropzoneText) dropzoneText.textContent = defaultDropzoneText;
                showFileStatus(false, '✕ Ukuran berkas maksimal 50 MB.');
                return;
            }

            const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
            if (dropzoneText) dropzoneText.textContent = file.name;
            showFileStatus(true, `✓ Berkas dipilih: ${file.name} (${sizeMb} MB)`);
        });
    }

    function openMaterialModal() {
        if (materialModal) materialModal.classList.add('active');
    }

    function closeMaterialModal() {
        if (materialModal) materialModal.classList.remove('active');
    }

    if (btnOpenUpload) btnOpenUpload.addEventListener('click', openMaterialModal);
    if (btnCloseMaterialModal) btnCloseMaterialModal.addEventListener('click', closeMaterialModal);
    if (btnCancelMaterialModal) btnCancelMaterialModal.addEventListener('click', closeMaterialModal);

    if (materialModal) {
        materialModal.addEventListener('click', (e) => {
            if (e.target === materialModal) closeMaterialModal();
        });
    }

    // 3. MODAL EDIT MATERI
    const editModal = document.getElementById('editMaterialModalOverlay');
    const formEdit = document.getElementById('formEditMaterial');
    const editId = document.getElementById('editMaterialId');
    const editTitle = document.getElementById('editMaterialTitle');
    const editDesc = document.getElementById('editMaterialDesc');
    const editUrl = document.getElementById('editMaterialUrl');
    const editFile = document.getElementById('editMaterialFile');
    const editFileContainer = document.getElementById('editFileContainer');
    const editLinkContainer = document.getElementById('editLinkContainer');
    const editCurrentFile = document.getElementById('editCurrentFile');

    function openEditModal(btn) {
        if (!formEdit) return;
        const isLink = btn.dataset.type === 'link';

        formEdit.action = formEdit.dataset.actionTemplate.replace('__ID__', btn.dataset.id);
        if (editId) editId.value = btn.dataset.id;
        if (editTitle) editTitle.value = btn.dataset.title || '';
        if (editDesc) editDesc.value = btn.dataset.description || '';
        if (editUrl) editUrl.value = btn.dataset.url || '';
        if (editFile) editFile.value = '';

        if (editFileContainer) editFileContainer.style.display = isLink ? 'none' : 'block';
        if (editLinkContainer) editLinkContainer.style.display = isLink ? 'block' : 'none';

        if (editFile) editFile.disabled = isLink;
        if (editUrl) {
            editUrl.disabled = !isLink;
            editUrl.required = isLink;
        }
        if (editCurrentFile) {
            editCurrentFile.textContent = isLink ? '' : 'Berkas saat ini: ' + (btn.dataset.filename || '-');
        }

        if (editModal) editModal.classList.add('active');
    }

    function closeEditModal() {
        if (editModal) editModal.classList.remove('active');
    }

    document.querySelectorAll('.btn-edit-material').forEach(btn => {
        btn.addEventListener('click', () => openEditModal(btn));
    });

    const btnCloseEditModal = document.getElementById('btnCloseEditModal');
    const btnCancelEditModal = document.getElementById('btnCancelEditModal');
    if (btnCloseEditModal) btnCloseEditModal.addEventListener('click', closeEditModal);
    if (btnCancelEditModal) btnCancelEditModal.addEventListener('click', closeEditModal);

    if (editModal) {
        editModal.addEventListener('click', (e) => {
            if (e.target === editModal) closeEditModal();
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (materialModal) materialModal.classList.remove('active');
            if (editModal) editModal.classList.remove('active');
        }
    });
});
