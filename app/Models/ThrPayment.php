<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThrPayment extends Model
{
    use HasFactory;

    protected $table = 'thr_payments';

    protected $fillable = [
        'event_name',
        'year',
        'distribution_date',
        'status',
        'total_amount',
        'employee_count',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'year' => 'integer',
        'distribution_date' => 'date',
        'total_amount' => 'float',
        'employee_count' => 'integer',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(ThrPaymentDetail::class, 'thr_payment_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusBadgeHtmlAttribute(): string
    {
        return match ($this->status) {
            'FINALIZED', 'PAID' => '<span class="badge bg-label-success">Terbayar</span>',
            'CALCULATED' => '<span class="badge bg-label-info">Dihitung</span>',
            'DRAFT' => '<span class="badge bg-label-warning">Draft</span>',
            default => '<span class="badge bg-label-secondary">' . e($this->status) . '</span>',
        };
    }
}
