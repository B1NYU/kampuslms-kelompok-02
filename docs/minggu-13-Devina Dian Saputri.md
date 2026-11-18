# Minggu 13 — READ: Riwayat Git Saya

**Nama:** Devina Dian Saputri (NIM 10241022)    
**Repo:** `kampuslms-kelompok-02` (akun GitHub: Devina Dian)

Perintah yang dipakai: `git log --oneline --graph --all`, `git shortlog -sn`, `git log --pretty='%an' | sort | uniq -c`, `git log --stat`.

---

## 1. Berapa commit atas nama saya? Bandingkan dengan anggota lain

`git shortlog -sn` menampilkan **117 commit** atas nama saya. Dari jumlah itu, 36 adalah merge commit ("Merge pull request") dan **81 adalah commit yang saya tulis sendiri**.

| Anggota | Commit tulisan sendiri | Total (dengan merge) |
|---|---:|---:|
| **Devina Dian** | **81** | **117** |
| Clara Uenike | 50 | 70 |
| Desta Rifqi | 41 | 53 |
| Baihaqi Abimanyu | 29 | 64 |
| Calvin Winata | 19 | 23 |

Saya memiliki commit terbanyak di kelompok (81 dari 220 commit tulisan sendiri). Jumlah ini besar sebagian karena saya sering commit **satu berkas per commit** lewat editor web GitHub, bukan karena pekerjaannya lebih banyak. Beberapa anggota muncul dengan lebih dari satu nama di Git (misalnya Desta dengan tiga nama), jadi angkanya sudah saya gabung berdasarkan email.

## 2. Adakah minggu di mana saya tidak punya commit sama sekali?

Setiap minggu saya punya commit, **kecuali 12–18 Oktober 2026**. Minggu itu adalah **minggu UTS**, saat perubahan pada repo tidak boleh dilakukan sebelum dinilai secara keseluruhan. Di minggu itu seluruh anggota kelompok juga tidak punya commit.

## 3. Berapa persen pesan commit saya yang mengikuti Conventional Commits?

**11 dari 81 commit, atau sekitar 14%** (merge commit tidak dihitung karena pesannya dibuat otomatis oleh GitHub). Untuk seluruh kelompok angkanya 68 dari 220 atau sekitar 31%.

Sebanyak 70 pesan yang tidak sesuai adalah pesan bawaan editor web GitHub ("Create …" / "Update …") dari 6 Oktober ke belakang. **Sejak 8 Oktober, semua commit saya (11 dari 11) sudah mengikuti format**, misalnya `fix:`, `feat:`, `refactor:`, dan `docs:`.

## 4. Satu commit saya yang pesannya buruk

**Commit `52c29c6` (29 September):** `Update tugas.blade.php`

Pesan ini hanya menyebut nama berkas dan tidak menjelaskan apa yang berubah. Padahal isinya cukup besar: mengganti pilihan mata kuliah palsu (SI101, SI102, SI103) dengan data dari database, menambah form buat/ubah/hapus tugas, serta menambah tampilan untuk data kosong dan pesan error.

**Seharusnya ditulis:**
`feat(dosen): ganti data dummy halaman tugas dengan data database dan tambah form kelola tugas`

## 5. Commit terbesar saya. Apakah seharusnya dipecah?

Commit terbesar saya adalah **`61ba4fa` (27 Oktober)**: `feat: implementasi halaman notifikasi, rekap nilai, alur penilaian, dan penambahan test feature`. Besarnya 2.461 baris (2.430 ditambah, 31 dihapus) pada 17 berkas.

**Seharusnya dipecah.** Pesannya sendiri sudah menyebut empat hal berbeda. Sebaiknya dijadikan beberapa commit terpisah, misalnya:
1. halaman notifikasi,
2. rekap nilai dosen dan mahasiswa,
3. perbaikan alur penilaian,
4. test feature, dipisah per topik.

Alasannya, commit sebesar ini sulit direview dan sulit dibatalkan sebagian kalau ada satu bagian yang bermasalah. Sesuai modul, satu fitur sebaiknya satu commit atau satu PR.
