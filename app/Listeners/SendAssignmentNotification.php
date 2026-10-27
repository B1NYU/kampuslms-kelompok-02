<?php

namespace App\Listeners;

use App\Events\AssignmentPublished;
use App\Notifications\NewAssignmentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

/**
 * Memberi tahu mahasiswa di mata kuliah itu bahwa ada tugas baru.
 *
 * `implements ShouldQueue` membuat listener ini berjalan di worker, bukan di dalam request dosen.
 * Notification-nya juga di-queue, jadi satu tugas untuk 40 mahasiswa menghasilkan
 * 1 job listener + 40 job notifikasi (satu penerima gagal tidak menjatuhkan yang lain).
 */
class SendAssignmentNotification implements ShouldQueue
{
    /** Maksimal percobaan; setelah itu job masuk tabel failed_jobs. */
    public int $tries = 3;

    /** Jeda (detik) sebelum percobaan ke-2 dan ke-3. */
    public array $backoff = [10, 60];

    public function handle(AssignmentPublished $event): void
    {
        $assignment = $event->assignment->loadMissing('course');

        // Antrean bisa terlambat diproses: tugas mungkin sudah dikembalikan ke draft.
        if ($assignment->status !== 'published') {
            return;
        }

        // Mahasiswa hanya boleh melihat tugas di MK active/archived (CoursePolicy).
        // Jangan kirim notifikasi yang tautannya akan berujung 403.
        if (! in_array($assignment->course->status, ['active', 'archived'], true)) {
            return;
        }

        // Satu query untuk seluruh penerima (tidak per mahasiswa).
        $mahasiswa = $assignment->course->students()
            ->where('users.role', 'mahasiswa')
            ->get();

        if ($mahasiswa->isEmpty()) {
            return;
        }

        Notification::send($mahasiswa, new NewAssignmentNotification($assignment));
    }

    /** Dipanggil setelah seluruh percobaan habis. Job juga tercatat di failed_jobs. */
    public function failed(AssignmentPublished $event, Throwable $exception): void
    {
        Log::error('Gagal memproses notifikasi tugas baru.', [
            'assignment_id' => $event->assignment->getKey(),
            'error'         => $exception->getMessage(),
        ]);
    }
}
