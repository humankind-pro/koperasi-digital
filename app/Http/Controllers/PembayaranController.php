<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use App\Models\Pembayaran;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage; // <-- TAMBAHKAN INI

class PembayaranController extends Controller
{
    // ... method indexPinjamanAktif() tidak berubah ...
    public function indexPinjamanAktif()
{
    $pinjamanAktif = Pinjaman::where('status', 'disetujui')
                            ->where('diajukan_oleh_user_id', Auth::id()) // <-- KUNCI PEMBATASAN
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
        // 1. Tambahkan validasi untuk file gambar
        $request->validate([
            'pinjaman_id' => 'required|exists:pinjaman,id',
            'jumlah_bayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
            'bukti_transfer' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Maks 2MB
        ]);

        DB::beginTransaction();
        try {
            $pinjaman = Pinjaman::where('id', $request->pinjaman_id)
                            ->where('diajukan_oleh_user_id', Auth::id()) // <-- TAMBAHAN KEAMANAN
                            ->firstOrFail(); // Gunakan firstOrFail agar 404 jika mencoba bayar punya orang lain

            // ... (Guard, logika skor kredit, dll. tidak berubah) ...
            if (is_null($pinjaman->tenggat_berikutnya)) { /* ... */ }
            $anggota = $pinjaman->anggota;
            // ... (dst) ...
            $anggota->save();


            // ===============================================
            // LOGIKA UPLOAD BUKTI TRANSFER
            // ===============================================
            $buktiPath = null;
            if ($request->hasFile('bukti_transfer')) {
                // Buat nama file unik: [id_pinjaman]_[timestamp].[ekstensi]
                $filename = $pinjaman->id . '_' . time() . '.' . $request->file('bukti_transfer')->getClientOriginalExtension();
                
                // Simpan file ke 'storage/app/public/bukti_transfer'
                // $buktiPath akan berisi 'bukti_transfer/namafile.jpg'
                $buktiPath = $request->file('bukti_transfer')->storeAs('bukti_transfer', $filename, 'public');
            }
            // ===============================================


            // 1. Catat pembayaran
            Pembayaran::create([
                'pinjaman_id' => $pinjaman->id,
                'jumlah_bayar' => $request->jumlah_bayar,
                'tanggal_bayar' => $request->tanggal_bayar,
                'diproses_oleh_user_id' => Auth::id(),
                'bukti_transfer_path' => $buktiPath, // <-- Simpan path-nya
            ]);

            // ... (Logika update sisa hutang & tenggat tidak berubah) ...
            $pinjaman->sisa_hutang -= $request->jumlah_bayar;
            if ($pinjaman->sisa_hutang <= 0) { /* ... */ }
            $pinjaman->save(); 

            DB::commit();
            return redirect()->route('karyawan.pinjaman.aktif')->with('success', 'Pembayaran berhasil dicatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal mencatat pembayaran. ' . $e->getMessage()])->withInput();
        }
    }
}