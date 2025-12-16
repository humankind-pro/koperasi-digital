<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pinjaman;
use Carbon\Carbon;
use App\Models\PengaturanAbsensi;
use App\Models\User;

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
        // 1. Ambil input bulan & tahun
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        // 2. Query data pinjaman (Tampilkan semua pengajuan bulan itu)
        $dataPinjaman = Pinjaman::with(['anggota', 'diajukanOleh', 'divalidasiOleh'])
            ->whereMonth('tanggal_pengajuan', $bulan)
            ->whereYear('tanggal_pengajuan', $tahun)
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        // 3. Hitung Statistik
        $totalPengajuan = $dataPinjaman->count();
        
        // REVISI: Hitung jumlah item yang 'disetujui' ATAU 'lunas'
        $totalDisetujui = $dataPinjaman->whereIn('status', ['disetujui', 'lunas'])->count();

        // 4. REVISI TOTAL NOMINAL
        // Menjumlahkan kolom 'jumlah_disetujui' untuk status 'disetujui' DAN 'lunas'
        $totalNominal = Pinjaman::whereMonth('tanggal_pengajuan', $bulan)
            ->whereYear('tanggal_pengajuan', $tahun)
            ->whereIn('status', ['disetujui', 'lunas']) // <--- PERUBAHAN DISINI
            ->sum('jumlah_disetujui');

        return view('sekertaris.pinjaman.rekap', compact(
            'dataPinjaman', 
            'bulan', 
            'tahun', 
            'totalPengajuan', 
            'totalDisetujui', 
            'totalNominal'
        ));
    }
    public function editPengaturan()
{
    $pengaturan = PengaturanAbsensi::first();
    return view('sekertaris.pengaturan.edit', compact('pengaturan'));
}

public function updatePengaturan(Request $request)
{
    $request->validate([
        'jam_masuk' => 'required',
        'potongan_per_terlambat' => 'required|numeric|min:0',
    ]);

    $pengaturan = PengaturanAbsensi::first();
    $pengaturan->update([
        'jam_masuk' => $request->jam_masuk,
        'potongan_per_terlambat' => $request->potongan_per_terlambat,
    ]);

    return back()->with('success', 'Pengaturan absensi diperbarui.');
}
public function createFingerprint()
    {
        // Ambil semua user (Karyawan & Admin) untuk dipilih
        $users = \App\Models\User::whereIn('role', ['karyawan', 'admin'])
                                 ->orderBy('name')
                                 ->get();
                                 
        return view('sekertaris.absensi.registrasi', compact('users'));
    }

    // Menyimpan ID Fingerprint ke User
    public function storeFingerprint(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'fingerprint_id' => 'required|string|unique:users,fingerprint_id', // ID harus unik
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        
        $user->update([
            'fingerprint_id' => $request->fingerprint_id
        ]);

        return redirect()->back()->with('success', "ID Fingerprint berhasil didaftarkan untuk pegawai: {$user->name}");
    }
}