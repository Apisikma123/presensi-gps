<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('action', 50)->index(); // CREATE, UPDATE, DELETE, APPROVE, REJECT, IMPORT, EXPORT, LOGIN
                $table->string('module', 50)->index(); // employee, payroll, attendance, leave, reimbursement, loan, performance, settings, auth
                $table->string('record_id', 50)->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->json('details')->nullable();
                $table->timestamp('created_at')->useCurrent()->index();

                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        }

        // Add performance compound indexes if not already present
        try {
            $presensiIndexes = DB::select("SHOW INDEX FROM presensi WHERE Key_name = 'idx_presensi_nik_tgl'");
            if (empty($presensiIndexes)) {
                DB::statement("CREATE INDEX idx_presensi_nik_tgl ON presensi (nik, tgl_presensi)");
            }
        } catch (\Throwable $e) {
            // Silently ignore if table doesn't exist or index already exists
        }

        try {
            $karyawanIndexes = DB::select("SHOW INDEX FROM karyawan WHERE Key_name = 'idx_karyawan_dept_cabang_status'");
            if (empty($karyawanIndexes)) {
                DB::statement("CREATE INDEX idx_karyawan_dept_cabang_status ON karyawan (kode_dept, kode_cabang, status_aktif_karyawan)");
            }
        } catch (\Throwable $e) {
            // Silently ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
