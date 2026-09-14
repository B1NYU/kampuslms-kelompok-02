<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'user_id',
        'file_path',
        'original_name',
        'file_size',
        'note',
        'submitted_at',
        'is_late',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'submitted_at' => 'datetime',
            'is_late' => 'boolean',
        ];
    }

    /**
     * Tugas yang dikumpulkan lewat submission ini.
     */
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Mahasiswa pengumpul submission ini.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Nilai untuk submission ini (relasi one-to-one).
     */
    public function grade()
    {
        return $this->hasOne(Grade::class);
    }
}
