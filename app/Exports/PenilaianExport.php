<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class PenilaianExport implements FromView, WithStyles, WithColumnWidths, WithEvents
{
    public function __construct(
        public $data,
        public $unit,
        public $divisi,
        public $supervisor,
        public $hrd,
    ) {}

    public function styles(Worksheet $sheet)
    {
        // Set font default
        $sheet->getParent()->getDefaultStyle()->getFont()
            ->setName('Times New Roman')
            ->setSize(12);

        // Menghilangkan gridlines agar terlihat profesional (seperti kertas putih)
        $sheet->setShowGridlines(false);
    }

    public function columnWidths(): array
    {
        // Karena kita mulai dari kolom B, maka index kolom bergeser
        return [
            'A' => 4, 
            'B' => 25, 
            'C' => 4, 
            'D' => 6, 
            'E' => 12, 
            'P' => 16, 
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Menambahkan baris kosong di atas (Baris 1)
                $event->sheet->insertNewRowBefore(1, 1);
                
                // Menambahkan kolom kosong di kiri (Kolom A)
                $event->sheet->insertNewColumnBefore('A', 1);
            },
        ];
    }

    public function view(): View
    {
        return view('Exports.penilaian', [
            'data'   => $this->data,
            'unit'   => $this->unit,
            'divisi' => $this->divisi,
            'supervisor' => $this->supervisor,
            'hrd' => $this->hrd,
        ]);
    }
}


