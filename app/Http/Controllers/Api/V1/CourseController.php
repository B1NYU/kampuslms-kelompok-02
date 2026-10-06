<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseCollection;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * GET /api/v1/courses
     * dosen: MK yang diajar; mahasiswa: MK yang diikuti; admin: semua.
     */
    public function index(Request $request): CourseCollection
    {
        $user = $request->user();

        $query = match ($user->role) {
            'admin'     => Course::query(),
            'dosen'     => $user->taughtCourses(),
            'mahasiswa' => $user->courses(),            // membawa pivot enrolled_at
            default     => abort(403),
        };

        $perPage = max(1, min(50, $request->integer('per_page', 15)));

        $courses = $query
            ->with('lecturer:id,name')                  // cegah N+1 pada nama dosen
            ->orderBy('courses.code')
            ->paginate($perPage);

        return new CourseCollection($courses);
    }

    /**
     * GET /api/v1/courses/{course}
     * Detail + jumlah materi/tugas.
     */
    public function show(Request $request, Course $course): CourseResource
    {
        abort_unless($course->isViewableBy($request->user()), 403);

        $course->load('lecturer:id,name');

        if ($request->user()->role === 'mahasiswa') {
            // Mahasiswa tidak boleh "mengintip" jumlah tugas draft.
            $course->loadCount([
                'materials',
                'assignments as assignments_count' => fn ($q) => $q->where('status', 'published'),
            ]);
        } else {
            $course->loadCount(['materials', 'assignments']);
        }

        return new CourseResource($course);
    }
}
