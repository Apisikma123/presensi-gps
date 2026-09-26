<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingTemplateTask extends Model
{
    use HasFactory;

    protected $table = 'onboarding_template_tasks';

    protected $fillable = [
        'onboarding_template_id',
        'task_name',
        'category',
        'day_offset',
        'is_mandatory',
        'sort_order',
    ];

    protected $casts = [
        'day_offset' => 'integer',
        'is_mandatory' => 'boolean',
        'sort_order' => 'integer',
    ];

    public const CATEGORIES = [
        'DOCUMENT' => 'Dokumen & Legalitas',
        'IT_ACCESS' => 'Akses IT & Sistem',
        'HR_BRIEFING' => 'Orientasi & Briefing HR',
        'TRAINING' => 'Pelatihan & SOP',
        'ASSET' => 'Perangkat & Aset Kerja',
    ];

    public function template()
    {
        return $this->belongsTo(OnboardingTemplate::class, 'onboarding_template_id');
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
