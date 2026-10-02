# Load Testing Guide

Sederhana dan langsung menggunakan [Artillery](https://www.artillery.io).

## Prasyarat
- Node.js 18+
- Set target URL pada environment:
  ```powershell
  $env:TARGET_URL = "http://localhost:8000"
  ```

## Skenario yang Tersedia
1. **Normal Baseline (`scenarios/normal.yaml`)**
   Simulasi aktivitas browsing dashboard dan status presensi harian (10–50 concurrent users).
   ```bash
   npx artillery run tests/load/scenarios/normal.yaml
   ```

2. **Attendance Spike (`scenarios/attendance.yaml`)**
   Simulasi lonjakan presensi pagi hari (50–100 concurrent users).
   ```bash
   npx artillery run tests/load/scenarios/attendance.yaml
   ```

> **Catatan:** Jangan simpan file hasil benchmark (`*.json`, `*.html`, `*.csv`) di dalam repository Git.
