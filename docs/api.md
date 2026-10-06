# Dokumentasi API KampusLMS v1

- **Base URL:** `http://127.0.0.1:8000/api/v1`
- **Autentikasi:** Bearer token (Laravel Sanctum), didapat dari `POST /auth/login`
- **Header:** `Accept: application/json` (dan `Content-Type: application/json` untuk request ber-body)
- Contoh `curl` memakai format Windows CMD, sama seperti saat pengujian.

## Akun uji

| Nama | ID | Role | `identifier` | Password |
|---|---|---|---|---|
| Mahasiswa Demo | 3 | `mahasiswa` | `10240000` | `password` |
| Dosen Demo (Dosen A) | 2 | `dosen` | `NIP-000` | `password` |

Selain dua akun di atas, **Dosen B** didaftarkan secara manual lewat `php artisan tinker` dengan NIP `NIP-999` (ID 35, email `dosenB@kampuslms.test`, password `password123`) untuk keperluan uji otorisasi antar-dosen:

```bash
php artisan tinker --execute="\App\Models\User::create(['name' => 'Dosen B', 'email' => 'dosenB@kampuslms.test', 'role' => 'dosen', 'nim_nip' => 'NIP-999', 'password' => bcrypt('password123')]);"
```

Login Dosen B memakai `identifier` = `NIP-999`.

Pada contoh, `<TOKEN_MAHASISWA>` adalah token Mahasiswa Demo dan `<TOKEN_DOSEN_A>` adalah token Dosen Demo.

## Hasil pengujian

Setiap endpoint diuji dengan empat kondisi. Hasilnya:

| # | Kondisi | Endpoint yang diuji | Harapan | Hasil |
|---|---|---|---|---|
| 1 | Tanpa token | `GET /me` | 401 | **401** ✅ |
| 2 | Token mahasiswa mengakses endpoint dosen | `POST /assignments` | 403 | **403** ✅ |
| 3 | Token Dosen A mengakses data dosen lain | `PUT /assignments/1` | 403 | **403** ✅ |
| 4 | Token dan hak yang benar | `POST /assignments` (course 2), `PUT /assignments/16` | 200/201 | **sukses** ✅ (PUT `200`, POST mengembalikan tugas ID 16) |

Arti hasilnya:

- **401:** semua endpoint selain login berada di bawah `auth:sanctum`, jadi request tanpa token ditolak sebelum menyentuh controller.
- **403 (mahasiswa):** membuat tugas hanya boleh untuk role `dosen`. Mahasiswa ditolak dengan pesan `Anda tidak memiliki akses ke sumber daya ini.`
- **403 (Dosen A):** role-nya benar, tetapi tugas ID 1 bukan milik mata kuliah yang diampu Dosen A. Jadi otorisasi mengecek peran sekaligus kepemilikan.
- **200/201:** dosen pengampu (Dosen A, mata kuliah ID 2) boleh membuat dan mengubah tugas miliknya.

Dua temuan tambahan dari pengujian:

- Otorisasi (`403`) dicek lebih dulu daripada validasi (`422`).
- `PUT` bersifat penuh: semua field wajib dikirim. Mengirim hanya `title` atau hanya sebagian field menghasilkan `422`.

## Ringkasan endpoint

| Method | URI | Peran |
|---|---|---|
| `POST` | `/auth/login` | Publik |
| `GET` | `/me` | Semua yang login |
| `POST` | `/assignments` | `dosen` pengampu |
| `PUT` / `PATCH` | `/assignments/{assignment}` | `dosen` pengampu |

Dokumentasi ini hanya mencakup empat endpoint di atas, sesuai yang diuji.

---

## POST `/auth/login`

**Peran:** Publik.

| Field | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `identifier` | string | Ya | NIM atau NIP (bukan `nim_nip`) |
| `password` | string | Ya | |

**Request**

```bash
curl -X POST "http://127.0.0.1:8000/api/v1/auth/login" -H "Accept: application/json" -H "Content-Type: application/json" -d "{\"identifier\": \"10240000\", \"password\": \"password\"}"
```

**Sukses (`200`)**

```json
{
  "data": {
    "token": "1|ZucRvuGPYUMONKB5AdZNtwvQkjEoLd7zCjl6f0m2...",
    "token_type": "Bearer",
    "expires_at": "2026-10-06T20:57:46+00:00",
    "user": {
      "id": 3,
      "name": "Mahasiswa Demo",
      "email": "mahasiswa@kampuslms.test",
      "role": "mahasiswa",
      "nim_nip": "10240000"
    }
  }
}
```

Login Dosen A (`NIP-000`) menghasilkan `user.id = 2`, `role = "dosen"`.

**Gagal (`422`)** — field `nim_nip` dikirim, padahal yang benar `identifier`:

```json
{
  "message": "Data yang diberikan tidak valid.",
  "errors": {
    "identifier": ["The identifier field is required."],
    "password": ["The password field is required."]
  }
}
```

---

## GET `/me`

**Peran:** Semua pengguna yang login. Tidak ada parameter.

**Request tanpa token (kondisi 1)**

```bash
curl -s -o /dev/null -w "Status: %{http_code}\n" -H "Accept: application/json" "http://127.0.0.1:8000/api/v1/me"
```

**Gagal**

```
Status: 401
```

---

## POST `/assignments`

**Peran:** `dosen` pengampu mata kuliah tujuan.

| Field | Tipe | Wajib | Default |
|---|---|---|---|
| `course_id` | integer | Ya | |
| `title` | string | Ya | |
| `instructions` | string | Ya | |
| `due_at` | datetime (`YYYY-MM-DD HH:MM:SS`) | Ya | |
| `max_score` | integer | Tidak | `100` |
| `allow_late` | boolean | Tidak | `true` |
| `status` | string | Tidak | `draft` |

**Request sukses (kondisi 4, Dosen A, course 2)**

```bash
curl -X POST "http://127.0.0.1:8000/api/v1/assignments" -H "Accept: application/json" -H "Content-Type: application/json" -H "Authorization: Bearer <TOKEN_DOSEN_A>" -d "{\"course_id\": 2, \"title\": \"Tugas Uji Dosen A\", \"instructions\": \"Tes Status 200\", \"due_at\": \"2026-10-25 23:59:00\"}"
```

**Sukses**

```json
{
  "data": {
    "id": 16,
    "course_id": 2,
    "created_by": 2,
    "title": "Tugas Uji Dosen A",
    "instructions": "Tes Status 200",
    "due_at": "2026-10-25T23:59:00+00:00",
    "max_score": 100,
    "allow_late": true,
    "status": "draft"
  }
}
```

**Gagal `403` — token mahasiswa (kondisi 2)**

```bash
curl -s -o /dev/null -w "Status: %{http_code}\n" -H "Accept: application/json" -H "Content-Type: application/json" -H "Authorization: Bearer <TOKEN_MAHASISWA>" -X POST -d "{\"title\":\"Tugas\",\"course_id\":1}" "http://127.0.0.1:8000/api/v1/assignments"
```

```
Status: 403
```

Body-nya:

```json
{ "message": "Anda tidak memiliki akses ke sumber daya ini." }
```

**Gagal `422` — field salah nama** (`description` dan `due_date` dikirim, bukan `instructions` dan `due_at`):

```json
{
  "message": "Data yang diberikan tidak valid.",
  "errors": {
    "instructions": ["The instructions field is required."],
    "due_at": ["The due at field is required."]
  }
}
```

---

## PUT `/assignments/{assignment}`

**Peran:** `dosen` pengampu mata kuliah dari tugas tersebut. `PATCH` memakai rute yang sama.

Semua field wajib dikirim: `course_id`, `title`, `instructions`, `due_at`, `max_score`, `allow_late`, `status`.

**Request sukses (kondisi 4, Dosen A, tugas ID 16)**

```bash
curl -s -o /dev/null -w "Status: %{http_code}\n" -X PUT "http://127.0.0.1:8000/api/v1/assignments/16" -H "Accept: application/json" -H "Content-Type: application/json" -H "Authorization: Bearer <TOKEN_DOSEN_A>" -d "{\"course_id\": 2, \"title\": \"Revisi Tugas Uji Dosen A\", \"instructions\": \"Tes Status 200\", \"due_at\": \"2026-10-25 23:59:00\", \"max_score\": 100, \"allow_late\": true, \"status\": \"draft\"}"
```

**Sukses**

```
Status: 200
```

**Gagal `403` — tugas milik dosen lain (kondisi 3)**

```bash
curl -s -o /dev/null -w "Status: %{http_code}\n" -H "Accept: application/json" -H "Content-Type: application/json" -H "Authorization: Bearer <TOKEN_DOSEN_A>" -X PUT -d "{\"title\":\"Edit B\"}" "http://127.0.0.1:8000/api/v1/assignments/1"
```

```
Status: 403
```

**Gagal `422` — field tidak lengkap** (hanya `course_id`, `title`, `instructions`, `due_at`):

```json
{
  "message": "Data yang diberikan tidak valid.",
  "errors": {
    "max_score": ["The max score field is required."],
    "allow_late": ["The allow late field is required."],
    "status": ["The status field is required."]
  }
}
```
