<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeMovement extends Model
{
    use HasFactory;

    protected $table = 'employee_movements';

    protected $fillable = [
        'no_sk',
        'nik',
        'movement_type',
        'effective_date',
        'old_values',
        'new_values',
        'reason',
        'document_path',
        'status',
        'approved_by',
        'created_by',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Movement types dictionary
     */
    public const TYPES = [
        'BRANCH_TRANSFER' => 'Mutasi Cabang',
        'DEPT_TRANSFER' => 'Mutasi Departemen',
        'DIVISION_CHANGE' => 'Perubahan Divisi',
        'POSITION_CHANGE' => 'Rotasi Jabatan',
        'PROMOTION' => 'Promosi',
        'DEMOTION' => 'Demosi',
        'SUPERVISOR_CHANGE' => 'Pergantian Atasan',
        'STATUS_CHANGE' => 'Perubahan Status Kerja',
        'SALARY_CHANGE' => 'Penyesuaian Gaji',
    ];

    /**
     * Relationship to employee
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    /**
     * Relationship to user approver
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relationship to user creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Movement label
     */
    public function getMovementTypeLabelAttribute(): string
    {
        return self::TYPES[$this->movement_type] ?? $this->movement_type;
    }

    /**
     * Status badge HTML
     */
    public function getStatusBadgeHtmlAttribute(): string
    {
        return match ($this->status) {
            'APPLIED' => '<span class="badge bg-success-lt text-success fw-bold"><i class="ti ti-check me-1"></i>Diterapkan</span>',
            'APPROVED' => '<span class="badge bg-primary-lt text-primary fw-bold"><i class="ti ti-thumb-up me-1"></i>Disetujui</span>',
            'PENDING' => '<span class="badge bg-warning-lt text-warning fw-bold"><i class="ti ti-hourglass me-1"></i>Menunggu</span>',
            'CANCELLED' => '<span class="badge bg-secondary-lt text-secondary fw-bold"><i class="ti ti-x me-1"></i>Dibatalkan</span>',
            default => '<span class="badge bg-light text-muted">' . e($this->status) . '</span>',
        };
    }
}
