<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    /** Dosen pengampu mata kuliah tugas ini boleh mengelola tugas. */
    public function isManageableBy(User $user): bool
    {
        return $this->course->isTaughtBy($user);
    }

    /**
     * Boleh melihat tugas:
     * - dosen pengampu (termasuk draft)
     * - mahasiswa terdaftar, hanya bila tugas sudah dipublikasikan
     */
    public function isVisibleTo(User $user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'dosen') {
            return $this->isManageableBy($user);
        }

        return $user->role === 'mahasiswa'
            && $this->status === 'published'
            && $this->course->isEnrolledBy($user);
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
