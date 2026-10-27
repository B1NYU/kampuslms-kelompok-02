/**
 * Script untuk Halaman Detail & Perkuliahan Mahasiswa (Mata Kuliah)
 */

// Switcher Tab Detail, Presensi & Nilai
export function switchCourseTab(tab) {
    const btnDetail    = document.getElementById('tabBtnDetail');
    const btnNilai     = document.getElementById('tabBtnNilai');
    const btnPresensi  = document.getElementById('tabBtnPresensi');
    const panelDetail  = document.getElementById('panelCourseDetail');
    const panelNilai   = document.getElementById('panelCourseNilai');
    const panelPresensi = document.getElementById('panelCoursePresensi');

    if (btnDetail) btnDetail.classList.remove('active');
    if (btnNilai) btnNilai.classList.remove('active');
    if (btnPresensi) btnPresensi.classList.remove('active');

    if (panelDetail) panelDetail.style.display   = 'none';
    if (panelNilai) panelNilai.style.display    = 'none';
    if (panelPresensi) panelPresensi.style.display = 'none';

    if (tab === 'nilai') {
        if (btnNilai) btnNilai.classList.add('active');
        if (panelNilai) panelNilai.style.display = 'block';
        if (window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.set('tab', 'nilai');
            window.history.replaceState({}, '', url);
        }
    } else if (tab === 'presensi') {
        if (btnPresensi) btnPresensi.classList.add('active');
        if (panelPresensi) panelPresensi.style.display = 'block';
        if (window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.set('tab', 'presensi');
            window.history.replaceState({}, '', url);
        }
    } else {
        if (btnDetail) btnDetail.classList.add('active');
        if (panelDetail) panelDetail.style.display = 'block';
        if (window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.delete('tab');
            window.history.replaceState({}, '', url);
        }
    }
}

// Pembantu: buat elemen dengan teks aman (tanpa innerHTML => tidak ada XSS dari judul/nama berkas).
function el(tag, className, text) {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (text !== undefined && text !== null) node.textContent = text;
    return node;
}

function emptyNote(message, bg, border, color) {
    const box = el('div', null, message);
    box.style.cssText = `padding:14px 16px;background:${bg};border:1px dashed ${border};border-radius:12px;color:${color};font-size:12.5px;text-align:center;font-weight:600;`;
    return box;
}

// Switcher Pertemuan Mingguan (Left -> Right). Data dibaca dari atribut data-* kartu.
export function switchRightToDetail(minggu, card) {
    const defaultRightPanel = document.getElementById('defaultRightPanel');
    const detailRightPanel = document.getElementById('detailRightPanel');
    const detailBadgeSession = document.getElementById('detailBadgeSession');
    const detailHeading = document.getElementById('detailHeading');

    if (defaultRightPanel) defaultRightPanel.style.display = 'none';
    if (detailRightPanel) detailRightPanel.style.display = 'block';

    if (detailBadgeSession) detailBadgeSession.innerText = 'Minggu ' + minggu;
    if (detailHeading && card) detailHeading.innerText = card.dataset.judul || '';

    const materiList = JSON.parse((card && card.dataset.materi) || '[]');
    const tasks = JSON.parse((card && card.dataset.tasks) || '[]');

    // --- Materi: tombol Unduh/Buka mengarah ke route berpolicy ---
    const materiContainer = document.getElementById('materiListContainer');
    if (materiContainer) {
        materiContainer.replaceChildren();

        if (materiList.length === 0) {
            materiContainer.appendChild(emptyNote('Belum ada materi untuk pertemuan ini.', '#F0F9FF', 'rgba(3,159,250,0.3)', '#0284C7'));
        } else {
            materiList.forEach(m => {
                const item = el('div', 'resource-card');
                const left = el('div', 'resource-left');
                left.appendChild(el('div', 'resource-icon', m.kind === 'link' ? '\u{1F517}' : '\u{1F4C4}'));
                const info = el('div');
                info.appendChild(el('div', 'resource-name', m.judul));
                info.appendChild(el('div', 'resource-meta', m.nama + ' \u00B7 ' + m.meta));
                left.appendChild(info);

                const btn = el('a', 'btn-mini-download', m.kind === 'link' ? 'Buka' : 'Unduh');
                btn.href = m.url;
                btn.style.textDecoration = 'none';
                if (m.kind === 'link') { btn.target = '_blank'; btn.rel = 'noopener noreferrer'; }

                item.append(left, btn);
                materiContainer.appendChild(item);
            });
        }
    }

    // --- Tugas: tombol membuka halaman detail & pengumpulan ---
    const tasksContainer = document.getElementById('tasksListContainer');
    const tasksHeaderTitle = document.getElementById('tasksHeaderTitle');
    if (tasksHeaderTitle) tasksHeaderTitle.innerText = 'Aktivitas & Tugas (' + tasks.length + ' Penugasan)';

    if (tasksContainer) {
        tasksContainer.replaceChildren();

        if (tasks.length === 0) {
            tasksContainer.appendChild(emptyNote('Tidak ada penugasan terjadwal untuk pertemuan ini.', '#FFFBEB', 'rgba(245,158,11,0.35)', '#92400E'));
        } else {
            tasks.forEach((task, index) => {
                const typeLower = (task.tipe || '').toLowerCase();
                let badgeClass = '';
                if (typeLower.includes('kuis')) badgeClass = 'quiz';
                else if (typeLower.includes('praktikum') || typeLower.includes('latihan')) badgeClass = 'praktikum';
                else if (typeLower.includes('milestone')) badgeClass = 'milestone';
                else if (typeLower.includes('ujian') || typeLower.includes('uts') || typeLower.includes('uas')) badgeClass = 'exam';

                const box = el('div', 'task-card-box');
                const head = el('div', 'task-card-header');
                head.appendChild(el('span', 'task-badge-tag ' + badgeClass, task.tipe));
                const num = el('span', null, 'Penugasan #' + (index + 1));
                num.style.cssText = 'font-size:11px;font-weight:800;color:#F96305;';
                head.appendChild(num);

                const action = el('a', 'btn-task-action');
                action.innerHTML = task.btn; // teks tombol disusun server (berisi &rarr;), bukan input pengguna
                action.href = task.url;
                action.style.cssText = 'text-decoration:none;display:block;text-align:center;';

                box.append(head, el('div', 'task-card-title', task.judul), el('div', 'task-deadline', task.deadline), action);
                tasksContainer.appendChild(box);
            });
        }
    }

    document.querySelectorAll('.session-card').forEach(c => c.classList.remove('selected-active'));
    const activeCard = document.getElementById('cardWeek' + minggu);
    if (activeCard) activeCard.classList.add('selected-active');
}

export function restoreDefaultInfo() {
    const detailRightPanel = document.getElementById('detailRightPanel');
    const defaultRightPanel = document.getElementById('defaultRightPanel');
    if (detailRightPanel) detailRightPanel.style.display = 'none';
    if (defaultRightPanel) defaultRightPanel.style.display = 'flex';
    document.querySelectorAll('.session-card').forEach(card => {
        card.classList.remove('selected-active');
    });
}

// Ekspos ke global window untuk inline onclick
window.switchCourseTab = switchCourseTab;
window.switchRightToDetail = switchRightToDetail;
window.restoreDefaultInfo = restoreDefaultInfo;

document.addEventListener('DOMContentLoaded', function () {
    // Live Search Tabel Nilai
    const searchInput = document.getElementById('gradeSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#gradeTableBody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }
});
