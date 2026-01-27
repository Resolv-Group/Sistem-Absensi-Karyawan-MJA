<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class PenilaianExport implements FromView, WithStyles, WithColumnWidths
{
    public function __construct(
        public $data,
        public $unit,
        public $divisi,
        public $supervisor,
    ) {}

    public function view(): View
    {
        return view('Exports.penilaian', [
            'data'   => $this->data,
            'unit'   => $this->unit,
            'divisi' => $this->divisi,
            'supervisor' => $this->supervisor,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getParent()->getDefaultStyle()->getFont()
            ->setName('Times New Roman')
            ->setSize(12);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 4,
            'C' => 4,
            'D' => 6,
            'E' => 4,
            'B' => 25,
            'P' => 16,
        ];
    }
}


