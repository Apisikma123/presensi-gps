<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeWarning extends Model
{
    use HasFactory;

    protected $table = 'employee_warnings';

    protected $fillable = [
        'sp_number',
        'nik',
        'level',
        'incident_date',
        'effective_date',
        'expiry_date',
        'violation_description',
        'pasal_pelanggaran',
        'action_plan',
        'document_file',
        'status',
        'issued_by',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'effective_date' => 'date',
        'expiry_date' => 'date',
    ];

    public const LEVELS = [
        'TEGURAN_LISAN' => 'Teguran Lisan',
        'SP_1' => 'Surat Peringatan I (SP 1)',
        'SP_2' => 'Surat Peringatan II (SP 2)',
        'SP_3' => 'Surat Peringatan III (SP 3)',
        'SKORSING' => 'Skorsing Sementara',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function getLevelLabelAttribute(): string
    {
        return self::LEVELS[$this->level] ?? $this->level;
    }

    public function getLevelBadgeHtmlAttribute(): string
    {
        return match ($this->level) {
            'SP_3', 'SKORSING' => '<span class="badge bg-danger-lt text-danger fw-bold">' . e($this->level_label) . '</span>',
            'SP_2' => '<span class="badge bg-warning-lt text-warning fw-bold">' . e($this->level_label) . '</span>',
            'SP_1' => '<span class="badge bg-yellow-lt text-dark fw-bold">' . e($this->level_label) . '</span>',
            default => '<span class="badge bg-secondary-lt text-secondary fw-bold">' . e($this->level_label) . '</span>',
        };
    }

    public function getStatusBadgeHtmlAttribute(): string
    {
        return match ($this->status) {
            'ACTIVE' => '<span class="badge bg-danger-lt text-danger fw-bold">Berlaku Aktif</span>',
            'EXPIRED' => '<span class="badge bg-light text-muted">Kedaluwarsa</span>',
            'REVOKED' => '<span class="badge bg-success-lt text-success fw-bold">Dicabut</span>',
            default => '<span class="badge bg-light text-muted">' . e($this->status) . '</span>',
        };
    }
}
