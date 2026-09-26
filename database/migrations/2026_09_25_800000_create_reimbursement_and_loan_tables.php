<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Phase 8: Payslip, Reimbursement, and Employee Loan (Kasbon) Tables
     */
    public function up(): void
    {
        // 1. Reimbursement Categories / Types
        if (!Schema::hasTable('reimbursement_types')) {
            Schema::create('reimbursement_types', function (Blueprint $table) {
                $table->id();
                $table->string('code', 30)->unique();
                $table->string('name', 100);
                $table->decimal('max_limit', 12, 2)->nullable()->comment('Plafon maksimal klaim (null = tidak terbatas)');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Reimbursements Claims
        if (!Schema::hasTable('reimbursements')) {
            Schema::create('reimbursements', function (Blueprint $table) {
                $table->id();
                $table->string('claim_number', 50)->unique();
                $table->char('nik', 9);
                $table->unsignedBigInteger('reimbursement_type_id');
                $table->date('claim_date');
                $table->decimal('amount', 12, 2);
                $table->text('description')->nullable();
                $table->string('receipt_attachment', 255)->nullable();
                $table->enum('status', ['SUBMITTED', 'APPROVED', 'REJECTED', 'PAID'])->default('SUBMITTED');
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->unsignedBigInteger('payroll_period_id')->nullable();
                $table->timestamps();

                $table->foreign('nik')->references('nik')->on('karyawan')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreign('reimbursement_type_id')->references('id')->on('reimbursement_types')->cascadeOnDelete();
                $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('payroll_period_id')->references('id')->on('payroll_periods')->nullOnDelete();
                $table->index(['nik', 'status'], 'idx_reimbursements_nik_status');
            });
        }

        // 3. Employee Loans (Kasbon) Header
        if (!Schema::hasTable('employee_loans')) {
            Schema::create('employee_loans', function (Blueprint $table) {
                $table->id();
                $table->string('loan_number', 50)->unique();
                $table->char('nik', 9);
                $table->decimal('loan_amount', 12, 2)->comment('Nominal pinjaman/kasbon pokok');
                $table->decimal('interest_rate', 5, 2)->default(0.00)->comment('Bunga pinjaman (%) jika ada');
                $table->decimal('total_amount', 12, 2)->comment('Total kewajiban pembayaran');
                $table->unsignedSmallInteger('installment_months')->default(1)->comment('Tenor cicilan dalam bulan');
                $table->decimal('monthly_installment', 12, 2)->comment('Cicilan per bulan');
                $table->decimal('remaining_amount', 12, 2)->comment('Sisa pinjaman belum terbayar');
                $table->date('start_date');
                $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED', 'ACTIVE', 'PAID_OFF'])->default('PENDING');
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('nik')->references('nik')->on('karyawan')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
                $table->index(['nik', 'status'], 'idx_loans_nik_status');
            });
        }

        // 4. Employee Loan Installment Schedule
        if (!Schema::hasTable('employee_loan_installments')) {
            Schema::create('employee_loan_installments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_loan_id');
                $table->unsignedSmallInteger('installment_number');
                $table->date('due_date');
                $table->decimal('amount', 12, 2);
                $table->decimal('paid_amount', 12, 2)->default(0.00);
                $table->timestamp('paid_at')->nullable();
                $table->unsignedBigInteger('payroll_period_id')->nullable();
                $table->enum('status', ['UNPAID', 'PAID'])->default('UNPAID');
                $table->timestamps();

                $table->foreign('employee_loan_id')->references('id')->on('employee_loans')->cascadeOnDelete();
                $table->foreign('payroll_period_id')->references('id')->on('payroll_periods')->nullOnDelete();
                $table->index(['employee_loan_id', 'status'], 'idx_installments_loan_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_loan_installments');
        Schema::dropIfExists('employee_loans');
        Schema::dropIfExists('reimbursements');
        Schema::dropIfExists('reimbursement_types');
    }
};
