<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $indexExists = function ($table, $indexName) {
            try {
                $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
                return count($indexes) > 0;
            } catch (\Throwable $e) {
                return false;
            }
        };

        // 1. presensi: composite index on (tanggal, status) for daily dashboard and presence filters
        if (Schema::hasTable('presensi')) {
            Schema::table('presensi', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('presensi', 'idx_presensi_tanggal_status') && Schema::hasColumns('presensi', ['tanggal', 'status'])) {
                    $table->index(['tanggal', 'status'], 'idx_presensi_tanggal_status');
                }
            });
        }

        // 2. presensi_jamkerja_bydept_detail: lookup on hari and kode_jk_dept on shift monitoring
        if (Schema::hasTable('presensi_jamkerja_bydept_detail')) {
            Schema::table('presensi_jamkerja_bydept_detail', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('presensi_jamkerja_bydept_detail', 'idx_jk_dept_detail_hari_jk') && Schema::hasColumns('presensi_jamkerja_bydept_detail', ['hari', 'kode_jk_dept'])) {
                    $table->index(['hari', 'kode_jk_dept'], 'idx_jk_dept_detail_hari_jk');
                }
            });
        }

        // 3. presensi_jamkerja_bydept: lookup on kode_dept on shift monitoring
        if (Schema::hasTable('presensi_jamkerja_bydept')) {
            Schema::table('presensi_jamkerja_bydept', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('presensi_jamkerja_bydept', 'idx_jk_bydept_kode_dept') && Schema::hasColumn('presensi_jamkerja_bydept', 'kode_dept')) {
                    $table->index('kode_dept', 'idx_jk_bydept_kode_dept');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $dropIndex = function ($table, $indexName) {
            try {
                Schema::table($table, function (Blueprint $table) use ($indexName) {
                    $table->dropIndex($indexName);
                });
            } catch (\Throwable $e) {
                // Ignore
            }
        };

        $dropIndex('presensi', 'idx_presensi_tanggal_status');
        $dropIndex('presensi_jamkerja_bydept_detail', 'idx_jk_dept_detail_hari_jk');
        $dropIndex('presensi_jamkerja_bydept', 'idx_jk_bydept_kode_dept');
    }
};
