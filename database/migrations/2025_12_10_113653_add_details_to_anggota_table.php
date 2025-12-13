<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('anggota', function (Blueprint $table) {
            // Data Pribadi Tambahan
            $table->string('pendidikan')->nullable(); // SD, SMP, SMA, dll
            $table->integer('umur')->nullable();
            $table->integer('tanggungan')->default(0); // Jumlah anak/istri
            $table->string('status_tempat_tinggal')->nullable(); // Milik Sendiri, Sewa, dll
            $table->integer('lama_bekerja_tahun')->default(0);
            
            // Keuangan
            $table->decimal('pengeluaran_bulanan', 15, 2)->default(0);
            // Pendapatan bulanan biasanya sudah ada, tapi jika belum, uncomment baris bawah:
            // $table->decimal('pendapatan_bulanan', 15, 2)->default(0);

            // Data Pinjaman Awal (Opsional, untuk data preferensi)
            $table->string('tujuan_pinjaman_preferensi')->nullable(); 
            
            // Jaminan
            $table->boolean('memiliki_jaminan')->default(false);
            $table->string('jenis_jaminan')->nullable(); // BPKB, Sertifikat, dll
            $table->text('deskripsi_jaminan_lainnya')->nullable(); // Jika pilih "Lainnya"
        });
    }

    public function down()
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->dropColumn([
                'pendidikan', 'umur', 'tanggungan', 'status_tempat_tinggal', 
                'lama_bekerja_tahun', 'pengeluaran_bulanan', 
                'tujuan_pinjaman_preferensi', 'memiliki_jaminan', 
                'jenis_jaminan', 'deskripsi_jaminan_lainnya'
            ]);
        });
    }
};