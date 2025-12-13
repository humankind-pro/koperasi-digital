<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use App\Models\User;
use App\Models\Absensi;
use App\Models\PengaturanAbsensi;
use Illuminate\Http\Request;
use App\Models\AlatAbsenLog;
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
        // 1. Ambil Input dari AJAX
        $userId    = $request->user_id;
        $bulan     = $request->bulan;
        $tahun     = $request->tahun;
        $gajiPokok = $request->gaji_pokok ?? 0;

        // 2. Cek User & ID Fingerprint
        $user = User::find($userId);
        if (!$user) return response()->json(['error' => 'Pegawai tidak ditemukan']);

        // Jika pegawai belum menghubungkan jari, anggap kehadiran 0
        if (!$user->fingerprint_id) {
            return response()->json([
                'jumlah_hadir' => 0,
                'jumlah_terlambat' => 0,
                'jumlah_alpa' => 0,
                'nominal_potongan_alpa' => 0,
                'nominal_potongan_terlambat' => 0
            ]);
        }

        // 3. Konfigurasi Aturan (Bisa dipindah ke database setting nanti)
        $jamMasukBatas = '08:00:00';
        $dendaPerTelat = 50000;  // Rp 50.000 per telat
        $dendaPerAlpa  = 100000; // Rp 100.000 per alpa

        // 4. Ambil Data Log Absensi dari IoT
        $logs = AlatAbsenLog::where('fingerprint_id', $user->fingerprint_id)
                    ->whereMonth('created_at', $bulan)
                    ->whereYear('created_at', $tahun)
                    ->whereIn('action', ['verification_success', 'enroll']) // Ambil yang sukses saja
                    ->get();

        // 5. Logika Perhitungan Absensi
        $jumlahHadir = 0;
        $jumlahTerlambat = 0;

        // Grouping berdasarkan tanggal (agar scan berkali-kali sehari tetap dihitung 1 kehadiran)
        $logsPerHari = $logs->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('Y-m-d');
        });

        foreach ($logsPerHari as $tanggal => $harian) {
            $jumlahHadir++; // Hitung kehadiran

            // Ambil waktu scan pertama hari itu
            $scanPertama = $harian->sortBy('created_at')->first();
            $jamMasuk = Carbon::parse($scanPertama->created_at)->format('H:i:s');

            // Cek Keterlambatan
            if ($jamMasuk > $jamMasukBatas) {
                $jumlahTerlambat++;
            }
        }

        // 6. Hitung Alpha (Mangkir)
        // Hitung total hari kerja efektif sampai hari ini (Senin-Jumat)
        $totalHariKerja = 0;
        $startOfMonth = Carbon::createFromDate($tahun, $bulan, 1);
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();
        
        // Batasi perhitungan sampai hari ini saja jika bulan berjalan
        $today = Carbon::now();
        if ($endOfMonth > $today) {
            $endOfMonth = $today;
        }

        // Loop hitung hari kerja (Senin-Jumat)
        for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
            if (!$date->isWeekend()) {
                $totalHariKerja++;
            }
        }

        // Jumlah Alpa = Total Hari Kerja Seharusnya - Jumlah Hadir
        // (Pastikan tidak minus)
        $jumlahAlpa = max(0, $totalHariKerja - $jumlahHadir);

        // 7. Hitung Nominal Rupiah
        $totalPotonganTelat = $jumlahTerlambat * $dendaPerTelat;
        $totalPotonganAlpa  = $jumlahAlpa * $dendaPerAlpa;

        // 8. Kirim Response JSON ke View
        return response()->json([
            'jumlah_hadir' => $jumlahHadir,
            'jumlah_terlambat' => $jumlahTerlambat,
            'jumlah_alpa' => $jumlahAlpa,
            'nominal_potongan_terlambat' => $totalPotonganTelat,
            'nominal_potongan_alpa' => $totalPotonganAlpa
        ]);
    }

    public function store(Request $request)
    {
        // Simpan data gaji final ke database
        Gaji::create($request->except(['_token']));
        return redirect()->route('sekertaris.gaji.index')->with('success', 'Data Gaji Berhasil Disimpan!');
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