---
name: Presence Universal HR
colors:
  surface: '#faf9f8'
  surface-dim: '#dadad9'
  surface-bright: '#faf9f8'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f4f3f2'
  surface-container: '#eeeeed'
  surface-container-high: '#e9e8e7'
  surface-container-highest: '#e3e2e1'
  on-surface: '#1a1c1c'
  on-surface-variant: '#4f4540'
  inverse-surface: '#2f3130'
  inverse-on-surface: '#f1f0f0'
  outline: '#81756f'
  outline-variant: '#d3c3bd'
  surface-tint: '#705a4f'
  primary: '#25160e'
  on-primary: '#ffffff'
  primary-container: '#3c2a21'
  on-primary-container: '#aa9084'
  inverse-primary: '#dec1b3'
  secondary: '#755841'
  on-secondary: '#ffffff'
  secondary-container: '#fdd5b8'
  on-secondary-container: '#785b44'
  tertiary: '#041e03'
  on-tertiary: '#ffffff'
  tertiary-container: '#193413'
  on-tertiary-container: '#7f9e73'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#fbdcce'
  primary-fixed-dim: '#dec1b3'
  on-primary-fixed: '#281810'
  on-primary-fixed-variant: '#574238'
  secondary-fixed: '#ffdcc2'
  secondary-fixed-dim: '#e5bfa3'
  on-secondary-fixed: '#2b1705'
  on-secondary-fixed-variant: '#5b412c'
  tertiary-fixed: '#caecbc'
  tertiary-fixed-dim: '#afd0a1'
  on-tertiary-fixed: '#062104'
  on-tertiary-fixed-variant: '#324e2a'
  background: '#faf9f8'
  on-background: '#1a1c1c'
  surface-variant: '#e3e2e1'
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
- **Bottom Badge (Status & Indicator Dot):**
  - Base Class: `inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0` with `w-1.5 h-1.5 rounded-full` dot
  - **Disetujui / Hadir / Tepat Waktu:** `bg-[#f0fdf4] text-[#15803d] border border-[#bbf7d0]` (Dot: `bg-[#16a34a]`)
  - **Ditolak / Telat:** `bg-[#fef2f2] text-[#e11d48] border border-[#fecdd3]` (Dot: `bg-[#e11d48]`)
  - **Pending / Menunggu:** `bg-[#fffbeb] text-[#b45309] border border-[#fde68a]` (Dot: `bg-[#d97706]`)
  - **Dispensasi / Izin:** `bg-[#e0f2fe] text-[#0284c7] border border-[#bae6fd]` (Dot: `bg-[#0284c7]`)
- **Proportional Symmetry:**
  Both badges share the exact same `px-2 py-0.5 rounded-md` geometry, aligning flush on the card's right edge.
  Never add redundant detail buttons onto the card; the whole card container is interactive (`cursor-pointer active:scale-[0.99]`) and smoothly opens the detail modal.