<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\User;
use App\Models\Material;
use App\Models\Assignment;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'sks',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'sks' => 'integer',
        ];
    }


    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    } // many-to-one, setiap mata kuliah diajar oleh satu dosen


    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('enrolled_at')
            ->withTimestamps();
    } // one-to-many, satu mata kuliah untuk mengambil seluruh daftar mahasiswa yang menjadi peserta kelas

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    } // one-to-many, satu mata kuliah dapat memiliki banyak materi pembelajaran

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    } // one-to-many, satu mata kuliah dapat memiliki banyak tugas yang diupload oleh dosen
}
