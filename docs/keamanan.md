# Dokumentasi Keamanan & Analisis Titik Rawan IDOR

**Proyek:** KampusLMS (Kelompok 02)  
**Tujuan Dokumen:** Referensi Asesmen Keamanan & Live Penetration Test (UTS Minggu 8)

---

## 1. Arsitektur Pertahanan Berlapis (*Defense-in-Depth*)

Aplikasi KampusLMS menerapkan 4 lapisan pertahanan untuk menjamin keamanan data dan mencegah celah **IDOR (*Insecure Direct Object Reference*)**:

```
[ Request Masuk (Browser / curl) ]
               │
               ▼
   [ Lapis 1: Autentikasi (Middleware 'auth') ]
   ↳ Memastikan sesi aktif; tamu dialihkan (302) atau ditolak (401).
               │
               ▼
   [ Lapis 2: Berbasis Peran (Middleware 'role:admin' / 'role:dosen' / 'role:mahasiswa') ]
   ↳ Membatasi akses pintu masuk modul (contoh: /admin/*, /dosen/*).
               │
               ▼
   [ Lapis 3: Otorisasi Objek (Laravel Policy & Gate) ]
   ↳ Memeriksa kepemilikan data spesifik per baris database (Anti-IDOR).
               │
               ▼
   [ Lapis 4: Integritas Input (Form Request / validate() inline & Server-Side Binding) ]
   ↳ Hanya field tervalidasi yang dipakai; ID pengguna diambil dari auth()->id(), bukan dari payload.
```

---

## 2. Tabel Utama: Daftar Titik Rawan IDOR

Tabel ini adalah rujukan bagi penguji saat **Asesmen UTS Bagian 3 (Uji Keamanan Langsung)**. Penguji dapat memilih salah satu baris dan mengujinya melalui `curl` atau manipulasi URL.

| No | Method | URI / Endpoint | Target Objek | Hak Akses yang Sah | Potensi Serangan IDOR | Lapisan Pengaman Kode | Hasil yang Diharapkan |
|:--:|:--|:---|:---|:---|:---|:---|:---:|
| **1** | `GET` | `/submissions/{submission}` | `Submission` | Mahasiswa pemilik, Dosen pengampu, Admin | Mahasiswa A mengganti angka ID di URL untuk mengintip hasil tugas dan nilai Mahasiswa B. | **`SubmissionPolicy@view`**<br>`(int) $submission->user_id === (int) $user->id` | `403 Forbidden` |
| **2** | `GET` | `/submissions/{submission}/unduh` | `Submission` | Mahasiswa pemilik, Dosen pengampu, Admin | Pengguna luar mengunduh file lampiran jawaban tugas milik mahasiswa lain via direct link. | **`SubmissionPolicy@view`**<br>Dipanggil di `SubmissionController@download` | `403 Forbidden` |
| **3** | `GET` | `/mahasiswa/mata-kuliah/{mata_kuliah}` | `Course` | Mahasiswa terdaftar pada MK tersebut, Dosen pengampu, Admin | Mahasiswa membuka materi dan aktivitas mata kuliah yang tidak ia kontrak / belum disetujui KRS. | **`CoursePolicy@view`**<br>`$this->isEnrolled($user, $course)` via tabel `course_user` | `403 Forbidden` |
| **4** | `GET` | `/assignments/{assignment}` | `Assignment` | Mahasiswa terdaftar (hanya status `published`), Dosen pengampu, Admin | Mahasiswa mengintip soal tugas yang masih berstatus `draft` sebelum resmi dirilis dosen. | **`AssignmentPolicy@view`**<br>`$assignment->status === 'published'` jika role mahasiswa | `403 Forbidden` |
| **5** | `POST` | `/mahasiswa/assignments/{assignment}/submissions` | `Submission` | Mahasiswa terdaftar di MK tujuan | Mahasiswa menyisipkan parameter `user_id=<id_korban>` di form untuk memalsukan identitas pengumpul. | **`StoreSubmissionRequest@authorize`** → `SubmissionPolicy@create`<br>**`SubmissionController@store`**: nilai `user_id` dipaksa dari `$request->user()->id`, input form diabaikan | `302 Redirect`<br>*(Tersimpan atas nama penyerang, bukan korban)* |
| **6** | `GET` | `/dosen/courses/{course}/assignments` | `Course` | Dosen pengampu mata kuliah terkait, Admin | Dosen A membuka daftar kelola tugas di mata kuliah milik Dosen B. | **`AssignmentPolicy@viewAny`**<br>`canSeeCourseContent()` → `teaches()` (`course->lecturer_id`) | `403 Forbidden` |
| **7** | `POST` | `/dosen/courses/{course}/assignments` | `Course` | Dosen pengampu mata kuliah terkait | Dosen A membuat tugas perkuliahan di kelas milik Dosen B. | **`AssignmentPolicy@create`**<br>`canManageCourseContent()`: `teaches()` + MK berstatus `draft`/`active` | `403 Forbidden` |
| **8** | `PUT/PATCH` | `/dosen/assignments/{assignment}` | `Assignment` | Dosen pengampu mata kuliah tugas tersebut (MK `draft`/`active`) | Dosen A mengedit deadline atau instruksi tugas milik Dosen B. | **`AssignmentPolicy@update`**<br>`canManageCourseContent($user, $assignment->course)`: kepemilikan lewat `course->lecturer_id`, **bukan** `created_by` | `403 Forbidden` |
| **9** | `DELETE` | `/dosen/assignments/{assignment}` | `Assignment` | Dosen pengampu (jika belum ada berkas masuk) | Dosen A menghapus tugas mata kuliah Dosen B. | **`AssignmentPolicy@delete`**<br>Dicegat kepemilikan dan relasi pengumpulan | `403 Forbidden` |
| **10** | `DELETE` | `/dosen/mahasiswa/{course}/{student}` | `Course`, `User` | Dosen pengampu mata kuliah terkait (MK `draft`/`active`) | Dosen A mengeluarkan mahasiswa dari kelas yang diampu oleh Dosen B. | **`Dosen\StudentController@destroy`**<br>`Gate::authorize('manageEnrollment', $course)` → `CoursePolicy@manageEnrollment` | `403 Forbidden` |
| **11** | `PUT` | `/dosen/penilaian/{submission}` | `Submission` | Dosen pengampu mata kuliah tugas terkait (MK `active`) | Dosen A memberikan atau mengubah nilai pada tugas kelas Dosen B. | **`GradeSubmissionRequest@authorize`** → `Gate::authorize('grade', $submission)` = **`SubmissionPolicy@grade`**<br>`teaches($user, $course)` + MK `active` | `403 Forbidden` |
| **12** | `PUT` | `/admin/pengguna/{user}` | `User` | Khusus Administrator | Mahasiswa atau Dosen mengirim request manipulasi role menjadi `admin` (*Privilege Escalation*). | **Middleware `role:admin`** pada grup route `/admin`<br>Validasi inline `$request->validate()` di `Admin\UserController@update` (tidak ada Form Request) | `403 Forbidden` |

---
