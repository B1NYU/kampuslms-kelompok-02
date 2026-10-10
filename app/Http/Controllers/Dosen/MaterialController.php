<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMaterialRequest;
use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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

        // Dosen pengampu, MK draft atau active (MK archived tidak boleh diubah).
        Gate::authorize('create', [Material::class, $course]);

        $payload = [
            'uploaded_by' => $request->user()->id,
            'session'     => $data['session'] ?? null,
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

    public function update(Request $request, Material $material)
    {
        Gate::authorize('update', $material);

        $rules = [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];

        if ($material->type === 'file') {
            // Berkas opsional: kosong = pertahankan berkas lama.
            $rules['file'] = ['nullable', 'file', 'mimes:pdf,ppt,pptx', 'max:51200'];
        } else {
            $rules['external_url'] = ['required', 'url', 'max:2048'];
        }

        // Error masuk ke bag 'editMaterial' agar tidak bentrok dengan modal unggah.
        $data = $request->validateWithBag('editMaterial', $rules);

        $payload = [
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
        ];

        if ($material->type === 'file') {
            if ($request->hasFile('file')) {
                $file = $request->file('file');

                // Hapus berkas lama setelah berkas baru berhasil disimpan.
                $newPath = $file->store("materials/{$material->course_id}", 'local');

                if ($material->file_path) {
                    Storage::disk('local')->delete($material->file_path);
                }

                $payload['file_path']     = $newPath;
                $payload['original_name'] = $file->getClientOriginalName();
                $payload['file_size']     = $file->getSize();
                $payload['mime_type']     = $file->getMimeType();
            }
        } else {
            $payload['external_url'] = $data['external_url'];
        }

        $material->update($payload);

        return redirect()
            ->route('dosen.materi')
            ->with('success', "Materi \"{$material->title}\" berhasil diperbarui.");
    }

    public function destroy(Material $material)
    {
        Gate::authorize('delete', $material);

        if ($material->type === 'file' && $material->file_path) {
            Storage::disk('local')->delete($material->file_path);
        }

        $material->delete();

        return redirect()
            ->route('dosen.materi')
            ->with('success', 'Materi berhasil dihapus.');
    }
}
