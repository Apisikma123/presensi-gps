<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setjamkerjabyday extends Model
{
    use HasFactory;

    protected $table = 'presensi_jamkerja_byday';
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'nik',
        'hari',
        'kode_jam_kerja',
        'kode_cabang',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function jamkerja()
    {
        return $this->belongsTo(Jamkerja::class, 'kode_jam_kerja', 'kode_jam_kerja');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'kode_cabang', 'kode_cabang');
    }
}
