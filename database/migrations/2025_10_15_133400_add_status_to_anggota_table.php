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
        $table->string('status')->default('pending')->after('tanggal_bergabung');
        $table->foreignId('divalidasi_oleh_user_id')->nullable()->constrained('users')->after('status');
    });
}
};
