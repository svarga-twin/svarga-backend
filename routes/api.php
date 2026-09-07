<?php

use App\Http\Controllers\Api\FestivalController;
use App\Http\Controllers\Api\SensorReadingController;
use App\Http\Controllers\Api\UmkmController;
use Illuminate\Support\Facades\Route;

Route::get('/umkm', [UmkmController::class, 'index']);
Route::get('/umkm/{id}', [UmkmController::class, 'show']);

Route::get('/festivals', [FestivalController::class, 'index']);
Route::get('/festivals/{id}', [FestivalController::class, 'show']);

// Uji coba input sensor (suhu & kualitas udara) — lihat SensorReadingController
// untuk penjelasan alur sensor -> FE -> DB lengkap.
Route::post('/sensors/readings', [SensorReadingController::class, 'store']);
Route::get('/sensors/live', [SensorReadingController::class, 'live']);
Route::get('/sensors/latest', [SensorReadingController::class, 'latest']);
