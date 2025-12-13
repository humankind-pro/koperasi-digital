<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
     * DENGAN FITUR PENCARIAN (FIXED)
     */
    public function riwayatPinjaman(Request $request)
    {
        // 1. Query Dasar
        $query = Pinjaman::where('status', '!=', 'pending')
                         // PERUBAHAN DI SINI: Tambahkan 'pembayaran'
                         ->with(['anggota', 'diajukanOleh', 'divalidasiOleh', 'pembayaran']); 

        // 2. LOGIKA PENCARIAN
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->whereHas('anggota', function($q) use ($keyword) {
                $q->where('no_ktp', 'like', "%{$keyword}%")
                  ->orWhere('nama', 'like', "%{$keyword}%");
            });
        }

        // 3. Ambil Data
        $riwayatValidasi = $query->latest('updated_at')
                                 ->paginate(15)
                                 ->withQueryString();

        // 4. Data untuk Modal Transfer
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
            'sisa_hutang' => $jumlahAwalPinjaman, // Hutang awal = jumlah pengajuan (atau disetujui, tergantung kebijakan)
            'tanggal_validasi' => $tanggalValidasi,
            // SET TENGGAT PERTAMA: 30 hari dari sekarang
            'tenggat_berikutnya' => $tanggalValidasi->copy()->addMonths(1), 
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

    // Ini API untuk pencarian via AJAX (Jika Anda menggunakannya di modal)
    public function searchNikRiwayatAdmin(Request $request)
    {
        // 1. Validasi Input
        $request->validate(['nik' => 'required|string']);
        $nik = $request->input('nik');

        // 2. Cari Anggota berdasarkan NIK
        $anggota = Anggota::where('no_ktp', $nik)->first();

        if (!$anggota) {
            return response()->json([
                'success' => false, 
                'message' => 'Nasabah dengan NIK tersebut tidak ditemukan.'
            ], 404);
        }

        // 3. Ambil Riwayat Pinjaman milik Anggota tersebut
        $riwayatPinjaman = Pinjaman::where('anggota_id', $anggota->id)
            ->where('status', '!=', 'pending')
            ->with([
                'diajukanOleh:id,name',
                'divalidasiOleh:id,name',
                'pembayaran' => function($query) {
                    $query->orderBy('tanggal_bayar', 'desc'); // Urutkan pembayaran terbaru
                }
            ])
            ->latest('tanggal_validasi')
            ->get();

        // 4. Return JSON (Agar bisa dibaca JavaScript)
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

    public function cekAi($id)
    {
        try {
            $pinjaman = \App\Models\Pinjaman::with('anggota')->findOrFail($id);
            $anggota = $pinjaman->anggota;

            // Siapkan Data
            $p1 = (float) $anggota->pendapatan_bulanan;
            $p2 = (float) $pinjaman->jumlah_pinjaman;
            $p3 = (float) $pinjaman->lama_angsuran;
            $p4 = (float) ($anggota->tanggungan ?? 0);
            
            // Riwayat
            $cekMacet = \App\Models\Pinjaman::where('anggota_id', $anggota->id)
                        ->where('id', '!=', $id)
                        ->where('status', 'macet')->exists();
            $p5 = $cekMacet ? 'Macet' : 'Lancar';

            // Dummy data (karena model ML di atas hanya pakai 6 fitur inti biar stabil)
            $p6 = 'Lainnya'; 
            $p7 = 30; 
            $p8 = 0; 
            $p9 = 1; 
            
            // Jaminan
            $p10 = 'Tidak Ada';
            if ($anggota->memiliki_jaminan) {
                $p10 = $anggota->jenis_jaminan ?? 'Ada';
            }

            $pythonExec = "C:\\Users\\ACER\\AppData\\Local\\Programs\\Python\\Python312\\python.exe";
            $scriptPath = "predict.py"; 
            $workingDir = storage_path('app/python');

            $process = new \Symfony\Component\Process\Process([
                $pythonExec, $scriptPath,
                $p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9, $p10
            ]);
            
            $process->setWorkingDirectory($workingDir);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new \Symfony\Component\Process\Exception\ProcessFailedException($process);
            }

            $output = trim($process->getOutput());
            
            if (preg_match('/\{.*\}/s', $output, $matches)) {
                return response($matches[0])->header('Content-Type', 'application/json');
            }

            return response()->json(['status' => 'error', 'pesan' => 'Output: ' . $output]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'pesan' => $e->getMessage()]);
        }
    }
}