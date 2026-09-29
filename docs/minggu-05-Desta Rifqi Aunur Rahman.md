### Read Week 5

1. Jalankan `php artisan route:list --except-vendor`. Salin keluarannya ke catatan.     
   _Jawaban_ :          
   ```
    GET|HEAD        / .......................................................................................................................... routes/web.php:13
    GET|HEAD        admin/dashboard .......................................................................................... admin.dashboard › routes/web.php:91
    GET|HEAD        admin/mata-kuliah ........................................................................... admin.matkul › Admin\AdminCourseController@index
    POST            admin/mata-kuliah ..................................................................... admin.matkul.store › Admin\AdminCourseController@store
    PUT             admin/mata-kuliah/{matkul} .......................................................... admin.matkul.update › Admin\AdminCourseController@update
    DELETE          admin/mata-kuliah/{matkul} ........................................................ admin.matkul.destroy › Admin\AdminCourseController@destroy
    GET|HEAD        admin/pendaftaran ..................................................................................... admin.pendaftaran › routes/web.php:109
    GET|HEAD        admin/pengguna ................................................................................... admin.pengguna › Admin\UserController@index
    POST            admin/pengguna ............................................................................. admin.pengguna.store › Admin\UserController@store
    PUT             admin/pengguna/{user} .................................................................... admin.pengguna.update › Admin\UserController@update
    DELETE          admin/pengguna/{user} .................................................................. admin.pengguna.destroy › Admin\UserController@destroy
    GET|HEAD        assignments/{assignment} ........................................................................ assignments.show › AssignmentController@show
    POST            assignments/{assignment}/submissions .............................................. assignments.submissions.store › SubmissionController@store
    GET|HEAD        dashboard ...................................................................................................... dashboard › routes/web.php:31
    PUT|PATCH       dosen/assignments/{assignment} .................................................. dosen.assignments.update › Dosen\AssignmentController@update
    DELETE          dosen/assignments/{assignment} ................................................ dosen.assignments.destroy › Dosen\AssignmentController@destroy
    GET|HEAD        dosen/assignments/{assignment}/edit ................................................. dosen.assignments.edit › Dosen\AssignmentController@edit
    GET|HEAD        dosen/courses/{course}/assignments ........................................ dosen.courses.assignments.index › Dosen\AssignmentController@index
    POST            dosen/courses/{course}/assignments ........................................ dosen.courses.assignments.store › Dosen\AssignmentController@store
    GET|HEAD        dosen/courses/{course}/assignments/create ............................... dosen.courses.assignments.create › Dosen\AssignmentController@create
    GET|HEAD        dosen/dashboard .......................................................................................... dosen.dashboard › routes/web.php:56
    GET|HEAD        dosen/mahasiswa .......................................................................................... dosen.mahasiswa › routes/web.php:64
    GET|HEAD        dosen/materi ................................................................................................ dosen.materi › routes/web.php:68
    GET|HEAD        dosen/penilaian .......................................................................................... dosen.penilaian › routes/web.php:76
    GET|HEAD        dosen/tugas .................................................................................................. dosen.tugas › routes/web.php:72
    GET|HEAD        login .............................................................................................................. login › routes/web.php:22
    POST            login ................................................................................................... login.attempt › AuthController@login
    POST            logout ........................................................................................................ logout › AuthController@logout
    GET|HEAD        mata-kuliah .................................................................... mata-kuliah.index › Mahasiswa\MahasiswaCourseController@index
    GET|HEAD        mata-kuliah/{mata_kuliah} ........................................................ mata-kuliah.show › Mahasiswa\MahasiswaCourseController@show
    GET|HEAD        submissions/{submission} ........................................................................ submissions.show › SubmissionController@show
  

2. Route yang Menerima Parameter Model

- `admin/mata-kuliah/{matkul}` (Menerima parameter `{matkul}`) — pada method **PUT** & **DELETE**
- `admin/pengguna/{user}` (Menerima parameter `{user}`) — pada method **PUT** & **DELETE**
- `assignments/{assignment}` (Menerima parameter `{assignment}`) — pada method **GET**
- `assignments/{assignment}/submissions` (Menerima parameter `{assignment}`) — pada method **POST**
- `dosen/assignments/{assignment}` (Menerima parameter `{assignment}`) — pada method **PUT|PATCH** & **DELETE**
- `dosen/assignments/{assignment}/edit` (Menerima parameter `{assignment}`) — pada method **GET**
- `dosen/courses/{course}/assignments` (Menerima parameter `{course}`) — pada method **GET** & **POST**
- `dosen/courses/{course}/assignments/create` (Menerima parameter `{course}`) — pada method **GET**
- `mata-kuliah/{mata_kuliah}` (Menerima parameter `{mata_kuliah}`) — pada method **GET**
- `submissions/{submission}` (Menerima parameter `{submission}`) — pada method **GET**

---

3. Otorisasi dan Pembatasan Akses Route

- **`admin/mata-kuliah/{matkul}` & `admin/pengguna/{user}`** *(Method PUT/DELETE)*
  - **Hak Akses Seharusnya:** Hanya Admin.
  - **Kondisi Saat Ini:** Baru dilindungi oleh middleware `role:admin` pada grup route. Belum terdapat validasi di tingkat controller atau model untuk memastikan bahwa data spesifik yang diperbarui aman dari manipulasi tingkat lanjut.

- **`assignments/{assignment}`** *(Method GET)*
  - **Hak Akses Seharusnya:** Mahasiswa yang terdaftar pada mata kuliah terkait, Dosen pengampu, dan Admin.
  - **Kondisi Saat Ini:** Belum ada proteksi otorisasi khusus. Sistem masih mengandalkan *Route Model Binding* (`findOrFail($id)`). Asalkan ID tugas valid di database, siapa pun dapat melihat detail tugas tersebut tanpa pengecekan kepemilikan kelas.

- **`assignments/{assignment}/submissions`** *(Method POST)*
  - **Hak Akses Seharusnya:** Mahasiswa yang terdaftar untuk mengumpulkan tugas milik mereka sendiri.
  - **Kondisi Saat Ini:** Baru dibatasi oleh autentikasi umum (`auth`). Belum ada validasi untuk mencegah mahasiswa mengirimkan tugas atas nama orang lain atau mengunggah ke tugas yang bukan miliknya jika ID dimanipulasi pada form.

- **`dosen/assignments/{assignment}` & `dosen/assignments/{assignment}/edit`** *(Method PUT/PATCH/DELETE/GET)*
  - **Hak Akses Seharusnya:** Hanya Dosen pengampu yang membuat tugas tersebut (atau pengampu mata kuliah terkait).
  - **Kondisi Saat Ini:** Hanya dilindungi middleware `role:dosen` di tingkat grup route. Akibatnya, semua pengguna ber-role dosen dapat mengaksesnya, sehingga berpotensi menimbulkan celah *IDOR (Insecure Direct Object Reference)* antar-dosen (misalnya Dosen A mengedit/menghapus tugas milik Dosen B dengan mengubah ID di URL).

- **`dosen/courses/{course}/assignments`** *(dan turunannya yang memiliki parameter `{course}`)*
  - **Hak Akses Seharusnya:** Dosen pengampu dari mata kuliah terkait.
  - **Kondisi Saat Ini:** Baru dijaga oleh middleware `role:dosen`. Hal ini mencegah mahasiswa masuk, namun dosen lain masih bisa mengakses mata kuliah yang bukan diampunya jika tidak ada validasi relasi data.

- **`mata-kuliah/{mata_kuliah}`** *(Method GET)*
  - **Hak Akses Seharusnya:** Mahasiswa yang mengontrak/mengambil mata kuliah tersebut.
  - **Kondisi Saat Ini:** Belum ada mekanisme pembatasan. Mahasiswa dapat membuka halaman mata kuliah mana pun hanya dengan mengganti ID pada URL.

- **`submissions/{submission}`** *(Method GET)*
  - **Hak Akses Seharusnya:** Mahasiswa pemilik *submission* tersebut, Dosen penilai, dan Admin.
  - **Kondisi Saat Ini:** Belum ada proteksi otorisasi. Ini merupakan titik yang sangat rawan celah IDOR, karena pengguna cukup mengganti ID di URL (contoh: dari `/submissions/1` ke `/submissions/2`) untuk melihat tugas dan nilai milik pengguna lain.

---

> **Kesimpulan:** Sebagian besar route berparameter di atas belum mengimplementasikan pengecekan kepemilikan data (*data ownership*) secara rinci, melainkan baru dibatasi secara umum menggunakan middleware role (`admin`/`dosen`).


4. Daftar Titik Rawan IDOR :

| No | Method | URI / Endpoint | Parameter Model | Siapa yang Berhak Mengakses | Potensi Celah / Risiko IDOR Saat Ini |
| :-: | :--- | :--- | :--- | :--- | :--- |
| 1 | `GET` | `assignments/{assignment}` | `{assignment}` | Mahasiswa peserta matkul, Dosen, Admin | Belum ada verifikasi hak akses. Mahasiswa dari program studi atau kelas lain tetap bisa mengintip rincian tugas hanya dengan mengganti parameter ID di URL. |
| 2 | `GET` | `mata-kuliah/{mata_kuliah}` | `{mata_kuliah}` | Mahasiswa terdaftar, Dosen, Admin | Sistem belum mengecek relasi KRS/kontrak mata kuliah. Mahasiswa bisa mengakses halaman mata kuliah yang tidak mereka ambil. |
| 3 | `GET` | `submissions/{submission}` | `{submission}` | Mahasiswa pembuat tugas, Dosen pengampu, Admin | Peserta didik dapat mengubah ID pada link (contoh: `/submissions/41` menjadi `/submissions/42`) untuk mengintip hasil pengerjaan dan nilai milik orang lain. |
| 4 | `PUT/PATCH` | `dosen/assignments/{assignment}` | `{assignment}` | Dosen pemilik tugas / pengampu kelas | Pengamanan baru sebatas middleware `role:dosen`. Seorang dosen berpotensi mengubah konten tugas milik dosen lain karena belum ada pengecekan Policy/Gate tingkat objek. |
| 5 | `DELETE` | `dosen/assignments/{assignment}` | `{assignment}` | Dosen pemilik tugas / pengampu kelas | Mengalami masalah serupa; tanpa otorisasi berbasis kepemilikan data, seorang dosen bisa membuang/menghapus tugas buatan dosen lain. |
| 6 | `PUT/DELETE` | `admin/mata-kuliah/{matkul}` | `{matkul}` | Khusus Admin | Akses memang dibatasi oleh middleware `role:admin`, tetapi logika di Controller tetap wajib mengecek validitas dan otorisasi resource agar data tidak sembarangan diubah. |
