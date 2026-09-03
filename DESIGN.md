# Design System: Brew & Beam Enterprise (HRIS & Presensi Biometrik)

## 1. Visual Theme & Atmosphere
A high-precision, warm-editorial Enterprise Workforce & Coffee Shop Operations management system. The aesthetic blends **tactile Swiss-utilitarian grid discipline** with **warm roasted-bean minimalism** (Density: 6/10 "Daily App Balanced", Variance: 6/10 "Offset Asymmetric", Motion: 7/10 "Fluid Spring Motion").

The interface eliminates generic bootstrap admin tropes and neon gradients in favor of **deep artisan espresso/emerald tones, tactile push surfaces, crisp tabular monospaced data, and frictionless mobile-first attendance gestures**.

---

## 2. Color Palette & Functional Roles
Calibrated 60-30-10 palette with strict WCAG AA+ contrast accessibility:

* **Espresso Emerald (Primary Brand / CTA):** `#1E4D3E` — Primary action buttons, active tab indicators, selected calendar dates, attendance check-in trigger.
* **Jade Roast (Primary Accent / Focus Ring):** `#32745E` — Hover states, active badges, focus ring outlines, biometric camera contour ring.
* **Canvas Crema (Body Background):** `#F8FAF8` — Soft, glare-reducing global page background for day & night shifts.
* **Pure Surface (Cards / Modals / Sheets):** `#FFFFFF` — Elevated cards, bottom navigation sheet, floating dialogs, data table rows.
* **Roasted Charcoal (Primary Typography):** `#0F172A` (Slate-900) — Primary headlines, employee names, KPI metrics, attendance timestamps.
* **Muted Graphite (Secondary Typography):** `#64748B` (Slate-500) — Department labels, subtitle metadata, input placeholders, timestamp descriptors.
* **Whisper Border (Structural Dividers):** `rgba(15, 23, 42, 0.08)` — 1px border lines, card separators, table cell borders.
* **Signal Amber (Warning / Pending Status):** `#D97706` — Pending approvals, late arrival warnings, overtime notices.
* **Signal Crimson (Danger / Rejected):** `#DC2626` — Rejected permits, GPS mock location alerts, out-of-radius violations.
* **Signal Matcha (Success / Approved):** `#059669` — Verified check-in, approved leave, verified biometric face match.

> **Banned Colors:** No pure black (`#000000`), no oversaturated cyan/blue neon, no purple glow drop-shadows.

---

## 3. Typographic Architecture
Clean sans-serif pairing with dedicated tabular monospacing for time tracking, GPS coordinates, and payroll figures:

* **Display & Section Headlines:** `Outfit` / `Geist` — Font-weight 700/800, tight tracking (`letter-spacing: -0.02em`), compact line-height (`1.15`), weight-driven hierarchy.
* **Interface & Body Text:** `Inter` / `Satoshi` — Font-weight 400/500/600, relaxed leading (`1.5`), max line-length 65 characters.
* **Tabular & Numerical Data:** `Geist Mono` / `JetBrains Mono` — Applied to time clocks (`07:59:12`), GPS coordinates (`-6.229728, 106.807464`), NIK employee codes (`BAR-001`), and rupiah salary formatting (`Rp 4.500.000`).
* **Banned Fonts:** Generic serif fonts (`Times New Roman`, `Georgia`, `Garamond`), decorative handwritten fonts, Comic Sans.

---

## 4. Key Screen Blueprints & Component Behaviors

### A. Mobile PWA Attendance & Biometric HUD (`/presensi/create`)
* **Camera Biometric Viewport:** Rounded rectangular viewfinder (`border-radius: 24px`) with dynamic facial bounding contour that glows Emerald (`#1E4D3E`) on face detection and Crimson (`#DC2626`) on face missing.
* **Live Telemetry Strip:** Bottom overlay containing mini GPS radar map (Leaflet 4:3 rounded overlay), coordinate watermark, speed anomaly detector, and distance to outlet radius meter.
* **Primary Dual Clock-in Actions:** Large tactile action button pair (Masuk & Pulang) with push-in feedback (`active: scale(0.97)`), 56px height, prominent icon pairing (`ion-icon`).

### B. Employee Daily Hub & Modern Bottom Navigation (`/dashboard`)
* **Greeting & Shift Card:** Asymmetric banner featuring employee avatar, active shift schedule pill badge (e.g. *Shift 1 Pagi*), live digital clock, and quick check-in indicator.
* **Bento Stat Grid:** 4-quadrant summary metrics (Hadir, Sakit, Cuti, Terlambat) with soft tinted background fills and tabular counters.
* **Quick Action Carousel:** Horizontal scrollable icon pills for Permits (Izin), Overtime (Lembur), Schedule Shift (Ajuan Jadwal), Reimbursement, and Loan (Pinjaman).
* **Floating Bottom Sheet Navigation:** Curved bottom navbar (`border-radius: 20px 20px 0 0`) with center-elevated biometric action button and subtle backdrop blur.

### C. Admin Web Operations & Approval Matrix (`/admin/*`)
* **Sidebar Layout:** Dark/Light adaptive sidebar with grouped hierarchical navigation (Master Data, Operasional Outlet, Laporan, Konfigurasi).
* **Data Table System:** Dense, freeze-column tables with alternating row hover states, status pills (Pending, Approved, Rejected), avatar stacked columns, and inline action icons.
* **Filter Bar:** Multi-select dropdowns (Outlet Cabang, Departemen Bar/Kitchen/Service, Tanggal Range), instant search debounce, and export buttons (Excel / PDF).

---

## 5. Component Styling Specifications

* **Buttons:**
  * Primary: `#1E4D3E` background, `#FFFFFF` bold text, `12px` rounded corners, zero outer glow, tactile `-1px` vertical translation on click.
  * Secondary / Ghost: Border `1px solid rgba(15,23,42,0.12)`, `#0F172A` text, hover background `rgba(30,77,62,0.06)`.
* **Cards & Bento Panels:**
  * Generous corner radius (`16px` on mobile, `20px` on desktop).
  * Subtle elevation: `box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.06)`.
  * Background `#FFFFFF` with 1px border `rgba(15, 23, 42, 0.06)`.
* **Inputs & Form Controls:**
  * Clear top-aligned labels with `13px` font weight 600.
  * Inputs have `12px` border radius, `#F8FAFC` background fill, transitioning to white with `#1E4D3E` focus ring on click.
  * Error state: Inline red text with icon under the field, red border outline.
* **Modal Sheets & Popups (SweetAlert2 & Datepickers):**
  * Bottom-anchored sheet on mobile (`border-radius: 24px 24px 0 0`), centered dialog on desktop (`border-radius: 20px`).
  * Backdrop filter blur (`backdrop-filter: blur(8px)`).
  * Datepicker calendar: Includes interactive "Hari ini" and "Hapus" bottom action buttons with instant date selection.

---

## 6. Layout Principles & Spatial System

* **Responsive Breakpoint Strategy:**
  * Mobile (< 768px): Single-column stack, bottom navigation bar, touch target minimum `48px`, edge-to-edge container with `16px` padding.
  * Tablet (768px – 1024px): 2-column adaptive grid, collapsible sidebar navigation.
  * Desktop (>= 1024px): 12-column grid layout, pinned sidebar, data tables with frozen first column.
* **No Overlapping Clutter:** Every component occupies a distinct spatial zone. No unanchored absolute overlays blocking interactions.
* **Safe Areas:** Full-height mobile sections use `min-height: 100dvh` (accommodates dynamic mobile address bar without layout jump).

---

## 7. Motion & Interaction Rules

* **Spring Physics Parameters:** Default transition curves: `cubic-bezier(0.16, 1, 0.3, 1)` (duration: `250ms - 350ms`) for silky, instant tactile feel.
* **Skeleton Loading Shimmer:** Layout-matching skeleton shapes with subtle horizontal wave gradient (`#F1F5F9` to `#E2E8F0`), replacing jarring circular loading spinners.
* **Perpetual Micro-Interactions:**
  * Pulsing green dot on live GPS location tracker.
  * Subtle scale push (`scale: 0.97`) on all card and button taps.
  * Smooth accordion expansion on leave approval timeline steps.

---

## 8. Anti-Patterns & Strict Banned Clichés (NEVER DO)

1. **NO Emojis in Core UI:** Use SVG/Ionicons exclusively (e.g. `ti ti-calendar`, `finger-print-outline`).
2. **NO Pure Black (`#000000`):** Use Slate-900 (`#0F172A`) or Charcoal.
3. **NO Neon Glowing Drop-Shadows:** No saturated purple/cyan glow effects under buttons.
4. **NO Generic 3-Card Symmetric Clones:** Use asymmetric Bento Grids, data feeds, or metric strips.
5. **NO Marketing Cliché Copy:** Use professional operational terms: "Presensi Masuk Outlet", "Rekapitulasi Shift", "Approval Layer", "Radius Geofencing", "Verifikasi Biometrik".
6. **NO Circular Generic Spinners:** Always use dimensional skeleton loaders matching the exact shape of incoming data.
