<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    use HasFactory;
    protected $table = 'pinjaman';

    /**
     * INI ADALAH SOLUSI UTAMA ANDA
     * Memastikan semua kolom yang ingin diisi atau diubah ada di sini.
     */
    protected $fillable = [
        'anggota_id',
        'diajukan_oleh_user_id',
        'divalidasi_oleh_user_id',
        'jumlah_pinjaman',
        'jumlah_disetujui',
        'tenor_bulan',
        'skor_risiko',
        'tujuan',
        'status',
        'tanggal_pengajuan',
        'tanggal_validasi',
        'catatan',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    public function diajukanOleh()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh_user_id');
    }

    public function divalidasiOleh()
    {
        return $this->belongsTo(User::class, 'divalidasi_oleh_user_id');
    }
}