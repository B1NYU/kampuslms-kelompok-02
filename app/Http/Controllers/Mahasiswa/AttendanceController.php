<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Course;
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

        // ID sesi yang sudah dihadiri mahasiswa
        $attendedSessionIds = Attendance::where('user_id', $student->id)
            ->pluck('attendance_session_id')
            ->toArray();

        // Riwayat kehadiran mahasiswa
        $attendanceHistory = Attendance::where('user_id', $student->id)
            ->with(['session.course.lecturer'])
            ->orderByDesc('attended_at')
            ->get();

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

        if (! $session) {
            return redirect($redirectUrl)
                ->withInput()
                ->withErrors(['code' => 'Kode presensi tidak ditemukan. Mohon periksa kembali kode yang diberikan oleh dosen.']);
        }

        // VALIDASI KRUSIAL: Sesi presensi WAJIB milik mata kuliah yang sedang dibuka ini!
        if ((int) $session->course_id !== (int) $mata_kuliah->id) {
            return redirect($redirectUrl)
                ->withInput()
                ->withErrors([
                    'code' => "Kode presensi ini bukan untuk mata kuliah {$mata_kuliah->name} ({$mata_kuliah->code}). Anda hanya dapat melakukan presensi untuk mata kuliah ini pada halaman yang bersangkutan.",
                ]);
        }

        // Cek status keaktifan sesi
        if (! $session->is_active) {
            return redirect($redirectUrl)
                ->withInput()
                ->withErrors(['code' => "Sesi presensi '{$session->title}' telah ditutup oleh dosen pengampu."]);
        }

        // Cek apakah belum dimulai
        if ($session->start_time && now()->lt($session->start_time)) {
            return redirect($redirectUrl)
                ->withInput()
                ->withErrors(['code' => "Sesi presensi '{$session->title}' belum dibuka (dimulai pada {$session->start_time->format('d/m/Y H:i')})."]);
        }

        // Cek masa berlaku (kedaluwarsa)
        if ($session->isExpired()) {
            return redirect($redirectUrl)
                ->withInput()
                ->withErrors(['code' => "Masa berlaku kode presensi untuk '{$session->title}' telah kedaluwarsa pada {$session->expires_at->format('H:i')}. Presensi tidak dapat dilakukan lagi."]);
        }

        // Cek apakah mahasiswa sudah melakukan presensi sebelumnya
        $existing = Attendance::where('attendance_session_id', $session->id)
            ->where('user_id', $student->id)
            ->first();

        if ($existing) {
            $waktu = $existing->attended_at ? $existing->attended_at->format('d M Y H:i') : '-';
            return redirect($redirectUrl)
                ->withInput()
                ->withErrors(['code' => "Anda sudah melakukan presensi pada sesi '{$session->title}' ({$session->course->code}) pada {$waktu} WIB dengan status " . strtoupper($existing->status) . "."]);
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

        // Cari sesi presensi berdasarkan kode unik
        $session = AttendanceSession::where('code', $code)
            ->with('course')
            ->first();

        if (! $session) {
            return back()
                ->withInput()
                ->withErrors(['code' => 'Kode presensi tidak ditemukan. Mohon periksa kembali kode yang diberikan oleh dosen.']);
        }

        // Jika course_id disertakan, pastikan kode cocok dengan mata kuliah tersebut
        if (! empty($data['course_id']) && (int) $session->course_id !== (int) $data['course_id']) {
            $targetCourse = Course::find($data['course_id']);
            $targetName = $targetCourse ? "{$targetCourse->name} ({$targetCourse->code})" : 'mata kuliah yang dibuka';
            return back()
                ->withInput()
                ->withErrors(['code' => "Kode presensi ini bukan untuk {$targetName}. Anda hanya dapat melakukan presensi pada halaman mata kuliah yang sesuai."]);
        }

        // Cek apakah mahasiswa terdaftar pada mata kuliah yang bersangkutan
        if (! $session->course->isEnrolledBy($student)) {
            return back()
                ->withInput()
                ->withErrors(['code' => "Anda tidak terdaftar sebagai peserta pada mata kuliah {$session->course->code} - {$session->course->name}."]);
        }

        // Cek status keaktifan sesi
        if (! $session->is_active) {
            return back()
                ->withInput()
                ->withErrors(['code' => "Sesi presensi '{$session->title}' telah ditutup oleh dosen pengampu."]);
        }

        // Cek apakah belum dimulai
        if ($session->start_time && now()->lt($session->start_time)) {
            return back()
                ->withInput()
                ->withErrors(['code' => "Sesi presensi '{$session->title}' belum dibuka (dimulai pada {$session->start_time->format('d/m/Y H:i')})."]);
        }

        // Cek masa berlaku (kedaluwarsa)
        if ($session->isExpired()) {
            return back()
                ->withInput()
                ->withErrors(['code' => "Masa berlaku kode presensi untuk '{$session->title}' telah kedaluwarsa pada {$session->expires_at->format('H:i')}. Presensi tidak dapat dilakukan lagi."]);
        }

        // Cek apakah mahasiswa sudah melakukan presensi sebelumnya
        $existing = Attendance::where('attendance_session_id', $session->id)
            ->where('user_id', $student->id)
            ->first();

        if ($existing) {
            $waktu = $existing->attended_at ? $existing->attended_at->format('d M Y H:i') : '-';
            return back()
                ->withInput()
                ->withErrors(['code' => "Anda sudah melakukan presensi pada sesi '{$session->title}' ({$session->course->code}) pada {$waktu} WIB dengan status " . strtoupper($existing->status) . "."]);
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
}
