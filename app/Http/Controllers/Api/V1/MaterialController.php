<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\MaterialCollection;
use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MaterialController extends Controller
{
    /**
     * GET /api/v1/courses/{course}/materials?page=&per_page=
     * Otorisasi: MaterialPolicy@viewAny (admin, dosen pengampu, mahasiswa terdaftar di MK
     * yang bukan draft). Hanya metadata; berkas diunduh lewat route web (policy yang sama).
     */
    public function index(Request $request, Course $course): MaterialCollection
    {
        Gate::authorize('viewAny', [Material::class, $course]);

        $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $perPage = max(1, min(50, $request->integer('per_page', 15)));

        $materials = $course->materials()
            ->with('uploader:id,name')              // 1 query untuk semua materi, bukan 1 per baris
            ->orderByRaw('session IS NULL')         // materi tanpa pertemuan di akhir
            ->orderBy('session')
            ->orderBy('id')
            ->paginate($perPage);

        return new MaterialCollection($materials);
    }
}
