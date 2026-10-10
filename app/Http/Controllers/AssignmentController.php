<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function show(Assignment $assignment): View
    {
        Gate::authorize('view', $assignment);

        // Cari submission milik pengguna yang sedang login untuk tugas ini
        $mySubmission = $assignment->submissions()
            ->where('user_id', auth()->id())
            ->first();

        return view('assignments.show', compact('assignment', 'mySubmission'));
    }
}