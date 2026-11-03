<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use App\Support\DosenDashboardStats;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Minggu 11, build 6: cache statistik dashboard dosen.
 * phpunit.xml memakai CACHE_STORE=array, jadi cache bersih di setiap tes.
 */
class DashboardDosenCacheTest extends TestCase
{
    use RefreshDatabase;

    private User $dosen;
    private User $mhs1;
    private Course $course;
    private Submission $submission;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->dosen = User::factory()->dosen()->create();
        $this->mhs1  = User::factory()->mahasiswa()->create();
        $mhs2        = User::factory()->mahasiswa()->create();

        $this->course = Course::factory()->create(['lecturer_id' => $this->dosen->id, 'status' => 'active']);
        foreach ([$this->mhs1, $mhs2] as $mhs) {
            $this->course->students()->attach($mhs->id, ['enrolled_at' => now()]);
        }

        Material::factory()->create(['course_id' => $this->course->id, 'uploaded_by' => $this->dosen->id]);

        $tugas = Assignment::factory()->create(['course_id' => $this->course->id, 'created_by' => $this->dosen->id]);
        Assignment::factory()->create(['course_id' => $this->course->id, 'created_by' => $this->dosen->id, 'status' => 'draft']);

        // Satu pengumpulan yang belum dinilai.
        $this->submission = Submission::factory()->create([
            'assignment_id' => $tugas->id,
            'user_id'       => $this->mhs1->id,
        ]);
    }

    private function key(): string
    {
        return DosenDashboardStats::key($this->dosen->id);
    }

    public function test_statistik_dihitung_di_database_dan_disimpan_di_cache(): void
    {
        $stats = DosenDashboardStats::for($this->dosen);

        $this->assertSame(2, $stats['totalStudents']);
        $this->assertSame(1, $stats['totalMaterials']);
        $this->assertSame(2, $stats['totalAssignments']);
        $this->assertSame(1, $stats['pendingGrading']);
        $this->assertTrue(Cache::has($this->key()));
    }

    public function test_kunjungan_kedua_tidak_menghitung_ulang_tiga_angka_yang_di_cache(): void
    {
        DosenDashboardStats::for($this->dosen);

        DB::flushQueryLog();
        DB::enableQueryLog();
        DosenDashboardStats::for($this->dosen);
        $sql = strtolower(collect(DB::getQueryLog())->pluck('query')->implode("\n"));

        $this->assertStringNotContainsString('course_user', $sql, 'mahasiswa seharusnya dibaca dari cache');
        $this->assertStringNotContainsString('materials', $sql, 'materi seharusnya dibaca dari cache');
    }

    public function test_tugas_baru_membersihkan_cache(): void
    {
        DosenDashboardStats::for($this->dosen);
        $this->assertTrue(Cache::has($this->key()));

        Assignment::factory()->create(['course_id' => $this->course->id, 'created_by' => $this->dosen->id]);

        $this->assertFalse(Cache::has($this->key()));
        $this->assertSame(3, DosenDashboardStats::for($this->dosen)['totalAssignments']);
    }

    public function test_materi_baru_membersihkan_cache(): void
    {
        DosenDashboardStats::for($this->dosen);

        Material::factory()->create(['course_id' => $this->course->id, 'uploaded_by' => $this->dosen->id]);

        $this->assertFalse(Cache::has($this->key()));
        $this->assertSame(2, DosenDashboardStats::for($this->dosen)['totalMaterials']);
    }

    public function test_penghapusan_tugas_membersihkan_cache(): void
    {
        DosenDashboardStats::for($this->dosen);

        Assignment::where('course_id', $this->course->id)->where('status', 'draft')->first()->delete();

        $this->assertFalse(Cache::has($this->key()));
        $this->assertSame(1, DosenDashboardStats::for($this->dosen)['totalAssignments']);
    }

    public function test_ganti_dosen_pengampu_membersihkan_cache_dosen_lama_dan_baru(): void
    {
        $dosenBaru = User::factory()->dosen()->create();
        DosenDashboardStats::for($this->dosen);
        DosenDashboardStats::for($dosenBaru);

        $this->course->update(['lecturer_id' => $dosenBaru->id]);

        $this->assertFalse(Cache::has(DosenDashboardStats::key($this->dosen->id)));
        $this->assertFalse(Cache::has(DosenDashboardStats::key($dosenBaru->id)));
    }

    public function test_perlu_dinilai_selalu_segar_dan_tidak_ikut_di_cache(): void
    {
        $this->assertSame(1, DosenDashboardStats::for($this->dosen)['pendingGrading']);

        Grade::factory()->create([
            'submission_id' => $this->submission->id,
            'graded_by'     => $this->dosen->id,
        ]);

        // Cache tiga angka lain tidak disentuh, tetapi "perlu dinilai" langsung turun.
        $this->assertTrue(Cache::has($this->key()));
        $this->assertSame(0, DosenDashboardStats::for($this->dosen)['pendingGrading']);
    }

    public function test_halaman_dashboard_menampilkan_angka_yang_sama(): void
    {
        $html = $this->actingAs($this->dosen)
            ->get(route('dosen.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('id="statTotalStudents">2</span>', $html);
        $this->assertStringContainsString('id="statTotalMaterials">1</span>', $html);
        $this->assertStringContainsString('id="statActiveAssignments">2</span>', $html);
        $this->assertStringContainsString('id="statPendingGrading">1</span>', $html);
    }
}
