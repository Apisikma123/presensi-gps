<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyPolicy extends Model
{
    use HasFactory;

    protected $table = 'company_policies';

    protected $fillable = [
        'policy_code',
        'title',
        'category',
        'effective_date',
        'version',
        'file_path',
        'description',
        'is_active',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'is_active' => 'boolean',
    ];

    public const CATEGORIES = [
        'PERATURAN_PERUSAHAAN' => 'Peraturan Perusahaan (PP)',
        'SOP_OPERASIONAL' => 'Standar Operasional Prosedur (SOP)',
        'SURAT_EDARAN' => 'Surat Edaran Direksi',
        'CODE_OF_CONDUCT' => 'Kode Etik & Tata Tertib',
        'KEBIJAKAN_HR' => 'Kebijakan HR & Benefit',
    ];

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
