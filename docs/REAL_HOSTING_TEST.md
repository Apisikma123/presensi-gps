# Panduan Real Load Testing Eksternal (Rumahweb Shared Hosting - 2 Cabang)

Dokumen ini merupakan panduan praktis untuk melakukan **Real Concurrency Load Test** setelah project di-deploy ke hosting Rumahweb dan domain/SSL aktif.

> **PENTING**:
> 1. Pengujian beban **HARUS** dijalankan dari laptop / PC eksternal melalui koneksi internet publik, **BUKAN** dari dalam terminal server hosting itu sendiri.
> 2. Pada paket shared hosting (seperti Rumahweb Unlimited S), **JANGAN** menguji dengan concurrency > 20 agar akun tidak terkena auto-suspend CloudLinux (HTTP 508 Resource Limit Reached).

---

## 1. Profil Beban Target Coffee Shop (2 Cabang)

- **Total Cabang**: 2 Cabang (Cabang A & Cabang B).
- **Total Karyawan**: ±30 Karyawan (~15 karyawan per cabang).
- **Skenario Peak Beban Masuk (07:45 - 08:15 WIB)**:
  - **Cabang A**: 5 karyawan clock-in hampir bersamaan.
  - **Cabang B**: 5 karyawan clock-in hampir bersamaan.
  - **Admin HR / Owner**: 1 request monitoring dashboard presensi.
  - **Total Concurrency Target**: **~11 simultaneous requests**.

---

## 2. Tahapan Pengujian Bertingkat (Stepped Concurrency)

Gunakan tool HTTP benchmarking modern seperti **k6**, **autocannon**, atau **Apache Bench (`ab`)** dari laptop penguji:

### Tahap 1: Baseline Single Request (Concurrency 1)
Mengukur latency dasar jaringan dan hosting:
```bash
ab -n 50 -c 1 https://domain-anda.com/
```

### Tahap 2: Beban Ringan (Concurrency 5)
Mengukur stabilitas PHP-FPM saat 5 user membuka halaman login:
```bash
ab -n 100 -c 5 https://domain-anda.com/
```

### Tahap 3: Beban Target Nyata (Concurrency 11)
Mensimulasikan jam puncak coffee shop (10 barista/kasir + 1 admin):
```bash
ab -n 150 -c 11 https://domain-anda.com/
```

### Tahap 4: Beban Maksimal Shared Hosting (Concurrency 15 - 20)
Menguji batas atas Entry Process (EP) CloudLinux:
```bash
ab -n 200 -c 15 https://domain-anda.com/
```

---

## 3. Rekomendasi Endpoint yang Diuji

### A. Non-Destructive Endpoints (Aman Dijalankan Kapan Saja)
1. **Halaman Login**: `GET /login` (menguji rendering Blade, static assets, session generation).
2. **Dashboard Read-Only**: `GET /dashboard` (menguji koneksi database, query agregasi status hari ini, cache query).
3. **Riwayat Presensi**: `GET /presensi/histori` (menguji pagination dan query filter karyawan).
4. **Pemeriksaan Update API**: `GET /api/update/version` (menguji overhead framework Sanctum/API).

### B. Clock-In Presensi (Gunakan Akun Khusus Testing)
> **PERINGATAN**: JANGAN melakukan spam POST presensi menggunakan NIK karyawan asli karena akan mencemari data absensi operasional.

Gunakan akun khusus staging/demo yang sudah disiapkan untuk menguji POST `/presensi/store`:
- Pastikan payload menyertakan koordinat GPS valid dan gambar webcam dummy.
- Uji maksimal 5–10 request berulang dengan jeda yang wajar.

---

## 4. Metrik yang Wajib Dicatat

Pada setiap pengujian concurrency, catat metrik berikut:

| Metrik | Target Optimal | Batas Toleransi |
|---|---|---|
| **Average Response Time** | < 800 ms | < 2.0 s |
| **P50 (Median)** | < 600 ms | < 1.5 s |
| **P95** | < 1.5 s | < 3.0 s |
| **P99** | < 2.5 s | < 4.5 s |
| **Error Rate** | 0.0% | < 1.0% |
| **HTTP 429 (Rate Limit)** | 0 | Wajar jika brute-force throttle login aktif |
| **HTTP 500 (Internal Server Error)** | 0 | Investigasi file `storage/logs/laravel.log` |
| **HTTP 503 (Service Unavailable)** | 0 | Indikasi web server kehabisan worker |
| **HTTP 508 (Resource Limit Reached)**| 0 | CloudLinux EP / Memory hosting terlampaui |
| **Timeouts** | 0 | Latency melebihi batas request |

---

## 5. Monitoring cPanel Secara Bersamaan

Saat pengujian eksternal sedang berjalan:
1. Buka **cPanel** > menu **Resource Usage** (atau **Metrics** > **CPU and Concurrent Connection Usage**).
2. Perhatikan grafik:
   - **CPU Usage**: Apakah menyentuh 100% (garis merah)?
   - **Physical Memory (RAM)**: Apakah mendekati limit RAM paket?
   - **Entry Processes (EP)**: Berapa puncak EP saat 11 request masuk? Jika EP limit paket 20 dan terpakai 11–14, hosting aman. Jika menyentuh 20, request berikutnya akan mendapat HTTP 508.
   - **I/O Usage**: Apakah disk I/O tercekik (menghambat query database)?

---

## 6. Contoh Script Pengujian Otomatis Menggunakan k6

Buat file `loadtest.js` di laptop lokal:

```javascript
import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  stages: [
    { duration: '30s', target: 5 },  // Ramp-up 5 users
    { duration: '1m', target: 11 },  // Peak 11 users (target 2 cabang)
    { duration: '30s', target: 15 }, // Stress test 15 users
    { duration: '30s', target: 0 },  // Ramp-down
  ],
  thresholds: {
    http_req_duration: ['p(95)<3000'], // 95% request harus di bawah 3 detik
    http_req_failed: ['rate<0.01'],    // Error rate di bawah 1%
  },
};

export default function () {
  const res = http.get('https://domain-anda.com/');
  check(res, {
    'status is 200': (r) => r.status === 200,
  });
  sleep(1);
}
```

Jalankan:
```bash
k6 run loadtest.js
```
