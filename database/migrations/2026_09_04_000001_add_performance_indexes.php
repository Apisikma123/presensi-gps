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
        Schema::table('karyawan', function (Blueprint $table) {
            $table->index('nama_karyawan', 'idx_karyawan_nama');
            $table->index('nik_show', 'idx_karyawan_nik_show');
        });

        Schema::table('presensi_izinabsen', function (Blueprint $table) {
            $table->index(['status', 'tanggal'], 'idx_izinabsen_status_tanggal');
        });

        Schema::table('presensi_izinsakit', function (Blueprint $table) {
            $table->index(['status', 'tanggal'], 'idx_izinsakit_status_tanggal');
        });

        Schema::table('presensi_izincuti', function (Blueprint $table) {
            $table->index(['status', 'tanggal'], 'idx_izincuti_status_tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropIndex('idx_karyawan_nama');
            $table->dropIndex('idx_karyawan_nik_show');
        });

        Schema::table('presensi_izinabsen', function (Blueprint $table) {
            $table->dropIndex('idx_izinabsen_status_tanggal');
        });

        Schema::table('presensi_izinsakit', function (Blueprint $table) {
            $table->dropIndex('idx_izinsakit_status_tanggal');
        });

        Schema::table('presensi_izincuti', function (Blueprint $table) {
            $table->dropIndex('idx_izincuti_status_tanggal');
        });
    }
};
