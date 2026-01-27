<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use MaatWebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\Border;

class KwitansiBoronganExport implements FromView, WithStyles, WithColumnWidths
{
    public function __construct(
        public $resi,
        public $nama_unit,
        public $terbilang,
        public $bidangUsaha,
        public $MitraKerja,
        public $periode,
        public $total_tagihan,
    ) {}

    public function view(): View
    {
        return view('Exports.kwitansi-borongan', [
            'resi' => $this->resi,
            'nama_unit' => $this->nama_unit,
            'terbilang' => $this->terbilang,
            'bidangUsaha' => $this->bidangUsaha,
            'MitraKerja' => $this->MitraKerja,
            'periode' => $this->periode,
            'total_tagihan' => $this->total_tagihan,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('O11:P11')->applyFromArray([
            'font' => [
                'bold' => true,
                'name' => 'Cambria',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical'   => 'center',
            ],
        ]);

        $sheet->getStyle('E23:I23')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical'   => 'center',
            ],
        ]);
        
        return [
            // Rotate text 90 degrees in column A
            'A2' => [
                'alignment' => [
                    'textRotation' => -90, // 90 or -90 for vertical
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],

            'R2' => [
                'alignment' => [
                    'textRotation' => -90, // 90 or -90 for vertical
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];

        
    }

    public function columnWidths(): array
    {
        return [
            'B' => 5.78,
            'C' => 9,
            'D' => 4,
            'E' => 2,
            'F' => 2,
            'G' => 9.5,
            'J' => 4,
            'I' => 34,
            'K' => 4,
            'L' => 4,
            'M' => 4,
            'N' => 4,
            'O' => 4,
            'P' => 15,
        ];
    }
}
