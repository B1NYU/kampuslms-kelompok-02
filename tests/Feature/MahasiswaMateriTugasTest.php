<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Alur mahasiswa: lihat mata kuliah, lihat/unduh materi, lihat tugas, kumpulkan tugas.
 */
class MahasiswaMateriTugasTest extends TestCase
{
    use RefreshDatabase;

    private User $dosen;
    private User $dosenLain;
    private User $admin;
    private User $budi;
    private User $outsider;
    private Course $course;
    private Course $courseLain;
    private Assignment $tugas;
    private Assignment $draft;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->withoutVite(); // tes tidak butuh hasil build npm

        $this->dosen     = User::factory()->dosen()->create();
        $this->dosenLain = User::factory()->dosen()->create();
        $this->admin     = User::factory()->admin()->create();
        $this->budi      = User::factory()->mahasiswa()->create(['name' => 'Budi']);
        $this->outsider  = User::factory()->mahasiswa()->create(['name' => 'Orang Luar']);

        $this->course     = Course::factory()->create(['lecturer_id' => $this->dosen->id, 'name' => 'Pemrograman Web']);
        $this->courseLain = Course::factory()->create(['lecturer_id' => $this->dosenLain->id, 'name' => 'Kecerdasan Buatan']);
        $this->course->students()->attach($this->budi->id, ['enrolled_at' => now()]);

        $this->tugas = Assignment::factory()->active()->create([
            'course_id' => $this->course->id, 'created_by' => $this->dosen->id, 'title' => 'Tugas Terbuka',
        ]);
        $this->draft = Assignment::factory()->draft()->create([
            'course_id' => $this->course->id, 'created_by' => $this->dosen->id, 'title' => 'Rahasia Draft',
        ]);
    }

    private function materi(array $override = []): Material
    {
        $path = 'materials/' . $this->course->id . '/modul.pdf';
        Storage::disk('local')->put($path, '%PDF-1.4 isi-materi');

        return Material::factory()->create(array_merge([
            'course_id' => $this->course->id, 'uploaded_by' => $this->dosen->id, 'session' => 1,
            'title' => 'Modul 1', 'file_path' => $path, 'original_name' => 'modul-1.pdf', 'file_size' => 19,
        ], $override));
    }

    private function kumpul(Assignment $a, User $u, ?UploadedFile $file = null, array $extra = [])
    {
        return $this->actingAs($u)->post(
            route('mahasiswa.assignments.submissions.store', $a),
            array_merge(['file' => $file ?? UploadedFile::fake()->create('jawaban.pdf', 100, 'application/pdf')], $extra)
        );
    }

    // ------------------------------------------------------ daftar & detail MK

    public function test_daftar_mata_kuliah_hanya_menampilkan_yang_diikuti_beserta_progres(): void
    {
        Submission::factory()->create(['assignment_id' => $this->tugas->id, 'user_id' => $this->budi->id]);
        $this->materi();

        $this->actingAs($this->budi)->get(route('mahasiswa.mata-kuliah.index'))
            ->assertOk()
            ->assertSee('Pemrograman Web')
            ->assertDontSee('Kecerdasan Buatan')
            ->assertSee('1/1 Tugas Terkumpul')   // draft tidak dihitung
            ->assertSee('1 Materi');
    }

    public function test_detail_mata_kuliah_403_bila_tidak_terdaftar(): void
    {
        $this->actingAs($this->budi)->get(route('mahasiswa.mata-kuliah.show', $this->courseLain))->assertForbidden();
        $this->actingAs($this->outsider)->get(route('mahasiswa.mata-kuliah.show', $this->course))->assertForbidden();
    }

    public function test_detail_mata_kuliah_tidak_membocorkan_judul_tugas_draft(): void
    {
        $this->actingAs($this->budi)->get(route('mahasiswa.mata-kuliah.show', $this->course))
            ->assertOk()
            ->assertSee('Tugas Terbuka')
            ->assertDontSee('Rahasia Draft');
    }

    public function test_materi_tampil_per_pertemuan_dengan_tautan_unduh_nyata(): void
    {
        $m = $this->materi(['session' => 3, 'title' => 'Modul Tiga']);

        $html = $this->actingAs($this->budi)->get(route('mahasiswa.mata-kuliah.show', $this->course))
            ->assertOk()->getContent();

        // Ambil data-materi kartu Minggu 3 dan baca seperti yang dilakukan JavaScript di browser.
        $this->assertSame(1, preg_match('/id="cardWeek3"[^>]*data-materi="([^"]*)"/s', $html, $cocok));
        $daftar = json_decode(html_entity_decode($cocok[1]), true);

        $this->assertCount(1, $daftar);
        $this->assertSame('Modul Tiga', $daftar[0]['judul']);
        $this->assertSame(route('mahasiswa.materi.download', $m), $daftar[0]['url']);
        $this->assertStringNotContainsString("alert('Mengunduh", $html);
    }

    public function test_materi_umum_tanpa_pertemuan_tampil_di_panel_info(): void
    {
        $this->materi(['session' => null, 'title' => 'Panduan Umum Kelas']);

        $this->actingAs($this->budi)->get(route('mahasiswa.mata-kuliah.show', $this->course))
            ->assertOk()->assertSee('Materi Umum')->assertSee('Panduan Umum Kelas');
    }

    public function test_judul_materi_berbahaya_di_escape(): void
    {
        $this->materi(['title' => "<script>alert('xss')</script>"]);

        $html = $this->actingAs($this->budi)->get(route('mahasiswa.mata-kuliah.show', $this->course))->getContent();

        $this->assertStringNotContainsString("<script>alert('xss')</script>", $html);
    }

    // ----------------------------------------------------------- unduh materi

    public function test_mahasiswa_terdaftar_bisa_mengunduh_materi_sebagai_lampiran(): void
    {
        $m = $this->materi();

        $res = $this->actingAs($this->budi)->get(route('mahasiswa.materi.download', $m))->assertOk();

        $this->assertStringContainsString('attachment', $res->headers->get('content-disposition'));
        $this->assertStringContainsString('modul-1.pdf', $res->headers->get('content-disposition'));
        $this->assertSame('%PDF-1.4 isi-materi', $res->streamedContent());
    }

    public function test_unduh_materi_ditolak_untuk_yang_tidak_berhak(): void
    {
        $m = $this->materi();

        $this->get(route('mahasiswa.materi.download', $m))->assertRedirect(route('login'));
        $this->actingAs($this->outsider)->get(route('mahasiswa.materi.download', $m))->assertForbidden();
        $this->actingAs($this->dosenLain)->get(route('dosen.materi.download', $m))->assertForbidden();
        // Dosen tidak memakai route mahasiswa.
        $this->actingAs($this->dosen)->get(route('mahasiswa.materi.download', $m))->assertForbidden();
    }

    public function test_dosen_pengampu_dan_admin_tetap_bisa_mengunduh(): void
    {
        $m = $this->materi();

        $this->actingAs($this->dosen)->get(route('dosen.materi.download', $m))->assertOk();
        $this->actingAs($this->admin)->get(route('dosen.materi.download', $m))->assertForbidden(); // route dosen tertutup untuk admin
    }

    public function test_unduh_materi_404_bila_berkas_hilang_dan_404_untuk_id_tidak_ada(): void
    {
        $m = $this->materi();
        Storage::disk('local')->delete($m->file_path);

        $this->actingAs($this->budi)->get(route('mahasiswa.materi.download', $m))->assertNotFound();
        $this->actingAs($this->budi)->get('/mahasiswa/materi/99999/unduh')->assertNotFound();
    }

    public function test_materi_tautan_diarahkan_ke_url_dan_skema_berbahaya_ditolak(): void
    {
        $aman = $this->materi();
        $aman->forceFill(['type' => 'link', 'file_path' => null, 'external_url' => 'https://laravel.com/docs'])->save();

        $bahaya = $this->materi();
        $bahaya->forceFill(['type' => 'link', 'file_path' => null, 'external_url' => 'javascript:alert(1)'])->save();

        $this->actingAs($this->budi)->get(route('mahasiswa.materi.download', $aman))->assertRedirect('https://laravel.com/docs');
        $this->actingAs($this->budi)->get(route('mahasiswa.materi.download', $bahaya))->assertNotFound();
    }

    // ---------------------------------------------------- tugas: lihat & akses

    public function test_mahasiswa_melihat_detail_tugas_published_dan_ditolak_untuk_draft_atau_non_peserta(): void
    {
        $this->actingAs($this->budi)->get(route('assignments.show', $this->tugas))
            ->assertOk()->assertSee('Tugas Terbuka')->assertSee('Kumpulkan');

        $this->actingAs($this->budi)->get(route('assignments.show', $this->draft))->assertForbidden();
        $this->actingAs($this->outsider)->get(route('assignments.show', $this->tugas))->assertForbidden();
    }

    public function test_form_ditutup_bila_tenggat_lewat_dan_tidak_boleh_terlambat(): void
    {
        $lewat = Assignment::factory()->pastDeadline()->create([
            'course_id' => $this->course->id, 'created_by' => $this->dosen->id, 'allow_late' => false,
        ]);

        $this->actingAs($this->budi)->get(route('assignments.show', $lewat))
            ->assertOk()->assertSee('tidak menerima pengumpulan terlambat')->assertDontSee('name="file"', false);

        $this->kumpul($lewat, $this->budi)->assertForbidden();
        $this->assertDatabaseMissing('submissions', ['assignment_id' => $lewat->id]);
    }

    // ------------------------------------------------------ tugas: mengumpulkan

    public function test_pengumpulan_berhasil_tersimpan_di_disk_privat(): void
    {
        $res = $this->kumpul($this->tugas, $this->budi, UploadedFile::fake()->create('laporan.pdf', 200, 'application/pdf'), ['note' => 'Selesai']);

        $sub = Submission::where('assignment_id', $this->tugas->id)->where('user_id', $this->budi->id)->firstOrFail();

        $res->assertRedirect(route('submissions.show', $sub))->assertSessionHas('success');
        $this->assertSame('laporan.pdf', $sub->original_name);
        $this->assertSame('Selesai', $sub->note);
        $this->assertFalse($sub->is_late);
        Storage::disk('local')->assertExists($sub->file_path);
        $this->assertStringStartsWith("submissions/{$this->tugas->id}/", $sub->file_path);
    }

    public function test_user_id_selalu_dari_sesi_bukan_dari_input(): void
    {
        $this->kumpul($this->tugas, $this->budi, null, ['user_id' => $this->outsider->id]);

        $this->assertDatabaseHas('submissions', ['assignment_id' => $this->tugas->id, 'user_id' => $this->budi->id]);
        $this->assertDatabaseMissing('submissions', ['user_id' => $this->outsider->id]);
    }

    public function test_pengumpulan_setelah_tenggat_ditandai_terlambat_bila_diizinkan(): void
    {
        $lewat = Assignment::factory()->pastDeadline()->create([
            'course_id' => $this->course->id, 'created_by' => $this->dosen->id, 'allow_late' => true,
        ]);

        $this->kumpul($lewat, $this->budi)->assertSessionHasNoErrors();

        $this->assertTrue(Submission::where('assignment_id', $lewat->id)->firstOrFail()->is_late);
    }

    public function test_validasi_berkas_menolak_format_ukuran_dan_kosong(): void
    {
        $this->kumpul($this->tugas, $this->budi, UploadedFile::fake()->create('virus.exe', 10, 'application/x-msdownload'))
            ->assertSessionHasErrors('file');
        // Isi teks biasa tetapi bernama .exe: lolos cek isi (dianggap txt), harus tetap ditolak lewat ekstensi.
        $tmp = tempnam(sys_get_temp_dir(), 'sub');
        file_put_contents($tmp, "MZ\n");
        $nyata = new UploadedFile($tmp, 'menyamar.exe', null, null, true); // berkas sungguhan: tipe dideteksi dari ISI
        $this->kumpul($this->tugas, $this->budi, $nyata)->assertSessionHasErrors('file');
        $this->kumpul($this->tugas, $this->budi, UploadedFile::fake()->create('besar.pdf', 11 * 1024, 'application/pdf'))
            ->assertSessionHasErrors('file');
        $this->actingAs($this->budi)->post(route('mahasiswa.assignments.submissions.store', $this->tugas), [])
            ->assertSessionHasErrors('file');

        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_pesan_error_validasi_tampil_di_halaman_tugas(): void
    {
        $this->actingAs($this->budi)
            ->from(route('assignments.show', $this->tugas))
            ->followingRedirects()
            ->post(route('mahasiswa.assignments.submissions.store', $this->tugas), [])
            ->assertSee('Pilih berkas jawaban yang akan dikumpulkan.');
    }

    public function test_kumpul_ulang_ditolak_dan_berkas_lama_tetap_utuh(): void
    {
        $this->kumpul($this->tugas, $this->budi, UploadedFile::fake()->create('v1.pdf', 50, 'application/pdf'));
        $asli = Submission::firstOrFail();

        // Pengumpulan tidak dapat diubah (SubmissionPolicy@create) -> 403.
        $this->kumpul($this->tugas, $this->budi, UploadedFile::fake()->create('v2.pdf', 50, 'application/pdf'))
            ->assertForbidden();

        $this->assertDatabaseCount('submissions', 1);
        $this->assertSame('v1.pdf', $asli->fresh()->original_name);
        Storage::disk('local')->assertExists($asli->file_path);
    }

    public function test_pengumpulan_yang_sudah_dinilai_tidak_bisa_diganti(): void
    {
        $this->kumpul($this->tugas, $this->budi);
        $sub = Submission::firstOrFail();
        Grade::create(['submission_id' => $sub->id, 'graded_by' => $this->dosen->id, 'score' => 90, 'feedback' => 'Bagus sekali', 'graded_at' => now()]);
        $pathAsli = $sub->file_path;

        $this->kumpul($this->tugas, $this->budi, UploadedFile::fake()->create('curang.pdf', 50, 'application/pdf'))
            ->assertForbidden();

        $this->assertSame($pathAsli, $sub->fresh()->file_path);

        $this->actingAs($this->budi)->get(route('assignments.show', $this->tugas))
            ->assertOk()->assertSee('Bagus sekali')->assertSee('90 / 100')->assertDontSee('name="file"', false);
    }

    public function test_dosen_dan_non_peserta_tidak_bisa_mengumpulkan(): void
    {
        $this->kumpul($this->tugas, $this->outsider)->assertForbidden();
        $this->kumpul($this->draft, $this->budi)->assertForbidden();
        $this->kumpul($this->tugas, $this->dosen)->assertForbidden();
        $this->assertDatabaseCount('submissions', 0);
    }

    // ------------------------------------------------ unduh berkas pengumpulan

    public function test_unduh_berkas_pengumpulan_hanya_untuk_pemilik_pengampu_dan_admin(): void
    {
        $this->kumpul($this->tugas, $this->budi, UploadedFile::fake()->createWithContent('jawab.txt', 'jawaban-budi'));
        $sub = Submission::firstOrFail();

        $res = $this->actingAs($this->budi)->get(route('submissions.download', $sub))->assertOk();
        $this->assertSame('jawaban-budi', $res->streamedContent());
        $this->assertStringContainsString('attachment', $res->headers->get('content-disposition'));

        $this->actingAs($this->dosen)->get(route('submissions.download', $sub))->assertOk();
        $this->actingAs($this->admin)->get(route('submissions.download', $sub))->assertOk();

        $this->actingAs($this->outsider)->get(route('submissions.download', $sub))->assertForbidden();
        $this->actingAs($this->dosenLain)->get(route('submissions.download', $sub))->assertForbidden();
        auth()->logout();
        $this->get(route('submissions.download', $sub))->assertRedirect(route('login'));
    }

    public function test_unduh_berkas_pengumpulan_404_bila_file_hilang(): void
    {
        $sub = Submission::factory()->create(['assignment_id' => $this->tugas->id, 'user_id' => $this->budi->id]);

        $this->actingAs($this->budi)->get(route('submissions.download', $sub))->assertNotFound();
    }

    // ----------------------------------------------------------- dashboard

    public function test_dashboard_menghitung_tugas_dari_data_nyata(): void
    {
        $kedua = Assignment::factory()->active()->create([
            'course_id' => $this->course->id, 'created_by' => $this->dosen->id, 'title' => 'Tugas Kedua',
        ]);
        Submission::factory()->create(['assignment_id' => $this->tugas->id, 'user_id' => $this->budi->id]);

        $this->actingAs($this->budi)->get(route('mahasiswa.dashboard'))
            ->assertOk()
            ->assertSee('50%')                    // 1 dari 2 tugas published (draft tidak dihitung)
            ->assertSee('Tugas Kedua')            // belum dikumpulkan: muncul
            ->assertDontSee('Tugas Terbuka')      // sudah dikumpulkan: tidak muncul
            ->assertDontSee('Rahasia Draft');
    }

    // --------------------------------------------- dosen: pertemuan pada materi

    public function test_dosen_dapat_menetapkan_pertemuan_saat_mengunggah_materi(): void
    {
        $this->actingAs($this->dosen)->post(route('dosen.materi.store'), [
            'course_id' => $this->course->id,
            'session'   => 5,
            'title'     => 'Modul Lima',
            'type'      => 'file',
            'file'      => UploadedFile::fake()->createWithContent('lima.pdf', "%PDF-1.4\n%%EOF"),
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('materials', ['title' => 'Modul Lima', 'session' => 5]);

        $this->actingAs($this->dosen)->post(route('dosen.materi.store'), [
            'course_id' => $this->course->id, 'session' => 99, 'title' => 'X', 'type' => 'link', 'external_url' => 'https://a.test',
        ])->assertSessionHasErrors('session');
    }
}
