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
    Schema::table('gaji', function (Blueprint $table) {
        // Kita tambahkan kolom untuk menyimpan detail hitungan
        $table->integer('jumlah_hadir')->default(0)->after('potongan');
        $table->integer('jumlah_alpa')->default(0)->after('jumlah_hadir');
        $table->decimal('nominal_potongan_alpa', 15, 2)->default(0)->after('jumlah_alpa');
    });
}
};
