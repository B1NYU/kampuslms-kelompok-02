<?php

namespace App\Listeners;

use App\Events\SubmissionGraded;
use App\Notifications\GradeReceivedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Memberi tahu pemilik pengumpulan bahwa nilainya sudah keluar (atau diperbarui).
 */
class SendGradeNotification implements ShouldQueue
{
    public int $tries = 3;

    public array $backoff = [10, 60];

    public function handle(SubmissionGraded $event): void
    {
        $submission = $event->grade->loadMissing('submission.student', 'submission.assignment.course')->submission;
        $mahasiswa = $submission?->student;

        if (! $mahasiswa) {
            return;
        }

        // Kirim hanya bila mahasiswa memang berhak membuka halaman hasilnya. Memakai Policy yang
        // sama dengan halaman itu (SubmissionPolicy@view) supaya aturannya tidak ditulis dua kali.
        if (! Gate::forUser($mahasiswa)->allows('view', $submission)) {
            return;
        }

        $mahasiswa->notify(new GradeReceivedNotification($submission, $event->regraded));
    }

    public function failed(SubmissionGraded $event, Throwable $exception): void
    {
        Log::error('Gagal memproses notifikasi nilai.', [
            'grade_id' => $event->grade->getKey(),
            'error'    => $exception->getMessage(),
        ]);
    }
}
