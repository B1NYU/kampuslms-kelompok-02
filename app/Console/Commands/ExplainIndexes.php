<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Minggu 11, build 4: bahan bukti EXPLAIN untuk index di migrasi
 * 2026_11_03_170242_add_performance_indexes_table.
 *
 * Cara pakai (database LOKAL saja):
 *   php artisan perf:explain-indexes            # kondisi dengan index (sesudah)
 *   php artisan migrate:rollback --step=1       # lepas index
 *   php artisan perf:explain-indexes            # kondisi tanpa index (sebelum)
 *   php artisan migrate                         # pasang index lagi
 *
 * Nilai id dipilih otomatis dari data yang ada, jadi tidak perlu diedit.
 * Perintah ini hanya membaca (EXPLAIN), tidak mengubah data.
 */
class ExplainIndexes extends Command
{
    protected $signature = 'perf:explain-indexes';

    protected $description = 'Jalankan EXPLAIN untuk query yang diuntungkan index build 4 (bahan docs/performa.md)';

    public function handle(): int
    {
        $driver = DB::connection()->getDriverName();
        $this->info("Database: {$driver}");

        // Ambil id nyata dari data yang ada supaya query-nya relevan.
        $courseId   = (int) (DB::table('assignments')->value('course_id') ?? 1);
        $lecturerId = (int) (DB::table('courses')->where('id', $courseId)->value('lecturer_id') ?? 1);
        $userId     = (int) (DB::table('course_user')->where('course_id', $courseId)->value('user_id') ?? 1);

        $assignmentIds = DB::table('assignments')->where('course_id', $courseId)->orderBy('id')->limit(2)->pluck('id')->map(fn ($v) => (int) $v)->all() ?: [1];
        $studentIds    = DB::table('course_user')->where('course_id', $courseId)->orderBy('user_id')->limit(3)->pluck('user_id')->map(fn ($v) => (int) $v)->all() ?: [1];

        $aList = implode(',', $assignmentIds);
        $uList = implode(',', $studentIds);

        $queries = [
            ['1',  'idx_submissions_user_id',            "SELECT * FROM submissions WHERE user_id = {$userId}"],
            ['1b', 'idx_submissions_user_id',            "SELECT * FROM submissions WHERE assignment_id IN ({$aList}) AND user_id IN ({$uList})"],
            ['2',  'idx_submissions_submitted_at',       "SELECT * FROM submissions WHERE EXISTS (SELECT 1 FROM assignments WHERE assignments.id = submissions.assignment_id AND assignments.course_id IN ({$courseId})) ORDER BY submitted_at DESC LIMIT 10"],
            ['3',  'idx_assignments_course_status_due',  "SELECT * FROM assignments WHERE course_id = {$courseId} AND status = 'published' ORDER BY due_at"],
            ['4',  'idx_courses_lecturer_status',        "SELECT * FROM courses WHERE lecturer_id = {$lecturerId} AND status = 'active'"],
            ['5',  'idx_course_user_user_id',            "SELECT * FROM course_user WHERE user_id = {$userId}"],
        ];

        $prefix = $driver === 'sqlite' ? 'EXPLAIN QUERY PLAN ' : 'EXPLAIN ';
        $tabel = [];

        foreach ($queries as [$no, $index, $sql]) {
            $hasil = DB::select($prefix . $sql);
            $tabel[] = [$no, $index, $this->ringkas($driver, $hasil)];
        }

        $this->table(['#', 'Index yang diuji', 'Hasil EXPLAIN'], $tabel);

        $this->newLine();
        $this->line('Query yang dijalankan:');
        foreach ($queries as [$no, , $sql]) {
            $this->line("  [{$no}] {$sql}");
        }

        return self::SUCCESS;
    }

    /** Ringkas hasil EXPLAIN menjadi satu baris yang mudah disalin ke laporan. */
    private function ringkas(string $driver, array $rows): string
    {
        $rows = array_map(fn ($r) => (array) $r, $rows);

        return match ($driver) {
            'mysql', 'mariadb' => collect($rows)->map(function ($r) {
                $extra = ! empty($r['Extra']) ? ', Extra=' . $r['Extra'] : '';

                return sprintf(
                    '%s: type=%s, key=%s, rows=%s%s',
                    $r['table'] ?? '?',
                    $r['type'] ?? '?',
                    $r['key'] ?? 'NULL',
                    $r['rows'] ?? '?',
                    $extra
                );
            })->implode(' | '),

            'sqlite' => collect($rows)->pluck('detail')->implode(' | '),

            default => collect($rows)->map(fn ($r) => (string) reset($r))->implode(' | '),
        };
    }
}
