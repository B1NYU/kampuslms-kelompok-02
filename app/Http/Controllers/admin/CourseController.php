<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index(): View
    {
        $matkulList = Course::with('lecturer')->orderByDesc('created_at')->get();
        $dosenList = User::where('role', 'dosen')->orderBy('name')->get(['id', 'name']);

        return view()->file(
            resource_path('views/admin/admin.matkul.blade.php'),
            compact('matkulList', 'dosenList')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'        => ['required', 'string', 'max:20', 'unique:courses,code'],
            'name'        => ['required', 'string', 'max:255'],
            'sks'         => ['required', 'integer', 'min:1', 'max:6'],
            'lecturer_id' => ['required', 'exists:users,id'],
            'status'      => ['required', Rule::in(['draft', 'active', 'archived'])],
            'description' => ['nullable', 'string'],
        ]);

        $course = Course::create($validated);
        $course->load('lecturer');

        return response()->json([
            'message' => 'Mata kuliah berhasil ditambahkan.',
            'data'    => $this->transform($course),
        ], 201);
    }

    public function update(Request $request, Course $matkul)
    {
        $validated = $request->validate([
            'code'        => ['required', 'string', 'max:20', Rule::unique('courses', 'code')->ignore($matkul->id)],
            'name'        => ['required', 'string', 'max:255'],
            'sks'         => ['required', 'integer', 'min:1', 'max:6'],
            'lecturer_id' => ['required', 'exists:users,id'],
            'status'      => ['required', Rule::in(['draft', 'active', 'archived'])],
            'description' => ['nullable', 'string'],
        ]);

        $matkul->update($validated);
        $matkul->load('lecturer');

        return response()->json([
            'message' => 'Mata kuliah berhasil diperbarui.',
            'data'    => $this->transform($matkul),
        ]);
    }

    public function destroy(Course $matkul)
    {
        $matkul->delete();

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
        $student = auth()->user()
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
