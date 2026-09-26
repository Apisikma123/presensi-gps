<?php

namespace App\Exports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KaryawanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Karyawan::query()
            ->select(
                'karyawan.*',
                'departemen.nama_dept',
                'jabatan.nama_jabatan',
                'cabang.nama_cabang',
                'presensi_jamkerja.nama_jam_kerja',
                'divisions.nama_divisi',
                'spv.nama_karyawan as nama_supervisor'
            )
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('presensi_jamkerja', 'karyawan.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->leftJoin('divisions', 'karyawan.kode_divisi', '=', 'divisions.kode_divisi')
            ->leftJoin('karyawan as spv', 'karyawan.direct_supervisor_nik', '=', 'spv.nik')
            ->orderBy('nama_karyawan', 'asc');

        if (!empty($this->filters['nama_karyawan'])) {
            $nama = $this->filters['nama_karyawan'];
            $query->where(function ($sub) use ($nama) {
                $sub->where('karyawan.nama_karyawan', 'like', '%' . $nama . '%')
                    ->orWhere('karyawan.nik', 'like', '%' . $nama . '%')
                    ->orWhere('karyawan.nik_show', 'like', '%' . $nama . '%');
            });
        }

        if (!empty($this->filters['kode_cabang'])) {
            $query->where('karyawan.kode_cabang', $this->filters['kode_cabang']);
        }

        if (!empty($this->filters['kode_dept'])) {
            $query->where('karyawan.kode_dept', $this->filters['kode_dept']);
        }

        if (!empty($this->filters['kode_jabatan'])) {
            $query->where('karyawan.kode_jabatan', $this->filters['kode_jabatan']);
        }

        if (!empty($this->filters['kode_jam_kerja'])) {
            $query->where('karyawan.kode_jam_kerja', $this->filters['kode_jam_kerja']);
        }

        $user = auth()->user();
        if ($user && !$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!empty($userCabangs)) {
                $query->whereIn('karyawan.kode_cabang', $userCabangs);
            } else {
                $query->whereRaw('1 = 0');
            }

            if (!empty($userDepartemens)) {
                $query->whereIn('karyawan.kode_dept', $userDepartemens);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'NIK',
            'NIK Perusahaan',
            'No. KTP',
            'Nama Karyawan',
            'Jenis Kelamin',
            'Agama',
            'Kewarganegaraan',
            'No. HP',
            'Email Pribadi',
            'Email Kantor',
            'Alamat',
            'Cabang',
            'Departemen',
            'Divisi / Tim',
            'Jabatan',
            'Grade Level',
            'Atasan Langsung',
            'Tipe Hubungan Kerja',
            'Jam Kerja',
            'Status Keaktifan',
            'Tanggal Masuk',
            'Tanggal Nonaktif',
            'Bank',
            'No. Rekening',
            'Nama Rekening',
            'NPWP',
            'BPJS Kesehatan',
            'BPJS TK',
            'Kontak Darurat',
            'Hubungan Kontak'
        ];
    }

    public function map($karyawan): array
    {
        $jenisKelamin = $karyawan->jenis_kelamin == 'L' ? 'Laki-laki' : ($karyawan->jenis_kelamin == 'P' ? 'Perempuan' : ($karyawan->jenis_kelamin ?: '-'));
        $statusAktif = $karyawan->status_aktif_karyawan == 1 ? 'Aktif' : 'Non Aktif';
        $user = auth()->user();
        $canSensitive = $user && $user->can('employee.sensitive_data');

        $noKtp = $canSensitive ? ($karyawan->no_ktp ?: '-') : ($karyawan->masked_no_ktp ?: '-');
        $npwp = $canSensitive ? ($karyawan->npwp_number ?: '-') : ($karyawan->masked_npwp ?: '-');
        $noRekening = $canSensitive ? ($karyawan->no_rekening ?: '-') : ($karyawan->masked_no_rekening ?: '-');

        $data = [
            "'" . $karyawan->nik,
            $karyawan->nik_show ?: $karyawan->nik,
            $noKtp,
            $karyawan->nama_karyawan,
            $jenisKelamin,
            $karyawan->religion ?: '-',
            $karyawan->nationality ?: 'WNI',
            $karyawan->no_hp ?: '-',
            $karyawan->personal_email ?: '-',
            $karyawan->company_email ?: ($karyawan->email ?: '-'),
            $karyawan->alamat ?: '-',
            $karyawan->nama_cabang ?: $karyawan->kode_cabang,
            $karyawan->nama_dept ?: $karyawan->kode_dept,
            $karyawan->nama_divisi ?: '-',
            $karyawan->nama_jabatan ?: $karyawan->kode_jabatan,
            $karyawan->grade_level ?: '-',
            $karyawan->nama_supervisor ?: '-',
            $karyawan->employment_type ?: 'PKWT',
            $karyawan->nama_jam_kerja ?: $karyawan->kode_jam_kerja,
            $statusAktif,
            $karyawan->tanggal_masuk ?: '-',
            $karyawan->tanggal_nonaktif ?: '-',
            $karyawan->nama_bank ?: '-',
            $noRekening,
            $karyawan->nama_rekening ?: '-',
            $npwp,
            $karyawan->bpjs_kesehatan_number ?: '-',
            $karyawan->bpjs_ketenagakerjaan_number ?: '-',
            $karyawan->kontak_darurat ?: '-',
            $karyawan->hubungan_kontak_darurat ?: '-'
        ];

        return array_map(function ($value) {
            if (is_string($value) && preg_match('/^[=\+\-@\t\r]/', $value)) {
                return "'" . $value;
            }
            return $value;
        }, $data);
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Style the headers (Row 1)
        $sheet->getRowDimension(1)->setRowHeight(25);
        $headerStyle = $sheet->getStyle('A1:' . $highestColumn . '1');
        
        $headerStyle->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF1F4E78');
        $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $headerStyle->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Apply borders and vertical alignment to all cells
        $bodyStyle = $sheet->getStyle('A1:' . $highestColumn . $highestRow);
        $bodyStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $bodyStyle->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        return [];
    }
}
