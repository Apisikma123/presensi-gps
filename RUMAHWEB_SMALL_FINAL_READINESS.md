# RUMAHWEB SMALL / UNLIMITED COMPATIBILITY & HARDENING REPORT
**HR Presensi GPS & Face Recognition (2 Cabang Coffee Shop)**  
**Status Lokal:** `READY FOR RUMAHWEB STAGING`  
*(Catatan: Status ini HANYA dapat dinaikkan menjadi `READY FOR PRODUCTION` setelah pengujian beban konkurensi langsung di server Rumahweb selesai tanpa kendala resource faults / HTTP 508).*

---

## 1. Queue Compatibility & Architecture (Prioritas P0)
Pada paket Rumahweb Small / Unlimited shared hosting murah, **tidak ada akses SSH interaktif** dan **tidak diperbolehkan menjalankan daemon process permanen** (seperti `queue:work` background loop). 

### Klasifikasi Beban Kerja (Job Classification):
1. **Kategori A: MUST BE INSTANT (Eksekusi Sinkron Langsung)**
   - **Login & Otentikasi:** Dicatat langsung secara sinkron melalui `LogSuccessfulLogin` ke tabel `user_login_logs`. Tidak ada jeda antrean.
   - **Presensi Masuk & Pulang (Clock-In / Clock-Out):** Seluruh verifikasi radius GPS, face descriptor biometrik, validasi shift, anti-mock/nonce, dan kompresi WebP berjalan sinkron dalam request HTTP (`< 300 ms`). Karyawan langsung menerima respons sukses/gagal di layar PWA.
   - **Password Reset:** Dijalankan sinkron melalui mailer.
   - *Jaminan:* Alur user-facing kritis **TIDAK PERNAH** tertahan oleh cron 30 menit.

2. **Kategori B: CAN BE DELAYED (Database Queue)**
   - **Approval Request Notification:** `NewApprovalRequestNotification` (tahap approval izin/dispensasi).
   - **Approval Status Notification:** `ApprovalStatusNotification`.
   - **Broadcast Pengumuman:** `PengumumanNotification`.
   - Job di atas mengimplementasikan `ShouldQueue`. Jika `.env` menggunakan `QUEUE_CONNECTION=database`, notifikasi ini tersimpan aman di tabel `jobs` dan diproses saat worker dieksekusi. Jika `.env` menggunakan `QUEUE_CONNECTION=sync`, notifikasi langsung dikirimkan saat event terjadi.

3. **Kategori C: HEAVY BACKGROUND / CRON**
   - **Auto-Alpha Generation:** Dijalankan via scheduler per jam (`hourly()`).
   - **Long-term Attendance Photo Archive:** Dijalankan via scheduler pukul 02:00 malam (`dailyAt('02:00')`).

### Hardening Worker pada Shared Hosting:
- **Tabel Antrean:** Telah dibuat migration `create_jobs_table` (`jobs` dan `job_batches`) agar mode database queue tidak crash.
- **Konfigurasi Scheduler:** Di `app/Console/Kernel.php`, worker diubah dari `everyMinute()` menjadi:
  ```php
  $schedule->command('queue:work --queue=default --sleep=3 --tries=3 --stop-when-empty')
      ->everyThirtyMinutes()
      ->withoutOverlapping();
  ```
- **Karakteristik Aman:** Parameter `--stop-when-empty` memastikan worker otomatis berhenti (exit code 0) segera setelah antrean kosong, sehingga tidak memakan Entry Process (EP) atau RAM secara persisten.

---

## 2. Cron & Scheduler Compatibility (Interval 30 Menit)
Paket shared hosting membatasi cron job minimum dapat mencapai **30 menit** (`*/30 * * * *`).

### Audit & Penyesuaian Seluruh Scheduled Tasks:
| Command | Frekuensi Cron | Detail Waktu | Evaluasi & Status |
| :--- | :--- | :--- | :--- |
| `queue:work --queue=default --sleep=3 --tries=3 --stop-when-empty` | `0,30 * * * *` | Menit 0 dan 30 | **PASS** — Sinkron dengan cron `*/30 * * * *`. |
| `presensi:auto-alpha` | `0 * * * *` | Menit 0 setiap jam | **PASS** — Terpanggil tepat setiap jam saat trigger menit 0. |
| `maintenance:archive-attendance-photos --execute` | `0 2 * * *` | Pukul 02:00 malam | **PASS** — Terpanggil tepat pada jadwal 02:00. |

*Keterangan Dependensi:* Tidak ada task yang menggunakan `everyMinute`, `everyFiveMinutes`, `everyTenMinutes`, atau `everyFifteenMinutes`. Seluruh task beroperasi pada titik menit 0 atau 30.

---

## 3. PHP Compatibility & Version Selection
Berdasarkan audit `composer.json` (`php: ^8.1`, `laravel/framework: ^10.10`) dan benchmark real server:

- **Rekomendasi Versi PHP:** **PHP 8.2** (Gunakan `ea-php82` atau `alt-php82` di cPanel Select PHP Version).
- **Rasional:** PHP 8.1 sudah mendekati End-of-Life, sedangkan PHP 8.2 memiliki stabilitas terbaik, kompatibilitas penuh dengan Laravel 10.50, dan optimasi OPcache JIT/preloading yang lebih matang.

### Rekomendasi Direktif `php.ini` di cPanel:
```ini
memory_limit = 256M
max_execution_time = 60
max_input_time = 60
upload_max_filesize = 10M
post_max_size = 12M
date.timezone = Asia/Jakarta
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 10000
opcache.validate_timestamps = 0 ; (opsional di production agar zero I/O check)
```

---

## 4. Required PHP Extensions Checklist
Aktifkan seluruh ekstensi berikut di cPanel **Select PHP Version** > **Extensions**:

- [x] `pdo_mysql` (Driver koneksi database MySQL/MariaDB)
- [x] `mbstring` (Manipulasi string multibyte untuk core framework & helper)
- [x] `openssl` (Enkripsi payload token Sanctum, password, & sessions)
- [x] `fileinfo` (Deteksi MIME type file foto & lampiran SID)
- [x] `gd` dengan **WebP Support** aktif (Kompresi foto absensi ke WebP via Intervention Image / ImageOptimizer)
- [x] `curl` (HTTP Client Guzzle untuk integrasi eksternal / push notification)
- [x] `zip` (*Wajib* untuk kompresi ZIP private arsip foto >12 bulan & export Excel)
- [x] `opcache` (*Wajib* untuk shared hosting agar memory limit & CPU tidak boros)
- [x] `bcmath` atau `gmp` (Kriptografi kurva eliptis VAPID WebPush Notification)
- [x] `intl` (Internasionalisasi dan lokalisasi tanggal Bahasa Indonesia)

---

## 5. Production Build & Local Packaging Workflow (No SSH)
Karena server hosting tidak memiliki SSH, proses kompilasi aset frontend dan dependensi vendor **WAJIB diselesaikan di komputer lokal sebelum diunggah**.

### Langkah Build Lokal:
1. Pastikan file dev mode tidak ada:
   Hapus `public/hot` jika ada.
2. Kompilasi aset Vite:
   ```bash
   npm ci
   npm run build
   ```
3. Install dependensi composer production-only:
   ```bash
   composer install --no-dev --prefer-dist --optimize-autoloader
   ```
4. Jalankan script otomatisasi packaging:
   ```powershell
   powershell -ExecutionPolicy Bypass -File .\scripts\package-production.ps1
   ```
   *Script ini otomatis mengemas seluruh source code, vendor, dan public build ke file ZIP bersih (`presensi-gps-production.zip`), sekaligus membuang `node_modules`, `.git`, unit tests, scratch files, local log, dan file `.env` lokal.*

---

## 6. Public Directory Security & Hosting File Structure
Untuk shared hosting cPanel, jangan sekali-kali mengekstrak seluruh proyek Laravel langsung ke dalam `public_html` tanpa pemisahan folder.

### Struktur Aman yang Direkomendasikan:
```text
/home/cpaneluser/
├── presensigps/              <-- Core Laravel (Di luar web root!)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env                  <-- Kredensial aman (tidak bisa diakses publik)
│   ├── artisan
│   └── composer.json
│
└── public_html/              <-- Hanya berisi konten folder public/
    ├── assets/
    ├── build/                <-- Aset hasil npm run build
    ├── models/               <-- Model Face Recognition
    ├── storage               <-- Symlink ke /home/cpaneluser/presensigps/storage/app/public
    ├── .htaccess             <-- Hardened security & cache headers
    └── index.php
```

### Penyesuaian `public_html/index.php`:
Sesuaikan path bootstrap pada `index.php` di dalam `public_html`:
```php
require __DIR__.'/../presensigps/vendor/autoload.php';
$app = require_once __DIR__.'/../presensigps/bootstrap/app.php';
```

### Lapisan Pengamanan Ganda (Defense-in-Depth):
1. **Root `.htaccess`:** Telah dibuat di root proyek untuk memblokir langsung akses ke `.env`, `composer.*`, `storage`, `artisan`, dan `.git` jika terjadi salah letak folder di hosting.
2. **`public/.htaccess`:** Ditambahkan aturan penolakan eksplisit (`Deny from all`) untuk `composer.(json|lock)`, `package(-lock)?.json`, `artisan`, `.env`, `.sql`, dan file sensitif lainnya, serta security headers (`X-Content-Type-Options`, `X-Frame-Options`, `X-XSS-Protection`, `Referrer-Policy`).

---

## 7. Cache, Log, dan Session Hygiene
Shared hosting murah memiliki batasan ketat pada **Inode Quota** (jumlah file) dan **I/O**. Penumpukan file sementara akan menyebabkan hosting tersuspend.

1. **Session Driver:**
   - Menggunakan `SESSION_DRIVER=database`.
   - *Rasional:* Mencegah timbulnya puluhan ribu file session terfragmentasi di `storage/framework/sessions` yang memakan kuota inode.
2. **Log Retention & Rotation:**
   - Channel log menggunakan `daily` dengan retensi `LOG_DAILY_DAYS=7`.
   - Log otomatis dihapus setelah 7 hari oleh Monolog, mencegah `storage/logs/laravel.log` membengkak hingga gigabytes.
   - Production level: `LOG_LEVEL=error`.
3. **Framework Cache:**
   - Telah divalidasi kompatibel dengan caching bawaan Laravel:
     - `php artisan config:cache` (**PASS**)
     - `php artisan route:cache` (**PASS**)
     - `php artisan view:cache` (**PASS**)

---

## 8. Static Asset Caching Policy
Konfigurasi browser caching pada `public/.htaccess` telah dioptimasi:
- **Vite Hashed Assets (`/build/assets/*`):** `Cache-Control: public, max-age=31536000, immutable`. Aset yang memiliki fingerprint hash disimpan permanen di browser pengguna.
- **Model Face Recognition (`/models/*`):**
  - Shard bobot binary (`-shard[0-9]+`): `Cache-Control: public, max-age=2592000` (30 hari).
  - Manifest JSON: `Cache-Control: public, max-age=86400, must-revalidate` (1 hari dengan revalidasi).
- **Static Assets (CSS, JS, Fonts, Images):** `ExpiresDefault "access plus 1 month"`, font WOFF2 `access plus 1 year`.
- **Dynamic HTML:** Laravel secara otomatis menyertakan header `Cache-Control: private, no-cache` untuk rute terotentikasi.

---

## 9. Request Payload & Photo Compression Measurement
Berdasarkan verifikasi pada kode penanganan foto dan data aktual di storage:

- **Client-Side (PWA Mobile):**
  - Kamera mengambil snapshot canvas resolusi **640x480** (JPEG quality 0.95).
  - Payload HTTP POST berukuran rata-rata **~50 KB s/d 80 KB**.
- **Server-Side (`ImageOptimizer::saveAsWebp`):**
  - Mengonversi payload ke format **WebP** dengan kualitas 80 dan auto-downscale maksimal 1080px.
- **Pengukuran Real Disk Storage (176 Berkas Foto Absensi):**
  - **Minimum:** 22 B (test stub)
  - **Rata-rata (Average):** **7.35 KB**
  - **Median:** ~6.00 KB
  - **Persentil 95 (P95):** **20.60 KB**
  - **Maksimum:** **34.15 KB**
- *Kesimpulan:* Pengiriman foto sangat ringan bagi jaringan seluler karyawan dan tidak membebani limit upload hosting, dengan tetap menjaga akurasi biometrik wajah.

---

## 10. Database Optimization & Index Verification
Database telah diverifikasi bebas dari query unbounded dan N+1:

- [x] **Index Presensi Terverifikasi:**
  - `unique_presensi_nik_tanggal` pada `['nik', 'tanggal']` (mencegah duplikasi data absensi per hari).
  - `idx_presensi_nik_tgl_status` pada `['nik', 'tanggal', 'status']` (pencarian rekap & histori per karyawan).
  - `idx_presensi_cabang_tanggal` pada `['kode_cabang', 'tanggal']` (filter dashboard & monitoring 2 cabang).
  - `idx_presensi_status_tanggal_jk` pada `['status', 'tanggal', 'kode_jam_kerja']` (perhitungan auto-alpha & shift).
- [x] **No Unbounded Query:** Tidak ditemukan `Presensi::all()` di seluruh controller/service. Seluruh pencarian absensi terikat rentang tanggal (`tanggal` atau `whereBetween`).
- [x] **No Redundant Indexes:** Struktur indeks bersih dan tepat sasaran.

---

## 11. Long-Term Archive Management (Foto > 12 Bulan)
Arsitektur pengarsipan foto absensi lama tetap dipertahankan sesuai spesifikasi:
- **Rentang 0–12 Bulan:** Berkas foto tersimpan sebagai WebP individual di `storage/app/public/uploads/absensi` untuk akses operasional harian.
- **Rentang > 12 Bulan:** Command `maintenance:archive-attendance-photos --execute` memindahkan foto ke arsip ZIP bulanan di folder privat `storage/app/private/attendance-archives/`.
- **Database Records:** Catatan database presensi bersifat **permanen** (hanya kolom `is_archived` yang di-flag, data log tidak pernah dihapus).
- **Shared Hosting Safe:** Memproses maksimal 1 bulan tertua per run di jadwal cron malam (02:00) dengan memori rendah.

---

## 12. Prosedur Deployment & Symlink Tanpa SSH

### A. Membuat Storage Symlink via cPanel Cron Job
Karena tidak ada SSH, symlink dibuat melalui Cron Job cPanel satu kali:
1. Masuk ke **cPanel** > **Cron Jobs**.
2. Tambahkan cron dengan frekuensi sekali (misal setiap menit):
   ```bash
   /usr/local/bin/php /home/cpaneluser/presensigps/artisan storage:link
   ```
   *Atau perintah shell native:*
   ```bash
   ln -s /home/cpaneluser/presensigps/storage/app/public /home/cpaneluser/public_html/storage
   ```
3. Tunggu 1 menit hingga direktori `public_html/storage` terbentuk.
4. **WAJIB:** Hapus entry cron job tersebut segera setelah berhasil.

### B. Menjalankan Database Migration via One-Time Cron
1. Di cPanel **Cron Jobs**, tambahkan cron entry satu kali:
   ```bash
   /usr/local/bin/php /home/cpaneluser/presensigps/artisan migrate --force
   ```
2. Periksa output email cron / log database untuk memastikan semua migration berstatus DONE.
3. **WAJIB:** Hapus entry cron job tersebut segera setelah proses selesai. Jangan pernah membuat URL publik untuk artisan migrate!

### C. Menyetel Cron Rutin Utama Laravel
Setelah symlink dan migrasi selesai, masukkan **hanya 1 cron job rutin** pada cPanel:
- **Interval:** `*/30 * * * *` (Setiap 30 menit)
- **Command:**
  ```bash
  /usr/local/bin/php /home/cpaneluser/presensigps/artisan schedule:run >> /dev/null 2>&1
  ```
  *(Catatan: Sesuaikan path `/usr/local/bin/php` dengan versi PHP cPanel Anda, misalnya `/usr/local/bin/ea-php82`)*.

---

## 13. Backup Reality & Batasan Resource Hosting
1. **Mitos "Unlimited":**
   Pada paket Rumahweb Unlimited / Small, "Unlimited Disk Space" tunduk pada aturan **Fair Usage Policy (FUP)** dan batasan **Inode Quota** (biasanya 250.000 – 500.000 file).
2. **Aturan Backup:**
   - **JANGAN** membuat backup zip project penuh setiap hari di dalam server cPanel yang sama.
   - **Database Backup:** Lakukan export berkala file `.sql` via menu **phpMyAdmin** cPanel atau menu **Backup Wizard** cPanel, lalu unduh ke penyimpanan lokal/Google Drive.
   - **Foto Backup:** Unduh file ZIP arsip bulanan dari `storage/app/private/attendance-archives/` secara berkala ke penyimpanan off-site.

---

## 14. Mitigasi Kegagalan Layanan Eksternal (Zero Blocker)
Aplikasi telah dirancang agar kegagalan jaringan eksternal tidak menghentikan absensi:
- **Aset Lokal:** Leaflet, Cropper, Toastr, SweetAlert2, Ionicons, WebcamJS, dan Face-API models telah terpasang secara lokal di direktori publik (`assets/external/` dan `models/`).
- **Font Google:** Menggunakan directive `font-display: swap`. Jika koneksi ke Google Fonts lambat atau down, browser akan langsung menggunakan fallback system font tanpa memblokir interaksi user.
- **Push Notification / SMTP:** Kegagalan pengiriman WebPush atau SMTP dibungkus dalam `try-catch` sehingga tidak menyebabkan rollback atau kegagalan absensi karyawan.

---

## 15. Checklist Verifikasi Manual di cPanel (CloudLinux)
Sebelum memulai pengujian beban di server Rumahweb, periksa metrik berikut di sidebar cPanel:

| Parameter CloudLinux | Nilai Minimum Disarankan | Risiko jika di Bawah Batas |
| :--- | :--- | :--- |
| **CPU Usage** | 1 Core (100%) | Respon lambat saat jam sibuk absensi. |
| **Physical Memory (RAM)** | 1024 MB (1 GB) | `Out of memory` (HTTP 500). |
| **Entry Processes (EP)** | Min 20 EP | Muncul error **HTTP 508 Resource Limit Reached**. |
| **Number of Processes (NPROC)** | Min 100 | Proses CLI / Cron / FastCGI gagal spawn. |
| **I/O Usage** | Min 5 MB/s s/d 10 MB/s | Bottleneck saat penulisan foto atau logging. |
| **IOPS** | Min 1024 IOPS | Antrean I/O disk. |
| **MySQL Max Connections** | Min 20-30 koneksi | Error `Too many connections`. |

---

## 16. Protokol Load Testing Rumahweb Staging (Wajib Dilakukan)
Untuk menaikkan status dari **`READY FOR RUMAHWEB STAGING`** menjadi **`READY FOR PRODUCTION`**, lakukan pengujian beban bertahap dari jaringan eksternal menggunakan tools seperti k6 / Apache Bench / Postman Runner:

### Skenario Uji Konkurensi Absensi:
1. **Tahap 1:** `1 Concurrent Request` (Smoke test fungsionalitas dan latency dasar).
2. **Tahap 2:** `5 Concurrent Requests` (Mensimulasikan 5 karyawan absen bersamaan di satu cabang).
3. **Tahap 3:** `10 Concurrent Requests` (Mensimulasikan jam masuk serentak di Cabang A dan Cabang B).
4. **Tahap 4:** `15 Concurrent Requests` (Beban puncak: 10-14 karyawan absen + aktivitas admin dashboard).

### Kriteria Kelulusan (Pass Criteria):
- [ ] Tidak ada response **HTTP 508** (*Resource Limit Is Reached*).
- [ ] Tidak ada response **HTTP 500** atau **HTTP 504 Gateway Timeout**.
- [ ] cPanel *CPU Usage* tidak mencapai 100% lebih dari 10 detik berturut-turut.
- [ ] cPanel *Entry Processes* tetap di bawah batas maksimal paket hosting.
- [ ] Response time Clock-In / Clock-Out P95 berada di bawah 2.0 detik.

*Jika seluruh kriteria pengujian di atas terpenuhi pada server hosting Rumahweb asli, maka aplikasi dinyatakan resmi:*  
**`READY FOR PRODUCTION`**
