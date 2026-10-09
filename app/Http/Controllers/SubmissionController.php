<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
     * Unduh berkas pengumpulan. Aturan akses sama dengan show(): pemilik,
     * dosen pengampu, atau admin. Berkas ada di disk privat, jadi satu-satunya
     * jalan keluar adalah lewat method ini.
     */
    public function download(Submission $submission): StreamedResponse
    {
        abort_unless($submission->isViewableBy(auth()->user()), 403);

        abort_unless(
            $submission->file_path && Storage::exists($submission->file_path),
            404,
            'Berkas pengumpulan tidak ditemukan di server.'
        );

        return Storage::download($submission->file_path, $submission->original_name);
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

        // Pengumpulan yang sudah dinilai dikunci supaya nilai tidak berpisah dari berkas yang dinilai.
        $sudahDinilai = Submission::where('assignment_id', $assignment->id)
            ->where('user_id', $user->id)
            ->whereHas('grade')
            ->exists();

        if ($sudahDinilai) {
            return back()->withErrors(['file' => 'Tugas ini sudah dinilai dosen sehingga tidak dapat dikumpulkan ulang.']);
        }

        $data = $request->validate([
            // mimes = cek ISI berkas, extensions = cek EKSTENSI nama aslinya. Keduanya wajib lolos,
            // supaya berkas bernama .exe yang isinya teks biasa pun tidak tersimpan.
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,zip,txt', 'extensions:pdf,doc,docx,zip,txt'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [
            'file.required' => 'Pilih berkas jawaban yang akan dikumpulkan.',
            'file.file'     => 'Berkas gagal diunggah. Ukurannya mungkin melebihi batas server.',
            'file.mimes'      => 'Berkas harus berformat PDF, DOC, DOCX, ZIP, atau TXT.',
            'file.extensions' => 'Berkas harus berformat PDF, DOC, DOCX, ZIP, atau TXT.',
            'file.max'      => 'Ukuran berkas maksimal 10 MB.',
            'note.max'      => 'Catatan maksimal 1000 karakter.',
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
