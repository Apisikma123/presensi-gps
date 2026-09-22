<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facerecognition extends Model
{
    use HasFactory;

    protected $table = 'karyawan_wajah';
    protected $fillable = [
        'nik',
        'wajah',
        'descriptor',
    ];

    protected $casts = [
        'descriptor' => 'array',
    ];

}
