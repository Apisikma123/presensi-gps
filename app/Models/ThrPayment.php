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
            'FINALIZED', 'PAID' => '<span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md" style="background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;"><span class="rounded-full" style="width: 6px; height: 6px; background: #16a34a;"></span>Terbayar</span>',
            'CALCULATED' => '<span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;"><span class="rounded-full" style="width: 6px; height: 6px; background: #0284c7;"></span>Dihitung</span>',
            'DRAFT' => '<span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md" style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a;"><span class="rounded-full" style="width: 6px; height: 6px; background: #d97706;"></span>Draft</span>',
            default => '<span class="badge bg-secondary">' . e($this->status) . '</span>',
        };
    }
}
