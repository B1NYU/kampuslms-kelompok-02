<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MahasiswaCourseController extends Controller
{
    /**
     * Daftar mata kuliah yang DIIKUTI mahasiswa, tanpa yang berstatus draft
     * (MK draft tidak terlihat mahasiswa — Q4).
     */
    public function index(): View
    {
        $student = Auth::user();

        $courses = $student->courses()
            ->whereIn('courses.status', ['active', 'archived'])
            ->with('lecturer')
            ->withCount([
                'students',
                'assignments as published_assignments_count' => fn ($q) => $q->where('status', 'published'),
                'materials',
            ])
            ->orderBy('code')
            ->get();

        $terkumpul = Submission::query()
            ->join('assignments', 'assignments.id', '=', 'submissions.assignment_id')
            ->where('submissions.user_id', $student->id)
            ->where('assignments.status', 'published')
            ->selectRaw('assignments.course_id, COUNT(*) AS total')
            ->groupBy('assignments.course_id')
            ->pluck('total', 'course_id');

        $matkulList = $courses;

        return view()->file(
            resource_path('views/courses/index.blade.php'),
            compact('courses', 'matkulList', 'terkumpul')
        );
    }

    /**
     * Rincian MK. CoursePolicy@view memastikan mahasiswa terdaftar DAN MK bukan draft.
     * Hanya tugas published, dan hanya pengumpulan/nilai MILIKNYA yang dimuat.
     */
    public function show(Course $mata_kuliah): View
    {
        $student = Auth::user();

        Gate::authorize('view', $mata_kuliah);

        $course = $mata_kuliah->load([
            'lecturer',
            'students',
            'materials' => fn ($q) => $q->orderBy('created_at'),
            'assignments' => fn ($q) => $q->where('status', 'published')->orderBy('due_at'),
            'assignments.submissions' => fn ($q) => $q->where('user_id', $student->id),
            'assignments.submissions.grade',
        ]);

        $mataKuliah = $this->formatCourseMetadata($course);
        $nilaiData = $this->calculateGradesAndTasks($course, $student);

        return view()->file(
            resource_path('views/courses/show.blade.php'),
            compact('course', 'mataKuliah', 'nilaiData', 'student')
        );
    }

    private function formatCourseMetadata(Course $course): array
    {
        return [
            'id'             => $course->id,
            'kode'           => $course->code,
            'nama'           => $course->name,
            'sks'            => $course->sks,
            'dosen'          => $course->lecturer?->name ?? 'Dosen Pengampu',
            'deskripsi'      => $course->description ?: 'Mata kuliah kurikulum aktif semester ini.',
            'students_count' => $course->students->count(),
            'status'         => $course->status ?? 'active',
        ];
    }

    private function calculateGradesAndTasks(Course $course, ?User $student): array
    {
        $assignments = $course->assignments->sortBy('due_at')->values();
        $totalAssignments = $assignments->count();
        $items = [];
        $totalScore = 0;
        $gradedCount = 0;

        foreach ($assignments as $index => $assignment) {
            $submission = $student ? $assignment->submissions->firstWhere('user_id', $student->id) : null;
            $grade = $submission?->grade;

            // Tugas draft tidak pernah sampai ke sini (disaring di query, Q5),
            // jadi cabang "Belum Dibuka" dihapus.
            $status = 'Belum Dikumpulkan';
            if ($submission) {
                $status = $grade ? 'Dinilai' : 'Menunggu Penilaian';
            } elseif ($assignment->due_at && $assignment->due_at->isPast()) {
                $status = 'Lewat Deadline';
            }

            if ($grade) {
                $totalScore += (float) $grade->score;
                $gradedCount++;
            }

            $tanggalKumpul = 'Jadwal Belum Ditentukan';
            if ($submission && $submission->submitted_at) {
                $tanggalKumpul = $submission->submitted_at->translatedFormat('d M Y, H:i') . ' WITA';
            } elseif ($assignment->due_at) {
                $tanggalKumpul = 'Batas: ' . $assignment->due_at->translatedFormat('d M Y, H:i') . ' WITA';
            }

            $feedback = 'Silakan selesaikan dan kumpulkan sebelum batas waktu.';
            if ($grade && !empty($grade->feedback)) {
                $feedback = $grade->feedback;
            } elseif ($submission) {
                // Sebelumnya selalu tertulis "tepat waktu" walau pengumpulan terlambat.
                $feedback = $submission->is_late
                    ? 'Tugas dikumpulkan setelah batas waktu (terlambat). Sedang dalam proses evaluasi oleh dosen pengampu.'
                    : 'Tugas telah dikumpulkan tepat waktu. Sedang dalam proses evaluasi oleh dosen pengampu.';
            } elseif ($status === 'Lewat Deadline') {
                $feedback = 'Tenggat waktu pengumpulan telah berakhir. Hubungi dosen pengampu jika memerlukan dispensasi.';
            } elseif (!empty($assignment->instructions)) {
                $feedback = 'Instruksi: ' . $assignment->instructions;
            }

            $items[] = [
                'id'             => $assignment->id,
                'url'            => route('assignments.show', $assignment),
                'pertemuan'      => 'Sesi ' . ($index + 1),
                'tipe'           => 'Tugas Kuliah',
                'judul'          => $assignment->title,
                'tanggal_kumpul' => $tanggalKumpul,
                'status'         => $status,
                'terlambat'      => (bool) $submission?->is_late,   // untuk badge "Terlambat" di view
                'bobot'          => round(100 / max(1, $totalAssignments)) . '%',
                'nilai'          => $grade ? round((float) $grade->score, 1) : null,
                'feedback'       => $feedback,
            ];
        }

        $avg = $gradedCount > 0 ? round($totalScore / $gradedCount, 1) : null;
        $indeksPredikat = $this->convertScoreToGrade($avg);

        return [
            'stats' => [
                'rata_rata'      => $avg !== null ? number_format($avg, 1) : '-',
                'indeks'         => $indeksPredikat['indeks'],
                'predikat'       => $indeksPredikat['predikat'],
                'tugas_dinilai'  => "{$gradedCount} dari {$totalAssignments}",
                'bobot_tercapai' => $totalAssignments > 0 ? round(($gradedCount / $totalAssignments) * 100) . '%' : '0%',
            ],
            'items' => $items,
        ];
    }

    private function convertScoreToGrade(?float $avg): array
    {
        if ($avg === null) {
            return ['indeks' => '-', 'predikat' => 'Belum Ada Penilaian'];
        }

        if ($avg >= 85) { return ['indeks' => 'A',  'predikat' => 'Sangat Memuaskan']; }
        if ($avg >= 75) { return ['indeks' => 'B+', 'predikat' => 'Memuaskan']; }
        if ($avg >= 70) { return ['indeks' => 'B',  'predikat' => 'Baik']; }
        if ($avg >= 65) { return ['indeks' => 'C+', 'predikat' => 'Cukup Baik']; }
        if ($avg >= 60) { return ['indeks' => 'C',  'predikat' => 'Cukup']; }
        if ($avg >= 50) { return ['indeks' => 'D',  'predikat' => 'Kurang']; }

        return ['indeks' => 'E', 'predikat' => 'Tidak Lulus'];
    }
}
