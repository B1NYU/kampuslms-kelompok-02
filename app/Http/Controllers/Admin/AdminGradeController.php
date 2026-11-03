<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Submission;
use Carbon\Carbon;
use Illuminate\View\View;

class AdminGradeController extends Controller
{
    /**
     * Tampilkan rekapitulasi nilai seluruh mahasiswa untuk admin.
     * Mengambil data pengumpulan dan evaluasi secara dinamis via Eloquent.
     */
    public function index(): View
    {
        $dbSubmissions = Submission::with(['student', 'assignment.course.lecturer', 'grade.grader'])
            ->latest('submitted_at')
            ->paginate(10)
            ->withQueryString();

        // Helper predikat huruf mutu
        $getGradeLetter = function ($score) {
            if ($score === null) return '-';
            $s = (float) $score;
            if ($s >= 85) return 'A';
            if ($s >= 80) return 'A-';
            if ($s >= 75) return 'B+';
            if ($s >= 70) return 'B';
            if ($s >= 65) return 'B-';
            if ($s >= 60) return 'C+';
            if ($s >= 55) return 'C';
            if ($s >= 40) return 'D';
            return 'E';
        };

        // Helper CSS class predikat
        $getGradeClass = function ($letter) {
            if (str_starts_with($letter, 'A')) return 'grade-pill-a';
            if (str_starts_with($letter, 'B')) return 'grade-pill-b';
            if (str_starts_with($letter, 'C')) return 'grade-pill-c';
            if (in_array($letter, ['D', 'E'])) return 'grade-pill-d';
            return 'grade-pill-pending';
        };

        $gradeItems = $dbSubmissions->through(function ($sub) use ($getGradeLetter, $getGradeClass) {
            $score = $sub->grade ? (float) $sub->grade->score : null;
            $letter = $getGradeLetter($score);
            return [
                'id' => $sub->id,
                'student_name' => $sub->student?->name ?? 'Mahasiswa',
                'student_nim' => $sub->student?->nim_nip ?? '10221000',
                'course_code' => $sub->assignment?->course?->code ?? 'MK001',
                'course_name' => $sub->assignment?->course?->name ?? 'Mata Kuliah',
                'lecturer_name' => $sub->assignment?->course?->lecturer?->name ?? ($sub->grade?->grader?->name ?? 'Dosen Pengampu'),
                'assignment_title' => $sub->assignment?->title ?? 'Tugas Kuliah',
                'submitted_at' => $sub->submitted_at ? Carbon::parse($sub->submitted_at)->format('d M Y, H:i') : '-',
                'is_late' => (bool) $sub->is_late,
                'score' => $score !== null ? number_format($score, 1) : null,
                'letter' => $letter,
                'letter_class' => $getGradeClass($letter),
                'status' => $sub->grade ? 'graded' : 'pending',
                'feedback' => $sub->grade?->feedback ?? 'Belum ada catatan umpan balik.',
                'graded_at' => $sub->grade?->graded_at ? Carbon::parse($sub->grade->graded_at)->format('d M Y, H:i') : '-',
                'file_name' => $sub->original_name ?? 'tugas_mahasiswa.pdf',
                'file_size' => $sub->file_size ? number_format($sub->file_size / 1024, 1) . ' KB' : '1.4 MB',
            ];
        });

        // Kalkulasi KPI statistik dari database
        $totalCount = Submission::count();
        $gradedCount = Submission::has('grade')->count();
        $pendingCount = $totalCount - $gradedCount;
        $avgScore = round((float) \App\Models\Grade::avg('score'), 1);
        $pctGraded = $totalCount > 0 ? round(($gradedCount / $totalCount) * 100) : 0;

        // Distribusi predikat
        $countA = \App\Models\Grade::where('score', '>=', 80)->count();
        $countB = \App\Models\Grade::where('score', '>=', 65)->where('score', '<', 80)->count();

        // Opsi unik mata kuliah untuk filter
        $uniqueCourses = Course::orderBy('code')->pluck('name', 'code');

        return view()->file(
            resource_path('views/admin/admin.nilai.blade.php'),
            compact(
                'gradeItems',
                'totalCount',
                'gradedCount',
                'pendingCount',
                'avgScore',
                'pctGraded',
                'countA',
                'countB',
                'uniqueCourses'
            )
        );
    }
}
