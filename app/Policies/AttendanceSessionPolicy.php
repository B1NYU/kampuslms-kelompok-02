<?php

namespace App\Policies;

use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\User;
use App\Policies\Concerns\ChecksCourseAccess;

class AttendanceSessionPolicy
{
    use ChecksCourseAccess;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AttendanceSession $session): bool
    {
        if ($this->isAdmin($user) || $this->teaches($user, $session->course)) {
            return true;
        }

        return $this->isMahasiswa($user) && $this->isEnrolled($user, $session->course);
    }

    public function create(User $user, Course $course): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $this->teaches($user, $course)
            && in_array($this->courseStatus($course), ['draft', 'active'], true);
    }

    public function update(User $user, AttendanceSession $session): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $this->teaches($user, $session->course)
            && in_array($this->courseStatus($session->course), ['draft', 'active'], true);
    }

    public function delete(User $user, AttendanceSession $session): bool
    {
        return $this->update($user, $session);
    }
}
