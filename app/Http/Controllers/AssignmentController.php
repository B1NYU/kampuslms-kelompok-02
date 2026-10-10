<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Detail tugas untuk mahasiswa (terdaftar), dosen (pengampu), dan admin.
 * Route model binding hanya membuktikan tugas itu ADA; hak akses dicek
 * AssignmentPolicy@view (tugas draft tidak terlihat mahasiswa; MK draft juga tidak).
 */
class AssignmentController extends Controller
{
    public function show(Assignment $assignment): View
    {
        $user = auth()->user();

        Gate::authorize('view', $assignment);

        $assignment->load('course.lecturer');

        if ($user->role === 'mahasiswa') {
            // Hanya pengumpulan milik sendiri.
            $mySubmission = $assignment->submissions()->with('grade')->where('user_id', $user->id)->first();
            $submissions = collect();
        } else {
            // Admin dan dosen pengampu lolos 'view' di atas, sehingga boleh melihat semua pengumpulan.
            $mySubmission = null;
            $submissions = $assignment->submissions()->with(['student', 'grade'])->get();
        }

        return view('assignments.show', compact('assignment', 'mySubmission', 'submissions'));
    }
}
