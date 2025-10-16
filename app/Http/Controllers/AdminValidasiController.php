<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminValidasiController extends Controller
{
    /**
     * Menampilkan daftar semua pengajuan anggota yang 'pending'.
     */
    public function index()
    {
        $pengajuanAnggota = Anggota::where('status', 'pending')
                                ->with('dibuatOleh')
                                ->latest()
                                ->paginate(10);
                                
        return view('admins.validasi.index', compact('pengajuanAnggota'));
    }

    /**
     * Memproses persetujuan pengajuan anggota.
     */
    public function setujui(Anggota $anggota)
    {
        $anggota->update([
            'status' => 'disetujui',
            'divalidasi_oleh_user_id' => Auth::id(),
        ]);

        return redirect()->route('admin.validasi.nasabah.index')->with('success', 'Pengajuan anggota berhasil disetujui.');
    }

    /**
     * Memproses penolakan pengajuan anggota.
     */
    public function tolak(Anggota $anggota)
    {
        $anggota->update([
            'status' => 'ditolak',
            'divalidasi_oleh_user_id' => Auth::id(),
        ]);

        return redirect()->route('admin.validasi.nasabah.index')->with('success', 'Pengajuan anggota telah ditolak.');
    }

    /**
     * Menampilkan riwayat semua pinjaman yang telah divalidasi.
     */
    public function riwayatPinjaman()
    {
        // Menggunakan whereRaw untuk menghapus spasi sebelum membandingkan
        $riwayatValidasi = Pinjaman::whereRaw("TRIM(status) != 'pending'")
                                ->with(['anggota', 'diajukanOleh', 'divalidasiOleh'])
                                ->latest('updated_at')
                                ->paginate(15);

        return view('admins.riwayat.pinjaman', compact('riwayatValidasi'));
    }

    /**
     * Menampilkan daftar pengajuan pinjaman yang 'pending'.
     */
    public function validasiPinjaman()
{
    // Menggunakan TRIM untuk mengabaikan spasi tersembunyi
    $pengajuanPinjaman = Pinjaman::whereRaw('TRIM(status) = ?', ['pending'])
                            ->with(['anggota', 'diajukanOleh'])
                            ->latest()
                            ->paginate(10);
    
    return view('admins.validasi.pinjaman', compact('pengajuanPinjaman'));
}

    /**
     * Memproses persetujuan pinjaman.
     */
    public function setujuiPinjaman(Request $request, Pinjaman $pinjaman)
    {
        $request->validate(['jumlah_disetujui' => 'required|numeric|min:0']);

        $pinjaman->update([
            'status' => 'disetujui',
            'divalidasi_oleh_user_id' => Auth::id(),
            'jumlah_disetujui' => $request->jumlah_disetujui,
            'tanggal_validasi' => now(),
        ]);

        return redirect()->route('admin.validasi.pinjaman.index')->with('success', 'Pengajuan pinjaman berhasil disetujui.');
    }

    /**
     * Memproses penolakan pinjaman.
     */
    public function tolakPinjaman(Pinjaman $pinjaman)
    {
        $pinjaman->update([
            'status' => 'ditolak',
            'divalidasi_oleh_user_id' => Auth::id(),
            'tanggal_validasi' => now(),
        ]);

        return redirect()->route('admin.validasi.pinjaman.index')->with('success', 'Pengajuan pinjaman telah ditolak.');
    }
}