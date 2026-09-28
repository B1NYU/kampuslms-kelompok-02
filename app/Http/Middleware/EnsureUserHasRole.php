<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menjawab pertanyaan "boleh masuk area ini?" berdasarkan peran.
 *
 * Pemakaian: ->middleware('role:admin') atau ->middleware('role:admin,dosen')
 *
 * - Belum login            -> 401 (atau redirect ke login untuk request web biasa)
 * - Login, peran tidak cocok -> 403
 *
 * CATATAN: middleware ini TIDAK memeriksa kepemilikan data. Untuk itu
 * dibutuhkan pengecekan per-objek (scoped binding / Policy).
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            // Request JSON/AJAX (mis. fetch di halaman admin pengguna) mendapat 401.
            // Request browser biasa diarahkan ke halaman login.
            if ($request->expectsJson()) {
                abort(401, 'Anda belum login.');
            }

            return redirect()->guest(route('login'));
        }

        abort_unless(in_array($user->role, $roles, true), 403, 'Peran Anda tidak diizinkan mengakses halaman ini.');

        return $next($request);
    }
}
