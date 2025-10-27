<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pinjaman; 
class KaryawanDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard untuk karyawan.
     */
    public function index()
    {
        // Ambil ID karyawan yang sedang login
        $userId = Auth::id();

        // Ambil 5 data pinjaman terakhir yang diajukan oleh karyawan ini
        // Kita juga memuat data anggota terkait menggunakan 'with('anggota')'
        $pinjamanTerbaru = Pinjaman::where('diajukan_oleh_user_id', $userId)
                                ->with('anggota')
                                ->latest()
                                ->take(5)
                                ->get();

        // Kirim data ke view
        return view('karyawans.dashboard', compact('pinjamanTerbaru'));
    }
}