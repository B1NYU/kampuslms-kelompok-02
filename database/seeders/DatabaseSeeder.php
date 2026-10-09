<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
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

        $demoDosenB = User::create([
            'name' => 'Dosen B',
            'email' => 'dosenB@kampuslms.test',
            'nim_nip' => 'NIP-999',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $demoDosenB->role = 'dosen';
        $demoDosenB->save();

        $dosenLain = User::factory()->dosen()->count(1)->create();
        $mahasiswaLain = User::factory()->mahasiswa()->count(29)->create();

        $semuaDosen = collect([$demoDosen])->merge($dosenLain);
        $semuaMahasiswa = collect([$demoMahasiswa])->merge($mahasiswaLain);

        $this->command?->info(
            'Users: 1 admin, ' . $semuaDosen->count() . ' dosen, ' . $semuaMahasiswa->count() . ' mahasiswa.'
        );

        // Mata kuliah 1 diampu Dosen Demo, mata kuliah 2 diampu Dosen B (tetap, agar
        // skrip uji otorisasi deterministik); sisanya acak.
        $courses = collect(range(1, 5))->map(function (int $nomor) use ($dosenLain, $demoDosen, $demoDosenB) {
            $dosen = match ($nomor) {
                1 => $demoDosen,
                2 => $demoDosenB,
                default => $dosenLain->random(), // MK 3-5 bukan milik Dosen A/B
            };

            return Course::factory()->create([
                'lecturer_id' => $dosen->id,
                'status' => 'active',
            ]);
        });

        $courses->each(function (Course $course) use ($semuaMahasiswa, $demoMahasiswa, $courses) {
            $jumlahEnroll = random_int(15, min(22, $semuaMahasiswa->count()));
            $terdaftar = $semuaMahasiswa->random($jumlahEnroll);

            // Mahasiswa Demo selalu terdaftar di MK 1 dan TIDAK terdaftar di MK 2
            // (untuk menguji 403 pada mahasiswa yang bukan peserta).
            $terdaftar = $terdaftar->reject(fn (User $u) => $u->id === $demoMahasiswa->id);
            if ($course->id !== $courses[1]->id) {
                if ($course->id === $courses[0]->id || random_int(0, 1)) {
                    $terdaftar->push($demoMahasiswa);
                }
            }

            $pivot = $terdaftar->mapWithKeys(fn (User $mhs) => [
                $mhs->id => ['enrolled_at' => now()->subDays(random_int(10, 90))],
            ]);

            $course->students()->attach($pivot->all());
        });

        // Materi per mata kuliah: berkas PDF sungguhan di disk privat (supaya tombol
        // unduh mahasiswa benar-benar berfungsi) + satu materi berupa tautan.
        $courses->each(function (Course $course) {
            foreach (range(1, 5) as $pertemuan) {
                $judul = "Materi Pertemuan {$pertemuan} - {$course->code}";
                $path = "materials/{$course->id}/pertemuan-{$pertemuan}.pdf";
                Storage::put($path, self::pdfSederhana($judul));

                Material::factory()->create([
                    'course_id' => $course->id,
                    'uploaded_by' => $course->lecturer_id,
                    'session' => $pertemuan,
                    'title' => $judul,
                    'file_path' => $path,
                    'original_name' => "pertemuan-{$pertemuan}.pdf",
                    'file_size' => Storage::size($path),
                ]);
            }

            Material::factory()->link('https://laravel.com/docs/12.x')->create([
                'course_id' => $course->id,
                'uploaded_by' => $course->lecturer_id,
                'session' => 1,
                'title' => 'Referensi: Dokumentasi Laravel 12',
            ]);

            Material::factory()->link('https://developer.mozilla.org/id/')->create([
                'course_id' => $course->id,
                'uploaded_by' => $course->lecturer_id,
                'session' => null, // materi umum
                'title' => 'Referensi umum: MDN Web Docs',
            ]);
        });

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

    /** PDF satu halaman minimal yang valid (tanpa dependensi) untuk data demo. */
    private static function pdfSederhana(string $teks): string
    {
        $teks = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $teks);
        $isi = "BT /F1 18 Tf 72 720 Td ({$teks}) Tj ET";
        $objek = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
            '<< /Length ' . strlen($isi) . " >>\nstream\n{$isi}\nendstream",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
        ];

        $pdf = "%PDF-1.4\n";
        $offset = [];
        foreach ($objek as $i => $o) {
            $offset[$i + 1] = strlen($pdf);
            $pdf .= ($i + 1) . " 0 obj\n{$o}\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objek) + 1) . "\n0000000000 65535 f \n";
        foreach ($offset as $o) {
            $pdf .= sprintf("%010d 00000 n \n", $o);
        }

        return $pdf . "trailer\n<< /Size " . (count($objek) + 1) . " /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF";
    }
}
