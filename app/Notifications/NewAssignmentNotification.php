<?php

namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * "Tugas baru" untuk mahasiswa. Disimpan di tabel notifications (saluran database).
 */
class NewAssignmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 60];

    public function __construct(public Assignment $assignment) {}

    public function via(object $notifiable): array
    {
        return ['database']; // tambahkan 'mail' bila perlu
    }

    /**
     * Kolom `data` TIDAK terenkripsi: hanya ID dan judul yang boleh disimpan di sini.
     * `url` berupa path relatif supaya tetap benar di host mana pun (worker tidak punya request).
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'kind'          => 'assignment_published',
            'assignment_id' => $this->assignment->id,
            'course_id'     => $this->assignment->course_id,
            'course_name'   => $this->assignment->course->name,
            'title'         => $this->assignment->title,
            'due_at'        => $this->assignment->due_at->toIso8601String(),
            'message'       => "Tugas baru di {$this->assignment->course->name}: {$this->assignment->title}",
            'url'           => route('assignments.show', $this->assignment, false),
        ];
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Notifikasi tugas baru gagal dikirim.', [
            'assignment_id' => $this->assignment->getKey(),
            'error'         => $exception->getMessage(),
        ]);
    }
}
