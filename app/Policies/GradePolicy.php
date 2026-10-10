<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;
use App\Policies\Concerns\ChecksCourseAccess;
use Illuminate\Support\Facades\Gate;

/**
 * Kepemilikan: grade->submission->assignment->course.
 * Muat dengan Grade::with('submission.assignment.course') untuk daftar.
 */
class GradePolicy
{
    use ChecksCourseAccess;

    /**
     * Rekap per MK: Gate::authorize('viewAny', [Grade::class, $course]).
     * Mahasiswa lolos hanya untuk melihat nilainya sendiri; query rekap
     * WAJIB disaring lewat submissions.user_id.
     */
    public function viewAny(User $user, Course $course): bool
    {
        return $this->canSeeCourseContent($user, $course);
    }

    public function view(User $user, Grade $grade): bool
    {
        $submission = $grade->submission;
        $assignment = $submission->assignment;
        $course = $assignment->course;

        if ($this->isAdmin($user) || $this->teaches($user, $course)) {
            return true;
        }

        return $this->isMahasiswa($user)
            && (int) $submission->user_id === (int) $user->id
            && $this->enumValue($assignment->status) === 'published'
            && $this->canSeeCourseContent($user, $course);
    }

    /** Gate::authorize('create', [Grade::class, $submission]) — mengikuti ability 'grade'. */
    public function create(User $user, Submission $submission): bool
    {
        return Gate::forUser($user)->allows('grade', $submission);
    }

    /** Penilaian ulang (upsert) tetap hanya oleh dosen pengampu di MK active. */
    public function update(User $user, Grade $grade): bool
    {
        return Gate::forUser($user)->allows('grade', $grade->submission);
    }

    /** Nilai tidak boleh dihapus oleh siapa pun (Q9b). */
    public function delete(User $user, Grade $grade): bool
    {
        return false;
    }
}
