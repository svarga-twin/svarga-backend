<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FestivalController;
use App\Http\Controllers\Api\GeofenceController;
use App\Http\Controllers\Api\GreenSpaceController;
use App\Http\Controllers\Api\KoridorController;
use App\Http\Controllers\Api\MoodLogController;
use App\Http\Controllers\Api\SensorReadingController;
use App\Http\Controllers\Api\SoundscapeController;
use App\Http\Controllers\Api\UmkmController;
use Illuminate\Support\Facades\Route;

Route::get('/umkm', [UmkmController::class, 'index']);
Route::get('/umkm/{id}', [UmkmController::class, 'show']);

Route::get('/festivals', [FestivalController::class, 'index']);
Route::get('/festivals/{id}', [FestivalController::class, 'show']);

// Green space & koridor — menggantikan koleksi Firestore `green_space`/`koridor`
// yang kosong (lihat KoridorController).
Route::get('/green-spaces', [GreenSpaceController::class, 'index']);
Route::get('/koridors', [KoridorController::class, 'index']);
Route::get('/koridors/{id}', [KoridorController::class, 'show']);

// Uji coba input sensor (suhu & kualitas udara) — lihat SensorReadingController
// untuk penjelasan alur sensor -> FE -> DB lengkap.
Route::post('/sensors/readings', [SensorReadingController::class, 'store']);
Route::get('/sensors/live', [SensorReadingController::class, 'live']);
Route::get('/sensors/latest', [SensorReadingController::class, 'latest']);

// Autentikasi (Sanctum, token-based — cocok untuk PWA/mobile).
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
});

// Mood Tracker. `store` sengaja TIDAK memakai middleware auth:sanctum agar
// mode tamu (anonim) tetap bisa mengirim mood sesuai Batasan 4.1 proposal —
// middleware itu akan menolak request tanpa token. Kalau token dikirim &
// valid, MoodLogController tetap mengenali user lewat $request->user('sanctum')
// (guard sanctum bisa dipanggil langsung tanpa middleware), jadi login tetap
// opsional, bukan wajib.
Route::post('/mood-logs', [MoodLogController::class, 'store']);
Route::get('/mood-logs/summary', [MoodLogController::class, 'summary']);

// Geofencing & Soundscape Therapy.
Route::get('/geofences', [GeofenceController::class, 'index']);
Route::get('/soundscapes/{id}', [SoundscapeController::class, 'show']);
