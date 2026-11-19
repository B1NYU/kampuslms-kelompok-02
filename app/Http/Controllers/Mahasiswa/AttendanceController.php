<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Tampilkan halaman input presensi, daftar sesi aktif, dan riwayat presensi mahasiswa.
     */
    public function index(Request $request): View
    {
        $student = $request->user();

        // Mata kuliah aktif/archived yang diikuti mahasiswa
        $enrolledCourses = $student->courses()
            ->whereIn('courses.status', ['active', 'archived'])
            ->with('lecturer')
            ->orderBy('code')
            ->get();

        $enrolledCourseIds = $enrolledCourses->pluck('id');

        // Sesi presensi yang sedang berlangsung (aktif dan belum kedaluwarsa)
        $activeSessions = AttendanceSession::whereIn('course_id', $enrolledCourseIds)
            ->where('is_active', true)
            ->where('expires_at', '>=', now())
            ->where('start_time', '<=', now())
            ->with(['course.lecturer'])
            ->orderBy('expires_at')
            ->get();

        // Riwayat kehadiran mahasiswa (paginasi 15 per halaman)
        $attendanceHistory = Attendance::where('user_id', $student->id)
            ->with(['session.course.lecturer'])
            ->orderByDesc('attended_at')
            ->paginate(15)
            ->withQueryString();

        // ID sesi aktif yang sudah dihadiri mahasiswa (hanya query sesi aktif saat ini)
        $activeSessionIds = $activeSessions->pluck('id');
        $attendedSessionIds = $activeSessionIds->isNotEmpty()
            ? Attendance::where('user_id', $student->id)
                ->whereIn('attendance_session_id', $activeSessionIds)
                ->pluck('attendance_session_id')
                ->toArray()
            : [];

        return view('mahasiswa.presensi.index', compact(
            'enrolledCourses',
            'activeSessions',
            'attendedSessionIds',
            'attendanceHistory'
        ));
    }

    /**
     * Catat kehadiran mahasiswa khusus untuk mata kuliah tertentu yang sedang dibuka.
     * Tidak boleh presensi mata kuliah lain pada halaman mata kuliah ini.
     */
    public function storeForCourse(Request $request, Course $mata_kuliah): RedirectResponse
    {
        $student = $request->user();
        Gate::authorize('view', $mata_kuliah);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:20'],
        ], [
            'code.required' => 'Silakan masukkan kode presensi.',
        ]);

        $code = strtoupper(trim($data['code']));
        $redirectUrl = route('mahasiswa.mata-kuliah.show', ['mata_kuliah' => $mata_kuliah, 'tab' => 'presensi']);

        // Cari sesi presensi berdasarkan kode unik
        $session = AttendanceSession::where('code', $code)
            ->with('course')
            ->first();

        if ($errorMessage = $this->verifyAttendance($session, $student, $mata_kuliah)) {
            return redirect($redirectUrl)
                ->withInput()
                ->withErrors(['code' => $errorMessage]);
        }

        // Simpan presensi mahasiswa
        Attendance::create([
            'attendance_session_id' => $session->id,
            'user_id'               => $student->id,
            'status'                => 'hadir',
            'attended_at'           => now(),
            'notes'                 => 'Hadir mandiri melalui halaman mata kuliah',
        ]);

        return redirect($redirectUrl)
            ->with('success', "Presensi berhasil! Anda tercatat HADIR pada mata kuliah {$session->course->code} - {$session->course->name} ({$session->title}) pada " . now()->format('H:i') . ' WIB.');
    }

    /**
     * Catat kehadiran mahasiswa berdasarkan kode unik presensi (fallback umum).
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code'      => ['required', 'string', 'max:20'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
        ], [
            'code.required' => 'Silakan masukkan kode presensi.',
        ]);

        $student = $request->user();
        $code = strtoupper(trim($data['code']));
        $targetCourse = ! empty($data['course_id']) ? Course::find($data['course_id']) : null;

        // Cari sesi presensi berdasarkan kode unik
        $session = AttendanceSession::where('code', $code)
            ->with('course')
            ->first();

        if ($errorMessage = $this->verifyAttendance($session, $student, $targetCourse)) {
            return back()
                ->withInput()
                ->withErrors(['code' => $errorMessage]);
        }

        // Simpan presensi mahasiswa
        Attendance::create([
            'attendance_session_id' => $session->id,
            'user_id'               => $student->id,
            'status'                => 'hadir',
            'attended_at'           => now(),
            'notes'                 => 'Hadir mandiri melalui kode unik',
        ]);

        return redirect()
            ->route('mahasiswa.presensi.index')
            ->with('success', "Presensi berhasil! Anda tercatat HADIR pada mata kuliah {$session->course->code} - {$session->course->name} ({$session->title}) pada " . now()->format('H:i') . ' WIB.');
    }

    /**
     * Verifikasi kelayakan presensi mahasiswa ke suatu sesi.
     * Mengembalikan pesan error jika tidak memenuhi syarat, atau null jika valid.
     */
    private function verifyAttendance(?AttendanceSession $session, User $student, ?Course $expectedCourse = null): ?string
    {
        if (! $session) {
            return 'Kode presensi tidak ditemukan. Mohon periksa kembali kode yang diberikan oleh dosen.';
        }

        // Jika dipanggil dari halaman MK tertentu, pastikan sesi milik MK tersebut
        if ($expectedCourse && (int) $session->course_id !== (int) $expectedCourse->id) {
            return 'Kode presensi tidak valid untuk mata kuliah ini.';
        }

        // Pastikan mahasiswa terdaftar pada mata kuliah yang bersangkutan tanpa membocorkan nama/kode MK lain
        if (! $session->course || ! $session->course->isEnrolledBy($student)) {
            return 'Kode presensi tidak valid atau Anda tidak terdaftar pada mata kuliah ini.';
        }

        // Cek status keaktifan sesi
        if (! $session->is_active) {
            return "Sesi presensi '{$session->title}' telah ditutup oleh dosen pengampu.";
        }

        // Cek apakah belum dimulai
        if ($session->start_time && now()->lt($session->start_time)) {
            return "Sesi presensi '{$session->title}' belum dibuka (dimulai pada {$session->start_time->format('d/m/Y H:i')}).";
        }

        // Cek masa berlaku (kedaluwarsa)
        if ($session->isExpired()) {
            return "Masa berlaku kode presensi untuk '{$session->title}' telah kedaluwarsa pada {$session->expires_at->format('H:i')}. Presensi tidak dapat dilakukan lagi.";
        }

        // Cek apakah mahasiswa sudah melakukan presensi sebelumnya
        $existing = Attendance::where('attendance_session_id', $session->id)
            ->where('user_id', $student->id)
            ->first();

        if ($existing) {
            $waktu = $existing->attended_at ? $existing->attended_at->format('d M Y H:i') : '-';
            return "Anda sudah melakukan presensi pada sesi '{$session->title}' ini sebelumnya pada {$waktu} WIB dengan status " . strtoupper($existing->status) . ".";
        }

        return null;
    }
}
