<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    use HasFactory;
    protected $table = 'pinjaman';

    /**
     * PASTIKAN SEMUA KOLOM INI ADA DI DALAM $fillable
     */
    protected $fillable = [
        'anggota_id',
        'diajukan_oleh_user_id',
        'divalidasi_oleh_user_id', // <-- WAJIB ADA
        'jumlah_pinjaman',
        'jumlah_disetujui',       // <-- WAJIB ADA
        'sisa_hutang',            // <-- WAJIB ADA
        'tenor_bulan',
        'skor_risiko',
        'tujuan',
        'status',                 // <-- WAJIB ADA
        'tanggal_pengajuan',
        'tanggal_validasi',       // <-- WAJIB ADA
        'catatan',
    ];

    // ... (Relasi) ...
    public function anggota() { return $this->belongsTo(Anggota::class, 'anggota_id'); }
    public function diajukanOleh() { return $this->belongsTo(User::class, 'diajukan_oleh_user_id'); }
    public function divalidasiOleh() { return $this->belongsTo(User::class, 'divalidasi_oleh_user_id'); }
    public function pembayaran() { return $this->hasMany(Pembayaran::class); } // Jika sudah dibuat
}