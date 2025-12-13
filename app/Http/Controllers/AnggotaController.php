<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pinjaman;

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
        // 1. Validasi Input Lengkap
        $request->validate([
            'nama' => 'required|string|max:100',
            'no_ktp' => 'required|numeric|unique:anggota,no_ktp',
            'umur' => 'required|integer|min:17|max:90',
            'pendidikan' => 'required|string',
            'tanggungan' => 'required|integer|min:0',
            'status_tempat_tinggal' => 'required|string',
            'alamat' => 'required|string',
            'nomor_telepon' => 'required|string',
            'pekerjaan' => 'required|string',
            'lama_bekerja_tahun' => 'required|integer|min:0',
            'pendapatan_bulanan' => 'required|numeric|min:0',
            'pengeluaran_bulanan' => 'required|numeric|min:0',
            'tujuan_pinjaman_preferensi' => 'required|string',
            
            // Validasi Jaminan
            'memiliki_jaminan' => 'nullable',
            'jenis_jaminan' => 'nullable|required_if:memiliki_jaminan,1',
            'deskripsi_jaminan_lainnya' => 'nullable|required_if:jenis_jaminan,LAINNYA',
        ]);

        // 2. Logic Jaminan "Lainnya"
        $finalJenisJaminan = $request->jenis_jaminan;
        if ($request->jenis_jaminan == 'LAINNYA' && $request->deskripsi_jaminan_lainnya) {
            $finalJenisJaminan = $request->deskripsi_jaminan_lainnya;
        }
        if (!$request->has('memiliki_jaminan')) {
            $finalJenisJaminan = null;
        }

        // 3. Simpan ke Database
        Anggota::create([
            'nama' => $request->nama,
            'no_ktp' => $request->no_ktp,
            'umur' => $request->umur,
            'pendidikan' => $request->pendidikan,
            'tanggungan' => $request->tanggungan,
            'status_tempat_tinggal' => $request->status_tempat_tinggal,
            'alamat' => $request->alamat,
            'nomor_telepon' => $request->nomor_telepon,
            'pekerjaan' => $request->pekerjaan,
            'lama_bekerja_tahun' => $request->lama_bekerja_tahun,
            'pendapatan_bulanan' => $request->pendapatan_bulanan,
            'pengeluaran_bulanan' => $request->pengeluaran_bulanan,
            'tujuan_pinjaman_preferensi' => $request->tujuan_pinjaman_preferensi,
            'memiliki_jaminan' => $request->has('memiliki_jaminan'),
            'jenis_jaminan' => $finalJenisJaminan,
            'dibuat_oleh_user_id' => Auth::id(),
            'status' => 'pending',
            'kode_anggota' => 'KOP-' . now()->timestamp,
            'tanggal_bergabung' => now(),
        ]);

        return redirect()->route('anggota.index')->with('success', 'Data Anggota Lengkap Berhasil Disimpan.');
    }

    /**
     * API Pencarian Nasabah untuk Form Pengajuan Pinjaman
     * Method ini dipanggil oleh AJAX di create.blade.php
     */
    public function cariNasabahByNik(Request $request)
    {
        $request->validate(['nik' => 'required|string']);
        $nik = $request->input('nik');

        // Cari Anggota
        $anggota = Anggota::where('no_ktp', $nik)
                          ->where('dibuat_oleh_user_id', Auth::id()) // Hanya nasabah buatan karyawan ini
                          ->first(); // Ambil SEMUA field (jangan cuma id & nama)

        if ($anggota) {
            // Cek Status
            if ($anggota->status !== 'disetujui') {
                return response()->json([
                    'success' => false, 
                    'message' => 'Nasabah ditemukan, tetapi statusnya masih Pending/Ditolak.'
                ]);
            }

            return response()->json(['success' => true, 'anggota' => $anggota]);
        } else {
            return response()->json(['success' => false, 'message' => 'Nasabah tidak ditemukan di daftar Anda.'], 404);
        }
    }

    // --- Method Tambahan Lainnya ---

    public function showSearchRiwayatForm()
    {
        return view('karyawans.anggota.search_riwayat');
    }

    public function searchNikRiwayat(Request $request)
    {
        $request->validate(['nik' => 'required|string']);
        $nik = $request->input('nik');

        $anggota = Anggota::where('no_ktp', $nik)
                          ->where('dibuat_oleh_user_id', Auth::id())
                          ->first();

        if (!$anggota) {
            return response()->json(['success' => false, 'message' => 'Nasabah tidak ditemukan.'], 404);
        }

        $riwayatPinjaman = Pinjaman::where('anggota_id', $anggota->id)
                                    ->with(['diajukanOleh:id,name', 'divalidasiOleh:id,name', 'pembayaran'])
                                    ->latest('tanggal_pengajuan')
                                    ->get(); 

        return response()->json([
            'success' => true,
            'anggota' => $anggota,
            'riwayat' => $riwayatPinjaman
        ]);
    }

    public function indexForSuperAdmin()
    {
        $anggotas = Anggota::with('dibuatOleh')
                           ->latest()
                           ->paginate(15);
        return view('super_admin.anggota.index', compact('anggotas'));
    }

    public function edit(Anggota $anggota)
    {
        return view('super_admin.anggota.edit', compact('anggota'));
    }

    public function update(Request $request, Anggota $anggota)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:100',
            'no_ktp' => 'required|string|max:20|unique:anggota,no_ktp,'.$anggota->id, 
            'alamat' => 'required|string',
            'nomor_telepon' => 'required|string',
            'pekerjaan' => 'required|string|max:50',
            'pendapatan_bulanan' => 'required|numeric',
            'status' => 'required|in:pending,disetujui,ditolak',
        ]);

        $anggota->update($validatedData);

        return redirect()->route('superadmin.anggota.index')->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(Anggota $anggota)
    {
        try {
            $anggota->delete();
            return redirect()->route('superadmin.anggota.index')->with('success', 'Data anggota berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('superadmin.anggota.index')->with('error', 'Gagal menghapus anggota. Pastikan tidak ada data pinjaman terkait.');
        }
    }
}