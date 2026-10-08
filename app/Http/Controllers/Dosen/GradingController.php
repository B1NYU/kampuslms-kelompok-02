<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradingController extends Controller
{
    /**
     * Halaman Penilaian & Feedback: pengumpulan dari mata kuliah aktif yang diampu dosen login.
     */
    public function index(Request $request): View
    {
        $courses = $request->user()
            ->taughtCourses()
            ->where('status', 'active')
            ->orderBy('code')
            ->get();

        $allSubmissions = Submission::query()
            ->whereHas('assignment', fn ($q) => $q->whereIn('course_id', $courses->pluck('id')))
            ->with(['assignment.course', 'student', 'grade'])
            ->latest('submitted_at')
            ->get();

        $totalSubmissions = $allSubmissions->count();
        $totalGraded      = $allSubmissions->filter(fn ($s) => $s->grade !== null)->count();
        $totalPending     = $totalSubmissions - $totalGraded;
        $persenGraded     = $totalSubmissions > 0 ? (int) round(($totalGraded / $totalSubmissions) * 100) : 0;

        return view('dosen.penilaian', compact(
            'courses',
            'allSubmissions',
            'totalSubmissions',
            'totalGraded',
            'totalPending',
            'persenGraded',
        ));
    }

    /**
     * Simpan (baru / ubah) nilai & feedback untuk satu pengumpulan. Dipanggil via fetch (JSON).
     */
    public function update(Request $request, Submission $submission): JsonResponse
    {
        $submission->loadMissing('assignment.course');

        // Hanya dosen pengampu mata kuliah tersebut yang boleh menilai.
        abort_unless($submission->assignment?->course?->isTaughtBy($request->user()), 403);

        $data = $request->validate([
            'score'    => ['required', 'numeric', 'min:0', 'max:100'],
            'feedback' => ['required', 'string', 'max:2000'],
        ], [
            'score.required'    => 'Nilai wajib diisi.',
            'score.numeric'     => 'Nilai harus berupa angka.',
            'score.min'         => 'Nilai minimal 0.',
            'score.max'         => 'Nilai maksimal 100.',
            'feedback.required' => 'Feedback wajib diisi.',
            'feedback.max'      => 'Feedback maksimal 2000 karakter.',
        ]);

        $grade = Grade::updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'graded_by' => $request->user()->id,
                'score'     => $data['score'],
                'feedback'  => $data['feedback'],
                'graded_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Nilai dan feedback berhasil disimpan.',
            'grade'   => [
                'score'    => (float) $grade->score,
                'feedback' => $grade->feedback,
            ],
        ]);
    }
}
