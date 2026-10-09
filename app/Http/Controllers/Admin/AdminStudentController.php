<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminStudentController extends Controller
{
    /**
     * Halaman pendaftaran: semua mata kuliah beserta mahasiswanya.
     */
    public function index(Request $request): View
    {
        // Eager load agar tidak N+1: 1 query MK, 1 query dosen, 1 query mahasiswa.
        $courses = Course::with(['lecturer', 'students'])
            ->withCount('students')
            ->orderBy('code')
            ->get();

        $mahasiswaList = User::where('role', 'mahasiswa')
            ->orderBy('name')
            ->get(['id', 'name', 'nim_nip']);

        // Filter MK yang sedang aktif (?mk=KODE), default semua.
        $selectedCode = $courses->firstWhere('code', $request->query('mk'))?->code ?? 'all';

        // Blade ini diakses lewat file path (nama file memakai titik), bukan nama view.
        return view()->file(
            resource_path('views/admin/admin.pendaftaran.blade.php'),
            compact('courses', 'mahasiswaList', 'selectedCode')
        );
    }

    /**
     * Daftarkan mahasiswa ke mata kuliah.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'mahasiswa')->whereNull('deleted_at'),
            ],
            'course_id' => [
                'required',
                'integer',
                Rule::exists('courses', 'id')->where('status', 'active'),
            ],
        ], [
            'student_id.required' => 'Silakan pilih mahasiswa.',
            'student_id.exists'   => 'Mahasiswa yang dipilih tidak ditemukan.',
            'course_id.required'  => 'Silakan pilih mata kuliah.',
            'course_id.exists'    => 'Mata kuliah tidak ditemukan atau belum berstatus aktif.',
        ]);

        $course  = Course::findOrFail($data['course_id']);
        $student = User::findOrFail($data['student_id']);

        // Cegah enroll ganda dengan pesan yang jelas
        // (unique composite course_id + user_id di database tetap jadi pengaman terakhir).
        if ($course->isEnrolledBy($student)) {
            return back()
                ->withInput()
                ->withErrors(['student_id' => "{$student->name} sudah terdaftar di {$course->code}."]);
        }

        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        return redirect()
            ->route('admin.pendaftaran', ['mk' => $course->code])
            ->with('success', "{$student->name} ({$student->nim_nip}) berhasil didaftarkan ke {$course->code}.");
    }

    /**
     * Batalkan pendaftaran mahasiswa dari mata kuliah.
     */
    public function destroy(Course $course, User $student): RedirectResponse
    {
        $removed = $course->students()->detach($student->id);

        if ($removed === 0) {
            return back()->withErrors([
                'enroll' => "{$student->name} tidak terdaftar di {$course->code}.",
            ]);
        }

        return redirect()
            ->route('admin.pendaftaran', ['mk' => $course->code])
            ->with('success', "Pendaftaran {$student->name} dari {$course->code} dibatalkan.");
    }
}
