<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Drop id_mesin from presensi if exists
        if (Schema::hasTable('presensi') && Schema::hasColumn('presensi', 'id_mesin')) {
            Schema::table('presensi', function (Blueprint $table) {
                try {
                    $table->dropForeign(['id_mesin']);
                } catch (\Exception $e) {
                }
                $table->dropColumn('id_mesin');
            });
        }

        // 2. Drop dead legacy tables not present in view or system
        $deadTables = [
            'mesin_fingerprints',
            'log_mesin_presensis',
            'messages',
            'wamessages',
        ];

        foreach ($deadTables as $table) {
            Schema::dropIfExists($table);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed for cleanup of dead tables
    }
};
