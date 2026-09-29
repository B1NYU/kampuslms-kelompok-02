<?php

use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dosen\AssignmentController as DosenAssignmentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Mahasiswa\MahasiswaCourseController;
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
    Route::middleware('role:mahasiswa')->group(function () {
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

    // ===== Mahasiswa & dosen: detail tugas (kepemilikan dicek di controller) =====
    Route::get('/assignments/{assignment}', [AssignmentController::class, 'show'])
        ->middleware('role:mahasiswa,dosen')
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

        Route::get('/mahasiswa', function () {
            return view()->file(resource_path('views/dosen/mahasiswa.blade.php'));
        })->name('mahasiswa');

        Route::get('/materi', function () {
            return view()->file(resource_path('views/dosen/materi.blade.php'));
        })->name('materi');

        // Pintu masuk menu "Buat Tugas": diarahkan ke mata kuliah pertama yang diampu.
        Route::get('/tugas', [DosenAssignmentController::class, 'landing'])->name('tugas');

        Route::get('/penilaian', function () {
            return view()->file(resource_path('views/dosen/penilaian.blade.php'));
        })->name('penilaian');

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
    });

});
