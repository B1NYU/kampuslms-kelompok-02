<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pertemuan (minggu 1-16) tempat materi ditampilkan. NULL = materi umum
     * yang tidak terikat pada pertemuan tertentu.
     */
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->unsignedTinyInteger('session')->nullable()->after('course_id');
            $table->index(['course_id', 'session']);
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropIndex(['course_id', 'session']);
            $table->dropColumn('session');
        });
    }
};
