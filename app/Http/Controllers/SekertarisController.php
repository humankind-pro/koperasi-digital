<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pinjaman;
use Carbon\Carbon;

class SekertarisController extends Controller
{
    /**
     * Menampilkan Dashboard Sekertaris
     */
    public function index()
    {
        // Statistik sederhana untuk bulan ini
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $totalPinjamanBulanIni = Pinjaman::whereMonth('tanggal_pengajuan', $bulanIni)
                                         ->whereYear('tanggal_pengajuan', $tahunIni)
                                         ->count();

        $totalNominalDisetujui = Pinjaman::where('status', 'disetujui')
                                         ->whereMonth('tanggal_validasi', $bulanIni)
                                         ->whereYear('tanggal_validasi', $tahunIni)
                                         ->sum('jumlah_disetujui');

        return view('sekertaris.dashboard', compact('totalPinjamanBulanIni', 'totalNominalDisetujui'));
    }

    /**
     * Menampilkan Halaman Rekap Pinjaman Bulanan
     */
    public function rekapPinjaman(Request $request)
    {
        // Ambil input bulan & tahun dari request, atau default ke bulan/tahun saat ini
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        // Query data pinjaman berdasarkan filter
        $dataPinjaman = Pinjaman::with(['anggota', 'diajukanOleh', 'divalidasiOleh'])
            ->whereMonth('tanggal_pengajuan', $bulan)
            ->whereYear('tanggal_pengajuan', $tahun)
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        // Hitung total untuk footer tabel
        $totalPengajuan = $dataPinjaman->count();
        $totalDisetujui = $dataPinjaman->where('status', 'disetujui')->count();
        $totalNominal = $dataPinjaman->where('status', 'disetujui')->sum('jumlah_disetujui');

        return view('sekertaris.pinjaman.rekap', compact(
            'dataPinjaman', 
            'bulan', 
            'tahun', 
            'totalPengajuan', 
            'totalDisetujui', 
            'totalNominal'
        ));
    }
}