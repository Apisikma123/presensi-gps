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
        // 1. Job Vacancies (Lowongan Kerja)
        if (!Schema::hasTable('recruitment_vacancies')) {
            Schema::create('recruitment_vacancies', function (Blueprint $table) {
                $table->id();
                $table->string('vacancy_code', 50)->unique();
                $table->string('title', 150);
                $table->string('kode_dept', 10)->nullable()->index();
                $table->string('kode_cabang', 10)->nullable()->index();
                $table->string('employment_type', 50)->default('PKWT'); // PKWT, PKWTT, PROBATION, INTERNSHIP, FREELANCE
                $table->integer('quota')->default(1);
                $table->integer('min_experience_years')->default(0);
                $table->decimal('salary_min', 15, 2)->nullable();
                $table->decimal('salary_max', 15, 2)->nullable();
                $table->text('description')->nullable();
                $table->text('requirements')->nullable();
                $table->date('deadline')->nullable();
                $table->string('status', 30)->default('OPEN'); // DRAFT, OPEN, CLOSED, CANCELLED
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        // 2. Candidates (Pelamar)
        if (!Schema::hasTable('recruitment_candidates')) {
            Schema::create('recruitment_candidates', function (Blueprint $table) {
                $table->id();
                $table->string('candidate_code', 50)->unique();
                $table->unsignedBigInteger('recruitment_vacancy_id')->index();
                $table->string('name', 150);
                $table->string('email', 150)->index();
                $table->string('phone', 50)->nullable();
                $table->enum('gender', ['L', 'P'])->default('L');
                $table->decimal('expected_salary', 15, 2)->nullable();
                $table->string('resume_file', 255)->nullable();
                $table->string('portfolio_url', 255)->nullable();
                $table->text('notes')->nullable();
                $table->string('stage', 30)->default('APPLIED'); // APPLIED, SCREENING, INTERVIEW, OFFERING, HIRED, REJECTED
                $table->dateTime('interview_scheduled_at')->nullable();
                $table->text('interview_notes')->nullable();
                $table->decimal('offered_salary', 15, 2)->nullable();
                $table->char('hired_nik', 9)->nullable()->index();
                $table->dateTime('hired_at')->nullable();
                $table->timestamps();

                $table->foreign('recruitment_vacancy_id')
                    ->references('id')
                    ->on('recruitment_vacancies')
                    ->onDelete('cascade');
            });
        }

        // 3. Onboarding Templates
        if (!Schema::hasTable('onboarding_templates')) {
            Schema::create('onboarding_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->text('description')->nullable();
                $table->string('kode_dept', 10)->nullable()->index();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 4. Onboarding Template Tasks
        if (!Schema::hasTable('onboarding_template_tasks')) {
            Schema::create('onboarding_template_tasks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('onboarding_template_id')->index();
                $table->string('task_name', 200);
                $table->string('category', 50)->default('HR_BRIEFING'); // DOCUMENT, IT_ACCESS, HR_BRIEFING, TRAINING, ASSET
                $table->integer('day_offset')->default(1);
                $table->boolean('is_mandatory')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('onboarding_template_id')
                    ->references('id')
                    ->on('onboarding_templates')
                    ->onDelete('cascade');
            });
        }

        // 5. Employee Onboardings
        if (!Schema::hasTable('employee_onboardings')) {
            Schema::create('employee_onboardings', function (Blueprint $table) {
                $table->id();
                $table->char('nik', 9)->index();
                $table->unsignedBigInteger('onboarding_template_id')->nullable()->index();
                $table->date('start_date');
                $table->date('target_completion_date')->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->string('status', 30)->default('IN_PROGRESS'); // IN_PROGRESS, COMPLETED, OVERDUE
                $table->char('mentor_nik', 9)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 6. Employee Onboarding Tasks
        if (!Schema::hasTable('employee_onboarding_tasks')) {
            Schema::create('employee_onboarding_tasks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_onboarding_id')->index();
                $table->string('task_name', 200);
                $table->string('category', 50)->default('HR_BRIEFING');
                $table->date('due_date')->nullable();
                $table->boolean('is_completed')->default(false);
                $table->dateTime('completed_at')->nullable();
                $table->unsignedBigInteger('completed_by')->nullable();
                $table->text('notes')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('employee_onboarding_id')
                    ->references('id')
                    ->on('employee_onboardings')
                    ->onDelete('cascade');
            });
        }

        // 7. Offboarding Clearance Items
        if (!Schema::hasTable('offboarding_clearances')) {
            Schema::create('offboarding_clearances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_resignation_id')->index();
                $table->string('item_name', 200);
                $table->string('department', 50)->default('HR'); // IT, HR, FINANCE, OPERATIONAL
                $table->boolean('is_cleared')->default(false);
                $table->unsignedBigInteger('cleared_by')->nullable();
                $table->dateTime('cleared_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('employee_resignation_id')
                    ->references('id')
                    ->on('employee_resignations')
                    ->onDelete('cascade');
            });
        }

        // 8. Add settlement columns to employee_resignations if not present
        if (Schema::hasTable('employee_resignations')) {
            Schema::table('employee_resignations', function (Blueprint $table) {
                if (!Schema::hasColumn('employee_resignations', 'severance_pay')) {
                    $table->decimal('severance_pay', 15, 2)->default(0)->after('status_clearance');
                }
                if (!Schema::hasColumn('employee_resignations', 'service_pay')) {
                    $table->decimal('service_pay', 15, 2)->default(0)->after('severance_pay');
                }
                if (!Schema::hasColumn('employee_resignations', 'compensation_pay')) {
                    $table->decimal('compensation_pay', 15, 2)->default(0)->after('service_pay');
                }
                if (!Schema::hasColumn('employee_resignations', 'final_salary_pay')) {
                    $table->decimal('final_salary_pay', 15, 2)->default(0)->after('compensation_pay');
                }
                if (!Schema::hasColumn('employee_resignations', 'deductions_pay')) {
                    $table->decimal('deductions_pay', 15, 2)->default(0)->after('final_salary_pay');
                }
                if (!Schema::hasColumn('employee_resignations', 'total_settlement')) {
                    $table->decimal('total_settlement', 15, 2)->default(0)->after('deductions_pay');
                }
                if (!Schema::hasColumn('employee_resignations', 'settlement_status')) {
                    $table->string('settlement_status', 30)->default('PENDING')->after('total_settlement'); // PENDING, CALCULATED, PAID
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offboarding_clearances');
        Schema::dropIfExists('employee_onboarding_tasks');
        Schema::dropIfExists('employee_onboardings');
        Schema::dropIfExists('onboarding_template_tasks');
        Schema::dropIfExists('onboarding_templates');
        Schema::dropIfExists('recruitment_candidates');
        Schema::dropIfExists('recruitment_vacancies');
    }
};
