<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * Tampilkan halaman CRUD mata kuliah beserta data awal.
     */
    public function index(): View
    {
        $matkulList = Course::with('lecturer')->orderByDesc('created_at')->get();
        $dosenList = User::where('role', 'dosen')->orderBy('name')->get(['id', 'name']);

        return view()->file(
            resource_path('views/admin/admin.matkul.blade.php'),
            compact('matkulList', 'dosenList')
        );
    }

    /**
     * Simpan mata kuliah baru (dipanggil via fetch/AJAX).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'        => ['required', 'string', 'max:20', 'unique:courses,code'],
            'name'        => ['required', 'string', 'max:255'],
            'sks'         => ['required', 'integer', 'min:1', 'max:6'],
            'lecturer_id' => ['required', 'exists:users,id'],
            'status'      => ['required', Rule::in(['draft', 'active', 'archived'])],
            'description' => ['nullable', 'string'],
        ]);

        $course = Course::create($validated);
        $course->load('lecturer');

        return response()->json([
            'message' => 'Mata kuliah berhasil ditambahkan.',
            'data'    => $this->transform($course),
        ], 201);
    }

    /**
     * Update mata kuliah yang sudah ada (dipanggil via fetch/AJAX).
     *
     * Parameter route sengaja tetap bernama $matkul (bukan $course) supaya
     * URL & route name lama (admin.matkul.*) tidak perlu diubah.
     */
    public function update(Request $request, Course $matkul)
    {
        $validated = $request->validate([
            'code'        => ['required', 'string', 'max:20', Rule::unique('courses', 'code')->ignore($matkul->id)],
            'name'        => ['required', 'string', 'max:255'],
            'sks'         => ['required', 'integer', 'min:1', 'max:6'],
            'lecturer_id' => ['required', 'exists:users,id'],
            'status'      => ['required', Rule::in(['draft', 'active', 'archived'])],
            'description' => ['nullable', 'string'],
        ]);

        $matkul->update($validated);
        $matkul->load('lecturer');

        return response()->json([
            'message' => 'Mata kuliah berhasil diperbarui.',
            'data'    => $this->transform($matkul),
        ]);
    }

    /**
     * Hapus mata kuliah (dipanggil via fetch/AJAX).
     */
    public function destroy(Course $matkul)
    {
        $matkul->delete();

        return response()->json([
            'message' => 'Mata kuliah berhasil dihapus.',
        ]);
    }

    /**
     * Bentuk payload JSON yang konsisten untuk frontend (termasuk nama dosen
     * hasil join, karena kolom 'dosen' string sudah tidak ada di skema baru).
     */
    private function transform(Course $course): array
    {
        return [
            'id'            => $course->id,
            'code'          => $course->code,
            'name'          => $course->name,
            'sks'           => $course->sks,
            'lecturer_id'   => $course->lecturer_id,
            'lecturer_name' => $course->lecturer?->name,
            'status'        => $course->status,
            'description'   => $course->description,
        ];
    }
}
