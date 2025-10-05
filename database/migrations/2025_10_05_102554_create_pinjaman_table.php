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
    Schema::create('pinjaman', function (Blueprint $table) {
        $table->id();

        // Foreign key ke tabel nasabah, jika nasabah dihapus, pinjamannya juga ikut terhapus.
        // KODE YANG SUDAH DIPERBAIKI
$table->foreignId('anggota_id')->constrained('anggota')->onDelete('cascade');

        // Foreign key ke tabel users, mencatat siapa KARYAWAN yang mengajukan.
        $table->foreignId('diajukan_oleh_user_id')->constrained('users');

        // Foreign key ke tabel users, mencatat siapa ADMIN yang memvalidasi.
        // Jika admin dihapus, datanya di sini jadi NULL.
        $table->foreignId('divalidasi_oleh_user_id')->nullable()->constrained('users')->onDelete('set null');

        $table->decimal('jumlah_pinjaman', 15, 2); // Jumlah yang diminta nasabah
        $table->decimal('jumlah_disetujui', 15, 2)->default(0); // Jumlah yang disetujui admin
        $table->integer('tenor_bulan');
        $table->tinyInteger('skor_risiko')->nullable(); // Skor 1-10, bisa diisi oleh sistem atau admin
        $table->text('tujuan')->nullable();
        $table->string('status')->default('pending'); // pending, disetujui, ditolak

        $table->date('tanggal_pengajuan');
        $table->date('tanggal_validasi')->nullable();
        $table->text('catatan')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinjaman');
    }
};
