# Pembahasan Checkpoint Minggu 11: Optimisasi Performa

**Mata Kuliah:** Pemrograman Web  
**Proyek:** KampusLMS, Kelompok 02  
**Acuan:** `modulminggu9-12.md` bagian 11.4  
**Sumber data:** `docs/performa.md` (hasil ukur tim dengan Debugbar), `app/Console/Commands/ExplainIndexes.php`, `app/Support/DosenDashboardStats.php`, `tests/Feature/DashboardDosenCacheTest.php`

> [!IMPORTANT]
> Semua angka di dokumen ini diambil dari `docs/performa.md` milik tim, bukan perkiraan. Kalau dosen bertanya angka, rujuk tabel di file itu. Kalau ada pertanyaan yang jawabannya berbeda dari teori modul, akui saja dan jelaskan alasannya. Itu lebih aman daripada menghafal jawaban modul.

---

## Daftar Isi

1. [Halaman terburuk sebelum optimisasi](#1-halaman-terburuk-sebelum-optimisasi)
2. [Peragaan N+1](#2-peragaan-n1)
3. [`withCount('students')` vs `count($course->students)`](#3-withcountstudents-vs-countcourse-students)
4. [Index dan bukti EXPLAIN](#4-index-dan-bukti-explain)
5. [Data yang tidak boleh di-cache](#5-data-yang-tidak-boleh-di-cache)
6. [`env()` setelah `config:cache`](#6-env-setelah-configcache)
7. [Pertanyaan lanjutan yang mungkin muncul](#7-pertanyaan-lanjutan-yang-mungkin-muncul)
8. [Ringkasan jawaban singkat](#8-ringkasan-jawaban-singkat)

---

## 1. Halaman terburuk sebelum optimisasi

> ❓ *Buka `docs/performa.md`. Halaman mana yang terburuk sebelum optimisasi? Kenapa halaman itu?*

### Data sebelum optimisasi (`docs/performa.md` bagian 2)

| Halaman | Query | Models |
|---|---:|---:|
| **Detail mata kuliah (mahasiswa)** | **16** | 33 |
| **Dashboard dosen** | 10 | **69** |
| Daftar pengumpulan (dosen) | 10 | 50 |
| Rekap nilai (dosen) | 9 | 62 |
| Dashboard mahasiswa | 9 | 19 |
| Daftar tugas (dosen) | 8 | 6 |
| Daftar mata kuliah (mahasiswa) | 7 | 7 |

### Jawaban

Jawabannya tergantung ukuran yang dipakai, dan itu perlu disebutkan:

- **Dari jumlah query:** yang terburuk adalah **Detail mata kuliah (mahasiswa)**, 16 query. Halaman ini memuat banyak bagian sekaligus: timeline 16 pertemuan, materi, tugas, presensi, dan nilai. Relasi materi dan tugas belum di-eager load secara optimal (temuan 4 di `docs/performa.md`).
- **Dari jumlah model (memori):** yang terburuk adalah **Dashboard dosen**, 69 model hanya untuk menampilkan **4 angka**. Blade lama memuat seluruh `students`, `materials`, dan `assignments.submissions.grade` ke memori, lalu menghitungnya di PHP. Ada empat percabangan yang masing-masing memuat relasi yang sama.

### Kenapa bukan rekap nilai, padahal modul bilang rekap nilai hampir pasti terburuk?

Modul (baris 915) memprediksi rekap nilai yang terburuk. Di KampusLMS kami, rekap nilai **sudah dirancang tanpa N+1 sejak awal**. Lihat komentar di `Dosen/RekapNilaiController.php`:

```php
/**
 * Jumlah query tetap (±6) berapa pun banyaknya mahasiswa dan tugas: mahasiswa, tugas, dan
 * pengumpulan+nilai masing-masing diambil SEKALI, lalu matriksnya dirakit di PHP.
 */
```

Mahasiswa, tugas, dan pengumpulan diambil masing-masing satu kali (`whereIn`), lalu matriks mahasiswa × tugas dirakit di PHP. Karena itu rekap nilai hanya 9 query sebelum optimisasi, dan target modul "rekap nilai di bawah 15 query" sudah terpenuhi sebelum maupun sesudah (9 lalu 10 query).

Masalah rekap nilai ada di **jumlah model** (62), bukan di jumlah query. Setelah pagination, angkanya turun menjadi 31.

### Hasil sesudah optimisasi

| Halaman | Models sebelum → sesudah | Query sebelum → sesudah |
|---|---|---|
| Dashboard dosen | 69 → **2** | 10 → 10 |
| Rekap nilai (dosen) | 62 → 31 | 9 → 10 |
| Daftar pengumpulan (dosen) | 50 → 30 | 10 → 11 |
| Detail mata kuliah (mahasiswa) | 33 → 30 | 16 → 16 (belum diperbaiki) |

> [!NOTE]
> **Kenapa jumlah query tidak turun?** Ada dua alasan. Pertama, `paginate()` menambah satu query `COUNT(*)` untuk menghitung total halaman. Kedua, `CACHE_STORE=database` membuat pembacaan cache juga satu query. Jadi perbaikan yang terlihat jelas ada di jumlah model, bukan jumlah query.

---

## 2. Peragaan N+1

> ❓ *Matikan `with()` pada satu halaman dan tunjukkan lonjakan query di Debugbar.*

### Konsep

N+1 terjadi ketika aplikasi menjalankan **1 query** untuk daftar induk, lalu **N query tambahan** di dalam loop untuk mengambil relasi tiap baris satu per satu.

### Halaman yang dipakai untuk demo

**Daftar mata kuliah mahasiswa** (`/mahasiswa/mata-kuliah`), di [MahasiswaCourseController.php](file:///d:/Proweb/kampuslms-kelompok-02/app/Http/Controllers/Mahasiswa/MahasiswaCourseController.php#L23-L33):

```php
$courses = $student->courses()
    ->whereIn('courses.status', ['active', 'archived'])
    ->with('lecturer')              // ← ini yang dimatikan saat demo
    ->withCount([
        'students',
        'assignments as published_assignments_count' => fn ($q) => $q->where('status', 'published'),
        'materials',
    ])
    ->orderBy('code')
    ->paginate(9)
    ->withQueryString();
```

Blade [courses/index.blade.php](file:///d:/Proweb/kampuslms-kelompok-02/resources/views/courses/index.blade.php#L95) memanggil relasi dosen di setiap kartu:

```blade
{{ $item->lecturer?->name ?? 'Dosen Pengampu' }}
```

### Langkah demo

**Tahap 1: tunjukkan proteksi otomatis.**

1. Hapus atau komentari `->with('lecturer')`.
2. Buka `/mahasiswa/mata-kuliah` sebagai Mahasiswa Demo.
3. Hasilnya langsung error:
   ```
   Illuminate\Database\LazyLoadingViolationException
   Attempted to lazy load [lecturer] on model [App\Models\Course] but lazy loading is disabled.
   ```

Error ini muncul karena [AppServiceProvider.php](file:///d:/Proweb/kampuslms-kelompok-02/app/Providers/AppServiceProvider.php#L24) mengaktifkan:

```php
Model::preventLazyLoading(! app()->isProduction());
```

Di lokal, N+1 menjadi error yang tidak bisa diabaikan. Di produksi, aplikasi tetap jalan agar pengguna tidak melihat halaman rusak.

**Tahap 2: tunjukkan lonjakan query di Debugbar.**

1. Komentari sementara baris `Model::preventLazyLoading(...)`.
2. Muat ulang halaman dan buka tab **Queries** di Debugbar.
3. Akan terlihat query berulang dengan pola sama:
   ```sql
   select * from `users` where `users`.`id` = ? limit 1
   select * from `users` where `users`.`id` = ? limit 1
   ...
   ```
   Satu query per kartu mata kuliah. Mahasiswa Demo mengikuti 4 mata kuliah, jadi akan ada 4 query `users` terpisah. Dengan `with('lecturer')`, keempatnya digabung menjadi **1 query** `where users.id in (...)`.
4. Bandingkan: 8 query dengan `with()` menjadi sekitar 11 query tanpa `with()` (1 eager query diganti 4 lazy query). Semakin banyak mata kuliah, semakin besar selisihnya. Pada halaman penuh (9 kartu), tanpa `with()` ada 9 query dosen.

**Tahap 3: kembalikan semua seperti semula.** Pasang lagi `->with('lecturer')` dan `Model::preventLazyLoading(...)`.

> [!CAUTION]
> Jangan sampai perubahan demo ini ikut ter-commit. Cek `git status` setelah demo.

---

## 3. `withCount('students')` vs `count($course->students)`

> ❓ *Apa bedanya? Kenapa yang kedua boros?*

| | `withCount('students')` | `count($course->students)` |
|---|---|---|
| Dihitung di | Database (MySQL) | PHP (RAM) |
| SQL yang dijalankan | Subquery `(select count(*) from users inner join course_user ...) as students_count` | `select users.* from users inner join course_user ...` |
| Data yang dikirim ke PHP | 1 angka per mata kuliah | Seluruh baris mahasiswa beserta semua kolomnya |
| Objek Eloquent dibuat | 0 | 1 objek `User` per mahasiswa |
| Di dalam loop | Tetap 1 query (subquery di query induk) | 1 query per mata kuliah, sehingga jadi N+1 |

### Kenapa yang kedua boros

`$course->students` memuat **seluruh** baris mahasiswa ke memori, membuat satu objek `User` per baris, lalu `count()` hanya menghitung panjang koleksinya. Setelah itu semua objek dibuang. Jadi yang diminta hanya satu angka, tapi yang dimuat seluruh data mahasiswa.

Kalau dipanggil di dalam loop daftar mata kuliah, masalahnya berlipat: satu query plus satu koleksi penuh untuk setiap mata kuliah.

### Contoh nyata di KampusLMS

1. **Daftar mata kuliah mahasiswa** sudah memakai `withCount(['students', ..., 'materials'])`. Blade cukup membaca `$item->students_count`.
2. **Dashboard dosen** adalah contoh kasus "memuat untuk menghitung". Blade lama memuat seluruh relasi untuk 4 angka (**69 model**). Sekarang [DosenDashboardStats.php](file:///d:/Proweb/kampuslms-kelompok-02/app/Support/DosenDashboardStats.php#L115-L133) menghitungnya dengan `COUNT` di database:
   ```php
   'materials'   => Material::whereIn('course_id', $courseIds)->count(),
   'assignments' => Assignment::whereIn('course_id', $courseIds)->count(),
   ```
   Hasilnya **2 model**. Ini bukti angka untuk pertanyaan ini.

> [!TIP]
> Perhatikan bedanya: `$course->students()->count()` (pakai kurung) adalah **satu query COUNT** di database. `$course->students->count()` atau `count($course->students)` (tanpa kurung) **memuat seluruh koleksi** dulu. Hanya beda sepasang tanda kurung, tapi dampaknya besar.

Untuk membuktikan langsung (percobaan BREAK nomor 3), ganti `withCount('students')` di `MahasiswaCourseController` dengan `count($item->students)` di Blade (perlu mematikan `preventLazyLoading` dulu), lalu bandingkan kolom **Models** dan **Memory** di Debugbar.

---

## 4. Index dan bukti EXPLAIN

> ❓ *Tunjukkan index yang Anda tambahkan. Jalankan `EXPLAIN` dan buktikan ia terpakai.*

### Index yang ditambahkan

Migrasi: [2026_11_03_170242_add_performance_indexes_table.php](file:///d:/Proweb/kampuslms-kelompok-02/database/migrations/2026_11_03_170242_add_performance_indexes_table.php)

| # | Index | Kolom | Query yang diuntungkan |
|---|---|---|---|
| 1 | `idx_submissions_user_id` | `submissions(user_id)` | Riwayat pengumpulan milik satu mahasiswa |
| 2 | `idx_submissions_submitted_at` | `submissions(submitted_at)` | `latest('submitted_at')` di daftar pengumpulan |
| 3 | `idx_assignments_course_status_due` | `assignments(course_id, status, due_at)` | Tugas `published` per mata kuliah, urut tenggat |
| 4 | `idx_courses_lecturer_status` | `courses(lecturer_id, status)` | Mata kuliah aktif milik dosen (dipakai `DosenDashboardStats`) |
| 5 | `idx_course_user_user_id` | `course_user(user_id)` | Daftar mata kuliah yang diikuti mahasiswa |

Setiap index punya komentar di migrasi yang menyebut query yang diuntungkan dan biayanya (penulisan jadi sedikit lebih lambat).

### Cara membuktikan

Tim sudah membuat command khusus [ExplainIndexes.php](file:///d:/Proweb/kampuslms-kelompok-02/app/Console/Commands/ExplainIndexes.php). Command ini hanya membaca (`EXPLAIN`), tidak mengubah data, dan memilih id secara otomatis dari data yang ada.

```bash
php artisan perf:explain-indexes            # kondisi DENGAN index
php artisan migrate:rollback --step=1       # lepas index
php artisan perf:explain-indexes            # kondisi TANPA index
php artisan migrate                         # pasang index lagi
```

> [!WARNING]
> `migrate:rollback` hanya dijalankan di database **lokal**. Jangan di server produksi.

### Hasil EXPLAIN (`docs/performa.md` bagian 5)

| # | Index | Tanpa index | Dengan index |
|---|---|---|---|
| 3 | `idx_assignments_course_status_due` | `key=assignments_course_id_due_at_index, rows=3, Extra=Using where` | `key=idx_assignments_course_status_due, rows=2` |
| 4 | `idx_courses_lecturer_status` | `key=courses_lecturer_id_foreign, rows=1, Extra=Using where` | `key=idx_courses_lecturer_status, rows=1` |
| 1 | `idx_submissions_user_id` | `key=submissions_user_id_foreign` | `key=idx_submissions_user_id` |
| 2 | `idx_submissions_submitted_at` | `Using filesort` | `Using filesort` (tidak berubah) |
| 5 | `idx_course_user_user_id` | `key=course_user_user_id_foreign` | `key=course_user_user_id_foreign` (tidak berubah) |

### Cara membaca hasil EXPLAIN

- **`key`**: index yang benar-benar dipilih MySQL. Kalau isinya nama index kita, berarti index terpakai.
- **`type`**: `ALL` berarti seluruh tabel dipindai (buruk). `ref` atau `range` berarti memakai index.
- **`rows`**: perkiraan jumlah baris yang diperiksa.
- **`Extra=Using where`**: MySQL masih harus menyaring baris setelah membaca index. Kalau hilang, index sudah mencakup semua kondisi `WHERE`.
- **`Extra=Using filesort`**: MySQL mengurutkan hasil secara manual, tidak memakai urutan index.

### Bukti terkuat: index nomor 3 dan 4

Untuk demo ke dosen, pakai **nomor 3**:

```sql
EXPLAIN SELECT * FROM assignments WHERE course_id = 1 AND status = 'published' ORDER BY due_at;
```

- Sebelum: MySQL memakai index lama `(course_id, due_at)` lalu masih harus menyaring `status` (`Using where`), dengan perkiraan 3 baris.
- Sesudah: `possible_keys` menampilkan dua kandidat, dan optimizer **memilih index baru** karena mencakup `course_id`, `status`, dan `due_at` sekaligus. `Using where` hilang dan perkiraan baris turun menjadi 2.

Nomor 4 polanya sama: `Using where` hilang karena `status` sudah ada di dalam index.

> [!WARNING]
> **Siapkan jawaban jujur untuk index nomor 1, 2, dan 5.** Dosen yang teliti akan menanyakannya.
>
> - **Nomor 5 (`course_user.user_id`) tidak terpakai.** MySQL tetap memilih `course_user_user_id_foreign`. Penyebabnya: InnoDB **otomatis membuat index untuk setiap foreign key** (modul baris 722). Jadi index ini duplikat dan sebenarnya tidak perlu ditambahkan. Index ini hanya menambah biaya penulisan tanpa manfaat.
> - **Nomor 1 (`submissions.user_id`)** memang dipilih, tapi hanya menggantikan index foreign key `submissions_user_id_foreign` yang sudah ada dengan fungsi yang sama. Manfaat nyatanya hampir nol, jadi kasusnya sama seperti nomor 5.
> - **Nomor 2 (`submitted_at`)** tidak menghilangkan `Using filesort`. Query-nya memfilter lewat `EXISTS` ke tabel `assignments`, sehingga MySQL tidak bisa langsung membaca urutan dari index `submitted_at`.
>
> Ini justru menjawab salah satu ketentuan prompt modul (bagian 11.2 C): *"Tandai index yang TIDAK perlu ditambahkan meski kelihatan masuk akal, dan jelaskan kenapa."* Mengakui ini menunjukkan kalian benar-benar membaca hasil EXPLAIN.

Keterbatasan lain: data uji kecil (17 mahasiswa, 20 pengumpulan), jadi selisih `rows` kecil. Dengan data besar (percobaan BREAK nomor 5: 300 mahasiswa, 1000 submission), perbedaannya akan jauh lebih terlihat.

---

## 5. Data yang tidak boleh di-cache

> ❓ *Sebutkan satu data di aplikasi Anda yang tidak boleh di-cache. Kenapa?*

### Jawaban utama: angka "Perlu dinilai" di dashboard dosen

Ini contoh terbaik karena keputusannya **tertulis di kode**. Di [DosenDashboardStats.php](file:///d:/Proweb/kampuslms-kelompok-02/app/Support/DosenDashboardStats.php#L56-L63), tiga angka di-cache tetapi "Perlu dinilai" sengaja dihitung ulang setiap kali:

```php
return [
    'courses'          => $courses,
    'totalStudents'    => $stats['students'],     // dari cache
    'totalMaterials'   => $stats['materials'],    // dari cache
    'totalAssignments' => $stats['assignments'],  // dari cache
    // Tidak di-cache: lihat catatan di atas class.
    'pendingGrading'   => self::perluDinilai($courseIds),
];
```

**Alasannya:** dosen yang baru selesai memberi nilai lalu kembali ke dashboard dan melihat angka "Perlu dinilai" tidak turun akan mengira penilaiannya **gagal tersimpan**. Ia bisa menilai ulang atau melapor bug. Padahal menghitungnya cukup satu query `COUNT` ringan, jadi menyimpannya di cache tidak sepadan dengan risikonya.

Ini juga dibuktikan oleh test `test_perlu_dinilai_selalu_segar_dan_tidak_ikut_di_cache` di [DashboardDosenCacheTest.php](file:///d:/Proweb/kampuslms-kelompok-02/tests/Feature/DashboardDosenCacheTest.php#L128).

### Data lain yang sengaja tidak di-cache

| Data | Alasan |
|---|---|
| Nilai mahasiswa dan rekap nilai | Nilai sudah keluar tetapi belum tampil langsung merugikan mahasiswa dan memicu komplain. |
| Status pengumpulan dan batas waktu | Mahasiswa harus melihat status terkini sebelum tenggat. Cache bisa membuatnya mengira tugas belum terkirim lalu mengunggah ulang. |
| Presensi yang sedang berlangsung | Sesi presensi berbatas waktu. Mahasiswa yang sudah memasukkan kode harus langsung melihat status "Hadir". Modul menyebut rekap kehadiran basi saat presensi berlangsung sebagai "bencana". |
| Dashboard mahasiswa | Datanya milik tiap mahasiswa dan berubah setiap kali ia mengumpulkan tugas. |
| Lonceng notifikasi | Harus selalu terbaru. |

### Data yang di-cache, sebagai pembanding

Tiga angka dashboard dosen (jumlah mahasiswa, materi, tugas), kunci `dosen.{id}.dashboard-stats`, berlaku 10 menit. Kalau basi beberapa menit, tidak ada yang dirugikan.

Pembersihan otomatis dipasang **di model** agar semua jalur penulisan (controller dosen, admin, dan API) ikut tertangkap. Contoh di `Assignment`:

```php
protected static function booted(): void
{
    static::saved(fn (Assignment $assignment) => DosenDashboardStats::forgetForCourse($assignment->course_id));
    static::deleted(fn (Assignment $assignment) => DosenDashboardStats::forgetForCourse($assignment->course_id));
}
```

Pengecualiannya pendaftaran mahasiswa: `attach()` dan `detach()` **tidak memicu event model**, jadi `Cache::forget` dipanggil manual di `Dosen/StudentController` dan `Admin/AdminStudentController`.

---

## 6. `env()` setelah `config:cache`

> ❓ *Apa yang terjadi pada `env()` setelah `config:cache`? Kenapa ini penting minggu depan?*

### Apa yang terjadi

Setelah `php artisan config:cache`, pemanggilan `env('NAMA')` di luar folder `config/` **mengembalikan `null`**.

### Kenapa

`config:cache` membaca seluruh `config/*.php` (termasuk nilai dari `.env` di dalamnya), lalu menyimpannya menjadi satu file `bootstrap/cache/config.php`. Setelah file itu ada, Laravel **tidak membaca `.env` lagi** di setiap request, supaya proses boot lebih cepat. Akibatnya `env()` tidak punya sumber nilai.

Pengecualian: environment variable sungguhan yang diset di server tetap terbaca. Tapi di KampusLMS semua nilai ada di `.env`, jadi praktis semuanya menjadi `null`.

### Kenapa penting untuk Minggu 12

Minggu 12 adalah deployment. Di server produksi, `config:cache`, `route:cache`, dan `view:cache` adalah langkah standar. Kalau masih ada `env()` di controller, aplikasi akan **berjalan normal di laptop tapi rusak di server**, misalnya nama aplikasi kosong, URL salah, atau API key `null`. Bug ini sulit dilacak karena tidak muncul di lokal.

**Aturan:** `env()` **hanya** boleh dipanggil di dalam `config/*.php`. Di tempat lain gunakan `config('app.name')`.

### Status di KampusLMS

Sudah dibersihkan di PR #84 (build 7). Verifikasinya: pencarian `env(` di folder `app`, `routes`, `resources`, `database`, dan `bootstrap` **tidak menghasilkan apa pun**.

### Demo (percobaan BREAK nomor 6)

```bash
php artisan config:cache
php artisan tinker --execute="var_dump(env('APP_NAME'), config('app.name'));"
# env('APP_NAME')     → NULL
# config('app.name')  → "Laravel" / nama aplikasi kalian

php artisan config:clear   # WAJIB: kembalikan ke kondisi development
```

> [!CAUTION]
> Selalu jalankan `php artisan config:clear` setelah demo. Kalau lupa, perubahan `.env` di lokal tidak akan terbaca sampai cache dibersihkan.

---

## 7. Pertanyaan lanjutan yang mungkin muncul

**"Kenapa jumlah query malah naik setelah optimisasi?"**  
Karena `paginate()` menambah satu query `COUNT(*)` untuk menghitung total halaman. Itu biaya yang wajar: lebih baik +1 query daripada memuat seluruh tabel ke memori. Ukuran yang membaik adalah jumlah model.

**"Kenapa dashboard dosen tetap 10 query padahal sudah di-cache?"**  
Karena `CACHE_STORE=database`, sehingga membaca cache juga satu query ke tabel `cache`. Yang turun drastis adalah jumlah model (69 menjadi 2). Dengan Redis atau file, pembacaan cache tidak dihitung sebagai query database.

**"Apa efek samping pagination yang kalian temukan?"**  
Dicatat di `docs/performa.md` temuan 2 dan 3. Jumlah "Berkas Masuk" di detail tugas dan kartu ringkasan rekap nilai memakai `$submissions->count()` atau akumulator di dalam `through()`, sehingga hanya menghitung **halaman aktif** (maksimal 10), bukan seluruh kelas. Statusnya masih ditinjau ulang. Solusinya: hitung total dengan query `COUNT` terpisah, bukan dari koleksi yang sudah dipaginasi.

**"Halaman mana yang belum diperbaiki?"**  
Detail mata kuliah mahasiswa, tetap 16 query. Ini kandidat perbaikan berikutnya dengan eager loading relasi materi dan tugas.

**"Kenapa `LIKE '%kata%'` tidak memakai index?"**  
Index diurutkan dari karakter pertama. Kalau pola diawali wildcard, MySQL tidak tahu harus mulai dari mana, sehingga harus memindai seluruh tabel. `LIKE 'kata%'` masih bisa memakai index.

---

## 8. Ringkasan jawaban singkat

| Pertanyaan | Jawaban inti |
|---|---|
| Halaman terburuk? | Dari query: detail mata kuliah mahasiswa (16). Dari model: dashboard dosen (69 model untuk 4 angka). Rekap nilai tidak terburuk karena sejak awal dirancang tanpa N+1. |
| Peragaan N+1? | Hapus `with('lecturer')` di `MahasiswaCourseController@index`. Muncul `LazyLoadingViolationException`. Tanpa proteksi, ada query `users` berulang per kartu di Debugbar. |
| `withCount` vs `count()`? | `withCount` menghitung di database (1 angka). `count($relasi)` memuat seluruh baris jadi objek di RAM. Contoh nyata: dashboard dosen 69 model menjadi 2. |
| Bukti index? | `php artisan perf:explain-indexes`. Index nomor 3 dipilih optimizer, `Using where` hilang, rows 3 menjadi 2. Index nomor 5 duplikat dengan index FK bawaan InnoDB. |
| Tidak boleh di-cache? | "Perlu dinilai" di dashboard dosen. Kalau basi, dosen mengira penilaiannya gagal. Cukup satu query `COUNT`. |
| `env()` setelah `config:cache`? | Mengembalikan `null` karena `.env` tidak dibaca lagi. Penting untuk deployment Minggu 12. Pakai `config()`. |
