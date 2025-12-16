<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use App\Models\User;
use App\Models\AlatAbsenLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class GajiController extends Controller
{
    // ... method index & create biarkan sama ...
    public function indexSekertaris() {
        $dataGaji = Gaji::with('user')->latest('tanggal_gaji')->paginate(10);
        return view('sekertaris.gaji.index', compact('dataGaji'));
    }

    public function create() {
        $pegawai = User::whereIn('role', ['admin', 'karyawan'])->get();
        return view('sekertaris.gaji.create', compact('pegawai'));
    }

    /**
     * API: Hitung Pendapatan Berdasarkan Kehadiran (No Work No Pay)
     */
    public function hitungPotongan(Request $request)
    {
        $userId     = $request->user_id;
        $bulan      = $request->bulan;
        $tahun      = $request->tahun;
        // Input ini kita anggap sebagai GAJI PER HARI (Rate Harian)
        $rateHarian = $request->gaji_pokok ?? 0; 

        $user = User::find($userId);
        if (!$user) return response()->json(['error' => 'Pegawai tidak ditemukan']);

        // Jika tidak ada data fingerprint, gaji otomatis 0
        if (!$user->fingerprint_id) {
            return response()->json([
                'jumlah_hadir' => 0,
                'jumlah_terlambat' => 0,
                'total_gaji_bersih' => 0, // 0 Rupiah
                'rincian' => []
            ]);
        }

        // 1. Ambil Log Absensi Valid
        $logs = AlatAbsenLog::where('fingerprint_id', $user->fingerprint_id)
                    ->whereMonth('created_at', $bulan)
                    ->whereYear('created_at', $tahun)
                    ->whereIn('action', ['verification_success', 'enroll'])
                    ->orderBy('created_at', 'asc')
                    ->get();

        // 2. Grouping Per Hari
        $logsPerHari = $logs->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('Y-m-d');
        });

        // 3. Logika Perhitungan Akumulasi
        $totalPendapatan = 0;
        $jumlahHadir = 0;
        $jumlahTerlambat = 0;
        $rincianPendapatan = [];

        foreach ($logsPerHari as $tanggal => $harian) {
            $dateObj = Carbon::parse($tanggal);

            // SKIP HARI MINGGU (Jika ada yang iseng absen hari minggu, tidak dihitung gaji)
            // Kecuali jika kebijakan Anda lembur minggu dibayar, hapus baris ini.
            if ($dateObj->isSunday()) {
                continue; 
            }

            $jumlahHadir++;
            
            // Ambil scan pertama (Jam Masuk)
            $scanPertama = $harian->sortBy('created_at')->first();
            $jamMasuk = Carbon::parse($scanPertama->created_at)->format('H:i:s');

            // --- ATURAN JAM ---
            // Batas Toleransi: 09:00:00
            // Jika masuk <= 09:00 -> Gaji Full (100%)
            // Jika masuk > 09:00  -> Gaji Setengah (50%) - Anggap terlambat

            $pendapatanHariIni = 0;
            $status = '';

            if ($jamMasuk > '09:00:00') {
                // TERLAMBAT
                $jumlahTerlambat++;
                $pendapatanHariIni = $rateHarian * 0.5; // Dapat 50%
                $status = "Terlambat ($jamMasuk) - Gaji 50%";
            } else {
                // TEPAT WAKTU (Sebelum jam 9)
                $pendapatanHariIni = $rateHarian * 1; // Dapat 100%
                $status = "Tepat Waktu ($jamMasuk) - Gaji 100%";
            }

            // Tambahkan ke total dompet
            $totalPendapatan += $pendapatanHariIni;

            // Simpan rincian untuk ditampilkan
            $rincianPendapatan[] = [
                'tanggal' => $dateObj->format('d M'),
                'status' => $status,
                'nominal' => number_format($pendapatanHariIni, 0, ',', '.')
            ];
        }

        // 4. Kirim Response
        return response()->json([
            'jumlah_hadir' => $jumlahHadir,
            'jumlah_terlambat' => $jumlahTerlambat,
            'rate_harian' => $rateHarian,
            'total_gaji_bersih' => $totalPendapatan, // Total Akhir
            'rincian' => $rincianPendapatan
        ]);
    }

    public function store(Request $request)
    {
        Gaji::create($request->except(['_token']));
        return redirect()->route('sekertaris.gaji.index')->with('success', 'Data Gaji Tersimpan');
    }
    
    // ... method riwayatGajiSaya ...
    public function riwayatGajiSaya()
    {
        $riwayatGaji = Gaji::where('user_id', Auth::id())
                           ->latest('tanggal_gaji')
                           ->paginate(10);

        return view('gaji.riwayat_saya', compact('riwayatGaji'));
    }
}