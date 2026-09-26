<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingTemplate extends Model
{
    use HasFactory;

    protected $table = 'onboarding_templates';

    protected $fillable = [
        'name',
        'description',
        'kode_dept',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(Departemen::class, 'kode_dept', 'kode_dept');
    }

    public function tasks()
    {
        return $this->hasMany(OnboardingTemplateTask::class, 'onboarding_template_id')->orderBy('sort_order');
    }

    public function onboardings()
    {
        return $this->hasMany(EmployeeOnboarding::class, 'onboarding_template_id');
    }
}
