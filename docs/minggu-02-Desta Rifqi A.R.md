# Read Minggu ke-2


1.Baris mana di routes/web.php yang menangkapnya?


_Jawaban:_
```php
Route::get('/tentang', function () {
    return view('anggota');
});
```

2.Kalau ditangani controller, berkas dan method mana?


_Jawaban:_ Route ini tidak ditangani oleh controller, melainkan ditangani secara langsung menggunakan Closure (anonymous function) bawaan pada file routes/web.php.


3.View mana yang dikembalikan? Di path apa persisnya?


_Jawaban:_ View yang dikembalikan yaitu anggota dan path nya: resources -> view -> anggota.blade.php


4.Layout apa yang membungkusnya?


_Jawaban:_ _Jawaban:_ Dari kode pada folder anggota.blade.php, tidak ada layout yang membungkusnya.File ini berdiri sendiri (standalone) karena struktur HTML-nya ditulis secara lengkap dari tag <!DOCTYPE html>, <head>, hingga <body> di dalam satu file.


5.Jalankan php artisan route:list --path=tentang. Cocok dengan analisis Anda?


_Jawaban:_ <img src="DestaW2RJ5.png">

Hasil tersebut menunjukkan bahwa routes/web.php:9 ditangani secara langsung oleh kode di file routes/web.php pada baris ke-9