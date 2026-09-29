Nama : **Baihaqi Abimanyu Toro Putra**
NIM : **10241014**

---

### Read

1. Jalankan `php artisan route:list --except-vendor`. Salin keluarannya ke catatan.
*Jawaban* :
```text
GET|HEAD        / .......................................................................................................... routes/web.php:13
GET|HEAD        admin/dashboard .......................................................................... admin.dashboard › routes/web.php:91
GET|HEAD        admin/mata-kuliah ........................................................... admin.matkul › Admin\AdminCourseController@index
POST            admin/mata-kuliah ..................................................... admin.matkul.store › Admin\AdminCourseController@store
PUT             admin/mata-kuliah/{matkul} .......................................... admin.matkul.update › Admin\AdminCourseController@update
DELETE          admin/mata-kuliah/{matkul} ........................................ admin.matkul.destroy › Admin\AdminCourseController@destroy
GET|HEAD        admin/pendaftaran ..................................................................... admin.pendaftaran › routes/web.php:109
GET|HEAD        admin/pengguna ................................................................... admin.pengguna › Admin\UserController@index
POST            admin/pengguna ............................................................. admin.pengguna.store › Admin\UserController@store
PUT             admin/pengguna/{user} .................................................... admin.pengguna.update › Admin\UserController@update
DELETE          admin/pengguna/{user} .................................................. admin.pengguna.destroy › Admin\UserController@destroy
GET|HEAD        assignments/{assignment} ........................................................ assignments.show › AssignmentController@show
POST            assignments/{assignment}/submissions .............................. assignments.submissions.store › SubmissionController@store
GET|HEAD        dashboard ...................................................................................... dashboard › routes/web.php:31
PUT|PATCH       dosen/assignments/{assignment} .................................. dosen.assignments.update › Dosen\AssignmentController@update
DELETE          dosen/assignments/{assignment} ................................ dosen.assignments.destroy › Dosen\AssignmentController@destroy
GET|HEAD        dosen/assignments/{assignment}/edit ................................. dosen.assignments.edit › Dosen\AssignmentController@edit
GET|HEAD        dosen/courses/{course}/assignments ........................ dosen.courses.assignments.index › Dosen\AssignmentController@index
POST            dosen/courses/{course}/assignments ........................ dosen.courses.assignments.store › Dosen\AssignmentController@store
GET|HEAD        dosen/courses/{course}/assignments/create ............... dosen.courses.assignments.create › Dosen\AssignmentController@create
GET|HEAD        dosen/dashboard .......................................................................... dosen.dashboard › routes/web.php:56
GET|HEAD        dosen/mahasiswa .......................................................................... dosen.mahasiswa › routes/web.php:64
GET|HEAD        dosen/materi ................................................................................ dosen.materi › routes/web.php:68
GET|HEAD        dosen/penilaian .......................................................................... dosen.penilaian › routes/web.php:76
GET|HEAD        dosen/tugas .................................................................................. dosen.tugas › routes/web.php:72
GET|HEAD        login .............................................................................................. login › routes/web.php:22
POST            login ................................................................................... login.attempt › AuthController@login
POST            logout ........................................................................................ logout › AuthController@logout
GET|HEAD        mata-kuliah .................................................... mata-kuliah.index › Mahasiswa\MahasiswaCourseController@index
GET|HEAD        mata-kuliah/{mata_kuliah} ........................................ mata-kuliah.show › Mahasiswa\MahasiswaCourseController@show
GET|HEAD        submissions/{submission} ........................................................ submissions.show › SubmissionController@show

```


2. Tandai setiap route yang menerima parameter model (`{course}`, `{assignment}`, dst).
*Jawaban* :
* `admin/mata-kuliah/{matkul}` (Method: `PUT`, `DELETE`) — Parameter: `{matkul}`
* `admin/pengguna/{user}` (Method: `PUT`, `DELETE`) — Parameter: `{user}`
* `assignments/{assignment}` (Method: `GET`) — Parameter: `{assignment}`
* `assignments/{assignment}/submissions` (Method: `POST`) — Parameter: `{assignment}`
* `dosen/assignments/{assignment}` (Method: `PUT`, `PATCH`, `DELETE`) — Parameter: `{assignment}`
* `dosen/assignments/{assignment}/edit` (Method: `GET`) — Parameter: `{assignment}`
* `dosen/courses/{course}/assignments` (Method: `GET`, `POST`) — Parameter: `{course}`
* `dosen/courses/{course}/assignments/create` (Method: `GET`) — Parameter: `{course}`
* `mata-kuliah/{mata_kuliah}` (Method: `GET`) — Parameter: `{mata_kuliah}`
* `submissions/{submission}` (Method: `GET`) — Parameter: `{submission}`


3. Untuk setiap route bertanda, jawab: **siapa saja yang seharusnya boleh mengaksesnya, dan apa yang saat ini mencegah orang lain?**
*Jawaban* :
* **`admin/mata-kuliah/{matkul}` & `admin/pengguna/{user}` (PUT & DELETE)**
* **Hak Akses Ideal:** Administrator sistem.
* **Mekanisme Proteksi Saat Ini:** Baru sebatas pengecekan peran di tingkat route via middleware `role:admin`. Belum terdapat mekanisme validasi otorisasi mendalam pada tingkatan controller maupun model handler.


* **`assignments/{assignment}` (GET)**
* **Hak Akses Ideal:** Mahasiswa peserta mata kuliah terkait, Dosen pengampu, serta Administrator.
* **Mekanisme Proteksi Saat Ini:** Belum ada proteksi otorisasi spesifik. Sistem hanya mengandalkan *Route Model Binding* bawaan (`findOrFail($id)`). Selama parameter ID valid di basis data, siapa pun yang terautentikasi dapat mengakses detail tugas tanpa adanya verifikasi keikutsertaan kelas.


* **`assignments/{assignment}/submissions` (POST)**
* **Hak Akses Ideal:** Mahasiswa aktif yang terdaftar dalam mata kuliah bersangkutan.
* **Mekanisme Proteksi Saat Ini:** Terbatas pada otorisasi global `auth`. Belum ada pengecekan kepemilikan entitas yang mencegah mahasiswa mengirimkan berkas jawaban ke tugas mata kuliah yang tidak diambil atau atas nama pengguna lain.


* **`dosen/assignments/{assignment}` & `dosen/assignments/{assignment}/edit` (PUT/PATCH/DELETE & GET)**
* **Hak Akses Ideal:** Dosen pembuat tugas atau pengampu utama mata kuliah tersebut.
* **Mekanisme Proteksi Saat Ini:** Dilindungi oleh middleware `role:dosen`. Karena otorisasi sebatas berbasis peran umum, Dosen A dapat memanipulasi atau menghapus entitas tugas milik Dosen B dengan cara mengubah nilai ID pada URL (celah IDOR antardosen).


* **`dosen/courses/{course}/assignments` & turunannya yang memuat `{course}` (GET & POST)**
* **Hak Akses Ideal:** Dosen pengampu mata kuliah terkait.
* **Mekanisme Proteksi Saat Ini:** Baru dipagari oleh middleware `role:dosen`. Mahasiswa memang terblokir, namun belum ada validasi relasi kepemilikan mata kuliah untuk membatasi akses antardosen.


* **`mata-kuliah/{mata_kuliah}` (GET)**
* **Hak Akses Ideal:** Mahasiswa yang secara resmi mengontrak mata kuliah tersebut.
* **Mekanisme Proteksi Saat Ini:** Belum ada skema batasan. Pengguna dapat meretas akses ke konten mata kuliah mana pun cukup dengan mengganti nilai parameter ID pada URL.


* **`submissions/{submission}` (GET)**
* **Hak Akses Ideal:** Mahasiswa pemilik lembar jawaban, Dosen penilai, dan Administrator.
* **Mekanisme Proteksi Saat Ini:** Belum ada proteksi kepemilikan entitas. Kondisi ini sangat rentan terhadap eksploitasi IDOR, di mana pengguna cukup melakukan enumerasi ID URL (misalnya dari `/submissions/1` ke `/submissions/2`) untuk mengintip berkas serta nilai milik pengguna lain.




*Kesimpulan:* Mayoritas *route* berparameter belum dilengkapi validasi hak kepemilikan objek (*object-level authorization*) dan masih sebatas mengandalkan proteksi peran (*role-based access control*) di tingkat middleware.
4. Buat tabel di `docs/minggu-05-<nama>.md` berjudul "Daftar Titik Rawan IDOR".
*Jawaban* :
### Daftar Titik Rawan IDOR


| No | Method | URI / Endpoint | Parameter Model | Hak Akses Ideal | Potensi Celah / Risiko IDOR Saat Ini |
| --- | --- | --- | --- | --- | --- |
| 1 | `GET` | `assignments/{assignment}` | `{assignment}` | Mahasiswa terdaftar, Dosen pengampu, Admin | Belum ada validasi kepemilikan. Mahasiswa luar kelas dapat mengintip rincian tugas via manipulasi ID pada URL. |
| 2 | `GET` | `mata-kuliah/{mata_kuliah}` | `{mata_kuliah}` | Mahasiswa peserta matkul, Dosen pengampu, Admin | Belum ada filter relasi KRS/kontrak matkul. Mahasiswa dapat mengakses ruang mata kuliah yang tidak diampu/dikontrak. |
| 3 | `GET` | `submissions/{submission}` | `{submission}` | Mahasiswa pemilik, Dosen penilai, Admin | Belum ada pengecekan kepemilikan berkas. Pengguna dapat melakukan enumerasi ID URL untuk melihat hasil pekerjaan dan nilai mahasiswa lain. |
| 4 | `PUT/PATCH` | `dosen/assignments/{assignment}` | `{assignment}` | Dosen pembuat/pengampu tugas | Hanya dilindungi middleware `role:dosen`. Dosen lain berpotensi memperbarui tugas yang bukan wewenangnya. |
| 5 | `DELETE` | `dosen/assignments/{assignment}` | `{assignment}` | Dosen pembuat/pengampu tugas | Proteksi sebatas level *role*. Belum ada verifikasi kepemilikan objek, berisiko penghapusan tugas lintas dosen. |
| 6 | `PUT/DELETE` | `admin/mata-kuliah/{matkul}` | `{matkul}` | Khusus Administrator | Terproteksi middleware `role:admin`. Perlu penataan *model binding* dan validasi internal controller agar eksekusi data tetap aman. |
