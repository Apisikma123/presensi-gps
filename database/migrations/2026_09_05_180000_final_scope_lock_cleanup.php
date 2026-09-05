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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Drop out-of-scope tables
        $tablesToDrop = [
            // Lembur
            'lembur', 'lembur_aturan', 'lembur_karyawan_khusus',
            // Kontrak
            'kontrak',
            // Payroll & Gaji & Pajak
            'slip_gaji', 'slip_gaji_harian', 'slip_gaji_harian_detail',
            'pph21_settings', 'pph21_formula_komponen', 'pph21_progresif_rates', 'pph21_slip_detail', 'pph21_ter_rates',
            'pinjaman', 'pembayaran_pinjaman', 'pinjaman_generate_history', 'rencana_cicilan',
            'karyawan_gaji_pokok', 'karyawan_penyesuaian_gaji', 'karyawan_penyesuaian_gaji_detail',
            'karyawan_tunjangan', 'karyawan_tunjangan_detail', 'jenis_tunjangan',
            'karyawan_bpjskesehatan', 'karyawan_bpjstenagakerja',
            // Reimbursement
            'reimbursement', 'reimbursement_detail', 'reimbursement_karyawan', 'jenis_reimbursement',
            // KPI & Performance
            'kpi_employees', 'kpi_indicators', 'kpi_indicator_details', 'kpi_periods', 'kpi_details',
            // Pelatihan, Pelanggaran, Mutasi, Resign
            'pelanggaran', 'mutasi_karyawan', 'resign_karyawans', 'kategori_resign', 'karyawan_pelatihan',
            // Project Management
            'projects', 'project_categories', 'project_members', 'project_tasks',
            'project_task_attachments', 'project_task_comments', 'project_task_logs', 'project_task_members',
            // Kunjungan & Aktivitas & Denda & Pengumuman
            'kunjungan', 'aktivitas_karyawan', 'denda', 'pengumuman',
            // Status kawin & status karyawan
            'status_kawin', 'status_karyawan',
            // Multi-approval layers
            'approval_layers', 'approval_features', 'approvals',
            // Complex scheduling
            'grup', 'grup_detail', 'grup_jamkerja_bydate',
            'presensi_jamkerja_bydate', 'presensi_jamkerja_byday',
            'presensi_jamkerja_bydept', 'presensi_jamkerja_bydept_detail', 'ajuan_jadwal',
            // Izin Dinas & Koreksi (Replaced by clean Dispensasi)
            'presensi_izindinas', 'presensi_koreksi', 'presensi_koreksi_approve',
            'konfigurasi_dokumen'
        ];

        foreach ($tablesToDrop as $table) {
            Schema::dropIfExists($table);
        }

        // 2. Clean table karyawan
        if (Schema::hasTable('karyawan')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $dropCols = [
                    'no_ktp', 'npwp', 'hitung_pph21', 'tempat_lahir', 'tanggal_lahir',
                    'alamat_sesuai_ktp', 'kontak_darurat', 'hubungan_kontak_darurat',
                    'nama_bank', 'no_rekening', 'nama_rekening', 'kode_status_kawin',
                    'pendidikan_terakhir', 'jurusan', 'tanggal_off_gaji'
                ];
                foreach ($dropCols as $col) {
                    if (Schema::hasColumn('karyawan', $col)) {
                        $table->dropColumn($col);
                    }
                }
                if (!Schema::hasColumn('karyawan', 'kode_jam_kerja')) {
                    $table->char('kode_jam_kerja', 4)->nullable()->default('JK01')->after('kode_cabang');
                }
            });
        }

        // 3. Clean table presensi
        if (Schema::hasTable('presensi')) {
            Schema::table('presensi', function (Blueprint $table) {
                $dropPresensiCols = [
                    'denda', 'jam_lembur_aktual', 'jam_lembur_netto', 'nominal_lembur',
                    'is_lembur_khusus', 'status_potongan', 'status_potongan_istirahat',
                    'istirahat_in', 'lokasi_istirahat_in', 'foto_istirahat_in',
                    'istirahat_out', 'lokasi_istirahat_out', 'foto_istirahat_out'
                ];
                foreach ($dropPresensiCols as $col) {
                    if (Schema::hasColumn('presensi', $col)) {
                        $table->dropColumn($col);
                    }
                }
                if (!Schema::hasColumn('presensi', 'is_dispensasi')) {
                    $table->tinyInteger('is_dispensasi')->default(0)->after('status');
                }
                if (!Schema::hasColumn('presensi', 'dispensasi_id')) {
                    $table->unsignedBigInteger('dispensasi_id')->nullable()->after('is_dispensasi');
                }
                if (!Schema::hasColumn('presensi', 'keterangan')) {
                    $table->string('keterangan', 255)->nullable()->after('dispensasi_id');
                }
            });
        }

        // 4. Update presensi_jamkerja for 2 shifts only with configurable tolerance
        if (Schema::hasTable('presensi_jamkerja')) {
            Schema::table('presensi_jamkerja', function (Blueprint $table) {
                if (!Schema::hasColumn('presensi_jamkerja', 'batas_toleransi')) {
                    $table->time('batas_toleransi')->nullable()->default('07:05:00')->after('jam_masuk');
                }
                if (!Schema::hasColumn('presensi_jamkerja', 'toleransi_menit')) {
                    $table->integer('toleransi_menit')->default(5)->after('batas_toleransi');
                }
                if (!Schema::hasColumn('presensi_jamkerja', 'status_aktif')) {
                    $table->char('status_aktif', 1)->default('1')->after('toleransi_menit');
                }
            });

            // Upsert standard 2 shifts
            DB::table('presensi_jamkerja')->updateOrInsert(
                ['kode_jam_kerja' => 'JK01'],
                [
                    'nama_jam_kerja' => 'Shift Pagi',
                    'jam_masuk' => '07:00:00',
                    'batas_toleransi' => '07:05:00',
                    'toleransi_menit' => 5,
                    'jam_pulang' => '15:00:00',
                    'status_aktif' => '1',
                    'keterangan' => 'Shift Pagi (Toleransi sampai 07:05)',
                    'color' => '#1E4D3E',
                    'updated_at' => now()
                ]
            );

            DB::table('presensi_jamkerja')->updateOrInsert(
                ['kode_jam_kerja' => 'JK02'],
                [
                    'nama_jam_kerja' => 'Shift Siang',
                    'jam_masuk' => '13:00:00',
                    'batas_toleransi' => '13:05:00',
                    'toleransi_menit' => 5,
                    'jam_pulang' => '21:00:00',
                    'status_aktif' => '1',
                    'keterangan' => 'Shift Siang (Toleransi sampai 13:05)',
                    'color' => '#D97706',
                    'updated_at' => now()
                ]
            );

            // Remove any other shift codes
            DB::table('presensi_jamkerja')->whereNotIn('kode_jam_kerja', ['JK01', 'JK02'])->delete();
        }

        // 5. Update pengaturan_umum (add monthly_leave_quota)
        if (Schema::hasTable('pengaturan_umum')) {
            Schema::table('pengaturan_umum', function (Blueprint $table) {
                if (!Schema::hasColumn('pengaturan_umum', 'monthly_leave_quota')) {
                    $table->integer('monthly_leave_quota')->default(3)->after('denda');
                }
            });
        }

        // 6. Create presensi_dispensasi table
        if (!Schema::hasTable('presensi_dispensasi')) {
            Schema::create('presensi_dispensasi', function (Blueprint $table) {
                $table->id();
                $table->char('nik', 9);
                $table->date('tanggal');
                $table->time('batas_dispensasi');
                $table->text('alasan');
                $table->string('status', 20)->default('PENDING'); // PENDING, APPROVED, REJECTED
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamps();

                $table->index(['nik', 'tanggal']);
                $table->index('status');
            });
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_dispensasi');
    }
};
