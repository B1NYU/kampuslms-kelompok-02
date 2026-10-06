<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AssignmentCollection;
use App\Http\Resources\AssignmentResource;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class AssignmentController extends Controller
{
    /**
     * GET /api/v1/courses/{course}/assignments?status=&page=
     */
    public function index(Request $request, Course $course): AssignmentCollection
    {
        $user = $request->user();

        abort_unless($course->isViewableBy($user), 403);

        $request->validate([
            'status'   => ['nullable', Rule::in(['draft', 'published'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $isStudent = $user->role === 'mahasiswa';
        $perPage   = max(1, min(50, $request->integer('per_page', 15)));

        $query = $course->assignments()
            ->when($isStudent, fn ($q) => $q->where('status', 'published')) // draft tak terlihat mahasiswa
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->orderBy('due_at')
            ->orderBy('id');

        if ($isStudent) {
            // 1 query tambahan untuk SEMUA tugas di halaman, bukan 1 per tugas.
            $query->with(['submissions' => fn ($q) => $q
                ->where('user_id', $user->id)
                ->select('id', 'assignment_id', 'user_id', 'is_late')]);
        } else {
            $query->withCount('submissions');
        }

        return new AssignmentCollection($query->paginate($perPage));
    }

    /**
     * POST /api/v1/assignments → 201
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Cek peran dasar
        abort_unless($request->user()->role === 'dosen', 403);

        // 2. Cek kepemilikan Course TERLEBIH DAHULU jika course_id dikirimkan
        if ($courseId = $request->input('course_id')) {
            $course = Course::find($courseId);
            
            // Jika course ditemukan tetapi tidak diampu oleh dosen yang login -> AUTO 403!
            if ($course) {
                abort_unless($course->isTaughtBy($request->user()), 403);
            }
        }

        // 3. BARU JALANKAN VALIDASI DATA
        $data = $request->validate([
            'course_id'    => ['required', 'integer', 'exists:courses,id'],
            'title'        => ['required', 'string', 'max:255'],
            'instructions' => ['required', 'string'],
            'due_at'       => ['required', 'date'],
            'max_score'    => ['sometimes', 'integer', 'between:1,100'],
            'allow_late'   => ['sometimes', 'boolean'],
            'status'       => ['sometimes', Rule::in(['draft', 'published'])],
        ]);

        // Ambil ulang instance course dari data yang ter-validasi
        $course = Course::findOrFail($data['course_id']);

        // 4. Simpan assignment
        $assignment = $course->assignments()->create([
            'created_by'   => $request->user()->id,
            'title'        => $data['title'],
            'instructions' => $data['instructions'],
            'due_at'       => $data['due_at'],
            'status'       => $data['status'] ?? 'draft',
        ] + Arr::only($data, ['max_score', 'allow_late']));

        return (new AssignmentResource($assignment->refresh()))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * PUT|PATCH /api/v1/assignments/{assignment} → 200
     */
    public function update(Request $request, Assignment $assignment): AssignmentResource
    {
        // Cek kepemilikan DULU, sebelum validasi.
        abort_unless($assignment->isManageableBy($request->user()), 403);

        // PUT = semua field wajib; PATCH = hanya field yang dikirim yang divalidasi.
        $presence = $request->isMethod('PATCH') ? ['sometimes', 'required'] : ['required'];

        // course_id sengaja tidak ada: tugas tidak boleh dipindah antar mata kuliah.
        $data = $request->validate([
            'title'        => [...$presence, 'string', 'max:255'],
            'instructions' => [...$presence, 'string'],
            'due_at'       => [...$presence, 'date'],
            'max_score'    => [...$presence, 'integer', 'between:1,100'],
            'allow_late'   => [...$presence, 'boolean'],
            'status'       => [...$presence, Rule::in(['draft', 'published'])],
        ]);

        $assignment->update($data);

        return new AssignmentResource($assignment->refresh());
    }

    /**
     * DELETE /api/v1/assignments/{assignment} → 204
     * Ditolak (409) bila sudah ada pengumpulan — sama dengan perilaku versi web.
     */
    public function destroy(Request $request, Assignment $assignment): JsonResponse|Response
    {
        abort_unless($assignment->isManageableBy($request->user()), 403);

        if ($assignment->submissions()->exists()) {
            return response()->json([
                'message' => 'Tugas tidak dapat dihapus karena sudah ada pengumpulan mahasiswa.',
            ], 409);
        }

        $assignment->delete();

        return response()->noContent();
    }
}
