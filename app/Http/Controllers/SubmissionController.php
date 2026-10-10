<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubmissionRequest;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionController extends Controller
{
    public function show(Submission $submission): View
    {
        Gate::authorize('view', $submission);

        return view('submissions.show', compact('submission'));
    }

    public function download(Submission $submission): StreamedResponse
    {
        Gate::authorize('view', $submission);

        abort_unless(
            $submission->file_path && Storage::exists($submission->file_path),
            404,
            'Berkas pengumpulan tidak ditemukan di server.'
        );

        return Storage::download($submission->file_path, $submission->original_name);
    }

    public function store(StoreSubmissionRequest $request, Assignment $assignment): RedirectResponse
    {
        $file = $request->file('file');
        $path = $file->store("submissions/{$assignment->id}");

        try {
            $submission = $assignment->submissions()->create([
                'user_id'       => $request->user()->id,
                'file_path'     => $path,
                'original_name' => $file->getClientOriginalName(),
                'file_size'     => $file->getSize(),
                'note'          => $request->validated('note'),
                'submitted_at'  => now(),
                'is_late'       => now()->greaterThan($assignment->due_at),
            ]);
        } catch (UniqueConstraintViolationException) {
            Storage::delete($path);

            return back()->withErrors(['file' => 'Anda sudah mengumpulkan tugas ini.']);
        }

        return redirect()
            ->route('submissions.show', $submission)
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }
}