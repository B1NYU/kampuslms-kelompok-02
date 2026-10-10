<?php

namespace App\Policies\Concerns;

use App\Models\Course;
use App\Models\User;
use BackedEnum;

/**
 * Helper bersama untuk semua policy KampusLMS.
 *
 * Semua aturan kepemilikan bertumpu pada courses.lecturer_id (pengampu),
 * BUKAN pada created_by (keputusan Q2).
 */
trait ChecksCourseAccess
{
    /** Normalkan nilai kolom enum, baik string biasa maupun enum cast PHP. */
    protected function enumValue(mixed $value): string
    {
        return $value instanceof BackedEnum ? (string) $value->value : (string) $value;
    }

    protected function isAdmin(User $user): bool
    {
        return $this->enumValue($user->role) === 'admin';
    }

    protected function isDosen(User $user): bool
    {
        return $this->enumValue($user->role) === 'dosen';
    }

    protected function isMahasiswa(User $user): bool
    {
        return $this->enumValue($user->role) === 'mahasiswa';
    }

    protected function courseStatus(Course $course): string
    {
        return $this->enumValue($course->status);
    }

    /** Dosen pengampu: dibandingkan lewat FK, tanpa query tambahan. */
    protected function teaches(User $user, Course $course): bool
    {
        return $this->isDosen($user) && (int) $course->lecturer_id === (int) $user->id;
    }

    /**
     * Terdaftar di MK. Memakai relasi yang sudah dimuat bila ada
     * (with('students')); jika tidak, satu query exists() — tidak pernah
     * memuat seluruh koleksi mahasiswa.
     */
    protected function isEnrolled(User $user, Course $course): bool
    {
        if ($course->relationLoaded('students')) {
            return $course->students->contains('id', $user->id);
        }

        return $course->students()->where('users.id', $user->id)->exists();
    }

    /**
     * Boleh MELIHAT isi MK (materi/tugas) — Q3a, Q4, Q14.
     * Admin: semua status. Dosen pengampu: semua status.
     * Mahasiswa: terdaftar, dan MK bukan draft.
     */
    protected function canSeeCourseContent(User $user, Course $course): bool
    {
        if ($this->isAdmin($user) || $this->teaches($user, $course)) {
            return true;
        }

        return $this->isMahasiswa($user)
            && in_array($this->courseStatus($course), ['active', 'archived'], true)
            && $this->isEnrolled($user, $course);
    }

    /**
     * Boleh MENGUBAH isi MK (tambah/ubah/hapus materi dan tugas) — Q4.
     * Admin: semua status. Dosen pengampu: draft dan active, tidak archived.
     */
    protected function canManageCourseContent(User $user, Course $course): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $this->teaches($user, $course)
            && in_array($this->courseStatus($course), ['draft', 'active'], true);
    }
}
