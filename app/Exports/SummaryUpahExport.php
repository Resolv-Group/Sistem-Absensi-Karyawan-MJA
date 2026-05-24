<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class SummaryUpahExport implements FromView, ShouldAutoSize, WithStyles, WithTitle, WithDrawings
{
    protected $data, $totals, $periode, $unitName, $penanggungjawab, $jabatan;

    // Terima parameter dari Controller
    public function __construct($data, $totals, $periode, $unitName, $penanggungjawab = [], $jabatan = [])
    {
        $this->data = $data;
        $this->totals = $totals;
        $this->periode = $periode;
        $this->unitName = $unitName;
        $this->penanggungjawab = $penanggungjawab;
        $this->jabatan = $jabatan;
    }

    public function title(): string
    {
        return 'Summary Payroll ' . $this->unitName;
    }

    public function view(): View
    {
        // Langsung lempar ke view
        return view('Exports.summary-upah', [
            'data' => $this->data,
            'totals' => $this->totals,
            'periode' => $this->periode,
            'unit_name' => $this->unitName,
            'penanggungjawab' => $this->penanggungjawab,
            'jabatan' => $this->jabatan,
        ]);
    }

    public function drawings()
    {
        $mja = new Drawing();
        $mja->setPath(public_path('images\mja-logo-excel.png'));
        $mja->setCoordinates('A1');
        $mja->setHeight(60); 

        return [$mja];
    }

    public function styles(Worksheet $sheet)
    {
        // Kerangka untuk styling otomatis (Border, Font, dll)
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]], // Judul
            'A1:AZ500' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}