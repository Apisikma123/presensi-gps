<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TemplateKaryawanExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        return [
            'nik',
            'nama_karyawan',
            'no_hp',
            'jenis_kelamin',
            'alamat',
            'kode_cabang',
            'kode_dept',
            'kode_jabatan',
            'kode_jam_kerja',
            'tanggal_masuk',
            'status_karyawan',
            'status_aktif_karyawan'
        ];
    }

    public function title(): string
    {
        return 'Template Import Karyawan';
    }
}
