#!/usr/bin/env bash

# Berhenti otomatis jika ada perintah yang gagal (error)
set -e

echo "=== MEMULAI PROSES DEPLOYMENT PRODUKSI ==="

# 1. Aktifkan mode maintenance agar pengguna tidak mengalami error saat deployment
php artisan down || true

# 2. Ambil perubahan kode terbaru dari repositori Git
git pull origin main

# 3. Install dependensi composer (tanpa paket dev & optimasi autoloader)
composer install --optimize-autoloader --ignore-platform-reqs

# 4. Compile aset frontend untuk lingkungan produksi
npm run build

# 5. Jalankan migrasi database (menggunakan --force untuk lingkungan produksi)
php artisan migrate --force

# 6. Bersihkan dan buat ulang seluruh cache aplikasi
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. WAJIB: Restart queue worker agar memuat kode terbaru dari RAM
php artisan queue:restart

# 8. Matikan mode maintenance (aplikasi kembali online)
php artisan up

echo "=== DEPLOYMENT BERHASIL & KAMPUSLMS KEMBALI ONLINE ==="