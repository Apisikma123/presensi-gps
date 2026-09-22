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
                'presensi_jamkerja.nama_jam_kerja'
            )
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('presensi_jamkerja', 'karyawan.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
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
            'Nama Karyawan',
            'Jenis Kelamin',
            'No. HP',
            'Email',
            'Alamat',
            'Cabang',
            'Departemen',
            'Jabatan',
            'Jam Kerja',
            'Status Karyawan',
            'Status Keaktifan',
            'Tanggal Masuk',
            'Tanggal Nonaktif',
            'Lock Lokasi',
            'Lock Jam Kerja',
            'RFID UID'
        ];
    }

    public function map($karyawan): array
    {
        $statusKaryawan = $karyawan->status_karyawan == 'K' ? 'Kontrak' : ($karyawan->status_karyawan == 'T' ? 'Tetap' : ($karyawan->status_karyawan ?: '-'));
        $jenisKelamin = $karyawan->jenis_kelamin == 'L' ? 'Laki-laki' : ($karyawan->jenis_kelamin == 'P' ? 'Perempuan' : ($karyawan->jenis_kelamin ?: '-'));
        $statusAktif = $karyawan->status_aktif_karyawan == 1 ? 'Aktif' : 'Non Aktif';
        $lockLocation = $karyawan->lock_location == 1 ? 'Ya' : 'Tidak';
        $lockJamKerja = $karyawan->lock_jam_kerja == 1 ? 'Ya' : 'Tidak';

        $data = [
            "'" . $karyawan->nik,
            $karyawan->nik_show ?: $karyawan->nik,
            $karyawan->nama_karyawan,
            $jenisKelamin,
            $karyawan->no_hp ?: '-',
            $karyawan->email ?: '-',
            $karyawan->alamat ?: '-',
            $karyawan->nama_cabang ?: $karyawan->kode_cabang,
            $karyawan->nama_dept ?: $karyawan->kode_dept,
            $karyawan->nama_jabatan ?: $karyawan->kode_jabatan,
            $karyawan->nama_jam_kerja ?: $karyawan->kode_jam_kerja,
            $statusKaryawan,
            $statusAktif,
            $karyawan->tanggal_masuk ?: '-',
            $karyawan->tanggal_nonaktif ?: '-',
            $lockLocation,
            $lockJamKerja,
            $karyawan->rfid_uid ?: '-'
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
