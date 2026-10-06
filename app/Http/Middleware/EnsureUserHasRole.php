<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // 1. Jika belum login (User NULL)
        if (! $user) {
            // Jika request ditujukan ke endpoint API (/api/*) atau meminta JSON, paksa kembalikan JSON 401
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.'
                ], 401);
            }

            return redirect()->guest(route('login'));
        }

        // 2. Jika sudah login, tetapi role tidak sesuai (403 Forbidden)
        if (! in_array($user->role, $roles, true)) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Anda tidak memiliki akses ke sumber daya ini.'
                ], 403);
            }

            abort(403, 'Peran Anda tidak diizinkan mengakses halaman ini.');
        }

        return $next($request);
    }
}