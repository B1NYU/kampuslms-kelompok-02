<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;

class AdminCourseController extends Controller
{
    private const STATUSES = ['draft', 'active', 'archived'];

    /**
     * Tampilkan daftar mata kuliah dengan pencarian, filter status & dosen, dan pagination.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        // Whitelist: nilai di luar daftar dianggap "tidak memfilter".
        $status = $request->query('status');
        $status = in_array($status, self::STATUSES, true) ? $status : null;

        $lecturerId = $request->query('lecturer_id');
        $lecturerId = is_string($lecturerId) && ctype_digit($lecturerId) ? (int) $lecturerId : null;

        $matkulList = Course::query()
            ->with('lecturer')
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('code', 'like', $like)
                      ->orWhere('name', 'like', $like);
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($lecturerId, fn ($query) => $query->where('lecturer_id', $lecturerId))
            ->orderByDesc('created_at')
            ->orderByDesc('id') // tie-breaker agar urutan antarhalaman stabil
            ->paginate(3)
            ->withQueryString();

        $dosenList = User::where('role', 'dosen')->orderBy('name')->get(['id', 'name']);

        $filters = [
            'q'           => $search,
            'status'      => $status,
            'lecturer_id' => $lecturerId,
        ];

        return view()->file(
            resource_path('views/admin/admin.matkul.blade.php'),
            compact('matkulList', 'dosenList', 'filters')
        );
    }

    public function store(StoreCourseRequest $request)
    {
        Course::create($request->validated());

        return redirect()
            ->action([self::class, 'index'], $this->redirectFilters($request))
            ->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function update(UpdateCourseRequest $request, Course $matkul)
    {
        $matkul->update($request->validated());

        return redirect()
            ->action([self::class, 'index'], $this->redirectFilters($request))
            ->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(Request $request, Course $matkul)
    {
        try {
            $matkul->delete();
        } catch (QueryException $e) {
            // 23000 = MySQL/MariaDB integrity violation, 23503 = PostgreSQL foreign key violation.
            if (in_array((string) $e->getCode(), ['23000', '23503'], true)) {
                return redirect()
                    ->action([self::class, 'index'], $this->redirectFilters($request))
                    ->with('error', 'Mata kuliah tidak dapat dihapus karena masih memiliki data terkait '
                        . '(tugas, materi, atau mahasiswa). Ubah statusnya menjadi archived sebagai gantinya.');
            }

            throw $e;
        }

        return redirect()
            ->action([self::class, 'index'], $this->redirectFilters($request))
            ->with('success', 'Mata kuliah berhasil dihapus.');
    }

    /**
     * Ambil filter yang sedang aktif dari hidden field form (redirect_q,
     * redirect_status, redirect_lecturer_id), lalu buang yang kosong agar
     * tidak muncul sebagai ?status=&lecturer_id= di URL hasil redirect.
     */
    private function redirectFilters(Request $request): array
    {
        return array_filter([
            'q'           => $request->input('redirect_q'),
            'status'      => $request->input('redirect_status'),
            'lecturer_id' => $request->input('redirect_lecturer_id'),
        ]);
    }

    private function transform(Course $course): array
    {
        return [
            'id'            => $course->id,
            'code'          => $course->code,
            'name'          => $course->name,
            'sks'           => $course->sks,
            'lecturer_id'   => $course->lecturer_id,
            'lecturer_name' => $course->lecturer?->name,
            'status'        => $course->status,
            'description'   => $course->description,
        ];
    }


}
