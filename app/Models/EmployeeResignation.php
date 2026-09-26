<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeResignation extends Model
{
    use HasFactory;

    protected $table = 'employee_resignations';

    protected $fillable = [
        'nik',
        'tanggal_pengajuan',
        'tanggal_keluar',
        'kategori_keluar',
        'alasan',
        'status_clearance',
        'dokumen',
        'catatan_hr',
        'status',
        'approved_by',
        'severance_pay',
        'service_pay',
        'compensation_pay',
        'final_salary_pay',
        'deductions_pay',
        'total_settlement',
        'settlement_status',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_keluar' => 'date',
        'severance_pay' => 'decimal:2',
        'service_pay' => 'decimal:2',
        'compensation_pay' => 'decimal:2',
        'final_salary_pay' => 'decimal:2',
        'deductions_pay' => 'decimal:2',
        'total_settlement' => 'decimal:2',
    ];

    public const CATEGORIES = [
        'RESIGNED' => 'Mengundurkan Diri (Resign)',
        'END_OF_CONTRACT' => 'Kontrak Selesai / Habis',
        'TERMINATED' => 'Pemutusan Hubungan Kerja (PHK)',
        'RETIRED' => 'Pensiun',
        'DECEASED' => 'Meninggal Dunia',
        'OTHER' => 'Lainnya',
    ];

    /**
     * Relationship to employee
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    /**
     * Relationship to approver
     */
     public function approver()
     {
         return $this->belongsTo(User::class, 'approved_by');
     }

    /**
     * Relationship to clearances
     */
    public function clearances()
    {
        return $this->hasMany(OffboardingClearance::class, 'employee_resignation_id');
    }

    /**
     * Category label
     */
    public function getKategoriKeluarLabelAttribute(): string
    {
        return self::CATEGORIES[$this->kategori_keluar] ?? $this->kategori_keluar;
    }

    /**
     * Status badge HTML
     */
    public function getStatusBadgeHtmlAttribute(): string
    {
        return match ($this->status) {
            'APPROVED' => '<span class="badge bg-danger-lt text-danger fw-bold"><i class="ti ti-check me-1"></i>Disetujui</span>',
            'PENDING' => '<span class="badge bg-warning-lt text-warning fw-bold"><i class="ti ti-clock me-1"></i>Menunggu</span>',
            'REJECTED' => '<span class="badge bg-secondary-lt text-secondary fw-bold"><i class="ti ti-x me-1"></i>Ditolak</span>',
            default => '<span class="badge bg-light text-muted">' . e($this->status) . '</span>',
        };
    }

    /**
     * Clearance badge HTML
     */
    public function getClearanceBadgeHtmlAttribute(): string
    {
        return match ($this->status_clearance) {
            'CLEARED' => '<span class="badge bg-success-lt text-success fw-bold"><i class="ti ti-shield-check me-1"></i>Selesai / Cleared</span>',
            'IN_PROGRESS' => '<span class="badge bg-info-lt text-info fw-bold"><i class="ti ti-rotate-clockwise me-1"></i>Dalam Proses</span>',
            'PENDING' => '<span class="badge bg-warning-lt text-warning fw-bold"><i class="ti ti-clock me-1"></i>Pending Handover</span>',
            default => '<span class="badge bg-light text-muted">' . e($this->status_clearance) . '</span>',
        };
    }
}
