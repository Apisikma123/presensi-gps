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
        Schema::table('karyawan_wajah', function (Blueprint $table) {
            if (!Schema::hasColumn('karyawan_wajah', 'descriptor')) {
                $table->longText('descriptor')->nullable()->after('wajah');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan_wajah', function (Blueprint $table) {
            if (Schema::hasColumn('karyawan_wajah', 'descriptor')) {
                $table->dropColumn('descriptor');
            }
        });
    }
};
