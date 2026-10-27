/**
 * Script untuk Manajemen Tugas Perkuliahan oleh Admin
 */
document.addEventListener('DOMContentLoaded', function() {
    const configEl = document.getElementById('tugasConfig');
    const storeUrl = configEl ? configEl.dataset.storeUrl : '';

    let currentCourseFilter = 'all';
    let currentStatusFilter = 'all';
    let currentSearchQuery  = '';

    const modalOverlay = document.getElementById('tugasModal');
    const btnOpenAddModal = document.getElementById('btnOpenAddModal');
    const btnCloseModal = document.getElementById('btnCloseModal');
    const btnCancelModal = document.getElementById('btnCancelModal');
    const formTugas = document.getElementById('formTugas');
    const formMethodField = document.getElementById('formMethodField');
    const modalTitle = document.getElementById('modalTitle');
    const editTugasId = document.getElementById('editTugasId');
    const btnSubmitText = document.getElementById('btnSubmitText');

    const modalCourseSelect = document.getElementById('modalCourseSelect');
    const modalTugasStatus = document.getElementById('modalTugasStatus');
    const taskSession = document.getElementById('taskSession');
    const modalTugasTitle = document.getElementById('modalTugasTitle');

    // Sesi Pertemuan generator
    if (taskSession) {
        taskSession.addEventListener('change', function() {
            const sessionVal = this.value;
            if (sessionVal && (!modalTugasTitle.value || modalTugasTitle.value.startsWith('Tugas Pertemuan'))) {
                modalTugasTitle.value = `Tugas Pertemuan ${sessionVal}: `;
                modalTugasTitle.focus();
            }
        });
    }

    // FILTERING & SEARCH
    function applyFilters() {
        const cards = document.querySelectorAll('.assignment-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const courseId = card.dataset.courseId;
            const status = card.dataset.status;
            const text = card.textContent.toLowerCase();

            const matchCourse = (currentCourseFilter === 'all' || courseId === currentCourseFilter);
            const matchStatus = (currentStatusFilter === 'all' || status === currentStatusFilter);
            const matchSearch = (!currentSearchQuery || text.includes(currentSearchQuery));

            if (matchCourse && matchStatus && matchSearch) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const emptyState = document.getElementById('tugasFilterEmptyState');
        if (emptyState) {
            emptyState.style.display = (cards.length > 0 && visibleCount === 0) ? 'block' : 'none';
        }
    }

    // Custom Course Filter Dropdown di Toolbar
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

    const selectFilterStatus = document.getElementById('selectFilterStatus');
    if (selectFilterStatus) {
        selectFilterStatus.addEventListener('change', function() {
            currentStatusFilter = this.value;
            applyFilters();
        });
    }

    const inputSearchTugas = document.getElementById('inputSearchTugas');
    if (inputSearchTugas) {
        inputSearchTugas.addEventListener('input', function() {
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
        if (selectFilterStatus) selectFilterStatus.value = 'all';
        currentStatusFilter = 'all';
        if (inputSearchTugas) inputSearchTugas.value = '';
        currentSearchQuery = '';
        applyFilters();
    };

    // MODAL CONTROLS (CREATE & EDIT)
    function openModal(isEdit = false, card = null) {
        if (!modalOverlay || !formTugas) return;
        if (isEdit && card) {
            if (modalTitle) modalTitle.textContent = 'Edit Tugas Perkuliahan';
            if (btnSubmitText) btnSubmitText.textContent = 'Perbarui Tugas';
            if (editTugasId) editTugasId.value = card.dataset.id;
            formTugas.action = `/admin/tugas/${card.dataset.id}`;
            if (formMethodField) formMethodField.value = 'PUT';

            if (modalCourseSelect) {
                modalCourseSelect.value = card.dataset.courseId || '';
            }

            if (modalTugasTitle) modalTugasTitle.value = card.dataset.title || '';
            const modalInstructions = document.getElementById('modalTugasInstructions');
            if (modalInstructions) modalInstructions.value = card.dataset.instructions || '';
            const modalScore = document.getElementById('modalTugasMaxScore');
            if (modalScore) modalScore.value = card.dataset.score || 100;
            const modalAllowLate = document.getElementById('modalTugasAllowLate');
            if (modalAllowLate) modalAllowLate.checked = card.dataset.allowLate == '1';

            if (card.dataset.dueDate) {
                const dueDateEl = document.getElementById('modalTugasDueDate');
                if (dueDateEl) dueDateEl.value = card.dataset.dueDate;
            }
            if (card.dataset.dueTime) {
                const dueTimeEl = document.getElementById('modalTugasDueTime');
                if (dueTimeEl) dueTimeEl.value = card.dataset.dueTime;
            }

            if (taskSession) {
                taskSession.value = card.dataset.session || '';
            }

            if (modalTugasStatus) {
                modalTugasStatus.value = card.dataset.status || 'published';
            }
        } else {
            if (modalTitle) modalTitle.textContent = 'Buat Penugasan Baru';
            if (btnSubmitText) btnSubmitText.textContent = 'Simpan Tugas';
            formTugas.action = storeUrl;
            if (formMethodField) formMethodField.value = '';
            if (editTugasId) editTugasId.value = '';

            formTugas.reset();

            // Set default due date: 7 hari dari sekarang jam 23:59
            const d = new Date();
            d.setDate(d.getDate() + 7);
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            const dueDateEl = document.getElementById('modalTugasDueDate');
            if (dueDateEl) dueDateEl.value = `${yyyy}-${mm}-${dd}`;
            const dueTimeEl = document.getElementById('modalTugasDueTime');
            if (dueTimeEl) dueTimeEl.value = '23:59';
            const modalScore = document.getElementById('modalTugasMaxScore');
            if (modalScore) modalScore.value = 100;
            const modalAllowLate = document.getElementById('modalTugasAllowLate');
            if (modalAllowLate) modalAllowLate.checked = true;

            if (modalTugasStatus) {
                modalTugasStatus.value = 'published';
            }
            if (modalCourseSelect) {
                modalCourseSelect.value = '';
            }
            if (taskSession) {
                taskSession.value = '';
            }
        }

        modalOverlay.classList.add('active');
        modalOverlay.classList.add('show');
    }

    function closeModal() {
        if (!modalOverlay) return;
        modalOverlay.classList.remove('active');
        modalOverlay.classList.remove('show');
    }

    if (btnOpenAddModal) {
        btnOpenAddModal.addEventListener('click', () => openModal(false));
    }
    if (btnCloseModal) {
        btnCloseModal.addEventListener('click', closeModal);
    }
    if (btnCancelModal) {
        btnCancelModal.addEventListener('click', closeModal);
    }
    if (modalOverlay) {
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) closeModal();
        });
    }
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modalOverlay && (modalOverlay.classList.contains('active') || modalOverlay.classList.contains('show'))) {
            closeModal();
        }
    });

    // Bind Edit Buttons on Cards
    document.querySelectorAll('.assignment-card').forEach(card => {
        const btnEdit = card.querySelector('.btn-edit-tugas');
        if (btnEdit) {
            btnEdit.addEventListener('click', (e) => {
                e.stopPropagation();
                openModal(true, card);
            });
        }
    });

    // Toast Notifications
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
