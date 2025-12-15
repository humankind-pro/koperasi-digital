<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\PinjamanDisetujui; // <--- Jangan lupa import ini di paling atas
use App\Models\User;

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
     * [CORE UPDATE: SEAMLESS ONBOARDING]
     * Menyetujui Anggota & Otomatis Membuat Record di Tabel Pinjaman
     */
    public function setujui(Anggota $anggota)
    {
        DB::beginTransaction();

        try {
            // 1. Update Status Anggota menjadi Disetujui/Aktif
            $anggota->update([
                'status' => 'disetujui',
                'divalidasi_oleh_user_id' => Auth::id(),
                'tanggal_bergabung' => now(),
            ]);

            // 2. OTOMATIS BUAT PINJAMAN PERTAMA
            // Cek apakah nasabah ini memiliki rencana pinjaman awal?
            if ($anggota->jumlah_pinjaman > 0) {
                
                // Siapkan data jaminan untuk dimasukkan ke kolom 'catatan'
                // karena tabel pinjaman tidak punya kolom jaminan khusus.
                $infoJaminan = "Jaminan: " . ($anggota->jaminan ?? '-');
                if ($anggota->keterangan_jaminan) {
                    $infoJaminan .= " (" . $anggota->keterangan_jaminan . ")";
                }
                $catatanAuto = "Pinjaman perdana (Auto-create saat validasi nasabah). " . $infoJaminan;

                Log::info("🔄 Auto-Creating Loan for Anggota ID: " . $anggota->id);

                Pinjaman::create([
                    // Relasi
                    'anggota_id'            => $anggota->id,
                    'diajukan_oleh_user_id' => $anggota->dibuat_oleh_user_id, // Kredit sales yg input
                    
                    // Mapping Data dari Tabel Anggota ke Tabel Pinjaman
                    'jumlah_pinjaman'       => $anggota->jumlah_pinjaman,
                    'tenor_bulan'           => $anggota->tenor,           // Sesuai kolom DB Anda
                    'tujuan'                => $anggota->tujuan_pinjaman, // Sesuai kolom DB Anda
                    'skor_risiko'           => $anggota->skor_kredit,     // Ambil skor dari anggota
                    
                    // Status & Tanggal
                    'status'                => 'pending', // Masuk antrian validasi pinjaman
                    'tanggal_pengajuan'     => now(),
                    
                    // Informasi Tambahan
                    'catatan'               => $catatanAuto,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.validasi.nasabah.index')
                ->with('success', 'Nasabah disetujui & Pinjaman awal otomatis dibuat (Cek menu Validasi Pinjaman).');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Gagal Setujui Anggota: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    public function tolak(Anggota $anggota)
    {
        $anggota->update([
            'status' => 'ditolak',
            'divalidasi_oleh_user_id' => Auth::id(),
        ]);

        return redirect()->route('admin.validasi.nasabah.index')->with('success', 'Pengajuan anggota telah ditolak.');
    }

    /**
     * Halaman Validasi Pinjaman
     * Menampilkan pinjaman (termasuk yang baru dibuat otomatis) yang statusnya 'pending'
     */
    public function validasiPinjaman()
    {
        $pengajuanPinjaman = Pinjaman::where('status', 'pending')
                                    ->with(['anggota', 'diajukanOleh'])
                                    ->latest()
                                    ->paginate(10);
        
        return view('admins.validasi.pinjaman', compact('pengajuanPinjaman'));
    }

    /**
     * Proses Persetujuan Pinjaman (Pencairan)
     */
    public function setujuiPinjaman(Request $request, Pinjaman $pinjaman)
    {
        $request->validate(['jumlah_disetujui' => 'required|numeric|min:0']);

        // ... (Logika update status pinjaman Anda yang sudah ada tetap sama) ...
        $jumlahDisetujui = $request->jumlah_disetujui;
        $tanggalValidasi = now();
        $tenggatBerikutnya = $tanggalValidasi->copy()->addMonth();

        $pinjaman->update([
            'status'                  => 'disetujui',
            'divalidasi_oleh_user_id' => Auth::id(),
            'jumlah_disetujui'        => $jumlahDisetujui,
            'sisa_hutang'             => $jumlahDisetujui,
            'tanggal_validasi'        => $tanggalValidasi,
            'tenggat_berikutnya'      => $tenggatBerikutnya,
        ]);

        // === TAMBAHAN: KIRIM NOTIFIKASI KE KARYAWAN ===
        // Cari karyawan yang mengajukan pinjaman ini
        $karyawan = User::find($pinjaman->diajukan_oleh_user_id);
        
        if ($karyawan) {
            $karyawan->notify(new PinjamanDisetujui($pinjaman));
        }
        // ==============================================

        return redirect()->route('admin.validasi.pinjaman.index')
            ->with('success', 'Pinjaman berhasil disetujui dan notifikasi dikirim ke karyawan.');
    }

    public function tolakPinjaman(Pinjaman $pinjaman)
    {
        $pinjaman->update([
            'status' => 'ditolak',
            'divalidasi_oleh_user_id' => Auth::id(),
            'tanggal_validasi' => now(),
        ]);

        return redirect()->route('admin.validasi.pinjaman.index')
            ->with('success', 'Pengajuan pinjaman telah ditolak.');
    }

    /**
     * Riwayat Pinjaman (Search by NIK updated)
     */
    public function riwayatPinjaman(Request $request)
    {
        $query = Pinjaman::where('status', '!=', 'pending')
                         ->with(['anggota', 'diajukanOleh', 'divalidasiOleh']); 

        // SEARCH LOGIC
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->whereHas('anggota', function($q) use ($keyword) {
                $q->where('nik', 'like', "%{$keyword}%") 
                  ->orWhere('nama', 'like', "%{$keyword}%");
            });
        }

        $riwayatValidasi = $query->latest('updated_at')
                                 ->paginate(15)
                                 ->withQueryString();

        // Data untuk dropdown filter (opsional)
        $semuaAnggotaDisetujui = Anggota::where('status', 'disetujui')
                                        ->orderBy('nama')
                                        ->get(['id', 'nama', 'nik']);

        return view('admins.riwayat.pinjaman', compact('riwayatValidasi', 'semuaAnggotaDisetujui'));
    }

    // API AJAX: Search NIK
    public function searchNikRiwayatAdmin(Request $request)
    {
        $request->validate(['nik' => 'required|string']);
        $nik = $request->input('nik');

        // Cari Anggota by NIK
        $anggota = Anggota::where('nik', $nik)->first();

        if (!$anggota) {
            return response()->json([
                'success' => false, 
                'message' => 'Nasabah dengan NIK tersebut tidak ditemukan.'
            ], 404);
        }

        // Cari History Pinjaman
        $riwayatPinjaman = Pinjaman::where('anggota_id', $anggota->id)
            ->where('status', '!=', 'pending')
            ->with([
                'diajukanOleh:id,name',
                'divalidasiOleh:id,name',
                // Hapus relasi 'pembayaran' jika belum ada model/tabelnya
                // 'pembayaran' => function($query) { $query->orderBy('tanggal_bayar', 'desc'); }
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

        if ($pinjaman->status !== 'disetujui') {
            return back()->with('error', 'Hanya pinjaman aktif yang bisa dipindahkan.');
        }

        if ($pinjaman->anggota_id == $request->new_anggota_id) {
             return back()->with('error', 'Tidak bisa mentransfer ke nasabah yang sama.');
        }

        $originalAnggotaId = $pinjaman->original_anggota_id ?? $pinjaman->anggota_id;

        $pinjaman->update([
            'anggota_id' => $request->new_anggota_id,
            'original_anggota_id' => $originalAnggotaId,
            'ditransfer_oleh_user_id' => Auth::id(),
            'tanggal_transfer' => now(),
            'alasan_transfer' => $request->alasan_transfer,
        ]);

        return redirect()->route('admin.riwayat.pinjaman')->with('success', 'Pinjaman berhasil dipindahkan.');
    }
}