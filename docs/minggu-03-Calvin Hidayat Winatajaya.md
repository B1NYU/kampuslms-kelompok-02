Nama : **Calvin Hidayat Winatajaya**      
NIM : **10241016**

---
### Break

1. Hapus `unique(['course_id', 'user_id'])` dari `course_user`, lalu daftarkan mahasiswa yang sama dua kali     
   _Jawaban_ :      
   Menghapus indeks `unique` pada berkas migrasi `course_user`. Baris kode yang dihapus adalah:  
   ``` php 
   $table->unique(['course_id', 'user_id']);
   ```     
    Tanpa adanya indeks unique composite, database MySQL/MariaDB tidak akan memeriksa duplikasi kombinasi kolom `course_id` dan `user_id`. Perintah `DB::table('course_user')->insert()` pada terminal saat menjalankan `php artisan tinker` dengan nilai yang sama dapat dieksekusi berulang kali dan mengembalikan nilai `true`, sehingga menghasilkan baris data ganda (duplicate row) pada tabel pivot.  

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
    User baru berhasil dibuat dan langsung mendapatkan atribut `role: "admin"`. 
    Properti `$fillable` menentukan kolom apa saja yang boleh diisi secara otomatis menggunakan fungsi seperti `User::create($request->all())`. Jika `'role'` terdaftar di sana, penyerang dapat memodifikasi payload HTTP/form request untuk mengubah status akun mereka menjadi `admin` (Privilege Escalation).   

3. Ganti seluruh `$fillable` dengan `protected $guarded = [];`, lalu ulangi nomor 2     
   _Jawaban_ :          
   Mengubah properti `$fillable` pada model `User` menjadi:
   ``` php
   protected $guarded = [];
   ```
   Proses pembuatan user baru tetap berhasil berjalan dan atribut `role` secara langsung terisi sebagai `"admin"`. 
   Menuliskan `$guarded = []` sama saja dengan memberi tahu Laravel: "Tidak ada satu pun kolom yang dilindungi". Hal ini mematikan seluruh fitur keamanan Mass Assignment Protection pada Eloquent. Penyerang dapat mengirimkan data apa pun yang ada pada kolom tabel (seperti `role`, `email_verified_at`, hingga hash password), dan database akan langsung menyimpannya.   

4. Kosongkan isi `down()` di salah satu migrasi, lalu jalankan `php artisan migrate:refresh`            
   _Jawaban_ :      
   Mengosongkan instruksi di dalam method `down()` pada migrasi tabel `courses`:  
   ``` php
   public function down(): void
    {
        // Dikosongkan (Schema::dropIfExists('courses') dihapus)
    }
    ```
    Ketika `php artisan migrate:refresh` dijalankan, Laravel memanggil method `down()` secara berurutan. Karena method `down()` pada migrasi `courses` dikosongkan, tabel `courses` gagal dihapus. Akibatnya, saat Laravel mencoba menghapus tabel `users`, MySQL menolaknya (`QueryException / SQLSTATE[HY000]: 3730`) karena foreign key `lecturer_id` pada tabel `courses` masih aktif mengikat tabel `users`.           
    Secara konsep, fungsi `up()` berguna untuk menerapkan perubahan (membuat tabel/kolom), sedangkan fungsi `down()` berguna untuk membatalkan perubahan tersebut (menghapus tabel/kolom). Perintah `migrate:refresh` bekerja dengan cara memanggil fungsi `down()` terlebih dahulu untuk menghapus tabel lama, lalu memanggil `up()` untuk membuat tabel baru. Karena `down()` kosong, Laravel tidak menghapus tabel `courses` yang lama. Saat `up()` mencoba membuat tabel `courses` lagi, database menolaknya karena tabel tersebut sudah ada.  

5. Ubah `restrictOnDelete` pada `lecturer_id` menjadi `cascadeOnDelete`, lalu hapus satu dosen          
   _Jawaban_ :      
   Mengubah definisi relasi foreign key pada berkas migrasi courses:  
   ``` php
   // $table->foreignId('lecturer_id')->constrained('users')->restrictOnDelete();
    $table->foreignId('lecturer_id')->constrained('users')->cascadeOnDelete();
    ```
    Saat menjalankan `$dosen->delete()` pada terminal `php artisan tinker`, perintah mengembalikan nilai `true`. Ketika dilakukan pengecekan data mata kuliah yang diampu menggunakan `Course::where('lecturer_id', $dosen->id)->get()`, sistem mengembalikan koleksi kosong (`all: []`). Hal ini membuktikan bahwa seluruh data mata kuliah yang terhubung dengan `lecturer_id` dosen tersebut telah ikut terhapus secara otomatis oleh database[cite: 1].            
    Penggunaan `cascadeOnDelete` pada `lecturer_id` sangat berbahaya untuk lingkungan produksi (production)[cite: 1]. Jika akun seorang dosen dihapus, seluruh data mata kuliah yang diampu—termasuk materi, tugas, dan nilai mahasiswa di dalamnya—akan hilang secara permanen[cite: 1]. Oleh karena itu, wajib menggunakan `restrictOnDelete()` agar database menolak penghapusan data dosen selama masih ada data mata kuliah yang terikat padanya[cite: 1].