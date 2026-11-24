<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Menjaga agar seluruh migrasi bisa di-rollback (down() valid) lalu di-migrate ulang.
 * Sengaja TIDAK memakai RefreshDatabase, karena trait itu memakai migrate:fresh
 * yang tidak pernah memanggil down().
 */
class MigrationRollbackTest extends TestCase
{
    public function test_semua_migrasi_bisa_di_rollback_dan_dimigrasi_ulang(): void
    {
        $this->artisan('migrate:fresh')->assertExitCode(0);

        $this->assertTrue(Schema::hasColumn('users', 'nim_nip'));

        $this->artisan('migrate:rollback')->assertExitCode(0);

        $this->assertFalse(Schema::hasTable('users'));

        $this->artisan('migrate')->assertExitCode(0);

        $this->assertTrue(Schema::hasColumn('users', 'nim_nip'));
    }
}
