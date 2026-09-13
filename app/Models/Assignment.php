<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

use App\Models\Course;
use App\Models\Submission;
use App\Models\Grade;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
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
            'max_score' => 'integer',
            'allow_late' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    } // many-to-one, sebuah tugas terikat dan menjadi bagian dari satu mata kuliah tertentu

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    } // one-to-many, satu tugas dapat menerima banyak berkas pengumpulan dari berbagai mahasiswa

    public function grades(): HasManyThrough
    {
        return $this->hasManyThrough(Grade::class, Submission::class);
    } // has many through, mahasiswa mendapatkan nilai dari sebuah tugas melalui tabel submission terlebih dahulu.
    // Has Many Through: Menghubungkan Tabel A ke Tabel C melalui Tabel B. assignment -> submission -> grade
}
