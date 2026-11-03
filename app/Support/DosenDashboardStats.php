<?php

namespace App\Support;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Data kartu statistik dashboard dosen (Minggu 11, build 6: cache).
 *
 * Sebelumnya empat angka ini dihitung di dalam Blade dengan memuat seluruh
 * mahasiswa, materi, tugas, pengumpulan, dan nilai ke memori (69 model hanya
 * untuk menampilkan empat angka). Sekarang dihitung di database lewat COUNT.
 *
 * Yang di-cache (10 menit, satu kunci per dosen):
 *   - jumlah mahasiswa unik, jumlah materi, jumlah tugas.
 *     Basi beberapa menit tidak merugikan siapa pun, dan dibersihkan otomatis
 *     setiap kali datanya berubah (lihat forgetForCourse / forgetForLecturer).
 *
 * Yang SENGAJA tidak di-cache:
 *   - "Perlu dinilai". Dosen baru saja memberi nilai lalu melihat angkanya
 *     tidak turun akan mengira penilaiannya gagal. Cukup satu query COUNT.
 */
class DosenDashboardStats
{
    public const TTL_MENIT = 10;

    public static function key(int $lecturerId): string
    {
        return "dosen.{$lecturerId}.dashboard-stats";
    }

    /**
     * @return array{courses: Collection, totalStudents: int, totalMaterials: int, totalAssignments: int, pendingGrading: int}
     */
    public static function for(?User $dosen): array
    {
        $courses = self::courses($dosen);
        $courseIds = $courses->pluck('id')->all();

        // Ada pengguna login: cache per dosen. Tanpa pengguna: hitung langsung.
        $stats = $dosen
            ? Cache::remember(
                self::key($dosen->id),
                now()->addMinutes(self::TTL_MENIT),
                fn () => self::hitung($courseIds)
            )
            : self::hitung($courseIds);

        return [
            'courses'          => $courses,
            'totalStudents'    => $stats['students'],
            'totalMaterials'   => $stats['materials'],
            'totalAssignments' => $stats['assignments'],
            // Tidak di-cache: lihat catatan di atas class.
            'pendingGrading'   => self::perluDinilai($courseIds),
        ];
    }

    /** Bersihkan cache dosen pengampu dari satu mata kuliah (butuh satu query kecil untuk mencari dosennya). */
    public static function forgetForCourse(int|string|null $courseId): void
    {
        if (! $courseId) {
            return;
        }

        self::forgetForLecturer(Course::whereKey($courseId)->value('lecturer_id'));
    }

    /** Bersihkan cache seorang dosen. Dipakai bila id dosen sudah diketahui (tanpa query tambahan). */
    public static function forgetForLecturer(int|string|null $lecturerId): void
    {
        if (! $lecturerId) {
            return;
        }

        Cache::forget(self::key((int) $lecturerId));
    }

    /**
     * Daftar mata kuliah untuk dashboard. Aturan pemilihannya sama dengan
     * versi di Blade sebelumnya, hanya saja tanpa memuat relasi apa pun.
     */
    private static function courses(?User $dosen): Collection
    {
        $kolom = ['courses.id', 'courses.code', 'courses.name', 'courses.sks'];
        $courses = collect();

        if ($dosen && $dosen->role === 'dosen') {
            $courses = $dosen->taughtCourses()->where('status', 'active')->get($kolom);

            if ($courses->isEmpty()) {
                $courses = $dosen->taughtCourses()->get($kolom);
            }
        }

        if ($courses->isEmpty()) {
            $courses = Course::where('status', 'active')->get($kolom);
        }

        if ($courses->isEmpty()) {
            $courses = Course::get($kolom);
        }

        return $courses;
    }

    /** Tiga angka yang di-cache. Hanya dijalankan saat cache kosong. */
    private static function hitung(array $courseIds): array
    {
        if ($courseIds === []) {
            return ['students' => 0, 'materials' => 0, 'assignments' => 0];
        }

        return [
            // Unik: mahasiswa yang ikut dua mata kuliah dosen yang sama dihitung sekali.
            // Akun yang sudah dihapus (soft delete) tidak dihitung, sama seperti relasi students() sebelumnya.
            'students'    => DB::table('course_user')
                ->join('users', 'users.id', '=', 'course_user.user_id')
                ->whereNull('users.deleted_at')
                ->whereIn('course_user.course_id', $courseIds)
                ->distinct()
                ->count('course_user.user_id'),
            'materials'   => Material::whereIn('course_id', $courseIds)->count(),
            'assignments' => Assignment::whereIn('course_id', $courseIds)->count(),
        ];
    }

    /** Pengumpulan yang belum punya nilai, di seluruh tugas pada mata kuliah dashboard. */
    private static function perluDinilai(array $courseIds): int
    {
        if ($courseIds === []) {
            return 0;
        }

        return Submission::whereIn('assignment_id', Assignment::whereIn('course_id', $courseIds)->select('id'))
            ->whereDoesntHave('grade')
            ->count();
    }
}
