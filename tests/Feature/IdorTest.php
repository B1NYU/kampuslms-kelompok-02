<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IdorTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $dosen;       // pengampu
    private User $dosenLain;
    private User $budi;        // mahasiswa terdaftar
    private User $siti;        // mahasiswa terdaftar
    private User $outsider;    // mahasiswa TIDAK terdaftar
    private Course $course;
    private Course $courseLain; // diampu dosenLain
    private Assignment $assignment;
    private Submission $subBudi;
    private Submission $subSiti;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->admin = User::factory()->admin()->create();
        $this->dosen = User::factory()->dosen()->create();
        $this->dosenLain = User::factory()->dosen()->create();
        $this->budi = User::factory()->mahasiswa()->create();
        $this->siti = User::factory()->mahasiswa()->create();
        $this->outsider = User::factory()->mahasiswa()->create();

        $this->course = Course::factory()->create(['lecturer_id' => $this->dosen->id]);
        $this->courseLain = Course::factory()->create(['lecturer_id' => $this->dosenLain->id]);
        $this->course->students()->attach([$this->budi->id, $this->siti->id], ['enrolled_at' => now()]);

        $this->assignment = Assignment::factory()->active()->create([
            'course_id' => $this->course->id, 'created_by' => $this->dosen->id,
        ]);

        $this->subBudi = Submission::factory()->create([
            'assignment_id' => $this->assignment->id, 'user_id' => $this->budi->id,
            'original_name' => 'jawaban-budi.pdf',
        ]);
        $this->subSiti = Submission::factory()->create([
            'assignment_id' => $this->assignment->id, 'user_id' => $this->siti->id,
            'original_name' => 'jawaban-siti-RAHASIA.pdf',
        ]);
        Grade::factory()->create(['submission_id' => $this->subSiti->id, 'graded_by' => $this->dosen->id]);
    }

    // ---------- /submissions/{submission} (contoh IDOR di modul) ----------

    public function test_mahasiswa_bisa_melihat_submission_sendiri(): void
    {
        $this->actingAs($this->budi)->get(route('submissions.show', $this->subBudi))->assertOk();
    }

    public function test_mahasiswa_tidak_bisa_melihat_submission_orang_lain(): void
    {
        // Persis skenario Budi mengganti angka di URL.
        $this->actingAs($this->budi)->get(route('submissions.show', $this->subSiti))->assertForbidden();
        $this->actingAs($this->outsider)->get(route('submissions.show', $this->subBudi))->assertForbidden();
    }

    public function test_dosen_pengampu_dan_admin_boleh_melihat_submission(): void
    {
        $this->actingAs($this->dosen)->get(route('submissions.show', $this->subSiti))->assertOk();
        $this->actingAs($this->admin)->get(route('submissions.show', $this->subSiti))->assertOk();
    }

    public function test_dosen_lain_tidak_boleh_melihat_submission(): void
    {
        $this->actingAs($this->dosenLain)->get(route('submissions.show', $this->subSiti))->assertForbidden();
    }

    public function test_id_yang_tidak_ada_mendapat_404_dan_tamu_diarahkan_ke_login(): void
    {
        $this->actingAs($this->budi)->get('/submissions/999999')->assertNotFound();
        auth()->logout();
        $this->get(route('submissions.show', $this->subBudi))->assertRedirect(route('login'));
    }

    // ---------- /mata-kuliah/{id} ----------

    public function test_mahasiswa_terdaftar_bisa_membuka_matkul_dan_hanya_melihat_data_sendiri(): void
    {
        $this->actingAs($this->budi)->get(route('mata-kuliah.show', $this->course))
            ->assertOk()
            ->assertDontSee('jawaban-siti-RAHASIA.pdf');
    }

    public function test_mahasiswa_tidak_terdaftar_ditolak(): void
    {
        $this->actingAs($this->outsider)->get(route('mata-kuliah.show', $this->course))->assertForbidden();
    }

    // ---------- /assignments/{assignment} ----------

    public function test_detail_tugas_hanya_untuk_terdaftar_atau_dosen_pengampu(): void
    {
        $this->actingAs($this->budi)->get(route('assignments.show', $this->assignment))->assertOk();
        $this->actingAs($this->dosen)->get(route('assignments.show', $this->assignment))->assertOk();
        $this->actingAs($this->outsider)->get(route('assignments.show', $this->assignment))->assertForbidden();
        $this->actingAs($this->dosenLain)->get(route('assignments.show', $this->assignment))->assertForbidden();
    }

    public function test_mahasiswa_tidak_melihat_pengumpulan_temannya_di_detail_tugas(): void
    {
        $this->actingAs($this->budi)->get(route('assignments.show', $this->assignment))
            ->assertDontSee('jawaban-siti-RAHASIA.pdf');
    }

    public function test_tugas_draft_tidak_terlihat_oleh_mahasiswa(): void
    {
        $draft = Assignment::factory()->draft()->create([
            'course_id' => $this->course->id, 'created_by' => $this->dosen->id,
        ]);

        $this->actingAs($this->budi)->get(route('assignments.show', $draft))->assertForbidden();
        $this->actingAs($this->dosen)->get(route('assignments.show', $draft))->assertOk();
    }

    // ---------- POST pengumpulan ----------

    public function test_mengumpulkan_tugas_memakai_user_id_dari_auth_bukan_dari_input(): void
    {
        Storage::fake('local');
        $this->subBudi->delete();

        $this->actingAs($this->budi)->post(
            route('assignments.submissions.store', $this->assignment),
            ['file' => UploadedFile::fake()->create('tugas.pdf', 100, 'application/pdf'), 'user_id' => $this->siti->id]
        )->assertRedirect();

        $this->assertDatabaseHas('submissions', ['assignment_id' => $this->assignment->id, 'user_id' => $this->budi->id]);
        // Milik Siti tidak tertimpa/diubah.
        $this->assertSame('jawaban-siti-RAHASIA.pdf', $this->subSiti->fresh()->original_name);
    }

    public function test_mahasiswa_tidak_terdaftar_tidak_bisa_mengumpulkan(): void
    {
        Storage::fake('local');

        $this->actingAs($this->outsider)->post(
            route('assignments.submissions.store', $this->assignment),
            ['file' => UploadedFile::fake()->create('tugas.pdf', 100, 'application/pdf')]
        )->assertForbidden();

        $this->assertDatabaseMissing('submissions', ['user_id' => $this->outsider->id]);
    }

    // ---------- Dosen: hanya mata kuliah yang diampu ----------

    public function test_dosen_hanya_mengelola_tugas_matkul_miliknya(): void
    {
        $this->actingAs($this->dosen)->get(route('dosen.courses.assignments.index', $this->course))->assertOk();
        $this->actingAs($this->dosen)->get(route('dosen.courses.assignments.index', $this->courseLain))->assertForbidden();
        $this->actingAs($this->dosen)->get(route('dosen.courses.assignments.create', $this->courseLain))->assertForbidden();
    }

    public function test_dosen_tidak_bisa_membuat_tugas_di_matkul_dosen_lain(): void
    {
        $this->actingAs($this->dosen)->post(route('dosen.courses.assignments.store', $this->courseLain), $this->payload())
            ->assertForbidden();

        $this->assertDatabaseMissing('assignments', ['course_id' => $this->courseLain->id]);
    }

    public function test_dosen_bisa_membuat_tugas_dan_course_id_dari_route(): void
    {
        $this->actingAs($this->dosen)->post(
            route('dosen.courses.assignments.store', $this->course),
            $this->payload(['course_id' => $this->courseLain->id, 'created_by' => $this->dosenLain->id])
        )->assertRedirect();

        $this->assertDatabaseHas('assignments', [
            'title' => 'Tugas Uji', 'course_id' => $this->course->id, 'created_by' => $this->dosen->id,
        ]);
    }

    public function test_dosen_lain_tidak_bisa_mengubah_atau_menghapus_tugas(): void
    {
        $this->actingAs($this->dosenLain)->get(route('dosen.assignments.edit', $this->assignment))->assertForbidden();
        $this->actingAs($this->dosenLain)->put(route('dosen.assignments.update', $this->assignment), $this->payload())->assertForbidden();
        $this->actingAs($this->dosenLain)->delete(route('dosen.assignments.destroy', $this->assignment))->assertForbidden();

        $this->assertDatabaseHas('assignments', ['id' => $this->assignment->id]);
    }

    public function test_tugas_yang_sudah_ada_pengumpulan_tidak_bisa_dihapus(): void
    {
        $this->actingAs($this->dosen)->delete(route('dosen.assignments.destroy', $this->assignment))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('assignments', ['id' => $this->assignment->id]);
    }

    public function test_route_dosen_tertutup_untuk_mahasiswa(): void
    {
        $this->actingAs($this->budi)->get(route('dosen.courses.assignments.index', $this->course))->assertForbidden();
    }

    private function payload(array $extra = []): array
    {
        return array_merge([
            'title' => 'Tugas Uji',
            'instructions' => 'Kerjakan soal.',
            'due_at' => now()->addWeek()->format('Y-m-d\TH:i'),
            'max_score' => 100,
            'status' => 'published',
        ], $extra);
    }
}
