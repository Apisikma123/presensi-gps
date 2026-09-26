<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSalaryAssignment extends Model
{
    use HasFactory;

    protected $table = 'employee_salary_assignments';

    protected $fillable = [
        'nik',
        'salary_component_id',
        'amount',
        'effective_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'float',
        'effective_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(SalaryComponent::class, 'salary_component_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForEmployee(Builder $query, string $nik): Builder
    {
        return $query->where('nik', $nik);
    }
}
