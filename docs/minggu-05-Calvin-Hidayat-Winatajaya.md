# Catatan Analisis Route dan Potensi IDOR (Minggu 05)

## 1. Output `php artisan route:list --except-vendor`

```text
GET|HEAD        / .................................................................... routes/web.php:13
GET|HEAD        admin/dashboard .................................... admin.dashboard › routes/web.php:90
GET|HEAD        admin/mata-kuliah .................. admin.matkul › Admin\AdminCourseController@index
POST            admin/mata-kuliah .................. admin.matkul.store › Admin\AdminCourseController@store
PUT             admin/mata-kuliah/{matkul} ........ admin.matkul.update › Admin\AdminCourseController@update
DELETE          admin/mata-kuliah/{matkul} ...... admin.matkul.destroy › Admin\AdminCourseController@destroy
GET|HEAD        admin/pendaftaran ................................. admin.pendaftaran › routes/web.php:108
GET|HEAD        admin/pengguna .................... admin.pengguna › Admin\UserController@index
POST            admin/pengguna .................... admin.pengguna.store › Admin\UserController@store
PUT             admin/pengguna/{user} ............. admin.pengguna.update › Admin\UserController@update
DELETE          admin/pengguna/{user} ............. admin.pengguna.destroy › Admin\UserController@destroy
GET|HEAD        assignments/{assignment} ................... assignments.show › AssignmentController@show
POST            assignments/{assignment}/submissions ....... assignments.submissions.store › SubmissionController@store
GET|HEAD        dashboard .................................................. dashboard › routes/web.php:31
PUT|PATCH       dosen/assignments/{assignment} ..... dosen.assignments.update › Dosen\AssignmentController@update
DELETE          dosen/assignments/{assignment} .... dosen.assignments.destroy › Dosen\AssignmentController@destroy
GET|HEAD        dosen/assignments/{assignment}/edit ....... dosen.assignments.edit › Dosen\AssignmentController@edit
GET|HEAD        dosen/courses/{course}/assignments ......... dosen.courses.assignments.index › Dosen\AssignmentController@index
POST            dosen/courses/{course}/assignments ......... dosen.courses.assignments.store › Dosen\AssignmentController@store
GET|HEAD        dosen/courses/{course}/assignments/create .. dosen.courses.assignments.create › Dosen\AssignmentController@create
GET|HEAD        dosen/dashboard .................................... dosen.dashboard › routes/web.php:56
GET|HEAD        dosen/mahasiswa .................................... dosen.mahasiswa › routes/web.php:64
GET|HEAD        dosen/materi .......................................... dosen.materi › routes/web.php:68
GET|HEAD        dosen/penilaian .................................... dosen.penilaian › routes/web.php:75
GET|HEAD        dosen/tugas ................................ dosen.tugas › Dosen\AssignmentController@landing
GET|HEAD        login .......................................................... login › routes/web.php:22
POST            login .................................................... login.attempt › AuthController@login
POST            logout .................................................. logout › AuthController@logout
GET|HEAD        mata-kuliah ....................... mata-kuliah.index › Mahasiswa\MahasiswaCourseController@index
GET|HEAD        mata-kuliah/{mata_kuliah} ......... mata-kuliah.show › Mahasiswa\MahasiswaCourseController@show
GET|HEAD        submissions/{submission} ................... submissions.show › SubmissionController@show
GET|HEAD        tentang .............................................................. routes/web.php:17
```

---

## 2. Daftar Titik Rawan IDOR

Tabel di bawah ini memuat daftar route yang menerima parameter model (seperti `{matkul}`, `{user}`, `{assignment}`, `{course}`, `{mata_kuliah}`, dan `{submission}`) beserta analisis otorisasi dan pencegahan akses yang berlaku saat ini.

| Method | URI Route | Parameter | Pengakses yang Seharusnya Diizinkan | Mekanisme Pencegahan Saat Ini | Potensi Kerentanan (IDOR) |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **PUT** | `admin/mata-kuliah/{matkul}` | `{matkul}` | **Admin** | Belum ada / Hanya autentikasi role | Dosen/Mahasiswa atau Admin lain dapat mengubah mata kuliah tanpa otorisasi tingkat data. |
| **DELETE** | `admin/mata-kuliah/{matkul}` | `{matkul}` | **Admin** | Belum ada / Hanya autentikasi role | Akses tanpa otorisasi data memungkinkan penghapusan mata kuliah secara acak. |
| **PUT** | `admin/pengguna/{user}` | `{user}` | **Admin** | Belum ada / Hanya autentikasi role | User dapat mengubah data akun pengguna lain jika ID diubah pada URL/Request. |
| **DELETE** | `admin/pengguna/{user}` | `{user}` | **Admin** | Belum ada / Hanya autentikasi role | Pengguna dapat menghapus akun lain jika tidak ada validasi hak akses per entitas. |
| **GET** | `assignments/{assignment}` | `{assignment}` | **Mahasiswa & Dosen** yang terdaftar di kelas terkait | Belum ada apa-apa | Mahasiswa dapat mengintip tugas dari mata kuliah/kelas yang bukan miliknya. |
| **POST** | `assignments/{assignment}/submissions` | `{assignment}` | **Mahasiswa** yang terdaftar pada mata kuliah terkait | Belum ada apa-apa | Mahasiswa bisa mengirimkan jawaban (*submission*) ke tugas kelas lain. |
| **PUT/PATCH**| `dosen/assignments/{assignment}` | `{assignment}` | **Dosen** pengampu tugas/kelas tersebut | Belum ada apa-apa | Dosen A dapat mengubah/merusak tugas yang dibuat oleh Dosen B. |
| **DELETE** | `dosen/assignments/{assignment}` | `{assignment}` | **Dosen** pengampu tugas/kelas tersebut | Belum ada apa-apa | Dosen A dapat menghapus tugas milik Dosen B dengan menguji ID pada URL. |
| **GET** | `dosen/assignments/{assignment}/edit` | `{assignment}` | **Dosen** pengampu tugas/kelas tersebut | Belum ada apa-apa | Dosen dapat mengakses halaman edit tugas dosen lain. |
| **GET** | `dosen/courses/{course}/assignments` | `{course}` | **Dosen** pengampu mata kuliah tersebut | Belum ada apa-apa | Dosen dapat melihat daftar tugas mata kuliah dosen lain. |
| **POST** | `dosen/courses/{course}/assignments` | `{course}` | **Dosen** pengampu mata kuliah tersebut | Belum ada apa-apa | Dosen dapat menambahkan tugas ke mata kuliah yang dikelola dosen lain. |
| **GET** | `dosen/courses/{course}/assignments/create` | `{course}` | **Dosen** pengampu mata kuliah tersebut | Belum ada apa-apa | Dosen dapat membuat tugas pada *course* yang bukan wewenangnya. |
| **GET** | `mata-kuliah/{mata_kuliah}` | `{mata_kuliah}` | **Mahasiswa** & **Dosen** terkait | Belum ada apa-apa | Pengguna bisa mengakses detail mata kuliah yang tidak diambil/diajar. |
| **GET** | `submissions/{submission}` | `{submission}` | **Mahasiswa** pemilik tugas & **Dosen** penguji | Belum ada apa-apa | Mahasiswa bisa melihat/mengunduh hasil tugas (*submission*) milik mahasiswa lain. |

---
