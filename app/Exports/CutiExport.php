<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CutiExport implements FromView, WithColumnWidths, WithTitle, WithStyles
{
    protected $data;
    protected $view;

    public function __construct(array $data, $view = 'laporan.cuti_excel')
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
        return 'Rekap Cuti ' . ($this->data['tahun'] ?? '');
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 14,
            'C' => 28,
            'D' => 20,
            'E' => 7,
            'F' => 7,
            'G' => 7,
            'H' => 7,
            'I' => 7,
            'J' => 7,
            'K' => 7,
            'L' => 7,
            'M' => 7,
            'N' => 7,
            'O' => 7,
            'P' => 7,
            'Q' => 14,
            'R' => 12,
            'S' => 12,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setShowGridlines(true);
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Segoe UI');
        return [];
    }
}
