<?php

namespace Tests\Feature;

use App\Events\SubmissionGraded;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Enam endpoint kontrak API yang ditambahkan setelah 9 endpoint awal:
 *   GET  /courses/{id}/materials          GET  /assignments/{id}/submissions
 *   POST /assignments/{id}/submissions    PUT  /submissions/{id}/grade
 *   GET  /notifications                   POST /notifications/{id}/read
 * Tiap endpoint diuji dengan empat kondisi: 401, 403/404, data milik orang lain, hak benar.
 */
class ApiV1EndpointBaruTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/api/v1';

    private User $dosenA;
    private User $dosenB;
    private User $mhs;
    private User $mhs2;
    private User $outsider;
    private Course $courseA;
    private Course $courseB;
    private Assignment $published;
    private Assignment $draft;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake();

        $this->dosenA   = User::factory()->dosen()->create();
        $this->dosenB   = User::factory()->dosen()->create();
        $this->mhs      = User::factory()->mahasiswa()->create();
        $this->mhs2     = User::factory()->mahasiswa()->create();
        $this->outsider = User::factory()->mahasiswa()->create();

        $this->courseA = Course::factory()->create(['lecturer_id' => $this->dosenA->id]);
        $this->courseB = Course::factory()->create(['lecturer_id' => $this->dosenB->id]);
        $this->courseA->students()->attach([
            $this->mhs->id  => ['enrolled_at' => now()],
            $this->mhs2->id => ['enrolled_at' => now()],
        ]);

        $this->published = Assignment::factory()->active()->create([
            'course_id' => $this->courseA->id, 'created_by' => $this->dosenA->id,
        ]);
        $this->draft = Assignment::factory()->draft()->create([
            'course_id' => $this->courseA->id, 'created_by' => $this->dosenA->id,
        ]);
    }

    private function as(User $user): array
    {
        $this->app['auth']->forgetGuards();

        return ['Authorization' => 'Bearer ' . $user->createToken('test')->plainTextToken];
    }

    private function berkas(string $nama = 'jawaban.pdf'): UploadedFile
    {
        return UploadedFile::fake()->create($nama, 100, 'application/pdf');
    }

    // ============================================================ GET /courses/{id}/materials

    public function test_materi_tanpa_token_401(): void
    {
        $this->getJson(self::BASE . "/courses/{$this->courseA->id}/materials")->assertStatus(401);
    }

    public function test_materi_mahasiswa_tidak_terdaftar_dan_dosen_lain_mendapat_403(): void
    {
        $url = self::BASE . "/courses/{$this->courseA->id}/materials";

        $this->getJson($url, $this->as($this->outsider))->assertForbidden();
        $this->getJson($url, $this->as($this->dosenB))->assertForbidden();
    }

    public function test_materi_hak_benar_200_tanpa_file_path_dan_tanpa_n_plus_satu(): void
    {
        Material::factory()->count(2)->create(['course_id' => $this->courseA->id]);
        Material::factory()->create(['course_id' => $this->courseB->id, 'uploaded_by' => $this->dosenB->id]);

        $url = self::BASE . "/courses/{$this->courseA->id}/materials";

        $hitung = function () use ($url): int {
            $h = $this->as($this->mhs);
            DB::flushQueryLog();
            DB::enableQueryLog();
            $this->getJson($url, $h)->assertOk();
            $n = count(DB::getQueryLog());
            DB::disableQueryLog();

            return $n;
        };

        $res = $this->getJson($url, $this->as($this->mhs))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 2)
            ->assertJsonStructure(['data' => [['id', 'course_id', 'title', 'type', 'uploader' => ['id', 'name']]], 'meta']);

        $this->assertArrayNotHasKey('file_path', $res->json('data.0'));
        $this->assertSame([$this->courseA->id], array_unique(array_column($res->json('data'), 'course_id'))); // MK lain tidak ikut

        $dengan2 = $hitung();
        Material::factory()->count(5)->create(['course_id' => $this->courseA->id]);   // tiap materi punya pengunggah sendiri
        $dengan7 = $hitung();

        $this->assertSame($dengan2, $dengan7, "Jumlah query naik dari $dengan2 ke $dengan7: ada N+1.");
    }

    public function test_materi_mk_draft_tidak_terbuka_bagi_mahasiswa_tetapi_terbuka_bagi_dosen_pengampu(): void
    {
        $this->courseA->update(['status' => 'draft']);
        $url = self::BASE . "/courses/{$this->courseA->id}/materials";

        $this->getJson($url, $this->as($this->mhs))->assertForbidden();
        $this->getJson($url, $this->as($this->dosenA))->assertOk();
    }

    public function test_materi_id_tidak_ada_404(): void
    {
        $this->getJson(self::BASE . '/courses/999999/materials', $this->as($this->dosenA))
            ->assertNotFound()->assertJson(['message' => 'Sumber daya tidak ditemukan.']);
    }

    // ============================================================ GET /assignments/{id}/submissions

    public function test_daftar_pengumpulan_tanpa_token_401(): void
    {
        $this->getJson(self::BASE . "/assignments/{$this->published->id}/submissions")->assertStatus(401);
    }

    public function test_daftar_pengumpulan_mahasiswa_dan_dosen_lain_mendapat_403(): void
    {
        $url = self::BASE . "/assignments/{$this->published->id}/submissions";
        $this->pengumpulan();   // ada data milik mahasiswa; tetap tidak boleh dibaca lewat endpoint ini

        // Kontrak: hanya dosen (pemilik). Mahasiswa, termasuk pemilik data, dan dosen lain: 403.
        $this->getJson($url, $this->as($this->mhs))->assertForbidden();
        $this->getJson($url, $this->as($this->outsider))->assertForbidden();
        $this->getJson($url, $this->as($this->dosenB))->assertForbidden();
    }

    public function test_daftar_pengumpulan_dosen_pengampu_melihat_semua_beserta_nilai_tanpa_n_plus_satu(): void
    {
        $s1 = Submission::factory()->create(['assignment_id' => $this->published->id, 'user_id' => $this->mhs->id]);
        Submission::factory()->create(['assignment_id' => $this->published->id, 'user_id' => $this->mhs2->id]);
        Grade::factory()->create(['submission_id' => $s1->id, 'graded_by' => $this->dosenA->id]);

        $url = self::BASE . "/assignments/{$this->published->id}/submissions";

        $hitung = function () use ($url): int {
            $h = $this->as($this->dosenA);          // pembuatan token tidak ikut dihitung
            DB::flushQueryLog();
            DB::enableQueryLog();
            $this->getJson($url, $h)->assertOk();
            $n = count(DB::getQueryLog());
            DB::disableQueryLog();

            return $n;
        };

        $res = $this->getJson($url, $this->as($this->dosenA))
            ->assertOk()->assertJsonCount(2, 'data')->assertJsonPath('meta.total', 2);
        $this->assertNotNull(collect($res->json('data'))->firstWhere('id', $s1->id)['grade']);

        $dengan2 = $hitung();

        // Tambah 4 pengumpulan lagi (masing-masing punya mahasiswa dan nilai sendiri).
        Submission::factory()->count(4)->create(['assignment_id' => $this->published->id])
            ->each(fn ($s) => Grade::factory()->create(['submission_id' => $s->id, 'graded_by' => $this->dosenA->id]));

        $dengan6 = $hitung();

        $this->assertSame($dengan2, $dengan6, "Jumlah query naik dari $dengan2 ke $dengan6 saat baris bertambah: ada N+1.");
    }

    public function test_daftar_pengumpulan_tugas_draft_tetap_terbuka_bagi_dosen_pengampu(): void
    {
        $s = Submission::factory()->count(2)->create(['assignment_id' => $this->draft->id]);

        $res = $this->getJson(self::BASE . "/assignments/{$this->draft->id}/submissions", $this->as($this->dosenA))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 2);

        $this->assertEqualsCanonicalizing($s->pluck('id')->all(), collect($res->json('data'))->pluck('id')->all());
    }

    // ============================================================ POST /assignments/{id}/submissions

    public function test_kumpul_tanpa_token_401(): void
    {
        $this->postJson(self::BASE . "/assignments/{$this->published->id}/submissions")->assertStatus(401);
    }

    public function test_kumpul_oleh_dosen_mendapat_403(): void
    {
        $this->post(self::BASE . "/assignments/{$this->published->id}/submissions",
            ['file' => $this->berkas()], $this->as($this->dosenA) + ['Accept' => 'application/json'])
            ->assertForbidden();
    }

    public function test_kumpul_oleh_mahasiswa_tidak_terdaftar_403_sebelum_validasi(): void
    {
        // Tanpa file sama sekali: kalau validasi jalan dulu hasilnya 422, bukan 403.
        $this->postJson(self::BASE . "/assignments/{$this->published->id}/submissions", [], $this->as($this->outsider))
            ->assertForbidden();
    }

    public function test_kumpul_berhasil_201_dan_user_id_dipaksa_dari_token_bukan_dari_input(): void
    {
        $res = $this->post(
            self::BASE . "/assignments/{$this->published->id}/submissions",
            ['file' => $this->berkas(), 'note' => 'Halo', 'user_id' => $this->mhs2->id],   // upaya IDOR
            $this->as($this->mhs) + ['Accept' => 'application/json'],
        )->assertCreated()
         ->assertJsonPath('data.assignment_id', $this->published->id)
         ->assertJsonPath('data.is_late', false);

        $this->assertArrayNotHasKey('file_path', $res->json('data'));
        $this->assertDatabaseHas('submissions', [
            'id' => $res->json('data.id'), 'user_id' => $this->mhs->id,
        ]);
        $this->assertDatabaseMissing('submissions', ['user_id' => $this->mhs2->id]);
    }

    public function test_kumpul_dua_kali_ditolak_403_dan_pengumpulan_pertama_tidak_berubah(): void
    {
        $url = self::BASE . "/assignments/{$this->published->id}/submissions";
        $h   = $this->as($this->mhs) + ['Accept' => 'application/json'];

        $this->post($url, ['file' => $this->berkas()], $h)->assertCreated();
        $this->post($url, ['file' => $this->berkas('lain.pdf')], $h)->assertForbidden();

        $this->assertSame(1, Submission::where('assignment_id', $this->published->id)->where('user_id', $this->mhs->id)->count());
    }

    public function test_kumpul_validasi_berkas_422(): void
    {
        $url = self::BASE . "/assignments/{$this->published->id}/submissions";
        $h   = $this->as($this->mhs) + ['Accept' => 'application/json'];

        $this->post($url, [], $h)->assertStatus(422)->assertJsonValidationErrors('file');
        $this->post($url, ['file' => UploadedFile::fake()->create('malware.exe', 10)], $h)
            ->assertStatus(422)->assertJsonValidationErrors('file');
    }

    public function test_kumpul_ke_tugas_draft_ditolak_403(): void
    {
        $this->post(self::BASE . "/assignments/{$this->draft->id}/submissions",
            ['file' => $this->berkas()], $this->as($this->mhs) + ['Accept' => 'application/json'])
            ->assertForbidden();
    }

    // ============================================================ PUT /submissions/{id}/grade

    private function pengumpulan(?User $mahasiswa = null): Submission
    {
        return Submission::factory()->create([
            'assignment_id' => $this->published->id, 'user_id' => ($mahasiswa ?? $this->mhs)->id,
        ]);
    }

    public function test_nilai_tanpa_token_401(): void
    {
        $s = $this->pengumpulan();
        $this->putJson(self::BASE . "/submissions/{$s->id}/grade", ['score' => 80, 'feedback' => 'ok'])->assertStatus(401);
    }

    public function test_nilai_oleh_mahasiswa_403_dan_dosen_lain_403(): void
    {
        $s    = $this->pengumpulan();
        $url  = self::BASE . "/submissions/{$s->id}/grade";
        $body = ['score' => 80, 'feedback' => 'ok'];

        $this->putJson($url, $body, $this->as($this->mhs))->assertForbidden();
        $this->putJson($url, $body, $this->as($this->dosenB))->assertForbidden();
        $this->assertDatabaseCount('grades', 0);
    }

    public function test_nilai_otorisasi_dicek_sebelum_validasi(): void
    {
        $s = $this->pengumpulan();

        // Body kosong: dosen lain tetap 403 (bukan 422).
        $this->putJson(self::BASE . "/submissions/{$s->id}/grade", [], $this->as($this->dosenB))->assertForbidden();
    }

    public function test_nilai_berhasil_201_lalu_nilai_ulang_200_menimpa_dan_batas_mengikuti_max_score(): void
    {
        Event::fake([SubmissionGraded::class]);

        $s   = $this->pengumpulan();
        $url = self::BASE . "/submissions/{$s->id}/grade";

        // Nilai pertama = 201 Created; menilai ulang (menimpa) = 200.
        $this->putJson($url, ['score' => 85.5, 'feedback' => 'Bagus'], $this->as($this->dosenA))
            ->assertCreated()
            ->assertJsonPath('data.submission_id', $s->id)
            ->assertJsonPath('data.score', 85.5)
            ->assertJsonPath('data.grader.id', $this->dosenA->id);

        $this->putJson($url, ['score' => 90, 'feedback' => 'Revisi'], $this->as($this->dosenA))
            ->assertOk()->assertJsonPath('data.score', 90);

        $this->assertSame(1, Grade::where('submission_id', $s->id)->count());   // upsert, bukan duplikat
        Event::assertDispatchedTimes(SubmissionGraded::class, 2);

        // max_score tugas = 100 pada factory; di atas itu ditolak.
        $this->putJson($url, ['score' => $this->published->max_score + 1, 'feedback' => 'x'], $this->as($this->dosenA))
            ->assertStatus(422)->assertJsonValidationErrors('score');
        $this->putJson($url, ['score' => -1, 'feedback' => 'x'], $this->as($this->dosenA))
            ->assertStatus(422)->assertJsonValidationErrors('score');
        $this->putJson($url, ['score' => 50], $this->as($this->dosenA))
            ->assertStatus(422)->assertJsonValidationErrors('feedback');
    }

    public function test_nilai_ditolak_bila_mk_tidak_active(): void
    {
        $s = $this->pengumpulan();
        $this->courseA->update(['status' => 'archived']);

        $this->putJson(self::BASE . "/submissions/{$s->id}/grade", ['score' => 80, 'feedback' => 'ok'], $this->as($this->dosenA))
            ->assertForbidden();
    }

    public function test_nilai_menyimpan_ulang_tanpa_perubahan_tidak_mengirim_notifikasi_lagi(): void
    {
        Event::fake([SubmissionGraded::class]);

        $s   = $this->pengumpulan();
        $url = self::BASE . "/submissions/{$s->id}/grade";
        $b   = ['score' => 70, 'feedback' => 'Cukup'];

        $this->putJson($url, $b, $this->as($this->dosenA))->assertCreated();
        $this->putJson($url, $b, $this->as($this->dosenA))->assertOk();

        Event::assertDispatchedTimes(SubmissionGraded::class, 1);
    }

    // ============================================================ GET /notifications

    private function notifikasi(User $untuk, array $data = [], bool $dibaca = false): DatabaseNotification
    {
        return $untuk->notifications()->create([
            'id'      => (string) Str::uuid(),
            'type'    => 'App\\Notifications\\GradeReceivedNotification',
            'data'    => $data + ['kind' => 'grade_received', 'message' => 'Nilai keluar', 'course_name' => 'MK A', 'rahasia' => 'JANGAN-BOCOR'],
            'read_at' => $dibaca ? now() : null,
        ]);
    }

    public function test_daftar_notifikasi_tanpa_token_401(): void
    {
        $this->getJson(self::BASE . '/notifications')->assertStatus(401);
    }

    public function test_daftar_notifikasi_hanya_milik_sendiri_daftar_putih_dan_unread_count(): void
    {
        $a = $this->notifikasi($this->mhs);
        $this->notifikasi($this->mhs, [], true);
        $orangLain = $this->notifikasi($this->mhs2);

        $res = $this->getJson(self::BASE . '/notifications', $this->as($this->mhs))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.unread_count', 1);

        $this->assertNotContains($orangLain->id, array_column($res->json('data'), 'id'));
        $this->assertStringNotContainsString('JANGAN-BOCOR', $res->getContent());   // kolom data tidak dibuka mentah
        $this->assertContains($a->id, array_column($res->json('data'), 'id'));
    }

    public function test_daftar_notifikasi_filter_belum_dan_validasi_filter(): void
    {
        $this->notifikasi($this->mhs);
        $this->notifikasi($this->mhs, [], true);

        $this->getJson(self::BASE . '/notifications?filter=belum', $this->as($this->mhs))
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.is_read', false);

        $this->getJson(self::BASE . '/notifications?filter=ngawur', $this->as($this->mhs))
            ->assertStatus(422)->assertJsonValidationErrors('filter');
    }

    public function test_notifikasi_hanya_mengembalikan_id_dan_teks_bukan_kolom_data_mentah(): void
    {
        $this->notifikasi($this->mhs, ['assignment_id' => 5, 'submission_id' => 9]);

        $item = $this->getJson(self::BASE . '/notifications', $this->as($this->mhs))
            ->assertOk()->json('data.0');

        $this->assertSame(5, $item['assignment_id']);
        $this->assertSame(9, $item['submission_id']);
        $this->assertEqualsCanonicalizing(
            ['id', 'kind', 'message', 'course', 'assignment_id', 'submission_id', 'is_read', 'read_at', 'created_at'],
            array_keys($item),
        );
    }

    // ============================================================ POST /notifications/{id}/read

    public function test_tandai_dibaca_tanpa_token_401(): void
    {
        $n = $this->notifikasi($this->mhs);
        $this->postJson(self::BASE . "/notifications/{$n->id}/read")->assertStatus(401);
    }

    public function test_tandai_dibaca_notifikasi_orang_lain_404_bukan_403_dan_tidak_berubah(): void
    {
        $milikDia = $this->notifikasi($this->mhs2);

        $this->postJson(self::BASE . "/notifications/{$milikDia->id}/read", [], $this->as($this->mhs))
            ->assertNotFound();

        $this->assertNull($milikDia->fresh()->read_at);
    }

    public function test_tandai_dibaca_id_sembarang_atau_tidak_ada_404(): void
    {
        $h = $this->as($this->mhs);

        $this->postJson(self::BASE . '/notifications/bukan-uuid/read', [], $h)->assertNotFound();
        $this->postJson(self::BASE . '/notifications/' . Str::uuid() . '/read', [], $h)->assertNotFound();
    }

    public function test_tandai_dibaca_berhasil_200_dan_idempoten(): void
    {
        $n = $this->notifikasi($this->mhs);
        $h = $this->as($this->mhs);

        $this->postJson(self::BASE . "/notifications/{$n->id}/read", [], $h)
            ->assertOk()->assertJsonPath('data.is_read', true);

        $this->assertNotNull($n->fresh()->read_at);

        $this->postJson(self::BASE . "/notifications/{$n->id}/read", [], $h)->assertOk();
        $this->getJson(self::BASE . '/notifications', $h)->assertJsonPath('meta.unread_count', 0);
    }
}
