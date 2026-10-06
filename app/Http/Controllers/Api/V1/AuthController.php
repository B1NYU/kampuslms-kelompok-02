<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserSummaryResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const TOKEN_TTL_HOURS = 12;

    /**
     * POST /api/v1/auth/login  (publik)
     * Identitas boleh email atau NIM/NIP, sama seperti login web.
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'password'   => ['required', 'string', 'max:255'],
        ]);

        // Dua pembatas: per identitas+IP (5/menit) dan per IP saja (20/menit).
        $perIdentity = 'api-login|' . strtolower($data['identifier']) . '|' . $request->ip();
        $perIp       = 'api-login-ip|' . $request->ip();

        if (RateLimiter::tooManyAttempts($perIdentity, 5) || RateLimiter::tooManyAttempts($perIp, 20)) {
            $detik = max(RateLimiter::availableIn($perIdentity), RateLimiter::availableIn($perIp));

            abort(429, "Terlalu banyak percobaan login. Coba lagi dalam {$detik} detik.", [
                'Retry-After' => $detik,
            ]);
        }

        $field = filter_var($data['identifier'], FILTER_VALIDATE_EMAIL) ? 'email' : 'nim_nip';

        // Pengguna nonaktif (soft delete) otomatis tidak ditemukan.
        $user = User::where($field, $data['identifier'])->first();

        // Hash::check SELALU dijalankan, walau pengguna tidak ada, supaya waktu respons
        // tidak membocorkan apakah identitas itu terdaftar.
        $hash  = $user?->password ?? Hash::make(Str::random(16));
        $valid = Hash::check($data['password'], $hash);

        if (! $user || ! $valid) {
            RateLimiter::hit($perIdentity, 60);
            RateLimiter::hit($perIp, 60);

            // Pesan SAMA untuk "tidak terdaftar" dan "password salah".
            throw ValidationException::withMessages([
                'identifier' => ['NIM/NIP atau password salah.'],
            ]);
        }

        RateLimiter::clear($perIdentity);

        $token = $user->createToken('api', ['*'], now()->addHours(self::TOKEN_TTL_HOURS));

        return response()->json([
            'data' => [
                'token'      => $token->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $token->accessToken->expires_at?->toIso8601String(),
                'user'       => (new UserSummaryResource($user))->resolve(),
            ],
        ]);
    }

    /** POST /api/v1/auth/logout — mencabut token yang sedang dipakai. */
    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    /** GET /api/v1/me — profil + role milik pemanggil. */
    public function me(Request $request): UserSummaryResource
    {
        return new UserSummaryResource($request->user());
    }
}
