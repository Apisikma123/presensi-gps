<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeTraining extends Model
{
    use HasFactory;

    protected $table = 'employee_trainings';

    protected $fillable = [
        'training_code',
        'nik',
        'title',
        'provider',
        'start_date',
        'end_date',
        'duration_hours',
        'cost',
        'certificate_number',
        'certificate_file',
        'status',
        'score',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'duration_hours' => 'integer',
        'cost' => 'decimal:2',
        'score' => 'decimal:2',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function getStatusBadgeHtmlAttribute(): string
    {
        return match ($this->status) {
            'COMPLETED' => '<span class="badge bg-success-lt text-success fw-bold"><i class="ti ti-check me-1"></i>Selesai</span>',
            'ONGOING' => '<span class="badge bg-info-lt text-info fw-bold"><i class="ti ti-clock me-1"></i>Sedang Berjalan</span>',
            'SCHEDULED' => '<span class="badge bg-warning-lt text-warning fw-bold">Dijadwalkan</span>',
            'CANCELLED' => '<span class="badge bg-secondary-lt text-secondary fw-bold">Dibatalkan</span>',
            default => '<span class="badge bg-light text-muted">' . e($this->status) . '</span>',
        };
    }
}
