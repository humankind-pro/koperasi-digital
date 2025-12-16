<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    /**
     * Menampilkan daftar pinjaman aktif milik nasabah yang dihandle karyawan ini.
     */
    public function indexPinjamanAktif(Request $request)
    {
        $query = Pinjaman::with('anggota')
            ->whereIn('status', ['disetujui', 'menunggak']) // Hanya pinjaman aktif
            ->where('diajukan_oleh_user_id', Auth::id()); // Filter: Hanya nasabah binaan karyawan ini

        // (Opsional) Fitur Search Nama Nasabah
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('anggota', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        $pinjamanAktif = $query->latest('tanggal_validasi')->paginate(10);
        
        return view('karyawans.pembayaran.index_pinjaman_aktif', compact('pinjamanAktif'));
    }

    /**
     * Menyimpan data pembayaran baru dan update sisa hutang.
     */
    public function storePembayaran(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'pinjaman_id'    => 'required|exists:pinjaman,id',
            'jumlah_bayar'   => 'required|numeric|min:1',
            'tanggal_bayar'  => 'required|date',
            'bukti_transfer' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Max 2MB
        ]);

        DB::beginTransaction();
        try {
            // 2. Ambil Data Pinjaman (Lock For Update untuk mencegah race condition)
            $pinjaman = Pinjaman::where('id', $request->pinjaman_id)
                                ->where('diajukan_oleh_user_id', Auth::id()) // Keamanan: Pastikan milik karyawan ini
                                ->lockForUpdate()
                                ->firstOrFail();

            // 3. Proses Upload Bukti Transfer (Jika ada)
            $buktiPath = null;
            if ($request->hasFile('bukti_transfer')) {
                $file = $request->file('bukti_transfer');
                // Nama file unik: bayar_[ID]_[TIMESTAMP].[EXT]
                $filename = 'bayar_' . $pinjaman->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Simpan ke storage/app/public/bukti_transfer
                $buktiPath = $file->storeAs('bukti_transfer', $filename, 'public');
            }

            // 4. Simpan Data ke Tabel Pembayaran
            Pembayaran::create([
                'pinjaman_id'           => $pinjaman->id,
                'jumlah_bayar'          => $request->jumlah_bayar,
                'tanggal_bayar'         => $request->tanggal_bayar,
                'diproses_oleh_user_id' => Auth::id(), // Karyawan yang input
                'bukti_transfer_path'   => $buktiPath,
            ]);

            // 5. Logika Update Sisa Hutang
            $sisaHutangBaru = $pinjaman->sisa_hutang - $request->jumlah_bayar;

            // Mencegah nilai minus (jika bayar kelebihan)
            if ($sisaHutangBaru < 0) {
                $sisaHutangBaru = 0;
            }

            $pinjaman->sisa_hutang = $sisaHutangBaru;

            // 6. Cek Lunas
            if ($sisaHutangBaru == 0) {
                $pinjaman->status = 'lunas';
                $pinjaman->tenggat_berikutnya = null; // Hapus tenggat jika lunas

                // (Opsional) Update Skor Kredit Anggota
                $anggota = $pinjaman->anggota;
                if ($anggota) {
                    $anggota->increment('skor_kredit', 2); // Tambah poin karena lunas
                }
            } else {
                // (Opsional) Jika belum lunas, update tenggat berikutnya (misal +1 bulan)
                // $pinjaman->tenggat_berikutnya = Carbon::parse($pinjaman->tenggat_berikutnya)->addMonth();
            }

            $pinjaman->save(); // Simpan perubahan ke tabel Pinjaman

            DB::commit();

            return redirect()->route('karyawan.pinjaman.aktif')
                             ->with('success', 'Pembayaran berhasil dicatat. Sisa hutang: Rp ' . number_format($sisaHutangBaru));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mencatat pembayaran: ' . $e->getMessage())->withInput();
        }
    }
}