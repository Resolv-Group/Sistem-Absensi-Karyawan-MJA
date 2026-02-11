<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DailyReportHarianExport implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    protected $data;
    protected $periode;
    protected $grandTotal;
    protected $unitName;
    protected $totalDays;
    protected $tglAwal;
    protected $tglAkhir;
    protected $attendanceMap;
    protected $unit;

    public function __construct($data, $periode, $grandTotal, $unitName, $totalDays, $tglAwal, $tglAkhir, $attendanceMap, $unit)
    {
        $this->data = $data;
        $this->periode = $periode;
        $this->grandTotal = $grandTotal;
        $this->unitName = $unitName;
        $this->totalDays = $totalDays;
        $this->tglAwal = $tglAwal;
        $this->tglAkhir = $tglAkhir;
        $this->attendanceMap = $attendanceMap;
        $this->unit = $unit;
    }

    public function view(): View
    {
        return view('Exports.daily-report-harian', [
            'items' => $this->data,
            'periode' => $this->periode,
            'grand_total' => $this->grandTotal,
            'unit_name' => $this->unitName,
            'tglAwal' => $this->tglAwal,
            'tglAkhir' => $this->tglAkhir,
            'attendanceMap' => $this->attendanceMap,
            'unit' => $this->unit
        ]);
    }

    public function title(): string
    {
        return 'Laporan Upah Harian';
    }

    public function styles(Worksheet $sheet)
    {
        // Hitung baris terakhir data (Header 7 baris + Data)
        $totalRows = count($this->data);
        $lastDataRow = 16 + $totalRows;
        $footerRow = $lastDataRow + 1;

        $leftStaticCount = 10; 
        
        $dateStartColIndex = $leftStaticCount + 1; // Mulai setelah kolom statis kiri
        $dateEndColIndex   = $leftStaticCount + $this->totalDays; // Ditambah jumlah har
        $rightStaticStartColIndex = $dateEndColIndex + 1;
        
        $rightStaticEndColIndex   = $rightStaticStartColIndex + 21; 

        $colLeftEnd  = Coordinate::stringFromColumnIndex($leftStaticCount); // Huruf J
        $colRightStart = Coordinate::stringFromColumnIndex($rightStaticStartColIndex); // Huruf AO (jika 30 hari)
        $colRightEnd   = Coordinate::stringFromColumnIndex($rightStaticEndColIndex);   // Huruf BK (misal)


        // 1. SET BORDER HITAM TIPIS (Untuk seluruh tabel Data + Header)
        // Range: A6 sampai M(Footer)
        $sheet->getStyle('A16:I' . $footerRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('J7:J10')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,     
            ]
        ]);

        $sheet->getStyle($colRightStart . '16:' . $colRightEnd . '16')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true, // WRAP TEXT DI SINI
            ]
        ]);

        // 2. STYLE HEADER (Peach Background + Bold + Center)
        $sheet->getStyle('A16:I16')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,     
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FCE4D6'], // Warna Peach
            ],
        ]);

        // 4. Format Angka (Kolom D s/d M)
        // Agar Excel membacanya sebagai angka (bisa disum user), bukan teks
        $sheet->getStyle('D8:M' . $footerRow)->getNumberFormat()->setFormatCode('#,##0');
    }
}