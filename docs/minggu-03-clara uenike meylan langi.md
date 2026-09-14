## Catatan 3.3 - Praktikum Read & Break

Nama : **Clara Uenike Meylan Langi**  
NIM : **10241018**  
Kelompok : **02 - Pemrograman Web**  

---

### Bagian Break (Eksperimen & Analisis Masalah)

Berikut adalah catatan hasil uji coba, analisis kegagalan, dan pemahaman yang saya dapatkan setelah melakukan 5 percobaan pada arsitektur database dan model Laravel:

---

#### 1. Hapus `unique(['course_id', 'user_id'])` dari `course_user`, lalu daftarkan mahasiswa yang sama dua kali
- **Langkah yang Saya Lakukan:**  
  Saya membuka berkas migrasi `database/migrations/2026_09_14_010200_create_course_user_table.php` dan menghapus (atau mengomentari) batasan indeks unik gabungan berikut:
  ```php
  // $table->unique(['course_id', 'user_id']);
  ```
  Kemudian saya melakukan migrasi ulang dan mencoba mendaftarkan mahasiswa yang sama pada mata kuliah yang sama sebanyak dua kali melalui `php artisan tinker`:
  ```php
  DB::table('course_user')->insert(['course_id' => 1, 'user_id' => 3, 'created_at' => now(), 'updated_at' => now()]);
  DB::table('course_user')->insert(['course_id' => 1, 'user_id' => 3, 'created_at' => now(), 'updated_at' => now()]);
  ```

- **Hasil Observasi:**  
  Kedua proses insert menghasilkan nilai `true` tanpa adanya pesan error dari database. Ketika saya memeriksa tabel `course_user`, terdapat dua baris data dengan pasangan `course_id = 1` dan `user_id = 3` yang identik.

- **Analisis & Kesimpulan Saya:**  
  Ketiadaan aturan `unique` pada tabel perantara (*pivot table*) membuat database kehilangan kendali integritas data. Akibatnya, satu mahasiswa bisa terdaftar berulang kali pada mata kuliah yang sama. Hal ini memicu redundansi data dan berpotensi menimbulkan bug fatal pada fitur absensi, perhitungan kuota kelas, maupun rekap nilai akhir. Oleh karena itu, *composite unique key* sangat krusial dipertahankan pada tabel relasi *many-to-many*.

---

#### 2. Tambahkan `role` ke `$fillable` model `User`, lalu kirim request pembuatan user dengan `role=admin` lewat form yang tidak memiliki field `role`
- **Langkah yang Saya Lakukan:**  
  Saya menambahkan atribut `'role'` ke dalam daftar `$fillable` pada model `App\Models\User`:
  ```php
  protected $fillable = [
      'name',
      'email',
      'password',
      'nim_nip',
      'role', // atribut role ditambahkan ke whitelist fillable
  ];
  ```
  Selanjutnya, saya menyimulasikan pengiriman data registrasi/pembuatan akun baru menggunakan mass assignment, di mana payload request diselipkan parameter `role => 'admin'`, meskipun pada formulir tampilan antarmuka (UI) tidak ada inputan untuk role tersebut.

- **Hasil Observasi:**  
  Akun pengguna baru berhasil dibuat dan langsung tersimpan di dalam database dengan nilai `role = "admin"`.

- **Analisis & Kesimpulan Saya:**  
  Ketika sebuah kolom didaftarkan ke dalam properti `$fillable`, Laravel mengizinkan kolom tersebut diisi secara massal (*mass assignment*) melalui perintah seperti `User::create($request->all())`.  
  Ini menciptakan celah kerentanan keamanan yang serius yang dikenal sebagai **Privilege Escalation** (peningkatan hak akses tidak sah). Pengguna biasa dapat memodifikasi payload HTTP (misalnya lewat inspect element browser, Postman, atau cURL) untuk menyuntikkan hak akses sebagai administrator. Kolom penentu otorisasi sensitif seperti `role` sebaiknya tidak dimasukkan ke dalam `$fillable` secara sembarangan, melainkan harus diatur secara terpisah dan terlindungi di sisi backend.

---

#### 3. Ganti seluruh `$fillable` dengan `protected $guarded = [];`, lalu ulangi nomor 2
- **Langkah yang Saya Lakukan:**  
  Saya menghapus seluruh konfigurasi `$fillable` pada model `User` dan menggantinya dengan array kosong pada properti `$guarded`:
  ```php
  protected $guarded = [];
  ```
  Setelah itu, saya mengulangi pengujian pembuatan user dengan menyisipkan parameter `role = 'admin'` seperti pada percobaan kedua.

- **Hasil Observasi:**  
  Proses pembuatan user tetap berjalan mulus dan nilai atribut `role` berhasil tersimpan sebagai `"admin"`.

- **Analisis & Kesimpulan Saya:**  
  Properti `$guarded` bekerja sebagai *blacklist* (daftar atribut yang diblokir dari mass assignment). Ketika kita memberikan nilai array kosong `[]`, artinya kita menonaktifkan seluruh sistem proteksi mass assignment di Eloquent.  
  Kondisi ini jauh lebih berbahaya daripada percobaan nomor 2 karena semua kolom tabel di database menjadi terbuka tanpa perlindungan. Pengguna jahat tidak hanya bisa mengubah `role`, tetapi juga bisa memanipulasi kolom sensitif lain seperti `email_verified_at`, token keamanan, atau bahkan atribut internal lainnya yang seharusnya dikelola eksklusif oleh sistem.

---

#### 4. Kosongkan isi `down()` di salah satu migrasi, lalu jalankan `php artisan migrate:refresh`
- **Langkah yang Saya Lakukan:**  
  Saya mengosongkan instruksi pada method `down()` di berkas migrasi tabel `courses`:
  ```php
  public function down(): void
  {
      // Perintah Schema::dropIfExists('courses'); sengaja dikosongkan
  }
  ```
  Kemudian saya menjalankan perintah refresh migrasi pada terminal:
  ```bash
  php artisan migrate:refresh
  ```

- **Hasil Observasi:**  
  Proses `migrate:refresh` mengalami kegagalan dan memunculkan error integrity constraint / foreign key (seperti `SQLSTATE[HY000]: 3730`). Laravel gagal menjatuhkan (*drop*) tabel lain seperti `users` karena tabel `courses` masih tertinggal dan memegang referensi foreign key ke tabel `users`.

- **Analisis & Kesimpulan Saya:**  
  Perintah `migrate:refresh` bekerja secara dua tahap: memanggil method `down()` secara berurutan terbalik (rollback) untuk membersihkan semua tabel lama, lalu memanggil `up()` untuk membangun skema baru dari awal.  
  Jika method `down()` kosong atau tidak menghapus tabel terkait dengan benar, struktur relasi basis data akan terkunci dan memicu penolakan dari engine MySQL. Dari sini saya memahami bahwa method `down()` wajib ditulis secara simetris sebagai pembalik yang tepat dari method `up()` demi kelancaran siklus pengembangan (*development & rollback*).

---

#### 5. Ubah `restrictOnDelete` pada `lecturer_id` menjadi `cascadeOnDelete`, lalu hapus satu dosen
- **Langkah yang Saya Lakukan:**  
  Pada berkas migrasi `create_courses_table`, saya mengubah aturan penghapusan kunci asing (*foreign key*) pada kolom `lecturer_id`:
  ```php
  // Semula:
  // $table->foreignId('lecturer_id')->constrained('users')->restrictOnDelete();

  // Diubah menjadi:
  $table->foreignId('lecturer_id')->constrained('users')->cascadeOnDelete();
  ```
  Setelah migrasi diperbarui, saya menjalankan `php artisan tinker` dan menghapus salah satu akun dosen yang sedang mengampu mata kuliah:
  ```php
  $dosen = User::where('role', 'dosen')->first();
  $dosen->delete();
  ```
  Lalu saya memeriksa apakah data mata kuliah yang terhubung masih ada:
  ```php
  Course::where('lecturer_id', $dosen->id)->get();
  ```

- **Hasil Observasi:**  
  Perintah penghapusan akun dosen berhasil dieksekusi tanpa penolakan. Namun, saat mata kuliah diperiksa, hasilnya mengembalikan koleksi kosong (`all: []`). Artinya, seluruh mata kuliah yang diampu oleh dosen tersebut langsung terhapus otomatis dari database.

- **Analisis & Kesimpulan Saya:**  
  Aturan `cascadeOnDelete()` secara otomatis menghapus seluruh baris data turunan (*child records*) saat data induk (*parent record*) dihapus. Meskipun fitur ini terlihat praktis, penerapannya pada entitas utama perkuliahan sangat berisiko fatal pada sistem produksi nyata. Jika seorang dosen terhapus (baik sengaja maupun tidak sengaja), maka seluruh mata kuliah, materi ajar, daftar tugas, pengumpulan tugas mahasiswa, serta riwayat nilai mahasiswa yang berada di bawahnya akan ikut terhapus permanen.  
  Oleh sebab itu, konfigurasi bawaan `restrictOnDelete()` terbukti jauh lebih aman dan tepat untuk relasi ini, karena sistem akan menolak penghapusan akun dosen selama ia masih tercatat mengampu mata kuliah aktif.
