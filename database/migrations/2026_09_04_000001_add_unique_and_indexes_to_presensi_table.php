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
        Schema::table('presensi', function (Blueprint $table) {
            // Add unique constraint on (nik, tanggal) to strictly enforce single record per employee per day
            $table->unique(['nik', 'tanggal'], 'unique_presensi_nik_tanggal');

            // Add composite index for rapid dashboard & recap lookups
            $table->index(['nik', 'tanggal', 'status'], 'idx_presensi_nik_tgl_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropUnique('unique_presensi_nik_tanggal');
            $table->dropIndex('idx_presensi_nik_tgl_status');
        });
    }
};
