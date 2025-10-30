<?php

namespace App\Models;

// Pastikan use statement ini mengarah ke 'Illuminate\Database\Eloquent'
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'absensi';

    /**
     * Atribut yang diizinkan untuk diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'waktu_absensi',
    ];

    /**
     * Mendapatkan user (karyawan) yang memiliki absensi ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}