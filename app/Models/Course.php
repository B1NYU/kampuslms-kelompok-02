<?php

namespace App\Models;

use App\Support\DosenDashboardStats;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

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
     * Daftar mata kuliah dan statistik dashboard dosen bergantung pada dosen pengampu
     * dan status mata kuliah. Kalau dosen pengampu berganti, cache dosen lama ikut dibersihkan.
     */
    protected static function booted(): void
    {
        static::saved(function (Course $course) {
            DosenDashboardStats::forgetForLecturer($course->lecturer_id);

            if ($course->wasChanged('lecturer_id')) {
                DosenDashboardStats::forgetForLecturer($course->getOriginal('lecturer_id'));
            }
        });

        static::deleted(fn (Course $course) => DosenDashboardStats::forgetForLecturer($course->lecturer_id));
    }

    /** Apakah $user dosen pengampu mata kuliah ini? (murni fakta relasi, bukan aturan akses) */
    public function isTaughtBy(User $user): bool
    {
        return $user->role === 'dosen' && (int) $this->lecturer_id === (int) $user->id;
    }

    /** Apakah $user (mahasiswa) terdaftar di mata kuliah ini? (murni fakta relasi) */
    public function isEnrolledBy(User $user): bool
    {
        return $this->students()->whereKey($user->id)->exists();
    }

    /**
     * Boleh melihat MK? Aturannya HANYA ada di CoursePolicy@view.
     * Method ini dipertahankan supaya pemanggil lama tidak putus.
     */
    public function isViewableBy(User $user): bool
    {
        return Gate::forUser($user)->allows('view', $this);
    }

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('enrolled_at')
            ->withTimestamps();
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function attendanceSessions()
    {
        return $this->hasMany(AttendanceSession::class);
    }
}
