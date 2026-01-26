<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

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
    }
}
