<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    // 1. Tabel Log untuk menampung data mentah dari ESP32
    Schema::create('absensi_logs', function (Blueprint $table) {
        $table->id();
        $table->integer('fingerprint_id'); // ID Jari (1-127)
        $table->string('action');          // enroll, verification_success, delete
        $table->string('keterangan')->nullable(); // Nama user sementara / status
        $table->timestamp('created_at')->useCurrent(); // Waktu scan masuk
        $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
    });

    // 2. Kolom ID Jari di tabel Users (Untuk menghubungkan Log ke Karyawan)
    if (!Schema::hasColumn('users', 'fingerprint_id')) {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('fingerprint_id')->nullable()->unique()->after('email');
        });
    }
}
};
