<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Absensi;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Ambil data absensi, kelompokkan per hari,
        // dan ambil jam masuk (MIN) dan jam pulang (MAX)
        $riwayatAbsensi = Absensi::where('user_id', $userId)
            ->select(
                DB::raw('DATE(waktu_absensi) as tanggal'),
                DB::raw('MIN(waktu_absensi) as jam_masuk'),
                DB::raw('MAX(waktu_absensi) as jam_pulang')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc') // Tampilkan yang terbaru di atas
            ->paginate(10); // 10 hari per halaman

        return view('karyawans.absensi', compact('riwayatAbsensi'));
    }
}