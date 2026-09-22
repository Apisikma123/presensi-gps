# LONG-TERM DATA READINESS & STORAGE STRATEGY
**Aplikasi HR Presensi Coffee Shop 2 Cabang (±30–50 Karyawan)**  
**Target Environment:** Shared Hosting Murah (cPanel / Linux) — Tanpa Akses SSH Interaktif

---

## 1. Executive Summary & Final Strategy

Strategi penyimpanan jangka panjang untuk aplikasi HR Presensi Coffee Shop 2 Cabang dirancang untuk memenuhi batasan shared hosting berbiaya rendah dengan prinsip **Zero Data Loss**, **Zero External Paid Storage**, dan **Shared-Hosting-Friendly CPU/IO**:

```
+-----------------------------------------------------------------------------------+
|                            ARSITEKTUR PENYIMPANAN DATA                            |
+-----------------------------------------------------------------------------------+
|  HOT STORAGE (0–12 BULAN)                                                         |
|  - Foto Clock-In & Clock-Out tersimpan sebagai berkas individual WebP             |
|  - Lokasi: storage/app/public/uploads/absensi/                                    |
|  - Ditampilkan langsung pada aplikasi (dashboard, histori, dan modal detail)       |
+-----------------------------------------------------------------------------------+
|  COLD ARCHIVE (>12 BULAN)                                                         |
|  - Foto presensi lama dikompres PER BULAN ke dalam ZIP private                    |
|  - Lokasi: storage/app/private/attendance-archive/{YYYY-MM}.zip                   |
|  - Verifikasi integritas 10 langkah (cek entri + SHA-256) SEBELUM hapus source   |
|  - Berkas foto TIDAK DIHAPUS PERMANEN, melainkan dipindahkan menjadi ZIP arsip    |
+-----------------------------------------------------------------------------------+
|  DATABASE RELASIONAL (PERMANEN)                                                   |
|  - 100% baris presensi, tanggal, jam in/out, status, GPS, nama foto DIPERTAHANKAN |
|  - Flag metadata: is_archived = 1, foto_in_archived = 1, foto_out_archived = 1    |
|  - Riwayat audit dan koreksi tidak pernah dihapus                                 |
+-----------------------------------------------------------------------------------+
```

---

## 2. Parameter Konfigurasi Terpusat

Seluruh aturan masa simpan dikelola secara terpusat melalui file [config/attendance.php](file:///d:/presensigpsv2-main/config/attendance.php) dan variabel environment `.env` tanpa hardcode angka di berbagai file:

| Parameter Config | Variabel Environment | Default | Keterangan |
|---|---|---|---|
| `attendance.archive_enabled` | `ATTENDANCE_PHOTO_ARCHIVE_ENABLED` | `true` | Jika `false`, proses arsip tidak akan memindahkan atau menghapus file |
| `attendance.hot_months` | `ATTENDANCE_PHOTO_HOT_MONTHS` | `12` | Ambang batas bulan foto tetap berstatus Hot Storage |
| `attendance.archive_disk_path` | - | `storage/app/private/attendance-archive` | Lokasi direktori private arsip ZIP |
| `attendance.leave_attachment_retention_months` | `LEAVE_ATTACHMENT_RETENTION_MONTHS` | `24` | Masa simpan dokumen medis / surat dokter (SID) |

---

## 3. Verifikasi Faktual Data & Schema

### A. Referensi Foto Presensi
Hanya ada **2 kolom** pada database yang menyimpan berkas foto presensi:
1. **`presensi.foto_in`** (`varchar(255)`) — Berkas WebP Clock-In (contoh: `1001-2026-09-18-in.webp`).
2. **`presensi.foto_out`** (`varchar(255)`) — Berkas WebP Clock-Out (contoh: `1001-2026-09-18-out.webp`).

### B. Isolasi Berkas Non-Presensi (TIDAK BOLEH DIARSIPKAN)
Proses pengarsipan bulanan **HANYA** mengambil berkas foto presensi (`*-in.webp` dan `*-out.webp`). Berkas sistem berikut diisolasi penuh dan **DILARANG** dimasukkan ke dalam ZIP presensi:
- Foto profil karyawan (`storage/app/public/uploads/karyawan/`)
- Data biometrik / model wajah (`storage/app/private/uploads/facerecognition/`)
- Surat izin sakit / dokumen SID (`storage/app/private/uploads/sid/`)
- Berkas aplikasi lainnya

### C. Frekuensi Berkas Riil
- **Maksimal per hari per karyawan hadir via GPS**: **2 foto** (1 Masuk + 1 Pulang).
- **Karyawan libur / izin / cuti**: **0 foto**.
- **Karyawan absensi via fingerprint**: **0 foto** (`foto_in` & `foto_out` bernilai `NULL`).
- Basis perhitungan kapasitas maksimal (worst-case: 50 karyawan GPS penuh 365 hari/tahun):
  $$\text{Files/Hari} = 50 \times 2 = 100\text{ foto / hari}$$
  $$\text{Files/Tahun} = 50 \times 2 \times 365 = 36.500\text{ foto / tahun}$$

---

## 4. Hasil Pengukuran Nyata (Measured Benchmark)

Pengukuran langsung terhadap berkas foto WebP aktif pada aplikasi:
- **Jumlah sampel berkas**: 334 berkas WebP.
- **Measured Average File Size**: **7,56 KB** ($0,00738\text{ MB}$).
- **Measured P95 File Size (Persentil 95)**: **20,12 KB** ($0,01965\text{ MB}$).
- **Minimum File Size**: 0,06 KB (placeholder).
- **Maximum File Size**: 33,35 KB (high-detail selfie).

---

## 5. Proyeksi Penyimpanan & Inodes

Formula perhitungan:
$$\text{Storage (MB)} = \frac{\text{Jumlah File} \times \text{Ukuran Berkas (KB)}}{1.024}$$

### A. Proyeksi Storage Foto (Hot Storage 0–12 Bulan)

| Periode | Akumulasi File Hot | Total Storage Average (7,56 KB) | Total Storage P95 (20,12 KB) |
|---|---|---|---|
| **1 Tahun Pertama** | Maksimal 36.500 file | **269,47 MB** | **717,17 MB** |
| **Tahun ke-2 (Setelah Arsip Aktif)** | $\le$ 36.500 file (Ter-cap) | **$\le$ 269,47 MB** | **$\le$ 717,17 MB** |
| **Tahun ke-5 (Setelah Arsip Aktif)** | $\le$ 36.500 file (Ter-cap) | **$\le$ 269,47 MB** | **$\le$ 717,17 MB** |

### B. Proyeksi Cold Archives (>12 Bulan)
- Foto lama dikompres menjadi **1 ZIP per bulan** ($\approx$ 12 file ZIP per tahun).
- Estimasi ukuran 1 file ZIP bulanan (maksimal 3.040 foto): **~22 MB – 60 MB**.
- Akumulasi Cold Archives 5 tahun: **48 file ZIP** ($\approx$ 1,0 GB – 2,8 GB).

### C. Proyeksi Inodes Akun Hosting

| Komponen | Status Saat Ini | Proyeksi 1 Tahun | Proyeksi 3 Tahun | Proyeksi 5 Tahun |
|---|---|---|---|---|
| **Baseline Aplikasi & Vendor** | 11.127 | 11.127 | 11.127 | 11.127 |
| **Hot Attendance Files (Individual)** | 172 | $\le$ 36.500 | $\le$ 36.500 (Ter-cap) | $\le$ 36.500 (Ter-cap) |
| **Cold Archive ZIP Files** | 0 | 0 | 24 ZIPs | 48 ZIPs |
| **Total Inodes Akun** | **11.299** | **$\le$ 47.627** | **$\le$ 47.651** | **$\le$ 47.675** |

> [!IMPORTANT]
> **Status Inode Hosting:** Jangan mengarang kuota inode hosting tertentu. Periksa penggunaan dan limit nyata hosting melalui cPanel menu **"File Usage"** atau **"Inodes"**. Dengan strategi Hot/Cold ini, pertumbuhan inode individual **terkunci permanen pada $\le$ 47.700 inodes** (sangat aman di bawah batasan shared hosting standar yang umumnya berkisar 100.000–300.000 inodes).

---

## 6. Proyeksi Database Relasional (`presensi`)

- **Ukuran tabel saat ini**: 4,22 MB (7.569 baris).
- **Rata-rata ukuran per baris**: ±580 bytes.
- **Pertumbuhan baris per tahun**: 50 karyawan × 365 hari = 18.250 baris/tahun (+10,6 MB/tahun).

| Periode | Akumulasi Baris Presensi | Estimasi Ukuran Database | Status Baris |
|---|---|---|---|
| **1 Tahun** | 25.819 baris | **~14,8 MB** | 100% Intact |
| **3 Tahun** | 62.319 baris | **~36,0 MB** | 100% Intact |
| **5 Tahun** | 98.819 baris | **~57,1 MB** | 100% Intact |

Seluruh baris presensi **TIDAK PERNAH DIHAPUS**. Dalam 5 tahun, database hanya mencapai **~57 MB**, sehingga tidak memerlukan database sharding atau partitioning.

---

## 7. Mekanisme Zero Data Loss Archive Command

Command Laravel:
```bash
php artisan maintenance:archive-attendance-photos
```

### A. Prinsip Eksekusi
1. **Default DRY RUN**: Menjalankan analisis dan estimasi tanpa memodifikasi atau menghapus berkas apapun.
2. **Maksimal 1 Bulan per Run**: Hanya memproses 1 bulan paling tua yang eligible per eksekusi untuk mencegah CPU/IO spike di shared hosting.
3. **Syarat Kelayakan (Eligibility)**: Hanya mengarsipkan bulan yang sudah **sepenuhnya lebih tua** dari `ATTENDANCE_PHOTO_HOT_MONTHS` (misal jika saat ini September 2028 dan ambang batas 12 bulan, maka Agustus 2027 dan sebelumnya eligible; bulan berjalan tidak pernah disentuh).

### B. Urutan 10 Langkah Verifikasi Sebelum Hapus (Zero Data Loss Flow)
1. Kumpulkan daftar foto eligible dari DATABASE (`is_archived = 0` pada bulan target).
2. Periksa keberadaan fisik berkas pada disk (`storage/app/public/uploads/absensi/`).
3. Buat berkas ZIP TEMPORARY (`{YYYY-MM}.zip.tmp.{timestamp}`) di direktori private.
4. Tambahkan seluruh berkas sumber dengan prefix `uploads/absensi/{filename}` dan tutup ZIP.
5. Buka kembali ZIP dengan mode verifikasi (`ZipArchive::CHECKCONS`).
6. Verifikasi berkas ZIP tidak corrupt.
7. Hitung jumlah entri di dalam ZIP.
8. Pastikan jumlah entri == jumlah berkas sumber fisik yang dimasukkan.
9. Pindahkan/rename ZIP temporary ke lokasi final (`storage/app/private/attendance-archive/{YYYY-MM}.zip`), catat manifest JSON (`index.json` dengan SHA-256 checksum), dan perbarui database (`is_archived = 1`).
10. **HANYA SETELAH langkah 1–9 lolos 100%**, hapus berkas individual sumber dari disk.

*Jika salah satu langkah 1–9 gagal:* **ABORT**. Berkas temporary dihapus, berkas sumber tetap utuh, dan database tidak berubah.

---

## 8. Integrasi Antarmuka Pengguna (UI Tanpa 404)

- **Foto Hot (<12 bulan)**: Ditampilkan normal langsung dari berkas publik.
- **Foto Cold Archive (>12 bulan)**:
  - Antarmuka **TIDAK ME-REQUEST** URL gambar lama ke browser, sehingga tidak menghasilkan network error 404.
  - Tampilan Karyawan: Menampilkan kartu status netral **"Foto telah diarsipkan"**.
  - Tampilan Admin / Super Admin: Menampilkan kartu status **"Foto telah diarsipkan"** dilengkapi informasi bulan arsip: **"Arsip: [Bulan Tahun]"** (contoh: *Arsip: Januari 2027*) serta tombol unduh berkas ZIP private bagi Super Admin.
  - **Zero Unzip Overhead**: Aplikasi tidak pernah mengekstrak file ZIP saat membuka halaman histori/detail.

---

## 9. Unduh Berkas Arsip Admin-Only

Pengunduhan file arsip bulanan dikelola melalui controller aman [app/Http/Controllers/ProtectedFileController.php](file:///d:/presensigpsv2-main/app/Http/Controllers/ProtectedFileController.php):
- **Endpoint**: `GET /files/attendance-archive/{month}` (Route: `file.attendance-archive`)
- **Keamanan**:
  - Wajib login (`auth` middleware).
  - Wajib memiliki wewenang Super Admin atau permission `presensi.index`.
  - Sanitasi ketat parameter `{month}` regex `^\d{4}-(0[1-9]|1[0-2])$` untuk mencegah celah Path Traversal (`../`).
  - Berkas dialirkan langsung dari storage private menggunakan `response()->download()` tanpa membocorkan path fisik filesystem server.

---

## 10. Konfigurasi Scheduler & cPanel Cron Jobs

Hosting produksi tidak memiliki terminal SSH interaktif. Otomatisasi berjalan mandiri menggunakan scheduler Laravel yang dipanggil oleh cron job bawaan cPanel.

### A. Scheduler Laravel
Pada [app/Console/Kernel.php](file:///d:/presensigpsv2-main/app/Console/Kernel.php):
```php
$schedule->command('maintenance:archive-attendance-photos --execute')
    ->dailyAt('02:00')
    ->withoutOverlapping();
```
*Mengapa harian (Daily) pukul 02:00:* Karena command hanya memproses 1 bulan tertua yang eligible. Jika tidak ada bulan yang eligible, command keluar dalam hitungan milidetik secara normal tanpa beban server.

### B. Format Cron Generik di cPanel
Masukkan baris cron berikut di menu **cPanel $\rightarrow$ Cron Jobs** (jadwal Setiap Menit `* * * * *`):

```bash
* * * * * PHP_BINARY /path/to/project/artisan schedule:run >/dev/null 2>&1
```

> [!NOTE]
> Ganti `PHP_BINARY` dan `/path/to/project` sesuai konfigurasi riil hosting setelah deployment:
> - `PHP_BINARY`: Contoh `/usr/local/bin/php` atau `/usr/bin/php` (lihat PHP Path di cPanel Info Server).
> - `/path/to/project`: Path direktori root proyek di hosting (misal `/home/usercpanel/presensi`).

---

## 11. Prosedur Recovery Foto Lama

Jika pemilik usaha atau auditor memerlukan foto presensi lama (>12 bulan):
1. Buka histori presensi untuk mengetahui bulan presensi yang dicari (misal `2027-01`).
2. Masuk ke **cPanel $\rightarrow$ File Manager** $\rightarrow$ buka folder `storage/app/private/attendance-archive/` (atau unduh langsung melalui tombol Admin pada detail presensi).
3. Temukan berkas `2027-01.zip`.
4. Ekstrak ZIP secara lokal pada komputer atau folder temporary:
   - File tersimpan dengan nama:
     `uploads/absensi/{NIK}-{TANGGAL}-in.webp`
     `uploads/absensi/{NIK}-{TANGGAL}-out.webp`
5. **Jangan pernah mengekstrak seluruh arsip tahunan ke folder publik server** agar kuota inode hosting tetap terkendali.

---

## 12. Hasil Uji Verifikasi 12 Poin Wajib

Pengujian otomatis komprehensif dijalankan pada [tests/archive_storage_test.php](file:///d:/presensigpsv2-main/tests/archive_storage_test.php):

| No | Kasus Uji | Ekspektasi | Hasil |
|---|---|---|---|
| 1 | **Dry Run** | Tidak ada file yang dimodifikasi atau dihapus | **PASS** |
| 2 | **Archive 1 Bulan Test** | ZIP berhasil dibuat di storage private | **PASS** |
| 3 | **Validasi Integritas ZIP** | ZIP dapat dibuka dan lolos uji konsistensi | **PASS** |
| 4 | **Jumlah Entri ZIP** | Jumlah entri ZIP sama persis dengan berkas sumber | **PASS** |
| 5 | **Pembersihan Berkas Sumber** | Berkas individual WebP terhapus setelah verifikasi lolos | **PASS** |
| 6 | **Preservasi Database** | Baris presensi tetap utuh 100% dengan `is_archived = 1` | **PASS** |
| 7 | **UI Resilience** | Tampil "Foto telah diarsipkan" & "Arsip: Mei 2025" tanpa tag `<img>` 404 | **PASS** |
| 8 | **Isolasi Foto Profil** | Foto profil karyawan tidak ikut masuk ZIP dan tetap utuh | **PASS** |
| 9 | **Isolasi Bukti SID** | Dokumen surat dokter (SID) tidak ikut masuk ZIP dan tetap utuh | **PASS** |
| 10 | **Failure Rollback** | Saat pembuatan arsip disimulasikan gagal, seluruh berkas sumber tetap utuh | **PASS** |
| 11 | **Idempotency** | Eksekusi berulang tidak menduplikasi atau merusak arsip yang sudah ada | **PASS** |
| 12 | **No Eligible Months** | Jika seluruh data masih dalam hot period, command exit normal (code 0) | **PASS** |
| 13 | **Security & Traversal** | Admin download valid, akses traversal (`../../`) diblokir 400 | **PASS** |

---

## 13. Final Verdict

### Status: **READY FOR LOW-COST LONG-TERM STORAGE**

1. **Client Sensitif Biaya Terpenuhi**: Nol biaya langganan cloud / S3 / VPS tambahan.
2. **Shared Hosting Friendly**: Maksimal 1 bulan per run, memory footprint rendah, aman dari CPU/IO throttling cPanel.
3. **Zero Data Loss**: Berkas foto lama dipindahkan secara terverifikasi ke private ZIP bulanan, row database presensi utuh selamanya.
4. **Maintenance Otomatis**: Berjalan mandiri via Laravel scheduler dan cPanel Cron Jobs standar tanpa memerlukan akses terminal SSH interaktif.
