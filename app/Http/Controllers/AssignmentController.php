<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\View\View;

/**
 * Detail tugas untuk mahasiswa (terdaftar) dan dosen (pengampu).
 * Route model binding hanya membuktikan tugas itu ADA; kepemilikan dicek di sini.
 */
class AssignmentController extends Controller
{
    public function show(Assignment $assignment): View
    {
        $user = auth()->user();

        abort_unless($assignment->isVisibleTo($user), 403);

        $assignment->load('course.lecturer');

        if ($user->role === 'mahasiswa') {
            // Hanya pengumpulan milik sendiri.
            $mySubmission = $assignment->submissions()->with('grade')->where('user_id', $user->id)->first();
            $submissions = collect();
        } else {
            $mySubmission = null;
            $submissions = $assignment->submissions()->with(['student', 'grade'])->get();
        }

        return view('assignments.show', compact('assignment', 'mySubmission', 'submissions'));
    }
}
