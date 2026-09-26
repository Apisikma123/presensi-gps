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
        // 1. Table attendance_policies
        if (!Schema::hasTable('attendance_policies')) {
            Schema::create('attendance_policies', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->default('Kebijakan Presensi Standar');
                $table->boolean('is_default')->default(true);
                $table->boolean('require_gps')->default(true);
                $table->boolean('require_face_recognition')->default(true);
                $table->boolean('require_photo')->default(true);
                $table->integer('allow_late_tolerance_minutes')->default(5);
                $table->integer('max_out_of_radius_meters')->default(50);
                $table->boolean('allow_out_of_radius')->default(false);
                $table->boolean('allow_overnight')->default(true);
                $table->boolean('enable_early_checkout_penalty')->default(false);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 2. Table leave_types (Generic / Statutory Leave Types)
        if (!Schema::hasTable('leave_types')) {
            Schema::create('leave_types', function (Blueprint $table) {
                $table->id();
                $table->string('code', 20)->unique();
                $table->string('name', 100);
                $table->boolean('is_paid')->default(true);
                $table->boolean('requires_attachment')->default(false);
                $table->boolean('requires_approval')->default(true);
                $table->boolean('uses_quota')->default(true);
                $table->string('quota_type', 20)->default('ANNUAL'); // ANNUAL, MONTHLY, UNLIMITED
                $table->decimal('default_quota', 5, 1)->default(12.0);
                $table->char('gender_restriction', 1)->nullable(); // M, F, or null
                $table->integer('min_notice_days')->default(0);
                $table->integer('max_consecutive_days')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 3. Table leave_quotas (Per-employee leave balance)
        if (!Schema::hasTable('leave_quotas')) {
            Schema::create('leave_quotas', function (Blueprint $table) {
                $table->id();
                $table->char('nik', 9)->index();
                $table->unsignedBigInteger('leave_type_id')->index();
                $table->integer('year')->index();
                $table->decimal('opening_balance', 5, 1)->default(0.0);
                $table->decimal('earned', 5, 1)->default(0.0);
                $table->decimal('used', 5, 1)->default(0.0);
                $table->decimal('adjustment', 5, 1)->default(0.0);
                $table->decimal('carry_forward', 5, 1)->default(0.0);
                $table->decimal('expired', 5, 1)->default(0.0);
                $table->decimal('closing_balance', 5, 1)->default(0.0);
                $table->timestamps();

                $table->unique(['nik', 'leave_type_id', 'year'], 'uniq_nik_leavetype_year');
            });
        }

        // 4. Table leave_quota_transactions (Audit ledger for deductions & additions)
        if (!Schema::hasTable('leave_quota_transactions')) {
            Schema::create('leave_quota_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('leave_quota_id')->index();
                $table->string('type', 30); // OPENING, EARNED, USED, ADJUSTMENT, CARRY_FORWARD, EXPIRED
                $table->decimal('amount', 5, 1);
                $table->string('reference_type', 100)->nullable();
                $table->string('reference_id', 100)->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('leave_quota_id')->references('id')->on('leave_quotas')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_quota_transactions');
        Schema::dropIfExists('leave_quotas');
        Schema::dropIfExists('leave_types');
        Schema::dropIfExists('attendance_policies');
    }
};
