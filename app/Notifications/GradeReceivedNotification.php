<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * "Nilai sudah keluar" untuk pemilik pengumpulan.
 *
 * Payload SENGAJA tidak memuat skor maupun feedback: tabel notifications tidak terenkripsi dan
 * bisa dibaca siapa pun yang punya akses ke database. Nilainya dibaca dari halaman hasil
 * (yang dilindungi SubmissionPolicy), bukan dari notifikasi.
 */
class GradeReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 60];

    public function __construct(public Submission $submission, public bool $regraded = false) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $assignment = $this->submission->assignment;
        $judul = $assignment->title;

        return [
            'kind'          => 'grade_received',
            'submission_id' => $this->submission->id,
            'assignment_id' => $assignment->id,
            'course_name'   => $assignment->course->name,
            'title'         => $judul,
            'regraded'      => $this->regraded,
            'message'       => $this->regraded
                ? "Nilai tugas \"{$judul}\" diperbarui."
                : "Nilai tugas \"{$judul}\" sudah keluar.",
            'url'           => route('submissions.show', $this->submission, false),
        ];
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Notifikasi nilai gagal dikirim.', [
            'submission_id' => $this->submission->getKey(),
            'error'         => $exception->getMessage(),
        ]);
    }
}
