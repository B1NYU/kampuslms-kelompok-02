<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private User $dosen;
    private User $dosenLain;
    private User $mahasiswaEnrolled;
    private User $mahasiswaOutsider;
    private Course $course;
    private Course $courseLain;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->dosen = User::factory()->dosen()->create();
        $this->dosenLain = User::factory()->dosen()->create();

        $this->mahasiswaEnrolled = User::factory()->mahasiswa()->create();
        $this->mahasiswaOutsider = User::factory()->mahasiswa()->create();

        $this->course = Course::factory()->create([
            'lecturer_id' => $this->dosen->id,
            'status'      => 'active',
        ]);

        $this->courseLain = Course::factory()->create([
            'lecturer_id' => $this->dosenLain->id,
            'status'      => 'active',
        ]);

        $this->course->students()->attach($this->mahasiswaEnrolled->id, ['enrolled_at' => now()]);
    }

    public function test_dosen_bisa_membuka_halaman_presensi(): void
    {
        $response = $this->actingAs($this->dosen)->get(route('dosen.presensi.index'));
        $response->assertOk();
        $response->assertSee('Presensi Perkuliahan Berbasis Kode Unik');
    }

    public function test_dosen_bisa_membuat_sesi_presensi_dengan_kode_unik_dan_masa_berlaku(): void
    {
        $response = $this->actingAs($this->dosen)->post(route('dosen.presensi.store'), [
            'course_id'        => $this->course->id,
            'title'            => 'Pertemuan 1 - Pengantar Basis Data',
            'code'             => 'TEST01',
            'duration_minutes' => 30,
            'description'      => 'Silakan isi presensi dalam 30 menit perkuliahan.',
        ]);

        $this->assertDatabaseHas('attendance_sessions', [
            'course_id' => $this->course->id,
            'title'     => 'Pertemuan 1 - Pengantar Basis Data',
            'code'      => 'TEST01',
            'is_active' => true,
        ]);

        $session = AttendanceSession::where('code', 'TEST01')->first();
        $this->assertNotNull($session);
        $this->assertTrue($session->isOpen());
        $this->assertFalse($session->isExpired());

        $response->assertRedirect(route('dosen.presensi.show', $session));
    }

    public function test_dosen_tidak_bisa_membuat_sesi_di_mata_kuliah_dosen_lain(): void
    {
        $response = $this->actingAs($this->dosen)->post(route('dosen.presensi.store'), [
            'course_id'        => $this->courseLain->id,
            'title'            => 'Percobaan Ilegal',
            'duration_minutes' => 30,
        ]);

        $response->assertForbidden();
    }

    public function test_mahasiswa_terdaftar_bisa_presensi_dengan_kode_unik_yang_masih_berlaku(): void
    {
        $session = AttendanceSession::create([
            'course_id'   => $this->course->id,
            'created_by'  => $this->dosen->id,
            'title'       => 'Pertemuan 2',
            'code'        => 'HADIR9',
            'start_time'  => now(),
            'expires_at'  => now()->addMinutes(20),
            'is_active'   => true,
        ]);

        $response = $this->actingAs($this->mahasiswaEnrolled)->post(route('mahasiswa.presensi.store'), [
            'code' => 'hadir9', // case-insensitive input
        ]);

        $response->assertRedirect(route('mahasiswa.presensi.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'attendance_session_id' => $session->id,
            'user_id'               => $this->mahasiswaEnrolled->id,
            'status'                => 'hadir',
        ]);
    }

    public function test_mahasiswa_tidak_bisa_presensi_jika_masa_berlaku_sudah_habis(): void
    {
        $session = AttendanceSession::create([
            'course_id'   => $this->course->id,
            'created_by'  => $this->dosen->id,
            'title'       => 'Pertemuan Selesai',
            'code'        => 'EXPIRE1',
            'start_time'  => now()->subHours(2),
            'expires_at'  => now()->subMinutes(10), // sudah lewat masa berlaku
            'is_active'   => true,
        ]);

        $response = $this->actingAs($this->mahasiswaEnrolled)->post(route('mahasiswa.presensi.store'), [
            'code' => 'EXPIRE1',
        ]);

        $response->assertSessionHasErrors('code');

        $this->assertDatabaseMissing('attendances', [
            'attendance_session_id' => $session->id,
            'user_id'               => $this->mahasiswaEnrolled->id,
        ]);
    }

    public function test_mahasiswa_tidak_bisa_presensi_jika_sesi_ditutup_manual_oleh_dosen(): void
    {
        $session = AttendanceSession::create([
            'course_id'   => $this->course->id,
            'created_by'  => $this->dosen->id,
            'title'       => 'Sesi Ditutup',
            'code'        => 'CLOSED2',
            'start_time'  => now(),
            'expires_at'  => now()->addMinutes(30),
            'is_active'   => false, // ditutup dosen
        ]);

        $response = $this->actingAs($this->mahasiswaEnrolled)->post(route('mahasiswa.presensi.store'), [
            'code' => 'CLOSED2',
        ]);

        $response->assertSessionHasErrors('code');

        $this->assertDatabaseMissing('attendances', [
            'attendance_session_id' => $session->id,
            'user_id'               => $this->mahasiswaEnrolled->id,
        ]);
    }

    public function test_mahasiswa_tidak_bisa_presensi_dua_kali_pada_sesi_yang_sama(): void
    {
        $session = AttendanceSession::create([
            'course_id'   => $this->course->id,
            'created_by'  => $this->dosen->id,
            'title'       => 'Pertemuan Dobel',
            'code'        => 'DOBEL1',
            'start_time'  => now(),
            'expires_at'  => now()->addMinutes(30),
            'is_active'   => true,
        ]);

        Attendance::create([
            'attendance_session_id' => $session->id,
            'user_id'               => $this->mahasiswaEnrolled->id,
            'status'                => 'hadir',
            'attended_at'           => now(),
        ]);

        $response = $this->actingAs($this->mahasiswaEnrolled)->post(route('mahasiswa.presensi.store'), [
            'code' => 'DOBEL1',
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertEquals(1, Attendance::where('attendance_session_id', $session->id)->count());
    }

    public function test_mahasiswa_yang_tidak_terdaftar_tidak_bisa_presensi(): void
    {
        $session = AttendanceSession::create([
            'course_id'   => $this->course->id,
            'created_by'  => $this->dosen->id,
            'title'       => 'Pertemuan Khusus',
            'code'        => 'OUTSIDE',
            'start_time'  => now(),
            'expires_at'  => now()->addMinutes(30),
            'is_active'   => true,
        ]);

        $response = $this->actingAs($this->mahasiswaOutsider)->post(route('mahasiswa.presensi.store'), [
            'code' => 'OUTSIDE',
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertDatabaseMissing('attendances', [
            'attendance_session_id' => $session->id,
            'user_id'               => $this->mahasiswaOutsider->id,
        ]);
    }

    public function test_dosen_bisa_menutup_dan_membuka_kembali_sesi_presensi(): void
    {
        $session = AttendanceSession::create([
            'course_id'   => $this->course->id,
            'created_by'  => $this->dosen->id,
            'title'       => 'Pertemuan Toggle',
            'code'        => 'TOGGLE1',
            'start_time'  => now(),
            'expires_at'  => now()->addMinutes(30),
            'is_active'   => true,
        ]);

        $this->actingAs($this->dosen)->post(route('dosen.presensi.toggle', $session));
        $this->assertFalse($session->fresh()->is_active);

        $this->actingAs($this->dosen)->post(route('dosen.presensi.toggle', $session));
        $this->assertTrue($session->fresh()->is_active);
    }

    public function test_dosen_bisa_memperpanjang_masa_berlaku_sesi(): void
    {
        $session = AttendanceSession::create([
            'course_id'   => $this->course->id,
            'created_by'  => $this->dosen->id,
            'title'       => 'Pertemuan Extend',
            'code'        => 'EXTEND1',
            'start_time'  => now(),
            'expires_at'  => now()->addMinutes(10),
            'is_active'   => true,
        ]);

        $origExpires = $session->expires_at->timestamp;

        $this->actingAs($this->dosen)->post(route('dosen.presensi.extend', $session), [
            'minutes' => 15,
        ]);

        $newExpires = $session->fresh()->expires_at->timestamp;
        $this->assertGreaterThan($origExpires, $newExpires);
    }

    public function test_dosen_bisa_mengubah_status_kehadiran_mahasiswa_secara_manual(): void
    {
        $session = AttendanceSession::create([
            'course_id'   => $this->course->id,
            'created_by'  => $this->dosen->id,
            'title'       => 'Pertemuan Manual',
            'code'        => 'MANUAL1',
            'start_time'  => now(),
            'expires_at'  => now()->addMinutes(30),
            'is_active'   => true,
        ]);

        $this->actingAs($this->dosen)->post(route('dosen.presensi.manual', $session), [
            'user_id' => $this->mahasiswaEnrolled->id,
            'status'  => 'izin',
            'notes'   => 'Izin ada keperluan keluarga',
        ]);

        $this->assertDatabaseHas('attendances', [
            'attendance_session_id' => $session->id,
            'user_id'               => $this->mahasiswaEnrolled->id,
            'status'                => 'izin',
            'notes'                 => 'Izin ada keperluan keluarga',
        ]);
    }

    public function test_mahasiswa_bisa_presensi_langsung_pada_halaman_mata_kuliah_yang_dibuka(): void
    {
        $session = AttendanceSession::create([
            'course_id'   => $this->course->id,
            'created_by'  => $this->dosen->id,
            'title'       => 'Pertemuan Halaman MK',
            'code'        => 'MKVALID1',
            'start_time'  => now(),
            'expires_at'  => now()->addMinutes(25),
            'is_active'   => true,
        ]);

        $response = $this->actingAs($this->mahasiswaEnrolled)
            ->post(route('mahasiswa.mata-kuliah.presensi.store', $this->course), [
                'code' => 'MKVALID1',
            ]);

        $response->assertRedirect(route('mahasiswa.mata-kuliah.show', ['mata_kuliah' => $this->course, 'tab' => 'presensi']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'attendance_session_id' => $session->id,
            'user_id'               => $this->mahasiswaEnrolled->id,
            'status'                => 'hadir',
        ]);
    }

    public function test_mahasiswa_tidak_bisa_presensi_mata_kuliah_lain_pada_halaman_mata_kuliah_tersebut(): void
    {
        // Enrol mahasiswa ke courseLain juga
        $this->courseLain->students()->attach($this->mahasiswaEnrolled->id, ['enrolled_at' => now()]);

        // Buat sesi di courseLain
        $sessionLain = AttendanceSession::create([
            'course_id'   => $this->courseLain->id,
            'created_by'  => $this->dosenLain->id,
            'title'       => 'Pertemuan Matkul Lain',
            'code'        => 'OTHERMK',
            'start_time'  => now(),
            'expires_at'  => now()->addMinutes(25),
            'is_active'   => true,
        ]);

        // Mahasiswa mencoba memasukkan kode OTHERMK saat berada di halaman $this->course!
        $response = $this->actingAs($this->mahasiswaEnrolled)
            ->post(route('mahasiswa.mata-kuliah.presensi.store', $this->course), [
                'code' => 'OTHERMK',
            ]);

        // Harus ditolak dengan error 'code'
        $response->assertRedirect(route('mahasiswa.mata-kuliah.show', ['mata_kuliah' => $this->course, 'tab' => 'presensi']));
        $response->assertSessionHasErrors('code');

        // Pastikan tidak ada data kehadiran yang tersimpan di sesi courseLain
        $this->assertDatabaseMissing('attendances', [
            'attendance_session_id' => $sessionLain->id,
            'user_id'               => $this->mahasiswaEnrolled->id,
        ]);
    }

    public function test_halaman_mata_kuliah_menampilkan_tab_presensi(): void
    {
        $response = $this->actingAs($this->mahasiswaEnrolled)
            ->get(route('mahasiswa.mata-kuliah.show', ['mata_kuliah' => $this->course, 'tab' => 'presensi']));

        $response->assertOk();
        $response->assertSee('Presensi Kehadiran Kelas');
        $response->assertSee($this->course->code);
    }
}
