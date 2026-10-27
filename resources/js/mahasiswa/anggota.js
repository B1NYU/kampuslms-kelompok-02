/**
 * Script untuk modal detail anggota kelompok (Halaman Anggota)
 */
export function showDetail(nama, nim, peran, foto, color) {
    const modalImg = document.getElementById('modalImg');
    const modalNama = document.getElementById('modalNama');
    const modalNim = document.getElementById('modalNim');
    const peranEl = document.getElementById('modalPeran');
    const modal = document.getElementById('memberModal');

    if (modalImg) modalImg.src = foto;
    if (modalNama) modalNama.textContent = nama;
    if (modalNim) modalNim.textContent = 'NIM: ' + nim;
    if (peranEl) {
        peranEl.textContent = peran;
        if (color) {
            peranEl.style.background = color;
        }
    }
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

export function closeModal() {
    const modal = document.getElementById('memberModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

// Ekspos ke global window untuk inline onclick
window.showDetail = showDetail;
window.closeModal = closeModal;

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeModal();
});
