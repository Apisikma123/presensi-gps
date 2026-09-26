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
        // 1. Table Kontrak (Contracts)
        if (!Schema::hasTable('kontrak')) {
            Schema::create('kontrak', function (Blueprint $table) {
                $table->id();
                $table->string('no_kontrak', 100)->unique();
                $table->char('nik', 9)->index();
                $table->string('jenis_kontrak', 50)->default('PKWT'); // PKWT, PKWTT, PROBATION, INTERNSHIP, FREELANCE
                $table->date('tanggal_mulai');
                $table->date('tanggal_selesai')->nullable(); // Nullable for PKWTT (Permanent)
                $table->string('status', 30)->default('ACTIVE'); // ACTIVE, EXPIRING_SOON, EXPIRED, RENEWED, TERMINATED
                $table->string('jabatan', 100)->nullable();
                $table->char('kode_cabang', 3)->nullable();
                $table->char('kode_dept', 3)->nullable();
                $table->decimal('gaji_pokok', 15, 2)->nullable();
                $table->string('dokumen', 255)->nullable();
                $table->text('keterangan')->nullable();
                $table->integer('reminder_days')->default(30);
                $table->boolean('reminder_sent')->default(false);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index(['nik', 'status'], 'idx_kontrak_nik_status');
                $table->index(['tanggal_selesai', 'status'], 'idx_kontrak_selesai_status');
            });
        }

        // 2. Table Employee Movements (Mutasi, Promosi, Demosi, Rotasi)
        if (!Schema::hasTable('employee_movements')) {
            Schema::create('employee_movements', function (Blueprint $table) {
                $table->id();
                $table->string('no_sk', 100)->nullable(); // Nomor Surat Keputusan
                $table->char('nik', 9)->index();
                $table->string('movement_type', 50); // BRANCH_TRANSFER, DEPT_TRANSFER, DIVISION_CHANGE, POSITION_CHANGE, PROMOTION, DEMOTION, SUPERVISOR_CHANGE, STATUS_CHANGE, SALARY_CHANGE
                $table->date('effective_date');
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->text('reason')->nullable();
                $table->string('document_path', 255)->nullable();
                $table->string('status', 30)->default('APPLIED'); // PENDING, APPROVED, APPLIED, CANCELLED
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index(['nik', 'effective_date'], 'idx_movements_nik_date');
                $table->index(['movement_type'], 'idx_movements_type');
            });
        }

        // 3. Table Employee Resignations / Offboarding
        if (!Schema::hasTable('employee_resignations')) {
            Schema::create('employee_resignations', function (Blueprint $table) {
                $table->id();
                $table->char('nik', 9)->index();
                $table->date('tanggal_pengajuan');
                $table->date('tanggal_keluar');
                $table->string('kategori_keluar', 50)->default('RESIGNED'); // RESIGNED, END_OF_CONTRACT, TERMINATED, RETIRED, DECEASED, OTHER
                $table->text('alasan')->nullable();
                $table->string('status_clearance', 30)->default('PENDING'); // PENDING, IN_PROGRESS, CLEARED
                $table->string('dokumen', 255)->nullable();
                $table->text('catatan_hr')->nullable();
                $table->string('status', 30)->default('APPROVED'); // PENDING, APPROVED, REJECTED
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamps();

                $table->index(['nik', 'tanggal_keluar'], 'idx_resignation_nik_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_resignations');
        Schema::dropIfExists('employee_movements');
        Schema::dropIfExists('kontrak');
    }
};
