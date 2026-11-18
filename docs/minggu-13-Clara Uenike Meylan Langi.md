# Evaluasi Riwayat Git — Read Week 13

* **Nama:** Clara Uenike Meylan Langi
* **NIM:** 10241018
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
    35  10241020-destakun
    29  B1NYU
    19  calvinwinata21
     8  destaa-chan
```

**Jawaban:**
Untuk commit pekerjaan kode (*non-merge*), terdapat **50 commit** atas nama akun Git saya, `clara-uenike`.

Total keseluruhan commit *non-merge* kelompok pada branch utama adalah 221 commit. Saya memiliki kontribusi sekitar 22% dari total seluruh commit.

---

## 2. Adakah minggu di mana Anda tidak punya commit sama sekali?

**Perintah Git:**
```bash
git log --author="clara-uenike" --no-merges --date=short --pretty=format:"%ad | %an | %s"
```

**Distribusi Commit Non-Merge per Pekan (24 Agustus – 18 November 2026):**
* **Minggu 01** (24 Agt – 30 Agt 2026): 2 commit
* **Minggu 02** (31 Agt – 06 Sep 2026): 7 commit
* **Minggu 03** (07 Sep – 13 Sep 2026): 12 commit
* **Minggu 04** (14 Sep – 20 Sep 2026): 6 commit
* **Minggu 05** (21 Sep – 27 Sep 2026): 4 commit
* **Minggu 06** (28 Sep – 04 Okt 2026): 1 commit
* **Minggu 07** (05 Okt – 11 Okt 2026): 12 commit
* **Minggu 08 (12 Okt – 18 Okt 2026): 0 commit (Kosong)**
* **Minggu 09 (19 Okt – 25 Okt 2026): 0 commit (Kosong)**
* **Minggu 10** (26 Okt – 01 Nov 2026): 3 commit
* **Minggu 11** (02 Nov – 08 Nov 2026): 1 commit
* **Minggu 12** (09 Nov – 15 Nov 2026): 2 commit
* **Minggu 13 (16 Nov – 22 Nov 2026): 0 commit (Sedang berjalan per 18 Nov)**

**Jawaban:**
**Ya, ada.** Terdapat jeda aktivitas coding selama 2 minggu berturut-turut pada **Minggu ke-8 dan Minggu ke-9** yaitu rentang tanggal **12 Oktober s/d 25 Oktober 2026**. 

Periode kosong tersebut bertepatan dengan masa pelaksanaan Ujian Tengah Semester (UTS) dan evaluasi Milestone UTS (M2), di mana pengerjaan kelompok difokuskan pada persiapan demo dan asesmen individu. Setelah UTS selesai, aktivitas commit coding kembali aktif pada Minggu ke-10 (27 Oktober 2026).

---

## 3. Berapa persen pesan commit yang mengikuti Conventional Commits?

**Perintah Git:**
```powershell
git log --author="clara-uenike" --no-merges --pretty=format:"%s" | Select-String -Pattern "^(feat|fix|docs|style|refactor|perf|test|build|ci|chore|revert)(\(.+\))?:"
```

**Daftar Commit yang Mengikuti Standar (27 Commit):**
```text
f04f956 chore: hapus file catatan checkpoint dari repository
1da5d6d refactor: migrasi styling portal dosen dan halaman bersama ke tailwind css murni
882c94c refactor: migrasi styling portal mahasiswa dan navbar ke tailwind css murni
5466f99 refactor: migrasi halaman portal admin dan rekap nilai ke tailwind css murni
a65c69f feat: implement tailwind admin dashboard and navbar
4234d9d chore: ignore week 9 check point files
401b55c chore: abaikan file uts-minggu-8.md dari repository
8f01d75 fix: perbaiki variabel submissions di AssignmentController, otorisasi StoreCourseRequest, dan tambah docs keamanan
b059db6 refactor(admin): convert CRUD forms to modal dialogs and expand tables to full-width
bae14de style(navbar): gunakan safe route check untuk rute admin.nilai
ba0e492 chore: samakan routes/web.php dan StoreCourseRequest dengan main agar dihandle rekan tim
9fbcea6 style(admin): perapihan header halaman pendaftaran mahasiswa
c54ae41 feat(admin): buat AdminGradeController dan refactor route rekap nilai ke standar MVC
b184030 feat(admin): integrasi data dinamis dashboard, eliminasi data dummy rekap nilai, dan perapihan tata letak
7be5301 feat(admin): implementasi antarmuka rekap nilai portal admin dan penyesuaian mahasiswa dosen
4143662 feat(admin): implementasi antarmuka manajemen tugas perkuliahan portal admin (F6)
476d706 feat(admin): implementasi antarmuka manajemen materi perkuliahan portal admin (F5)
b3ae032 feat(dosen): redesign table kelola mahasiswa, header luar, dan update error pages
5b2b36f feat(ui): selaraskan palet dosen & admin, update modal buat tugas, dan rapikan topbar
d944e32 refactor(admin): optimize imports and add course controller docblock
63c427f chore: untrack modulminggu4-7.md and spesifikasi_proyek, add to .gitignore
d3e9d89 feat(admin): adjust course pagination limit and refine dashboard quick actions layout
6690b2f feat(admin): filter search query string dan pagination mata kuliah
ee80f6a feat: sinkronisasi database matkul, integrasi nilai riil, dan perapian UI portal mahasiswa
f272299 feat: tambah tampilan 404 kustom dan dokumen minggu-03 clara
de114dd feat(ui): perbarui tampilan courses, dosen, mahasiswa dan komponen UI
56a2a85 feat(views): update dashboard & views admin, dosen, mahasiswa, serta perbaikan routes
```

**Jawaban:**
Dari total **50 commit non-merge** atas nama saya:
* **27 commit (54,0%)** telah mengikuti aturan baku *Conventional Commits* (menggunakan format `<type>(<scope>): <pesan>` seperti `feat:`, `feat(admin):`, `refactor:`, `style(admin):`, `fix:`, dan `chore:`).
* **23 commit (46,0%)** belum mengikuti aturan tersebut. Seluruh commit ini berasal dari minggu pertama pengerjaan yaitu Minggu 1 sampai awal Minggu 3 yang masih memakai pesan default web/upload GitHub (`Create ...`, `Update ...`, `Delete ...`, `Add files via upload`) atau kalimat deskriptif bebas tanpa prefix tipe.

Pada 30 commit non-merge terakhir (sejak pertengahan Minggu 3 hingga Minggu 12), **27 commit di antaranya (90%)** sudah konsisten menerapkan format *Conventional Commits*. Perbaikan ini saya terapkan setelah mendapatkan komentar pada penilaian tugas terkait commit pada repositosi kelompok.

---

## 4. Temukan satu commit Anda sendiri yang pesannya buruk. Tulis ulang seharusnya bagaimana.

**Perintah Git:**
```bash
git show b2259b2 --stat
```

**Output:**
```text
commit b2259b205fed39107bb93f6603845ec85889de0e
Author: clara-uenike <10241018@student.itk.ac.id>
Date:   Tue Sep 8 20:57:03 2026 +0800

    Update show.blade.php

 resources/views/courses/show.blade.php | 371 ++++++++++++++++++++++++++++++---
 1 file changed, 342 insertions(+), 29 deletions(-)
```

**Jawaban:**
Salah satu commit *non-merge* saya yang memiliki pesan paling buruk adalah commit **`b2259b2`** pada tanggal 8 September 2026:

* **Pesan Lama:** `Update show.blade.php`
* **Alasan Mengapa Pesan Ini Buruk:**
  1. **Minim Konteks & Informasi:** Pesan commit hanya mencantumkan nama file generik (`show.blade.php`) tanpa menjelaskan tujuan fungsional dari perubahan tersebut.
  2. **Skala Perubahan Tidak Dijelaskan:** Commit ini sebenarnya merupakan perubahan besar yang mengubah 371 baris kode (+342 baris baru), yaitu membangun antarmuka lengkap halaman detail mata kuliah yang mencakup informasi umum matkul, silabus materi, dan daftar tugas mahasiswa.
  3. **Terjadi Kekeliruan Anggota Kelompok:** Pesan ini tidak memberi tahu apakah perubahan tersebut memperbaiki bug, styling, atau menambahkan fitur baru.
* **Penulisan Ulang Seharusnya (Conventional Commits):**
  ```text
  feat(matkul): implementasi tampilan detail mata kuliah lengkap dengan silabus materi dan daftar tugas
  ```

---

## 5. `git log --stat` — temukan commit terbesar Anda. Apakah seharusnya dipecah?

**Perintah Git:**
```bash
git log --author="clara-uenike" --no-merges --shortstat --oneline
git show 5466f99 --stat
```

**Output:**
```text
commit 5466f992f26262039a377231bd6a8568300fa4f1
Author: clara-uenike <10241018@student.itk.ac.id>
Date:   Tue Oct 27 20:28:44 2026 +0800

    refactor: migrasi halaman portal admin dan rekap nilai ke tailwind css murni

 app/Http/Controllers/Dosen/GradingController.php   |   23 +-
 .../Controllers/Dosen/RekapNilaiController.php     |   12 +-
 resources/css/admin/admin.common.css               | 1083 --------------------
 resources/css/admin/admin.dashboard.css            |  351 -------
 resources/css/admin/admin.materi.css               |  836 ---------------
 resources/css/admin/admin.matkul.css               |  195 ----
 resources/css/admin/admin.nilai.css                |  570 -----------
 resources/css/admin/admin.pendaftaran.css          |  125 ---
 resources/css/admin/admin.pengguna.css             |   78 --
 resources/css/admin/admin.tugas.css                | 1071 -------------------
 resources/css/dosen/dosen.penilaian.css            |   18 +
 resources/js/admin/admin.pengguna.js               |   82 +-
 resources/views/admin/admin.dashboard.blade.php    |   50 -
 resources/views/admin/admin.materi.blade.php       |  428 ++++----
 resources/views/admin/admin.matkul.blade.php       |  445 ++++----
 resources/views/admin/admin.nilai.blade.php        |  517 +++++-----
 resources/views/admin/admin.pendaftaran.blade.php  |  352 ++++---
 resources/views/admin/admin.pengguna.blade.php     |  372 ++++---
 resources/views/admin/admin.tugas.blade.php        |  421 ++++----
 resources/views/dosen/mahasiswa.blade.php          |   12 +-
 resources/views/dosen/penilaian.blade.php          |   88 +-
 resources/views/dosen/rekap-nilai.blade.php        |    9 +-
 resources/views/submissions/show.blade.php         |  124 ++-
 vite.config.js                                     |    9 -
 24 files changed, 1751 insertions(+), 5520 deletions(-)
```

**Jawaban:**
Berdasarkan data `git log --stat`, commit terbesar non-merge atas nama saya adalah commit **`5466f99`** (`refactor: migrasi halaman portal admin dan rekap nilai ke tailwind css murni`) pada tanggal 27 Oktober 2026.

Commit ini menyentuh **24 file** sekaligus, dengan **1.751 baris penambahan (*insertions*)** dan **5.520 baris penghapusan (*deletions*)**, menghasilkan total perubahan sebesar **7.271 baris kode**. Dengan skala perubahan sebesar itu maka seharusnya commit tersebut dipecah.

**Alasan Mengapa Perlu Dipecah:**
1. **Skala Perubahan Terlalu Masif untuk *Code Review*:** Perubahan lebih dari 7.000 baris kode dalam 24 file membuat rekan satu tim sangat kesulitan melakukan peninjauan kode (*pull request review*) secara teliti, sehingga resiko adanya penilain yang terlewat.
2. **Pencampuran Berbagai Tanggung Jawab:** Commit ini mencampurkan beberapa modul yang berbeda:
   * Menghapus 4.000+ baris CSS kustom lawas di `resources/css/admin/` dan pembaruan `vite.config.js`.
   * Memodifikasi template blade pada 6 halaman portal admin yang berbeda (materi, matkul, nilai, pendaftaran, pengguna, tugas).
   * Memodifikasi template blade portal dosen dan tampilan submission mahasiswa.
   * Mengubah logika PHP Controller (`GradingController.php`, `RekapNilaiController.php`) serta skrip JavaScript (`admin.pengguna.js`).

**Saran Pemecahan Commit :**
Commit besar gabungan ini seharusnya dipecah menjadi setidaknya 4 atau 5 commit terpisah:
1. `refactor(css): remove legacy admin custom stylesheets and update vite build config` (fokus pada penghapusan file CSS kustom lama).
2. `refactor(admin): migrate course and material views to tailwind` (fokus pada modul manajemen mata kuliah dan materi admin).
3. `refactor(admin): migrate assignment, grade, and user views to tailwind` (fokus pada modul tugas, nilai, dan pengguna).
4. `refactor(dosen): migrate lecturer grade and student views to tailwind` (fokus pada template portal dosen).
5. `fix(dosen): adjust grading and recap controllers with view payload` (fokus pada perubahan logika PHP controller dan JS).
