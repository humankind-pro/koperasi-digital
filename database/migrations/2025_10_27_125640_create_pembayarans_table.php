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
    Schema::create('pembayaran', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pinjaman_id')->constrained('pinjaman')->onDelete('cascade');
        $table->decimal('jumlah_bayar', 15, 2);
        $table->date('tanggal_bayar');
        $table->foreignId('diproses_oleh_user_id')->constrained('users'); // Karyawan yang input
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
};
