<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lembur extends Model
{
    use HasFactory;

    protected $table = 'lembur';

    protected $fillable = [
        'no_spk',
        'tanggal',
        'nik',
        'overtime_policy_id',
        'day_type',
        'lembur_mulai',
        'lembur_selesai',
        'lembur_in',
        'lembur_out',
        'foto_lembur_in',
        'foto_lembur_out',
        'lokasi_lembur_in',
        'lokasi_lembur_out',
        'planned_duration_minutes',
        'actual_duration_minutes',
        'approved_duration_minutes',
        'calculated_rate_hours',
        'status',
        'keterangan',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'lembur_mulai' => 'datetime',
        'lembur_selesai' => 'datetime',
        'lembur_in' => 'datetime',
        'lembur_out' => 'datetime',
        'planned_duration_minutes' => 'integer',
        'actual_duration_minutes' => 'integer',
        'approved_duration_minutes' => 'integer',
        'calculated_rate_hours' => 'float',
        'approved_at' => 'datetime',
    ];

    // Relations
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(OvertimePolicy::class, 'overtime_policy_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'PENDING');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeForEmployee(Builder $query, string $nik): Builder
    {
        return $query->where('nik', $nik);
    }

    public function scopeForMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->whereYear('tanggal', $year)
                     ->whereMonth('tanggal', $month);
    }

    // Accessors & Formatters
    public function getDurationHoursAttribute(): float
    {
        $minutes = $this->approved_duration_minutes ?? $this->actual_duration_minutes ?? $this->planned_duration_minutes;
        return round($minutes / 60, 2);
    }

    public function getFormattedDurationAttribute(): string
    {
        $minutes = $this->approved_duration_minutes ?? $this->actual_duration_minutes ?? $this->planned_duration_minutes;
        $hours = floor($minutes / 60);
        $rem = $minutes % 60;
        if ($hours > 0 && $rem > 0) {
            return "{$hours}j {$rem}m";
        } elseif ($hours > 0) {
            return "{$hours} jam";
        }
        return "{$rem} menit";
    }

    public function getDayTypeLabelAttribute(): string
    {
        return match ($this->day_type) {
            'WORKDAY' => 'Hari Kerja',
            'OFFDAY_5DAYS' => 'Libur (5 Hari Kerja)',
            'OFFDAY_6DAYS' => 'Libur (6 Hari Kerja)',
            'PUBLIC_HOLIDAY' => 'Libur Nasional',
            default => $this->day_type,
        };
    }

    /**
     * DESIGN.md compliant status badge
     */
    public function getStatusBadgeHtmlAttribute(): string
    {
        return match ($this->status) {
            'APPROVED', 'COMPLETED' => '<span class="badge bg-label-success">Disetujui</span>',
            'PENDING' => '<span class="badge bg-label-warning">Menunggu</span>',
            'REJECTED', 'CANCELLED' => '<span class="badge bg-label-danger">Ditolak</span>',
            'DRAFT' => '<span class="badge bg-label-secondary">Draft</span>',
            default => '<span class="badge bg-label-secondary">' . e($this->status) . '</span>',
        };
    }
}
