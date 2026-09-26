<style>
    /* App Bottom Menu Base - Responsive to Safe Area (antislop-layoutmobile) */
    .appBottomMenu {
        height: calc(56px + env(safe-area-inset-bottom, 0px));
        padding-bottom: env(safe-area-inset-bottom, 0px) !important;
        box-sizing: border-box !important;
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        max-width: 480px !important;
        margin-left: auto !important;
        margin-right: auto !important;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999;
        background: #ffffff !important;
        border-top: 1px solid #e2e8f0 !important;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.05) !important;
    }

    /* Items Layout */
    .appBottomMenu a,
    .appBottomMenu a:visited,
    .appBottomMenu .item,
    .appBottomMenu .item:visited {
        color: #64748b !important;
        text-decoration: none !important;
    }

    .appBottomMenu .item {
        flex: 1 1 0% !important;
        max-width: 96px !important;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.1s ease-in-out, background-color 0.2s;
        -webkit-tap-highlight-color: transparent; 
        border-radius: 12px;
        margin: 0 2px;
    }
    .appBottomMenu .item:active {
        transform: scale(0.92);
        background-color: rgba(0,0,0,0.03);
    }

    .appBottomMenu .item .col {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        line-height: 1.2;
    }

    /* Icon & Label Styles */
    .appBottomMenu .item ion-icon {
        font-size: 22px;
        margin-bottom: 3px;
        color: #64748b !important;
        transition: color 0.3s;
    }
    .appBottomMenu .item strong {
        display: block;
        font-size: 10px;
        font-weight: 500;
        color: #64748b !important;
        transition: color 0.3s;
    }

    /* Active State */
    .appBottomMenu .item.active ion-icon, 
    .appBottomMenu .item.active strong {
        color: var(--color-primary, {{ $t['primary'] ?? '#3C2A21' }}) !important;
        font-weight: 700 !important;
    }

    /* Center Action Button (Fingerprint) */
    .appBottomMenu .item .action-button.large {
        width: 60px !important;
        height: 60px !important;
        margin-top: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: var(--color-primary, {{ $t['primary'] ?? '#3C2A21' }}) !important;
        box-shadow: 0 4px 12px rgba(var(--color-primary-rgb, 60, 42, 33), 0.3) !important;
        position: relative !important;
        top: -10px !important;
        transition: transform 0.1s ease-in-out, box-shadow 0.2s !important;
    }
    .appBottomMenu .item .action-button.large ion-icon {
        color: var(--theme-primary-contrast, #ffffff) !important;
        font-size: 32px !important;
        margin-bottom: 0 !important;
    }
    .appBottomMenu .item:active .action-button.large {
        transform: scale(0.9) !important;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2) !important;
    }

    /* Keep nav tap targets comfortable and clear of home gesture line */
    .appBottomMenu .item {
        min-height: 48px;
    }
</style>
<div class="appBottomMenu">
    <a href="/dashboard" class="item {{ request()->is('dashboard') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="home-outline"></ion-icon>
            <strong>Home</strong>
        </div>
    </a>

    @if(module_enabled('attendance'))
        <a href="{{ route('presensi.histori') }}" class="item {{ request()->is('presensi/histori') ? 'active' : '' }}">
            <div class="col">
                <ion-icon name="document-text-outline" role="img" class="md hydrated" aria-label="document text outline"></ion-icon>
                <strong>Histori</strong>
            </div>
        </a>

        <a href="/presensi/create" class="item ">
            <div class="col">
                <div class="action-button large">
                    <ion-icon name="finger-print-outline"></ion-icon>
                </div>
            </div>
        </a>
    @endif

    @if(module_enabled('leave'))
        <a href="{{ route('pengajuanizin.index') }}" class="item {{ request()->is('pengajuanizin') ? 'active' : '' }}">
            <div class="col">
                <ion-icon name="calendar-outline"></ion-icon>
                <strong>Izin/Cuti</strong>
            </div>
        </a>
    @endif

    @if(!module_enabled('attendance') && module_enabled('payroll') && Route::has('payslip.my_payslips'))
        <a href="{{ route('payslip.my_payslips') }}" class="item {{ request()->is('my-payslips') ? 'active' : '' }}">
            <div class="col">
                <ion-icon name="cash-outline"></ion-icon>
                <strong>Slip Gaji</strong>
            </div>
        </a>
    @elseif(!module_enabled('attendance') && Route::has('shortcut.index'))
        <a href="{{ route('shortcut.index') }}" class="item {{ request()->is('shortcut') ? 'active' : '' }}">
            <div class="col">
                <ion-icon name="grid-outline"></ion-icon>
                <strong>Menu</strong>
            </div>
        </a>
    @endif

    <a href="{{ route('profile.index') }}"
        class="item {{ request()->is(['profile', 'profile/*']) ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="person-outline"></ion-icon>
            <strong>Profil</strong>
        </div>
    </a>
</div>
<!-- * App Bottom Menu -->

