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
        // Skor kredit default adalah 100
        $table->integer('skor_kredit')->default(100)->after('pendapatan_bulanan');
    });
}
};
