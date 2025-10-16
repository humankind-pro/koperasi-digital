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
    public function create()
    {
        // Hanya ambil anggota yang statusnya sudah 'disetujui'
        $anggotas = Anggota::where('status', 'disetujui')->orderBy('nama')->get();
        return view('karyawans.pinjaman.create', compact('anggotas'));
    }

    /**
     * Menyimpan data pengajuan pinjaman baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'anggota_id' => 'required|exists:anggota,id',
            'jumlah_pinjaman' => 'required|numeric|min:100000',
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

    // ... method lain seperti index() ...
}