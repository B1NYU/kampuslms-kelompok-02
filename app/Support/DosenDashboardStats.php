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
 *   - daftar mata kuliah, ID mata kuliah, jumlah mahasiswa unik, jumlah materi,
 *     jumlah tugas. Dibersihkan otomatis saat data berubah (lihat forgetForCourse /
 *     forgetForLecturer dan hook Course::saved / deleted).
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
        $ambil = function () use ($dosen): array {
            $courses = self::courses($dosen);
            $ids = $courses->pluck('id')->all();
            $hitung = self::hitung($ids);

            return [
                'courses'     => $courses->map(fn ($c) => $c->only(['id', 'code', 'name', 'sks']))->all(),
                'courseIds'   => $ids,
                'students'    => $hitung['students'],
                'materials'   => $hitung['materials'],
                'assignments' => $hitung['assignments'],
            ];
        };

        // Ada pengguna login: cache per dosen. Tanpa pengguna: hitung langsung.
        $data = $dosen
            ? Cache::remember(
                self::key($dosen->id),
                now()->addMinutes(self::TTL_MENIT),
                $ambil
            )
            : $ambil();

        $hasValidCache = is_array($data)
            && array_key_exists('courses', $data)
            && array_key_exists('courseIds', $data)
            && array_key_exists('students', $data)
            && array_key_exists('materials', $data)
            && array_key_exists('assignments', $data);

        if (! $hasValidCache) {
            $data = $ambil();

            if ($dosen) {
                Cache::put(self::key($dosen->id), $data, now()->addMinutes(self::TTL_MENIT));
            }
        }

        return [
            'courses'          => collect($data['courses'])->map(fn ($c) => (object) $c),
            'totalStudents'    => $data['students'],
            'totalMaterials'   => $data['materials'],
            'totalAssignments' => $data['assignments'],
            // Tidak di-cache: lihat catatan di atas class.
            'pendingGrading'   => self::perluDinilai($data['courseIds']),
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
     * Daftar mata kuliah yang diampu dosen untuk dashboard.
     * Tidak pernah jatuh ke mata kuliah milik dosen lain.
     */
    private static function courses(?User $dosen): Collection
    {
        $kolom = ['courses.id', 'courses.code', 'courses.name', 'courses.sks'];

        // Bukan dosen atau belum login: kembalikan koleksi kosong.
        if (! $dosen || $dosen->role !== 'dosen') {
            return collect();
        }

        $courses = $dosen->taughtCourses()->where('status', 'active')->get($kolom);

        // Fallback aman: hanya MK milik dosen itu sendiri (misalnya semua masih draft).
        if ($courses->isEmpty()) {
            $courses = $dosen->taughtCourses()->get($kolom);
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
