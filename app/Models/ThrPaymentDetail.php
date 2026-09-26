<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThrPaymentDetail extends Model
{
    use HasFactory;

    protected $table = 'thr_payment_details';

    protected $fillable = [
        'thr_payment_id',
        'nik',
        'hire_date',
        'service_months',
        'base_salary',
        'multiplier',
        'thr_amount',
        'category',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'service_months' => 'float',
        'base_salary' => 'float',
        'multiplier' => 'float',
        'thr_amount' => 'float',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(ThrPayment::class, 'thr_payment_id');
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }
}
