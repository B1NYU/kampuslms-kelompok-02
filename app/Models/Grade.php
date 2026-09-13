<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'score',
        'feedback',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'graded_at' => 'datetime',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    } // one-to-one, setiap nilai merujuk pada satu berkas pengumpulan tugas spesifik yang dinilai

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    } // many-to-one, setiap nilai diberikan oleh satu dosen yang menilai berkas pengumpulan tugas
}
