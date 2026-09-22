<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalJamkerja extends Model
{
    use HasFactory;

    protected $table = 'global_jamkerja';
    protected $guarded = [];

    /**
     * Relasi ke master jam kerja
     */
    public function jamkerja()
    {
        return $this->belongsTo(Jamkerja::class, 'kode_jam_kerja', 'kode_jam_kerja');
    }
}
