1.Buka public/index.php. Baca dari atas ke bawah. Tulis dalam 3 kalimat apa yang dilakukan berkas ini.

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());

```

_Jawaban:_ Berkas ini bertindak sebagai gerbang utama yang menyambut setiap pengunjung dan mengukur waktu pemrosesan aplikasi. Berkas ini bertugas memeriksa mode perbaikan serta memuat seluruh library dan konfigurasi awal yang dibutuhkan oleh mesin Laravel. Setelah semuanya siap, berkas ini menangkap data permintaan pengunjung untuk diproses hingga menghasilkan tampilan web yang dikirim kembali ke layar.

2.Buka bootstrap/app.php. Identifikasi bagian mana yang mengurus route, mana yang mengurus middleware, mana yang mengurus exception.

_Jawaban:_ 

```php
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
```
Kode tersebut merupakan bagian yang mengurus Route. 
Blok kode `withRouting` untuk menentukan berkas rute mana saja yang dimuat oleh Laravel, `routes/web.php` untuk rute tampilan web, `routes/console.php` dan membuat endpoint bawaan `/up` untuk cek status.

```php
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
```
Kode tersebut merupakan bagian yang mengurus Middleware. Blok kode `withMiddleware`digunakan untuk mengatur penyaring (filter) lalu lintas HTTP. 


```php
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
```
Kode tersebut merupakan bagian yang mengurus Exception (Error Handling). Blok kode `withExceptions` mengatur bagaimana Laravel jika menangani error. Ada aturan khusus jika terjadi error dan asal permintaannya dari API (`api/*`) dengan format JSON, maka Laravel wajib mengembalikan pesan error dalam bentuk JSON, bukan tampilan HTML 500/404 biasa.

3.Buka routes/web.php. Temukan route yang menghasilkan halaman selamat datang. Ubah teksnya, muat ulang browser, pastikan berubah.

_Jawaban:_ 
<img src="JAWABAN NO3.png">

4.Jalankan php artisan route:list. Cocokkan keluarannya dengan isi routes/web.php.

_Jawaban:_ 

php artisan route:list
<img src="NO4.png">


routes/web.php

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
```

Hasil routes/web.php sesuai dengan hasil php artisan route:list. Baris pertama pada tabel terminal menunjukkan bahwa URL utama (/) ditangani oleh baris ke-5 di file routes/web.php.

