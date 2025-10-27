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
        $request->validate([
            'nama' => 'required|string|max:100',
            'no_ktp' => 'required|string|max:20|unique:anggota',
            'alamat' => 'required|string',
            'nomor_telepon' => 'required|string',
            'pekerjaan' => 'required|string|max:50',
            'pendapatan_bulanan' => 'required|numeric',
        ]);

        Anggota::create([
            ...$request->all(),
            'dibuat_oleh_user_id' => Auth::id(),
            'status' => 'pending',
            'kode_anggota' => 'KOP-' . now()->timestamp,
            'tanggal_bergabung' => now(),
        ]);

        return redirect()->route('anggota.index')->with('success', 'Pengajuan data nasabah baru berhasil dikirim ke Admin.');
    }

    
    public function searchByNik(Request $request)
    {
        $request->validate(['nik' => 'required|string']);
        $nik = $request->input('nik');

        $anggota = Anggota::where('no_ktp', $nik)
                          ->where('status', 'disetujui')
                          ->first(['id', 'nama']);

        if ($anggota) {
            return response()->json(['success' => true, 'anggota' => $anggota]);
        } else {
            return response()->json(['success' => false, 'message' => 'Nasabah dengan NIK tersebut tidak ditemukan atau belum disetujui.'], 404);
        }
    }

    public function showSearchRiwayatForm()
    {
        return view('karyawans.anggota.search_riwayat');
    }

    /**
     * Mencari anggota berdasarkan NIK dan mengembalikan riwayat pinjamannya.
     */
    public function searchNikRiwayat(Request $request)
    {
        $request->validate(['nik' => 'required|string']);
        $nik = $request->input('nik');

        $anggota = Anggota::where('no_ktp', $nik)->first();

        if (!$anggota) {
            return response()->json(['success' => false, 'message' => 'Nasabah dengan NIK tersebut tidak ditemukan.'], 404);
        }

        // Kode ini sekarang akan berfungsi karena Model Pinjaman sudah diimpor
        $riwayatPinjaman = Pinjaman::where('anggota_id', $anggota->id)
                                ->with(['diajukanOleh', 'divalidasiOleh'])
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
        $anggotas = Anggota::with('dibuatOleh') // Ambil info karyawan pembuat
                          ->latest()
                          ->paginate(15);
        return view('super_admin.anggota.index', compact('anggotas'));
    }

    /**
     * Menampilkan form edit anggota untuk Super Admin.
     */
    public function edit(Anggota $anggota)
    {
        // Pastikan hanya Super Admin yang bisa akses (bisa ditambah Gate/Policy nanti)
        // if (Auth::user()->role !== 'super_admin') { abort(403); }

        return view('super_admin.anggota.edit', compact('anggota'));
    }

    /**
     * Memperbarui data anggota.
     */
    public function update(Request $request, Anggota $anggota)
    {
        // Pastikan hanya Super Admin yang bisa akses
        // if (Auth::user()->role !== 'super_admin') { abort(403); }

        $validatedData = $request->validate([
            'nama' => 'required|string|max:100',
            // Gunakan 'unique' dengan pengecualian ID saat ini
            'no_ktp' => 'required|string|max:20|unique:anggota,no_ktp,'.$anggota->id, 
            'alamat' => 'required|string',
            'nomor_telepon' => 'required|string',
            'pekerjaan' => 'required|string|max:50',
            'pendapatan_bulanan' => 'required|numeric',
            'status' => 'required|in:pending,disetujui,ditolak', // Super Admin bisa ubah status
        ]);

        $anggota->update($validatedData);

        return redirect()->route('superadmin.anggota.index')->with('success', 'Data anggota berhasil diperbarui.');
    }

    /**
     * Menghapus data anggota.
     */
    public function destroy(Anggota $anggota)
    {
        // Pastikan hanya Super Admin yang bisa akses
        // if (Auth::user()->role !== 'super_admin') { abort(403); }
        
        try {
            $anggota->delete();
            return redirect()->route('superadmin.anggota.index')->with('success', 'Data anggota berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani jika ada error foreign key (misal anggota punya pinjaman aktif)
            return redirect()->route('superadmin.anggota.index')->with('error', 'Gagal menghapus anggota. Pastikan tidak ada data pinjaman terkait.');
        }
    }
}