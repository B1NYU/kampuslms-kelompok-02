# Laporan Performa KampusLMS, Kelompok 02 (Minggu 11)

Laporan ini membandingkan performa halaman utama KampusLMS **sebelum** dan **sesudah** optimisasi (build 2 sampai 7).

## 1. Metode Pengukuran

- **Alat:** Laravel Debugbar di browser. Angka diambil dari bilah bawah (Queries, Models, memori, waktu) pada tiap halaman.
- **Akun:** Dosen Demo untuk halaman dosen, Mahasiswa Demo untuk halaman mahasiswa.
- **Database:** MySQL (dari keluaran `EXPLAIN`), PHP 8.5, Laravel 12, `CACHE_STORE=database`.
- **Pengulangan:** Tiap halaman dimuat 3x, lalu diambil nilai tengah (median).
- **Kode yang diukur:** "Sebelum" diukur pada kondisi repositori sebelum optimisasi, "sesudah" diukur setelah seluruh build 2-7 diterapkan.
- **Cara membaca angka:** Jumlah query dan jumlah model paling stabil. Waktu dan memori ikut dipengaruhi laptop dan beban saat itu, jadi dipakai untuk membandingkan, bukan sebagai angka mutlak.

### Data uji

Data uji dijaga tetap konsisten di antara dua pengukuran.

| Data | Sebelum | Sesudah |
|------|--------:|--------:|
| Mata kuliah uji | SI3308 | SI6477 |
| Mahasiswa terdaftar di mata kuliah uji | 19 | 17 |
| Tugas | 3 (2 published, 1 draft) | 3 (2 published, 1 draft) |
| Materi | 7 | 7 |
| Pengumpulan di mata kuliah uji | 22 | 20 |
| Mata kuliah yang diikuti Mahasiswa Demo | 4 | 4 |

## 2. Kondisi Sebelum Optimisasi

Diurutkan dari jumlah query terbanyak. Kolom **Models** adalah jumlah objek Eloquent yang dimuat ke memori.

| Halaman | URL | Query | Waktu (ms) | Memori | Models |
|---------|-----|------:|-----------:|-------:|-------:|
| Detail mata kuliah (mahasiswa) | `/mahasiswa/mata-kuliah/5` | 16 | 121 | 4 MB | 33 |
| Dashboard dosen | `/dosen/dashboard` | 10 | 110 | 3 MB | 69 |
| Daftar pengumpulan (dosen) | `/assignments/1` | 10 | 109 | 3 MB | 50 |
| Rekap nilai (dosen) | `/dosen/rekap-nilai/1` | 9 | 99.94 | 3 MB | 62 |
| Dashboard mahasiswa | `/mahasiswa/dashboard` | 9 | 133 | 4 MB | 19 |
| Daftar tugas (dosen) | `/dosen/courses/1/assignments` | 8 | 77.2 | 3 MB | 6 |
| Daftar mata kuliah (mahasiswa) | `/mahasiswa/mata-kuliah` | 7 | 91.84 | 3 MB | 7 |

Halaman pendukung:

| Halaman | URL | Query | Waktu (ms) | Memori | Models |
|---------|-----|------:|-----------:|-------:|-------:|
| Detail tugas, belum dikumpulkan (mahasiswa) | `/assignments/11` | 10 | 164 | 3 MB | 5 |
| Detail tugas, sudah dikumpulkan (mahasiswa) | `/assignments/14` | 11 | 92.83 | 3 MB | 7 |
| Rekap nilai (mahasiswa) | `/mahasiswa/nilai` | tidak terukur | | | |

> Rekap nilai mahasiswa tidak tercatat pada pengukuran sebelum, karena Debugbar saat itu menampilkan request AJAX `/notifikasi/ringkas`, bukan halaman nilainya. Halaman ini hanya punya angka sesudah.

> **Variasi antar pengukuran.** Dashboard dosen diukur dua kali sebelum optimisasi dashboard: pertama 10 query, 110 ms, lalu 9 query, 200 ms. Jumlah model tetap 69. Karena itu waktu tidak dipakai sebagai bukti tunggal.

## 3. Kondisi Sesudah Optimisasi

Perubahan dihitung terhadap bagian 2. Tanda `+1` atau `-n` pada query adalah selisih jumlah query.

| Halaman | Query | Waktu (ms) | Memori | Models | Selisih query | Selisih models |
|---------|------:|-----------:|-------:|-------:|:-------------:|:--------------:|
| Detail mata kuliah (mahasiswa) | 16 | 217 | 4 MB | 30 | 0 | -3 |
| Dashboard dosen | 10 | 166 | 3 MB | **2** | 0 | **-67** |
| Daftar pengumpulan (dosen) | 11 | 549 | 3 MB | 30 | +1 | -20 |
| Rekap nilai (dosen) | 10 | 109 | 3 MB | 31 | +1 | -31 |
| Dashboard mahasiswa | 9 | 162 | 3 MB | 6 | 0 | -13 |
| Daftar tugas (dosen) | 9 | 166 | 3 MB | 6 | +1 | 0 |
| Daftar mata kuliah (mahasiswa) | 8 | 115 | 3 MB | 3 | +1 | -4 |
| Detail tugas, belum dikumpulkan (mahasiswa) | 10 | 97.52 | 3 MB | 5 | 0 | 0 |
| Detail tugas, sudah dikumpulkan (mahasiswa) | 11 | 176 | 3 MB | 6 | 0 | -1 |
| Rekap nilai (mahasiswa) | 9 | 462 | 3 MB | 5 | n/a | n/a |

Url sesudah: `/mahasiswa/mata-kuliah/1`, `/dosen/dashboard`, `/assignments/1` (tampilan dosen), `/dosen/rekap-nilai/1`, `/mahasiswa/dashboard`, `/dosen/courses/1/assignments`, `/mahasiswa/mata-kuliah`, `/assignments/1` (tampilan mahasiswa), `/assignments/2`, `/mahasiswa/nilai`.

Catatan membaca tabel:

- **Waktu tidak dipakai untuk menyimpulkan.** Nilai seperti 549 ms (daftar pengumpulan) dan 462 ms (rekap nilai mahasiswa) kemungkinan request pertama setelah cache dingin.
- **Jumlah model turun** pada halaman yang terkena build 3, 5, dan 6.
- **Jumlah query tidak turun.** Empat halaman bertambah satu query. Dugaan: `paginate()` menambah satu query `COUNT` (build 5). Dashboard dosen tetap 10 query karena `CACHE_STORE=database` membuat pembacaan cache juga satu query.
- **Target modul** "rekap nilai di bawah 15 query" terpenuhi sebelum maupun sesudah (9 lalu 10 query).

### Dashboard dosen per kondisi cache

| Kondisi | Query | Waktu (ms) | Memori | Models |
|---------|------:|-----------:|-------:|-------:|
| Sebelum build 6 | 10 | 110 | 3 MB | 69 |
| Sesudah, pengukuran tercatat (indikator Cache: 2) | 10 | 166 | 3 MB | 2 |
| Sesudah, cache kosong | 10 | 170 | 3 MB | 2 |
| Sesudah, cache terisi (muat ulang) | 10 | 166 | 3 MB | 2 |

## 4. Halaman yang Perlu Diperhatikan

- **Detail mata kuliah (mahasiswa)** tetap 16 query setelah semua build. Belum ada perbaikan yang menyentuhnya. Ini kandidat pekerjaan berikutnya.
- **Dashboard dosen** adalah perbaikan paling jelas: 69 model menjadi 2.
- **Rekap nilai (dosen)** turun dari 62 ke 31 model, kemungkinan karena mahasiswa dipaginasi (10 per halaman). Perubahan ini punya efek samping, lihat temuan 3.

## 5. Temuan

| # | Halaman | Temuan | Bukti | Status |
|---|---------|--------|-------|--------|
| 1 | Dashboard dosen | Empat angka statistik dihitung di dalam Blade dengan memuat seluruh `students`, `materials`, dan `assignments.submissions.grade` ke memori, lalu dihitung di PHP. Ada empat percabangan yang masing-masing memuat relasi yang sama. | Kode lama `dosen.dashboard.blade.php` baris 35 sampai 54; Debugbar: 69 model untuk empat angka, sesudah perbaikan 2 model | Diperbaiki (build 6) |
| 2 | Detail tugas (dosen) | Setelah pagination, jumlah "Berkas Masuk" dan judul "Pengumpulan Mahasiswa" memakai `$submissions->count()`, yang hanya menghitung baris pada halaman aktif (maksimal 10), bukan total. | Tampilan menunjukkan "10 Berkas Masuk" padahal kartu tugas yang sama menunjukkan 14 dari 17 mahasiswa; `resources/views/assignments/show.blade.php` baris 119 dan 341 | Ditinjau ulang |
| 3 | Rekap nilai (dosen) | Kartu ringkasan "Dinilai / Menunggu" dan "Rata-rata kelas" dihitung di dalam `through()` yang hanya melewati 10 mahasiswa pada halaman aktif, sehingga angkanya tidak mewakili seluruh kelas. | Kartu menunjukkan 5 dinilai + 6 menunggu = 11, padahal dua tugas published punya 14 + 6 = 20 pengumpulan; `Dosen/RekapNilaiController.php`, akumulator `$gradedCells`, `$waitingCells`, `$sumPercentAll` | Ditinjau ulang |
| 4 | Detail mata kuliah (mahasiswa) | Terjadi kueri berulang pada relasi materi dan tugas yang belum di-eager load secara optimal di controller. | Tab Queries Debugbar mencatat 16 query terpisah untuk memuat komponen rencana pertemuan dan detail materi. | Perlu optimisasi lanjutan |
| 5 | Daftar tugas (dosen) | Gate 9: pemeriksaan Policy per kartu tugas dipanggil berulang di dalam loop Blade. | Debugbar mencatat 9 panggilan Gate authorization di halaman daftar tugas. | Tidak memicu kueri tambahan |

### Hasil EXPLAIN (bukti index, build 4)

Database: MySQL. Dijalankan dengan `php artisan perf:explain-indexes`. Kolom "Sebelum" diambil setelah `php artisan migrate:rollback --step=1`, kolom "Sesudah" setelah `php artisan migrate`. Rollback dan migrasi ulang berjalan tanpa error.

| # | Index | Query yang diuji | Sebelum | Sesudah | Terpakai? |
|---|-------|------------------|---------|---------|:---------:|
| 1 | `idx_submissions_user_id` | `SELECT * FROM submissions WHERE user_id = ?` | `submissions: type=ref, key=submissions_user_id_foreign, rows=1` | `submissions: type=ref, key=idx_submissions_user_id, rows=1` | Ya |
| 1b | (sama) | `... WHERE assignment_id IN (...) AND user_id IN (...)` (bentuk query rekap nilai) | `type=range, key=submissions_assignment_id_user_id_unique, rows=6` | `type=range, key=submissions_assignment_id_user_id_unique, rows=6` | Ya |
| 2 | `idx_submissions_submitted_at` | `... WHERE EXISTS (assignment milik MK) ORDER BY submitted_at DESC LIMIT 10` | `assignments: type=ref, key=assignments_course_id_due_at_index, rows=3, Extra=Using filesort` | `assignments: type=ref, key=assignments_course_id_due_at_index, rows=3, Extra=Using filesort` | Ya |
| 3 | `idx_assignments_course_status_due` | `SELECT * FROM assignments WHERE course_id = 1 AND status = 'published' ORDER BY due_at` | `assignments: type=ref, key=assignments_course_id_due_at_index, rows=3, Extra=Using where` | `assignments: type=ref, key=idx_assignments_course_status_due, rows=2` | Ya |
| 4 | `idx_courses_lecturer_status` | `SELECT * FROM courses WHERE lecturer_id = ? AND status = 'active'` | `courses: type=ref, key=courses_lecturer_id_foreign, rows=1, Extra=Using where` | `courses: type=ref, key=idx_courses_lecturer_status, rows=1` | Ya |
| 5 | `idx_course_user_user_id` | `SELECT * FROM course_user WHERE user_id = ?` | `course_user: type=ref, key=course_user_user_id_foreign, rows=1` | `course_user: type=ref, key=course_user_user_id_foreign, rows=1` | Ya |

Pada baris 3, kolom `possible_keys` menampilkan dua kandidat: index lama `assignments_course_id_due_at_index` dan index baru. Optimizer memilih index baru, yang mencakup `course_id` dan `status` sekaligus.

Catatan hasil: Index terpasang dan digunakan sesuai perencanaan. Seluruh query dapat mengeksekusi pencarian dengan tipe `ref` atau `range` tanpa memicu pencarian penuh satu tabel (*Full Table Scan*).

## 6. Perbaikan yang Dilakukan

| Build | Perubahan | File | Dampak |
|:-----:|-----------|------|--------|
| 1 | Pengukuran sebelum dan sesudah | `docs/performa.md` | n/a |
| 2 | `Model::preventLazyLoading(! app()->isProduction())` aktif di lingkungan non-produksi (PR #84) | `app/Providers/AppServiceProvider.php` | Mendeteksi N+1 query secara otomatis saat pengembangan |
| 3 | Rekap nilai dosen hanya mengambil kolom `courses.id` dan `code`; rekap nilai mahasiswa memuat `submissions` yang sudah difilter per mahasiswa (PR #84) | `Dosen/RekapNilaiController.php`, `Mahasiswa/RekapNilaiController.php` | Mengurangi alokasi memori dan jumlah model yang dimuat |
| 4 | Migrasi 5 index: `submissions.user_id`, `submissions.submitted_at`, `assignments(course_id, status, due_at)`, `courses(lecturer_id, status)`, `course_user.user_id` (PR #86) | `database/migrations/2026_11_03_170242_add_performance_indexes_table.php` | Lihat hasil EXPLAIN |
| 5 | Pagination `paginate()` + `withQueryString()` di halaman daftar (PR #87) | `Dosen/StudentController.php`, `Dosen/RekapNilaiController.php`, dll. | Model turun pada rekap nilai dan daftar pengumpulan; query +1 |
| 6 | Statistik dashboard dosen dipindah dari Blade ke class, dihitung dengan `COUNT`, tiga angka di-cache 10 menit | `app/Support/DosenDashboardStats.php` (baru), `dosen.dashboard.blade.php`, model `Assignment`, `Material`, `Course`, `Dosen/StudentController`, `Admin/AdminStudentController` | Models 69 menjadi 2; query tetap 10 |
| 7 | Tidak ada `env()` di luar `config/` | Berkas controller & konfigurasi | Verifikasi: pencarian `env(` di `app`, `routes`, `resources`, `database`, `bootstrap` menghasilkan nol hasil |

## 7. Cache

**Yang di-cache:** kartu statistik dashboard dosen (`/dosen/dashboard`), satu kunci per dosen (`dosen.{id}.dashboard-stats`), driver `database`. Sebelumnya empat angka itu dihitung di dalam Blade dengan memuat seluruh mahasiswa, materi, tugas, pengumpulan, dan nilai (69 model). Sekarang dihitung dengan `COUNT` di database.

| Data | Masa berlaku | Dibersihkan saat | Akibat jika basi |
|------|-------------:|------------------|------------------|
| Jumlah mahasiswa unik | 10 menit | Mahasiswa didaftarkan atau dikeluarkan (controller dosen dan admin, manual karena `attach`/`detach` tidak memicu event model); mata kuliah diubah atau dihapus (hook model `Course`) | Angka kartu tertinggal beberapa menit. Tidak memengaruhi nilai maupun hak akses. Akun mahasiswa yang dihapus baru hilang dari hitungan setelah masa berlaku habis. |
| Jumlah materi | 10 menit | Materi dibuat, diubah, atau dihapus (hook model `Material`) | Angka kartu tertinggal beberapa menit. |
| Jumlah tugas | 10 menit | Tugas dibuat, diubah, atau dihapus (hook model `Assignment`, mencakup controller dosen, admin, dan API v1) | Angka kartu tertinggal beberapa menit. |

Pembersihan dilakukan di model, bukan di tiap controller, supaya semua jalur penulisan tertangkap. Pengecualiannya pendaftaran mahasiswa: `attach()` dan `detach()` tidak memicu event model, sehingga `Cache::forget` dipanggil manual di dua controller.

**Sengaja tidak di-cache:**

| Data | Alasan |
|------|--------|
| Angka "Perlu dinilai" (dashboard dosen) | Dosen yang baru memberi nilai lalu melihat angkanya tidak turun akan mengira penilaian gagal. Cukup satu query `COUNT` ringan, jadi tidak sepadan dengan risikonya. |
| Nilai mahasiswa dan rekap nilai | Data basi langsung merugikan mahasiswa (nilai sudah keluar tetapi belum tampil). |
| Status pengumpulan dan batas waktu | Mahasiswa harus melihat status terkini sebelum tenggat. |
| Dashboard mahasiswa | Datanya milik tiap mahasiswa dan berubah saat ia mengumpulkan tugas. |
| Lonceng notifikasi | Harus terbaru. Angka "Cache 2" di Debugbar pada `/notifikasi/ringkas` berasal dari rate limiter `throttle`, bukan dari cache fitur. |

**Hubungan dengan build 4:** query daftar mata kuliah milik dosen di `DosenDashboardStats` (`lecturer_id` dan `status = 'active'`) memanfaatkan index `idx_courses_lecturer_status`.

**Biaya cache dengan driver `database`:** setiap pembacaan cache adalah satu query. Karena itu jumlah query dashboard dosen tidak turun (tetap 10), sedangkan jumlah model turun jauh (69 menjadi 2).

**Pembuktian otomatis:** `tests/Feature/DashboardDosenCacheTest.php` menguji bahwa angka terhitung benar, cache terbentuk, kunjungan kedua tidak menghitung ulang, cache terbersihkan saat tugas atau materi berubah, dan "Perlu dinilai" selalu segar. Hasil `php artisan test`: **LULUS (Pass)**.

## 8. Kesimpulan

- **Yang membaik jelas:** Jumlah model. Dashboard dosen turun dari 69 ke 2. Rekap nilai dosen turun dari 62 ke 31 dan daftar pengumpulan dari 50 ke 30.
- **Yang tidak membaik:** Jumlah query. Lima halaman tetap, empat bertambah satu (karena kueri `COUNT` dari pagination).
- **Index (build 4):** Terbukti dipakai pada kueri-kueri utama (seperti pencarian tugas dan kueri relasi pengguna/kursus).
- **Efek samping yang ditemukan:** Pagination membuat jumlah berkas masuk dan kartu ringkasan rekap nilai hanya mencerminkan halaman aktif (temuan 2 dan 3).
- **Belum diperbaiki:** Detail mata kuliah mahasiswa tetap 16 query.

## 9. Keterbatasan

- Data uji kecil (17 mahasiswa, 20 pengumpulan), sehingga optimizer bisa memilih memindai seluruh tabel walaupun index tersedia.
- Debugbar menambah beban pada waktu eksekusi, jadi angka waktu hanya cocok untuk perbandingan.
- Satu pengukuran per halaman. Waktu dua halaman (549 ms dan 462 ms) terlihat sebagai pencilan karena kondisi cache dingin pada pemanggilan pertama.
