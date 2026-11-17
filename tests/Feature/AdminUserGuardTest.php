<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * Pengaman CRUD pengguna oleh admin (UserPolicy + Form Request).
 * Setiap pengaman punya satu jalur ditolak dan satu jalur yang tetap boleh.
 */
class AdminUserGuardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    private function data(array $override = []): array
    {
        return array_merge([
            'name' => 'Pengguna Uji',
            'nim_nip' => 'UJI-001',
            'email' => 'uji@kampus.test',
            'role' => 'mahasiswa',
            'password' => 'rahasia123',
            'status' => 'aktif',
        ], $override);
    }

    private function dosenMengampu(int $jumlah = 1): User
    {
        $dosen = User::factory()->dosen()->create();
        Course::factory()->count($jumlah)->create(['lecturer_id' => $dosen->id]);

        return $dosen;
    }

    // ---------- Validasi: email wajib, sandi kuat ----------

    public function test_buat_pengguna_tanpa_email_ditolak_422_bukan_500(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/admin/pengguna', $this->data(['email' => null]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');

        $this->assertDatabaseMissing('users', ['nim_nip' => 'UJI-001']);
    }

    public function test_sandi_lemah_ditolak_dan_sandi_kuat_diterima(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/admin/pengguna', $this->data(['password' => 'abcdef']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');

        $this->actingAs($this->admin)
            ->postJson('/admin/pengguna', $this->data(['password' => 'abc12345']))
            ->assertCreated();
    }

    public function test_sandi_tersimpan_ter_hash_dan_role_diisi_eksplisit(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/admin/pengguna', $this->data(['role' => 'dosen']))
            ->assertCreated();

        $user = User::where('nim_nip', 'UJI-001')->firstOrFail();
        $this->assertSame('dosen', $user->role);
        $this->assertNotSame('rahasia123', $user->password);
        $this->assertTrue(password_verify('rahasia123', $user->password));
    }

    // ---------- Admin tidak boleh mematikan dirinya sendiri ----------

    public function test_admin_tidak_bisa_menghapus_diri_sendiri(): void
    {
        $this->actingAs($this->admin)
            ->deleteJson("/admin/pengguna/{$this->admin->id}")
            ->assertForbidden()
            ->assertJsonFragment(['message' => 'Anda tidak dapat menghapus akun Anda sendiri.']);

        $this->assertNotSoftDeleted($this->admin);
    }

    public function test_admin_tidak_bisa_menonaktifkan_atau_menurunkan_peran_diri_sendiri(): void
    {
        $base = $this->data([
            'name' => $this->admin->name, 'nim_nip' => $this->admin->nim_nip,
            'email' => $this->admin->email, 'password' => null,
        ]);

        $this->actingAs($this->admin)
            ->putJson("/admin/pengguna/{$this->admin->id}", $base + ['role' => 'admin', 'status' => 'nonaktif'])
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->putJson("/admin/pengguna/{$this->admin->id}", array_merge($base, ['role' => 'mahasiswa', 'status' => 'aktif']))
            ->assertForbidden();

        $this->assertNotSoftDeleted($this->admin);
        $this->assertSame('admin', $this->admin->fresh()->role);
    }

    public function test_admin_boleh_menghapus_admin_lain_selama_masih_ada_admin_aktif(): void
    {
        $lain = User::factory()->admin()->create();

        $this->actingAs($this->admin)
            ->deleteJson("/admin/pengguna/{$lain->id}")
            ->assertOk();

        $this->assertSoftDeleted($lain);
    }

    public function test_policy_menolak_menghapus_admin_aktif_terakhir_dengan_409(): void
    {
        // Aktor yang sudah nonaktif; $this->admin tinggal satu-satunya admin aktif.
        $aktor = User::factory()->admin()->create();
        $aktor->delete();

        $hasil = Gate::forUser($aktor)->inspect('delete', $this->admin);

        $this->assertTrue($hasil->denied());
        $this->assertSame(409, $hasil->status());
    }

    // ---------- Dosen yang masih mengampu MK dilindungi (restrictOnDelete) ----------

    public function test_dosen_yang_masih_mengampu_mk_tidak_bisa_dihapus(): void
    {
        $dosen = $this->dosenMengampu(2);

        $this->actingAs($this->admin)
            ->deleteJson("/admin/pengguna/{$dosen->id}")
            ->assertStatus(409)
            ->assertJsonFragment(['message' => 'Tidak dapat menghapus dosen yang masih mengampu 2 mata kuliah. Pindahkan pengampunya terlebih dahulu.']);

        $this->assertNotSoftDeleted($dosen);
        $this->assertSame(2, Course::where('lecturer_id', $dosen->id)->count());
    }

    public function test_dosen_tanpa_mk_boleh_dihapus(): void
    {
        $dosen = User::factory()->dosen()->create();

        $this->actingAs($this->admin)
            ->deleteJson("/admin/pengguna/{$dosen->id}")
            ->assertOk();

        $this->assertSoftDeleted($dosen);
    }

    public function test_menonaktifkan_dosen_yang_mengampu_mk_ditolak(): void
    {
        $dosen = $this->dosenMengampu();

        $this->actingAs($this->admin)
            ->putJson("/admin/pengguna/{$dosen->id}", $this->data([
                'name' => $dosen->name, 'nim_nip' => $dosen->nim_nip, 'email' => $dosen->email,
                'password' => null, 'role' => 'dosen', 'status' => 'nonaktif',
            ]))
            ->assertStatus(409);

        $this->assertNotSoftDeleted($dosen);
    }

    public function test_mengubah_peran_dosen_yang_mengampu_mk_ditolak(): void
    {
        $dosen = $this->dosenMengampu();

        $this->actingAs($this->admin)
            ->putJson("/admin/pengguna/{$dosen->id}", $this->data([
                'name' => $dosen->name, 'nim_nip' => $dosen->nim_nip, 'email' => $dosen->email,
                'password' => null, 'role' => 'mahasiswa', 'status' => 'aktif',
            ]))
            ->assertStatus(409);

        $this->assertSame('dosen', $dosen->fresh()->role);
    }

    public function test_mengedit_data_biasa_dosen_pengampu_tetap_boleh(): void
    {
        $dosen = $this->dosenMengampu();

        $this->actingAs($this->admin)
            ->putJson("/admin/pengguna/{$dosen->id}", $this->data([
                'name' => 'Nama Baru', 'nim_nip' => $dosen->nim_nip, 'email' => $dosen->email,
                'password' => null, 'role' => 'dosen', 'status' => 'aktif',
            ]))
            ->assertOk();

        $this->assertSame('Nama Baru', $dosen->fresh()->name);
    }

    // ---------- Non-admin ditolak di semua pintu ----------

    public function test_non_admin_tidak_bisa_mengelola_pengguna(): void
    {
        $mhs = User::factory()->mahasiswa()->create();
        $target = User::factory()->mahasiswa()->create();

        $this->actingAs($mhs)->postJson('/admin/pengguna', $this->data())->assertForbidden();
        $this->actingAs($mhs)->putJson("/admin/pengguna/{$target->id}", $this->data(['role' => 'admin']))->assertForbidden();
        $this->actingAs($mhs)->deleteJson("/admin/pengguna/{$target->id}")->assertForbidden();

        $this->assertSame('mahasiswa', $target->fresh()->role);
        $this->assertNotSoftDeleted($target);
    }
}
