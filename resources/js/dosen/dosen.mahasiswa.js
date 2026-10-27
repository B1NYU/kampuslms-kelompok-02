/**
 * Script untuk Manajemen Mahasiswa & Peserta Kuliah oleh Dosen
 */
document.addEventListener('DOMContentLoaded', () => {
    const configEl = document.getElementById('dosenMahasiswaConfig');
    const studentsDataScript = document.getElementById('registeredStudentsData');
    let registeredStudentsDb = [];
    if (studentsDataScript) {
        try {
            registeredStudentsDb = JSON.parse(studentsDataScript.textContent || '[]');
        } catch (e) {
            console.error('Failed to parse registered students data', e);
        }
    }

    function showToast(message, isSuccess = true) {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = 'dosen-toast';
        if (!isSuccess) toast.style.borderLeftColor = '#EF4444';

        toast.innerHTML = `
            <svg class="toast-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="${isSuccess ? '#10B981' : '#EF4444'}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span>${message}</span>
        `;
        container.appendChild(toast);

        setTimeout(() => toast.classList.add('show'), 50);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 350);
        }, 3500);
    }

    const formAddStudent = document.getElementById('formAddStudent');
    const studentTableBody = document.getElementById('studentTableBody');
    const studentTableCount = document.getElementById('studentTableCount');
    const searchStudentInput = document.getElementById('searchStudentInput');
    const selectCurrentCourse = document.getElementById('selectCurrentCourse');
    const sortStudentSelect = document.getElementById('sortStudentSelect');

    // Modal Controls
    const studentModal = document.getElementById('studentModalOverlay');
    const btnOpenAddStudent = document.getElementById('btnOpenAddStudentModal');
    const btnCloseStudentModal = document.getElementById('btnCloseStudentModal');
    const btnCancelStudentModal = document.getElementById('btnCancelStudentModal');

    function openStudentModal() {
        if (!selectCurrentCourse || !selectCurrentCourse.value) {
            alert('Anda belum memiliki mata kuliah yang diampu untuk mendaftarkan mahasiswa.');
            return;
        }
        // Mata kuliah di modal mengikuti filter yang sedang aktif
        const mhsMatkul = document.getElementById('mhsMatkul');
        if (mhsMatkul) {
            const opt = Array.from(mhsMatkul.options).find(o => o.dataset.code === selectCurrentCourse.value);
            if (!opt) {
                alert('Peserta mata kuliah ini tidak dapat diubah (mis. berstatus archived).');
                return;
            }
            mhsMatkul.value = opt.value;
        }
        if (studentModal) studentModal.classList.add('active');
    }

    function closeStudentModal() {
        if (studentModal) studentModal.classList.remove('active');
    }

    if (btnOpenAddStudent) btnOpenAddStudent.addEventListener('click', openStudentModal);
    if (btnCloseStudentModal) btnCloseStudentModal.addEventListener('click', closeStudentModal);
    if (btnCancelStudentModal) btnCancelStudentModal.addEventListener('click', closeStudentModal);
    if (studentModal) {
        studentModal.addEventListener('click', (e) => {
            if (e.target === studentModal) closeStudentModal();
        });
    }
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && studentModal && studentModal.classList.contains('active')) {
            closeStudentModal();
        }
    });

    const mhsNimInput = document.getElementById('mhsNim');
    const mhsNamaInput = document.getElementById('mhsNama');
    const nimLookupStatus = document.getElementById('nimLookupStatus');

    function handleNimLookup() {
        const query = mhsNimInput ? mhsNimInput.value.trim().toLowerCase() : '';
        if (!query) {
            if (mhsNamaInput) mhsNamaInput.value = '';
            if (nimLookupStatus) {
                nimLookupStatus.textContent = 'Masukkan NIM untuk melengkapi nama secara otomatis';
                nimLookupStatus.style.color = '#64748B';
            }
            return;
        }

        // Match exact or contains
        const match = registeredStudentsDb.find(s => s.nim_nip && s.nim_nip.toLowerCase() === query);
        if (match) {
            if (mhsNamaInput) mhsNamaInput.value = match.name;
            if (nimLookupStatus) {
                nimLookupStatus.textContent = `✓ Mahasiswa teridentifikasi: ${match.name}`;
                nimLookupStatus.style.color = '#10B981';
            }
        } else {
            const partialMatch = registeredStudentsDb.find(s => s.nim_nip && s.nim_nip.toLowerCase().includes(query));
            if (partialMatch && query.length >= 6) {
                if (mhsNamaInput) mhsNamaInput.value = partialMatch.name;
                if (nimLookupStatus) {
                    nimLookupStatus.textContent = `✓ Mahasiswa teridentifikasi: ${partialMatch.name} (${partialMatch.nim_nip})`;
                    nimLookupStatus.style.color = '#10B981';
                }
            } else {
                if (mhsNamaInput) mhsNamaInput.value = '';
                if (nimLookupStatus) {
                    nimLookupStatus.textContent = 'NIM tidak terdaftar di sistem. Periksa kembali NIM.';
                    nimLookupStatus.style.color = '#EF4444';
                }
            }
        }
    }

    if (mhsNimInput) {
        mhsNimInput.addEventListener('input', handleNimLookup);
        mhsNimInput.addEventListener('change', handleNimLookup);
    }

    if (formAddStudent) {
        formAddStudent.addEventListener('submit', (e) => {
            if (!mhsNimInput.value.trim() || !mhsNamaInput.value.trim()) {
                e.preventDefault();
                alert('Harap masukkan NIM yang valid dan terdaftar di database agar nama terisi otomatis!');
            }
        });
    }

    function sortStudentRows() {
        if (!studentTableBody) return;
        const sortBy = sortStudentSelect ? sortStudentSelect.value : 'default';
        const rows = Array.from(studentTableBody.querySelectorAll('tr[data-mk]'));

        rows.sort((a, b) => {
            if (sortBy === 'name-asc') {
                const nameA = (a.dataset.name || a.querySelector('.student-name')?.textContent || '').toLowerCase().trim();
                const nameB = (b.dataset.name || b.querySelector('.student-name')?.textContent || '').toLowerCase().trim();
                return nameA.localeCompare(nameB, 'id', { sensitivity: 'base' });
            } else if (sortBy === 'name-desc') {
                const nameA = (a.dataset.name || a.querySelector('.student-name')?.textContent || '').toLowerCase().trim();
                const nameB = (b.dataset.name || b.querySelector('.student-name')?.textContent || '').toLowerCase().trim();
                return nameB.localeCompare(nameA, 'id', { sensitivity: 'base' });
            } else if (sortBy === 'nim-asc') {
                const nimA = (a.dataset.nim || a.querySelector('.student-nim')?.textContent || '').trim();
                const nimB = (b.dataset.nim || b.querySelector('.student-nim')?.textContent || '').trim();
                return nimA.localeCompare(nimB, undefined, { numeric: true });
            } else if (sortBy === 'nim-desc') {
                const nimA = (a.dataset.nim || a.querySelector('.student-nim')?.textContent || '').trim();
                const nimB = (b.dataset.nim || b.querySelector('.student-nim')?.textContent || '').trim();
                return nimB.localeCompare(nimA, undefined, { numeric: true });
            } else {
                const orderA = parseInt(a.dataset.order || '0', 10);
                const orderB = parseInt(b.dataset.order || '0', 10);
                return orderA - orderB;
            }
        });

        rows.forEach(row => {
            studentTableBody.appendChild(row);
        });
    }

    if (sortStudentSelect) {
        sortStudentSelect.addEventListener('change', () => {
            sortStudentRows();
            applyCourseFilter();
        });
    }

    function applyCourseFilter() {
        if (!studentTableBody) return;
        const currentMk = selectCurrentCourse ? selectCurrentCourse.value : '';
        const query = (searchStudentInput ? searchStudentInput.value : '').toLowerCase().trim();
        let visibleCount = 0;

        const rows = studentTableBody.querySelectorAll('tr[data-mk]');
        rows.forEach(row => {
            const matchMk = (!currentMk || row.dataset.mk === currentMk);
            const text = row.textContent.toLowerCase();
            const matchSearch = (!query || text.includes(query));

            if (matchMk && matchSearch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (studentTableCount) studentTableCount.textContent = visibleCount;

        const emptyRow = document.getElementById('emptyCourseRow');
        if (emptyRow) {
            emptyRow.style.display = visibleCount === 0 ? '' : 'none';
        }
    }

    if (selectCurrentCourse) {
        selectCurrentCourse.addEventListener('change', applyCourseFilter);
    }

    if (searchStudentInput) {
        searchStudentInput.addEventListener('input', applyCourseFilter);
    }

    sortStudentRows();
    applyCourseFilter();

    if (configEl) {
        if (configEl.dataset.toastSuccess) {
            showToast(configEl.dataset.toastSuccess);
        }
        if (configEl.dataset.hasErrors === '1') {
            if (configEl.dataset.toastError) {
                showToast(configEl.dataset.toastError, false);
            }
            if (studentModal) studentModal.classList.add('active');
            handleNimLookup();
        }
    }
});
