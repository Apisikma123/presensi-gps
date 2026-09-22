# Panduan Deployment Staging & Production (HR Coffee Shop 2 Cabang)

Dokumen ini berisi panduan deployment resmi untuk sistem Presensi GPS & Biometrik Wajah ke server staging atau production (cPanel / VPS / Cloud Server).

---

## 1. Persyaratan Server (System Requirements)

- **PHP Version**: PHP 8.2 atau 8.3 (Rekomendasi: PHP 8.3)
- **PHP Extensions**:
  - `BCMath`, `Ctype`, `cURL`, `DOM`, `Fileinfo`, `Filter`, `GD` / `Imagick`, `Hash`, `Mbstring`, `OpenSSL`, `PCRE`, `PDO`, `PDO_MySQL`, `Session`, `Tokenizer`, `XML`, `ZIP`
- **Database**: MySQL 8.0+ atau MariaDB 10.5+
- **Web Server**: Apache / Nginx / LiteSpeed dengan modul `mod_rewrite` aktif
- **Memory Limit**: Minimal `256M` (Rekomendasi: `512M` untuk pemrosesan export laporan besar)
- **Max Execution Time**: Minimal `60` detik
- **Upload Max Filesize**: Minimal `10M`

---

## 2. Persiapan File & Upload ke Server

1. **Build Frontend di Komputer Lokal**:
   ```bash
   npm run build
   ```
   Pastikan folder `public/build/` terisi hasil kompilasi aset (`manifest.json`, CSS, dan JS).
   > **PENTING**: Jangan pernah menjalankan `npm run dev` di server production.

2. **Arsipkan File Project**:
   Kompres file project ke format `.zip`, **kecualikan**:
   - `node_modules/`
   - `.env` (lokal)
   - `.git/`
   - `storage/logs/*.log`
   - `storage/framework/cache/data/*`
   - `storage/framework/sessions/*`
   - `storage/framework/views/*`

3. **Upload ke Server**:
   - Ekstrak file zip ke direktori root aplikasi (misal: `/home/<user>/presensi/` di luar `public_html`, atau langsung di root jika menggunakan document root khusus).
   - Arahkan Document Root domain ke folder `public/`.

---

## 3. Konfigurasi Environment (`.env`)

Salin file `.env.example` menjadi `.env` di server:
```bash
cp .env.example .env
```

Atur variabel environment berikut:
```env
APP_NAME="HR Coffee Shop"
APP_ENV=production
APP_KEY=base64:... (generate via artisan jika belum ada)
APP_DEBUG=false
APP_URL=https://nama-domain-anda.com

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=user_database_anda
DB_PASSWORD=password_database_anda

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Storage Disk
FILESYSTEM_DISK=public
```

---

## 4. Install Dependencies (Composer)

Jalankan perintah berikut di direktori root project:
```bash
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
```

---

## 5. Menjalankan Perintah Laravel Production

Jalankan rangkaian perintah berikut secara berurutan:

```bash
# 1. Jalankan migrasi database
php artisan migrate --force

# 2. Buat symlink storage ke folder public
php artisan storage:link

# 3. Optimasi dan caching konfigurasi, route, dan view
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 6. Pengaturan Cron Job / Task Scheduler

Sistem membutuhkan Laravel Scheduler untuk menjalankan otomatisasi (seperti Auto-Alpha presensi karyawan yang tidak hadir).

Tambahkan baris berikut pada crontab server (setiap 1 menit):
```cron
* * * * * cd /path/ke/project/presensi && php artisan schedule:run >> /dev/null 2>&1
```
*(Ganti `/path/ke/project/presensi` dengan path absolut direktori project di server Anda)*.

---

## 7. Izin Akses Folder (Permissions)

Pastikan web server memiliki akses tulis (writable) pada direktori storage dan cache:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```
*(Sesuaikan user web server, misal `nobody` pada LiteSpeed/cPanel)*.

---

## 8. Verifikasi Pasca Deployment (Smoke Testing)

Lakukan pengujian cepat di browser setelah deployment:
1. **Halaman Login**: Buka `https://nama-domain-anda.com`, pastikan halaman login termuat rapi dengan aset CSS/JS termuat sempurna.
2. **Login Admin**: Masuk dengan akun admin, verifikasi Dashboard menampilkan statistik kehadiran cabang.
3. **Monitoring Presensi**: Buka menu Monitoring Presensi, pastikan filter tanggal dan cabang berfungsi.
4. **Login Karyawan / Mobile**: Buka tampilan mobile, verifikasi tombol presensi, kamera/face recognition, dan GPS geolocation.
5. **Cetak Laporan**: Coba cetak laporan presensi (PDF/Excel) untuk memastikan ekstensi GD/Zip berjalan normal.

---

## 9. Prosedur Rollback Dasar

Jika terjadi kendala saat proses update/deploy:

1. **Rollback Database**:
   - Selalu lakukan backup snapshot database (`mysqldump`) sebelum menjalankan `php artisan migrate --force`.
   - Jika migrasi bermasalah, restore database dari file backup:
     ```bash
     mysql -u <username> -p <database_name> < backup_sebelum_deploy.sql
     ```
2. **Rollback Aplikasi**:
   - Kembalikan ke release file sebelumnya.
   - Bersihkan cache:
     ```bash
     php artisan optimize:clear
     php artisan config:cache
     php artisan route:cache
     php artisan view:cache
     ```
