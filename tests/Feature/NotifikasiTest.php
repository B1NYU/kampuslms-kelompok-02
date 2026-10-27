<?php

namespace Tests\Feature;

use App\Events\AssignmentPublished;
use App\Events\SubmissionGraded;
use App\Listeners\SendAssignmentNotification;
use App\Listeners\SendGradeNotification;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;
use App\Notifications\GradeReceivedNotification;
use App\Notifications\NewAssignmentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

/**
 * Minggu 10: Event, Listener, Notification, Queue, dan lonceng notifikasi.
 * phpunit.xml memakai QUEUE_CONNECTION=sync; tes yang butuh antrean sungguhan mengganti ke 'database'.
 */
class NotifikasiTest extends TestCase
{
    use RefreshDatabase;

    private User $dosen;
    private User $dosenLain;
    private User $admin;
    private User $mhs1;
    private User $mhs2;
    private User $mhs3;
    private User $luar;
    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->dosen     = User::factory()->dosen()->create();
        $this->dosenLain = User::factory()->dosen()->create();
        $this->admin     = User::factory()->admin()->create();
        $this->mhs1      = User::factory()->mahasiswa()->create(['name' => 'Mhs Satu']);
        $this->mhs2      = User::factory()->mahasiswa()->create();
        $this->mhs3      = User::factory()->mahasiswa()->create();
        $this->luar      = User::factory()->mahasiswa()->create();

        $this->course = Course::factory()->create(['lecturer_id' => $this->dosen->id, 'status' => 'active', 'name' => 'Pemrograman Web']);
        foreach ([$this->mhs1, $this->mhs2, $this->mhs3] as $m) {
            $this->course->students()->attach($m->id, ['enrolled_at' => now()]);
        }
    }

    // ----------------------------------------------------------------- pembantu

    private function dataTugas(string $status = 'published', array $ganti = []): array
    {
        return array_merge([
            'title' => 'Tugas Baru Uji', 'instructions' => 'Kerjakan.', 'due_at' => now()->addWeek()->toDateTimeString(),
            'max_score' => 100, 'allow_late' => 1, 'status' => $status,
        ], $ganti);
    }

    private function buatTugas(string $status = 'draft', ?Course $course = null): Assignment
    {
        return Assignment::factory()->create([
            'course_id' => ($course ?? $this->course)->id, 'created_by' => $this->dosen->id,
            'status' => $status, 'due_at' => now()->addWeek(), 'title' => 'Judul ' . Str::random(5),
        ]);
    }

    private function sebagai(User $user): static
    {
        $this->app['auth']->forgetGuards();

        return $this->actingAs($user);
    }

    private function terbitkanLewatWeb(string $status = 'published'): void
    {
        $this->sebagai($this->dosen)
            ->post(route('dosen.courses.assignments.store', $this->course), $this->dataTugas($status))
            ->assertSessionHasNoErrors();
    }

    private function pengumpulan(User $mhs, ?Assignment $tugas = null): Submission
    {
        return Submission::factory()->create([
            'assignment_id' => ($tugas ?? $this->buatTugas('published'))->id, 'user_id' => $mhs->id,
        ]);
    }

    private function notifikasiMilik(User $u)
    {
        return DatabaseNotification::query()->where('notifiable_id', $u->id)->where('notifiable_type', User::class)->get();
    }

    // ============================================================ 1. PEMICU EVENT

    public function test_tugas_published_mengumumkan_event_sedangkan_draft_tidak(): void
    {
        Event::fake([AssignmentPublished::class]);

        $this->terbitkanLewatWeb('draft');
        Event::assertNotDispatched(AssignmentPublished::class);

        $this->terbitkanLewatWeb('published');
        Event::assertDispatchedTimes(AssignmentPublished::class, 1);
    }

    public function test_draft_menjadi_published_mengumumkan_sekali_dan_simpan_ulang_tidak_mengulang(): void
    {
        Event::fake([AssignmentPublished::class]);
        $tugas = $this->buatTugas('draft');

        $this->sebagai($this->dosen)->put(route('dosen.assignments.update', $tugas), $this->dataTugas('published'));
        Event::assertDispatchedTimes(AssignmentPublished::class, 1);

        // Mengubah judul tugas yang sudah published: bukan peralihan, tidak boleh mengumumkan lagi.
        $this->sebagai($this->dosen)->put(route('dosen.assignments.update', $tugas), $this->dataTugas('published', ['title' => 'Judul Diubah']));
        Event::assertDispatchedTimes(AssignmentPublished::class, 1);
    }

    public function test_published_kembali_draft_lalu_published_lagi_mengumumkan_lagi(): void
    {
        Event::fake([AssignmentPublished::class]);
        $tugas = $this->buatTugas('published');

        $this->sebagai($this->dosen)->put(route('dosen.assignments.update', $tugas), $this->dataTugas('draft'));
        Event::assertNotDispatched(AssignmentPublished::class);

        $this->sebagai($this->dosen)->put(route('dosen.assignments.update', $tugas), $this->dataTugas('published'));
        Event::assertDispatchedTimes(AssignmentPublished::class, 1);
    }

    public function test_jalur_admin_dan_api_juga_mengumumkan_event(): void
    {
        Event::fake([AssignmentPublished::class]);

        // Admin: buat published, lalu ubah draft -> published.
        $this->sebagai($this->admin)->post('/admin/tugas', ['course_id' => $this->course->id] + $this->dataTugas('published'));
        Event::assertDispatchedTimes(AssignmentPublished::class, 1);

        $draft = $this->buatTugas('draft');
        $this->sebagai($this->admin)->put('/admin/tugas/' . $draft->id, ['course_id' => $this->course->id] + $this->dataTugas('published'));
        Event::assertDispatchedTimes(AssignmentPublished::class, 2);

        // API: buat published, lalu PATCH draft -> published.
        $this->app['auth']->forgetGuards();
        $token = $this->dosen->createToken('t')->plainTextToken;
        $this->withToken($token)->postJson('/api/v1/assignments', ['course_id' => $this->course->id] + $this->dataTugas('published'))->assertCreated();
        Event::assertDispatchedTimes(AssignmentPublished::class, 3);

        $draft2 = $this->buatTugas('draft');
        $this->app['auth']->forgetGuards();
        $this->withToken($token)->patchJson('/api/v1/assignments/' . $draft2->id, ['status' => 'published'])->assertOk();
        Event::assertDispatchedTimes(AssignmentPublished::class, 4);
    }

    public function test_penilaian_mengumumkan_event_hanya_bila_nilai_atau_feedback_berubah(): void
    {
        Event::fake([SubmissionGraded::class]);
        $sub = $this->pengumpulan($this->mhs1);
        $url = route('dosen.penilaian.update', $sub);

        $this->sebagai($this->dosen)->putJson($url, ['score' => 80, 'feedback' => 'Baik'])->assertOk();
        Event::assertDispatched(SubmissionGraded::class, fn ($e) => $e->regraded === false);

        // Simpan ulang tanpa perubahan: tidak ada notifikasi ulang.
        $this->sebagai($this->dosen)->putJson($url, ['score' => 80, 'feedback' => 'Baik'])->assertOk();
        Event::assertDispatchedTimes(SubmissionGraded::class, 1);

        // Skor berubah: diumumkan sebagai nilai diperbarui.
        $this->sebagai($this->dosen)->putJson($url, ['score' => 90, 'feedback' => 'Baik'])->assertOk();
        Event::assertDispatchedTimes(SubmissionGraded::class, 2);
        Event::assertDispatched(SubmissionGraded::class, fn ($e) => $e->regraded === true);
    }

    // ============================================================ 2. LISTENER + NOTIFICATION (tugas baru)

    public function test_hanya_mahasiswa_terdaftar_menerima_notifikasi_tugas_baru(): void
    {
        $this->terbitkanLewatWeb();

        foreach ([$this->mhs1, $this->mhs2, $this->mhs3] as $m) {
            $this->assertCount(1, $this->notifikasiMilik($m), 'mahasiswa terdaftar harus menerima');
        }
        $this->assertCount(0, $this->notifikasiMilik($this->luar), 'mahasiswa lain tidak boleh menerima');
        $this->assertCount(0, $this->notifikasiMilik($this->dosen), 'dosen pembuat tidak menerima');
        $this->assertCount(0, $this->notifikasiMilik($this->dosenLain));
        $this->assertDatabaseCount('notifications', 3);
    }

    public function test_payload_tugas_baru_berisi_id_dan_judul_dengan_url_relatif(): void
    {
        $this->terbitkanLewatWeb();
        $tugas = Assignment::latest('id')->first();
        $n = $this->notifikasiMilik($this->mhs1)->first();

        $this->assertSame(NewAssignmentNotification::class, $n->type);
        $this->assertSame([
            'kind' => 'assignment_published', 'assignment_id' => $tugas->id, 'course_id' => $this->course->id,
            'course_name' => 'Pemrograman Web', 'title' => 'Tugas Baru Uji',
            'due_at' => $tugas->due_at->toIso8601String(),
            'message' => 'Tugas baru di Pemrograman Web: Tugas Baru Uji',
            'url' => '/assignments/' . $tugas->id,
        ], $n->data);
        $this->assertNull($n->read_at);
    }

    public function test_tidak_ada_notifikasi_untuk_mk_draft_atau_tugas_yang_sudah_kembali_ke_draft(): void
    {
        // MK draft: mahasiswa belum boleh melihat tugasnya, jadi tidak ada notifikasi yang berujung 403.
        $mkDraft = Course::factory()->create(['lecturer_id' => $this->dosen->id, 'status' => 'draft']);
        $mkDraft->students()->attach($this->mhs1->id, ['enrolled_at' => now()]);
        $tugasMkDraft = $this->buatTugas('published', $mkDraft);
        (new SendAssignmentNotification)->handle(new AssignmentPublished($tugasMkDraft));
        $this->assertCount(0, $this->notifikasiMilik($this->mhs1));

        // Antrean terlambat: saat diproses, tugas sudah dikembalikan ke draft.
        $tugas = $this->buatTugas('published');
        $event = new AssignmentPublished($tugas);
        $tugas->update(['status' => 'draft']);
        (new SendAssignmentNotification)->handle($event);
        $this->assertCount(0, $this->notifikasiMilik($this->mhs1));
    }

    public function test_pemilihan_penerima_di_listener_tidak_n_plus_satu(): void
    {
        // Notification::fake() menahan pengiriman, jadi yang terhitung hanya kerja listener itu sendiri.
        // (Job per-penerima di worker memang mengambil ulang tugas/MK/penerima: itu rancangan antrean,
        //  bukan N+1 di listener.)
        Notification::fake();

        $hitungSelect = function (): int {
            $tugas = $this->buatTugas('published');
            $jumlah = 0;
            DB::listen(function ($q) use (&$jumlah) {
                if (str_starts_with(strtolower(ltrim($q->sql)), 'select')) {
                    $jumlah++;
                }
            });
            (new SendAssignmentNotification)->handle(new AssignmentPublished($tugas));

            return $jumlah;
        };

        $sedikit = $hitungSelect();                       // 3 mahasiswa
        foreach (User::factory()->mahasiswa()->count(27)->create() as $m) {
            $this->course->students()->attach($m->id, ['enrolled_at' => now()]);
        }
        $banyak = $hitungSelect();                        // 30 mahasiswa

        $this->assertSame($sedikit, $banyak, "SELECT bertambah dari {$sedikit} ke {$banyak}: kemungkinan N+1");
        Notification::assertSentToTimes($this->mhs1, NewAssignmentNotification::class, 2);   // satu per tugas yang diumumkan
    }

    // ============================================================ 3. QUEUE SUNGGUHAN

    public function test_worker_mati_job_menumpuk_tanpa_error_lalu_worker_menghasilkan_notifikasi(): void
    {
        config(['queue.default' => 'database']);

        $this->terbitkanLewatWeb();   // request berhasil, tugas tersimpan

        // Worker belum jalan: 1 job (listener), nol notifikasi, dan TIDAK ada error di mana pun.
        $this->assertDatabaseCount('jobs', 1);
        $this->assertDatabaseCount('notifications', 0);
        $this->assertDatabaseCount('failed_jobs', 0);
        $this->assertStringContainsString('SendAssignmentNotification', DB::table('jobs')->value('payload'));

        // Worker berjalan: listener melahirkan 1 job per penerima, lalu semuanya diproses.
        Artisan::call('queue:work', ['--stop-when-empty' => true, '--sleep' => 0]);

        $this->assertDatabaseCount('notifications', 3);
        $this->assertDatabaseCount('jobs', 0);
        $this->assertDatabaseCount('failed_jobs', 0);
    }

    public function test_job_gagal_diulang_sesuai_tries_dan_backoff_lalu_masuk_failed_jobs(): void
    {
        config(['queue.default' => 'database']);
        Log::spy();

        $this->terbitkanLewatWeb();
        Schema::drop('notifications');   // membuat pengiriman notifikasi selalu gagal

        // Percobaan 1: listener sukses menitipkan 3 job notifikasi, ketiganya gagal dan dijadwalkan ulang (+10 dtk).
        Artisan::call('queue:work', ['--stop-when-empty' => true, '--sleep' => 0]);
        $this->assertDatabaseCount('failed_jobs', 0);
        $this->assertSame(3, DB::table('jobs')->count(), 'job yang gagal kembali ke antrean untuk dicoba lagi');

        // Percobaan 2 (+10 dtk) gagal lagi, dijadwalkan ulang +60 dtk.
        $this->travel(11)->seconds();
        Artisan::call('queue:work', ['--stop-when-empty' => true, '--sleep' => 0]);
        $this->assertDatabaseCount('failed_jobs', 0);

        // Percobaan 3 (tries = 3) gagal: tidak diulang lagi, masuk failed_jobs.
        $this->travel(61)->seconds();
        Artisan::call('queue:work', ['--stop-when-empty' => true, '--sleep' => 0]);
        $this->travelBack();

        $this->assertDatabaseCount('failed_jobs', 3);
        $this->assertDatabaseCount('jobs', 0);
        Log::shouldHaveReceived('error')->withArgs(fn ($pesan) => str_contains($pesan, 'Notifikasi tugas baru gagal dikirim'))->times(3);
    }

    public function test_konfigurasi_ketahanan_listener_dan_notification(): void
    {
        $assignment = $this->buatTugas('published');
        $submission = $this->pengumpulan($this->mhs1);
        $grade = Grade::create(['submission_id' => $submission->id, 'graded_by' => $this->dosen->id, 'score' => 80, 'feedback' => 'x', 'graded_at' => now()]);

        foreach ([new SendAssignmentNotification, new SendGradeNotification, new NewAssignmentNotification($assignment), new GradeReceivedNotification($submission)] as $objek) {
            $nama = class_basename($objek);
            $this->assertInstanceOf(ShouldQueue::class, $objek, "{$nama} harus ShouldQueue");
            $this->assertSame(3, $objek->tries, "{$nama}::\$tries");
            $this->assertSame([10, 60], $objek->backoff, "{$nama}::\$backoff");
            $this->assertTrue(method_exists($objek, 'failed'), "{$nama} harus punya failed()");
        }

        // failed() mencatat log.
        Log::spy();
        (new SendAssignmentNotification)->failed(new AssignmentPublished($assignment), new RuntimeException('boom'));
        (new SendGradeNotification)->failed(new SubmissionGraded($grade), new RuntimeException('boom'));
        (new NewAssignmentNotification($assignment))->failed(new RuntimeException('boom'));
        (new GradeReceivedNotification($submission))->failed(new RuntimeException('boom'));
        Log::shouldHaveReceived('error')->times(4);
    }

    public function test_listener_ditemukan_otomatis_oleh_laravel(): void
    {
        $this->assertTrue(app('events')->hasListeners(AssignmentPublished::class));
        $this->assertTrue(app('events')->hasListeners(SubmissionGraded::class));
    }

    // ============================================================ 4. NOTIFIKASI NILAI

    public function test_nilai_keluar_notifikasi_ke_pemilik_tanpa_membocorkan_nilai_atau_feedback(): void
    {
        $sub = $this->pengumpulan($this->mhs1);

        $this->sebagai($this->dosen)
            ->putJson(route('dosen.penilaian.update', $sub), ['score' => 87.5, 'feedback' => 'RAHASIA-FEEDBACK-XYZ'])
            ->assertOk();

        $this->assertCount(1, $this->notifikasiMilik($this->mhs1));
        $this->assertCount(0, $this->notifikasiMilik($this->mhs2), 'mahasiswa lain tidak diberi tahu');
        $this->assertCount(0, $this->notifikasiMilik($this->dosen));

        $n = $this->notifikasiMilik($this->mhs1)->first();
        $this->assertSame(GradeReceivedNotification::class, $n->type);
        $this->assertSame('grade_received', $n->data['kind']);
        $this->assertFalse($n->data['regraded']);
        $this->assertSame('/submissions/' . $sub->id, $n->data['url']);

        // Pemeriksaan pada data MENTAH di database, bukan pada objek.
        $mentah = DB::table('notifications')->where('id', $n->id)->value('data');
        $this->assertStringNotContainsString('RAHASIA-FEEDBACK-XYZ', $mentah);
        $this->assertStringNotContainsString('87.5', $mentah);
        $this->assertStringNotContainsString('score', $mentah);
        $this->assertStringNotContainsString('feedback', $mentah);
    }

    public function test_nilai_diperbarui_menghasilkan_notifikasi_kedua_bertanda_regraded(): void
    {
        $sub = $this->pengumpulan($this->mhs1);
        $url = route('dosen.penilaian.update', $sub);

        $this->sebagai($this->dosen)->putJson($url, ['score' => 70, 'feedback' => 'Cukup'])->assertOk();
        $this->sebagai($this->dosen)->putJson($url, ['score' => 70, 'feedback' => 'Cukup'])->assertOk();   // sama: diam
        $this->sebagai($this->dosen)->putJson($url, ['score' => 95, 'feedback' => 'Cukup'])->assertOk();

        $semua = $this->notifikasiMilik($this->mhs1)->sortBy('created_at')->values();
        $this->assertCount(2, $semua);
        $this->assertCount(1, $semua->filter(fn ($n) => $n->data['regraded'] === true));
        $this->assertTrue($semua->contains(fn ($n) => str_contains($n->data['message'], 'diperbarui')));
    }

    public function test_tidak_ada_notifikasi_nilai_bila_mahasiswa_sudah_tidak_berhak_membuka_hasilnya(): void
    {
        $sub = $this->pengumpulan($this->mhs1);
        $grade = Grade::create(['submission_id' => $sub->id, 'graded_by' => $this->dosen->id, 'score' => 80, 'feedback' => 'x', 'graded_at' => now()]);

        $this->course->students()->detach($this->mhs1->id);   // dikeluarkan dari kelas sebelum job diproses
        (new SendGradeNotification)->handle(new SubmissionGraded($grade));

        $this->assertCount(0, $this->notifikasiMilik($this->mhs1));
    }

    // ============================================================ 5. LONCENG (polling)

    private function buatNotifikasi(User $u, string $pesan, array $ekstra = []): DatabaseNotification
    {
        $n = (new DatabaseNotification)->forceFill(array_merge([
            'id' => (string) Str::uuid(), 'type' => 'x', 'notifiable_type' => User::class, 'notifiable_id' => $u->id,
            'data' => ['message' => $pesan, 'url' => '/assignments/1'],
            'read_at' => null, 'created_at' => now(), 'updated_at' => now(),
        ], $ekstra));
        $n->save();

        return $n;
    }

    public function test_endpoint_lonceng_hanya_untuk_yang_login(): void
    {
        $this->getJson(route('notifikasi.ringkas'))->assertUnauthorized();
    }

    public function test_endpoint_lonceng_mengembalikan_milik_sendiri_belum_dibaca_terbaru_maksimal_lima(): void
    {
        foreach (range(1, 7) as $i) {
            $this->buatNotifikasi($this->mhs1, "Pesan {$i}", ['created_at' => now()->subMinutes(10 - $i)]);
        }
        $this->buatNotifikasi($this->mhs1, 'Sudah dibaca', ['read_at' => now()]);
        $this->buatNotifikasi($this->mhs2, 'Milik orang lain');

        $res = $this->sebagai($this->mhs1)->getJson(route('notifikasi.ringkas'))->assertOk();

        $res->assertJsonPath('unread_count', 7)->assertJsonCount(5, 'items');
        $this->assertSame(['Pesan 7', 'Pesan 6', 'Pesan 5', 'Pesan 4', 'Pesan 3'], collect($res->json('items'))->pluck('message')->all());
        $this->assertSame(['id', 'message', 'time', 'url'], collect($res->json('items.0'))->keys()->sort()->values()->all());
        $this->assertStringContainsString('no-store', $res->headers->get('Cache-Control'));
        $this->assertStringNotContainsString('Milik orang lain', $res->getContent());
        $this->assertStringNotContainsString('Sudah dibaca', $res->getContent());
    }

    public function test_url_notifikasi_yang_tidak_internal_ditolak(): void
    {
        $this->buatNotifikasi($this->mhs1, 'a', ['data' => ['message' => 'a', 'url' => 'https://jahat.test/x'], 'created_at' => now()->subMinutes(3)]);
        $this->buatNotifikasi($this->mhs1, 'b', ['data' => ['message' => 'b', 'url' => '//jahat.test/x'], 'created_at' => now()->subMinutes(2)]);
        $this->buatNotifikasi($this->mhs1, 'c', ['data' => ['message' => 'c', 'url' => '/assignments/9'], 'created_at' => now()->subMinutes(1)]);

        $urls = collect($this->sebagai($this->mhs1)->getJson(route('notifikasi.ringkas'))->json('items'))->pluck('url', 'message');

        $this->assertNull($urls['a']);
        $this->assertNull($urls['b']);
        $this->assertSame('/assignments/9', $urls['c']);
    }

    public function test_lonceng_tampil_di_navbar_mahasiswa_dengan_hitungan_awal_dan_tidak_di_navbar_dosen(): void
    {
        $this->buatNotifikasi($this->mhs1, 'Satu');
        $this->buatNotifikasi($this->mhs1, 'Dua');

        $html = $this->sebagai($this->mhs1)->get(route('mahasiswa.dashboard'))->assertOk()->getContent();
        $this->assertStringContainsString('id="notifWrapper"', $html);
        $this->assertMatchesRegularExpression('/id="notifBadge"\s*>2</', $html);
        $this->assertStringContainsString(route('notifikasi.ringkas'), $html);

        $htmlDosen = $this->sebagai($this->dosen)->get(route('dosen.dashboard'))->assertOk()->getContent();
        $this->assertStringNotContainsString('notifWrapper', $htmlDosen);
    }

    public function test_endpoint_lonceng_dibatasi_60_per_menit(): void
    {
        $kode = [];
        for ($i = 0; $i < 61; $i++) {
            $kode[] = $this->sebagai($this->mhs1)->getJson(route('notifikasi.ringkas'))->status();
        }

        $this->assertSame(429, end($kode));
        $this->assertSame(60, count(array_filter($kode, fn ($k) => $k === 200)));
    }
}
