<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'graded_by',
        'score',
        'feedback',
        'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'graded_at' => 'datetime',
        ];
    }

    /**
     * Submission yang dinilai lewat entri ini.
     */
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * Dosen yang memberi nilai ini.
     */
    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
