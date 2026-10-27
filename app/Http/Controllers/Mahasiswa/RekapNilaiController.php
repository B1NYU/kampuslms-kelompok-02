<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Rekap nilai milik mahasiswa yang sedang login, lintas mata kuliah.
 *
 * Penyaringan kepemilikan ada di query (Q8b): pengumpulan dimuat dengan
 * where('user_id', <mahasiswa login>), jadi tidak mungkin memuat nilai orang lain.
 * Hanya MK active/archived yang DIIKUTI dan hanya tugas published (sama dengan CoursePolicy@view).
 */
class RekapNilaiController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user();

        $courses = $student->courses()
            ->whereIn('courses.status', ['active', 'archived'])
            ->orderBy('courses.code')
            ->with([
                'assignments' => fn ($q) => $q->where('status', 'published')->orderBy('due_at'),
                'assignments.submissions' => fn ($q) => $q->where('user_id', $student->id),
                'assignments.submissions.grade',
            ])
            ->get();

        $totalGraded = 0;
        $totalPercent = 0.0;

        $rekap = $courses->map(function ($course) use ($student, &$totalGraded, &$totalPercent) {
            $items = [];
            $graded = 0;
            $sumPercent = 0.0;

            foreach ($course->assignments as $assignment) {
                $sub = $assignment->submissions->firstWhere('user_id', $student->id);
                $grade = $sub?->grade;

                $state = $sub ? ($grade ? 'graded' : 'waiting') : ($assignment->due_at->isPast() ? 'missed' : 'open');

                if ($grade) {
                    $percent = $assignment->max_score > 0 ? (float) $grade->score / $assignment->max_score * 100 : 0;
                    $graded++;
                    $sumPercent += $percent;
                    $totalGraded++;
                    $totalPercent += $percent;
                }

                $items[] = [
                    'assignment'    => $assignment,
                    'state'         => $state,
                    'score'         => $grade ? (float) $grade->score : null,
                    'late'          => (bool) $sub?->is_late,
                    'submission_id' => $sub?->id,
                ];
            }

            return [
                'course'  => $course,
                'items'   => $items,
                'graded'  => $graded,
                'total'   => count($items),
                'average' => $graded > 0 ? round($sumPercent / $graded, 1) : null,
            ];
        });

        $overallAverage = $totalGraded > 0 ? round($totalPercent / $totalGraded, 1) : null;

        return view('mahasiswa.rekap-nilai', compact('rekap', 'overallAverage', 'totalGraded'));
    }
}
