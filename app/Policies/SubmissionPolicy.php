<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use App\Policies\Concerns\ChecksCourseAccess;
use Illuminate\Auth\Access\Response;

/**
 * PENTING: jangan memasang Gate::before() "admin boleh semua" di aplikasi ini.
 * Admin sengaja DITOLAK untuk create/update/delete/grade submission, dan
 * Gate::before akan melewati seluruh policy ini.
 *
 * Kepemilikan: submission->assignment->course->lecturer_id (dosen),
 * submission->user_id + course_user (mahasiswa).
 * Muat dengan Submission::with('assignment.course') untuk daftar/loop.
 */
class SubmissionPolicy
{
    use ChecksCourseAccess;

    /**
     * Per tugas: Gate::authorize('viewAny', [Submission::class, $assignment]).
     * Mahasiswa lolos HANYA agar bisa melihat daftar miliknya; controller
     * WAJIB menyaring where('user_id', auth()->id()) (Q8b).
     */
    public function viewAny(User $user, Assignment $assignment): bool
    {
        $course = $assignment->course;

        if ($this->isAdmin($user) || $this->teaches($user, $course)) {
            return true;
        }

        return $this->enumValue($assignment->status) === 'published'
            && $this->canSeeCourseContent($user, $course);
    }

    /**
     * Berlaku juga untuk mengunduh berkas submission (Q8a).
     * Inilah pemeriksaan "mahasiswa A tidak boleh membuka submission B".
     */
    public function view(User $user, Submission $submission): bool
    {
        $assignment = $submission->assignment;
        $course = $assignment->course;

        if ($this->isAdmin($user) || $this->teaches($user, $course)) {
            return true;
        }

        // Mahasiswa: miliknya sendiri, masih terdaftar (Q3b), tugas published (Q5).
        return $this->isMahasiswa($user)
            && (int) $submission->user_id === (int) $user->id
            && $this->enumValue($assignment->status) === 'published'
            && $this->canSeeCourseContent($user, $course);
    }

    /**
     * Gate::authorize('create', [Submission::class, $assignment])
     * Syarat Q6: mahasiswa terdaftar, MK active, tugas published, belum punya
     * submission, dan (bila lewat deadline) allow_late aktif.
     * Penanda is_late diisi controller. Unique (assignment_id, user_id) di
     * database tetap menjadi pagar terakhir bila ada permintaan ganda.
     */
    public function create(User $user, Assignment $assignment): bool|Response
    {
        if (! $this->isMahasiswa($user)) {
            return false;
        }

        $course = $assignment->course;

        if ($this->courseStatus($course) !== 'active'
            || $this->enumValue($assignment->status) !== 'published'
            || ! $this->isEnrolled($user, $course)) {
            return false;
        }

        if (now()->greaterThan($assignment->due_at) && ! $assignment->allow_late) {
            return Response::deny('Batas waktu pengumpulan sudah lewat.');
        }

        if ($assignment->submissions()->where('user_id', $user->id)->exists()) {
            return Response::deny('Anda sudah mengumpulkan tugas ini dan pengumpulan tidak dapat diubah.');
        }

        return true;
    }

    /** Tidak ada yang boleh mengubah submission, termasuk admin (Q7, Q12). */
    public function update(User $user, Submission $submission): bool
    {
        return false;
    }

    /** Tidak ada yang boleh menghapus submission (Q13). */
    public function delete(User $user, Submission $submission): bool
    {
        return false;
    }

    /**
     * Ability tambahan (Q10): hanya dosen pengampu, hanya MK active.
     * Admin dan mahasiswa tidak.
     */
    public function grade(User $user, Submission $submission): bool
    {
        $course = $submission->assignment->course;

        return $this->teaches($user, $course)
            && $this->courseStatus($course) === 'active';
    }
}
