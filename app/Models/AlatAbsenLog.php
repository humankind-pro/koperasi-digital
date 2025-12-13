<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AlatAbsenLog extends Model
{
    protected $table = 'absensi_logs';
    protected $guarded = [];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'fingerprint_id', 'fingerprint_id');
    }
}