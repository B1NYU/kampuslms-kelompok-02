<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function download(Material $material)
    {
        // TODO (Policy): hanya dosen pengampu, admin, atau mahasiswa yang terdaftar di MK ini.

        abort_unless(
            $material->type === 'file'
                && $material->file_path
                && Storage::disk('local')->exists($material->file_path),
            404
        );

        return Storage::disk('local')->download($material->file_path, $material->original_name);
    }
}
