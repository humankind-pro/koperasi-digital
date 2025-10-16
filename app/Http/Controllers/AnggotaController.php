<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnggotaController extends Controller
{
    // Menampilkan daftar nasabah yang diinput oleh karyawan ini
    public function index()
    {
        $anggotas = Anggota::where('dibuat_oleh_user_id', Auth::id())
                            ->latest()
                            ->paginate(10);
        return view('karyawans.anggota.index', compact('anggotas'));
    }

    // Menampilkan form input nasabah
    public function create()
    {
        return view('karyawans.anggota.create');
    }

    // Menyimpan pengajuan nasabah baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'no_ktp' => 'required|string|max:20|unique:anggota',
            'alamat' => 'required|string',
            'nomor_telepon' => 'required|string',
            'pekerjaan' => 'required|string|max:50',
            'pendapatan_bulanan' => 'required|numeric',
        ]);

        Anggota::create([
            // Menggunakan array_merge untuk menggabungkan data request
            // dengan data yang kita set secara manual
            ...$request->all(),
            'dibuat_oleh_user_id' => Auth::id(),
            'status' => 'pending', // Status awal adalah 'pending'
            'kode_anggota' => 'KOP-' . now()->timestamp, // Contoh kode unik
            'tanggal_bergabung' => now(),
        ]);

        return redirect()->route('anggota.index')->with('success', 'Pengajuan data nasabah baru berhasil dikirim ke Admin.');
    }

    // Anda bisa melengkapi method edit, update, destroy nanti
}