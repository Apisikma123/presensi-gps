<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;
    protected $table = 'presensi';
    protected $fillable = [
        'nik',
        'tanggal',
        'jam_in',
        'jam_out',
        'foto_in',
        'foto_out',
        'id_mesin',
        'kode_cabang',
        'lokasi_in',
        'lokasi_out',
        'status',
        'is_terlambat',
        'menit_terlambat',
        'kode_jam_kerja',
        'keterangan',
        'is_dispensasi',
        'dispensasi_id',
        'is_early_out',
        'early_out_minutes',
        'early_out_reason',
        'is_archived',
        'foto_in_archived',
        'foto_out_archived',
        'last_corrected_by',
        'last_correction_reason',
        'last_corrected_at',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'foto_in_archived' => 'boolean',
        'foto_out_archived' => 'boolean',
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

    public function corrector()
    {
        return $this->belongsTo(User::class, 'last_corrected_by');
    }

    public function getCorrectedByAttribute()
    {
        return $this->last_corrected_by;
    }

    public function getReasonAttribute()
    {
        return $this->last_correction_reason;
    }

    public function getCorrectedAtAttribute()
    {
        return $this->last_corrected_at;
    }
}
