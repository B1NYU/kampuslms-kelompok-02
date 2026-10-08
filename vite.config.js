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
                // Mode Admin
                'resources/css/admin/admin.common.css',
                'resources/css/admin/admin.dashboard.css',
                'resources/css/admin/admin.pengguna.css',
                'resources/css/admin/admin.matkul.css',
                'resources/css/admin/admin.pendaftaran.css',
                'resources/css/admin/admin.materi.css',
                'resources/css/admin/admin.tugas.css',
                // Fallbacks & JS
                'resources/css/welcome.css',
                'resources/css/index.css',
                'resources/css/course-show.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
