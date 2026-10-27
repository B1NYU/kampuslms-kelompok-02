<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ApiV1Test extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/api/v1';

    private User $dosenA;
    private User $dosenB;
    private User $mhs;
    private User $outsider;
    private Course $courseA;
    private Course $courseB;
    private Assignment $published;
    private Assignment $draft;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dosenA   = User::factory()->dosen()->create(['password' => 'password']);
        $this->dosenB   = User::factory()->dosen()->create(['password' => 'password']);
        $this->mhs      = User::factory()->mahasiswa()->create(['password' => 'password']);
        $this->outsider = User::factory()->mahasiswa()->create(['password' => 'password']);

        $this->courseA = Course::factory()->create(['lecturer_id' => $this->dosenA->id]);
        $this->courseB = Course::factory()->create(['lecturer_id' => $this->dosenB->id]);
        $this->courseA->students()->attach($this->mhs->id, ['enrolled_at' => now()]);

        $this->published = Assignment::factory()->active()->create([
            'course_id' => $this->courseA->id, 'created_by' => $this->dosenA->id,
        ]);
        $this->draft = Assignment::factory()->draft()->create([
            'course_id' => $this->courseA->id, 'created_by' => $this->dosenA->id,
        ]);
    }

    /** Token sungguhan (bukan Sanctum::actingAs) supaya perilaku logout/401 realistis. */
    private function as(User $user): array
    {
        // Satu tes = satu proses; di produksi tiap request proses baru, jadi guard harus dilupakan.
        $this->app['auth']->forgetGuards();

        return ['Authorization' => 'Bearer ' . $user->createToken('test')->plainTextToken];
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'course_id'    => $this->courseA->id,
            'title'        => 'Tugas Uji',
            'instructions' => 'Kerjakan soal 1-5.',
            'due_at'       => now()->addWeek()->toDateTimeString(),
        ], $override);
    }

    // ---------------------------------------------------------------- login

    public function test_login_berhasil_dengan_nim_nip_dan_tidak_membocorkan_kolom_sensitif(): void
    {
        $res = $this->postJson(self::BASE . '/auth/login', [
            'identifier' => $this->mhs->nim_nip, 'password' => 'password',
        ])->assertOk()
          ->assertJsonStructure(['data' => ['token', 'token_type', 'expires_at', 'user' => ['id', 'name', 'email', 'role', 'nim_nip']]]);

        $this->assertSame('Bearer', $res->json('data.token_type'));
        $this->assertArrayNotHasKey('password', $res->json('data.user'));
        $this->assertArrayNotHasKey('remember_token', $res->json('data.user'));
    }

    public function test_pesan_login_sama_untuk_identitas_tidak_ada_dan_password_salah(): void
    {
        $salah  = $this->postJson(self::BASE . '/auth/login', ['identifier' => $this->mhs->nim_nip, 'password' => 'salah']);
        $takAda = $this->postJson(self::BASE . '/auth/login', ['identifier' => 'tidak-ada', 'password' => 'salah']);

        $salah->assertStatus(422);
        $takAda->assertStatus(422);
        $this->assertSame($salah->json('errors'), $takAda->json('errors'));
    }

    public function test_login_dibatasi_maksimal_lima_per_menit(): void
    {
        $kode = [];
        for ($i = 0; $i < 7; $i++) {
            $kode[] = $this->postJson(self::BASE . '/auth/login', [
                'identifier' => 'x', 'password' => 'y',
            ])->status();
        }

        $this->assertSame([422, 422, 422, 422, 422, 429, 429], $kode);
    }

    // ------------------------------------------------------ 401 vs 403

    public function test_tanpa_token_mendapat_401_json_walau_tanpa_header_accept(): void
    {
        foreach (['/me', '/courses', '/courses/' . $this->courseA->id, '/courses/' . $this->courseA->id . '/assignments'] as $path) {
            $this->get(self::BASE . $path)->assertStatus(401)->assertJson(['message' => 'Tidak terautentikasi.']);
        }

        $this->postJson(self::BASE . '/assignments', $this->payload())->assertStatus(401);
        $this->putJson(self::BASE . '/assignments/' . $this->published->id, [])->assertStatus(401);
        $this->deleteJson(self::BASE . '/assignments/' . $this->published->id)->assertStatus(401);
    }

    public function test_token_yang_sudah_dicabut_mendapat_401(): void
    {
        $headers = $this->as($this->mhs);

        $this->postJson(self::BASE . '/auth/logout', [], $headers)->assertOk();

        // Guard menyimpan user di memori antar request dalam satu tes; lupakan agar token dicek ulang.
        $this->app['auth']->forgetGuards();

        $this->getJson(self::BASE . '/me', $headers)->assertStatus(401);
    }

    public function test_mahasiswa_ke_endpoint_dosen_mendapat_403_bukan_401(): void
    {
        $h = $this->as($this->mhs);

        $this->postJson(self::BASE . '/assignments', $this->payload(), $h)->assertForbidden();
        $this->putJson(self::BASE . '/assignments/' . $this->published->id, $this->payload(), $h)->assertForbidden();
        $this->deleteJson(self::BASE . '/assignments/' . $this->published->id, [], $h)->assertForbidden();
    }

    public function test_dosen_a_tidak_boleh_menyentuh_data_dosen_b(): void
    {
        $h = $this->as($this->dosenB);

        $this->getJson(self::BASE . '/courses/' . $this->courseA->id, $h)->assertForbidden();
        $this->getJson(self::BASE . '/courses/' . $this->courseA->id . '/assignments', $h)->assertForbidden();
        $this->postJson(self::BASE . '/assignments', $this->payload(), $h)->assertForbidden();
        $this->putJson(self::BASE . '/assignments/' . $this->published->id, $this->payload(), $h)->assertForbidden();
        $this->deleteJson(self::BASE . '/assignments/' . $this->published->id, [], $h)->assertForbidden();

        $this->assertDatabaseHas('assignments', ['id' => $this->published->id, 'title' => $this->published->title]);
    }

    public function test_mahasiswa_tidak_terdaftar_mendapat_403(): void
    {
        $h = $this->as($this->outsider);

        $this->getJson(self::BASE . '/courses/' . $this->courseA->id, $h)->assertForbidden();
        $this->getJson(self::BASE . '/courses/' . $this->courseA->id . '/assignments', $h)->assertForbidden();
    }

    public function test_id_tidak_ada_mendapat_404_json(): void
    {
        $this->getJson(self::BASE . '/courses/99999', $this->as($this->dosenA))
            ->assertNotFound()->assertJson(['message' => 'Sumber daya tidak ditemukan.']);
    }

    // -------------------------------------------------------------- courses

    public function test_daftar_mata_kuliah_sesuai_peran_dan_memuat_meta(): void
    {
        $this->getJson(self::BASE . '/courses', $this->as($this->mhs))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $this->courseA->id)
            ->assertJsonStructure(['data' => [['id', 'code', 'name', 'sks', 'status', 'lecturer' => ['id', 'name']]], 'meta' => ['current_page', 'last_page', 'total']]);

        $this->getJson(self::BASE . '/courses', $this->as($this->dosenB))
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $this->courseB->id);

        $this->getJson(self::BASE . '/courses', $this->as(User::factory()->admin()->create()))
            ->assertOk()->assertJsonPath('meta.total', 2);
    }

    public function test_resource_adalah_daftar_putih_tanpa_data_pribadi_dosen(): void
    {
        $json = $this->getJson(self::BASE . '/courses', $this->as($this->mhs))->json('data.0.lecturer');

        $this->assertSame(['id', 'name'], array_keys($json));
    }

    public function test_daftar_mata_kuliah_tidak_n_plus_satu(): void
    {
        foreach (range(1, 8) as $_) {
            $c = Course::factory()->create(['lecturer_id' => User::factory()->dosen()->create()->id]);
            $c->students()->attach($this->mhs->id, ['enrolled_at' => now()]);
        }

        $h = $this->as($this->mhs);
        DB::enableQueryLog();
        $this->getJson(self::BASE . '/courses', $h)->assertOk()->assertJsonCount(9, 'data');
        $jumlah = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Jumlah query tidak boleh tumbuh sesuai jumlah baris (token, user, count, list, lecturer).
        $this->assertLessThanOrEqual(8, $jumlah, "Terlalu banyak query ($jumlah): kemungkinan N+1.");
    }

    public function test_detail_mata_kuliah_mahasiswa_tidak_menghitung_tugas_draft(): void
    {
        $this->getJson(self::BASE . '/courses/' . $this->courseA->id, $this->as($this->mhs))
            ->assertOk()->assertJsonPath('data.assignments_count', 1);

        $this->getJson(self::BASE . '/courses/' . $this->courseA->id, $this->as($this->dosenA))
            ->assertOk()->assertJsonPath('data.assignments_count', 2);
    }

    // ---------------------------------------------------------- assignments

    public function test_mahasiswa_hanya_melihat_tugas_published_beserta_status_pengumpulannya(): void
    {
        Submission::factory()->create(['assignment_id' => $this->published->id, 'user_id' => $this->mhs->id, 'is_late' => false]);

        $res = $this->getJson(self::BASE . '/courses/' . $this->courseA->id . '/assignments', $this->as($this->mhs))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.submission_status', 'terkumpul')
            ->assertJsonStructure(['meta' => ['current_page', 'last_page', 'total']]);

        $this->assertNotContains($this->draft->id, collect($res->json('data'))->pluck('id')->all());
    }

    public function test_dosen_melihat_draft_dan_jumlah_pengumpulan(): void
    {
        $this->getJson(self::BASE . '/courses/' . $this->courseA->id . '/assignments', $this->as($this->dosenA))
            ->assertOk()->assertJsonCount(2, 'data')->assertJsonStructure(['data' => [['submissions_count']]]);
    }

    public function test_store_mengembalikan_201_dan_objek_yang_dibuat(): void
    {
        $this->postJson(self::BASE . '/assignments', $this->payload(), $this->as($this->dosenA))
            ->assertStatus(201)
            ->assertJsonPath('data.title', 'Tugas Uji')
            ->assertJsonPath('data.status', 'draft')
            ->assertJsonPath('data.created_by', $this->dosenA->id);
    }

    public function test_store_otorisasi_dicek_sebelum_validasi(): void
    {
        // Dosen B + course milik A + data kosong: harus 403, bukan 422.
        $this->postJson(self::BASE . '/assignments', ['course_id' => $this->courseA->id], $this->as($this->dosenB))
            ->assertForbidden();
    }

    public function test_store_validasi_gagal_mendapat_422(): void
    {
        $this->postJson(self::BASE . '/assignments', ['course_id' => $this->courseA->id], $this->as($this->dosenA))
            ->assertStatus(422)->assertJsonValidationErrors(['title', 'instructions', 'due_at']);
    }

    public function test_update_put_dan_patch(): void
    {
        $h = $this->as($this->dosenA);
        $url = self::BASE . '/assignments/' . $this->draft->id;

        $this->patchJson($url, ['title' => 'Judul Baru'], $h)->assertOk()->assertJsonPath('data.title', 'Judul Baru');

        $this->putJson($url, ['title' => 'Hanya Judul'], $h)->assertStatus(422);
    }

    public function test_destroy_204_dan_409_bila_sudah_ada_pengumpulan(): void
    {
        $h = $this->as($this->dosenA);

        $this->deleteJson(self::BASE . '/assignments/' . $this->draft->id, [], $h)->assertNoContent();
        $this->assertDatabaseMissing('assignments', ['id' => $this->draft->id]);

        Submission::factory()->create(['assignment_id' => $this->published->id, 'user_id' => $this->mhs->id]);
        $this->deleteJson(self::BASE . '/assignments/' . $this->published->id, [], $h)->assertStatus(409);
    }

    public function test_batas_umum_60_permintaan_per_menit(): void
    {
        $h = $this->as($this->mhs);

        $kode = [];
        for ($i = 0; $i < 62; $i++) {
            $kode[] = $this->getJson(self::BASE . '/me', $h)->status();
        }

        $this->assertSame(60, count(array_filter($kode, fn ($k) => $k === 200)));
        $this->assertSame(429, end($kode));
    }

    public function test_respons_429_json_bersih_tanpa_stack_trace(): void
    {
        config(['app.debug' => true]);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson(self::BASE . '/auth/login', ['identifier' => 'x', 'password' => 'y']);
        }

        $res = $this->postJson(self::BASE . '/auth/login', ['identifier' => 'x', 'password' => 'y'])
            ->assertStatus(429)
            ->assertJsonStructure(['message']);

        $this->assertSame(['message'], array_keys($res->json()));
        $this->assertTrue($res->headers->has('Retry-After'));
    }

    public function test_metode_salah_mendapat_405_json_bersih(): void
    {
        $res = $this->deleteJson(self::BASE . '/me', [], $this->as($this->mhs))->assertStatus(405);

        $this->assertSame(['message'], array_keys($res->json()));
    }
}
