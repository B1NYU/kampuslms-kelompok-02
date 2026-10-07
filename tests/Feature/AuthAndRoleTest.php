<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_login_berhasil_mengarahkan_sesuai_peran_di_database(): void
    {
        User::factory()->dosen()->create(['nim_nip' => 'NIP-1', 'password' => 'rahasia123']);

        // Field "role" dari browser diabaikan; peran diambil dari database.
        $this->post('/login', ['identifier' => 'NIP-1', 'password' => 'rahasia123', 'role' => 'admin'])
            ->assertRedirect(route('dosen.dashboard'));
    }

    public function test_login_gagal_dengan_password_salah(): void
    {
        User::factory()->mahasiswa()->create(['nim_nip' => '10240001', 'password' => 'benar']);

        $this->post('/login', ['identifier' => '10240001', 'password' => 'salah'])
            ->assertSessionHasErrors('identifier');
        $this->assertGuest();
    }

    public function test_login_palsu_lewat_session_tidak_lagi_berlaku(): void
    {
        $this->withSession(['user_role' => 'admin', 'user_name' => 'Siapa saja'])
            ->get('/admin/pengguna')
            ->assertRedirect(route('login'));
    }

    public function test_tamu_diarahkan_ke_login_dan_request_json_mendapat_401(): void
    {
        $this->get('/mahasiswa/dashboard')->assertRedirect(route('login'));
        $this->getJson('/admin/pengguna')->assertStatus(401);
    }

    public function test_peran_salah_mendapat_403(): void
    {
        $mhs = User::factory()->mahasiswa()->create();
        $dosen = User::factory()->dosen()->create();

        $this->actingAs($mhs)->get('/admin/pengguna')->assertForbidden();
        $this->actingAs($mhs)->get('/dosen/tugas')->assertForbidden();
        $this->actingAs($dosen)->get('/admin/pengguna')->assertForbidden();
        $this->actingAs($dosen)->get('/mahasiswa/dashboard')->assertForbidden();
    }

    public function test_mahasiswa_tidak_bisa_mengubah_role_lewat_endpoint_admin(): void
    {
        $mhs = User::factory()->mahasiswa()->create();

        $this->actingAs($mhs)
            ->putJson(route('admin.pengguna.update', $mhs), [
                'name' => $mhs->name, 'nim_nip' => $mhs->nim_nip, 'role' => 'admin', 'status' => 'aktif',
            ])->assertForbidden();

        $this->assertSame('mahasiswa', $mhs->fresh()->role);
    }

    public function test_logout_hanya_lewat_post(): void
    {
        $mhs = User::factory()->mahasiswa()->create();

        $this->actingAs($mhs)->get('/logout')->assertStatus(405);
        $this->actingAs($mhs)->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }
}
