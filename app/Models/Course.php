<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'sks',
        'lecturer_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'sks' => 'integer',
        ];
    }

    /**
     * Dosen pengampu mata kuliah ini.
     */
    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    /**
     * Mahasiswa yang terdaftar (enrolled) di mata kuliah ini.
     */
    public function students()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('enrolled_at')
            ->withTimestamps();
    }

    /**
     * Materi-materi yang diunggah pada mata kuliah ini.
     */
    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    /**
     * Tugas-tugas pada mata kuliah ini.
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
