<?php

namespace App\Models;

// Note: FULL REPLACEMENT untuk app/Models/User.php.
// Perubahan dari versi sebelumnya: 'role' DIKELUARKAN dari $fillable secara
// sengaja. Ini artinya User::create([...'role'=>...]) atau $user->fill([...])
// TIDAK akan pernah mengisi kolom role, meski payload-nya mengandung field
// itu. Role hanya boleh diisi lewat assignment eksplisit:
//
//     $user->role = $validated['role'];
//
// seperti yang dilakukan di Admin\UserController. Ini mencegah role
// ter-override secara tidak sengaja lewat mass assignment (misalnya kalau
// suatu saat ada form/endpoint lain yang forward $request->all() ke User).

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nim_nip',
        // 'role' SENGAJA TIDAK ADA DI SINI — lihat catatan di atas.
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

    /**
     * Mata kuliah yang diajar user ini (kalau role-nya dosen).
     */
    public function taughtCourses()
    {
        return $this->hasMany(Course::class, 'lecturer_id');
    }

    /**
     * Mata kuliah yang diikuti user ini (kalau role-nya mahasiswa).
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class)
            ->withPivot('enrolled_at')
            ->withTimestamps();
    }

    /**
     * Semua submission tugas yang pernah dikumpulkan user ini.
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Semua nilai yang pernah diberikan user ini (kalau role-nya dosen).
     */
    public function gradesGiven()
    {
        return $this->hasMany(Grade::class, 'graded_by');
    }
}
