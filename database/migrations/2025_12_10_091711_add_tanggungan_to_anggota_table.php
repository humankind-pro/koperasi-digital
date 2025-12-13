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
    Schema::table('anggota', function (Blueprint $table) {
        // Menambah kolom jumlah tanggungan (default 0)
        $table->integer('jumlah_tanggungan')->default(0)->after('pendapatan_bulanan');
    });
}
};
