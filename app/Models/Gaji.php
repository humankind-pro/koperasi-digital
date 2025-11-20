<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gaji extends Model
{
    use HasFactory;
    protected $table = 'gaji';
    protected $fillable = [
    'user_id', 'gaji_pokok', 'tunjangan', 'potongan', 
    'jumlah_hadir', 'jumlah_alpa', 'nominal_potongan_alpa',
    'total_gaji', 'tanggal_gaji', 'catatan'
];

    // Relasi ke User (Penerima Gaji)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}