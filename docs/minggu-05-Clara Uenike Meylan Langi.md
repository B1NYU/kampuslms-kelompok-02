## Catatan 5.3

Nama : **Clara Uenike Meylan Langi**      
NIM : **10241022**

---
### Read

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
  

2. Tandai setiap route yang menerima parameter model (`{course}`, `{assignment}`, dst).     
   _Jawaban_ :      
   - `admin/mata-kuliah/{matkul}` (Menerima parameter `{matkul}`) — pada method PUT & DELETE
   - `admin/pengguna/{user}` (Menerima parameter `{user}`) — pada method PUT & DELETE
   - `assignments/{assignment}` (Menerima parameter `{assignment}`) — pada method GET
   - `assignments/{assignment}/submissions` (Menerima parameter `{assignment}`) — pada method POST
   - `dosen/assignments/{assignment}` (Menerima parameter `{assignment}`) — pada method PUT|PATCH & DELETE
   - `dosen/assignments/{assignment}/edit` (Menerima parameter `{assignment}`) — pada method GET
   - `dosen/courses/{course}/assignments` (Menerima parameter `{course}`) — pada method GET & POST
   - `dosen/courses/{course}/assignments/create` (Menerima parameter `{course}`) — pada method GET
   - `mata-kuliah/{mata_kuliah}` (Menerima parameter `{mata_kuliah}`) — pada method GET
   - `submissions/{submission}` (Menerima parameter `{submission}`) — pada method GET
  
3. Untuk setiap route bertanda, jawab: **siapa saja yang seharusnya boleh mengaksesnya, dan apa yang saat ini mencegah orang lain?** Kemungkinan besar jawabannya "belum ada apa-apa" — itu wajar, dan itulah pekerjaan minggu ini dan minggu 7.        
   _Jawaban_ :      
   - `admin/mata-kuliah/{matkul}` & `admin/pengguna/{user}` (Method PUT/DELETE)
     - Siapa yang seharusnya boleh mengakses: Hanya Admin.
     - Apa yang saat ini mencegah orang lain: Saat ini baru dijaga oleh middleware `role:admin` pada grup routernya. Namun, belum ada pengecekan di dalam controller atau model untuk memastikan bahwa data spesifik yang diubah benar-benar valid atau aman dari manipulasi tingkat lanjut.
   - `assignments/{assignment}` (Method GET)
     - Siapa yang seharusnya boleh mengakses: Mahasiswa yang terdaftar pada mata kuliah tersebut, Dosen pengampu, dan Admin.
     - Apa yang saat ini mencegah orang lain: Belum ada apa-apa. Saat ini Laravel hanya menggunakan `findOrFail($id)` standar melalui Route Model Binding. Artinya, asalkan ID tugas tersebut ada di database, siapa pun yang memasukkan URL-nya bisa melihat detail tugas tersebut tanpa peduli apakah dia mahasiswa dari kelas itu atau bukan.
   - `assignments/{assignment}/submissions` (Method POST)
     - Siapa yang seharusnya boleh mengakses: Mahasiswa yang bersangkutan untuk mengumpulkan tugas.
     - Apa yang saat ini mencegah orang lain: Baru dibatasi oleh status login secara umum (`auth`), tetapi belum ada pencegahan bagi mahasiswa nakal untuk mengirimkan tugas atas nama mahasiswa lain atau ke tugas yang bukan miliknya jika ID-nya dimanipulasi di form.
   - `dosen/assignments/{assignment}` & `dosen/assignments/{assignment}/edit` (Method PUT/PATCH/DELETE/GET)
     - Siapa yang seharusnya boleh mengakses: Hanya Dosen pengampu yang membuat tugas tersebut (atau Dosen pemilik mata kuliah).
     - Apa yang saat ini mencegah orang lain: Baru dijaga oleh middleware `role:dosen` di level grup route. Bahayanya: Middleware ini meloloskan semua dosen. Artinya, Dosen A bisa saja mengedit atau menghapus tugas milik Dosen B hanya dengan menebak/mengganti angka ID di URL (potensi IDOR antar-dosen).
   - `dosen/courses/{course}/assignments` (dan turunannya yang memiliki `{course}`)
     - Siapa yang seharusnya boleh mengakses: Dosen pengampu mata kuliah terkait.
     - Apa yang saat ini mencegah orang lain: Middleware `role:dosen` (sehingga mahasiswa tidak bisa masuk, tapi dosen lain masih berpotensi saling mengakses jika tidak divalidasi dengan relasi mata kuliah).
   - `mata-kuliah/{mata_kuliah}` (Method GET)
     - Siapa yang seharusnya boleh mengakses: Mahasiswa yang mengambil mata kuliah tersebut.
     - Apa yang saat ini mencegah orang lain: Belum ada apa-apa. Mahasiswa bisa membuka halaman mata kuliah apa saja (termasuk yang tidak mereka ambil/kontrak) hanya dengan mengubah angka ID di URL.
   - `submissions/{submission}` (Method GET)
     - Siapa yang seharusnya boleh mengakses: Mahasiswa pemilik submission te
     - Apa yang saat ini mencegah orang lain: Belum ada apa-apa. Ini adalah titik paling rawan IDOR. Mahasiswa cukup mengubah angka di URL (misal dari `/submissions/1` ke `/submissions/2`) untuk membaca tugas dan nilai milik teman seangkatannya.rsebut, Dosen penilai, dan Admin.      
  
    Sebagian besar route berparameter di atas belum memiliki pengaman kepemilikan data yang spesifik, melainkan baru dijaga secara kasar oleh middleware role (admin/dosen).

4. Buat tabel di `docs/minggu-05-<nama>.md` berjudul "Daftar Titik Rawan IDOR". Tabel ini akan Anda pakai lagi di minggu 7 dan saat interview.      
   _Jawaban_ :      
   **Daftar Titik Rawan IDOR** : 
    | No | Method | URI / Endpoint | Parameter Model | Siapa yang Berhak Mengakses | Potensi Celah / Risiko IDOR Saat Ini |
    |:--:|:--|:---|:---|:---|:---|
    | 1 | `GET` | `assignments/{assignment}` | `{assignment}` | Mahasiswa terdaftar, Dosen, Admin | Belum ada validasi kepemilikan. Mahasiswa dari kelas/prodi lain bisa melihat detail tugas via manipulasi ID URL. |
    | 2 | `GET` | `mata-kuliah/{mata_kuliah}` | `{mata_kuliah}` | Mahasiswa yang mengambil matkul, Dosen, Admin | Belum ada filter relasi. Mahasiswa bisa membuka halaman mata kuliah yang tidak mereka kontrak. |
    | 3 | `GET` | `submissions/{submission}` | `{submission}` | Mahasiswa pemilik submission, Dosen penilai, Admin | Mahasiswa dapat mengganti angka ID di URL (misal `/submissions/41` ke `/submissions/42`) untuk membaca tugas dan nilai milik mahasiswa lain. |
    | 4 | `PUT/PATCH` | `dosen/assignments/{assignment}` | `{assignment}` | Dosen pengampu mata kuliah terkait | Hanya dijaga middleware `role:dosen`. Dosen A berhak masuk area dosen, tetapi bisa saja mengedit tugas milik Dosen B. |
    | 5 | `DELETE` | `dosen/assignments/{assignment}` | `{assignment}` | Dosen pengampu mata kuliah terkait | Sama seperti di atas, belum ada validasi kepemilikan tingkat objek, berisiko dosen menghapus tugas dosen lain. |
    | 6 | `PUT/DELETE` | `admin/mata-kuliah/{matkul}` | `{matkul}` | Hanya Admin | Dilindungi middleware `role:admin`, namun perlu dipastikan controller menggunakan model binding dengan benar. |