<?php

use App\Http\Controllers\Api\V1\AssignmentController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CourseController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // 1. Publik. Login dibatasi 5 permintaan/menit (route) dan masih ditambah
    //    pembatas per identitas di AuthController.
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // 2. Wajib token. Pembatasan umum 60 permintaan/menit.
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);

        // Baca: aturan akses di CoursePolicy / AssignmentPolicy (dicek di controller).
        Route::get('courses', [CourseController::class, 'index']);
        Route::get('courses/{course}', [CourseController::class, 'show']);
        Route::get('courses/{course}/assignments', [AssignmentController::class, 'index']);

        // 3. Tulis tugas: admin dan dosen (keputusan B3, melengkapi Bagian 5).
        //    Mahasiswa mendapat 403 (bukan 401). Kepemilikan MK dicek policy.
        Route::middleware('role:admin,dosen')->group(function () {
            Route::post('assignments', [AssignmentController::class, 'store']);
            Route::match(['put', 'patch'], 'assignments/{assignment}', [AssignmentController::class, 'update']);
            Route::delete('assignments/{assignment}', [AssignmentController::class, 'destroy']);
        });
    });
});
