<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CourseController extends Controller
{

    private function data(): array
    {
        return [
            1 => [
                'id' => 1,
                'kode' => 'IF101',
                'nama' => 'Pemrograman Dasar',
                'sks' => 3,
                'dosen' => 'Dr. Andi Wijaya',
                'deskripsi' => 'Pengantar konsep pemrograman menggunakan bahasa Python, mencakup variabel, struktur kontrol, dan fungsi.',
            ],
            2 => [
                'id' => 2,
                'kode' => 'IF201',
                'nama' => 'Struktur Data',
                'sks' => 3,
                'dosen' => 'Dr. Siti Rahma',
                'deskripsi' => 'Mempelajari struktur data dasar seperti array, linked list, stack, queue, tree, dan graph beserta penerapannya.',
            ],
            3 => [
                'id' => 3,
                'kode' => 'IF301',
                'nama' => 'Basis Data',
                'sks' => 3,
                'dosen' => 'Prof. Budi Santoso',
                'deskripsi' => 'Konsep perancangan basis data relasional, normalisasi, entity-relationship diagram, dan bahasa SQL.',
            ],
        ];
    }

    public function index(): View
    {
        $matakuliah = array_values($this->data());

        return view('mata-kuliah.index', [
            'matakuliah' => $matakuliah,
        ]);
    }

    public function show(int $mata_kuliah): View
    {
        $data = $this->data();

        abort_if(!isset($data[$mata_kuliah]), 404);

        return view('mata-kuliah.show', [
            'mataKuliah' => $data[$mata_kuliah],
        ]);
    }
}
