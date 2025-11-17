<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\User; // <-- Pastikan ini ada
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AdminValidasiController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\AbsensiController;


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
    $userRole = Auth::user()->role;

    // 1. SUPER ADMIN
    if ($userRole === 'super_admin') {
        // Logika Statistik Super Admin
        $totalAdmin = \App\Models\User::where('role', 'admin')->count();
        $totalKaryawan = \App\Models\User::where('role', 'karyawan')->count();
        $totalNasabah = \App\Models\Anggota::count();
        return view('super_admin.dashboard', compact('totalAdmin', 'totalKaryawan', 'totalNasabah'));

    // 2. ADMIN
    } elseif ($userRole === 'admin') {
        $totalKaryawan = User::where('role', 'karyawan')->count();
        $totalNasabah = Anggota::count();
        $pendingPinjaman = Pinjaman::where('status', 'pending')->count();
        return view('admins.dashboard', compact('totalKaryawan', 'totalNasabah', 'pendingPinjaman'));

    // 3. KARYAWAN
    } elseif ($userRole === 'karyawan') {
        $userId = Auth::id();
        $totalNasabah = Anggota::where('dibuat_oleh_user_id', $userId)->count();
        $pengajuanNasabahTerbaru = Anggota::where('dibuat_oleh_user_id', $userId)
                                    ->latest()
                                    ->take(5)
                                    ->get();
        return view('karyawans.dashboard', compact('totalNasabah', 'pengajuanNasabahTerbaru'));
    
    } elseif ($userRole === 'sekertaris') {
        // Panggil method index di controller untuk hitung statistik
        return app(\App\Http\Controllers\SekertarisController::class)->index();
    }
    
    // Fallback
    return view('dashboard'); 

})->middleware(['auth', 'verified'])->name('dashboard');





// Grup route khusus untuk Super Admin
Route::middleware(['auth', 'role:super_admin'])->group(function () {

    // Route resource untuk CRUD Admin
    Route::resource('admins', AdminController::class);
    Route::resource('karyawans', KaryawanController::class);
    Route::get('/anggota/manage', [AnggotaController::class, 'indexForSuperAdmin'])->name('superadmin.anggota.index');
    Route::get('/anggota/{anggota}/edit', [AnggotaController::class, 'edit'])->name('superadmin.anggota.edit');
    Route::put('/anggota/{anggota}', [AnggotaController::class, 'update'])->name('superadmin.anggota.update');
    Route::delete('/anggota/{anggota}', [AnggotaController::class, 'destroy'])->name('superadmin.anggota.destroy');
});


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
    Route::get('/search/nik-riwayat', [AdminValidasiController::class, 'searchNikRiwayatAdmin'])->name('search.nik.riwayat');
    Route::patch('/pinjaman/{pinjaman}/transfer', [AdminValidasiController::class, 'transferPinjaman'])->name('pinjaman.transfer');
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
    Route::get('/anggota/search/nik', [AnggotaController::class, 'searchByNik'])->name('anggota.search.nik');
    
    Route::get('/karyawan/absensi', [AbsensiController::class, 'index'])->name('karyawan.absensi');

    Route::get('/riwayat-pinjaman/cari', [AnggotaController::class, 'showSearchRiwayatForm'])->name('anggota.riwayat.search.form');
    // Route untuk menangani pencarian AJAX (mirip sebelumnya)
    Route::get('/anggota/search/nik-riwayat', [AnggotaController::class, 'searchNikRiwayat'])->name('anggota.search.nik.riwayat');
});

Route::get('/pinjaman-aktif', [PembayaranController::class, 'indexPinjamanAktif'])->name('karyawan.pinjaman.aktif');
    Route::post('/pembayaran', [PembayaranController::class, 'storePembayaran'])->name('karyawan.pembayaran.store');


    // ...

// Grup route khusus untuk Sekertaris
Route::middleware(['auth', 'role:sekertaris'])->prefix('sekertaris')->name('sekertaris.')->group(function () {
    // Route Rekap Pinjaman
    Route::get('/rekap-pinjaman', [\App\Http\Controllers\SekertarisController::class, 'rekapPinjaman'])->name('pinjaman.rekap');
    
    // Nanti tambah route absensi di sini
});
require __DIR__.'/auth.php';