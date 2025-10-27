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
    Schema::table('pinjaman', function (Blueprint $table) {
        // Tambahkan setelah 'jumlah_disetujui'
        // Defaultnya sama dengan jumlah disetujui
        $table->decimal('sisa_hutang', 15, 2)->nullable()->after('jumlah_disetujui'); 
    });
}

    /**
     * Reverse the migrations.
     */
};
