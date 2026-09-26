<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAsset extends Model
{
    use HasFactory;

    protected $table = 'employee_assets';

    protected $fillable = [
        'asset_code',
        'nik',
        'name',
        'category',
        'serial_number',
        'assigned_date',
        'returned_date',
        'condition',
        'notes',
        'status',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'returned_date' => 'date',
    ];

    public const CATEGORIES = [
        'HARDWARE' => 'Perangkat IT / Laptop / Tablet',
        'VEHICLE' => 'Kendaraan Operasional',
        'OFFICE_EQUIPMENT' => 'Peralatan Kantor / Toko',
        'ACCESS_CARD' => 'Kartu Akses / Kunci',
        'OTHER' => 'Inventaris Lainnya',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getStatusBadgeHtmlAttribute(): string
    {
        return match ($this->status) {
            'ASSIGNED' => '<span class="badge bg-primary-lt text-primary fw-bold">Sedang Dipinjam</span>',
            'RETURNED' => '<span class="badge bg-success-lt text-success fw-bold">Sudah Dikembalikan</span>',
            'LOST' => '<span class="badge bg-danger-lt text-danger fw-bold">Hilang</span>',
            'UNDER_MAINTENANCE' => '<span class="badge bg-warning-lt text-warning fw-bold">Perbaikan</span>',
            default => '<span class="badge bg-light text-muted">' . e($this->status) . '</span>',
        };
    }
}
