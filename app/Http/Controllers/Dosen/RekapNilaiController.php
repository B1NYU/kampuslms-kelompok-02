<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Rekap nilai per mata kuliah untuk dosen pengampu: tabel mahasiswa × tugas.
 *
 * Jumlah query tetap (±6) berapa pun banyaknya mahasiswa dan tugas: mahasiswa, tugas, dan
 * pengumpulan+nilai masing-masing diambil SEKALI, lalu matriksnya dirakit di PHP.
 * (Optimisasi lebih lanjut — index, cache — adalah bahan minggu 11.)
 */
class RekapNilaiController extends Controller
{
    /** /dosen/rekap-nilai — diarahkan ke mata kuliah pertama yang diampu. */
    public function landing(Request $request): RedirectResponse
    {
        $course = $request->user()->taughtCourses()->orderBy('code')->first();

        if (! $course) {
            return redirect()->route('dosen.dashboard')
                ->with('error', 'Anda belum mengampu mata kuliah apa pun.');
        }

        return redirect()->route('dosen.rekap.show', $course);
    }

    public function show(Request $request, Course $course): View
    {
        // Policy: hanya dosen pengampu MK ini. Dosen lain / mahasiswa / admin → 403.
        Gate::authorize('viewGradeRecap', $course);

        $courses = $request->user()->taughtCourses()->orderBy('code')->get(['id', 'code', 'name', 'sks']);

        // Hanya tugas published: draft belum terlihat mahasiswa, jadi belum layak direkap.
        $assignments = $course->assignments()
            ->where('status', 'published')
            ->orderBy('due_at')
            ->get(['id', 'course_id', 'title', 'due_at', 'max_score']);

        $students = $course->students()
            ->where('users.role', 'mahasiswa')
            ->orderBy('users.name')
            ->get(['users.id', 'users.name', 'users.nim_nip']);

        // Pengumpulan + nilai, hanya milik mahasiswa yang TERDAFTAR dan tugas yang ditampilkan.
        $submissions = Submission::query()
            ->whereIn('assignment_id', $assignments->pluck('id'))
            ->whereIn('user_id', $students->pluck('id'))
            ->with('grade:id,submission_id,score')
            ->get(['id', 'assignment_id', 'user_id', 'is_late'])
            ->keyBy(fn ($s) => $s->user_id . '-' . $s->assignment_id);

        $gradedCells = 0;
        $waitingCells = 0;
        $sumPercentAll = 0.0;

        $rows = $students->map(function ($student) use ($assignments, $submissions, &$gradedCells, &$waitingCells, &$sumPercentAll) {
            $cells = [];
            $graded = 0;
            $sumPercent = 0.0;

            foreach ($assignments as $assignment) {
                $sub = $submissions->get($student->id . '-' . $assignment->id);

                if (! $sub) {
                    $cells[] = ['state' => 'none'];
                    continue;
                }

                if (! $sub->grade) {
                    $waitingCells++;
                    $cells[] = ['state' => 'waiting', 'late' => (bool) $sub->is_late, 'submission_id' => $sub->id];
                    continue;
                }

                $score = (float) $sub->grade->score;
                $percent = $assignment->max_score > 0 ? $score / $assignment->max_score * 100 : 0;

                $graded++;
                $gradedCells++;
                $sumPercent += $percent;
                $sumPercentAll += $percent;

                $cells[] = [
                    'state'         => 'graded',
                    'score'         => $score,
                    'late'          => (bool) $sub->is_late,
                    'submission_id' => $sub->id,
                ];
            }

            return [
                'student'  => $student,
                'cells'    => $cells,
                'graded'   => $graded,
                'average'  => $graded > 0 ? round($sumPercent / $graded, 1) : null,
            ];
        });

        $stats = [
            'students'    => $students->count(),
            'assignments' => $assignments->count(),
            'graded'      => $gradedCells,
            'waiting'     => $waitingCells,
            'average'     => $gradedCells > 0 ? round($sumPercentAll / $gradedCells, 1) : null,
        ];

        return view('dosen.rekap-nilai', compact('course', 'courses', 'assignments', 'rows', 'stats'));
    }
}
