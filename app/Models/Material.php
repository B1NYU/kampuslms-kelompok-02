<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'session',
        'uploaded_by',
        'title',
        'description',
        'type',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
        'external_url',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'session' => 'integer',
        ];
    }

    /** Aturan ada di MaterialPolicy@view. Dipertahankan untuk pemanggil lama. */
    public function isViewableBy(User $user): bool
    {
        return Gate::forUser($user)->allows('view', $this);
    }

    public function isLink(): bool
    {
        return $this->type === 'link';
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
