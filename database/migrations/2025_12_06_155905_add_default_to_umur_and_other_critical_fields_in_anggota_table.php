<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            // Kolom yang sering bikin error 1364
            $table->integer('umur')->default(0)->change();
            $table->integer('jumlah_tanggungan')->default(0)->change();
            $table->decimal('pendapatan_bulanan', 15, 2)->default(0)->change();
            $table->decimal('pengeluaran_bulanan', 15, 2)->default(0)->change();
            $table->decimal('jumlah_pinjaman', 15, 2)->default(0)->change();
            $table->integer('tenor')->default(12)->change();
        });
    }

    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->integer('umur')->default(null)->change();
            $table->integer('jumlah_tanggungan')->default(null)->change();
            $table->decimal('pendapatan_bulanan', 15, 2)->default(null)->change();
            $table->decimal('pengeluaran_bulanan', 15, 2)->default(null)->change();
            $table->decimal('jumlah_pinjaman', 15, 2)->default(null)->change();
            $table->integer('tenor')->default(null)->change();
        });
    }
};