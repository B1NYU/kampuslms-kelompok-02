### Break

1. Hapus `unique(['course_id', 'user_id'])` dari `course_user`, lalu daftarkan mahasiswa yang sama dua kali     

Jawaban :   
Menghapus pembatasan `unique` pada file migrasi `course_user` melalui kode berikut:
```php
$table->unique(['course_id', 'user_id']);

```


Tanpa adanya composite unique index ini, database MySQL/MariaDB tidak lagi memeriksa duplikasi kombinasi `course_id` dan `user_id`. Akibatnya, mengeksekusi perintah `DB::table('course_user')->insert()` berulang kali di `php artisan tinker` dengan data yang sama akan terus mengembalikan nilai `true`, sehingga menghasilkan data ganda (duplicate row) pada tabel pivot.

2. Tambahkan `role` ke `$fillable` model `User`, lalu kirim request pembuatan user dengan `role=admin` lewat form yang tidak memiliki field `role`  

Jawaban :   
Menambahkan `role` ke dalam array `$fillable` pada model `User`:
```php
protected $fillable = [
    'name',
    'email',
    'password',
    'nim_nip',
    'role',
];

```


Data user baru berhasil tersimpan dan langsung mendapatkan nilai `role: "admin"`.
Properti `$fillable` menentukan kolom mana saja yang dapat diisi secara otomatis (mass assignment) melalui perintah seperti `User::create($request->all())`. Ketika `'role'` dimasukkan ke dalam daftar ini, penyerang dapat memanipulasi payload HTTP request untuk menyisipkan parameter `role` dan menaikkan hak akses akun mereka menjadi `admin` (Privilege Escalation).

3. Ganti seluruh `$fillable` dengan `protected $guarded = [];`, lalu ulangi nomor 2     

Jawaban :   
Mengubah properti keamanan pada model `User` menjadi:
```php
protected $guarded = [];

```


Pembuatan user baru tetap berhasil dan atribut `role` langsung terisi nilai `"admin"`.
Mengatur `$guarded = []` berarti memberi instruksi kepada Laravel untuk tidak melindungi kolom mana pun. Hal ini secara penuh mematikan fitur Mass Assignment Protection milik Eloquent. Dampaknya, penyerang dapat menyisipkan dan menyimpan data ke kolom sensitif apa pun di dalam tabel (seperti `role`, `email_verified_at`, hingga hash password).

4. Kosongkan isi `down()` di salah satu migrasi, lalu jalankan `php artisan migrate:refresh`    

Jawaban :   
Mengosongkan perintah di dalam method `down()` pada migrasi tabel `courses`:
```php
public function down(): void
{
    // Dikosongkan (Schema::dropIfExists('courses') dihapus)
}

```


Saat perintah `php artisan migrate:refresh` dijalankan, Laravel memanggil fungsi `down()` untuk menghapus tabel lama sebelum menjalankan `up()` kembali. Karena method `down()` pada `courses` kosong, tabel tersebut tidak terhapus. Hal ini menyebabkan error (`QueryException / SQLSTATE[HY000]: 3730`) saat Laravel mencoba menghapus tabel `users`, karena relasi foreign key `lecturer_id` dari tabel `courses` yang masih ada menghalangi penghapusan tabel `users`.

5. Ubah `restrictOnDelete` pada `lecturer_id` menjadi `cascadeOnDelete`, lalu hapus satu dosen  

Jawaban :   
Mengubah skema foreign key pada migrasi tabel `courses`:
```php
// $table->foreignId('lecturer_id')->constrained('users')->restrictOnDelete();
$table->foreignId('lecturer_id')->constrained('users')->cascadeOnDelete();

```


Ketika perintah `$dosen->delete()` dijalankan melalui `php artisan tinker`, eksekusi berhasil (`true`). Namun, saat dilakukan pengecekan data mata kuliah terkait menggunakan `Course::where('lecturer_id', $dosen->id)->get()`, sistem mengembalikan relasi kosong (`all: []`). Ini menandakan seluruh data mata kuliah yang terikat dengan dosen tersebut terhapus secara otomatis oleh database.
Penerapan `cascadeOnDelete` pada `lecturer_id` sangat berisiko di lingkungan produksi. Jika akun seorang dosen terhapus, seluruh data mata kuliah termasuk materi, tugas, dan nilai mahasiswa di dalamnya akan ikut hilang secara permanen. Oleh karena itu, penggunaan `restrictOnDelete()` lebih disarankan agar database menolak penghapusan dosen selama masih ada data mata kuliah yang terikat.