# Panduan Lengkap Preset Sistem & Manajemen Modul (Developer Guide)

Dokumentasi resmi arsitektur preset konfigurasi klien untuk developer, teknisi deployment, dan pengelola sistem Presence.

---

## 1. Konsep Dasar & Arsitektur Preset

Preset sistem adalah konfigurasi siap pakai yang menyesuaikan modul aktif, hak akses, dan parameter bisnis aplikasi sesuai dengan model industri klien (F&B, Retail, Korporasi, Agensi Jasa, atau Tim Remote).

### Prinsip Utama Sistem Preset:
1. **Isolasi Penuh dari Klien**: Matriks preset dan pergantian paket hanya dapat dijalankan oleh developer melalui terminal (CLI). Antarmuka admin klien tidak menampilkan pengaturan matriks internal ini.
2. **Zero Data Loss (100% Aman)**: Saat beralih antar-preset, tidak ada data historis yang dihapus. Data absensi lama, slip gaji, riwayat persetujuan, dan transaksi keuangan tetap tersimpan aman di database meskipun modul terkait dinonaktifkan.
3. **Sinkronisasi Otomatis**: Sekali perintah dijalankan, sistem otomatis menyelaraskan database modul (`module_features`), izin navigasi sidebar, form pengaturan umum (`pengaturan_umum`), kebijakan presensi (`attendance_policies`), dan membersihkan seluruh cache terkait.

---

## 2. Daftar Preset Standar (Preset A sampai E)

Berikut adalah 5 konfigurasi standar yang dirancang untuk berbagai jenis industri:

### Preset A: Small Cafe & F&B
- **Sasaran Bisnis**: Kafe, restoran, kedai kopi, franchise F&B (1 sampai 2 cabang).
- **Karakteristik**: Jam kerja shift bergantian, mobilitas staf tinggi, verifikasi ketat anti-titip absen, tidak memerlukan modul penggajian rumit.
- **Modul Aktif**:
  - Presensi Harian & Live Tracking GPS
  - Deteksi Wajah Kamera (Face Recognition AI)
  - Roster Jam Kerja Shift & Jadwal Khusus
  - Pengajuan Cuti & Izin Staf
  - Pengumuman Internal
  - Laporan Presensi Multi-Cabang
- **Modul Nonaktif**: Payroll, Lembur Depnaker, Pinjaman/Kasbon, Reimbursement, Kontrak, Dokumen, Rekrutmen, Onboarding.
- **Pengaturan Bawaan**:
  - `attendance_require_gps`: Aktif
  - `attendance_require_face`: Aktif
  - `attendance_radius_meters`: 50 meter
  - `company_type`: Food & Beverage

---

### Preset B: Corporate Office
- **Sasaran Bisnis**: Kantor korporat, agensi, konsultan, institusi formal (5 hari kerja Senin sampai Jumat).
- **Karakteristik**: Jam kerja teratur, tidak memerlukan pemindaian wajah kamera, fokus pada kepatuhan penggajian ketenagakerjaan resmi.
- **Modul Aktif**:
  - Presensi GPS (Radius Kantor)
  - Cuti Tahunan & Izin
  - Modul Penggajian Lengkap (Payroll)
  - Perhitungan PPh 21 TER (Kategori A, B, C)
  - Pemotongan BPJS Kesehatan & Ketenagakerjaan
  - Perhitungan THR Keagamaan & Slip Gaji PDF
  - Pengelolaan Dokumen Kepegawaian
- **Modul Nonaktif**: Face Recognition AI, Lembur Depnaker, Kasbon, Reimbursement, Rekrutmen.
- **Pengaturan Bawaan**:
  - `attendance_require_gps`: Aktif
  - `attendance_require_face`: Nonaktif (0)
  - `work_days_per_week`: 5 hari
  - `company_type`: Corporate / Agency

---

### Preset C: Retail & Chain Stores
- **Sasaran Bisnis**: Toko ritel, minimarket, butik, jaringan gerai cabang.
- **Karakteristik**: Banyak cabang, jam buka akhir pekan (weekend), rotasi hari libur staf di hari biasa, lembur operasional tinggi.
- **Modul Aktif**:
  - Presensi Multi-Cabang & Roster Fleksibel
  - Hari Libur Operasional & Tanggal Merah
  - Lembur Depnaker (Pengali 1.5x, 2x, 3x, 4x sesuai PP 35/2021)
  - Modul Penggajian (Payroll) terintegrasi lembur
  - Pengajuan Cuti & Izin
  - Laporan Rekapitulasi Multi-Outlet
- **Modul Nonaktif**: Face Recognition AI, Pinjaman/Kasbon, Reimbursement, Penilaian Kinerja.
- **Pengaturan Bawaan**:
  - `attendance_require_gps`: Aktif
  - `attendance_require_face`: Nonaktif (0)
  - `overtime_depnaker_rules`: Aktif (1)
  - `company_type`: Retail / Chain Stores

---

### Preset D: Professional Service Company
- **Sasaran Bisnis**: Kantor jasa, agensi digital, konsultan hukum, studio kreatif.
- **Karakteristik**: Staf sering mengeluarkan biaya operasional pribadi untuk keperluan klien/lapangan yang perlu diganti (reimbursement).
- **Modul Aktif**:
  - Presensi GPS Standar
  - Pengajuan Cuti & Izin
  - Klaim Reimbursement (dengan upload foto nota/kuitansi)
  - Modul Penggajian (Payroll) terintegrasi klaim
  - Dokumen & Kontrak Kerja
- **Modul Nonaktif**: Face Recognition AI, Lembur Depnaker, Pinjaman/Kasbon, Rekrutmen.
- **Pengaturan Bawaan**:
  - `attendance_require_gps`: Aktif
  - `attendance_require_face`: Nonaktif (0)
  - `company_type`: Professional Services

---

### Preset E: Remote / HR Core (No Attendance)
- **Sasaran Bisnis**: Startup teknologi, tim kerja remote murni, software house tanpa kantor fisik.
- **Karakteristik**: Tidak ada presensi harian atau GPS sama sekali. Aplikasi berfungsi murni sebagai sistem HRIS pengelolaan talenta dan gaji.
- **Modul Aktif**:
  - Kontrak Kerja (PKWT / PKWTT)
  - Penilaian Kinerja Karyawan (KPI & Performance Review)
  - Manajemen Dokumen Digital (KTP, NPWP, Portofolio)
  - Modul Penggajian Mandiri (Payroll)
  - Pengumuman & Kebijakan Perusahaan
- **Modul Nonaktif**: Presensi GPS (OFF total), Jam Kerja Shift, Tracking GPS, Biometrik Wajah, Lembur.
- **Pengaturan Bawaan**:
  - `attendance_enabled`: Nonaktif (0)
  - `attendance_require_gps`: Nonaktif (0)
  - `attendance_require_face`: Nonaktif (0)
  - `company_type`: Tech Startup / Remote

---

## 3. Cara Mengganti Preset (Perintah Developer CLI)

Gunakan perintah bawaan artisan yang telah disediakan di root proyek.

### Perintah Langsung (Satu Baris)

Buka terminal dan jalankan perintah sesuai preset target:

```bash
# Terapkan Preset A (Small Cafe & F&B)
php artisan presence:preset A --yes

# Terapkan Preset B (Corporate Office)
php artisan presence:preset B --yes

# Terapkan Preset C (Retail & Chain Stores)
php artisan presence:preset C --yes

# Terapkan Preset D (Professional Services)
php artisan presence:preset D --yes

# Terapkan Preset E (Remote / HR Core)
php artisan presence:preset E --yes
```

> Tambahkan flag `--yes` untuk langsung mengeksekusi tanpa pertanyaan konfirmasi interaktif.

---

### Perintah Pilihan Interaktif

Jika ingin melihat opsi pilihan menu langsung di terminal:

```bash
php artisan presence:preset
```
Terminal akan menampilkan daftar preset dan meminta Anda memilih:
```
DAFTAR PRESET STANDAR YANG TERSEDIA:
------------------------------------------------------------------------
  [A] Preset A: Small Cafe & F&B (Cafe / Resto)
  [B] Preset B: Corporate Office (Kantor / Korporasi)
  [C] Preset C: Retail & Chain Stores (Retail / Toko)
  [D] Preset D: Professional Service Company (Jasa / Konsultan)
  [E] Preset E: HR-Only / Remote Team (No Attendance) (Remote / HR Core)
------------------------------------------------------------------------

Pilih preset yang ingin diterapkan [A]:
 > 
```

---

### Menampilkan Informasi Daftar Preset

Untuk mengecek rincian preset tanpa mengubah konfigurasi sistem:

```bash
php artisan presence:preset --list
```

---

## 4. Perintah Tingkat Lanjut (Paket Deployment Klien)

Jika membutuhkan konfigurasi berbasis paket lisensi formal atau menambahkan modul spesifik (add-on) di luar preset standar, gunakan perintah:

```bash
php artisan presence:package <KODE_PAKET> [opsi]
```

### Tabel Pemetaan Paket ke Preset Bawaan

| Kode Paket | Kategori | Preset Otomatis | Modul Utama |
|---|---|---|---|
| `FNB_SMALL` | Food & Beverage | Preset A | Presensi, GPS, Wajah, Cuti |
| `FNB_STANDARD` | Food & Beverage | Preset A | Presensi, GPS, Wajah, Cuti, Roster |
| `FNB_PRO` | Food & Beverage | Preset A | Presensi, GPS, Wajah, Cuti, Multi-Cabang |
| `OFFICE_STANDARD` | Korporasi | Preset B | Presensi, Cuti, Payroll, PPh 21, BPJS |
| `RETAIL_SMALL` | Ritel & Toko | Preset C | Presensi, Roster, Lembur, Payroll |
| `FULL_HR` | Enterprise | Preset E / Full | Seluruh modul HR, Payroll, Keuangan aktif |

### Contoh Kasus Khusus:
1. **Melihat simulasi perubahan paket (Dry Run):**
   ```bash
   php artisan presence:package FNB_SMALL --preview
   ```

2. **Menerapkan paket kafe dengan tambahan modul pinjaman/kasbon:**
   ```bash
   php artisan presence:package FNB_SMALL --addons=loans --yes
   ```

3. **Mengecek status paket yang sedang aktif saat ini:**
   ```bash
   php artisan presence:package-info
   ```

---

## 5. Yang Terjadi di Balik Layar Saat Preset Berubah

Saat perintah `presence:preset` dijalankan, sistem melakukan tahapan transaksi berikut secara atomik:

```
[CLI Command] php artisan presence:preset B
      │
      ▼
1. Resolve Mapping Paket Dasar (OFFICE_STANDARD)
      │
      ▼
2. Sinkronisasi Entitlement (module_features -> is_entitled & is_enabled)
      │
      ▼
3. Update Parameter Bisnis (CompanySetting & AttendancePolicy)
      │
      ▼
4. Catat Jejak Audit (audit_logs & deployment_package_history)
      │
      ▼
5. Pembersihan Cache Global (Module Features, Policy, General Setting, Menu)
      │
      ▼
[Selesai] Sistem langsung berjalan dengan konfigurasi baru tanpa restart server
```

---

## 6. Troubleshooting & Pertanyaan Umum Developer

### Q1: Apakah menu di sidebar admin langsung berubah setelah ganti preset?
**Ya.** Navigasi sidebar dikendalikan secara dinamis melalui helper `module_enabled($moduleCode)`. Saat modul dinonaktifkan, link menu terkait otomatis hilang dari tampilan admin.

### Q2: Mengapa tampilan peramban (browser) belum berubah setelah menjalankan perintah?
Lakukan hard refresh di peramban (`Ctrl + F5` atau `Cmd + Shift + R`) untuk memastikan cache HTML dan JavaScript lokal di browser terbarui. Jika perlu, jalankan pembersihan cache manual:
```bash
php artisan optimize:clear
```

### Q3: Bagaimana cara memastikan preset mana yang sedang aktif saat ini di database?
Buka terminal dan jalankan:
```bash
php artisan tinker --execute="echo Cache::get('active_client_preset_code', 'Belum diset');"
```
Atau jalankan:
```bash
php artisan presence:package-info
```

### Q4: Apakah aman berganti preset saat aplikasi sudah digunakan oleh klien?
**Sangat aman.** Struktur database dirancang menggunakan relasi longgar (loosely coupled). Penonaktifan modul hanya menyembunyikan akses antarmuka dan memblokir middleware terkait, tanpa menghapus satu baris pun data tabel absensi, pegawai, izin, atau penggajian yang sudah ada.
