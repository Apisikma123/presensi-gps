<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveQuota extends Model
{
    use HasFactory;

    protected $table = 'leave_quotas';

    protected $fillable = [
        'nik',
        'leave_type_id',
        'year',
        'opening_balance',
        'earned',
        'used',
        'adjustment',
        'carry_forward',
        'expired',
        'closing_balance',
    ];

    protected $casts = [
        'year' => 'integer',
        'opening_balance' => 'decimal:1',
        'earned' => 'decimal:1',
        'used' => 'decimal:1',
        'adjustment' => 'decimal:1',
        'carry_forward' => 'decimal:1',
        'expired' => 'decimal:1',
        'closing_balance' => 'decimal:1',
    ];

    /**
     * Relationship to employee
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    /**
     * Relationship to leave category type
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    /**
     * Relationship to quota transaction audit ledger
     */
    public function transactions()
    {
        return $this->hasMany(LeaveQuotaTransaction::class, 'leave_quota_id')->orderBy('created_at', 'desc');
    }

    /**
     * Recalculate closing balance
     */
    public function recalculateBalance(): float
    {
        $closing = ($this->opening_balance + $this->earned + $this->carry_forward + $this->adjustment) - ($this->used + $this->expired);
        $this->closing_balance = max(0.0, (float) $closing);
        $this->save();

        return (float) $this->closing_balance;
    }
}
