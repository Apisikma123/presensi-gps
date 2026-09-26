<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Phase 5: Overtime Management (SPK Lembur & Overtime Policies)
     */
    public function up(): void
    {
        // 1. Overtime Policies (Standar Depnaker PP 35/2021 & aturan kustom perusahaan)
        if (!Schema::hasTable('overtime_policies')) {
            Schema::create('overtime_policies', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('code', 50)->unique();
                $table->string('version', 20)->default('1.0');
                $table->date('effective_from');
                $table->date('effective_to')->nullable();
                $table->json('rules')->comment('Tier multiplier configurations, day type rules, max hours');
                $table->string('source_reference', 255)->nullable()->comment('Contoh: PP 35/2021 Pasal 31 & Kepmenakertrans 102/2004');
                $table->decimal('max_hours_per_day', 4, 1)->default(4.0);
                $table->decimal('max_hours_per_week', 4, 1)->default(18.0);
                $table->boolean('requires_meal_allowance_after_4h')->default(true);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['is_active', 'code'], 'idx_overtime_policy_active');
            });
        }

        // 2. Lembur (Surat Perintah Kerja Lembur / SPK & Log Realisasi)
        if (!Schema::hasTable('lembur')) {
            Schema::create('lembur', function (Blueprint $table) {
                $table->id();
                $table->string('no_spk', 50)->unique()->comment('Nomor SPK Lembur resmi, e.g. SPK/202609/0001');
                $table->date('tanggal');
                $table->char('nik', 9);
                $table->unsignedBigInteger('overtime_policy_id')->nullable();
                $table->enum('day_type', ['WORKDAY', 'OFFDAY_6DAYS', 'OFFDAY_5DAYS', 'PUBLIC_HOLIDAY'])->default('WORKDAY');
                $table->dateTime('lembur_mulai');
                $table->dateTime('lembur_selesai');
                $table->dateTime('lembur_in')->nullable()->comment('Waktu aktual mulai presensi lembur');
                $table->dateTime('lembur_out')->nullable()->comment('Waktu aktual selesai presensi lembur');
                $table->string('foto_lembur_in')->nullable();
                $table->string('foto_lembur_out')->nullable();
                $table->string('lokasi_lembur_in')->nullable();
                $table->string('lokasi_lembur_out')->nullable();
                $table->integer('planned_duration_minutes')->default(0);
                $table->integer('actual_duration_minutes')->nullable();
                $table->integer('approved_duration_minutes')->nullable();
                $table->decimal('calculated_rate_hours', 5, 2)->default(0.00)->comment('Ekuivalen jam bayar hasil formula Depnaker');
                $table->enum('status', ['DRAFT', 'PENDING', 'APPROVED', 'REJECTED', 'COMPLETED', 'CANCELLED'])->default('PENDING');
                $table->text('keterangan')->comment('Uraian tugas pekerjaan lembur');
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->text('notes')->nullable()->comment('Catatan persetujuan / penolakan supervisor/HR');
                $table->timestamps();

                $table->index(['nik', 'tanggal'], 'idx_lembur_nik_tanggal');
                $table->index(['status', 'nik'], 'idx_lembur_status_nik');
                $table->index(['tanggal', 'status'], 'idx_lembur_tanggal_status');

                $table->foreign('nik')->references('nik')->on('karyawan')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreign('overtime_policy_id')->references('id')->on('overtime_policies')->nullOnDelete();
                $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lembur');
        Schema::dropIfExists('overtime_policies');
    }
};
