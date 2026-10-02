@extends('layouts.mobile.modern')

@section('title', 'Pusat Bantuan')

@section('header_left')
    <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('shortcut.index') }}"
        onclick="if (window.history.length > 1 && document.referrer && document.referrer.indexOf(window.location.host) !== -1) { event.preventDefault(); window.history.back(); }"
        class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/15 text-white active:scale-[0.98] transition-transform duration-150"
        title="Kembali">
        <ion-icon name="chevron-back-outline" class="text-base"></ion-icon>
    </a>
@endsection

@push('mystyle')
<style>
    /* =========================================================
       HERO & CARD SYSTEM (DESIGN.md / Modern Mobile UI)
       ========================================================= */
    .help-hero-card {
        background: linear-gradient(135deg, var(--color-primary, #1B365D) 0%, var(--theme-color-2, #2D4B73) 100%);
        border-radius: 20px;
        padding: 20px 18px;
        color: #ffffff;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        position: relative;
        overflow: hidden;
        margin-bottom: 14px;
    }
    .help-hero-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.18);
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 8px;
        backdrop-filter: blur(4px);
    }
    .help-hero-watermark {
        position: absolute;
        right: -15px;
        bottom: -22px;
        opacity: 0.12;
        font-size: 115px;
        pointer-events: none;
        line-height: 1;
        color: #ffffff;
    }

    /* Search Input Box */
    .search-input-box {
        width: 100%;
        height: 44px;
        padding-left: 42px;
        padding-right: 14px;
        border-radius: 14px;
        border: 1px solid rgba(15, 23, 42, 0.1);
        background: #FFFFFF;
        font-size: 13px;
        font-weight: 500;
        color: #0F172A;
        outline: none;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        transition: all 0.15s ease;
        box-sizing: border-box;
    }
    .search-input-box:focus {
        border-color: var(--color-primary, #1B365D);
        box-shadow: 0 0 0 3px var(--color-primary-soft, rgba(27, 54, 93, 0.12));
    }

    /* Category Chips Carousel */
    .category-chips-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
        margin-bottom: 16px;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    .category-chips-wrap::-webkit-scrollbar {
        display: none;
    }
    .category-chip {
        height: 36px;
        padding: 0 14px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: #FFFFFF;
        color: #64748B;
        transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1);
        flex-shrink: 0;
        outline: none;
    }
    .category-chip:active {
        transform: scale(0.96);
    }
    .category-chip.active {
        background: var(--color-primary, #1B365D) !important;
        color: #FFFFFF !important;
        border-color: var(--color-primary, #1B365D) !important;
        box-shadow: 0 2px 8px rgba(var(--bs-primary-rgb, 27, 54, 93), 0.25);
    }

    /* Menu Group & Rows (Matches shortcut.blade.php) */
    .menu-group {
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 18px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        overflow: hidden;
        margin-bottom: 16px;
    }
    .section-micro-label {
        font-size: 11px;
        font-weight: 800;
        color: #94A3B8;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 0 4px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Accordion FAQ Rows */
    .faq-accordion-row {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 13px 14px;
        background: #ffffff;
        border: none;
        outline: none;
        cursor: pointer;
        text-align: left;
        transition: background-color 0.15s ease;
    }
    .faq-accordion-row:active {
        background-color: #F8FAFC;
    }
    .faq-icon-box {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .faq-answer-pane {
        padding: 0 14px 14px 14px;
        background: #FFFFFF;
        font-size: 12.5px;
        color: #475569;
        line-height: 1.5;
    }
    .faq-answer-inner {
        background: #F8FAFC;
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: 12px;
        padding: 12px 14px;
    }
    .faq-chevron {
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        color: #94A3B8;
        font-size: 16px;
        flex-shrink: 0;
    }

    /* Quick Action Mini Button */
    .btn-faq-action {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 8px;
        padding: 6px 12px;
        border-radius: 8px;
        background: var(--color-primary, #1B365D);
        color: #FFFFFF !important;
        font-size: 11.5px;
        font-weight: 700;
        text-decoration: none !important;
        transition: opacity 0.15s ease;
    }
    .btn-faq-action:active {
        opacity: 0.85;
    }

    /* Quick Link Bento Cards at bottom */
    .quick-link-card {
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 16px;
        padding: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        transition: transform 0.14s ease, border-color 0.14s ease;
    }
    .quick-link-card:active {
        transform: scale(0.98);
        border-color: var(--color-primary, #1B365D);
    }
</style>
@endpush

@section('content')
<div class="px-1 pt-2 pb-24">

    {{-- 1. Hero Card (Executive Dark Tech/Modern Brand) --}}
    <div class="help-hero-card">
        <div style="position: relative; z-index: 2;">
            <div class="help-hero-pill">
                <ion-icon name="sparkles-outline"></ion-icon>
                <span>Bantuan Cepat</span>
            </div>
            <h2 style="font-family: 'Outfit', sans-serif; font-size: 18px; font-weight: 800; margin: 0 0 4px 0; line-height: 1.25; color: #ffffff;">
                Pusat Bantuan & Panduan
            </h2>
            <p style="font-size: 12px; opacity: 0.9; margin: 0; line-height: 1.4;">
                Petunjuk presensi, izin, dan solusi cepat kendala sistem.
            </p>
        </div>
        <div class="help-hero-watermark">
            <ion-icon name="help-buoy"></ion-icon>
        </div>
    </div>

    {{-- 2. Live Search Bar --}}
    <div style="position: relative; width: 100%; margin-bottom: 12px;">
        <div style="position: absolute; left: 14px; top: 0; bottom: 0; display: flex; align-items: center; pointer-events: none; color: #94A3B8;">
            <ion-icon name="search-outline" style="font-size: 18px;"></ion-icon>
        </div>
        <input type="text" id="helpFilterInput" class="search-input-box"
            placeholder="Cari kendala (contoh: gps, kamera, sakit, gaji)..."
            autocomplete="off">
    </div>

    {{-- 3. Category Filter Chips --}}
    <div class="category-chips-wrap" id="categoryChips">
        <button type="button" class="category-chip active" data-category="all">
            <ion-icon name="grid-outline"></ion-icon>
            <span>Semua</span>
        </button>
        <button type="button" class="category-chip" data-category="absen">
            <ion-icon name="finger-print-outline"></ion-icon>
            <span>Cara Absen</span>
        </button>
        <button type="button" class="category-chip" data-category="izin">
            <ion-icon name="calendar-outline"></ion-icon>
            <span>Izin & Cuti</span>
        </button>
        <button type="button" class="category-chip" data-category="kendala">
            <ion-icon name="build-outline"></ion-icon>
            <span>Kendala Teknis</span>
        </button>
        <button type="button" class="category-chip" data-category="jadwal">
            <ion-icon name="wallet-outline"></ion-icon>
            <span>Shift & Gaji</span>
        </button>
    </div>

    {{-- 4. Quick Step Guide (Menu Group Container) --}}
    <div class="help-section" data-category="absen">
        <div class="section-micro-label">
            <span>Alur Cepat Presensi</span>
            <span style="font-size: 10.5px; font-weight: 600; color: #64748B;">3 Langkah</span>
        </div>

        <div class="menu-group">
            <div style="padding: 11px 14px; background: #FAF9F8; border-bottom: 1px solid rgba(15, 23, 42, 0.06); display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 7px; height: 7px; border-radius: 50%; background: var(--color-primary, #1B365D);"></span>
                    <span style="font-family: 'Outfit', sans-serif; font-size: 13px; font-weight: 800; color: #0F172A;">Panduan Jam Kehadiran</span>
                </div>
                <span style="font-size: 10.5px; font-weight: 700; color: var(--color-primary, #1B365D); background: var(--color-primary-soft, rgba(27,54,93,0.08)); padding: 2px 8px; border-radius: 6px;">Ringkas</span>
            </div>

            {{-- Step 1 --}}
            <div style="display: flex; align-items: flex-start; gap: 12px; padding: 13px 14px; border-bottom: 1px solid rgba(15, 23, 42, 0.06);">
                <div style="width: 36px; height: 36px; border-radius: 12px; background: rgba(59, 130, 246, 0.1); color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; border: 1px solid rgba(59, 130, 246, 0.15);">
                    <ion-icon name="location-outline"></ion-icon>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 2px;">
                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 800; color: #2563EB;">01</span>
                        <h4 style="font-family: 'Outfit', sans-serif; font-size: 13px; font-weight: 700; color: #0F172A; margin: 0;">Nyalakan GPS di Lokasi</h4>
                    </div>
                    <p style="font-size: 12px; color: #64748B; margin: 0; line-height: 1.4;">
                        Aktifkan GPS HP akurasi tinggi dan pastikan Anda sudah sampai di kantor/outlet sebelum membuka menu absen.
                    </p>
                </div>
            </div>

            {{-- Step 2 --}}
            <div style="display: flex; align-items: flex-start; gap: 12px; padding: 13px 14px; border-bottom: 1px solid rgba(15, 23, 42, 0.06);">
                <div style="width: 36px; height: 36px; border-radius: 12px; background: rgba(99, 102, 241, 0.1); color: #4F46E5; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; border: 1px solid rgba(99, 102, 241, 0.15);">
                    <ion-icon name="camera-outline"></ion-icon>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 2px;">
                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 800; color: #4F46E5;">02</span>
                        <h4 style="font-family: 'Outfit', sans-serif; font-size: 13px; font-weight: 700; color: #0F172A; margin: 0;">Arahkan Wajah di Tempat Terang</h4>
                    </div>
                    <p style="font-size: 12px; color: #64748B; margin: 0; line-height: 1.4;">
                        Buka kamera presensi, posisikan wajah tegak lurus di lingkaran tanpa masker atau kacamata hitam.
                    </p>
                </div>
            </div>

            {{-- Step 3 --}}
            <div style="display: flex; align-items: flex-start; gap: 12px; padding: 13px 14px;">
                <div style="width: 36px; height: 36px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; border: 1px solid rgba(16, 185, 129, 0.15);">
                    <ion-icon name="checkmark-done-outline"></ion-icon>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 2px;">
                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 800; color: #059669;">03</span>
                        <h4 style="font-family: 'Outfit', sans-serif; font-size: 13px; font-weight: 700; color: #0F172A; margin: 0;">Tekan Absen Masuk / Pulang</h4>
                    </div>
                    <p style="font-size: 12px; color: #64748B; margin: 0; line-height: 1.4;">
                        Tekan <b>Absen Masuk</b> saat mulai kerja. Saat shift selesai, buka kembali kamera lalu tekan <b>Absen Pulang</b>.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. FAQ Accordion Group (Clean iOS Style) --}}
    <div class="section-micro-label">
        <span>Tanya Jawab Populer (FAQ)</span>
        <span id="faqCount" style="font-size: 10.5px; font-weight: 600; color: #64748B;">8 Topik</span>
    </div>

    <div class="menu-group divide-y divide-slate-100" id="faqList">

        {{-- FAQ 1: Radius GPS --}}
        <div class="faq-item" data-category="kendala" data-keywords="gps radius kantor jarak merah gagal jangkauan">
            <button type="button" class="faq-accordion-row">
                <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                    <div class="faq-icon-box" style="background: rgba(239, 68, 68, 0.1); color: #DC2626; border: 1px solid rgba(239, 68, 68, 0.15);">
                        <ion-icon name="navigate-outline"></ion-icon>
                    </div>
                    <div style="min-width: 0;">
                        <h4 style="font-family: 'Outfit', sans-serif; font-size: 13.5px; font-weight: 700; color: #0F172A; margin: 0; line-height: 1.3;">
                            Kenapa muncul "Di luar radius kantor"?
                        </h4>
                        <span style="font-size: 11px; color: #94A3B8; display: block; margin-top: 1px;">Solusi sinyal dan akurasi GPS</span>
                    </div>
                </div>
                <ion-icon name="chevron-down-outline" class="faq-chevron"></ion-icon>
            </button>
            <div class="faq-answer-pane" style="display: none;">
                <div class="faq-answer-inner">
                    <p style="margin: 0 0 6px 0; font-weight: 600; color: #0F172A;">
                        Sistem mendeteksi posisi Anda berada di luar jarak aman kantor/cabang.
                    </p>
                    <div style="font-size: 12px; color: #475569; line-height: 1.5;">
                        <div>1. Buka aplikasi <b>Google Maps</b>, tap titik biru hingga akurat.</div>
                        <div>2. Pastikan tidak berada di dalam basement/ruang tertutup tebal.</div>
                        <div>3. Refresh halaman presensi lalu coba lakukan absen ulang.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FAQ 2: Kamera Error / Layar Hitam --}}
        <div class="faq-item" data-category="kendala" data-keywords="kamera blank hitam izin akses browser chrome safari">
            <button type="button" class="faq-accordion-row">
                <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                    <div class="faq-icon-box" style="background: rgba(99, 102, 241, 0.1); color: #4F46E5; border: 1px solid rgba(99, 102, 241, 0.15);">
                        <ion-icon name="videocam-outline"></ion-icon>
                    </div>
                    <div style="min-width: 0;">
                        <h4 style="font-family: 'Outfit', sans-serif; font-size: 13.5px; font-weight: 700; color: #0F172A; margin: 0; line-height: 1.3;">
                            Kamera tidak mau terbuka / layar hitam?
                        </h4>
                        <span style="font-size: 11px; color: #94A3B8; display: block; margin-top: 1px;">Pengaturan izin akses browser</span>
                    </div>
                </div>
                <ion-icon name="chevron-down-outline" class="faq-chevron"></ion-icon>
            </button>
            <div class="faq-answer-pane" style="display: none;">
                <div class="faq-answer-inner">
                    <p style="margin: 0 0 6px 0; font-weight: 600; color: #0F172A;">
                        Browser di HP Anda belum diberikan izin mengakses kamera.
                    </p>
                    <div style="font-size: 12px; color: #475569; line-height: 1.5;">
                        <div>&bull; <b>Google Chrome:</b> Tap ikon gembok/setelan di kiri atas bar alamat &rarr; <i>Izin</i> &rarr; Aktifkan Kamera.</div>
                        <div>&bull; <b>Safari (iPhone):</b> Buka Pengaturan HP &rarr; Safari &rarr; Kamera &rarr; Pilih <i>Izinkan</i>.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FAQ 3: Cara Ajukan Izin / Sakit --}}
        <div class="faq-item" data-category="izin" data-keywords="izin sakit cuti surat dokter upload foto bukti">
            <button type="button" class="faq-accordion-row">
                <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                    <div class="faq-icon-box" style="background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.15);">
                        <ion-icon name="document-text-outline"></ion-icon>
                    </div>
                    <div style="min-width: 0;">
                        <h4 style="font-family: 'Outfit', sans-serif; font-size: 13.5px; font-weight: 700; color: #0F172A; margin: 0; line-height: 1.3;">
                            Bagaimana cara mengajukan Izin atau Sakit?
                        </h4>
                        <span style="font-size: 11px; color: #94A3B8; display: block; margin-top: 1px;">Formulir online dan lampiran bukti</span>
                    </div>
                </div>
                <ion-icon name="chevron-down-outline" class="faq-chevron"></ion-icon>
            </button>
            <div class="faq-answer-pane" style="display: none;">
                <div class="faq-answer-inner">
                    <div style="font-size: 12px; color: #475569; line-height: 1.5;">
                        <div>1. Masuk ke menu <b>Izin</b> dari navigasi bawah.</div>
                        <div>2. Pilih jenis permohonan: <b>Izin</b> (acara keluarga) atau <b>Sakit</b>.</div>
                        <div>3. Isi tanggal, ketik keterangan, dan lampirkan foto surat dokter jika sakit.</div>
                    </div>
                    @if(Route::has('pengajuanizin.index'))
                    <a href="{{ route('pengajuanizin.index') }}" class="btn-faq-action">
                        <span>Buka Formulir Izin</span>
                        <ion-icon name="arrow-forward-outline"></ion-icon>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- FAQ 4: Izin Jam Kerja / Pulang Awal --}}
        <div class="faq-item" data-category="izin" data-keywords="izin jam kerja keluar kantor dispensasi pulang awal">
            <button type="button" class="faq-accordion-row">
                <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                    <div class="faq-icon-box" style="background: rgba(245, 158, 11, 0.1); color: #D97706; border: 1px solid rgba(245, 158, 11, 0.15);">
                        <ion-icon name="time-outline"></ion-icon>
                    </div>
                    <div style="min-width: 0;">
                        <h4 style="font-family: 'Outfit', sans-serif; font-size: 13.5px; font-weight: 700; color: #0F172A; margin: 0; line-height: 1.3;">
                            Bagaimana jika izin hanya beberapa jam saja?
                        </h4>
                        <span style="font-size: 11px; color: #94A3B8; display: block; margin-top: 1px;">Dispensasi keluar kantor atau pulang awal</span>
                    </div>
                </div>
                <ion-icon name="chevron-down-outline" class="faq-chevron"></ion-icon>
            </button>
            <div class="faq-answer-pane" style="display: none;">
                <div class="faq-answer-inner">
                    <p style="margin: 0 0 6px 0; font-size: 12px; color: #475569;">
                        Gunakan fitur <b>Izin Jam Kerja</b> atau <b>Dispensasi</b>:
                    </p>
                    <div style="font-size: 12px; color: #475569; line-height: 1.5;">
                        <div>&bull; Tentukan jam mulai dan jam selesai izin (contoh: 13:00 - 15:00).</div>
                        <div>&bull; Tuliskan alasan keperluan secara jelas untuk diverifikasi atasan.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FAQ 5: Shift Malam & Hari Libur --}}
        <div class="faq-item" data-category="jadwal" data-keywords="shift malam lewat tengah malam libur tanggal merah off">
            <button type="button" class="faq-accordion-row">
                <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                    <div class="faq-icon-box" style="background: rgba(59, 130, 246, 0.1); color: #2563EB; border: 1px solid rgba(59, 130, 246, 0.15);">
                        <ion-icon name="moon-outline"></ion-icon>
                    </div>
                    <div style="min-width: 0;">
                        <h4 style="font-family: 'Outfit', sans-serif; font-size: 13.5px; font-weight: 700; color: #0F172A; margin: 0; line-height: 1.3;">
                            Shift malam & hari libur perhitungannya bagaimana?
                        </h4>
                        <span style="font-size: 11px; color: #94A3B8; display: block; margin-top: 1px;">Aturan pergantian hari dan jadwal libur</span>
                    </div>
                </div>
                <ion-icon name="chevron-down-outline" class="faq-chevron"></ion-icon>
            </button>
            <div class="faq-answer-pane" style="display: none;">
                <div class="faq-answer-inner">
                    <div style="font-size: 12px; color: #475569; line-height: 1.5;">
                        <div>&bull; <b>Shift Malam (Lintas Hari):</b> Absen masuk malam hari seperti biasa. Absen pulang dilakukan besok paginya saat jam kerja berakhir.</div>
                        <div style="margin-top: 4px;">&bull; <b>Hari Libur Operasional:</b> Pada hari libur muncul tanda <i>HARI LIBUR</i>. Anda tidak perlu presensi dan tidak dikenakan denda alpa.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FAQ 6: Lupa Absen / HP Mati --}}
        <div class="faq-item" data-category="kendala" data-keywords="lupa absen pulang hp mati baterai habis koreksi hadir">
            <button type="button" class="faq-accordion-row">
                <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                    <div class="faq-icon-box" style="background: rgba(245, 158, 11, 0.1); color: #D97706; border: 1px solid rgba(245, 158, 11, 0.15);">
                        <ion-icon name="alert-circle-outline"></ion-icon>
                    </div>
                    <div style="min-width: 0;">
                        <h4 style="font-family: 'Outfit', sans-serif; font-size: 13.5px; font-weight: 700; color: #0F172A; margin: 0; line-height: 1.3;">
                            Lupa absen pulang atau baterai HP habis?
                        </h4>
                        <span style="font-size: 11px; color: #94A3B8; display: block; margin-top: 1px;">Prosedur koreksi manual supervisor</span>
                    </div>
                </div>
                <ion-icon name="chevron-down-outline" class="faq-chevron"></ion-icon>
            </button>
            <div class="faq-answer-pane" style="display: none;">
                <div class="faq-answer-inner">
                    <p style="margin: 0 0 4px 0; font-weight: 600; color: #0F172A;">
                        Segera laporkan ke Supervisor atau Admin HR cabang Anda.
                    </p>
                    <div style="font-size: 12px; color: #475569; line-height: 1.5;">
                        Admin dapat melakukan <b>Koreksi Manual</b> dengan mencatat jam aktual Anda agar rekap kehadiran tetap tercatat normal.
                    </div>
                </div>
            </div>
        </div>

        {{-- FAQ 7: Fake GPS & Keamanan --}}
        <div class="faq-item" data-category="kendala" data-keywords="fake gps mock location titip absen curang blokir">
            <button type="button" class="faq-accordion-row">
                <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                    <div class="faq-icon-box" style="background: rgba(239, 68, 68, 0.1); color: #DC2626; border: 1px solid rgba(239, 68, 68, 0.15);">
                        <ion-icon name="shield-half-outline"></ion-icon>
                    </div>
                    <div style="min-width: 0;">
                        <h4 style="font-family: 'Outfit', sans-serif; font-size: 13.5px; font-weight: 700; color: #0F172A; margin: 0; line-height: 1.3;">
                            Apakah boleh menggunakan Fake GPS?
                        </h4>
                        <span style="font-size: 11px; color: #DC2626; font-weight: 600; display: block; margin-top: 1px;">Dilarang Keras & Terdeteksi Otomatis</span>
                    </div>
                </div>
                <ion-icon name="chevron-down-outline" class="faq-chevron"></ion-icon>
            </button>
            <div class="faq-answer-pane" style="display: none;">
                <div class="faq-answer-inner" style="background: #FEF2F2; border-color: #FEE2E2;">
                    <p style="margin: 0; font-size: 12px; color: #991B1B; line-height: 1.5;">
                        Sistem dilengkapi pendeteksi Fake GPS dan verifikasi biometrik. Penggunaan aplikasi pihak ketiga untuk manipulasi lokasi akan otomatis ditolak dan tercatat dalam log pelanggaran keamanan perusahaan.
                    </p>
                </div>
            </div>
        </div>

        {{-- FAQ 8: Slip Gaji & Klaim --}}
        <div class="faq-item" data-category="jadwal" data-keywords="slip gaji kasbon reimburse klaim payroll uang lembur">
            <button type="button" class="faq-accordion-row">
                <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                    <div class="faq-icon-box" style="background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.15);">
                        <ion-icon name="wallet-outline"></ion-icon>
                    </div>
                    <div style="min-width: 0;">
                        <h4 style="font-family: 'Outfit', sans-serif; font-size: 13.5px; font-weight: 700; color: #0F172A; margin: 0; line-height: 1.3;">
                            Di mana melihat slip gaji atau klaim biaya?
                        </h4>
                        <span style="font-size: 11px; color: #94A3B8; display: block; margin-top: 1px;">Akses slip bulanan dan reimbursement</span>
                    </div>
                </div>
                <ion-icon name="chevron-down-outline" class="faq-chevron"></ion-icon>
            </button>
            <div class="faq-answer-pane" style="display: none;">
                <div class="faq-answer-inner">
                    <div style="font-size: 12px; color: #475569; line-height: 1.5;">
                        <div>&bull; Buka menu <b>Shortcut</b> di navigasi bawah.</div>
                        <div>&bull; Pilih <b>Slip Gaji</b> untuk unduh rincian slip, atau <b>Reimbursement</b> untuk mengajukan klaim nota pengeluaran operasional.</div>
                    </div>
                    <a href="{{ route('shortcut.index') }}" class="btn-faq-action">
                        <span>Buka Menu Shortcut</span>
                        <ion-icon name="arrow-forward-outline"></ion-icon>
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- 6. No Search Result State --}}
    <div id="noResultsState" class="menu-group" style="display: none; padding: 24px 16px; text-align: center;">
        <div style="width: 44px; height: 44px; border-radius: 12px; background: #F1F5F9; color: #94A3B8; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px auto; font-size: 20px;">
            <ion-icon name="search-outline"></ion-icon>
        </div>
        <h5 style="font-family: 'Outfit', sans-serif; font-size: 14px; font-weight: 700; color: #1E293B; margin: 0 0 4px 0;">Topik Tidak Ditemukan</h5>
        <p style="font-size: 12px; color: #94A3B8; margin: 0;">Coba gunakan kata kunci lain seperti: <i>gps, kamera, izin, sakit, shift</i>.</p>
    </div>

    {{-- 7. Quick Navigation Bento Buttons --}}
    <div class="section-micro-label">
        <span>Tautan Cepat</span>
    </div>

    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 16px;">
        @if(Route::has('pengajuanizin.index'))
        <a href="{{ route('pengajuanizin.index') }}" class="quick-link-card">
            <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(59, 130, 246, 0.1); color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                <ion-icon name="create-outline"></ion-icon>
            </div>
            <div style="min-width: 0;">
                <div style="font-family: 'Outfit', sans-serif; font-size: 12.5px; font-weight: 700; color: #0F172A; line-height: 1.2;">Ajukan Izin</div>
                <div style="font-size: 10.5px; color: #94A3B8; margin-top: 1px;">Sakit & Cuti</div>
            </div>
        </a>
        @endif

        @if(Route::has('presensi.histori'))
        <a href="{{ route('presensi.histori') }}" class="quick-link-card">
            <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                <ion-icon name="time-outline"></ion-icon>
            </div>
            <div style="min-width: 0;">
                <div style="font-family: 'Outfit', sans-serif; font-size: 12.5px; font-weight: 700; color: #0F172A; line-height: 1.2;">Riwayat Absen</div>
                <div style="font-size: 10.5px; color: #94A3B8; margin-top: 1px;">Cek Kehadiran</div>
            </div>
        </a>
        @endif
    </div>

    {{-- 8. Support / Supervisor Contact Footer Banner --}}
    <div style="background: #ffffff; border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 18px; padding: 14px 16px; display: flex; align-items: center; justify-content: space-between; gap: 12px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
            <div style="width: 38px; height: 38px; border-radius: 12px; background: var(--color-primary-soft, rgba(27,54,93,0.08)); color: var(--color-primary, #1B365D); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                <ion-icon name="headset-outline"></ion-icon>
            </div>
            <div style="min-width: 0;">
                <div style="font-family: 'Outfit', sans-serif; font-size: 13px; font-weight: 700; color: #0F172A; line-height: 1.2;">Butuh Bantuan Lain?</div>
                <div style="font-size: 11px; color: #64748B; margin-top: 2px;">Hubungi Admin HRD atau Supervisor Anda.</div>
            </div>
        </div>
        <a href="{{ route('dashboard.index') }}"
           style="padding: 7px 12px; border-radius: 10px; font-size: 11.5px; font-weight: 700; background: var(--color-primary, #1B365D); color: #ffffff !important; text-decoration: none !important; shrink: 0; white-space: nowrap; transition: opacity 0.15s ease;">
            Dashboard
        </a>
    </div>

</div>

@push('myscript')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Smooth Accordion Toggle
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const toggleBtn = item.querySelector('.faq-accordion-row');
        const answerPane = item.querySelector('.faq-answer-pane');
        const chevron = item.querySelector('.faq-chevron');

        toggleBtn.addEventListener('click', function () {
            const isCurrentlyOpen = answerPane.style.display !== 'none';

            // Close all items smoothly
            faqItems.forEach(otherItem => {
                const otherPane = otherItem.querySelector('.faq-answer-pane');
                const otherChevron = otherItem.querySelector('.faq-chevron');
                otherPane.style.display = 'none';
                otherChevron.style.transform = 'rotate(0deg)';
            });

            // Toggle selected item
            if (!isCurrentlyOpen) {
                answerPane.style.display = 'block';
                chevron.style.transform = 'rotate(180deg)';
            }
        });
    });

    // 2. Category Filter & Live Search
    const categoryChips = document.querySelectorAll('.category-chip');
    const searchInput = document.getElementById('helpFilterInput');
    const sections = document.querySelectorAll('.help-section');
    const noResults = document.getElementById('noResultsState');
    const countBadge = document.getElementById('faqCount');

    let activeCategory = 'all';

    function filterHelpContent() {
        const query = (searchInput.value || '').trim().toLowerCase();
        let visibleCount = 0;

        // Filter sections (e.g. 3-step guide)
        sections.forEach(sec => {
            const secCat = sec.dataset.category;
            const matchesCat = (activeCategory === 'all' || activeCategory === secCat);
            const matchesSearch = query === '' || sec.innerText.toLowerCase().includes(query);
            sec.style.display = (matchesCat && matchesSearch) ? 'block' : 'none';
        });

        // Filter FAQ items
        faqItems.forEach(item => {
            const itemCat = item.dataset.category || '';
            const keywords = (item.dataset.keywords || '') + ' ' + item.innerText;
            const matchesCat = (activeCategory === 'all' || activeCategory === itemCat);
            const matchesSearch = query === '' || keywords.toLowerCase().includes(query);

            if (matchesCat && matchesSearch) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (countBadge) {
            countBadge.innerText = visibleCount + ' Topik';
        }

        if (visibleCount === 0 && query !== '') {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }

    // Category button click
    categoryChips.forEach(chip => {
        chip.addEventListener('click', function () {
            categoryChips.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            activeCategory = this.dataset.category;
            filterHelpContent();
        });
    });

    // Live search input event
    searchInput.addEventListener('input', filterHelpContent);
});
</script>
@endpush
@endsection
