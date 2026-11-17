<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Daftar putih untuk satu notifikasi (Illuminate\Notifications\DatabaseNotification).
 * Kolom `data` TIDAK dikembalikan mentah: hanya ID dan teks tampilan yang dipilih satu per satu
 * (sesuai modul minggu 10: simpan ID dan judul, bukan nilai/feedback). Klien membentuk tautan
 * sendiri dari assignment_id / submission_id.
 */
class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = (array) $this->data;

        return [
            'id'            => $this->id,
            'kind'          => (string) ($data['kind'] ?? 'umum'),
            'message'       => (string) ($data['message'] ?? 'Notifikasi baru'),
            'course'        => (string) ($data['course_name'] ?? ''),
            'assignment_id' => $data['assignment_id'] ?? null,
            'submission_id' => $data['submission_id'] ?? null,
            'is_read'       => $this->read_at !== null,
            'read_at'       => $this->read_at?->toIso8601String(),
            'created_at'    => $this->created_at?->toIso8601String(),
        ];
    }
}
