<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Halaman Kelola Mahasiswa: hanya mata kuliah yang diampu dosen login.
     */
    public function index(Request $request): View
    {
        $courses = $request->user()
            ->taughtCourses()
            ->with('students')
            ->orderBy('code')
            ->get();

        // Untuk auto-isi nama berdasarkan NIM di modal pendaftaran.
        $registeredStudents = User::where('role', 'mahasiswa')
            ->select('id', 'name', 'nim_nip')
            ->get();

        // MK yang terpilih di filter: ?course=KODE, default MK pertama.
        $selectedCode = $courses->firstWhere('code', $request->query('course'))?->code
            ?? $courses->first()?->code;

        return view('dosen.mahasiswa', compact('courses', 'registeredStudents', 'selectedCode'));
    }

    /**
     * Daftarkan mahasiswa (berdasarkan NIM) ke mata kuliah.
     * CoursePolicy@manageEnrollment: dosen pengampu, MK draft atau active.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'nim'       => ['required', 'string', 'max:50'],
        ], [
            'course_id.required' => 'Silakan pilih mata kuliah.',
            'course_id.exists'   => 'Mata kuliah yang dipilih tidak ditemukan.',
            'nim.required'       => 'NIM wajib diisi.',
        ]);

        $course = Course::findOrFail($data['course_id']);

        Gate::authorize('manageEnrollment', $course);

        $student = User::where('role', 'mahasiswa')
            ->where('nim_nip', trim($data['nim']))
            ->first();

        if (! $student) {
            return back()
                ->withInput()
                ->withErrors(['nim' => 'NIM tidak terdaftar sebagai mahasiswa di sistem.']);
        }

        if ($course->isEnrolledBy($student)) {
            return back()
                ->withInput()
                ->withErrors(['nim' => "{$student->name} sudah terdaftar di mata kuliah ini."]);
        }

        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        return redirect()
            ->route('dosen.mahasiswa', ['course' => $course->code])
            ->with('success', "Mahasiswa {$student->name} ({$student->nim_nip}) berhasil didaftarkan ke {$course->code}.");
    }

    /**
     * Keluarkan mahasiswa dari mata kuliah. Submission dan nilainya tetap
     * tersimpan untuk dosen dan admin; mahasiswa kehilangan akses (Q3b).
     */
    public function destroy(Request $request, Course $course, User $student): RedirectResponse
    {
        Gate::authorize('manageEnrollment', $course);

        $course->students()->detach($student->id);

        return redirect()
            ->route('dosen.mahasiswa', ['course' => $course->code])
            ->with('success', "{$student->name} telah dikeluarkan dari {$course->code}.");
    }
}
