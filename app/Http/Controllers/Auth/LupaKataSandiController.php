<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\KataSandi;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;

/**
 * Reset kata sandi lewat tautan email, untuk dosen dan mahasiswa yang LUPA kata sandinya.
 *
 * Prinsip keamanan:
 *  - Jawaban permintaan tautan SELALU sama, ada atau tidaknya akun (anti user enumeration).
 *  - Hanya peran dosen/mahasiswa; akun admin tidak bisa memakai jalur ini.
 *  - Token sekali pakai, kedaluwarsa (config/auth.php), disimpan ter-hash oleh broker Laravel.
 *  - Setelah berhasil, token API dan sesi lama dicabut.
 */
class LupaKataSandiController extends Controller
{
    public function formPermintaan(): View
    {
        return view('auth.lupa-sandi');
    }

    public function kirimTautan(Request $request): RedirectResponse
    {
        $data = $request->validate(
            ['identifier' => ['required', 'string', 'max:255']],
            ['identifier.required' => 'Masukkan NIM/NIP atau email Anda.']
        );

        // Identitas boleh email atau NIM/NIP, sama seperti halaman login.
        $kolom = filter_var($data['identifier'], FILTER_VALIDATE_EMAIL) ? 'email' : 'nim_nip';

        $user = User::query()
            ->whereIn('role', KataSandi::PERAN)
            ->where($kolom, $data['identifier'])
            ->first();

        if ($user && $user->email) {
            try {
                Password::broker()->sendResetLink(['email' => $user->email]);
            } catch (Throwable $e) {
                // Gagal kirim email (SMTP salah, dsb.) dicatat, tetapi pengguna tetap
                // menerima jawaban yang sama supaya error tidak membocorkan keberadaan akun.
                report($e);
            }
        }

        $menit = (int) config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

        return redirect()->route('password.request')->with(
            'status',
            "Jika akun dosen atau mahasiswa dengan data tersebut terdaftar, tautan atur ulang kata sandi "
            . "telah dikirim ke email yang terdaftar. Periksa kotak masuk dan folder spam. Tautan berlaku {$menit} menit."
        );
    }

    public function formReset(Request $request, string $token): View
    {
        return view('auth.reset-sandi', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        // Pengalihan eksplisit ke form reset (bukan back()): halaman ini memakai
        // Referrer-Policy no-referrer, jadi header Referer tidak bisa diandalkan.
        $kembali = fn () => redirect()->route('password.reset', [
            'token' => (string) $request->input('token'),
            'email' => (string) $request->input('email'),
        ]);

        $validator = Validator::make($request->all(), [
            'token'    => ['required', 'string'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', KataSandi::aturan()],
        ], KataSandi::pesan() + [
            'email.required' => 'Tautan tidak lengkap. Minta tautan reset baru.',
            'email.email'    => 'Tautan tidak valid. Minta tautan reset baru.',
        ]);

        if ($validator->fails()) {
            // Token kosong tidak punya form untuk dituju: kembali ke permintaan tautan.
            if (! $request->filled('token')) {
                return redirect()->route('password.request')->withErrors($validator);
            }

            return $kembali()->withErrors($validator);
        }

        $status = Password::broker()->reset(
            // 'role' membatasi pencarian akun: admin tidak bisa lolos walau punya token.
            $request->only('email', 'password', 'password_confirmation', 'token') + ['role' => KataSandi::PERAN],
            function (User $user, string $kataSandiBaru) {
                KataSandi::ganti($user, $kataSandiBaru);

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            // Token salah, kedaluwarsa, sudah terpakai, atau akun tidak berhak: satu pesan yang sama.
            return $kembali()
                ->withErrors(['email' => 'Tautan reset tidak valid atau sudah kedaluwarsa. Silakan minta tautan baru.']);
        }

        return redirect('/')->with('status', 'Kata sandi berhasil diubah. Silakan masuk dengan kata sandi baru Anda.');
    }
}
