<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryComponent extends Model
{
    use HasFactory;

    protected $table = 'salary_components';

    protected $fillable = [
        'code',
        'name',
        'type',
        'is_fixed',
        'is_taxable',
        'is_bpjs_basis',
        'is_recurring',
        'default_amount',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_fixed' => 'boolean',
        'is_taxable' => 'boolean',
        'is_bpjs_basis' => 'boolean',
        'is_recurring' => 'boolean',
        'default_amount' => 'float',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(EmployeeSalaryAssignment::class, 'salary_component_id');
    }

    public function scopeEarnings(Builder $query): Builder
    {
        return $query->where('type', 'EARNING');
    }

    public function scopeDeductions(Builder $query): Builder
    {
        return $query->where('type', 'DEDUCTION');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
