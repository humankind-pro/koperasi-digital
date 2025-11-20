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
    Schema::table('pengaturan_absensi', function (Blueprint $table) {
        // Menambahkan Jam Masuk (Default jam 8 pagi)
        $table->time('jam_masuk')->default('08:00:00')->after('id');

        // Menambahkan Persen Potongan per Terlambat (Default 1%)
        // Format DECIMAL(5,2) artinya bisa menyimpan angka seperti 100.00 atau 1.50
        $table->decimal('potongan_per_terlambat', 5, 2)->default(1.00)->after('jam_masuk');
    });
}

public function down(): void
{
    Schema::table('pengaturan_absensi', function (Blueprint $table) {
        $table->dropColumn(['jam_masuk', 'potongan_per_terlambat']);
    });
}
};
