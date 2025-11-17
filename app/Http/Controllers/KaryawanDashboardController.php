<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Anggota; // <-- Kita fokus ke model Anggota sekarang
use App\Models\Pinjaman;

class KaryawanDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. Hitung Total Nasabah yang diurus karyawan ini
        $totalNasabah = Anggota::where('dibuat_oleh_user_id', $userId)->count();

        // 2. Ambil 5 Pengajuan Pendaftaran Nasabah Terakhir
        $pengajuanNasabahTerbaru = Anggota::where('dibuat_oleh_user_id', $userId)
                                    ->latest() // Urutkan dari yang paling baru
                                    ->take(5)
                                    ->get();

        return view('karyawans.dashboard', compact('totalNasabah', 'pengajuanNasabahTerbaru'));
    }
}