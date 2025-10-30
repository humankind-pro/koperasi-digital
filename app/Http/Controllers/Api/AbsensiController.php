<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi;

class AbsensiController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi data dari ESP32
        $request->validate([
            'fingerprint_id' => 'required',
            'secret_key' => 'required', // Kunci rahasia agar aman
        ]);

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
    }
}