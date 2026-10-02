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
        Schema::table('pengaturan_umum', function (Blueprint $table) {
            $cols = [
                'cloud_id',
                'api_key',
                'domain_wa_gateway',
                'wa_api_key',
                'provider_wa',
                'tujuan_notifikasi_wa',
                'id_group_wa',
                'notifikasi_wa',
            ];

            foreach ($cols as $col) {
                if (Schema::hasColumn('pengaturan_umum', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_umum', function (Blueprint $table) {
            if (!Schema::hasColumn('pengaturan_umum', 'provider_wa')) {
                $table->string('provider_wa', 20)->nullable();
            }
            if (!Schema::hasColumn('pengaturan_umum', 'api_key')) {
                $table->string('api_key')->nullable();
            }
            if (!Schema::hasColumn('pengaturan_umum', 'cloud_id')) {
                $table->string('cloud_id')->nullable();
            }
            if (!Schema::hasColumn('pengaturan_umum', 'id_group_wa')) {
                $table->string('id_group_wa')->nullable();
            }
        });
    }
};
