<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $table = 'employee_documents';

    protected $fillable = [
        'nik',
        'document_type',
        'title',
        'file_path',
        'file_size_kb',
        'expiry_date',
        'notes',
        'uploaded_by',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'file_size_kb' => 'integer',
    ];

    public const TYPES = [
        'KTP' => 'Kartu Tanda Penduduk (KTP)',
        'NPWP' => 'Nomor Pokok Wajib Pajak (NPWP)',
        'KK' => 'Kartu Keluarga (KK)',
        'IJAZAH' => 'Ijazah & Transkrip Nilai',
        'KONTRAK' => 'Perjanjian Kerja (PKWT/PKWTT)',
        'SERTIFIKAT' => 'Sertifikat Kompetensi / Pelatihan',
        'BPJS' => 'Kartu BPJS Kesehatan / Ketenagakerjaan',
        'OTHER' => 'Dokumen Lainnya',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->document_type] ?? $this->document_type;
    }
}
