# 🏢 HR Presence - Smart Attendance & HR Management System

Sistem Manajemen Kehadiran & SDM Modern berbasis Web & Mobile dengan fitur **AI Face Recognition (Pengenalan Wajah)**, **GPS Geofencing Radius Lokasi**, **Manajemen Shift Kerja**, **Pengajuan Izin/Sakit/Cuti**, serta **Laporan Otomatis**.

---

## 🌟 Fitur Utama (Key Features)

- 📸 **AI Face Recognition**: Deteksi dan verifikasi sidik wajah otomatis menggunakan *Face-API.js (TinyFaceDetector)* dengan validasi posisi tengah lingkaran & anti-kecurangan (*strict threshold*).
- 📍 **GPS Geofencing Multi-Cabang**: Presensi berbasis koordinat GPS dan radius presisi menggunakan *Leaflet JS* & *OpenStreetMap*.
- 👥 **Manajemen Organisasi & Karyawan**: Pengelolaan data Master Karyawan, Departemen, Jabatan, dan Multi-Cabang kantor.
- ⏰ **Jadwal & Shift Jam Kerja**: Konfigurasi jam kerja harian, mingguan, per departemen, hingga jadwal fleksibel/lintas hari.
- 📑 **Pengajuan Izin, Cuti & Sakit**: Alur persetujuan (*Approval Workflow*) dari pengajuan karyawan hingga approval HRD/Manager.
- 📊 **Laporan & Rekap Otomatis**: Rekap presensi harian, bulanan, statistik kehadiran, dan ekspor instan ke format **PDF** & **Excel**.
- 📱 **PWA & Mobile UI**: Tampilan antarmuka mobile responsif dengan *Bottom Navigation Bar* khas aplikasi smartphone.
- ⚡ **REST API Mobile Ready**: Backend controller API lengkap di `/api/mobile/` yang siap diintegrasikan dengan aplikasi Android/iOS Native (Flutter / React Native).

---

## 🛠️ Kebutuhan Sistem (Prerequisites)

Sebelum melakukan instalasi, pastikan lingkungan server lokal Anda telah terinstall:

1. **PHP**: Versi `8.1` atau `8.2` atau `8.3` (Disarankan menggunakan **Laragon** atau **XAMPP**)
   - Ekstensi PHP wajib aktif: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd`, `curl`, `json`
2. **Composer**: Versi `2.x`
3. **Database**: MySQL `5.7+` atau MariaDB `10.3+`
4. **Web Browser**: Google Chrome / Microsoft Edge / Safari modern dengan izin akses **Kamera & Lokasi (GPS)**.

---

## 🚀 Panduan Instalasi Langkah demi Langkah

Ikuti langkah-langkah berikut untuk menjalankan proyek di komputer/server lokal:

### 1. Clone Repository
```bash
git clone https://github.com/Apisikma123/presensi-gps.git
cd presensi-gps
```

### 2. Install Dependensi Composer
```bash
composer install
```

### 3. Konfigurasi Environment File (`.env`)
Salin file template `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Buka file `.env` dan sesuaikan koneksi database Anda (default Laragon/XAMPP):
```env
APP_NAME="HR Presence"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=presensigpsv2
DB_USERNAME=root
DB_PASSWORD=
```
*(Catatan: Buat database baru bernama `presensigpsv2` di phpMyAdmin / MySQL).*

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Buat Symlink Storage (Wajib untuk Foto & File Upload)
```bash
php artisan storage:link
```

### 6. Migrasi Database & Seeder Lengkap (Semua Data Siap Pakai)
Jalankan migrasi database beserta seeder data default dan seeder MVP:
```bash
php artisan migrate:fresh --seed
php artisan db:seed --class=MvpPresentationSeeder
```

### 7. Jalankan Server Lokal
```bash
php artisan serve
```
Aplikasi kini berjalan dan dapat diakses di browser:  
👉 **[http://localhost:8000](http://localhost:8000)**

---

## 🔑 Kredensial Akun Login (Ready to Use)

Semua akun di bawah ini menggunakan **Password Seragam**: **`123456`**

### 👑 1. Akun Panel Administrator
URL Login: **[http://localhost:8000/login](http://localhost:8000/login)**

| No | Nama Akun | Username | Password | Role / Hak Akses |
|:---:|:---|:---|:---|:---|
| 1 | **Development MVP (Super Admin)** | `admin` | `123456` | Akses Penuh Seluruh Menu & Cabang |

---

### 📱 2. Akun Karyawan (Mobile Presensi)
URL Login / Dashboard Karyawan: **[http://localhost:8000/dashboard](http://localhost:8000/dashboard)**

| NIK | Nama Karyawan | Departemen | Lokasi Cabang | Password |
|:---:|:---|:---|:---|:---|
| **`1001`** | **Ahmad Rizki** *(IT Manager)* | Teknologi Informasi | Medan | `123456` |
| **`1002`** | **Siti Nurhaliza** *(HR Supervisor)* | Human Resources | Medan | `123456` |

---

## 🏢 Konfigurasi Cabang & Lokasi Default

- **Nama Cabang**: Kantor Cabang Medan (Sumatera Utara)
- **Kode Cabang**: `MDN`
- **Koordinat GPS**: `3.5951956, 98.6722227` *(Kota Medan)*
- **Radius Lokasi**: `500.000` Meter (500 KM) ➔ Mencakup seluruh wilayah Sumatera Utara untuk memudahkan demo dan uji coba presensi tanpa kendala di luar radius.

---

## 💡 Troubleshooting & Tips

1. **Foto Wajah / Avatar Tidak Muncul (404 Not Found)**:
   Pastikan symlink storage telah dibuat dengan menjalankan:
   ```bash
   php artisan storage:link
   ```
2. **Kamera Tidak Mau Terbuka di HP**:
   Browser mobile mewajibkan koneksi **HTTPS** atau **localhost** untuk mengizinkan akses kamera & geolocation. Jika dibuka dari HP melalui IP lokal (misal: `http://192.168.x.x:8000`), aktifkan izin kamera & GPS di pengaturan situs Chrome/Safari.
3. **Membersihkan Cache Aplikasi**:
   Jika terjadi perubahan konfigurasi atau tampilan tidak terupdate:
   ```bash
   php artisan optimize:clear
   ```

---

## 📄 Lisensi
Sistem ini dikembangkan untuk kebutuhan internal manajemen SDM dan presensi karyawan.