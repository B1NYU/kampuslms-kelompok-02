# =====================================================================
# Tahap 1: build aset frontend (Vite + Tailwind)
# Folder public/build ada di .gitignore, jadi harus dibuat saat build.
# =====================================================================
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# =====================================================================
# Tahap 2: aplikasi Laravel 12 (PHP 8.4 + Apache)
# =====================================================================
FROM php:8.4-apache

# 1. Ekstensi sistem & PHP yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    supervisor \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Apache: aktifkan mod_rewrite, hanya mpm_prefork yang aktif
RUN a2enmod rewrite \
    && rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* \
    && a2enmod mpm_prefork \
    && sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# 3. Document root ke /var/www/html/public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 3b. Konfigurasi Supervisor (Apache + queue worker)
COPY docker/supervisord.conf /etc/supervisor/supervisord.conf
RUN sed -i 's/\r$//' /etc/supervisor/supervisord.conf

# 4. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Salin kode proyek
WORKDIR /var/www/html
COPY . .

# 6. Install dependensi Composer.
#    CATATAN: TANPA --no-dev, karena DatabaseSeeder memakai User::factory()
#    dan fakerphp/faker ada di require-dev. Dengan --no-dev, seeding gagal
#    diam-diam (tertutup oleh "|| true" di CMD). Debugbar tetap mati karena
#    APP_DEBUG=false.
RUN composer install --optimize-autoloader --no-interaction --ignore-platform-reqs

# 7. Ambil hasil build Vite dari tahap 1
COPY --from=assets /app/public/build /var/www/html/public/build

# 8. Pastikan folder storage lengkap & punya hak akses yang benar
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views \
        storage/logs storage/app/private storage/app/public bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# 9. Batas upload PHP (bawaan image hanya 2 MB; aplikasi mengizinkan s.d. 20 MB)
RUN { echo "upload_max_filesize=25M"; echo "post_max_size=30M"; } > /usr/local/etc/php/conf.d/uploads.ini

EXPOSE 8080

# 10. Startup:
#   - PORT dinamis Railway
#   - FRESH_SEED=true  -> migrate:fresh --seed (HAPUS semua data, jalankan sekali lalu hapus variabelnya)
#   - selain itu       -> migrate + seed biasa (seed gagal diam-diam bila data sudah ada)
#   - supervisord      -> proses utama container; menjalankan Apache + queue worker
#   - chown di akhir   -> migrasi/seed berjalan sebagai root; tanpa chown, file/folder yang
#                         dibuatnya tidak bisa ditulis Apache (www-data) dan upload jadi 500
CMD ["sh", "-c", "rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* && export PORT=\"${PORT:-8080}\" && sed -i \"s/Listen .*/Listen $PORT/\" /etc/apache2/ports.conf && sed -i \"s/<VirtualHost \\*:.*>/<VirtualHost \\*:$PORT>/\" /etc/apache2/sites-available/000-default.conf && php artisan config:clear && php artisan route:clear && php artisan view:clear && if [ \"$FRESH_SEED\" = \"true\" ]; then php artisan migrate:fresh --seed --force; else (php artisan migrate --force || true); (php artisan db:seed --force || true); fi; chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf"]
