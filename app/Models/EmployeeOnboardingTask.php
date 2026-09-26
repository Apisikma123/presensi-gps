<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOnboardingTask extends Model
{
    use HasFactory;

    protected $table = 'employee_onboarding_tasks';

    protected $fillable = [
        'employee_onboarding_id',
        'task_name',
        'category',
        'due_date',
        'is_completed',
        'completed_at',
        'completed_by',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'due_date' => 'date',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'sort_order' => 'integer',
    ];

    public function onboarding()
    {
        return $this->belongsTo(EmployeeOnboarding::class, 'employee_onboarding_id');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        return OnboardingTemplateTask::CATEGORIES[$this->category] ?? $this->category;
    }
}
