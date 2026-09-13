import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/mahasiswa/mahasiswa.dashboard.css',
                'resources/css/index.css',
                'resources/css/mahasiswa/anggota.css',
                'resources/css/course-show.css',
                'resources/dosen/dosen.dashboard.css',
                'resources/css/dosen/dosen.dashboard.css',
                'resources/css/admin/admin.dashboard.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
