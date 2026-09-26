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
            if (!Schema::hasColumn('presensi', 'is_terlambat')) {
                $table->tinyInteger('is_terlambat')->nullable()->after('status')->index();
            }
            if (!Schema::hasColumn('presensi', 'menit_terlambat')) {
                $table->integer('menit_terlambat')->nullable()->default(0)->after('is_terlambat');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            if (Schema::hasColumn('presensi', 'menit_terlambat')) {
                $table->dropColumn('menit_terlambat');
            }
            if (Schema::hasColumn('presensi', 'is_terlambat')) {
                $table->dropColumn('is_terlambat');
            }
        });
    }
};
