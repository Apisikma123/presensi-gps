<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Phase 7: Indonesia Statutory Compliance (PPh 21 TER, BPJS TK, BPJS Kesehatan, THR Keagamaan)
     */
    public function up(): void
    {
        // 1. Unified Versioned Statutory Policy Rules
        if (!Schema::hasTable('indonesia_policy_rules')) {
            Schema::create('indonesia_policy_rules', function (Blueprint $table) {
                $table->id();
                $table->enum('policy_type', ['TAX', 'BPJS_TK', 'BPJS_KES', 'THR']);
                $table->string('name', 150);
                $table->string('version', 20)->default('1.0');
                $table->date('effective_from');
                $table->date('effective_to')->nullable();
                $table->text('source_reference')->nullable()->comment('Nomor resmi peraturan UU/PP/Permenaker');
                $table->json('config')->comment('Struktur tarif, threshold/plafon, rate brackets, koefisien');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['policy_type', 'is_active'], 'idx_policy_type_active');
            });
        }

        // 2. THR Payments & Distribution Events
        if (!Schema::hasTable('thr_payments')) {
            Schema::create('thr_payments', function (Blueprint $table) {
                $table->id();
                $table->string('event_name', 150)->comment('Contoh: THR Hari Raya Idul Fitri 2026');
                $table->unsignedSmallInteger('year');
                $table->date('distribution_date');
                $table->enum('status', ['DRAFT', 'CALCULATED', 'FINALIZED', 'PAID'])->default('DRAFT');
                $table->decimal('total_amount', 14, 2)->default(0.00);
                $table->unsignedInteger('employee_count')->default(0);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // 3. THR Payment Details (Per Employee Snapshot)
        if (!Schema::hasTable('thr_payment_details')) {
            Schema::create('thr_payment_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('thr_payment_id');
                $table->char('nik', 9);
                $table->date('hire_date');
                $table->decimal('service_months', 4, 1)->comment('Masa kerja dalam bulan');
                $table->decimal('base_salary', 12, 2)->comment('Upah dasar perhitungan (Gaji Pokok + Tunjangan Tetap)');
                $table->decimal('multiplier', 4, 3)->comment('Pengali: 1.000 jika >= 12 bulan, atau service_months/12');
                $table->decimal('thr_amount', 12, 2)->comment('Nominal THR yang dibayarkan');
                $table->string('category', 50)->default('PRORATA')->comment('FULL atau PRORATA');
                $table->timestamps();

                $table->unique(['thr_payment_id', 'nik'], 'idx_thr_payment_nik');
                $table->foreign('thr_payment_id')->references('id')->on('thr_payments')->cascadeOnDelete();
                $table->foreign('nik')->references('nik')->on('karyawan')->cascadeOnDelete()->cascadeOnUpdate();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thr_payment_details');
        Schema::dropIfExists('thr_payments');
        Schema::dropIfExists('indonesia_policy_rules');
    }
};
