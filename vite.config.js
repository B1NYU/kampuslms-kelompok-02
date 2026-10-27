import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/footer.css',
                'resources/css/sidebar.css',
                // Mode Mahasiswa
                'resources/css/mahasiswa/mahasiswa.dashboard.css',
                'resources/css/mahasiswa/mahasiswa.matkul.css',
                'resources/css/mahasiswa/anggota.css',
                // Mode Dosen
                'resources/css/dosen/dosen.common.css',
                'resources/css/dosen/dosen.dashboard.css',
                'resources/css/dosen/dosen.mahasiswa.css',
                'resources/css/dosen/dosen.materi.css',
                'resources/css/dosen/dosen.tugas.css',
                'resources/css/dosen/dosen.penilaian.css',
                // Fallbacks & JS
                'resources/css/welcome.css',
                'resources/css/index.css',
                'resources/css/course-show.css',
                'resources/js/app.js',
                // Mode Mahasiswa JS
                'resources/js/mahasiswa/anggota.js',
                'resources/js/mahasiswa/mahasiswa.matkul.js',
                'resources/js/mahasiswa/mahasiswa.tugas.js',
                'resources/js/mahasiswa/mahasiswa.navbar.js',
                // Mode Dosen JS
                'resources/js/dosen/dosen.navbar.js',
                'resources/js/dosen/dosen.tugas.js',
                'resources/js/dosen/dosen.materi.js',
                'resources/js/dosen/dosen.penilaian.js',
                'resources/js/dosen/dosen.mahasiswa.js',
                // Mode Admin JS
                'resources/js/admin/admin.navbar.js',
                'resources/js/admin/admin.pengguna.js',
                'resources/js/admin/admin.matkul.js',
                'resources/js/admin/admin.pendaftaran.js',
                'resources/js/admin/admin.materi.js',
                'resources/js/admin/admin.tugas.js',
                'resources/js/admin/admin.nilai.js',
                // Auth & Shared JS
                'resources/js/auth/welcome.js',
                'resources/js/auth/aturan-sandi.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
