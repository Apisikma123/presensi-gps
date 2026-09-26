<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeIncident extends Model
{
    use HasFactory;

    protected $table = 'employee_incidents';

    protected $fillable = [
        'case_number',
        'reporter_nik',
        'subject_nik',
        'title',
        'incident_date',
        'category',
        'description',
        'resolution_notes',
        'status',
        'handled_by',
        'resolved_at',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'resolved_at' => 'datetime',
    ];

    public const CATEGORIES = [
        'DISCIPLINARY' => 'Pelanggaran Disiplin Kerja',
        'HARASSMENT' => 'Pelecehan / Ketidaknyamanan',
        'SAFETY_ACCIDENT' => 'Insiden K3 & Keselamatan',
        'FRAUD' => 'Kecurangan & Fraud',
        'DISPUTE' => 'Perselisihan Antar Karyawan',
        'OTHER' => 'Kasus Lainnya',
    ];

    public function reporter()
    {
        return $this->belongsTo(Karyawan::class, 'reporter_nik', 'nik');
    }

    public function subject()
    {
        return $this->belongsTo(Karyawan::class, 'subject_nik', 'nik');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getStatusBadgeHtmlAttribute(): string
    {
        return match ($this->status) {
            'OPEN' => '<span class="badge bg-danger-lt text-danger fw-bold">Kasus Baru</span>',
            'INVESTIGATING' => '<span class="badge bg-warning-lt text-warning fw-bold">Investigasi</span>',
            'RESOLVED' => '<span class="badge bg-success-lt text-success fw-bold">Selesai (Resolved)</span>',
            'CLOSED' => '<span class="badge bg-secondary-lt text-secondary fw-bold">Ditutup</span>',
            default => '<span class="badge bg-light text-muted">' . e($this->status) . '</span>',
        };
    }
}
