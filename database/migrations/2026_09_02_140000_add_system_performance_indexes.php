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

        // 1. Presensi table indexes
        if (Schema::hasTable('presensi')) {
            Schema::table('presensi', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('presensi', 'idx_presensi_tanggal') && Schema::hasColumn('presensi', 'tanggal')) {
                    $table->index('tanggal', 'idx_presensi_tanggal');
                }
                if (!$indexExists('presensi', 'idx_presensi_status') && Schema::hasColumn('presensi', 'status')) {
                    $table->index('status', 'idx_presensi_status');
                }
                if (!$indexExists('presensi', 'idx_presensi_kode_jk') && Schema::hasColumn('presensi', 'kode_jam_kerja')) {
                    $table->index('kode_jam_kerja', 'idx_presensi_kode_jk');
                }
                if (!$indexExists('presensi', 'idx_presensi_id_mesin') && Schema::hasColumn('presensi', 'id_mesin')) {
                    $table->index('id_mesin', 'idx_presensi_id_mesin');
                }
            });
        }

        // 2. Karyawan table indexes
        if (Schema::hasTable('karyawan')) {
            Schema::table('karyawan', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('karyawan', 'idx_karyawan_status_cabang_dept') && 
                    Schema::hasColumns('karyawan', ['status_aktif_karyawan', 'kode_cabang', 'kode_dept'])) {
                    $table->index(['status_aktif_karyawan', 'kode_cabang', 'kode_dept'], 'idx_karyawan_status_cabang_dept');
                }
                if (!$indexExists('karyawan', 'idx_karyawan_status_karyawan') && Schema::hasColumn('karyawan', 'status_karyawan')) {
                    $table->index('status_karyawan', 'idx_karyawan_status_karyawan');
                }
            });
        }

        // 3. Kontrak table indexes
        if (Schema::hasTable('kontrak')) {
            Schema::table('kontrak', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('kontrak', 'idx_kontrak_nik_status_sampai') && 
                    Schema::hasColumns('kontrak', ['nik', 'status_kontrak', 'sampai'])) {
                    $table->index(['nik', 'status_kontrak', 'sampai'], 'idx_kontrak_nik_status_sampai');
                }
            });
        }

        // 4. Pelanggaran table indexes
        if (Schema::hasTable('pelanggaran')) {
            Schema::table('pelanggaran', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('pelanggaran', 'idx_pelanggaran_nik_dari_sampai') && 
                    Schema::hasColumns('pelanggaran', ['nik', 'dari', 'sampai'])) {
                    $table->index(['nik', 'dari', 'sampai'], 'idx_pelanggaran_nik_dari_sampai');
                }
            });
        }

        // 5. Izin tables indexes
        if (Schema::hasTable('presensi_izinabsen')) {
            Schema::table('presensi_izinabsen', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('presensi_izinabsen', 'idx_izinabsen_nik_status_tgl') && 
                    Schema::hasColumns('presensi_izinabsen', ['nik', 'status_approved', 'tanggal_izin_dari'])) {
                    $table->index(['nik', 'status_approved', 'tanggal_izin_dari'], 'idx_izinabsen_nik_status_tgl');
                }
            });
        }

        if (Schema::hasTable('presensi_izinsakit')) {
            Schema::table('presensi_izinsakit', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('presensi_izinsakit', 'idx_izinsakit_nik_status_tgl') && 
                    Schema::hasColumns('presensi_izinsakit', ['nik', 'status_approved', 'tanggal_izin_dari'])) {
                    $table->index(['nik', 'status_approved', 'tanggal_izin_dari'], 'idx_izinsakit_nik_status_tgl');
                }
            });
        }

        if (Schema::hasTable('presensi_izincuti')) {
            Schema::table('presensi_izincuti', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('presensi_izincuti', 'idx_izincuti_nik_status_tgl') && 
                    Schema::hasColumns('presensi_izincuti', ['nik', 'status_approved', 'tanggal_izin_dari'])) {
                    $table->index(['nik', 'status_approved', 'tanggal_izin_dari'], 'idx_izincuti_nik_status_tgl');
                }
            });
        }

        // 6. Pembayaran Pinjaman table indexes
        if (Schema::hasTable('pembayaran_pinjaman')) {
            Schema::table('pembayaran_pinjaman', function (Blueprint $table) use ($indexExists) {
                if (!$indexExists('pembayaran_pinjaman', 'idx_pinjaman_bayar_periode') && 
                    Schema::hasColumns('pembayaran_pinjaman', ['bulan_gaji', 'tahun_gaji', 'jenis_pembayaran'])) {
                    $table->index(['bulan_gaji', 'tahun_gaji', 'jenis_pembayaran'], 'idx_pinjaman_bayar_periode');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $indexExists = function ($table, $indexName) {
            try {
                $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
                return count($indexes) > 0;
            } catch (\Throwable $e) {
                return false;
            }
        };

        if (Schema::hasTable('presensi')) {
            Schema::table('presensi', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('presensi', 'idx_presensi_tanggal')) $table->dropIndex('idx_presensi_tanggal');
                if ($indexExists('presensi', 'idx_presensi_status')) $table->dropIndex('idx_presensi_status');
                if ($indexExists('presensi', 'idx_presensi_kode_jk')) $table->dropIndex('idx_presensi_kode_jk');
                if ($indexExists('presensi', 'idx_presensi_id_mesin')) $table->dropIndex('idx_presensi_id_mesin');
            });
        }

        if (Schema::hasTable('karyawan')) {
            Schema::table('karyawan', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('karyawan', 'idx_karyawan_status_cabang_dept')) $table->dropIndex('idx_karyawan_status_cabang_dept');
                if ($indexExists('karyawan', 'idx_karyawan_status_karyawan')) $table->dropIndex('idx_karyawan_status_karyawan');
            });
        }

        if (Schema::hasTable('kontrak')) {
            Schema::table('kontrak', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('kontrak', 'idx_kontrak_nik_status_sampai')) $table->dropIndex('idx_kontrak_nik_status_sampai');
            });
        }

        if (Schema::hasTable('pelanggaran')) {
            Schema::table('pelanggaran', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('pelanggaran', 'idx_pelanggaran_nik_dari_sampai')) $table->dropIndex('idx_pelanggaran_nik_dari_sampai');
            });
        }

        if (Schema::hasTable('presensi_izinabsen')) {
            Schema::table('presensi_izinabsen', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('presensi_izinabsen', 'idx_izinabsen_nik_status_tgl')) $table->dropIndex('idx_izinabsen_nik_status_tgl');
            });
        }

        if (Schema::hasTable('presensi_izinsakit')) {
            Schema::table('presensi_izinsakit', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('presensi_izinsakit', 'idx_izinsakit_nik_status_tgl')) $table->dropIndex('idx_izinsakit_nik_status_tgl');
            });
        }

        if (Schema::hasTable('presensi_izincuti')) {
            Schema::table('presensi_izincuti', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('presensi_izincuti', 'idx_izincuti_nik_status_tgl')) $table->dropIndex('idx_izincuti_nik_status_tgl');
            });
        }

        if (Schema::hasTable('pembayaran_pinjaman')) {
            Schema::table('pembayaran_pinjaman', function (Blueprint $table) use ($indexExists) {
                if ($indexExists('pembayaran_pinjaman', 'idx_pinjaman_bayar_periode')) $table->dropIndex('idx_pinjaman_bayar_periode');
            });
        }
    }
};
