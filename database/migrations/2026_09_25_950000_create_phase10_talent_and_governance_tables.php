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
        // 1. Performance Reviews (Penilaian Kinerja / KPI)
        if (!Schema::hasTable('performance_reviews')) {
            Schema::create('performance_reviews', function (Blueprint $table) {
                $table->id();
                $table->string('review_code', 50)->unique();
                $table->char('nik', 9)->index();
                $table->char('reviewer_nik', 9)->nullable()->index();
                $table->string('period_title', 100); // e.g. "Q3 2026", "Tahunan 2026"
                $table->date('start_date');
                $table->date('end_date');
                $table->decimal('overall_score', 5, 2)->default(0);
                $table->string('rating_grade', 20)->default('MEETS'); // EXCEEDS, MEETS, NEEDS_IMPROVEMENT, POOR
                $table->text('strengths')->nullable();
                $table->text('areas_for_improvement')->nullable();
                $table->text('goals_next_period')->nullable();
                $table->string('status', 30)->default('DRAFT'); // DRAFT, SUBMITTED, APPROVED, ACKNOWLEDGED
                $table->timestamps();
            });
        }

        // 2. Performance Review KPI Details
        if (!Schema::hasTable('performance_kpis')) {
            Schema::create('performance_kpis', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('performance_review_id')->index();
                $table->string('kpi_name', 150);
                $table->decimal('weight', 5, 2)->default(25.00); // bobot %
                $table->string('target_value', 100)->nullable();
                $table->string('actual_value', 100)->nullable();
                $table->decimal('score', 5, 2)->default(0); // 0 - 100
                $table->decimal('weighted_score', 5, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('performance_review_id')
                    ->references('id')
                    ->on('performance_reviews')
                    ->onDelete('cascade');
            });
        }

        // 3. Employee Trainings (Pelatihan & Pengembangan)
        if (!Schema::hasTable('employee_trainings')) {
            Schema::create('employee_trainings', function (Blueprint $table) {
                $table->id();
                $table->string('training_code', 50)->unique();
                $table->char('nik', 9)->index();
                $table->string('title', 200);
                $table->string('provider', 150)->nullable();
                $table->date('start_date');
                $table->date('end_date');
                $table->integer('duration_hours')->default(8);
                $table->decimal('cost', 15, 2)->default(0);
                $table->string('certificate_number', 100)->nullable();
                $table->string('certificate_file', 255)->nullable();
                $table->string('status', 30)->default('COMPLETED'); // SCHEDULED, ONGOING, COMPLETED, CANCELLED
                $table->decimal('score', 5, 2)->nullable();
                $table->timestamps();
            });
        }

        // 4. Employee Warnings (Surat Peringatan & Disiplin)
        if (!Schema::hasTable('employee_warnings')) {
            Schema::create('employee_warnings', function (Blueprint $table) {
                $table->id();
                $table->string('sp_number', 100)->unique();
                $table->char('nik', 9)->index();
                $table->string('level', 30)->default('SP_1'); // TEGURAN_LISAN, SP_1, SP_2, SP_3, SKORSING
                $table->date('incident_date');
                $table->date('effective_date');
                $table->date('expiry_date');
                $table->text('violation_description');
                $table->string('pasal_pelanggaran', 255)->nullable();
                $table->text('action_plan')->nullable();
                $table->string('document_file', 255)->nullable();
                $table->string('status', 30)->default('ACTIVE'); // ACTIVE, EXPIRED, REVOKED
                $table->unsignedBigInteger('issued_by')->nullable();
                $table->timestamps();
            });
        }

        // 5. Employee Documents (Brankas Berkas Karyawan)
        if (!Schema::hasTable('employee_documents')) {
            Schema::create('employee_documents', function (Blueprint $table) {
                $table->id();
                $table->char('nik', 9)->index();
                $table->string('document_type', 50); // KTP, NPWP, KK, IJAZAH, KONTRAK, SERTIFIKAT, BPJS, OTHER
                $table->string('title', 150);
                $table->string('file_path', 255);
                $table->integer('file_size_kb')->nullable();
                $table->date('expiry_date')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->timestamps();
            });
        }

        // 6. Company Policies (Peraturan Perusahaan & SOP Vault)
        if (!Schema::hasTable('company_policies')) {
            Schema::create('company_policies', function (Blueprint $table) {
                $table->id();
                $table->string('policy_code', 50)->unique();
                $table->string('title', 200);
                $table->string('category', 50)->default('SOP_OPERASIONAL'); // PERATURAN_PERUSAHAAN, SOP_OPERASIONAL, SURAT_EDARAN, CODE_OF_CONDUCT, KEBIJAKAN_HR
                $table->date('effective_date');
                $table->string('version', 20)->default('1.0');
                $table->string('file_path', 255)->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 7. Employee Assets (Inventaris & Fasilitas Kerja)
        if (!Schema::hasTable('employee_assets')) {
            Schema::create('employee_assets', function (Blueprint $table) {
                $table->id();
                $table->string('asset_code', 50)->unique();
                $table->char('nik', 9)->index();
                $table->string('name', 150);
                $table->string('category', 50)->default('HARDWARE'); // HARDWARE, VEHICLE, OFFICE_EQUIPMENT, ACCESS_CARD, OTHER
                $table->string('serial_number', 100)->nullable();
                $table->date('assigned_date');
                $table->date('returned_date')->nullable();
                $table->string('condition', 30)->default('GOOD'); // EXCELLENT, GOOD, FAIR, DAMAGED
                $table->text('notes')->nullable();
                $table->string('status', 30)->default('ASSIGNED'); // ASSIGNED, RETURNED, LOST, UNDER_MAINTENANCE
                $table->timestamps();
            });
        }

        // 8. Announcements (Pengumuman Internal)
        if (!Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                $table->string('title', 200);
                $table->text('content');
                $table->string('category', 50)->default('GENERAL'); // GENERAL, HOLIDAY, POLICY, URGENT, EVENT
                $table->string('kode_dept', 10)->nullable()->index();
                $table->string('kode_cabang', 10)->nullable()->index();
                $table->string('attachment', 255)->nullable();
                $table->dateTime('published_at');
                $table->dateTime('expires_at')->nullable();
                $table->boolean('is_pinned')->default(false);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        // 9. Employee Incidents & Grievances (Kasus HR)
        if (!Schema::hasTable('employee_incidents')) {
            Schema::create('employee_incidents', function (Blueprint $table) {
                $table->id();
                $table->string('case_number', 50)->unique();
                $table->char('reporter_nik', 9)->nullable()->index();
                $table->char('subject_nik', 9)->nullable()->index();
                $table->string('title', 200);
                $table->date('incident_date');
                $table->string('category', 50)->default('DISCIPLINARY'); // DISCIPLINARY, HARASSMENT, SAFETY_ACCIDENT, FRAUD, DISPUTE, OTHER
                $table->text('description');
                $table->text('resolution_notes')->nullable();
                $table->string('status', 30)->default('OPEN'); // OPEN, INVESTIGATING, RESOLVED, CLOSED
                $table->unsignedBigInteger('handled_by')->nullable();
                $table->dateTime('resolved_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_incidents');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('employee_assets');
        Schema::dropIfExists('company_policies');
        Schema::dropIfExists('employee_documents');
        Schema::dropIfExists('employee_warnings');
        Schema::dropIfExists('employee_trainings');
        Schema::dropIfExists('performance_kpis');
        Schema::dropIfExists('performance_reviews');
    }
};
