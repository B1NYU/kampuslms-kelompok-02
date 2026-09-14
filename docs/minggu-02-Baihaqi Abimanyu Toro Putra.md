### Route /tentang

1. Baris mana di `routes/web.php` yang menangkapnya?
Jawaban:
```php
Route::get('/tentang', function () {
    return view('anggota');
});
```

2. Kalau ditangani controller, berkas dan method mana?
Jawaban: Tidak ada berkas ataupun method yang menangani route `/tentang` dikarenakan route tersebut hanya perlu menggunakan data statis dan desain yang tidak akan pernah berubah

3. View mana yang dikembalikan? Di path apa persisnya?
Jawaban: View yang dikembalikan route tersebut adalah anggota.blade.php dan berada di path `resources/views/anggota.blade.php`

4. Layout apa yang membungkusnya?
Jawaban: Layout yang membungkus adalah layout satu satunya yang berada di components dan memiliki isi berupa navbar saja

5. Jalankan php artisan `route:list --path=tentang`. Cocok dengan analisis Anda?
Jawaban: Hasil yang diberikan berupa `GET|HEAD tentang ........ routes/web.php:10`, ini menjelaskan bahwa /tentang telah terdeteksi dan berada tepat di `web.php` pada baris 10
