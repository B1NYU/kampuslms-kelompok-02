<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

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
     * Otomatis hapus berkas fisik submission dari storage saat record dihapus dari DB
     */
    protected static function booted(): void
    {
        static::deleted(function (Submission $submission) {
            if ($submission->file_path && Storage::exists($submission->file_path)) {
                Storage::delete($submission->file_path);
            }
        });
    }

    /**
     * Accessor untuk format ukuran berkas agar mudah dibaca (misal: 1.25 MB)
     */
    protected function formattedFileSize(): Attribute
    {
        return Attribute::make(
            get: function () {
                $bytes = $this->file_size;
                if (! $bytes) return '0 B';

                $units = ['B', 'KB', 'MB', 'GB'];
                $i = floor(log($bytes, 1024));

                return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
            }
        );
    }

    /** Aturan ada di SubmissionPolicy@view. Dipertahankan untuk pemanggil lama. */
    public function isViewableBy(User $user): bool
    {
        return Gate::forUser($user)->allows('view', $this);
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function grade()
    {
        return $this->hasOne(Grade::class);
    }
}