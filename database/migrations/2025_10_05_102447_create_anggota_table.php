<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('anggota', function (Blueprint $table) {
        $table->id();
        $table->string('kode_anggota')->unique();
        $table->string('nama', 100);
        $table->string('no_ktp', 20)->unique(); // Sesuai permintaan Anda (sebelumnya 'nik')
        $table->text('alamat');
        $table->string('nomor_telepon');
        $table->string('pekerjaan', 50); // Sesuai permintaan Anda
        $table->decimal('pendapatan_bulanan', 15, 2)->default(0); // Sesuai permintaan Anda
        $table->date('tanggal_bergabung');

        // Foreign key ke tabel users untuk mencatat siapa yg mendaftarkan
        $table->foreignId('dibuat_oleh_user_id')->constrained('users');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};
