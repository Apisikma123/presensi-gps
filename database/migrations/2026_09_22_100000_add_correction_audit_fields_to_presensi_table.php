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
        if (Schema::hasTable('presensi')) {
            Schema::table('presensi', function (Blueprint $table) {
                if (!Schema::hasColumn('presensi', 'last_corrected_by')) {
                    $table->unsignedBigInteger('last_corrected_by')->nullable()->after('keterangan');
                    $table->foreign('last_corrected_by')
                        ->references('id')
                        ->on('users')
                        ->nullOnDelete();
                }

                if (!Schema::hasColumn('presensi', 'last_correction_reason')) {
                    $table->text('last_correction_reason')->nullable()->after('last_corrected_by');
                }

                if (!Schema::hasColumn('presensi', 'last_corrected_at')) {
                    $table->dateTime('last_corrected_at')->nullable()->after('last_correction_reason');
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
                if (Schema::hasColumn('presensi', 'last_corrected_by')) {
                    $table->dropForeign(['last_corrected_by']);
                    $table->dropColumn('last_corrected_by');
                }
                if (Schema::hasColumn('presensi', 'last_correction_reason')) {
                    $table->dropColumn('last_correction_reason');
                }
                if (Schema::hasColumn('presensi', 'last_corrected_at')) {
                    $table->dropColumn('last_corrected_at');
                }
            });
        }
    }
};
