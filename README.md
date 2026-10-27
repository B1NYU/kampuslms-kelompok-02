# Edupath

1. Baihaqi Abimanyu Toro Putra | 10241014
2. Calvin Hidayat Winatajaya | 10241016
3. Clara Uenike Meylan Langi | 10241018
4. Desta Rifqi Aunur Rahman | 10241020
5. Devina Dian Saputri | 10241022

### Cara Instalasi
1. Jika menggunakan Laragon, bisa melakukan instalasi Laragon melalui:https://github.com/leokhoa/laragon/releases   
2. Download PHP melalui: https://www.php.net/
3. Download Nodejs melalui: https://nodejs.org/en/download
4. Setup ketiganya satu per satu.
5. Selanjutnya buka folder "laragon" > "bin" > "php", lalu copy file PHP yang sudah didownload tadi kefolder "php" tersebut.
6. Buka aplikasi "Laragon" > klik kanan > pilih menu PHP kemudian select versi file PHP yang barudidownload.
7. Lakukan juga untuk file Nodejs yang tadi didownload, tapi lakukan pada folder "nodejs" seperti padagambar.
8. Selanjutnya bisa mendownload Composer melalui: https://getcomposer.org/download/ pilihComposer-Setup.exe
9. Kemudian kembali ke aplikasi Laragon > klik kanan > PHP > Extensions.
10. Lalu aktifkan:
    - pdo_sqlite
    - sqlite3
    - zip (untuk mengekstrak di laravel versi 12 nanti)
11. Jalankan laragon dengan klik "Start All" lalu klik kanan > Quick App > Laravel dan berikan nama projectLaravelnya.
12. Setelah itu Laravel akan langsung dibuat dan memakan waktu cukup lama tergantung device penggunamasing masing.
13. Kembali ke menu Laragon dan klik "Root" kemudian akan diarahkan ke folder "www" yang berisi fileLaravel yang sudah dibuat sebelumnya.
14. Terakhir bisa membuka VSCode dan project Laravel yang sudah dibuat tadi bisa digunakan.



### Queue & Notifikasi (Minggu 10)

Mahasiswa diberi tahu lewat **lonceng di navbar** saat (1) dosen menerbitkan tugas baru dan (2) nilai keluar atau diperbarui. Pengiriman notifikasi berjalan **di latar lewat queue**, bukan di dalam request dosen.

```
Dosen menyimpan tugas ──► Event AssignmentPublished ──► Listener SendAssignmentNotification (queued)
 (request selesai cepat)                                   └─► 1 job NewAssignmentNotification per mahasiswa ──► tabel notifications

Dosen menyimpan nilai ──► Event SubmissionGraded ──► Listener SendGradeNotification (queued)
                                                       └─► job GradeReceivedNotification ──► tabel notifications
```

Satu tugas untuk 40 mahasiswa menghasilkan **41 job** (1 listener + 40 notifikasi). Itu disengaja: satu notifikasi yang gagal tidak menjatuhkan 39 lainnya.

#### Menjalankan worker (WAJIB)

`.env` sudah memakai `QUEUE_CONNECTION=database` (lihat `.env.example`). Tabel `jobs` dan `failed_jobs` dibuat oleh `php artisan migrate`; **jangan** menjalankan `make:queue-table` atau `make:queue-failed-table` di proyek ini (akan membuat migrasi ganda dan `migrate` gagal).

```bash
php artisan migrate
php artisan queue:work          # terminal terpisah, biarkan tetap berjalan
```

Alternatif: `composer run dev` sudah menjalankan `queue:listen` bersama server dan Vite.

> **Kalau worker tidak berjalan, tidak ada notifikasi yang terkirim dan tidak ada pesan error apa pun.** Job hanya menumpuk di tabel `jobs`. Ini bukan bug. Untuk memastikan, lihat apakah ada baris di tabel `jobs` yang tidak berkurang.

Hal lain yang perlu diingat:

- `queue:work` memuat kode **sekali** saat mulai. Setelah mengubah Listener/Notification, jalankan `php artisan queue:restart` (atau pakai `queue:listen` yang memuat ulang tiap job).
- `QUEUE_CONNECTION=sync` menjalankan job langsung di dalam request (tanpa antrean). Tes PHPUnit memakainya (`phpunit.xml`); jangan dipakai untuk demo.
- Di server produksi worker harus dijaga proses pengawas (mis. Supervisor), karena kalau mati tidak ada yang memberi tahu.

#### Percobaan dan kegagalan

Setiap Listener dan Notification punya `$tries = 3` dan `$backoff = [10, 60]` (percobaan ke-2 setelah 10 detik, ke-3 setelah 60 detik) serta method `failed()` yang menulis ke log (`storage/logs/laravel.log`). Nilai di class ini mengalahkan opsi `--tries` pada perintah worker. Setelah tiga kali gagal, job masuk tabel `failed_jobs`.

```bash
php artisan queue:failed            # daftar job yang gagal
php artisan queue:retry all         # jalankan ulang semuanya
php artisan queue:forget <uuid>     # buang satu job gagal
php artisan queue:flush             # kosongkan failed_jobs
```

#### Lonceng notifikasi

- Browser memanggil `GET /notifikasi/ringkas` tiap **30 detik** (polling; dilewati saat tab disembunyikan dan dihentikan bila sesi habis). Endpoint ini tanpa parameter ID dan hanya mengembalikan notifikasi milik pengguna yang sedang login (maksimal 5 terbaru yang belum dibaca, plus jumlah totalnya), dibatasi 60 permintaan per menit.
- Payload notifikasi hanya berisi ID, judul, dan tautan relatif. **Skor dan feedback tidak pernah disimpan di tabel `notifications`.**
- Belum ada halaman daftar notifikasi dan tombol "tandai sudah dibaca" (Build #5 Minggu 10).

#### Mencoba sendiri

```bash
php artisan migrate:fresh --seed
php artisan serve                                  # terminal 1
# Terminal 2: login sebagai dosen (dosen@kampuslms.test) dan terbitkan tugas di MK 1 (status "published").
# Cek: baris baru di tabel `jobs`, tetapi `notifications` masih kosong (worker belum jalan).
php artisan queue:work --stop-when-empty           # terminal 2: proses antrean
# Login sebagai mahasiswa (mahasiswa@kampuslms.test): angka merah muncul di lonceng dalam 30 detik.
php artisan test --filter=NotifikasiTest           # bukti otomatis, termasuk skenario worker mati dan job gagal
```

### Jobdesk
| Anggota | Tugasnya |
|--------|----------|
| Baihaqi Abimanyu Toro Putra | System Analyst |
| Desta Rifqi Aunur Rahman | Backend 1 |
| Devina Dian Saputri | Backend 2 |
| Calvin Hidayat Winatajaya | Frontend 1 |
| Clara Uenike Meylan Langi | Frontend 2 |
