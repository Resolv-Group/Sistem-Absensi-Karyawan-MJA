<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SlipUpahExport implements FromView, WithColumnWidths, WithColumnFormatting
{
    public function __construct(
        public $data,
        public $divisi,
        public $total,
        public $admin,
        public $supervisor,
        public $periode
    ){}

    public function view(): View
    {
        return view('Exports.slip-upah', [
            'data' => $this->data,
            'divisi' => $this->divisi,
            'total' => $this->total,
            'admin' => $this->admin,
            'supervisor' => $this->supervisor,
            'periode' => $this->periode,
        ]);
    }
    public function columnWidths(): array
    {
        return [
            'A' => 4,  
            'B' => 13,
            'C' => 35,
            'D' => 22,
            'E' => 12,
            'F' => 17,
            'G' => 17,
            'H' => 28,
        ];
    }

    public function columnFormats(): array
{
    return [
        // Kolom G atau H (sesuaikan posisi upah) diatur format ribuan
        'G' => '#,##0', 
    ];
}
}
