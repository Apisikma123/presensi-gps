<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Karyawan extends Model
{
    use HasFactory;
    protected $table = "karyawan";
    protected $primaryKey = "nik";
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
    protected $casts = [
        'kode_cabang_array' => 'array',
    ];

    function getRekapstatuskaryawan($request = null)
    {
        // Get Total Active Employee Count
        $queryAktif = Karyawan::query();
        if (!empty($request->kode_cabang)) {
            if (is_array($request->kode_cabang)) {
                $queryAktif->whereIn('karyawan.kode_cabang', $request->kode_cabang);
            } else {
                $queryAktif->where('karyawan.kode_cabang', $request->kode_cabang);
            }
        }
        if (!empty($request->kode_dept)) {
            if (is_array($request->kode_dept)) {
                $queryAktif->whereIn('karyawan.kode_dept', $request->kode_dept);
            } else {
                $queryAktif->where('karyawan.kode_dept', $request->kode_dept);
            }
        }
        $jml_aktif = $queryAktif->where('status_aktif_karyawan', '1')->count();

        // Get Dynamic Status Recapitulation
        $queryRekap = DB::table('status_karyawan')
            ->leftJoin('karyawan', function($join) use ($request) {
                $join->on('status_karyawan.kode_status_karyawan', '=', 'karyawan.status_karyawan');
                if (!empty($request->kode_cabang)) {
                    if (is_array($request->kode_cabang)) {
                        $join->whereIn('karyawan.kode_cabang', $request->kode_cabang);
                    } else {
                        $join->where('karyawan.kode_cabang', '=', $request->kode_cabang);
                    }
                }
                if (!empty($request->kode_dept)) {
                    if (is_array($request->kode_dept)) {
                        $join->whereIn('karyawan.kode_dept', $request->kode_dept);
                    } else {
                        $join->where('karyawan.kode_dept', '=', $request->kode_dept);
                    }
                }
            })
            ->select('status_karyawan.nama_status_karyawan', DB::raw('count(karyawan.nik) as total'))
            ->groupBy('status_karyawan.nama_status_karyawan', 'status_karyawan.kode_status_karyawan')
            ->orderBy('status_karyawan.kode_status_karyawan')
            ->get();

        return (object) [
            'jml_aktif' => $jml_aktif,
            'rekap_status' => $queryRekap
        ];
    }

    // Relasi dengan Facerecognition
    public function facerecognition()
    {
        return $this->hasMany(Facerecognition::class, 'nik', 'nik');
    }





    // Relasi dengan GrupDetail
    // public function grupDetail()
    // {
    //     return $this->hasMany(GrupDetail::class, 'nik', 'nik');
    // }

    // Relasi ke Grup melalui GrupDetail
    // public function grup()
    // {
    //     return $this->hasManyThrough(Grup::class, GrupDetail::class, 'nik', 'kode_grup', 'nik', 'kode_grup');
    // }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'kode_jabatan', 'kode_jabatan');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'kode_dept', 'kode_dept');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'kode_cabang', 'kode_cabang');
    }

    public function jamkerja()
    {
        return $this->belongsTo(Jamkerja::class, 'kode_jam_kerja', 'kode_jam_kerja');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'kode_divisi', 'kode_divisi');
    }

    public function supervisor()
    {
        return $this->belongsTo(Karyawan::class, 'direct_supervisor_nik', 'nik');
    }

    public function subordinates()
    {
        return $this->hasMany(Karyawan::class, 'direct_supervisor_nik', 'nik');
    }

    /**
     * Masked KTP for privacy
     */
    public function getMaskedNoKtpAttribute(): ?string
    {
        if (empty($this->no_ktp)) return null;
        $len = strlen($this->no_ktp);
        if ($len <= 6) return str_repeat('*', $len);
        return substr($this->no_ktp, 0, 4) . str_repeat('*', max(0, $len - 6)) . substr($this->no_ktp, -2);
    }

    /**
     * Masked NPWP for privacy
     */
    public function getMaskedNpwpAttribute(): ?string
    {
        if (empty($this->npwp_number)) return null;
        $len = strlen($this->npwp_number);
        if ($len <= 6) return str_repeat('*', $len);
        return substr($this->npwp_number, 0, 4) . str_repeat('*', max(0, $len - 6)) . substr($this->npwp_number, -2);
    }

    /**
     * Masked Bank Account Number for privacy
     */
    public function getMaskedNoRekeningAttribute(): ?string
    {
        if (empty($this->no_rekening)) return null;
        $len = strlen($this->no_rekening);
        if ($len <= 5) return str_repeat('*', $len);
        return substr($this->no_rekening, 0, 3) . str_repeat('*', max(0, $len - 5)) . substr($this->no_rekening, -2);
    }

    /**
     * Relationship to employee contracts
     */
    public function kontraks()
    {
        return $this->hasMany(Kontrak::class, 'nik', 'nik')->orderBy('tanggal_mulai', 'desc');
    }

    /**
     * Relationship to active employee contract
     */
    public function activeKontrak()
    {
        return $this->hasOne(Kontrak::class, 'nik', 'nik')
            ->whereIn('status', ['ACTIVE', 'EXPIRING_SOON'])
            ->latestOfMany('tanggal_mulai');
    }

    /**
     * Relationship to employee career movements / mutasi
     */
    public function movements()
    {
        return $this->hasMany(EmployeeMovement::class, 'nik', 'nik')->orderBy('effective_date', 'desc');
    }

    /**
     * Relationship to employee resignations
     */
    public function resignations()
    {
        return $this->hasMany(EmployeeResignation::class, 'nik', 'nik')->orderBy('tanggal_keluar', 'desc');
    }

    /**
     * Universal Lifecycle Status
     */
    public function getLifecycleStatusAttribute(): string
    {
        if ($this->status_aktif_karyawan === '0') {
            $lastResign = $this->resignations->first();
            return $lastResign ? $lastResign->kategori_keluar : 'INACTIVE';
        }

        $activeContract = $this->activeKontrak;
        if ($activeContract && $activeContract->status === 'EXPIRING_SOON') {
            return 'EXPIRING_SOON';
        }

        return 'ACTIVE';
    }

    /**
     * Universal Lifecycle Badge HTML
     */
    public function getLifecycleBadgeHtmlAttribute(): string
    {
        return match ($this->lifecycle_status) {
            'ACTIVE' => '<span class="badge bg-success-lt text-success fw-bold"><i class="ti ti-circle-check me-1"></i>Aktif</span>',
            'EXPIRING_SOON' => '<span class="badge bg-warning-lt text-warning fw-bold"><i class="ti ti-clock me-1"></i>Kontrak Menipis</span>',
            'RESIGNED' => '<span class="badge bg-secondary-lt text-secondary fw-bold"><i class="ti ti-user-x me-1"></i>Resigned</span>',
            'END_OF_CONTRACT' => '<span class="badge bg-secondary-lt text-secondary fw-bold"><i class="ti ti-file-x me-1"></i>Kontrak Habis</span>',
            'TERMINATED' => '<span class="badge bg-danger-lt text-danger fw-bold"><i class="ti ti-ban me-1"></i>PHK</span>',
            'RETIRED' => '<span class="badge bg-info-lt text-info fw-bold"><i class="ti ti-award me-1"></i>Pensiun</span>',
            default => '<span class="badge bg-danger-lt text-danger fw-bold"><i class="ti ti-user-off me-1"></i>Nonaktif</span>',
        };
    }

    /**
     * Relationship to employee overtime (lembur) records
     */
    public function lemburs()
    {
        return $this->hasMany(Lembur::class, 'nik', 'nik')->orderBy('tanggal', 'desc');
    }

    /**
     * Relationship to employee salary assignments
     */
    public function salaryAssignments()
    {
        return $this->hasMany(EmployeeSalaryAssignment::class, 'nik', 'nik')->where('is_active', true);
    }

    /**
     * Relationship to employee payroll calculation snapshots
     */
    public function payrollDetails()
    {
        return $this->hasMany(PayrollDetail::class, 'nik', 'nik')->orderBy('payroll_period_id', 'desc');
    }

    public function dpt()
    {
        return $this->departemen();
    }

    public function reimbursements()
    {
        return $this->hasMany(Reimbursement::class, 'nik', 'nik');
    }

    public function loans()
    {
        return $this->hasMany(EmployeeLoan::class, 'nik', 'nik');
    }
}
