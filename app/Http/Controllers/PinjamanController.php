<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PinjamanController extends Controller
{
    /**
     * Halaman 1: Cari Nasabah untuk diajukan pinjaman baru
     */
    public function search()
    {
        return view('karyawans.pinjaman.search');
    }

    /**
     * Halaman 2: Form Input Pinjaman (Setelah nasabah ditemukan)
     */
    public function createExisting($anggotaId)
    {
        $anggota = Anggota::findOrFail($anggotaId);

        // Validasi: Pastikan nasabah statusnya aktif/disetujui
        if ($anggota->status !== 'disetujui' && $anggota->status !== 'aktif') {
            return redirect()->route('pinjaman.search')
                ->with('error', 'Nasabah ini belum disetujui atau statusnya tidak aktif.');
        }

        // Opsional: Cek apakah masih ada pinjaman pending?
        $existingPending = Pinjaman::where('anggota_id', $anggota->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingPending) {
            return redirect()->route('pinjaman.search')
                ->with('error', 'Nasabah ini masih memiliki pengajuan pinjaman yang belum divalidasi.');
        }

        return view('karyawans.pinjaman.create_existing', compact('anggota'));
    }

    /**
     * Proses Simpan Pengajuan Pinjaman Baru
     */
    public function storeExisting(Request $request)
    {
        $request->validate([
            'anggota_id'       => 'required|exists:anggota,id',
            'jumlah_pinjaman'  => 'required|numeric|min:100000',
            'tenor_bulan'      => 'required|integer|in:1,2,3,4,5,6,7,8,9,10,11,12,18,24,36,48,60',
            'tujuan'           => 'required|string',
            'jenis_jaminan'    => 'required|string', // Untuk digabung ke catatan
            'keterangan_jaminan'=> 'nullable|string',
        ]);

        $anggota = Anggota::findOrFail($request->anggota_id);

        // Gabungkan info jaminan ke kolom catatan (sesuai struktur DB Anda)
        $catatan = "Pengajuan Ulang (Repeat Order). Jaminan: " . $request->jenis_jaminan;
        if ($request->keterangan_jaminan) {
            $catatan .= " (" . $request->keterangan_jaminan . ")";
        }

        Pinjaman::create([
            'anggota_id'            => $anggota->id,
            'diajukan_oleh_user_id' => Auth::id(),
            
            'jumlah_pinjaman'       => $request->jumlah_pinjaman,
            'tenor_bulan'           => $request->tenor_bulan,
            'tujuan'                => $request->tujuan,
            'skor_risiko'           => $anggota->skor_kredit, // Ambil skor lama atau hitung ulang jika ada fitur update
            
            'status'                => 'pending', // Masuk antrian validasi admin
            'tanggal_pengajuan'     => now(),
            'catatan'               => $catatan,
        ]);

        return redirect()->route('pinjaman.search')
            ->with('success', 'Pengajuan pinjaman baru untuk ' . $anggota->nama . ' berhasil dikirim!');
    }
    /**
     * Menampilkan riwayat pinjaman yang diajukan oleh karyawan ini.
     * Dilengkapi fitur cari Nama/NIK.
     */
    public function riwayatPinjaman(Request $request)
    {
        // 1. Mulai Query: Ambil pinjaman yang diajukan oleh user yang sedang login
        $query = Pinjaman::with(['anggota', 'divalidasiOleh'])
                         ->where('diajukan_oleh_user_id', Auth::id());

        // 2. Logika Pencarian (Search)
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            
            // Cari di tabel relasi 'anggota'
            $query->whereHas('anggota', function($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                  ->orWhere('nik', 'like', "%{$keyword}%");
            });
        }

        // 3. Eksekusi Query
        $riwayat = $query->latest()->paginate(15)->withQueryString();

        // 4. Tampilkan View
        return view('karyawans.pinjaman.riwayat', compact('riwayat'));
    }
}