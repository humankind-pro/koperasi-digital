<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <-- Penting, tambahkan ini

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            // 1. Ubah nilai default untuk data baru
            $table->integer('skor_kredit')->default(0)->change();
        });

        // 2. Setel ulang skor semua anggota yang ada menjadi 0
        DB::statement('UPDATE anggota SET skor_kredit = 0');
    }

    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            // Logika jika ingin rollback
            $table->integer('skor_kredit')->default(100)->change();
        });
    }
};