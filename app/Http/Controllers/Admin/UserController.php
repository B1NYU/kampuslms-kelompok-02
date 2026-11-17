<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;


class UserController extends Controller
{
    private const ROLES = ['admin', 'dosen', 'mahasiswa'];

    public function index(Request $request): View
    {
        $role = $request->query('role');
        $search = trim((string) $request->query('q', ''));

        $userList = User::withTrashed()
            ->when(in_array($role, self::ROLES, true), fn ($q) => $q->where('role', $role))
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . $search . '%';
                $q->where(function ($sq) use ($like) {
                    $sq->where('name', 'like', $like)
                        ->orWhere('nim_nip', 'like', $like)
                        ->orWhere('email', 'like', $like);
                });
            })
            ->orderByRaw("CASE WHEN role = 'admin' THEN 1 WHEN role = 'dosen' THEN 2 ELSE 3 END, id ASC")
            ->paginate(10)
            ->withQueryString();

        $countAdmin = User::withTrashed()->where('role', 'admin')->count();
        $countDosen = User::withTrashed()->where('role', 'dosen')->count();
        $countMahasiswa = User::withTrashed()->where('role', 'mahasiswa')->count();
        $countTotal = User::withTrashed()->count();

        return view()->file(
            resource_path('views/admin/admin.pengguna.blade.php'),
            compact('userList', 'role', 'search', 'countAdmin', 'countDosen', 'countMahasiswa', 'countTotal')
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data) {
            $user = new User;
            $user->name = $data['name'];
            $user->nim_nip = $data['nim_nip'];
            $user->email = $data['email'];
            $user->password = $data['password']; // di-hash oleh cast 'hashed'
            $user->role = $data['role'];      // diisi eksplisit: 'role' tidak ada di $fillable
            $user->save();

            if ($data['status'] === 'nonaktif') {
                $user->delete();
            }

            return $user;
        });

        return response()->json([
            'message' => 'Pengguna berhasil ditambahkan.',
            'data' => $this->transform($user),
        ], 201);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $user) {
            $this->kunciBarisAdmin();
            $user->refresh();

            // Dicek ULANG di dalam kunci: dua admin yang bekerja bersamaan tidak boleh
            // sama-sama lolos dan menyisakan nol admin.
            Gate::authorize('changeRole', [$user, $data['role']]);

            $menonaktifkan = $data['status'] === 'nonaktif' && ! $user->trashed();
            if ($menonaktifkan) {
                Gate::authorize('deactivate', $user);
            }

            $user->name = $data['name'];
            $user->nim_nip = $data['nim_nip'];
            $user->email = $data['email'];
            $user->role = $data['role'];

            if (! empty($data['password'])) {
                $user->password = $data['password'];
            }

            $user->save();

            if ($menonaktifkan) {
                $user->tokens()->delete(); // token API ikut dicabut
                $user->delete();
            } elseif ($data['status'] === 'aktif' && $user->trashed()) {
                $user->restore();
            }
        });

        return response()->json([
            'message' => 'Pengguna berhasil diperbarui.',
            'data' => $this->transform($user->fresh()),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        DB::transaction(function () use ($user) {
            $this->kunciBarisAdmin();
            $user->refresh();

            Gate::authorize('delete', $user);

            $user->tokens()->delete();
            $user->delete();
        });

        return response()->json([
            'message' => 'Pengguna berhasil dihapus.',
        ]);
    }

    private function kunciBarisAdmin(): void
    {
        User::withTrashed()->where('role', 'admin')->lockForUpdate()->pluck('id');
    }

    private function transform(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'nim_nip' => $user->nim_nip,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->trashed() ? 'nonaktif' : 'aktif',
        ];
    }
}
