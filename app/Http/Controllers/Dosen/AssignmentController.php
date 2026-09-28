<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Pengelolaan tugas oleh dosen. Middleware role:dosen hanya membuka pintu;
 * dosen hanya boleh mengelola tugas di mata kuliah yang ia AMPU.
 */
class AssignmentController extends Controller
{
    public function index(Course $course): View
    {
        $this->authorizeCourse($course);

        $assignments = $course->assignments()->withCount('submissions')->orderBy('due_at')->get();

        return view('dosen.assignments.index', compact('course', 'assignments'));
    }

    public function create(Course $course): View
    {
        $this->authorizeCourse($course);

        return view('dosen.assignments.form', ['course' => $course, 'assignment' => new Assignment()]);
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        $this->authorizeCourse($course);

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
        $this->authorizeAssignment($assignment);

        return view('dosen.assignments.form', ['course' => $assignment->course, 'assignment' => $assignment]);
    }

    public function update(Request $request, Assignment $assignment): RedirectResponse
    {
        $this->authorizeAssignment($assignment);

        $assignment->update($this->validated($request));

        return redirect()->route('dosen.courses.assignments.index', $assignment->course_id)
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Assignment $assignment): RedirectResponse
    {
        $this->authorizeAssignment($assignment);

        if ($assignment->submissions()->exists()) {
            return back()->with('error', 'Tugas tidak dapat dihapus karena sudah ada pengumpulan mahasiswa.');
        }

        $courseId = $assignment->course_id;
        $assignment->delete();

        return redirect()->route('dosen.courses.assignments.index', $courseId)
            ->with('success', 'Tugas berhasil dihapus.');
    }

    private function authorizeCourse(Course $course): void
    {
        abort_unless($course->isTaughtBy(auth()->user()), 403);
    }

    private function authorizeAssignment(Assignment $assignment): void
    {
        abort_unless($assignment->isManageableBy(auth()->user()), 403);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'instructions' => ['required', 'string'],
            'due_at'       => ['required', 'date'],
            'max_score'    => ['required', 'integer', 'min:1', 'max:100'],
            'allow_late'   => ['sometimes', 'boolean'],
            'status'       => ['required', Rule::in(['draft', 'published'])],
        ]) + ['allow_late' => $request->boolean('allow_late')];
    }
}
