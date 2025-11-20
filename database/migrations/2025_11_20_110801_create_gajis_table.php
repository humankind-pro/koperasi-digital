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
    Schema::create('gaji', function (Blueprint $table) {
        $table->id();
        // User yang menerima gaji (Admin/Karyawan)
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->decimal('gaji_pokok', 15, 2);
        $table->decimal('tunjangan', 15, 2)->default(0);
        $table->decimal('potongan', 15, 2)->default(0);
        $table->decimal('total_gaji', 15, 2); // (Gaji Pokok + Tunjangan) - Potongan
        $table->date('tanggal_gaji'); // Untuk mencatat bulan/tahun gaji
        $table->text('catatan')->nullable();
        $table->timestamps();
    });
}
};
