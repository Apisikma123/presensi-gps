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
        // 1. Add kode_cabang to presensi_jamkerja_byday
        if (Schema::hasTable('presensi_jamkerja_byday')) {
            Schema::table('presensi_jamkerja_byday', function (Blueprint $table) {
                if (!Schema::hasColumn('presensi_jamkerja_byday', 'kode_cabang')) {
                    $table->char('kode_cabang', 3)->nullable()->after('kode_jam_kerja');
                    $table->foreign('kode_cabang')
                        ->references('kode_cabang')
                        ->on('cabang')
                        ->nullOnDelete()
                        ->cascadeOnUpdate();
                }
            });
        }

        // 2. Add kode_cabang to presensi_jamkerja_bydate
        if (Schema::hasTable('presensi_jamkerja_bydate')) {
            Schema::table('presensi_jamkerja_bydate', function (Blueprint $table) {
                if (!Schema::hasColumn('presensi_jamkerja_bydate', 'kode_cabang')) {
                    $table->char('kode_cabang', 3)->nullable()->after('kode_jam_kerja');
                    $table->foreign('kode_cabang')
                        ->references('kode_cabang')
                        ->on('cabang')
                        ->nullOnDelete()
                        ->cascadeOnUpdate();
                }
            });
        }

        // 3. Add kode_cabang & early out fields to presensi
        if (Schema::hasTable('presensi')) {
            Schema::table('presensi', function (Blueprint $table) {
                if (!Schema::hasColumn('presensi', 'kode_cabang')) {
                    $table->char('kode_cabang', 3)->nullable()->after('id_mesin');
                    $table->foreign('kode_cabang')
                        ->references('kode_cabang')
                        ->on('cabang')
                        ->nullOnDelete()
                        ->cascadeOnUpdate();
                    $table->index(['kode_cabang', 'tanggal'], 'idx_presensi_cabang_tanggal');
                }

                if (!Schema::hasColumn('presensi', 'is_early_out')) {
                    $table->boolean('is_early_out')->default(false)->after('is_dispensasi');
                }

                if (!Schema::hasColumn('presensi', 'early_out_minutes')) {
                    $table->integer('early_out_minutes')->nullable()->after('is_early_out');
                }

                if (!Schema::hasColumn('presensi', 'early_out_reason')) {
                    $table->string('early_out_reason', 255)->nullable()->after('early_out_minutes');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('presensi')) {
            Schema::table('presensi', function (Blueprint $table) {
                if (Schema::hasColumn('presensi', 'kode_cabang')) {
                    $table->dropForeign(['kode_cabang']);
                    $table->dropIndex('idx_presensi_cabang_tanggal');
                    $table->dropColumn('kode_cabang');
                }
                if (Schema::hasColumn('presensi', 'early_out_reason')) {
                    $table->dropColumn('early_out_reason');
                }
                if (Schema::hasColumn('presensi', 'early_out_minutes')) {
                    $table->dropColumn('early_out_minutes');
                }
                if (Schema::hasColumn('presensi', 'is_early_out')) {
                    $table->dropColumn('is_early_out');
                }
            });
        }

        if (Schema::hasTable('presensi_jamkerja_bydate')) {
            Schema::table('presensi_jamkerja_bydate', function (Blueprint $table) {
                if (Schema::hasColumn('presensi_jamkerja_bydate', 'kode_cabang')) {
                    $table->dropForeign(['kode_cabang']);
                    $table->dropColumn('kode_cabang');
                }
            });
        }

        if (Schema::hasTable('presensi_jamkerja_byday')) {
            Schema::table('presensi_jamkerja_byday', function (Blueprint $table) {
                if (Schema::hasColumn('presensi_jamkerja_byday', 'kode_cabang')) {
                    $table->dropForeign(['kode_cabang']);
                    $table->dropColumn('kode_cabang');
                }
            });
        }
    }
};
