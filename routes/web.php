<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaryawanController; // <-- DITAMBAHKAN
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AdminValidasiController; // <-- Tambahkan ini


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
    Route::resource('karyawans', KaryawanController::class);
});

// ===============================================================
// AKHIR DARI KODE YANG DIPERBAIKI & DITAMBAHKAN
// ===============================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Validasi Nasabah
    Route::get('/validasi/nasabah', [AdminValidasiController::class, 'index'])->name('validasi.nasabah.index');
    Route::patch('/validasi/nasabah/{anggota}/setujui', [AdminValidasiController::class, 'setujui'])->name('validasi.nasabah.setujui');
    Route::patch('/validasi/nasabah/{anggota}/tolak', [AdminValidasiController::class, 'tolak'])->name('validasi.nasabah.tolak');

    // Validasi Pinjaman
    Route::get('/validasi/pinjaman', [AdminValidasiController::class, 'validasiPinjaman'])->name('validasi.pinjaman.index');
    Route::patch('/validasi/pinjaman/{pinjaman}/setujui', [AdminValidasiController::class, 'setujuiPinjaman'])->name('validasi.pinjaman.setujui');
    Route::patch('/validasi/pinjaman/{pinjaman}/tolak', [AdminValidasiController::class, 'tolakPinjaman'])->name('validasi.pinjaman.tolak');
    
    // Riwayat Pinjaman
    Route::get('/riwayat-pinjaman', [AdminValidasiController::class, 'riwayatPinjaman'])->name('riwayat.pinjaman');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/karyawans/dashboard', [KaryawanController::class, 'index'])->name('karyawans.dashboard');

    
    Route::get('/pinjaman/ajukan', [PinjamanController::class, 'create'])->name('pinjaman.create');
    Route::post('/pinjaman', [PinjamanController::class, 'store'])->name('pinjaman.store');

    
    Route::get('/pinjaman', [PinjamanController::class, 'index'])->name('pinjaman.index');
    Route::resource('anggota', AnggotaController::class);

    
    Route::get('/karyawans/absensi', function() {
        return view('karyawans.absensi');
    })->name('karyawans.absensi');
});

// Memuat semua route otentikasi (login, register, dll.)
require __DIR__.'/auth.php';