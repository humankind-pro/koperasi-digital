<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pinjaman;
use App\Models\Anggota;
use Illuminate\Support\Facades\Auth;

class PinjamanController extends Controller
{
    /**
     * Menampilkan formulir "Ajukan Pinjaman".
     */
    // 1. METHOD INDEX (Riwayat Pengajuan Saya)
public function index()
{
    // Sudah benar (diajukan_oleh_user_id), tapi mari kita pastikan
    $riwayatPinjaman = Pinjaman::where('diajukan_oleh_user_id', Auth::id()) // <-- Pastikan ini Auth::id()
        ->with('anggota')
        ->latest('tanggal_pengajuan')
        ->paginate(10);
        
    return view('karyawans.pinjaman.index', compact('riwayatPinjaman'));
}

// 2. METHOD CREATE (Form Pengajuan - Dropdown/Pilihan)
public function create()
{
    // Hanya ambil anggota milik karyawan ini yang disetujui
    $anggotas = Anggota::where('status', 'disetujui')
                        ->where('dibuat_oleh_user_id', Auth::id()) // <-- KUNCI PEMBATASAN
                        ->orderBy('nama')
                        ->get();
                        
    return view('karyawans.pinjaman.create', compact('anggotas'));
}

    /**
     * Menyimpan data pengajuan pinjaman baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'anggota_id' => 'required|exists:anggota,id',
            'jumlah_pinjaman' => 'required|numeric|min:100000|max:20000000',
            'tenor_bulan' => 'required|integer|min:1',
            'tujuan' => 'required|string',
        ]);

        Pinjaman::create([
            'anggota_id' => $request->anggota_id,
            'diajukan_oleh_user_id' => Auth::id(),
            'jumlah_pinjaman' => $request->jumlah_pinjaman,
            'tenor_bulan' => $request->tenor_bulan,
            'tujuan' => $request->tujuan,
            'status' => 'pending', // <-- Status di-set sebagai 'pending'
            'tanggal_pengajuan' => now(),
        ]);

        // Redirect ke dashboard karyawan dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'Pengajuan pinjaman berhasil dikirim untuk divalidasi.');
    }
    
public function riwayatPinjaman(Request $request)
    {
        // 1. Query Dasar: Hanya pinjaman yang diajukan oleh Karyawan ini
        $query = Pinjaman::where('diajukan_oleh_user_id', \Illuminate\Support\Facades\Auth::id())
                         // PENTING: Load relasi 'pembayaran' agar bisa dilihat di modal
                         ->with(['anggota', 'pembayaran']);

        // 2. Logika Pencarian (Sama seperti Admin)
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->whereHas('anggota', function($q) use ($keyword) {
                $q->where('no_ktp', 'like', "%{$keyword}%")
                  ->orWhere('nama', 'like', "%{$keyword}%");
            });
        }

        // 3. Ambil Data (Pagination)
        $riwayat = $query->latest('updated_at')
                         ->paginate(10)
                         ->withQueryString();

        return view('karyawans.pinjaman.riwayat', compact('riwayat'));
    }
    // ... method lain seperti index() ...
}