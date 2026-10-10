<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Pengelolaan tugas oleh dosen. Middleware role:dosen hanya membuka pintu;
 * seluruh aturan akses (pengampu, status MK, penghapusan) ada di AssignmentPolicy.
 */
class AssignmentController extends Controller
{
    /** /dosen/tugas — diarahkan ke mata kuliah pertama yang diampu. */
    public function landing(Request $request): RedirectResponse
    {
        $course = $request->user()->taughtCourses()->orderBy('code')->first();

        if (! $course) {
            return redirect()->route('dosen.dashboard')
                ->with('error', 'Anda belum mengampu mata kuliah apa pun.');
        }

        return redirect()->route('dosen.courses.assignments.index', $course);
    }

    public function index(Course $course): View
    {
        Gate::authorize('viewAny', [Assignment::class, $course]);

        return $this->page($course, new Assignment());
    }

    /** Form tambah ada di halaman index; method ini menjaga route resource lama tetap valid. */
    public function create(Course $course): RedirectResponse
    {
        Gate::authorize('create', [Assignment::class, $course]);

        return redirect()->route('dosen.courses.assignments.index', $course);
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        Gate::authorize('create', [Assignment::class, $course]);

        $assignment = new Assignment($this->validated($request));
        // course_id dan created_by dari server, bukan dari input pengguna.
        $assignment->course_id = $course->id;
        $assignment->created_by = $request->user()->id;
        $assignment->save();

        return redirect()->route('dosen.courses.assignments.index', $course)
            ->with('success', 'Tugas berhasil dibuat.');
    }

    public function edit(Assignment $assignment): View
    {
        Gate::authorize('update', $assignment);

        return $this->page($assignment->course, $assignment);
    }

    public function update(Request $request, Assignment $assignment): RedirectResponse
    {
        Gate::authorize('update', $assignment);

        $assignment->update($this->validated($request));

        return redirect()->route('dosen.courses.assignments.index', $assignment->course_id)
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Assignment $assignment): RedirectResponse
    {
        // Tugas yang sudah punya pengumpulan ditolak 409 oleh policy; handler di
        // bootstrap/app.php mengubahnya menjadi redirect back()->with('error', ...).
        Gate::authorize('delete', $assignment);

        $courseId = $assignment->course_id;
        $assignment->delete();

        return redirect()->route('dosen.courses.assignments.index', $courseId)
            ->with('success', 'Tugas berhasil dihapus.');
    }

    private function page(Course $course, Assignment $assignment): View
    {
        $courses = auth()->user()->taughtCourses()->orderBy('code')->get();

        $assignments = $course->assignments()
            ->withCount('submissions')
            ->orderBy('due_at')
            ->get();

        $studentCount = $course->students()->count();

        return view('dosen.tugas', compact('courses', 'course', 'assignments', 'assignment', 'studentCount'));
    }

    private function validated(Request $request): array
    {
        $request->merge([
            'due_at' => $request->filled('due_date') && $request->filled('due_time')
                ? $request->input('due_date') . ' ' . $request->input('due_time')
                : $request->input('due_at'),
            'allow_late' => $request->boolean('allow_late'),
        ]);

        return $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'instructions' => ['required', 'string'],
            'due_at'       => ['required', 'date'],
            'max_score'    => ['required', 'integer', 'min:1', 'max:100'],
            'allow_late'   => ['boolean'],
            'status'       => ['required', Rule::in(['draft', 'published'])],
        ], [], [
            'title'        => 'judul tugas',
            'instructions' => 'instruksi',
            'due_at'       => 'batas waktu',
            'max_score'    => 'nilai maksimal',
            'allow_late'   => 'boleh keterlambatan',
            'status'       => 'status',
        ]);
    }
}
