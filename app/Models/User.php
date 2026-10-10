<?php

namespace App\Models;

use App\Notifications\ResetKataSandi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nim_nip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Email reset kata sandi memakai templat bahasa Indonesia. */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetKataSandi($token));
    }

    /**
     * Nama route dashboard sesuai peran pengguna.
     */
    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'admin' => 'admin.dashboard',
            'dosen' => 'dosen.dashboard',
            default => 'mahasiswa.dashboard',
        };
    }

    public function taughtCourses()
    {
        return $this->hasMany(Course::class, 'lecturer_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class)
            ->withPivot('enrolled_at')
            ->withTimestamps();
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function gradesGiven()
    {
        return $this->hasMany(Grade::class, 'graded_by');
    }
}
