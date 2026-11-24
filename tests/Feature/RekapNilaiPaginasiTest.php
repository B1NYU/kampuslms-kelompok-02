<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RekapNilaiPaginasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_kartu_ringkasan_rekap_nilai_konsisten_di_semua_halaman_paginasi(): void
    {
        $this->withoutVite();

        $dosen = User::factory()->dosen()->create();
        $course = Course::factory()->create([
            'lecturer_id' => $dosen->id,
            'status'      => 'active',
        ]);

        $assignment = Assignment::factory()->active()->create([
            'course_id'  => $course->id,
            'created_by' => $dosen->id,
            'max_score'  => 100,
        ]);

        // Buat 11 mahasiswa (melebihi PER_HALAMAN = 10 sehingga terpecah menjadi 2 halaman).
        $students = collect();
        for ($i = 1; $i <= 11; $i++) {
            $mhs = User::factory()->mahasiswa()->create([
                'name' => sprintf('Mahasiswa %02d', $i),
            ]);
            $course->students()->attach($mhs->id, ['enrolled_at' => now()]);
            $students->push($mhs);
        }

        // 9 mahasiswa pertama sudah dinilai (skor 80), 2 mahasiswa terakhir menunggu penilaian.
        foreach ($students as $index => $mhs) {
            $submission = Submission::create([
                'assignment_id' => $assignment->id,
                'user_id'       => $mhs->id,
                'file_path'     => 'submissions/dummy-' . $mhs->id . '.pdf',
                'original_name' => 'tugas-' . $mhs->id . '.pdf',
                'file_size'     => 1024,
                'submitted_at'  => now(),
                'is_late'       => false,
            ]);

            if ($index < 9) {
                Grade::create([
                    'submission_id' => $submission->id,
                    'graded_by'     => $dosen->id,
                    'score'         => 80,
                    'graded_at'     => now(),
                ]);
            }
        }

        // Cek Halaman 1
        $resPage1 = $this->actingAs($dosen)->get(route('dosen.rekap.show', $course));
        $resPage1->assertOk();
        $statsPage1 = $resPage1->viewData('stats');

        $this->assertSame(11, $statsPage1['students']);
        $this->assertSame(1, $statsPage1['assignments']);
        $this->assertSame(9, $statsPage1['graded']);
        $this->assertSame(2, $statsPage1['waiting']);
        $this->assertEquals(80.0, $statsPage1['average']);

        // Cek Halaman 2 (?page=2): statistik kartu harus tetap mewakili seluruh kelas (11 mahasiswa).
        $resPage2 = $this->actingAs($dosen)->get(route('dosen.rekap.show', [$course, 'page' => 2]));
        $resPage2->assertOk();
        $statsPage2 = $resPage2->viewData('stats');

        $this->assertSame($statsPage1, $statsPage2);
        $this->assertSame(9, $statsPage2['graded']);
        $this->assertSame(2, $statsPage2['waiting']);
        $this->assertEquals(80.0, $statsPage2['average']);
    }
}