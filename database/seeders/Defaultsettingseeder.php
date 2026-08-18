<?php

namespace Database\Seeders;

use App\Models\Pengaturanumum;
use Illuminate\Database\Seeder;

class Defaultsettingseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pengaturanumum::updateOrInsert(['id' => 1], [
            'nama_perusahaan' => 'HR Presence Indonesia',
            'nama_aplikasi' => 'HR Presence',
            'alamat' => 'Jl. Pemuda No. 1, Surabaya',
            'telepon' => '031-1234567',
            'logo' => 'logo.png',
            'total_jam_bulan' => '173',
            'show_rate_slip' => 1,
            'status_potongan_jam' => 0,
            'absen_istirahat' => 0,
            'potongan_istirahat' => 0,
            'sistem_hari_kerja' => '6',
            'global_jamkerja_aktif' => 0,
            'denda' => 1,
            'face_recognition' => 0,
            'periode_laporan_dari' => 1,
            'periode_laporan_sampai' => 31,
            'periode_laporan_next_bulan' => 0,
            'cloud_id' => '',
            'api_key' => '',
            'domain_email' => 'gmail.com',
            'domain_wa_gateway' => '',
            'wa_api_key' => '',
            'provider_wa' => 'ig',
            'tujuan_notifikasi_wa' => 0,
            'id_group_wa' => '',
            'batasi_absen' => 0,
            'batas_jam_absen' => 0,
            'batas_jam_absen_pulang' => 0,
            'multi_lokasi' => 0,
            'notifikasi_wa' => 0,
            'batasi_hari_izin' => 0,
            'jml_hari_izin_max' => 0,
            'batas_presensi_lintashari' => '00:00:00',
            'timezone' => 'Asia/Jakarta',
            'theme_color_1' => '#32745e',
            'theme_color_2' => '#58907D',
            'mobile_theme_scheme' => 'green',
            'session_time' => 120,
            'nama_hrd' => 'HRD Manager',
            'expired' => null,
        ]);
    }
}
