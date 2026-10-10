<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminAssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $coursesList = Course::with('lecturer')->orderBy('code')->get();

        $assignments = Assignment::with(['course.lecturer', 'creator'])
            ->withCount('submissions')
            ->orderBy('due_at')
            ->get();

        $totalAssignments = $assignments->count();
        $totalPublished   = $assignments->where('status', 'published')->count();
        $totalDraft       = $assignments->where('status', 'draft')->count();
        $totalSubmissions = $assignments->sum('submissions_count');

        return view()->file(resource_path('views/admin/admin.tugas.blade.php'), [
            'coursesList'      => $coursesList,
            'assignments'      => $assignments,
            'totalAssignments' => $totalAssignments,
            'totalPublished'   => $totalPublished,
            'totalDraft'       => $totalDraft,
            'totalSubmissions' => $totalSubmissions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateAssignment($request);

        $course = Course::findOrFail($data['course_id']);
        Gate::authorize('create', [Assignment::class, $course]);

        $assignment = new Assignment($data);
        $assignment->course_id  = $course->id;
        $assignment->created_by = $request->user()->id;   // id admin yang sedang login (keputusan B3)
        $assignment->save();

        return redirect()->route('admin.tugas')
            ->with('success', "Tugas \"{$assignment->title}\" berhasil ditambahkan.");
    }

    public function update(Request $request, Assignment $assignment): RedirectResponse
    {
        Gate::authorize('update', $assignment);

        $data = $this->validateAssignment($request);

        // course_id sengaja dibuang: tugas tidak boleh dipindah antar mata kuliah,
        // karena submission dan enrollment-nya terikat ke mata kuliah asal.
        $assignment->update(Arr::except($data, ['course_id']));

        return redirect()->route('admin.tugas')
            ->with('success', "Tugas \"{$assignment->title}\" berhasil diperbarui.");
    }

    public function destroy(Assignment $assignment): RedirectResponse
    {
        // Ditolak 409 oleh policy bila sudah ada pengumpulan; handler global
        // mengubahnya menjadi redirect back() dengan flash 'error'.
        Gate::authorize('delete', $assignment);

        $title = $assignment->title;
        $assignment->delete();

        return redirect()->route('admin.tugas')
            ->with('success', "Tugas \"{$title}\" berhasil dihapus.");
    }

    private function validateAssignment(Request $request): array
    {
        if ($request->filled('due_date') && $request->filled('due_time')) {
            $request->merge([
                'due_at' => $request->input('due_date') . ' ' . $request->input('due_time'),
            ]);
        }

        $request->merge([
            'allow_late' => $request->boolean('allow_late'),
        ]);

        return $request->validate([
            'course_id'    => ['required', 'exists:courses,id'],
            'title'        => ['required', 'string', 'max:255'],
            'instructions' => ['required', 'string'],
            'due_at'       => ['required', 'date'],
            'max_score'    => ['required', 'integer', 'min:1', 'max:100'],
            'allow_late'   => ['boolean'],
            'status'       => ['required', Rule::in(['draft', 'published'])],
        ], [], [
            'course_id'    => 'mata kuliah',
            'title'        => 'judul tugas',
            'instructions' => 'instruksi / petunjuk pengerjaan',
            'due_at'       => 'batas waktu',
            'max_score'    => 'nilai maksimal',
            'status'       => 'status publikasi',
        ]);
    }
}
