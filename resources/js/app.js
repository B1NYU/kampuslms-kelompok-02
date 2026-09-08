// Fitur Menampilkan Modal Detail
function showDetail(nama, nim, peran, foto) {
    document.getElementById('modalNama').innerText = nama;
    document.getElementById('modalPeran').innerText = peran;
    document.getElementById('modalImg').src = foto;
    document.getElementById('memberModal').classList.remove('hidden');
}

// Fitur Menutup Modal
function closeModal() {
    document.getElementById('memberModal').classList.add('hidden');
}