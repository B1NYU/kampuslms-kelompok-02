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
 *
 * Tampilan: satu halaman (dosen/tugas.blade.php) berisi form + daftar tugas.
 */
class AssignmentController extends Controller
{
    /**
     * /dosen/tugas — pintu masuk dari menu "Buat Tugas".
     * Diarahkan ke mata kuliah pertama yang diampu dosen.
     */
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
        $this->authorizeCourse($course);

        return $this->page($course, new Assignment());
    }

    /**
     * Form tambah sudah ada di halaman index, jadi cukup diarahkan ke sana.
     * (Method ini dipertahankan agar route resource lama tetap valid.)
     */
    public function create(Course $course): RedirectResponse
    {
        $this->authorizeCourse($course);

        return redirect()->route('dosen.courses.assignments.index', $course);
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

    /** Halaman yang sama dengan index, form terisi data tugas yang diubah. */
    public function edit(Assignment $assignment): View
    {
        $this->authorizeAssignment($assignment);

        return $this->page($assignment->course, $assignment);
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

    /** Data untuk view: dropdown mata kuliah, daftar tugas, dan tugas yang sedang diubah. */
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
        // Form memakai dua input (tanggal + jam); digabung menjadi satu nilai due_at.
        // Checkbox yang tidak dicentang tidak dikirim browser, jadi dipaksa menjadi boolean.
        // Keduanya di-merge ke request supaya ikut tersimpan sebagai old input
        // bila validasi gagal.
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
        ]);
    }
}
