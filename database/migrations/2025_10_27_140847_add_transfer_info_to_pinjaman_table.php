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
        $table->unsignedBigInteger('original_anggota_id')->nullable()->after('anggota_id');
        $table->foreignId('ditransfer_oleh_user_id')->nullable()->constrained('users')->after('divalidasi_oleh_user_id');
        $table->timestamp('tanggal_transfer')->nullable()->after('ditransfer_oleh_user_id');
        $table->text('alasan_transfer')->nullable()->after('tanggal_transfer');
    });
}
};
