<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'assignment_id' => $this->assignment_id,
            'original_name' => $this->original_name,
            'file_size'     => $this->file_size,
            'note'          => $this->note,
            'submitted_at'  => $this->submitted_at?->toIso8601String(),
            'is_late'       => (bool) $this->is_late,
            // file_path (lokasi di storage server) sengaja TIDAK disertakan.
            'student'       => new UserResource($this->whenLoaded('student')),
            'grade'         => new GradeResource($this->whenLoaded('grade')),
        ];
    }
}
    