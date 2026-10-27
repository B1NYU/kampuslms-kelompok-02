<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'created_by',
        'title',
        'code',
        'description',
        'start_time',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Generate unique random alphanumeric code for attendance.
     * Avoids easily confused characters: 0, O, I, 1.
     */
    public static function generateUniqueCode(int $length = 6): string
    {
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $charLength = strlen($characters);

        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, $charLength - 1)];
            }
        } while (static::where('code', $code)->exists());

        return $code;
    }

    /**
     * Check whether this attendance session is currently open and valid for attendance.
     */
    public function isOpen(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        if ($this->start_time && $now->lt($this->start_time)) {
            return false;
        }

        if ($this->expires_at && $now->gt($this->expires_at)) {
            return false;
        }

        return true;
    }

    /**
     * Check whether this attendance session is expired based on expires_at.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && now()->gt($this->expires_at);
    }

    /**
     * Human-readable label for status.
     */
    public function statusLabel(): string
    {
        if (! $this->is_active) {
            return 'Ditutup';
        }

        if ($this->isExpired()) {
            return 'Kedaluwarsa';
        }

        if ($this->start_time && now()->lt($this->start_time)) {
            return 'Belum Dimulai';
        }

        return 'Aktif';
    }

    /**
     * Color / class badge for status.
     */
    public function statusBadgeClass(): string
    {
        return match ($this->statusLabel()) {
            'Aktif' => 'badge-success',
            'Kedaluwarsa' => 'badge-warning',
            'Ditutup' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    /**
     * Human friendly remaining time or expired notice.
     */
    public function remainingTimeForHumans(): string
    {
        if (! $this->is_active) {
            return 'Sesi ditutup manual oleh dosen';
        }

        if ($this->isExpired()) {
            return 'Kedaluwarsa ' . $this->expires_at->diffForHumans();
        }

        return 'Berakhir ' . $this->expires_at->diffForHumans(['parts' => 2]);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'attendances')
            ->withPivot(['status', 'attended_at', 'notes'])
            ->withTimestamps();
    }
}
