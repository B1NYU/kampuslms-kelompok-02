<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. INDEKS SUBMISSIONS (user_id)
        // Query yang diuntungkan:
        // - RekapNilaiController: Submission::whereIn('user_id', $students->pluck('id'))
        // - AssignmentController: $assignment->submissions()->where('user_id', $user->id)
        // Penjelasan: Indeks bawaan hanya UNIQUE(assignment_id, user_id). Query yang memfilter
        // berdasarkan user_id saja tidak dapat menggunakan indeks komposit tersebut secara optimal
        // karena melanggar leftmost prefix rule.
        // Biaya: Overhead kecil saat penambahan baris pengumpulan baru.
        Schema::table('submissions', function (Blueprint $table) {
            $table->index('user_id', 'idx_submissions_user_id');
        });

        // 2. INDEKS SUBMISSIONS (submitted_at)
        // Query yang diuntungkan:
        // - GradingController: Submission::latest('submitted_at')
        // Penjelasan: Menghilangkan proses FileSort pada MySQL saat mengurutkan pengumpulan tugas terbaru.
        // Biaya: Overhead saat mahasiswa mengunggah tugas karena timestamp harus di-index.
        Schema::table('submissions', function (Blueprint $table) {
            $table->index('submitted_at', 'idx_submissions_submitted_at');
        });

        // 3. INDEKS KOMPOSIT ASSIGNMENTS (course_id, status, due_at)
        // Query yang diuntungkan:
        // - RekapNilaiController: $course->assignments()->where('status', 'published')->orderBy('due_at')
        // Penjelasan: Indeks komposit lama hanya ['course_id', 'due_at']. Menambahkan 'status'
        // mempercepat penyaringan tugas berstatus 'published' tanpa memindai tugas 'draft'.
        // Biaya: Overhead saat dosen mengubah status tugas dari draft ke published.
        Schema::table('assignments', function (Blueprint $table) {
            $table->index(['course_id', 'status', 'due_at'], 'idx_assignments_course_status_due');
        });

        // 4. INDEKS KOMPOSIT COURSES (lecturer_id, status)
        // Query yang diuntungkan:
        // - GradingController: $request->user()->taughtCourses()->where('status', 'active')
        // - StudentController: $request->user()->taughtCourses()->orderBy('code')
        // Penjelasan: Mempercepat pencarian mata kuliah aktif milik dosen yang sedang login.
        // Biaya: Overhead minimal saat pembaruan status mata kuliah oleh admin.
        Schema::table('courses', function (Blueprint $table) {
            $table->index(['lecturer_id', 'status'], 'idx_courses_lecturer_status');
        });

        // 5. INDEKS PIVOT COURSE_USER (user_id)
        // Query yang diuntungkan:
        // - MahasiswaCourseController / User::courses(): Mengambil daftar mata kuliah yang diikuti mahasiswa.
        // Penjelasan: Tabel pivot course_user hanya memiliki UNIQUE(course_id, user_id).
        // Pencarian dari perspektif user_id membutuhkan indeks tersendiri pada user_id.
        // Biaya: Overhead kecil saat pendaftaran mahasiswa ke mata kuliah baru.
        Schema::table('course_user', function (Blueprint $table) {
            $table->index('user_id', 'idx_course_user_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            // Lepas Foreign Key terlebih dahulu agar MySQL mengizinkan penghapusan indeks
            $table->dropForeign(['user_id']);

            $table->dropIndex('idx_submissions_user_id');
            $table->dropIndex('idx_submissions_submitted_at');

            // Pasang kembali Foreign Key bawaan
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropIndex('idx_assignments_course_status_due');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex('idx_courses_lecturer_status');
        });

        Schema::table('course_user', function (Blueprint $table) {
            // Terapkan penanganan serupa untuk foreign key user_id di course_user
            $table->dropForeign(['user_id']);
            $table->dropIndex('idx_course_user_user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
