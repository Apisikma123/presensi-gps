<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Panduan Bantuan Sistem Presensi & Operasional (Contextual Help System)
    |--------------------------------------------------------------------------
    |
    | Berisi materi panduan operasional resmi Bahasa Indonesia yang 100% selaras
    | dengan implementasi sistem terbaru:
    | - Jadwal Prioritas (By Date -> By Day / Weekly Roster -> Hari Libur -> Standar)
    | - GPS Otomatis Mengikuti Cabang Tugas (Terkunci, bukan pilih bebas)
    | - Verifikasi Biometrik Wajah (Face Recognition AI)
    | - Pulang Lebih Awal (Early Out) dengan Wajib Alasan
    | - Shift Malam (Lintas Hari / Overnight)
    | - Izin Susulan Pengganti Alpha & Larangan Menimpa Presensi Hadir Aktual
    | - Koreksi Presensi Manual Wajib Alasan dengan Catatan Jejak Audit
    | - Sistem Auto-Alpha Berdasarkan Shift Selesai
    | - Multi-Branch Reporting (Laporan Berdasarkan Cabang Penugasan Aktual)
    | - Foto Presensi Lama Berstatus "Telah Diarsipkan" (Cold Storage)
    |
    */

    'pages' => [
        // =====================================================================
        // 1. PANDUAN UTAMA KARYAWAN (CAFE & MOBILE OPERATIONAL FLOW)
        // =====================================================================
        'panduan_karyawan' => [
            'id' => 'panduan_karyawan',
            'pattern' => ['panduan-karyawan'],
            'title' => 'Panduan Presensi & Operasional Karyawan',
            'subtitle' => 'Petunjuk lengkap presensi masuk/pulang, shift tugas, izin, dan kendala operasional staf',
            'icon' => 'ti ti-user-check',
            'badge' => 'Panduan Karyawan',
            'about' => 'Panduan ini merangkum seluruh langkah operasional yang perlu diketahui oleh staf kafe/karyawan saat menggunakan aplikasi presensi mobile. Mulai dari melihat jadwal shift dan cabang penugasan harian di dashboard, melakukan presensi masuk/pulang dengan deteksi wajah dan radius GPS cabang tugas, aturan pulang lebih awal, shift malam lintas hari, pengajuan izin/sakit/cuti, hingga izin susulan setelah alpha.',
            'actions' => [
                'Masuk (Login) ke aplikasi menggunakan NIK dan kata sandi yang telah dibuatkan oleh Admin.',
                'Memeriksa Shift Kerja dan Cabang Tugas hari ini langsung pada kartu utama di Dashboard.',
                'Melihat status Hari OFF / Libur Rutin yang membebaskan kewajiban presensi.',
                'Melakukan Presensi Masuk (Clock-In) di lokasi cabang tugas saat jam shift dimulai.',
                'Mengetahui bahwa titik GPS otomatis terkunci ke cabang tugas hari ini (bukan pilih outlet bebas).',
                'Melakukan verifikasi wajah otomatis di depan kamera tanpa masker atau kacamata hitam.',
                'Mengetahui status kedatangan (Hadir Tepat Waktu, Terlambat, atau Hadir Dispensasi).',
                'Melakukan Presensi Pulang (Clock-Out) setelah jam kerja shift selesai.',
                'Mengisi alasan tertulis jika terpaksa Pulang Lebih Awal (Early Out).',
                'Menjalankan Presensi Masuk dan Pulang untuk Shift Malam (Lintas Hari / Overnight).',
                'Memeriksa riwayat jam absen dan foto bukti kehadiran di menu Histori Presensi.',
                'Mengajukan Izin Absen, Izin Sakit (wajib foto Surat Dokter), atau Cuti Tahunan sesuai kuota bulanan.',
                'Mengajukan Izin Susulan jika sebelumnya sempat tercatat Tidak Hadir / Alpha.',
                'Memahami status "Foto telah diarsipkan" pada rekaman presensi bulan-bulan lampau.',
            ],
            'steps' => [
                'Buka dashboard aplikasi setelah login, lalu periksa kartu "Shift Hari Ini" untuk memastikan jam kerja dan cabang penugasan Anda hari ini.',
                'Jika hari ini dijadwalkan Libur (OFF), kartu akan bertuliskan "Hari Libur / OFF" dan status "Bebas Absen". Anda tidak perlu dan tidak dapat melakukan presensi.',
                'Saat tiba di outlet cabang tugas, buka menu kamera presensi (tombol sidik jari). Pastikan GPS handphone aktif dan Anda berada di area kafe.',
                'Sistem secara otomatis mengunci lokasi ke cabang tugas hari ini. Posisikan wajah Anda tegak menghadap kamera di tempat yang cukup terang untuk verifikasi biometrik wajah.',
                'Tekan tombol "Presensi Masuk". Sistem akan memvalidasi radius GPS dan mencocokkan wajah Anda dalam hitungan detik.',
                'Jika hadir setelah batas toleransi shift, sistem akan mencatat menit keterlambatan. Jika memiliki izin dispensasi dari atasan, status Anda otomatis disesuaikan.',
                'Saat shift berakhir, buka kembali kamera presensi untuk menekan tombol "Presensi Pulang".',
                'Jika pulang mendahului jam shift (pulang cepat), sistem akan memunculkan kolom alasan. Tuliskan alasan kepulangan awal Anda secara jelas sebelum menyimpan.',
                'Untuk shift malam (misal 22:00 s.d. 06:00), lakukan absen masuk pada malam hari dan absen pulang keesokan paginya sebelum batas waktu lintas hari (06:00 WIB).',
                'Jika berhalangan hadir karena sakit atau keperluan mendesak, ajukan permohonan melalui menu "Ajuan Izin" agar tidak tercatat alpa.',
            ],
            'components' => [
                'Kartu Shift & Cabang Tugas' => 'Menampilkan nama shift (Pagi/Siang/Malam), rentang jam kerja, dan nama outlet cabang tempat Anda dijadwalkan bertugas hari ini.',
                'Status Hari Libur / OFF' => 'Indikator hijau yang menandakan hari ini adalah hari libur rutin atau tanggal merah. Sistem mengunci presensi agar tidak terjadi salah absen di hari libur.',
                'Kamera & Verifikasi Wajah AI' => 'Fitur pemindaian biometrik wajah otomatis. Memastikan presensi dilakukan oleh karyawan yang bersangkutan secara langsung (anti-titip absen).',
                'Radius GPS Terkunci' => 'Sistem otomatis mengarahkan jarak presensi ke titik cabang penugasan resmi hari ini. Karyawan tidak dapat mengubah lokasi cabang semaunya.',
                'Form Pulang Lebih Awal (Early Out)' => 'Jendela isian alasan wajib yang muncul otomatis jika Anda menekan tombol pulang sebelum jam shift resmi berakhir.',
                'Histori & Arsip Foto' => 'Daftar riwayat kehadiran harian. Foto absensi yang sudah berusia lebih dari satu bulan akan dipindahkan ke arsip dingin dengan label "Foto telah diarsipkan".',
                'Ajuan Izin Susulan' => 'Pengajuan izin resmi yang diajukan setelah status Alpha tercatat, yang jika disetujui atasan akan mengubah catatan Alpha menjadi Izin sah.',
            ],
            'tips' => [
                'Pastikan izin lokasi (GPS) pada peramban/handphone selalu disetel ke "Izinkan Saat Menggunakan Aplikasi" dan mode akurasi tinggi aktif.',
                'Hindari membelakangi lampu/cahaya silau saat selfie agar kamera dapat memindai kontur wajah dengan sempurna.',
                'Jika Anda adalah staf Cabang A namun hari ini dijadwalkan bertugas ke Cabang B, lakukan presensi langsung di Cabang B.',
                'Selalu periksa sisa kuota cuti bulanan Anda pada formulir cuti sebelum mengajukan tanggal libur.',
            ],
            'warnings' => 'Penggunaan aplikasi pemalsu lokasi (Fake GPS / Mock Location) dan titip absen dilarang keras oleh sistem. Percobaan presensi menggunakan Fake GPS akan langsung ditolak dan dilaporkan ke manajemen.',
        ],

        // =====================================================================
        // 2. PANDUAN UTAMA ADMIN & SUPERVISOR (ROSTER & OPERATIONAL HIERARCHY)
        // =====================================================================
        'panduan_admin_roster' => [
            'id' => 'panduan_admin_roster',
            'pattern' => ['panduan-admin', 'karyawan/*/setjamkerja'],
            'title' => 'Panduan Roster, Jadwal Prioritas & Approval Admin',
            'subtitle' => 'Tata cara pengaturan jadwal kerja, rotasi cabang, koreksi presensi, dan persetujuan izin',
            'icon' => 'ti ti-calendar-event',
            'badge' => 'Operasional Supervisor & Admin',
            'about' => 'Halaman ini merupakan panduan utama bagi Supervisor dan Admin Operasional Kafe dalam mengelola rotasi staf, jadwal kerja mingguan, penugasan pindah cabang sementara, serta tata kelola persetujuan izin dan koreksi data kehadiran yang akurat.',
            'actions' => [
                'Memahami perbedaan antara Home Branch (Cabang Induk) dan Cabang Tugas (Penugasan Harian).',
                'Menerapkan Aturan Prioritas Jadwal (By Date -> By Day/Weekly Roster -> Hari Libur -> Standar).',
                'Menyusun Roster Mingguan (By Day) untuk staf kafe selama 7 hari (Senin s.d. Minggu).',
                'Mengatur Jadwal Khusus Tanggal (By Date) untuk kebutuhan lembur, tukar shift, atau pindah cabang sementara.',
                'Menetapkan hari OFF rutin karyawan tanpa terikat bahwa hari Minggu harus libur.',
                'Meninjau dan menyetujui pengajuan Izin Absen, Izin Sakit (cek SID), dan Cuti Tahunan.',
                'Mengetahui bahwa sistem menolak approval izin jika karyawan sudah tercatat hadir fisik.',
                'Menyetujui Izin Susulan untuk mengganti catatan status Tanpa Keterangan (Alpha) yang sah.',
                'Melakukan Koreksi Presensi Manual dengan wajib menyertakan alasan perubahan (tercatat audit trail).',
                'Menjalankan fungsi Auto-Alpha untuk menandai staf yang tidak hadir setelah shift berakhir.',
                'Menarik Laporan Rekapitulasi Presensi berbasis Cabang Penugasan Aktual (Multi-Branch Reporting).',
                'Memantau data kepulangan lebih awal (Early Out) dan catatan alasan yang dimasukkan karyawan.',
            ],
            'steps' => [
                'Buka menu Data Karyawan, lalu klik tombol "Set Jam Kerja / Roster" pada baris karyawan yang ingin diatur jadwalnya.',
                'Pada bagian "Jadwal Mingguan (By Day)", pilih shift kerja (Pagi/Siang/Malam) dan cabang tugas untuk setiap hari (Senin s.d. Minggu). Hari yang dikosongkan atau diset OFF otomatis menjadi hari libur rutin karyawan.',
                'Jika ada kebutuhan mendesak seperti karyawan Cabang A diperbantukan ke Cabang B pada tanggal tertentu, buka bagian "Jadwal Khusus Tanggal (By Date)", pilih tanggal, pilih shift, dan pilih Cabang B.',
                'Ingat prinsip hierarki: Jadwal Khusus Tanggal (By Date) akan selalu mengalahkan Roster Mingguan (By Day) pada tanggal tersebut.',
                'Pantau kehadiran harian pada menu Monitoring Presensi. Karyawan yang absen masuk/pulang akan otomatis tercatat di cabang penugasan hari itu.',
                'Jika ada pengajuan izin masuk, periksa di menu Persetujuan Izin. Jika karyawan sudah memiliki jam hadir fisik pada tanggal tersebut, approval otomatis dicegah agar data hadir tidak tertimpa.',
                'Jika ada kendala teknis (misal handphone staf mati saat jam pulang), Admin dapat melakukan Koreksi Presensi dengan mengklik ikon pensil hijau dan wajib mengisi alasan koreksi.',
                'Jalankan fungsi Auto-Alpha di akhir shift atau gunakan penjadwalan otomatis. Sistem hanya meng-alpha karyawan aktif yang terjadwal kerja dan belum hadir setelah shift + 15 menit.',
                'Saat mencetak laporan bulanan di menu Laporan Presensi, filter cabang akan menampilkan seluruh staf yang bertugas di cabang tersebut (termasuk staf perbantuan).',
            ],
            'components' => [
                'Prioritas 1: Jadwal Khusus (By Date)' => 'Override tanggal kalender spesifik. Menentukan shift, cabang penugasan harian, atau libur perorangan. Mengalahkan semua jadwal lainnya.',
                'Prioritas 2: Roster Mingguan (By Day)' => 'Pola jadwal rutin 7 hari (Senin s.d. Minggu). Menentukan shift dan cabang tugas harian staf kafe. Hari Minggu dapat diset bekerja normal.',
                'Prioritas 3: Hari Libur Resmi' => 'Daftar tanggal merah nasional atau libur bersama perusahaan yang membebaskan kewajiban kerja.',
                'Prioritas 4: Shift Default Perusahaan' => 'Fallback jam kerja standar jika karyawan tidak memiliki pengaturan roster mingguan.',
                'Audit Trail Koreksi Manual' => 'Pencatatan otomatis siapa admin yang mengoreksi data presensi (nama, tanggal, jam) dan alasan resmi yang dituliskan.',
                'Multi-Branch Reporting Filter' => 'Penyaringan laporan berdasarkan lokasi penugasan aktual karyawan (bukan sekadar cabang asal terdaftar).',
            ],
            'tips' => [
                'Contoh Kasus Pindah Cabang: "Karyawan Cabang A yang hari ini dijadwalkan ke Cabang B pada form By Date harus melakukan presensi di Cabang B dan otomatis masuk ke laporan Cabang B."',
                'Pada bisnis kafe/F&B, gunakan Roster Mingguan untuk membagi hari libur staf secara bergantian di hari kerja biasa (weekday) karena akhir pekan (weekend) biasanya ramai pengunjung.',
                'Gunakan filter cabang pada tabel monitoring presensi untuk memusatkan perhatian pada gerai outlet yang sedang Anda awasi.',
            ],
            'warnings' => 'PENTING: Jangan menyetujui pengajuan izin jika staf sebenarnya sudah hadir dan bekerja pada tanggal tersebut. Jika data presensi keliru, lakukan koreksi presensi terlebih dahulu sebelum memproses izin.',
        ],

        // =====================================================================
        // 3. DASHBOARD MONITORING & STATISTIK
        // =====================================================================
        'dashboard' => [
            'id' => 'dashboard',
            'pattern' => ['dashboard', 'dashboard/*'],
            'title' => 'Dashboard Utama Presensi',
            'subtitle' => 'Pusat ringkasan kehadiran harian, grafik tren operasional, dan antrean pengajuan',
            'icon' => 'ti ti-layout-dashboard',
            'badge' => 'Halaman Utama',
            'about' => 'Halaman Dashboard adalah pusat pemantauan utama untuk melihat rangkuman kehadiran seluruh karyawan pada hari ini secara langsung. Di sini Anda dapat mengetahui berapa karyawan yang hadir tepat waktu, terlambat, dispensasi, izin, sakit, cuti, belum hadir, serta melihat antrean pengajuan izin yang membutuhkan tindakan persetujuan.',
            'actions' => [
                'Memantau jumlah karyawan yang hadir tepat waktu, terlambat, izin, sakit, cuti, dan alpa hari ini.',
                'Melihat grafik tren kehadiran selama sepekan terakhir.',
                'Mengetahui persentase kehadiran per outlet/cabang kerja.',
                'Melihat antrean pengajuan izin dan cuti terbaru yang menunggu persetujuan Admin/Supervisor.',
                'Melihat aktivitas presensi terkini lengkap dengan foto selfie, jam masuk/pulang, dan cabang tugas.',
                'Mengklik kartu statistik untuk membuka daftar detail nama karyawan yang bersangkutan.',
            ],
            'steps' => [
                'Pilih tanggal di bagian atas jika ingin melihat rangkuman kehadiran pada hari lain.',
                'Gunakan pilihan Cabang atau Departemen jika ingin membatasi pemantauan untuk gerai tertentu.',
                'Perhatikan 4 Kartu Angka Statistik Utama di bagian atas untuk melihat kondisi kehadiran staf secara instan.',
                'Klik salah satu kartu angka (misal: Terlambat atau Perlu Tindakan) untuk melihat daftar nama karyawan secara terperinci.',
                'Gulir layar ke bawah untuk meninjau pengajuan izin yang berstatus pending, lalu klik untuk memproses persetujuannya.',
            ],
            'components' => [
                'Pilihan Tanggal & Cabang' => 'Menyaring ringkasan data agar sesuai dengan hari dan outlet kerja yang diinginkan.',
                'Kartu Statistik Hadir & Telat' => 'Menampilkan jumlah staf yang hadir tepat waktu vs yang terlambat melewati batas toleransi shift.',
                'Kartu Perlu Tindakan' => 'Menghitung total permohonan izin, sakit, dan cuti karyawan yang belum diverifikasi.',
                'Aktivitas Presensi Terkini' => 'Aliran data langsung (real-time) foto selfie, jam masuk, jam pulang, dan cabang tugas staf.',
            ],
            'tips' => [
                'Periksa kartu "Perlu Tindakan" setiap pagi agar karyawan segera memperoleh kepastian izin atau cuti mereka.',
                'Bila angka belum berubah, gunakan tombol segarkan peramban web untuk memuat data kehadiran detik terakhir.',
            ],
            'warnings' => null,
        ],

        // =====================================================================
        // 4. DATA KARYAWAN & PENUGASAN (MASTER STAF)
        // =====================================================================
        'karyawan' => [
            'id' => 'karyawan',
            'pattern' => ['karyawan', 'karyawan/*'],
            'exclude_pattern' => ['karyawan/*/show', 'karyawan/*/setjamkerja'],
            'title' => 'Data Karyawan & Penugasan',
            'subtitle' => 'Pengelolaan data profil staf, home branch, pembuatan akun mobile, dan status aktif',
            'icon' => 'ti ti-users',
            'badge' => 'Data Master',
            'about' => 'Halaman Data Karyawan digunakan untuk mengelola seluruh informasi karyawan perusahaan, mulai dari NIK, nama lengkap, penempatan Home Branch (cabang asal), departemen, jabatan, status aktif, pembuatan akun login mobile, hingga status kunci lokasi GPS.',
            'actions' => [
                'Mencari data karyawan berdasarkan nama lengkap atau nomor NIK.',
                'Menyaring daftar staf berdasarkan Home Branch, departemen, atau jabatan.',
                'Menambah data karyawan baru ke dalam sistem kepegawaian.',
                'Membuka halaman Detail Profil untuk mengelola foto biometrik wajah staf.',
                'Membuka pengaturan Roster Jam Kerja (Weekly Roster By Day & Override By Date).',
                'Membuatkan akun login aplikasi mobile bagi karyawan baru.',
                'Mengaktifkan atau menonaktifkan status kunci lokasi GPS dan kunci shift jam kerja.',
                'Mengunduh data seluruh karyawan ke dalam file Microsoft Excel (.xlsx).',
            ],
            'steps' => [
                'Ketik nama atau NIK staf pada kolom pencarian di bagian atas, lalu tekan Enter atau klik Cari.',
                'Gunakan filter Cabang dan Departemen untuk mempersempit daftar staf sesuai divisi.',
                'Klik tombol "Tambah Karyawan" di pojok kanan atas untuk mendaftarkan staf baru.',
                'Pada baris karyawan di tabel, gunakan tombol aksi:',
                ' - Tombol Detail (Ikon Mata): Membuka profil dan dataset biometrik wajah.',
                ' - Tombol Roster (Ikon Kalender/Jam): Mengatur jadwal mingguan dan override per tanggal.',
                ' - Tombol Kunci GPS (Ikon Gembok): Menentukan apakah staf wajib absen di dalam radius kantor.',
            ],
            'components' => [
                'Home Branch (Cabang Asal)' => 'Cabang induk administratif tempat karyawan terdaftar pertama kali.',
                'Status Kunci GPS' => 'Gembok hijau menandakan karyawan wajib berada di radius resmi cabang tugas saat absen. Gembok merah berarti karyawan diberi toleransi bebas lokasi.',
                'Akun Login Mobile' => 'Menampilkan status apakah akun Android/iPhone staf sudah aktif atau belum.',
                'Tombol Set Jam Kerja' => 'Pintu masuk menuju pengaturan Roster Mingguan dan Jadwal Khusus Tanggal staf bersangkutan.',
            ],
            'tips' => [
                'Setelah menambah data staf baru, segera buatkan akun mobile dan daftarkan foto wajahnya di menu Detail Profil agar staf dapat langsung presensi keesokan harinya.',
                'Untuk staf yang sering bertugas keliling antar-outlet, pastikan penugasan cabang harian diatur via Jadwal By Date atau sesuaikan kunci GPS.',
            ],
            'warnings' => 'Menonaktifkan status karyawan akan langsung memblokir akses login mobile staf dan mengecualikan staf dari kalkulasi absensi aktif.',
        ],

        // =====================================================================
        // 5. DETAIL KARYAWAN & BIOMETRIK WAJAH
        // =====================================================================
        'karyawan_detail' => [
            'id' => 'karyawan_detail',
            'pattern' => ['karyawan/*/show'],
            'title' => 'Detail Profil & Biometrik Wajah',
            'subtitle' => 'Kelola foto pengenalan wajah AI (Face Recognition), data akun, dan biodata staf',
            'icon' => 'ti ti-id',
            'badge' => 'Profil Staf',
            'about' => 'Halaman Detail Karyawan menampilkan informasi menyeluruh tentang satu orang staf. Di sini Anda dapat mengelola akun login mobile, melihat riwayat penugasan, serta mendaftarkan foto dataset wajah yang digunakan oleh kecerdasan buatan (Face Recognition 128D) saat staf melakukan presensi selfie.',
            'actions' => [
                'Melihat informasi lengkap staf, tanggal bergabung, masa kerja, dan status akun.',
                'Membuatkan akun mobile staf baru atau mereset akun jika staf berganti handphone.',
                'Melihat galeri foto biometrik wajah yang sudah terdaftar di sistem.',
                'Menambahkan foto referensi wajah baru staf menggunakan kamera atau unggah berkas.',
                'Menghapus foto dataset wajah jika wajah staf perlu didaftarkan ulang.',
            ],
            'steps' => [
                'Periksa kartu profil di bagian atas untuk mengecek status akun dan biodata staf.',
                'Jika staf belum memiliki akun login, klik tombol "Buat Akun Mobile" untuk membuatkan username dan kata sandi sementara.',
                'Buka tab "Biometrik Wajah" untuk memeriksa apakah foto referensi wajah staf sudah ada.',
                'Klik "Tambah Foto Wajah" untuk mengambil foto wajah staf dari jarak wajar dengan pencahayaan terang dan ekspresi netral.',
                'Sistem secara otomatis mengekstraksi vektor biometrik 128 dimensi untuk dicocokkan saat presensi selfie di handphone.',
            ],
            'components' => [
                'Tab Biometrik Wajah' => 'Tempat menyimpan foto dan vektor pengenalan wajah berakurasi tinggi (threshold ketat 0.48).',
                'Tombol Buat/Reset Akun' => 'Menghasilkan akun kredensial login aplikasi mobile bagi staf bersangkutan.',
                'Tab Data Penugasan' => 'Rincian jabatan, divisi, home branch, nomor kontak, dan status kunci shift.',
            ],
            'tips' => [
                'Daftarkan 1 hingga 3 foto wajah staf dengan sudut pencahayaan yang jelas agar presensi di handphone berjalan cepat (< 0.1 detik).',
                'Pastikan staf tidak menggunakan kacamata hitam, topi lebar, atau masker saat pengambilan foto referensi biometrik.',
            ],
            'warnings' => 'Tindakan "Hapus Semua Wajah" akan menghapus seluruh rekaman biometrik staf bersangkutan sehingga staf tidak dapat melakukan presensi sebelum foto baru didaftarkan.',
        ],

        // =====================================================================
        // 6. MONITORING PRESENSI HARIAN
        // =====================================================================
        'presensi' => [
            'id' => 'presensi',
            'pattern' => ['presensi', 'presensi/*'],
            'exclude_pattern' => ['trackingpresensi', 'trackingpresensi/*'],
            'title' => 'Monitoring Presensi Harian',
            'subtitle' => 'Pemantauan jam masuk/pulang, foto selfie, lokasi GPS, early out, dan koreksi manual',
            'icon' => 'ti ti-calendar-check',
            'badge' => 'Kehadiran',
            'about' => 'Halaman Monitoring Presensi digunakan untuk memantau catatan kehadiran seluruh staf pada tanggal kerja tertentu secara langsung. Admin dapat melihat jam masuk/pulang, foto selfie, koordinat GPS pada peta, status terlambat, catatan kepulangan lebih awal (early out), status arsip foto bulanan, serta melakukan koreksi presensi manual berizin audit.',
            'actions' => [
                'Memantau daftar kehadiran staf harian secara langsung (real-time).',
                'Menyaring data presensi berdasarkan tanggal tertentu, cabang tugas, departemen, dan nama staf.',
                'Memfilter berdasarkan status: Hadir, Terlambat, Dispensasi, Izin, Sakit, Cuti, atau Tanpa Keterangan (Alpha).',
                'Melihat bukti foto selfie saat presensi masuk dan pulang.',
                'Melihat titik lokasi GPS presensi karyawan di peta digital.',
                'Melihat catatan dan alasan staf yang pulang lebih awal (Early Out).',
                'Melakukan Koreksi Presensi Manual jika terjadi kendala teknis (wajib mengisi alasan koreksi).',
                'Melihat riwayat jejak audit: siapa admin yang mengubah data presensi dan apa alasannya.',
                'Mengunduh berkas ZIP arsip foto presensi bulanan (Cold Storage).',
                'Menjalankan fungsi Auto-Alpha untuk menandai staf yang tidak hadir setelah shift berakhir.',
            ],
            'steps' => [
                'Tentukan tanggal yang ingin dipantau pada kotak "Pilih Tanggal Presensi".',
                'Pilih cabang pada filter jika ingin memusatkan tampilan pada outlet tertentu, lalu klik "Cari Data".',
                'Perhatikan kolom Jam Masuk dan Jam Pulang: klik tautan jam atau ikon foto untuk melihat foto selfie dan peta lokasi presensi.',
                'Jika ada staf yang pulang sebelum waktunya, perhatikan lencana "Early Out" beserta durasi menit dan alasan tertulisnya.',
                'Jika perlu mengoreksi jam atau status presensi staf karena kendala teknis, klik tombol pensil hijau (Koreksi Presensi), perbarui data, dan WAJIB ketikkan alasan perubahan.',
                'Untuk foto-foto presensi dari bulan lampau yang telah diarsipkan, sistem akan menampilkan keterangan "Foto telah diarsipkan".',
            ],
            'components' => [
                'Lencana Hadir / Telat / Dispensasi' => 'Hijau (Tepat Waktu), Kuning (Terlambat dengan rincian menit), Biru Muda (Hadir dengan Dispensasi Disetujui).',
                'Indikator Early Out (Pulang Cepat)' => 'Menampilkan menit kepulangan sebelum jam shift berakhir dan alasan resmi staf.',
                'Tombol Koreksi Presensi (Ikon Pensil)' => 'Formulir perbaikan data absensi manual yang mewajibkan pengisian alasan audit trail.',
                'Tombol Auto-Alpha' => 'Menjalankan evaluasi otomatis untuk menandai karyawan yang tidak hadir dan tidak berizin menjadi Alpha.',
                'Arsip Bulanan (ZIP)' => 'Akses unduhan paket arsip foto presensi yang telah dikompresi ke penyimpanan jangka panjang.',
            ],
            'tips' => [
                'Karyawan Cabang A yang sedang bertugas di Cabang B akan otomatis tercatat presensinya di Cabang B sesuai roster.',
                'Gunakan filter status di bagian atas tabel untuk langsung meninjau siapa saja staf yang terlambat hari ini.',
            ],
            'warnings' => 'Setiap koreksi manual presensi tercatat permanen di sistem lengkap dengan nama admin yang mengubah, tanggal perubahan, dan alasan resmi. Hindari melakukan koreksi data tanpa konfirmasi jelas dari kepala outlet.',
        ],

        // =====================================================================
        // 7. TRACKING GPS PRESENSI (PETA DIGITAL)
        // =====================================================================
        'trackingpresensi' => [
            'id' => 'trackingpresensi',
            'pattern' => ['trackingpresensi', 'trackingpresensi/*'],
            'title' => 'Live Tracking GPS Presensi',
            'subtitle' => 'Pemetaan sebaran titik lokasi presensi staf di atas peta digital interaktif',
            'icon' => 'ti ti-map-pin',
            'badge' => 'Peta GPS',
            'about' => 'Halaman Live Tracking GPS memetakan titik koordinat fisik tempat staf menekan tombol presensi masuk dan pulang di atas peta digital interaktif. Fitur ini memudahkan Admin untuk memastikan kejujuran lokasi presensi staf di sekeliling radius resmi kafe.',
            'actions' => [
                'Melihat sebaran titik presensi staf di atas peta interaktif secara visual.',
                'Menyaring tampilan peta berdasarkan tanggal presensi dan cabang tertentu.',
                'Mengklik penanda lokasi (pin / cluster) untuk melihat rincian nama staf, jam presensi, dan foto selfie.',
                'Memeriksa apakah lokasi presensi berada di dalam batas lingkaran toleransi outlet.',
            ],
            'steps' => [
                'Pilih tanggal presensi yang ingin dipantau pada filter bagian atas peta.',
                'Pilih cabang outlet jika ingin memfokuskan tampilan peta pada lokasi cabang tertentu.',
                'Gunakan tombol zoom (+) dan (-) atau roda mouse untuk memperbesar dan memperkecil peta.',
                'Klik lingkaran pengelompokan angka (cluster) untuk memecah titik-titik staf yang berdekatan.',
                'Klik salah satu pin staf untuk memunculkan jendela informasi berisi foto selfie, jam presensi, cabang tugas, dan koordinat.',
            ],
            'components' => [
                'Peta Digital Interaktif' => 'Peta layar penuh yang menampilkan kontur jalan dan lokasi presensi.',
                'Penanda Lokasi (Pin)' => 'Titik fisik koordinat saat staf menekan tombol absen di handphone.',
                'Lingkaran Cluster' => 'Pengelompokan titik lokasi yang berdekatan agar peta tetap rapi dan mudah dibaca.',
                'Jendela Popup Keterangan' => 'Kotak detail foto selfie, jam presensi masuk/pulang, dan alamat koordinat.',
            ],
            'tips' => [
                'Gunakan fitur ini untuk memverifikasi keabsahan lokasi saat ada staf yang melaporkan kesulitan absen di dalam gedung kafe.',
            ],
            'warnings' => null,
        ],

        // =====================================================================
        // 8. PERSETUJUAN IZIN ABSEN
        // =====================================================================
        'izinabsen' => [
            'id' => 'izinabsen',
            'pattern' => ['izinabsen', 'izinabsen/*'],
            'title' => 'Persetujuan Izin Absen',
            'subtitle' => 'Verifikasi dan persetujuan permohonan izin tidak masuk kerja karyawan',
            'icon' => 'ti ti-file-description',
            'badge' => 'Pengajuan Izin',
            'about' => 'Halaman Persetujuan Izin Absen digunakan untuk meninjau dan memproses permohonan izin tidak hadir yang diajukan oleh staf melalui aplikasi mobile, misalnya karena urusan mendesak atau keluarga. Sistem memiliki proteksi anti-self approval dan proteksi agar izin tidak menimpa karyawan yang sudah hadir fisik.',
            'actions' => [
                'Melihat daftar permohonan izin absen staf yang berstatus Pending, Disetujui, atau Ditolak.',
                'Menyaring pengajuan berdasarkan rentang tanggal, cabang, departemen, dan nama staf.',
                'Menyetujui (Approve) permohonan izin yang memenuhi ketentuan operasional.',
                'Menolak (Reject) permohonan izin jika tidak memenuhi kriteria perusahaan.',
                'Membatalkan status persetujuan jika diperlukan evaluasi ulang dari manajemen.',
                'Menyetujui Izin Susulan untuk staf yang sebelumnya tercatat Alpa (mengubah status Alpa menjadi Izin resmi).',
            ],
            'steps' => [
                'Pilih filter status "Pending" untuk menampilkan permohonan yang membutuhkan tindakan persetujuan.',
                'Klik baris pengajuan atau tombol detail untuk membaca alasan dan tanggal izin yang diajukan staf.',
                'Pastikan staf tidak sedang tercatat hadir fisik pada tanggal tersebut.',
                'Klik tombol persetujuan (Setujui/Approve) jika disetujui, atau tombol Tolak jika tidak diizinkan.',
                'Jika disetujui, jadwal kehadiran staf pada tanggal tersebut otomatis dicatat sebagai "Izin".',
            ],
            'components' => [
                'Proteksi Kehadiran Aktual' => 'Pencegahan otomatis dari sistem: Izin TIDAK DAPAT disetujui jika karyawan pada tanggal tersebut sudah tercatat hadir masuk (jam_in terisi).',
                'Proteksi Anti-Self Approval' => 'Atasan atau admin yang memiliki akun staf dilarang menyetujui pengajuan izin atas namanya sendiri.',
                'Izin Susulan (Pengganti Alpha)' => 'Jika staf sebelumnya tercatat Alpha (a), persetujuan izin susulan akan otomatis mengubah status menjadi Izin resmi dengan catatan audit.',
            ],
            'tips' => [
                'Segera tindak lanjuti pengajuan izin yang masuk agar supervisor outlet dapat mengatur staf pengganti di roster kafe.',
            ],
            'warnings' => 'Pengajuan izin yang disetujui akan memotong jam kerja normal. Pastikan sudah ada koordinasi dengan penanggung jawab shift outlet terkait sebelum memberikan persetujuan.',
        ],

        // =====================================================================
        // 9. PERSETUJUAN IZIN SAKIT
        // =====================================================================
        'izinsakit' => [
            'id' => 'izinsakit',
            'pattern' => ['izinsakit', 'izinsakit/*'],
            'title' => 'Persetujuan Izin Sakit',
            'subtitle' => 'Pemeriksaan foto Surat Izin Dokter (SID) dan verifikasi ketidakhadiran sakit',
            'icon' => 'ti ti-file-text',
            'badge' => 'Pengajuan Sakit',
            'about' => 'Halaman Persetujuan Izin Sakit digunakan untuk memverifikasi laporan ketidakhadiran staf karena sakit. Admin dapat langsung memeriksa foto Surat Izin Dokter (SID) yang diunggah staf sebelum mengesahkan status kehadiran menjadi Sakit.',
            'actions' => [
                'Melihat daftar permohonan izin sakit staf.',
                'Membuka dan memeriksa foto berkas Surat Izin Dokter (SID) secara langsung.',
                'Menyetujui izin sakit sehingga presensi staf tercatat resmi sebagai "Sakit".',
                'Menolak izin sakit jika lampiran surat dokter buram, palsu, atau tanggal istirahat tidak sesuai.',
                'Menyetujui Izin Sakit Susulan untuk mengganti catatan status Alpa menjadi Sakit.',
            ],
            'steps' => [
                'Buka permohonan izin sakit yang berstatus "Pending".',
                'Klik tombol "Lihat Surat Dokter / SID" untuk memeriksa stempel klinik/RS, nama dokter, dan tanggal istirahat yang diberikan.',
                'Pastikan tanggal istirahat pada surat dokter mencakup tanggal izin yang diajukan.',
                'Klik "Setujui" jika berkas lengkap dan sah. Sistem akan mencatat kehadiran sebagai "Sakit".',
                'Klik "Tolak" jika lampiran tidak memenuhi syarat atau tidak terbaca.',
            ],
            'components' => [
                'Lampiran Surat Dokter (SID)' => 'Bukti foto surat keterangan istirahat dari dokter atau instansi medis.',
                'Rentang Tanggal Sakit' => 'Hari mulai dan hari selesai istirahat sakit yang dimohonkan.',
            ],
            'tips' => [
                'Jika foto surat dokter buram, mintalah staf mengirimkan foto ulang yang jelas sebelum menyetujui.',
            ],
            'warnings' => 'Surat izin dokter bersifat rahasia medis kepegawaian. Gunakan berkas lampiran hanya untuk keperluan absensi resmi perusahaan.',
        ],

        // =====================================================================
        // 10. PERSETUJUAN CUTI KARYAWAN
        // =====================================================================
        'izincuti' => [
            'id' => 'izincuti',
            'pattern' => ['izincuti', 'izincuti/*'],
            'title' => 'Persetujuan Cuti Karyawan',
            'subtitle' => 'Pemeriksaan pengajuan cuti, sisa kuota cuti bulanan, dan pencetakan formulir',
            'icon' => 'ti ti-calendar-stats',
            'badge' => 'Pengajuan Cuti',
            'about' => 'Halaman Persetujuan Cuti Karyawan digunakan untuk meninjau permohonan cuti tahunan dan cuti khusus staf. Sistem secara otomatis menghitung pemakaian cuti terhadap Kuota Cuti Bulanan (Monthly Leave Quota) agar jatah cuti staf terkendali secara adil.',
            'actions' => [
                'Melihat daftar permohonan cuti yang diajukan seluruh staf.',
                'Memeriksa sisa kuota cuti staf pada bulan bersangkutan secara otomatis.',
                'Menyetujui pengajuan cuti sehingga jadwal staf tercatat resmi sebagai "Cuti".',
                'Menolak permohonan cuti jika kuota habis atau jadwal operasional kafe sedang padat.',
                'Mencetak berkas Formulir Pengajuan Cuti resmi untuk arsip fisik personalia.',
            ],
            'steps' => [
                'Buka baris pengajuan cuti yang berstatus "Pending".',
                'Periksa jenis cuti, lama hari cuti, dan sisa kuota cuti yang dimiliki staf pada bulan tersebut.',
                'Pastikan ketersediaan staf pengganti di cabang tugas terkait sebelum memberikan persetujuan.',
                'Klik "Setujui" untuk mengesahkan cuti, atau "Tolak" jika tidak memungkinkan.',
                'Klik tombol "Cetak Form Cuti" jika kantor membutuhkan lembar fisik bertanda tangan.',
            ],
            'components' => [
                'Kuota Cuti Bulanan' => 'Batas maksimal hari cuti yang boleh diambil staf dalam satu bulan kalender (diatur pada Pengaturan Umum).',
                'Jumlah Hari Cuti' => 'Total hari kerja efektif yang diajukan staf.',
                'Tombol Cetak Form Cuti' => 'Menghasilkan dokumen resmi formulir perizinan cuti siap cetak ke printer atau PDF.',
            ],
            'tips' => [
                'Perhitungan hari cuti mengacu pada hari kerja efektif staf dan tidak menghitung hari OFF/libur roster staf.',
            ],
            'warnings' => 'Persetujuan cuti tidak dapat diberikan jika staf sudah tercatat hadir fisik pada tanggal tersebut.',
        ],

        // =====================================================================
        // 11. DISPENSASI KETERLAMBATAN
        // =====================================================================
        'dispensasi' => [
            'id' => 'dispensasi',
            'pattern' => ['dispensasi', 'dispensasi/*'],
            'title' => 'Dispensasi Keterlambatan',
            'subtitle' => 'Persetujuan toleransi keterlambatan jam masuk kerja dengan alasan yang dapat dibenarkan',
            'icon' => 'ti ti-clock-check',
            'badge' => 'Dispensasi',
            'about' => 'Halaman Dispensasi Keterlambatan digunakan untuk memberikan izin khusus kepada staf yang datang terlambat karena alasan kuat yang dapat dibenarkan (misal: instruksi belanja logistik kafe mendadak, kendaraan mogok, atau bencana cuaca). Dengan dispensasi ini, presensi staf tercatat "Hadir Dispensasi" tanpa denda keterlambatan.',
            'actions' => [
                'Melihat daftar permohonan dispensasi keterlambatan staf.',
                'Menyetujui dispensasi sehingga status kehadiran staf disesuaikan menjadi Hadir Dispensasi.',
                'Menolak dispensasi jika alasan keterlambatan tidak dapat diterima perusahaan.',
                'Menambah data dispensasi langsung dari sisi Admin atas arahan pimpinan.',
            ],
            'steps' => [
                'Periksa daftar permohonan dispensasi pada tabel.',
                'Lihat tanggal kejadian, jam kedatangan staf, batas dispensasi, dan alasan keterlambatan.',
                'Klik tombol "Setujui" jika pimpinan outlet telah menyetujui alasan tersebut.',
                'Sistem akan otomatis menyesuaikan status absensi staf pada hari itu tanpa potongan menit keterlambatan.',
            ],
            'components' => [
                'Batas Jam Dispensasi' => 'Waktu maksimal kedatangan yang ditoleransi untuk staf tersebut pada hari kejadian.',
                'Alasan Dispensasi' => 'Keterangan tertulis penyebab terjadinya keterlambatan.',
            ],
            'tips' => [
                'Dispensasi sebaiknya hanya diberikan kepada staf yang telah mengabari atasan sebelum jam shift dimulai.',
            ],
            'warnings' => 'Pemberian dispensasi akan membebaskan staf dari catatan pelanggaran keterlambatan pada rekapitulasi gaji bulanan.',
        ],

        // =====================================================================
        // 12. HARI LIBUR & TANGGAL MERAH
        // =====================================================================
        'harilibur' => [
            'id' => 'harilibur',
            'pattern' => ['harilibur', 'harilibur/*'],
            'title' => 'Hari Libur & Tanggal Merah',
            'subtitle' => 'Penetapan hari libur nasional resmi dan libur operasional perusahaan/cabang',
            'icon' => 'ti ti-calendar-off',
            'badge' => 'Hari Libur',
            'about' => 'Halaman Hari Libur digunakan untuk mencatat tanggal merah resmi nasional maupun libur operasional perusahaan. Pada tanggal yang ditetapkan di sini, staf tidak diwajibkan presensi dan tidak ditandai alpa. PENTING: Hari kerja Minggu dan hari OFF rutin mingguan staf kafe BUKAN diatur di sini, melainkan mengikuti Weekly Roster (By Day).',
            'actions' => [
                'Melihat kalender daftar hari libur nasional dan libur perusahaan yang terdaftar.',
                'Menambah tanggal hari libur baru lengkap dengan nama keterangan libur.',
                'Menentukan apakah libur berlaku untuk Seluruh Cabang (ALL) atau cabang tertentu.',
                'Menetapkan libur khusus untuk individu staf tertentu via tombol "Atur Karyawan".',
                'Mengubah atau menghapus data tanggal libur jika terjadi pembatalan.',
            ],
            'steps' => [
                'Klik tombol "Tambah Hari Libur" di pojok kanan atas untuk memasukkan tanggal merah baru.',
                'Pilih tanggal libur, masukkan nama keterangan (contoh: "Tahun Baru Masehi"), dan pilih cabang yang berlaku (atau Semua Cabang).',
                'Klik "Simpan" untuk mendaftarkan hari libur ke dalam kalender kerja sistem.',
                'Jika ada libur yang hanya berlaku untuk staf tertentu, klik tombol "Atur Karyawan" pada baris hari libur tersebut.',
            ],
            'components' => [
                'Tabel Hari Libur' => 'Daftar tanggal, hari, nama perayaan libur, dan cakupan cabang yang diliburkan.',
                'Cakupan Cabang (ALL vs Cabang Tertentu)' => 'Membedakan hari libur yang berlaku serentak atau hanya untuk gerai di wilayah tertentu.',
                'Tombol Atur Karyawan' => 'Memungkinkan penetapan libur resmi perorangan karyawan.',
            ],
            'tips' => [
                'Ingat: Pada industri F&B/kafe, hari Minggu adalah hari kerja normal jika staf terjadwal di Weekly Roster. Jangan mendaftarkan hari Minggu sebagai hari libur nasional di halaman ini.',
                'Daftarkan seluruh tanggal merah resmi di awal tahun agar perhitungan absensi berjalan otomatis.',
            ],
            'warnings' => 'Menghapus tanggal libur yang sudah lampau dapat menyebabkan sistem mengevaluasi staf yang tidak hadir pada hari itu sebagai tidak masuk kerja (alpa).',
        ],

        // =====================================================================
        // 13. DATA MASTER CABANG / OUTLET
        // =====================================================================
        'cabang' => [
            'id' => 'cabang',
            'pattern' => ['cabang', 'cabang/*'],
            'title' => 'Outlet & Cabang Kantor',
            'subtitle' => 'Pengelolaan data lokasi gerai kafe, koordinat GPS, dan radius presensi',
            'icon' => 'ti ti-building-store',
            'badge' => 'Data Master',
            'about' => 'Halaman Cabang digunakan untuk mengelola seluruh gerai/outlet fisik perusahaan. Di sini Anda menentukan titik koordinat GPS (Latitude & Longitude) serta radius toleransi (dalam meter) agar presensi staf terkunci akurat di lokasi kerja yang sah.',
            'actions' => [
                'Melihat daftar seluruh outlet kafe yang terdaftar.',
                'Menambah cabang outlet baru ke dalam sistem presensi.',
                'Mengatur titik koordinat GPS gerai (garis lintang & garis bujur) secara presisi.',
                'Menentukan batas jarak radius aman presensi (contoh: 30 meter atau 50 meter).',
                'Mengatur zona waktu operasional cabang (WIB, WITA, atau WIT).',
                'Memperbarui alamat lengkap, nomor telepon, dan data penanggung jawab cabang.',
            ],
            'steps' => [
                'Klik tombol "Tambah Cabang" untuk mendaftarkan gerai kafe baru.',
                'Isi kode cabang yang unik (misal: CB01, PUSAT), nama outlet, dan alamat lengkap.',
                'Tentukan titik koordinat GPS kantor dengan mengetik koordinat atau memilih pin lokasi pada peta.',
                'Tentukan angka radius toleransi dalam meter (direkomendasikan 30 s.d. 50 meter).',
                'Klik "Simpan" agar outlet baru siap digunakan oleh staf dalam sistem roster dan presensi.',
            ],
            'components' => [
                'Titik Koordinat GPS' => 'Angka latitude dan longitude yang menunjukkan posisi fisik gerai di peta bumi.',
                'Radius Absen (Meter)' => 'Jarak lingkaran maksimal dari titik gerai di mana handphone staf diizinkan melakukan presensi.',
                'Timezone Cabang' => 'Zona waktu lokal yang digunakan untuk menentukan waktu presensi staf di cabang bersangkutan.',
            ],
            'tips' => [
                'Untuk mendapatkan titik koordinat akurat, buka Google Maps saat berada di dalam gerai, klik kanan pada titik lokasi, lalu salin angka koordinatnya.',
                'Gunakan radius minimal 30–50 meter untuk mengantisipasi deviasi sinyal GPS handphone staf saat berada di dalam ruangan.',
            ],
            'warnings' => 'Mengatur radius terlalu sempit (misal di bawah 15 meter) dapat menyebabkan staf kesulitan presensi karena fluktuasi sinyal GPS di area padat bangunan.',
        ],

        // =====================================================================
        // 14. DATA MASTER DEPARTEMEN & JABATAN
        // =====================================================================
        'departemen' => [
            'id' => 'departemen',
            'pattern' => ['departemen', 'departemen/*'],
            'title' => 'Data Master Departemen',
            'subtitle' => 'Pengelolaan struktur divisi dan bagian kerja operasional kafe',
            'icon' => 'ti ti-sitemap',
            'badge' => 'Data Master',
            'about' => 'Halaman Departemen digunakan untuk mengelola divisi kerja di perusahaan (seperti Barista & Pelayanan, Dapur, Operasional Outlet, Kasir, Gudang, dan Manajemen Pusat) guna pengelompokan staf dan pelaporan absensi.',
            'actions' => [
                'Melihat seluruh divisi/departemen kerja yang terdaftar.',
                'Menambah departemen baru ke dalam sistem.',
                'Mengubah nama atau kode departemen yang sudah ada.',
                'Menghapus departemen yang sudah tidak digunakan.',
            ],
            'steps' => [
                'Klik tombol "Tambah Departemen" di kanan atas.',
                'Masukkan kode departemen singkat (misal: OPR, BAR, KIT) dan nama lengkap divisi.',
                'Klik tombol "Simpan" untuk menambahkan ke dalam daftar.',
            ],
            'components' => [
                'Kode Departemen' => 'Singkatan unik divisi kerja.',
                'Nama Departemen' => 'Nama lengkap bagian atau divisi operasional perusahaan.',
            ],
            'tips' => [
                'Gunakan singkatan kode yang konsisten dan mudah diingat.',
            ],
            'warnings' => 'Departemen yang masih memiliki staf aktif di dalamnya tidak boleh dihapus agar data karyawan tidak mengalami error relasi.',
        ],

        'jabatan' => [
            'id' => 'jabatan',
            'pattern' => ['jabatan', 'jabatan/*'],
            'title' => 'Data Master Jabatan',
            'subtitle' => 'Pengelolaan struktur posisi profesi dan jenjang pekerjaan staf',
            'icon' => 'ti ti-briefcase',
            'badge' => 'Data Master',
            'about' => 'Halaman Jabatan digunakan untuk mengelola daftar posisi pekerjaan staf di perusahaan (misalnya: Store Manager, Supervisor Shift, Head Barista, Barista, Cook, Kasir, dan Server).',
            'actions' => [
                'Melihat daftar posisi jabatan kerja yang berlaku.',
                'Menambah nama posisi jabatan baru.',
                'Mengubah nama atau kode jabatan.',
                'Menghapus jabatan yang sudah tidak digunakan.',
            ],
            'steps' => [
                'Klik tombol "Tambah Jabatan" di kanan atas.',
                'Ketikkan kode jabatan dan nama gelar jabatan yang diinginkan.',
                'Klik "Simpan" untuk menyimpan data ke sistem.',
            ],
            'components' => [
                'Kode Jabatan' => 'Kode unik posisi jabatan.',
                'Nama Jabatan' => 'Nama sebutan profesi kerja staf.',
            ],
            'tips' => [
                'Sesuaikan penamaan jabatan dengan hierarki persetujuan (approval) operasional outlet.',
            ],
            'warnings' => 'Jangan menghapus jabatan yang sedang digunakan oleh karyawan aktif.',
        ],

        // =====================================================================
        // 15. DATA MASTER CUTI & SHIFT KERJA
        // =====================================================================
        'cuti' => [
            'id' => 'cuti',
            'pattern' => ['cuti', 'cuti/*'],
            'title' => 'Data Master Jenis Cuti',
            'subtitle' => 'Pengaturan kategori cuti dan jatah kuota hari tahunan',
            'icon' => 'ti ti-calendar-stats',
            'badge' => 'Data Master',
            'about' => 'Halaman Jenis Cuti digunakan untuk menentukan kategori cuti yang boleh diambil oleh staf beserta kuota maksimal hari dalam satu tahun kalender (contoh: Cuti Tahunan jatah 12 hari, Cuti Menikah 3 hari, Cuti Melahirkan 90 hari).',
            'actions' => [
                'Melihat daftar kategori cuti yang tersedia bagi staf.',
                'Menambah jenis cuti baru dengan menentukan jatah kuota hari tahunan.',
                'Mengubah nama jenis cuti atau memperbarui jumlah kuota hari.',
                'Menghapus jenis cuti yang sudah tidak berlaku.',
            ],
            'steps' => [
                'Klik tombol "Tambah Cuti" di bagian atas.',
                'Ketik kode cuti (misal: C01), nama cuti (misal: "Cuti Tahunan"), dan kuota jumlah hari (misal: 12).',
                'Klik "Simpan" untuk mengaktifkan jenis cuti tersebut di sistem pengajuan staf.',
            ],
            'components' => [
                'Kode Cuti' => 'Singkatan unik kategori cuti.',
                'Nama Cuti' => 'Nama resmi izin cuti.',
                'Jumlah Hari (Kuota Tahunan)' => 'Batas maksimal hari kerja yang berhak diambil staf untuk kategori ini.',
            ],
            'tips' => [
                'Pengambilan cuti tahunan staf juga dibatasi oleh kuota cuti bulanan yang diatur di Pengaturan Umum.',
            ],
            'warnings' => 'Mengubah jumlah kuota hari pada jenis cuti yang sedang berjalan akan langsung memengaruhi sisa saldo cuti seluruh karyawan pada tahun ini.',
        ],

        'jamkerja' => [
            'id' => 'jamkerja',
            'pattern' => ['jamkerja', 'jamkerja/*'],
            'title' => 'Master Shift & Jam Kerja',
            'subtitle' => 'Pengaturan jam masuk, jam pulang, shift malam lintas hari, dan batas toleransi',
            'icon' => 'ti ti-clock',
            'badge' => 'Shift & Jam Kerja',
            'about' => 'Halaman Shift & Jam Kerja digunakan untuk mengatur jadwal jam kerja operasional (misalnya: Shift Pagi dari jam 07:00 s.d 15:00, Shift Siang dari jam 15:00 s.d 23:00, dan Shift Malam lintas hari dari jam 22:00 s.d 06:00). Anda juga mengatur menit toleransi keterlambatan dan batas awal mulai boleh absen.',
            'actions' => [
                'Melihat daftar seluruh shift kerja operasional yang tersedia.',
                'Menambah pola shift kerja baru (jam masuk, jam pulang, dan toleransi menit).',
                'Mengaktifkan pengaturan Shift Lintas Hari (Shift Malam / Overnight).',
                'Mengatur batas awal mulai boleh absen (agar staf tidak absen terlalu dini).',
                'Mengatur batas toleransi keterlambatan (staf yang absen lewat toleransi dicatat Terlambat).',
                'Mengatur batas jam presensi pulang (untuk mencegah staf absen pulang sebelum waktunya).',
            ],
            'steps' => [
                'Klik tombol "Tambah Jam Kerja" di kanan atas.',
                'Beri kode dan nama shift (contoh: "Shift Pagi Outlet" atau "Shift Malam Barista").',
                'Tentukan Jam Masuk dan Jam Pulang kerja resmi.',
                'Jika jam pulang berada di keesokan harinya (misal masuk 22:00 pulang 06:00), centang opsi "Shift Lintas Hari (Lintas Hari)".',
                'Tentukan Batas Toleransi Keterlambatan (misal: toleransi 5 menit atau 15 menit).',
                'Klik "Simpan" agar jadwal shift siap digunakan dalam Roster Mingguan staf.',
            ],
            'components' => [
                'Jam Masuk & Pulang' => 'Jam resmi staf mulai bekerja dan jam resmi staf selesai bekerja.',
                'Shift Lintas Hari' => 'Penanda bahwa jam pulang shift berada di hari berikutnya (shift malam kafe). Presensi pulang dievaluasi sebelum batas jam pagi.',
                'Batas Toleransi Terlambat' => 'Menit kelonggaran sebelum sistem otomatis menandai kehadiran staf sebagai "Terlambat".',
                'Batas Awal Boleh Absen' => 'Waktu paling awal tombol presensi masuk mulai menerima absensi dari staf.',
            ],
            'tips' => [
                'Gunakan pengaturan Shift Lintas Hari untuk shift penutupan kafe yang selesai pada dini hari atau pagi hari berikutnya.',
                'Berikan batas awal absen masuk sekitar 30 menit sebelum jam shift agar staf yang datang lebih awal dapat absen dengan tenang.',
            ],
            'warnings' => 'Perubahan jam masuk/pulang pada shift yang sedang aktif akan langsung memengaruhi perhitungan keterlambatan dan status Auto-Alpha hari ini.',
        ],

        // =====================================================================
        // 16. LAPORAN REKAPITULASI (MULTI-BRANCH REPORTING)
        // =====================================================================
        'laporan_presensi' => [
            'id' => 'laporan_presensi',
            'pattern' => ['laporan/presensi', 'laporan/presensi/*'],
            'title' => 'Laporan Rekapitulasi Presensi',
            'subtitle' => 'Pencetakan laporan kehadiran multi-cabang berdasarkan cabang penugasan aktual',
            'icon' => 'ti ti-file-spreadsheet',
            'badge' => 'Laporan Multi-Cabang',
            'about' => 'Halaman Laporan Presensi digunakan untuk menyusun rekapitulasi kehadiran staf selama periode tertentu (bulanan, tahunan, atau rentang tanggal kustom). Sistem menggunakan MULTI-BRANCH REPORTING: staf yang ditugaskan bekerja di Cabang B akan otomatis terhitung dalam laporan Cabang B, bukan sekadar Home Branch asalnya.',
            'actions' => [
                'Memilih periode laporan: Bulanan, Tahunan, atau Rentang Tanggal Kustom (maksimal 31 hari).',
                'Menyaring laporan berdasarkan Cabang Penugasan Aktual atau seluruh cabang.',
                'Menyaring laporan berdasarkan divisi departemen tertentu.',
                'Memilih laporan untuk satu staf tertentu atau seluruh staf sekaligus.',
                'Mencetak laporan ke format dokumen cetak resmi PDF bertanda tangan.',
                'Mengunduh laporan rekapitulasi lengkap ke dalam berkas Microsoft Excel (.xlsx).',
            ],
            'steps' => [
                'Pilih Cabang penugasan dan Departemen yang ingin ditarik laporannya.',
                'Pilih Karyawan tertentu jika ingin rekap perorangan, atau kosongkan untuk seluruh staf.',
                'Tentukan periode laporan (misal: Bulanan, lalu pilih Bulan dan Tahun).',
                'Klik tombol "Cetak Laporan" untuk membuka lembar cetak formulir resmi bertanda tangan.',
                'Klik tombol "Export Excel" jika Anda ingin mengolah angka kehadiran di spreadsheet.',
            ],
            'components' => [
                'Multi-Branch Reporting' => 'Data kehadiran dihitung berdasarkan cabang tempat staf benar-benar bertugas dan melakukan presensi pada tanggal tersebut.',
                'Filter Periode Tanggal' => 'Batas awal dan batas akhir perhitungan rekapan absensi.',
                'Kolom Rekapitulasi' => 'Menampilkan total hari Hadir Tepat Waktu, Terlambat, Hadir Dispensasi, Izin, Sakit, Cuti, dan Alpha.',
            ],
            'tips' => [
                'Contoh Multi-Branch: Jika staf Cabang A bertugas di Cabang B selama 5 hari dalam sebulan, maka ke-5 hari tersebut akan masuk ke dalam rekapitulasi Cabang B saat Anda memfilter Cabang B.',
                'Pastikan seluruh pengajuan izin, sakit, dan dispensasi pada bulan terkait sudah selesai disetujui sebelum mengunduh rekap gaji.',
            ],
            'warnings' => null,
        ],

        'laporan_cuti' => [
            'id' => 'laporan_cuti',
            'pattern' => ['laporan/cuti', 'laporan/cuti/*'],
            'title' => 'Laporan Rekapitulasi Cuti',
            'subtitle' => 'Rekap penggunaan hak cuti staf, pemakaian per bulan, dan sisa saldo cuti tahunan',
            'icon' => 'ti ti-report-analytics',
            'badge' => 'Laporan Cuti',
            'about' => 'Halaman Laporan Cuti digunakan untuk melihat rekapitulasi pemakaian cuti seluruh staf dalam satu tahun berjalan. Di sini terlihat berapa hari cuti yang telah digunakan pada setiap bulan serta sisa saldo cuti yang masih dimiliki masing-masing karyawan.',
            'actions' => [
                'Memilih tahun periode laporan cuti yang ingin ditinjau.',
                'Menyaring rekap cuti berdasarkan cabang outlet dan departemen.',
                'Memilih jenis cuti tertentu (misal Cuti Tahunan) atau seluruh jenis cuti.',
                'Mencetak rekapitulasi cuti ke dokumen cetak PDF bertanda tangan.',
                'Mengunduh laporan rekapitulasi cuti ke berkas Microsoft Excel (.xlsx).',
            ],
            'steps' => [
                'Pilih cabang dan departemen yang ingin dilihat datanya.',
                'Pilih tahun laporan yang diinginkan.',
                'Klik "Cetak Laporan" untuk tampilan cetak fisik, atau klik "Export Excel" untuk menyimpannya sebagai file spreadsheet.',
            ],
            'components' => [
                'Kolom Bulan (Jan - Des)' => 'Rincian jumlah hari cuti yang diambil pada tiap-tiap bulan kalender.',
                'Total Ambil & Sisa Kuota' => 'Akumulasi hari cuti yang sudah disetujui vs sisa hak cuti staf.',
            ],
            'tips' => [
                'Gunakan laporan ini secara berkala untuk memantau agar pengambilan cuti staf merata sepanjang tahun dan tidak menumpuk di akhir tahun.',
            ],
            'warnings' => null,
        ],

        // =====================================================================
        // 17. PENGATURAN UMUM & KEAMANAN SISTEM
        // =====================================================================
        'generalsetting' => [
            'id' => 'generalsetting',
            'pattern' => ['generalsetting', 'generalsetting/*'],
            'title' => 'Pengaturan Umum Sistem',
            'subtitle' => 'Konfigurasi identitas aplikasi, warna tema, parameter GPS, dan kuota cuti bulanan',
            'icon' => 'ti ti-settings-cog',
            'badge' => 'Pengaturan',
            'about' => 'Halaman Pengaturan Umum adalah pusat kendali sistem untuk menentukan identitas aplikasi dan parameter penting kehadiran, seperti nama perusahaan, logo, warna tema antarmuka, radius standar absen, ambang batas pengenalan wajah, serta batasan kuota cuti bulanan.',
            'actions' => [
                'Mengubah Nama Aplikasi dan Nama Perusahaan.',
                'Mengunggah logo resmi perusahaan yang tampil di sidebar menu dan laporan.',
                'Mengatur Warna Tema Utama (Primary) dan Sekunder antarmuka sistem.',
                'Menentukan Jarak Radius Standar (dalam meter) untuk presensi GPS.',
                'Mengatur Kuota Cuti Bulanan (Monthly Leave Quota) maksimal per staf.',
                'Mengatur Batasan Jam Absen Masuk dan Pulang.',
                'Memperbaiki struktur hak akses sistem jika diperlukan (Fix Permissions).',
            ],
            'steps' => [
                'Ubah teks Nama Aplikasi atau Nama Perusahaan pada kolom yang tersedia.',
                'Pilih warna tema utama menggunakan pemilih warna jika ingin menyesuaikan dengan warna khas kafe.',
                'Tentukan angka radius standar presensi dan kuota cuti bulanan.',
                'Gulir ke bawah dan klik tombol "Simpan Pengaturan" untuk menerapkan perubahan.',
            ],
            'components' => [
                'Radius Standar Absen' => 'Jarak radius default jika cabang tertentu belum mengatur radius khusus.',
                'Kuota Cuti Bulanan' => 'Maksimal jumlah hari cuti yang diizinkan diambil staf dalam satu bulan kalender (standar: 3 hari/bulan).',
                'Threshold Wajah' => 'Tingkat ketelitian algoritma dalam mencocokkan wajah staf saat presensi selfie.',
            ],
            'tips' => [
                'Gunakan logo dengan format gambar PNG berlatar transparan agar terlihat rapi dan elegan pada sidebar.',
            ],
            'warnings' => 'Perubahan pada halaman ini memengaruhi tampilan dan pengaturan operasional di seluruh akun pengguna dan seluruh outlet.',
        ],

        // =====================================================================
        // 18. MANAJEMEN PENGGUNA & HAK AKSES
        // =====================================================================
        'users' => [
            'id' => 'users',
            'pattern' => ['users', 'users/*'],
            'title' => 'Manajemen Pengguna Admin',
            'subtitle' => 'Pengelolaan akun login administrator, supervisor, role, dan kata sandi',
            'icon' => 'ti ti-user-check',
            'badge' => 'Keamanan Akun',
            'about' => 'Halaman Manajemen Pengguna digunakan untuk mengelola akun yang memiliki akses ke Panel Admin (seperti Super Admin, Admin Personalia, Admin Cabang, atau Supervisor Outlet). Di sini Anda dapat membuat akun admin baru, mengatur role, dan mereset kata sandi.',
            'actions' => [
                'Melihat daftar seluruh akun pengguna yang memiliki akses ke dashboard admin.',
                'Mendaftarkan akun administrator atau supervisor baru.',
                'Mengatur penugasan cabang yang boleh diakses oleh admin cabang.',
                'Menetapkan peran (Role) pengguna (Super Admin, Admin, GM Administrasi, dll).',
                'Mengganti atau mereset kata sandi akun admin.',
                'Menghapus akun pengguna yang sudah tidak berhak mengakses panel admin.',
            ],
            'steps' => [
                'Klik tombol "Tambah Pengguna" di kanan atas untuk mendaftarkan akun admin baru.',
                'Ketikkan nama pengguna, alamat email aktif, kata sandi yang aman, dan pilih Role yang sesuai.',
                'Tentukan cabang yang dapat diakses oleh akun tersebut (khusus admin cabang).',
                'Klik "Simpan" untuk mengaktifkan akun baru.',
            ],
            'components' => [
                'Role / Hak Akses' => 'Tingkatan wewenang pengguna dalam mengakses menu-menu sistem.',
                'Cabang Akses' => 'Membatasi admin cabang agar hanya dapat melihat data staf di cabangnya saja.',
            ],
            'tips' => [
                'Gunakan kata sandi yang kuat dengan kombinasi huruf besar, huruf kecil, dan angka.',
            ],
            'warnings' => 'Jangan pernah membagikan email dan kata sandi akun administrator kepada pihak yang tidak berkepentingan demi menjaga keamanan data perusahaan.',
        ],

        'permissiongroups' => [
            'id' => 'permissiongroups',
            'pattern' => ['permissiongroups', 'permissiongroups/*'],
            'title' => 'Grup Hak Akses Sistem',
            'subtitle' => 'Pengelompokan kategori izin akses fitur aplikasi',
            'icon' => 'ti ti-folder-check',
            'badge' => 'Hak Akses',
            'about' => 'Halaman Grup Hak Akses digunakan untuk mengelompokkan izin-izin teknis ke dalam kelompok menu yang teratur (seperti kelompok Karyawan, Presensi, Pengajuan Izin, dan Laporan).',
            'actions' => [
                'Melihat daftar kelompok izin akses yang ada.',
                'Menambah kelompok izin baru.',
                'Mengubah nama kelompok hak akses.',
                'Menghapus kelompok yang tidak digunakan.',
            ],
            'steps' => [
                'Klik "Tambah Grup" untuk membuat kelompok baru.',
                'Ketikkan nama grup dan deskripsi singkat, lalu klik "Simpan".',
            ],
            'components' => [
                'Nama Grup' => 'Kategori menu tempat izin fitur dikelompokkan.',
            ],
            'tips' => [
                'Halaman ini dikhususkan bagi Super Admin untuk pemeliharaan struktur hak akses sistem.',
            ],
            'warnings' => 'Hindari menghapus grup hak akses yang sedang menaungi izin-izin aktif aplikasi.',
        ],

        'permissions' => [
            'id' => 'permissions',
            'pattern' => ['permissions', 'permissions/*'],
            'title' => 'Daftar Izin & Hak Akses',
            'subtitle' => 'Konfigurasi izin teknis setiap fitur dan tombol pada aplikasi',
            'icon' => 'ti ti-shield-lock',
            'badge' => 'Hak Akses',
            'about' => 'Halaman Daftar Izin digunakan oleh Super Admin untuk melihat dan mengonfigurasi izin akses detail setiap fitur dalam aplikasi (misal: izin melihat data karyawan, izin menambah data, izin menyetujui cuti, izin koreksi presensi).',
            'actions' => [
                'Melihat daftar seluruh izin teknis yang mengatur wewenang di aplikasi.',
                'Menambah izin fitur baru.',
                'Mengubah nama atau grup izin.',
                'Menghapus izin yang sudah tidak digunakan.',
            ],
            'steps' => [
                'Klik tombol "Tambah Permission" jika sistem memiliki modul fitur baru.',
                'Masukkan nama teknis izin (misal: "laporan.presensi") dan pilih grup menu terkait.',
                'Klik "Simpan" untuk mendaftarkan izin.',
            ],
            'components' => [
                'Nama Permission' => 'Kode pengenal izin fitur di dalam kode sistem.',
            ],
            'tips' => [
                'Izin ini bekerja secara otomatis bersama sistem Role Pengguna (Spatie Permission).',
            ],
            'warnings' => 'Mengubah atau menghapus nama izin yang sedang digunakan di aplikasi dapat menyebabkan tombol atau halaman tertentu tidak dapat dibuka oleh staf admin.',
        ],

        // =====================================================================
        // 19. BACKUP & PEMELIHARAAN SISTEM
        // =====================================================================
        'backup' => [
            'id' => 'backup',
            'pattern' => ['backup', 'backup/*'],
            'title' => 'Backup & Restore Database',
            'subtitle' => 'Pencadangan arsip data sistem dan pemulihan database secara aman',
            'icon' => 'ti ti-database-export',
            'badge' => 'Pemeliharaan',
            'about' => 'Halaman Backup & Restore Database digunakan untuk menjaga keamanan seluruh data perusahaan. Anda dapat mengunduh berkas salinan cadangan (.sql) ke komputer pribadi sebagai arsip aman, serta mengembalikan data jika terjadi insiden teknis.',
            'actions' => [
                'Mengunduh salinan cadangan database sistem (.sql) ke komputer pribadi.',
                'Menyimpan arsip data secara berkala untuk keperluan keamanan data kepegawaian.',
                'Melakukan pemulihan (restore) database dari file salinan cadangan yang pernah diunduh.',
            ],
            'steps' => [
                'Untuk membuat cadangan data, klik tombol besar "Download Backup Database (.sql)". Sistem akan menyiapkan berkas dan peramban Anda akan mulai mengunduh file cadangan.',
                'Simpan berkas hasil download tersebut di tempat penyimpanan yang aman (misalnya: flashdisk atau Google Drive perusahaan).',
                'Jika ingin memulihkan data dari berkas lama, klik tombol pilih file pada kartu Restore, pilih berkas .sql cadangan Anda, lalu klik "Mulai Restore Data".',
            ],
            'components' => [
                'Kartu Backup Data' => 'Area untuk memicu pembuatan dan pengunduhan salinan database terbaru.',
                'Kartu Restore Data' => 'Area untuk mengunggah berkas salinan database lama guna mengembalikan kondisi sistem.',
            ],
            'tips' => [
                'Sangat disarankan mengunduh backup database secara berkala (misalnya setiap akhir pekan atau sebelum rekapitulasi gaji bulanan).',
                'Beri nama berkas backup dengan mencantumkan tanggal pengunduhan agar mudah dikenali saat dibutuhkan.',
            ],
            'warnings' => 'PERHATIAN PENTING: Proses "Restore Database" akan MENIMPA dan MENGGANTIKAN seluruh data yang ada saat ini dengan data yang ada di dalam berkas cadangan. Lakukan restore hanya jika benar-benar diarahkan oleh tim teknis pimpinan.',
        ],
    ],
];
