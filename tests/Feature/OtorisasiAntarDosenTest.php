<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Menutup celah yang ditemukan lewat uji mutasi (Minggu 14, "BREAK" otomatis).
 *
 * Latar belakang: pemeriksaan Gate::authorize() di controller dosen untuk
 * materi, kelola mahasiswa, rekap nilai, dan presensi bisa DIHAPUS tanpa
 * membuat satu pun tes merah. Rute-rute itu hanya dilindungi middleware
 * role:dosen, sehingga pemisahan antar-dosen (dosen A vs dosen B) sepenuhnya
 * bergantung pada Gate. Tanpa tes di bawah ini, bolong IDOR tidak terdeteksi.
 *
 * Pola tiap tes: (1) dosen lain ditolak 403, (2) data TIDAK berubah, dan
 * (3) dosen pengampu tetap bisa, sebagai kontrol positif agar tes tidak
 * hijau hanya karena semua request kebetulan ditolak.
 */
class OtorisasiAntarDosenTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $dosen;        // pengampu $course
    private User $dosenLain;    // pengampu $courseLain, BUKAN pengampu $course
    private User $mhs;          // terdaftar di $course
    private User $outsider;     // tidak terdaftar di mana pun
    private Course $course;
    private Course $courseLain;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('local');

        $this->admin     = User::factory()->admin()->create();
        $this->dosen     = User::factory()->dosen()->create();
        $this->dosenLain = User::factory()->dosen()->create();
        $this->mhs       = User::factory()->mahasiswa()->create();
        $this->outsider  = User::factory()->mahasiswa()->create();

        $this->course     = Course::factory()->create(['lecturer_id' => $this->dosen->id, 'status' => 'active']);
        $this->courseLain = Course::factory()->create(['lecturer_id' => $this->dosenLain->id, 'status' => 'active']);
        $this->course->students()->attach($this->mhs->id, ['enrolled_at' => now()]);
    }

    private function buatSesi(array $override = []): AttendanceSession
    {
        return AttendanceSession::create(array_merge([
            'course_id'  => $this->course->id,
            'created_by' => $this->dosen->id,
            'title'      => 'Pertemuan Uji',
            'code'       => 'UJI001',
            'start_time' => now(),
            'expires_at' => now()->addMinutes(30),
            'is_active'  => true,
        ], $override));
    }

    // ================================================================ MATERI

    public function test_dosen_lain_tidak_bisa_mengunggah_materi_ke_mata_kuliah_dosen_lain(): void
    {
        $this->actingAs($this->dosenLain)->post(route('dosen.materi.store'), [
            'course_id'    => $this->course->id,
            'title'        => 'Materi Sisipan',
            'type'         => 'link',
            'external_url' => 'https://contoh.test/materi',
        ])->assertForbidden();

        $this->assertDatabaseMissing('materials', ['title' => 'Materi Sisipan']);
    }

    public function test_dosen_pengampu_bisa_mengunggah_materi_tautan(): void
    {
        $this->actingAs($this->dosen)->post(route('dosen.materi.store'), [
            'course_id'    => $this->course->id,
            'title'        => 'Materi Resmi',
            'type'         => 'link',
            'external_url' => 'https://contoh.test/materi',
        ])->assertRedirect(route('dosen.materi'));

        $this->assertDatabaseHas('materials', [
            'title' => 'Materi Resmi', 'course_id' => $this->course->id, 'uploaded_by' => $this->dosen->id,
        ]);
    }

    public function test_dosen_lain_tidak_bisa_mengubah_materi_dan_judul_tidak_berubah(): void
    {
        $materi = Material::factory()->link()->create([
            'course_id' => $this->course->id, 'uploaded_by' => $this->dosen->id, 'title' => 'Judul Asli',
        ]);

        $this->actingAs($this->dosenLain)->put(route('dosen.materi.update', $materi), [
            'title' => 'Diretas', 'external_url' => 'https://jahat.test',
        ])->assertForbidden();

        $this->assertDatabaseHas('materials', ['id' => $materi->id, 'title' => 'Judul Asli']);
        $this->assertDatabaseMissing('materials', ['title' => 'Diretas']);
    }

    public function test_dosen_pengampu_bisa_mengubah_materi(): void
    {
        $materi = Material::factory()->link()->create([
            'course_id' => $this->course->id, 'uploaded_by' => $this->dosen->id, 'title' => 'Judul Asli',
        ]);

        $this->actingAs($this->dosen)->put(route('dosen.materi.update', $materi), [
            'title' => 'Judul Baru', 'external_url' => 'https://contoh.test/baru',
        ])->assertRedirect(route('dosen.materi'));

        $this->assertDatabaseHas('materials', ['id' => $materi->id, 'title' => 'Judul Baru']);
    }

    public function test_dosen_lain_tidak_bisa_menghapus_materi_dan_berkas_tetap_ada(): void
    {
        Storage::disk('local')->put('materials/uji/rahasia.pdf', 'isi');
        $materi = Material::factory()->create([
            'course_id' => $this->course->id, 'uploaded_by' => $this->dosen->id,
            'file_path' => 'materials/uji/rahasia.pdf',
        ]);

        $this->actingAs($this->dosenLain)->delete(route('dosen.materi.destroy', $materi))->assertForbidden();

        $this->assertDatabaseHas('materials', ['id' => $materi->id]);
        Storage::disk('local')->assertExists('materials/uji/rahasia.pdf');
    }

    public function test_dosen_pengampu_bisa_menghapus_materi_beserta_berkasnya(): void
    {
        Storage::disk('local')->put('materials/uji/hapus.pdf', 'isi');
        $materi = Material::factory()->create([
            'course_id' => $this->course->id, 'uploaded_by' => $this->dosen->id,
            'file_path' => 'materials/uji/hapus.pdf',
        ]);

        $this->actingAs($this->dosen)->delete(route('dosen.materi.destroy', $materi))->assertRedirect(route('dosen.materi'));

        $this->assertDatabaseMissing('materials', ['id' => $materi->id]);
        Storage::disk('local')->assertMissing('materials/uji/hapus.pdf');
    }

    // ================================================== KELOLA MAHASISWA (baris 10 keamanan.md)

    public function test_dosen_lain_tidak_bisa_mendaftarkan_mahasiswa_ke_mata_kuliah_dosen_lain(): void
    {
        $this->actingAs($this->dosenLain)->post(route('dosen.mahasiswa.store'), [
            'course_id' => $this->course->id, 'nim' => $this->outsider->nim_nip,
        ])->assertForbidden();

        $this->assertDatabaseMissing('course_user', [
            'course_id' => $this->course->id, 'user_id' => $this->outsider->id,
        ]);
    }

    public function test_dosen_pengampu_bisa_mendaftarkan_mahasiswa(): void
    {
        $this->actingAs($this->dosen)->post(route('dosen.mahasiswa.store'), [
            'course_id' => $this->course->id, 'nim' => $this->outsider->nim_nip,
        ])->assertRedirect();

        $this->assertDatabaseHas('course_user', [
            'course_id' => $this->course->id, 'user_id' => $this->outsider->id,
        ]);
    }

    public function test_dosen_lain_tidak_bisa_mengeluarkan_mahasiswa_dari_kelas_dosen_lain(): void
    {
        $this->actingAs($this->dosenLain)
            ->delete(route('dosen.mahasiswa.destroy', [$this->course, $this->mhs]))
            ->assertForbidden();

        $this->assertDatabaseHas('course_user', [
            'course_id' => $this->course->id, 'user_id' => $this->mhs->id,
        ]);
    }

    public function test_dosen_pengampu_bisa_mengeluarkan_mahasiswa_dari_kelasnya(): void
    {
        $this->actingAs($this->dosen)
            ->delete(route('dosen.mahasiswa.destroy', [$this->course, $this->mhs]))
            ->assertRedirect();

        $this->assertDatabaseMissing('course_user', [
            'course_id' => $this->course->id, 'user_id' => $this->mhs->id,
        ]);
    }

    // ================================================================ REKAP NILAI

    public function test_dosen_lain_tidak_bisa_membuka_rekap_nilai_mata_kuliah_dosen_lain(): void
    {
        $this->actingAs($this->dosenLain)->get(route('dosen.rekap.show', $this->course))->assertForbidden();
    }

    public function test_dosen_pengampu_bisa_membuka_rekap_nilai_dan_melihat_mahasiswanya(): void
    {
        // Baris mahasiswa di rekap hanya tampil bila ada tugas yang sudah terbit.
        Assignment::factory()->active()->create(['course_id' => $this->course->id, 'created_by' => $this->dosen->id]);

        $this->actingAs($this->dosen)->get(route('dosen.rekap.show', $this->course))
            ->assertOk()
            ->assertSee($this->course->code)
            ->assertSee($this->mhs->name);
    }

    // ================================================================ PRESENSI DOSEN

    public function test_dosen_lain_tidak_bisa_melihat_detail_sesi_presensi_dosen_lain(): void
    {
        $sesi = $this->buatSesi();

        $this->actingAs($this->dosenLain)->get(route('dosen.presensi.show', $sesi))->assertForbidden();
    }

    public function test_dosen_pengampu_bisa_melihat_detail_sesi_presensi_dengan_kodenya(): void
    {
        $sesi = $this->buatSesi(['code' => 'KODE77']);

        $this->actingAs($this->dosen)->get(route('dosen.presensi.show', $sesi))
            ->assertOk()
            ->assertSee('KODE77');
    }

    public function test_dosen_lain_tidak_bisa_menutup_sesi_presensi_dosen_lain(): void
    {
        $sesi = $this->buatSesi();

        $this->actingAs($this->dosenLain)->post(route('dosen.presensi.toggle', $sesi))->assertForbidden();

        $this->assertTrue($sesi->fresh()->is_active, 'Sesi harus tetap aktif.');
    }

    public function test_dosen_lain_tidak_bisa_memperpanjang_sesi_presensi_dosen_lain(): void
    {
        $sesi = $this->buatSesi();
        $awal = $sesi->expires_at->timestamp;

        $this->actingAs($this->dosenLain)->post(route('dosen.presensi.extend', $sesi), ['minutes' => 15])
            ->assertForbidden();

        $this->assertSame($awal, $sesi->fresh()->expires_at->timestamp, 'Masa berlaku tidak boleh berubah.');
    }

    public function test_dosen_lain_tidak_bisa_mengubah_status_kehadiran_di_sesi_dosen_lain(): void
    {
        $sesi = $this->buatSesi();

        $this->actingAs($this->dosenLain)->post(route('dosen.presensi.manual', $sesi), [
            'user_id' => $this->mhs->id, 'status' => 'hadir',
        ])->assertForbidden();

        $this->assertDatabaseMissing('attendances', [
            'attendance_session_id' => $sesi->id, 'user_id' => $this->mhs->id,
        ]);
    }

    public function test_dosen_lain_tidak_bisa_menghapus_sesi_presensi_dosen_lain(): void
    {
        $sesi = $this->buatSesi();

        $this->actingAs($this->dosenLain)->delete(route('dosen.presensi.destroy', $sesi))->assertForbidden();

        $this->assertDatabaseHas('attendance_sessions', ['id' => $sesi->id]);
    }

    public function test_dosen_pengampu_bisa_menghapus_sesi_presensinya(): void
    {
        $sesi = $this->buatSesi();

        $this->actingAs($this->dosen)->delete(route('dosen.presensi.destroy', $sesi))->assertRedirect();

        $this->assertDatabaseMissing('attendance_sessions', ['id' => $sesi->id]);
    }

    // ================================================================ PRESENSI MAHASISWA

    public function test_mahasiswa_tidak_terdaftar_ditolak_403_saat_presensi_di_halaman_mata_kuliah_orang_lain(): void
    {
        $sesi = $this->buatSesi(['code' => 'MASUK1']);

        $this->actingAs($this->outsider)
            ->post(route('mahasiswa.mata-kuliah.presensi.store', $this->course), ['code' => 'MASUK1'])
            ->assertForbidden();

        $this->assertDatabaseMissing('attendances', [
            'attendance_session_id' => $sesi->id, 'user_id' => $this->outsider->id,
        ]);
    }

    // ================================================================ ADMIN: hapus MK

    public function test_admin_tidak_bisa_menghapus_mata_kuliah_yang_masih_punya_data_terkait(): void
    {
        // $this->course punya mahasiswa terdaftar; tambah materi supaya jelas "punya data".
        Material::factory()->link()->create(['course_id' => $this->course->id, 'uploaded_by' => $this->dosen->id]);

        $this->actingAs($this->admin)->delete(route('admin.matkul.destroy', $this->course));

        $this->assertDatabaseHas('courses', ['id' => $this->course->id]);
        $this->assertDatabaseHas('materials', ['course_id' => $this->course->id]);
    }

    public function test_admin_bisa_menghapus_mata_kuliah_kosong(): void
    {
        $kosong = Course::factory()->create(['lecturer_id' => $this->dosen->id, 'status' => 'draft']);

        $this->actingAs($this->admin)->delete(route('admin.matkul.destroy', $kosong))->assertRedirect();

        $this->assertDatabaseMissing('courses', ['id' => $kosong->id]);
    }
}
