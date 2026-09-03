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

        // 1. users_karyawan: Essential lookup on every employee authenticated request
        if (Schema::hasTable('users_karyawan')) {
            Schema::table('users_karyawan', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('users_karyawan', 'idx_users_karyawan_id_user') && Schema::hasColumn('users_karyawan', 'id_user')) {
                    $table->index('id_user', 'idx_users_karyawan_id_user');
                }
            });
        }

        // 2. presensi_izinabsen: status and date range filtering
        if (Schema::hasTable('presensi_izinabsen')) {
            Schema::table('presensi_izinabsen', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('presensi_izinabsen', 'idx_izinabsen_nik_status') && Schema::hasColumns('presensi_izinabsen', ['nik', 'status'])) {
                    $table->index(['nik', 'status'], 'idx_izinabsen_nik_status');
                }
                if (!$indexExists('presensi_izinabsen', 'idx_izinabsen_status_dari_sampai') && Schema::hasColumns('presensi_izinabsen', ['status', 'dari', 'sampai'])) {
                    $table->index(['status', 'dari', 'sampai'], 'idx_izinabsen_status_dari_sampai');
                }
            });
        }

        // 3. presensi_izinsakit: status and date range filtering
        if (Schema::hasTable('presensi_izinsakit')) {
            Schema::table('presensi_izinsakit', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('presensi_izinsakit', 'idx_izinsakit_nik_status') && Schema::hasColumns('presensi_izinsakit', ['nik', 'status'])) {
                    $table->index(['nik', 'status'], 'idx_izinsakit_nik_status');
                }
                if (!$indexExists('presensi_izinsakit', 'idx_izinsakit_status_dari_sampai') && Schema::hasColumns('presensi_izinsakit', ['status', 'dari', 'sampai'])) {
                    $table->index(['status', 'dari', 'sampai'], 'idx_izinsakit_status_dari_sampai');
                }
            });
        }

        // 4. presensi_izincuti: status and date range filtering
        if (Schema::hasTable('presensi_izincuti')) {
            Schema::table('presensi_izincuti', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('presensi_izincuti', 'idx_izincuti_nik_status') && Schema::hasColumns('presensi_izincuti', ['nik', 'status'])) {
                    $table->index(['nik', 'status'], 'idx_izincuti_nik_status');
                }
                if (!$indexExists('presensi_izincuti', 'idx_izincuti_status_dari_sampai') && Schema::hasColumns('presensi_izincuti', ['status', 'dari', 'sampai'])) {
                    $table->index(['status', 'dari', 'sampai'], 'idx_izincuti_status_dari_sampai');
                }
            });
        }

        // 5. presensi_jamkerja_bydate: lookup by tanggal on dashboard load
        if (Schema::hasTable('presensi_jamkerja_bydate')) {
            Schema::table('presensi_jamkerja_bydate', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('presensi_jamkerja_bydate', 'idx_jamkerja_bydate_tanggal') && Schema::hasColumn('presensi_jamkerja_bydate', 'tanggal')) {
                    $table->index('tanggal', 'idx_jamkerja_bydate_tanggal');
                }
            });
        }

        // 6. presensi_jamkerja_byday: lookup by hari on dashboard load
        if (Schema::hasTable('presensi_jamkerja_byday')) {
            Schema::table('presensi_jamkerja_byday', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('presensi_jamkerja_byday', 'idx_jamkerja_byday_hari') && Schema::hasColumn('presensi_jamkerja_byday', 'hari')) {
                    $table->index('hari', 'idx_jamkerja_byday_hari');
                }
            });
        }

        // 7. ajuan_jadwal: pending approvals count on dashboard load
        if (Schema::hasTable('ajuan_jadwal')) {
            Schema::table('ajuan_jadwal', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('ajuan_jadwal', 'idx_ajuan_jadwal_status_nik') && Schema::hasColumns('ajuan_jadwal', ['status', 'nik'])) {
                    $table->index(['status', 'nik'], 'idx_ajuan_jadwal_status_nik');
                }
            });
        }

        // 8. lembur: pending overtime lookups
        if (Schema::hasTable('lembur')) {
            Schema::table('lembur', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('lembur', 'idx_lembur_nik_status') && Schema::hasColumns('lembur', ['nik', 'status'])) {
                    $table->index(['nik', 'status'], 'idx_lembur_nik_status');
                }
            });
        }

        // 9. approval_layers: feature and level lookups
        if (Schema::hasTable('approval_layers')) {
            Schema::table('approval_layers', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('approval_layers', 'idx_approval_layers_feature_level') && Schema::hasColumns('approval_layers', ['feature', 'level'])) {
                    $table->index(['feature', 'level'], 'idx_approval_layers_feature_level');
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
                // Ignore if not found
            }
        };

        $dropIndex('users_karyawan', 'idx_users_karyawan_id_user');
        $dropIndex('presensi_izinabsen', 'idx_izinabsen_nik_status');
        $dropIndex('presensi_izinabsen', 'idx_izinabsen_status_dari_sampai');
        $dropIndex('presensi_izinsakit', 'idx_izinsakit_nik_status');
        $dropIndex('presensi_izinsakit', 'idx_izinsakit_status_dari_sampai');
        $dropIndex('presensi_izincuti', 'idx_izincuti_nik_status');
        $dropIndex('presensi_izincuti', 'idx_izincuti_status_dari_sampai');
        $dropIndex('presensi_jamkerja_bydate', 'idx_jamkerja_bydate_tanggal');
        $dropIndex('presensi_jamkerja_byday', 'idx_jamkerja_byday_hari');
        $dropIndex('ajuan_jadwal', 'idx_ajuan_jadwal_status_nik');
        $dropIndex('lembur', 'idx_lembur_nik_status');
        $dropIndex('approval_layers', 'idx_approval_layers_feature_level');
    }
};
