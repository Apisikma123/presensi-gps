<?php

namespace Database\Seeders;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Division;
use App\Models\EmployeeDocument;
use App\Models\EmployeeLoan;
use App\Models\EmployeeLoanInstallment;
use App\Models\EmployeeMovement;
use App\Models\EmployeeResignation;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Kontrak;
use App\Models\Lembur;
use App\Models\PresensiDispensasi;
use App\Models\Reimbursement;
use App\Models\ReimbursementType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminTablesDummySeeder extends Seeder
{
    /**
     * Run the database seeds for admin tables.
     */
    public function run(): void
    {
        $adminUser = User::first() ?? User::create([
            'name' => 'Administrator HR',
            'username' => 'admin_hr',
            'email' => 'admin@presence.id',
            'password' => bcrypt('password'),
        ]);

        $adminId = $adminUser->id;

        // 1. SEED DIVISIONS (Divisi & Tim Kerja)
        $this->command->info('Seeding Divisions...');
        $divisionsData = [
            ['kode_divisi' => 'DIV-ROAST', 'nama_divisi' => 'Roastery & Quality Lab', 'kode_dept' => 'PRD'],
            ['kode_divisi' => 'DIV-BREW',  'nama_divisi' => 'Barista & Brewing Service', 'kode_dept' => 'BAR'],
            ['kode_divisi' => 'DIV-WH',    'nama_divisi' => 'Gudang & Logistik Bahan', 'kode_dept' => 'KIT'],
            ['kode_divisi' => 'DIV-TECH',  'nama_divisi' => 'Maintenance Mesin & Teknis', 'kode_dept' => 'KIT'],
            ['kode_divisi' => 'DIV-HROPS', 'nama_divisi' => 'HR Operations & People Care', 'kode_dept' => 'HRD'],
            ['kode_divisi' => 'DIV-TALENT','nama_divisi' => 'Talent Acquisition & Learning', 'kode_dept' => 'SDM'],
            ['kode_divisi' => 'DIV-FIN',   'nama_divisi' => 'Finance, Tax & Treasury', 'kode_dept' => 'KUA'],
            ['kode_divisi' => 'DIV-ACC',   'nama_divisi' => 'General Accounting & Payroll', 'kode_dept' => 'KUA'],
            ['kode_divisi' => 'DIV-DEV',   'nama_divisi' => 'Core Systems & Mobile Apps', 'kode_dept' => 'IT'],
            ['kode_divisi' => 'DIV-MKT',   'nama_divisi' => 'Brand Activation & Media', 'kode_dept' => 'MKT'],
            ['kode_divisi' => 'DIV-STORE', 'nama_divisi' => 'Store Operations & Hospitality', 'kode_dept' => 'SRV'],
        ];

        foreach ($divisionsData as $div) {
            Division::updateOrCreate(
                ['kode_divisi' => $div['kode_divisi']],
                [
                    'nama_divisi' => $div['nama_divisi'],
                    'kode_dept' => $div['kode_dept'],
                    'is_active' => true,
                ]
            );
        }

        // Get employees pool for subsequent seedings
        $employees = Karyawan::with(['departemen', 'cabang', 'jabatan'])->limit(60)->get();
        if ($employees->isEmpty()) {
            $this->command->error('No employees found to seed related tables.');
            return;
        }

        // 2. SEED KONTRAK (Kontrak Kerja Karyawan)
        $this->command->info('Seeding Kontrak...');
        $contractTypes = ['PKWT', 'PKWTT', 'Probation', 'Magang'];
        $statuses = ['aktif', 'aktif', 'aktif', 'perpanjang', 'selesai'];

        for ($i = 0; $i < min(28, $employees->count()); $i++) {
            $emp = $employees[$i];
            $startDate = Carbon::now()->subMonths(rand(2, 14))->startOfMonth();
            $jenis = $contractTypes[$i % count($contractTypes)];
            $status = $statuses[$i % count($statuses)];
            $endDate = ($jenis === 'PKWTT') ? null : (clone $startDate)->addMonths(rand(6, 12));

            $noKontrak = sprintf('KTR/%04d/%02d/%04d', $startDate->year, $startDate->month, 100 + $i);

            Kontrak::updateOrCreate(
                ['no_kontrak' => $noKontrak],
                [
                    'nik' => $emp->nik,
                    'jenis_kontrak' => $jenis,
                    'tanggal_mulai' => $startDate->format('Y-m-d'),
                    'tanggal_selesai' => $endDate ? $endDate->format('Y-m-d') : null,
                    'status' => $status,
                    'jabatan' => $emp->nama_jabatan ?? 'Staff Operasional',
                    'kode_cabang' => $emp->kode_cabang ?? 'CS1',
                    'kode_dept' => $emp->kode_dept ?? 'PRD',
                    'gaji_pokok' => rand(45, 95) * 100000,
                    'keterangan' => 'Perjanjian kerja operasional outlet & kantor periode berjalan.',
                    'reminder_days' => 30,
                    'reminder_sent' => false,
                    'created_by' => $adminId,
                ]
            );
        }

        // 3. SEED REIMBURSEMENTS (Klaim Keuangan)
        $this->command->info('Seeding Reimbursements...');
        $reimbTypes = ReimbursementType::all();
        $reimbStatuses = ['APPROVED', 'PAID', 'APPROVED', 'SUBMITTED', 'REJECTED'];
        $descriptions = [
            'Bensin dinas monitoring antar cabang & logistik supply',
            'Biaya pembelian filter aeropress & cleaner mesin espresso',
            'Penggantian kacamata kerja operasional sesuai plafon tahunan',
            'Konsumsi meeting koordinasi mingguan supervisor area',
            'Tiket kereta & akomodasi supervisi cabang baru',
            'Pembelian perlengkapan P3K & APD roastery',
            'Biaya servis darurat grinder grinder bar barat',
            'Konsumsi shift malam perbaikan kelistrikan outlet',
        ];

        for ($i = 0; $i < 20; $i++) {
            $emp = $employees[$i % $employees->count()];
            $type = $reimbTypes->isNotEmpty() ? $reimbTypes[$i % $reimbTypes->count()] : null;
            $status = $reimbStatuses[$i % count($reimbStatuses)];
            $claimDate = Carbon::now()->subDays(rand(1, 45));
            $claimNum = sprintf('CLM-%s-%04d', $claimDate->format('Ym'), 101 + $i);

            Reimbursement::updateOrCreate(
                ['claim_number' => $claimNum],
                [
                    'nik' => $emp->nik,
                    'reimbursement_type_id' => $type ? $type->id : 1,
                    'claim_date' => $claimDate->format('Y-m-d'),
                    'amount' => rand(15, 120) * 10000,
                    'description' => $descriptions[$i % count($descriptions)],
                    'status' => $status,
                    'approved_by' => in_array($status, ['APPROVED', 'PAID']) ? $adminId : null,
                    'approved_at' => in_array($status, ['APPROVED', 'PAID']) ? $claimDate->copy()->addDays(2) : null,
                    'payroll_period_id' => $status === 'PAID' ? 4 : null,
                    'rejection_reason' => $status === 'REJECTED' ? 'Bukti nota/struk tidak terbaca jelas' : null,
                ]
            );
        }

        // 4. SEED EMPLOYEE LOANS & INSTALLMENTS (Pinjaman Karyawan & Cicilan)
        $this->command->info('Seeding Employee Loans...');
        $loanStatuses = ['ACTIVE', 'PAID_OFF', 'ACTIVE', 'APPROVED', 'PENDING'];
        $notes = [
            'Pinjaman pendidikan sertifikasi barista SCA',
            'Kasbon renovasi rumah mendesak',
            'Pinjaman darurat kesehatan keluarga',
            'Kasbon pembelian laptop pendukung kerja',
            'Pinjaman biaya sewa tempat tinggal dekat outlet',
        ];

        for ($i = 0; $i < 14; $i++) {
            $emp = $employees[($i + 5) % $employees->count()];
            $loanNum = sprintf('LOAN-%s-%04d', Carbon::now()->format('Ym'), 201 + $i);
            $amount = rand(3, 12) * 1000000;
            $tenor = [3, 6, 10, 12][$i % 4];
            $monthly = round($amount / $tenor, 2);
            $status = $loanStatuses[$i % count($loanStatuses)];
            
            $remaining = ($status === 'PAID_OFF') ? 0 : (($status === 'ACTIVE') ? round($monthly * rand(1, $tenor - 1), 2) : $amount);

            $loan = EmployeeLoan::updateOrCreate(
                ['loan_number' => $loanNum],
                [
                    'nik' => $emp->nik,
                    'loan_amount' => $amount,
                    'interest_rate' => 0,
                    'total_amount' => $amount,
                    'installment_months' => $tenor,
                    'monthly_installment' => $monthly,
                    'remaining_amount' => $remaining,
                    'start_date' => Carbon::now()->subMonths(rand(1, 4))->startOfMonth()->format('Y-m-d'),
                    'status' => $status,
                    'approved_by' => in_array($status, ['ACTIVE', 'PAID_OFF', 'APPROVED']) ? $adminId : null,
                    'approved_at' => in_array($status, ['ACTIVE', 'PAID_OFF', 'APPROVED']) ? Carbon::now()->subMonths(2) : null,
                    'notes' => $notes[$i % count($notes)],
                ]
            );

            // Installments
            EmployeeLoanInstallment::where('employee_loan_id', $loan->id)->delete();
            $paidCount = ($status === 'PAID_OFF') ? $tenor : (($status === 'ACTIVE') ? ($tenor - (int)ceil($remaining / $monthly)) : 0);

            for ($m = 1; $m <= $tenor; $m++) {
                $dueDate = Carbon::parse($loan->start_date)->addMonths($m - 1)->setDay(25);
                $isPaid = $m <= $paidCount;

                EmployeeLoanInstallment::create([
                    'employee_loan_id' => $loan->id,
                    'installment_number' => $m,
                    'due_date' => $dueDate->format('Y-m-d'),
                    'amount' => $monthly,
                    'paid_amount' => $isPaid ? $monthly : 0,
                    'paid_at' => $isPaid ? $dueDate->copy()->subDays(2) : null,
                    'payroll_period_id' => $isPaid ? 4 : null,
                    'status' => $isPaid ? 'PAID' : 'UNPAID',
                ]);
            }
        }

        // 5. SEED LEMBUR (Overtime / Lembur)
        $this->command->info('Seeding Lembur...');
        $dayTypes = ['WORKDAY', 'OFFDAY_5DAYS', 'OFFDAY_6DAYS', 'PUBLIC_HOLIDAY'];
        $otStatuses = ['APPROVED', 'APPROVED', 'PENDING', 'APPROVED', 'REJECTED'];
        $otReasons = [
            'Roasting batch kopi cadangan pesanan korporat',
            'Closing bulanan stock opname logistik bahan baku',
            'Persiapan event launching seasonal blend',
            'Overtime coverage rekan shift sakit mendadak',
            'Maintenance preventif espresso machine & water filter',
            'Pelatihan barista magang materi latte art advance',
        ];

        for ($i = 0; $i < 22; $i++) {
            $emp = $employees[($i + 2) % $employees->count()];
            $tanggal = Carbon::now()->subDays(rand(1, 30));
            $noSpk = sprintf('SPK/%s/%04d', $tanggal->format('Ymd'), 301 + $i);
            $dayType = $dayTypes[$i % count($dayTypes)];
            $status = $otStatuses[$i % count($otStatuses)];
            
            $startHour = rand(17, 19);
            $durationHours = rand(2, 4);
            $startDt = $tanggal->copy()->setTime($startHour, 0, 0);
            $endDt = $startDt->copy()->addHours($durationHours);

            Lembur::updateOrCreate(
                ['no_spk' => $noSpk],
                [
                    'tanggal' => $tanggal->format('Y-m-d'),
                    'nik' => $emp->nik,
                    'overtime_policy_id' => 1,
                    'day_type' => $dayType,
                    'lembur_mulai' => $startDt,
                    'lembur_selesai' => $endDt,
                    'lembur_in' => $status === 'APPROVED' ? $startDt->copy()->addMinutes(rand(-5, 5)) : null,
                    'lembur_out' => $status === 'APPROVED' ? $endDt->copy()->addMinutes(rand(0, 10)) : null,
                    'planned_duration_minutes' => $durationHours * 60,
                    'actual_duration_minutes' => $status === 'APPROVED' ? $durationHours * 60 : 0,
                    'approved_duration_minutes' => $status === 'APPROVED' ? $durationHours * 60 : 0,
                    'calculated_rate_hours' => $status === 'APPROVED' ? ($durationHours * 1.5) : 0.00,
                    'status' => $status,
                    'keterangan' => $otReasons[$i % count($otReasons)],
                    'approved_by' => $status === 'APPROVED' ? $adminId : null,
                    'approved_at' => $status === 'APPROVED' ? $tanggal->copy()->setTime(22, 0, 0) : null,
                ]
            );
        }

        // 6. SEED EMPLOYEE RESIGNATIONS & CLEARANCES
        $this->command->info('Seeding Resignations...');
        $categories = ['RESIGNED', 'END_OF_CONTRACT', 'RETIRED', 'RESIGNED'];
        $clearanceStatuses = ['CLEARED', 'IN_PROGRESS', 'PENDING'];
        $resReasons = [
            'Mendapat kesempatan studi lanjut pascasarjana di luar kota',
            'Relokasi domisili keluarga ke Jawa Tengah',
            'Membuka usaha kedai kopi keluarga sendiri',
            'Kontrak kerja masa waktu tertentu telah berakhir',
            'Memasuki usia pensiun sesuai ketentuan ketenagakerjaan',
        ];

        for ($i = 0; $i < 10; $i++) {
            $emp = $employees[($i + 12) % $employees->count()];
            $applyDate = Carbon::now()->subDays(rand(10, 60));
            $exitDate = $applyDate->copy()->addDays(30);
            $cat = $categories[$i % count($categories)];
            $clr = $clearanceStatuses[$i % count($clearanceStatuses)];

            EmployeeResignation::updateOrCreate(
                ['nik' => $emp->nik],
                [
                    'tanggal_pengajuan' => $applyDate->format('Y-m-d'),
                    'tanggal_keluar' => $exitDate->format('Y-m-d'),
                    'kategori_keluar' => $cat,
                    'alasan' => $resReasons[$i % count($resReasons)],
                    'status_clearance' => $clr,
                    'catatan_hr' => 'Proses wawancara keluar (exit interview) & inventaris operasional.',
                    'status' => ($clr === 'CLEARED') ? 'APPROVED' : 'PENDING',
                    'approved_by' => ($clr === 'CLEARED') ? $adminId : null,
                    'total_settlement' => rand(5, 20) * 1000000,
                    'settlement_status' => ($clr === 'CLEARED') ? 'PAID' : 'PENDING',
                ]
            );
        }

        // 7. SEED EMPLOYEE MOVEMENTS (Mutasi / Promosi / Rotasi)
        $this->command->info('Seeding Movements...');
        $movTypes = ['BRANCH_TRANSFER', 'PROMOTION', 'DEPT_TRANSFER', 'POSITION_CHANGE'];
        $allCabang = Cabang::pluck('kode_cabang')->toArray();
        $allDept = Departemen::pluck('kode_dept')->toArray();
        $allJabatan = Jabatan::pluck('nama_jabatan')->toArray();

        for ($i = 0; $i < 12; $i++) {
            $emp = $employees[($i + 18) % $employees->count()];
            $effDate = Carbon::now()->subDays(rand(5, 90));
            $noSk = sprintf('SK/MUT/%04d/%04d', $effDate->year, 401 + $i);
            $mType = $movTypes[$i % count($movTypes)];

            $oldVals = [
                'cabang' => $emp->kode_cabang ?? 'CS1',
                'departemen' => $emp->kode_dept ?? 'PRD',
                'jabatan' => $emp->nama_jabatan ?? 'Staff',
            ];

            $newVals = $oldVals;
            if ($mType === 'BRANCH_TRANSFER' && count($allCabang) > 1) {
                $targetCabang = array_values(array_diff($allCabang, [$oldVals['cabang']]))[0] ?? $oldVals['cabang'];
                $newVals['cabang'] = $targetCabang;
            } elseif ($mType === 'PROMOTION') {
                $newVals['jabatan'] = 'Supervisor Outlet Senior';
            } elseif ($mType === 'DEPT_TRANSFER' && count($allDept) > 1) {
                $targetDept = array_values(array_diff($allDept, [$oldVals['departemen']]))[0] ?? $oldVals['departemen'];
                $newVals['departemen'] = $targetDept;
            } else {
                $newVals['jabatan'] = 'Senior Specialist';
            }

            EmployeeMovement::updateOrCreate(
                ['no_sk' => $noSk],
                [
                    'nik' => $emp->nik,
                    'movement_type' => $mType,
                    'effective_date' => $effDate->format('Y-m-d'),
                    'old_values' => $oldVals,
                    'new_values' => $newVals,
                    'reason' => 'Pengembangan kapabilitas organisasi dan pemenuhan standar supervisi cabang.',
                    'status' => 'APPROVED',
                    'approved_by' => $adminId,
                    'created_by' => $adminId,
                ]
            );
        }

        // 8. SEED EMPLOYEE DOCUMENTS (Dokumen Kepegawaian)
        $this->command->info('Seeding Documents...');
        $docTypes = ['KTP', 'NPWP', 'BPJS', 'IJAZAH', 'SERTIFIKAT', 'KONTRAK'];
        $docTitles = [
            'KTP' => 'Kartu Tanda Penduduk Elektronik (e-KTP)',
            'NPWP' => 'Nomor Pokok Wajib Pajak (NPWP)',
            'BPJS' => 'Kartu Kepesertaan BPJS Ketenagakerjaan & Kesehatan',
            'IJAZAH' => 'Salinan Ijazah Pendidikan Terakhir & Transkrip',
            'SERTIFIKAT' => 'Sertifikat Kompetensi Barista & Sensory Evaluation',
            'KONTRAK' => 'Salinan Perjanjian Kerja Waktu Tertentu (PKWT)',
        ];

        for ($i = 0; $i < 24; $i++) {
            $emp = $employees[($i + 8) % $employees->count()];
            $dType = $docTypes[$i % count($docTypes)];

            EmployeeDocument::updateOrCreate(
                [
                    'nik' => $emp->nik,
                    'document_type' => $dType,
                ],
                [
                    'title' => $docTitles[$dType] ?? 'Dokumen Karyawan',
                    'file_path' => 'documents/dummy_' . strtolower($dType) . '.pdf',
                    'file_size_kb' => rand(150, 2400),
                    'expiry_date' => in_array($dType, ['KONTRAK', 'SERTIFIKAT']) ? Carbon::now()->addMonths(rand(6, 24))->format('Y-m-d') : null,
                    'notes' => 'Dokumen terverifikasi resmi oleh HR.',
                    'uploaded_by' => $adminId,
                ]
            );
        }

        // 9. SEED DISPENSASI PRESENSI
        $this->command->info('Seeding Dispensasi...');
        $dispReasons = [
            'Macet total akibat perbaikan jembatan akses utama outlet',
            'Banjir menggenangi jalan poros perumahan karyawan',
            'Kendala ban bocor saat dalam perjalanan antar pesanan',
            'Pemeriksaan medis pagi sebelum shift kerja siang',
        ];

        for ($i = 0; $i < 12; $i++) {
            $emp = $employees[($i + 14) % $employees->count()];
            $dispDate = Carbon::now()->subDays(rand(1, 20));
            $status = ['APPROVED', 'APPROVED', 'PENDING', 'REJECTED'][$i % 4];

            PresensiDispensasi::updateOrCreate(
                [
                    'nik' => $emp->nik,
                    'tanggal' => $dispDate->format('Y-m-d'),
                ],
                [
                    'batas_dispensasi' => '08:45:00',
                    'alasan' => $dispReasons[$i % count($dispReasons)],
                    'status' => $status,
                    'approved_by' => in_array($status, ['APPROVED', 'REJECTED']) ? $adminId : null,
                ]
            );
        }

        // 10. SEED PRESENSI IZIN SAKIT
        $this->command->info('Seeding Izin Sakit...');
        $sakitReasons = [
            'Demam tinggi & radang tenggorokan (Surat Dokter Terlampir)',
            'Pemeriksaan lambung / gastritis akut di klinik mitra',
            'Cedera terkilir pergelangan tangan (Istirahat 2 hari)',
            'Gejala tipes disarankan istirahat dokter RS Hermina',
        ];

        for ($i = 0; $i < 12; $i++) {
            $emp = $employees[($i + 22) % $employees->count()];
            $sakitDate = Carbon::now()->subDays(rand(1, 25));
            $kodeIzinSakit = sprintf('SKT%s%03d', $sakitDate->format('ymd'), $i + 1);
            $dari = $sakitDate->format('Y-m-d');
            $sampai = $sakitDate->copy()->addDays(rand(0, 2))->format('Y-m-d');
            $status = ['1', '1', '0', '2'][$i % 4]; // 1: approve, 0: pending, 2: tolak

            DB::table('presensi_izinsakit')->updateOrInsert(
                ['kode_izin_sakit' => $kodeIzinSakit],
                [
                    'nik' => $emp->nik,
                    'tanggal' => $dari,
                    'dari' => $dari,
                    'sampai' => $sampai,
                    'doc_sid' => 'sid_dummy_' . $emp->nik . '.jpg',
                    'keterangan' => $sakitReasons[$i % count($sakitReasons)],
                    'keterangan_hrd' => $status == '1' ? 'Disetujui berdasarkan surat istirahat dokter' : null,
                    'status' => $status,
                    'approval_step' => $status == '1' ? 2 : 1,
                    'id_user' => $adminId,
                    'created_at' => $sakitDate,
                    'updated_at' => $sakitDate,
                ]
            );
        }

        $this->command->info('All Admin Tables successfully populated with clean dummy data!');
    }
}
