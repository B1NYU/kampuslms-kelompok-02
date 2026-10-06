<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'code'        => $this->code,
            'name'        => $this->name,
            'description' => $this->description,
            'sks'         => $this->sks,
            'status'      => $this->status,
            'lecturer'    => new UserSummaryResource($this->whenLoaded('lecturer')),

            // Hanya muncul di GET /courses/{id} (loadCount).
            'materials_count'   => $this->whenCounted('materials'),
            'assignments_count' => $this->whenCounted('assignments'),

            // Hanya muncul untuk mahasiswa (relasi courses() dengan withPivot('enrolled_at')).
            'enrolled_at' => $this->whenPivotLoaded('course_user', fn () => $this->pivot->enrolled_at),
        ];
    }
}
