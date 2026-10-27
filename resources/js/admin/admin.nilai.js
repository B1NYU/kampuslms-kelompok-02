/**
 * Script untuk Rekapitulasi Nilai & Penilaian oleh Admin
 */
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchNilaiInput');
    const courseSelect = document.getElementById('filterCourseSelect');
    const statusSelect = document.getElementById('filterStatusSelect');
    const gradeSelect = document.getElementById('filterGradeSelect');
    const badgeTotalCount = document.getElementById('badgeTotalCount');
    const rows = document.querySelectorAll('.nilai-row');
    const emptyRow = document.getElementById('emptySearchRow');

    function applyFilters() {
        const searchVal = searchInput ? searchInput.value.trim().toLowerCase() : '';
        const courseVal = courseSelect ? courseSelect.value : 'all';
        const statusVal = statusSelect ? statusSelect.value : 'all';
        const gradeVal = gradeSelect ? gradeSelect.value : 'all';

        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const nim = row.getAttribute('data-nim') || '';
            const assignment = row.getAttribute('data-assignment') || '';
            const courseCode = row.getAttribute('data-course-code') || '';
            const status = row.getAttribute('data-status') || '';
            const letter = row.getAttribute('data-letter') || '';

            const matchSearch = !searchVal || 
                name.includes(searchVal) || 
                nim.includes(searchVal) || 
                assignment.includes(searchVal);

            const matchCourse = (courseVal === 'all') || (courseCode === courseVal);
            const matchStatus = (statusVal === 'all') || (status === statusVal);

            let matchGrade = true;
            if (gradeVal === 'A') {
                matchGrade = letter.startsWith('A');
            } else if (gradeVal === 'B') {
                matchGrade = letter.startsWith('B');
            } else if (gradeVal === 'C') {
                matchGrade = letter.startsWith('C');
            } else if (gradeVal === 'remedial') {
                matchGrade = (letter === 'D' || letter === 'E');
            }

            if (matchSearch && matchCourse && matchStatus && matchGrade) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (badgeTotalCount) {
            badgeTotalCount.textContent = `Menampilkan ${visibleCount} dari ${rows.length} Pengumpulan`;
        }
        if (emptyRow) {
            emptyRow.style.display = visibleCount === 0 ? '' : 'none';
        }
    }

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (courseSelect) courseSelect.addEventListener('change', applyFilters);
    if (statusSelect) statusSelect.addEventListener('change', applyFilters);
    if (gradeSelect) gradeSelect.addEventListener('change', applyFilters);

    // Modal Detail Lembar Nilai
    const modal = document.getElementById('modalGradeDetail');
    const btnCloseModal = document.getElementById('btnCloseModal');

    function openModal(itemData) {
        if (!modal) return;
        const modalStudentName = document.getElementById('modalStudentName');
        const modalStudentNim = document.getElementById('modalStudentNim');
        const modalAvatar = document.getElementById('modalAvatar');
        const modalScoreVal = document.getElementById('modalScoreVal');
        const modalGradePill = document.getElementById('modalGradePill');
        const modalCourse = document.getElementById('modalCourse');
        const modalLecturer = document.getElementById('modalLecturer');
        const modalAssignment = document.getElementById('modalAssignment');
        const modalSubmittedAt = document.getElementById('modalSubmittedAt');
        const modalGradedAt = document.getElementById('modalGradedAt');
        const modalFeedbackText = document.getElementById('modalFeedbackText');
        const modalFileName = document.getElementById('modalFileName');
        const modalFileSize = document.getElementById('modalFileSize');

        if (modalStudentName) modalStudentName.textContent = itemData.student_name;
        if (modalStudentNim) modalStudentNim.textContent = 'NIM: ' + itemData.student_nim;
        
        // Initials
        const parts = (itemData.student_name || '').trim().split(' ');
        const initials = (parts[0] ? parts[0][0] : 'M') + (parts[1] ? parts[1][0] : '');
        if (modalAvatar) modalAvatar.textContent = initials.toUpperCase();

        // Score & Letter
        if (itemData.score !== null && itemData.score !== '') {
            if (modalScoreVal) modalScoreVal.textContent = itemData.score;
            if (modalGradePill) {
                modalGradePill.textContent = itemData.letter;
                modalGradePill.className = 'grade-pill ' + itemData.letter_class;
            }
        } else {
            if (modalScoreVal) modalScoreVal.textContent = '-';
            if (modalGradePill) {
                modalGradePill.textContent = 'Belum Dinilai';
                modalGradePill.className = 'grade-pill grade-pill-pending';
            }
        }

        if (modalCourse) modalCourse.textContent = itemData.course_code + ' • ' + itemData.course_name;
        if (modalLecturer) modalLecturer.textContent = itemData.lecturer_name;
        if (modalAssignment) modalAssignment.textContent = itemData.assignment_title;
        if (modalSubmittedAt) modalSubmittedAt.textContent = itemData.submitted_at + (itemData.is_late ? ' (Terlambat)' : ' (Tepat Waktu)');

        if (modalGradedAt) modalGradedAt.textContent = itemData.graded_at !== '-' ? 'Dinilai: ' + itemData.graded_at : 'Menunggu Evaluasi';
        if (modalFeedbackText) modalFeedbackText.textContent = itemData.feedback;

        if (modalFileName) modalFileName.textContent = itemData.file_name;
        if (modalFileSize) modalFileSize.textContent = 'Ukuran berkas: ' + itemData.file_size;

        modal.classList.add('active');
    }

    function closeModal() {
        if (modal) modal.classList.remove('active');
    }

    document.querySelectorAll('.nilai-row').forEach(row => {
        row.addEventListener('click', function () {
            const rawData = this.getAttribute('data-item');
            try {
                const itemData = JSON.parse(rawData);
                openModal(itemData);
            } catch (e) {
                console.error('Failed to parse item data', e);
            }
        });
    });

    if (btnCloseModal) btnCloseModal.addEventListener('click', closeModal);

    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
            closeModal();
        }
    });

    function showToast(msg) {
        const toast = document.getElementById('toastNilai');
        const textEl = document.getElementById('toastNilaiText');
        if (!toast || !textEl) return;
        textEl.textContent = msg;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    const btnDownload = document.getElementById('btnDownloadFile');
    if (btnDownload) {
        btnDownload.addEventListener('click', function () {
            const fileName = document.getElementById('modalFileName')?.textContent || 'berkas';
            showToast(`Memulai pengunduhan berkas: ${fileName}`);
        });
    }

    // Ekspor CSV
    const btnExport = document.getElementById('btnExportCsv');
    if (btnExport) {
        btnExport.addEventListener('click', function () {
            let csvRows = [];
            csvRows.push([
                'No',
                'NIM',
                'Nama Mahasiswa',
                'Kode MK',
                'Mata Kuliah',
                'Dosen Pengampu',
                'Tugas Kuliah',
                'Waktu Pengumpulan',
                'Status Waktu',
                'Nilai Angka',
                'Predikat Huruf',
                'Status Penilaian',
                'Feedback Dosen'
            ].map(val => `"${val}"`).join(','));

            let visibleIdx = 1;
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    try {
                        const data = JSON.parse(row.getAttribute('data-item'));
                        csvRows.push([
                            visibleIdx++,
                            data.student_nim,
                            data.student_name,
                            data.course_code,
                            data.course_name,
                            data.lecturer_name,
                            data.assignment_title,
                            data.submitted_at,
                            data.is_late ? 'Terlambat' : 'Tepat Waktu',
                            data.score ?? '-',
                            data.letter,
                            data.status === 'graded' ? 'Sudah Dinilai' : 'Menunggu Penilaian',
                            (data.feedback || '').replace(/"/g, '""')
                        ].map(val => `"${val}"`).join(','));
                    } catch (e) {}
                }
            });

            const csvString = '\uFEFF' + csvRows.join('\r\n');
            const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `rekap_nilai_mahasiswa_${new Date().toISOString().slice(0, 10)}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);

            showToast('Berkas rekap nilai CSV berhasil diunduh!');
        });
    }
});
