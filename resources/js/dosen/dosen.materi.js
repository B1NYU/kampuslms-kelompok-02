/**
 * Script untuk Manajemen Materi Perkuliahan Dosen
 */
document.addEventListener('DOMContentLoaded', () => {
    const configEl = document.getElementById('dosenMateriConfig');

    const typePills = document.querySelectorAll('.type-pill');
    const fileUploadContainer = document.getElementById('fileUploadContainer');
    const linkInputContainer = document.getElementById('linkInputContainer');
    const materialDropzone = document.getElementById('materialDropzone');
    const materialFileInput = document.getElementById('materialFileInput');
    const dropzoneText = document.getElementById('dropzoneText');
    const fileStatus = document.getElementById('fileStatus');
    const materialTypeInput = document.getElementById('materialType');

    const defaultDropzoneText = dropzoneText ? dropzoneText.textContent : 'Klik untuk memilih file PDF atau PPTX, atau seret berkas ke sini';

    const materialPreviewContainer = document.getElementById('materialPreviewContainer');
    const materialFileTypeBadge = document.getElementById('materialFileTypeBadge');
    const materialPreviewName = document.getElementById('materialPreviewName');
    const materialPreviewSize = document.getElementById('materialPreviewSize');
    const materialInteractivePreview = document.getElementById('materialInteractivePreview');
    const materialEmbedHolder = document.getElementById('materialEmbedHolder');
    const btnChangeMaterial = document.getElementById('btnChangeMaterial');
    const btnRemoveMaterial = document.getElementById('btnRemoveMaterial');
    let materialCurrentObjectUrl = null;

    // Tampilkan status berkas di bawah dropzone (hijau = berhasil, merah = ditolak)
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
        if (materialPreviewContainer) materialPreviewContainer.style.display = 'none';
        if (materialDropzone) materialDropzone.style.display = 'block';
        if (materialInteractivePreview) materialInteractivePreview.style.display = 'none';
        if (materialEmbedHolder) materialEmbedHolder.innerHTML = '';
        if (materialCurrentObjectUrl) {
            URL.revokeObjectURL(materialCurrentObjectUrl);
            materialCurrentObjectUrl = null;
        }
    }

    function clearMaterialPreview() {
        if (materialFileInput) materialFileInput.value = '';
        resetFileStatus();
    }

    if (btnRemoveMaterial) {
        btnRemoveMaterial.addEventListener('click', (e) => {
            e.preventDefault();
            clearMaterialPreview();
        });
    }

    if (btnChangeMaterial) {
        btnChangeMaterial.addEventListener('click', (e) => {
            e.preventDefault();
            if (materialFileInput) materialFileInput.click();
        });
    }

    function setType(activeType) {
        typePills.forEach(p => p.classList.toggle('active', p.getAttribute('data-type') === activeType));
        if (materialTypeInput) materialTypeInput.value = activeType;

        if (activeType === 'link') {
            if (fileUploadContainer) fileUploadContainer.style.display = 'none';
            if (linkInputContainer) linkInputContainer.style.display = 'block';
            clearMaterialPreview();
        } else {
            if (fileUploadContainer) fileUploadContainer.style.display = 'block';
            if (linkInputContainer) linkInputContainer.style.display = 'none';
        }
    }

    typePills.forEach(pill => {
        pill.addEventListener('click', () => setType(pill.getAttribute('data-type')));
    });

    // Pulihkan pilihan format setelah validasi gagal
    if (materialTypeInput) {
        setType(materialTypeInput.value === 'link' ? 'link' : 'file');
    }

    // ===== Dropzone Events =====
    if (materialDropzone) {
        ['dragenter', 'dragover'].forEach(evt => {
            materialDropzone.addEventListener(evt, () => {
                materialDropzone.style.borderColor = '#039FFA';
                materialDropzone.style.background = 'rgba(3, 159, 250, 0.06)';
            });
        });
        ['dragleave', 'drop'].forEach(evt => {
            materialDropzone.addEventListener(evt, () => {
                materialDropzone.style.borderColor = '';
                materialDropzone.style.background = '';
            });
        });

        ['dragover', 'drop'].forEach(evt => {
            window.addEventListener(evt, (e) => {
                if (!materialDropzone.contains(e.target)) e.preventDefault();
            });
        });
    }

    if (materialFileInput) {
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
                if (materialPreviewContainer) materialPreviewContainer.style.display = 'none';
                if (materialDropzone) materialDropzone.style.display = 'block';
                showFileStatus(false, '✕ Format tidak didukung. Pilih berkas PDF, PPT, atau PPTX.');
                return;
            }

            if (file.size > 50 * 1024 * 1024) {
                materialFileInput.value = '';
                if (dropzoneText) dropzoneText.textContent = defaultDropzoneText;
                if (materialPreviewContainer) materialPreviewContainer.style.display = 'none';
                if (materialDropzone) materialDropzone.style.display = 'block';
                showFileStatus(false, '✕ Ukuran berkas maksimal 50 MB.');
                return;
            }

            const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
            if (dropzoneText) dropzoneText.textContent = file.name;
            showFileStatus(true, `✓ Berkas dipilih: ${file.name} (${sizeMb} MB)`);

            // Update Preview Card Details
            if (materialPreviewName) materialPreviewName.textContent = file.name;
            if (materialPreviewSize) materialPreviewSize.textContent = `${sizeMb} MB`;

            if (materialFileTypeBadge) {
                if (ext === 'pdf') {
                    materialFileTypeBadge.textContent = 'PDF';
                    materialFileTypeBadge.style.background = '#FEE2E2';
                    materialFileTypeBadge.style.color = '#DC2626';
                } else {
                    materialFileTypeBadge.textContent = ext.toUpperCase();
                    materialFileTypeBadge.style.background = '#FFEDD5';
                    materialFileTypeBadge.style.color = '#EA580C';
                }
            }

            // Interactive preview untuk berkas PDF
            if (materialCurrentObjectUrl) {
                URL.revokeObjectURL(materialCurrentObjectUrl);
                materialCurrentObjectUrl = null;
            }
            if (materialEmbedHolder) materialEmbedHolder.innerHTML = '';

            if (ext === 'pdf' && materialEmbedHolder && materialInteractivePreview) {
                materialCurrentObjectUrl = URL.createObjectURL(file);
                const embed = document.createElement('embed');
                embed.src = materialCurrentObjectUrl;
                embed.type = 'application/pdf';
                embed.style.width = '100%';
                embed.style.height = '100%';
                embed.style.border = 'none';
                embed.style.display = 'block';
                materialEmbedHolder.appendChild(embed);
                materialInteractivePreview.style.display = 'block';
            } else if (materialInteractivePreview) {
                materialInteractivePreview.style.display = 'none';
            }

            if (materialPreviewContainer) materialPreviewContainer.style.display = 'block';
            if (materialDropzone) materialDropzone.style.display = 'none';
        });
    }

    // ===== Modal Controls =====
    const materialModal = document.getElementById('materialModalOverlay');
    const btnOpenUpload = document.getElementById('btnOpenUploadMaterialModal');
    const btnCloseMaterialModal = document.getElementById('btnCloseMaterialModal');
    const btnCancelMaterialModal = document.getElementById('btnCancelMaterialModal');

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
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && materialModal && materialModal.classList.contains('active')) {
            closeMaterialModal();
        }
    });

    // Validasi server gagal -> buka lagi modal upload
    if (configEl && configEl.dataset.hasErrors === '1') {
        openMaterialModal();
    }

    // ===== Edit Materi =====
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

    function openEditModal(card) {
        if (!formEdit) return;
        const isLink = card.dataset.type === 'link';

        formEdit.action = formEdit.dataset.actionTemplate.replace('__ID__', card.dataset.id);
        if (editId) editId.value = card.dataset.id;
        if (editTitle) editTitle.value = card.dataset.title || '';
        if (editDesc) editDesc.value = card.dataset.description || '';
        if (editUrl) editUrl.value = card.dataset.url || '';
        if (editFile) editFile.value = '';

        if (editFileContainer) editFileContainer.style.display = isLink ? 'none' : 'block';
        if (editLinkContainer) editLinkContainer.style.display = isLink ? 'block' : 'none';

        if (editFile) editFile.disabled = isLink;
        if (editUrl) {
            editUrl.disabled = !isLink;
            editUrl.required = isLink;
        }
        if (editCurrentFile) {
            editCurrentFile.textContent = isLink ? '' : 'Berkas saat ini: ' + (card.dataset.filename || '-');
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
        editModal.addEventListener('click', (e) => { if (e.target === editModal) closeEditModal(); });
    }
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && editModal && editModal.classList.contains('active')) closeEditModal();
    });

    // Validasi edit gagal -> buka lagi modal edit dengan isian terakhir
    if (configEl && configEl.dataset.editErrorId) {
        const errorId = configEl.dataset.editErrorId;
        const btn = document.querySelector(`.btn-edit-material[data-id="${errorId}"]`);
        if (btn) {
            openEditModal(btn);
            if (editTitle) editTitle.value = configEl.dataset.oldTitle || '';
            if (editDesc) editDesc.value = configEl.dataset.oldDesc || '';
            if (editUrl && !editUrl.disabled) editUrl.value = configEl.dataset.oldUrl || '';
        }
    }

    // ===== Course Filter =====
    const selectCurrentCourse = document.getElementById('selectCurrentCourse');
    const materialCourse = document.getElementById('materialCourse');
    const materialsGrid = document.getElementById('materialsGrid');
    const materialListCount = document.getElementById('materialListCount');

    function applyCourseFilter() {
        if (!selectCurrentCourse || !materialsGrid) return;

        const selectedVal = selectCurrentCourse.value;
        let visibleCount = 0;

        materialsGrid.querySelectorAll('.material-card').forEach(card => {
            const show = !selectedVal || card.getAttribute('data-course-id') === selectedVal;
            card.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        if (materialListCount) materialListCount.textContent = visibleCount;

        // Dropdown di modal ikut mata kuliah yang sedang dilihat
        if (configEl && configEl.dataset.hasErrors !== '1') {
            if (materialCourse && selectedVal) materialCourse.value = selectedVal;
        }
    }

    if (selectCurrentCourse) {
        selectCurrentCourse.addEventListener('change', applyCourseFilter);
        applyCourseFilter();
    }
});
