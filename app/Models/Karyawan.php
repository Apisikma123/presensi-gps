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
}
