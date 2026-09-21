<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;

class AdminCourseController extends Controller
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

    public function store(StoreCourseRequest $request)
    {
        $course = Course::create($request->validated());
        $course->load('lecturer');

        return response()->json([
            'message' => 'Mata kuliah berhasil ditambahkan.',
            'data'    => $this->transform($course),
        ], 201);
    }

    public function update(UpdateCourseRequest $request, Course $matkul)
    {
        $matkul->update($request->validated());
        $matkul->load('lecturer');

        return response()->json([
            'message' => 'Mata kuliah berhasil diperbarui.',
            'data'    => $this->transform($matkul),
        ]);
    }

    public function destroy(Course $matkul)
    {
        try {
            $matkul->delete();
        } catch (QueryException $e) {
            // 23000 = MySQL/MariaDB integrity violation, 23503 = PostgreSQL foreign key violation.
            if (in_array((string) $e->getCode(), ['23000', '23503'], true)) {
                return response()->json([
                    'message' => 'Mata kuliah tidak dapat dihapus karena masih memiliki data terkait '
                        . '(tugas, materi, atau mahasiswa). Ubah statusnya menjadi archived sebagai gantinya.',
                ], 409);
            }

            throw $e;
        }

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
