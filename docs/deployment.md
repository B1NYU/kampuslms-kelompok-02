# Dokumentasi Deployment — KampusLMS Kelompok 02

**Aplikasi live:** https://kampuslms-kelompok-02-production.up.railway.app      
**Repositori:** https://github.com/B1NYU/kampuslms-kelompok-02          
**Tanggal deployment dan uji:** 10 November 2026

---

## 1. Spesifikasi

Aplikasi berjalan di **Railway.app** sebagai container Docker (Apache, bukan Nginx).

| Komponen | Produksi | Lokal |
|---|---|---|
| Platform | Railway (edge `sin1`) | Laptop Windows |
| Image | `php:8.4-apache` | - |
| PHP | 8.4.27 | 8.5.10 |
| Laravel | 12.x | 12.68.0 |
| Web server | Apache (document root `public/`) | Laragon |
| Database | MySQL Railway | MySQL |
| Queue, session, cache | `database` | `database` |
| Mode | `production`, `APP_DEBUG=false` | `local`, debug aktif |
| HTTPS | Otomatis dari Railway | tidak ada |

---

## 2. Langkah Deployment dari Nol

Rincian dan tangkapan layar ada di `PANDUAN_DEPLOY_RAILWAY.md`. Ringkasannya:

1. Siapkan `Dockerfile` di akar repo (PHP 8.4 + Apache, document root `public/`, izin `storage`, batas upload).
2. Buat project di Railway, login dengan GitHub.
3. Tambahkan service **MySQL**.
4. Hubungkan repo GitHub, branch `main`, aktifkan Auto Deploy.
5. **Generate Domain** (Settings → Networking, port `8080`).
6. Isi **Variables** (tabel di bawah). Domain harus dibuat dulu karena `APP_URL` bergantung padanya.
7. Deploy. Migrasi dan seed berjalan otomatis lewat `CMD` Dockerfile.
8. Buka situs, login dengan akun demo, uji fitur.

Variabel utama (nilai rahasia tidak ditulis di sini):

| Variabel | Nilai |
|---|---|
| `APP_ENV` / `APP_DEBUG` | `production` / `false` |
| `APP_KEY` | dibuat dengan `php artisan key:generate --show`, hanya di Railway Variables |
| `APP_URL` | `https://${{RAILWAY_PUBLIC_DOMAIN}}` |
| `DB_*` | referensi `${{MySQL.MYSQLHOST}}`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD` |
| `SESSION_DRIVER` / `QUEUE_CONNECTION` | `database` |
| `SESSION_SECURE_COOKIE` | `true` |
| `LOG_CHANNEL` | `stderr` |

---

## 3. Deploy Ulang

1. Perbaiki kode di lokal, lalu `git push origin main`.
2. Railway otomatis membangun ulang image dan menjalankan `migrate --force`.
3. Cek **Deploy Logs**, pastikan tidak ada error, lalu buka situsnya.

Manual tanpa push: `Ctrl + K`, ketik `redeploy`, Enter.

Railway membangun container baru dari `Dockerfile`, sehingga `git pull`, `composer install`, dan mode maintenance dari `deploy.sh` versi VPS tidak diperlukan.
Skrip `./scripts/deploy.sh` yang dijalankan di terminal lokal berfungsi untuk otomatisasi pemeliharaan, pembaruan dependensi composer, build aset frontend via Vite, hingga membersihkan *cache* konfigurasi sebelum perubahan didorong ke repositori. File skrip ini berada di dalam direktori `scripts/` pada repositori.

---

## 4. Membaca Log

`APP_DEBUG` tidak pernah dinyalakan di produksi. Penyebab error dicari lewat:

- **Build Logs**: Service → Deployments → Build Logs (error `composer install` atau Docker).
- **Deploy Logs**: Service → Deployments → Deploy Logs (Apache, hasil migrasi, error Laravel bila `LOG_CHANNEL=stderr`).

---

## 5. Bukti Keamanan

Diuji 10 November 2026, pukul 14:38 GMT, dari PowerShell dengan `curl.exe -I`.

| URL | Hasil | Arti |
|---|---|---|
| `/.env` | 404 | Aman |
| `/storage/logs/laravel.log` | 404 | Aman |
| `/composer.json` | 404 | Aman |
| `http://...` | 301 ke `https://...` | HTTP dialihkan ke HTTPS |

Ketiga berkas sensitif memberi 404 karena document root Apache adalah `public/`, sehingga `.env`, `composer.json`, dan `storage/` ada di luar folder yang disajikan. Permintaannya diteruskan ke Laravel, yang tidak punya route untuk path itu. Kalau document root salah, ketiganya akan terunduh dengan status 200.

Konfigurasi:
- `APP_DEBUG=false`, `APP_ENV=production`.
- Document root diarahkan ke `/var/www/html/public` di Dockerfile.
- Database hanya diakses lewat jaringan privat Railway.

Uji unduh *submission* menggunakan `curl` pada rute `/submissions/2/unduh` menghasilkan status `302 Found` yang mengarahkan ke halaman `/login` karena akses memerlukan autentikasi sesi.

### Uji otorisasi (`scripts/test-authz.sh`)

Dijalankan terhadap produksi (`BASE_URL` menunjuk ke domain Railway). Semua lolos:

```text
[1] MAHASISWA
Mahasiswa mendaftar submission mahasiswa lain ........ PASSED (403)
Mahasiswa mengunduh submission mahasiswa lain ........ PASSED (403)
Mahasiswa membuka detail MK tidak terdaftar (Web) ... PASSED (403)
Mahasiswa membuka tugas MK tidak terdaftar .......... PASSED (403)
Mahasiswa mengubah role via endpoint Admin User ..... PASSED (403)

[2] DOSEN
Dosen menghapus mahasiswa dari MK lain .............. PASSED (403)
Dosen mengakses dashboard Admin ..................... PASSED (403)
Dosen mengubah role via Admin User Endpoint ......... PASSED (403)

[3] ADMIN
Admin mengakses dashboard Admin ..................... PASSED (200)
```

### Uji dari HP

10 November 2026, sekitar pukul 21:55. Perangkat dan browser: Google Chrome mobile di Android. Situs terbuka lewat HTTPS, login sebagai mahasiswa berhasil, dashboard menampilkan 2 mata kuliah aktif, 7 SKS, dan 4 tugas belum dikumpulkan, dan tata letak tidak rusak.

---

## 6. Queue Worker

`QUEUE_CONNECTION=database`, sehingga tanpa worker notifikasi menumpuk di tabel `jobs` tanpa pesan error. `CMD` Dockerfile hanya menjalankan Apache. Karena Railway tidak memakai Supervisor, worker dijalankan sebagai service terpisah dari repo yang sama dengan start command:

```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

Status *queue worker* aktif di Railway dan berhasil memproses antrean notifikasi secara otomatis di latar belakang.

---

## 7. Akun Demo

| Peran | Login |
|---|---|
| Admin | `admin@kampuslms.test` |
| Dosen | `dosen@kampuslms.test` |
| Mahasiswa | `mahasiswa@kampuslms.test` |

Kata sandi diberikan terpisah ke dosen pengampu, tidak ditulis di repo publik. Kata sandi bawaan seeder telah diamankan di lingkungan produksi, dan uji coba login memastikan ketiganya dapat masuk sesuai peran masing-masing.

---

## 8. Masalah yang Ditemui

**M1. Crash karena urutan langkah `APP_URL`**
- Gejala: deploy pertama crash saat mengikuti panduan asisten dosen.
- Penyebab: `APP_URL` memakai `${{RAILWAY_PUBLIC_DOMAIN}}`, yang baru ada setelah domain dibuat (langkah 5), padahal di panduan variabel diisi lebih dulu.
- Perbaikan: Generate Domain dulu, lalu isi `APP_URL`, lalu redeploy.

**M2. Error 500 di halaman materi**
- Gejala: Error 500 terjadi pada bagian dosen unggah materi, admin unggah materi, dan admin unduh materi.
- Cara menemukan: Melalui Deploy Logs di dashboard Railway.
- Penyebab: di `routes/web.php`, route unduh materi milik admin menunjuk ke `AdminMaterialController`, padahal method `download` ada di `MaterialController`.
  ```php
  // sebelum
  Route::get('/materi/{material}/download', [AdminMaterialController::class, 'download'])->name('materi.download');
  // sesudah
  Route::get('/materi/{material}/download', [MaterialController::class, 'download'])->name('materi.download');
  ```
- Kerusakan pada rute unduh admin disebabkan oleh ketidaksesuaian pemetaan kontroler tersebut, sedangkan pembatasan ukuran berkas diatur terpisah pada penanganan unggahan.

**M3. Batas unggah berkas**
- Gejala: unggahan besar gagal, padahal aplikasi mengizinkan 20 MB.
- Penyebab: image `php:8.4-apache` membatasi upload 2 MB. Batas PHP diperiksa sebelum validasi Laravel (sama seperti percobaan minggu 9).
- Perbaikan: tambahan di Dockerfile.
  ```dockerfile
  RUN { echo "upload_max_filesize=25M"; echo "post_max_size=30M"; } > /usr/local/etc/php/conf.d/uploads.ini
  ```
- Bukti: Berkas berukuran ±15 MB berhasil diunggah dengan sukses di lingkungan produksi.

Masalah lain dari panduan:

| Gejala | Penyebab | Perbaikan |
|---|---|---|
| `composer install` gagal, butuh PHP >= 8.4.1 | Image PHP 8.3 | `FROM php:8.4-apache` |
| `AH00534: More than one MPM loaded` | `mpm_event` ikut aktif | Hapus `mpm_event`/`mpm_worker`, aktifkan `mpm_prefork` |
| 404 di semua halaman selain `/` | `.htaccess` diabaikan | `a2enmod rewrite` + `AllowOverride All` |
| `502 Application failed to respond` | Port publik tidak sama dengan port Apache | Samakan dengan `8080`, periksa `APP_KEY` dan `DB_*` |
| `Data truncated for column 'status'` saat seed | Nilai enum tidak sesuai migrasi | Pakai `'active'` |

---

## 9. Keterbatasan

- **Berkas upload bisa hilang saat redeploy**, karena filesystem container Railway bersifat sementara (Railway Volume dipasang pada `storage/app` untuk menjaga persistensi data unggahan).
- `db:seed` berjalan di tiap deploy (cocok untuk demo, tidak untuk aplikasi sungguhan), dan `migrate` memakai `|| true` sehingga kegagalan migrasi hanya terlihat di Deploy Logs.
- `APP_KEY` sempat tertulis di repo publik (`PANDUAN_DEPLOY_RAILWAY.md`), namun kunci enkripsi telah diganti dengan yang baru pada 10 November 2026 dan referensi dalam panduan telah diperbarui menjadi placeholder.
