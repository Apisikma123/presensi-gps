<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiDispensasi extends Model
{
    use HasFactory;

    protected $table = 'presensi_dispensasi';

    protected $fillable = [
        'nik',
        'tanggal',
        'batas_dispensasi',
        'alasan',
        'status',
        'approved_by'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
}
