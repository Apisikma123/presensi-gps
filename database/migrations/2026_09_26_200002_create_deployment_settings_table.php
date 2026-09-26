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
        if (!Schema::hasTable('deployment_settings')) {
            Schema::create('deployment_settings', function (Blueprint $table) {
                $table->string('setting_key', 50)->primary();
                $table->text('setting_value')->nullable();
                $table->timestamps();
            });

            // Seed default deployment settings
            DB::table('deployment_settings')->insertOrIgnore([
                ['setting_key' => 'deployment_package_code', 'setting_value' => 'FULL_HR', 'created_at' => now(), 'updated_at' => now()],
                ['setting_key' => 'deployment_addons', 'setting_value' => '[]', 'created_at' => now(), 'updated_at' => now()],
                ['setting_key' => 'deployment_locked', 'setting_value' => '1', 'created_at' => now(), 'updated_at' => now()],
                ['setting_key' => 'package_maintenance_mode', 'setting_value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployment_settings');
    }
};
