<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Email tautan reset kata sandi dalam bahasa Indonesia.
 *
 * Mewarisi ResetPassword bawaan Laravel agar pembuatan URL (route 'password.reset'),
 * masa berlaku token, dan pengiriman tetap memakai mekanisme standar.
 */
class ResetKataSandi extends ResetPassword
{
    protected function buildMailMessage($url): MailMessage
    {
        $menit = (int) config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

        return (new MailMessage)
            ->subject('Atur Ulang Kata Sandi KampusLMS')
            ->greeting('Halo!')
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun KampusLMS Anda.')
            ->action('Atur Ulang Kata Sandi', $url)
            ->line("Tautan ini hanya berlaku selama {$menit} menit dan hanya bisa dipakai satu kali.")
            ->line('Jika Anda tidak merasa meminta reset kata sandi, abaikan email ini. Kata sandi Anda tidak akan berubah.')
            ->salutation('Salam, Tim KampusLMS');
    }
}
