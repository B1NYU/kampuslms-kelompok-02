# Break Week 4

**Nama:** Desta Rifqi Aunur Rahman 
**NIM:** 10241020

---

### 1. Pengujian Efektivitas Proteksi CSRF

* **Tujuan Pengujian:** Menilai sejauh mana kerentanan aplikasi terhadap serangan *Cross-Site Request Forgery* ketika fitur proteksi bawaan dinonaktifkan.
* **Langkah-Langkah:**
  1. Buka berkas tampilan `resources/views/admin/admin.matkul.blade.php`.
  2. Hapus atau beri komentar pada direktif `@csrf` di dalam elemen formulir.
  3. Kirimkan data mata kuliah baru melalui antarmuka web.
* **Hasil Observasi:**  
  Aplikasi menolak proses pengiriman data dan langsung menampilkan kode respons HTTP `419 PAGE EXPIRED`.
* **Analisis & Kesimpulan:**  
  Laravel secara otomatis menerapkan pemeriksaan keamanan token untuk setiap metode pengiriman data bernilai destruktif/perubahan (`POST`, `PUT`, `DELETE`). Penolakan sistem ini membuktikan bahwa framework secara mandiri mencegah eksekusi permintaan yang tidak berasal dari sesi pengguna terautentikasi.

---

### 2. Bahaya Fitur Mass Assignment Tanpa Filter (`$request->all()`)

* **Tujuan Pengujian:** Menganalisis risiko keamanan akibat memproses input pengguna secara mentah langsung ke layer model tanpa proses penyaringan terlebih dahulu.
* **Langkah-Langkah:**
  1. Kecualikan pemeriksaan CSRF pada jalur endpoint terkait di `bootstrap/app.php`:
     ```php
     ->withMiddleware(function (Middleware $middleware) {
         $middleware->validateCsrfTokens(except: [
             'admin/mata-kuliah',
         ]);
     })
     ```
  2. Edit berkas `AdminCourseController.php` dan ubah implementasi pada method `store`:
     ```php
     public function store(StoreCourseRequest $request)
     {
         // Mengganti penyaringan validated() dengan penyerapan seluruh data input
         Course::create($request->all()); 
         return redirect()->back();
     }
     ```
  3. Kirimkan permintaan palsu bermuatan atribut tambahan menggunakan cURL melalui terminal:
     ```bash
     curl -X POST http://localhost:8000/admin/mata-kuliah -d "name=Algoritma" -d "code=IF101" -d "is_admin=1" -d "role=superadmin"
     ```
* **Hasil Observasi:**  
  Data berhasil tersimpan ke dalam basis data lengkap beserta atribut sensitif (`is_admin` dan `role`) yang sebenarnya tidak disediakan pada formulir asli.
* **Analisis & Kesimpulan:**  
  Penggunaan `$request->all()` secara ceroboh membuka celah *Mass Assignment Vulnerability*. Pengembang wajib memanfaatkan `$request->validated()` untuk memastikan hanya atribut yang terdaftar pada aturan validasi yang dapat dimuat ke dalam basis data.

---

### 3. Dampak Penghilangan Validasi Relasi Foreign Key (`exists`)

* **Tujuan Pengujian:** Mengamati konsekuensi pada integritas data jika ketersediaan entitas relasi (*referential integrity*) tidak diverifikasi terlebih dahulu.
* **Langkah-Langkah:**
  1. Buka berkas `app/Http/Requests/StoreCourseRequest.php`.
  2. Tanggalkan aturan pemeriksaan keberadaan data dosen pada bidang `lecturer_id`:
     ```php
     // Menghapus aturan 'exists:users,id'
     'lecturer_id' => ['required', 'integer'], 
     ```
  3. Jalankan perintah cURL dengan memasukkan nilai ID dosen yang tidak valid atau tidak tercatat di database:
     ```bash
     curl -X POST http://localhost:8000/admin/mata-kuliah -d "lecturer_id=99999"
     ```
* **Hasil Observasi:**  
  Sistem tetap memproses permintaan dan berhasil menambahkan entitas baru tanpa memunculkan peringatan kesalahan.
* **Analisis & Kesimpulan:**  
  Abaikan aturan `exists` berpotensi tinggi memicu kemunculan *orphan data* (data tanpa induk relasi yang jelas). Aturan validasi `exists:table,column` wajib diterapkan pada setiap input bernilai Foreign Key.

---

### 4. Kerentanan Validasi Input yang Hanya Mengandalkan Sisi Klien

* **Tujuan Pengujian:** Menguji tingkat keandalan sistem jika batasan nilai terdaftar (*enumeration*) di sisi backend dihilangkan dan hanya bergantung pada batas tampilan antarmuka.
* **Langkah-Langkah:**
  1. Hapus batasan opsi nilai pada berkas `StoreCourseRequest.php`:
     ```php
     // Menghapus pengecekan Rule::in(['draft', 'active', 'archived'])
     'status' => ['required'], 
     ```
  2. Buka formulir pada halaman web, aktifkan Developer Tools (`F12`) -> tab Console, lalu manipulasi nilai elemen via perintah JavaScript:
     ```javascript
     document.querySelector('[name=status]').value = "superadmin";
     ```
  3. Kirimkan formulir dan periksa rekam data terbaru melalui fitur Artisan Tinker (`Course::latest()->first()->status`).
* **Hasil Observasi:**  
  Nilai dari kolom `status` di dalam tabel database berubah menjadi teks yang disisipkan, yaitu `"superadmin"`.
* **Analisis & Kesimpulan:**  
  Validasi berbasis HTML maupun JavaScript pada browser pengguna sangat rentan dimodifikasi. Proteksi terhadap keabsahan nilai (*allowed values*) harus diperketat di sisi server dengan memanfaatkan validasi backend seperti `Rule::in(...)`.

---

### 5. Hilangnya Query String pada Fitur Paginasi

* **Tujuan Pengujian:** Mengidentifikasi efek tidak digunakannya pencatat parameter URL (`withQueryString()`) saat melakukan navigasi halaman data.
* **Langkah-Langkah:**
  1. Buka berkas `AdminCourseController.php`, kemudian modifikasi logika pada method `index`:
     ```php
     // Menghilangkan instruksi pencatatan query string ->withQueryString()
     $courses = Course::query()->paginate(3); 
     ```
  2. Lakukan pencarian data pada browser melalui alamat URL: `/admin/mata-kuliah?q=pemrograman`.
  3. Tekan tombol navigasi untuk berpindah ke halaman 2.
* **Hasil Observasi:**  
  Sistem berhasil berpindah ke halaman berikutnya, namun alamat URL berubah menjadi `/admin/mata-kuliah?page=2` sehingga kata kunci pencarian `q=pemrograman` menjadi hilang.
* **Analisis & Kesimpulan:**  
  Fungsi `->withQueryString()` memegang peranan krusial untuk mempertahankan (*retain*) seluruh parameter kueri/filter agar filter data tidak terikat kembali ke kondisi awal saat pengguna mengakses halaman lain.

---

### 6. Potensi Duplikasi Data Akibat Abaikan Pola PRG (Post-Redirect-Get)

* **Tujuan Pengujian:** Menganalisis risiko pengiriman data ganda jika method *controller* mengembalikan nilai *View* secara langsung alih-alih melakukan *Redirect* pasca-proses eksekusi formulir POST.
* **Langkah-Langkah:**
  1. Ubah bagian akhir respons pada method `store` di `AdminCourseController.php`:
     ```php
     return view('admin.admin-matkul', [
         'matkulList' => Course::with('lecturer')->paginate(3),
         'dosenList' => User::where('role', 'dosen')->get(['id', 'name']),
         'filters' => ['q' => null, 'status' => null, 'lecturer_id' => null]
     ]);
     ```
  2. Masukkan data baru (contoh dengan kode `TES-01`), lalu tekan tombol simpan.
  3. Setelah halaman selesai dimuat, muat ulang halaman dengan menekan tombol **F5** di browser lalu konfirmasi jendela *Confirm Form Resubmission*.
* **Hasil Observasi:**  
  Mata kuliah bertajuk `TES-01` terdaftar secara ganda pada tabel basis data.
* **Analisis & Kesimpulan:**  
  Penerapan arsitektur PRG (Post-Redirect-Get) menggunakan `return redirect()` merupakan standar krusial dalam pengembangan aplikasi web guna mengantisipasi aksi pengiriman ulang data secara tidak sengaja oleh pengguna saat menyegarkan (*refresh*) halaman.

---

### 7. Peran Helper `old()` dalam Menjaga Kenyamanan Pengguna (UX)

* **Tujuan Pengujian:** Mengetahui dampak hilangnya retensi input lama saat sistem mengembalikan respons kegagalan validasi.
* **Langkah-Langkah:**
  1. Buka berkas tampilan `resources/views/admin/admin.matkul.blade.php`.
  2. Hapus fungsi pemanggil `old()` pada bagian atribut `value` di elemen input:
     ```html
     <!-- Menghapus sintaks {{ old('code') }} -->
     <input type="text" name="code" value="">
     ```
  3. Isikan beberapa bidang formulir, lalu sengaja kosongkan/salahkan salah satu kolom agar memicu galat validasi, kemudian tekan simpan.
* **Hasil Observasi:**  
  Aplikasi kembali menampilkan formulir beserta pesan kesalahan, tetapi seluruh teks yang sebelumnya telah diisi oleh pengguna terhapus total.
* **Analisis & Kesimpulan:**  
  Penggunaan helper `old('field_name')` sangat vital dari segi Pengalaman Pengguna (*User Experience*). Fitur ini memastikan input yang pernah diketik pengguna tetap tersimpan sementara ketika sistem mendeteksi adanya kegagalan validasi.