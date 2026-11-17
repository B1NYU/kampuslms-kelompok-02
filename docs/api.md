# Dokumentasi API KampusLMS v1

- **Base URL:** `http://127.0.0.1:8000/api/v1` (`php artisan serve`)
- **Autentikasi:** Bearer token Laravel Sanctum dari `POST /auth/login` (berlaku 12 jam)
- **Header wajib:** `Accept: application/json`; tambahkan `Content-Type: application/json` untuk request ber-body JSON. Unggah berkas (`POST /assignments/{assignment}/submissions`) memakai `multipart/form-data` (`curl -F`), bukan JSON
- **Format sukses:** objek tunggal `{"data": {...}}`; koleksi `{"data": [...], "meta": {"current_page", "last_page", "total"}}`
- **Format gagal:** selalu JSON `{"message": "..."}` (422 menambahkan `errors`)
- Contoh `curl` memakai sintaks bash (Git Bash / WSL / Linux / macOS). Di terminal, simpan token ke variabel:

```bash
BASE=http://127.0.0.1:8000/api/v1
TOKEN=$(curl -s -X POST $BASE/auth/login -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"identifier":"10240000","password":"password"}' | grep -o '"token":"[^"]*"' | cut -d'"' -f4)
```

## Akun demo (`php artisan migrate:fresh --seed`)

| Nama | Role | `identifier` | Password | Keterangan |
|---|---|---|---|---|
| Super Administrator | `admin` | `ADMIN-000` | `password` | |
| Dosen Demo (Dosen A) | `dosen` | `NIP-000` | `password` | mengampu MK ID 1 |
| Dosen B | `dosen` | `NIP-999` | `password` | mengampu MK ID 2 |
| Mahasiswa Demo | `mahasiswa` | `10240000` | `password` | terdaftar di MK 1, **tidak** di MK 2 |

`identifier` boleh berupa email atau NIM/NIP.

## Ringkasan endpoint

| Method | URI | Peran | Sukses |
|---|---|---|---|
| POST | `/auth/login` | Publik (5/menit) | 200 |
| POST | `/auth/logout` | Login | 200 |
| GET | `/me` | Login | 200 |
| GET | `/courses` | Login | 200 |
| GET | `/courses/{course}` | Dosen pengampu, mahasiswa terdaftar, admin | 200 |
| GET | `/courses/{course}/assignments` | Dosen pengampu, mahasiswa terdaftar, admin | 200 |
| GET | `/courses/{course}/materials` | Dosen pengampu, mahasiswa terdaftar, admin | 200 |
| POST | `/assignments` | Dosen pengampu MK tujuan | 201 |
| PUT / PATCH | `/assignments/{assignment}` | Dosen pengampu | 200 |
| DELETE | `/assignments/{assignment}` | Dosen pengampu | 204 |
| GET | `/assignments/{assignment}/submissions` | Dosen pengampu | 200 |
| POST | `/assignments/{assignment}/submissions` | Mahasiswa terdaftar | 201 |
| PUT | `/submissions/{submission}/grade` | Dosen pengampu (MK `active`) | 201 (nilai pertama) / 200 (nilai ulang), sesuai kontrak spesifikasi |
| GET | `/notifications` | Login (hanya milik sendiri) | 200 |
| POST | `/notifications/{id}/read` | Login (hanya milik sendiri) | 200 |

Total **15 endpoint** (9 awal + 6 tambahan).

## Kode status

| Kode | Arti di API ini |
|---|---|
| 200 / 201 / 204 | Berhasil / berhasil membuat / berhasil tanpa isi |
| 401 | Token tidak ada, salah, kedaluwarsa, atau sudah dicabut |
| 403 | Sudah login tetapi tidak berhak (peran salah, bukan pemilik data, atau syarat kebijakan tidak terpenuhi, misalnya tugas sudah dikumpulkan) |
| 404 | Data tidak ditemukan. Khusus notifikasi, ID milik orang lain juga 404 (bukan 403) agar keberadaan ID tidak bocor |
| 405 | Metode HTTP tidak didukung endpoint |
| 409 | Konflik: tugas yang sudah punya pengumpulan tidak bisa dihapus |
| 422 | Validasi gagal |
| 429 | Terlalu banyak permintaan (login 5/menit per IP; endpoint lain 60/menit) |

Otorisasi (403) selalu dicek **sebelum** validasi (422).

## Keamanan yang diterapkan

- **API Resource = daftar putih.** Tidak ada model mentah yang dikembalikan. `password`, `remember_token`, `email_verified_at`, `file_path` tidak pernah keluar.
- **Data pengampu** pada daftar/detail MK hanya `id` dan `name`.
- **Login** memberi satu pesan yang sama untuk identitas tidak terdaftar dan password salah (mencegah *user enumeration*) dan menjalankan `Hash::check` di kedua kasus.
- **Rate limiting:** `throttle:5,1` pada login (ditambah pembatas per identitas+IP), `throttle:60,1` pada endpoint terlindungi.
- **Eager loading:** `with('lecturer:id,name')` dan `withCount`/filter submissions agar tidak ada N+1.
- **Tugas draft** tidak terlihat dan tidak dihitung bagi mahasiswa.
- **Pengumpulan:** `user_id` pengumpul selalu diambil dari token; input `user_id` dari klien diabaikan. Daftar pengumpulan hanya untuk dosen pengampu. `file_path` tidak pernah dikembalikan.
- **Penilaian:** otorisasi `SubmissionPolicy@grade` dicek sebelum validasi; batas skor mengikuti `max_score` tugasnya.
- **Notifikasi:** selalu lewat relasi milik pengguna login. Kolom `data` tidak dikembalikan mentah: hanya ID dan teks tampilan yang keluar (tanpa nilai maupun feedback).

---

## POST `/auth/login`

| Field | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `identifier` | string | Ya | Email atau NIM/NIP |
| `password` | string | Ya | |

```bash
curl -X POST $BASE/auth/login -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"identifier":"10240000","password":"password"}'
```

**200**
```json
{
  "data": {
    "token": "1|suQhrjjHacSZzmRWPOH65jb0LsE963qcFW5BuZgHcc0da9cb",
    "token_type": "Bearer",
    "expires_at": "2026-10-07T09:07:45+00:00",
    "user": { "id": 3, "name": "Mahasiswa Demo", "email": "mahasiswa@kampuslms.test", "role": "mahasiswa", "nim_nip": "10240000" }
  }
}
```

**422** (sama untuk identitas tidak ada maupun password salah)
```json
{ "message": "Data yang diberikan tidak valid.", "errors": { "identifier": ["NIM/NIP atau password salah."] } }
```

**429** (percobaan ke-6 dalam semenit)
```json
{ "message": "Terlalu banyak permintaan. Coba lagi nanti." }
```
Header `Retry-After` berisi sisa detik.

## POST `/auth/logout`

Mencabut token yang sedang dipakai.

```bash
curl -X POST $BASE/auth/logout -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```
**200** `{ "message": "Berhasil keluar." }`. Setelah itu token yang sama menghasilkan 401.

## GET `/me`

```bash
curl $BASE/me -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```
**200**
```json
{ "data": { "id": 3, "name": "Mahasiswa Demo", "email": "mahasiswa@kampuslms.test", "role": "mahasiswa", "nim_nip": "10240000" } }
```
**401** `{ "message": "Tidak terautentikasi." }`

## GET `/courses`

Dosen: MK yang diampu. Mahasiswa: MK yang diikuti (memuat `enrolled_at`). Admin: semua.

| Query | Tipe | Keterangan |
|---|---|---|
| `page` | integer | Halaman, default 1 |
| `per_page` | integer | 1 sampai 50, default 15 |

```bash
curl "$BASE/courses?per_page=10" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```
**200**
```json
{
  "data": [
    {
      "id": 1, "code": "SI2216", "name": "Pariatur quam iste", "description": "Nam qui soluta ...",
      "sks": 4, "status": "active",
      "lecturer": { "id": 2, "name": "Dosen Demo" },
      "enrolled_at": "2026-08-05 21:07:38"
    }
  ],
  "meta": { "current_page": 1, "last_page": 1, "total": 3 }
}
```

## GET `/courses/{course}`

Detail beserta jumlah materi dan tugas. Untuk mahasiswa, tugas draft tidak dihitung.

```bash
curl $BASE/courses/1 -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```
**200**
```json
{ "data": { "id": 1, "code": "SI2216", "name": "Pariatur quam iste", "description": "Nam qui soluta ...", "sks": 4, "status": "active",
  "lecturer": { "id": 2, "name": "Dosen Demo" }, "materials_count": 0, "assignments_count": 2 } }
```
**403** `{ "message": "Anda tidak memiliki akses ke sumber daya ini." }` (mahasiswa tidak terdaftar atau dosen bukan pengampu)
**404** `{ "message": "Sumber daya tidak ditemukan." }`

## GET `/courses/{course}/assignments`

| Query | Tipe | Keterangan |
|---|---|---|
| `status` | `draft` atau `published` | Filter; mahasiswa selalu hanya `published` |
| `page`, `per_page` | integer | Seperti `/courses` |

```bash
curl "$BASE/courses/1/assignments?status=published" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN_DOSEN_A"
```
**200 (dosen/admin)** menyertakan `submissions_count`:
```json
{
  "data": [
    { "id": 1, "course_id": 1, "created_by": 2, "title": "Tugas At aut dicta", "instructions": "Sit beatae ...",
      "due_at": "2026-09-16T06:23:52+00:00", "max_score": 100, "allow_late": true, "status": "published", "submissions_count": 21 }
  ],
  "meta": { "current_page": 1, "last_page": 1, "total": 2 }
}
```
**200 (mahasiswa)** menyertakan `submission_status` (`belum` / `terkumpul` / `terlambat`) milik mahasiswa itu sendiri sebagai pengganti `submissions_count`.

## POST `/assignments`

| Field | Tipe | Wajib | Default |
|---|---|---|---|
| `course_id` | integer | Ya | |
| `title` | string (maks 255) | Ya | |
| `instructions` | string | Ya | |
| `due_at` | datetime | Ya | |
| `max_score` | integer 1 sampai 100 | Tidak | 100 |
| `allow_late` | boolean | Tidak | true |
| `status` | `draft` / `published` | Tidak | `draft` |

```bash
curl -X POST $BASE/assignments -H "Accept: application/json" -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN_DOSEN_A" \
  -d '{"course_id":1,"title":"Tugas Baru","instructions":"Kerjakan soal 1-5","due_at":"2030-01-01 23:59:00"}'
```
**201**
```json
{ "data": { "id": 16, "course_id": 1, "created_by": 2, "title": "Tugas Baru", "instructions": "Kerjakan soal 1-5",
  "due_at": "2030-01-01T23:59:00+00:00", "max_score": 100, "allow_late": true, "status": "draft" } }
```
**403** token mahasiswa, atau dosen yang bukan pengampu `course_id`
**422** `{ "message": "Data yang diberikan tidak valid.", "errors": { "title": ["The title field is required."] } }`

## PUT / PATCH `/assignments/{assignment}`

`PUT` mewajibkan semua field (`title`, `instructions`, `due_at`, `max_score`, `allow_late`, `status`); `PATCH` hanya memvalidasi field yang dikirim. `course_id` tidak bisa diubah (tugas tidak boleh pindah mata kuliah).

```bash
curl -X PATCH $BASE/assignments/16 -H "Accept: application/json" -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN_DOSEN_A" -d '{"title":"Judul Revisi","status":"published"}'
```
**200** objek tugas seperti pada POST. **403** bukan dosen pengampu. **422** validasi gagal (misalnya `PUT` tidak lengkap).

## DELETE `/assignments/{assignment}`

```bash
curl -X DELETE $BASE/assignments/16 -H "Accept: application/json" -H "Authorization: Bearer $TOKEN_DOSEN_A"
```
**204** tanpa isi. **403** bukan dosen pengampu.
**409** `{ "message": "Tugas tidak dapat dihapus karena sudah ada pengumpulan mahasiswa." }`

## GET `/courses/{course}/materials`

Daftar materi satu mata kuliah (metadata saja; berkas diunduh lewat route web yang dilindungi `MaterialPolicy@view`). Otorisasi `MaterialPolicy@viewAny`: admin, dosen pengampu (semua status MK), mahasiswa terdaftar (MK bukan `draft`). Urut per pertemuan (`session`), materi tanpa pertemuan di akhir.

| Query | Tipe | Keterangan |
|---|---|---|
| `page`, `per_page` | integer | Seperti `/courses` |

```bash
curl "$BASE/courses/1/materials?per_page=2" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN"
```
**200**
```json
{
  "data": [
    { "id": 1, "course_id": 1, "title": "Materi Pertemuan 1 - SI3576", "description": "Molestias ea aperiam ...", "type": "file",
      "original_name": "pertemuan-1.pdf", "file_size": 601, "mime_type": "application/pdf", "external_url": null,
      "uploader": { "id": 2, "name": "Dosen Demo" }, "created_at": "2026-11-17T17:25:18+08:00" },
    { "id": 6, "course_id": 1, "title": "Referensi: Dokumentasi Laravel 12", "description": "In fugit qui ...", "type": "link",
      "original_name": null, "file_size": null, "mime_type": null, "external_url": "https://laravel.com/docs/12.x",
      "uploader": { "id": 2, "name": "Dosen Demo" }, "created_at": "2026-11-17T17:25:18+08:00" }
  ],
  "meta": { "current_page": 1, "last_page": 4, "total": 7 }
}
```
**403** mahasiswa tidak terdaftar / MK `draft` bagi mahasiswa / dosen bukan pengampu. **404** MK tidak ada.

## GET `/assignments/{assignment}/submissions`

Hanya dosen pengampu mata kuliah tugas tersebut (sesuai kontrak). Route dibatasi `role:dosen`, lalu `SubmissionPolicy@viewAny` memastikan dosen itu pemilik MK-nya. Mahasiswa (termasuk pemilik pengumpulan) dan dosen lain mendapat 403; mahasiswa melihat pengumpulannya sendiri lewat halaman web.

| Query | Tipe | Keterangan |
|---|---|---|
| `page`, `per_page` | integer | Seperti `/courses` |

```bash
curl "$BASE/assignments/1/submissions?per_page=1" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN_DOSEN_A"
```
**200**
```json
{
  "data": [
    { "id": 2, "assignment_id": 1, "original_name": "earum-occaecati-veniam-suscipit.pdf", "file_size": 3810871, "note": null,
      "submitted_at": "2026-10-29T14:39:34+08:00", "is_late": false,
      "student": { "id": 7, "name": "Prof. Buddy Reichel", "nim_nip": "10252724" }, "grade": null }
  ],
  "meta": { "current_page": 1, "last_page": 16, "total": 16 }
}
```
`grade` berisi objek nilai (lihat `PUT /submissions/{submission}/grade`) bila sudah dinilai.
**403** mahasiswa, admin, atau dosen bukan pengampu. **404** tugas tidak ada.

## POST `/assignments/{assignment}/submissions`

Mengumpulkan jawaban. Hanya mahasiswa (dosen dan admin 403). Request berupa `multipart/form-data`. Otorisasi (403) dicek **sebelum** validasi (422).

Syarat di `SubmissionPolicy@create` (semuanya 403 bila gagal): mahasiswa terdaftar, MK `active`, tugas `published`, belum pernah mengumpulkan (pengumpulan tidak dapat diubah), dan bila lewat batas waktu maka `allow_late` harus aktif. Keterlambatan dicatat di `is_late`.

| Field | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `file` | berkas | Ya | `pdf, doc, docx, zip, txt`, maks 10 MB (isi dan ekstensi dicek) |
| `note` | string (maks 1000) | Tidak | |

`user_id` **tidak** dibaca dari request: pengumpul selalu pemilik token.

```bash
curl -X POST $BASE/assignments/2/submissions -H "Accept: application/json" -H "Authorization: Bearer $TOKEN_MHS" \
  -F "file=@jawaban.pdf" -F "note=Catatan saya"
```
**201**
```json
{ "data": { "id": 117, "assignment_id": 2, "original_name": "jawaban.pdf", "file_size": 14, "note": "Catatan saya",
  "submitted_at": "2026-11-17T17:26:55+08:00", "is_late": false } }
```
**403** bukan mahasiswa, tidak terdaftar, tugas draft, batas waktu lewat tanpa `allow_late`, atau sudah mengumpulkan
**409** dua permintaan serentak lolos bersamaan (pagar terakhir: unique `assignment_id` + `user_id`)
**422** `{ "message": "Data yang diberikan tidak valid.", "errors": { "file": ["Pilih berkas jawaban yang akan dikumpulkan."] } }`

## PUT `/submissions/{submission}/grade`

Memberi nilai dan umpan balik. Otorisasi `SubmissionPolicy@grade`: hanya dosen pengampu, hanya MK `active`. Admin dan mahasiswa 403. Menilai ulang menimpa nilai sebelumnya (satu nilai per pengumpulan). Mahasiswa diberi notifikasi hanya bila nilai baru atau skor/feedback benar-benar berubah.

| Field | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `score` | number | Ya | 0 sampai `max_score` tugas tersebut |
| `feedback` | string (maks 2000) | Ya | |

```bash
curl -X PUT $BASE/submissions/117/grade -H "Accept: application/json" -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN_DOSEN_A" -d '{"score":88.5,"feedback":"Bagus sekali"}'
```
**201** (nilai pertama) atau **200** (nilai ulang)
```json
{ "data": { "id": 71, "submission_id": 117, "score": 88.5, "feedback": "Bagus sekali",
  "graded_at": "2026-11-17T17:26:55+08:00", "grader": { "id": 2, "name": "Dosen Demo" } } }
```
**403** mahasiswa, admin, dosen bukan pengampu, atau MK tidak `active`
**422** `{ "message": "Data yang diberikan tidak valid.", "errors": { "score": ["Nilai maksimal 100."] } }`

## GET `/notifications`

Notifikasi milik pengguna yang sedang login, terbaru dulu. Bukan data orang lain, karena query lewat relasi `user->notifications()`.

| Query | Tipe | Keterangan |
|---|---|---|
| `filter` | `semua` atau `belum` | Default `semua`; `belum` = belum dibaca |
| `page`, `per_page` | integer | Seperti `/courses` |

```bash
curl "$BASE/notifications?filter=belum&per_page=2" -H "Accept: application/json" -H "Authorization: Bearer $TOKEN_MHS"
```
**200** (`meta.unread_count` = total belum dibaca di seluruh halaman)
```json
{
  "data": [
    { "id": "385fc7ed-a435-405a-b9e2-39757a266096", "kind": "grade_received", "message": "Nilai tugas \"Tugas Quo vitae qui\" diperbarui.",
      "course": "Occaecati recusandae voluptatum", "assignment_id": 2, "submission_id": 117,
      "is_read": false, "read_at": null, "created_at": "2026-11-17T17:26:55+08:00" }
  ],
  "meta": { "current_page": 1, "last_page": 1, "total": 2, "unread_count": 2 }
}
```
`assignment_id` dan `submission_id` bernilai `null` bila tidak relevan; klien membentuk tautan sendiri dari ID itu. **422** `filter` bukan `semua`/`belum`.

## POST `/notifications/{id}/read`

Menandai satu notifikasi milik sendiri sebagai dibaca. Idempoten: memanggil ulang tetap 200.

```bash
curl -X POST $BASE/notifications/385fc7ed-a435-405a-b9e2-39757a266096/read -H "Accept: application/json" -H "Authorization: Bearer $TOKEN_MHS"
```
**200** objek notifikasi seperti di atas dengan `"is_read": true` dan `read_at` terisi.
**404** `{ "message": "Sumber daya tidak ditemukan." }` bila ID tidak ada, bukan UUID, **atau milik orang lain** (sengaja 404, bukan 403).

---

## Pengujian

- **Skrip curl (server sungguhan):** `bash scripts/test-api.sh`. Login otomatis, empat kondisi otorisasi per endpoint, plus cek user enumeration dan kebocoran `password`. Hasil terakhir: **42 lulus, 0 gagal**.
- **PHPUnit:** `php artisan test` (termasuk `tests/Feature/ApiV1Test.php`: 401/403/404/422/429, N+1, draft tersembunyi, token dicabut). Hasil terakhir: seluruh suite **177 lulus**. Enam endpoint tambahan diuji di `tests/Feature/ApiV1EndpointBaruTest.php` (30 tes: 401, 403/404, IDOR, validasi, N+1); skrip curl di atas belum memuat keenam endpoint itu.

### Matriks otorisasi

| Endpoint | 1. Tanpa token | 2. Mahasiswa (endpoint dosen / bukan peserta) | 3. Dosen A ke data Dosen B | 4. Hak benar |
|---|---|---|---|---|
| `GET /courses/{id}` | 401 | 403 | 403 | 200 |
| `GET /courses/{id}/assignments` | 401 | 403 | 403 | 200 |
| `POST /assignments` | 401 | 403 | 403 | 201 |
| `PUT/PATCH /assignments/{id}` | 401 | 403 | 403 | 200 |
| `DELETE /assignments/{id}` | 401 | 403 | 403 | 204 |
| `GET /courses/{id}/materials` | 401 | 403 | 403 | 200 |
| `GET /assignments/{id}/submissions` | 401 | 403 | 403 | 200 |
| `POST /assignments/{id}/submissions` | 401 | 403 (dosen) | 403 (mahasiswa tidak terdaftar) | 201 |
| `PUT /submissions/{id}/grade` | 401 | 403 | 403 | 201 / 200 |
| `GET /notifications` | 401 | n/a (semua peran login) | hanya milik sendiri | 200 |
| `POST /notifications/{id}/read` | 401 | n/a (semua peran login) | **404** (milik orang lain) | 200 |
