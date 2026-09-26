<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveQuotaTransaction extends Model
{
    use HasFactory;

    protected $table = 'leave_quota_transactions';

    protected $fillable = [
        'leave_quota_id',
        'type',
        'amount',
        'reference_type',
        'reference_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:1',
    ];

    /**
     * Relationship to parent quota record
     */
    public function quota()
    {
        return $this->belongsTo(LeaveQuota::class, 'leave_quota_id');
    }

    /**
     * Relationship to user creator / auditor
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
