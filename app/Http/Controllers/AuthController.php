<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'password'   => ['required', 'string'],
        ]);

        // Batasi percobaan login: 5 kali per menit per kombinasi identitas + IP.
        $throttleKey = strtolower($data['identifier']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $detik = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'identifier' => "Terlalu banyak percobaan login. Coba lagi dalam {$detik} detik.",
            ]);
        }

        // Identitas boleh berupa email atau NIM/NIP.
        $field = filter_var($data['identifier'], FILTER_VALIDATE_EMAIL) ? 'email' : 'nim_nip';

        // Pengguna nonaktif (soft delete) otomatis tidak ditemukan oleh guard.
        if (! Auth::attempt([$field => $data['identifier'], 'password' => $data['password']])) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'identifier' => 'NIM/NIP atau password salah.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        // Cegah session fixation.
        $request->session()->regenerate();

        // Peran ditentukan oleh database, BUKAN oleh tab/input dari browser.
        return redirect()->intended(route($request->user()->dashboardRoute()));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Anda telah berhasil logout.');
    }
}
