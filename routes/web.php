<?php

use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Mahasiswa\MahasiswaCourseController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/tentang', function () {
    return view()->file(resource_path('views/mahasiswa/anggota.blade.php'));
});

Route::match(['get', 'post'], '/login', function (\Illuminate\Http\Request $request) {
    $role = $request->input('role', 'mahasiswa');
    $nama = $request->input('nama', $role === 'dosen' ? 'Dr. Budi Santoso, M.Kom' : 'Mahasiswa');

    session(['user_role' => $role, 'user_name' => $nama]);

    if ($role === 'dosen') {
        return redirect()->route('dosen.dashboard');
    }

    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('dashboard');
})->name('login');

Route::match(['get', 'post'], '/logout', function () {
    session()->forget(['user_role', 'user_name']);
    session()->flush();
    return redirect('/')->with('status', 'Anda telah berhasil logout.');
})->name('logout');

Route::get('/dashboard', function () {
    return view()->file(resource_path('views/mahasiswa/mahasiswa.dashboard.blade.php'));
})->name('dashboard');

Route::prefix('mata-kuliah')->name('mata-kuliah.')->group(function () {
    Route::get('/', [MahasiswaCourseController::class, 'index'])->name('index');
    Route::get('/{mata_kuliah}', [MahasiswaCourseController::class, 'show'])->name('show');
});

Route::prefix('dosen')->name('dosen.')->group(function () {
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

    Route::get('/tugas', function () {
        return view()->file(resource_path('views/dosen/tugas.blade.php'));
    })->name('tugas');

    Route::get('/penilaian', function () {
        return view()->file(resource_path('views/dosen/penilaian.blade.php'));
    })->name('penilaian');
});

Route::prefix('admin')->name('admin.')->group(function () {
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
