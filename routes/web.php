<?php

use App\Http\Controllers\Admin\AdminAssignmentController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminMaterialController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dosen\AssignmentController as DosenAssignmentController;
use App\Http\Controllers\Dosen\GradingController;
use App\Http\Controllers\Dosen\MaterialController as DosenMaterialController;
use App\Http\Controllers\Dosen\StudentController as DosenStudentController;
use App\Http\Controllers\Mahasiswa\MahasiswaCourseController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/tentang', function () {
    return view()->file(resource_path('views/mahasiswa/anggota.blade.php'));
});

// Autentikasi sungguhan (Auth::attempt). Halaman login = halaman utama ('/').
Route::get('/login', fn () => redirect('/'))->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->name('login.attempt');

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ===== Mahasiswa =====
    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', function () {
            return view()->file(resource_path('views/mahasiswa/mahasiswa.dashboard.blade.php'));
        })->name('dashboard');

        Route::prefix('mata-kuliah')->name('mata-kuliah.')->group(function () {
            Route::get('/', [MahasiswaCourseController::class, 'index'])->name('index');
            // Kepemilikan (terdaftar atau tidak) dicek di controller.
            Route::get('/{mata_kuliah}', [MahasiswaCourseController::class, 'show'])->name('show');
        });

        Route::post('/assignments/{assignment}/submissions', [SubmissionController::class, 'store'])
            ->name('assignments.submissions.store');
    });

    // ===== Mahasiswa, dosen & admin: detail tugas (kepemilikan dicek di controller) =====
    Route::get('/assignments/{assignment}', [AssignmentController::class, 'show'])
        ->middleware('role:mahasiswa,dosen,admin')
        ->name('assignments.show');

    // ===== Semua peran login: pemilik / dosen pengampu / admin (dicek di controller) =====
    Route::get('/submissions/{submission}', [SubmissionController::class, 'show'])
        ->name('submissions.show');

    // ===== Dosen =====
    Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/dashboard', function () {
            $path = resource_path('views/dosen/dosen.dashboard.blade.php');
            if (file_exists($path)) {
                return view()->file($path);
            }
            return view('dosen.dashboard');
        })->name('dashboard');

        // Kelola mahasiswa: daftar / keluarkan mahasiswa dari mata kuliah yang diampu.
        Route::get('/mahasiswa', [DosenStudentController::class, 'index'])->name('mahasiswa');
        Route::post('/mahasiswa', [DosenStudentController::class, 'store'])->name('mahasiswa.store');
        Route::delete('/mahasiswa/{course}/{student}', [DosenStudentController::class, 'destroy'])->name('mahasiswa.destroy');

        // Materi: nama 'dosen.materi' dipertahankan agar link di navbar tidak putus.
        Route::get('/materi', [DosenMaterialController::class, 'index'])->name('materi');
        Route::post('/materi', [DosenMaterialController::class, 'store'])->name('materi.store');
        Route::put('/materi/{material}', [DosenMaterialController::class, 'update'])->name('materi.update');
        Route::delete('/materi/{material}', [DosenMaterialController::class, 'destroy'])->name('materi.destroy');
        // Download memakai MaterialController umum (App\Http\Controllers\MaterialController).
        Route::get('/materi/{material}/download', [MaterialController::class, 'download'])->name('materi.download');

        // Pintu masuk menu "Buat Tugas": diarahkan ke mata kuliah pertama yang diampu.
        Route::get('/tugas', [DosenAssignmentController::class, 'landing'])->name('tugas');

        // Penilaian & Feedback Dosen
        Route::get('/penilaian', [GradingController::class, 'index'])->name('penilaian');
        Route::put('/penilaian/{submission}', [GradingController::class, 'update'])->name('penilaian.update');

        // /dosen/courses/{course}/assignments (index, create, store)
        // /dosen/assignments/{assignment}     (edit, update, destroy)
        Route::scopeBindings()->group(function () {
            Route::resource('courses.assignments', DosenAssignmentController::class)
                ->shallow()
                ->except(['show']);
        });
    });

    // ===== Admin =====
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            $path = resource_path('views/admin/admin.dashboard.blade.php');
            if (file_exists($path)) {
                return view()->file($path);
            }
            return view('admin.dashboard');
        })->name('dashboard');

        Route::get('/pengguna', [AdminUserController::class, 'index'])->name('pengguna');
        Route::post('/pengguna', [AdminUserController::class, 'store'])->name('pengguna.store');
        Route::put('/pengguna/{user}', [AdminUserController::class, 'update'])->name('pengguna.update')->withTrashed();
        Route::delete('/pengguna/{user}', [AdminUserController::class, 'destroy'])->name('pengguna.destroy')->withTrashed();

        Route::get('/mata-kuliah', [AdminCourseController::class, 'index'])->name('matkul');
        Route::post('/mata-kuliah', [AdminCourseController::class, 'store'])->name('matkul.store');
        Route::put('/mata-kuliah/{matkul}', [AdminCourseController::class, 'update'])->name('matkul.update');
        Route::delete('/mata-kuliah/{matkul}', [AdminCourseController::class, 'destroy'])->name('matkul.destroy');

        Route::get('/pendaftaran', function () {
            return view()->file(resource_path('views/admin/admin.pendaftaran.blade.php'));
        })->name('pendaftaran');

        Route::get('/materi', [AdminMaterialController::class, 'index'])->name('materi');
        Route::post('/materi', [AdminMaterialController::class, 'store'])->name('materi.store');
        Route::put('/materi/{material}', [AdminMaterialController::class, 'update'])->name('materi.update');
        Route::delete('/materi/{material}', [AdminMaterialController::class, 'destroy'])->name('materi.destroy');
        Route::get('/materi/{material}/download', [AdminMaterialController::class, 'download'])->name('materi.download');

        Route::get('/tugas', [AdminAssignmentController::class, 'index'])->name('tugas');
        Route::post('/tugas', [AdminAssignmentController::class, 'store'])->name('tugas.store');
        Route::put('/tugas/{assignment}', [AdminAssignmentController::class, 'update'])->name('tugas.update');
        Route::delete('/tugas/{assignment}', [AdminAssignmentController::class, 'destroy'])->name('tugas.destroy');

        Route::get('/nilai', function () {
            return view()->file(resource_path('views/admin/admin.nilai.blade.php'));
        })->name('nilai');
    });

});