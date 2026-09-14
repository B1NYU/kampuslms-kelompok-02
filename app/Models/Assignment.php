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

    /**
     * Mata kuliah tempat tugas ini berada.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Semua submission untuk tugas ini.
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Nilai-nilai untuk tugas ini, diambil lewat tabel submissions.
     */
    public function grades()
    {
        return $this->hasManyThrough(Grade::class, Submission::class);
    }
}
