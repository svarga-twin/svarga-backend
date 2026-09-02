<?php

use App\Http\Controllers\Api\FestivalController;
use App\Http\Controllers\Api\UmkmController;
use Illuminate\Support\Facades\Route;

Route::get('/umkm', [UmkmController::class, 'index']);
Route::get('/umkm/{id}', [UmkmController::class, 'show']);

Route::get('/festivals', [FestivalController::class, 'index']);
Route::get('/festivals/{id}', [FestivalController::class, 'show']);
