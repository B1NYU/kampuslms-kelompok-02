# Catatan 4.3 — Eksperimen Break & Analisis Keamanan

**Nama:** Clara Uenike Meylan Langi  
**NIM:** 10241018  

---

### Eksperimen & Analisis Kasus (Break)

---

#### 1. Hapus `@csrf` dari form, lalu kirim  
* **Langkah Modifikasi & Pengujian:**
  Pada file view [`resources/views/admin/admin.matkul.blade.php`](file:///d:/Proweb/kampuslms-kelompok-02/resources/views/admin/admin.matkul.blade.php), nonaktifkan atau hapus pemanggilan direktif `@csrf` dari tag `<form>`. Selanjutnya, lakukan pengujian fungsional dengan mengisi formulir tambah mata kuliah baru dan klik tombol simpan melalui antarmuka web admin.

* **Hasil Pengamatan:**
  Aplikasi seketika menolak request dan memunculkan respons HTTP status **`419 Page Expired`**.

* **Analisis Teknis:**
  Laravel secara *default* mengaktifkan middleware verifikasi token CSRF (*Cross-Site Request Forgery*) pada seluruh rute web berbasis POST/PUT/DELETE. Directive `@csrf` bertugas menginjeksi token rahasia terselubung ke dalam form. Ketika token ini ditiadakan, middleware mendeteksi adanya potensi serangan pemalsuan permintaan antar-situs dan langsung memutus siklus request demi memastikan integritas session serta menjamin bahwa data hanya dapat dikirim dari antarmuka aplikasi yang sah.

---

#### 2. `$request->validated()` menjadi `$request->all()`, lalu kirim field liar lewat `curl`
* **Langkah Modifikasi & Pengujian:**
  Buka file `bootstrap/app.php`, kemudian daftarkan pengecualian sementara pada middleware token CSRF untuk endpoint pengujian:

  ```php
  ->withMiddleware(function (Middleware $middleware) {
      $middleware->validateCsrfTokens(except: [
          'admin/mata-kuliah', // Bypass sementara untuk uji penetrasi curl
      ]);
  })
  ```

  Selanjutnya, modifikasi method `store` di controller agar menampung seluruh payload tanpa filter Form Request:

  ```php
  // AdminCourseController.php
  public function store(StoreCourseRequest $request)
  {
      Course::create($request->all()); // Diganti dari validated() menjadi all()
      return redirect()->route('admin.courses.index');
  }
  ```

  Jalankan simulasi request melalui terminal dengan menyuntikkan parameter tak terdaftar (misalnya hak akses/privilese):
  ```bash
  curl -X POST http://127.0.0.1:8000/admin/mata-kuliah \
    -d "code=IF999" -d "name=Keamanan Siber" -d "sks=3" \
    -d "is_admin=1" -d "role=superadmin"
  ```

* **Hasil Pengamatan:**
  Permintaan lolos dan data berhasil tercatat ke dalam basis data beserta kolom tambahan jika kolom tersebut terdapat pada struktur tabel tanpa pembatasan ketat.

* **Analisis Teknis:**
  Fungsi `$request->all()` mengambil semua entri mentah yang dikirim oleh klien tanpa melewati proses penyaringan aturan validasi. Hal ini membuka celah keamanan serius berupa **Mass Assignment Vulnerability**, di mana pihak luar dapat memanipulasi kolom sensitif dalam basis data. Sebaliknya, penggunaan `$request->validated()` memberikan jaminan keamanan lapis pertama (*whitelisting*) karena hanya atribut yang terdaftar resmi dan lolos uji aturan Form Request yang berhak diproses oleh model.

---

#### 3. Hapus validasi `exists:users,id` pada `lecturer_id`, kirim `lecturer_id=99999` 
* **Langkah Modifikasi & Pengujian:**
  Buka kelas Form Request [`app/Http/Requests/StoreCourseRequest.php`](file:///d:/Proweb/kampuslms-kelompok-02/app/Http/Requests/StoreCourseRequest.php), lalu hilangkan aturan integritas `exists:users,id` sehingga atribut `lecturer_id` hanya divalidasi sebagai tipe bilangan bulat:

  ```php
  // StoreCourseRequest.php
  'lecturer_id' => ['required', 'integer'], // Aturan 'exists:users,id' dihilangkan
  ```

  Kirimkan payload pengujian menggunakan HTTP client atau `curl` dengan nilai foreign key fiktif yang tidak eksis di tabel pengguna:
  ```bash
  curl -X POST http://127.0.0.1:8000/admin/mata-kuliah \
    -d "code=MK001" -d "name=Struktur Data" -d "sks=3" -d "lecturer_id=99999"
  ```

* **Hasil Pengamatan:**
  Permintaan pembuatan mata kuliah baru tetap berhasil tersimpan di tabel `courses` dengan nilai `lecturer_id = 99999`.

* **Analisis Teknis:**
  Hilangnya validasi relasional `exists` mengakibatkan terciptanya **Orphan Record** (data yatim piatu), yaitu baris data anak yang menunjuk ke entitas induk yang tidak pernah ada. Hal ini membahayakan integritas relasional basis data dan dapat memicu runtime error (*null pointer exception* / *trying to get property of null*) ketika view atau controller mencoba memuat relasi Eloquent (`$course->lecturer->name`). Oleh karena itu, verifikasi `exists:table,column` mutlak diterapkan pada seluruh atribut kunci asing (*Foreign Key*).

---

#### 4. Hapus validasi `in:...` pada status, kirim `status=superadmin`  
* **Langkah Modifikasi & Pengujian:**
  Pada file [`app/Http/Requests/StoreCourseRequest.php`](file:///d:/Proweb/kampuslms-kelompok-02/app/Http/Requests/StoreCourseRequest.php), hapus pembatasan enum `Rule::in(['draft', 'active', 'archived'])` dan hanya sisakan aturan `required`:

  ```php
  // StoreCourseRequest.php
  'status' => ['required'], // Menghapus Rule::in(['draft', 'active', 'archived'])
  ```

  Buka formulir tambah mata kuliah di browser, lalu buka DevTools (`F12` > Console). Ubah nilai elemen input status secara paksa melalui skrip konsol:
  ```javascript
  document.querySelector('[name=status]').value = "superadmin";
  ```
  Kirim formulir, kemudian periksa record terbaru pada database melalui terminal `php artisan tinker`:
  ```bash
  php artisan tinker --execute="echo App\Models\Course::latest()->first()->status;"
  ```

* **Hasil Pengamatan:**
  Nilai status pada baris mata kuliah baru tersimpan dengan string `"superadmin"`.

* **Analisis Teknis:**
  Pengujian ini memperjelas prinsip dasar rekayasa perangkat lunak web: **jangan pernah mempercayai input dari sisi klien (*Never Trust Client-Side Input*)**. Elemen HTML seperti dropdown, radio button, atau atribut validasi browser (`required`, `pattern`) sangat mudah diubah atau di-bypass menggunakan DevTools maupun intercepting proxy. Validasi enumerasi nilai legal wajib dikunci secara absolut di sisi backend server untuk mencegah polusi data (*data corruption*) dan inkonsistensi logika aplikasi.

---

#### 5. Hapus `->withQueryString()`, lakukan pencarian lalu klik halaman 2
* **Langkah Modifikasi & Pengujian:**
  Pada controller pengelola mata kuliah (`AdminCourseController.php`), modifikasi baris pemanggilan paginasi pada query index dengan melepas fungsi `withQueryString()`:

  ```php
  // AdminCourseController.php
  $courses = Course::query()
      ->filter(request(['q', 'status', 'lecturer_id']))
      ->paginate(3); // Pemanggilan ->withQueryString() ditiadakan
  ```

  Lakukan pengujian dengan mengetik kata kunci pada bilah pencarian (misal: `/admin/mata-kuliah?q=pemrograman`), lalu klik tombol halaman 2 (page 2) pada navigasi paginasi di bawah tabel.

* **Hasil Pengamatan:**
  Saat berpindah ke halaman kedua, parameter filter pada URL mendadak terpotong menjadi sekadar `?page=2`, sehingga parameter `q=pemrograman` terhapus dan tabel menampilkan seluruh data umum alih-alih data yang sedang difilter.

* **Analisis Teknis:**
  Secara baku, paginasi bawaan Laravel hanya menghasilkan link navigasi dengan parameter `page`. Pemanggilan method `withQueryString()` memegang peranan krusial dalam mempertahankan konteks *state* URL, karena ia secara otomatis membaca, mengemas, dan menempelkan kembali seluruh parameter filter atau pencarian aktif ke tautan setiap nomor halaman. Tanpa method ini, pengalaman pengguna (*User Experience*) akan rusak ketika mengelola dataset berukuran besar.

---

#### 6. Ganti `return redirect()` menjadi `return view()` pada store, lalu tekan F5 setelah simpan 
* **Langkah Modifikasi & Pengujian:**
  Ubah akhir baris eksekusi method `store` pada controller mata kuliah dari pola pengalihan rute (*redirect*) menjadi perenderan tampilan langsung:

  ```php
  // AdminCourseController.php
  public function store(StoreCourseRequest $request)
  {
      Course::create($request->validated());
      
      // Mengembalikan tampilan langsung tanpa redirect:
      return view('admin.admin-matkul', [
          'courses' => Course::with('lecturer')->paginate(5),
          'lecturers' => User::where('role', 'dosen')->get(['id', 'name']),
          'filters' => request()->all()
      ]);
  }
  ```

  Tambahkan data mata kuliah baru dengan kode spesifik (contoh: `TES-PRG`), klik tombol simpan, lalu tekan tombol **F5 (Refresh Halaman)** pada web browser dan setujui dialog *Form Resubmission*.

* **Hasil Pengamatan:**
  Mata kuliah dengan kode `TES-PRG` masuk dan tersimpan sebanyak dua kali (terduplikasi) di dalam database.

* **Analisis Teknis:**
  Insiden ini membuktikan urgensi penerapan standar arsitektur web **Post-Redirect-Get (PRG)**. Jika sebuah request `POST` langsung direspons dengan tampilan HTML (`view`), status HTTP di browser masih berada dalam konteks transaksi `POST` terakhir. Akibatnya, operasi penyegaran (*refresh*) akan mengirimkan ulang seluruh payload yang sama ke server. Dengan menerapkan `return redirect()`, request `POST` segera diakhiri dan klien diarahkan melalui request `GET` yang bersifat aman (*idempotent*), mencegah bahaya duplikasi data ganda akibat ketidaksengajaan pengguna.

---

#### 7. Hapus `old(...)` dari semua input, lalu kirim form dengan satu kesalahan
* **Langkah Modifikasi & Pengujian:**
  Pada template tampilan [`resources/views/admin/admin.matkul.blade.php`](file:///d:/Proweb/kampuslms-kelompok-02/resources/views/admin/admin.matkul.blade.php), bersihkan pemanggilan fungsi `old('nama_field')` pada seluruh atribut `value` input, textarea, maupun select:

  ```blade
  <!-- Contoh sebelum: -->
  <input type="text" name="name" value="{{ old('name') }}">

  <!-- Setelah diubah (helper old dihilangkan): -->
  <input type="text" name="name" value="">
  ```

  Lakukan pengujian dengan mengisi form secara lengkap (misalnya nama mata kuliah, SKS, dosen), tetapi sengaja kosongkan kolom kode mata kuliah agar validasi server memicu pesan kesalahan (*validation error*).

* **Hasil Pengamatan:**
  Saat halaman dimuat ulang dengan menampilkan pesan error "Kode mata kuliah wajib diisi", seluruh kolom isian lainnya yang sebelumnya telah diketik rapi oleh pengguna langsung hilang dan kembali menjadi formulir kosong.

* **Analisis Teknis:**
  Ketika validasi backend gagal, Laravel secara otomatis menyimpan data masukan sementara ke dalam *flash session*. Helper `old('field_name')` berfungsi menarik kembali nilai masukan dari session tersebut agar tetap terisi di elemen antarmuka. Peniadaan fungsi ini sangat merugikan aspek *usability* dan kenyamanan pengguna karena memaksa mereka mengulang pengetikan data dari awal saat terjadi kesalahan minor pada salah satu kolom formulir.
