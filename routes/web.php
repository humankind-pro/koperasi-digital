<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sinilah Anda bisa mendaftarkan web route untuk aplikasi Anda.
| Route ini dimuat oleh RouteServiceProvider dan semuanya akan
| ditugaskan ke grup middleware "web". Buat sesuatu yang hebat!
|
*/

// Mengalihkan halaman utama ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// Route untuk dashboard yang hanya bisa diakses setelah login
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route untuk Super Admin
Route::get('/super-admin/dashboard', function () {
    return view('super_admin.dashboard');
})->middleware(['auth'])->name('super_admin.dashboard');

// Route untuk manajemen profil user
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Memuat semua route otentikasi (login, register, dll.)
require __DIR__.'/auth.php';