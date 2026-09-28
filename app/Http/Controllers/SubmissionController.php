<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    /**
     * Lihat satu pengumpulan. Pemilik, dosen pengampu, atau admin.
     * Tanpa pengecekan ini, mengganti /submissions/41 menjadi /submissions/42
     * membuka data orang lain (IDOR).
     */
    public function show(Submission $submission): View
    {
        abort_unless($submission->isViewableBy(auth()->user()), 403);

        $submission->load(['assignment.course', 'student', 'grade']);

        return view('submissions.show', compact('submission'));
    }

    /**
     * Mahasiswa mengumpulkan tugas. user_id SELALU dari auth(), tidak dari input.
     */
    public function store(Request $request, Assignment $assignment): RedirectResponse
    {
        $user = $request->user();

        abort_unless($assignment->isVisibleTo($user), 403);

        $isLate = $assignment->due_at->isPast();
        abort_if($isLate && ! $assignment->allow_late, 403, 'Batas waktu pengumpulan sudah lewat.');

        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,zip,txt'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $file = $request->file('file');
        $path = $file->store("submissions/{$assignment->id}"); // disk privat (bukan public)

        $existing = Submission::where('assignment_id', $assignment->id)->where('user_id', $user->id)->first();
        $oldPath = $existing?->file_path;

        $submission = Submission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'user_id' => $user->id],
            [
                'file_path'     => $path,
                'original_name' => $file->getClientOriginalName(),
                'file_size'     => $file->getSize(),
                'note'          => $data['note'] ?? null,
                'submitted_at'  => now(),
                'is_late'       => $isLate,
            ]
        );

        if ($oldPath && $oldPath !== $path) {
            Storage::delete($oldPath);
        }

        return redirect()
            ->route('submissions.show', $submission)
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }
}
