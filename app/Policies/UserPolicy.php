<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Aturan siapa boleh mengelola akun pengguna (hanya admin), beserta pengaman
 * supaya admin tidak bisa "mematikan" sistem atau merusak data bertaut.
 *
 * Penolakan karena peran = 403 polos. Penolakan karena KONDISI DATA memakai
 * pesan yang jelas (dibaca langsung oleh UI) — 403 untuk tindakan terhadap diri
 * sendiri, 409 untuk data yang masih bertaut. Peran diperiksa DULU supaya
 * non-admin tidak bisa mengintip kondisi data lewat pesan penolakan.
 */
class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $this->isAdmin($actor);
    }

    public function create(User $actor): bool
    {
        return $this->isAdmin($actor);
    }

    public function update(User $actor, User $target): bool
    {
        return $this->isAdmin($actor);
    }

    /** Hapus akun (soft delete). */
    public function delete(User $actor, User $target): bool|Response
    {
        return $this->guardRemoval($actor, $target, 'menghapus');
    }

    /** Menonaktifkan akun = soft delete juga, jadi aturannya sama dengan delete. */
    public function deactivate(User $actor, User $target): bool|Response
    {
        return $this->guardRemoval($actor, $target, 'menonaktifkan');
    }

    /** Ubah peran: Gate::authorize('changeRole', [$target, $peranBaru]). */
    public function changeRole(User $actor, User $target, string $newRole): bool|Response
    {
        if (! $this->isAdmin($actor)) {
            return false;
        }

        if ($newRole === $target->role) {
            return true;
        }

        if ($target->role === 'admin') {
            if ($actor->is($target)) {
                return Response::deny('Anda tidak dapat menurunkan peran akun Anda sendiri.');
            }

            if ($this->isLastActiveAdmin($target)) {
                return Response::denyWithStatus(409, 'Peran admin aktif terakhir tidak dapat diturunkan.');
            }
        }

        if ($target->role === 'dosen' && ($jumlah = $target->taughtCourses()->count()) > 0) {
            return Response::denyWithStatus(
                409,
                "Peran dosen tidak dapat diubah karena masih mengampu {$jumlah} mata kuliah. Pindahkan pengampunya terlebih dahulu."
            );
        }

        return true;
    }

    private function guardRemoval(User $actor, User $target, string $kata): bool|Response
    {
        if (! $this->isAdmin($actor)) {
            return false;
        }

        // Akun yang sudah nonaktif tidak punya apa-apa lagi untuk dilindungi.
        if ($target->trashed()) {
            return true;
        }

        if ($actor->is($target)) {
            return Response::deny("Anda tidak dapat {$kata} akun Anda sendiri.");
        }

        if ($this->isLastActiveAdmin($target)) {
            return Response::denyWithStatus(409, "Tidak dapat {$kata} admin aktif terakhir.");
        }

        // courses.lecturer_id memakai restrictOnDelete. Soft delete tidak memicu FK itu,
        // jadi aturannya harus ditegakkan di sini agar maksud skema tidak terlewati.
        if ($target->role === 'dosen' && ($jumlah = $target->taughtCourses()->count()) > 0) {
            return Response::denyWithStatus(
                409,
                "Tidak dapat {$kata} dosen yang masih mengampu {$jumlah} mata kuliah. Pindahkan pengampunya terlebih dahulu."
            );
        }

        return true;
    }

    private function isLastActiveAdmin(User $target): bool
    {
        // User::query() sudah mengecualikan akun yang di-soft-delete.
        return $target->role === 'admin'
            && ! $target->trashed()
            && User::where('role', 'admin')->count() <= 1;
    }

    private function isAdmin(User $user): bool
    {
        return $user->role === 'admin';
    }
}
