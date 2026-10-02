# Panduan Praktis Pindah Preset Sistem

Panduan ini menjelaskan cara mengganti preset sistem presensi dengan bahasa yang sederhana dan langkah yang runtut.

---

## Apa Itu Preset?

Preset adalah **paket pengaturan siap pakai**. 

Ibarat memilih mode di ponsel (mode hening, mode getar, mode normal), preset mengatur **menu apa saja yang muncul di aplikasi** agar sesuai dengan jenis bisnis Anda tanpa perlu mengubah kode program:

- **Kafe / Resto**: Butuh absensi shift, GPS, dan foto selfie wajah. Tidak butuh slip gaji rumit.
- **Kantor**: Butuh slip gaji resmi (PPh 21 & BPJS), absensi Senin–Jumat, tapi tidak wajib foto selfie setiap masuk.
- **Toko Ritel**: Butuh hitungan lembur otomatis dan jadwal masuk di akhir pekan.
- **Jasa / Agensi**: Butuh fitur klaim nota bensin / dinas (*reimbursement*).
- **Tim Remote**: Karyawan kerja dari rumah, jadi menu absensi GPS dimatikan total dan aplikasi hanya dipakai untuk kelola kontrak dan gaji.

---

## Pilih Cara yang Paling Mudah untuk Anda

Ada 2 cara untuk pindah preset:
1. **Cara 1: Lewat Web Browser** (Paling mudah, cukup klik-klik mouse, cocok untuk siapa saja).
2. **Cara 2: Lewat Terminal / Command Prompt** (Cepat, cukup ketik 1 baris perintah).

---

## Cara 1: Lewat Web Browser (Tanpa Koding)

Gunakan cara ini jika Anda lebih nyaman menggunakan tampilan visual di browser daripada mengetik perintah di terminal.

### Langkah 1: Buka File `.env`
1. Buka file `.env` di folder utama aplikasi (bisa lewat cPanel File Manager atau teks editor seperti VS Code).
2. Cari dua baris berikut:
   ```env
   PRESENCE_VENDOR_SETUP_ENABLED=true
   PRESENCE_VENDOR_KEY=260902
   ```
3. Pastikan nilainya `true`. Angka `260902` adalah PIN rahasia Anda (bisa diganti sesuai keinginan).
4. Simpan file `.env`.

### Langkah 2: Buka Halaman Pengaturan di Browser
1. Buka Google Chrome atau browser lain.
2. Ketik alamat website Anda ditambah `/vendor/deployment-setup`, contoh:
   ```
   http://127.0.0.1:8000/vendor/deployment-setup
   ```
   *(Ganti `127.0.0.1:8000` dengan nama domain website Anda jika sudah online)*.
3. Halaman login developer akan muncul. Masukkan PIN: **`260902`**, lalu klik **Masuk**.

### Langkah 3: Pilih Preset yang Diinginkan
1. Pada menu pilihan paket/preset, pilih salah satu:
   - **Preset A**: Kafe, Resto, Kedai Kopi
   - **Preset B**: Kantor Perusahaan
   - **Preset C**: Toko Ritel & Minimarket
   - **Preset D**: Perusahaan Jasa & Konsultan
   - **Preset E**: Tim Remote / HR Murni
2. Klik tombol **"Simulasi / Preview Dampak"**.
   Sistem akan menampilkan daftar menu: mana yang akan menyala dan mana yang akan dimatikan.
3. Klik tombol **"Terapkan & Kunci Deployment"**.
4. Selesai. Preset baru Anda sudah aktif.

### Langkah 4: Kunci Kembali File `.env` (Penting)
Setelah selesai, buka kembali file `.env` dan ubah:
```env
PRESENCE_VENDOR_SETUP_ENABLED=false
```
Langkah ini penting agar orang lain di internet tidak bisa membuka halaman pengaturan tersebut.

---

## Cara 2: Lewat Terminal / CMD (Menu Interaktif & Dropdown)

Gunakan cara ini jika Anda membuka terminal di VS Code, PowerShell, atau Command Prompt (CMD).

Cukup ketik perintah ini dan tekan **Enter**:
```bash
php artisan presence:preset
```

Nanti di terminal akan **muncul menu pilihan (dropdown interaktif)** lengkap dengan nama preset, kategori bisnis, dan keterangannya:
- Gunakan tombol **panah atas / bawah (↑ / ↓)** pada keyboard untuk memilih preset yang diinginkan (A, B, C, D, atau E).
- Tekan **Enter** untuk memilih.
- Sistem akan menampilkan ringkasan perubahan dan menanyakan konfirmasi, pilih **Ya** lalu Enter. Selesai!

---

### Ingin Langsung Terapkan Tanpa Menu? (Shortcut 1 Baris)
Jika Anda sudah tahu preset yang diinginkan dan ingin langsung jalan:

#### Untuk Kafe, Restoran, atau Kedai Kopi:
```bash
php artisan presence:preset A --yes
```

### Untuk Kantor Formal (Senin–Jumat):
```bash
php artisan presence:preset B --yes
```

### Untuk Toko Ritel atau Minimarket (Shift & Lembur):
```bash
php artisan presence:preset C --yes
```

### Untuk Perusahaan Jasa atau Agensi (Klaim Reimbursement):
```bash
php artisan presence:preset D --yes
```

### Untuk Tim Remote / Tanpa Kantor Fisik:
```bash
php artisan presence:preset E --yes
```

### Untuk Enterprise / SEMUA Fitur Aktif (FULL HR & Presensi):
```bash
php artisan presence:preset FULL --yes
```

> **Penjelasan Perintah**:
> - `presence:preset A` artinya terapkan Preset A.
> - `presence:preset FULL` (atau `presence:preset F`) artinya menyalakan seluruh modul tanpa ada yang dimatikan.
> - Tambahan `--yes` berguna agar sistem langsung mengeksekusi tanpa meminta konfirmasi ulang.

---

## Penjelasan Detail: Mana Preset yang Cocok untuk Saya?

| Preset | Nama Bisnis | Fitur yang Dinyalakan | Fitur yang Dimatikan |
|---|---|---|---|
| **A** | **Kafe & F&B** | Absen selfie kamera wajah, GPS radius gerai, tukar shift, cuti/izin. | Slip gaji, lembur, kasbon. |
| **B** | **Kantor** | Absen GPS, slip gaji lengkap (PPh 21 & BPJS), cuti, arsip dokumen. | Selfie wajah dimatikan agar absen masuk tidak antre lama. |
| **C** | **Toko Ritel** | Absen GPS, roster shift akhir pekan, hitungan lembur Depnaker, slip gaji. | Selfie wajah, kasbon. |
| **D** | **Jasa / Agensi** | Absen GPS, klaim nota/biaya dinas (*reimbursement*), slip gaji, cuti. | Selfie wajah, lembur pabrik. |
| **E** | **Tim Remote** | Kontrak kerja, penilaian KPI kerja, slip gaji, dokumen karyawan. | **Absensi & GPS dimatikan total**. Aplikasi menjadi HRIS murni. |
| **FULL** | **Enterprise / Lengkap** | **SEMUA MODUL AKTIF**: Absen GPS, Wajah, Lembur, Payroll BPJS/PPh21, Kasbon, Reimbursement, Dokumen, HR. | **Tidak ada yang dimatikan** (100% fitur menyala). |

---

## Perintah Tambahan yang Sering Dipakai

### 1. Mengecek Preset yang Sedang Aktif Saat Ini
Ketik di terminal:
```bash
php artisan presence:package-info
```
Sistem akan menampilkan nama paket aktif dan daftar menu yang sedang menyala.

### 2. Melihat Ringkasan Semua Preset di Terminal
Ketik di terminal:
```bash
php artisan presence:preset --list
```

### 3. Menambah Fitur Tertentu di Luar Preset (Kustom)
Contoh: Anda memilih **Preset A (Kafe)**, tetapi bos ingin ada menu **Kasbon / Pinjaman Karyawan** (`loans`):
```bash
php artisan presence:package-change FNB_SMALL --addons=loans --yes
```
Menu kasbon akan langsung muncul di Preset A tanpa mengganggu fitur lainnya.

---

## Pertanyaan yang Sering Muncul (FAQ)

### 1. Apakah data absen atau data karyawan saya akan hilang kalau ganti preset?
**Jawab: 100% aman, data tidak akan pernah hilang.**  
Saat Anda mengganti preset, sistem hanya **menyembunyikan menu** dari layar, bukan menghapus datanya dari database.  
*Contoh:* Jika Anda pernah membuat slip gaji, lalu pindah ke Preset A (yang tidak ada slip gaji), data slip gaji lama tetap tersimpan rapi di database. Begitu Anda pindah lagi ke Preset B, seluruh data gaji lama akan langsung muncul kembali seperti semula.

### 2. Saya sudah jalankan perintahnya, tapi kok menu di layar belum berubah?
**Jawab:** Itu karena browser atau website masih menyimpan tampilan lama (cache).  
Lakukan 2 langkah cepat ini:
1. Ketik perintah pembersihan cache di terminal:
   ```bash
   php artisan optimize:clear
   ```
2. Di browser, tekan kombinasi tombol **`Ctrl + F5`** (Windows) atau **`Cmd + Shift + R`** (Mac) untuk memuat ulang halaman secara bersih.

### 3. Muncul pesan: "Profil deployment telah dikunci", bagaimana solusinya?
**Jawab:** Sistem otomatis mengunci pengaturan agar pengguna biasa tidak sembarangan mengubah paket.  
Untuk membuka kuncinya, buka file `.env`, ubah baris:
```env
PRESENCE_DEPLOYMENT_LOCKED=false
```
Atau cukup jalankan perintah pindah preset lagi lewat terminal—perintah terminal otomatis memiliki hak akses untuk memperbarui kunci.
