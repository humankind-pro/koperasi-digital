<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use App\Models\Pembayaran;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
                                ->paginate(10);
        
        return view('karyawans.pembayaran.index', compact('pinjamanAktif'));
    }

    /**
     * Menyimpan data pembayaran baru dan update sisa hutang serta skor kredit.
     */
    public function storePembayaran(Request $request)
{
    $request->validate([
        'pinjaman_id' => 'required|exists:pinjaman,id',
        'jumlah_bayar' => 'required|numeric|min:1',
        'tanggal_bayar' => 'required|date',
    ]);

    DB::beginTransaction();
    try {
        $pinjaman = Pinjaman::findOrFail($request->pinjaman_id);

        if (is_null($pinjaman->tenggat_berikutnya)) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error: Pinjaman ini tidak memiliki tanggal tenggat aktif. Pembayaran tidak dapat dicatat.'])->withInput();
        }

        $anggota = $pinjaman->anggota;
        $jumlahBayar = $request->jumlah_bayar;

        // Normalisasi tanggal untuk perbandingan yang akurat
        $tanggalBayar = Carbon::parse($request->tanggal_bayar)->startOfDay();
        $tanggalTenggat = Carbon::parse($pinjaman->tenggat_berikutnya)->startOfDay();

        if ($jumlahBayar > $pinjaman->sisa_hutang) {
             DB::rollBack();
             return back()->withErrors(['jumlah_bayar' => 'Jumlah bayar melebihi sisa hutang (Rp ' . number_format($pinjaman->sisa_hutang, 0, ',', '.') . ').'])->withInput();
        }

        // ===============================================
        // LOGIKA SKOR KREDIT YANG DIPERBAIKI (floor -> ceil)
        // ===============================================
        
        if ($tanggalBayar->isAfter($tanggalTenggat)) {
            $hariTelat = $tanggalBayar->diffInDays($tanggalTenggat);
            
            // MENGGUNAKAN ceil() UNTUK MEMBULATKAN KE ATAS
            // 1-7 hari telat -> ceil(1...7 / 7) -> 1 minggu
            // 8-14 hari telat -> ceil(8...14 / 7) -> 2 minggu
            $mingguTelat = ceil($hariTelat / 7); 

            if ($mingguTelat > 0) {
                $poinPenalti = $mingguTelat * 10;
                $anggota->skor_kredit -= $poinPenalti;
            }
        } 
        
        // Pastikan skor tidak kurang dari 0
        if ($anggota->skor_kredit < 0) {
            $anggota->skor_kredit = 0;
        }
        $anggota->save(); // Simpan skor kredit baru
        // ===============================================

        // 1. Catat pembayaran
        Pembayaran::create([
            'pinjaman_id' => $pinjaman->id,
            'jumlah_bayar' => $jumlahBayar,
            'tanggal_bayar' => $request->tanggal_bayar,
            'diproses_oleh_user_id' => Auth::id(),
        ]);

        // 2. Update sisa hutang
        $pinjaman->sisa_hutang -= $jumlahBayar;
        
        // 3. Jika lunas, hapus tenggat
        if ($pinjaman->sisa_hutang <= 0) {
            $pinjaman->tenggat_berikutnya = null; 
        }
        
        $pinjaman->save(); 

        DB::commit();
        return redirect()->route('karyawan.pinjaman.aktif')->with('success', 'Pembayaran berhasil dicatat.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => 'Gagal mencatat pembayaran. ' . $e->getMessage()])->withInput();
    }
}
}