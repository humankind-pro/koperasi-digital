<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes; // Jika menggunakan soft deletes

class Anggota extends Model
{
    use HasFactory, SoftDeletes; 
    protected $table = 'anggota';// Tambahkan SoftDeletes jika migrasi Anda menyertakannya

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        // Data KTP
        'kode_anggota',
        'nama',
        'nik',
        'tempat_tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_perkawinan',
        'pekerjaan',
        'kewarganegaraan',
        'gol_darah',

        // Alamat
        'alamat',
        'rt_rw',
        'kelurahan_desa',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',

        // Kontak
        'nomor_telepon',

        // Data untuk ML
        'umur',
        'pendidikan',
        'lama_bekerja', // Text: "2 tahun 3 bulan"
        'lama_bekerja_tahun', // Float: 2.25 tahun
        'pendapatan_bulanan',
        'pengeluaran_bulanan',
        'skor_kredit',
        'jumlah_tanggungan',
        'status_tempat_tinggal',

        // Data Pinjaman & ML
        'jumlah_pinjaman',
        'tenor', // Kolom di database, dipetakan dari input 'Lama_Tenor_Bulan'
        'tujuan_pinjaman',
        'jaminan',
        'keterangan_jaminan',
        'riwayat_tunggakan',

        // Hasil ML
        'kelayakan',
        'tanggal_prediksi',

        // Dokumen
        'foto_ktp',
        'foto_selfie_ktp',

        // Status & Workflow
        'status',

        // Audit Trail
        'dibuat_oleh_user_id',
        'divalidasi_oleh_user_id',
    ];

    // Kolom-kolom yang harus di-cast ke tipe data tertentu
    protected $casts = [
        'pendapatan_bulanan' => 'decimal:2',
        'pengeluaran_bulanan' => 'decimal:2',
        'jumlah_pinjaman' => 'decimal:2',
        'lama_bekerja_tahun' => 'decimal:2', // Diperbaiki: casting ke decimal:2
        'skor_kredit' => 'integer',
        'umur' => 'integer',
        'jumlah_tanggungan' => 'integer',
        'tenor' => 'integer', // Diperbaiki: casting ke integer (kolom database)
        'dibuat_oleh_user_id' => 'integer',
        'divalidasi_oleh_user_id' => 'integer',
        'tanggal_prediksi' => 'date',
    ];

    // Kolom-kolom yang disembunyikan saat model dikonversi ke array/JSON
    protected $hidden = [
        // Tidak ada kolom yang disembunyikan secara default dalam kasus ini,
        // kecuali jika Anda memiliki kolom sensitif.
        // 'password', 'remember_token', // Contoh jika ada
    ];

    // Kolom-kolom yang harus dilindungi dari mass assignment
    protected $guarded = [
        'id', // ID biasanya tidak diisi secara manual
        'kode_anggota', // Kode di-generate otomatis
        'created_at',
        'updated_at',
        'deleted_at', // Jika menggunakan soft deletes
    ];

    // Relasi: Anggota dibuat oleh User
    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh_user_id');
    }

    // Relasi: Anggota divalidasi oleh User (Admin)
    public function divalidasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'divalidasi_oleh_user_id');
    }

    // Accessor: Format nama (misalnya, untuk tampilan)
    public function getNamaFormattedAttribute()
    {
        return ucfirst(strtolower($this->attributes['nama']));
    }

    // Accessor: Pastikan lama_bekerja_tahun selalu diakses sebagai float
    public function getLamaBekerjaTahunAttribute()
    {
        return (float) $this->attributes['lama_bekerja_tahun'];
    }

    // Scope: Filter anggota berdasarkan status kelayakan
    public function scopeKelayakan($query, $status)
    {
        return $query->where('kelayakan', $status);
    }

    // Scope: Filter anggota berdasarkan status aplikasi
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope: Filter anggota berdasarkan user pembuat
    public function scopeDibuatOleh($query, $userId)
    {
        return $query->where('dibuat_oleh_user_id', $userId);
    }

    // Contoh method untuk mengecek apakah anggota layak
    public function isLayak(): bool
    {
        return $this->kelayakan === 'Layak';
    }

    // Contoh method untuk mengecek apakah anggota dipertimbangkan
    public function isDipertimbangkan(): bool
    {
        return $this->kelayakan === 'Dipertimbangkan';
    }

    // Contoh method untuk mengecek apakah anggota tidak layak
    public function isTidakLayak(): bool
    {
        return $this->kelayakan === 'Tidak Layak';
    }

    // Contoh method untuk mengecek status aplikasi
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDisetujui(): bool
    {
        return $this->status === 'disetujui';
    }

    public function isDitolak(): bool
    {
        return $this->status === 'ditolak';
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }
}
