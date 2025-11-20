<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanAbsensi extends Model
{
    use HasFactory;
    
    protected $table = 'pengaturan_absensi';
    
    protected $fillable = [
        'hari_kerja_per_bulan', 
        'jam_masuk',             // <-- Tambahan
        'potongan_per_terlambat' // <-- Tambahan
    ];
}