<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pinjaman;
use Carbon\Carbon;

class CekPinjamanMenunggak extends Command
{
    /**
     * Nama perintah yang akan dijalankan oleh robot.
     */
    protected $signature = 'pinjaman:cek-menunggak';

    /**
     * Deskripsi perintah.
     */
    protected $description = 'Cek semua pinjaman aktif, ubah status jadi menunggak jika melewati tenggat waktu.';

    /**
     * Eksekusi perintah.
     */
    public function handle()
    {
        $hariIni = Carbon::now()->startOfDay();

        // 1. Cari pinjaman yang:
        //    - Statusnya masih 'disetujui' (artinya belum lunas dan belum menunggak)
        //    - Tenggat waktunya sudah LEWAT dari hari ini
        //    - Sisa hutang masih ada (> 0)
        
        $pinjamanTerlambat = Pinjaman::where('status', 'disetujui')
            ->where('sisa_hutang', '>', 0)
            ->whereDate('tenggat_berikutnya', '<', $hariIni)
            ->get();

        $jumlahDiupdate = 0;

        foreach ($pinjamanTerlambat as $pinjaman) {
            // Ubah status menjadi 'menunggak'
            $pinjaman->update([
                'status' => 'menunggak'
            ]);
            
            // (Opsional) Kurangi skor kredit nasabah sebagai hukuman
            if($pinjaman->anggota) {
                $pinjaman->anggota->decrement('skor_kredit', 5); 
            }

            $jumlahDiupdate++;
        }

        $this->info("Selesai! Ada {$jumlahDiupdate} pinjaman yang diubah menjadi menunggak.");
    }
}