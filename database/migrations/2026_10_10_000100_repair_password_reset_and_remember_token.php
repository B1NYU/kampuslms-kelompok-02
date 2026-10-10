<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Memperbaiki skema yang dibutuhkan fitur reset kata sandi.
 *
 * Migrasi awal (0001_01_01_000000_create_users_table) membuat:
 *  - password_reset_tokens hanya dengan kolom created_at (tanpa email dan token), sehingga
 *    Laravel tidak bisa menyimpan token reset;
 *  - users tanpa remember_token.
 *
 * Migrasi ini sengaja terpisah (bukan mengedit migrasi lama) agar database yang sudah
 * terlanjur dimigrasi ikut diperbaiki. Aman dijalankan berulang: tiap langkah diperiksa dulu.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Isi tabel ini hanya token sementara (kedaluwarsa 60 menit), jadi aman dibuat ulang.
        if (! Schema::hasColumn('password_reset_tokens', 'email')) {
            Schema::dropIfExists('password_reset_tokens');

            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasColumn('users', 'remember_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->rememberToken()->after('password');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'remember_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('remember_token');
            });
        }

        // password_reset_tokens sengaja tidak dikembalikan ke bentuk rusaknya.
    }
};
