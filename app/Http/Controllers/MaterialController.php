<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MaterialController extends Controller
{
    public function download(Request $request, Material $material): StreamedResponse|RedirectResponse
    {
        // Dosen tidak boleh menggunakan route unduh versi mahasiswa
        if ($request->routeIs('mahasiswa.materi.download') && (
            $request->user()->role === 'dosen' ||
            (method_exists($request->user(), 'hasRole') && $request->user()->hasRole('dosen')) ||
            (isset($request->user()->is_dosen) && $request->user()->is_dosen)
        )) {
            abort(403);
        }

        Gate::authorize('view', $material);

        if ($material->isLink()) {
            $scheme = strtolower((string) parse_url((string) $material->external_url, PHP_URL_SCHEME));
            abort_unless(in_array($scheme, ['http', 'https'], true), 404);

            return redirect()->away($material->external_url);
        }

        abort_unless(
            $material->file_path && Storage::disk('local')->exists($material->file_path),
            404,
            'Berkas materi tidak ditemukan di server. Hubungi dosen pengampu.'
        );

        return Storage::disk('local')->download($material->file_path, $material->original_name);
    }
}
