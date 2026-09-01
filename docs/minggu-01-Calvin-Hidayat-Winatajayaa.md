## 1.3 Read → Break → Fix → Build

### READ — Bedah instalasi Anda sendiri (45 menit)

Setelah instalasi selesai dan halaman selamat datang Laravel muncul, kerjakan **tanpa AI**:

1. Buka `public/index.php`. Baca dari atas ke bawah. Tulis dalam 3 kalimat apa yang dilakukan berkas ini.
2. Buka `bootstrap/app.php`. Identifikasi bagian mana yang mengurus route, mana yang mengurus middleware, mana yang mengurus exception.
3. Buka `routes/web.php`. Temukan route yang menghasilkan halaman selamat datang. Ubah teksnya, muat ulang browser, pastikan berubah.
4. Jalankan `php artisan route:list`. Cocokkan keluarannya dengan isi `routes/web.php`.

Tulis jawabannya di `docs/minggu-01-catatan.md` di repo kelompok. Setiap anggota menulis catatan sendiri di berkas terpisah (`docs/minggu-01-<nama>.md`).

## JAWABAN


1. `public/index.php`

File `public/index.php` adalah pintu masuk paling pertama ketika seseorang membuka website kita di browser. File ini bertugas memanggil dan menyalakan semua mesin utama framework Laravel di latar belakang. Setelah mesin siap, file ini mengambil permintaan halaman dari pengunjung dan menampilkan hasilnya ke layar browser.

---

2. `bootstrap/app.php`

**bagian-bagian utamanya:**

* **Routing:** Ada di bagian `->withRouting(...)`. Tugasnya menentukan jalur jalan halaman website (mengarah ke file `routes/web.php`).
* **Middleware:** Ada di bagian `->withMiddleware(...)`. Tugasnya seperti satpam yang mengecek atau mengamankan lalu lintas data sebelum masuk ke website.
* **Exceptions:** Ada di bagian `->withExceptions(...)`. Tugasnya seperti tim medis yang mengatur apa yang harus dilakukan jika terjadi error/kerusakan pada website.

---

3. `routes/web.php`

**penjelasan, dan cara mengubah teksnya**

Kode yang memanggil halaman selamat datang adalah:
```php
Route::get('/', function () {
    return view('welcome');
});
```

*(Artinya: kalau ada yang membuka alamat utama `/`, tampilkan desain dari file `welcome`)*.
* **cara untuk mengubah teksnya:**
1. Teks websitenya bukan di file ini, tapi ada di file tampilan yang namanya **`resources/views/welcome.blade.php`**.
2. Buka file `welcome.blade.php` tersebut, cari teks yang ingin diganti (misalnya judul "Lets Get Started"), lalu ubah jadi teks bebas
3. Simpan (`Ctrl + S`), lalu *refresh* halaman di browser.
![alt text](calvinn-1.png)
![alt text](calvinn.png)
![alt text](calvinn-2.png)
![alt text](calvinn-3.png)

---

4. Jalankan `php artisan route:list` dan Cocokkan dengan `routes/web.php**`

**caranya:**

1. Buka CMD di VS Code (atau bisa gunakan terminal laragon).
2. Ketik perintah seperti ini lalu tekan **Enter**:
```bash
php artisan route:list
```

**Hasil yang muncul di terminal:**

Terminal akan menampilkan daftar tabel rute yang terdaftar di aplikasi Laravel dengan bentuk seperti ini:

![alt text](calvinn-4.png)

> **Pencocokan hasil di terminal dengan `routes/web.php`:**
> * Di file `routes/web.php`, ada rute `Route::get('/')`.
> * Di terminal, rute tersebut tercatat pada baris **`GET|HEAD /`** dengan keterangan asal file `routes/web.php:5`.
> * Disini menunjukan bahwa setiap rute web yang dibuat di file `routes/web.php` akan terdaftar dan dibaca secara otomatis oleh sistem laravel.
> * Adapun rute lain seperti `storage/{path}` dan `up` yang tampil di terminal adalah rute otomatis bawaan dari sistem laravel (*internal/vendor*).

Perintah `php artisan route:list` digunakan untuk melihat seluruh daftar alamat atau URL yang sedang aktif dan bisa diakses di dalam aplikasi web