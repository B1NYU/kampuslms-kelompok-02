<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Support\DosenDashboardStats;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminStudentController extends Controller
{
    /**
     * Halaman pendaftaran: semua mata kuliah beserta mahasiswanya.
     */
    public function index(Request $request): View
    {
        $courses = Course::with('lecturer')->withCount('students')->orderBy('code')->get();
        $activeCourses = $courses->where('status', 'active');
        $coursesById = $courses->keyBy('id');

        $mahasiswaList = User::where('role', 'mahasiswa')
            ->orderBy('name')
            ->get(['id', 'name', 'nim_nip']);

        $selectedCode = $request->query('mk', 'all');

        $rows = DB::table('course_user')
            ->join('users', 'users.id', '=', 'course_user.user_id')
            ->join('courses', 'courses.id', '=', 'course_user.course_id')
            ->whereNull('users.deleted_at')
            ->when($selectedCode !== 'all', fn ($q) => $q->where('courses.code', $selectedCode))
            ->orderBy('courses.code')->orderBy('users.name')
            ->select('course_user.course_id', 'course_user.enrolled_at', 'users.id as student_id', 'users.name', 'users.nim_nip')
            ->paginate(10)->withQueryString();

        return view()->file(
            resource_path('views/admin/admin.pendaftaran.blade.php'),
            compact('courses', 'activeCourses', 'coursesById', 'mahasiswaList', 'selectedCode', 'rows')
        );
    }

    /**
     * Daftarkan mahasiswa ke mata kuliah. MK draft boleh (dosen perlu mendaftarkan
     * mahasiswa sebelum MK dibuka), MK archived tidak.
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
                Rule::exists('courses', 'id')->whereIn('status', ['draft', 'active']),
            ],
        ], [
            'student_id.required' => 'Silakan pilih mahasiswa.',
            'student_id.exists'   => 'Mahasiswa yang dipilih tidak ditemukan.',
            'course_id.required'  => 'Silakan pilih mata kuliah.',
            'course_id.exists'    => 'Mata kuliah tidak ditemukan atau sudah diarsipkan.',
        ]);

        $course  = Course::findOrFail($data['course_id']);
        $student = User::findOrFail($data['student_id']);

        Gate::authorize('manageEnrollment', $course);

        // Cegah enroll ganda dengan pesan yang jelas
        // (unique composite course_id + user_id di database tetap jadi pengaman terakhir).
        if ($course->isEnrolledBy($student)) {
            return back()
                ->withInput()
                ->withErrors(['student_id' => "{$student->name} sudah terdaftar di {$course->code}."]);
        }

        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        // attach() tidak memicu event model, jadi cache statistik dashboard dosen dibersihkan manual.
        DosenDashboardStats::forgetForLecturer($course->lecturer_id);

        return redirect()
            ->route('admin.pendaftaran', ['mk' => $course->code])
            ->with('success', "{$student->name} ({$student->nim_nip}) berhasil didaftarkan ke {$course->code}.");
    }

    /**
     * Batalkan pendaftaran mahasiswa dari mata kuliah.
     * Submission dan nilainya tidak dihapus; mahasiswa hanya kehilangan akses (Q3b).
     */
    public function destroy(Course $course, User $student): RedirectResponse
    {
        Gate::authorize('manageEnrollment', $course);

        $removed = $course->students()->detach($student->id);

        // detach() juga tidak memicu event model.
        DosenDashboardStats::forgetForLecturer($course->lecturer_id);

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
