<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggota';



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
    'status',
    'skor_kredit', 
    'divalidasi_oleh_user_id',
    // --- FIELD BARU ---
    'pendidikan',
    'umur',
    'tanggungan',
    'status_tempat_tinggal',
    'lama_bekerja_tahun',
    'pengeluaran_bulanan',
    'tujuan_pinjaman_preferensi',
    'memiliki_jaminan',
    'jenis_jaminan',
    'deskripsi_jaminan_lainnya', 
    'dibuat_oleh_user_id',
]; 
public function dibuatOleh()
{
    return $this->belongsTo(User::class, 'dibuat_oleh_user_id');
}
}