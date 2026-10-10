<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Material;
use App\Models\User;
use App\Policies\Concerns\ChecksCourseAccess;

/**
 * Kepemilikan selalu lewat material->course (lecturer_id / students()).
 * Panggil dengan Material::with('course') bila memeriksa banyak baris.
 */
class MaterialPolicy
{
    use ChecksCourseAccess;

    /** Per MK: Gate::authorize('viewAny', [Material::class, $course]) */
    public function viewAny(User $user, Course $course): bool
    {
        return $this->canSeeCourseContent($user, $course);
    }

    /** Berlaku juga untuk unduh berkas. Relasi: material->course. */
    public function view(User $user, Material $material): bool
    {
        return $this->canSeeCourseContent($user, $material->course);
    }

    /** Gate::authorize('create', [Material::class, $course]) */
    public function create(User $user, Course $course): bool
    {
        return $this->canManageCourseContent($user, $course);
    }

    public function update(User $user, Material $material): bool
    {
        return $this->canManageCourseContent($user, $material->course);
    }

    public function delete(User $user, Material $material): bool
    {
        return $this->canManageCourseContent($user, $material->course);
    }
}
