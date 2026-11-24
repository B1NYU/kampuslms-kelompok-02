<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'dosen', 'mahasiswa'])->default('mahasiswa')->after('email');
            $table->string('nim_nip')->nullable()->unique()->after('role');
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropUnique(['nim_nip']); // perbaikan bug: rollback gagal karena index unik tidak di-drop dulu
            $table->dropColumn(['role', 'nim_nip', 'deleted_at']);
        });
    }
};
