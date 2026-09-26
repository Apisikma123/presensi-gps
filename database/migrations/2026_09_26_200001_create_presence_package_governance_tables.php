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
        // 1. Add entitlement & control columns to module_features
        if (Schema::hasTable('module_features')) {
            Schema::table('module_features', function (Blueprint $table) {
                if (!Schema::hasColumn('module_features', 'is_entitled')) {
                    $table->boolean('is_entitled')->default(true)->after('is_enabled');
                }
                if (!Schema::hasColumn('module_features', 'control_level')) {
                    $table->string('control_level', 30)->default('client_toggleable')->after('is_entitled');
                }
                if (!Schema::hasColumn('module_features', 'is_locked')) {
                    $table->boolean('is_locked')->default(false)->after('control_level');
                }
                if (!Schema::hasColumn('module_features', 'entitlement_source')) {
                    $table->string('entitlement_source', 50)->default('package')->after('is_locked');
                }
                if (!Schema::hasColumn('module_features', 'package_code')) {
                    $table->string('package_code', 50)->default('FULL_HR')->after('entitlement_source');
                }
                if (!Schema::hasColumn('module_features', 'activated_at')) {
                    $table->timestamp('activated_at')->nullable()->after('package_code');
                }
                if (!Schema::hasColumn('module_features', 'deactivated_at')) {
                    $table->timestamp('deactivated_at')->nullable()->after('activated_at');
                }
            });
        }

        // 2. Create deployment_packages table
        if (!Schema::hasTable('deployment_packages')) {
            Schema::create('deployment_packages', function (Blueprint $table) {
                $table->id();
                $table->string('package_code', 50)->unique();
                $table->string('package_name', 100);
                $table->string('category', 50)->default('General');
                $table->text('description')->nullable();
                $table->json('entitled_modules');
                $table->json('default_enabled_modules')->nullable();
                $table->json('locked_modules')->nullable();
                $table->json('client_toggleable_modules')->nullable();
                $table->boolean('is_system')->default(true);
                $table->timestamps();
            });
        }

        // 3. Create deployment_package_history table
        if (!Schema::hasTable('deployment_package_history')) {
            Schema::create('deployment_package_history', function (Blueprint $table) {
                $table->id();
                $table->string('old_package_code', 50)->nullable();
                $table->string('new_package_code', 50);
                $table->string('action', 50); // INITIAL_DEPLOYMENT, UPGRADE, DOWNGRADE, ADDON_ADDED, ADDON_REMOVED
                $table->unsignedBigInteger('actor_id')->nullable();
                $table->string('actor_name', 100)->nullable();
                $table->text('notes')->nullable();
                $table->json('diff_summary')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // 4. Create module_state_history table
        if (!Schema::hasTable('module_state_history')) {
            Schema::create('module_state_history', function (Blueprint $table) {
                $table->id();
                $table->string('module_code', 50);
                $table->json('old_state')->nullable();
                $table->json('new_state')->nullable();
                $table->string('action', 50); // CLIENT_TOGGLE, PACKAGE_UPGRADE, PACKAGE_DOWNGRADE, DEPLOYMENT_SETUP
                $table->unsignedBigInteger('actor_id')->nullable();
                $table->string('actor_name', 100)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_state_history');
        Schema::dropIfExists('deployment_package_history');
        Schema::dropIfExists('deployment_packages');

        if (Schema::hasTable('module_features')) {
            Schema::table('module_features', function (Blueprint $table) {
                $cols = ['is_entitled', 'control_level', 'is_locked', 'entitlement_source', 'package_code', 'activated_at', 'deactivated_at'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('module_features', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
