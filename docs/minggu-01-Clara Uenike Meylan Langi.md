# 1.3 Read

## 1. READ — Bedah instalasi
### 1) Fungsi `public/index.php`  
Sebagai pintu masuk utama (entry point) aplikasi Laravel yang menerima request dari pengguna. Kode ini juga berfungsi untuk menginisialisasi Laravel agar seluruh sistem aplikasi siap dijalankan. Selanjutnya, request tersebut diteruskan ke sistem Laravel untuk diproses dan menghasilkan response.
### 2) Identifikasi Bagian - Bagian `bootstrap/app.php`  
1) **Routing**
```
->withRouting(
    web: __DIR__.'/../routes/web.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```
2) **Middleware**
```
->withMiddleware(function (Middleware $middleware) {
    //
})
```
3) **Exception**
```
->withExceptions(function (Exceptions $exceptions) {
    //
})
```
### 3) Mengubah Teks pada Halaman Welcome `routes\web.php`   
- Sebelum diubah
<img src="image\clara-sebelum.png">
- Sesudah diubah
<img src="image\clara-sesudah.png">
---

## 2. Menjalankan `php artisan route:list`
Menjalankan `php artisan route:list.` Cocokkan keluarannya dengan isi routes/web.php.
- Isi `routes/web.php`
```
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
```
- Hasil run `php artisan route:list.`
<img src="image\clara-routelist.png">

---

Setelah menjalankan `php artisan route:list`, terdapat route `GET|HEAD /` yang berasal dari `routes/web.php` pada baris ke-5. Route tersebut sesuai dengan kode `Route::get('/', ...)`, yang berarti URL `/` menggunakan method GET dan berfungsi sebagai halaman utama aplikasi. Selain itu, terdapat beberapa route bawaan Laravel seperti `storage/{path}` dan `up`

---
