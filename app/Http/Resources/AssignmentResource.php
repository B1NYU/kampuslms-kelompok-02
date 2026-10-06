<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'course_id'    => $this->course_id,
            'created_by'   => $this->created_by,
            'title'        => $this->title,
            'instructions' => $this->instructions,
            'due_at'       => $this->due_at?->toIso8601String(),
            'max_score'    => $this->max_score,
            'allow_late'   => (bool) $this->allow_late,
            'status'       => $this->status,

            // Dosen/admin: jumlah pengumpulan (withCount).
            'submissions_count' => $this->whenCounted('submissions'),

            // Mahasiswa: status pengumpulan miliknya sendiri: belum | terkumpul | terlambat.
            // Relasi submissions sudah difilter ke user_id pemanggil di controller.
            'submission_status' => $this->when(
                $this->relationLoaded('submissions') && $request->user()?->role === 'mahasiswa',
                function () {
                    $submission = $this->submissions->first();

                    return match (true) {
                        $submission === null => 'belum',
                        $submission->is_late => 'terlambat',
                        default              => 'terkumpul',
                    };
                }
            ),
        ];
    }
}
