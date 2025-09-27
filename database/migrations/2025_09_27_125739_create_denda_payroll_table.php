<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('denda_payroll', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
            $table->char('bulan_tahun', 7);
            $table->decimal('total_denda', 15, 2)->default(0);
            $table->enum('status_potong_gaji', ['pending', 'dipotong'])->default('pending');
            $table->timestamps();
            $table->unique(['karyawan_id', 'bulan_tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denda_payroll');
    }
};