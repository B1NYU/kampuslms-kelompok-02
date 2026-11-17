<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\SubmissionGraded;
use App\Http\Controllers\Controller;
use App\Http\Requests\GradeSubmissionRequest;
use App\Http\Requests\StoreSubmissionRequest;
use App\Http\Resources\GradeResource;
use App\Http\Resources\SubmissionCollection;
use App\Http\Resources\SubmissionResource;
use App\Models\Assignment;
use App\Models\Grade;
use App\Models\Submission;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    /**
     * GET /api/v1/assignments/{assignment}/submissions?page=&per_page=
     * Kontrak: dosen (pemilik). Route dibatasi role:dosen (mahasiswa 403); SubmissionPolicy@viewAny
     * lalu memastikan dosen itu pengampu MK tugas ini (dosen lain 403).
     */
    public function index(Request $request, Assignment $assignment): SubmissionCollection
    {
        Gate::authorize('viewAny', [Submission::class, $assignment]);

        $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $perPage = max(1, min(50, $request->integer('per_page', 15)));

        $submissions = $assignment->submissions()
            ->with(['student:id,name,nim_nip', 'grade'])   // tanpa N+1
            ->orderBy('submitted_at')
            ->orderBy('id')
            ->paginate($perPage);

        return new SubmissionCollection($submissions);
    }

    /**
     * POST /api/v1/assignments/{assignment}/submissions → 201 (multipart/form-data)
     * Otorisasi dan validasi ada di StoreSubmissionRequest (403 dulu, baru 422).
     * user_id SELALU dari token; input user_id dari klien diabaikan.
     */
    public function store(StoreSubmissionRequest $request, Assignment $assignment): JsonResponse
    {
        $file = $request->file('file');
        $path = $file->store("submissions/{$assignment->id}");

        try {
            $submission = $assignment->submissions()->create([
                'user_id'       => $request->user()->id,
                'file_path'     => $path,
                'original_name' => $file->getClientOriginalName(),
                'file_size'     => $file->getSize(),
                'note'          => $request->validated('note'),
                'submitted_at'  => now(),
                'is_late'       => now()->greaterThan($assignment->due_at),
            ]);
        } catch (UniqueConstraintViolationException) {
            // Permintaan ganda yang lolos bersamaan: pagar terakhir adalah unique (assignment_id, user_id).
            Storage::delete($path);

            return response()->json(['message' => 'Anda sudah mengumpulkan tugas ini.'], 409);
        }

        return (new SubmissionResource($submission))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * PUT /api/v1/submissions/{submission}/grade → 200
     * Otorisasi: SubmissionPolicy@grade (dosen pengampu, MK active) lewat GradeSubmissionRequest,
     * sebelum validasi. Batas skor mengikuti max_score tugasnya. Menilai ulang = menimpa (upsert).
     */
    public function grade(GradeSubmissionRequest $request, Submission $submission): GradeResource
    {
        $data = $request->validated();

        $grade = Grade::updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'graded_by' => $request->user()->id,
                'score'     => $data['score'],
                'feedback'  => $data['feedback'],
                'graded_at' => now(),
            ]
        );

        // Notifikasi ke mahasiswa hanya untuk nilai baru atau nilai yang benar-benar berubah.
        if ($grade->wasRecentlyCreated || $grade->wasChanged(['score', 'feedback'])) {
            SubmissionGraded::dispatch($grade, regraded: ! $grade->wasRecentlyCreated);
        }

        return new GradeResource($grade->load('grader:id,name'));
    }
}
