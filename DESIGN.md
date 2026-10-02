---
name: Presence Modern Clean HR
colors:
  surface: '#ffffff'
  surface-dim: '#f8fafc'
  surface-bright: '#ffffff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f8fafc'
  surface-container: '#f1f5f9'
  surface-container-high: '#e2e8f0'
  surface-container-highest: '#cbd5e1'
  on-surface: '#0f172a'
  on-surface-variant: '#475569'
  inverse-surface: '#0f172a'
  inverse-on-surface: '#ffffff'
  outline: '#cbd5e1'
  outline-variant: '#e2e8f0'
  surface-tint: '#1a5276'
  primary: '#1a5276'
  on-primary: '#ffffff'
  primary-container: '#154360'
  on-primary-container: '#ffffff'
  inverse-primary: '#2980b9'
  secondary: '#2980b9'
  on-secondary: '#ffffff'
  secondary-container: '#e0f2fe'
  on-secondary-container: '#0369a1'
  tertiary: '#0f172a'
  on-tertiary: '#ffffff'
  tertiary-container: '#1e293b'
  on-tertiary-container: '#ffffff'
  error: '#dc2626'
  on-error: '#ffffff'
  error-container: '#fee2e2'
  on-error-container: '#991b1b'
  background: '#f8fafc'
  on-background: '#0f172a'
  surface-variant: '#f1f5f9'
typography:
  display-lg:
    fontFamily: Outfit
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 56px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Outfit
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Outfit
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-caps:
    fontFamily: Geist
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
  data-mono:
    fontFamily: JetBrains Mono
    fontSize: 14px
    fontWeight: '500'
    lineHeight: 20px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: 8px
  container-max: 1440px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 40px
---

## Brand & Style
The design system embodies a "Modern Roastery & Swiss Precision" aesthetic. It balances the industrial, tactile warmth of high-end coffee production with the rigorous, clinical clarity required for enterprise HR and operations management. 

The visual direction follows a **Modern/Minimalist** approach with **Tactile** influences. It utilizes heavy whitespace, high-precision typography, and "bento-style" layouts to organize complex data feeds into digestible, editorial-quality surfaces. The emotional response should be one of sophisticated reliability—moving away from generic "SaaS Blue" into a grounded, artisanal workspace that feels as premium as the coffee being managed.

## Colors
The palette is anchored by "Espresso Deep" (#3C2A21), a rich, dark brown that provides the structural weight typically reserved for black. "Crema White" serves as the primary canvas, offering a warmer, more sophisticated backdrop than pure white, reducing eye strain during long administrative shifts. 

"Matcha Green" (#4A6741) and "Roasted Walnut" (#634832) provide functional semantic signaling for success and secondary actions while staying within the natural, organic spectrum of the café environment. Use the Primary color for navigation backgrounds and key call-to-actions to maintain brand authority.

## Typography
Typography is the cornerstone of the Swiss precision aspect of the design system. **Outfit** is used for headlines with tight tracking to create a modern, impactful presence. **Inter** provides high legibility for long-form data and body copy. 

A specialized **Data/Metrics** tier using **JetBrains Mono** is reserved for inventory counts, timestamps, and GPS coordinates to ensure no ambiguity in character recognition. Ensure that all labels for data points use the `label-caps` style for a distinct visual hierarchy.

## Layout & Spacing
The design system utilizes a **Bento Grid** philosophy. Content is housed in distinct, modular containers that adapt to a 12-column fluid grid. 

- **Desktop:** 12 columns with 24px gutters. Use the 40px margin to create "breathable" editorial layouts.
- **Tablet:** 8 columns with 24px gutters.
- **Mobile:** 4 columns with 16px gutters.

Spacing follows a strict 8px linear scale. For inventory lists and data-heavy tables, use "Compact" spacing (8px-12px padding) to maximize information density. For dashboard "Bento" cards, use "Spacious" padding (24px-32px) to emphasize a premium, clean look.

## Elevation & Depth
Depth is communicated through **Tonal Layers** and **Subtle Shadows**, avoiding the heavy shadows of traditional skeuomorphism. 

- **Level 0 (Background):** Crema White (#FDFCFB).
- **Level 1 (Cards/Surface):** Pure White (#FFFFFF) with a 1px border in `rgba(60, 42, 33, 0.08)`.
- **Level 2 (Active/Hover):** Shadow `0 4px 20px rgba(60, 42, 33, 0.04)`.

This approach ensures the UI feels tactile and physical—like a clean ceramic cup on a wooden counter—without sacrificing the precision of a digital interface.

## Shapes
The shape language is structured and professional. Balanced corner radii are applied to primary containers and dashboard "Bento" cards to provide a modern and reliable feel. Smaller elements like buttons and input fields should utilize the `rounded-lg` (16px) or `rounded-DEFAULT` (8px) setting to maintain a crisp, consistent rhythm across the interface.

## Components
### Buttons
Buttons must feel mechanical and tactile. 
- **Primary:** Espresso Deep background with White text.
- **Secondary:** White background with 1px border in Espresso Deep (8% opacity).
- **Shape:** Use the `rounded-lg` or `rounded-xl` setting to align with the professional roundedness theme.
- **Interaction:** On `:active` state, apply a `-1px` vertical translation (Y-axis) to simulate a physical "click" feel.

### Cards (Bento)
Dashboard widgets use the Bento Grid style. Each card has a white background, 16px corner radius, and the standard subtle elevation. Labels inside cards should use `label-caps` in Steam Gray.

### Input Fields
Inputs are minimal: 1px border, 12px vertical padding, and `rounded-DEFAULT` (8px) corners. No background color (transparent) to allow the Surface color to show through. On focus, the border color shifts to Espresso Deep at 40% opacity.

### Data Feeds & Lists
For HR management and inventory, use a "Steam Gray" separator line (1px). Metrics should be displayed in the Data Mono font to separate "numbers" from "narrative."

### Chips & Status Badges (Standard Proportional Stack)
Across all employee mobile list cards (Histori Presensi, Pengajuan Izin, Dashboard Karyawan), status badges must strictly adhere to the proportional vertical 2-badge stack on the right edge:
- **Top Badge (Category / Shift):**
  - Class: `text-[10.5px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md shrink-0`
  - Examples: `Shift Pagi`, `Cuti Tahunan`, `Izin Absen`, `Izin Sakit`
- **Bottom Badge (Status):**
  - Base Class: `inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0`
  - **Disetujui / Hadir / Tepat Waktu:** `bg-[#f0fdf4] text-[#15803d] border border-[#bbf7d0]`
  - **Ditolak / Telat:** `bg-[#fef2f2] text-[#e11d48] border border-[#fecdd3]`
  - **Pending / Menunggu:** `bg-[#fffbeb] text-[#b45309] border border-[#fde68a]`
  - **Dispensasi / Izin:** `bg-[#e0f2fe] text-[#0284c7] border border-[#bae6fd]`
- **Proportional Symmetry:**
  Both badges share the exact same `px-2 py-0.5 rounded-md` geometry, aligning flush on the card's right edge.
  Never add redundant detail buttons onto the card; the whole card container is interactive (`cursor-pointer active:scale-[0.99]`) and smoothly opens the detail modal.