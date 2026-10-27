<?php

namespace App\Events;

use App\Models\Grade;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Diumumkan ketika dosen menyimpan nilai (baru atau perubahan) untuk sebuah pengumpulan.
 * $regraded = true bila pengumpulan itu sebelumnya sudah punya nilai.
 */
class SubmissionGraded
{
    use Dispatchable, SerializesModels;

    public function __construct(public Grade $grade, public bool $regraded = false) {}
}
