<?php

namespace App\Http\Controllers;

use App\Support\KataSandi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Ubah kata sandi untuk dosen/mahasiswa yang SUDAH login (tahu kata sandi lama).
 */
class AkunKataSandiController extends Controller
{
    public function edit(): View
    {
        return view('akun.kata-sandi');
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', 'different:current_password', KataSandi::aturan()],
        ], KataSandi::pesan() + [
            'current_password.required'         => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini salah.',
        ]);

        KataSandi::ganti($request->user(), $request->input('password'), $request->session()->getId());

        // Ganti ID sesi (cegah session fixation) tanpa memutus sesi yang sedang dipakai.
        $request->session()->regenerate();

        return redirect()->route('akun.kata-sandi')
            ->with('success', 'Kata sandi berhasil diubah. Perangkat lain yang sedang login telah dikeluarkan.');
    }
}
