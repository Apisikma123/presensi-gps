<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $table = 'announcements';

    protected $fillable = [
        'title',
        'content',
        'category',
        'kode_dept',
        'kode_cabang',
        'attachment',
        'published_at',
        'expires_at',
        'is_pinned',
        'created_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_pinned' => 'boolean',
    ];

    public const CATEGORIES = [
        'GENERAL' => 'Informasi Umum',
        'HOLIDAY' => 'Hari Libur & Cuti Bersama',
        'POLICY' => 'Kebijakan Baru',
        'URGENT' => 'Pengumuman Mendesak',
        'EVENT' => 'Acara / Kegiatan',
    ];

    public function department()
    {
        return $this->belongsTo(Departemen::class, 'kode_dept', 'kode_dept');
    }

    public function branch()
    {
        return $this->belongsTo(Cabang::class, 'kode_cabang', 'kode_cabang');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
