<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggota';

    // app/Models/Anggota.php

protected $fillable = [
    'kode_anggota',
    'nama',
    'no_ktp',
    'alamat',
    'nomor_telepon',
    'pekerjaan',
    'pendapatan_bulanan',
    'tanggal_bergabung',
    'dibuat_oleh_user_id',
    'status', // <-- Tambahkan ini
    'divalidasi_oleh_user_id', // <-- Tambahkan ini
];
public function dibuatOleh()
{
    return $this->belongsTo(User::class, 'dibuat_oleh_user_id');
}
}