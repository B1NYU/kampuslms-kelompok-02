<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------------------------------------------------------------
        // 1. AKUN DEMO WAJIB — dibuat manual (bukan lewat factory random)
        //    supaya email & nama-nya PERSIS sesuai ketentuan. Password
        //    'password' di sini HANYA untuk dev/staging.
        // ---------------------------------------------------------------
        $demoAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@kampuslms.test',
            'nim_nip' => 'ADMIN-000',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $demoAdmin->role = 'admin';
        $demoAdmin->save();

        $demoDosen = User::create([
            'name' => 'Dosen Demo',
            'email' => 'dosen@kampuslms.test',
            'nim_nip' => 'NIP-000',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $demoDosen->role = 'dosen';
        $demoDosen->save();

        $demoMahasiswa = User::create([
            'name' => 'Mahasiswa Demo',
            'email' => 'mahasiswa@kampuslms.test',
            'nim_nip' => '10240000',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $demoMahasiswa->role = 'mahasiswa';
        $demoMahasiswa->save();

        // ---------------------------------------------------------------
        // 2. Lengkapi jumlah wajib: 1 admin (dari demo, sudah cukup),
        //    3 dosen total, 30 mahasiswa total.
        // ---------------------------------------------------------------
        $dosenLain = User::factory()->dosen()->count(2)->create();
        $mahasiswaLain = User::factory()->mahasiswa()->count(29)->create();

        $semuaDosen = collect([$demoDosen])->merge($dosenLain);
        $semuaMahasiswa = collect([$demoMahasiswa])->merge($mahasiswaLain);

        $this->command?->info(
            'Users: 1 admin, ' . $semuaDosen->count() . ' dosen, ' . $semuaMahasiswa->count() . ' mahasiswa.'
        );

        // ---------------------------------------------------------------
        // 3. 5 mata kuliah, dosen pengampu diambil acak dari 3 dosen.
        // ---------------------------------------------------------------
        $courses = collect(range(1, 5))->map(function () use ($semuaDosen) {
            return Course::factory()->create([
                'lecturer_id' => $semuaDosen->random()->id,
                'status' => 'active',
            ]);
        });

        // ---------------------------------------------------------------
        // 4. Enrollment: tiap MK minimal 15 mahasiswa.
        // ---------------------------------------------------------------
        $courses->each(function (Course $course) use ($semuaMahasiswa) {
            $jumlahEnroll = random_int(15, min(22, $semuaMahasiswa->count()));
            $terdaftar = $semuaMahasiswa->random($jumlahEnroll);

            $pivot = $terdaftar->mapWithKeys(fn (User $mhs) => [
                $mhs->id => ['enrolled_at' => now()->subDays(random_int(10, 90))],
            ]);

            $course->students()->attach($pivot->all());
        });

        // ---------------------------------------------------------------
        // 5. 3 tugas per MK: 1 lewat deadline, 1 aktif, 1 draft.
        // ---------------------------------------------------------------
        $publishedAssignments = collect();

        $courses->each(function (Course $course) use (&$publishedAssignments) {
            $lecturerId = $course->lecturer_id;

            $pastDeadline = Assignment::factory()->pastDeadline()->create([
                'course_id' => $course->id,
                'created_by' => $lecturerId,
            ]);

            $active = Assignment::factory()->active()->create([
                'course_id' => $course->id,
                'created_by' => $lecturerId,
            ]);

            Assignment::factory()->draft()->create([
                'course_id' => $course->id,
                'created_by' => $lecturerId,
            ]);

            $publishedAssignments->push($pastDeadline);
            $publishedAssignments->push($active);
        });

        // ---------------------------------------------------------------
        // 6. Submission — hanya untuk tugas published (draft belum boleh
        //    dikumpulkan). Target total >= 100.
        // ---------------------------------------------------------------
        $allSubmissions = collect();

        foreach ($publishedAssignments as $assignment) {
            $course = $assignment->course;
            $enrolledStudents = $course->students;

            $isPastDeadline = $assignment->due_at->isPast();

            // Tugas lewat deadline: mayoritas sudah mengumpulkan.
            // Tugas masih aktif: baru sebagian kecil (submitter awal).
            $ratio = $isPastDeadline
                ? random_int(70, 95) / 100
                : random_int(30, 60) / 100;

            $jumlahSubmit = max(1, (int) round($enrolledStudents->count() * $ratio));
            $jumlahSubmit = min($jumlahSubmit, $enrolledStudents->count());

            $pengumpul = $enrolledStudents->random($jumlahSubmit);

            foreach ($pengumpul as $mahasiswa) {
                $isLate = $isPastDeadline && random_int(1, 100) <= 15;

                $submittedAt = $isPastDeadline
                    ? ($isLate
                        ? (clone $assignment->due_at)->modify('+' . random_int(1, 4) . ' days')
                        : (clone $assignment->due_at)->modify('-' . random_int(1, 6) . ' days'))
                    : now()->subDays(random_int(0, 5));

                $submission = Submission::factory()->create([
                    'assignment_id' => $assignment->id,
                    'user_id' => $mahasiswa->id,
                    'submitted_at' => $submittedAt,
                    'is_late' => $isLate,
                ]);

                $allSubmissions->push($submission);
            }
        }

        $this->command?->info('Total submission dibuat: ' . $allSubmissions->count());

        // ---------------------------------------------------------------
        // 7. Grade — sekitar 60% dari seluruh submission.
        // ---------------------------------------------------------------
        $jumlahDinilai = (int) round($allSubmissions->count() * 0.6);
        $jumlahDinilai = min($jumlahDinilai, $allSubmissions->count());
        $submissionDinilai = $allSubmissions->random($jumlahDinilai);

        foreach ($submissionDinilai as $submission) {
            $assignment = $submission->assignment;

            Grade::factory()->create([
                'submission_id' => $submission->id,
                'graded_by' => $assignment->created_by,
                'graded_at' => (clone $submission->submitted_at)->modify('+' . random_int(1, 5) . ' days'),
            ]);
        }

        $persen = $allSubmissions->count() > 0
            ? round($submissionDinilai->count() / $allSubmissions->count() * 100)
            : 0;

        $this->command?->info("Total dinilai: {$submissionDinilai->count()} (~{$persen}%).");
        $this->command?->info('Seeding selesai. Login demo: admin@kampuslms.test / dosen@kampuslms.test / mahasiswa@kampuslms.test — password: password');
    }
}
