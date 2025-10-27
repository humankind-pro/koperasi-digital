<?php

// app/Models/Pembayaran.php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model {
    use HasFactory;
    protected $table = 'pembayaran';
    protected $fillable = [
        'pinjaman_id', 'jumlah_bayar', 'tanggal_bayar', 'diproses_oleh_user_id'
    ];

    public function pinjaman() { return $this->belongsTo(Pinjaman::class); }
    public function diprosesOleh() { return $this->belongsTo(User::class, 'diproses_oleh_user_id'); }
}