<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izinsakit extends Model
{
    use HasFactory;
    protected $table = 'presensi_izinsakit';
    protected $guarded = [];
    protected $primaryKey = 'kode_izin_sakit';
    public $incrementing = false;

    public function canApprove($user = null)
    {
        return $this->status == 0;
    }

    public function hasApproved($user = null)
    {
        return $this->status == 1;
    }
}
