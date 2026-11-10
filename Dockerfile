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

EXPOSE 8080

# 9. Startup: PORT dinamis Railway, bersihkan cache, migrasi, seed, jalankan Apache
CMD ["sh", "-c", "rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* && export PORT=\"${PORT:-8080}\" && sed -i \"s/Listen .*/Listen $PORT/\" /etc/apache2/ports.conf && sed -i \"s/<VirtualHost \\*:.*>/<VirtualHost \\*:$PORT>/\" /etc/apache2/sites-available/000-default.conf && php artisan config:clear && php artisan route:clear && php artisan view:clear && (php artisan migrate --force || true) && (php artisan db:seed --force || true) && exec apache2-foreground"]
