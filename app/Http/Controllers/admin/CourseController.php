<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    private const STATUSES = ['draft', 'active', 'archived'];

    public function index(Request $request): View
    {
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
            ->paginate(15)
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


    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'        => ['required', 'string', 'max:20', 'unique:courses,code'],
            'name'        => ['required', 'string', 'max:255'],
            'sks'         => ['required', 'integer', 'min:1', 'max:6'],
            'lecturer_id' => ['required', 'exists:users,id'],
            'status'      => ['required', Rule::in(['draft', 'active', 'archived'])],
            'description' => ['nullable', 'string'],
        ]);

        $course = Course::create($validated);
        $course->load('lecturer');

        return response()->json([
            'message' => 'Mata kuliah berhasil ditambahkan.',
            'data'    => $this->transform($course),
        ], 201);
    }

    public function update(Request $request, Course $matkul)
    {
        $validated = $request->validate([
            'code'        => ['required', 'string', 'max:20', Rule::unique('courses', 'code')->ignore($matkul->id)],
            'name'        => ['required', 'string', 'max:255'],
            'sks'         => ['required', 'integer', 'min:1', 'max:6'],
            'lecturer_id' => ['required', 'exists:users,id'],
            'status'      => ['required', Rule::in(['draft', 'active', 'archived'])],
            'description' => ['nullable', 'string'],
        ]);

        $matkul->update($validated);
        $matkul->load('lecturer');

        return response()->json([
            'message' => 'Mata kuliah berhasil diperbarui.',
            'data'    => $this->transform($matkul),
        ]);
    }

    public function destroy(Course $matkul)
    {
        $matkul->delete();

        return response()->json([
            'message' => 'Mata kuliah berhasil dihapus.',
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

