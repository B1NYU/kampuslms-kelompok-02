Nama : **Calvin Hidayat Winatajaya**      
NIM : **10241016**

---
### Read

1. Baris mana di `routes/web.php` yang menangkapnya?        
     _Jawaban_ :
    ``` php
    Route::get('/tentang', function () {
        return view('anggota');
    });
    ```
    Kode ini seperti instruksi otomatis. Saat ada orang yang mengetik/mengklik alamat `/tentang` di browser, Laravel akan langsung membaca baris ini dan menampilkan halaman `anggota`.

2. Kalau ditangani controller, berkas dan method mana?          
    _Jawaban_ : Route /tentang di kode tidak ditangani oleh Controller manapun (termasuk `Controller.php` maupun `CourseController.php`).
    pada kode 
    ``` php
    Route::get('/tentang', function () {
        return view('anggota');
    });
    ```
    Fungsi `function () { ... }` dinamakan Closure (fungsi langsung/tanpa nama). Jadi, Laravel langsung memproses tampilannya di tempat tanpa perlu memanggil berkas Controller.

3. View mana yang dikembalikan? Di path apa persisnya?        
     _Jawaban_ : view yang dikembalikan adalah `anggota` (`anggota.blade.php`) dan untuk path nya `resources/views/anggota.blade.php`. Di dalam route `/tentang`, ada perintah `return view('anggota');`. Kata 'anggota' di dalam tanda kurung tersebut memanggil file Blade (tampilan HTML) yang disimpan di folder bawaan Laravel untuk tampilan, yaitu `resources/views/`

4. Layout apa yang membungkusnya?           
     _Jawaban_ : File `anggota.blade.php` tidak menggunakan layout pembungkus (standalone view / berdiri sendiri).

5. Jalankan php artisan route:list --path=tentang. Cocok dengan analisis Anda?
     _Jawaban_ : Output `GET|HEAD tentang ........ routes/web.php:10` menunjukkan bahwa route `/tentang` berhasil terdeteksi dan didefinisikan pada file `routes/web.php` baris ke-10.