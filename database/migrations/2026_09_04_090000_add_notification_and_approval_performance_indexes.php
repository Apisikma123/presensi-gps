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

        // 1. notifications: composite index for unread notifications lookup
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('notifications', 'idx_notifications_notifiable_read_at') && 
                    Schema::hasColumns('notifications', ['notifiable_type', 'notifiable_id', 'read_at'])) {
                    $table->index(['notifiable_type', 'notifiable_id', 'read_at'], 'idx_notifications_notifiable_read_at');
                }
            });
        }

        // 2. presensi_koreksi: status and approval step
        if (Schema::hasTable('presensi_koreksi')) {
            Schema::table('presensi_koreksi', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('presensi_koreksi', 'idx_koreksi_status_step') && 
                    Schema::hasColumns('presensi_koreksi', ['status', 'approval_step'])) {
                    $table->index(['status', 'approval_step'], 'idx_koreksi_status_step');
                }
                if (!$indexExists('presensi_koreksi', 'idx_koreksi_nik_status') && 
                    Schema::hasColumns('presensi_koreksi', ['nik', 'status'])) {
                    $table->index(['nik', 'status'], 'idx_koreksi_nik_status');
                }
            });
        }

        // 3. reimbursement: status and approval step
        if (Schema::hasTable('reimbursement')) {
            Schema::table('reimbursement', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('reimbursement', 'idx_reimbursement_status_step') && 
                    Schema::hasColumns('reimbursement', ['status', 'approval_step'])) {
                    $table->index(['status', 'approval_step'], 'idx_reimbursement_status_step');
                }
                if (!$indexExists('reimbursement', 'idx_reimbursement_nik_status') && 
                    Schema::hasColumns('reimbursement', ['nik', 'status'])) {
                    $table->index(['nik', 'status'], 'idx_reimbursement_nik_status');
                }
            });
        }

        // 4. lembur: status leading index
        if (Schema::hasTable('lembur')) {
            Schema::table('lembur', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('lembur', 'idx_lembur_status_nik') && 
                    Schema::hasColumns('lembur', ['status', 'nik'])) {
                    $table->index(['status', 'nik'], 'idx_lembur_status_nik');
                }
            });
        }

        // 5. presensi_izindinas: status and approval step
        if (Schema::hasTable('presensi_izindinas')) {
            Schema::table('presensi_izindinas', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('presensi_izindinas', 'idx_izindinas_status_step') && 
                    Schema::hasColumns('presensi_izindinas', ['status', 'approval_step'])) {
                    $table->index(['status', 'approval_step'], 'idx_izindinas_status_step');
                }
                if (!$indexExists('presensi_izindinas', 'idx_izindinas_nik_status') && 
                    Schema::hasColumns('presensi_izindinas', ['nik', 'status'])) {
                    $table->index(['nik', 'status'], 'idx_izindinas_nik_status');
                }
            });
        }

        // 6. presensi_izinabsen: status and approval step
        if (Schema::hasTable('presensi_izinabsen')) {
            Schema::table('presensi_izinabsen', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('presensi_izinabsen', 'idx_izinabsen_status_step') && 
                    Schema::hasColumns('presensi_izinabsen', ['status', 'approval_step'])) {
                    $table->index(['status', 'approval_step'], 'idx_izinabsen_status_step');
                }
            });
        }

        // 7. presensi_izincuti: status and approval step
        if (Schema::hasTable('presensi_izincuti')) {
            Schema::table('presensi_izincuti', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('presensi_izincuti', 'idx_izincuti_status_step') && 
                    Schema::hasColumns('presensi_izincuti', ['status', 'approval_step'])) {
                    $table->index(['status', 'approval_step'], 'idx_izincuti_status_step');
                }
            });
        }

        // 8. presensi_izinsakit: status and approval step
        if (Schema::hasTable('presensi_izinsakit')) {
            Schema::table('presensi_izinsakit', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('presensi_izinsakit', 'idx_izinsakit_status_step') && 
                    Schema::hasColumns('presensi_izinsakit', ['status', 'approval_step'])) {
                    $table->index(['status', 'approval_step'], 'idx_izinsakit_status_step');
                }
            });
        }

        // 9. karyawan_gaji_pokok: covering index on nik, kode_gaji
        if (Schema::hasTable('karyawan_gaji_pokok')) {
            Schema::table('karyawan_gaji_pokok', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('karyawan_gaji_pokok', 'idx_gaji_pokok_nik_kode_gaji') && 
                    Schema::hasColumns('karyawan_gaji_pokok', ['nik', 'kode_gaji'])) {
                    $table->index(['nik', 'kode_gaji'], 'idx_gaji_pokok_nik_kode_gaji');
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

        $dropIndex('notifications', 'idx_notifications_notifiable_read_at');
        $dropIndex('presensi_koreksi', 'idx_koreksi_status_step');
        $dropIndex('presensi_koreksi', 'idx_koreksi_nik_status');
        $dropIndex('reimbursement', 'idx_reimbursement_status_step');
        $dropIndex('reimbursement', 'idx_reimbursement_nik_status');
        $dropIndex('lembur', 'idx_lembur_status_nik');
        $dropIndex('presensi_izindinas', 'idx_izindinas_status_step');
        $dropIndex('presensi_izindinas', 'idx_izindinas_nik_status');
        $dropIndex('presensi_izinabsen', 'idx_izinabsen_status_step');
        $dropIndex('presensi_izincuti', 'idx_izincuti_status_step');
        $dropIndex('presensi_izinsakit', 'idx_izinsakit_status_step');
        $dropIndex('karyawan_gaji_pokok', 'idx_gaji_pokok_nik_kode_gaji');
    }
};
