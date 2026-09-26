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
        Schema::table('karyawan', function (Blueprint $table) {
            if (!Schema::hasColumn('karyawan', 'personal_email')) {
                $table->string('personal_email', 100)->nullable()->after('email');
            }
            if (!Schema::hasColumn('karyawan', 'company_email')) {
                $table->string('company_email', 100)->nullable()->after('personal_email');
            }
            if (!Schema::hasColumn('karyawan', 'kontak_darurat')) {
                $table->string('kontak_darurat', 100)->nullable()->after('company_email');
            }
            if (!Schema::hasColumn('karyawan', 'hubungan_kontak_darurat')) {
                $table->string('hubungan_kontak_darurat', 50)->nullable()->after('kontak_darurat');
            }
            if (!Schema::hasColumn('karyawan', 'no_ktp')) {
                $table->string('no_ktp', 30)->nullable()->after('hubungan_kontak_darurat');
            }
            if (!Schema::hasColumn('karyawan', 'npwp_number')) {
                $table->string('npwp_number', 50)->nullable()->after('no_ktp');
            }
            if (!Schema::hasColumn('karyawan', 'bpjs_kesehatan_number')) {
                $table->string('bpjs_kesehatan_number', 50)->nullable()->after('npwp_number');
            }
            if (!Schema::hasColumn('karyawan', 'bpjs_ketenagakerjaan_number')) {
                $table->string('bpjs_ketenagakerjaan_number', 50)->nullable()->after('bpjs_kesehatan_number');
            }
            if (!Schema::hasColumn('karyawan', 'nama_bank')) {
                $table->string('nama_bank', 50)->nullable()->after('bpjs_ketenagakerjaan_number');
            }
            if (!Schema::hasColumn('karyawan', 'no_rekening')) {
                $table->string('no_rekening', 50)->nullable()->after('nama_bank');
            }
            if (!Schema::hasColumn('karyawan', 'nama_rekening')) {
                $table->string('nama_rekening', 100)->nullable()->after('no_rekening');
            }
            if (!Schema::hasColumn('karyawan', 'nationality')) {
                $table->string('nationality', 50)->default('WNI')->after('nama_rekening');
            }
            if (!Schema::hasColumn('karyawan', 'religion')) {
                $table->string('religion', 50)->nullable()->after('nationality');
            }
            if (!Schema::hasColumn('karyawan', 'grade_level')) {
                $table->string('grade_level', 50)->nullable()->after('religion');
            }
            if (!Schema::hasColumn('karyawan', 'direct_supervisor_nik')) {
                $table->char('direct_supervisor_nik', 9)->nullable()->index()->after('grade_level');
            }
            if (!Schema::hasColumn('karyawan', 'employment_type')) {
                $table->string('employment_type', 50)->default('PKWT')->after('direct_supervisor_nik');
            }
            if (!Schema::hasColumn('karyawan', 'kode_divisi')) {
                $table->string('kode_divisi', 10)->nullable()->index()->after('employment_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $cols = [
                'personal_email',
                'company_email',
                'kontak_darurat',
                'hubungan_kontak_darurat',
                'no_ktp',
                'npwp_number',
                'bpjs_kesehatan_number',
                'bpjs_ketenagakerjaan_number',
                'nama_bank',
                'no_rekening',
                'nama_rekening',
                'nationality',
                'religion',
                'grade_level',
                'direct_supervisor_nik',
                'employment_type',
                'kode_divisi',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('karyawan', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
