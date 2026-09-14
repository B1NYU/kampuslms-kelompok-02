<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    private const ROLES = ['admin', 'dosen', 'mahasiswa'];

    /**
     * Tampilkan halaman CRUD pengguna. Memakai withTrashed() supaya
     * pengguna yang statusnya "Non-aktif" (soft-deleted) tetap tampil
     * di tabel, bukan hilang begitu saja.
     */
    public function index(): View
    {
        $userList = User::withTrashed()->orderByDesc('created_at')->get();

        return view()->file(
            resource_path('views/admin/admin.pengguna.blade.php'),
            compact('userList')
        );
    }

    /**
     * Simpan pengguna baru (dipanggil via fetch/AJAX).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'nim_nip'  => ['required', 'string', 'max:50', 'unique:users,nim_nip'],
            'email'    => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'role'     => ['required', Rule::in(self::ROLES)],
            'password' => ['required', 'string', 'min:6'],
            'status'   => ['required', Rule::in(['aktif', 'nonaktif'])],
        ]);

        $user = new User();
        $user->name     = $validated['name'];
        $user->nim_nip  = $validated['nim_nip'];
        $user->email    = $validated['email'] ?? null;
        $user->password = Hash::make($validated['password']);
        // Assignment eksplisit, BUKAN lewat $fillable / mass assignment.
        $user->role = $validated['role'];
        $user->save();

        if ($validated['status'] === 'nonaktif') {
            $user->delete(); // soft delete
        }

        return response()->json([
            'message' => 'Pengguna berhasil ditambahkan.',
            'data'    => $this->transform($user),
        ], 201);
    }

    /**
     * Update pengguna yang sudah ada (dipanggil via fetch/AJAX).
     * Menerima juga pengguna yang sedang soft-deleted, supaya statusnya
     * bisa diaktifkan kembali lewat form edit.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'nim_nip'  => ['required', 'string', 'max:50', Rule::unique('users', 'nim_nip')->ignore($user->id)],
            'email'    => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'     => ['required', Rule::in(self::ROLES)],
            'password' => ['nullable', 'string', 'min:6'],
            'status'   => ['required', Rule::in(['aktif', 'nonaktif'])],
        ]);

        $user->name    = $validated['name'];
        $user->nim_nip = $validated['nim_nip'];
        $user->email   = $validated['email'] ?? null;
        // Assignment eksplisit, BUKAN lewat $fillable / mass assignment.
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Sinkronkan status aktif/non-aktif dengan soft delete.
        if ($validated['status'] === 'nonaktif' && !$user->trashed()) {
            $user->delete();
        } elseif ($validated['status'] === 'aktif' && $user->trashed()) {
            $user->restore();
        }

        return response()->json([
            'message' => 'Pengguna berhasil diperbarui.',
            'data'    => $this->transform($user->fresh()),
        ]);
    }

    /**
     * Hapus pengguna (soft delete — data tetap ada di database sesuai
     * trait SoftDeletes pada tabel users).
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'Pengguna berhasil dihapus.',
        ]);
    }

    /**
     * Bentuk payload JSON yang konsisten untuk frontend.
     */
    private function transform(User $user): array
    {
        return [
            'id'      => $user->id,
            'name'    => $user->name,
            'nim_nip' => $user->nim_nip,
            'email'   => $user->email,
            'role'    => $user->role,
            'status'  => $user->trashed() ? 'nonaktif' : 'aktif',
        ];
    }
}
