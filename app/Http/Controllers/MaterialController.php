<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Unduh materi. Dipakai bersama oleh mahasiswa, dosen, dan admin.
 *
 * Route model binding hanya membuktikan materi itu ADA; hak akses dicek
 * MaterialPolicy@view (admin, dosen pengampu, atau mahasiswa terdaftar di MK
 * yang bukan draft). Tanpa pengecekan ini, mengganti ID di URL membuka
 * materi mata kuliah lain (IDOR).
 */
class MaterialController extends Controller
{
    public function download(Request $request, Material $material): StreamedResponse|RedirectResponse
    {
        Gate::authorize('view', $material);

        // Materi berupa tautan: arahkan ke URL-nya, tetapi hanya http/https
        // (mencegah javascript:, data:, dan skema berbahaya lain).
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

        // download() = Content-Disposition: attachment, jadi berkas tidak dieksekusi di browser.
        return Storage::disk('local')->download($material->file_path, $material->original_name);
    }
}
