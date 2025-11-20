<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi;
use App\Models\PengaturanAbsensi;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi data dari ESP32
        $request->validate([
            'fingerprint_id' => 'required',
            'secret_key' => 'required', // Kunci rahasia agar aman
        ]);

         $pengaturan = PengaturanAbsensi::first(); // Ambil baris pertama
    $jamMasukBatas = Carbon::createFromTimeString($pengaturan->jam_masuk);
    
    // Waktu Absen Sekarang
    $waktuAbsen = now();
    $jamAbsen = Carbon::createFromTimeString($waktuAbsen->toTimeString());

    // Cek Keterlambatan
    $status = 'tepat_waktu';
    $menitTelat = 0;

        // 2. Pastikan Kunci Rahasia cocok
        if ($request->secret_key !== 'KUNCIRAHASIAANDA123') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // 3. Cari user berdasarkan fingerprint_id
        $user = User::where('fingerprint_id', $request->fingerprint_id)->first();

        if (!$user) {
            // Jika sidik jari tidak terdaftar
            return response()->json(['message' => 'Fingerprint not registered'], 404);
        }

        // 4. Simpan data absensi
        Absensi::create([
            'user_id' => $user->id,
            'waktu_absensi' => now() // Gunakan waktu server
        ]);

        return response()->json(['message' => 'Attendance recorded successfully for ' . $user->name], 200);

    if ($jamAbsen->gt($jamMasukBatas)) {
        $status = 'terlambat';
        $menitTelat = $jamAbsen->diffInMinutes($jamMasukBatas);
    }

    // Simpan Data
    Absensi::create([
        'user_id' => $user->id,
        'waktu_absensi' => $waktuAbsen,
        'status' => $status,
        'menit_keterlambatan' => $menitTelat
    ]);

    return response()->json([
        'message' => 'Absensi berhasil. Status: ' . $status,
        'user' => $user->name
    ], 200);
    }
    
}