<?php

namespace App\Http\Controllers;

use App\Models\AlatAbsenLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SekertarisAbsensiController extends Controller
{
    public function index(Request $request)
{
    $karyawans = User::whereIn('role', ['karyawan', 'admin'])->orderBy('name')->get();

    // Query Agregasi
    $query = \App\Models\AlatAbsenLog::select(
                'fingerprint_id',
                \Illuminate\Support\Facades\DB::raw('DATE(created_at) as tanggal'),
                \Illuminate\Support\Facades\DB::raw('MIN(created_at) as jam_masuk'),
                \Illuminate\Support\Facades\DB::raw('MAX(created_at) as jam_keluar'),
                \Illuminate\Support\Facades\DB::raw('COUNT(*) as jumlah_scan')
            )
            ->with('user')
            ->groupBy('fingerprint_id', \Illuminate\Support\Facades\DB::raw('DATE(created_at)'));

    // --- PERBAIKAN FILTER KARYAWAN (GUNAKAN whereHas) ---
    // Ini akan mencari data log milik User yang ID-nya dipilih di dropdown.
    // Teknik ini otomatis mengikuti definisi relasi di Model Anda, jadi lebih aman.
    if ($request->filled('user_id')) {
        $query->whereHas('user', function($q) use ($request) {
            $q->where('id', $request->user_id);
        });
    }

    // --- Filter Tanggal (Tidak berubah) ---
    if ($request->filled('start_date')) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }
    if ($request->filled('end_date')) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    // Sorting
    $rekapAbsensi = $query->orderBy('tanggal', 'desc')
                          ->paginate(20)
                          ->withQueryString();

    return view('sekertaris.absensi.index', compact('rekapAbsensi', 'karyawans'));
}
}