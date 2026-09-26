<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OvertimePolicy extends Model
{
    use HasFactory;

    protected $table = 'overtime_policies';

    protected $fillable = [
        'name',
        'code',
        'version',
        'effective_from',
        'effective_to',
        'rules',
        'source_reference',
        'max_hours_per_day',
        'max_hours_per_week',
        'requires_meal_allowance_after_4h',
        'is_active',
    ];

    protected $casts = [
        'rules' => 'array',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'max_hours_per_day' => 'float',
        'max_hours_per_week' => 'float',
        'requires_meal_allowance_after_4h' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Active policy singleton / cached query
     */
    public static function getDefaultPolicy(): self
    {
        $policy = self::where('is_active', true)
            ->where('code', 'DEPNAKER_STANDARD')
            ->first();

        if ($policy) {
            return $policy;
        }

        $anyActive = self::where('is_active', true)->first();
        if ($anyActive) {
            return $anyActive;
        }

        // Fallback default instance (unsaved or save on demand)
        return self::create([
            'name' => 'Standar Depnaker (PP 35/2021 & Kepmenakertrans 102/2004)',
            'code' => 'DEPNAKER_STANDARD',
            'version' => '1.0',
            'effective_from' => '2021-02-02',
            'source_reference' => 'PP No. 35 Tahun 2021 Pasal 31 & Kepmenakertrans No. KEP.102/MEN/VI/2004',
            'max_hours_per_day' => 4.0,
            'max_hours_per_week' => 18.0,
            'requires_meal_allowance_after_4h' => true,
            'is_active' => true,
            'rules' => self::defaultDepnakerRules(),
        ]);
    }

    /**
     * Standard statutory rule definition
     */
    public static function defaultDepnakerRules(): array
    {
        return [
            'WORKDAY' => [
                ['from_hour' => 0, 'to_hour' => 1, 'multiplier' => 1.5],
                ['from_hour' => 1, 'to_hour' => null, 'multiplier' => 2.0],
            ],
            'OFFDAY_5DAYS' => [
                ['from_hour' => 0, 'to_hour' => 8, 'multiplier' => 2.0],
                ['from_hour' => 8, 'to_hour' => 9, 'multiplier' => 3.0],
                ['from_hour' => 9, 'to_hour' => null, 'multiplier' => 4.0],
            ],
            'OFFDAY_6DAYS' => [
                ['from_hour' => 0, 'to_hour' => 7, 'multiplier' => 2.0],
                ['from_hour' => 7, 'to_hour' => 8, 'multiplier' => 3.0],
                ['from_hour' => 8, 'to_hour' => null, 'multiplier' => 4.0],
            ],
            'PUBLIC_HOLIDAY' => [
                ['from_hour' => 0, 'to_hour' => 8, 'multiplier' => 2.0],
                ['from_hour' => 8, 'to_hour' => 9, 'multiplier' => 3.0],
                ['from_hour' => 9, 'to_hour' => null, 'multiplier' => 4.0],
            ],
        ];
    }

    public function lemburs(): HasMany
    {
        return $this->hasMany(Lembur::class, 'overtime_policy_id');
    }
}
