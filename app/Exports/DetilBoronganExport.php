<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DetilBoronganExport implements FromView, WithStyles
{
    public function __construct(
        public $data,
        public $periode,
        public $nama,
        public $bagian,
        public $pot_bpjs,
        public $pot_kesehatan,
        public $pot_lain,
        public $tunjangan,
        public $take_home_pay
    ) {}

    public function view(): View
    {
        return view('Exports.detil-borongan', [
            'data' => $this->data,
            'periode' => $this->periode,
            'nama' => $this->nama,
            'bagian' => $this->bagian,
            'pot_bpjs' => $this->pot_bpjs,
            'pot_lain' => $this->pot_lain,
            'take_home_pay' => $this->take_home_pay,
            'pot_kesehatan' => $this->pot_kesehatan,
            'tunjangan' => $this->tunjangan,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getParent()->getDefaultStyle()->getFont()
            ->setName('Times New Roman')
            ->setSize(12);
        // 1. Hitung total baris data secara dinamis
        // + baris header (misal header Anda mulai dari baris 6 dan data mulai baris 9)
        $totalData = count($this->data);
        $startRow = 9; // Sesuaikan dengan baris pertama data di foreach Anda
        $endRow = $startRow + $totalData - 1;

        // 1. Tentukan range baris
        $rangeFull = "A$startRow:O$endRow";

        // 2. Berikan border tipis ke seluruh tabel terlebih dahulu agar garis dalam tetap ada
        $sheet->getStyle($rangeFull)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        // 3. Terapkan Border THICK pada sisi kiri dan kanan untuk kolom A sampai E
        // 1. Terapkan untuk kolom A sampai E
        $sheet->getStyle("A$startRow:E$endRow")->applyFromArray([
            'borders' => [
                'left' => ['borderStyle' => Border::BORDER_THICK],
                'right' => ['borderStyle' => Border::BORDER_THICK],
                // Ini akan membuat semua garis vertikal di DALAM kolom A sampai E menjadi tebal
                'vertical' => ['borderStyle' => Border::BORDER_THICK],
            ],
        ]);

        // 2. Terapkan untuk kolom K sampai O
        $sheet->getStyle("K$startRow:O$endRow")->applyFromArray([
            'borders' => [
                'left' => ['borderStyle' => Border::BORDER_THICK],
                'right' => ['borderStyle' => Border::BORDER_THICK],
                // Ini akan membuat semua garis vertikal di DALAM kolom K sampai O menjadi tebal
                'vertical' => ['borderStyle' => Border::BORDER_THICK],
            ],
        ]);

        // 3. Berikan BORDER TEBAL (Thick) hanya untuk bingkai luar tabel data
        $sheet->getStyle("A$startRow:O$endRow")->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        // 4. Jika ingin header juga tebal bingkainya (misal baris 6 sampai 8)
        $sheet->getStyle("A6:O8")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THICK,
                ],
            ],
        ]);

        // 1. Tentukan baris akhir data dari foreach
        $lastDataRow = $startRow + count($this->data);

        // 2. Tambahkan 7 baris lagi setelah data berakhir
        $footerEndRow = $lastDataRow + 6;

        // 3. Terapkan Full Border Thick pada seluruh rentang tersebut (A sampai O)
        $sheet->getStyle("A$lastDataRow:O$footerEndRow")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        $a = $footerEndRow + 6;

        $sheet->getStyle("N$a:O$a")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        $b = $footerEndRow + 2;

        $sheet->getStyle("N$b:O$b")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        $c = $footerEndRow + 3;
        $d = $footerEndRow + 5;

        $sheet->getStyle("N$c:O$d")->applyFromArray([
            'borders' => [
                'left' => ['borderStyle' => Border::BORDER_THICK],
                'right' => ['borderStyle' => Border::BORDER_THICK],
                // Ini akan membuat semua garis vertikal di DALAM kolom K sampai O menjadi tebal
                'vertical' => ['borderStyle' => Border::BORDER_THICK],
            ],
        ]);
    }
}
