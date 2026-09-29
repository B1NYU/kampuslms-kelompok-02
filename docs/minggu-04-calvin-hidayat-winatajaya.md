

**Nama:** Calvin Hidayat Winatajaya  
**NIM:** 10241016
---

#### 1. Pengujian Mekanisme Keamanan CSRF
* **Tujuan Eksperimen:** Menguji ketahanan aplikasi terhadap serangan *Cross-Site Request Forgery* dengan menonaktifkan proteksi CSRF token.
* **Prosedur Modifikasi:**
  1. Akses file `resources/views/admin/admin.matkul.blade.php`.
  2. Nonaktifkan atau hapus baris `@csrf` pada elemen formulir.
  3. Lakukan pengiriman data mata kuliah baru dari antarmuka Web.
* **Hasil & Temuan:**
  Sistem secara otomatis memblokir permintaan dan menampilkan halaman error `419 PAGE EXPIRED`. 
* **Analisis & Kesimpulan:**
  Laravel secara *default* mewajibkan verifikasi token pada setiap permintaan POST/PUT/DELETE. Penghapusan directive `@csrf` membuktikan bahwa framework secara aktif melindungi endpoint dari eksekusi instruksi liar di luar sesi aplikasi yang sah.

---

#### 2. Bahaya Penggunaan Mass Assignment Tanpa Filter (`$request->all()`)
* **Tujuan Eksperimen:** Mengetahui dampak keamanan ketika memproses data input langsung ke model tanpa melewati tahap validasi.
* **Prosedur Modifikasi:**
  1. Daftarkan pengecualian rute pada `bootstrap/app.php` untuk membebaskan pengecekan CSRF pada endpoint target sementara waktu:
     ```php
     ->withMiddleware(function (Middleware $middleware) {
         $middleware->validateCsrfTokens(except: [
             'admin/mata-kuliah',
         ]);
     })
     ```
  2. Buka `AdminCourseController.php` dan ubah skema ekstraksi data pada method `store`:
     ```php
     public function store(StoreCourseRequest $request)
     {
         // Mengganti penyaringan validated() dengan penyerapan total all()
         Course::create($request->all()); 
         return redirect()->back();
     }
     ```
  3. Eksekusi pengiriman data liar via terminal menggunakan cURL:
     ```bash
     curl -X POST http://localhost:8000/admin/mata-kuliah -d "name=Algoritma" -d "code=IF101" -d "is_admin=1" -d "role=superadmin"
     ```
* **Hasil & Temuan:**
  Data berhasil diinjeksikan dan tersimpan ke dalam database beserta atribut tambahan (`is_admin`, `role`) yang sebenarnya tidak disediakan di formulir.
* **Analisis & Kesimpulan:**
  Memakai `$request->all()` berisiko tinggi memicu celah *Mass Assignment Vulnerability*. Penggunaan `$request->validated()` mutlak diperlukan agar hanya kolom yang telah terdaftar di Form Request saja yang boleh masuk ke basis data.

---

#### 3. Integritas Relasi Foreign Key Tanpa Rule `exists`
* **Tujuan Eksperimen:** Mengamati efek yang terjadi jika validasi ketersediaan data relasi (*referential integrity*) diabaikan.
* **Prosedur Modifikasi:**
  1. Buka `app/Http/Requests/StoreCourseRequest.php`.
  2. Hapus pengecekan keabsahan ID dosen pada atribut `lecturer_id`:
     ```php
     // Menghapus 'exists:users,id'
     'lecturer_id' => ['required', 'integer'], 
     ```
  3. Tembak endpoint menggunakan cURL dengan menyertakan ID dosen yang tidak pernah ada di database:
     ```bash
     curl -X POST http://localhost:8000/admin/mata-kuliah -d "lecturer_id=99999"
     ```
* **Hasil & Temuan:**
  Aplikasi tetap menerima data dan memproses pembuatan rekam baru tanpa adanya komplain dari sistem.
* **Analisis & Kesimpulan:**
  Tanpa validasi `exists`, database berpotensi tercemar oleh *orphan data* (data yatim piatu yang merujuk pada entitas kosong). Aturan `exists:table,column` wajib ada pada setiap bidang berunsur Foreign Key.

---

#### 4. Kerapuhan Validasi Input Hanya di Sisi Klien (Frontend)
* **Tujuan Eksperimen:** Menguji seberapa mudah aturan input dibobol jika validasi nilai terbatas (*enumeration*) di tingkat server dihilangkan.
* **Prosedur Modifikasi:**
  1. Hapus batasan nilai pada `StoreCourseRequest.php`:
     ```php
     // Menghapus aturan Rule::in(['draft', 'active', 'archived'])
     'status' => ['required'], 
     ```
  2. Buka formulir input di browser, panggil Developer Tools (`F12`) -> Console, lalu eksekusi manipulasi nilai atribut lewat skrip JavaScript:
     ```javascript
     document.querySelector('[name=status]').value = "superadmin";
     ```
  3. Kirimkan formulir dan periksa baris terakhir di database melalui Artisan Tinker (`Course::latest()->first()->status`).
* **Hasil & Temuan:**
  Atribut `status` di database berubah menjadi string `"superadmin"`.
* **Analisis & Kesimpulan:**
  Validasi HTML/JavaScript di sisi browser sangat mudah dimanipulasi oleh pengguna. Proteksi nilai pilihan (*allowed values*) wajib dikunci rapat di backend melalui validasi `Rule::in(...)`.

---

#### 5. Kehilangan Parameter Filter pada Paginasi
* **Tujuan Eksperimen:** Mengetahui dampak tidak dipanggilnya pemelihara state query string (`withQueryString()`) pada fitur pagination.
* **Prosedur Modifikasi:**
  1. Buka `AdminCourseController.php` dan sesuaikan method `index`:
     ```php
     // Menghilangkan fungsi ->withQueryString()
     $courses = Course::query()->paginate(3); 
     ```
  2. Jalankan pencarian di browser dengan URL: `/admin/mata-kuliah?q=pemrograman`.
  3. Klik tombol halaman 2 pada bagian navigasi tabel.
* **Hasil & Temuan:**
  Navigasi berhasil berpindah halaman, namun URL berubah menjadi `/admin/mata-kuliah?page=2` dan kata kunci pencarian `q=pemrograman` hilang begitu saja.
* **Analisis & Kesimpulan:**
  Fungsi `->withQueryString()` berperan penting untuk mengikat (*retain*) seluruh parameter pencarian/filter agar tidak musnah saat pengguna berpindah halaman data.

---

#### 6. Risiko Resubmission Form Tanpa Pola PRG (Post-Redirect-Get)
* **Tujuan Eksperimen:** Membuktikan bahaya duplikasi data jika controller langsung mengembalikan *View* alih-alih melakukan *Redirect* setelah aksi POST.
* **Prosedur Modifikasi:**
  1. Ubah baris penutup pada method `store` di `AdminCourseController.php`:
     ```php
     return view('admin.admin-matkul', [
         'matkulList' => Course::with('lecturer')->paginate(3),
         'dosenList' => User::where('role', 'dosen')->get(['id', 'name']),
         'filters' => ['q' => null, 'status' => null, 'lecturer_id' => null]
     ]);
     ```
  2. Input data baru (misal kode `TES-01`) lalu simpan.
  3. Setelah halaman tampil, tekan tombol **F5** (Refresh) di browser dan setujui dialog *Confirm Form Resubmission*.
* **Hasil & Temuan:**
  Mata kuliah dengan kode `TES-01` terinput ganda di tabel database.
* **Analisis & Kesimpulan:**
  Pola PRG (Post-Redirect-Get) menggunakan `return redirect()` adalah standar wajib web development untuk mencegah pengguna melakukan pengiriman ulang data (*double submission*) saat merefresh browser.

---

#### 7. Peran Helper `old()` dalam Menjaga Sesi Input
* **Tujuan Eksperimen:** Mengamati dampak hilangnya fungsi pembaca nilai lama (`old()`) ketika formulir mengalami kegagalan validasi.
* **Prosedur Modifikasi:**
  1. Buka berkas view `resources/views/admin/admin.matkul.blade.php`.
  2. Kosongkan nilai atribut `value` pada input field dengan menghapus helper `old()`:
     ```html
     <!-- Menghapus {{ old('code') }} -->
     <input type="text" name="code" value="">
     ```
  3. Isikan beberapa field dengan benar, namun sengaja salahkan salah satu kolom agar memicu error validasi, lalu klik simpan.
* **Hasil & Temuan:**
  Halaman kembali dengan pesan error, tetapi seluruh isian formulir yang sebelumnya diketik oleh pengguna mendadak terhapus bersih.
* **Analisis & Kesimpulan:**
  Fungsi `old('field_name')` sangat penting dari sudut pandang *User Experience* (UX) untuk menjaga agar data yang sudah diisi tidak hilang saat sistem mengembalikan respons kesalahan validasi.