<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class DetilBoronganExport implements FromView
{
    public function __construct(
        public $data,
        public $periode,
        public $nama,
        public $bagian,
        public $pot_bpjs,
        public $pot_lain,
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
        ]);
    }
}
