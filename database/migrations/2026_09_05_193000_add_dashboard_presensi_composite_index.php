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
        $existing = collect(\Illuminate\Support\Facades\DB::select("SHOW INDEXES FROM presensi"))
            ->pluck('Key_name')
            ->all();

        if (!in_array('idx_presensi_status_tanggal_jk', $existing)) {
            Schema::table('presensi', function (Blueprint $table) {
                $table->index(['status', 'tanggal', 'kode_jam_kerja'], 'idx_presensi_status_tanggal_jk');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropIndex('idx_presensi_status_tanggal_jk');
        });
    }
};
