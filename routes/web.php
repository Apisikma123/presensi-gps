<?php

use App\Http\Controllers\CabangController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\DispensasiController;
use App\Http\Controllers\FacerecognitionController;
use App\Http\Controllers\GeneralsettingController;
use App\Http\Controllers\HariliburController;
use App\Http\Controllers\PengajuanizinController;
use App\Http\Controllers\IzinabsenController;
use App\Http\Controllers\IzincutiController;
use App\Http\Controllers\IzinsakitController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\JamkerjaController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\Permission_groupController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrackingPresensiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IconGeneratorController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\ModuleFeatureController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\KontrakController;
use App\Http\Controllers\EmployeeMovementController;
use App\Http\Controllers\EmployeeResignationController;
use App\Http\Controllers\AttendancePolicyController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\LeaveQuotaController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\OvertimePolicyController;
use App\Http\Controllers\PayrollPeriodController;
use App\Http\Controllers\SalaryComponentController;
use App\Http\Controllers\EmployeeSalaryController;
use App\Http\Controllers\IndonesiaComplianceController;
use App\Http\Controllers\ThrPaymentController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\ReimbursementController;
use App\Http\Controllers\EmployeeLoanController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\OffboardingController;
use App\Http\Controllers\PerformanceReviewController;
use App\Http\Controllers\TalentGovernanceController;
use App\Http\Controllers\UniversalReportController;
use App\Http\Controllers\OrgChartController;
use App\Http\Controllers\EmployeeImportController;
use App\Http\Controllers\HelpCenterController;
use App\Http\Controllers\SettingsHubController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\PresetMatrixController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Whitelist - Final Scope Lock)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('auth.loginuser');
    })->name('loginuser');

    Route::get('/loginuser', function () {
        return redirect()->route('loginuser');
    });
});

Route::middleware('auth')->group(function () {

    // Profile
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile.index');
        Route::put('/profile', 'update')->name('profile.update');
        Route::get('/profile/editprofile', 'editprofile')->name('profile.editprofile');
        Route::post('/profile/updateprofile', 'updateprofile')->name('profile.updateprofile');
    });

    // Dashboard
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard.index');
        Route::post('/dashboard/get-karyawan-presensi', 'getKaryawanPresensi')->name('dashboard.get.karyawan.presensi')->can('presensi.index');
        Route::get('/dashboard/global-search', 'globalSearch')->name('dashboard.global-search')->middleware('throttle:global-search');
    });
    Route::get('/help/drawer-content', [HelpCenterController::class, 'drawerContent'])->name('help.drawer-content');

    // Manajemen Karyawan (Attendance Focused)
    Route::controller(KaryawanController::class)->group(function () {
        Route::get('/karyawan', 'index')->name('karyawan.index')->can('karyawan.index');
        Route::get('/karyawan/create', 'create')->name('karyawan.create')->can('karyawan.create');
        Route::post('/karyawan', 'store')->name('karyawan.store')->can('karyawan.create');
        Route::get('/karyawan/export', 'export')->name('karyawan.export')->can('karyawan.index');
        Route::get('/karyawan/{nik}/edit', 'edit')->name('karyawan.edit')->can('karyawan.edit');
        Route::put('/karyawan/{nik}', 'update')->name('karyawan.update')->can('karyawan.edit');
        Route::delete('/karyawan/{nik}', 'destroy')->name('karyawan.delete')->can('karyawan.delete');
        Route::get('/karyawan/{nik}/show', 'show')->name('karyawan.show')->can('karyawan.show');

        // Akun & Security (CSRF & Method Safe)
        Route::post('/karyawan/{nik}/createuser', 'createuser')->name('karyawan.createuser')->can('users.create');
        Route::match(['post', 'delete'], '/karyawan/{nik}/deleteuser', 'deleteuser')->name('karyawan.deleteuser')->can('users.create');
        Route::post('/karyawan/{nik}/lockunlocklocation', 'lockunlocklocation')->name('karyawan.lockunlocklocation')->can('karyawan.edit');
        Route::post('/karyawan/{nik}/lockunlockjamkerja', 'lockunlockjamkerja')->name('karyawan.lockunlockjamkerja')->can('karyawan.edit');

        // Pengaturan Jam Kerja / Roster Karyawan (By Day & By Date)
        Route::get('/karyawan/{nik}/setjamkerja', 'setjamkerja')->name('karyawan.setjamkerja')->can('karyawan.edit');
        Route::post('/karyawan/{nik}/storejamkerjabyday', 'storejamkerjabyday')->name('karyawan.storejamkerjabyday')->can('karyawan.edit');
        Route::post('/karyawan/storejamkerjabydate', 'storejamkerjabydate')->name('karyawan.storejamkerjabydate')->can('karyawan.edit');
        Route::post('/karyawan/getjamkerjabydate', 'getjamkerjabydate')->name('karyawan.getjamkerjabydate')->can('karyawan.edit');
        Route::post('/karyawan/deletejamkerjabydate', 'deletejamkerjabydate')->name('karyawan.deletejamkerjabydate')->can('karyawan.edit');

        Route::get('/karyawan/getkaryawan', 'getkaryawan')->name('karyawan.getkaryawan');
        Route::get('/karyawan/getkaryawantable', 'getkaryawantable')->name('karyawan.getkaryawantable');
    });

    // Datamaster: Cabang / Outlet
    Route::controller(CabangController::class)->group(function () {
        Route::get('/cabang', 'index')->name('cabang.index')->can('cabang.index');
        Route::get('/cabang/create', 'create')->name('cabang.create')->can('cabang.create');
        Route::post('/cabang', 'store')->name('cabang.store')->can('cabang.create');
        Route::get('/cabang/{kode_cabang}', 'edit')->name('cabang.edit')->can('cabang.edit');
        Route::put('/cabang/{kode_cabang}', 'update')->name('cabang.update')->can('cabang.edit');
        Route::delete('/cabang/{kode_cabang}/delete', 'destroy')->name('cabang.delete')->can('cabang.delete');
    });

    // Datamaster: Departemen
    Route::controller(DepartemenController::class)->group(function () {
        Route::get('/departemen', 'index')->name('departemen.index')->can('departemen.index');
        Route::get('/departemen/create', 'create')->name('departemen.create')->can('departemen.create');
        Route::post('/departemen', 'store')->name('departemen.store')->can('departemen.create');
        Route::get('/departemen/{nik}', 'edit')->name('departemen.edit')->can('departemen.edit');
        Route::put('/departemen/{nik}', 'update')->name('departemen.update')->can('departemen.edit');
        Route::delete('/departemen/{nik}/delete', 'destroy')->name('departemen.delete')->can('departemen.delete');
    });

    // Datamaster: Divisi & Tim
    Route::controller(DivisionController::class)->group(function () {
        Route::get('/divisi', 'index')->name('divisi.index')->can('divisi.index');
        Route::post('/divisi', 'store')->name('divisi.store')->can('divisi.create');
        Route::get('/divisi/{id}/edit', 'edit')->name('divisi.edit')->can('divisi.edit');
        Route::put('/divisi/{id}', 'update')->name('divisi.update')->can('divisi.edit');
        Route::delete('/divisi/{id}/delete', 'destroy')->name('divisi.delete')->can('divisi.delete');
    });

    // Kepegawaian: Kontrak Kerja
    Route::middleware('module:contract')->controller(KontrakController::class)->group(function () {
        Route::get('/kontrak', 'index')->name('kontrak.index')->can('kontrak.index');
        Route::get('/kontrak/create', 'create')->name('kontrak.create')->can('kontrak.create');
        Route::post('/kontrak', 'store')->name('kontrak.store')->can('kontrak.create');
        Route::get('/kontrak/{id}/edit', 'edit')->name('kontrak.edit')->can('kontrak.edit');
        Route::put('/kontrak/{id}', 'update')->name('kontrak.update')->can('kontrak.edit');
        Route::post('/kontrak/{id}/renew', 'renew')->name('kontrak.renew')->can('kontrak.create');
        Route::delete('/kontrak/{id}', 'destroy')->name('kontrak.delete')->can('kontrak.delete');
    });

    // Kepegawaian: Mutasi & Riwayat Karir
    Route::middleware('module:movement')->controller(EmployeeMovementController::class)->group(function () {
        Route::get('/movement', 'index')->name('movement.index')->can('movement.index');
        Route::get('/movement/create', 'create')->name('movement.create')->can('movement.create');
        Route::post('/movement', 'store')->name('movement.store')->can('movement.create');
        Route::get('/movement/{id}', 'show')->name('movement.show')->can('movement.show');
        Route::delete('/movement/{id}', 'destroy')->name('movement.delete')->can('movement.delete');
    });

    // Kepegawaian: Resignasi & Offboarding
    Route::middleware('module:resignation')->controller(EmployeeResignationController::class)->group(function () {
        Route::get('/resignation', 'index')->name('resignation.index')->can('resignation.index');
        Route::get('/resignation/create', 'create')->name('resignation.create')->can('resignation.create');
        Route::post('/resignation', 'store')->name('resignation.store')->can('resignation.create');
        Route::put('/resignation/{id}', 'update')->name('resignation.update')->can('resignation.edit');
        Route::delete('/resignation/{id}', 'destroy')->name('resignation.delete')->can('resignation.delete');
    });

    // Kepegawaian: Rekrutmen & Pelamar
    Route::middleware('module:recruitment')->controller(RecruitmentController::class)->group(function () {
        Route::get('/recruitment', 'index')->name('recruitment.index')->can('recruitment.index');
        Route::get('/recruitment/create', 'create')->name('recruitment.create')->can('recruitment.create');
        Route::post('/recruitment', 'store')->name('recruitment.store')->can('recruitment.create');
        Route::get('/recruitment/{vacancy}', 'show')->name('recruitment.show')->can('recruitment.index');
        Route::post('/recruitment/{vacancy}/candidates', 'storeCandidate')->name('recruitment.candidate.store')->can('recruitment.create');
        Route::post('/recruitment/candidates/{candidate}/stage', 'updateCandidateStage')->name('recruitment.candidate.stage')->can('recruitment.edit');
        Route::get('/recruitment/candidates/{candidate}/hire', 'hireForm')->name('recruitment.hire.form')->can('recruitment.hire');
        Route::post('/recruitment/candidates/{candidate}/hire', 'hire')->name('recruitment.hire')->can('recruitment.hire');
        Route::delete('/recruitment/{vacancy}', 'destroy')->name('recruitment.destroy')->can('recruitment.delete');
    });

    // Kepegawaian: Onboarding Karyawan Baru
    Route::middleware('module:onboarding')->controller(OnboardingController::class)->group(function () {
        Route::get('/onboarding', 'index')->name('onboarding.index')->can('onboarding.index');
        Route::get('/onboarding/create', 'create')->name('onboarding.create')->can('onboarding.create');
        Route::post('/onboarding', 'store')->name('onboarding.store')->can('onboarding.create');
        Route::get('/onboarding/templates', 'templates')->name('onboarding.templates')->can('onboarding.index');
        Route::get('/onboarding/templates/create', 'createTemplate')->name('onboarding.templates.create')->can('onboarding.create');
        Route::post('/onboarding/templates', 'storeTemplate')->name('onboarding.templates.store')->can('onboarding.create');
        Route::get('/onboarding/{onboarding}', 'show')->name('onboarding.show')->can('onboarding.index');
        Route::post('/onboarding/tasks/{task}/toggle', 'toggleTask')->name('onboarding.task.toggle')->can('onboarding.complete');
        Route::delete('/onboarding/{onboarding}', 'destroy')->name('onboarding.destroy')->can('onboarding.create');
    });

    // Kepegawaian: Offboarding Clearance & Settlement
    Route::middleware('module:offboarding')->controller(OffboardingController::class)->group(function () {
        Route::get('/offboarding/{id}', 'show')->name('offboarding.show')->can('offboarding.clearance');
        Route::post('/offboarding/clearance/{id}/toggle', 'toggleClearance')->name('offboarding.clearance.toggle')->can('offboarding.clearance');
        Route::get('/offboarding/{id}/calculate-settlement', 'calculateSettlement')->name('offboarding.settlement.calculate')->can('offboarding.settlement');
        Route::post('/offboarding/{id}/save-settlement', 'saveSettlement')->name('offboarding.settlement.save')->can('offboarding.settlement');
        Route::post('/offboarding/{id}/finalize', 'finalize')->name('offboarding.finalize')->can('offboarding.settlement');
        Route::get('/offboarding/{id}/print', 'printClearance')->name('offboarding.print')->can('offboarding.clearance');
    });

    // Kepegawaian: Penilaian Kinerja & KPI (Performance)
    Route::middleware('module:performance')->controller(PerformanceReviewController::class)->group(function () {
        Route::get('/performance', 'index')->name('performance.index')->can('performance.index');
        Route::get('/performance/create', 'create')->name('performance.create')->can('performance.create');
        Route::post('/performance', 'store')->name('performance.store')->can('performance.create');
        Route::get('/performance/{id}', 'show')->name('performance.show')->can('performance.index');
        Route::post('/performance/{id}/approve', 'approve')->name('performance.approve')->can('performance.approve');
        Route::delete('/performance/{id}', 'destroy')->name('performance.destroy')->can('performance.create');
    });

    // Kepegawaian & Tata Kelola: Talent Governance (Module Protected per Feature)
    Route::controller(TalentGovernanceController::class)->group(function () {
        // Pelatihan & Kursus
        Route::middleware('module:training')->group(function () {
            Route::get('/trainings', 'trainingIndex')->name('training.index')->can('training.index');
            Route::post('/trainings', 'trainingStore')->name('training.store')->can('training.create');
            Route::delete('/trainings/{id}', 'trainingDestroy')->name('training.destroy')->can('training.delete');
        });

        // Disiplin & Surat Peringatan (SP)
        Route::middleware('module:discipline')->group(function () {
            Route::get('/warnings', 'warningIndex')->name('warning.index')->can('warning.index');
            Route::post('/warnings', 'warningStore')->name('warning.store')->can('warning.create');
            Route::delete('/warnings/{id}', 'warningDestroy')->name('warning.destroy')->can('warning.delete');
        });

        // Brankas Dokumen Karyawan
        Route::middleware('module:documents')->group(function () {
            Route::get('/employee-documents', 'documentIndex')->name('document.index')->can('document.index');
            Route::post('/employee-documents', 'documentStore')->name('document.store')->can('document.upload');
            Route::delete('/employee-documents/{id}', 'documentDestroy')->name('document.destroy')->can('document.delete');
        });

        // Peraturan Perusahaan & SOP
        Route::middleware('module:policy')->group(function () {
            Route::get('/company-policies', 'policyIndex')->name('policy.index');
            Route::post('/company-policies', 'policyStore')->name('policy.store')->can('document.upload');
        });

        // Inventaris & Aset Fasilitas
        Route::middleware('module:asset')->group(function () {
            Route::get('/assets', 'assetIndex')->name('asset.index')->can('asset.index');
            Route::post('/assets', 'assetStore')->name('asset.store')->can('asset.create');
            Route::post('/assets/{id}/return', 'assetReturn')->name('asset.return')->can('asset.return');
        });

        // Pengumuman Internal
        Route::middleware('module:announcements')->group(function () {
            Route::get('/announcements', 'announcementIndex')->name('announcement.index')->can('announcement.index');
            Route::post('/announcements', 'announcementStore')->name('announcement.store')->can('announcement.create');
            Route::delete('/announcements/{id}', 'announcementDestroy')->name('announcement.destroy')->can('announcement.delete');
        });

        // Kasus & Insiden
        Route::middleware('module:incident')->group(function () {
            Route::get('/incidents', 'incidentIndex')->name('incident.index')->can('incident.index');
            Route::post('/incidents', 'incidentStore')->name('incident.store')->can('incident.create');
            Route::post('/incidents/{id}/resolve', 'incidentResolve')->name('incident.resolve')->can('incident.resolve');
        });
    });

    // Kepegawaian: Lembur & SPK Overtime
    Route::middleware('module:overtime')->controller(OvertimeController::class)->group(function () {
        Route::get('/overtime', 'index')->name('overtime.index')->can('overtime.index');
        Route::get('/overtime/create', 'create')->name('overtime.create')->can('overtime.create');
        Route::post('/overtime', 'store')->name('overtime.store')->can('overtime.create');
        Route::post('/overtime/calculate-preview', 'calculatePreview')->name('overtime.preview')->can('overtime.create');
        Route::get('/overtime/{lembur}', 'show')->name('overtime.show')->can('overtime.index');
        Route::get('/overtime/{lembur}/edit', 'edit')->name('overtime.edit')->can('overtime.edit');
        Route::put('/overtime/{lembur}', 'update')->name('overtime.update')->can('overtime.edit');
        Route::post('/overtime/{lembur}/approve', 'approve')->name('overtime.approve')->can('overtime.approve');
        Route::post('/overtime/{lembur}/reject', 'reject')->name('overtime.reject')->can('overtime.approve');
        Route::delete('/overtime/{lembur}', 'destroy')->name('overtime.delete')->can('overtime.delete');
    });

    // Keuangan: Payroll Suite (Module Protected)
    Route::middleware('module:payroll')->group(function () {
        // Payroll Core
        Route::controller(PayrollPeriodController::class)->group(function () {
            Route::get('/payroll', 'index')->name('payroll.index')->can('payroll.index');
            Route::get('/payroll/create', 'create')->name('payroll.create')->can('payroll.create');
            Route::post('/payroll', 'store')->name('payroll.store')->can('payroll.create');
            Route::get('/payroll/{period}', 'show')->name('payroll.show')->can('payroll.index');
            Route::post('/payroll/{period}/calculate', 'calculate')->name('payroll.calculate')->can('payroll.calculate');
            Route::post('/payroll/{period}/finalize', 'finalize')->name('payroll.finalize')->can('payroll.finalize');
            Route::post('/payroll/{period}/reopen', 'reopen')->name('payroll.reopen')->can('payroll.reopen');
            Route::delete('/payroll/{period}', 'destroy')->name('payroll.delete')->can('payroll.delete');
        });

        // Komponen Gaji
        Route::controller(SalaryComponentController::class)->group(function () {
            Route::get('/salary-components', 'index')->name('salary_components.index')->can('salary_component.index');
            Route::post('/salary-components', 'store')->name('salary_components.store')->can('salary_component.create');
            Route::put('/salary-components/{component}', 'update')->name('salary_components.update')->can('salary_component.edit');
            Route::delete('/salary-components/{component}', 'destroy')->name('salary_components.delete')->can('salary_component.delete');
        });

        // Struktur Gaji Karyawan
        Route::controller(EmployeeSalaryController::class)->group(function () {
            Route::get('/employee-salary', 'index')->name('employee_salary.index')->can('employee_salary.index');
            Route::get('/employee-salary/{karyawan}/edit', 'edit')->name('employee_salary.edit')->can('employee_salary.edit');
            Route::put('/employee-salary/{karyawan}', 'update')->name('employee_salary.update')->can('employee_salary.edit');
        });

        // Periode Penggajian (Payroll)
        Route::controller(PayrollPeriodController::class)->group(function () {
            Route::get('/payroll-periods', 'index')->name('payroll_periods.index')->can('payroll.index');
            Route::get('/payroll-periods/create', 'create')->name('payroll_periods.create')->can('payroll.create');
            Route::post('/payroll-periods', 'store')->name('payroll_periods.store')->can('payroll.create');
            Route::get('/payroll-periods/{period}', 'show')->name('payroll_periods.show')->can('payroll.index');
            Route::post('/payroll-periods/{period}/calculate', 'calculate')->name('payroll_periods.calculate')->can('payroll.calculate');
            Route::post('/payroll-periods/{period}/finalize', 'finalize')->name('payroll_periods.finalize')->can('payroll.finalize');
            Route::get('/payroll-periods/{period}/export', 'export')->name('payroll_periods.export')->can('payroll.index');
        });

        // Indonesia Statutory Compliance (PPh 21 TER & BPJS)
        Route::controller(IndonesiaComplianceController::class)->group(function () {
            Route::get('/compliance', 'index')->name('compliance.index')->can('compliance.index');
            Route::put('/compliance/{rule}', 'update')->name('compliance.update')->can('compliance.edit');
        });

        // THR Keagamaan
        Route::controller(ThrPaymentController::class)->group(function () {
            Route::get('/thr', 'index')->name('thr.index')->can('thr.index');
            Route::get('/thr/create', 'create')->name('thr.create')->can('thr.create');
            Route::post('/thr', 'store')->name('thr.store')->can('thr.create');
            Route::get('/thr/{thr}', 'show')->name('thr.show')->can('thr.index');
            Route::post('/thr/{thr}/calculate', 'calculate')->name('thr.calculate')->can('thr.calculate');
            Route::post('/thr/{thr}/finalize', 'finalize')->name('thr.finalize')->can('thr.finalize');
            Route::get('/thr/{thr}/export', 'export')->name('thr.export')->can('thr.export');
        });

        // Slip Gaji (Payslips)
        Route::controller(PayslipController::class)->group(function () {
            Route::get('/payslips', 'index')->name('payslip.index')->can('payslip.index');
            Route::get('/payslips/{detail}', 'show')->name('payslip.show')->can('payslip.show');
            Route::get('/payslips/{detail}/print', 'print')->name('payslip.print')->can('payslip.print');
            Route::get('/my-payslips', 'myPayslips')->name('payslip.my_payslips');
        });
    });

    // Keuangan: Reimbursement & Klaim
    Route::redirect('/reimbursement', '/reimbursements');
    Route::middleware('module:reimbursement')->controller(ReimbursementController::class)->group(function () {
        Route::get('/reimbursements', 'index')->name('reimbursement.index')->can('reimbursement.index');
        Route::get('/reimbursements/create', 'create')->name('reimbursement.create')->can('reimbursement.create');
        Route::post('/reimbursements', 'store')->name('reimbursement.store')->can('reimbursement.create');
        Route::post('/reimbursements/{reimbursement}/approve', 'approve')->name('reimbursement.approve')->can('reimbursement.approve');
        Route::post('/reimbursements/{reimbursement}/reject', 'reject')->name('reimbursement.reject')->can('reimbursement.approve');
        Route::delete('/reimbursements/{reimbursement}', 'destroy')->name('reimbursement.destroy')->can('reimbursement.delete');
    });

    // Keuangan: Pinjaman & Kasbon Karyawan
    Route::redirect('/loan', '/loans');
    Route::middleware('module:loans')->controller(EmployeeLoanController::class)->group(function () {
        Route::get('/loans', 'index')->name('loan.index')->can('loan.index');
        Route::get('/loans/create', 'create')->name('loan.create')->can('loan.create');
        Route::post('/loans', 'store')->name('loan.store')->can('loan.create');
        Route::get('/loans/{loan}', 'show')->name('loan.show')->can('loan.index');
        Route::post('/loans/{loan}/approve', 'approve')->name('loan.approve')->can('loan.approve');
        Route::post('/loans/installments/{installment}/repay', 'repayInstallment')->name('loan.repay')->can('loan.approve');
        Route::delete('/loans/{loan}', 'destroy')->name('loan.destroy')->can('loan.delete');
    });

    // Datamaster: Jabatan
    Route::controller(JabatanController::class)->group(function () {
        Route::get('/jabatan', 'index')->name('jabatan.index')->can('jabatan.index');
        Route::get('/jabatan/create', 'create')->name('jabatan.create')->can('jabatan.create');
        Route::post('/jabatan', 'store')->name('jabatan.store')->can('jabatan.create');
        Route::get('/jabatan/{kode_jabatan}', 'edit')->name('jabatan.edit')->can('jabatan.edit');
        Route::put('/jabatan/{kode_jabatan}', 'update')->name('jabatan.update')->can('jabatan.edit');
        Route::delete('/jabatan/{kode_jabatan}/delete', 'destroy')->name('jabatan.delete')->can('jabatan.delete');
    });

    // Datamaster: Cuti
    Route::controller(CutiController::class)->group(function () {
        Route::get('/cuti', 'index')->name('cuti.index')->can('cuti.index');
        Route::get('/cuti/create', 'create')->name('cuti.create')->can('cuti.create');
        Route::post('/cuti', 'store')->name('cuti.store')->can('cuti.create');
        Route::get('/cuti/{kode_cuti}', 'edit')->name('cuti.edit')->can('cuti.edit');
        Route::put('/cuti/{kode_cuti}', 'update')->name('cuti.update')->can('cuti.edit');
        Route::delete('/cuti/{kode_cuti}/delete', 'destroy')->name('cuti.delete')->can('cuti.delete');
    });

    // Datamaster: Jenis Cuti (Universal Statutory & Company)
    Route::controller(LeaveTypeController::class)->group(function () {
        Route::get('/leave-types', 'index')->name('leave_types.index')->can('leave_types.index');
        Route::get('/leave-types/create', 'create')->name('leave_types.create')->can('leave_types.create');
        Route::post('/leave-types', 'store')->name('leave_types.store')->can('leave_types.create');
        Route::get('/leave-types/{id}/edit', 'edit')->name('leave_types.edit')->can('leave_types.edit');
        Route::put('/leave-types/{id}', 'update')->name('leave_types.update')->can('leave_types.edit');
        Route::delete('/leave-types/{id}', 'destroy')->name('leave_types.delete')->can('leave_types.delete');
    });

    // Kepegawaian: Saldo & Kuota Cuti Karyawan
    Route::controller(LeaveQuotaController::class)->group(function () {
        Route::get('/leave-quotas', 'index')->name('leave_quotas.index')->can('leave_quotas.index');
        Route::post('/leave-quotas/adjust', 'adjust')->name('leave_quotas.adjust')->can('leave_quotas.adjust');
        Route::post('/leave-quotas/generate', 'generate')->name('leave_quotas.generate')->can('leave_quotas.adjust');
    });

    // Datamaster: Jam Kerja (Shift Pagi & Siang)
    Route::controller(JamkerjaController::class)->group(function () {
        Route::get('/jamkerja', 'index')->name('jamkerja.index')->can('jamkerja.index');
        Route::get('/jamkerja/create', 'create')->name('jamkerja.create')->can('jamkerja.create');
        Route::post('/jamkerja', 'store')->name('jamkerja.store')->can('jamkerja.create');
        Route::get('/jamkerja/{kode_jam_kerja}/edit', 'edit')->name('jamkerja.edit')->can('jamkerja.edit');
        Route::put('/jamkerja/{kode_jam_kerja}/update', 'update')->name('jamkerja.update')->can('jamkerja.edit');
        Route::delete('/jamkerja/{kode_jam_kerja}/delete', 'destroy')->name('jamkerja.delete')->can('jamkerja.delete');
    });

    // Presensi Kehadiran & Monitoring
    Route::controller(PresensiController::class)->group(function () {
        Route::get('/presensi', 'index')->name('presensi.index')->can('presensi.index');
        Route::get('/presensi/histori', 'histori')->name('presensi.histori');
        Route::get('/presensi/create', 'create')->name('presensi.create');
        Route::post('/presensi', 'store')->name('presensi.store');
        Route::post('/presensi/edit', 'edit')->name('presensi.edit')->can('presensi.edit');
        Route::post('/presensi/update', 'update')->name('presensi.update')->can('presensi.edit');
        Route::delete('/presensi/{id}/delete', 'destroy')->name('presensi.delete')->can('presensi.delete');
        Route::get('/presensi/{id}/{status}/show', 'show')->name('presensi.show');
        Route::get('/presensi/download-zip', 'downloadZip')->name('presensi.download-zip')->can('presensi.index');
        Route::post('/presensi/auto-alpha', 'generateAutoAlpha')->name('presensi.auto-alpha')->can('presensi.edit');
    });

    // Tracking Presensi GPS
    Route::middleware('permission:trackingpresensi.index')->controller(TrackingPresensiController::class)->group(function () {
        Route::get('/trackingpresensi', 'index')->name('trackingpresensi.index');
        Route::get('/trackingpresensi/getData', 'getData')->name('trackingpresensi.getData');
    });

    // Face Recognition (Biometrik Wajah - Module Protected)
    Route::middleware('module:face_recognition')->controller(FacerecognitionController::class)->group(function () {
        Route::post('/facerecognition/hapus-semua/{nik}', 'destroyAll')->name('facerecognition.destroyAll')->can('karyawan.edit');
        Route::get('/facerecognition/{nik}/create', 'create')->name('facerecognition.create');
        Route::get('/karyawan/daftarkan-wajah', 'createKaryawan')->name('facerecognition.karyawan.create');
        Route::get('/karyawan/preview-wajah', 'previewKaryawan')->name('facerecognition.karyawan.preview');
        Route::post('/karyawan/hapus-wajah', 'destroyAllKaryawan')->name('facerecognition.karyawan.destroyAll');
        Route::post('/facerecognition/store', 'store')->name('facerecognition.store');
        Route::post('/facerecognition/sync-descriptors', 'syncDescriptors')->name('facerecognition.syncDescriptors');
        Route::delete('/facerecognition/{id}/delete', 'destroy')->name('facerecognition.delete');
        Route::get('/facerecognition/getwajah', 'getWajah')->name('facerecognition.getwajah');
    });

    // Protected Storage File Access (Biometrics, Medical SIDs, Attendance Photos & Archives)
    Route::controller(\App\Http\Controllers\ProtectedFileController::class)->group(function () {
        Route::get('/files/sid/{filename}', 'streamSid')->name('file.sid');
        Route::get('/files/facerecognition/{folder}/{filename}', 'streamFace')->name('file.face');
        Route::get('/files/attendance-archive/{month}', 'downloadAttendanceArchive')->name('file.attendance-archive');
        Route::get('/files/absensi/{filename}', 'streamAttendancePhoto')->name('file.absensi');
    });

    // Manajemen Cuti & Izin (Module Protected)
    Route::middleware('module:leave')->group(function () {
        // Pengajuan Izin Absen
        Route::controller(IzinabsenController::class)->group(function () {
            Route::get('/izinabsen', 'index')->name('izinabsen.index')->can('izinabsen.index');
            Route::get('/izinabsen/create', 'create')->name('izinabsen.create')->can('izinabsen.create');
            Route::post('/izinabsen', 'store')->name('izinabsen.store')->can('izinabsen.create');
            Route::get('/izinabsen/{kode_izin}/approve', 'approve')->name('izinabsen.approve')->can('izinabsen.approve');
            Route::delete('/izinabsen/{kode_izin}/cancelapprove', 'cancelapprove')->name('izinabsen.cancelapprove')->can('izinabsen.approve');
            Route::post('/izinabsen/{kode_izin}/storeapprove', 'storeapprove')->name('izinabsen.storeapprove')->can('izinabsen.approve');
            Route::get('/izinabsen/{id}/edit', 'edit')->name('izinabsen.edit')->can('izinabsen.edit');
            Route::put('/izinabsen/{id}', 'update')->name('izinabsen.update')->can('izinabsen.edit');
            Route::get('/izinabsen/{kode_izin}/show', 'show')->name('izinabsen.show')->can('izinabsen.index');
            Route::delete('/izinabsen/{id}/delete', 'destroy')->name('izinabsen.delete')->can('izinabsen.delete');
        });

        // Pengajuan Izin Sakit
        Route::controller(IzinsakitController::class)->group(function () {
            Route::get('/izinsakit', 'index')->name('izinsakit.index')->can('izinsakit.index');
            Route::get('/izinsakit/create', 'create')->name('izinsakit.create')->can('izinsakit.create');
            Route::post('/izinsakit', 'store')->name('izinsakit.store')->can('izinsakit.create');
            Route::get('/izinsakit/{kode_izin_sakit}/edit', 'edit')->name('izinsakit.edit')->can('izinsakit.edit');
            Route::put('/izinsakit/{kode_izin_sakit}', 'update')->name('izinsakit.update')->can('izinsakit.edit');
            Route::get('/izinsakit/{kode_izin_sakit}/show', 'show')->name('izinsakit.show')->can('izinsakit.index');
            Route::delete('/izinsakit/{kode_izin_sakit}/delete', 'destroy')->name('izinsakit.delete')->can('izinsakit.delete');
            Route::get('/izinsakit/{kode_izin_sakit}/approve', 'approve')->name('izinsakit.approve')->can('izinsakit.approve');
            Route::delete('/izinsakit/{kode_izin_sakit}/cancelapprove', 'cancelapprove')->name('izinsakit.cancelapprove')->can('izinsakit.approve');
            Route::post('/izinsakit/{kode_izin_sakit}/storeapprove', 'storeapprove')->name('izinsakit.storeapprove')->can('izinsakit.approve');
        });

        // Pengajuan Cuti (Quota Bulanan)
        Route::controller(IzincutiController::class)->group(function () {
            Route::get('/izincuti', 'index')->name('izincuti.index')->can('izincuti.index');
            Route::get('/izincuti/create', 'create')->name('izincuti.create')->can('izincuti.create');
            Route::get('/izincuti/print-report', 'printReport')->name('izincuti.print-report')->can('izincuti.index');
            Route::post('/izincuti', 'store')->name('izincuti.store')->can('izincuti.create');
            Route::get('/izincuti/{kode_izin_cuti}/edit', 'edit')->name('izincuti.edit')->can('izincuti.edit');
            Route::put('/izincuti/{kode_izin_cuti}', 'update')->name('izincuti.update')->can('izincuti.edit');
            Route::get('/izincuti/{kode_izin_cuti}/show', 'show')->name('izincuti.show')->can('izincuti.index');
            Route::get('/izincuti/{kode_izin_cuti}/print', 'print')->name('izincuti.print')->can('izincuti.index');
            Route::delete('/izincuti/{kode_izin_cuti}/delete', 'destroy')->name('izincuti.delete')->can('izincuti.delete');
            Route::get('/izincuti/{kode_izin_cuti}/approve', 'approve')->name('izincuti.approve')->can('izincuti.approve');
            Route::delete('/izincuti/{kode_izin_cuti}/cancelapprove', 'cancelapprove')->name('izincuti.cancelapprove')->can('izincuti.approve');
            Route::post('/izincuti/{kode_izin_cuti}/storeapprove', 'storeapprove')->name('izincuti.storeapprove')->can('izincuti.approve');
            Route::get('/izincuti/getsisaharicuti', 'getsisaharicuti')->name('izincuti.getsisaharicuti');
            Route::get('/cuti/hitung-hari', 'hitungHariAjax')->name('cuti.hitungHariAjax');
            Route::get('/izincuti/hitung-hari', 'hitungHariAjax')->name('izincuti.hitungHariAjax');
        });

        // Hub Pengajuan Izin (Mobile)
        Route::get('/pengajuanizin', [PengajuanizinController::class, 'index'])->name('pengajuanizin.index');
    });

    // Dispensasi Keterlambatan
    Route::controller(DispensasiController::class)->group(function () {
        Route::get('/dispensasi', 'index')->name('dispensasi.index');
        Route::get('/dispensasi/create', 'create')->name('dispensasi.create');
        Route::post('/dispensasi', 'store')->name('dispensasi.store');
        Route::get('/dispensasi/{id}/approve', 'approve')->name('dispensasi.approve');
        Route::post('/dispensasi/{id}/storeapprove', 'storeApprove')->name('dispensasi.storeApprove');
        Route::delete('/dispensasi/{id}/cancelapprove', 'cancelApprove')->name('dispensasi.cancelApprove');
        Route::delete('/dispensasi/{id}', 'destroy')->name('dispensasi.destroy');
    });

    // Hari Libur / Tanggal Merah
    Route::controller(HariliburController::class)->group(function () {
        Route::get('/harilibur', 'index')->name('harilibur.index')->can('harilibur.index');
        Route::get('/harilibur/create', 'create')->name('harilibur.create')->can('harilibur.create');
        Route::post('/harilibur', 'store')->name('harilibur.store')->can('harilibur.create');
        Route::get('/harilibur/{kode_libur}/edit', 'edit')->name('harilibur.edit')->can('harilibur.edit');
        Route::put('/harilibur/{kode_libur}', 'update')->name('harilibur.update')->can('harilibur.edit');
        Route::delete('/harilibur/{kode_libur}/delete', 'destroy')->name('harilibur.delete')->can('harilibur.delete');
        Route::get('/harilibur/{kode_libur}/aturharilibur', 'aturharilibur')->name('harilibur.aturharilibur')->can('harilibur.setharilibur');
        Route::get('/harilibur/{kode_libur}/getkaryawanlibur', 'getkaryawanlibur')->name('harilibur.getkaryawanlibur')->can('harilibur.setharilibur');
        Route::get('/harilibur/{kode_libur}/aturkaryawan', 'aturkaryawan')->name('harilibur.aturkaryawan')->can('harilibur.setharilibur');
        Route::post('/harilibur/getkaryawan', 'getkaryawan')->name('harilibur.getkaryawan')->can('harilibur.setharilibur');
        Route::post('/harilibur/updateliburkaryawan', 'updateliburkaryawan')->name('harilibur.updateliburkaryawan')->can('harilibur.setharilibur');
        Route::post('/harilibur/deletekaryawanlibur', 'deletekaryawanlibur')->name('harilibur.deletekaryawanlibur')->can('harilibur.setharilibur');
        Route::post('/harilibur/tambahkansemua', 'tambahkansemua')->name('harilibur.tambahkansemua')->can('harilibur.setharilibur');
        Route::post('/harilibur/batalkansemua', 'batalkansemua')->name('harilibur.batalkansemua')->can('harilibur.setharilibur');
    });

    // Mobile Shortcut Menu
    Route::get('/shortcut', [\App\Http\Controllers\ShortcutController::class, 'index'])->name('shortcut.index');

    // Laporan / Rekap (Presensi & Cuti Saja - Module Guarded)
    Route::controller(LaporanController::class)->group(function () {
        Route::middleware('module:attendance')->group(function () {
            Route::get('/laporan/presensi', 'presensi')->name('laporan.presensi')->can('laporan.presensi');
            Route::post('/laporan/cetakpresensi', 'cetakpresensi')->name('laporan.cetakpresensi')->can('laporan.presensi');
        });
        Route::middleware('module:leave')->group(function () {
            Route::get('/laporan/cuti', 'cuti')->name('laporan.cuti')->can('laporan.cuti');
            Route::post('/laporan/cetakcuti', 'cetakcuti')->name('laporan.cetakcuti')->can('laporan.cuti');
        });
    });

    // Laporan & Analitik Universal HR
    Route::middleware('module:reports')->controller(UniversalReportController::class)->group(function () {
        Route::get('/reports', 'index')->name('reports.index')->can('reports.index');
        Route::get('/reports/print', 'print')->name('reports.print')->can('reports.index');
        Route::get('/reports/export', 'export')->name('reports.export')->can('reports.export');
    });

    // Struktur Organisasi & Organigram
    Route::controller(OrgChartController::class)->group(function () {
        Route::get('/org-chart', 'index')->name('org_chart.index')->can('org_chart.index');
    });

    // Import Karyawan & Kelola Massal
    Route::controller(EmployeeImportController::class)->group(function () {
        Route::get('/karyawan-import', 'index')->name('karyawan.import.index')->can('karyawan.import');
        Route::get('/karyawan-import/template', 'downloadTemplate')->name('karyawan.import.template')->can('karyawan.import');
        Route::post('/karyawan-import/process', 'processImport')->name('karyawan.import.process')->can('karyawan.import');
        Route::post('/karyawan-import/bulk-action', 'bulkAction')->name('karyawan.bulk_action')->can('karyawan.import');
    });

    // Pusat Bantuan & Dokumentasi Sistem
    Route::controller(HelpCenterController::class)->group(function () {
        Route::get('/help', 'index')->name('help.index');
    });

    // Pusat Direktori Pengaturan (Settings Hub)
    Route::controller(SettingsHubController::class)->group(function () {
        Route::get('/settings', 'index')->name('settings.hub')->can('settings.index');
    });

    // Audit Trail System (Phase 12)
    Route::controller(AuditLogController::class)->group(function () {
        Route::get('/settings/audit-logs', 'index')->name('settings.audit_logs.index')->can('audit_logs.index');
        Route::get('/settings/audit-logs/export', 'export')->name('settings.audit_logs.export')->can('audit_logs.export');
    });

    // Client Preset Matrix A - E (Phase 12)
    Route::controller(PresetMatrixController::class)->group(function () {
        Route::get('/settings/presets', 'index')->name('settings.presets.index')->can('presets.index');
        Route::post('/settings/presets/apply', 'apply')->name('settings.presets.apply')->can('presets.apply');
    });

    // Pengaturan Umum / Super Admin
    Route::middleware('role:super admin')->group(function () {
        Route::controller(GeneralsettingController::class)->group(function () {
            Route::get('/generalsetting', 'index')->name('generalsetting.index')->can('generalsetting.index');
            Route::put('/generalsetting/{id}', 'update')->name('generalsetting.update')->can('generalsetting.edit');
            Route::post('/generalsetting/fix-permissions', 'fixPermissions')->name('generalsetting.fix-permissions');
            Route::get('/generalsetting/hosting-readiness', [\App\Http\Controllers\HostingReadinessController::class, 'index'])->name('generalsetting.hosting-readiness');
        });

        Route::controller(CompanySettingController::class)->group(function () {
            Route::get('/settings/company', 'index')->name('company_settings.index')->can('company_settings.index');
            Route::put('/settings/company', 'update')->name('company_settings.update')->can('company_settings.edit');
        });

        Route::controller(ModuleFeatureController::class)->group(function () {
            Route::get('/settings/modules', 'index')->name('module_features.index')->can('module_features.index');
            Route::post('/settings/modules/toggle/{id}', 'toggle')->name('module_features.toggle')->can('module_features.edit');
            Route::put('/settings/modules/{id}', 'update')->name('module_features.update')->can('module_features.edit');
        });

        Route::controller(\App\Http\Controllers\PackageInfoController::class)->group(function () {
            Route::get('/settings/package-info', 'index')->name('settings.package_info.index');
            Route::post('/settings/package-info/request-upgrade', 'requestUpgrade')->name('settings.package_info.request_upgrade');
        });

        Route::controller(AttendancePolicyController::class)->group(function () {
            Route::get('/settings/attendance', 'index')->name('attendance_policy.index')->can('attendance_policy.index');
            Route::put('/settings/attendance', 'update')->name('attendance_policy.update')->can('attendance_policy.edit');
        });

        Route::controller(OvertimePolicyController::class)->group(function () {
            Route::get('/settings/overtime', 'index')->name('overtime_policy.index')->can('overtime_policy.index');
            Route::put('/settings/overtime/{policy}', 'update')->name('overtime_policy.update')->can('overtime_policy.edit');
        });

        Route::controller(IconGeneratorController::class)->group(function () {
            Route::post('/pwa/generate-icons', 'generate')->name('pwa.generate-icons');
            Route::get('/pwa/preview-icons', 'preview')->name('pwa.preview-icons');
        });

        Route::controller(UserController::class)->group(function () {
            Route::get('/users', 'index')->name('users.index')->can('users.index');
            Route::get('/users/create', 'create')->name('users.create')->can('users.create');
            Route::post('/users', 'store')->name('users.store')->can('users.create');
            Route::get('/users/{id}/edit', 'edit')->name('users.edit')->can('users.edit');
            Route::put('/users/{id}/update', 'update')->name('users.update')->can('users.edit');
            Route::delete('/users/{id}/delete', 'destroy')->name('users.delete')->can('users.delete');
            Route::get('/users/{id}/editpassword', 'editpassword')->name('users.editpassword')->can('users.edit');
            Route::put('/users/{id}/updatepassword', 'updatepassword')->name('users.updatepassword')->can('users.edit');
        });

        Route::controller(Permission_groupController::class)->middleware(['role:super admin|master admin'])->group(function () {
            Route::get('/permissiongroups', 'index')->name('permissiongroups.index');
            Route::get('/permissiongroups/create', 'create')->name('permissiongroups.create');
            Route::post('/permissiongroups', 'store')->name('permissiongroups.store');
            Route::get('/permissiongroups/{id}/edit', 'edit')->name('permissiongroups.edit');
            Route::put('/permissiongroups/{id}/update', 'update')->name('permissiongroups.update');
            Route::delete('/permissiongroups/{id}/delete', 'destroy')->name('permissiongroups.delete');
        });

        Route::controller(PermissionController::class)->middleware(['role:super admin|master admin'])->group(function () {
            Route::get('/permissions', 'index')->name('permissions.index');
            Route::get('/permissions/create', 'create')->name('permissions.create');
            Route::post('/permissions', 'store')->name('permissions.store');
            Route::get('/permissions/{id}/edit', 'edit')->name('permissions.edit');
            Route::put('/permissions/{id}/update', 'update')->name('permissions.update');
            Route::delete('/permissions/{id}/delete', 'destroy')->name('permissions.delete');
        });
    });
});

// Scoped caching route fallback for face recognition model files
Route::get('/models/{file}', function ($file) {
    $baseDir = realpath(public_path('models'));
    $targetPath = realpath(public_path('models/' . $file));

    if (!$baseDir || !$targetPath || !str_starts_with($targetPath, $baseDir) || !file_exists($targetPath)) {
        abort(404);
    }
    $isJson = str_ends_with($file, '.json');
    $mime = $isJson ? 'application/json' : 'application/octet-stream';
    // Shards: 30 days; JSON manifests: 1 day with must-revalidate so updates propagate promptly
    $cacheControl = $isJson
        ? 'public, max-age=86400, must-revalidate'
        : (preg_match('/-shard[0-9]+$/', $file) ? 'public, max-age=2592000' : 'public, max-age=86400');

    return response()->file($targetPath, [
        'Content-Type' => $mime,
        'Cache-Control' => $cacheControl,
        'Access-Control-Allow-Origin' => '*',
    ]);
})->where('file', '[a-zA-Z0-9_\-\.]+');

// Require auth routes
require __DIR__ . '/auth.php';
