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
            if (!Schema::hasColumn('presensi', 'is_archived')) {
                $table->boolean('is_archived')->default(false)->after('foto_out')->index('idx_presensi_is_archived');
            }
            if (!Schema::hasColumn('presensi', 'foto_in_archived')) {
                $table->boolean('foto_in_archived')->default(false)->after('is_archived');
            }
            if (!Schema::hasColumn('presensi', 'foto_out_archived')) {
                $table->boolean('foto_out_archived')->default(false)->after('foto_in_archived');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            if (Schema::hasColumn('presensi', 'is_archived')) {
                $table->dropIndex('idx_presensi_is_archived');
                $table->dropColumn('is_archived');
            }
            if (Schema::hasColumn('presensi', 'foto_in_archived')) {
                $table->dropColumn('foto_in_archived');
            }
            if (Schema::hasColumn('presensi', 'foto_out_archived')) {
                $table->dropColumn('foto_out_archived');
            }
        });
    }
};
