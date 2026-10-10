<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Semua error di /api/* selalu JSON, walau klien lupa header Accept.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, Throwable $e) => $request->is('api/*') || $request->expectsJson()
        );

        // 422 — validasi
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Data yang diberikan tidak valid.',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // 401 — belum login / token tidak valid
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Tidak terautentikasi.'], 401);
            }
        });

        // 409 — penolakan dari Policy karena data bertaut (MK/tugas yang masih punya data),
        //       lewat Response::denyWithStatus(409, ...). Pesannya sudah ramah pengguna:
        //       API → JSON {"message": ...}; web → kembali ke halaman asal dengan flash 'error'
        //       (konvensi yang sama dengan controller lama).
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e, Request $request) {
            if ($e->getStatusCode() !== 409) {
                return null;
            }

            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 409);
            }

            return back()->with('error', $e->getMessage());
        });

        // 403 / 429 / 405 — dari abort(), AuthorizationException, throttle, dll.
        // Selalu JSON bersih tanpa stack trace (walau APP_DEBUG=true).
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $message = match ($e->getStatusCode()) {
                403 => 'Anda tidak memiliki akses ke sumber daya ini.',
                405 => 'Metode HTTP tidak diizinkan untuk endpoint ini.',
                429 => $e->getMessage() && ! str_contains($e->getMessage(), 'Too Many')
                    ? $e->getMessage()
                    : 'Terlalu banyak permintaan. Coba lagi nanti.',
                default => null,
            };

            return $message === null
                ? null
                : response()->json(['message' => $message], $e->getStatusCode(), $e->getHeaders());
        });

        // 404 — ModelNotFoundException juga dikonversi menjadi NotFoundHttpException
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Sumber daya tidak ditemukan.'], 404);
            }
        });
    })->create();
