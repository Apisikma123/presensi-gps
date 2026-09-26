<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndonesiaPolicyRule extends Model
{
    use HasFactory;

    protected $table = 'indonesia_policy_rules';

    protected $fillable = [
        'policy_type',
        'name',
        'version',
        'effective_from',
        'effective_to',
        'source_reference',
        'config',
        'is_active',
    ];

    protected $casts = [
        'config' => 'array',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('policy_type', $type);
    }

    public static function getActiveRule(string $type): ?self
    {
        return self::where('policy_type', $type)
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->first();
    }
}
