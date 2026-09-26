<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('karyawan') && !Schema::hasColumn('karyawan', 'ptkp_status')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->string('ptkp_status', 10)->default('TK/0')->after('npwp_number')->comment('Status PTKP: TK/0, TK/1, TK/2, TK/3, K/0, K/1, K/2, K/3');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('karyawan') && Schema::hasColumn('karyawan', 'ptkp_status')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->dropColumn('ptkp_status');
            });
        }
    }
};
