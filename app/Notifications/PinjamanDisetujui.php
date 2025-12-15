<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PinjamanDisetujui extends Notification
{
    use Queueable;

    protected $pinjaman;

    public function __construct($pinjaman)
    {
        $this->pinjaman = $pinjaman;
    }

    public function via($notifiable)
    {
        return ['database']; // Simpan ke database
    }

    public function toArray($notifiable)
    {
        return [
            'pinjaman_id' => $this->pinjaman->id,
            'nama_nasabah' => $this->pinjaman->anggota->nama,
            'nik_nasabah' => $this->pinjaman->anggota->nik, // Penting untuk search otomatis nanti
            'pesan' => 'Peminjaman nasabah ' . $this->pinjaman->anggota->nama . ' telah DISETUJUI.',
            'waktu' => now(),
        ];
    }
}