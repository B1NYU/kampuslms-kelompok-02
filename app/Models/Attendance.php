<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_session_id',
        'user_id',
        'status',
        'attended_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'attended_at' => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->student();
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'hadir' => 'badge-success',
            'izin' => 'badge-info',
            'sakit' => 'badge-warning',
            'alpa' => 'badge-danger',
            default => 'badge-secondary',
        };
    }
}
