## Catatan 4.3

Nama : **Devina Dian Saputri**      
NIM : **10241022**

---
### Break

1. Hapus `@csrf` dari form, lalu kirim      
_Jawaban_ :         
    Yang diubah: file tampilan `resources/views/admin/admin.matkul.blade.php`, lalu jadikan komentar atau hapus baris kode `@csrf` yang ada di dalam form. Setelah itu, coba lakukan demo dengan menambahkan mata kuliah baru melalui halaman admin.        
    Hasil: Aplikasi akan langsung menampilkan halaman error `419 PAGE EXPIRED`. Munculnya pesan error ini membuktikan bahwa Laravel secara otomatis menolak permintaan yang dikirim tanpa adanya Token CSRF. Directive `@csrf` berfungsi sebagai kunci pengaman yang memastikan bahwa data yang masuk benar-benar dikirim secara sah melalui form aplikasi kita sendiri, bukan dari manipulasi luar atau situs asing.  

2. Ganti `$request->validated()` menjadi `$request->all()`, lalu kirim field liar lewat `curl`      
_Jawaban_ :         
    Yang diubah: file `bootstrap/app.php` lalu tambahkan pengecualian CSRF sementara untuk route `admin/mata-kuliah` agar bisa diuji via `curl`. Selanjutnya, ubah kode di dalam method `store` pada `AdminCourseController.php` dari yang awalnya `Course::create($request->validated());` menjadi `Course::create($request->all());`. Terakhir, jalankan perintah curl di terminal dengan menyelipkan field tambahan yang tidak ada di form, seperti `-d "is_admin=1" -d "role=superadmin"`.      
    `bootstrap/app.php`

    ``` php
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'admin/mata-kuliah', // Exclude sementara untuk testing curl
        ]);
    })
    ```

    `AdminCourseController.php`

    ``` php
    public function store(StoreCourseRequest $request)
    {
        Course::create($request->all()); // Diganti dari validated() ke all()
        return redirect()->...
    }
    ```
    Hasil: Data berhasil masuk dan tersimpan ke database. Hal ini terjadi karena penggunaan `$request->all()` membuat aplikasi mengambil seluruh data input secara mentah tanpa penyaringan. Oleh karena itu, aplikasi harus selalu menggunakan `$request->validated()` agar hanya field yang sudah lolos aturan validasi di Form Request saja yang boleh masuk.  

3. Hapus validasi `exists:users,id` pada `lecturer_id`, kirim `lecturer_id=99999`       
_Jawaban_ :         
    Yang diubah: file `app/Http/Requests/StoreCourseRequest.php`, lalu hapus aturan `exists:users,id` pada bagian validasi field `lecturer_id` sehingga hanya menyisakan validasi `integer`. Setelah itu, jalankan perintah `curl` di terminal untuk mengirimkan data baru dengan mengisi `lecturer_id=99999` (ID dosen yang tidak terdaftar di database).      
    `StoreCourseRequest.php`

    ``` php
    'lecturer_id' => ['required', 'integer'], // Menghapus 'exists:users,id'
    ```
    Hasil: Data tetap berhasil diproses dan masuk ke database. Penghapusan aturan `exists` ini terbukti menjebol integritas data karena membiarkan masuknya data yatim atau orphan data (data relasi yang pemilik aslinya tidak ada), sehingga aturan validasi `exists` wajib dipertahankan pada setiap field yang berfungsi sebagai Foreign Key.  

4. Hapus validasi `in:...` pada status, kirim `status=superadmin`       
_Jawaban_ :         
    Yang diubah: file `StoreCourseRequest.php`, ubah aturan validasi pada field status yang awalnya dibatasi dengan `Rule::in(['draft', 'active', 'archived'])` menjadi hanya `required`. Cara mengujinya, buka halaman tambah mata kuliah di browser, tekan `F12` untuk membuka DevTools, masuk ke tab Console, lalu ubah nilai input status secara paksa dengan perintah JavaScript `document.querySelector('[name=status]').value = "superadmin"`. Setelah itu, submit form seperti biasa dan cek hasilnya di database menggunakan `php artisan tinker` (`Course::latest()->first()->status`).       
    `StoreCourseRequest.php`

    ``` php
    'status' => ['required'], // Menghapus Rule::in(['draft', 'active', 'archived'])
    ```
    Perintah JavaScript di Console Browser (F12):

    ``` js
    document.querySelector('[name=status]').value = "superadmin";
    ```

    Hasil: Nilai status di database benar-benar berubah menjadi `superadmin`. Pengujian ini membuktikan bahwa validasi di sisi frontend atau tampilan sangat mudah dimanipulasi, sehingga aturan pilihan nilai yang sah wajib dipunci di sisi server.  

5. Hapus `->withQueryString()`, lakukan pencarian lalu klik halaman 2       
_Jawaban_ : 
    Yang diubah: file `AdminCourseController.php` pada method index, lalu hapus sambungan fungsi `->withQueryString()` pada bagian paginasi (biarkan `paginate(3)` tetap ada, atau ganti angkanya menjadi kecil supaya tombol halaman 2 cepat muncul jika data sedikit). Cara mengujinya, buka halaman `/admin/mata-kuliah?q=pemrograman`, lalu klik tautan halaman 2 di bagian bawah tabel.        
    `AdminCourseController.php`     

    ``` php
    $courses = Course::query()->paginate(3); // Menghapus sambungan ->withQueryString()
    ```
    Hasil: Perhatikan URL di address bar browser, parameter pencarian `q=pemrograman` mendadak hilang saat pindah halaman. Hal ini membuktikan pentingnya fungsi `withQueryString()`, yang bertugas memastikan agar parameter filter atau pencarian tetap terbawa saat pengguna berpindah ke halaman berikutnya.

6. Ganti `return redirect()` menjadi `return view()` pada store, lalu tekan F5 setelah simpan       
_Jawaban_ :         
    Yang diubah: di dalam method `store` pada `AdminCourseController.php`, ganti baris perintah yang tadinya menggunakan return `redirect()->action(...)` menjadi mengembalikan tampilan secara langsung menggunakan `return view('admin.admin-matkul', [...])`. Cara mengujinya, isi form tambah mata kuliah dengan kode unik (misalnya `TES-01`), lalu submit. Perhatikan URL di browser yang tidak berubah menjadi redirect, kemudian tekan tombol `F5` (Refresh) dan pilih konfirmasi kirim ulang data jika diminta.      
    `AdminCourseController.php`         

    ``` php
    return view('admin.admin-matkul', [
        'matkulList' => Course::with('lecturer')->paginate(3),
        'dosenList' => User::where('role', 'dosen')->get(['id', 'name']),
        'filters' => ['q' => null, 'status' => null, 'lecturer_id' => null]
    ]);
    ```
    Hasil: Cek daftar mata kuliah di database, data `TES-01` akan muncul tersimpan dua kali (ganda). Kejadian ini menunjukkan alasan mengapa pola PRG (Post-Redirect-Get) wajib diterapkan agar aplikasi tidak memproses data yang sama berulang kali saat halaman direfresh.  

7. Hapus `old(...)` dari semua input, lalu kirim form dengan satu kesalahan     
_Jawaban_ :         
    Yang diubah: file tampilan `resources/views/admin/admin.matkul.blade.php`, lalu hapus seluruh fungsi `{{ old('xxx') }}` pada atribut value di form input dan ganti menjadi string kosong. Cara mengujinya, isi form dengan data nama mata kuliah yang benar, tetapi bagian kode mata kuliah sengaja dikosongkan agar validasi gagal ketika disubmit.        
    `resources/views/admin/admin.matkul.blade.php`      
    ``` blade
    <!-- Contoh mengubah old() menjadi string kosong -->
    <input type="text" name="code" value="">
    ```
    Hasil: Ketika halaman kembali menampilkan pesan error validasi, seluruh kolom isian yang sebelumnya sudah diketik oleh pengguna mendadak kosong kembali. Pengujian ini memperlihatkan betapa pentingnya fungsi `old()` untuk mempertahankan input pengguna agar mereka tidak harus mengetik ulang seluruh formulir dari awal ketika terjadi kesalahan pengisian.  
