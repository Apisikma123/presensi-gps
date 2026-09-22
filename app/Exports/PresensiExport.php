<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PresensiExport implements FromView, WithColumnWidths, WithTitle, WithStyles
{
    protected $data;
    protected $view;

    public function __construct(array $data, $view = 'laporan.presensi_excel')
    {
        $this->data = $data;
        $this->view = $view;
    }

    public function view(): View
    {
        return view($this->view, $this->data);
    }

    public function title(): string
    {
        return 'Rekap Presensi';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 14,
            'C' => 28,
            'D' => 20,
            'E' => 16,
            'F' => 12,
            'G' => 12,
            'H' => 12,
            'I' => 10,
            'J' => 10,
            'K' => 10,
            'L' => 10,
            'M' => 14,
            'N' => 10,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setShowGridlines(true);
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Segoe UI');
        return [];
    }
}
