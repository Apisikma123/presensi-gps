<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $table = 'divisions';
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'kode_dept', 'kode_dept');
    }

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'kode_divisi', 'kode_divisi');
    }
}
