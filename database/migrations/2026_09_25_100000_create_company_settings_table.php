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
        if (!Schema::hasTable('company_settings')) {
            Schema::create('company_settings', function (Blueprint $table) {
                $table->id();
                $table->string('company_name')->default('Presence Universal HR');
                $table->string('legal_name')->nullable();
                $table->string('logo')->nullable();
                $table->string('business_type')->default('General');
                $table->string('npwp', 50)->nullable();
                $table->string('nib', 50)->nullable();
                $table->text('address')->nullable();
                $table->string('province', 100)->nullable();
                $table->string('city', 100)->nullable();
                $table->string('postal_code', 10)->nullable();
                $table->string('phone', 50)->nullable();
                $table->string('email', 100)->nullable();
                $table->string('website', 150)->nullable();
                $table->string('timezone', 50)->default('Asia/Jakarta');
                $table->string('locale', 10)->default('id');
                $table->string('currency', 10)->default('IDR');
                $table->string('date_format', 20)->default('d-m-Y');
                $table->unsignedTinyInteger('payroll_cutoff_date')->nullable()->default(20);
                $table->unsignedTinyInteger('payroll_payment_date')->nullable()->default(25);
                $table->string('theme_color_primary', 20)->default('#3C2A21');
                $table->string('theme_color_secondary', 20)->default('#634832');
                $table->string('app_name', 100)->default('Presence');
                $table->string('app_tagline', 255)->default('Universal HR Management System');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
