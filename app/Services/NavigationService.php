<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class NavigationService
{
    /**
     * Get all searchable navigation features filtered dynamically by:
     * 1. Active Client Preset Module Feature Flags (module_enabled)
     * 2. User Roles and Spatie Permissions (can / hasRole)
     *
     * @return array<int, array{name: string, url: string, icon: string, category: string, desc: string, keywords: string}>
     */
    public static function getSearchableFeatures(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        $items = [];

        // ------------------------------------------------------------------
        // 1. UTAMA
        // ------------------------------------------------------------------
        $items[] = [
            'name' => 'Dashboard',
            'url' => route('dashboard.index'),
            'icon' => 'ti-home',
            'category' => 'Utama',
            'desc' => 'Ringkasan presensi & analitik operasional real-time',
            'keywords' => 'dashboard beranda home utama monitoring ringkasan statistik analitik',
        ];

        // ------------------------------------------------------------------
        // 2. DATA MASTER
        // ------------------------------------------------------------------
        if ($user->can('karyawan.index')) {
            $hasFace = module_enabled('face_recognition');
            $items[] = [
                'name' => $hasFace ? 'Karyawan & Wajah' : 'Data Karyawan',
                'url' => route('karyawan.index'),
                'icon' => 'ti-users',
                'category' => 'Data Master',
                'desc' => $hasFace ? 'Master data karyawan & pendaftaran biometrik AI wajah' : 'Master data biodata karyawan & profil staf',
                'keywords' => 'karyawan pegawai staff biometric wajah face data nik biodata anggota',
            ];
        }

        if (module_enabled('attendance') && $user->can('jamkerja.index')) {
            $items[] = [
                'name' => 'Shift Kerja (Pagi & Siang)',
                'url' => route('jamkerja.index'),
                'icon' => 'ti-clock',
                'category' => 'Data Master',
                'desc' => 'Atur jadwal shift kerja (pagi/siang/malam)',
                'keywords' => 'shift jam kerja jadwal roster pagi siang malam jam masuk jam pulang',
            ];
        }

        if ($user->can('harilibur.index')) {
            $items[] = [
                'name' => 'Hari Libur / Tanggal Merah',
                'url' => route('harilibur.index'),
                'icon' => 'ti-calendar-off',
                'category' => 'Data Master',
                'desc' => 'Daftar hari libur nasional & cuti bersama',
                'keywords' => 'hari libur tanggal merah kalender cuti bersama libur nasional tanggal merah',
            ];
        }

        if ($user->can('cabang.index')) {
            $items[] = [
                'name' => 'Outlet / Cabang',
                'url' => route('cabang.index'),
                'icon' => 'ti-building-store',
                'category' => 'Data Master',
                'desc' => 'Master data outlet, cabang, & radius GPS presensi',
                'keywords' => 'cabang outlet toko store coffee shop lokasi branch radius koordinat titik gps',
            ];
        }

        if ($user->can('departemen.index')) {
            $items[] = [
                'name' => 'Departemen',
                'url' => route('departemen.index'),
                'icon' => 'ti-building',
                'category' => 'Data Master',
                'desc' => 'Manajemen departemen & struktur bagian kerja',
                'keywords' => 'departemen bagian section unit department divisi kerja organisasi',
            ];
        }

        if ($user->can('divisi.index')) {
            $items[] = [
                'name' => 'Divisi & Tim',
                'url' => route('divisi.index'),
                'icon' => 'ti-sitemap',
                'category' => 'Data Master',
                'desc' => 'Kelola sub-divisi, squad, & tim operasional',
                'keywords' => 'divisi tim squad sub bagian unit kelompok kerja team group',
            ];
        }

        if ($user->can('jabatan.index')) {
            $items[] = [
                'name' => 'Jabatan / Posisi',
                'url' => route('jabatan.index'),
                'icon' => 'ti-id',
                'category' => 'Data Master',
                'desc' => 'Struktur tingkatan posisi & jabatan karyawan',
                'keywords' => 'jabatan posisi role pangkat title occupation hierarki level grading',
            ];
        }

        if (module_enabled('leave') && ($user->can('leave_types.index') || $user->can('cuti.index'))) {
            $items[] = [
                'name' => 'Jenis Cuti',
                'url' => $user->can('leave_types.index') ? route('leave_types.index') : route('cuti.index'),
                'icon' => 'ti-calendar-event',
                'category' => 'Data Master',
                'desc' => 'Master kategori cuti tahunan, cuti khusus, & izin normatif',
                'keywords' => 'jenis cuti tahunan libur kuota annual leave aturan hak cuti melahirkan kematian',
            ];
        }

        // ------------------------------------------------------------------
        // 3. KEPEGAWAIAN & KARIR
        // ------------------------------------------------------------------
        if (module_enabled('contract') && $user->can('kontrak.index')) {
            $items[] = [
                'name' => 'Kontrak Kerja',
                'url' => route('kontrak.index'),
                'icon' => 'ti-file-pencil',
                'category' => 'Kepegawaian',
                'desc' => 'Kelola PKWT, PKWTT, probation, & perpanjangan SPK',
                'keywords' => 'kontrak kerja pkwt pkwtt perjanjian kerja masa berlaku perpanjangan spk probation masa percobaan',
            ];
        }

        if (module_enabled('movement') && $user->can('movement.index')) {
            $items[] = [
                'name' => 'Mutasi & Karir',
                'url' => route('movement.index'),
                'icon' => 'ti-arrows-exchange',
                'category' => 'Kepegawaian',
                'desc' => 'Riwayat mutasi cabang, promosi, demosi, & rotasi kerja',
                'keywords' => 'mutasi promosi demosi rotasi pindah cabang karir pergerakan karyawan jabatan baru',
            ];
        }

        if (module_enabled('resignation') && $user->can('resignation.index')) {
            $items[] = [
                'name' => 'Resign & Offboarding',
                'url' => route('resignation.index'),
                'icon' => 'ti-user-minus',
                'category' => 'Kepegawaian',
                'desc' => 'Pengajuan pengunduran diri, exit clearance, & pesangon PP 35',
                'keywords' => 'resign offboarding pengunduran diri keluar berhenti exit clearance pesangon hak akhir',
            ];
        }

        if (module_enabled('leave') && $user->can('leave_quotas.index')) {
            $items[] = [
                'name' => 'Saldo Kuota Cuti',
                'url' => route('leave_quotas.index'),
                'icon' => 'ti-chart-pie',
                'category' => 'Kepegawaian',
                'desc' => 'Alokasi saldo kuota tahunan, pemakaian, & penyesuaian hak cuti',
                'keywords' => 'saldo kuota cuti sisa cuti penyesuaian hak cuti alokasi balance carry forward',
            ];
        }

        if (module_enabled('overtime') && $user->can('overtime.index')) {
            $items[] = [
                'name' => 'Lembur & SPK',
                'url' => route('overtime.index'),
                'icon' => 'ti-clock-bolt',
                'category' => 'Kepegawaian',
                'desc' => 'Surat perintah kerja lembur & kalkulasi upah lembur Depnaker',
                'keywords' => 'lembur spk surat perintah kerja overtime jam lembur upah depnaker hitung lembur',
            ];
        }

        if (module_enabled('recruitment') && $user->can('recruitment.index')) {
            $items[] = [
                'name' => 'Rekrutmen & Pelamar',
                'url' => route('recruitment.index'),
                'icon' => 'ti-briefcase',
                'category' => 'Kepegawaian',
                'desc' => 'Lowongan kerja, pipeline kandidat, & seleksi pelamar HR',
                'keywords' => 'rekrutmen pelamar lowongan kerja kandidat interview hrd loker recruitment hiring seleksi',
            ];
        }

        if (module_enabled('onboarding') && $user->can('onboarding.index')) {
            $items[] = [
                'name' => 'Onboarding Karyawan',
                'url' => route('onboarding.index'),
                'icon' => 'ti-user-plus',
                'category' => 'Kepegawaian',
                'desc' => 'Checklist orientasi karyawan baru & induction program',
                'keywords' => 'onboarding orientasi karyawan baru checklist tugas masa percobaan induction penyambutan staf',
            ];
        }

        if ($user->can('org_chart.index')) {
            $items[] = [
                'name' => 'Bagan Organisasi',
                'url' => route('org_chart.index'),
                'icon' => 'ti-hierarchy-2',
                'category' => 'Kepegawaian',
                'desc' => 'Visualisasi struktur hierarki & pohon organisasi perusahaan',
                'keywords' => 'bagan organisasi struktur organisasi pohon hierarki tree chart organigram atasan bawahan',
            ];
        }

        if ($user->can('karyawan.import')) {
            $items[] = [
                'name' => 'Import & Kelola Massal',
                'url' => route('karyawan.import.index'),
                'icon' => 'ti-file-upload',
                'category' => 'Kepegawaian',
                'desc' => 'Import template CSV karyawan & pembaruan massal',
                'keywords' => 'import kelola massal excel csv upload data karyawan bulk update unduh template',
            ];
        }

        // ------------------------------------------------------------------
        // 4. KINERJA & TATA KELOLA
        // ------------------------------------------------------------------
        if (module_enabled('performance') && $user->can('performance.index')) {
            $items[] = [
                'name' => 'Penilaian Kinerja & KPI',
                'url' => route('performance.index'),
                'icon' => 'ti-award',
                'category' => 'Kinerja & Tata Kelola',
                'desc' => 'Evaluasi kinerja berkala, review KPI, & penilaian kompetensi',
                'keywords' => 'kinerja kpi penilaian evaluasi performa appraisal skor review target capaian kompetensi',
            ];
        }

        if (module_enabled('training') && $user->can('training.index')) {
            $items[] = [
                'name' => 'Pelatihan & Sertifikasi',
                'url' => route('training.index'),
                'icon' => 'ti-certificate',
                'category' => 'Kinerja & Tata Kelola',
                'desc' => 'Program pelatihan, workshop, pengembangan skill, & sertifikat',
                'keywords' => 'pelatihan sertifikasi training kursus workshop seminar skill kompetensi sertifikat pengembangan',
            ];
        }

        if (module_enabled('warning') && $user->can('warning.index')) {
            $items[] = [
                'name' => 'Disiplin & Surat Peringatan',
                'url' => route('warning.index'),
                'icon' => 'ti-alert-triangle',
                'category' => 'Kinerja & Tata Kelola',
                'desc' => 'Penerbitan SP 1, SP 2, SP 3, teguran, & masa berlaku sanksi',
                'keywords' => 'disiplin surat peringatan sp1 sp2 sp3 sanksi pelanggaran tata tertib surat teguran',
            ];
        }

        if (module_enabled('document') && $user->can('document.index')) {
            $items[] = [
                'name' => 'Brankas Dokumen',
                'url' => route('document.index'),
                'icon' => 'ti-folder',
                'category' => 'Kinerja & Tata Kelola',
                'desc' => 'Arsip berkas digital, KTP, ijazah, & dokumen kepegawaian',
                'keywords' => 'brankas dokumen arsip berkas ktp ijazah file sim sertifikat digital scan berkas staf',
            ];
        }

        if (module_enabled('policy')) {
            $items[] = [
                'name' => 'Peraturan & SOP',
                'url' => route('policy.index'),
                'icon' => 'ti-book',
                'category' => 'Kinerja & Tata Kelola',
                'desc' => 'Peraturan perusahaan, panduan tata kerja, & standar operasional',
                'keywords' => 'peraturan sop tata tertib kebijakan perusahaan standar operasional regulasi kepatuhan buku panduan',
            ];
        }

        if (module_enabled('asset') && $user->can('asset.index')) {
            $items[] = [
                'name' => 'Aset & Fasilitas',
                'url' => route('asset.index'),
                'icon' => 'ti-device-laptop',
                'category' => 'Kinerja & Tata Kelola',
                'desc' => 'Inventaris laptop, seragam, kendaraan, & fasilitas kerja',
                'keywords' => 'aset fasilitas inventaris peminjaman laptop seragam alat kerja device handover barang kantor',
            ];
        }

        if (module_enabled('announcement') && $user->can('announcement.index')) {
            $items[] = [
                'name' => 'Pengumuman Internal',
                'url' => route('announcement.index'),
                'icon' => 'ti-speakerphone',
                'category' => 'Kinerja & Tata Kelola',
                'desc' => 'Siaran berita perusahaan, edaran manajemen, & memo internal',
                'keywords' => 'pengumuman internal siaran broadcast memo edaran info berita perusahaan broadcast news',
            ];
        }

        if (module_enabled('incident') && $user->can('incident.index')) {
            $items[] = [
                'name' => 'Kasus & Insiden HR',
                'url' => route('incident.index'),
                'icon' => 'ti-shield-alert',
                'category' => 'Kinerja & Tata Kelola',
                'desc' => 'Pencatatan pelanggaran, perselisihan kerja, & mediasi internal',
                'keywords' => 'insiden kasus kecelakaan pelanggaran keluhan perselisihan mediasi grievance investigasi',
            ];
        }

        // ------------------------------------------------------------------
        // 5. ABSENSI & KEHADIRAN
        // ------------------------------------------------------------------
        if (module_enabled('attendance')) {
            if ($user->can('presensi.index')) {
                $items[] = [
                    'name' => 'Monitoring Presensi',
                    'url' => route('presensi.index'),
                    'icon' => 'ti-map-pin-check',
                    'category' => 'Kehadiran & Absensi',
                    'desc' => 'Pantau absensi harian & foto presensi realtime',
                    'keywords' => 'monitoring presensi absensi absen hari ini kehadiran checkin checkout foto log hadir live monitoring',
                ];
            }

            if ($user->can('trackingpresensi.index')) {
                $items[] = [
                    'name' => 'Live Tracking GPS',
                    'url' => route('trackingpresensi.index'),
                    'icon' => 'ti-radar',
                    'category' => 'Kehadiran & Absensi',
                    'desc' => 'Pelacakan koordinat GPS & riwayat rute presensi',
                    'keywords' => 'live tracking gps peta maps lokasi lacak rute real-time koordinat radius geofencing',
                ];
            }

            $items[] = [
                'name' => 'Dispensasi Terlambat',
                'url' => route('dispensasi.index'),
                'icon' => 'ti-clock-edit',
                'category' => 'Kehadiran & Absensi',
                'desc' => 'Kompensasi & batas toleransi keterlambatan kehadiran',
                'keywords' => 'dispensasi terlambat telat late kompensasi waktu batas toleransi izin masuk pengampunan telat',
            ];
        }

        // ------------------------------------------------------------------
        // 6. PENGAJUAN & PERSETUJUAN
        // ------------------------------------------------------------------
        if (module_enabled('leave')) {
            if ($user->can('izinabsen.index')) {
                $items[] = [
                    'name' => 'Persetujuan Izin',
                    'url' => route('izinabsen.index'),
                    'icon' => 'ti-calendar-event',
                    'category' => 'Persetujuan',
                    'desc' => 'Persetujuan permohonan izin absen biasa',
                    'keywords' => 'persetujuan izin absen permohonan dispensasi approval verifikasi izin tidak masuk form izin',
                ];
            }

            if ($user->can('izinsakit.index')) {
                $items[] = [
                    'name' => 'Persetujuan Izin Sakit',
                    'url' => route('izinsakit.index'),
                    'icon' => 'ti-file-certificate',
                    'category' => 'Persetujuan',
                    'desc' => 'Verifikasi izin sakit & surat keterangan dokter',
                    'keywords' => 'persetujuan izin sakit dokter bukti surat sakit verifikasi medis opname istirahat sakit',
                ];
            }

            if ($user->can('izincuti.index')) {
                $items[] = [
                    'name' => 'Persetujuan Cuti Karyawan',
                    'url' => route('izincuti.index'),
                    'icon' => 'ti-calendar-time',
                    'category' => 'Persetujuan',
                    'desc' => 'Konfirmasi & persetujuan pengajuan cuti tahunan',
                    'keywords' => 'persetujuan cuti tahunan izin cuti verifikasi sisa kuota approve pengajuan libur acc cuti',
                ];
            }
        }

        // ------------------------------------------------------------------
        // 7. KEUANGAN & PAYROLL
        // ------------------------------------------------------------------
        if (module_enabled('payroll')) {
            if ($user->can('payroll.index')) {
                $items[] = [
                    'name' => 'Periode Payroll',
                    'url' => route('payroll.index'),
                    'icon' => 'ti-calculator',
                    'category' => 'Keuangan & Payroll',
                    'desc' => 'Proses hitung gaji bulanan, lembur, potongan & take home pay',
                    'keywords' => 'payroll gaji penggajian hitung gaji proses payroll bulanan slip thp upah rekap transfer gaji',
                ];
            }

            if ($user->can('payslip.index')) {
                $items[] = [
                    'name' => 'Slip Gaji (Payslip)',
                    'url' => route('payslip.index'),
                    'icon' => 'ti-receipt',
                    'category' => 'Keuangan & Payroll',
                    'desc' => 'Unduh & cetak slip gaji karyawan digital dengan enkripsi PIN',
                    'keywords' => 'slip gaji payslip cetak download rincian gaji potongan upah karyawan pdf slip digital',
                ];
            }

            if ($user->can('employee_salary.index')) {
                $items[] = [
                    'name' => 'Struktur Gaji Karyawan',
                    'url' => route('employee_salary.index'),
                    'icon' => 'ti-cash',
                    'category' => 'Keuangan & Payroll',
                    'desc' => 'Penetapan gaji pokok, tunjangan jabatan, & upah per karyawan',
                    'keywords' => 'struktur gaji karyawan nominal gaji pokok tunjangan penyesuaian gaji tetap setting upah',
                ];
            }

            if ($user->can('salary_component.index')) {
                $items[] = [
                    'name' => 'Komponen Gaji',
                    'url' => route('salary_components.index'),
                    'icon' => 'ti-adjustments',
                    'category' => 'Keuangan & Payroll',
                    'desc' => 'Master komponen penambah (allowance) & pengurang gaji (deduction)',
                    'keywords' => 'komponen gaji tunjangan potongan allowance deduction master upah rumus potongan',
                ];
            }

            if ($user->can('compliance.index')) {
                $items[] = [
                    'name' => 'Kepatuhan & Pajak TER',
                    'url' => route('compliance.index'),
                    'icon' => 'ti-scale',
                    'category' => 'Keuangan & Payroll',
                    'desc' => 'Kalkulasi PPh 21 tarif efektif rata-rata (TER) & iuran BPJS TK/Kes',
                    'keywords' => 'kepatuhan pajak ter pph21 bpjs ketenagakerjaan kesehatan ptkp simulasi kalkulator potongan resmi',
                ];
            }

            if ($user->can('thr.index')) {
                $items[] = [
                    'name' => 'THR Keagamaan',
                    'url' => route('thr.index'),
                    'icon' => 'ti-gift',
                    'category' => 'Keuangan & Payroll',
                    'desc' => 'Kalkulasi tunjangan hari raya keagamaan penuh & prorata PP 36',
                    'keywords' => 'thr tunjangan hari raya keagamaan idul fitri natal prorata bonus tahunan hari raya',
                ];
            }

            if (module_enabled('reimbursement') && $user->can('reimbursement.index')) {
                $items[] = [
                    'name' => 'Reimbursement & Klaim',
                    'url' => route('reimbursement.index'),
                    'icon' => 'ti-receipt-refund',
                    'category' => 'Keuangan & Payroll',
                    'desc' => 'Pengajuan & verifikasi klaim biaya operasional, medis, bensin',
                    'keywords' => 'reimbursement klaim biaya reimburse pengeluaran nota struk pengembalian uang kuitansi klaim',
                ];
            }

            if (module_enabled('loans') && $user->can('loan.index')) {
                $items[] = [
                    'name' => 'Pinjaman & Kasbon',
                    'url' => route('loan.index'),
                    'icon' => 'ti-credit-card',
                    'category' => 'Keuangan & Payroll',
                    'desc' => 'Kelola kasbon karyawan & skema cicilan potong gaji otomatis',
                    'keywords' => 'pinjaman kasbon utang cicilan potong gaji angsuran kredit karyawan hutang dana darurat',
                ];
            }
        }

        // ------------------------------------------------------------------
        // 8. REKAP & LAPORAN
        // ------------------------------------------------------------------
        if ($user->can('reports.index')) {
            $items[] = [
                'name' => 'Laporan & Analitik HR',
                'url' => route('reports.index'),
                'icon' => 'ti-chart-bar',
                'category' => 'Rekap & Laporan',
                'desc' => 'Dashboard metrik komprehensif, turnover, demografi, & headcount',
                'keywords' => 'laporan analitik hr metrik demografi statistik turnover headcount rekapitulasi chart grafik tren',
            ];
        }

        if (module_enabled('attendance') && $user->can('laporan.presensi')) {
            $items[] = [
                'name' => 'Laporan Presensi (Excel)',
                'url' => route('laporan.presensi'),
                'icon' => 'ti-file-spreadsheet',
                'category' => 'Rekap & Laporan',
                'desc' => 'Cetak & unduh rekap absensi kehadiran harian & bulanan',
                'keywords' => 'laporan presensi excel rekap absensi cetak download format rekapitulasi export',
            ];
        }

        if (module_enabled('leave') && $user->can('laporan.cuti')) {
            $items[] = [
                'name' => 'Rekap Cuti Karyawan',
                'url' => route('laporan.cuti'),
                'icon' => 'ti-file-report',
                'category' => 'Rekap & Laporan',
                'desc' => 'Laporan pemakaian hak cuti & sisa kuota seluruh karyawan',
                'keywords' => 'rekap cuti laporan cuti karyawan penggunaan sisa kuota periode rekapitulasi libur',
            ];
        }

        // ------------------------------------------------------------------
        // 9. PENGATURAN SISTEM
        // ------------------------------------------------------------------
        if ($user->hasRole('super admin')) {
            if ($user->can('settings.index')) {
                $items[] = [
                    'name' => 'Pusat Direktori Pengaturan',
                    'url' => route('settings.hub'),
                    'icon' => 'ti-settings',
                    'category' => 'Pengaturan Sistem',
                    'desc' => 'Pusat navigasi & konfigurasi seluruh pengaturan sistem',
                    'keywords' => 'pengaturan hub direktori settings sistem konfigurasi pusat kontrol panel admin',
                ];
            }

            if ($user->can('company_settings.index')) {
                $items[] = [
                    'name' => 'Profil Perusahaan & Branding',
                    'url' => route('company_settings.index'),
                    'icon' => 'ti-building-skyscraper',
                    'category' => 'Pengaturan Sistem',
                    'desc' => 'Nama perusahaan, logo, warna tema, & identitas visual',
                    'keywords' => 'profil perusahaan branding logo nama instansi warna tema brand identity',
                ];
            }

            if ($user->can('module_features.index')) {
                $items[] = [
                    'name' => 'Modul & Feature Flags',
                    'url' => route('module_features.index'),
                    'icon' => 'ti-toggle-left',
                    'category' => 'Pengaturan Sistem',
                    'desc' => 'Aktif / nonaktifkan modul fitur secara fleksibel',
                    'keywords' => 'modul feature flags toggle aktif nonaktif modul fitur sistem switch on off',
                ];
            }

            if (module_enabled('attendance') && $user->can('attendance_policy.index')) {
                $items[] = [
                    'name' => 'Kebijakan Presensi',
                    'url' => route('attendance_policy.index'),
                    'icon' => 'ti-clock-cog',
                    'category' => 'Pengaturan Sistem',
                    'desc' => 'Kebijakan toleransi keterlambatan, hari kerja, & metode absen',
                    'keywords' => 'kebijakan presensi aturan absen toleransi hari kerja jam kantor batas radius',
                ];
            }

            if (module_enabled('overtime') && $user->can('overtime_policy.index')) {
                $items[] = [
                    'name' => 'Kebijakan Lembur',
                    'url' => route('overtime_policy.index'),
                    'icon' => 'ti-clock-pin',
                    'category' => 'Pengaturan Sistem',
                    'desc' => 'Aturan perkalian tarif lembur hari kerja & hari libur Depnaker',
                    'keywords' => 'kebijakan lembur aturan tarif overtime tier depnaker pengali rumus lembur',
                ];
            }

            if ($user->can('generalsetting.index')) {
                $items[] = [
                    'name' => 'Pengaturan Operasional & GPS',
                    'url' => route('generalsetting.index'),
                    'icon' => 'ti-adjustments-alt',
                    'category' => 'Pengaturan Sistem',
                    'desc' => 'Konfigurasi radius GPS outlet, batas toleransi, & bot notifikasi',
                    'keywords' => 'pengaturan umum gps setting radius lokasi outlet konfigurasi aplikasi bot wa',
                ];
            }

            $items[] = [
                'name' => 'Manajemen Akun User',
                'url' => route('users.index'),
                'icon' => 'ti-user-cog',
                'category' => 'Pengaturan Sistem',
                'desc' => 'Kelola akun login admin, hak akses, peran, & permission Spatie',
                'keywords' => 'manajemen akun user pengguna admin login hak akses role permissions sandi password',
            ];
        }

        if ($user->can('audit_logs.index')) {
            $items[] = [
                'name' => 'Log Jejak Audit',
                'url' => route('settings.audit_logs.index'),
                'icon' => 'ti-history',
                'category' => 'Keamanan & Audit',
                'desc' => 'Rekaman aktivitas pengguna, riwayat perubahan data, & audit trail',
                'keywords' => 'log audit jejak audit trail riwayat aktivitas keamanan user tracker security rekaman aksi',
            ];
        }

        if ($user->can('presets.index')) {
            $items[] = [
                'name' => 'Matriks Preset Klien',
                'url' => route('settings.presets.index'),
                'icon' => 'ti-layout-grid',
                'category' => 'Pengaturan Sistem',
                'desc' => 'Beralih template preset bisnis (Cafe F&B, Office, Retail, Jasa, Remote)',
                'keywords' => 'preset matriks template preset klien model bisnis cafe retail remote switch template',
            ];
        }

        // ------------------------------------------------------------------
        // 10. BANTUAN & PROFIL
        // ------------------------------------------------------------------
        $items[] = [
            'name' => 'Pusat Bantuan & Panduan',
            'url' => route('help.index'),
            'icon' => 'ti-help',
            'category' => 'Bantuan',
            'desc' => 'Panduan penggunaan sistem, alur modul, & dokumentasi teknis',
            'keywords' => 'pusat bantuan panduan dokumentasi cara penggunaan faq help center manual user guide',
        ];

        $items[] = [
            'name' => 'Pengaturan Profil Saya',
            'url' => route('profile.editprofile'),
            'icon' => 'ti-user-check',
            'category' => 'Akun Saya',
            'desc' => 'Ubah data profil akun, email, & ganti kata sandi login',
            'keywords' => 'profil ubah ganti password kata sandi akun saya user update foto email biodata',
        ];

        return $items;
    }
}
