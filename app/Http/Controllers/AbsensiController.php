<?php

namespace App\Http\Controllers;

use App\Models\AlatAbsenLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    /**
     * FITUR 1: HALAMAN KARYAWAN
     * Hanya menampilkan riwayat absen milik user yang login
     */
    public function index()
    {
        $user = Auth::user();

        // Jika user belum punya ID Fingerprint, tampilkan kosong
        if (!$user->fingerprint_id) {
            $riwayat = collect([]); // Koleksi kosong
        } else {
            // Ambil Log yang ID-nya sama dengan User DAN statusnya SUKSES
            $riwayat = AlatAbsenLog::where('fingerprint_id', $user->fingerprint_id)
                ->where('action', 'verification_success') // Filter hanya yang sukses
                ->latest()
                ->paginate(15);
        }

        return view('karyawans.absensi.index', compact('riwayat'));
    }

    /**
     * FITUR 2: HALAMAN SEKERTARIS (MONITORING & PENDAFTARAN)
     * Menampilkan semua log dan deteksi jari asing
     */
    public function monitor() // Bisa juga dinamakan 'createFingerprint' sesuai route kamu
    {
        // 1. Ambil Log Live
        $logs = \App\Models\AlatAbsenLog::with('user')->latest()->paginate(10);

        // 2. DETEKSI OTOMATIS: Cari ID yang statusnya 'enroll' tapi belum ada pemiliknya
        $idAsing = \App\Models\AlatAbsenLog::leftJoin('users', 'absensi_logs.fingerprint_id', '=', 'users.fingerprint_id')
                    ->whereNull('users.id') // Tidak ada di tabel user
                    ->where('absensi_logs.action', '!=', 'verification_failed') // Abaikan yang gagal login
                    ->latest('absensi_logs.created_at')
                    ->first(['absensi_logs.fingerprint_id', 'absensi_logs.created_at']);

        // 3. Arahkan ke file view registrasi kamu
        return view('sekertaris.absensi.registrasi', compact('logs', 'idAsing'));
    }

    /**
     * FITUR 3: IOT RECEIVER (Tetap sama seperti kodemu)
     */
    public function storeFromIot(Request $request)
    {
        $data = $request->validate([
            'fingerprint_id' => 'required|integer',
            'action'         => 'required|string',
            'keterangan'     => 'nullable|string',
        ]);

        AlatAbsenLog::create([
            'fingerprint_id' => $data['fingerprint_id'],
            'action'         => $data['action'],
            'keterangan'     => $data['keterangan'] ?? 'Dari API',
        ]);

        return response()->json(['status' => 'berhasil', 'pesan' => 'Data masuk']);
    }

    /**
     * FITUR 4: HUBUNGKAN KARTU (Aksi Sekertaris)
     */
    public function hubungkanKartu(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'fingerprint_id' => 'required|integer'
        ]);

        // Update User dengan ID Jari baru
        User::where('id', $request->user_id)->update([
            'fingerprint_id' => $request->fingerprint_id
        ]);

        return back()->with('success', "Fingerprint ID {$request->fingerprint_id} berhasil dihubungkan ke Pegawai!");
    }

    
}