<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PresensiKaryawanExport implements FromView, WithColumnWidths, WithTitle, WithStyles, WithDrawings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('laporan.presensi_karyawan_excel', $this->data);
    }

    public function title(): string
    {
        return 'Laporan Presensi Karyawan';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 14,
            'C' => 14,
            'D' => 20,
            'E' => 14,
            'F' => 14,
            'G' => 18,
            'H' => 32,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setShowGridlines(true);
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Segoe UI');
        return [];
    }

    public function drawings()
    {
        $karyawan = $this->data['karyawan'] ?? null;
        $drawings = [];

        if ($karyawan && !empty($karyawan->foto)) {
            $path = storage_path('app/public/karyawan/' . $karyawan->foto);
            $targetPath = null;
            if (file_exists($path)) {
                $targetPath = $path;
            } else {
                $publicPath = public_path('storage/karyawan/' . $karyawan->foto);
                if (file_exists($publicPath)) {
                    $targetPath = $publicPath;
                }
            }

            if ($targetPath) {
                $drawing = new Drawing();
                $drawing->setName('Foto Karyawan');
                $drawing->setDescription('Foto Karyawan');
                $drawing->setPath($targetPath);
                $drawing->setHeight(75);
                $drawing->setCoordinates('H4');
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(4);
                $drawings[] = $drawing;
            }
        }
        
        return $drawings;
    }
}
