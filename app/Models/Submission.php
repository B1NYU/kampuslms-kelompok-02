<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Models\Assignment;
use App\Models\User;
use App\Models\Grade;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_path',
        'original_name',
        'file_size',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'submitted_at' => 'datetime',
            'is_late' => 'boolean',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    } // many-to-one, setiap pengumpulan berkas merujuk pada satu tugas spesifik yang dikerjakan

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    } // many-to-one, pengumpulan berkas dimiliki oleh satu mahasiswa

    public function grade(): HasOne
    {
        return $this->hasOne(Grade::class);
    } // one-to-one, satu berkas pengumpulan tugas hanya bisa memiliki maksimal satu nilai
}
