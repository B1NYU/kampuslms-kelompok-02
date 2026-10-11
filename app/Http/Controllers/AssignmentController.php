<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function show(Assignment $assignment): View
    {
        $user = auth()->user();

        Gate::authorize('view', $assignment);

        $assignment->load('course.lecturer');

        if ($user->role === 'mahasiswa') {
            // Mahasiswa: hanya melihat pengumpulan milik sendiri
            $mySubmission = $assignment->submissions()->with('grade')->where('user_id', $user->id)->first();
            $submissions = collect();
        } else {
            // Dosen & Admin: melihat semua berkas pengumpulan mahasiswa
            $mySubmission = null;
            $submissions = $assignment->submissions()->with(['student', 'grade'])->get();
        }

        return view('assignments.show', compact('assignment', 'mySubmission', 'submissions'));
    }
}