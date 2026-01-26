<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class InvoiceBoronganExport implements FromView, WithStyles, WithColumnWidths, WithEvents
{
    public function __construct(
        public $resi,
        public $nama_unit,
        public $alamat,
        public $bidangUsaha,
        public $nama_mitra,
        public $grand_total,
        public $terbilang,
        public $periode,
        public $management_fee,
        public $ppn,
        public $pph,
        public $total_tagihan,
        public $umk,
    ) {}

    public function view(): View
    {
        return view('Exports.invoice-borongan', [
            'resi' => $this->resi,
            'nama_unit' => $this->nama_unit,
            'alamat' => $this->alamat,
            'bidangUsaha' => $this->bidangUsaha,
            'nama_mitra' => $this->nama_mitra,
            'grand_total' => $this->grand_total,
            'terbilang' => $this->terbilang,
            'periode' => $this->periode,
            'management_fee' => $this->management_fee,
            'ppn' => $this->ppn,
            'pph' => $this->pph,
            'total_tagihan' => $this->total_tagihan,
            'umk' => $this->umk,
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
            'A' => 8,  
            'B' => 8,
            'C' => 16,
            'D' => 13,
            'E' => 12,
            'G' => 16,
            'J' => 16,
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Perbesar ROW ke-20
                $event->sheet->getRowDimension(20)->setRowHeight(30);
                $event->sheet->getRowDimension(21)->setRowHeight(35);
            },
        ];
    }

}
