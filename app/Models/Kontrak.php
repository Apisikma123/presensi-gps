<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kontrak extends Model
{
    use HasFactory;

    protected $table = 'kontrak';

    protected $fillable = [
        'no_kontrak',
        'nik',
        'jenis_kontrak',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'jabatan',
        'kode_cabang',
        'kode_dept',
        'gaji_pokok',
        'dokumen',
        'keterangan',
        'reminder_days',
        'reminder_sent',
        'created_by',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'reminder_sent' => 'boolean',
        'reminder_days' => 'integer',
        'gaji_pokok' => 'decimal:2',
    ];

    /**
     * Relationship to employee
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    /**
     * Relationship to branch
     */
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'kode_cabang', 'kode_cabang');
    }

    /**
     * Relationship to department
     */
    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'kode_dept', 'kode_dept');
    }

    /**
     * Relationship to user creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if contract is expiring within reminder_days
     */
    public function getIsExpiringAttribute(): bool
    {
        if (!$this->tanggal_selesai || in_array($this->status, ['EXPIRED', 'RENEWED', 'TERMINATED'])) {
            return false;
        }

        $now = Carbon::today();
        $daysUntilEnd = $now->diffInDays($this->tanggal_selesai, false);

        return $daysUntilEnd >= 0 && $daysUntilEnd <= ($this->reminder_days ?: 30);
    }

    /**
     * Get remaining days until contract ends
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->tanggal_selesai) {
            return null; // PKWTT / indefinite
        }

        $now = Carbon::today();
        return (int) $now->diffInDays($this->tanggal_selesai, false);
    }

    /**
     * Status badge HTML
     */
    public function getStatusBadgeHtmlAttribute(): string
    {
        return match ($this->status) {
            'ACTIVE' => '<span class="badge bg-success-lt text-success fw-bold"><i class="ti ti-check me-1"></i>Aktif</span>',
            'EXPIRING_SOON' => '<span class="badge bg-warning-lt text-warning fw-bold"><i class="ti ti-clock me-1"></i>Segera Berakhir</span>',
            'EXPIRED' => '<span class="badge bg-danger-lt text-danger fw-bold"><i class="ti ti-alert-triangle me-1"></i>Habis</span>',
            'RENEWED' => '<span class="badge bg-info-lt text-info fw-bold"><i class="ti ti-refresh me-1"></i>Diperpanjang</span>',
            'TERMINATED' => '<span class="badge bg-secondary-lt text-secondary fw-bold"><i class="ti ti-x me-1"></i>Diakhiri</span>',
            default => '<span class="badge bg-light text-muted">' . e($this->status) . '</span>',
        };
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    public function scopeExpiring($query, int $days = 30)
    {
        $today = Carbon::today();
        $threshold = Carbon::today()->addDays($days);

        return $query->whereIn('status', ['ACTIVE', 'EXPIRING_SOON'])
            ->whereNotNull('tanggal_selesai')
            ->whereBetween('tanggal_selesai', [$today, $threshold]);
    }

    public function scopeExpired($query)
    {
        $today = Carbon::today();

        return $query->where(function ($q) use ($today) {
            $q->where('status', 'EXPIRED')
              ->orWhere(function ($sub) use ($today) {
                  $sub->whereIn('status', ['ACTIVE', 'EXPIRING_SOON'])
                      ->whereNotNull('tanggal_selesai')
                      ->where('tanggal_selesai', '<', $today);
              });
        });
    }

    /**
     * Recheck and dynamically update status
     */
    public function checkAndUpdateStatus(): string
    {
        if (in_array($this->status, ['RENEWED', 'TERMINATED']) || !$this->tanggal_selesai) {
            return $this->status;
        }

        $today = Carbon::today();
        if ($this->tanggal_selesai->lt($today)) {
            $this->update(['status' => 'EXPIRED']);
            return 'EXPIRED';
        }

        if ($this->is_expiring) {
            if ($this->status !== 'EXPIRING_SOON') {
                $this->update(['status' => 'EXPIRING_SOON']);
            }
            return 'EXPIRING_SOON';
        }

        if ($this->status !== 'ACTIVE') {
            $this->update(['status' => 'ACTIVE']);
        }
        return 'ACTIVE';
    }
}
