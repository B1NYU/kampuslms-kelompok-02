<?php

use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tentang', function () {
    return view('anggota');
});


Route::prefix('mata-kuliah')->name('mata-kuliah.')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('index');
    Route::get('/mata_kuliah', [CourseController::class, 'show'])->name('show');
});