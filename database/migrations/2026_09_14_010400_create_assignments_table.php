<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->unsignedTinyInteger('session')->nullable();   // pertemuan 1-16
            $table->foreignId('created_by')->constrained('users');
            $table->string('title');
            $table->text('instructions');
            $table->dateTime('due_at');
            $table->unsignedTinyInteger('max_score')->default(100);
            $table->boolean('allow_late')->default(true);
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();

            $table->index(['course_id', 'due_at']);
            $table->index(['course_id', 'session']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
