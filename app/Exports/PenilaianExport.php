<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class PenilaianExport implements FromView
{
    public function __construct(
        public $data,
        public $unit,
        public $divisi
    ) {}

    public function view(): View
    {
        return view('Exports.penilaian', [
            'data'   => $this->data,
            'unit'   => $this->unit,
            'divisi' => $this->divisi,
        ]);
    }
}


