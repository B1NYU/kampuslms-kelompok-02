# Evaluasi Riwayat Git — Read Week 13

* **Nama:** Calvin Hidayat Winatajaya
* **NIM:** 10241016
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
    27  calvinwinata21
     7  destaa-chan
```

**Jawaban:**
Total terdapat **27 commit** atas nama akun Git saya (`calvinwinata21`) pada seluruh riwayat branch proyek (atau **19 commit** jika dihitung khusus commit non-merge yang telah terintegrasi ke branch utama `origin/main`). 

Jika dibandingkan dengan anggota tim lainnya dari total 229 commit keseluruhan tim, saya berada di **peringkat ke-5** dengan persentase kontribusi sebesar **11,79%** (atau 8,60% di branch utama). Rincian kontribusi tim secara keseluruhan adalah:
1. **Devina Dian**: 81 commit (35,37%)
2. **clara-uenike**: 50 commit (21,83%)
3. **Desta Rifqi Aunur Rahman** (`10241020-destakun` & `destaa-chan`): 42 commit (18,34%)
4. **Baihaqi Abimanyu** (`B1NYU`): 29 commit (12,66%)
5. **Calvin Hidayat Winatajaya** (`calvinwinata21`): 27 commit (11,79%)

---

## 2. Adakah minggu di mana Anda tidak punya commit sama sekali?

**Perintah Git:**
```bash
git log --all --author="calvin" --no-merges --date=short --pretty=format:"%ad | %an | %s"
```

**Jawaban:**
Secara umum saya memiliki aktivitas commit yang cukup aktif sejak awal perkuliahan di akhir Agustus hingga November 2026. Namun, terdapat **2 periode minggu** di mana saya tidak memiliki commit sama sekali:
1. **Rentang 11–26 Oktober 2026 (±2 minggu)**: Tidak ada aktivitas commit setelah commit `94764df` (10 Oktober) hingga `f8412fe` (27 Oktober). Rentang waktu ini bertepatan dengan masa tenang dan pelaksanaan Ujian Tengah Semester (UTS / Minggu 8).
2. **Rentang 11–18 November 2026 (±1 minggu)**: Tidak ada commit baru setelah commit `25e078e` (10 November) hingga saat audit minggu ke-13 ini dilakukan.

---

## 3. Berapa persen pesan commit yang mengikuti Conventional Commits?

**Perintah Git:**
```powershell
git log --all --author="calvin" --no-merges --pretty=format:"%s" | Select-String -Pattern "^(feat|fix|docs|style|refactor|perf|test|build|ci|chore|revert)(\(.+\))?:" -CaseSensitive
```

**Output:**
```text
feat: implementasi penyesuaian tampilan responsif mobile pada landing page dan dashboard
feat: implementasi server side pagination menggunakan paginate dan withQueryStrin
fix: pemisahan kode javascript tampilan ke folder resources/js per role
fix: penyempurnaan UI mode mahasiswa dosen dan halaman lupa sandi
feat: pembaruan antarmuka portal dosen pada kelola mahasiswa unggah materi penilaian dan navbar
fix: penyederhanaan form login general dan penyesuaian tata letak frontend
feat: penyelarasan UI role, navigasi, dan restrukturisasi CSS
```

**Jawaban:**
Dari total 27 commit non-merge pada seluruh branch, terdapat **7 commit (25,93%)** yang mematuhi aturan baku *Conventional Commits* secara ketat. Jika dihitung khusus pada branch utama (`origin/main` sebanyak 19 commit non-merge), persentasenya adalah **7 dari 19 commit (36,84%)**.

Pada fase awal proyek (Minggu 1–4), sebagian besar commit masih menggunakan pesan deskriptif non-standar maupun format default web upload (`update README`, `summary`, `update tampilan`). Namun terdapat perbaikan alur kerja yang sangat signifikan pada fase lanjutan proyek: terhitung sejak 29 September hingga 10 November 2026, **100% commit terakhir saya (7 dari 7 commit berturut-turut)** secara konsisten dan disiplin telah menerapkan *Conventional Commits* dengan prefix `feat:` dan `fix:`.

---

## 4. Temukan satu commit Anda sendiri yang pesannya buruk. Tulis ulang seharusnya bagaimana.

**Perintah Git:**
```bash
git show ff5a37f --stat
```

**Output:**
```text
commit ff5a37fb90d1e34d05dbd75b6674bf17a92a3e3d
Author: calvinwinata21 <10241016@student.itk.ac.id>
Date:   Tue Sep 1 18:44:13 2026 +0800

    summary

 docs/image/calvinn-1.png                     | Bin 0 -> 6424 bytes
 docs/image/calvinn-2.png                     | Bin 0 -> 41914 bytes
 docs/image/calvinn-3.png                     | Bin 0 -> 24941 bytes
 docs/image/calvinn-4.png                     | Bin 0 -> 24667 bytes
 docs/image/calvinn.png                       | Bin 0 -> 38961 bytes
 docs/minggu-01-Calvin-Hidayat-Winatajayaa.md |  78 +++++++++++++++++++++++++++
 6 files changed, 78 insertions(+)
```

**Jawaban:**
Salah satu commit saya yang pesannya buruk adalah commit **`ff5a37f`** (pada tanggal 1 September 2026):
* **Pesan Lama:** `summary`
* **Alasan Buruk:** Pesan hanya terdiri dari satu kata yang sangat ambigu dan tidak memberikan konteks sama sekali. Pesan tersebut tidak menjelaskan berkas apa yang ditambahkan, untuk tujuan apa, serta tidak mencantumkan tipe konvensi (*type/scope*). Padahal isi commit tersebut adalah penambahan dokumen laporan refleksi mandiri Minggu 1 beserta berkas gambar tangkapan layar pendukung.
* **Penulisan Ulang (Conventional Commits):**
  ```text
  docs(week-01): add week 1 individual report and evidence screenshots for Calvin
  ```

---

## 5. `git log --stat` — temukan commit terbesar Anda. Apakah seharusnya dipecah?

**Perintah Git:**
```bash
git log --all --author="calvin" --no-merges --shortstat --oneline
git show 8f35395 --stat
```

**Output:**
```text
commit 8f35395faa65a3151e4e7f2e61643b094d87d062 (origin/feat/Penyelarasan-UI-dan-Restrukturasi-CSS)
Author: calvinwinata21 <10241016@student.itk.ac.id>
Date:   Tue Sep 29 14:08:03 2026 +0800

    feat: penyelarasan UI role, navigasi, dan restrukturisasi CSS

 package-lock.json                                 |    2 +-
 resources/css/admin/admin.common.css              |  943 +++++++++++
 resources/css/admin/admin.dashboard.css           | 1102 +------------
 resources/css/admin/admin.matkul.css              |  172 +-
 resources/css/admin/admin.pendaftaran.css         |  112 +-
 resources/css/admin/admin.pengguna.css            |   78 +
 resources/css/dosen/dosen.common.css              |  963 +++++++++++
 resources/css/dosen/dosen.dashboard.css           |  Bin 67168 -> 6693 bytes
 resources/css/dosen/dosen.mahasiswa.css           |   13 +
 resources/css/dosen/dosen.materi.css              |  220 +++
 resources/css/dosen/dosen.penilaian.css           |   90 +
 resources/css/dosen/dosen.tugas.css               |  133 ++
 resources/css/footer.css                          |   36 +-
 resources/css/mahasiswa/anggota.css               |   54 +
 resources/css/mahasiswa/mahasiswa.matkul.css      | 1829 +++++++++++++++++++++
 resources/css/sidebar.css                         |   94 +-
 resources/dosen/dosen.dashboard.css               | 1508 -----------------
 resources/views/admin/admin.dashboard.blade.php   |   39 -
 resources/views/admin/admin.matkul.blade.php      |    2 +-
 resources/views/admin/admin.pendaftaran.blade.php |    2 +-
 resources/views/admin/admin.pengguna.blade.php    |    2 +-
 resources/views/admin/dashboard.blade.php         |   39 -
 resources/views/components/layout.blade.php       |   11 -
 resources/views/components/navbar-admin.blade.php |   14 +-
 resources/views/components/navbar-dosen.blade.php |    4 +-
 resources/views/courses/index.blade.php           |    2 +-
 resources/views/courses/show.blade.php            |    2 +-
 resources/views/dosen/dashboard.blade.php         |  230 ---
 resources/views/dosen/dosen.dashboard.blade.php   |    1 +
 resources/views/dosen/mahasiswa.blade.php         |    5 +-
 resources/views/dosen/materi.blade.php            |    5 +-
 resources/views/dosen/penilaian.blade.php         |    5 +-
 resources/views/dosen/tugas.blade.php             |    5 +-
 resources/views/mahasiswa/anggota.blade.php       |   19 +-
 vite.config.js                                    |   21 +-
 35 files changed, 4732 insertions(+), 3025 deletions(-)
```

**Jawaban:**
Berdasarkan keluaran `git log --stat`, commit terbesar saya adalah commit **`8f35395`** (`feat: penyelarasan UI role, navigasi, dan restrukturisasi CSS`), yang memodifikasi **35 berkas** dengan total **4.732 baris penambahan (*insertions*)** dan **3.025 baris penghapusan (*deletions*)** (total perputaran perubahan mencapai 7.757 baris kode).

**Apakah seharusnya dipecah?**
**Ya, sangat disarankan untuk dipecah.**
Commit ini berskala terlalu besar (*mega-commit*) dan mencampurkan beberapa domain tanggung jawab perubahan sekaligus: restrukturisasi besar-besaran file CSS per role, perombakan view blade komponen navigasi (navbar & sidebar), serta pembaruan konfigurasi bundler `vite.config.js`. Menggabungkan semuanya dalam satu commit meningkatkan risiko konflik git dan menyulitkan rekan tim saat melakukan *code review*.

Idealnya commit tersebut dipecah menjadi beberapa *atomic commit*:
1. `refactor(css): restrukturisasi file CSS modular per role (admin, dosen, mahasiswa)`
2. `feat(nav): penyelarasan layout navbar dan sidebar navigasi multi-role`
3. `build(vite): update vite.config.js dan package-lock.json untuk bundling aset CSS baru`
