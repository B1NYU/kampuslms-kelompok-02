<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use App\Policies\Concerns\ChecksCourseAccess;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    use ChecksCourseAccess;

    /**
     * Semua pengguna login boleh membuka daftar MK.
     * ISI daftar difilter di query (lihat Course::scopeVisibleTo di contoh pemanggilan),
     * bukan di policy.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /** Relasi: lecturer_id (dosen), students() (mahasiswa). */
    public function view(User $user, Course $course): bool
    {
        if ($this->isAdmin($user) || $this->teaches($user, $course)) {
            return true; // dosen melihat MK yang diampu di semua status (Q14)
        }

        return $this->isMahasiswa($user)
            && in_array($this->courseStatus($course), ['active', 'archived'], true)
            && $this->isEnrolled($user, $course);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Course $course): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Hanya admin. MK yang masih punya data bertaut ditolak dengan 409
     * (keputusan Q9a opsi a). Pemeriksaan peran DULU, supaya non-admin tetap
     * mendapat 403 polos dan tidak bisa mengintip apakah MK punya data.
     */
    public function delete(User $user, Course $course): bool|Response
    {
        if (! $this->isAdmin($user)) {
            return false;
        }

        $hasRelatedData = $course->materials()->exists()
            || $course->assignments()->exists()
            || $course->students()->exists();

        return $hasRelatedData
            ? Response::denyWithStatus(409, 'Mata kuliah tidak dapat dihapus karena masih memiliki data terkait (tugas, materi, atau mahasiswa). Ubah statusnya menjadi archived sebagai gantinya.')
            : true;
    }

    /**
     * Ability tambahan (Q10, Q3 pada jawaban terakhir).
     * Admin: semua status. Dosen pengampu: draft dan active, tidak archived.
     */
    public function manageEnrollment(User $user, Course $course): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $this->teaches($user, $course)
            && in_array($this->courseStatus($course), ['draft', 'active'], true);
    }
}
