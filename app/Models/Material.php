<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    /** Admin, dosen pengampu, atau mahasiswa terdaftar di mata kuliahnya. */
    public function isViewableBy(User $user): bool
    {
        return $this->course->isViewableBy($user);
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
