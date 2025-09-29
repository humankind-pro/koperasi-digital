<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

/**
 * Rute utama ('/') akan langsung mengarahkan pengguna ke halaman login.
 */
Route::get('/', function () {
    return redirect()->route('login');
});

/**
 * Grup rute untuk panel admin.
 * Semua rute di dalam grup ini akan memiliki awalan URL '/admin'
 * dan dilindungi oleh middleware 'auth:admin', yang memastikan
 * hanya pengguna dari tabel 'master_admin' yang bisa mengaksesnya.
 */
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    
    // URL: /admin/dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // URL: /admin/profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});


/**
 * Memuat file rute untuk autentikasi (login, logout, dll.).
 * Biarkan baris ini di bagian paling bawah.
 */
require __DIR__.'/auth.php';