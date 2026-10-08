<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMaterialRequest;
use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        // Hanya mata kuliah yang diampu dosen yang sedang login.
        $courses = $request->user()
            ->taughtCourses()
            ->with('materials')   // eager load agar tidak N+1
            ->orderBy('code')
            ->get();

        return view('dosen.materi', compact('courses'));
    }

    public function store(StoreMaterialRequest $request)
    {
        $data   = $request->validated();
        $course = Course::findOrFail($data['course_id']);

        // TODO (Policy): pastikan MK ini diampu dosen yang sedang login.

        $payload = [
            'uploaded_by' => $request->user()->id,
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'type'        => $data['type'],
        ];

        if ($data['type'] === 'file') {
            $file = $request->file('file');

            $payload['original_name'] = $file->getClientOriginalName();
            $payload['file_size']     = $file->getSize();
            $payload['mime_type']     = $file->getMimeType();
            // Disk 'local' = privat (tidak bisa diakses lewat URL publik).
            $payload['file_path']     = $file->store("materials/{$course->id}", 'local');
        } else {
            $payload['external_url'] = $data['external_url'];
        }

        $course->materials()->create($payload);

        return redirect()
            ->route('dosen.materi')
            ->with('success', "Materi \"{$data['title']}\" berhasil diunggah.");
    }

    public function destroy(Material $material)
    {
        // TODO (Policy): hanya dosen pengampu MK ini yang boleh menghapus.

        if ($material->type === 'file' && $material->file_path) {
            Storage::disk('local')->delete($material->file_path);
        }

        $material->delete();

        return redirect()
            ->route('dosen.materi')
            ->with('success', 'Materi berhasil dihapus.');
    }
}
