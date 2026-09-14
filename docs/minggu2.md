# 2.3 Read & Break

## 1. READ - Bedah instalasi
Ambil route /tentang yang Anda buat minggu lalu. Tanpa AI, tulis di catatan Anda:
### 1) Baris mana di routes/web.php yang menangkapnya?  
Baris yang berisi code berikut
```
Route::get('/tentang', function () {
    return view('tentang');
})->name('tentang');
```
### 2) Kalau ditangani controller, berkas dan method mana? 
Tidak ada berkas controller dan method; route ini ditangani langsung menggunakan Closure (fungsi anonim) di file routes/web.php.
- Route `/tentang` tidak ditangani oleh controller.
- Route ini ditangani langsung oleh sebuah Closure / Anonymous Function (fungsi anonim tanpa nama) yang ditulis langsung di dalam 
routes/web.php.
- `route /mata-kuliah` barulah ditangani oleh controller, yaitu CourseController pada method index().
### 3) View mana yang dikembalikan? Di path apa persisnya?
View yang dikembalikan bernama tentang. Pathnya yaitu sebagai berikut :
```
resources/views/tentang.blade.php
``` 

### 4) View mana yang dikembalikan? Di path apa persisnya?
Di dalam file tentang.blade.php, struktur kodenya adalah dokumen HTML lengkap yang berdiri sendiri:
```
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tentang Kelompok</title>
</head>
<body>
    <h1>Kelompok 2</h1>
    ...
</body>
</html>
``` 
Halaman ini dibuat sebagai file HTML/Blade mandiri (standalone), belum menggunakan layout template seperti komponen `<x-layout>` yang ada pada `resources/views/tentang.blade.php
`

### 5) Jalankan php artisan `route:list --path=tentang`. Cocok dengan analisis Anda?
Hasil setelah telah menjalankan `php artisan route:list --path=tentang
`  
//nanti ada gambar disini  
Outputnya menunjukkan :
- Method & URI: GET|HEAD tentang
- Nama Route: tentang
- Action / Handler: `routes/web.php:14`  

Ini menunjukkan bahwa penangan route berada di baris 14 file `routes/web.php` dan berupa closure, bukan controller.