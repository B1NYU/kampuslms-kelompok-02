<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'name'    => $this->name,
            'email'   => $this->whenHas('email'),
            'role'    => $this->whenHas('role'),
            'nim_nip' => $this->whenHas('nim_nip'),
        ];
    }
}
