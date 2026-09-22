<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('presensi_jamkerja_byday')) {
            Schema::create('presensi_jamkerja_byday', function (Blueprint $table) {
                $table->char('nik', 9);
                $table->string('hari');
                $table->char('kode_jam_kerja', 4);
                $table->timestamps();

                $table->primary(['nik', 'hari']);
                $table->foreign('nik')->references('nik')->on('karyawan')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreign('kode_jam_kerja')->references('kode_jam_kerja')->on('presensi_jamkerja')->restrictOnDelete()->cascadeOnUpdate();
            });
        }

        if (!Schema::hasTable('presensi_jamkerja_bydate')) {
            Schema::create('presensi_jamkerja_bydate', function (Blueprint $table) {
                $table->char('nik', 9);
                $table->date('tanggal');
                $table->char('kode_jam_kerja', 4);
                $table->timestamps();

                $table->primary(['nik', 'tanggal']);
                $table->foreign('nik')->references('nik')->on('karyawan')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreign('kode_jam_kerja')->references('kode_jam_kerja')->on('presensi_jamkerja')->restrictOnDelete()->cascadeOnUpdate();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi_jamkerja_bydate');
        Schema::dropIfExists('presensi_jamkerja_byday');
    }
};
