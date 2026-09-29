**Nama: Baihaqi Abimanyu Toro Putra**   
**NIM: 10241014**

1. Hapus `@csrf` dari form, lalu kirim
*   **Langkah Modifikasi & Pengujian:**  
    Buka `resources/views/admin/admin.matkul.blade.php`, lalu hapus atau jadikan komentar directive `@csrf` pada elemen `<form>`. Coba lakukan submit data mata kuliah baru dari antarmuka admin.
*   **Hasil Observasi & Analisis:**  
    Sistem merespons dengan pesan kesalahan `419 PAGE EXPIRED`. Ini mengonfirmasi bahwa Laravel secara aktif memblokir *request* yang tidak menyertakan Token CSRF. Directive `@csrf` berfungsi sebagai mekanisme validasi keamanan untuk memastikan pengiriman data berasal dari form internal aplikasi, bukan via serangan *Cross-Site Request Forgery*.

2. Ganti `$request->validated()` menjadi `$request->all()`, lalu kirim field liar lewat `curl`
*   **Langkah Modifikasi & Pengujian:**  
    Tambahkan pengecualian CSRF sementara pada route `admin/mata-kuliah` di file `bootstrap/app.php`. Pada `AdminCourseController.php`, sesuaikan method `store` dari `Course::create($request->validated());` menjadi `Course::create($request->all());`. Kirimkan *payload* melalui terminal menggunakan `curl` dengan menyisipkan atribut tambahan di luar kriteria (contoh: `-d "is_admin=1" -d "role=superadmin"`).
*   **Hasil Observasi & Analisis:**  
    Atribut tambahan berhasil tersimpan ke dalam basis data. Penggunaan `$request->all()` berisiko tinggi karena meloloskan seluruh input mentah tanpa proses penyaringan (*Mass Assignment*). Penerapan `$request->validated()` mutlak diperlukan agar hanya kolom yang secara eksplisit disetujui oleh Form Request yang dapat diproses.

3. Hapus validasi `exists:users,id` pada `lecturer_id`, kirim `lecturer_id=99999`
*   **Langkah Modifikasi & Pengujian:**  
    Buka `app/Http/Requests/StoreCourseRequest.php` dan hapus aturan `exists:users,id` pada field `lecturer_id`, sehingga hanya menyisakan aturan `integer`. Kirimkan data baru via `curl` dengan nilai `lecturer_id=99999` (ID yang tidak ada pada entitas pengguna).
*   **Hasil Observasi & Analisis:**  
    Sistem tetap memproses dan menyimpan data tersebut. Absennya validasi `exists` merusak integritas relasional data (*data integrity*) karena memicu terciptanya *orphan data*. Aturan `exists` wajib dipasang pada seluruh parameter yang bertindak sebagai *Foreign Key*.

4. Hapus validasi `in:...` pada status, kirim `status=superadmin`
*   **Langkah Modifikasi & Pengujian:**  
    Pada `StoreCourseRequest.php`, ganti aturan validasi field status yang semula menggunakan `Rule::in(['draft', 'active', 'archived'])` menjadi sekadar `required`. Dari browser, buka DevTools (`F12`), jalankan instruksi `document.querySelector('[name=status]').value = "superadmin"` pada Console, lalu kirimkan form. Periksa entitas terbaru menggunakan Artisan Tinker (`Course::latest()->first()->status`).
*   **Hasil Observasi & Analisis:**  
    Kolom status di basis data berhasil terisi nilai `superadmin`. Eksperimen ini menegaskan bahwa validasi di tingkat *client-side* (frontend) sangat ringkih terhadap manipulasi. Pembatasan nilai opsi wajib dikunci secara ketat di *server-side*.

5. Hapus `->withQueryString()`, lakukan pencarian lalu klik halaman 2
*   **Langkah Modifikasi & Pengujian:**  
    Pada `AdminCourseController.php` method `index`, lepas pemanggilan method `->withQueryString()` pada rantai *pagination*. Akses URL pencarian seperti `/admin/mata-kuliah?q=pemrograman`, kemudian navigasikan halaman ke tombol paginasi ke-2.
*   **Hasil Observasi & Analisis:**  
    Parameter URL `q=pemrograman` terlepas dan hilang setelah berpindah halaman. Kehadiran `withQueryString()` terbukti krusial untuk mempertahankan konteks parameter *query* (seperti filter atau kata kunci pencarian) tetap melekat sepanjang navigasi halaman.

6. Ganti `return redirect()` menjadi `return view()` pada store, lalu tekan F5 setelah simpan
*   **Langkah Modifikasi & Pengujian:**  
    Di dalam method `store` pada `AdminCourseController.php`, ubah instruksi `return redirect()->action(...)` menjadi pengembalian tampilan langsung via `return view('admin.admin-matkul', [...])`. Submit data baru (misal kode: `TES-01`), lalu lakukan *refresh* halaman (`F5`) dan konfirmasi pengiriman ulang form.
*   **Hasil Observasi & Analisis:**  
    Tercatat dua entitas data `TES-01` yang identik di dalam basis data. Hal ini memvalidasi pentingnya pola rancangan **PRG (Post-Redirect-Get)** untuk mencegah eksekusi ulang *request* bernilai sama akibat aksi *refresh* oleh pengguna.

7. Hapus `old(...)` dari semua input, lalu kirim form dengan satu kesalahan
*   **Langkah Modifikasi & Pengujian:**  
    Pada `resources/views/admin/admin.matkul.blade.php`, hapus seluruh pemanggilan helper `{{ old('xxx') }}` pada atribut `value`. Isi sebagian form dengan benar namun kosongkan salah satu field wajib (misal: kode mata kuliah) agar memicu kegagalan validasi, lalu kirim form.
*   **Hasil Observasi & Analisis:**  
    Form kembali dalam keadaan kosong total saat pesan error ditampilkan. Helper `old()` terbukti vital bagi kenyamanan pengguna (*user experience*) untuk mempertahankan data yang sebelumnya telah diketik agar tidak perlu mengulang pengisian dari awal.
