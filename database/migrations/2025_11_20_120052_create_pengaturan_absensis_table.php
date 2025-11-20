<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_pengaturan_absensis_table.php
    public function up(): void
{
    Schema::create('pengaturan_absensi', function (Blueprint $table) {
        $table->id();
        $table->integer('hari_kerja_per_bulan')->default(26); // Default 26 hari
        $table->timestamps();
    });

    // Insert data default langsung
    DB::table('pengaturan_absensi')->insert([
        'hari_kerja_per_bulan' => 26,
        'created_at' => now(),
        'updated_at' => now()
    ]);
}
};
