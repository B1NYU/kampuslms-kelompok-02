# Evaluasi Riwayat Git — Read Week 13

* **Nama:** Desta Rifqi Aunur Rahman
* **NIM:** 10241020
* **Mata Kuliah:** Pemrograman Web B

---

## 1. Berapa commit atas nama Anda? Bandingkan dengan anggota lain.

**Perintah Git:**
```bash
git shortlog -sn --all --no-merges
```

**Output:**
```text
    81  Devina Dian
    50  clara-uenike
    34  10241020-destakun
    28  B1NYU
    19  calvinwinata21
     8  destaa-chan
```

**Jawaban:**
Total terdapat **42 commit** atas nama saya, yang terbagi ke dalam dua konfigurasi akun Git yaitu `10241020-destakun` (34 commit) dan `destaa-chan` (8 commit). Jika dibandingkan dengan anggota tim lainnya (dari total 220 commit keseluruhan tim), saya berada di **peringkat ke-3** dengan kontribusi sebesar **19,1%**—berada di bawah **Devina Dian** (81 commit) dan **clara-uenike** (50 commit), serta unggul di atas **B1NYU** (28 commit) dan **calvinwinata21** (19 commit).

---

## 2. Adakah minggu di mana Anda tidak punya commit sama sekali?

**Perintah Git:**
```bash
git log --author="desta" --no-merges --date=short --pretty=format:"%ad | %an | %s"
```

**Jawaban:**
Secara keseluruhan saya konsisten melakukan commit setiap minggu sejak proyek dimulai pada 29 Agustus hingga 17 November 2026. Namun, terdapat **2 minggu** di mana saya tidak memiliki commit sama sekali, yaitu pada rentang tanggal **4–12 September 2026** dan **11–19 Oktober 2026**.

---

## 3. Berapa persen pesan commit yang mengikuti Conventional Commits?

**Perintah Git:**
```powershell
git log --author="desta" --no-merges --pretty=format:"%s" | Select-String -Pattern "^(feat|fix|docs|style|refactor|perf|test|build|ci|chore|revert)(\(.+\))?:" -CaseSensitive
```

**Output:**
```text
fix: perbaiki case sensitivity Linux, validasi session tugas, dan aktifkan test di CI
feat: complete week 12 deployment setup, deployment docs, and fix Mahasiswa PSR-4 casing
refactor(admin): gabungkan query dashboard dari ±22 menjadi ±9
perf(week-11): optimize rekap nilai queries, add N+1 guard, and cleanup env calls
feat(attendance): implementasi fitur presensi kelas berbasis kode unik dengan batas masa berlaku
feat(week9): implement secure file management, storage cleanup, and timezone config
feat(authz): add test-authz script and fix policy null-checks
feat: implement full CRUD materials for admin and dosen roles
docs: minggu 4 & minggu 5
```

**Jawaban:**
Dari total **41 commit** non-merge pada branch utama (gabungan dua akun), hanya **9 commit (21,95%)** yang mengikuti aturan baku *Conventional Commits* secara ketat (atau **10 commit / 24,4%** jika satu commit berawalan huruf kapital `Feat: CRUD Membuat Tugas Admin` ikut dihitung).

Sebanyak 31 commit awal masih memakai pesan default bawaan GitHub Web Upload (`Update...` dan `Add files via upload`), sedangkan pada 10 commit terakhir setelah menggunakan Git CLI secara penuh, tingkat kepatuhan terhadap *Conventional Commits* mencapai **90%**.

---

## 4. Temukan satu commit Anda sendiri yang pesannya buruk. Tulis ulang seharusnya bagaimana.

**Perintah Git:**
```bash
git log --author="desta" --no-merges --grep="Add files via upload" --stat
```

**Output:**
```text
commit 2341dc6dd6a1e036d67b1e48d57a757833765dc2 (origin/gambar-erd)
Author: destaa-chan <destarifqi579@gmail.com>
Date:   Tue Sep 15 01:15:38 2026 +0800

    Add files via upload

 docs/image/erd02.jpeg | Bin 0 -> 192596 bytes
 1 file changed, 0 insertions(+), 0 deletions(-)
```

**Jawaban:**
Salah satu commit saya yang pesannya buruk adalah commit **`2341dc6`** (atas nama `destaa-chan` pada 15 September 2026):

* **Pesan Lama:** `Add files via upload`
* **Alasan Buruk:** Pesan ini merupakan teks default bawaan fitur unggah web GitHub yang sama sekali tidak menjelaskan jenis perubahan maupun konteks file yang ditambahkan (`docs/image/erd02.jpeg`).
* **Penulisan Ulang (Conventional Commits):**
  ```text
  docs(erd): add Entity Relationship Diagram image (erd02.jpeg)
  ```

---

## 5. `git log --stat` — temukan commit terbesar Anda. Apakah seharusnya dipecah?

**Perintah Git:**
```bash
git log --author="desta" --no-merges --shortstat --oneline
git show 83041e3 --stat
```

**Output:**
```text
commit 83041e390f0773bd1735f50d048ce72c9c4d5ca0 (origin/feat/presensi-kode-unik, feat/presensi-kode-unik)
Author: 10241020-destakun <destarifqi579@gmail.com>
Date:   Mon Oct 26 18:11:44 2026 +0800

    feat(attendance): implementasi fitur presensi kelas berbasis kode unik dengan batas masa berlaku

 .../Controllers/Dosen/AttendanceController.php     | 256 ++++++++
 .../Mahasiswa/MahasiswaCourseController.php        |  23 +-
 .../Controllers/mahasiswa/AttendanceController.php | 232 +++++++
 app/Models/Attendance.php                          |  53 ++
 app/Models/AttendanceSession.php                   | 155 +++++
 app/Models/Course.php                              |   5 +
 app/Models/User.php                                |  10 +
 app/Policies/AttendanceSessionPolicy.php           |  52 ++
 .../2026_10_26_000100_create_attendance_tables.php |  51 ++
 ... (total: 16 files changed, 3061 insertions(+), 23 deletions(-))
```

**Jawaban:**
Berdasarkan output `git log --stat`, commit terbesar saya adalah commit **`83041e3`** (`feat(attendance): implementasi fitur presensi kelas berbasis kode unik dengan batas masa berlaku`), yang mengubah **16 file** dengan **3.061 baris penambahan (*insertions*)** dan **23 baris penghapusan (*deletions*)**.

**Apakah seharusnya dipecah?**
**Ya, seharusnya dipecah.** Meskipun seluruh perubahan tersebut masih berada dalam satu lingkup fitur presensi, satu commit berisi lebih dari 3.000 baris menyulitkan proses *code review* dan *rollback* jika ditemukan bug pada bagian tertentu. Dari rincian filenya, commit ini mencampur berbagai lapisan arsitektur sekaligus sehingga idealnya dipecah menjadi beberapa *atomic commit*:
1. `feat(attendance): add attendance migrations, models, and relations` (untuk file migrasi dan model).
2. `feat(attendance): add policy and attendance controllers for dosen and mahasiswa` (untuk otorisasi dan *business logic* pada controller).
3. `feat(attendance): add attendance views, routes, and feature tests` (untuk tampilan antarmuka, rute, dan pengujian fitur).