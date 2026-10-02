<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Panduan Bantuan Sistem Presensi GPS & Operasional Admin
    |--------------------------------------------------------------------------
    |
    | Panduan ringkas, padat, dan langsung ke tindakan nyata untuk Admin,
    | HR, dan Supervisor operasional. Disusun singkat agar cepat dipahami
    | tanpa istilah teknis berbelit atau kalimat berulang.
    |
    */

    'pages' => [
        // 1. PANDUAN UTAMA ADMIN & ROSTER JADWAL KERJA
        'panduan_admin_roster' => [
            'id' => 'panduan_admin_roster',
            'pattern' => ['panduan-admin', 'karyawan/*/setjamkerja'],
            'title' => 'Panduan Roster & Jadwal Kerja',
            'subtitle' => 'Atur jadwal mingguan, tukar shift harian, dan persetujuan izin',
            'icon' => 'ti ti-calendar-event',
            'badge' => 'Admin & Supervisor',
            'about' => 'Pusat pengaturan jam kerja staf, tukar shift per tanggal, perbantuan cabang, dan koreksi absen harian.',
            'steps' => [
                'Buka menu Data Karyawan, lalu klik ikon Set Jam Kerja (kalender) pada baris staf.',
                'Tentukan shift kerja dan cabang tugas harian pada bagian Jadwal Mingguan.',
                'Jika staf tukar shift atau diperbantukan ke cabang lain, isi tanggalnya pada Jadwal Khusus.',
                'Pantau kehadiran di Monitoring Presensi. Klik ikon pensil hijau jika butuh koreksi absen manual.',
                'Buka menu Persetujuan Izin untuk menyetujui izin atau cuti staf.',
            ],
            'components' => [
                'Jadwal Khusus Tanggal' => 'Khusus tukar shift atau rotasi cabang harian. Otomatis menimpa jadwal mingguan.',
                'Roster Mingguan' => 'Pola kerja rutin 7 hari. Hari libur (OFF) bebas diatur di hari apa saja.',
                'Koreksi Absen Manual' => 'Koreksi jam masuk atau pulang jika staf terkendala, wajib isi alasan resmi.',
            ],
            'tips' => [
                'Jika staf ditugaskan sementara ke cabang lain, atur di Jadwal Khusus agar titik GPS absennya otomatis terkunci di cabang tujuan.',
            ],
            'warnings' => 'Jangan menyetujui izin jika staf sudah absen hadir fisik pada tanggal tersebut. Hapus atau perbaiki absen terlebih dahulu.',
        ],

        // 2. PANDUAN RINGKAS KARYAWAN
        'panduan_karyawan' => [
            'id' => 'panduan_karyawan',
            'pattern' => ['panduan-karyawan'],
            'title' => 'Panduan Presensi Karyawan',
            'subtitle' => 'Cara absen masuk, pulang, selfie wajah, dan izin di aplikasi HP',
            'icon' => 'ti ti-user-check',
            'badge' => 'Karyawan & Staf',
            'about' => 'Panduan singkat untuk membantu staf melakukan absen masuk, absen pulang, dan pengajuan izin dari handphone.',
            'steps' => [
                'Buka aplikasi di HP, lalu login dengan NIK dan kata sandi dari admin.',
                'Cek kartu Shift Hari Ini untuk melihat jam kerja dan lokasi cabang tugas.',
                'Tiba di lokasi cabang tugas, buka kamera presensi dan pastikan GPS HP aktif.',
                'Arahkan wajah tegak ke kamera di tempat terang, lalu tekan Absen Masuk.',
                'Saat jam kerja selesai, buka kembali kamera presensi lalu tekan Absen Pulang.',
            ],
            'components' => [
                'Bebas Absen' => 'Muncul otomatis saat hari libur (OFF). Staf tidak perlu melakukan presensi.',
                'Pulang Lebih Awal' => 'Wajib ketik alasan jika menekan tombol pulang sebelum jam shift berakhir.',
                'Radius GPS' => 'Tombol absen hanya aktif jika staf berada di dalam area cabang tugas.',
            ],
            'tips' => [
                'Untuk shift malam yang melewati tengah malam (misal 22:00 sampai 06:00), absen masuk malam hari dan absen pulang keesokan paginya.',
            ],
            'warnings' => 'Penggunaan aplikasi Fake GPS dilarang keras. Sistem otomatis mendeteksi dan menolak presensi yang dimanipulasi.',
        ],

        // 3. DASHBOARD MONITORING & STATISTIK
        'dashboard' => [
            'id' => 'dashboard',
            'pattern' => ['dashboard', 'dashboard/*'],
            'title' => 'Dashboard Utama Presensi',
            'subtitle' => 'Ringkasan kehadiran hari ini, keterlambatan, dan izin tertunda',
            'icon' => 'ti ti-layout-dashboard',
            'badge' => 'Dashboard',
            'about' => 'Pantau kondisi kehadiran staf hari ini secara langsung: hadir tepat waktu, terlambat, atau belum hadir.',
            'steps' => [
                'Pilih filter Cabang di atas jika ingin memantau outlet tertentu.',
                'Periksa kartu Perlu Tindakan untuk memproses izin atau cuti yang belum disetujui.',
                'Klik salah satu kartu angka (misal Terlambat) untuk melihat daftar nama staf bersangkutan.',
            ],
            'components' => [
                'Perlu Tindakan' => 'Total permohonan izin, sakit, dan cuti yang menunggu keputusan admin.',
                'Aktivitas Terkini' => 'Aliran foto selfie dan jam absen staf yang baru masuk atau pulang.',
            ],
            'tips' => [
                'Cek kartu Perlu Tindakan setiap pagi agar pengajuan staf sudah beres sebelum jam operasional dimulai.',
            ],
            'warnings' => null,
        ],

        // 4. MONITORING PRESENSI HARIAN
        'presensi' => [
            'id' => 'presensi',
            'pattern' => ['presensi', 'presensi/index'],
            'title' => 'Monitoring Presensi Harian',
            'subtitle' => 'Pantau jam masuk, jam pulang, foto selfie, dan koreksi absen manual',
            'icon' => 'ti ti-fingerprint',
            'badge' => 'Kehadiran',
            'about' => 'Periksa catatan presensi seluruh staf per hari, verifikasi foto selfie di lokasi, dan perbaiki jam hadir jika staf terkendala.',
            'steps' => [
                'Pilih Tanggal dan Cabang yang ingin dicek, lalu klik Cari.',
                'Klik nama staf atau foto untuk melihat foto selfie dan titik koordinat di peta.',
                'Jika staf lupa absen atau HP mati, klik ikon pensil hijau (Koreksi).',
                'Masukkan jam yang benar, ketik alasan perubahan, lalu klik Simpan.',
            ],
            'components' => [
                'Status Hadir vs Terlambat' => 'Dihitung otomatis berdasarkan toleransi jam masuk pada master shift.',
                'Koreksi Manual' => 'Fitur admin untuk memperbaiki jam hadir jika staf terkendala teknis.',
            ],
            'tips' => [
                'Gunakan fitur Auto-Alpha di akhir shift untuk menandai staf yang tidak hadir tanpa keterangan.',
            ],
            'warnings' => 'Setiap koreksi absen oleh admin dicatat dalam riwayat audit (nama admin, waktu edit, dan alasan).',
        ],

        // 5. LIVE TRACKING GPS PRESENSI
        'trackingpresensi' => [
            'id' => 'trackingpresensi',
            'pattern' => ['trackingpresensi', 'trackingpresensi/*'],
            'title' => 'Peta Lokasi Presensi GPS',
            'subtitle' => 'Sebaran titik lokasi presensi staf di atas peta digital',
            'icon' => 'ti ti-map-pin',
            'badge' => 'Pelacakan GPS',
            'about' => 'Periksa posisi koordinat fisik staf saat menekan tombol absen masuk dan pulang di atas peta.',
            'steps' => [
                'Pilih tanggal dan cabang di bagian atas peta.',
                'Klik tombol Tampilkan pada Peta.',
                'Klik pin staf untuk melihat nama, jam absen, dan foto selfie di lokasi.',
            ],
            'components' => [
                'Lingkaran Radius' => 'Batas jarak toleransi resmi dari titik pusat kantor atau outlet.',
                'Pin Lokasi Staf' => 'Titik koordinat handphone staf saat tombol absen ditekan.',
            ],
            'tips' => [
                'Jika pin staf berada di luar lingkaran radius kantor, pastikan apakah staf memiliki status bebas lokasi atau dispensasi.',
            ],
            'warnings' => null,
        ],

        // 6. MASTER SHIFT & JAM KERJA
        'jamkerja' => [
            'id' => 'jamkerja',
            'pattern' => ['jamkerja', 'jamkerja/*'],
            'title' => 'Shift & Jam Kerja',
            'subtitle' => 'Atur jam masuk, jam pulang, toleransi telat, dan shift malam',
            'icon' => 'ti ti-clock',
            'badge' => 'Jam Kerja',
            'about' => 'Kelola pola jam kerja (shift) perusahaan, batas toleransi telat, dan opsi shift malam lintas hari.',
            'steps' => [
                'Klik tombol Tambah Shift Kerja.',
                'Beri nama shift (contoh: Shift Pagi, Shift Siang), jam masuk, dan jam pulang.',
                'Tentukan batas toleransi keterlambatan dalam menit (misal 15 menit).',
                'Jika shift melewati tengah malam (misal 22:00 ke 06:00), centang Shift Malam Lintas Hari.',
                'Klik Simpan.',
            ],
            'components' => [
                'Toleransi Telat' => 'Batas menit keterlambatan yang masih dihitung hadir normal.',
                'Shift Lintas Hari' => 'Memastikan absen pulang keesokan pagi tetap tercatat pada hari kerja yang sama.',
            ],
            'tips' => [
                'Hindari mengubah jam shift yang sedang berjalan pada hari yang sama agar catatan hadir staf tidak terganggu.',
            ],
            'warnings' => 'Perubahan jam shift aktif dapat mengubah hitungan menit terlambat pada staf yang sudah absen hari ini.',
        ],

        // 7. HARI LIBUR & TANGGAL MERAH
        'harilibur' => [
            'id' => 'harilibur',
            'pattern' => ['harilibur', 'harilibur/*'],
            'title' => 'Hari Libur & Tanggal Merah',
            'subtitle' => 'Penetapan tanggal merah nasional dan libur khusus operasional',
            'icon' => 'ti ti-calendar-off',
            'badge' => 'Hari Libur',
            'about' => 'Tetapkan tanggal libur resmi di luar jadwal mingguan agar staf tidak tercatat alpa saat kantor atau outlet libur.',
            'steps' => [
                'Klik tombol Tambah Hari Libur.',
                'Pilih tanggal libur dan ketik nama libur (contoh: Idul Fitri, Tahun Baru).',
                'Pilih berlaku untuk Semua Cabang atau hanya cabang tertentu.',
                'Klik Simpan.',
            ],
            'components' => [
                'Semua Cabang' => 'Seluruh staf di semua cabang otomatis dibebaskan dari kewajiban absen pada tanggal tersebut.',
                'Cabang Tertentu' => 'Hanya berlaku untuk gerai tertentu, misalnya cabang yang sedang direnovasi.',
            ],
            'tips' => [
                'Input tanggal merah nasional di awal tahun agar rekapan alpa staf akurat sejak awal.',
            ],
            'warnings' => null,
        ],

        // 8. DATA KARYAWAN & PENUGASAN
        'karyawan' => [
            'id' => 'karyawan',
            'pattern' => ['karyawan'],
            'exclude_pattern' => ['karyawan/*/show', 'karyawan/*/setjamkerja'],
            'title' => 'Data Karyawan & Penugasan',
            'subtitle' => 'Pendaftaran staf baru, akun login HP, cabang induk, dan kunci GPS',
            'icon' => 'ti ti-users',
            'badge' => 'Data Karyawan',
            'about' => 'Pusat data staf: pendaftaran akun, cabang asal, hak akses aplikasi mobile, dan kunci lokasi GPS.',
            'steps' => [
                'Klik tombol Tambah Karyawan di kanan atas.',
                'Isi NIK, nama lengkap, nomor HP, email, cabang asal, divisi, dan jabatan.',
                'Pilih Kunci Lokasi: Ya (wajib di radius kantor) atau Tidak (bebas lokasi untuk tim luar/sales).',
                'Klik Simpan, lalu buka Detail Profil untuk membuat akun login dan mendaftarkan wajah.',
            ],
            'components' => [
                'Gembok GPS Hijau' => 'Wajib absen di dalam radius kantor atau outlet tugas.',
                'Gembok GPS Merah' => 'Bebas lokasi, staf bisa absen di mana saja.',
                'Ikon Kalender' => 'Pintu masuk ke pengaturan jadwal mingguan dan jadwal per tanggal staf.',
            ],
            'tips' => [
                'Setelah input staf baru, segera daftarkan foto wajahnya di profil agar staf bisa langsung presensi.',
            ],
            'warnings' => 'Menonaktifkan status staf akan langsung memblokir akses login mereka ke aplikasi mobile.',
        ],

        // 9. DETAIL PROFIL & BIOMETRIK WAJAH
        'karyawan_detail' => [
            'id' => 'karyawan_detail',
            'pattern' => ['karyawan/*/show', 'facerecognition*'],
            'title' => 'Profil & Biometrik Wajah Staf',
            'subtitle' => 'Daftarkan foto wajah selfie dan reset kata sandi login HP',
            'icon' => 'ti ti-id',
            'badge' => 'Profil Staf',
            'about' => 'Daftarkan foto wajah referensi staf untuk verifikasi kamera selfie saat absen dan reset kata sandi aplikasi.',
            'steps' => [
                'Buka profil staf, lalu klik tab Biometrik Wajah.',
                'Klik tombol Tambah Foto Wajah.',
                'Ambil foto atau unggah foto wajah tegak, pencahayaan terang, tanpa masker atau kacamata hitam.',
                'Klik Simpan. Sistem otomatis memproses data wajah.',
            ],
            'components' => [
                'Foto Referensi' => 'Foto patokan sah saat sistem mencocokkan wajah staf di handphone.',
                'Reset Password' => 'Membuat kata sandi baru jika staf lupa password aplikasi mobile.',
            ],
            'tips' => [
                'Daftarkan foto dengan pencahayaan terang dan latar polos agar pemindaian saat absen berjalan cepat dan akurat.',
            ],
            'warnings' => 'Foto buram, gelap, atau memakai topi/kacamata hitam dapat membuat verifikasi absen staf gagal.',
        ],

        // 10. CABANG & LOKASI KANTOR
        'cabang' => [
            'id' => 'cabang',
            'pattern' => ['cabang', 'cabang/*'],
            'title' => 'Cabang & Lokasi Kantor',
            'subtitle' => 'Atur nama outlet, titik koordinat peta GPS, dan batas radius meter',
            'icon' => 'ti ti-building-store',
            'badge' => 'Master Cabang',
            'about' => 'Kelola daftar kantor dan outlet tempat staf bertugas, lengkap dengan titik GPS dan batas radius absen.',
            'steps' => [
                'Klik Tambah Cabang.',
                'Isi kode cabang, nama cabang, dan alamat lengkap.',
                'Tentukan titik koordinat dengan klik pada peta atau salin dari Google Maps.',
                'Masukkan Radius (contoh: 50 meter). Staf wajib berada dalam jarak ini saat absen.',
                'Klik Simpan.',
            ],
            'components' => [
                'Koordinat GPS' => 'Titik tengah kantor sebagai patokan perhitungan jarak.',
                'Radius Meter' => 'Jarak maksimal dari titik tengah kantor yang diizinkan untuk absen.',
            ],
            'tips' => [
                'Untuk gedung kantor atau ruko standar, radius 50 sampai 75 meter merupakan ukuran paling stabil di lapangan.',
            ],
            'warnings' => null,
        ],

        // 11. DEPARTEMEN & DIVISI
        'departemen' => [
            'id' => 'departemen',
            'pattern' => ['departemen', 'departemen/*'],
            'title' => 'Departemen & Divisi',
            'subtitle' => 'Pengelompokan bagian kerja staf perusahaan',
            'icon' => 'ti ti-category',
            'badge' => 'Struktur Divisi',
            'about' => 'Kelola nama departemen atau bagian kerja perusahaan untuk kemudahan rekapitulasi dan pembagian shift.',
            'steps' => [
                'Klik Tambah Departemen.',
                'Ketik nama divisi (contoh: Operasional, Pemasaran, Keuangan, HR), lalu klik Simpan.',
            ],
            'components' => [
                'Filter Divisi' => 'Memudahkan penyaringan saat menarik laporan kehadiran dan pembagian jadwal.',
            ],
            'tips' => [
                'Gunakan penamaan divisi yang konsisten di semua cabang agar rekap bulanan rapi.',
            ],
            'warnings' => null,
        ],

        // 12. JABATAN PEKERJAAN
        'jabatan' => [
            'id' => 'jabatan',
            'pattern' => ['jabatan', 'jabatan/*'],
            'title' => 'Jabatan Pekerjaan',
            'subtitle' => 'Pengelolaan posisi profesi dan jenjang kerja staf',
            'icon' => 'ti ti-briefcase',
            'badge' => 'Struktur Jabatan',
            'about' => 'Atur nama posisi kerja staf seperti Manager, Supervisor, Staff Administrasi, atau Operator.',
            'steps' => [
                'Klik Tambah Jabatan.',
                'Ketik nama jabatan, lalu klik Simpan.',
            ],
            'components' => [
                'Gelar Jabatan' => 'Tercantum pada profil karyawan, struktur tim, dan slip gaji.',
            ],
            'tips' => [
                'Samakan penamaan jabatan antar cabang agar struktur jenjang pekerjaan tetap seragam.',
            ],
            'warnings' => null,
        ],

        // 13. PERSETUJUAN IZIN ABSEN
        'izinabsen' => [
            'id' => 'izinabsen',
            'pattern' => ['izinabsen', 'izinabsen/*'],
            'title' => 'Persetujuan Izin Absen',
            'subtitle' => 'Verifikasi dan persetujuan permohonan izin tidak masuk kerja',
            'icon' => 'ti ti-file-text',
            'badge' => 'Persetujuan Izin',
            'about' => 'Tinjau permohonan izin tidak masuk kerja yang diajukan staf melalui aplikasi mobile.',
            'steps' => [
                'Buka daftar pengajuan, klik tombol aksi pada permohonan berstatus Pending.',
                'Periksa tanggal izin dan alasan yang ditulis oleh staf.',
                'Pilih Disetujui atau Ditolak, beri catatan singkat jika perlu, lalu klik Simpan.',
            ],
            'components' => [
                'Status Pending' => 'Pengajuan baru yang belum diputuskan oleh atasan.',
                'Disetujui' => 'Status kehadiran hari itu otomatis berubah menjadi Izin Sah (bukan Alpa).',
            ],
            'tips' => [
                'Putuskan pengajuan izin sebelum shift dimulai agar laporan harian langsung rapi.',
            ],
            'warnings' => 'Sistem otomatis menolak approval jika staf sudah memiliki jam hadir fisik pada tanggal tersebut.',
        ],

        // 14. PERSETUJUAN IZIN SAKIT
        'izinsakit' => [
            'id' => 'izinsakit',
            'pattern' => ['izinsakit', 'izinsakit/*'],
            'title' => 'Persetujuan Izin Sakit',
            'subtitle' => 'Pemeriksaan foto surat dokter dan persetujuan sakit staf',
            'icon' => 'ti ti-first-aid-kit',
            'badge' => 'Persetujuan Sakit',
            'about' => 'Periksa permohonan izin sakit staf lengkap dengan bukti foto surat keterangan dokter dari klinik atau rumah sakit.',
            'steps' => [
                'Klik tombol aksi pada pengajuan sakit berstatus Pending.',
                'Klik foto lampiran untuk memperbesar dan memastikan keaslian surat keterangan dokter.',
                'Jika valid, pilih Disetujui lalu klik Simpan.',
            ],
            'components' => [
                'Surat Izin Dokter (SID)' => 'Bukti resmi istirahat sakit dari dokter yang diunggah staf dari handphone.',
            ],
            'tips' => [
                'Cocokkan tanggal istirahat pada surat dokter dengan rentang tanggal yang diajukan staf.',
            ],
            'warnings' => 'Jika staf tidak melampirkan surat dokter yang sah, admin berhak menolak atau mengarahkan ke Izin Biasa/Cuti.',
        ],

        // 15. PERSETUJUAN CUTI KARYAWAN
        'izincuti' => [
            'id' => 'izincuti',
            'pattern' => ['izincuti', 'izincuti/*'],
            'title' => 'Persetujuan Cuti Karyawan',
            'subtitle' => 'Pemeriksaan sisa saldo cuti dan persetujuan libur tahunan',
            'icon' => 'ti ti-calendar-stats',
            'badge' => 'Persetujuan Cuti',
            'about' => 'Tinjau pengajuan cuti tahunan staf. Sistem otomatis menampilkan sisa saldo cuti staf agar tidak melebihi kuota.',
            'steps' => [
                'Buka daftar pengajuan cuti berstatus Pending.',
                'Periksa kolom sisa saldo cuti staf untuk memastikan kuota masih mencukupi.',
                'Jika operasional aman, klik Setujui. Saldo cuti staf otomatis berkurang.',
            ],
            'components' => [
                'Sisa Saldo Cuti' => 'Jatah hari cuti aktif staf yang tersisa pada tahun berjalan.',
                'Cetak Form Cuti' => 'Mencetak dokumen formulir cuti resmi untuk arsip fisik.',
            ],
            'tips' => [
                'Ingatkan staf mengajukan cuti beberapa hari sebelumnya agar jadwal pengganti shift bisa diatur.',
            ],
            'warnings' => 'Persetujuan cuti otomatis memotong kuota cuti staf. Pembatalan cuti harus diproses melalui admin.',
        ],

        // 16. MASTER JENIS & KUOTA CUTI
        'cuti' => [
            'id' => 'cuti',
            'pattern' => ['cuti', 'cuti/*'],
            'title' => 'Master Jenis Cuti & Kuota',
            'subtitle' => 'Pengaturan jenis cuti tahunan, cuti khusus, dan batas jatah hari',
            'icon' => 'ti ti-calendar',
            'badge' => 'Master Cuti',
            'about' => 'Atur kategori cuti perusahaan (contoh: Cuti Tahunan 12 hari, Menikah, Melahirkan) beserta batas hari kuotanya.',
            'steps' => [
                'Klik Tambah Cuti.',
                'Isi nama cuti dan jumlah kuota hari kerja.',
                'Klik Simpan.',
            ],
            'components' => [
                'Kuota Hari' => 'Batas maksimal hari libur yang boleh diambil untuk jenis cuti tersebut.',
            ],
            'tips' => [
                'Standar cuti tahunan di Indonesia umumnya adalah 12 hari kerja per tahun.',
            ],
            'warnings' => null,
        ],

        // 17. DISPENSASI KETERLAMBATAN
        'dispensasi' => [
            'id' => 'dispensasi',
            'pattern' => ['dispensasi', 'dispensasi/*'],
            'title' => 'Dispensasi Keterlambatan',
            'subtitle' => 'Toleransi jam masuk untuk staf dengan alasan darurat yang sah',
            'icon' => 'ti ti-clock-check',
            'badge' => 'Dispensasi',
            'about' => 'Berikan toleransi bagi staf yang terlambat karena alasan darurat sah (contoh: ban bocor, tugas luar mendadak).',
            'steps' => [
                'Klik Tambah Dispensasi.',
                'Pilih nama staf, tanggal kejadian, dan jam dispensasi.',
                'Ketik alasan lengkap, lalu klik Simpan.',
            ],
            'components' => [
                'Status Dispensasi' => 'Staf tetap dihitung hadir normal tanpa potongan denda keterlambatan.',
            ],
            'tips' => [
                'Berikan dispensasi hanya jika kejadian sudah dikonfirmasi ke supervisor langsung.',
            ],
            'warnings' => null,
        ],

        // 18. LEMBUR & SPK (OVERTIME)
        'lembur' => [
            'id' => 'lembur',
            'pattern' => ['lembur', 'kepegawaian/lembur*'],
            'title' => 'Lembur & Perintah Kerja Lembur',
            'subtitle' => 'Surat perintah lembur, persetujuan atasan, dan hitungan upah lembur',
            'icon' => 'ti ti-clock-play',
            'badge' => 'Lembur',
            'about' => 'Kelola surat perintah lembur (SPK), persetujuan atasan, dan kalkulasi upah lembur otomatis.',
            'steps' => [
                'Klik Tambah Lembur / SPK.',
                'Pilih nama staf, tanggal lembur, jam mulai, dan jam selesai.',
                'Tuliskan tugas pekerjaan yang dikerjakan saat lembur.',
                'Klik Simpan. Upah lembur otomatis masuk ke kalkulasi gaji jika disetujui.',
            ],
            'components' => [
                'SPK Lembur' => 'Surat perintah resmi sebagai dasar sah penghitungan upah lembur.',
                'Pengali Otomatis' => 'Sistem menghitung 1.5x untuk jam pertama dan 2x untuk jam berikutnya.',
            ],
            'tips' => [
                'Pastikan lembur disetujui sebelum periode gaji ditutup agar terhitung di slip gaji bulan berjalan.',
            ],
            'warnings' => 'Jam lembur tanpa persetujuan resmi tidak akan dimasukkan ke perhitungan upah lembur bulanan.',
        ],

        // 19. PAYROLL & PENGGAJIAN
        'payroll' => [
            'id' => 'payroll',
            'pattern' => ['payroll', 'keuangan/payroll*', 'keuangan/payslip*'],
            'title' => 'Penggajian & Slip Gaji (Payroll)',
            'subtitle' => 'Hitung gaji bulanan, potongan kehadiran, kasbon, dan cetak slip gaji',
            'icon' => 'ti ti-report-money',
            'badge' => 'Penggajian',
            'about' => 'Hitung otomatis gaji pokok, tunjangan, upah lembur, potongan telat atau alpha, dan cicilan kasbon staf.',
            'steps' => [
                'Buka menu Payroll, klik Tambah Periode Gaji.',
                'Tentukan rentang tanggal cut-off absensi, lalu klik Hitung Otomatis.',
                'Periksa rincian gaji per staf. Lakukan penyesuaian manual jika diperlukan.',
                'Klik Kunci Periode jika angka sudah final, lalu cetak slip gaji PDF.',
            ],
            'components' => [
                'Cut-Off Absensi' => 'Rentang tanggal kehadiran yang menjadi patokan hitungan gaji bulan berjalan.',
                'Kunci Periode' => 'Mengunci pembukuan gaji agar angka slip gaji tidak dapat diubah lagi.',
            ],
            'tips' => [
                'Pastikan seluruh permohonan izin, sakit, dan lembur sudah disetujui sebelum menekan Hitung Otomatis.',
            ],
            'warnings' => 'Setelah periode gaji dikunci, rincian pada slip gaji tidak dapat diubah lagi demi integritas pembukuan.',
        ],

        // 20. PINJAMAN & KASBON KARYAWAN
        'loan' => [
            'id' => 'loan',
            'pattern' => ['loan', 'keuangan/loan*'],
            'title' => 'Pinjaman & Kasbon Karyawan',
            'subtitle' => 'Catat kasbon staf, tenor cicilan, dan pemotongan otomatis dari gaji',
            'icon' => 'ti ti-cash',
            'badge' => 'Kasbon & Pinjaman',
            'about' => 'Kelola pinjaman dana staf. Cicilan bulanan otomatis memotong gaji staf sampai lunas.',
            'steps' => [
                'Klik Tambah Pinjaman / Kasbon.',
                'Pilih nama staf, isi total pinjaman, dan tentukan tenor (jumlah bulan cicilan).',
                'Klik Simpan. Sistem otomatis membagi rata cicilan per bulan pada slip gaji.',
            ],
            'components' => [
                'Tenor Cicilan' => 'Jumlah bulan pengembalian kasbon (misal 3 bulan atau 6 bulan).',
                'Sisa Saldo' => 'Jumlah hutang kasbon yang belum terbayar.',
            ],
            'tips' => [
                'Cek kemampuan potong gaji staf sebelum menyetujui nominal pinjaman yang besar.',
            ],
            'warnings' => 'Jika staf mengundurkan diri sebelum kasbon lunas, sisa pinjaman otomatis memotong hak akhir di menu offboarding.',
        ],

        // 21. REIMBURSEMENT & KLAIM BIAYA
        'reimbursement' => [
            'id' => 'reimbursement',
            'pattern' => ['reimbursement', 'keuangan/reimbursement*'],
            'title' => 'Klaim Reimbursement',
            'subtitle' => 'Verifikasi klaim biaya operasional staf dengan bukti nota pembayaran',
            'icon' => 'ti ti-receipt',
            'badge' => 'Reimbursement',
            'about' => 'Proses pengembalian biaya operasional yang ditalangi staf (contoh: bensin dinas, pulsa, konsumsi rapat).',
            'steps' => [
                'Buka daftar klaim berstatus Menunggu Verifikasi.',
                'Klik detail untuk melihat tanggal transaksi, nominal, dan foto bukti kuitansi.',
                'Jika sesuai, klik Setujui. Nominal klaim siap dibayarkan.',
            ],
            'components' => [
                'Foto Nota' => 'Bukti asli fisik belanja yang wajib diunggah staf dari handphone.',
            ],
            'tips' => [
                'Tolak pengajuan klaim jika foto nota buram, sobek, atau tanggal transaksi tidak sesuai.',
            ],
            'warnings' => null,
        ],

        // 22. DISIPLIN & SURAT PERINGATAN (SP)
        'warning' => [
            'id' => 'warning',
            'pattern' => ['warning', 'kepegawaian/warning*', 'governance/warning*'],
            'title' => 'Disiplin & Surat Peringatan (SP)',
            'subtitle' => 'Penerbitan SP 1, SP 2, SP 3 dengan masa berlaku 6 bulan',
            'icon' => 'ti ti-alert-triangle',
            'badge' => 'Disiplin & SP',
            'about' => 'Catat sanksi disiplin bagi staf yang melanggar peraturan kerja. SP berlaku selama 6 bulan.',
            'steps' => [
                'Klik Terbitkan Surat Peringatan.',
                'Pilih nama staf, tingkat SP (SP 1, SP 2, atau SP 3), dan tanggal surat.',
                'Ketik uraian pelanggaran dengan jelas.',
                'Klik Simpan dan cetak surat untuk ditandatangani staf dan atasan.',
            ],
            'components' => [
                'Masa Berlaku 6 Bulan' => 'Sistem otomatis menghitung masa kedaluwarsa sanksi selama 6 bulan.',
            ],
            'tips' => [
                'Lakukan pembinaan atau teguran lisan terlebih dahulu sebelum menerbitkan surat peringatan tertulis.',
            ],
            'warnings' => 'Penerbitan SP sebaiknya berjenjang (SP 1 lalu SP 2 lalu SP 3) kecuali untuk pelanggaran berat.',
        ],

        // 23. REKRUTMEN KARYAWAN
        'recruitment' => [
            'id' => 'recruitment',
            'pattern' => ['recruitment', 'kepegawaian/recruitment*'],
            'title' => 'Rekrutmen Karyawan',
            'subtitle' => 'Kelola lowongan kerja, berkas pelamar, dan konversi ke data karyawan',
            'icon' => 'ti ti-user-plus',
            'badge' => 'Rekrutmen',
            'about' => 'Kelola penerimaan staf baru: buka lowongan posisi kerja, seleksi berkas pelamar, hingga pengangkatan kerja.',
            'steps' => [
                'Buka menu Rekrutmen, klik Buat Lowongan untuk posisi yang dibutuhkan.',
                'Tinjau data dan berkas CV pelamar yang masuk.',
                'Perbarui status pelamar (Lamaran Masuk, Interview, Diterima, Ditolak).',
                'Jika diterima, klik Jadikan Karyawan untuk memindahkan data ke Data Karyawan secara instan.',
            ],
            'components' => [
                'Tombol Jadikan Karyawan' => 'Otomatis membuat profil staf baru tanpa perlu ketik ulang data.',
            ],
            'tips' => [
                'Tuliskan catatan hasil wawancara di kolom komentar agar tim penilai lain dapat membacanya.',
            ],
            'warnings' => null,
        ],

        // 24. ONBOARDING STAF BARU
        'onboarding' => [
            'id' => 'onboarding',
            'pattern' => ['onboarding', 'kepegawaian/onboarding*'],
            'title' => 'Onboarding Staf Baru',
            'subtitle' => 'Checklist berkas dan kesiapan tugas untuk staf baru bergabung',
            'icon' => 'ti ti-checklist',
            'badge' => 'Onboarding',
            'about' => 'Pastikan kebutuhan staf baru tuntas di minggu pertama: berkas KTP, seragam, akun login, dan SOP kerja.',
            'steps' => [
                'Pilih staf baru, terapkan checklist yang sesuai posisi kerjanya.',
                'Centang setiap tugas yang sudah selesai (contoh: Akun dibuat, foto wajah didaftarkan, seragam diserahkan).',
            ],
            'components' => [
                'Checklist Tugas' => 'Daftar hal wajib yang harus diselesaikan di hari-hari pertama staf bekerja.',
            ],
            'tips' => [
                'Selesaikan pendaftaran foto wajah di hari pertama agar staf bisa langsung presensi mandiri keesokan harinya.',
            ],
            'warnings' => null,
        ],

        // 25. PENILAIAN KINERJA (PERFORMANCE / KPI)
        'performance' => [
            'id' => 'performance',
            'pattern' => ['performance', 'kepegawaian/performance*'],
            'title' => 'Penilaian Kinerja (KPI)',
            'subtitle' => 'Evaluasi kedisiplinan dan pencapaian target staf secara berkala',
            'icon' => 'ti ti-award',
            'badge' => 'Kinerja & KPI',
            'about' => 'Beri penilaian kedisiplinan, sikap kerja, dan target staf sebagai dasar pertimbangan bonus atau promosi.',
            'steps' => [
                'Klik Buat Penilaian Kinerja, pilih nama staf dan periode evaluasi.',
                'Beri nilai pada kriteria yang diuji (Kedisiplinan, Tanggung Jawab, Kerjasama).',
                'Tuliskan masukan untuk staf, lalu klik Simpan.',
            ],
            'components' => [
                'Skor Akhir' => 'Akumulasi total nilai performa staf yang dihitung secara proporsional.',
            ],
            'tips' => [
                'Gunakan data rekap absensi dan keterlambatan dari sistem sebagai rujukan penilaian yang objektif.',
            ],
            'warnings' => null,
        ],

        // 26. KARYAWAN KELUAR & PESANGON (OFFBOARDING)
        'offboarding' => [
            'id' => 'offboarding',
            'pattern' => ['offboarding', 'kepegawaian/offboarding*'],
            'title' => 'Karyawan Keluar & Pesangon',
            'subtitle' => 'Checklist pengembalian aset, serah terima tugas, dan pelunasan kasbon',
            'icon' => 'ti ti-user-x',
            'badge' => 'Offboarding',
            'about' => 'Kelola pengunduran diri staf: pengembalian aset kantor, pemotongan sisa kasbon, dan penonaktifan akun.',
            'steps' => [
                'Klik Tambah Karyawan Keluar, pilih nama staf dan tanggal efektif keluar.',
                'Periksa sisa kasbon dan sisa cuti staf pada kartu ringkasan.',
                'Selesaikan checklist serah terima aset (kunci, seragam, inventaris).',
                'Setelah tuntas, nonaktifkan status staf pada Data Karyawan.',
            ],
            'components' => [
                'Exit Clearance' => 'Lembar persetujuan serah terima tugas dan inventaris kantor.',
                'Potongan Kasbon' => 'Sisa kasbon yang otomatis memotong hak akhir staf.',
            ],
            'tips' => [
                'Pastikan seluruh barang kantor sudah dikembalikan sebelum surat keterangan kerja diserahkan.',
            ],
            'warnings' => 'Segera nonaktifkan status login staf pada tanggal efektif keluar untuk mencegah akses data dari luar.',
        ],

        // 27. LAPORAN REKAPITULASI PRESENSI
        'laporan_presensi' => [
            'id' => 'laporan_presensi',
            'pattern' => ['laporan/presensi', 'laporan_presensi'],
            'title' => 'Laporan Rekapitulasi Presensi',
            'subtitle' => 'Cetak rekapitulasi kehadiran bulanan multi-cabang ke PDF atau Excel',
            'icon' => 'ti ti-printer',
            'badge' => 'Laporan Presensi',
            'about' => 'Tarik laporan rekap bulanan kehadiran, keterlambatan, izin, sakit, dan cuti seluruh staf siap cetak atau olah di Excel.',
            'steps' => [
                'Pilih Bulan dan Tahun yang ingin ditarik.',
                'Pilih Cabang (Semua Cabang atau cabang spesifik).',
                'Klik tombol Cetak untuk format cetak PDF, atau Export Excel untuk mengunduh spreadsheet.',
            ],
            'components' => [
                'Laporan Multi-Cabang' => 'Laporan disusun berdasarkan cabang penugasan aktual staf pada hari kerja tersebut.',
            ],
            'tips' => [
                'Gunakan format Excel jika data ingin diolah lebih lanjut untuk keperluan pembukuan internal.',
            ],
            'warnings' => null,
        ],

        // 28. LAPORAN REKAPITULASI CUTI
        'laporan_cuti' => [
            'id' => 'laporan_cuti',
            'pattern' => ['laporan/cuti', 'laporan_cuti'],
            'title' => 'Laporan Rekapitulasi Cuti',
            'subtitle' => 'Rekap pemakaian cuti staf dan sisa jatah cuti tahun berjalan',
            'icon' => 'ti ti-file-analytics',
            'badge' => 'Laporan Cuti',
            'about' => 'Pantau penggunaan cuti seluruh staf dan sisa jatah cuti tahunan yang masih aktif hingga akhir tahun.',
            'steps' => [
                'Pilih Tahun berjalan dan Cabang.',
                'Klik Tampilkan atau Cetak.',
            ],
            'components' => [
                'Terpakai & Sisa' => 'Rincian hari cuti yang sudah disetujui vs sisa saldo aktif staf.',
            ],
            'tips' => [
                'Periksa laporan ini menjelang akhir tahun untuk mengingatkan staf yang masih memiliki sisa cuti.',
            ],
            'warnings' => null,
        ],

        // 29. PENGATURAN UMUM SISTEM
        'generalsetting' => [
            'id' => 'generalsetting',
            'pattern' => ['generalsetting', 'generalsetting/*'],
            'title' => 'Pengaturan Umum Sistem',
            'subtitle' => 'Konfigurasi nama perusahaan, logo kantor, dan radius default GPS',
            'icon' => 'ti ti-settings',
            'badge' => 'Pengaturan',
            'about' => 'Atur nama perusahaan pada aplikasi, logo navbar, dan batas radius standar presensi cabang baru.',
            'steps' => [
                'Buka menu Pengaturan Umum.',
                'Perbarui nama perusahaan, unggah logo, dan tentukan radius standar presensi.',
                'Klik Simpan Perubahan.',
            ],
            'components' => [
                'Logo Perusahaan' => 'Tampil pada navbar aplikasi, halaman login, dan kop cetak laporan.',
                'Radius Standar' => 'Jarak patokan dalam meter saat membuat cabang baru.',
            ],
            'tips' => [
                'Gunakan file logo berlatar transparan (PNG) agar tampilan di navbar rapi.',
            ],
            'warnings' => null,
        ],

        // 30. MANAJEMEN AKUN ADMIN
        'users' => [
            'id' => 'users',
            'pattern' => ['users', 'users/*'],
            'title' => 'Manajemen Akun Admin',
            'subtitle' => 'Pengelolaan akun login administrator, supervisor, dan hak akses menu',
            'icon' => 'ti ti-user-cog',
            'badge' => 'Kelola Pengguna',
            'about' => 'Kelola akun siapa saja yang boleh login ke dashboard admin dan tentukan wewenang peran (Role) masing-masing.',
            'steps' => [
                'Klik Tambah Pengguna Admin.',
                'Isi nama, email, username, dan password.',
                'Pilih Role (Super Admin, Admin, atau Supervisor) yang sesuai tanggung jawab kerjanya.',
                'Klik Simpan.',
            ],
            'components' => [
                'Role Akun' => 'Membatasi menu apa saja yang boleh dibuka oleh akun bersangkutan.',
            ],
            'tips' => [
                'Berikan role Supervisor untuk kepala cabang agar mereka hanya mengelola jadwal dan absen di cabangnya sendiri.',
            ],
            'warnings' => 'Jangan membagikan kata sandi akun Super Admin kepada sembarang staf.',
        ],

        // 31. PENCADANGAN DATABASE (BACKUP)
        'backup' => [
            'id' => 'backup',
            'pattern' => ['backup', 'backup/*'],
            'title' => 'Pencadangan Database (Backup)',
            'subtitle' => 'Unduh cadangan data sistem untuk keamanan arsip perusahaan',
            'icon' => 'ti ti-database',
            'badge' => 'Keamanan Data',
            'about' => 'Unduh salinan data seluruh sistem presensi secara berkala agar data tetap aman jika terjadi kendala pada server.',
            'steps' => [
                'Buka menu Backup, klik tombol Buat Backup Baru.',
                'Setelah proses selesai, klik ikon Unduh untuk menyimpan file cadangan di tempat yang aman.',
            ],
            'components' => [
                'File Backup' => 'Arsip lengkap data karyawan, riwayat presensi, dan pengaturan sistem.',
            ],
            'tips' => [
                'Lakukan backup minimal sebulan sekali setelah periode penggajian selesai ditutup.',
            ],
            'warnings' => 'Simpan file cadangan di tempat yang aman. File backup berisi data rahasia seluruh karyawan.',
        ],
    ],
];
