# Dokumentasi API KampusLMS v1

- **Base URL:** `http://127.0.0.1:8000/api/v1` (`php artisan serve`)
- **Autentikasi:** Bearer token Laravel Sanctum dari `POST /auth/login` (berlaku 12 jam)
- **Header wajib:** `Accept: application/json`; tambahkan `Content-Type: application/json` untuk request ber-body
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
| POST | `/assignments` | Dosen pengampu MK tujuan | 201 |
| PUT / PATCH | `/assignments/{assignment}` | Dosen pengampu | 200 |
| DELETE | `/assignments/{assignment}` | Dosen pengampu | 204 |

## Kode status

| Kode | Arti di API ini |
|---|---|
| 200 / 201 / 204 | Berhasil / berhasil membuat / berhasil tanpa isi |
| 401 | Token tidak ada, salah, kedaluwarsa, atau sudah dicabut |
| 403 | Sudah login tetapi tidak berhak (peran salah atau bukan pemilik data) |
| 404 | Data tidak ditemukan |
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

---

## Pengujian

- **Skrip curl (server sungguhan):** `bash scripts/test-api.sh`. Login otomatis, empat kondisi otorisasi per endpoint, plus cek user enumeration dan kebocoran `password`. Hasil terakhir: **42 lulus, 0 gagal**.
- **PHPUnit:** `php artisan test` (termasuk `tests/Feature/ApiV1Test.php`: 401/403/404/422/429, N+1, draft tersembunyi, token dicabut). Hasil terakhir: **50 lulus**.

### Matriks otorisasi

| Endpoint | 1. Tanpa token | 2. Mahasiswa (endpoint dosen / bukan peserta) | 3. Dosen A ke data Dosen B | 4. Hak benar |
|---|---|---|---|---|
| `GET /courses/{id}` | 401 | 403 | 403 | 200 |
| `GET /courses/{id}/assignments` | 401 | 403 | 403 | 200 |
| `POST /assignments` | 401 | 403 | 403 | 201 |
| `PUT/PATCH /assignments/{id}` | 401 | 403 | 403 | 200 |
| `DELETE /assignments/{id}` | 401 | 403 | 403 | 204 |
