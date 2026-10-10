<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminCourseController extends Controller
{
    private const STATUSES = ['draft', 'active', 'archived'];

    /**
     * Daftar mata kuliah dengan pencarian, filter status & dosen, dan pagination.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Course::class);

        $search = trim((string) $request->query('q', ''));

        // Whitelist: nilai di luar daftar dianggap "tidak memfilter".
        $status = $request->query('status');
        $status = in_array($status, self::STATUSES, true) ? $status : null;

        $lecturerId = $request->query('lecturer_id');
        $lecturerId = is_string($lecturerId) && ctype_digit($lecturerId) ? (int) $lecturerId : null;

        $matkulList = Course::query()
            ->with('lecturer')
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('code', 'like', $like)
                      ->orWhere('name', 'like', $like);
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($lecturerId, fn ($query) => $query->where('lecturer_id', $lecturerId))
            ->orderByDesc('created_at')
            ->orderByDesc('id') // tie-breaker agar urutan antarhalaman stabil
            ->paginate(3)
            ->withQueryString();

        $dosenList = User::where('role', 'dosen')->orderBy('name')->get(['id', 'name']);

        $filters = [
            'q'           => $search,
            'status'      => $status,
            'lecturer_id' => $lecturerId,
        ];

        return view()->file(
            resource_path('views/admin/admin.matkul.blade.php'),
            compact('matkulList', 'dosenList', 'filters')
        );
    }

    public function store(StoreCourseRequest $request)
    {
        Gate::authorize('create', Course::class);

        Course::create($request->validated());

        return redirect()
            ->action([self::class, 'index'], $this->redirectFilters($request))
            ->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function update(UpdateCourseRequest $request, Course $matkul)
    {
        Gate::authorize('update', $matkul);

        $matkul->update($request->validated());

        return redirect()
            ->action([self::class, 'index'], $this->redirectFilters($request))
            ->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    /**
     * Hanya admin. MK yang masih punya tugas, materi, atau mahasiswa ditolak
     * CoursePolicy@delete dengan 409; handler di bootstrap/app.php mengubahnya
     * menjadi redirect back() dengan flash 'error' (filter di URL tetap terjaga).
     * Pemeriksaan ini sengaja di aplikasi, karena FK materials/assignments
     * memakai cascadeOnDelete (Bagian 4.2) dan database tidak akan menolak.
     */
    public function destroy(Request $request, Course $matkul)
    {
        Gate::authorize('delete', $matkul);

        $matkul->delete();

        return redirect()
            ->action([self::class, 'index'], $this->redirectFilters($request))
            ->with('success', 'Mata kuliah berhasil dihapus.');
    }

    /**
     * Ambil filter yang sedang aktif dari hidden field form (redirect_q,
     * redirect_status, redirect_lecturer_id), lalu buang yang kosong.
     */
    private function redirectFilters(Request $request): array
    {
        return array_filter([
            'q'           => $request->input('redirect_q'),
            'status'      => $request->input('redirect_status'),
            'lecturer_id' => $request->input('redirect_lecturer_id'),
        ]);
    }

    private function transform(Course $course): array
    {
        return [
            'id'            => $course->id,
            'code'          => $course->code,
            'name'          => $course->name,
            'sks'           => $course->sks,
            'lecturer_id'   => $course->lecturer_id,
            'lecturer_name' => $course->lecturer?->name,
            'status'        => $course->status,
            'description'   => $course->description,
        ];
    }
}
