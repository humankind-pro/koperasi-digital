<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;

// Route untuk IoT (Tanpa Login/Auth sementara agar mudah)
Route::post('/iot/absen', [AbsensiController::class, 'storeFromIot']);
