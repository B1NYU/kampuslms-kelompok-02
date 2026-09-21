<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;

class CourseController extends Controller
{
    private const STATUSES = ['draft', 'active', 'archived'];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        // Whitelist: nilai di luar daftar dianggap "tidak memfilter".
        $status = $request->query('status');
        $status = in_array($status, self::STATUSES, true) ? $status : null;

        $lecturerId = $request->query('lecturer_id');
        $lecturerId = is_string($lecturerId) && ctype_digit($lecturerId) ? (int) $lecturerId : null;

        $matkulList = Course::query()
            ->with('lecturer')
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('code', 'like', $like)
                      ->orWhere('name', 'like', $like);
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($lecturerId, fn ($query) => $query->where('lecturer_id', $lecturerId))
            ->orderByDesc('created_at')
            ->orderByDesc('id') // tie-breaker agar urutan antarhalaman stabil
            ->paginate(10)
            ->withQueryString();

        $dosenList = User::where('role', 'dosen')->orderBy('name')->get(['id', 'name']);

        $filters = [
            'q'           => $search,
            'status'      => $status,
            'lecturer_id' => $lecturerId,
        ];

        return view()->file(
            resource_path('views/admin/admin.matkul.blade.php'),
            compact('matkulList', 'dosenList', 'filters')
        );
    }

    public function store(StoreCourseRequest $request)
    {
        $course = Course::create($request->validated());
        $course->load('lecturer');

        return response()->json([
            'message' => 'Mata kuliah berhasil ditambahkan.',
            'data'    => $this->transform($course),
        ], 201);
    }

    public function update(UpdateCourseRequest $request, Course $matkul)
    {
        $matkul->update($request->validated());
        $matkul->load('lecturer');

        return response()->json([
            'message' => 'Mata kuliah berhasil diperbarui.',
            'data'    => $this->transform($matkul),
        ]);
    }

    public function destroy(Course $matkul)
    {
        try {
            $matkul->delete();
        } catch (QueryException $e) {
            // 23000 = MySQL/MariaDB integrity violation, 23503 = PostgreSQL foreign key violation.
            if (in_array((string) $e->getCode(), ['23000', '23503'], true)) {
                return response()->json([
                    'message' => 'Mata kuliah tidak dapat dihapus karena masih memiliki data terkait '
                        . '(tugas, materi, atau mahasiswa). Ubah statusnya menjadi archived sebagai gantinya.',
                ], 409);
            }

            throw $e;
        }

        return response()->json([
            'message' => 'Mata kuliah berhasil dihapus.',
        ]);
    }

    private function transform(Course $course): array
    {
        return [
            'id'            => $course->id,
            'code'          => $course->code,
            'name'          => $course->name,
            'sks'           => $course->sks,
            'lecturer_id'   => $course->lecturer_id,
            'lecturer_name' => $course->lecturer?->name,
            'status'        => $course->status,
            'description'   => $course->description,
        ];
    }

    public function studentIndex(): View
    {
        $courses = Course::with(['lecturer', 'students', 'assignments'])->orderBy('code')->get();
        $matkulList = $courses;

        return view()->file(
            resource_path('views/courses/index.blade.php'),
            compact('courses', 'matkulList')
        );
    }

    public function studentShow(Course $mata_kuliah): View
    {
        $course = $mata_kuliah->loadMissing([
            'lecturer',
            'students',
            'materials',
            'assignments.submissions.grade',
        ]);

        $mataKuliah = [
            'id'             => $course->id,
            'kode'           => $course->code,
            'nama'           => $course->name,
            'sks'            => $course->sks,
            'dosen'          => $course->lecturer?->name ?? 'Dosen Pengampu',
            'deskripsi'      => $course->description ?: 'Mata kuliah kurikulum aktif semester ini.',
            'students_count' => $course->students->count(),
            'status'         => $course->status ?? 'active',
        ];

        // Ambil data mahasiswa aktif (dari auth atau fallback mahasiswa demo)
        $student = Auth::user()
            ?? User::where('role', 'mahasiswa')->where('email', 'mahasiswa@kampuslms.test')->first()
            ?? User::where('role', 'mahasiswa')->first();

        // Urutkan tugas berdasarkan tanggal deadline
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
        $indeks = '-';
        $predikat = 'Belum Ada Penilaian';

        if ($avg !== null) {
            if ($avg >= 85) { $indeks = 'A'; $predikat = 'Sangat Memuaskan'; }
            elseif ($avg >= 75) { $indeks = 'B+'; $predikat = 'Memuaskan'; }
            elseif ($avg >= 70) { $indeks = 'B'; $predikat = 'Baik'; }
            elseif ($avg >= 65) { $indeks = 'C+'; $predikat = 'Cukup Baik'; }
            elseif ($avg >= 60) { $indeks = 'C'; $predikat = 'Cukup'; }
            elseif ($avg >= 50) { $indeks = 'D'; $predikat = 'Kurang'; }
            else { $indeks = 'E'; $predikat = 'Tidak Lulus'; }
        }

        $nilaiData = [
            'stats' => [
                'rata_rata'      => $avg !== null ? number_format($avg, 1) : '-',
                'indeks'         => $indeks,
                'predikat'       => $predikat,
                'tugas_dinilai'  => "{$gradedCount} dari {$totalAssignments}",
                'bobot_tercapai' => $totalAssignments > 0 ? round(($gradedCount / $totalAssignments) * 100) . '%' : '0%',
            ],
            'items' => $items,
        ];

        return view()->file(
            resource_path('views/courses/show.blade.php'),
            compact('course', 'mataKuliah', 'nilaiData', 'student')
        );
    }
}
