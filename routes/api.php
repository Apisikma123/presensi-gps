<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Update API Routes
Route::prefix('update')->group(function () {
    Route::get('/check', [App\Http\Controllers\Api\UpdateController::class, 'checkUpdate']);
    Route::get('/version', [App\Http\Controllers\Api\UpdateController::class, 'getCurrentVersion']);
    Route::middleware(['auth:sanctum', 'role:super admin'])->group(function () {
        Route::get('/list', [App\Http\Controllers\Api\UpdateController::class, 'listUpdates']);
        Route::get('/history', [App\Http\Controllers\Api\UpdateController::class, 'history']);
        Route::get('/log/{id}', [App\Http\Controllers\Api\UpdateController::class, 'showLog']);
        Route::get('/status/{logId}', [App\Http\Controllers\Api\UpdateController::class, 'getStatus']);
        Route::post('/{version}/download', [App\Http\Controllers\Api\UpdateController::class, 'downloadUpdate']);
        Route::post('/{version}/install', [App\Http\Controllers\Api\UpdateController::class, 'installUpdate']);
        Route::post('/{version}/update-now', [App\Http\Controllers\Api\UpdateController::class, 'updateNow']);
        Route::get('/{version}', [App\Http\Controllers\Api\UpdateController::class, 'show']);
    });
});

// Mobile API Routes (Final Scope Whitelist)
Route::prefix('mobile')->group(function () {
    Route::post('/login', [App\Http\Controllers\Api\Mobile\AuthController::class, 'login'])
        ->middleware('throttle:10,1');

    Route::middleware(['auth:sanctum', \App\Http\Middleware\CheckKaryawanExists::class])->group(function () {
        Route::post('/logout', [App\Http\Controllers\Api\Mobile\AuthController::class, 'logout']);
        Route::get('/profile', [App\Http\Controllers\Api\Mobile\ProfileController::class, 'index']);
        Route::post('/profile/password', [App\Http\Controllers\Api\Mobile\ProfileController::class, 'updatePassword']);
        Route::post('/profile/foto', [App\Http\Controllers\Api\Mobile\ProfileController::class, 'updateFoto']);
        Route::get('/dashboard', [App\Http\Controllers\Api\Mobile\DashboardController::class, 'index']);

        // Presensi (Kehadiran, GPS, Face Recognition)
        Route::post('/presensi/masuk', [App\Http\Controllers\Api\Mobile\PresensiController::class, 'masuk']);
        Route::post('/presensi/pulang', [App\Http\Controllers\Api\Mobile\PresensiController::class, 'pulang']);
        Route::get('/presensi/riwayat', [App\Http\Controllers\Api\Mobile\PresensiController::class, 'riwayat']);

        // Izin / Sakit / Cuti
        Route::get('/izin', [App\Http\Controllers\Api\Mobile\IzinController::class, 'index']);
        Route::post('/izin', [App\Http\Controllers\Api\Mobile\IzinController::class, 'store']);
        Route::delete('/izin/{kode}', [App\Http\Controllers\Api\Mobile\IzinController::class, 'destroy']);

        // Face Recognition
        Route::get('/facerecognition', [App\Http\Controllers\Api\Mobile\FacerecognitionController::class, 'index']);
        Route::post('/facerecognition', [App\Http\Controllers\Api\Mobile\FacerecognitionController::class, 'store']);
        Route::delete('/facerecognition', [App\Http\Controllers\Api\Mobile\FacerecognitionController::class, 'destroy']);
    });
});
