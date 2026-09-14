<?php

use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MataKuliahController;

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
    Route::get('/', [CourseController::class, 'index'])->name('index');
    Route::get('/{mata_kuliah}', [CourseController::class, 'show'])->name('show');
    Route::get('/{mata_kuliah}/nilai', [CourseController::class, 'nilai'])->name('nilai');
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

    Route::get('/pengguna', function () {
        return view()->file(resource_path('views/admin/admin.pengguna.blade.php'));
    })->name('pengguna');

    Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])->name('matkul');
    Route::post('/mata-kuliah', [MataKuliahController::class, 'store'])->name('matkul.store');
    Route::put('/mata-kuliah/{matkul}', [MataKuliahController::class, 'update'])->name('matkul.update');
    Route::delete('/mata-kuliah/{matkul}', [MataKuliahController::class, 'destroy'])->name('matkul.destroy');

    Route::get('/pendaftaran', function () {
        return view()->file(resource_path('views/admin/admin.pendaftaran.blade.php'));
    })->name('pendaftaran');
});
