<?php

use Illuminate\Database\Migrations\Migration; // <-- Sudah diperbaiki
use Illuminate\Database\Schema\Blueprint; // <-- Sudah diperbaiki
use Illuminate\Support\Facades\Schema; // <-- Sudah diperbaiki
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            // 1. Ubah nilai default untuk data baru
            $table->integer('skor_kredit')->default(100)->change();
        });

        // 2. Setel ulang skor semua anggota yang ada menjadi 100
        DB::statement('UPDATE anggota SET skor_kredit = 100');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            // Logika jika ingin rollback
            $table->integer('skor_kredit')->default(0)->change();
        });
    }
};