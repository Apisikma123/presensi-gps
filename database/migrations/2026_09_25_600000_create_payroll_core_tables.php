<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Phase 6: Payroll Core (Periods, Components, Employee Assignments, Snapshots)
     */
    public function up(): void
    {
        // 1. Payroll Periods
        if (!Schema::hasTable('payroll_periods')) {
            Schema::create('payroll_periods', function (Blueprint $table) {
                $table->id();
                $table->unsignedTinyInteger('period_month');
                $table->unsignedSmallInteger('period_year');
                $table->date('cutoff_start');
                $table->date('cutoff_end');
                $table->date('payment_date');
                $table->enum('status', ['DRAFT', 'CALCULATED', 'REVIEW', 'FINALIZED', 'PAID', 'CANCELLED'])->default('DRAFT');
                $table->unsignedBigInteger('finalized_by')->nullable();
                $table->dateTime('finalized_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['period_year', 'period_month'], 'idx_payroll_year_month');
                $table->index(['status', 'period_year'], 'idx_payroll_status_year');
                $table->foreign('finalized_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // 2. Salary Components
        if (!Schema::hasTable('salary_components')) {
            Schema::create('salary_components', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50)->unique();
                $table->string('name', 100);
                $table->enum('type', ['EARNING', 'DEDUCTION'])->default('EARNING');
                $table->boolean('is_fixed')->default(true);
                $table->boolean('is_taxable')->default(true);
                $table->boolean('is_bpjs_basis')->default(false);
                $table->boolean('is_recurring')->default(true);
                $table->decimal('default_amount', 12, 2)->default(0.00);
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['type', 'is_active'], 'idx_salary_comp_type_active');
            });
        }

        // 3. Employee Salary Assignments
        if (!Schema::hasTable('employee_salary_assignments')) {
            Schema::create('employee_salary_assignments', function (Blueprint $table) {
                $table->id();
                $table->char('nik', 9);
                $table->unsignedBigInteger('salary_component_id');
                $table->decimal('amount', 12, 2)->default(0.00);
                $table->date('effective_date');
                $table->date('end_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['nik', 'is_active'], 'idx_emp_salary_nik_active');
                $table->foreign('nik')->references('nik')->on('karyawan')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreign('salary_component_id')->references('id')->on('salary_components')->cascadeOnDelete();
            });
        }

        // 4. Payroll Details (Calculation Snapshot)
        if (!Schema::hasTable('payroll_details')) {
            Schema::create('payroll_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('payroll_period_id');
                $table->char('nik', 9);
                $table->decimal('basic_salary', 12, 2)->default(0.00);
                $table->decimal('total_allowances', 12, 2)->default(0.00);
                $table->decimal('total_overtime_pay', 12, 2)->default(0.00);
                $table->decimal('total_deductions', 12, 2)->default(0.00);
                $table->decimal('take_home_pay', 12, 2)->default(0.00);
                $table->json('components_breakdown')->comment('Snapshot of all earning/deduction components & calculation metadata');
                $table->enum('status', ['DRAFT', 'VERIFIED', 'PAID'])->default('DRAFT');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['payroll_period_id', 'nik'], 'idx_payroll_period_nik');
                $table->index(['nik', 'status'], 'idx_payroll_detail_nik_status');
                $table->foreign('payroll_period_id')->references('id')->on('payroll_periods')->cascadeOnDelete();
                $table->foreign('nik')->references('nik')->on('karyawan')->cascadeOnDelete()->cascadeOnUpdate();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_details');
        Schema::dropIfExists('employee_salary_assignments');
        Schema::dropIfExists('salary_components');
        Schema::dropIfExists('payroll_periods');
    }
};
