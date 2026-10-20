<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'session',
        'uploaded_by',
        'title',
        'description',
        'type',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
        'external_url',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'session' => 'integer',
        ];
    }

    /**
     * Otomatis hapus berkas fisik saat record Material dihancurkan/dihapus dari DB
     */
    protected static function booted(): void
    {
        static::deleted(function (Material $material) {
            if ($material->file_path && Storage::exists($material->file_path)) {
                Storage::delete($material->file_path);
            }
        });
    }

    /**
     * Accessor untuk format ukuran berkas agar terbaca manusia (misal: 2.50 MB)
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

    /** Aturan ada di MaterialPolicy@view. Dipertahankan untuk pemanggil lama. */
    public function isViewableBy(User $user): bool
    {
        return Gate::forUser($user)->allows('view', $this);
    }

    public function isLink(): bool
    {
        return $this->type === 'link';
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}