<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('waktu_masuk')->nullable();
            $table->time('waktu_keluar')->nullable();
            $table->integer('keterlambatan_menit')->default(0);
            $table->text('alasan_keterlambatan')->nullable();
            $table->enum('status_izin', ['none', 'pending', 'disetujui', 'ditolak'])->default('none');
            $table->text('alasan_izin')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('admin')->onDelete('set null');
            $table->decimal('denda', 15, 2)->default(0);
            $table->timestamps();
            $table->unique(['karyawan_id', 'tanggal']);
        });

        // Trigger untuk Denda Absensi
        DB::statement("
            CREATE TRIGGER update_denda_absensi BEFORE INSERT ON absensi
            FOR EACH ROW
            BEGIN
                IF NEW.status_izin = 'disetujui' THEN
                    SET NEW.denda = 0;
                ELSE
                    SET NEW.denda = FLOOR(NEW.keterlambatan_menit / 10) * 1000;
                END IF;
            END
        ");
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS update_denda_absensi');
        Schema::dropIfExists('absensi');
    }
};