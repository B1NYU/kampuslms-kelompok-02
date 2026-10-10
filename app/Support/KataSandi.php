<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/**
 * Aturan dan efek samping yang dipakai bersama oleh "lupa kata sandi"
 * dan "ubah kata sandi", supaya keduanya tidak bisa menyimpang.
 */
class KataSandi
{
    /** Hanya peran ini yang boleh memakai reset/ubah kata sandi mandiri. */
    public const PERAN = ['dosen', 'mahasiswa'];

    /** Minimal 8 karakter, wajib mengandung huruf dan angka. */
    public static function aturan(): Password
    {
        return Password::min(8)->letters()->numbers();
    }

    /** Pesan validasi bahasa Indonesia untuk field password. */
    public static function pesan(): array
    {
        return [
            'password.required'  => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.letters'   => 'Kata sandi harus mengandung huruf.',
            'password.numbers'   => 'Kata sandi harus mengandung angka.',
            'password.different' => 'Kata sandi baru tidak boleh sama dengan kata sandi saat ini.',
        ];
    }

    /**
     * Simpan kata sandi baru dan matikan semua akses lama:
     *  - remember_token diganti (cookie "ingat saya" lama tidak berlaku),
     *  - token API Sanctum dicabut,
     *  - sesi di perangkat lain dihapus (kecuali $sesiSaatIni bila diberikan).
     */
    public static function ganti(User $user, string $kataSandiBaru, ?string $sesiSaatIni = null): void
    {
        $user->forceFill([
            'password'       => $kataSandiBaru, // di-hash oleh cast 'hashed'
            'remember_token' => Str::random(60),
        ])->save();

        $user->tokens()->delete();

        if (config('session.driver') === 'database') {
            $query = DB::table(config('session.table', 'sessions'))->where('user_id', $user->id);

            if ($sesiSaatIni !== null) {
                $query->where('id', '!=', $sesiSaatIni);
            }

            $query->delete();
        }
    }
}
