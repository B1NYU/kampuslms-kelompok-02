<?php

use App\Http\Controllers\Api\V1\AssignmentController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CourseController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    
    // 1. Endpoint Publik
    Route::post('auth/login', [AuthController::class, 'login']);

    // 2. Endpoint yang Butuh Autentikasi (Semua Role: Mahasiswa, Dosen, Admin)
    Route::middleware('auth:sanctum')->group(function () {
        
        // Auth Management
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);

        // Read-only Courses & Assignments (Mahasiswa & Dosen bisa akses)
        Route::get('courses', [CourseController::class, 'index']);
        Route::get('courses/{course}', [CourseController::class, 'show']);
        Route::get('courses/{course}/assignments', [AssignmentController::class, 'index']);

        // 3. Endpoint Khusus Dosen (Mencegah Mahasiswa masuk -> Auto 403)
        Route::middleware('role:dosen')->group(function () {
            Route::post('assignments', [AssignmentController::class, 'store']);
            Route::match(['put', 'patch'], 'assignments/{assignment}', [AssignmentController::class, 'update']);
            Route::delete('assignments/{assignment}', [AssignmentController::class, 'destroy']);
        });

    });
});