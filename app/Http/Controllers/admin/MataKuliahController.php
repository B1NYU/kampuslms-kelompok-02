<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class MataKuliahController extends Controller
{
    /**
     * Tampilkan halaman CRUD mata kuliah beserta data awal.
     * Memakai view()->file() supaya konsisten dengan pola routing
     * admin lain di project ini (lihat routes/web.php).
     */
    public function index(): View
    {
        $matkulList = MataKuliah::orderByDesc('created_at')->get();

        return view()->file(
            resource_path('views/admin/admin.matkul.blade.php'),
            compact('matkulList')
        );
    }

    /**
     * Simpan mata kuliah baru (dipanggil via fetch/AJAX).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode'      => ['required', 'string', 'max:20', 'unique:mata_kuliahs,kode'],
            'nama'      => ['required', 'string', 'max:255'],
            'sks'       => ['required', 'integer', 'min:1', 'max:6'],
            'dosen'     => ['nullable', 'string', 'max:255'],
            'semester'  => ['nullable', 'string', 'max:50'],
            'status'    => ['required', Rule::in(['Aktif', 'Tidak Aktif'])],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $matkul = MataKuliah::create($validated);

        return response()->json([
            'message' => 'Mata kuliah berhasil ditambahkan.',
            'data'    => $matkul,
        ], 201);
    }

    /**
     * Update mata kuliah yang sudah ada (dipanggil via fetch/AJAX).
     */
    public function update(Request $request, MataKuliah $matkul)
    {
        $validated = $request->validate([
            'kode'      => ['required', 'string', 'max:20', Rule::unique('mata_kuliahs', 'kode')->ignore($matkul->id)],
            'nama'      => ['required', 'string', 'max:255'],
            'sks'       => ['required', 'integer', 'min:1', 'max:6'],
            'dosen'     => ['nullable', 'string', 'max:255'],
            'semester'  => ['nullable', 'string', 'max:50'],
            'status'    => ['required', Rule::in(['Aktif', 'Tidak Aktif'])],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $matkul->update($validated);

        return response()->json([
            'message' => 'Mata kuliah berhasil diperbarui.',
            'data'    => $matkul,
        ]);
    }

    /**
     * Hapus mata kuliah (dipanggil via fetch/AJAX).
     */
    public function destroy(MataKuliah $matkul)
    {
        $matkul->delete();

        return response()->json([
            'message' => 'Mata kuliah berhasil dihapus.',
        ]);
    }
}
