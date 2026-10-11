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
   [ Lapis 2: Berbasis Peran (Middleware 'role:admin|dosen|mahasiswa') ]
   ↳ Membatasi akses pintu masuk modul (contoh: /admin/*, /dosen/*).
               │
               ▼
   [ Lapis 3: Otorisasi Objek (Laravel Policy & Gate) ]
   ↳ Memeriksa kepemilikan data spesifik per baris database (Anti-IDOR).
               │
               ▼
   [ Lapis 4: Integritas Input (Form Request & Server-Side Binding) ]
   ↳ Memaksa ID diambil dari auth()->id(), bukan dari payload input pengguna.
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
| **5** | `POST` | `/assignments/{assignment}/submissions` | `Submission` | Mahasiswa terdaftar di MK tujuan | Mahasiswa menyisipkan parameter `user_id=<id_korban>` di form untuk memalsukan identitas pengumpul. | **`SubmissionController@store`**<br>Nilai `user_id` dipaksa dari `$request->user()->id`, mengabaikan input form | `302 Redirect`<br>*(Tersimpan atas nama penyerang, bukan korban)* |
| **6** | `GET` | `/dosen/courses/{course}/assignments` | `Course` | Dosen pengampu mata kuliah terkait, Admin | Dosen A membuka daftar kelola tugas di mata kuliah milik Dosen B. | **`AssignmentPolicy@viewAny`** / **`ChecksCourseAccess::teaches`** | `403 Forbidden` |
| **7** | `POST` | `/dosen/courses/{course}/assignments` | `Course` | Dosen pengampu mata kuliah terkait | Dosen A membuat tugas perkuliahan di kelas milik Dosen B. | **`AssignmentPolicy@create`**<br>`$this->teaches($user, $course)` | `403 Forbidden` |
| **8** | `PUT/PATCH` | `/dosen/assignments/{assignment}` | `Assignment` | Dosen pembuat / pengampu mata kuliah | Dosen A mengedit deadline atau instruksi tugas milik Dosen B. | **`AssignmentPolicy@update`**<br>`$assignment->created_by === $user->id` atau pengampu | `403 Forbidden` |
| **9** | `DELETE` | `/dosen/assignments/{assignment}` | `Assignment` | Dosen pengampu (jika belum ada berkas masuk) | Dosen A menghapus tugas mata kuliah Dosen B. | **`AssignmentPolicy@delete`**<br>Dicegat kepemilikan dan relasi pengumpulan | `403 Forbidden` |
| **10** | `DELETE` | `/dosen/mahasiswa/{course}/{student}` | `Course`, `User` | Dosen pengampu mata kuliah terkait | Dosen A mengeluarkan mahasiswa dari kelas yang diampu oleh Dosen B. | **`DosenStudentController@destroy`**<br>`abort_unless($course->lecturer_id === auth()->id(), 403)` | `403 Forbidden` |
| **11** | `PUT` | `/dosen/penilaian/{submission}` | `Submission` | Dosen pengampu mata kuliah tugas terkait | Dosen A memberikan atau mengubah nilai pada tugas kelas Dosen B. | **`GradePolicy@update`**<br>`$this->teaches($user, $assignment->course)` | `403 Forbidden` |
| **12** | `PUT` | `/admin/pengguna/{user}` | `User` | Khusus Administrator | Mahasiswa atau Dosen mengirim request manipulasi role menjadi `admin` (*Privilege Escalation*). | **Middleware `role:admin`** + Form Request | `403 Forbidden` |

---
