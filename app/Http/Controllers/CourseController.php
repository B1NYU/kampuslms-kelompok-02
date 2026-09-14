<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CourseController extends Controller
{

    public function index(): View
    {
        // Data statis sementara list mata kuliah
        $matakuliah = [
            [
                'id' => 1,
                'kode' => 'SI101',
                'nama' => 'Pemrograman Web',
                'sks' => 3,
                'dosen' => 'Dr. Budi Santoso',
            ],
            [
                'id' => 2,
                'kode' => 'SI102',
                'nama' => 'Basis Data',
                'sks' => 3,
                'dosen' => 'Siti Rahma, M.Kom.',
            ],
            [
                'id' => 3,
                'kode' => 'SI103',
                'nama' => 'Analisis dan Perancangan Sistem',
                'sks' => 3,
                'dosen' => 'Andi Wijaya, M.Kom.',
            ],
        ];

        return view('courses.index', compact('matakuliah'));
    }

    public function show(int $mata_kuliah): View
    {
        $daftarMataKuliah = [
            1 => [
                'id' => 1,
                'kode' => 'SI101',
                'nama' => 'Pemrograman Web',
                'sks' => 3,
                'dosen' => 'Dr. Budi Santoso',
                'deskripsi' => 'Mempelajari dasar-dasar pengembangan aplikasi web menggunakan PHP dan Laravel.',
            ],
            2 => [
                'id' => 2,
                'kode' => 'SI102',
                'nama' => 'Basis Data',
                'sks' => 3,
                'dosen' => 'Siti Rahma, M.Kom.',
                'deskripsi' => 'Mempelajari konsep basis data relasional, perancangan database, dan SQL.',
            ],
            3 => [
                'id' => 3,
                'kode' => 'SI103',
                'nama' => 'Analisis dan Perancangan Sistem',
                'sks' => 3,
                'dosen' => 'Andi Wijaya, M.Kom.',
                'deskripsi' => 'Mempelajari proses analisis kebutuhan dan perancangan arsitektur sistem informasi.',
            ],
        ];

        // Cari mata kuliah berdasarkan ID ($mata_kuliah) yang dikirim dari URL
        $mataKuliah = $daftarMataKuliah[$mata_kuliah] ?? null;

        // Jika ID tidak ada, tampilkan halaman 404 (Not Found)
        abort_if($mataKuliah === null, 404);

        $daftarNilaiSemua = [
            1 => [
                'stats' => [
                    'rata_rata' => '89.5',
                    'indeks' => 'A',
                    'predikat' => 'Sangat Memuaskan',
                    'tugas_dinilai' => '5 dari 7',
                    'bobot_tercapai' => '55%',
                ],
                'items' => [
                    [
                        'pertemuan' => 'Minggu 1',
                        'tipe' => 'Tugas Mandiri',
                        'judul' => 'Setup Repo GitHub & Laporan Bacaan 1',
                        'tanggal_kumpul' => '18 Feb 2026, 21:40 WITA',
                        'status' => 'Dinilai',
                        'bobot' => '10%',
                        'nilai' => 95,
                        'feedback' => 'Setup repositori sangat rapi, struktur branch sesuai konvensi Git flow praktikum.',
                    ],
                    [
                        'pertemuan' => 'Minggu 2',
                        'tipe' => 'Tugas Praktikum',
                        'judul' => 'Tugas Praktikum: Route Parameter & Show',
                        'tanggal_kumpul' => '25 Feb 2026, 20:15 WITA',
                        'status' => 'Dinilai',
                        'bobot' => '15%',
                        'nilai' => 90,
                        'feedback' => 'Route list dan controller method tersusun bersih, model binding bekerja optimal.',
                    ],
                    [
                        'pertemuan' => 'Minggu 2',
                        'tipe' => 'Kuis Teori',
                        'judul' => 'Kuis 1: Konsep Routing & Controller di Laravel',
                        'tanggal_kumpul' => '27 Feb 2026, 14:30 WITA',
                        'status' => 'Dinilai',
                        'bobot' => '10%',
                        'nilai' => 85,
                        'feedback' => 'Skor 8.5/10. Pelajari kembali mekanisme Controller Dependency Injection.',
                    ],
                    [
                        'pertemuan' => 'Minggu 3',
                        'tipe' => 'Milestone 1',
                        'judul' => 'Tugas 1: Database Migration & Seeder 8 Tabel',
                        'tanggal_kumpul' => '06 Mar 2026, 22:10 WITA',
                        'status' => 'Dinilai',
                        'bobot' => '20%',
                        'nilai' => 92,
                        'feedback' => 'Skema 8 tabel relasional sangat solid, foreign key dan seeder berjalan lancar.',
                    ],
                    [
                        'pertemuan' => 'Minggu 4',
                        'tipe' => 'Latihan Praktikum',
                        'judul' => 'Implementasi Relasi Model Course & User',
                        'tanggal_kumpul' => '12 Mar 2026, 19:45 WITA',
                        'status' => 'Dinilai',
                        'bobot' => '10%',
                        'nilai' => 88,
                        'feedback' => 'Relasi hasMany dan belongsTo sudah tepat, eager loading diterapkan dengan baik.',
                    ],
                    [
                        'pertemuan' => 'Minggu 5',
                        'tipe' => 'Tugas Praktikum',
                        'judul' => 'Latihan CRUD Mata Kuliah & Form Request',
                        'tanggal_kumpul' => '14 Mar 2026, 11:30 WITA',
                        'status' => 'Menunggu Penilaian',
                        'bobot' => '10%',
                        'nilai' => null,
                        'feedback' => 'Tugas telah dikumpulkan tepat waktu. Sedang dalam proses evaluasi oleh asisten/dosen.',
                    ],
                    [
                        'pertemuan' => 'Minggu 8',
                        'tipe' => 'Ujian UTS',
                        'judul' => 'Ujian Tengah Semester (UTS): Presentasi Project',
                        'tanggal_kumpul' => 'Jadwal: Minggu 8',
                        'status' => 'Belum Dibuka',
                        'bobot' => '25%',
                        'nilai' => null,
                        'feedback' => 'Sesi interview dan live demo aplikasi kelompok.',
                    ],
                ]
            ],
            2 => [
                'stats' => [
                    'rata_rata' => '91.2',
                    'indeks' => 'A',
                    'predikat' => 'Sangat Memuaskan',
                    'tugas_dinilai' => '4 dari 6',
                    'bobot_tercapai' => '50%',
                ],
                'items' => [
                    [
                        'pertemuan' => 'Minggu 1',
                        'tipe' => 'Tugas Mandiri',
                        'judul' => 'Perancangan Skema ERD Konseptual',
                        'tanggal_kumpul' => '17 Feb 2026, 20:30 WITA',
                        'status' => 'Dinilai',
                        'bobot' => '10%',
                        'nilai' => 95,
                        'feedback' => 'Diagram ERD sangat komprehensif, kardinalitas dan batasan entitas tepat.',
                    ],
                    [
                        'pertemuan' => 'Minggu 2',
                        'tipe' => 'Tugas Praktikum',
                        'judul' => 'Normalisasi Basis Data 1NF sampai 3NF',
                        'tanggal_kumpul' => '24 Feb 2026, 21:10 WITA',
                        'status' => 'Dinilai',
                        'bobot' => '15%',
                        'nilai' => 88,
                        'feedback' => 'Dekomposisi tabel memenuhi kaidah BCNF/3NF tanpa data redundancy.',
                    ],
                    [
                        'pertemuan' => 'Minggu 2',
                        'tipe' => 'Kuis Teori',
                        'judul' => 'Kuis 1: Aljabar Relasional & DDL SQL',
                        'tanggal_kumpul' => '26 Feb 2026, 15:00 WITA',
                        'status' => 'Dinilai',
                        'bobot' => '10%',
                        'nilai' => 90,
                        'feedback' => 'Hasil kuis sangat baik, pemahaman sintaks DDL sangat kuat.',
                    ],
                    [
                        'pertemuan' => 'Minggu 3',
                        'tipe' => 'Milestone 1',
                        'judul' => 'Implementasi Skema Fisik & Constraint SQL',
                        'tanggal_kumpul' => '05 Mar 2026, 23:00 WITA',
                        'status' => 'Dinilai',
                        'bobot' => '20%',
                        'nilai' => 92,
                        'feedback' => 'Constraint foreign key dan index pada kolom unik diimplementasikan dengan sangat baik.',
                    ],
                    [
                        'pertemuan' => 'Minggu 4',
                        'tipe' => 'Tugas Praktikum',
                        'judul' => 'Optimasi Query SQL dengan Indexing & EXPLAIN',
                        'tanggal_kumpul' => '13 Mar 2026, 16:20 WITA',
                        'status' => 'Menunggu Penilaian',
                        'bobot' => '15%',
                        'nilai' => null,
                        'feedback' => 'Telah diunggah tepat waktu, menunggu proses penilaian dosen.',
                    ],
                ]
            ],
            3 => [
                'stats' => [
                    'rata_rata' => '90.5',
                    'indeks' => 'A',
                    'predikat' => 'Sangat Memuaskan',
                    'tugas_dinilai' => '4 dari 6',
                    'bobot_tercapai' => '45%',
                ],
                'items' => [
                    [
                        'pertemuan' => 'Minggu 1',
                        'tipe' => 'Tugas Mandiri',
                        'judul' => 'Identifikasi Kebutuhan Fungsional & Non-Fungsional',
                        'tanggal_kumpul' => '19 Feb 2026, 22:00 WITA',
                        'status' => 'Dinilai',
                        'bobot' => '10%',
                        'nilai' => 92,
                        'feedback' => 'Analisis kebutuhan sistem sangat detail dan terstruktur dengan format standar IEEE.',
                    ],
                    [
                        'pertemuan' => 'Minggu 2',
                        'tipe' => 'Tugas Praktikum',
                        'judul' => 'Pemodelan Use Case Diagram & Activity Diagram',
                        'tanggal_kumpul' => '26 Feb 2026, 21:30 WITA',
                        'status' => 'Dinilai',
                        'bobot' => '15%',
                        'nilai' => 90,
                        'feedback' => 'Use case narrative lengkap dan alur swimlane pada activity diagram sangat jelas.',
                    ],
                    [
                        'pertemuan' => 'Minggu 2',
                        'tipe' => 'Kuis Teori',
                        'judul' => 'Kuis 1: Konsep SDLC & Agile Methodology',
                        'tanggal_kumpul' => '28 Feb 2026, 13:00 WITA',
                        'status' => 'Dinilai',
                        'bobot' => '10%',
                        'nilai' => 88,
                        'feedback' => 'Pemahaman iterasi Scrum dan definisi sprint backlog sangat baik.',
                    ],
                    [
                        'pertemuan' => 'Minggu 3',
                        'tipe' => 'Milestone 1',
                        'judul' => 'Penyusunan Dokumen SRS (Software Requirements)',
                        'tanggal_kumpul' => '07 Mar 2026, 23:45 WITA',
                        'status' => 'Dinilai',
                        'bobot' => '20%',
                        'nilai' => 95,
                        'feedback' => 'Dokumen SRS sangat profesional, mencakup semua skenario pengguna dan kamus data.',
                    ],
                ]
            ],
        ];

        $nilaiData = $daftarNilaiSemua[$mata_kuliah] ?? $daftarNilaiSemua[1];

        // Dikirim dengan nama 'mataKuliah' dan 'nilaiData' ke view show
        return view('courses.show', compact('mataKuliah', 'nilaiData'));
    }

    public function nilai(int $mata_kuliah): View
    {
        request()->merge(['tab' => 'nilai']);
        return $this->show($mata_kuliah);
    }
}