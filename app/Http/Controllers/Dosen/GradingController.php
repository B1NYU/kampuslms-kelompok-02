<?php

namespace App\Http\Controllers\Dosen;

use App\Events\SubmissionGraded;
use App\Http\Controllers\Controller;
use App\Http\Requests\GradeSubmissionRequest;
use App\Models\Grade;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradingController extends Controller
{
    /**
     * Pengumpulan dari mata kuliah AKTIF yang diampu dosen login.
     * Penyaringan baris dilakukan di query (taughtCourses), sejalan dengan
     * aturan SubmissionPolicy@grade: hanya MK active yang bisa dinilai.
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
     * Simpan (baru / ubah) nilai & feedback. Otorisasi 'grade' dan validasi
     * dijalankan GradeSubmissionRequest sebelum method ini dipanggil.
     */
    public function update(GradeSubmissionRequest $request, Submission $submission): JsonResponse|RedirectResponse
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

        // Beri tahu mahasiswa hanya bila ada nilai baru atau skor/feedback benar-benar berubah
        // (menyimpan ulang tanpa perubahan tidak boleh mengirim notifikasi berulang).
        if ($grade->wasRecentlyCreated || $grade->wasChanged(['score', 'feedback'])) {
            SubmissionGraded::dispatch($grade, regraded: ! $grade->wasRecentlyCreated);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Nilai dan feedback berhasil disimpan.',
                'grade'   => [
                    'score'    => (float) $grade->score,
                    'feedback' => $grade->feedback,
                ],
            ]);
        }

        return back()->with('success', 'Nilai dan umpan balik berhasil disimpan.');
    }
}
