# Laporan Perbaikan Bug (Debugging Log) KampusLMS

Dokumen ini mencatat daftar bug krusial yang ditemukan, dianalisis, dan diperbaiki oleh anggota kelompok beserta langkah reproduksi, cara mempersempit penyebab, perbaikan kode, dan tes pencegah regresi (regression test).

---

## Bug #1 — Tes hijau, padahal pemeriksaan otorisasi (Gate) dapat dihapus

**Berkas:** `app/Http/Controllers/Dosen/StudentController.php` (dan rute dosen lainnya)  
**Ditemukan oleh:** Baihaqi Abimanyu Toro Putra (10241014) · **Status:** Diperbaiki (PR #112)

### Gejala
Perintah `php artisan test` menunjukkan seluruh tes lulus (190 tes). Namun, ketika baris `Gate::authorize('manageEnrollment', $course)` dihapus dari `Dosen\StudentController::destroy`, **tidak ada satu pun tes yang menjadi merah**. Artinya, dosen A bisa mengeluarkan mahasiswa dari kelas milik dosen B tanpa terdeteksi oleh suite pengujian otomatis (sesuai baris 10 pada tabel Titik Rawan IDOR di `docs/keamanan.md`).

### Langkah Reproduksi
1. Buka `app/Http/Controllers/Dosen/StudentController.php`, method `destroy`.
2. Beri komentar pada baris `Gate::authorize('manageEnrollment', $course);`.
3. Jalankan `php artisan test`.
4. **Hasil:** Semua tes tetap lulus. Pulihkan kode dengan `git checkout -- app`.

### Cara Mempersempit Penyebab
- **Hipotesis:** Tes keamanan yang ada hanya menguji jalur sukses (pengampu berhak), bukan jalur ditolak (dosen lain).
- **Pemeriksaan (Uji Mutasi):** 54 titik `Gate::authorize` / `abort_unless` di `app/Http/Controllers` dan `app/Http/Requests` dihapus satu per satu secara sistematis.
- **Hasil Uji Mutasi:** 25 titik membuat tes merah, sedangkan **29 titik tidak membuat tes apa pun merah**.
- **Mempersempit Lebih Jauh:** Dibandingkan dengan peta rute, rute dosen hanya dilindungi middleware `role:dosen`, sehingga pemisahan akses antar-dosen sepenuhnya bergantung pada `Gate::authorize`. Di situlah celah IDOR terkonsentrasi: materi, kelola mahasiswa, rekap nilai, dan presensi dosen.

### Penyebab
Suite tes yang ada sebelumnya hanya membuktikan bahwa **pengampu berhak** bisa melakukan aksinya. Tidak ada tes yang membuktikan bahwa **dosen lain ditolak**. Menghapus Gate tidak mengubah hasil tes mana pun karena jalur sukses tetap berjalan. Selain itu, belum ada tes yang berhasil memeriksa isi halaman rekap nilai dosen untuk jalur sukses.

### Perbaikan dan Tes Pencegah
Ditambahkan `tests/Feature/OtorisasiAntarDosenTest.php` (22 tes). Setiap tes jalur ditolak menjalankan:
1. Pengiriman request sebagai dosen lain dan memastikan respons HTTP **403 Forbidden**.
2. Verifikasi bahwa **data tidak berubah** di database/storage (baris DB tetap ada, berkas tidak terhapus).
3. Pengujian dipasangkan dengan **kontrol positif** (pengampu sah tetap berhasil) agar tes tidak *false positive*.

**Bukti Perbaikan:** Uji mutasi diulang pada 54 titik. Mutan terbunuh naik dari 25 menjadi 38, mutan selamat turun dari 29 menjadi 16 (sisa 16 merupakan mutan ekuivalen yang dilindungi `role:admin` atau middleware API).

### Pelajaran
- Tes hijau tidak membuktikan apa pun kecuali tes tersebut pernah dibuat merah.
- Cara menguji kualitas tes adalah merusak kodenya dengan sengaja dan memastikan tes menjadi merah.
- Jalur "ditolak" (negative path) sama pentingnya dengan jalur "sukses" (positive path) pada aplikasi berotorisasi.

#### Catatan Tambahan: `dd()` Tertinggal di Controller
Percobaan meletakkan `dd('DEBUG TERTINGGAL');` di `RekapNilaiController::show` menyebabkan seluruh proses PHPUnit berhenti di tengah jalan tanpa baris ringkasan `Tests: X passed`. Hal ini terpotong secara diam-diam sehingga sangat berbahaya jika CI hanya dibaca sekilas. Dipastikan `dd()` tidak boleh masuk ke Pull Request.

---

## Bug #2 — `migrate:rollback` gagal di SQLite karena `down()` migrasi users tidak menghapus indeks unique

**Berkas:** `database/migrations/2026_09_14_010000_add_role_and_softdeletes_to_users_table.php`  
**Ditemukan oleh:** Devina Dian Saputri (10241022) · **Status:** Diperbaiki (PR #113)

### Gejala
Menjalankan `php artisan migrate:fresh` lalu `php artisan migrate:rollback` dengan `DB_CONNECTION=sqlite` menghasilkan error berikut:

```text
2026_09_14_010000_add_role_and_softdeletes_to_users_table ... FAIL
SQLSTATE[HY000]: General error: 1 error in index users_nim_nip_unique
after drop column: no such column: "nim_nip"
(SQL: alter table "users" drop column "nim_nip")
```

Pada MySQL, perintah yang sama lolos penuh tanpa error. Hal ini menyebabkan CI (yang menggunakan MySQL 8) tetap hijau dan bug tersembunyi.

### Langkah Reproduksi
1. Atur `.env` atau jalankan environment dengan `DB_CONNECTION=sqlite`.
2. Jalankan `php artisan migrate:fresh`.
3. Jalankan `php artisan migrate:rollback`.
4. **Hasil:** Rollback membatalkan 12 migrasi terakhir lalu berhenti dengan error `users_nim_nip_unique`.

### Cara Mempersempit Penyebab
1. **Analisis Stack Trace:** Baris pertama di luar `vendor/` menunjuk ke `database/migrations/2026_09_14_010000_...php:24` di method `down()`.
2. **Pesan Error:** Indeks `users_nim_nip_unique` masih melekat saat kolom `nim_nip` dicoba untuk dihapus (`dropColumn`).
3. **Komparasi `up()` dan `down()`:** Method `up()` membuat indeks `role` dan `unique('nim_nip')`. Namun, method `down()` hanya memanggil `dropIndex(['role'])` tanpa menghapus `dropUnique(['nim_nip'])`.
4. **Perbedaan Engine DB:** SQLite menolak menghapus kolom yang masih diacu oleh indeks unique, sedangkan MySQL menghapus indeks kolom tunggal secara otomatis saat kolom dihapus.

### Perbaikan
Menambahkan `dropUnique(['nim_nip'])` sebelum pemanggilan `dropColumn()` pada method `down()` migrasi terkait:

```php
public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropIndex(['role']);
        $table->dropUnique(['nim_nip']); // Menghapus indeks unique SQLite terlebih dahulu
        $table->dropColumn(['role', 'nim_nip', 'deleted_at']);
    });
}
```

### Test yang Mencegahnya Kembali
Dibuat `tests/Feature/MigrationRollbackTest.php::test_semua_migrasi_bisa_di_rollback_dan_dimigrasi_ulang` yang menjalankan `migrate:fresh`, `migrate:rollback`, lalu `migrate` di SQLite in-memory (`phpunit.xml`).

- **Sebelum perbaikan:** Merah (`QueryException ... users_nim_nip_unique`).
- **Setelah perbaikan:** Hijau (1 test, 6 assertions).

### Pelajaran
- Setiap `unique()` atau `index()` di method `up()` wajib memiliki pasangan `dropUnique()` / `dropIndex()` di method `down()` sebelum memanggil `dropColumn()`.
- Pengujian pada satu DBMS saja (misal MySQL di CI) tidak menjamin skrip migrasi aman di DBMS lain (misal SQLite di dev/test).

---

## Bug #3 — Kartu ringkasan statistik rekap nilai berubah mengikuti paginasi aktif

**Berkas:** `app/Support/RekapNilaiService.php`  
**Ditemukan oleh:** Desta Rifqi Aunur Rahman (10241020) · **Status:** Diperbaiki

### Gejala
Pada halaman Rekapitulasi Nilai Dosen, saat berpindah dari Halaman 1 ke Halaman 2 (`?page=2`), kartu ringkasan **Mahasiswa** (`15`) dan **Tugas Published** (`2`) tetap konstan. Namun, kartu **Dinilai / Menunggu** dan **Rata-rata Kelas** berubah drastis mengikuti sisa mahasiswa yang hanya tampil di halaman aktif tersebut:

| Kartu Ringkasan | Halaman 1 (10 Mhs) | Halaman 2 (5 Mhs) | Nilai Seharusnya (Total Kelas) |
| :--- | :---: | :---: | :---: |
| **Mahasiswa** | `15` | `15` | `15` |
| **Tugas Published** | `2` | `2` | `2` |
| **Dinilai / Menunggu** | **`8 / 3`** | **`6 / 2`** | **`14 / 5`** |
| **Rata-rata Kelas** | **`77%`** | **`79.2%`** | **Rata-rata Keseluruhan Kelas** |

### Langkah Reproduksi
1. Buat/pilih Mata Kuliah dengan 15 mahasiswa terdaftar dan 2 Tugas berstatus *published*.
2. Isi beberapa nilai pengumpulan tugas (misal 14 submission dinilai, 5 submission menunggu).
3. Buka rute `dosen.rekap.show` (Halaman 1, menampilkan 10 mahasiswa pertama). Catat angka statistik kartu.
4. Klik pagination ke Halaman 2 (`?page=2`, menampilkan 5 mahasiswa tersisa).
5. **Hasil:** Angka pada kartu "Dinilai / Menunggu" dan "Rata-rata Kelas" berubah menyusut karena hanya menghitung 5 mahasiswa di halaman 2.

### Cara Mempersempit Penyebab
1. Buka `app/Support/RekapNilaiService.php` pada method `buildForCourse()`.
2. Amati variabel penampung statistik agregat (`$gradedCells`, `$waitingCells`, `$sumPercentAll`).
3. Variabel tersebut diakumulasikan menggunakan pass-by-reference (`&$gradedCells`, `&$waitingCells`, `&$sumPercentAll`) di dalam callback `$students->through(function ($student) use (...) { ... })`.
4. Karena `$students` adalah instance `LengthAwarePaginator` (di-limit 10 per halaman), fungsi `$students->through()` hanya melakukan iterasi pada 10 mahasiswa di halaman terpilih, bukan 15 mahasiswa di kelas.

### Penyebab
Logika perhitungan statistik kartu ringkasan menumpang pada fungsi pembentukan baris tabel paginasi. Akibatnya, statistik agregat kelas ikut terpotong (*truncated*) sesuai subset data mahasiswa yang sedang ditampilkan oleh paginator.

### Perbaikan
Memisahkan perhitungan data agregat kelas dari pembentukan baris tabel halaman aktif. Statistik dihitung dari seluruh submission mahasiswa di kelas tersebut, kemudian collection submission disaring (*filter*) di memori untuk keperluan penyusunan baris tabel paginasi:

```php
// 1. Ambil seluruh ID mahasiswa terdaftar di kelas
$allStudentIds = $course->students()
    ->where('users.role', 'mahasiswa')
    ->pluck('users.id');

$assignmentsById = $assignments->keyBy('id');

// 2. Query seluruh submission untuk statistik kelas
$allSubmissions = Submission::query()
    ->whereIn('assignment_id', $assignments->pluck('id'))
    ->whereIn('user_id', $allStudentIds)
    ->with('grade:id,submission_id,score')
    ->get(['id', 'assignment_id', 'user_id', 'is_late']);

$gradedCells = 0;
$waitingCells = 0;
$sumPercentAll = 0.0;

foreach ($allSubmissions as $sub) {
    if (! $sub->grade) {
        $waitingCells++;
        continue;
    }

    $assignment = $assignmentsById->get($sub->assignment_id);
    $maxScore = $assignment ? (float) $assignment->max_score : 0.0;
    $score = (float) $sub->grade->score;
    $percent = $maxScore > 0 ? ($score / $maxScore) * 100 : 0.0;

    $gradedCells++;
    $sumPercentAll += $percent;
}

// 3. Filter di memori untuk baris tabel halaman aktif saja (tanpa query ulang)
$pageStudentIds = $students->pluck('id')->flip();
$submissions = $allSubmissions
    ->filter(fn ($s) => $pageStudentIds->has($s->user_id))
    ->keyBy(fn ($s) => $s->user_id . '-' . $s->assignment_id);

$rows = $students->through(function ($student) use ($assignments, $submissions) {
    // Murni membentuk struktur cell per mahasiswa tanpa mengubah state agregat luar
    ...
});
```

### Test yang Mencegahnya Kembali
Dibuat `tests/Feature/RekapNilaiPaginasiTest.php::test_kartu_ringkasan_rekap_nilai_konsisten_di_semua_halaman_paginasi` yang membuat 11 mahasiswa (2 halaman paginasi) dengan 9 submission dinilai dan 2 submission menunggu.

- **Sebelum perbaikan:** Merah (Assertion gagal pada Halaman 1 karena menghitung 9/1, dan Halaman 2 menghitung 0/1).
- **Setelah perbaikan:** Hijau (1 test, 11 assertions). Statistik Halaman 1 dan Halaman 2 terbukti identik (`statsPage1 === statsPage2`).

### Pelajaran
- Data statistik ringkasan/agregat tingkat entitas (kelas/toko/organisasi) tidak boleh dihitung dari item yang sudah ter-paginate.
- Filter data untuk penyajian tampilan (view pagination) harus dipisahkan dari domain perhitungan statistik agregat.