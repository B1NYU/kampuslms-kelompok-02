<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetKataSandi;
use App\Support\KataSandi;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * Lupa kata sandi (tamu, lewat email) dan ubah kata sandi (sudah login)
 * untuk dosen dan mahasiswa.
 */
class KataSandiTest extends TestCase
{
    use RefreshDatabase;

    private const LAMA = 'passwordlama1';
    private const BARU = 'KataBaru2026';

    private User $mhs;
    private User $dosen;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->mhs   = User::factory()->mahasiswa()->create(['password' => self::LAMA, 'nim_nip' => '10240001']);
        $this->dosen = User::factory()->dosen()->create(['password' => self::LAMA, 'nim_nip' => 'NIP-777']);
        $this->admin = User::factory()->admin()->create(['password' => self::LAMA]);
    }

    private function token(User $u): string
    {
        return Password::broker()->createToken($u);
    }

    private function reset(User $u, string $token, string $baru = self::BARU, ?string $konfirmasi = null)
    {
        return $this->post(route('password.update'), [
            'token' => $token, 'email' => $u->email,
            'password' => $baru, 'password_confirmation' => $konfirmasi ?? $baru,
        ]);
    }

    // =========================================================== lupa sandi: minta tautan

    public function test_halaman_login_menautkan_ke_lupa_sandi_dan_halamannya_terbuka(): void
    {
        $this->get('/')->assertOk()->assertSee('Lupa kata sandi?')->assertSee(route('password.request'));
        $this->get(route('password.request'))->assertOk()->assertSee('Lupa Kata Sandi?');
    }

    public function test_pengguna_yang_sudah_login_dialihkan_dari_halaman_lupa_sandi(): void
    {
        $this->actingAs($this->mhs)->get(route('password.request'))->assertRedirect();
    }

    public function test_tautan_dikirim_untuk_mahasiswa_dan_dosen_lewat_email_maupun_nim_nip(): void
    {
        Notification::fake();

        $this->post(route('password.email'), ['identifier' => $this->mhs->email])->assertSessionHas('status');
        $this->post(route('password.email'), ['identifier' => 'NIP-777'])->assertSessionHas('status');

        Notification::assertSentTo($this->mhs, ResetKataSandi::class);
        Notification::assertSentTo($this->dosen, ResetKataSandi::class);
    }

    public function test_email_berbahasa_indonesia_dan_tautannya_memuat_token(): void
    {
        Notification::fake();
        $this->post(route('password.email'), ['identifier' => $this->mhs->email]);

        Notification::assertSentTo($this->mhs, ResetKataSandi::class, function (ResetKataSandi $n) {
            $mail = $n->toMail($this->mhs);

            $this->assertSame('Atur Ulang Kata Sandi KampusLMS', $mail->subject);
            $this->assertSame('Atur Ulang Kata Sandi', $mail->actionText);
            $this->assertStringContainsString('/reset-sandi/' . $n->token, $mail->actionUrl);
            $this->assertStringContainsString(urlencode($this->mhs->email), $mail->actionUrl);

            return true;
        });
    }

    public function test_admin_tidak_bisa_meminta_reset_mandiri(): void
    {
        Notification::fake();

        $this->post(route('password.email'), ['identifier' => $this->admin->email])->assertSessionHas('status');

        Notification::assertNothingSent();
    }

    public function test_jawaban_sama_untuk_akun_ada_tidak_ada_admin_dan_nonaktif(): void
    {
        Notification::fake();

        $nonaktif = User::factory()->mahasiswa()->create(['nim_nip' => '10249999']);
        $nonaktif->delete();

        $jawaban = collect([$this->mhs->email, 'tidak-ada@kampus.test', '99999999', $this->admin->email, '10249999'])
            ->map(function (string $id) {
                $res = $this->post(route('password.email'), ['identifier' => $id]);
                $res->assertRedirect()->assertSessionHasNoErrors();

                return session('status');
            });

        // Satu-satunya perbedaan yang boleh ada hanya di email terkirim, bukan di jawaban ke pengguna.
        $this->assertCount(1, $jawaban->unique());
        Notification::assertSentTimes(ResetKataSandi::class, 1);
    }

    public function test_kegagalan_kirim_email_tidak_membocorkan_keberadaan_akun(): void
    {
        // Pengirim email dibuat gagal; jawaban ke pengguna harus tetap sama (bukan 500).
        config(['mail.default' => 'smtp', 'mail.mailers.smtp.host' => '127.0.0.1', 'mail.mailers.smtp.port' => 1]);

        $this->post(route('password.email'), ['identifier' => $this->mhs->email])
            ->assertRedirect()->assertSessionHas('status');
    }

    public function test_permintaan_berulang_untuk_akun_yang_sama_hanya_mengirim_satu_email(): void
    {
        Notification::fake();

        $this->post(route('password.email'), ['identifier' => $this->mhs->email]);
        $this->post(route('password.email'), ['identifier' => $this->mhs->email]);

        Notification::assertSentToTimes($this->mhs, ResetKataSandi::class, 1);
    }

    public function test_permintaan_tautan_dibatasi_lima_per_menit(): void
    {
        $kode = [];
        for ($i = 0; $i < 6; $i++) {
            $kode[] = $this->post(route('password.email'), ['identifier' => "acak{$i}"])->status();
        }

        $this->assertSame(429, end($kode));
        $this->assertSame(302, $kode[0]);
    }

    public function test_identifier_wajib_diisi(): void
    {
        $this->post(route('password.email'), [])->assertSessionHasErrors(['identifier' => 'Masukkan NIM/NIP atau email Anda.']);
    }

    // =========================================================== lupa sandi: atur ulang

    public function test_form_reset_tampil_dengan_token_dan_tidak_membocorkan_referer(): void
    {
        $this->get(route('password.reset', ['token' => 'abc123']) . '?email=' . urlencode($this->mhs->email))
            ->assertOk()
            ->assertSee('name="token" value="abc123"', false)
            ->assertSee('name="email" value="' . $this->mhs->email . '"', false)
            ->assertSee('<meta name="referrer" content="no-referrer">', false);
    }

    public function test_reset_berhasil_mengganti_kata_sandi_dan_pengguna_bisa_login(): void
    {
        Event::fake([PasswordReset::class]);
        $token = $this->token($this->mhs);

        $this->reset($this->mhs, $token)
            ->assertRedirect('/')
            ->assertSessionHas('status', 'Kata sandi berhasil diubah. Silakan masuk dengan kata sandi baru Anda.');

        $this->assertTrue(Hash::check(self::BARU, $this->mhs->fresh()->password));
        $this->assertFalse(Hash::check(self::LAMA, $this->mhs->fresh()->password));
        Event::assertDispatched(PasswordReset::class);

        $this->post('/login', ['identifier' => '10240001', 'password' => self::BARU])
            ->assertRedirect(route('mahasiswa.dashboard'));
    }

    public function test_reset_juga_berlaku_untuk_dosen(): void
    {
        $this->reset($this->dosen, $this->token($this->dosen))->assertRedirect('/');

        $this->assertTrue(Hash::check(self::BARU, $this->dosen->fresh()->password));
    }

    public function test_reset_mencabut_token_api_dan_mengganti_remember_token(): void
    {
        $this->mhs->createToken('hp');
        $this->mhs->createToken('laptop');
        $rememberLama = $this->mhs->fresh()->remember_token;

        $this->reset($this->mhs, $this->token($this->mhs));

        $this->assertSame(0, $this->mhs->tokens()->count());
        $this->assertNotSame($rememberLama, $this->mhs->fresh()->remember_token);
    }

    public function test_token_hanya_bisa_dipakai_sekali(): void
    {
        $token = $this->token($this->mhs);

        $this->reset($this->mhs, $token)->assertRedirect('/');
        $this->reset($this->mhs, $token, 'LainLagi2027')
            ->assertSessionHasErrors(['email' => 'Tautan reset tidak valid atau sudah kedaluwarsa. Silakan minta tautan baru.']);

        $this->assertTrue(Hash::check(self::BARU, $this->mhs->fresh()->password));
    }

    public function test_token_kedaluwarsa_setelah_masa_berlaku(): void
    {
        $token = $this->token($this->mhs);

        $this->travel(61)->minutes();

        $this->reset($this->mhs, $token)->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check(self::LAMA, $this->mhs->fresh()->password));
    }

    public function test_token_salah_atau_milik_akun_lain_ditolak(): void
    {
        $this->reset($this->mhs, 'token-ngawur')->assertSessionHasErrors('email');

        $tokenDosen = $this->token($this->dosen);
        $this->reset($this->mhs, $tokenDosen)->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check(self::LAMA, $this->mhs->fresh()->password));
        $this->assertTrue(Hash::check(self::LAMA, $this->dosen->fresh()->password));
    }

    public function test_akun_admin_tidak_bisa_direset_walau_punya_token_valid(): void
    {
        $this->reset($this->admin, $this->token($this->admin))->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check(self::LAMA, $this->admin->fresh()->password));
    }

    public function test_akun_nonaktif_tidak_bisa_direset(): void
    {
        $token = $this->token($this->mhs);
        $this->mhs->delete();

        $this->reset($this->mhs, $token)->assertSessionHasErrors('email');
    }

    public function test_kata_sandi_lemah_atau_tidak_cocok_ditolak_dengan_pesan_indonesia(): void
    {
        $token = $this->token($this->mhs);

        $this->reset($this->mhs, $token, 'abc12')->assertSessionHasErrors(['password' => 'Kata sandi minimal 8 karakter.']);
        $this->reset($this->mhs, $token, 'hurufsajaa')->assertSessionHasErrors(['password' => 'Kata sandi harus mengandung angka.']);
        $this->reset($this->mhs, $token, '1234567890')->assertSessionHasErrors(['password' => 'Kata sandi harus mengandung huruf.']);
        $this->reset($this->mhs, $token, self::BARU, 'BedaSandi123')->assertSessionHasErrors(['password' => 'Konfirmasi kata sandi tidak cocok.']);

        // Token tidak terbakar oleh percobaan gagal validasi: masih bisa dipakai.
        $this->reset($this->mhs, $token)->assertRedirect('/');
    }

    public function test_reset_gagal_selalu_kembali_ke_form_reset_dan_menampilkan_pesan(): void
    {
        $url = route('password.reset', ['token' => 'salah', 'email' => $this->mhs->email]);

        // Tanpa header Referer sama sekali (seperti browser dengan no-referrer).
        $this->reset($this->mhs, 'salah')->assertRedirect($url);

        $this->followingRedirects()->reset($this->mhs, 'salah')
            ->assertOk()
            ->assertSee('Tautan reset tidak valid atau sudah kedaluwarsa.')
            ->assertSee('Minta tautan baru')
            ->assertSee('name="token" value="salah"', false);

        // Kegagalan validasi (sandi lemah) juga kembali ke form yang sama, dengan token yang sama.
        $token = $this->token($this->mhs);
        $this->reset($this->mhs, $token, 'abc')
            ->assertRedirect(route('password.reset', ['token' => $token, 'email' => $this->mhs->email]))
            ->assertSessionHasErrors('password');
    }

    public function test_reset_dibatasi_lima_per_menit(): void
    {
        $kode = [];
        for ($i = 0; $i < 6; $i++) {
            $kode[] = $this->reset($this->mhs, 'salah')->status();
        }

        $this->assertSame(429, end($kode));
    }

    // =========================================================== pembantu KataSandi::ganti

    public function test_ganti_menghapus_sesi_lain_tetapi_menyisakan_sesi_yang_dikecualikan(): void
    {
        config(['session.driver' => 'database']);

        $baris = fn (string $id, int $uid) => DB::table('sessions')->insert([
            'id' => $id, 'user_id' => $uid, 'ip_address' => '127.0.0.1', 'user_agent' => 't',
            'payload' => '', 'last_activity' => time(),
        ]);
        $baris('laptop', $this->mhs->id);
        $baris('hp', $this->mhs->id);
        $baris('sesi-orang-lain', $this->dosen->id);

        KataSandi::ganti($this->mhs, self::BARU, 'laptop');

        $this->assertSame(['laptop'], DB::table('sessions')->where('user_id', $this->mhs->id)->pluck('id')->all());
        $this->assertSame(1, DB::table('sessions')->where('user_id', $this->dosen->id)->count());

        KataSandi::ganti($this->mhs, self::BARU); // tanpa pengecualian: semua sesi hilang
        $this->assertSame(0, DB::table('sessions')->where('user_id', $this->mhs->id)->count());
    }

    // =========================================================== ubah sandi (sudah login)

    public function test_halaman_ubah_sandi_untuk_dosen_dan_mahasiswa_dengan_tautan_navbar(): void
    {
        $this->actingAs($this->mhs)->get(route('akun.kata-sandi'))
            ->assertOk()->assertSee('Ubah Kata Sandi')
            ->assertSee($this->mhs->name)                 // navbar kini memakai akun yang login
            ->assertDontSee('Baihaqi Abimanyu')
            ->assertSee(route('akun.kata-sandi'));

        $this->actingAs($this->dosen)->get(route('akun.kata-sandi'))
            ->assertOk()->assertSee('Ubah Kata Sandi')->assertSee(route('akun.kata-sandi'));
    }

    public function test_ubah_sandi_ditolak_untuk_tamu_dan_admin(): void
    {
        $this->get(route('akun.kata-sandi'))->assertRedirect(route('login'));
        $this->put(route('akun.kata-sandi.update'), [])->assertRedirect(route('login'));
        $this->actingAs($this->admin)->get(route('akun.kata-sandi'))->assertForbidden();
        $this->actingAs($this->admin)->put(route('akun.kata-sandi.update'), [
            'current_password' => self::LAMA, 'password' => self::BARU, 'password_confirmation' => self::BARU,
        ])->assertForbidden();

        $this->assertTrue(Hash::check(self::LAMA, $this->admin->fresh()->password));
    }

    public function test_ubah_sandi_berhasil_untuk_mahasiswa_dan_dosen(): void
    {
        foreach ([$this->mhs, $this->dosen] as $user) {
            $user->createToken('perangkat');

            $this->actingAs($user)->put(route('akun.kata-sandi.update'), [
                'current_password' => self::LAMA, 'password' => self::BARU, 'password_confirmation' => self::BARU,
            ])->assertRedirect(route('akun.kata-sandi'))->assertSessionHas('success')->assertSessionHasNoErrors();

            $this->assertTrue(Hash::check(self::BARU, $user->fresh()->password));
            $this->assertSame(0, $user->tokens()->count(), 'token API harus dicabut');
        }
    }

    public function test_ubah_sandi_menolak_kata_sandi_saat_ini_yang_salah(): void
    {
        $this->actingAs($this->mhs)->put(route('akun.kata-sandi.update'), [
            'current_password' => 'salahbanget1', 'password' => self::BARU, 'password_confirmation' => self::BARU,
        ])->assertSessionHasErrors(['current_password' => 'Kata sandi saat ini salah.']);

        $this->assertTrue(Hash::check(self::LAMA, $this->mhs->fresh()->password));
    }

    public function test_ubah_sandi_menolak_sama_dengan_yang_lama_lemah_dan_tidak_cocok(): void
    {
        $kirim = fn (array $data) => $this->actingAs($this->mhs)->put(route('akun.kata-sandi.update'), $data);

        $kirim(['current_password' => self::LAMA, 'password' => self::LAMA, 'password_confirmation' => self::LAMA])
            ->assertSessionHasErrors(['password' => 'Kata sandi baru tidak boleh sama dengan kata sandi saat ini.']);
        $kirim(['current_password' => self::LAMA, 'password' => 'pendek1', 'password_confirmation' => 'pendek1'])
            ->assertSessionHasErrors(['password' => 'Kata sandi minimal 8 karakter.']);
        $kirim(['current_password' => self::LAMA, 'password' => 'hurufsajaa', 'password_confirmation' => 'hurufsajaa'])
            ->assertSessionHasErrors(['password' => 'Kata sandi harus mengandung angka.']);
        $kirim(['current_password' => self::LAMA, 'password' => self::BARU, 'password_confirmation' => 'Berbeda12345'])
            ->assertSessionHasErrors(['password' => 'Konfirmasi kata sandi tidak cocok.']);
        $kirim([])->assertSessionHasErrors(['current_password', 'password']);

        $this->assertTrue(Hash::check(self::LAMA, $this->mhs->fresh()->password));
    }

    public function test_ubah_sandi_dibatasi_enam_percobaan_per_menit(): void
    {
        $kode = [];
        for ($i = 0; $i < 7; $i++) {
            $kode[] = $this->actingAs($this->mhs)->put(route('akun.kata-sandi.update'), [
                'current_password' => 'tebakan' . $i, 'password' => self::BARU, 'password_confirmation' => self::BARU,
            ])->status();
        }

        $this->assertSame(429, end($kode));
    }
}
