<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Submission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminGradeController extends Controller
{
    /**
     * Tampilkan rekapitulasi nilai seluruh mahasiswa untuk admin.
     * Mengambil data pengumpulan dan evaluasi secara dinamis via Eloquent.
     */
    public function index(): View
    {
        // Satu query berisi semua kolom yang dibutuhkan lewat LEFT JOIN (mahasiswa, tugas, mata kuliah,
        // dosen pengampu, nilai, penilai), bukan eager load 6 relasi (6 query). Akun yang sudah
        // dihapus (soft delete) tetap menghasilkan nilai null seperti relasi sebelumnya.
        $dbSubmissions = Submission::query()
            ->leftJoin('users as mhs', fn ($j) => $j->on('mhs.id', '=', 'submissions.user_id')->whereNull('mhs.deleted_at'))
            ->leftJoin('assignments', 'assignments.id', '=', 'submissions.assignment_id')
            ->leftJoin('courses', 'courses.id', '=', 'assignments.course_id')
            ->leftJoin('users as dsn', fn ($j) => $j->on('dsn.id', '=', 'courses.lecturer_id')->whereNull('dsn.deleted_at'))
            ->leftJoin('grades', 'grades.submission_id', '=', 'submissions.id')
            ->leftJoin('users as pnl', fn ($j) => $j->on('pnl.id', '=', 'grades.graded_by')->whereNull('pnl.deleted_at'))
            ->orderByDesc('submissions.submitted_at')
            ->select([
                'submissions.id',
                'submissions.submitted_at',
                'submissions.is_late',
                'submissions.original_name',
                'submissions.file_size',
                'mhs.name as student_name',
                'mhs.nim_nip as student_nim',
                'assignments.title as assignment_title',
                'courses.code as course_code',
                'courses.name as course_name',
                'dsn.name as lecturer_name',
                'grades.score as grade_score',
                'grades.feedback as grade_feedback',
                'grades.graded_at as grade_graded_at',
                'pnl.name as grader_name',
            ])
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
            $score = $sub->grade_score !== null ? (float) $sub->grade_score : null;
            $letter = $getGradeLetter($score);
            return [
                'id' => $sub->id,
                'student_name' => $sub->student_name ?? 'Mahasiswa',
                'student_nim' => $sub->student_nim ?? '10221000',
                'course_code' => $sub->course_code ?? 'MK001',
                'course_name' => $sub->course_name ?? 'Mata Kuliah',
                'lecturer_name' => $sub->lecturer_name ?? ($sub->grader_name ?? 'Dosen Pengampu'),
                'assignment_title' => $sub->assignment_title ?? 'Tugas Kuliah',
                'submitted_at' => $sub->submitted_at ? Carbon::parse($sub->submitted_at)->format('d M Y, H:i') : '-',
                'is_late' => (bool) $sub->is_late,
                'score' => $score !== null ? number_format($score, 1) : null,
                'letter' => $letter,
                'letter_class' => $getGradeClass($letter),
                'status' => $score !== null ? 'graded' : 'pending',
                'feedback' => $sub->grade_feedback ?? 'Belum ada catatan umpan balik.',
                'graded_at' => $sub->grade_graded_at ? Carbon::parse($sub->grade_graded_at)->format('d M Y, H:i') : '-',
                'file_name' => $sub->original_name ?? 'tugas_mahasiswa.pdf',
                'file_size' => $sub->file_size ? number_format($sub->file_size / 1024, 1) . ' KB' : '1.4 MB',
            ];
        });

        // KPI: total pengumpulan sudah dihitung paginator (COUNT tanpa filter = Submission::count()).
        // Statistik nilai (jumlah, rata-rata, sebaran A dan B) digabung jadi SATU query, bukan 4.
        // grades.submission_id unik dan berelasi ke submissions, jadi jumlah nilai = pengumpulan yang sudah dinilai.
        $g = DB::table('grades')->selectRaw(
            'count(*) as graded, avg(score) as avg_score, '
            . 'sum(case when score >= 80 then 1 else 0 end) as count_a, '
            . 'sum(case when score >= 65 and score < 80 then 1 else 0 end) as count_b'
        )->first();

        $totalCount = $dbSubmissions->total();
        $gradedCount = (int) $g->graded;
        $pendingCount = $totalCount - $gradedCount;
        $avgScore = round((float) $g->avg_score, 1);
        $pctGraded = $totalCount > 0 ? round(($gradedCount / $totalCount) * 100) : 0;

        // Distribusi predikat
        $countA = (int) $g->count_a;
        $countB = (int) $g->count_b;

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