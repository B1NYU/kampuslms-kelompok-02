<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Support\RekapNilaiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Rekap nilai per mata kuliah untuk dosen pengampu: tabel mahasiswa × tugas.
 *
 * Jumlah query tetap (±6) berapa pun banyaknya mahasiswa dan tugas: mahasiswa, tugas, dan
 * pengumpulan+nilai masing-masing diambil SEKALI, lalu matriksnya dirakit di PHP.
 * (Optimisasi lebih lanjut — index, cache — adalah bahan minggu 11.)
 */
class RekapNilaiController extends Controller
{
    public function __construct(
        private readonly RekapNilaiService $rekapNilaiService,
    ) {}

    /** /dosen/rekap-nilai — diarahkan ke mata kuliah pertama yang diampu. */
    public function landing(Request $request): RedirectResponse
    {
        // Optimasi: Ambil hanya kolom courses.id dan code
        $course = $request->user()->taughtCourses()->orderBy('code')->first(['courses.id', 'code']);

        if (! $course) {
            return redirect()->route('dosen.dashboard')
                ->with('error', 'Anda belum mengampu mata kuliah apa pun.');
        }

        return redirect()->route('dosen.rekap.show', $course);
    }

    public function show(Request $request, Course $course): View
    {
        // Policy: hanya dosen pengampu MK ini. Dosen lain / mahasiswa / admin → 403.
        Gate::authorize('viewGradeRecap', $course);

        $data = $this->rekapNilaiService->buildForCourse($request->user(), $course);

        return view('dosen.rekap-nilai', array_merge(
            ['course' => $course],
            $data
        ));
    }
}
