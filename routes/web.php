<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController; // <-- DITAMBAHKAN
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Mengalihkan halaman utama ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// Route untuk dashboard biasa (untuk admin dan karyawan)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// ===============================================================
// AWAL DARI KODE YANG DIPERBAIKI & DITAMBAHKAN
// ===============================================================

// Grup route khusus untuk Super Admin
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    // Route untuk dashboard Super Admin
    Route::get('/super-admin/dashboard', function () {
        return view('super_admin.dashboard');
    })->name('super_admin.dashboard');

    // Route resource untuk CRUD Admin
    Route::resource('admins', AdminController::class);
});

// ===============================================================
// AKHIR DARI KODE YANG DIPERBAIKI & DITAMBAHKAN
// ===============================================================


// Route untuk manajemen profil user
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Memuat semua route otentikasi (login, register, dll.)
require __DIR__.'/auth.php';