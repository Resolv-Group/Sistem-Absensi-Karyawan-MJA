<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RincianUpahExport implements FromView, WithEvents, WithColumnWidths
{

    protected $data;
    protected $periode;
    protected $kategoriPotongan;
    
    public function __construct($data, $periode, $kategoriPotongan = []) 
    {
        $this->data = $data;
        $this->periode = $periode;
        $this->kategoriPotongan = $kategoriPotongan;
    }

    public function view(): View
    {
        $chunks = $this->data->chunk(3);
        return view('Exports.rincian-upah', [
            'chunks'  => $chunks,
            'periode' => $this->periode,
            'kategoriPotongan' => $this->kategoriPotongan
        ]);

    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 13,  
            'B' => 2,
            'C' => 2,
            'D' => 2,
            'E' => 4,
            'F' => 6,
            'G' => 11,
            'H' => 2,
            'I' => 18,
            'J' => 2,

            //2nd Row
            'K' => 13,  
            'L' => 2,
            'M' => 2,
            'N' => 2,
            'O' => 4,
            'P' => 6,
            'Q' => 11,
            'R' => 2,
            'S' => 18,
            'T' => 2,

            //3rd Row
            'U' => 13,  
            'V' => 2,
            'W' => 2,
            'X' => 2,
            'Y' => 4,
            'Z' => 6,
            'AA' => 11,
            'AB' => 2,
            'AC' => 18,
            'AD' => 2,
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
}
