<?php

namespace App\Events;

use App\Models\Assignment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Diumumkan ketika sebuah tugas BARU SAJA menjadi 'published'.
 *
 * Controller hanya mengumumkan; siapa yang diberi tahu dan lewat saluran apa
 * adalah urusan Listener (App\Listeners\SendAssignmentNotification).
 */
class AssignmentPublished
{
    use Dispatchable, SerializesModels;

    public function __construct(public Assignment $assignment) {}

    /**
     * Umumkan hanya pada PERALIHAN ke 'published':
     *  - tugas dibuat langsung berstatus published (statusSebelumnya = null), atau
     *  - tugas draft diubah menjadi published.
     * Menyimpan ulang tugas yang sudah published (mis. mengubah judul) TIDAK
     * mengumumkan lagi, supaya mahasiswa tidak dikirimi notifikasi berulang.
     */
    public static function dispatchIfNewlyPublished(Assignment $assignment, ?string $statusSebelumnya = null): void
    {
        if ($assignment->status === 'published' && $statusSebelumnya !== 'published') {
            static::dispatch($assignment);
        }
    }
}
