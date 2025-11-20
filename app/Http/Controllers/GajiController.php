<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use App\Models\User;
use App\Models\Absensi;
use App\Models\PengaturanAbsensi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class GajiController extends Controller
{
    // ===============================================
    // BAGIAN SEKERTARIS (CRUD)
    // ===============================================

    /**
     * Menampilkan daftar gaji semua pegawai.
     */
    public function indexSekertaris()
    {
        $dataGaji = Gaji::with('user')->latest('tanggal_gaji')->paginate(10);
        return view('sekertaris.gaji.index', compact('dataGaji'));
    }

    /**
     * Menampilkan form tambah gaji.
     */
    public function create()
    {
        $pegawai = User::whereIn('role', ['admin', 'karyawan'])->get();
        return view('sekertaris.gaji.create', compact('pegawai'));
    }

    /**
     * API Internal: Hitung Potongan Otomatis
     */
    public function hitungPotongan(Request $request)
    {
        $userId = $request->user_id;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $gajiPokok = $request->gaji_pokok;

        // 1. Ambil Pengaturan
        $setting = PengaturanAbsensi::first();
        $hariKerjaStandar = $setting ? $setting->hari_kerja_per_bulan : 26; 
        $persenDendaTelat = $setting ? $setting->potongan_per_terlambat : 1.00;

        // 2. Hitung Data Absensi
        $dataAbsensi = Absensi::where('user_id', $userId)
                        ->whereMonth('waktu_absensi', $bulan)
                        ->whereYear('waktu_absensi', $tahun)
                        ->get();

        // Hitung Hari Hadir
        $jumlahHadir = $dataAbsensi->groupBy(function($date) {
            return Carbon::parse($date->waktu_absensi)->format('Y-m-d');
        })->count();

        $jumlahTerlambat = $dataAbsensi->where('status', 'terlambat')->count();

        // Hitung Alpa
        $jumlahAlpa = max(0, $hariKerjaStandar - $jumlahHadir);

        // 3. Hitung Nominal
        $gajiHarian = ($gajiPokok > 0) ? ($gajiPokok / $hariKerjaStandar) : 0;

        $potonganAlpa = $jumlahAlpa * $gajiHarian;
        $potonganTelat = $jumlahTerlambat * ($persenDendaTelat / 100) * $gajiHarian;

        return response()->json([
            'jumlah_hadir' => $jumlahHadir,
            'jumlah_alpa' => $jumlahAlpa,
            'jumlah_terlambat' => $jumlahTerlambat,
            'nominal_potongan_alpa' => round($potonganAlpa),
            'nominal_potongan_terlambat' => round($potonganTelat)
        ]);
    }

    /**
     * Menyimpan data gaji baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input (Sesuai dengan name di form HTML)
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'gaji_pokok' => 'required|numeric|min:0',
            'tanggal_gaji' => 'required|date',
            // Input hidden/readonly dari kalkulasi otomatis
            'jumlah_hadir' => 'required|integer',
            'jumlah_alpa' => 'required|integer',
            'nominal_potongan_alpa' => 'required|numeric',
            'nominal_potongan_terlambat' => 'required|numeric',
        ]);

        // 2. Ambil nilai opsional (default 0 jika kosong)
        $tunjangan = $request->tunjangan ?? 0;
        $potonganLain = $request->potongan_lain ?? 0; // Di form name="potongan_lain"
        
        // 3. Hitung Total Gaji Bersih di Server
        // Rumus: (Gaji Pokok + Tunjangan) - (Potongan Alpa + Potongan Telat + Potongan Lain)
        $totalBersih = ($request->gaji_pokok + $tunjangan) - 
                       ($request->nominal_potongan_alpa + $request->nominal_potongan_terlambat + $potonganLain);

        // 4. Simpan ke Database
        Gaji::create([
            'user_id' => $request->user_id,
            'gaji_pokok' => $request->gaji_pokok,
            'tunjangan' => $tunjangan,
            'potongan' => $potonganLain, // Mapping ke kolom 'potongan' di DB
            
            // Data Absensi
            'jumlah_hadir' => $request->jumlah_hadir,
            'jumlah_alpa' => $request->jumlah_alpa,
            'jumlah_terlambat' => $request->jumlah_terlambat ?? 0, // Gunakan 0 jika tidak ada input hidden ini
            'nominal_potongan_alpa' => $request->nominal_potongan_alpa,
            'nominal_potongan_terlambat' => $request->nominal_potongan_terlambat,
            
            'total_gaji' => $totalBersih,
            'tanggal_gaji' => $request->tanggal_gaji,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('sekertaris.gaji.index')->with('success', 'Data gaji berhasil disimpan.');
    }

    // ===============================================
    // BAGIAN PEGAWAI (View Only)
    // ===============================================

    /**
     * Menampilkan riwayat gaji untuk user yang sedang login.
     */
    public function riwayatGajiSaya()
    {
        $riwayatGaji = Gaji::where('user_id', Auth::id())
                           ->latest('tanggal_gaji')
                           ->paginate(10);

        return view('gaji.riwayat_saya', compact('riwayatGaji'));
    }
}