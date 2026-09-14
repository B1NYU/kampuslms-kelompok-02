## Catatan 3.3

Nama : **Devina Dian Saputri**      
NIM : **10241022**

---
### Break

1. Hapus `unique(['course_id', 'user_id'])` dari `course_user`, lalu daftarkan mahasiswa yang sama dua kali     
   _Jawaban_ :      
   Menghapus indeks `unique` pada berkas migrasi `course_user`. Baris kode yang dihapus adalah:  
   ``` php 
   $table->unique(['course_id', 'user_id']);
   ```     
    Penghapusan kode tersebut membuat database tidak lagi memiliki aturan yang melarang kombinasi`course_id` dan `user_id` yang sama. Oleh karena itu, ketika data mahasiswa yang sama dimasukkan kembali menggunakan `DB::table('course_user')->insert()` melalui `php artisan tinker` proses tetap dapat dilakukan dan menghasilkan `true`.
    Dari hasil tersebut dapat dilihat bahwa satu mahasiswa bisa tercatat lebih dari satu kali pada mata kuliah yang sama. Hal ini menyebabkan munculnya data duplikat pada tabel pivot.

2. Tambahkan `role` ke `$fillable` model `User`, lalu kirim request pembuatan user dengan `role=admin` lewat form yang tidak memiliki field `role`      
   _Jawaban_ :      
   Menambahkan `role` ke dalam properti `$fillable` pada model `User`
   ``` php
   protected $fillable = [
        'name',
        'email',
        'password',
        'nim_nip',
        'role',
    ];
    ```
    Setelah perubahan dilakukan, user baru dapat dibuat dengan nilai `role` berupa `"admin"`, meskipun form yang digunakan tidak menyediakan input untuk role.  
    Hal ini terjadi karena `$fillable` memberikan izin kepada Laravel untuk memasukkan atribut yang tercantum di dalamnya ketika menggunakan mass assignment, misalnya melalui `User::create($request->all())`. 
    Kondisi tersebut dapat menjadi celah keamanan. Jika pengguna dapat mengubah isi request, mereka bisa menambahkan `role=admin` dan memperoleh hak akses sebagai admin. Situasi ini disebut Privilege Escalation, yaitu ketika pengguna berhasil mendapatkan tingkat akses yang seharusnya tidak dimiliki.

3. Ganti seluruh `$fillable` dengan `protected $guarded = [];`, lalu ulangi nomor 2     
   _Jawaban_ :          
   Mengubah properti `$fillable` pada model `User` menjadi:
   ``` php
   protected $guarded = [];
   ```
   Hasil percobaan menunjukkan bahwa pembuatan user tetap berhasil dan nilai `role` dapat diisi dengan `"admin"`.
   `$guarded` digunakan untuk menentukan atribut yang tidak boleh diisi melalui mass assignment. Ketika nilainya dibuat menjadi array kosong (`[]`), tidak ada atribut yang dibatasi. Artinya, seluruh kolom pada model dapat menerima data dari proses mass assignment.
   Pengaturan ini dapat menimbulkan risiko keamanan karena pengguna berpotensi memasukkan data pada kolom yang seharusnya tidak dapat mereka ubah. Contohnya adalah `role` dan `email_verified_at`, bahkan data yang berkaitan dengan password juga dapat ikut dimasukkan jika tersedia pada request.  

4. Kosongkan isi `down()` di salah satu migrasi, lalu jalankan `php artisan migrate:refresh`            
   _Jawaban_ :      
   Mengosongkan instruksi di dalam method `down()` pada migrasi tabel `courses`:  
   ``` php
   public function down(): void
    {
        // Dikosongkan (Schema::dropIfExists('courses') dihapus)
    }
    ```
    Saat `php artisan migrate:refresh` dijalankan, Laravel tidak dapat menghapus tabel `courses` karena tidak ada perintah penghapusan di dalam `down()`.
    Masalah tersebut kemudian memengaruhi proses penghapusan tabel lainnya. Tabel `courses` masih memiliki hubungan foreign key `lecturer_id` dengan tabel `users`, sehingga ketika Laravel mencoba menghapus tabel `users`, MySQL menolak proses tersebut dan menghasilkan error `SQLSTATE[HY000]: 3730`.
    Dari percobaan ini dapat dipahami bahwa method `up()` dan `down()` memiliki fungsi yang berlawanan. up() digunakan ketika perubahan database diterapkan, sedangkan down() digunakan untuk mengembalikan atau membatalkan perubahan tersebut.
    Pada `migrate:refresh`, Laravel terlebih dahulu menjalankan `down()` untuk membongkar struktur database yang lama, kemudian menjalankan `up()` untuk membuatnya kembali. Jika salah satu `down()` tidak menjalankan proses penghapusan dengan benar, proses refresh dapat mengalami kegagalan.  

5. Ubah `restrictOnDelete` pada `lecturer_id` menjadi `cascadeOnDelete`, lalu hapus satu dosen          
   _Jawaban_ :      
   Mengubah definisi relasi foreign key pada berkas migrasi courses:  
   ``` php
   // $table->foreignId('lecturer_id')->constrained('users')->restrictOnDelete();
    $table->foreignId('lecturer_id')->constrained('users')->cascadeOnDelete();
    ```
    Ketika data dosen dihapus menggunakan `$dosen->delete()` melalui `php artisan tinker`, proses berhasil dan menghasilkan true.
    Pengecekan berikutnya dilakukan untuk melihat apakah masih ada mata kuliah yang memiliki `lecturer_id` milik dosen tersebut:
    `Course::where('lecturer_id', $dosen->id)->get()`
    Hasilnya menunjukkan koleksi kosong (`all: []`). Artinya, data mata kuliah yang sebelumnya terhubung dengan dosen tersebut ikut terhapus secara otomatis ketika data dosennya dihapus.
    Walaupun cara ini memudahkan penghapusan data yang saling berhubungan, `cascadeOnDelete` memiliki risiko yang cukup besar jika diterapkan pada data produksi. Penghapusan satu akun dosen dapat menyebabkan seluruh data mata kuliah yang berkaitan dengannya ikut hilang, termasuk materi, tugas, dan nilai mahasiswa.
    Oleh sebab itu, penggunaan `restrictOnDelete()` lebih aman untuk kondisi tersebut. Dengan aturan ini, data dosen tidak dapat dihapus selama masih terdapat data mata kuliah yang menggunakan dosen tersebut sebagai `lecturer_id`.