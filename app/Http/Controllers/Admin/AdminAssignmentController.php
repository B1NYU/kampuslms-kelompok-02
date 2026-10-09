<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminAssignmentController extends Controller
{
    /**
     * Tampilkan halaman manajemen tugas di panel admin.
     */
    public function index(Request $request): View
    {
        $coursesList = Course::with('lecturer')->orderBy('code')->get();

        $assignments = Assignment::with(['course.lecturer', 'creator'])
            ->withCount('submissions')
            ->orderBy('due_at')
            ->get();

        // Hitung statistik untuk bar metrik ringkasan
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

    /**
     * Simpan penugasan baru yang dibuat oleh admin.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateAssignment($request);

        $assignment = new Assignment($data);
        $assignment->course_id  = $data['course_id'];
        $assignment->created_by = $request->user()->id;
        $assignment->save();

        return redirect()->route('admin.tugas')
            ->with('success', "Tugas \"{$assignment->title}\" berhasil ditambahkan.");
    }

    /**
     * Perbarui data tugas yang ada.
     */
    public function update(Request $request, Assignment $assignment): RedirectResponse
    {
        $data = $this->validateAssignment($request);

        $assignment->update($data);

        return redirect()->route('admin.tugas')
            ->with('success', "Tugas \"{$assignment->title}\" berhasil diperbarui.");
    }

    /**
     * Hapus tugas jika belum ada pengumpulan dari mahasiswa.
     */
    public function destroy(Assignment $assignment): RedirectResponse
    {
        if ($assignment->submissions()->exists()) {
            return back()->with('error', 'Tugas tidak dapat dihapus karena sudah ada pengumpulan dari mahasiswa.');
        }

        $title = $assignment->title;
        $assignment->delete();

        return redirect()->route('admin.tugas')
            ->with('success', "Tugas \"{$title}\" berhasil dihapus.");
    }

    /**
     * Validasi data input tugas dari form admin.
     */
    private function validateAssignment(Request $request): array
    {
        // Mendukung format input tanggal & jam terpisah (seperti mode dosen) atau input tunggal due_at
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
