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
    $riwayatValidasi = Pinjaman::where('status', '!=', 'pending')
                            ->with(['anggota', 'diajukanOleh', 'divalidasiOleh'])
                            ->latest('updated_at')
                            ->paginate(15);

    // Ambil SEMUA anggota yang disetujui untuk dropdown modal
    $semuaAnggotaDisetujui = Anggota::where('status', 'disetujui')
                                    ->orderBy('nama')
                                    ->get(['id', 'nama', 'no_ktp']);

    return view('admins.riwayat.pinjaman', compact('riwayatValidasi', 'semuaAnggotaDisetujui'));
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

    $jumlahAwalPinjaman = $pinjaman->jumlah_pinjaman;
    $jumlahDisetujui = $request->jumlah_disetujui;
    $tanggalValidasi = now();

    $pinjaman->update([
        'status' => 'disetujui',
        'divalidasi_oleh_user_id' => Auth::id(),
        'jumlah_disetujui' => $jumlahDisetujui,
        'sisa_hutang' => $jumlahAwalPinjaman,
        'tanggal_validasi' => $tanggalValidasi,
        // SET TENGGAT PERTAMA: 30 hari dari sekarang
        'tenggat_berikutnya' => $tanggalValidasi->copy()->addMonths(2), // <-- Tambahkan ini
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

    public function searchNikRiwayatAdmin(Request $request)
{
    $request->validate(['nik' => 'required|string']);
    $nik = $request->input('nik');

    $anggota = Anggota::where('no_ktp', $nik)->first();

    if (!$anggota) {
        return response()->json(['success' => false, 'message' => 'Nasabah dengan NIK tersebut tidak ditemukan.'], 404);
    }

    $riwayatPinjaman = Pinjaman::where('anggota_id', $anggota->id)
                            ->where('status', '!=', 'pending')
                            ->with([
                                'diajukanOleh:id,name',
                                'divalidasiOleh:id,name',
                                'pembayaran' // <-- Tambahkan ini untuk mengambil riwayat pembayaran
                            ])
                            ->latest('tanggal_validasi')
                            ->get();

    return response()->json([
        'success' => true,
        'anggota' => $anggota,
        'riwayat' => $riwayatPinjaman
    ]);
}

public function transferPinjaman(Request $request, Pinjaman $pinjaman)
    {
        $request->validate([
            'new_anggota_id' => 'required|exists:anggota,id',
            'alasan_transfer' => 'nullable|string',
        ]);

        // Pastikan pinjaman yang ditransfer adalah pinjaman aktif
        if ($pinjaman->status !== 'disetujui') {
            return back()->with('error', 'Hanya pinjaman yang sedang berjalan (disetujui) yang bisa dipindahkan.');
        }

        // Pastikan tidak mentransfer ke anggota yang sama
        if ($pinjaman->anggota_id == $request->new_anggota_id) {
             return back()->with('error', 'Tidak bisa mentransfer pinjaman ke nasabah yang sama.');
        }

        // Simpan ID anggota asli jika belum ada (opsional)
        $originalAnggotaId = $pinjaman->original_anggota_id ?? $pinjaman->anggota_id;

        // Update pinjaman
        $pinjaman->update([
            'anggota_id' => $request->new_anggota_id,
            'original_anggota_id' => $originalAnggotaId, // Simpan ID asli
            'ditransfer_oleh_user_id' => Auth::id(),
            'tanggal_transfer' => now(),
            'alasan_transfer' => $request->alasan_transfer,
        ]);

        return redirect()->route('admin.riwayat.pinjaman')->with('success', 'Pinjaman berhasil dipindahkan ke nasabah baru.');
    }
}