<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MahasiswaCourseController extends Controller
{
    /**
     * Tampilkan katalog seluruh mata kuliah untuk mahasiswa.
     */
    public function index(): View
    {
        $courses = Course::with(['lecturer', 'students', 'assignments'])->orderBy('code')->get();
        $matkulList = $courses;

        return view()->file(
            resource_path('views/courses/index.blade.php'),
            compact('courses', 'matkulList')
        );
    }

    /**
     * Tampilkan rincian alur 16 sesi perkuliahan dan rekapitulasi nilai tugas mahasiswa.
     */
    public function show(Course $mata_kuliah): View
    {
        $course = $mata_kuliah->loadMissing([
            'lecturer',
            'students',
            'materials',
            'assignments.submissions.grade',
        ]);

        $student = $this->resolveActiveStudent();
        $mataKuliah = $this->formatCourseMetadata($course);
        $nilaiData = $this->calculateGradesAndTasks($course, $student);

        return view()->file(
            resource_path('views/courses/show.blade.php'),
            compact('course', 'mataKuliah', 'nilaiData', 'student')
        );
    }

    /**
     * Helper: Ambil data mahasiswa aktif (Auth, session user_name, atau fallback aman database).
     */
    private function resolveActiveStudent(): ?User
    {
        try {
            if (Auth::check()) {
                return Auth::user();
            }

            if (session()->has('user_name')) {
                $user = User::where('name', session('user_name'))->first();
                if ($user) {
                    return $user;
                }
            }

            return User::where('role', 'mahasiswa')->where('email', 'mahasiswa@kampuslms.test')->first()
                ?? User::where('role', 'mahasiswa')->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Helper: Format ringkasan metadata mata kuliah untuk tampilan detail.
     */
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

    /**
     * Helper: Kalkulasi status pengerjaan tugas, deadline WITA, umpan balik dosen, dan nilai.
     */
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

            $status = 'Belum Dikumpulkan';
            if ($assignment->status === 'draft') {
                $status = 'Belum Dibuka';
            } elseif ($submission) {
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

            $feedback = 'Tugas belum dibuka untuk pengumpulan.';
            if ($grade && !empty($grade->feedback)) {
                $feedback = $grade->feedback;
            } elseif ($submission) {
                $feedback = 'Tugas telah dikumpulkan tepat waktu. Sedang dalam proses evaluasi oleh dosen pengampu.';
            } elseif ($status === 'Lewat Deadline') {
                $feedback = 'Tenggat waktu pengumpulan telah berakhir. Hubungi dosen pengampu jika memerlukan dispensasi.';
            } elseif ($status === 'Belum Dikumpulkan') {
                $feedback = !empty($assignment->instructions) ? 'Instruksi: ' . $assignment->instructions : 'Silakan selesaikan dan kumpulkan sebelum batas waktu.';
            }

            $items[] = [
                'pertemuan'      => 'Sesi ' . ($index + 1),
                'tipe'           => 'Tugas Kuliah',
                'judul'          => $assignment->title,
                'tanggal_kumpul' => $tanggalKumpul,
                'status'         => $status,
                'bobot'          => round(100 / max(1, $totalAssignments)) . '%',
                'nilai'          => $grade ? round((float) $grade->score, 1) : null,
                'feedback'       => $feedback,
            ];
        }

        // Kalkulasi nilai rata-rata dan indeks prestasi
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

    /**
     * Helper: Konversi nilai numerik (0-100) ke Indeks Huruf & Predikat Akademik.
     */
    private function convertScoreToGrade(?float $avg): array
    {
        if ($avg === null) {
            return [
                'indeks'   => '-',
                'predikat' => 'Belum Ada Penilaian',
            ];
        }

        if ($avg >= 85) {
            return ['indeks' => 'A',  'predikat' => 'Sangat Memuaskan'];
        }
        if ($avg >= 75) {
            return ['indeks' => 'B+', 'predikat' => 'Memuaskan'];
        }
        if ($avg >= 70) {
            return ['indeks' => 'B',  'predikat' => 'Baik'];
        }
        if ($avg >= 65) {
            return ['indeks' => 'C+', 'predikat' => 'Cukup Baik'];
        }
        if ($avg >= 60) {
            return ['indeks' => 'C',  'predikat' => 'Cukup'];
        }
        if ($avg >= 50) {
            return ['indeks' => 'D',  'predikat' => 'Kurang'];
        }

        return ['indeks' => 'E', 'predikat' => 'Tidak Lulus'];
    }
}
