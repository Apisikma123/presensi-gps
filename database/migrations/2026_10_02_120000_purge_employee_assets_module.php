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
        // 1. Drop employee_assets table if it exists
        Schema::dropIfExists('employee_assets');

        // 2. Delete module_features record for asset
        DB::table('module_features')->where('module_code', 'asset')->delete();

        // 3. Delete permissions for asset
        $assetPermissions = DB::table('permissions')->where('name', 'like', 'asset.%')->pluck('id');
        if ($assetPermissions->isNotEmpty()) {
            DB::table('role_has_permissions')->whereIn('permission_id', $assetPermissions)->delete();
            DB::table('model_has_permissions')->whereIn('permission_id', $assetPermissions)->delete();
            DB::table('permissions')->whereIn('id', $assetPermissions)->delete();
        }

        // 4. Delete permission group if empty
        $group = DB::table('permission_groups')->where('name', 'Aset & Fasilitas')->first();
        if ($group) {
            $hasOther = DB::table('permissions')->where('id_permission_group', $group->id)->exists();
            if (!$hasOther) {
                DB::table('permission_groups')->where('id', $group->id)->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('employee_assets')) {
            Schema::create('employee_assets', function (Blueprint $table) {
                $table->id();
                $table->string('asset_code', 50)->unique();
                $table->char('nik', 9)->index();
                $table->string('name', 150);
                $table->string('category', 50)->default('HARDWARE');
                $table->string('serial_number', 100)->nullable();
                $table->date('assigned_date');
                $table->date('returned_date')->nullable();
                $table->string('condition', 30)->default('GOOD');
                $table->text('notes')->nullable();
                $table->string('status', 30)->default('ASSIGNED');
                $table->timestamps();
            });
        }
    }
};
