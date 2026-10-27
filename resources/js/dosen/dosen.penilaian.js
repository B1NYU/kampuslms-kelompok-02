/**
 * Script untuk Penilaian Tugas Mahasiswa oleh Dosen
 */
document.addEventListener('DOMContentLoaded', () => {
    const configEl = document.getElementById('dosenPenilaianConfig');
    const gradeUrlTemplate = configEl ? configEl.dataset.gradeUrlTemplate : '';

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

    const modalOverlay = document.getElementById('gradingModalOverlay');
    const btnCloseGradingModal = document.getElementById('btnCloseGradingModal');
    const btnCancelGrading = document.getElementById('btnCancelGrading');
    const btnSaveGrading = document.getElementById('btnSaveGrading');
    const modalAssignmentTitle = document.getElementById('modalAssignmentTitle');
    const modalStudentName = document.getElementById('modalStudentName');
    const inputScore = document.getElementById('inputScore');
    const inputFeedback = document.getElementById('inputFeedback');
    const modalMaxLabel = document.getElementById('modalMaxLabel');

    let activeSubmissionId = null;
    let activeMax = 100;   // nilai maksimal tugas yang sedang dinilai (dari data-max)

    // Pintasan nilai = persentase dari nilai maksimal tugas, bukan angka tetap.
    document.querySelectorAll('.btn-score-quick').forEach(b => {
        b.addEventListener('click', () => {
            if (inputScore) {
                inputScore.value = Math.round(activeMax * Number(b.dataset.pct) / 100);
            }
        });
    });

    function attachGradingButtons() {
        const gradeBtns = document.querySelectorAll('.btn-grade-action');
        gradeBtns.forEach(btn => {
            btn.onclick = function() {
                activeSubmissionId = btn.getAttribute('data-id');
                const student = btn.getAttribute('data-student');
                const title = btn.getAttribute('data-title');
                const score = btn.getAttribute('data-score') || '';
                const feedback = btn.getAttribute('data-feedback') || '';
                activeMax = Number(btn.getAttribute('data-max')) || 100;

                if (modalMaxLabel) modalMaxLabel.textContent = activeMax;
                if (inputScore) inputScore.max = activeMax;
                if (modalAssignmentTitle) modalAssignmentTitle.textContent = title;
                if (modalStudentName) modalStudentName.textContent = `Mahasiswa: ${student}`;
                if (inputScore) inputScore.value = score;
                if (inputFeedback) inputFeedback.value = feedback;

                if (modalOverlay) {
                    modalOverlay.style.display = 'flex';
                    if (inputScore) inputScore.focus();
                }
            };
        });
    }
    attachGradingButtons();

    function closeModal() {
        if (modalOverlay) modalOverlay.style.display = 'none';
        activeSubmissionId = null;
    }

    if (btnCloseGradingModal) btnCloseGradingModal.addEventListener('click', closeModal);
    if (btnCancelGrading) btnCancelGrading.addEventListener('click', closeModal);

    if (modalOverlay) {
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) closeModal();
        });
    }

    if (btnSaveGrading) {
        btnSaveGrading.addEventListener('click', async () => {
            const score = parseFloat(inputScore ? inputScore.value : 0);
            const feedback = inputFeedback ? inputFeedback.value.trim() : '';

            if (isNaN(score) || score < 0 || score > activeMax) {
                alert('Harap masukkan nilai valid antara 0 hingga ' + activeMax + '!');
                return;
            }

            if (!feedback) {
                alert('Harap masukkan feedback / ulasan konstruktif bagi mahasiswa!');
                return;
            }

            const originalBtnHtml = btnSaveGrading.innerHTML;
            btnSaveGrading.disabled = true;
            btnSaveGrading.innerHTML = 'Menyimpan ke Database...';

            try {
                const gradeUrl = gradeUrlTemplate.replace('__ID__', activeSubmissionId);

                const res = await fetch(gradeUrl, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ score: score, feedback: feedback })
                });

                const data = await res.json().catch(() => ({}));

                if (res.ok && data && data.success) {
                    const targetRow = document.querySelector(`#submissionTableBody tr[data-id="${activeSubmissionId}"]`);
                    if (targetRow) {
                        targetRow.setAttribute('data-status', 'graded');

                        const scoreElem = targetRow.querySelector('.item-score');
                        if (scoreElem) {
                            scoreElem.className = 'score-badge score-badge-graded item-score';
                            scoreElem.textContent = `${score} / ${activeMax}`;
                        }

                        const feedbackElem = targetRow.querySelector('.item-feedback');
                        if (feedbackElem) {
                            feedbackElem.style.color = '#0F172A';
                            feedbackElem.style.fontStyle = 'normal';
                            feedbackElem.title = feedback;
                            feedbackElem.textContent = `"${feedback.length > 60 ? feedback.substring(0, 60) + '...' : feedback}"`;
                        }

                        const actionBtn = targetRow.querySelector('.btn-grade-action');
                        if (actionBtn) {
                            actionBtn.style.background = 'linear-gradient(135deg, #039FFA 0%, #0284C7 100%)';
                            actionBtn.textContent = 'Edit Nilai';
                            actionBtn.setAttribute('data-score', score);
                            actionBtn.setAttribute('data-feedback', feedback);
                        }
                    }

                    applyFilters();
                    closeModal();
                    showToast(`Nilai (${score}) & Feedback berhasil disimpan.`);
                } else {
                    const firstError = data.errors ? Object.values(data.errors).flat()[0] : null;
                    alert(firstError || data.message || 'Gagal menyimpan nilai ke database. Silakan coba lagi.');
                }
            } catch (err) {
                console.error('Grading save error:', err);
                alert('Terjadi kesalahan saat menyimpan nilai ke database.');
            } finally {
                btnSaveGrading.disabled = false;
                btnSaveGrading.innerHTML = originalBtnHtml;
            }
        });
    }

    const selectCurrentCourse = document.getElementById('selectCurrentCourse');
    const filterSubBtns = document.querySelectorAll('.filter-submission-btn');
    const filterSearchInput = document.getElementById('filterSearchInput');
    const gradedSummary = document.getElementById('gradedSummary');
    const ungradedSummary = document.getElementById('ungradedSummary');

    function updateKpiStats(selectedCourse) {
        const rows = document.querySelectorAll('#submissionTableBody tr[data-status]');
        let totalAll = 0;
        let totalGraded = 0;
        let totalPending = 0;

        rows.forEach(r => {
            const rowCourseId = r.getAttribute('data-course-id');
            const rowStatus = r.getAttribute('data-status');
            if (selectedCourse === 'all' || rowCourseId === selectedCourse) {
                totalAll++;
                if (rowStatus === 'graded') totalGraded++;
                else totalPending++;
            }
        });

        const kpiTotalAll = document.getElementById('kpiTotalAll');
        const kpiTotalGraded = document.getElementById('kpiTotalGraded');
        const kpiTotalPending = document.getElementById('kpiTotalPending');
        const kpiGradedPercent = document.getElementById('kpiGradedPercent');

        if (kpiTotalAll) kpiTotalAll.textContent = totalAll;
        if (kpiTotalGraded) kpiTotalGraded.textContent = totalGraded;
        if (kpiTotalPending) kpiTotalPending.textContent = totalPending;
        if (kpiGradedPercent) {
            const pct = totalAll > 0 ? Math.round((totalGraded / totalAll) * 100) : 0;
            kpiGradedPercent.textContent = `${pct}%`;
        }
    }

    function applyFilters() {
        const selectedCourse = selectCurrentCourse ? selectCurrentCourse.value : 'all';
        const activeFilterBtn = document.querySelector('.filter-submission-btn.active');
        const statusFilter = activeFilterBtn ? activeFilterBtn.getAttribute('data-sub-filter') : 'all';
        const searchQuery = filterSearchInput ? filterSearchInput.value.toLowerCase().trim() : '';

        let visibleGraded = 0;
        let visiblePending = 0;

        const rows = document.querySelectorAll('#submissionTableBody tr[data-status]');
        rows.forEach(row => {
            const rowCourseId = row.getAttribute('data-course-id');
            const rowStatus = row.getAttribute('data-status');
            const rowSearch = row.getAttribute('data-search') || '';

            const courseMatch = (selectedCourse === 'all' || rowCourseId === selectedCourse);
            const statusMatch = (statusFilter === 'all' || rowStatus === statusFilter);
            const searchMatch = !searchQuery || rowSearch.includes(searchQuery);

            if (courseMatch && statusMatch && searchMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }

            if (courseMatch) {
                if (rowStatus === 'graded') visibleGraded++;
                else if (rowStatus === 'pending') visiblePending++;
            }
        });

        if (gradedSummary) gradedSummary.textContent = `${visibleGraded} Dinilai`;
        if (ungradedSummary) ungradedSummary.textContent = `${visiblePending} Menunggu Review`;

        updateKpiStats(selectedCourse);
    }

    if (selectCurrentCourse) {
        selectCurrentCourse.addEventListener('change', applyFilters);
    }

    if (filterSearchInput) {
        filterSearchInput.addEventListener('input', applyFilters);
    }

    filterSubBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterSubBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            applyFilters();
        });
    });

    // Initial filter run
    applyFilters();
});
