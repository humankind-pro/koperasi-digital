<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <-- Untuk transaksi

class PembayaranController extends Controller
{
    /**
     * Menampilkan halaman daftar pinjaman yang aktif (disetujui).
     */
    public function indexPinjamanAktif()
    {
        $pinjamanAktif = Pinjaman::where('status', 'disetujui')
                            ->with('anggota')
                            ->latest('tanggal_validasi')
                            ->paginate(10); // Atau sesuai kebutuhan

        return view('karyawans.pembayaran.index', compact('pinjamanAktif'));
    }

    /**
     * Menyimpan data pembayaran baru dan update sisa hutang.
     */
    public function storePembayaran(Request $request)
    {
        $request->validate([
            'pinjaman_id' => 'required|exists:pinjaman,id',
            'jumlah_bayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
        ]);

        // Gunakan transaksi database untuk memastikan konsistensi
        DB::beginTransaction();
        try {
            $pinjaman = Pinjaman::findOrFail($request->pinjaman_id);
            $jumlahBayar = $request->jumlah_bayar;

            // Pastikan pembayaran tidak melebihi sisa hutang
            if ($jumlahBayar > $pinjaman->sisa_hutang) {
                // Batalkan transaksi dan kembalikan dengan error
                DB::rollBack();
                return back()->withErrors(['jumlah_bayar' => 'Jumlah bayar melebihi sisa hutang (Rp ' . number_format($pinjaman->sisa_hutang, 0, ',', '.') . ').'])->withInput();
            }

            // 1. Catat pembayaran
            Pembayaran::create([
                'pinjaman_id' => $pinjaman->id,
                'jumlah_bayar' => $jumlahBayar,
                'tanggal_bayar' => $request->tanggal_bayar,
                'diproses_oleh_user_id' => Auth::id(),
            ]);

            // 2. Update sisa hutang
            $pinjaman->sisa_hutang -= $jumlahBayar;
            $pinjaman->save();

            // Jika semua berhasil, commit transaksi
            DB::commit();

            return redirect()->route('karyawan.pinjaman.aktif')->with('success', 'Pembayaran berhasil dicatat.');

        } catch (\Exception $e) {
            // Jika terjadi error, batalkan semua perubahan
            DB::rollBack();
            // Log error atau tampilkan pesan error umum
            return back()->withErrors(['error' => 'Gagal mencatat pembayaran. Silakan coba lagi.'])->withInput();
        }
    }
}