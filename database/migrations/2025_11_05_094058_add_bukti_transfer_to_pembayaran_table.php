<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            // Kolom untuk menyimpan path/nama file gambar
            $table->string('bukti_transfer_path')->nullable()->after('diproses_oleh_user_id');
        });
    }

    public function down(): void
    {
         Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn('bukti_transfer_path');
        });
    }
};