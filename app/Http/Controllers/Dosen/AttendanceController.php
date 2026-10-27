<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Tampilkan daftar sesi presensi pada mata kuliah yang diampu dosen.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Hanya mata kuliah yang diampu oleh dosen yang sedang login
        $courses = $user->taughtCourses()
            ->withCount(['students', 'attendanceSessions'])
            ->orderBy('code')
            ->get();

        $selectedCourseId = $request->query('course_id');
        if (! $selectedCourseId && $courses->isNotEmpty()) {
            $selectedCourseId = $courses->first()->id;
        }

        $sessionsQuery = AttendanceSession::whereIn('course_id', $courses->pluck('id'))
            ->with(['course', 'attendances'])
            ->withCount('attendances')
            ->orderByDesc('created_at');

        if ($selectedCourseId) {
            $sessionsQuery->where('course_id', $selectedCourseId);
        }

        $sessions = $sessionsQuery->get();

        // Kode default yang di-generate untuk form pembukaan presensi
        $generatedCode = AttendanceSession::generateUniqueCode();

        return view('dosen.presensi.index', compact(
            'courses',
            'sessions',
            'selectedCourseId',
            'generatedCode'
        ));
    }

    /**
     * Buka sesi presensi baru dengan kode unik dan batas masa berlaku.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'course_id'        => ['required', 'integer', 'exists:courses,id'],
            'title'            => ['required', 'string', 'max:255'],
            'code'             => ['nullable', 'string', 'max:16', 'alpha_num', 'unique:attendance_sessions,code'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'description'      => ['nullable', 'string', 'max:1000'],
        ], [
            'course_id.required'        => 'Mata kuliah wajib dipilih.',
            'title.required'            => 'Judul atau topik pertemuan wajib diisi.',
            'code.unique'               => 'Kode unik presensi sudah digunakan. Silakan gunakan kode lain atau kosongkan untuk otomatis.',
            'duration_minutes.required' => 'Masa berlaku wajib ditentukan.',
            'duration_minutes.min'      => 'Masa berlaku minimal 1 menit.',
        ]);

        $course = Course::findOrFail($data['course_id']);

        Gate::authorize('create', [AttendanceSession::class, $course]);

        // Jika kode tidak diisi atau kosong, generate otomatis
        $code = ! empty($data['code'])
            ? strtoupper(trim($data['code']))
            : AttendanceSession::generateUniqueCode();

        $duration = (int) $data['duration_minutes'];
        $startTime = now();
        $expiresAt = $startTime->copy()->addMinutes($duration);

        $session = AttendanceSession::create([
            'course_id'   => $course->id,
            'created_by'  => $request->user()->id,
            'title'       => trim($data['title']),
            'code'        => $code,
            'description' => $data['description'] ? trim($data['description']) : null,
            'start_time'  => $startTime,
            'expires_at'  => $expiresAt,
            'is_active'   => true,
        ]);

        return redirect()
            ->route('dosen.presensi.show', $session)
            ->with('success', "Sesi presensi '{$session->title}' berhasil dibuka! Kode unik: {$session->code} (berlaku selama {$duration} menit).");
    }

    /**
     * Tampilkan detail sesi presensi, kode unik besar, dan daftar kehadiran mahasiswa.
     */
    public function show(AttendanceSession $session): View
    {
        Gate::authorize('view', $session);

        $session->load([
            'course.students',
            'attendances.student',
            'creator',
        ]);

        $course = $session->course;
        $allStudents = $course->students->sortBy('name');

        // Petakan kehadiran berdasarkan user_id
        $attendancesByUser = $session->attendances->keyBy('user_id');

        $attendedList = [];
        $unattendedList = [];

        foreach ($allStudents as $student) {
            if ($attendancesByUser->has($student->id)) {
                $att = $attendancesByUser->get($student->id);
                $attendedList[] = [
                    'student'     => $student,
                    'status'      => $att->status,
                    'attended_at' => $att->attended_at,
                    'notes'       => $att->notes,
                    'attendance'  => $att,
                ];
            } else {
                $unattendedList[] = [
                    'student' => $student,
                ];
            }
        }

        $totalEnrolled = $allStudents->count();
        $totalAttended = count($attendedList);
        $percentAttended = $totalEnrolled > 0 ? round(($totalAttended / $totalEnrolled) * 100) : 0;

        return view('dosen.presensi.show', compact(
            'session',
            'course',
            'attendedList',
            'unattendedList',
            'totalEnrolled',
            'totalAttended',
            'percentAttended'
        ));
    }

    /**
     * Tutup atau buka kembali sesi presensi secara manual.
     */
    public function toggleStatus(AttendanceSession $session): RedirectResponse
    {
        Gate::authorize('update', $session);

        $session->is_active = ! $session->is_active;

        // Jika diaktifkan kembali dan sudah expired, perpanjang otomatis 15 menit
        if ($session->is_active && $session->isExpired()) {
            $session->expires_at = now()->addMinutes(15);
        }

        $session->save();

        $statusMsg = $session->is_active ? 'diaktifkan kembali' : 'ditutup';

        return back()->with('success', "Sesi presensi berhasil {$statusMsg}.");
    }

    /**
     * Perpanjang masa berlaku sesi presensi.
     */
    public function extend(Request $request, AttendanceSession $session): RedirectResponse
    {
        Gate::authorize('update', $session);

        $minutes = (int) $request->input('minutes', 15);
        if ($minutes < 1 || $minutes > 720) {
            $minutes = 15;
        }

        // Jika sudah lewat waktu, hitung dari sekarang
        if ($session->isExpired()) {
            $session->expires_at = now()->addMinutes($minutes);
        } else {
            $session->expires_at = $session->expires_at->addMinutes($minutes);
        }

        $session->is_active = true;
        $session->save();

        return back()->with('success', "Masa berlaku sesi presensi berhasil diperpanjang {$minutes} menit (berakhir {$session->expires_at->format('H:i')}).");
    }

    /**
     * Ubah atau tambahkan presensi mahasiswa secara manual oleh dosen.
     */
    public function updateStudentAttendance(Request $request, AttendanceSession $session): RedirectResponse
    {
        Gate::authorize('update', $session);

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'status'  => ['required', 'string', 'in:hadir,izin,sakit,alpa'],
            'notes'   => ['nullable', 'string', 'max:255'],
        ]);

        $student = User::where('id', $data['user_id'])->where('role', 'mahasiswa')->firstOrFail();

        // Pastikan mahasiswa memang terdaftar di mata kuliah ini
        if (! $session->course->isEnrolledBy($student)) {
            return back()->withErrors(['user_id' => 'Mahasiswa tidak terdaftar pada mata kuliah ini.']);
        }

        Attendance::updateOrCreate(
            [
                'attendance_session_id' => $session->id,
                'user_id'               => $student->id,
            ],
            [
                'status'      => $data['status'],
                'attended_at' => now(),
                'notes'       => $data['notes'] ? trim($data['notes']) : null,
            ]
        );

        return back()->with('success', "Kehadiran untuk {$student->name} ({$student->nim_nip}) berhasil diperbarui menjadi " . strtoupper($data['status']) . ".");
    }

    /**
     * Hapus sesi presensi.
     */
    public function destroy(AttendanceSession $session): RedirectResponse
    {
        Gate::authorize('delete', $session);

        $courseId = $session->course_id;
        $title = $session->title;
        $session->delete();

        return redirect()
            ->route('dosen.presensi.index', ['course_id' => $courseId])
            ->with('success', "Sesi presensi '{$title}' berhasil dihapus.");
    }
}
