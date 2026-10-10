<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;
use App\Policies\Concerns\ChecksCourseAccess;
use Illuminate\Auth\Access\Response;

/**
 * Kepemilikan lewat assignment->course->lecturer_id (BUKAN created_by).
 * Admin boleh membuat/mengelola tugas (keputusan Q1 / B3); created_by diisi
 * dengan id pengguna yang sedang login di controller.
 */
class AssignmentPolicy
{
    use ChecksCourseAccess;

    /**
     * Per MK. Admin dan dosen pengampu melihat semua tugas; mahasiswa hanya
     * yang published — penyaringan status itu dilakukan di query
     * (Assignment::scopeVisibleTo), karena policy ini menjawab "boleh membuka
     * daftar", bukan "baris mana yang muncul".
     */
    public function viewAny(User $user, Course $course): bool
    {
        return $this->canSeeCourseContent($user, $course);
    }

    public function view(User $user, Assignment $assignment): bool
    {
        $course = $assignment->course;

        if ($this->isAdmin($user) || $this->teaches($user, $course)) {
            return true; // termasuk tugas draft
        }

        // Mahasiswa: tugas draft tidak terlihat sama sekali (Q5).
        return $this->enumValue($assignment->status) === 'published'
            && $this->canSeeCourseContent($user, $course);
    }

    /** Gate::authorize('create', [Assignment::class, $course]) */
    public function create(User $user, Course $course): bool
    {
        return $this->canManageCourseContent($user, $course);
    }

    public function update(User $user, Assignment $assignment): bool
    {
        return $this->canManageCourseContent($user, $assignment->course);
    }

    /**
     * Peran dulu (403), baru syarat data (409). Tugas yang sudah punya
     * submission tidak boleh dihapus karena FK cascade akan ikut membuang
     * pengumpulan dan nilai mahasiswa.
     */
    public function delete(User $user, Assignment $assignment): bool|Response
    {
        if (! $this->canManageCourseContent($user, $assignment->course)) {
            return false;
        }

        return $assignment->submissions()->exists()
            ? Response::denyWithStatus(409, 'Tugas ini sudah memiliki pengumpulan dari mahasiswa, sehingga tidak dapat dihapus.')
            : true;
    }
}
