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

        // Dikirim dengan nama 'mataKuliah' agar cocok dengan $mataKuliah['nama'] di view show
        return view('courses.show', compact('mataKuliah'));
    }
}