<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'created_by',
        'title',
        'instructions',
        'due_at',
        'max_score',
        'allow_late',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'allow_late' => 'boolean',
            'max_score' => 'integer',
        ];
    }

    /** Aturan ada di AssignmentPolicy@update. Dipertahankan untuk pemanggil lama. */
    public function isManageableBy(User $user): bool
    {
        return Gate::forUser($user)->allows('update', $this);
    }

    /** Aturan ada di AssignmentPolicy@view (tugas draft tidak terlihat mahasiswa). */
    public function isVisibleTo(User $user): bool
    {
        return Gate::forUser($user)->allows('view', $this);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function grades()
    {
        return $this->hasManyThrough(Grade::class, Submission::class);
    }
}
