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

    public function index(): View
    {
        $userList = User::withTrashed()->orderByDesc('created_at')->get();

        return view()->file(
            resource_path('views/admin/admin.pengguna.blade.php'),
            compact('userList')
        );
    }

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
        $user->role = $validated['role'];
        $user->save();

        if ($validated['status'] === 'nonaktif') {
            $user->delete();
        }

        return response()->json([
            'message' => 'Pengguna berhasil ditambahkan.',
            'data'    => $this->transform($user),
        ], 201);
    }

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
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

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

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'Pengguna berhasil dihapus.',
        ]);
    }

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
