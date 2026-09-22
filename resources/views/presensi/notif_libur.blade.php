@extends('layouts.mobile.app')
@section('content')
    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="javascript:;" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">E-Presensi</div>
            <div class="right"></div>
        </div>
    </div>
    <div id="content-section">
        <div class="row" style="margin-top: 60px">
            <div class="col">
                <div class="alert alert-warning">
                    <p>
                        Hari ini Anda dijadwalkan LIBUR / OFF. Tidak ada kewajiban presensi.
                        <br>
                        <small class="text-muted">{{ $keterangan_libur ?? ($harilibur->keterangan ?? '') }}</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
