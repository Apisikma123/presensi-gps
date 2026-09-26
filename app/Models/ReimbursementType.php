<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReimbursementType extends Model
{
    use HasFactory;

    protected $table = 'reimbursement_types';

    protected $fillable = [
        'code',
        'name',
        'max_limit',
        'is_active',
    ];

    protected $casts = [
        'max_limit' => 'float',
        'is_active' => 'boolean',
    ];

    public function reimbursements(): HasMany
    {
        return $this->hasMany(Reimbursement::class, 'reimbursement_type_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
